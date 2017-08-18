<?php
include_once('zxbn.php');
include_once('classes_zxpkru.php');

$cacheDir = dirname(__FILE__) . '/cache/';

$banner = new Zxbn\ZxpkruMainHtmlBanner();
$banner->setCacheDir($cacheDir);
if (isset($_GET['reset'])) {
    $banner->setUseCache(false);
}
if (isset($_GET['link'])) {
    $banner->setTrackingLink($_GET['link']);
}
echo $banner->getHtml();
