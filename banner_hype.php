<?php
include_once('zxbn.php');
include_once('classes_hype.php');

$banner = new Zxbn\HypeHtmlBanner();
$banner->setCacheDir(sys_get_temp_dir());
if (isset($_GET['reset'])) {
    $banner->setUseCache(false);
}
if (isset($_GET['link'])) {
    $banner->setTrackingLink($_GET['link']);
}
echo $banner->getHtml();
