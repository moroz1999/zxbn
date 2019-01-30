<?php

namespace Zxbn;

class IrnHtmlBanner extends HtmlBanner
{
    protected $listUrl = 'http://www.indieretronews.com/search/label/Zx%20Spectrum';
    protected $limit = 5;
    protected $type = 'irn';
    protected $parserType = '\Zxbn\IrnParser';
}

class IrnParser extends HtmlParser
{
    public function parseHtml($html)
    {
        $data = false;
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
					font-size: 11px;
					font-family: "Helvetica Neue", sans-serif;
					line-height: 1.5;
					color: #eee;
					position: relative;
					background-color: #222;
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
					margin-bottom: 10px;
				}

				.logo {
					display: block;
					max-height: 37px;
					max-width: 220px;
					margin: 2px 10px 0 3px;
					float: left;
				}

				.site-title {
					padding-top: 2px;
				}

				h1, h1 a {
					text-decoration: none;
					font-family: "Helvetica Neue", sans-serif;
					font-weight: normal;
					color: #eee;
					line-height: 1.1;
					margin: 0;
					font-size: 18px;
					text-transform: uppercase;
				}

				.heading {
					margin-left: 5px;
					margin-right: 5px;
					margin-bottom: 5px;
				}

				.controls {
					position: absolute;
					bottom: 0;
					text-align: center;
					background: linear-gradient(to bottom, rgba(34, 34, 34, 0), rgba(34, 34, 34, 1) 50%);
					width: 100%;
					padding-top: 40px;
				}

				.button {
					color: #ddd;
					text-decoration: underline;
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
					padding: 0 5px;
				}
			</style>
		</head>
		<body>
		<div class="main">
			<header class="header" role="banner">
				<img class="logo" src="//emulate.su/wp-content/uploads/2017/01/cropped-emulate-su-logo.png" />
				<h1 class="site-title"><a href="//emulate.su/" rel="home">EMULATE.SU</a></h1>
				<div class="site-description">Добрые видеоигры!</div>
			</header>
			<h1 class="heading"><?php echo $data['title']; ?></h1>
            <?php if (!empty($data['image'])) {
                echo '<div class="imagetop" style="background-image: url(\'' . $data['image'] . '\');" /></div>';
            } ?>
			<div class="content"><?php echo $data['text']; ?></div>
			<div class="controls">
				<div class="button">Читать статью</div>
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
