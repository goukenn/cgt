<?php
//------------------------------------------------------------------
//author: C.A.D BONDJE DOUE
//file : IGKApplicationManager.php
//desc: controller of template
//------------------------------------------------------------------



function igk_io_tempfile($prefix='tmp'){
	return tempnam(sys_get_temp_dir(), $prefix);
}


function igk_template_class_uri(){
	return igk_base_uri_name(__FILE__); 
}


// igk_wln(igk_template_class_uri());


function igk_view_render_if_visible($ctrl){
	if ($ctrl->IsVisible)
		$ctrl->View();	
}

function igk_template_mananer_ctrl(){
	
	$n = igk_template_class_uri();
	return igk_getctrl($n);
}


final class igk_template_expressions{
	const PACKAGENAME = "%{packagename}";
	const REQUIRE = "%{required(?)";
}

class IGKApplicationManager extends IGKControllerBase{
	private $md_db;
	private $m_createdCtrls;
	private $m_config_view;
	
	const TEMPLATE_DIR = "Templates";
	const MAIN_FILE = "Application.php";
	const MANIFEST_TAGNAME = "balafon_manifest";
	
	public function getName(){ 		
		return igk_template_class_uri();
	}
	
	public function getDb()
	{
		if ($this->md_db==null)
			$this->md_db = new IGKApplicationManagerDbUtility($this);			 
		return $this->md_db;
	}
	///<summary>.ctr</summary>
	function __construct(){
		parent::__construct();
		// igk_wln("done ".igk_base_uri_name(__FILE__));
		$this->m_createdCtrls = array();
	}
	public function getDataAdapterName(){
		return IGK_MYSQL_DATAADAPTER;
	}
	
	public function initDb(){	
		parent::initDb();
	}
	public function getUseDataSchema(){
		return true;
	}
	
	public function getPackageStoreDir(){
		return $this->getDataDir()."/packages";
	}
	public function getUser(){
		return $this->App->Session->User;
	}
	public function getConfigView(){
		if (empty($this->m_config_view)){
			$this->m_config_view = "config";
		}
		return $this->m_config_view;
	}
	
	public function InitComplete(){
		parent::InitComplete();
		//igk_notification_reg_event("sys://event/config_user_changed", array($this, "View"));
		$cb = igk_create_func_callback('igk_view_render_if_visible',  array($this));
		igk_notification_reg_event("sys://event/config_user_changed", $cb);
		
// igk_wln($this->getUri());
		
		//test for notification
			// igk_debug(1);
		// igk_wln("?".//, "View"));
		// igk_wln("?".igk_notification_reg_event("sys://event/config_user_changed","igk_view_render_if_visible"));//, "View"));
		// igk_wln("?".igk_notification_reg_event("sys://event/config_user_changed","igk_view_render_if_visible"));//, "View"));
		
		
		// igk_notification_unreg_event("sys://event/config_user_changed",$cb);
		// igk_notification_unreg_event("sys://event/config_user_changed","igk_view_render_if_visible");
	
		// igk_notification_push_event("sys://event/config_user_changed", null);
		// igk_debug(0);
		// session_destroy();
		// exit;
		
		// igk_wln($this->getUri("install_template_package_ajx"));
		
	}
	
	
	
	//private
	private function _get_manifest_obj($fname){
		
		$e = igk_zip_unzip_file_content($fname, ".manifest");
		if ($e ==null){
			//no manifest file found
			igk_ilog("no manisfest file found in the package {$fname}");
			return null;
		}
		
		//igk_debug(1);
		//igk_wln($e);
		$d = igk_createXMLNode("dom");		
		//igk_wln(get_class($d));		
		$d->load($e);
		//igk_debug(0);
		//print_r($d->Attributes);
		
		// exit;
		$t = igk_html_select($d, IGKApplicationManager::MANIFEST_TAGNAME);
		if (igk_count($t)!=1){
			igk_ilog("invalid manifest .".$f);
			return;
		}
		$manifest = $t[0];
		$m_obj = igk_xml_to_obj($manifest);
		return $m_obj;
	}
	
	
	
