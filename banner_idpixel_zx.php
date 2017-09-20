<?php
include_once('include/zxbn/zxbn.php');
include_once('include/zxbn/classes_idpixel.php');

$cacheDir = dirname(__FILE__) . '/cache/';

$banner = new Zxbn\IdpixelZxHtmlBanner();
$banner->setCacheDir($cacheDir);
if (isset($_GET['reset'])) {
    $banner->setUseCache(false);
}
if (isset($_GET['link'])) {
    $banner->setTrackingLink($_GET['link']);
}
echo $banner->getHtml();
