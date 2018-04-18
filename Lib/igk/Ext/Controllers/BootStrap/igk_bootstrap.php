<?php
/*
boot strap structure controller 
*/
final class IGKBootstrapCtrl extends IGKConfigCtrlBase
{
	public function getName(){
		return igk_base_uri_name(__FILE__."/".__CLASS__);//bootstrap";
	}
	public function getIsSystemController()
	{
		return true;
	}
	public function getIsEnabled(){
		return igk_sys_getconfig("BootStrap.Enabled", false);
	}
	public function getIsJsEnabled(){
			return igk_sys_getconfig("BootStrap.JsEnabled", false);
	}
	
	public function getCDNBundleJs(){
		return igk_sys_getconfig("bootstrap.cdn.bundle.js", null);
	}
	public function getCDNCss(){
		return igk_sys_getconfig("bootstrap.cdn.css", null);
	}
	public function getCDNJs(){
		return igk_sys_getconfig("bootstrap.cdn.js", null);
	}
	protected function InitComplete(){		
		parent::InitComplete();
		$this->_loadInfo();	
		//register for new document creation
		//$this->App->addNewDocumentCreatedEvent($this, "docCreated");
		
		// igk_reg_session_event(IGK_ENV_NEW_DOC_CREATED, array($this, "docCreated"));		
		igk_notification_reg_event("bootstrap://propertieschanged", array($this, "View"));		
		// igk_admin_reg_menu("bootstrap");
	}
	
	public function onHandleSessionEvent($msg){
		switch($msg){
			case IGK_ENV_NEW_DOC_CREATED:
				$args = array_slice(func_get_args(), 1);
				$this->docCreated($args[0], $args[1]);
			break;
		}
	}
	protected function getGlobalHelpArticle(){
		return "./help/help.global.bootstrap";
		
	}
	
	public function getBootStrapFile(){
		return igk_io_baseDir("Lib/bootstrap/css/bootstrap.min.css");
	}
	public function docCreated($n, $a)
	{
		if ($a !=null)
			$this->__bindBootstrap($a);
	}
	private function __bindBootstrap($doc)
	{
		$tab = array(
		$this->getCDNCss(),
		$this->getCDNJs(),
		$this->getCDNBundleJS());
		
		$f = $tab[0] ?? $this->getBootStrapFile();
		// $js= $tab[0]
		$dir = igk_io_baseDir("Lib/bootstrap/");
		if (!empty($f)){	
			if ($this->IsEnabled)	{
				if (is_dir($dir)){
				igk_css_reg_font_package("glyphicons" , igk_io_basePath($dir."fonts/glyphicons-halflings-regular.ttf"),null,"TrueTypeFont");
				igk_css_reg_font_package("glyphicons" , igk_io_basePath($dir."fonts/glyphicons-halflings-regular.woff"),null, "WOFF");	
				}
				$doc->addStyle($f);	
			}				
			else {
				$doc->removeStyle($f);
				igk_css_unreg_font_package("glyphicons");
			}
		}
		
		
		// //$dir = igk_io_baseDir("Lib/bootstrap/");
		// $f = $this->getBootStrapFile();//igk_io_baseDir("Lib/bootstrap/css/bootstrap.min.css");
		// $d = igk_io_baseDir("Lib/bootstrap/js/bootstrap.min.js");
		
		// if (file_exists($f))		
		// {
			// $f = igk_io_basePath($f);
			// if ($this->IsEnabled)
			// {			
				// igk_css_reg_font_package("glyphicons" , igk_io_basePath($dir."fonts/glyphicons-halflings-regular.ttf"),null,"TrueTypeFont");
				// igk_css_reg_font_package("glyphicons" , igk_io_basePath($dir."fonts/glyphicons-halflings-regular.woff"),null, "WOFF");				
				// $doc->addStyle($f);
			// }
			// else {
				// $doc->removeStyle($f);
				// igk_css_unreg_font_package("glyphicons");				
			// }
		// }
		$d = $tab[1] ?? igk_io_baseDir("Lib/bootstrap/js/bootstrap.min.js");
		if (!empty($d)){
			if ($this->IsJsEnabled)
			{
				$doc->Body->appendScript($d, false, 100);
			}
			else {
				$doc->Body->removeScript($d);
			}
		}
	}
	private function _loadInfo(){
		// igk_ilog("Bind loading info to documents : ". igk_count(igk_get_documents()));
		$v_docs = igk_get_documents();
		if ($v_docs!=null){
			foreach($v_docs as $k=>$v){
					$this->__bindBootstrap($v);
			}
		}
		$this->__bindBootstrap($this->App->Doc);
	}
	