	public function is_set_as_default($p){
		//igk_ilog(__METHOD__);
		if ($p){
			return ($p->clPackageName == igk_app()->Configs->web_pagectrl);
		}
		return 0;
	}
	public function make_default_ajx($id = null){
		$id = $id ?? igk_getr("id");
		if ($this->Db->connect()){
			$row = $this->Db->selectSingleRow(TBIGK_TEMPLATES, $id);
			if ($row){
				igk_app()->Configs->web_pagectrl = $row->clPackageName;
				igk_save_config();
			}
			$this->Db->close();
		}
		$this->__renderConfigPage_ajx();
	}
	
	
	public function created($n){
		return isset($this->m_createdCtrls[$n]);
	}
	public function getCreatedCtrl($n){
		return $this->created($n) ? $this->m_createdCtrls[$n] : null;
	}
	///<summary>register  template controller</summary>
	///<param name="obj">controller template application</summary>
	///<param name="n">name of the global namespace uri</summary>
	public function register($obj, $n){
		if ($obj===null){
			if (isset($this->m_createdCtrls[$n])){
			unset($this->m_createdCtrls[$n]);
			return 1;
			}
		}
		else{//replace ctrl 
			if (igk_reflection_class_extends(get_class($obj) , "IGKTemplateBase")){
				$this->m_createdCtrls[$n] = $obj;
				
				//init complete object
				IGKControllerBase::InvokeInitCompleteOn($obj);
				
				
				return 1;
			}
		}
		return 0;
	}
	public function getIsVisible(){
		return parent::getIsVisible() && igk_is_conf_connected();
	}
	
	public function showConfig($t){		
		//load controller view
		igk_ctrl_view($this, $t, $this->ConfigView);
	}
	
	public function get_css_class_ajx(){
		igk_wln(igk_css_str2class_name($this->Name));
	}
	// // public function demo_configpage_ajx(){
		// $this->__renderConfigPage_ajx();
		// // $this->genTemplates(null);
		// // igk_resetr();
		// // $this->__renderConfigPage_ajx();					
		// // igk_exit();
	// // }
	public function getAssetsUri($row){
		//igk_ilog($row);
		return $this->getUri("assets&q={$row->clPackageName}");
	}
	public function assets(){
		$q = igk_getr("q");
		$w = igk_getr("w");
		$h = igk_getr("h");
		if ($q == 'none'){
			$s = file_get_contents($this->getDataDir()."/R/DefaultAssets/template100x100.png");
			ob_clean();
			header("Content-type: image/png");
			igk_wl($s);
			igk_exit();
		}
		$c = $this->getPackageManifestFile($q);
		if (file_exists($c)){
			$dc = igk_zip_unzip_entry($c, "r/Assets/logo.{$w}x{$h}.png");
			if ($dc){
				header("Content-type: image/png");
				igk_wl($dc);
				igk_exit();				
			}
		}
		igk_navto($this->getUri("assets&q=none"));
		igk_exit();
		
	}
	public function visit(){
		igk_navtobase(IGK_STR_EMPTY);
		exit;
	}
	private $m_replacementNode;
	
