<?php
//author: C.A.D. BONDJE DOUE
//desc: global templates function
//date: 116/01/2017

include_once("View/uri.listener.pinc");

function igk_template_view_btn_loadandinstall($t, $ctrl){
	$dv = $t->addDiv();
	$dv->addSectionTitle("5")->setFont("arial")->Content = R::ngets("title.loadTemplate");
	$frm = $dv->addForm();
	$frm->addNotifyHost("template://msg");
	$frm->addajxPickFile($ctrl->getUri("loadtemplate_package_ajx"), "{complete:igk.ajx.fn.complete_ready('^form')}")->setClass("igk-btn igk-btn-default")->Content = R::ngets("btn.loadandinstall.templates");
}

function igk_template_init_env(){
	
	$dir = IGKIO::GetDir(igk_io_baseDir()."/".IGKApplicationManager::TEMPLATE_DIR);
	if (IGKIO::CreateDir($dir)){
		//
			IGKIO::WriteToFileAsUtf8WBOM($dir."/.htaccess", "deny from all");
	}
	
}

igk_reg_initenv_callback("igk_template_init_env");

function igk_template_default_script_content(){
	return file_get_contents(dirname(__FILE__)."/Data/scripts/application.default");
}
function igk_template_default_content($name){
	$c = dirname(__FILE__)."/Data/scripts/".$name.".default";
	if (file_exists($c))
		return file_get_contents($c);
	return 0;
}
///<summary>create a template setting</summary>
function igk_template_createTemplateInfo(){
		return  (object)array(
			"clPackageName"=>"com.igkdev.nothing",
			"clTitle"=>"Demo template",
			"clAuthor"=>"C.A.D BONDJE DOUE",
			"clDate"=>igk_date_now(),
			"clVersion"=>"1.0",
			"installClass"=>"init_php",
			"IsConfigurable"=>1, //get if the template can' be configured
			"IsDesignable"=>1, //get if the template can be designed in 
			"required-permission"=>array(),
			"required"=>array(),//store package required namespace			
			"uses-permission"=>array(array("name"=>"resolv.INTERNET"),array("name"=>"resolv.STORAGE")),
			"category"=>array((object)array("name"=>"portfolio"))
			// "inter-filter"=>array(
			// (object)array("action"=>"mono.android.intent.action.SEPPUKU",
			// "category"=>"mapl"),
			// (object)array("action"=>"mono.android.intent.action.SEPPUKU",
			// "category"=>"mapl")
		);
}

function igk_templage_get_dir($ns){
	return IGKIO::GetDir(igk_io_baseDir()."/".IGKApplicationManager::TEMPLATE_DIR."/".str_replace(".","/",$ns));
}


