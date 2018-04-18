<?php
function igk_html_node_designerButton(){
	$n = igk_createNode("div");
	$n["class"] = "igk-dsg-btn";
	return $n;
}

///<summary>get the current global controller title</summary>
function igk_get_current_ctrl_title(){	
	return igk_sys_getconfig("website_title");
}


function igk_designer_node_visible($node){
	$doc = igk_get_env(IGK_ENV_CURRENT_RENDERING_DOC);
	return igk_designer_is_active($doc);
}
function igk_designer_is_active($doc=null, $o=null){	
	
	$v = 0;
	// exit;
	$sk = "sys://designMode/off";
	$doc = $doc ?? igk_get_env(IGK_ENV_CURRENT_RENDERING_DOC);
	if ($doc){
		$s = $doc->getParam(IGK_DOC_ID_PARAM);
		if (($s === IGK_DOC_ERROR_ID) || $doc->getParam($sk)){			
			$v= false;
		}
	}else
		$v=  !igk_get_env($sk) && !igk_sys_cache_require() && igk_is_conf_connected() && !igk_sys_is_subdomain() && (igk_app()->CurrentPageFolder != IGK_CONFIG_PAGEFOLDER);	
	
	// igk_wln("is designer active ?".$v);
	return  $v; 
}
function igk_designer_off($doc, $v=true){
	$doc->setParam("sys://designMode/off", $v?1:null);
}
?>