	private function getReplacementNode(){
		if (!$this->m_replacementNode){
			$this->m_replacementNode = igk_createNode("ajxLnkReplace");
		}
		return $this->m_replacementNode ;
	}
	private function __renderConfigPage_ajx(){
			$s = igk_getr("ajx-lnk")? "^":"";
			//$t = igk_createNode("ajxLnkReplace",null, array($s.".".strtolower(igk_css_str2class_name($this->Name))));
			$t = $this->getReplacementNode();
			$t->clearChilds();
			$t->setTarget($s.".".strtolower(igk_css_str2class_name($this->Name)));
		// $this->showConfig($n->addNoTagNode());
		// $n->RenderAJX();
			$this->showConfig($t->addNoTagNode());
			$t->renderAJX();
	}
	public function addNewTemplate_ajx(){
		
		 if (igk_qr_confirm()){
			$obj = igk_get_robj();
			$ns = "";	
			$obj->clPackageName = trim($obj->clPackageName);
			
			if (!empty($obj->clPackageName) && !igk_template_package_exists($obj->clPackageName) &&!empty($ns =igk_io_basenamewithoutext($obj->clPackageName)) && !igk_template_package_exists($ns)){
				if ($this->genTemplates($obj)){
					igk_notifyctrl("template://addnew")->addMsgr("template.msg.templateadded");
				}else{
					igk_notifyctrl("template://addnew")->addErrorr("error.template.addfailed");
				}
				igk_resetr();
				$this->__renderConfigPage_ajx();					
				igk_exit();
			}
			else
				igk_notifyctrl("template://addnew")->addError("/!\\ no name defined or packagename name already exists");
		 }
		
			$frame = igk_createNode("frameDialog",null, array(__METHOD__, $this));
			$frame->Title = R::ngets("title.newTemplate");
			$d = $frame->BoxContent;
			$d->ClearChilds();
			
			$frm= $d->addForm();
			$frm["action"] = $this->getUri(__FUNCTION__);
			$frm["igk-ajx-form"] = 1;
			$frm->addNotifyHost("template://addnew");
			$dv = $frm->addDiv();
			$dv->setStyle("padding:32px 24px; min-width:400px;");
			
			
			$tab = igk_db_form_data(TBIGK_TEMPLATES, function($n, & $t){
				if ($n=="clId")
					return 1;
				switch($n){
					case "clPackageName":
					case "clTitle":
							$t[$n] = array("required"=>1);
						return 1;
					case "clVersion":
							$t[$n] = array("required"=>1,"attribs"=>array("value"=>"1.0"));
						return 1;
					case "clInstallDate":
					case "clPath":
					case "clDescription":
						return 1; //ignore that field
					default:
					break;
				}
				
				return false;
			});
			
			$tab["clDefaultLang"] = array("type"=>"select", "attribs"=>array(
						"options"=>array("fr"=>"Français(Belgique)", "en"=>"English(England)"),
						"options-select-attribs"=>array("useArrayIndex"=>1)));
						
			$tab["confirm"] = array("type"=>"hidden", "attribs"=>array("value"=>"1"));
			
			
			
			igk_html_buildform($dv, $tab, "div");
			
			
			// igk_html_buildform($dv, array(
			// "clPackageName"=>array("required"=>1),
			// "clTitle"=>array("required"=>1),
			// "clVersion"=>array("required"=>1, "attribs"=>array("value"=>"1.0")),
			// "clDefaultLang"=>array("type"=>"select", "attribs"=>array(
						// "options"=>array("fr"=>"Français(Belgique)", "en"=>"English(England)"),
						// "options-select-attribs"=>array("useArrayIndex"=>1))
			// ),
			// "confirm"=>array("type"=>"hidden", "attribs"=>array("value"=>"1")),			
			// ), "div");
		
			//action
			$dv = $frm->addDiv();
			$dv->addInput("btn.add","submit", R::ngets("btn.create"));
			$frame->RenderAJX();
			
		
	}
	public function genTemplates($obj=null, $renderxml=0){
		
		//igk_wln("generate a templates");
		$m = igk_createNode(IGKApplicationManager::MANIFEST_TAGNAME);
		$m->setAttribute("xmlns:balafon", "http://schemas.igkdev.com/templates/manifest");
		
		// igk_wln($obj);
		// exit;
		//$t = $m->addNode("template");//
		$obj =igk_obj_binddata(igk_template_createTemplateInfo(), $obj);
		$t = igk_xml_to_node($obj, "template");
		$m->add($t);
		// foreach($obj as $k=>$v){
			// $t[$k] = $v;//->setContent($v);
			// //$t->addNode($k)->setContent($v);
		// }
		
		
		
		$f = $this->getPackageStoreDir()."/".$obj->clPackageName.".xtbl";
		if (file_exists($f))
			@unlink($f);
		
		IGKIO::CreateDir(dirname($f));
		
		$zip = igk_zip_content($f, ".manifest", $m->Render(),0);
		$zip->addEmptyDir("lib");
		$zip->addEmptyDir("r");
		$zip->addEmptyDir("src");
		
		$zip->addFromString(".init.php","");
		$zip->addFromString(".erase.php","");
		$zip->addFromString(".data.json","");
		
		
		//load builded folder
		// $zip->addEmptyDir("src/".IGK_VIEW_FOLDER);
		// $zip->addEmptyDir("src/".IGK_DATA_FOLDER);
		// $zip->addEmptyDir("src/".IGK_ARTICLES_FOLDER);
		// $zip->addEmptyDir("src/".IGK_SCRIPT_FOLDER);
		// $zip->addEmptyDir("src/".IGK_STYLE_FOLDER);
		
		
		//load default data		
		// $zip->addFromString("src/".IGK_VIEW_FOLDER."/.htaccess", "deny from all");//default.phtml", igk_template_default_content('view/default'));
		// $zip->addFromString("src/".IGK_DATA_FOLDER."/.htaccess", "deny from all");//default.phtml", igk_template_default_content('view/default'));
		// $zip->addFromString("src/".IGK_ARTICLES_FOLDER."/.htaccess", "deny from all");//default.phtml", igk_template_default_content('view/default'));
		// $zip->addFromString("src/".IGK_SCRIPT_FOLDER."/.htaccess", "allow from all");//default.phtml", igk_template_default_content('view/default'));
		
		
		igk_init_controller(new igk_template_init_listener($zip));
		$zip->addFromString("src/".IGK_VIEW_FOLDER."/default.phtml", igk_template_default_content('view/default'));		
		//addController definition
		$zip->addFromString("src/".IGKApplicationManager::MAIN_FILE, igk_template_default_script_content());
		
		
		//load assets
		$zip->addFromString("r/assets/logo100x100.png", file_get_contents($this->getDataDir()."/R/DefaultAssets/template100x100.png"));
		
		// igk_wln($zip);
		$zip->close();
		
		$this->install_template_package_ajx($f);
		if ($renderxml)
			$m->RenderXML();
		//exit;
		return 1;
		
	}

