<?php
//services 

define("IGK_SERVICES_REQUEST", 1);
require_once(dirname(__FILE__)."/igk_framework.php");
$_SERVER["REQUEST_URI"] = "/services";	
$dir = realpath(dirname(__FILE__)."/../../");
// igk_wln($dir);
chdir($dir);
include_once("index.php");

$doc = igk_get_document("sys://documents/services");
$doc->Title = "Services - ".igk_app()->Configs->website_domain;
$doc->Favicon = new IGKHtmlRelativeUriValueAttribute("Lib/igk/Default/R/Img/cfavicon.ico");
$bbox = $doc->body->addBodyBox();
$bbox->ClearChilds();


///<summary>get ctrl from request uri</summary>
function igk_get_ctrl_ruri($uri){
	if(preg_match_all("/(\?|\&)c=(?P<name>(([a-z]|[_])([a-z0-9_]*))+)/i", $uri, $tab))
		return igk_getctrl(igk_getv($tab["name"],0));
		return null;		
}


$t = $bbox->addDiv();

$t->addSectionTitle("title.ListOfServices");

$ctn = $t->addContainer();

$ac = igk_getctrl(IGK_SYSACTION_CTRL);
$srv =0;
		$actions = $ac->getActions();
		foreach($actions as $k=>$v){
			
			if (preg_match_all("/^\^\/services\/(?P<name>([^\/\(])+)(\/)?/i", $k, $tab)){
				$ctrl = igk_get_ctrl_ruri($v);
				$r = $ctn->addRow();
				$dv= $r->addCol()->addDiv();
				$nn = igk_getv($tab["name"], 0);
				$dv->addDiv()->addA(igk_io_baseUri()."/services/".$nn)->setStyle("font-size:2.1em")->Content =  $nn;
				$dv->addDiv()->Content = $ctrl->getServiceDescription();
				$srv++;
			}
			// $s= igk_pattern_get_uri_from_key($k, $buri);	
			// $url->add("loc")->Content =$s;		
			//igk_wln($k . " | ".$s);
			// break;
		}
if ($srv==0){
	$ctn->addRow()->addCol()->Content= "NoServices found";
}
$doc->RenderAJX();
?>