<?php
function igk_api_syncfrom_ctrl($ctrl, $server=null){

		$server = $server ? $server: 
		igk_getr("clServer",IGK_WEB_SITE ); 
		if(empty($server)){
			igk_navto($ctrl->getAppUri(""));
			igk_exit();
		}
		if (!IGKValidator::IsUri($server)){
			$server = "http://".$server;
		}
	
		$p = igk_getctrl("api")->datadb("syncfrom", $server, $ctrl->Name, $ctrl->User->clLogin);		
		igk_setheader(200, IGK_HTML_CONTENT_TYPE);	
		if ($p){
			igk_wln("sync ok" . $p);
			igk_notifyctrl()->addMsg("Synchronize ok");
			
		}else{
			igk_wln("bad !::: sync not ok");
			igk_notifyctrl()->addError("Synchronize not ok");
		};
		igk_navto($ctrl->getAppUri(""));
}

function igk_api_syncto_ctrl($ctrl, $server=null){
	$server = $server ? $server: igk_getr("clServer"); //support uri
		if (igk_server_request_onlocal_server()){
			//request : sync to local server not allowed
			igk_navto($ctrl->getAppUri(""));
			igk_exit();
		}
		
		// igk_wln($_SERVER);
		// exit;
		if(empty($server)){
			igk_navto($ctrl->getAppUri(""));
			igk_exit();
		}
		
		if (!IGKValidator::IsUri($server)){
			$server = "http://".$server;
		}
			// igk_server
		//
		//
		//for demonstration
		$p = igk_getctrl("api")->datadb("syncto", $server
		///*."/igkdev"
		, 
		$ctrl->Name, $ctrl->User->clLogin);		
		igk_setheader(200, IGK_HTML_CONTENT_TYPE);	
		if ($p){
			igk_wln("sync ok" . $p);
			igk_notifyctrl()->addMsg("Synchronize ok");
			
		}else{
			igk_wln("bad !::: sync not ok");
			igk_notifyctrl()->addError("Synchronize not ok");
		};
}

function igk_api_build_ctrl_manifest($ctrl, $node){
	$node->Attributes->Set("xmlns", IGK_WEB_SITE);
	$node->addNode("Source")->Content = gethostbyname(igk_getv($_SERVER, "SERVER_NAME"));
	$node->addNode("CtrlSourceName")->Content = $ctrl->Name;//gethostbyname(igk_getv($_SERVER, "SERVER_NAME"));
}
?>