	///<summary>available templates</summary>
	public function avail_t_ajx(){
		$r = igk_post_uri( "http://local.com/balafon/templates/list/xml");
		$n = igk_createNode();
		
		$n->addDiv()->Content = "Result";
		$d = $n->addDiv()->setStyle("max-height:500px; min-height:200px;");
		$v = igk_createNode("d");
		$v->add(IGKHtmlReader::LoadXML($r));
		//$v->LoadXML($r);
		$tobj = array();
		foreach($v->getElementsByTagName("item") as $k=>$v){
			$obj = igk_xml_to_obj($v);
			// $obj = igk_createObj();
			// foreach($v->Attributes as $s=>$ss){
				// $obj->$s = $ss;
			// }
			// foreach($v->Childs as $s=>$ss){
				// $tn = $ss->tagName;
				// $obj->$tn = $ss;
			// }
			
			$tobj[] = $obj;
		}
		// igk_wln($tobj);
		// exit;
		$d->addDiv()->load($r);
		//$n->addObData($r);
		
		$n->RenderAJX();
	}
	public function install(){//used to install a templates
		//add to controller cache template if is used
	}
	// public  function demo_click_ajx(){
		
		// $n = igk_createNode("script");
		// $n->Content = <<<EOF
// // igk.winui.ajx.lnk.getLink().select("^.config").setHtml("done"+igk.winui.ajx.lnk.getXhr().responseText);
// EOF;
// $n->RenderAJX();

		
		// $n = igk_createNode("ajxLnkReplace",null, array("^.config"));
		// $this->showConfig($n->addNoTagNode());
		// $n->RenderAJX();
		// unset($n);
		
		
	// }
	
	public function uninstall_ajx($id=null){// uninstall template
		$id = $id ?? igk_getr("id");
		if ($this->Db->connect()){
			$row = $this->Db->selectSingleRow(TBIGK_TEMPLATES, $id);
			if ($row){
				$n =igk_template_name($row->clPackageName);
				
				$ctrl = $this->getCreatedCtrl($n);
				if ($ctrl){
					$ctrl->uninstall();
				}
				unset($this->m_createdCtrls[$n]);
				// igk_wln(array_keys($this->m_createdCtrls));
				// exit;
				$dir = igk_templage_get_dir($row->clPackageName);				
				if (is_dir($dir))
					IGKIO::RmDir($dir);		

				if (igk_app()->Configs->web_pagectrl == $row->clPackageName){
					igk_app()->Configs->web_pagectrl = null;
					igk_save_config();
				}				
				$this->Db->dropTemplate($id);
			}
			$this->Db->close();
		}
		// igk_wln("test");
		$this->showConfig($this->TargetNode);
		// $this->TargetNode->RenderAJX();
		$sc = igk_createNode("script");
		$sc->Content = <<<EOF
ns_igk.winui.ajx.lnk.getLink().select("^.igk-roll-owner").setCss({ opacity:0.0}).transEnd('remove');
EOF;
		$sc->RenderAJX();
	}
	public static function LoadTemplates(){
		//used to load namespace
	}
	
