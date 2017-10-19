<?php

namespace Zxbn;

class ZxchipHtmlBanner extends HtmlBanner
{
    protected $rssUrl = 'https://podster.fm/rss.xml?pid=263';
    protected $limit = 3;
    protected $type = 'zxchip';
}

class ZxchipTemplate
{
    public function render($data, $trackingLink)
    {
        ob_start();
        $matches = [];
        preg_match('#\/([0-9]*)\?#', $data['link'], $matches);
        if (isset($matches[1])) {
            $data['number'] = $matches[1];
        }
        ?>
		<!doctype html>
		<html>
		<head>
			<meta charset="utf-8">
			<title>zx-chip</title>
			<style type="text/css">
				body {
					margin: 0;
					background: #fff;
				}

				#top {
					position: relative;
					overflow: hidden;
				}

				#pic1 {
					position: fixed;
					top: 20px;
					margin-left: 20px;
					z-index: 200;
					width: 200px;
					height: 200px;
					align-content: center;
				}

				#pic2 {
					margin-top: 30px;
					margin-left: 20px;
					z-index: 100;
					width: 200px;
					height: 200px;
					align-content: center;
					-webkit-filter: blur(15px);
					-moz-filter: blur(15px);
				}

				#pic1 img,
				#pic2 img {
					width: 200px;
					height: 200px;
					display: block;
				}

				#num {
					max-width: 240px;
					text-align: center;
					margin-top: 1px;
					z-index: 300;
					font-size: 1.3em
				}

				#named {
					max-width: 240px;
					text-align: center;
					margin-top: 5px;
					z-index: 300;
					font-size: .8em
				}

				#rss {
					position: fixed;
					margin-top: 8px;
					margin-left: 20px;
					z-index: 300;
				}

				#vk {
					position: fixed;
					margin-top: 12px;
					margin-left: 106px;
					z-index: 300;
				}

				#tg {
					margin-top: 6px;
					margin-left: 193px;
					z-index: 300;
				}

				.link_cover {
					cursor: pointer;
					position: absolute;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					display: block;
					z-index: 1000;
				}
			</style>

		</head>

		<body>
		<div width="240px" height="320px" frameborder="0" style="border: none;">
			<div id="top">
                <?php if ($data['image']) { ?>
					<div id="pic2"><img src="<?php echo $data['image']; ?>"></div>
					<div id="pic1"><img src="<?php echo $data['image']; ?>"></div>
                <?php } else { ?>
					<div id="pic2"><img src="zxchip_images/slice4.png"></div>
					<div id="pic1"><img src="zxchip_images/slice4.png"></div>
                <?php } ?>
				<div id="num">zx-chip <?php echo $data['number']; ?></div>
				<div id="named"><?php echo $data['title']; ?></div>
				<a
					href="<?php echo $trackingLink . $data['link']; ?>"
					onclick="window.open('<?php echo $trackingLink . $data['link']; ?>', '_blank');return false;" class="link_cover"></a>
			</div>
			<a href="<?php echo $trackingLink ?>https://podster.fm/rss.xml?pid=263"><img id="rss" src="zxchip_images/slice1.png"></a>
			<a href="<?php echo $trackingLink ?>https://vk.com/zxchip"><img id="vk" src="zxchip_images/slice2.png"></a>
			<a href="<?php echo $trackingLink ?>https://t.me/zxchip"><img id="tg" src="zxchip_images/slice3.png"></a>

		</div>

		</body>
		</html>
        <?php

        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}