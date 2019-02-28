<?php

namespace Zxbn;

class IdpixelHtmlBanner extends HtmlBanner
{
    protected $listUrl = 'https://idpixel.ru/rss/news.rss';
    protected $limit = 5;
    protected $type = 'idpixel';
    protected $parserType = '\Zxbn\RssParser';
}

class IdpixelTemplate
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
                    font-size: 11px;
                    font-family: "Roboto Light", sans-serif;
                    line-height: 1.2;
                    color: #333;
                    position: relative;
                    background-color: #fff;
                }

                .imagetop {
                    display: block;
                    height: 140px;
                    margin: 0 auto 5px;
                    background-size: cover;
                    background-position: center;
                }

                .header {
                    background-color: #000;
                    height: 40px;
                    position: relative;
                    text-align: center;
                    margin-bottom: 10px;
                }

                .logo {
                    display: block;
                    max-height: 37px;
                    max-width: 220px;
                    margin: 0 auto;
                    padding-top: 3px;
                }

                h1, h1 a {
                    text-decoration: none;
                    font-family: "Roboto", Helvetica, Arial, sans-serif;
                    font-weight: normal;
                    color: #333;
                    line-height: 1.1;
                    margin: 0;
                    font-size: 17px;
                }

                .heading {
                    margin-bottom: 5px;
                }

                .controls {
                    position: absolute;
                    bottom: 0;
                    text-align: center;
                    background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 1) 60%);
                    width: 100%;
                    padding-top: 20px;
                }

                .button {
                    font-size: 11px;
                    display: inline-block;
                    text-align: center;
                    padding: 5px 10px;
                    font-weight: normal;
                    line-height: 10px;
                    color: #282828;
                    background-color: #ffe04d;
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
            </style>
        </head>
        <body>
        <div class="main">
            <header class="header" role="banner">
                <img class="logo" src="http://idpixel.ru/i/logo.png"/>
            </header>
            <h1 class="heading"><?php echo $data['title']; ?></h1>
            <?php if (!empty($data['image'])) { ?>
                <div class="imagetop" style="background-image: url(' <?php echo $data['image'] ?> '\');"></div>;
            <?php } ?>
            <div class="content"><?php echo $data['text']; ?></div>
            <div class="controls">
                <div class="button">Читать статью</div>
            </div>
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

class IdpixelZxTemplate extends IdpixelTemplate
{

}