	///<summary>create a new package</summary>
	public function make_package(){
		$id = igk_getr("id");
		$row = $this->Db->getPackageRow($id);
		if (!$row){
			return;
		}
		
		$m = igk_createNode(IGKApplicationManager::MANIFEST_TAGNAME);
		$m->setAttribute("xmlns:balafon", "http://schemas.igkdev.com/templates/manifest");
		
		// igk_wln($obj);
		// exit;
		//$t = $m->addNode("template");//
		$obj =igk_obj_binddata(igk_template_createTemplateInfo(), $row);
		$t = igk_xml_to_node($obj, "template");
		$m->add($t);		
		
		$pfile = $this->getPackageManifestFile($row->clPackageName);
		$f = null;
		$temp = 0;
		if (!file_exists($pfile)){
			$temp = 1;
			$f = igk_io_tempfile('xtb');		
			IGKIO::CreateDir(dirname($f));
			//$f = dirname(__FILE__)."/Packages/".$obj->clPackageName.".xtbl";
			if (file_exists($f))
				@unlink($f);
		}else{
			$f = $pfile;
		}
		
		$zip = igk_zip_content($f, ".manifest", $m->Render(),0);
		$zip->addEmptyDir("lib");
		$zip->addEmptyDir("r");
		$zip->addEmptyDir("src");
		
		$zip->addFromString(".init.php","");
		$zip->addFromString(".erase.php","");
		$zip->addFromString(".data.json","");
		
		
		$bpath =igk_io_getdir(igk_io_basedir()."/".$row->clPath);
		$tf = igk_io_getfiles($bpath);//igk_io_basedir()."/".$row->clPath);

		foreach($tf as $k=>$v){
			$p = igk_io_unix_path(substr($v, strlen($bpath)+1));	
			if ($p==".manifest")
				continue;
			$zip->addFromString("src/{$p}", file_get_contents($v));
		}
		
		$zip->close();		
		igk_download_file($row->clPackageName.".xtbl", $f, "binary", 0);		
		if ($temp)
		unlink($f);
		igk_exit();
	}
	public function getPackageManifestFile($packagename){
		return IGKIO::GetDir($this->getPackageStoreDir()."/{$packagename}.xtbl");
	}
	public function getPackageManifest($packagename){
		$v_fname = $this->getPackageManifestFile($packagename);//$this->getPackageStoreDir()."/{$packageName}.xtbl";
		if (file_exists($v_fname)){
		$obj = $this->_get_manifest_obj($v_fname);
		return $obj;
		}
		return 0;
	}
	public function back_ajx(){
		$this->setParam("template://row_id", null);
		$this->resetCurrentView(null);
		$this->m_config_view = "config";		
		$this->__renderConfigPage_ajx();
	}
	public function back(){
		$this->setParam("template://row_id", null);
		$this->m_config_view = "config";
		igk_navtocurrent();
	}
	public function e_manifest(){
		$this->setParam("template://row_id", igk_getr("id"));
		$this->m_config_view = "editmanifest";
		igk_navtocurrent();
	}
	public function e_manifest_ajx(){
		$this->setParam("template://row_id", igk_getr("id"));
		$this->m_config_view = "editmanifest";
		$this->__renderConfigPage_ajx();
	}
	
