<?php

//method 1: site map generator
final class IGKSitemapGeneratorTools extends IGKToolCtrlBase
{

	public function getImageUri(){ 		
		$uri = igk_html_resolv_img_uri(igk_io_baseDir("Lib/igk/Default/R/Img/pics_48x48/tool_sitemapgen.png"));
		return $uri;
	}
	public function doAction()
	{
		igk_sys_gen_global_sitemap(1);
		
		igk_notifyctrl()->addMsgr("msg.sitemapgenerated");
		$this->refreshToolView();
		igk_navtocurrent();
		exit;
	}	
}

function igk_sys_gen_global_sitemap($store=0){
	// if (!igk_is_conf_connected()){
		
		// igk_notifyctrl()->addWarningr("mgs.notallowed");
		// return false;
	// }
	
		$n = igk_html_node_IGKSiteMap();
		$pages = igk_sys_pagelist();
		$buri = igk_str_rm_last(igk_io_baseUri(),'/');
		// foreach($pages as $k=>$v)
		// {
			// $url = $n->addNode("url");
			// $url->add("loc")->Content = igk_html_uri($buri."/".$v);
		// }
		$ac = igk_getctrl(IGK_SYSACTION_CTRL);
		$actions = $ac->getActions();
		foreach($actions as $k=>$v){
			$url = $n->addNode("url");
			$s= igk_pattern_get_uri_from_key($k, $buri);	
			$url->add("loc")->Content =$s;		
			// igk_wln($k . " | ".$s);
			// break;
		}
		// exit;
		$options = igk_xml_create_render_option();
		$options->Indent=1;
		$uri = igk_io_baseUri()."/Lib/igk/Styles/sitemap.xsl";
		igk_wl(igk_xml_header());

		
		//!!!!!immportant note xml-stylesheet not working use xsl-stylesheet

igk_wl(<<<EOF
<?xml-stylesheet type="text/xsl" href="{$uri}"?>
EOF
);
	if ($store){
		$o = $n->Render($options);
		igk_io_save_file_as_utf8(igk_io_baseDir("sitemap.xml"), $o);
		header("Content-Type: application/xml");
		igk_wl($o);
	}else
		$n->RenderXML($options);
}


//method 2: callback registration
//register  sitemap tool
igk_tool_reg("sitemap", array("ImageUri"=>"", "Action"=>function(){
		if (func_num_args()>0)
			igk_sys_gen_global_sitemap(func_get_arg(0));
		else
			igk_sys_gen_global_sitemap();
		return true;
	
}));
// igk_sys_reg_tools("size_view", array("ImageUri"=>"", "Action"=>function(){
	
	
// }));


?>