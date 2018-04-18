<?php
//File: google.php
//REPRESENT GOOGLE API EXTENSION
//

//basic developper key
define("GOOGLE_GEO_APPKEY","AIzaSyAhIEla-i89QTvwdQTQSqwljvf6XsE4akk");


igk_css_reg_global_style_file(dirname(__FILE__)."/Styles/igk.google.pgcss");

define("IGK_GOOGLE_DEFAULT_PROFILE_PIC", "//lh3.googleusercontent.com/uFp_tsTJboUY7kue5XAsGA=s120");

igk_sys_lib_ignore(dirname(__FILE__)."/Api");


//-------------------------------------------------------------------------------------------
//-google packagage control
//-------------------------------------------------------------------------------------------
function igk_html_node_googleCircleWaiter(){
	$n = igk_createNode();
	$n->setClass("igk-google-circle-waiter");
	return $n;
}
function igk_html_demo_googleCircleWaiter($t){
	$t->addDiv()->Content = R::ngets("msg.pleasewait");
	$t->addgoogleCircleWaiter();
}

function igk_html_node_googleLineWaiter(){
	$n = igk_createNode();
	$n->setClass("igk-google-line-waiter");
	return $n;
}
function igk_html_demo_googleLineWaiter($t){
	$n = igk_createNode();
	$n->setClass("igk-google-line-waiter");
	$t->add($n);
	return $n;
}
function igk_google_get_drive_uri($folderid, $filename){
	//https://43d9d02b89325b2646d31068e27c7914eb5200b1.googledrive.com/host/0B6JpNoO3mfq3WWxWalhxWFdqYUE/photo.jpg
	return "//googledrive.com/host/".$folderid."/".$filename;
}

// igk_reg_doc_func("GoogleFont", function($doc, $uri){
		// $doc->addLink($uri);
// });

function igk_google_zonectrl(){
$CF = igk_ctrl_zone_init(__FILE__);	
return $CF;
}

function igk_google_zoneinit($g){	
require_once(IGK_LIB_DIR."/../api/google-api-client/vendor/autoload.php");
}


///<summary>add google font to theme</summary>
///<exemple>igk_google_addfont($doc, 'Roboto');</exemple>
function igk_google_addfont($doc, $family, $size=400, $temp=0){

	$g = str_replace(" ", "+", $family);	
	if (empty($g)){		
		igk_die("font name is empty");
	}
	$key = "https://fonts.googleapis.com/css?family={$g}:{$size}";	
	$doc->head->addCssLink($key, $temp);
	$doc->Theme->def[".google-".str_replace(" ", "-", $family)]="font-family:'{$family}', sans-serif;";
}


///<summary>download font to files
function igk_google_get_font($ft = "Open Sans", $dir=null){
	// igk_wln("get font data");
	$files = array();
	//make compatible
	$ft= str_replace(" ","+", $ft);
	$options = "";
	//link
	$url = "https://fonts.googleapis.com/css?family=".$ft.$options;	
	$g = igk_curl_post_uri($url);
	// igk_io_w2file("d:\\temp\\font.css", $g);
	// igk_text($g);	
	// $g = igk_io_read_allfile("d:\\temp\\font.css");
	
	if (preg_match_all("/url\s*\((?P<link>[^)]+)\)/", $g, $tab)>0){
		
		$dir = $dir ?? igk_io_basedir()."/R/Fonts/google";
		$dir = "{$dir}/{$ft}";
		IGKIO::CreateDir($dir);
		
		foreach($tab["link"] as $v){
			if (isset($files[$v])){
				continue;
			}
			$files[$v]=igk_io_dir($dir."/".basename($v));
			$b = igk_curl_post_uri($v);
			igk_io_w2file($dir."/".basename($v), $b);			
		}
	}
	return $files;
}




///<summary>add google follows us button</summary>
///rel: author or publisher
///height: 15,20,24
//annotation: none, vertical-bubble, bubble
function igk_html_node_googleFollowUsButton($id, $height=15, $rel="author", $annotation="none"){
	$n = igk_createXmlNode("g:follow");
	$n["class"]="g-follow";
	$n["href"] = "https://plus.google.com/".$id;
	$n["rel"] = $rel;
	$n["annotation"] = $rel;
	$n["height"] = $height;

	
	$b = igk_html_node_OnRenderCallback(igk_create_expression_callback(<<<EOF
\$doc = igk_getv(\$extra[0], "Document");
if (\$doc){
	\$d = \$doc->addTempScript('https://apis.google.com/js/platform.js',1);		
	\$d->activate("async");
	return 1;
}
return 0;
EOF
,array("n"=>$n)));
	$n->add($b);
	return $n;
}

