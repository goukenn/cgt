<?php

final class IGKWebDesignerController extends IGKControllerBase
implements IIGKCssCtrlHost
{
	
	// private $m_hierachiNode;
	public function getName(){
		//igk_ilogdie("jd");
		return "balafon.component.designer";
	}
	protected function InitComplete()
	{
		parent::InitComplete();
		IGKOwnViewCtrl::RegViewCtrl($this);
		// igk_reg_session_event(IGK_ENV_NEW_DOC_CREATED, array($this, "bindToDoc"));
		$app =$this->App;
		//$this->App->addNewDocumentCreatedEvent($this, "bindToDoc");
		// igk_register_event_callback($app, 
		// array($this, "bindToDoc"));
		
		$this->bindToDoc($app, $app->Doc);
		
		//igk_css_reg_doc($this);
		igk_notification_reg_event(IGK_CONF_USER_CHANGE_EVENT, array($this, 'View'));
		//$this->View();
	}
	public function onHandleSessionEvent($msg){
		igk_ilog("recieve message");
		switch($msg){
			case IGK_ENV_NEW_DOC_CREATED:
			$this->bindToDoc(igk_app(), func_get_args(1));
			break;
			
		}
	}
	public function bindToDoc($app, $e){	
		$t = $this->TargetNode;
		$c =igk_html_node_CloneNode($t);
		$c->Index = -50000;
		igk_html_add($c, $e->body);
		//bind hierachi
		$c = igk_html_node_CloneNode($t->hierachiNode );
		$c->Index = -50000;
		igk_html_add($c, $e->body);		
	}
	protected function bindNodeClass($t,$fname,$css_def=null){
		//override the node class. for not changing
	}
	//view not visible
	public function View(){		
		$this->_renderViewFile();	
	}
	public function getIsCssActive($doc=null){
		return igk_designer_is_active($doc);
	}
	public function bindCss(){
		$this->_initCssStyle();
	}

	protected function initTargetNode()
	{		
		$m= igk_createNode("div");
		$m["class"]="igk-winui-hierarchi";
		$m->addbalafonJS()->Content = "igk.winui.designer.inithierarchi(this);";
		$m->setCallback("getIsVisible", "igk_designer_node_visible");	
		
		//IGKIO::WriteToFileAsUtf8WBOM($this->getFile("/".IGK_STYLE_FOLDER."/default.pcss"), igk_get_default_style());
		//igk_ilog("init designer");
		$node = parent::initTargetNode();
		$node["class"]="igk-winui-designer";
		//$node->setCallback("getIsVisible", "return igk_is_conf_connected();");
		$node->setCallback("getIsVisible", "igk_designer_node_visible");	
		$node->hierachiNode = $m;		
		//TODO : not init 
		
		return $node;
	}
	private function setDesignCtrl($v){
		$this->setParam('sys://desing/ctrl', $v);
	}
	public function getDesignCtrl(){
		return $this->getParam('sys://desing/ctrl');
	}
	public function getDesignCtrlName(){
		$g = $this->getDesignCtrl();
		if ($g)
			return $g->Name;
		return null;
	}
	public function _storeCurrentCtrl(){
		$c = igk_get_current_base_ctrl();		
		$this->setDesignCtrl($c);
		return $c;
	}
	public function CurrentControllerView($ul){		
		$ul->clearChilds();
		$c = $this->getDesignCtrl();
		if ($c){
			foreach(igk_io_getfiles($c->getViewDir(),'/^([^\.]+)\.phtml$/i') as $k=>$v){
				$s = igk_io_basenamewithoutext($v);
				$ul->addLi()->addA($c->getAppUri($s))->Content =$s;			
			}
		}
	}
	public function exploreFolder(){
		$c = $this->getDesignCtrl();
		if ($c)	{
			$dir = dirname($c->getDeclaredFileName());
			igk_wl($dir);
		}
		igk_exit();
	}
	public function CurrentNavigableController($ul){
		$ul->clearChilds();
		$ctrltab = igk_get_all_uri_page_ctrl();
		$ctrl = $this->getDesignCtrl();
		if ( !$ctrl || igk_count($ctrltab)<=1){
			//no - controller or single uri ctrl for 
		}
		else{
				//$v_kn = strtolower($this->App->Configs->web_pagectrl);
				$v_kn = "";			
				if (igk_template_is_template_class($ctrl)){
					$v_kn = igk_io_basenamewithoutext( str_replace("\\",".", strtolower($ctrl->Name)));
				}else
					$v_kn = strtolower($ctrl->Name);
				
				// if (method_exists($ctrl, "getAppUri")){
				// $dv = $p->addDiv();
				// $appuri = $ctrl->getAppUri();
				// if ($appuri)
				// $dv->addABtn($appuri)->Content = "Visit";
				// 
				$uri = igk_io_baseUri();
		
				foreach($ctrltab["@base"] as $k)
				{
					$opt = $ul->add("li")->add("a");;
					$n = strtolower($k->Name);					
					//$opt["value"] = $k->Name;
					if ($n == $v_kn)
					{
						$opt["class"] ="igk-active";
					}
					$opt->Content = $k->getDisplayName();
					$s = $k->getAppUri();
					if ($s==$uri){
						$s = igk_ctrl_get_cmd_uri($this, "SelectCtrl/".$k->Name);
					}
					
					$opt["href"] = $s;
				}
				
				foreach($ctrltab["@templates"] as $k)
				{
					
					$opt = $ul->add("li")->setClass('l')->add('a');
					$n = strtolower($k);
					$opt["value"] = $k;
					if ($n == $v_kn)
					{
						$opt["class"] ="igk-active";
					}
					$opt["href"] =  igk_ctrl_get_cmd_uri($this, "SelectCtrl/".$k."/1");
					$opt->Content = $k;
				}
		// foreach($ctrltab as $k=>$v){			
			// $ul->addLi()->addA($c->getAppUri($s))->Content = $s;			
		// }	
		}
	}
	public function getCanEditConfig(){		
		return false;
	}
	public function SelectCtrl($n, $template=0){
		if (!$template){
		$c = igk_getctrl($n);
		if ($c){			
			$this->setDesignCtrl($c);	
			igk_app()->Configs->web_pagectrl=$n;
			
		}
		}
		else{			
			igk_app()->Configs->web_pagectrl=$n;
			$this->_storeCurrentCtrl();
		
		}		
		// exit;		
	}
	private function _getRenderingDocument($ctrl){
		if (igk_template_is_template_class($ctrl))
			return igk_app()->Doc;
		return $ctrl->getCurrentDoc();
	}
	private function _bindFrameToDoc($frame){
		$doc = $this->_getRenderingDocument($this->getDesignCtrl());		
		igk_html_add($frame, $doc->body);
		$doc->setParam("defAddView", null);
	}
	public function addView(){
		
		igk_resetr();
		$defc = $this->getDesignCtrl();
		$ctrl = igk_getctrl(IGK_CA_CTRL); 
		$ctrl->SelectedController = $defc;//this->getDesignCtrl();
			// igk_wln("desing ctrl ".$this->DesignCtrl);
			// igk_wln("s : ".$ctrl->SelectedController);
			
			// igk_debug(1);
		$frame = $ctrl->ca_add_view_frame();
		$frame->Box["class"]="+no-center";
		$this->_bindFrameToDoc($frame);
		// igk_wln("faram ".$defc->getCurrentDoc()->Name);
		
		// igk_wln(" ".$frame->Render());
		// exit;
		if(igk_is_ajx_demand()){
			$frame->RenderAJX();			
		}		
	}
	public function addArticle(){
		// $frame = igk_add_new_frame($this, __METHOD__);
		// $frame->Title = R::ngets("title.addArticle");
		
		igk_resetr();
		$ctrl = igk_getctrl(IGK_CA_CTRL); 
		$ctrl->SelectedController = $this->getDesignCtrl();
		
		$frame = $ctrl->ca_add_article_frame();
		$frame->Box["class"]="+no-center";
		$this->_bindFrameToDoc($frame);
	}

	public function addmenu(){
		
	}
	public function addcontroller(){
		igk_resetr();
		$ctrl = igk_getctrl(IGK_CA_CTRL); 
		$ctrl->SelectedController = $this->getDesignCtrl();
		
		$frame = $ctrl->ca_add_ctrl_frame();
		$frame->Box["class"]="+no-center +no-rs-location";
		igk_html_add($frame, $this->DesignCtrl->getCurrentDoc()->body);
	}
	public function addmedia(){
		
		
	}
	public function IsFunctionExposed($fname){
		if (igk_designer_is_active())
			return true;
		return false;
	}
	//------------------------------------------------------------------------------------------------------
	//view functions
	//------------------------------------------------------------------------------------------------------
	public function clearView(){
		$c = $this->getDesignCtrl();
		$v = $c->currentView;
		if (!empty($v)){
			
		}		
	}
	public function backupView($v=null){
		$tmp= $this->getTempDir();
		igk_die("not implement", __METHOD__);
	}
	
	public function dragdrop(){//drop file on body 
		$c = $this->getDesignCtrl();
		$f = $c->getDataDir()."/R/.private/".date('Y/m/d');
		IGKIO::CreateDir($f);
		// igk_wln($f);
		// igk_wln($_REQUEST);		
		$of = igk_io_store_ajx_uploaded_data($f);
		igk_post_message("storeMediaFile", $c , $of);
		igk_notifybox_ajx("upload complete");
		igk_exit();
	}
	
	public function design(){
		$this->setParam("designmode",1);
		$u = $this->getDesignCtrlUri($this->getDesignCtrl());
		// igk_wln($u);
		ob_clean();
		igk_navto($u);
		igk_exit();
	}
	public function getDesignCtrlUri($ctrl){
		return $ctrl ? $ctrl->getAppUri() : igk_io_baseUri();
	}
	protected function inithierachy($n){
		$n->clearChilds();
		$n->addBalafonJS()->Content="igk.winui.designer.inithierarchi(this);";

	}
	public function getHierachi(){	
		$n = igk_html_node_notagNode();
		$this->getViewContent("hierachi", $n, true);
		$this->inithierachy($n);
		$n->RenderAJX();		
		igk_exit();
	}
	private function _include_style($f){
		include($f);
		$b = get_defined_vars();
		$o = array();
		foreach($b as $k=>$v){
			$o[$k] = $v;
		}
		return (object)$o;
	}
	public function storeStyle(){
		//store only require template data
		$data = array("global"=>"def",
		"xlg"=>"xlg_screen",
		"xxlg"=>"xxlg_screen",
		"sm"=>"sm_screen",
		"xsm"=>"xsm_screen");
		
		$d = base64_decode(igk_getr("q"));
		if ($d){
			$obj = igk_json_parse($d);
		$c=$this->getDesignCtrl();
		$dir = $c->getStylesDir();
		$f = $dir ."/".$c->currentView.".dsgpcss";		
		// igk_wln("store to ".$f);
		$o = "";
		
		if (file_exists($f)){
			foreach($this->_include_style($f) as $c=>$t){
				if ($c=='def'){
					if (!isset($obj->global)){
							$obj->global = $t;
					}
					continue;
				}
				if (!isset($obj->$c)){
					// igk_ilog("add data ".$c. " ".$t);
					$obj->$c=$t;
				}
			}
		}
		// igk_ilog(igk_ob_get(array_keys((array)$obj)));
		
		foreach($obj as $k=>$v){
			$m = $k;
			if ($k=="global"){
				$m= "def";
			}else{
				if (isset($data[$k]))
					$m = $data[$k];
				else
					continue;
			}
			
			foreach($v as $c=>$vv){
				$o .= "\${$m}[\"{$c}\"] = \"{$vv}\";\n";
			}
		}
		
		// igk_ilog($_REQUEST);
		// igk_ilog("data : ".$d);
		// igk_ilog("obj");
		// igk_wln($o);
		if (!empty($o))
			igk_io_save_file_as_utf8_wbom($f, "<?php\n{$o}?>", true);
		}
		igk_exit();
	}

}


