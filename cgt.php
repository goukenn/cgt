<?php
/*
Plugin Name: CGT - PIVOT Plugin Manager
Version:1.0
Date: 01/12/2017
Author: C.A.D. BONDJE DOUE / TreenityConsulting  / CGT
Author Uri: https//igkdev.be
Description: Intégration des services PIVOT associciés au Commissariat Génaral au Tourisme (Belgique-Benelux).
*/



define("IGK_CGT_PLUGIN_DIR", dirname(__FILE__));

include_once(IGK_CGT_PLUGIN_DIR."/cgt-common.phlib");

///<summary>Entry cgt plugin class</summary>
class IGK_CGT_Plugin{
	public function __construct(){	
		//include all .plib files present on the plugin dir
		igk_wp_registerlib(IGK_CGT_PLUGIN_DIR."/");	
	}
}



//init global value if not loaded
global $action;
new IGK_CGT_Plugin();




?>