<?php

namespace Zxbn;

class ZxpkruMainHtmlBanner extends HtmlBanner
{
    protected $rssUrl = 'https://zx-pk.ru/external.php?do=rss&type=newcontent&sectionid=1&days=120&count=10';
    protected $limit = 1;
    protected $type = 'zxpkruMain';
}

class ZxpkruHtmlBanner extends GroupedHtmlBanner
{
    protected $rssUrl = 'https://zx-pk.ru/external.php?type=RSS2&forumids=5,10,23,21,17,11,12,14,26,70,71,73,69,74,92,13,8,9,16';
    protected $limit = 10;
    protected $type = 'zxpkru';
}

class ZxpkruTemplate
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
					font-size: 10pt;
					font-family: verdana, geneva, lucida, "lucida grande", arial, helvetica, sans-serif;
					line-height: 1.2;
					color: #000;
					position: relative;
					background-color: #fff;
				}

				.header {
					background-color: #F9F9F9;
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

				.toptext {
					border: 1px solid #E3E6E8;
					background: #76A4E2 none repeat-x;
					color: #FFFFFF;
					padding: 4px;
				}

				.centerblock {
					box-shadow: -2px 2px 2px #c8c8c8;
					margin: 0 5px 2px;
					height: 267px;
					overflow: hidden;
				}

				.item {
					background: #F6F6F6 url('//zx-pk.ru/images/icons/icon1.png') 3px center no-repeat;
					border: 1px solid rgb(255, 255, 255);
					display: block;
					color: inherit;
					vertical-align: middle;
					font-size: 8pt;
					padding: 3pt 3pt 3pt 25px;
					text-decoration: none;
					position: relative;
				}

				.item:hover {
					background-color: #ffffff;
				}
			</style>
		</head>
		<body>
		<div class="main">
			<header class="header" role="banner">
				<a class="logo_link"
				   href="//zx-pk.ru/forum.php"
				   onclick="window.open('//zx-pk.ru/forum.php?utm_source=zxbn&utm_medium=banner&utm_campaign=zxbn', '_blank');return false;"
				><img class="logo" src="//zx-pk.ru/images/styles/asdialup/misc/zxpkru_logo_ani.gif" /></a>
			</header>
			<div class="centerblock">
				<div class="toptext">Новые топики</div>
                <?php
                foreach ($data as $info) {
                    ?>
					<a class="item"
					   onclick="window.open('<?php echo $trackingLink . $info['link']; ?>', '_blank');return false;"
					   href="<?php echo $trackingLink . $info['link']; ?>">
                        <?php echo $info['title']; ?></a>
                    <?php
                }
                ?>
			</div>
		</div>
		</body>
		</html>
        <?php

        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}


class ZxpkruMainTemplate
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
					font-size: 10pt;
					font-family: verdana, geneva, lucida, "lucida grande", arial, helvetica, sans-serif;
					line-height: 1.2;
					color: #000;
					position: relative;
					background-color: #fff;
				}

				.header {
					background-color: #F9F9F9;
					height: 40px;
					position: relative;
					text-align: center;
				}

				.logo {
					display: block;
					max-height: 37px;
					max-width: 220px;
					margin: 0 auto;
					padding-top: 3px;
				}

				.centerblock {
					border-top: 4px solid #76A4E2;
					padding-top: 5px;
				}

				.toptext {
					border: 1px solid #E3E6E8;
					background: #76A4E2 none repeat-x;
					color: #FFFFFF;
					padding: 4px;
				}

				h1 {
					text-decoration: underline;
					margin: 0 0 5px 0;
					font: bold 14px Tahoma, Calibri, Verdana, Geneva, sans-serif;
				}

				.content {
					font: 13px Tahoma, Calibri, Verdana, Geneva, sans-serif;
				}

				.imagetop {
					display: block;
					height: 140px;
					margin: 0 auto 5px;
					background-size: cover;
					background-position: center top;
				}

				.controls {
					position: absolute;
					bottom: 0;
					text-align: center;
					background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 1) 60%);
					width: 100%;
					padding-top: 40px;
				}

				.button {
					position: relative;
					left: -10px;
					display: inline-block;
					text-align: center;
					padding: 0 20px 0 0;
					background: url('//zx-pk.ru/images/cms/read_more-right.png') right center no-repeat;
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
				<a class="logo_link"
				   href="//zx-pk.ru/forum.php"
				   onclick="window.open('//zx-pk.ru/forum.php?utm_source=zxbn&utm_medium=banner&utm_campaign=zxbn', '_blank');return false;"
				><img class="logo" src="//zx-pk.ru/images/styles/asdialup/misc/zxpkru_logo_ani.gif" /></a>
			</header>
			<div class="centerblock">
				<!--				<div class="toptext">Новые топики</div>-->
				<h1 class="heading"><?php echo $data['title']; ?></h1>
                <?php if (!empty($data['image'])) {
                    echo '<div class="imagetop" style="background-image: url(\'' . $data['image'] . '\');" /></div>';
                } ?>
				<div class="content"><?php echo $data['text']; ?></div>
				<div class="controls">
					<div class="button">Подробнее</div>
				</div>
				<a href="<?php echo $trackingLink . $data['link']; ?>"
				   onclick="window.open('<?php echo $trackingLink . $data['link']; ?>', '_blank');return false;"
				   class="link_cover"></a>
			</div>
		</div>
		</body>
		</html>
        <?php

        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}