	public function getConfigPage(){
		return "bootstrap";
	}
	
	
	public function View(){
		if (!$this->getIsVisible())
		{
			igk_html_rm($this->TargetNode);
			return;
		}
		$c = $this->TargetNode;		
		igk_html_add($c, $this->ConfigNode);
		
		$c->ClearChilds();
		igk_html_add_title($c, "title.ConfigBootStrap");
		$c->addHSep();
		igk_add_article($this, "./help/help.bootstrap", $c->addDiv(), null, null, true);		
		$c->addHSep();
		
		
		$div = $c->addDiv();
		$frm = $div->addForm();
		$frm["method"]="POST";
		$frm["action"]=$this->getUri("update_bootstrap_setting");
		
		$d = $frm->addDiv();
		$d->addScript()->Content = <<<EOF
(function(){
	igk.system.createNS("igk.lib.bootstrap", {
		update:function(xhr){
			if (this.isReady()){
				this.replaceBody(xhr.responseText);
			}
		}
	});
})();
EOF;
		$v_usetting =  igk_js_post_form_uri($this->getUri("update_bootstrap_setting_ajx"),"ns_igk.lib.bootstrap.update");
		$d["class"]="form-group";
		//enable boot strap
		$ct = $d->addSLabelInput("clEnableBootStrap", 
		 "checkbox" , "1");
		$ct->input["onchange"] = $v_usetting;
		$ct->input["checked"] =  (igk_parsebool($this->IsEnabled)=="true")?"true": null;
		
		
		//enabble boot js
		$d = $frm->addDiv();
		$d["class"]="form-group";
		$ct = $d->addSLabelInput("clEnableJSBootStrap", 
		 "checkbox" , "1");
		$ct->input["onchange"] = $v_usetting;
		$ct->input["checked"] =  (igk_parsebool($this->IsJsEnabled)=="true")?"true": null;
		
		
		
		
		$d->addHSep();
		
		$d = $frm->addDiv();
		$box = $d->addPanelBox();
		igk_html_title($box->addDiv(), R::ngets( "title.CDNSettings" ) );
		
		igk_html_buildform($box->add("ul"), array(
			"bootstrap.cdn.css"=>array("attribs"=>array("value"=>$this->getCDNCss())),
			"bootstrap.cdn.js"=>array("attribs"=>array("value"=>$this->getCDNJs())),
			"bootstrap.cdn.bundle.js"=>array("attribs"=>array("value"=>$this->getCDNBundleJs()))
		));
		
		$box->addInput("update", "submit", R::ngets("btn.update"))->setClass("igk-btn igk-default")->setStyle("font-size:1em; width:auto; line-height:1em;");
		
		
		$s = $d->addDiv()->addPanelBox();
		$s->addSectionTitle(4)->Content = "Files";
		$f = igk_io_getfiles(igk_io_currentRelativePath("Lib/bootstrap"), "/\.(css|js)$/i");
		$ul = $s->add("ul");
		if (igk_count($f)>0 ){
		foreach($f as $k=>$v){
			$ul->add("li")->Content = IGKIO::GetDir($v);
		}
		}
		else{
			$p = $d->addPanel();
			$p->Content = R::ngets("msg.bootstrap.nofilefound");
			$frm = $p->addForm();
			$frm["action"] = "http://www.bootstrap.com/download";
			$frm->addInput("clGetBootStrap", "submit", R::ngets("btn.getbootstrap"));			
		}
	}
	public function update_bootstrap_setting()
	{
		$this->update_bootstrap_setting_ajx();
	}
	public function update_bootstrap_setting_ajx(){
		$app = igk_app();
		$k = igk_getr("clEnableBootStrap", false);
		
		// igk_wln($_REQUEST);
		// igk_exit();
		$app->Configs->SetConfig("BootStrap.Enabled", $k);
		$app->Configs->SetConfig("BootStrap.JsEnabled", igk_getr("clEnableJSBootStrap", false));
		$app->Configs->SetConfig("bootstrap.cdn.css", igk_getr("bootstrap_cdn_css", false));
		$app->Configs->SetConfig("bootstrap.cdn.js", igk_getr("bootstrap_cdn_js", false));
		$app->Configs->SetConfig("bootstrap.cdn.bundle.js", igk_getr("bootstrap_cdn_bundle_js", false));
		
		igk_save_config();	
		$this->_loadInfo();
		$this->View();
		$b =  IGKHtmlItem::CreateWebNode("bootstrap-response");
		$b->add("status-bootstrap")->Content = igk_parsebool($this->IsEnabled);
		$b->add("status-js-bootstrap")->Content = igk_parsebool($this->IsJSEnabled);
		//$b->RenderAJX();
		igk_notification_push_event("bootstrap://propertieschanged", $this);
		
		$val = array();
		$val[0]='false';
		$val[1]='true';
		
		$sc = igk_createNode("script");
		$uri = igk_io_fullpath2fulluri($this->getBootStrapFile());
		$sc->Content = <<<EOF
		igk.ready(
function(){ var f = 0; var r = 0; var i = igk.css.selectStyle({href:/bootstrap\.min\.css\$/}, function(q){ q.disabled= !{$val[$this->IsEnabled]}; f = 1; }); if (!f){
	igk.css.appendStyle("{$uri}");
}}
);
EOF;
		$app->Doc->body->add(new IGKHtmlSingleNodeViewer($sc));
		if (igk_is_ajx_demand()){				
			igk_render_doc();		
			igk_exit();		
		}
	}
	
	
}


