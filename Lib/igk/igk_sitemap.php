<?php

/*
desc: site map file is used for default controller or domain controllers
*/

define("IGK_SITEMAP_REQUEST", 1);
require_once(dirname(__FILE__)."/igk_framework.php");

//will be used only for redirection context

if (!preg_match("/\/sitemap(\.xml)?/", igk_getv($_SERVER, "REDIRECT_URL"))){
	
	header("Status: 404");
	igk_show_error_doc(null, 404, false);
	igk_exit();
}
// igk_wln("site map function");
// igk_wln("domain ".igk_sys_domain_name());
// igk_wln("domain ".igk_sys_current_domain_name());
// igk_wln("domain ".igk_sys_is_subdomain());
// igk_wln("domain ".igk_io_domain_uri_name());
// igk_wln("domain ".igk_io_subdomain_uri_name());
// igk_wln("domain ".igk_io_is_subdomain_uri());
//goto root dir
$dir = realpath(dirname(__FILE__)."/../../");
// if (!defined("IGK_APP_DIR"))
	// define("IGK_APP_DIR", $dir);
chdir($dir);
include_once("index.php");
$s = igk_io_subdomain_uri_name();

// igk_wln($dir);
//chdir($dir);
// igk_wln($s);
// exit;

// if (empty($s))
// {
	// header("Content-Type: application/xml");
	// $v_gen = igk_getr("gen");
	// //global site map
	// if (!$v_gen && file_exists("sitemap.xml"))
	// {
		// igk_wl(file_get_contents("sitemap.xml"));
	// }else{
		// // if  ($v_gen == 1){
			// //generate site map
			// include_once("index.php");
			// $s = igk_tool_call("sitemap", igk_getr("s"));
			// if (!$s)
			// igk_wln("sitemap generation failed ");
			// igk_exit();			
		    // }
			// else{
				// igk_wl("<no />");
		// }
	// }
// }else{
	// call the site maps of the subdomain
	// igk_wln("site map of the subdomain ".$s);
	// change the request uri to sitemap
	// $_SERVER["REQUEST_URI"] = "/sitemap";		
	// include_once("index.php");
// }
?>