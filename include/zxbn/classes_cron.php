<?php

namespace Zxbn;

class CronPopHtmlBanner extends GroupedHtmlBanner
{
    protected $listUrl = 'https://cronosoft.fwscart.com/Spectrum_cassette_tape/cat5357733_4119462.aspx';
    protected $limit = 1;
    protected $type = 'cronPop';
    protected $parserType = '\Zxbn\CronPopParser';
}

class CronNewHtmlBanner extends GroupedHtmlBanner
{
    protected $listUrl = 'https://cronosoft.fwscart.com/Spectrum_cassette_tape/cat5357733_4119462.aspx';
    protected $limit = 1;
    protected $type = 'cronNew';
    protected $parserType = '\Zxbn\CronNewParser';
}

class CronPopParser extends HtmlParser
{
    protected $blockId = 'top-sellers';

    public function parseHtml($html, $limit)
    {
        $data = [];
        if ($xPath = $this->getXpath($html)) {
            $postNodes = $xPath->query("//section[contains(@id,'" . $this->blockId . "')]");
            if ($postNodes->length) {
                foreach ($postNodes as $postNode) {
                    if ($articleNodes = $xPath->query(".//article", $postNode)) {
                        foreach ($articleNodes as $articleNode) {
                            $itemInfo = [
                                'title' => '',
                                'image' => '',
                                'price' => '',
                                'text'  => '',
                                'link'  => '',
                            ];
                            if ($h5Nodes = $xPath->query(".//h5", $articleNode)) {
                                if ($h5Node = $h5Nodes->item(0)) {
                                    $itemInfo['title'] = $h5Node->textContent;
                                }
                            }
                            if ($spanNodes = $xPath->query(".//span[contains(@class, 'price')]", $articleNode)) {
                                if ($spanNode = $spanNodes->item(0)) {
                                    $itemInfo['price'] = $spanNode->textContent;
                                }
                            }
                            if ($imgNodes = $xPath->query(".//img", $articleNode)) {
                                $imgNode = $imgNodes->item(0);
                                $itemInfo['image'] = $imgNode->getAttribute('src');
                            }
                            if ($aNodes = $xPath->query(".//a[contains(@class, 'button')]", $articleNode)) {
                                $aNode = $aNodes->item(0);
                                $itemInfo['link'] = $aNode->getAttribute('href');
                            }
                            $data[] = $itemInfo;
                        }
                    }
                    break;
                }
            }
        }
        return $data;
    }
}

class CronPopTemplate
{
    public function render($data, $trackingLink)
    {
        ob_start();

        ?>
		<!DOCTYPE HTML>
		<html>
		<head>
			<link href="//fonts.googleapis.com/css?family=Electrolize:regular" rel="stylesheet" type="text/css" />
			<style>
				html, body, .main {
					width: 240px;
					height: 320px;
					padding: 0;
					margin: 0;
					overflow: hidden;
					position: relative;
					background-color: #fff;
					color: #6600FF;
					font-family: Electrolize, sans-serif;
					font-weight: normal;
					font-style: normal;
					line-height: 24px;
					cursor: auto;
				}

				h4 {
					font-family: Electrolize, sans-serif;
					font-weight: bold;
					font-style: normal;
					color: #666666;
					margin: 0;
				}

				.product-box {
					border-bottom: 1px solid #e3e3e3;
					overflow: hidden;
					padding: 5px;
				}

				.product-box img {

					float: left;
					margin-right: 5px;
					height: 55px
				}

				h5 a {
					font-weight: normal;
				}

				a {
					color: #0067b0;
					text-decoration: none;
					line-height: inherit;
				}

				a {
					background: transparent;
				}

				a img {
					border: none;
				}

				img {
					display: inline-block;
					vertical-align: middle;
					max-width: 100%;
					height: auto;
					border: 0;
				}

				.product-box h5 {
					margin-bottom: 0;
					min-height: 35px;
					height: auto;
					line-height: 1rem;
					font-family: 'Open Sans', sans-serif !important;
					max-height: 35px;
					overflow: hidden;
					margin-top: 2px;
					font-size: 0.775rem;
				}

				.price {
					font-weight: bold;
				}

				button.tiny, .button.tiny {

					padding: 0.275rem 0.65rem 0.3375rem;
					font-size: 0.6875rem;

				}

				.add_to_cart_button, #add_to_cart, .button {

					float: right;
					margin-left: 10px;
					margin-right: 10px;
					border-radius: 0;

				}

				.button {
					display: inline-block;
					border-style: solid;
					border-width: 0px;
					cursor: pointer;
					font-family: Electrolize, sans-serif;
					font-weight: normal;
					line-height: normal;
					margin: 0;
					position: relative;
					text-decoration: none;
					text-align: center;
					padding: 1rem 2rem 1.0625rem;
					font-size: 1rem;
					border-color: #402362;
					color: #FFFF00;
					transition: background-color 300ms ease-out;
				}

				.button:hover {
					opacity: 0.8;
				}

				.icon-basket {
					width: 15px;
					height: 13px;
					background: url("/images/cron/basket.png") no-repeat center #502c7b;
				}

				.right {
					float: right !important;
				}
			</style>
		</head>
		<body>
		<div class="main">
			<header class="header" role="banner">
				<a class="logo_link"
				   href="//zx-pk.ru/forum.php"
				   onclick="window.open('//cronosoft.fwscart.com/Spectrum_cassette_tape/cat5357733_4119462.aspx?utm_source=zxbn&utm_medium=banner&utm_campaign=zxbn', '_blank');return false;"
				><img class="logo" src="/images/cron/cronologo-gif.png" /></a>
			</header>
			<div class="left-col-block">

                <?php
                foreach ($data as $info) {
                    ?>
					<article class="product-box clearfix">
						<a
							onclick="window.open('<?php echo $trackingLink . $info['link']; ?>', '_blank');return false;"
							href="<?php echo $trackingLink . $info['link']; ?>"
						><img src="<?php echo $info['image']; ?>"></a>
						<h5>
							<a
								onclick="window.open('<?php echo $trackingLink . $info['link']; ?>', '_blank');return false;"
								href="<?php echo $trackingLink . $info['link']; ?>"
							><?php echo $info['title']; ?></a>
						</h5>
						<span class="price">£5.00</span>
						<a
							onclick="window.open('<?php echo $trackingLink . $info['link']; ?>', '_blank');return false;"
							href="<?php echo $trackingLink . $info['link']; ?>"
							class="button tiny right icon-basket"></a>
					</article>
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

class CronNewParser extends CronPopParser
{
    protected $blockId = 'new-products';
}

class CronNewTemplate extends CronPopTemplate
{
}