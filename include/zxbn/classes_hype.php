<?php

namespace Zxbn;

class HypeHtmlBanner extends HtmlBanner
{
    protected $rssUrl = 'http://hype.retroscene.org/rss/index/';
    protected $limit = 3;
    protected $type = 'hype';
}

class HypeTemplate
{
    public function render($data, $trackingLink)
    {
        ob_start();

        ?>
		<!DOCTYPE HTML>
		<html>
		<head>
			<link href='//fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
			<style>
				html, body, .main {
					width: 240px;
					height: 320px;
					padding: 0;
					margin: 0;
					overflow: hidden;
					font-size: 11px;
					font-family: Verdana, "Helvetica Neue", Helvetica, Arial, sans-serif;
					line-height: 18px;
					color: #000;
					position: relative;
					background-color: #fff;
				}

				.imagetop {
					max-width: 100%;
					max-height: 100px;
					height: auto !important;
					display: block;
					float: none;
					margin: 0 auto 10px;
				}

				#header {
					background-color: #000;
					height: 51px;
					margin: 0 auto;
					position: relative;
					text-align: center;
				}

				h1 {
					text-decoration: underline;
					color: #275ec2;
					font-size: 20px;
					line-height: 1.1em;
					font-weight: normal;
					margin: 0 0 10px 0;
					padding: 0;
					font-family: 'PT Sans', Arial, sans-serif;
				}

				.controls {
					position: absolute;
					bottom: 0;
					text-align: center;
					background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 1) 60%);
					width: 100%;
					padding-top: 60px;
				}

				.button {
					border: 1px solid #27ace8;
					padding: 2px 15px 4px;
					border-radius: 15px;
					line-height: 17px;
					color: #fff;
					font-size: 11px;
					display: inline-block;
					text-align: center;
					font-family: Verdana, sans-serif;
					cursor: pointer;
					text-decoration: none;
					background: linear-gradient(to bottom, #66cfff, #2abcfe);
				}

				.link_cover {
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
			<header id="header" role="banner">
				<img src="http://hype.retroscene.org/rnd-logo.php" />
			</header>
			<h1 class="heading"><?php echo $data['title']; ?></h1>
            <?php if (!empty($data['image'])) {
                echo '<img class="imagetop" src="' . $data['image'] . '" />';
            } ?>
			<div class="content"><?php echo $data['text']; ?></div>
			<div class="controls"><span class="button">Читать статью</span></div>
			<a href="<?php echo $trackingLink . $data['link']; ?>"
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
