<?php
include_once('include/zxbn/zxbn.php');
include_once('include/zxbn/classes_cron.php');

$cacheDir = dirname(__FILE__) . '/cache/';

if (isset($_GET['pop'])) {
    $banner = new Zxbn\CronPopHtmlBanner();
} else {
    $banner = new Zxbn\CronNewHtmlBanner();
}
$banner->setCacheDir($cacheDir);
if (isset($_GET['reset'])) {
    $banner->setUseCache(false);
}
if (isset($_GET['link'])) {
    $banner->setTrackingLink($_GET['link']);
}
echo $banner->getHtml();
