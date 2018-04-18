<?php
require_once("igk_framework.php");
$file =dirname(__FILE__)."/../../index.php";
IGKApp::$BASEDIR = dirname($file);

$d =igk_getr("d");
//load content
file_put_contents(
    igk_io_currentBasePath($d), 
    file_get_contents("php://input")
  );
?>