<?php

namespace Zxbn;

abstract class HtmlBanner
{
    protected $cacheDir;
    protected $useCache = true;
    protected $rssUrl = true;
    protected $limit = 5;
    protected $type;
    protected $trackingLink;
    protected $generatorType;

    /**
     * @param mixed $trackingLink
     */
    public function setTrackingLink($trackingLink)
    {
        $trackingLink = filter_var($trackingLink, FILTER_SANITIZE_URL);
        if (filter_var($trackingLink, FILTER_VALIDATE_URL)) {
            $this->trackingLink = $trackingLink;
        } else {
            $this->trackingLink = '';
        }
    }

    /**
     * @param bool $useCache
     */
    public function setUseCache($useCache)
    {
        $this->useCache = $useCache;
    }

    /**
     * @param mixed $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    protected function getBannerNumber()
    {
        return rand(0, $this->limit - 1);
    }

    public function getHtml()
    {
        $bannerNumber = $this->getBannerNumber();
        if ($this->generatorType !== null) {
            $className = 'Zxbn\\' . ucfirst($this->generatorType) . 'HtmlGenerator';
            $htmlGenerator = new $className();
        } else {
            $htmlGenerator = new HtmlGenerator();
        }
        $htmlGenerator->setTrackingLink($this->trackingLink);
        $htmlGenerator->setType($this->type);
        $htmlGenerator->setCacheDir($this->cacheDir);

        if (!$this->useCache || !$htmlGenerator->isCacheValid($bannerNumber)) {
            if ($this->rssUrl) {
                $rssParser = new RssParser();
                if ($data = $rssParser->getData($this->rssUrl, $this->limit)) {
                    $htmlGenerator->setData($data);
                    $htmlGenerator->generate();
                }
            }
        }
        return $htmlGenerator->getHtml($bannerNumber);
    }
}

abstract class GroupedHtmlBanner extends HtmlBanner
{
    protected $generatorType = 'grouped';

    protected function getBannerNumber()
    {
        return 0;
    }
}

abstract class Template
{
    abstract public function render($data, $trackingLink);
}

class RssParser
{
    const namespaceDc = 'http://purl.org/dc/elements/1.1/';
    const namespaceItunes = 'http://www.itunes.com/dtds/podcast-1.0.dtd';

    public function getData($url, $limit)
    {
        $data = array();

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
        if ($rss = curl_exec($curlHandle)) {

            libxml_use_internal_errors(true);
            if ($xml = simplexml_load_string($rss)) {
                $xml->registerXPathNamespace('dc', self::namespaceDc);
                $xml->registerXPathNamespace('itunes', self::namespaceItunes);
                $number = 0;
                foreach ($xml->channel->item as $item) {
                    $itemInfo = array();
                    if ($number >= $limit) {
                        break;
                    }

                    $itemInfo['title'] = trim($item->title);
                    $itemInfo['link'] = trim($item->link);
                    if (stripos($itemInfo['link'], '?') === false) {
                        $itemInfo['link'] .= '?';
                    } elseif (stripos($itemInfo['link'], '&') === false) {
                        $itemInfo['link'] .= '&';
                    }
                    $itemInfo['link'] .= 'utm_source=zxbn&utm_medium=banner&utm_campaign=zxbn';
                    $itemInfo['category'] = trim($item->category);
                    $itemInfo['creator'] = trim($item->children(self::namespaceDc)->creator);
                    $itemInfo['text'] = $this->htmlToPlainText($item->description);
                    $itemInfo['image'] = '';
                    if (isset($item->children(self::namespaceItunes)->image)){
                        $itemInfo['image'] = trim($item->children(self::namespaceItunes)->image->attributes()->href);
                    }
                    if (!$itemInfo['image']){
                        preg_match('/src="([^"]+)"/', $item->description, $matches);
                        if (isset($matches[1])) {
                            $itemInfo['image'] = $matches[1];
                        }
                    }

                    $number++;
                    $data[] = $itemInfo;
                }
            }
        }
        curl_close($curlHandle);
        return $data;
    }

    protected function htmlToPlainText($src)
    {
        $result = $src;
        $result = html_entity_decode($result, ENT_QUOTES);
        $result = preg_replace('/<style([\s\S]*?)<\/style>/', '', $result); // remove stylesheet
        $result = preg_replace('/[\xA0]*/', '', $result);
        $result = preg_replace('#[\n\r\t]#', "", $result);
        $result = preg_replace('#[\s]+#', " ", $result);
        $result = preg_replace('#(</li>|</div>|</td>|</tr>|<br />|<br/>|<br>)#', "$1\n", $result);
        $result = preg_replace('#(</h1>|</h2>|</h3>|</h4>|</h5>|</p>)#', "$1\n\n", $result);
        $result = strip_tags($result);
        $result = preg_replace('#^ +#m', "", $result); //left trim whitespaces on each line
        $result = preg_replace('#([\n]){2,}#', "\n\n", $result); //limit newlines to 2 max
        $result = trim($result);
        return $result;
    }

}

class HtmlGenerator
{
    protected $trackingLink;
    protected $type;
    protected $data;
    protected $cacheDir;

    /**
     * @param mixed $trackingLink
     */
    public function setTrackingLink($trackingLink)
    {
        $this->trackingLink = $trackingLink;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir);
        }
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    protected function getCachePath($number)
    {
        return $this->cacheDir . $this->type . '.bannercache.' . $number . '.html';
    }

    public function generate()
    {
        foreach ($this->data as $number => $info) {
            $className = 'Zxbn\\' . ucfirst($this->type) . 'Template';
            /** @var Template $template */
            $template = new $className();
            if ($content = $template->render($info, $this->trackingLink)) {
                file_put_contents($this->getCachePath($number), $content);
            }
        }
    }

    public function getHtml($bannerNumber)
    {
        $path = $this->getCachePath($bannerNumber);
        if (is_file($path)) {
            return file_get_contents($path);
        }
        return false;
    }

    public function isCacheValid($bannerNumber)
    {
        $cacheLifeTime = 60 * 60 * 1;

        $cacheFilePath = $this->getCachePath($bannerNumber);
        //check cache existance
        $cacheValid = false;
        if (is_file($cacheFilePath)) {
            $modifiedTime = filemtime($cacheFilePath);
            if (time() - $modifiedTime < $cacheLifeTime) {
                $cacheValid = true;
            }
        }
        return $cacheValid;
    }
}

class GroupedHtmlGenerator extends HtmlGenerator
{
    public function generate()
    {
        $className = 'Zxbn\\' . ucfirst($this->type) . 'Template';
        /** @var Template $template */
        $template = new $className();
        if ($content = $template->render($this->data, $this->trackingLink)) {
            file_put_contents($this->getCachePath(0), $content);
        }
    }
}
