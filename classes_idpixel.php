<?php

namespace Zxbn;

class IdpixelHtmlBanner extends HtmlBanner
{
    protected $rssUrl = 'http://idpixel.ru/rss/news_zx.rss';
    protected $limit = 5;
    protected $type = 'idpixel';
}

class IdpixelZxHtmlBanner extends HtmlBanner
{
    protected $rssUrl = 'http://idpixel.ru/rss/news.rss';
    protected $limit = 5;
    protected $type = 'idpixelZx';
}

class IdpixelTemplate
{
    public function render($data)
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
					line-height: 1.5;
					color: #333;
					position: relative;
					background-color: #fff;
				}

				.imagetop {
					max-width: 100%;
					height: auto !important;
					display: block;
					float: none;
					margin: 0 auto 5px;
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
					padding-top: 30px;
				}

				.button {
					font-size: 11px;
					display: inline-block;
					text-align: center;
					padding: 10px;
					font-weight: normal;
					line-height: 10px;
					color: #282828;
					background-color: #ffe04d;
				}
			</style>
		</head>
		<body>
		<div class="main">
			<header class="header" role="banner">
				<a href="//idpixel.ru"><img class="logo" src="//idpixel.ru/i/logo.png" /></a>
			</header>
			<h1 class="heading"><a href="<?php echo $data['link']; ?>"><?php echo $data['title']; ?></a></h1>
            <?php if (!empty($data['image'])) {
                echo '<a href="' . $data['link'] . '"><img class="imagetop" src="' . $data['image'] . '" /></a>';
            } ?>
			<div class="content"><?php echo $data['text']; ?></div>
			<div class="controls"><a class="button" href="<?php echo $data['link']; ?>">Читать статью</a></div>
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