function igk_html_demo_googleFollowUsButton($t){
	$t->addDiv()->Content = R::ngets("msg.followuseongoogle");
	//$t->addgoogleFollowUsButton("4544");
}




//register follow us service button
igk_community_register_followus_service("googleplus", function($cmd, $t, $v=null){
	switch($cmd){
		case "edit":
		$name= igk_getv(func_get_args(),3);
			$t->addInput("cl".$name,"text", igk_conf_get($v,"googleplus"));
		break;
		case "getlink":
			if (isset($v->googleplus))
				return "https://plus.google.com/".$v->googleplus;		
			return null;
		default: //view
			$targs = array_merge(explode(",",$v), array_slice(func_get_args(),3));
			call_user_func_array(array($t, "addGoogleFollowUsButton"), $targs);
			//$t->addGoogleFollowUsButton(igk_getv($targs,0));
		break;
	}
});


igk_register_service("google", "googlemap", function($cmd, $t, $config=null){
	switch($cmd){
		case "apiuri":
				$c = igk_conf_get($config, "Google/ApiKey");
				return "https://maps.googleapis.com/maps/api/js?key=".$c;
			break;
		case "apikey":
				//store the api key
			break;
			
	}
});





function igk_html_node_googleMapGeo($loc){
	$n = igk_createNode("div");
	$n["class"]="igk-winui-google-map";
	$q = $loc;
	$key = igk_google_apikey() ?? GOOGLE_GEO_APPKEY;
	$t="place";
	$lnk = "https://www.google.com/maps/embed/v1/{$t}?key={$key}&q={$q}";
	$iframe = $n->addHtmlNode("iframe");
	$iframe["class"] = "fitw";
	$iframe["frameborder"]="0";
	$iframe["src"] = $lnk;
	$iframe["onerror"]="event.target.innerHTML ='---failed to load map---';";
	return $n;	
}

function igk_html_demo_googleMapGeo($t){	
	$t->addDiv()->addPanelBox()->addCode()->Content = "\$t->addGoogleMapGeo(\"50.847311,4.355072\");";

	return $t->addGoogleMapGeo("50.847311,4.355072");	
}

function igk_google_jsmap_acceptrender_callback($n){
	return 1;
}

///<summary>add google maps javascript api node</summary>
function igk_html_node_googleJSMaps($data=null, $apikey=null){
	$apikey = $apikey ?? igk_google_apikey() ?? GOOGLE_GEO_APPKEY;
	$n = igk_createNode("div");
	$n["class"] = "igk-gmaps";

	$srv = igk_getv(igk_get_services("google"), "googlemap");
	$mapuri = $srv("apiuri", null, (object)["Google"=>(object)["ApiKey"=>$apikey ]]);
	
	// igk_ajx_include_script($mapuri);
	// $sr = igk_io_baseUri(dirname(__FILE__)."/Scripts/igk.google.maps.js");
	// igk_ajx_include_script($sr);
	
	$n->setCallback("AcceptRender", "igk_google_jsmap_acceptrender_callback");
	
	// wp_enqueue_script("googlemaps", $sr);
	$mapjs = igk_io_baseUri('Lib/igk/Ext/ControllerModel/GoogleControllers/Scripts/igk.google.maps.js');
	///'https://local.com/Lib/igk/Ext/ControllerModel/GoogleControllers/Scripts/igk.google.maps.js'
	
	$n->addScript()->Content = <<<EOF
(function(q){
var b = ['{$mapuri}'];
if (!igk._\$exists('igk.google.maps'))
	b.push('{$mapjs}');
igk.js.require(b).promise(function(h){ns_igk.google.maps.initMap(h);}, q);

})(igk.getParentScript());
EOF;
	$n["igk:data"]=$data ?? "{zoom:7, center:{lat:50.41438075875331, lng:4.904006734252908}}";
	return $n;
}

function igk_html_demo_googleJSMaps($t){	
	$n = $t->addGoogleJSMaps("{zoom:15,center:{lat:50.850402, lng:4.357879}}");
	$n["style"]="height:300px;";
	$t->addCode()->Content= htmlentities(<<<EOF
\$t->addGoogleJSMaps("{zoom:15,center:{lat:50.850402, lng:4.357879}}");
EOF
);
	
}


igk_sys_reg_componentname(
[
"googlemapgeo"=>"GoogleMapGeo",
"googlefollowusbutton"=>"GoogleFollowUsButton",
"googlejsmaps"=>"GoogleJSMaps"
]);

//register component file and reference directory
igk_sys_reg_referencedir(__FILE__, igk_io_dir(dirname(__FILE__)."/Data/References"));

?>