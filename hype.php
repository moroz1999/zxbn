<?php
include_once('zxbn.php');
include_once('hype_classes.php');

$banner = new Zxbn\HypeHtmlBanner();
$banner->setCacheDir(sys_get_temp_dir());
if (isset($_GET['reset'])) {
    $banner->setUseCache(false);
}
echo $banner->getHtml();