	public function update_manifest(){
		$obj = igk_get_component_by_id(igk_getr("com_id"));
		if ($obj!=null){
			$obj->update();
		}else{
			igk_wln("not parameter with id ".igk_getr("com_id"));
		}
	}
	///<summary> load and install a template manually</summary>
	public function loadtemplate_package_ajx(){
		$hb = igk_get_header_obj();
	
		if(igk_getv($hb, "UPLOADFILE")){
			$n = $hb->FILE_NAME;
			$s = $hb->UP_FILE_SIZE;		
			$v_c=  file_get_contents("php://input",0,null,-1, $s);
			
			//check for valid package zip
			// igk_wln("d : ".substr($v_c, 0, 10));
			// $r = "PK\x0\x0\x0\x0\x0\x0\x0\x0";
			// igk_wln("r:  ".$r);
			// exit;
			if ((igk_io_path_ext($n)=='xtbl') && substr($v_c, 0, 2)==="PK"){
				$usr = $this->getUser() ;
				$u = $usr? $usr->clLogin."/" : "";
				// igk_wln("save file ......");
				$v_fname = $this->getPackageStoreDir()."/{$u}{$n}";
				igk_io_save_file_as_utf8_wbom($v_fname, $v_c);
				igk_notifyctrl("template://msg")->addMsg("File uploaded");
				$this->install_template_package_ajx($v_fname);
			}else{
				igk_notifyctrl("template://msg")->addError("uploaded file not a valid balafon template package");
			}
		}
		$this->__renderConfigPage_ajx();
		// $t = igk_createNode();
		// $t->addNotifyHost("template://msg");
		// $t->addajxPickFile($this->getUri("loadtemplate_package_ajx"), "{complete:igk.ajx.fn.complete_ready('^form')}")->setClass("igk-btn igk-btn-default")->Content = R::ngets("btn.loadandinstall.templates");		
		// $t->renderAJX();
	}
	
	public function install_template_package_ajx($f=null){
		// $r = igk_createNode("eventSourceHost", null, array($this->getUri("eventMessage")));
		// $r->addDiv()->Content = "Event Host";
		// $r->RenderAJX();
		igk_flush_data();
		
		$f = $f ?? dirname(__FILE__)."/Packages/igkdev.com.rcard.xtbl";
		
		$e = igk_zip_unzip_file_content($f, ".manifest");
		if ($e ==null){
			//no manifest file found
			igk_ilog("no manisfest file found in the package");
			return;
		}
		
		//igk_debug(1);
		//igk_wln($e);
		$d = igk_createXMLNode("dom");		
		//igk_wln(get_class($d));		
		$d->load($e);
		//igk_debug(0);
		//print_r($d->Attributes);
		
		// exit;
		$t = igk_html_select($d, IGKApplicationManager::MANIFEST_TAGNAME);
		if (igk_count($t)!=1){
			igk_ilog("invalid manifest .".$f);
			return;
		}
		$manifest = $t[0];
		$m_obj = igk_xml_to_obj($manifest);
		//igk_wln($m_obj);//igk_xml_to_obj($manifest));
		
		$dir = igk_templage_get_dir($m_obj->template->clPackageName);
		if (!IGKIO::CreateDir($dir))
			igk_die("failed to create a dir");
		
		//extract all src
		igk_zip_unzip_to($f, $dir, "src/");
		
		$app_file = $dir."/".IGKApplicationManager::MAIN_FILE;
		if (!file_exists($app_file))
			igk_die("no app file exists");
		
		//treat and init package files
		$str = file_get_contents($app_file);
		$ns = str_replace("." ,"\\", $m_obj->template->clPackageName);
		$str = str_replace('%{$packagenamespace}', "namespace {$ns}; \nuse \\IGKTemplateBase as IGKTemplateBase;",$str);
		
		//execute .init_php script
		
		
		
		igk_io_save_file_as_utf8($app_file, $str);//str_replace("%{$packagenamespace}", "namespace {$ns};", file_get_contents($f)));
		//TODO ADD TO DB
		//treat app file
		
		$this->Db->addInstalledTemplate($m_obj->template, igk_io_unix_path(igk_io_basePath(dirname($app_file))));
		
		
		//igk_createNode("textarea")->setContent(igk_xml_to_node($m_obj, "manifest")->Render())->RenderAJX();
		
// igk_wln(igk_templage_get_dir($m_obj->template->clPackageName));
		
		//create template folder
		
		
		// igk_wln(igk_html_select($d, IGKApplicationManager::MANIFEST_TAGNAME));
		// igk_wln(igk_count($d->getElementsByTagName("uses-permission")));//IGKApplicationManager::MANIFEST_TAGNAME));
		
		
		
		
		// igk_wln("? : ".(igk_zip_unzip_file_content($f, ".manifest")== null));
		
		
		
		// sleep(3);
		// igk_wln("...");
		// igk_flush_data();
		// sleep(2);
		// igk_wln("done");
		// igk_flush_data();
		
	}
	public function eventMessage(){
		igk_wln("event message ....");
	}
}