//bootrap class item
class IGKHtmlBootstrapFormBuilder
{	
	var $frm ;
	private $m_groups;
	public function __construct($frm){
		$this->frm = $frm;
	}
	public function getView(){
	
		$c = null;
		if ($this->m_groups){
			$c = $this->m_groups;
		}
		else 
			$c = $this->frm;
			return $c;
	}
	public function addGroup(){
		$this->m_groups = $this->frm->addDiv();
		$this->m_groups["class"]="form-group";
		return $this;
	}
	public function addLabel($id, $class=null){
		$c = $this->getView();
		$t = $c->addLabel("lb.".$id, $id);		
		$t["class"] = $class;
		return $this;
	}
	public function addControl($id, $type="text", $style=null){
		$c = $this->getView();
		$t = $c->addInput($id, $type);
		$t["class"] = "-cltext form-control ".$style;
		return $this;
	}
	public function addButton($id, $caption, $type="button", $style=null){
		$c = $this->getView();
		$t = $c->addInput($id, $type, $caption);
		$t["class"] = "btn btn-default igk-btn igk-btn-default ".$style;
		return $this;
	}
	public function addTextarea($id, $style=null){
		$c = $this->getView();
		$t = $c->add("textarea");		
		$t["id"] = $t["name"] = $id;
		$t["class"] = "form-control ".$style;
		return $this;
	}
	public function addLabelControl($id, $type="text", $style=null)
	{
		return $this->addLabel($id)
		->addControl($id,$type, $style);
	}
	public function addLabelTextarea($id, $style="")
	{
		return $this->addLabel($id)
		->addTextarea($id, $style);
	}
	public function addLabelSelect($id, $data, $filter){
		$this->addLabel($id);
		$c = $this->getView()->addSelect($id);
		$c["class"]="form-control";
		if ($data){
			foreach($data->Rows as $k=>$v){
				$op = $c->add("option");
				$op["value"] = igk_getv($v,  igk_getv($filter, "value", "clId"));
				$op->Content = igk_getv($v,  igk_getv($filter, "key", "clName"));
			}
		}
		return $this;
		
	}
	
}


