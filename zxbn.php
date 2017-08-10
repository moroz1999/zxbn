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

    public function getHtml()
    {
        $bannerNumber = rand(0, $this->limit);
        $htmlGenerator = new HtmlGenerator();
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

abstract class Template
{
    abstract public function render($data);
}

class RssParser
{
    public function getData($url, $limit)
    {
        $data = [];
        if ($rss = file_get_contents($url)) {

            libxml_use_internal_errors(true);
            if ($xml = simplexml_load_string($rss)) {
                $xml->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
                $number = 0;
                foreach ($xml->channel->item as $item) {
                    $itemInfo = [];
                    if ($number > $limit) {
                        break;
                    }

                    $itemInfo['title'] = $item->title;
                    $itemInfo['link'] = $item->link;
                    $itemInfo['image'] = '';
                    $itemInfo['text'] = $this->htmlToPlainText($item->description);
                    preg_match('/src="([^"]+)"/', $item->description, $matches);
                    if (isset($matches[1])) {
                        $itemInfo['image'] = $matches[1];
                    }

                    $number++;
                    $data[] = $itemInfo;
                }
            }
        }
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
        return $this->cacheDir . '/' . $this->type . '.bannercache.' . $number . '.html';
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