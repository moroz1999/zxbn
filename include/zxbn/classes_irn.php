<?php

namespace Zxbn;

class IrnHtmlBanner extends HtmlBanner
{
    protected $listUrl = 'http://www.indieretronews.com/search/label/Zx%20Spectrum';
    protected $limit = 10;
    protected $type = 'irn';
    protected $parserType = '\Zxbn\IrnParser';
}

class IrnParser extends HtmlParser
{
    public function parseHtml($html, $limit)
    {
        $data = [];
        if ($xPath = $this->getXpath($html)) {
            $postNodes = $xPath->query("//div[contains(@itemtype,'http://schema.org/BlogPosting')]");
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
                    if ($h3Nodes = $xPath->query(".//h3[contains(@class, 'post-title')]", $postNode)) {
                        if ($h3Node = $h3Nodes->item(0)) {
                            $itemInfo['title'] = $h3Node->textContent;
                        }
                    }
                    if ($imgNodes = $xPath->query(".//div[contains(@class, 'post-body')]//a/img", $postNode)) {
                        if ($imgNode = $imgNodes->item(0)) {
                            $itemInfo['image'] = $imgNode->getAttribute('src');
                        }
                    }
                    if ($contentNodes = $xPath->query(".//div[contains(@class, 'post-body')]", $postNode)) {
                        if ($contentNode = $contentNodes->item(0)) {
                            $itemInfo['text'] = $contentNode->textContent;
                        }
                    }
                    if ($aNodes = $xPath->query(".//div[contains(@class, 'jump-link')]/a", $postNode)) {
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

class IrnTemplate
{
    public function render($data, $trackingLink)
    {
        ob_start();

        ?>
        <!DOCTYPE HTML>
        <html>
        <head>
            <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&amp;subset=cyrillic" rel="stylesheet">
            <style>
                html, body, .main {
                    width: 240px;
                    height: 320px;
                    padding: 0;
                    margin: 0;
                    overflow: hidden;
                    font-family: "Verdana", sans-serif;
                    line-height: 1.5;
                    color: #eee;
                    position: relative;
                    background-color: #000;
                }

                .imagetop {
                    position: absolute;
                    top: 0;
                    left: 0;
                    display: block;
                    height: 256px;
                    width: 240px;
                    background-size: cover;
                    background-position: center;
                }

                .logo {
                    display: block;
                    width: 240px;
                    height: 63px;
                    position: absolute;
                    bottom: 0;
                    left: 0;
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

                .content {
                    padding: 5px 7px 0;
                    box-sizing: border-box;
                    top: 167px;
                    left: 10px;
                    right: 10px;
                    position: absolute;
                    font-size: 10px;
                    background-color: #303030;
                    border: solid 1px #444444;
                    border-radius: 10px;
                    height: 75px;
                    overflow: hidden;
                }

                .content::after {
                    display: block;
                    content: "";
                    position: absolute;
                    left: 7px;
                    bottom: 0;
                    right: 7px;
                    height: 22px;
                    background: linear-gradient(to top, #303030 30%, rgba(48, 48, 48, 0));
                }
            </style>
        </head>
        <body>
        <div class="main">
            <?php if (!empty($data['image'])) {
                ?>
            <div class="imagetop" style="background-image: url('<?php echo $data['image']; ?>  ');"></div><?php
            } ?>
            <div class="content"><?php echo $data['text']; ?></div>
            <div class="controls">
                <div class="button">Читать статью</div>
            </div>
            <img class="logo" src="/images/irn/logo.png"/>
            <a
                    href="<?php echo $trackingLink . $data['link']; ?>"
                    onclick="window.open('<?php echo $trackingLink . $data['link']; ?>', '_blank');return false;"
                    class="link_cover"></a>
        </div>
        </body>
        </html>
        <?php

        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
