<?php

namespace Zxbn;

abstract class HtmlBanner
{
    protected $cacheDir;
    protected $useCache = true;
    protected $listUrl = true;
    protected $limit = 5;
    protected $type;
    protected $trackingLink;
    protected $parserType;
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
            if ($this->listUrl) {
                if ($this->parserType !== null) {
                    $className = $this->parserType;
                    /**
                     * @var Parser $contentParser
                     */
                    $contentParser = new $className();

                    if ($data = $contentParser->getData($this->listUrl, $this->limit)) {
                        $htmlGenerator->setData($data);
                        $htmlGenerator->generate();
                    }
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

class Downloader
{
    protected function download($url)
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $result;
    }

    protected function addUtmParameters($link)
    {
        if (stripos($link, '?') === false) {
            $link .= '?';
        } elseif (stripos($link, '&') === false) {
            $link .= '&';
        }
        $link .= 'utm_source=zxbn&utm_medium=banner&utm_campaign=zxbn';
        return $link;
    }
}

interface Parser
{
    public function getData($url, $limit);
}

abstract class HtmlParser extends Downloader implements Parser
{
    abstract function parseHtml($html, $limit);

    public function getData($url, $limit)
    {
        if ($html = $this->download($url)) {
            $data = $this->parseHtml($html, $limit);
            foreach ($data as $key => $item) {
                $data[$key]['link'] = $this->addUtmParameters($item['link']);
            }
            return $data;
        }
        return false;
    }

    protected function getXpath($string)
    {
        if ($html = $this->getHtmlDocument($string)) {
            $xPath = new \DOMXPath($html);
            return $xPath;
        }
        return false;
    }

    protected function getHtmlDocument($string)
    {
        if ($string) {
            $dom = new \DOMDocument;
            $dom->strictErrorChecking = false;
            $dom->encoding = 'UTF-8';
            $dom->recover = true;
            $dom->substituteEntities = true;
            $dom->strictErrorChecking = false;
            $dom->formatOutput = false;
            @$dom->loadHTML('<?xml version="1.0" encoding="UTF-8"?>' . $string);
            $dom->normalizeDocument();
            return $dom;
        }
        return false;
    }

}

class RssParser extends Downloader implements Parser
{
    const namespaceDc = 'http://purl.org/dc/elements/1.1/';
    const namespaceItunes = 'http://www.itunes.com/dtds/podcast-1.0.dtd';
    const namespaceContent = 'http://purl.org/rss/1.0/modules/content/';

    public function getData($url, $limit)
    {
        $data = array();
        if ($rss = $this->download($url)) {
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
                    $itemInfo['link'] = $this->addUtmParameters($itemInfo['link']);
                    $itemInfo['category'] = trim($item->category);
                    $itemInfo['creator'] = trim($item->children(self::namespaceDc)->creator);
                    $itemInfo['text'] = $this->htmlToPlainText($item->description);
                    $itemInfo['youtubeId'] = '';
                    $itemInfo['image'] = '';
                    if ($this instanceof RssImageParser) {
                        $itemInfo['image'] = $this->getImageUrl($item);
                    } else {
                        if (isset($item->children(self::namespaceItunes)->image)) {
                            $itemInfo['image'] = trim($item->children(self::namespaceItunes)->image->attributes()->href);
                        }
                        if (!$itemInfo['image']) {
                            preg_match('/<img(.*)src="(.*)"/', $item->description, $matches);
                            if (isset($matches[2])) {
                                $itemInfo['image'] = $matches[2];
                            }
                        }
                        if (!$itemInfo['image']) {
                            if (isset($item->children(self::namespaceContent)->encoded)) {
                                $html = trim($item->children(self::namespaceContent)->encoded);

                                preg_match('/<img(.*)src="(.*)"/', $html, $matches);
                                if (isset($matches[2])) {
                                    $itemInfo['image'] = $matches[2];
                                }
                                preg_match('#www.youtube.com\/embed\/([A-Za-z0-9]*)#i', $html, $matches);
                                if (isset($matches[1])) {
                                    $itemInfo['youtubeId'] = $matches[1];
                                }
                            }
                        }
                        if (!$itemInfo['youtubeId']) {
                            preg_match('#www.youtube.com\/embed\/([A-Za-z0-9]*)#i', $item->description, $matches);
                            if (isset($matches[1])) {
                                $itemInfo['youtubeId'] = $matches[1];
                            }
                        }
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

interface RssImageParser
{
    public function getImageUrl($channelItemXml);
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
