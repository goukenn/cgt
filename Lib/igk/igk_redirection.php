<?php
/*
//author : C.A.D. BONDJE DOUE
//igk_redirection.php
//description: redirection handler
//             this file manage error on framework. by redirecting data if IGKSystemUri contains reference
//             or framework contains Sites/....
*/


//301 Moved Permanently


define("IGK_REDIRECTION", 1);
require_once(dirname(__FILE__)."/igk_framework.php");

// igk_wln("redirect");
// igk_wln($_REQUEST);
// igk_exit();
$igk_index_file =dirname(__FILE__)."/../../index.php";




chdir(dirname(__FILE__)."/../../");
// for fast cgi
// header("Status: 200 OKo"); 
// header($_SERVER['SERVER_PROTOCOL'] . " 200 OoK");
if (!file_exists($igk_index_file))
{
	igk_wl("<div>/!\\ Index file not exist. please reinstall the igk <a href='./Lib/igk/igk_init.php'>balafon</a> core lib.</div>");
	return;
}
chdir(dirname($igk_index_file));
header("Status: 200 OK"); // for fast cgi
header($_SERVER['SERVER_PROTOCOL'] . " 200 OK");
// define("IGK_FORCSS", 1);  //disable redirection
//init by include index file

include_once($igk_index_file);
unset($igk_index_file);
if (!defined('IGK_APP_DIR'))
	define("IGK_APP_DIR", getcwd());
$defctrl = igk_get_defaultwebpagectrl();
$app = igk_app();
$code = igk_getv($_REQUEST, "code", 902);//no code found

$query = igk_getv($_SERVER, "REQUEST_URI");
$redirect = igk_getv($_SERVER, "REDIRECT_URL");
$redirect_status = igk_getv($_SERVER, "REDIRECT_STATUS");
$r = igk_getv($_SERVER, "REDIRECT_REQUEST_METHOD");


switch($code){
	case 901:
		//OVH specilally redirect /sitemap to /sitemap.xml
		if ($redirect == "/sitemap.xml"){
			include(dirname(__FILE__)."/igk_sitemap.php");
			igk_exit();
		}
		$rx = "#^(".igk_io_baseUri().")?\/!@(?P<type>".IGK_IDENTIFIER_RX.")\/\/(?P<ctrl>".IGK_FQN_NS_RX.")\/(?P<function>".IGK_IDENTIFIER_RX.")(\/(?P<args>(.)*))?$#i";

		$c = preg_match_all($rx, $redirect, $ctab);
		if ($c>0){		
			igk_getctrl(IGK_SYSACTION_CTRL)->invokePageAction(
			$ctab["type"][0],
			$ctab["ctrl"][0],
			$ctab["function"][0],
			$ctab["args"][0]
			);
			return;
		}
	break;
	case 904:
		//in most case file access not found
		header("Status: 404");
		header("HTTP/1.0 404 Not Found");
		igk_exit();
		break;
	case 503:
		//access to path is available. but server response Forbiden
		header("Status: 403");
		header("HTTP/1.0 403 Forbiden");
		igk_sys_show_error_doc(503);
		igk_exit();
		break;
	case 404:
		if (igk_getr("m")=="config"){
			igk_navto("/Configs");
			exit;
		}
	break;
}

$args = igk_getquery_args(igk_getv($_SERVER,"REDIRECT_QUERY_STRING"));
$_REQUEST = array_merge($_REQUEST, $args);

//$_REQUEST["server_error"] = $_SERVER;
	if ($r == "POST" && ($code!=901))
	{	
		igk_notifyctrl()->addMsg("Posted data are lost . ".$code);
	}
	
	///------------------------------------
	///RUN REQUEST URI
	///------------------------------------	
	
	$app = igk_app();	
	$v_ruri = igk_io_baseRequestUri();	
	
	// $v_ruri = str_replace("//", "/", $v_ruri);
	// $_SERVER["REQUEST_URI"] = $v_ruri;
	
	//igk_wln($v_ruri);
	// igk_wln("rootbase request uri ". igk_io_rootBaseRequestUri());//$v_ruri);
	// igk_wln($_REQUEST);
	// igk_wln($_SERVER);
	// exit;
	$tab = explode('?',$v_ruri);
	$uri = igk_getv($tab, 0);
	$params = igk_getv($tab, 1);
	$page = "/".$uri;
	$lang = null;
	//primitive actions	

	$actionctrl = igk_getctrl(IGK_SYSACTION_CTRL);

	if (igk_io_handle_redirection_uri($actionctrl, $page, $params, 1))
		return;
	
	// if ($actionctrl  && ($e = $actionctrl->matche($page)))
	// {	
		// $app->Session->RedirectionContext = 1;
		// try{		
			// $actionctrl->invokeUriPattern($e);
		// }catch(Exception $e){
			// igk_show_exception($e);			
			// igk_exit();
		// }
		// return;
	// }
	
	try{
		//default page management
		if (igk_sys_ispagesupported($page))
		{		
			$tab = $_REQUEST;
			igk_resetr();
			$_REQUEST["p"] = $page;
			$_REQUEST["l"] = $lang;
			$_REQUEST["from_error"] = true;
			$app->ControllerManager->InvokeUri();
			igk_render_doc();
			igk_exit();
		}	
	}
	catch(Exception $ex){		
	}
	if (!empty($page)){	

	//virtual web drive
	$dir = getcwd()."/Sites/".$page;
	if (is_dir($dir)){
		igk_ilog("dir exists".$dir);
		//$s = igk_io_currentRelativeUri("/Sites/".$page);		
		chdir($dir);
		R::ChangeLang($lang);
		$IGK_APP_DIR = $dir;
		//remove all variable
		include("index.php");
		igk_exit();
	}
		}
//uri
$page = "/".$uri;
// igk_io_check_request_file($page, function(){
	//destroy session if for no-cache
	// session_destroy();
	// session_write_close();
// });
//try to handle by default web ctrl
if ($defctrl!==null){
	if ($defctrl->handle_redirection_uri($page)){		
		igk_exit();
	}	
}
$suri = igk_getv($_SERVER, "REQUEST_URI");
if (preg_match("/\.(jpeg|jpg|bmp|png|gkds)$/i", $suri))
{
	header("Status: 301"); // for fast cgi
	header($_SERVER['SERVER_PROTOCOL'] . " 301 permanent");	
	igk_exit();
}
		

igk_set_header(301);
igk_show_error_doc(null, $code, $redirect);
igk_exit();
?>