function igk_template_package_exists($ns){
	$ctrl = igk_template_mananer_ctrl();
	if ($ctrl->Db->connect()){
		$r = $ctrl->Db->select("tbigk_templates", array("clPackageName"=>$ns))->RowCount == 1;
		$ctrl->Db->close();
		return $r;
	}
	return 0;
}
function igk_template_load($f){
	$tns = igk_get_env("sys://templates/loaded", array());
	$d = igk_io_basedir();
	$f = IGKIO::GetDir($d."/".$f);
	if (!file_exists($f))
		return 0;
	if (strstr($f, igk_ob_get(get_included_files()),$f)==$f)
		return 1;
	
	$ns = str_replace(".","\\", igk_base_uri_name(dirname($f), igk_io_dir($d."/Templates")));	
	
	if (isset($tns[$ns]))
		return 1;
	//eval("namespace ".$ns."; ? >");//.file_get_contents($f));		
	//install global namespace entries
	//igk_io_save_file_as_utf8($f, str_replace("%NS%", "namespace {$ns};", file_get_contents($f)));	
	// igk_io_save_file_as_utf8("temp.ns","<?php namespace igkdev\com\games\jbgame; ? >");
	// include("temp.ns");
	$o = 0;
	try{
		//igk_loadcontroller(dirname($f));
		// igk_wln(igk_show_trace());
		$g = igk_io_getfiles(dirname($f), "/\.php/i");
		// igk_wln($g);
		// igk_wln("include script ::: ".$ns);
		igk_include_script($g, $ns);
		
		// include($f);
		$o = 1;
		$tns[$ns]=1;
		igk_set_env("sys://templates/loaded", $tns);
	}
	catch(Exception $ex){
		igk_ilog("Inclusion of file failed");
		$o =  0; // because of inclusion
	}
	// $f = get_declared_classes();
	// igk_wln("?".igk_count($f));
	// igk_wln("last : ".$f[count($f)-1]);
	return 1;
}
///<summary>create template application instance</summary>
function igk_template_create($n){
	$n = str_replace(".","\\",$n);
	
	if (class_exists($n)){
		return new $n();
	}
	return null;
}
///<summary>load template from namespace</summary>
function igk_template_load_ns($ns){
	//igk_wln(__FUNCTION__. " == ". $ns);
	$s = IGKIO::GetDir(IGKApplicationManager::TEMPLATE_DIR."/".str_replace(".","/",$ns)."/".IGKApplicationManager::MAIN_FILE);
	//$cl =$ns."\\Application";		
	if (igk_template_load($s)){
		return 1;
	}
	return 0;
}

spl_autoload_register(function($n){
	//igk_wln(__FILE__." auto load templates.... : ".$n);
	if (preg_match(IGK_IS_NS_IDENTIFIER_REGEX, $n)){	
		// igk_ilog("resolv template : ".$n);
		igk_template_load_ns(str_replace("\\", ".", dirname($n)));
	}	
}
);
function igk_template_name($packagename){
	return str_replace(".","\\",$packagename);
}
///<summary>create and init application template controller</summary>
function igk_template_create_ctrl($n){
	
	
	$tc = igk_template_mananer_ctrl();
	
	igk_assert_die(($tc==null) && !igk_sys_env_production(), ["message"=>"TemplateManager: controller [{$n}] not found or session data not valid. Failed to create [{$n}]", "code"=>0xAC0443]);
	
	if ($tc)
		return null;
	
	//igk_getctrl(igk_template_class_uri());
	$n = str_replace(".","\\",$n);
	$cl =$n."\\Application"; //base class
	if ($tc && $tc->created($n)){
		if (!class_exists($cl, 0)){//because of spl_register_function will not be called
			igk_template_load_ns($n);
		}
		return $tc->getCreatedCtrl($n);
	}	
	$o = null;
	//igk_wln("init ------------------- : ".$n);
	if (class_exists($n, 0)){
		$o =  new $n();
	}else{
		//load template application file
		$s = IGKIO::GetDir(IGKApplicationManager::TEMPLATE_DIR."/".str_replace(".","/",$n)."/".IGKApplicationManager::MAIN_FILE);
	
		if (igk_template_load($s)){
			// igk_wln($s);
			// igk_wln($n);

			if (class_exists($cl)){
				$o = new $cl();		
			}
		}
	}
	if ($o && $tc->register($o, $n)){
		return $o;
	}	
	return null;
}

///<summary>function that return a list of avaible controller</summary>
function igk_template_get_ctrls(){
	$g = igk_getctrl(igk_template_class_uri());
	$t = array();
	$q = $g->Db->getTemplateNames();
	if($q)
	foreach($q->Rows as $k=>$v){
			$t[] = $v->clPackageName;
	}
	return $t;
}


//view function 
function igk_template_view_badge($n, $ctrl){
	$n->addDiv()->setContent("set here bagde for many resolution");
	
	$row = $n->addDiv()->addRow();
}
function igk_template_view_assets_favicon($n, $ctrl){
	$n->addDiv()->setContent("set the default favicon");
	$row = $n->addDiv()->addRow();
	
}

function igk_template_is_template_class($ctrl){
	return igk_reflection_class_extends($ctrl, 'IGKApplicationBase');
}
?>