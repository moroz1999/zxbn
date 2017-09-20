<?php
include_once('include/zxbn/zxbn.php');
include_once('include/zxbn/classes_hype.php');

$cacheDir = sys_get_temp_dir() . '/cache/';

$banner = new Zxbn\HypeHtmlBanner();
$banner->setCacheDir($cacheDir);
if (isset($_GET['reset'])) {
    $banner->setUseCache(false);
}
if (isset($_GET['link'])) {
    $banner->setTrackingLink($_GET['link']);
}
echo $banner->getHtml();