class IGKHtmlBootStrapGrid extends IGKHtmlItem
{
	private $m_row;
	private $m_cell;
	
	public function __construct(){
		parent::__construct("div");
		$this["class"]="igk-grid";
	}
	public function addRow(){
		$t = $this->add("div");
		$t["class"]="igk-grid-row";
		$this->m_row = $t;
		return $t;
	}
	public function addCell($hoverColor=null){
	
		$r = $this->m_row == null? $this->addRow() : $this->m_row;
		$t = new IGKHtmlBootStrapGridCell(); 
		$t["class"]="igk-grid-cell";
		$this->m_cell = $t;
		$t->setHoverColor ($hoverColor);
		$r->add($t);
		return $t;
	}
	public function innerHTML(& $options =null){
		$s = parent::innerHTML();
		$c =  IGKHtmlItem::CreateWebNode("script");
		$c->Content = <<<EOF
(function(ps){ \$ns_igk.ready(function(){ 
var q = ps.select(":igk-cell-hover-color"); 
q.reg_event("mouseover", function(){ 
var c = this.getAttribute("igk-cell-hover-color");
q["igk-cell-hover-oldcl"] = \$igk(this).getComputedStyle("backgroundColor");
\$igk(this).firstChild().setCss({backgroundColor: c});
}).reg_event("mouseout", function(){ \$igk(this).firstChild().setCss({backgroundColor: this.getAttribute("igk-cell-hover-oldcl")} ); } );
;
});})(\$igk(\$ns_igk.getParentScript()));
EOF;
		$s .= $c->Render($options);
		return $s;
	}
}
class IGKHtmlBootStrapGridCell extends IGKHtmlItem
{
	private $m_vcontent;
	// private $m_box;
	private $m_i;
	function getHoverColor(){
		return $this["igk-cell-hover-color"];
	}
	public function setHoverColor($value){
		$this["igk-cell-hover-color"] = $value;
	}
	public function __construct(){
		parent::__construct("div");
		$this->m_vcontent = parent::add("div");
		$this->m_vcontent["class"] = "igk-grid-cell-content";
		
	}
	public function getCellNode(){
		return $this->m_vcontent;
	}
	
	// public function getBox(){return $this->m_box; }
	public function getContent(){
		return $this->m_vcontent->Content ;
	}
	public function setContent($value){
		$this->m_vcontent->Content  = $value;
	}
	public function ClearChilds(){
		return $this->m_vcontent->ClearChilds();
	}
	public function add($n, $b=null, $s=null){

		return $this->m_vcontent->add($n, $b, $s);
	}
	public function innerHTML(& $options = null)
	{
		if ($this->m_vcontent->HasChilds){
	//		igk_wln("child ");
			$p = igk_getv($this->m_vcontent->Childs , 0);
			if ($p && (get_class($p) == "HtmlBootStrapGrid")){			
				//igk_wln("contains grid ". get_class($p) == "HtmlBootStrapGrid");
				//remove 
				$this->m_vcontent ["class"] = "+igk-grid-cell-container";
			}
			else 
				$this->m_vcontent ["class"] = "-igk-grid-cell-container";
		}
		return $this->m_vcontent->Render($options);
	}
}

?>