<?php

$dir = dirname(__FILE__);
require_once($dir."/igk_framework.php");
//set the base dir to be the index directory
IGKApp::$BASEDIR = realpath( $dir."/../../");
session_start();

class IGKPlateformManagement extends IGKObject
{	
	public $bbox;
	
	public function __construct(){		
	}
	public function invoke($m, $args=null){
		if (method_exists(__CLASS__, $m))
		{
			if (empty($args))
				$args = array();
			call_user_func_array(array($this, $m), $args);
		}
		else 
			$this->bbox->addNotifyBox("danger")->Content = "method not found";
	}
	
	public function clearcache(){
		IGKControllerManagerObject::ClearCache();
		$this->bbox->addNotifyBox("danger")->Content = "cache clear";
	}
	public function connect(){
		igk_wln("connect");
	}
	public function logout(){
	}
	
	
}
$pman = new IGKPlateformManagement();
$doc = new IGKHtmlDoc(IGKApp::getInstance(), true);
$bbox = $doc->body->addBodyBox();

$pman->bbox = $bbox;

$cm = new IGKUriPatternMatcher();
$ca = "^/(:function(/:params+)?)?";
$p = igk_getv($_SERVER, "PATH_INFO");
$muri = new IGKSystemUriActionPatternInfo(array(
	"action"=>$ca,
	"value"=>null,
	"pattern"=> $cm->getPattern($ca),
	"uri"=>$p,
	"keys"=> igk_str_get_pattern_keys($ca)
));




if ($muri->matche($p))
{	
	$k = $muri->getParams();
	$cm = igk_getv($k, "function");
	$pm = igk_getv($k, "params");
	$pman->invoke($cm, $pm);
}

switch(igk_getv($_SERVER , 'REQUEST_METHOD'))
{
	case "POST":
		break;
	case "GET":
		$bbox->addDiv()->addContainer()->add("blockquote")->Content = "Get operation not ALLOWED";		
		break;
}
$doc->RenderAJX();
?>