final class IGKApplicationManagerDbUtility extends IGKDbUtility{
	
	
	public function updateTemplate($row){
		$r = 0;
		if ($this->connect()){
			
			$r =$this->update(TBIGK_TEMPLATES, $row, $row->clId);
			$this->close();
		}
		return $r;
	}
	public function dropTemplate($id){
		$this->delete(TBIGK_TEMPLATES, $id);
	}
	public function getTemplateNames(){
		$h= null;
		if ($this->connect()){
			$h  = $this->select("tbigk_templates", null, array("columns"=>array("clPackageName")));
			$this->close();			
		}
		return $h;		
	}
	public function getPackageRow($id){
		$row =null;
		if ($this->connect()){
			$row = $this->selectSingleRow(TBIGK_TEMPLATES, $id);
			$this->close();
		}		
		return $row;
	}
	public function getInstallTemplates (){
		//igk_wln("start ");
		
		
		igk_set_env("sys://db/constraint_key", "tmplate_");
		//update db from schema
	//	igk_debug(1);
		//igk_set_env("sys://db/tabfinfo/createTable", null);
		// $this->ctrl->Invoke("dropDbFromSchemas");
		// $this->ctrl->Invoke("initDb");
		// igk_notification_push_event("system/dbchanged", $this->Ctrl);
	//	igk_debug(0);
		
		// exit;
		// $obj = igk_createObj();
		// $obj->Name ="com.igkdev.dummytemplate";		
		// $obj->Display = "Dummy Template";
		
		// $this->insertTemplate($obj);
		
		//igk_wln("use data schema". $this->ctrl->getUseDataSchema());	
		//igk_wln("done ....");
		
		
		$h = null;
		if ($this->connect()){
			$h  = $this->select("tbigk_templates");
			$this->close();
		}
		return $h;
	}
	
	public function insertTemplate($inf){
			if ($this->connect()){
				$tb_a = "tbigk_templates_authors";
				$row = igk_db_create_row($tb_a);
				$row->clUser_Id= 1;
				$id = -1;
				if (($r = $this->selectSingleRow($tb_a, 	$row->clUser_Id))== null){
				
					$row->clGuid = "C4DF3C06-33E4-4A78-A31B-57E958F47230";
					//pay a license to become a developer. and deploy to balafon community
					$this->insert($tb_a, $row);
					$id = $this->LastId;
				}else{
					$id = $r->clId;
				}
				
				//register template
				$tb_a = "tbigk_templates";
				if (!$this->selectSingleRow($tb_a, array("clPackageName"=>$inf->Name))){ 
					$row = igk_db_create_row($tb_a);
					$row->clInstallDate = igk_db_createExpression("Now()");//igk_mysql_date_now();
					//$row->clAuthor_Id = $id;
					$row->clPackageName = $inf->Name;
					$row->clVersion = 1;
					$row->clPath = "";
					$row->clTitle = $inf->Display;
					
					$this->insert($tb_a, $row);
				}
				
				$this->close();
			}
	}
	public function addInstalledTemplate($manifest, $outdir){
		//register template
		if ($this->connect()){
			$tb_a = "tbigk_templates";
			if (!$this->selectSingleRow($tb_a, array("clPackageName"=>$manifest->clPackageName))){ 
				$row = igk_db_create_row($tb_a, $manifest);
				$row->clInstallDate = igk_db_createExpression("Now()");//igk_mysql_date_now();
				//$row->clAuthor_Id = $id;
				//$row->clName = $manifest->Name;
				$row->clVersion = 1;
				$row->clPath = $outdir;
				//$row->clDisplay = $manifest->Display;
				// igk_wln("donnected");
				//igk_wln($row);
				$this->insert($tb_a, $row);
			}
			$this->close();
		}
		
	}
}



?>