<?php

namespace Zxbn;

class VivaHtmlBanner extends HtmlBanner
{
    protected $listUrl = 'https://viva-games.ru/samye-populyarnye-retro-igry?spage=1';
    protected $limit = 20;
    protected $type = 'viva';
    protected $parserType = '\Zxbn\VivaParser';
}

class VivaParser extends HtmlParser
{
    public function parseHtml($html, $limit)
    {
        $data = [];
        if ($xPath = $this->getXpath($html)) {
            $postNodes = $xPath->query("//div[contains(@class,'resitem')]");
            if ($postNodes->length) {
                foreach ($postNodes as $postNode) {
                    if (count($data) > $limit) {
                        break;
                    }
                    $itemInfo = [
                        'title' => '',
                        'image' => '',
                        'text' => '',
                        'link' => '',
                    ];
                    if ($h3Nodes = $xPath->query(".//h3", $postNode)) {
                        if ($h3Node = $h3Nodes->item(0)) {
                            $itemInfo['title'] = $h3Node->textContent;
                        }
                    }
                    if ($imgNodes = $xPath->query(".//a/img", $postNode)) {
                        if ($imgNode = $imgNodes->item(0)) {
                            $itemInfo['image'] = $imgNode->getAttribute('src');
                        }
                    }
                    if ($aNodes = $xPath->query(".//a", $postNode)) {
                        $aNode = $aNodes->item(0);
                        $itemInfo['link'] = $aNode->getAttribute('href');
                    }
                    $data[] = $itemInfo;
                }
            }
        }
        return $data;
    }
}

class VivaTemplate
{
    public function render($data, $trackingLink)
    {
        ob_start();

        ?>
        <!DOCTYPE HTML>
        <html>
        <head>
            <style>
                html, body, .main {
                    width: 240px;
                    height: 320px;
                    padding: 0;
                    margin: 0;
                    overflow: hidden;
                    font-family: Tahoma, sans-serif;
                    line-height: 1.5;
                    color: #000;
                    position: relative;
                    background: #ffffff url("/images/viva/body-bg.gif") repeat;
                }

                .logo {
                    display: block;
                    width: 240px;
                    height: 87px;
                }

                .title {
                    font-weight: bold;
                    text-align: center;
                    font-size: 16px;
                    color: #2B6FB6;
                    text-shadow: 1px 1px 1px #ffffff;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    margin: 5px;
                }

                .imagetop {
                    display: block;
                    height: 150px;
                    width: 240px;
                    background-size: contain;
                    background-position: center;
                    background-repeat: no-repeat;
                }

                .link_cover {
                    cursor: pointer;
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    display: block;
                }

                .button {
                    display: block;
                    width: 67px;
                    height: 30px;
                    background: #ffffff url("/images/viva/play.png") no-repeat;
                    margin-top: 10px;
                    margin-left: auto;
                    margin-right: auto;
                }
            </style>
        </head>
        <body>
        <div class="main">
            <img class="logo" src="/images/viva/logo.png"/>
            <div class="title"><?php echo $data['title']; ?></div>
            <?php if (!empty($data['image'])) {
                ?>
            <div class="imagetop" style="background-image: url('<?php echo $data['image']; ?>  ');"></div><?php
            } ?>
            <div class="controls">
                <div class="button"></div>
            </div>
            <a
                    href="<?php echo $trackingLink . $data['link']; ?>"
                    onclick="window.open('<?php echo $trackingLink . $data['link']; ?>', '_blank');return false;" class="link_cover"></a>
        </div>
        </body>
        </html>
        <?php

        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