// final class IGKControllerPageEditor extends IGKControllerBase
// {
	// private $m_script;	
	// public function getDataAdapterName(){ return IGK_MYSQL_DATAADAPTER; }	
	// public function getRegisterToViewMecanism(){return true;}
	// protected function InitComplete(){
		// parent::InitComplete();
		// igk_getctrl(IGK_MENU_CTRL)->addPageChangedEvent($this, "View");
		// $this->App->addNewDocumentCreatedEvent($this, "onnewDocCreated");
		// IGKOwnViewCtrl::RegViewCtrl($this);
	// }
	// protected function onnewDocCreated($o,$e)
	// {
		// if ($this->IsVisible){
		// $this->TargetNode->ClearChilds();
		// $this->_build_view($this->TargetNode);
		
		
		// igk_html_add(igk_html_node_CloneNode($this->TargetNode), $e->body);
		// }
	// }
	// //<summary>override the page folder changed definition</summary>
	// public function pageFolderChanged(){
		// $this->View();		
	// }
	// protected function initTargetNode()
	// {		
		// $node = parent::initTargetNode();
		// $node["class"] = "igk-configpage-editor";			
		// return $node;
	// }
	// private function _editor_ca_script($uri)
	// {
		// return "javascript: var f = \$igk(this).getParentForm(); \$igk(this).$.ajx.post('{$uri}&n='+f.clCtrls.value,null,function(xhr){if (this.isReady()){ igk.$.ctrl.frames.appendFrameResponseToBody(xhr.responseText);}}); return false;";
	// }
	// private function _build_view($node){
				
		// $frm = $node->addForm();
		// $frm["action"] = $this->getUri("editor_update");
		// //add page editor action
		// $d = $frm;//->addspan();
		// $d->addLabel("lb.page", "clPages");
		// $s = $d->add("select");
		// $s->setId("clPages");
		// $s["onchange"] = "javascript: window.igk.navigator.navTo('?p='+this.value); return false;";///, null, function(xhr){ if(this.isReady()){this.replaceBody();}})";
		// //get available page
		// $t = igk_sys_pagelist();
		// foreach($t as $k=>$v)
		// {
			// $opt = $s->add("option");
			// $opt->Content = $v;
			// $opt["value"] = $v;
			// if ($this->CurrentPage == $v)
			// {
				// $opt["selected"]=1;
			// }
		// }
		
		// $d  = $frm;//->addspan();
		// $d->addLabel("lb.ctrls", "clCtrls");
		// $t = igk_sys_getuserctrls();				
		// $setting = igk_html_build_select_setting();		
		// $setting->resolvtext = false;
		// $setting->keysupport = true;
		// $setting->valuekey = "Name";
		// $setting->displaykey = "Name";	
		// igk_html_build_select($d, "clCtrls", $t, (array)$setting,null);
		// $edit = igk_getctrl(IGK_CA_CTRL)->getUri("ca_edit_ctrl_ajx");
		// IGKHtmlUtils::AddImgLnk($d, $this->_editor_ca_script($edit), "edit_16x16");
		// $edit = igk_getctrl(IGK_CA_CTRL)->getUri("ca_edit_ctrl_properties_ajx");
		// IGKHtmlUtils::AddImgLnk($d,  $this->_editor_ca_script($edit), "setting");
		
		// //EDITOR Options
		// IGKHtmlUtils::AddAnimImgLnk($frm, igk_js_post_frame($this->getUri("editor_add_ctrl_ajx")), "editor_add", "32px", "32px");		
		// IGKHtmlUtils::AddAnimImgLnk($frm, igk_js_post_frame($this->getUri("editor_menu_ajx")), "editor_menu", "32px", "32px");	
		// IGKHtmlUtils::AddAnimImgLnk($frm, igk_js_post_frame($this->getUri("editor_font_ajx")), "editor_form", "32px", "32px");
		// IGKHtmlUtils::AddAnimImgLnk($frm, 
		// "javascript: ns_igk.cssbuilder.openwindow('".$this->getUri("style_coloreditor")."');return false;"
		// , "editor_xcss", "32px", "32px");
		// IGKHtmlUtils::AddAnimImgLnk($frm, igk_js_post_frame($this->getUri("editor_db_ajx")), "editor_db", "32px", "32px");
		
		
		// // IGKHtmlUtils::AddAnimImgLnk($frm, "javascript: window.igk.ctrl.show_ctrl_options(); return false;", "editor_viewc", "32px", "32px");
		// // IGKHtmlUtils::AddAnimImgLnk($frm, "javascript: window.igk.ctrl.show_article_options(); return false;", "editor_viewa", "32px", "32px");	
		// $frm->addDiv()->setClass("igk-cleartab");
		// $frm->addDiv()
		// ->setClass("close-bar")
		// ->setStyle("+/height:4px;")
		// ->setAttribute("onclick", "javascript: ns_igk.ctrl.pageeditor.hide(this); return false; ")
		// ->addSpace();
		// $this->m_script = $node->addScript();
		// $this->m_script->Content  = "ns_igk.readyinvoke('igk.ctrl.pageeditor.init');";
	// }
	// public function editor_menu_ajx()
	// {
		// $frame = igk_add_new_frame($this, "editor_menu_frame");
		// $frame->Title =R::ngets("title.primaryMenuEditor");
		// $c = $frame->BoxContent;
		// $c->ClearChilds();
		// $d = $c->addDiv();
		// igk_getctrl(IGK_MENU_CTRL)->MenuConfig($d);
		
		// igk_getctrl(IGK_CA_CTRL)->__viewMenuHostCtrl($d);
		
		
		// $s = $frame->regView;
		// if (!$s)
		// {
			// igk_getctrl(IGK_MENU_CTRL)->regView($this, "editor_menu_ajx");
			// $frame->regView = true;
			// $frame->addCloseCallBackEvent($this,"editor_menu_frame_closed");
			// $frame->RenderAJX();	
		// }	
		// igk_notifyctrl()->setNotifyHost($d->addDiv());
	// }
	// public function editor_menu_frame_closed($frame)
	// {
		// $frame = igk_get_frame("editor_menu_frame");
		// igk_getctrl(IGK_MENU_CTRL)->unregView($this);
		// $frame->regView=false;
		// $frame->removeCloseCallBackEvent($this, "editor_menu_frame_closed");
		// igk_notifyctrl()->setNotifyHost(null);		
	// }
	
	// public function editor_font_ajx(){
		// $frame = igk_add_new_frame($this, "editor_font_frame");
		// $frame->Title = R::ngets("title.font-editor");
		// $d = $frame->BoxContent ;
		// $d->ClearChilds();
		
		// $frm = $d->addForm();
		// $frm["action"] = $this->getUri("editor_font_validate");		
		// $frm["style"]="position:relative; border:1px solid black; height:100%;";
		// $frm->Box["style"]="position:relative; height:100%; margin-bottom:56px;";
		// $ftview = $frm->addDiv();
		// $ftview["class"]= "fitw fith posr";
		
		// $table = $ftview->addTable();
		// $ul = $ftview->add("ul", array("id"=>"font_list", "class"=>"font_list"));
		// igk_getctrl(IGK_THEME_CTRL)->theme_buildFontTable($table, $ul);
		// $p = $ftview->addDiv();
		// $p["style"]="position:absolute; bottom:0px; right:0px; width:100%; height: 56px;";
		// $p->addHSep();
		
		// $i = $p->addInput("btn.validate", "submit");	
		// $i["style"]="position:absolute; bottom:4px; right:4px;";
		
		// $frame->RenderAJX();		
	// }	
	
	// public function editor_db_validate(){
		// //validate db
		// igk_notifyctrl()->addMsgr("msg.databaseupdated");
		// igk_getctrl(IGK_MYSQL_DB_CTRL)->pinitSDb();
	// }
	// public function editor_db_ajx(){
		// $frame = igk_add_new_frame($this, "editor_db_frame");
		// $frame->Title = R::ngets("title.initdatabase");
		// $d = $frame->BoxContent ;
		// $d->ClearChilds();
		// $frm = $d->addForm();
		// $frm["action"] = $this->getUri("editor_db_validate");		
		
		// $frm->addHSep();
		// $frm->addInput("btn.validate", "submit", R::ngets("btn.initdb"))->setClass("-clsubmit igk-btn igk-btn-default");
		// $frame->RenderAJX();		
	// }
	
	
	// public function editor_xcss_drop_ajx()
	// {
		// $n = igk_getr("n");
		// if ($n){
			// igk_getctrl(IGK_THEME_CTRL)->theme_rmkey($n);
		// }
	// }
	// public function editor_xcssvalidate_ajx()
	// {
		// $obj = igk_get_robj();		
		// $this->editor_xcss_ajx(false);
		// igk_getctrl(IGK_THEME_CTRL)->theme_addKey();
		
	// }
	
	// public function editor_langvalidate()
	// {
		// $obj = igk_get_robj();
		// R::AddLang($n, $v);
		// R::SaveLang();
		// igk_navtocurrent();
	// }
	
	// public function editor_add_ctrl_ajx(){
		// //update the current editor
		// igk_getctrl(IGK_CA_CTRL)->ca_add_ctrl_frame_ajx(true);
	// }	
	
	// public function getIsVisible(){
		// return ($this->App->CurrentPageFolder != IGK_CONFIG_PAGEFOLDER) 
				// && ($this->App->ConfigMode && $this->App->Configs->allow_article_config) && 
				// IGKViewMode::IsWebMaster();
	// }
	// public function getCanAddChild(){
		// return false;
	// }
	// public function info_ajx(){
		// $r = igk_new_response();
		// $r->add("item", array("name"=>"test_1"))->Content = "se";
		// for($i=0; $i< 30; $i++){
			// $r->addItem("text".$i, "text".$i, null);
		// }
		// $r->RenderAJX();
	// }
	// public function View(){
		// if ($this->getIsVisible())
		// {
			// $this->TargetNode->ClearChilds();
			// $this->_build_view($this->TargetNode);
			// igk_html_add($this->TargetNode, $this->App->Doc->body);
			// $this->_onViewComplete();
			// $this->App->Doc->body["class"] = "+igk-page-editor";
		// }
		// else{
			// igk_html_rm($this->TargetNode);
			// $this->App->Doc->body["class"] = "-igk-page-editor";
		// }
		
	// }
	
	// function style_editor_save(){
		// $s = igk_getr("clSelector");
		// $t = igk_getr("clMediaType", "global");
		// $th = $this->App->Doc->Theme;
		// $def = null;
		// $v = igk_getr("clValue");
		// switch($t){
			// case "xxlg":
				// $def = $th->get_media(IGKHtmlDocThemeMediaType::XXLG_MEDIA);
			// break;
			// case "xlg":
				// $def = $th->get_media(IGKHtmlDocThemeMediaType::XLG_MEDIA);
			// break;
			// case "lg":
				// $def = $th->get_media(IGKHtmlDocThemeMediaType::LG_MEDIA);
			// break;
			// case "sm":
				// $def = $th->get_media(IGKHtmlDocThemeMediaType::SM_MEDIA);
			// break;
			// case "xsm":
				// $def = $th->get_media(IGKHtmlDocThemeMediaType::XSM_MEDIA);
			// break;			
			// default:
			// igk_css_regclass($s, $v);						
			// break;
		// }		
		// if ($def){
		
			// $def[$s] = $v;
		// }
		
		// $this->App->Doc->Theme->save();
		// igk_exit();
	// }
	// function style_coloreditor(){	
	
		// $doc = new IGKHtmlDoc($this->App, true);
		// $doc->Title = "cssEditor";
		// //"size:no = "force browser to open as dialog"
		// $doc->body->ClearChilds();
		// $bbox = $doc->body->addBodyBox();
		// $bbox->addIGKHeaderBar()->Title= "CssEditor";
		// $bbox->addMenuBar();
		// $frm = $bbox->addForm();
		// $frm["onsubmit"]="javascript:return false;";				
		// $frm->addInput("clValue", "hidden", "");
		// $frm->addInput("clMediaType", "hidden", "");
		
		// $div = $frm->addDiv();
		// $row = $div->addSLabelInput("clSelector", "text");
		// $row->input["class"]="igk-form-control";
		// $row->input["placeholder"]=R::ngets("tip.cssbuilder.select");
		
		// $div->add('span')->setClass("cls-v")->Content = 0; //selection value
		// $div->addDiv()->setAttribute("igk-js-eval-init", "igk.cssbuilder.initmediaview(this);");
		
		// $row = $div->addRow();
		
		// $col = $row->addDiv()->setClass("igk-col-lg-12-3");
		// $d = $col->addDiv();
		// $d->addDiv()
		// ->addInput("search", "text")
		// ->setClass("igk-form-control")
		// ->setAttribute("placeholder", R::ngets("tip.cssbuilder.searchproperty"))
		// ->setAttribute("igk-js-eval-init","if (typeof(igk.cssbuilder) !='undefined') igk.cssbuilder.initsearch()");
		// $d->addDiv()->addScript()->Content = "ns_igk.cssbuilder.init()";
		
		// $col = $row->addDiv()->setClass("igk-col-lg-12-9");
		
		// $div = $col->addDiv();
		// $top = $div->addDiv();
		// $top->addTrackbar();
		
		// $top = $div->addDiv();
		// $top->addInput("clMode", "radio", 1)
		// ->setAttribute("checked", "1")
		// ->Content = R::ngets("lb.BackgroundColor");
		// $top->addInput("clMode", "radio", 2)->Content = R::ngets("lb.Color");
		
		
		// $pck = $div->addCircleColorPicker();
		// $pck["igk-data"] = "{update:function(evt){ ns_igk.winui.style_editor.initcolor.apply(this, [evt]);}}";
		
		// $div->addDiv()->addButton("btn.maketransparent", R::ngets("btn.maketransparent"))->setAttribute("onclick", "javascript: ns_igk.winui.style_editor.maketransparent(); return false; ");
		
		
		// $div = $frm->addDiv()->setClass("igk-add-margin")->addActionBar();
		// $div->addButton("btn.save", R::ngets("btn.save"))->setAttribute("onclick", "javascript: ns_igk.winui.style_editor.save(); return false; ");
		// $div->addButton("btn.reset", R::ngets("btn.reset"))->setAttribute("onclick", "javascript: ns_igk.winui.style_editor.reset(); return false; ");
	
		// $frm["action"] = $this->getUri("style_editor_save");
		// $frm->addScript()->Content =
// <<<EOF
// ns_igk.winui.style_editor.init();
// EOF;
		// $doc->RenderAJX();
		// igk_exit();
	// }

	
// }

?>