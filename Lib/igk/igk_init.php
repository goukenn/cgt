<?php
//create index if not exist
//try to destroy session

//define constant
define("IGK_INIT",  1);//init application
@session_start();

@session_destroy();

// echo "php version : ".phpversion();
// exit;
require_once(dirname(__FILE__)."/igk_framework.php");
$file =dirname(__FILE__)."/../../index.php";
$htaccess =dirname(__FILE__)."/../../.htaccess";
$bdir = realpath(dirname($file));
// IGKApp::$BASEDIR = $bdir;
$redirect = igk_getr("redirect", 1);
$wizeinstall = igk_getr("wizeinstall", !file_exists(IGKIO::GetDir($bdir."/Data/configure")));

IGKControllerManagerObject::ClearCache($bdir."/".IGK_CACHE_FOLDER);

function igk_init_setparam($igk, $wizeinstall){
	$s = $igk->Session;
	$s->setParam("igk_wizeinstall", $wizeinstall);
	$s->setParam("igk_init", 1);
	$s->setParam("datetime", igk_date_now());
}

if (file_exists($file))
{	
	include_once($file);	
	if ($redirect)
	{
		igk_init_setparam(IGKApp::getInstance(),$wizeinstall);
		header("Location: ../../Configs");		
	}
}
else{
	$indexsrc = igk_getbaseindex_src();
	if (igk_io_save_file_as_utf8($file, $indexsrc , true))
	{
		$file = realpath($file);
		define("IGK_APP_INIT", 1); //define app init to diseable domain redirection
		include_once($file);
		igk_io_save_file_as_utf8($htaccess, igk_getbase_access(), true);
		 if ($redirect)
		 {
			//redirect to configs pages
			igk_init_setparam(IGKApp::getInstance(),$wizeinstall);		
			header("Location: ../../Configs");			
		 }
	}
}
igk_exit();
?>