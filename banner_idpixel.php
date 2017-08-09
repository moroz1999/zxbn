<?php
include_once('zxbn.php');
include_once('classes_idpixel.php');

$banner = new Zxbn\IdpixelHtmlBanner();
$banner->setCacheDir(sys_get_temp_dir());
if (isset($_GET['reset'])) {
    $banner->setUseCache(false);
}
echo $banner->getHtml();
