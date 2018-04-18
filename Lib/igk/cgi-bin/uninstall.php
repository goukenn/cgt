#!/user/bin/php
<?php

define("APP_DIR", dirname(__FILE__));
if (!defined("IGK_FRAMEWORK")){
require_once(APP_DIR."/../igk_framework.php");
}
igk_wln("Init uninstall.....");

$dir = realpath(APP_DIR."/../../../");




igk_wln("basedir : ".$dir);
IGKIO::RmDir($dir."/Caches");
IGKIO::RmDir($dir."/Data");
IGKIO::RmDir($dir."/Lib/igk");

igk_wln("done");

?>