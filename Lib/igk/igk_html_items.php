<?php


////<summary>represesent a node use to setup article configuration</summary>
final class IGKHtmlArticleConfigNode extends IGKHtmlItem{
	private $m_target;
	private $m_ctrl;
	private $m_filename;
	private $m_dropfileUri;
	private $m_forceview;
	
	public function getdropFileUri(){return $this->m_dropfileUri; } 
	public function setdropFileUri($v){ $this->m_dropfileUri = $v; return $v; } 
	
	public function __construct($ctrl=null, $target=null, $filename=null, $forceview=0){
		
		parent::__construct("div");
		$this->m_filename = $filename;
		$this->m_target = $target;
		$this->m_ctrl = $ctrl;
		$f = $filename;
		$this->m_forceview = $forceview;
		$this["class"]="igk-article-options";
		$this["igk-article-options"] = "true";
		
		$config = igk_getctrl(IGK_CA_CTRL);
		$n = ($ctrl)? $ctrl->Name : "";
		// igk_ilog("file ".$f);
		if ($config)
		{
			// $this->addspan()
			// ->setClass("mark")
			// ->setAttribute("igk-tooltip", "{data:'".basename($f)."'}")->Content = igk_svg_use("edit");//addSymbol(8);
			IGKHtmlUtils::AddImgLnk($this, igk_js_post_frame($config->getUri("ca_edit_article_ajx&navigate=1&ctrlid=".$n."&m=1&fc=1&fn=".base64_encode($f)), $ctrl), "edit_16x16");
			// IGKHtmlUtils::AddImgLnk($this, igk_js_post_frame($config->getUri("ca_edit_articlewtiny_f_ajx&navigate=1&ctrlid=".$n."&m=1&fc=1&fn=".base64_encode($f)), $ctrl), "tiny_16x16");
			
		// if($ctrl->App->CurrentPageFolder != IGK_CONFIG_PAGEFOLDER)
		// {
			IGKHtmlUtils::AddImgLnk($this, igk_js_post_frame($config->getUri("ca_add_article_frame_ajx&ctrlid=".$n."&m=1&fc=1&fn=".base64_encode($f)), $ctrl), "add_16x16");
			
			
			if (file_exists($f)){
			
				$this->m_dropfileUri =  $config->getUri("ca_drop_article_ajx&navigate=1&ctrlid=".$n."&n=".base64_encode($f));
				IGKHtmlUtils::AddImgLnk($this, 
					igk_js_post_frame(
						new IGKValueListener($this, "dropFileUri")
					, $ctrl),
					"drop_16x16"					
					)->setAlt("droparticle");
			}
		}
		else{
			$this->Content = "no config article found";
		}
		$target->add($this);
		$this->setIndex(-1000);
	}
	public function getIsVisible(){	
		return $this->m_forceview || (parent::getIsVisible() && igk_app()->IsSupportViewMode(IGKViewMode::WEBMASTER));
	}
}

final class IGKHtmlSearchItem extends IGKHtmlItem
{
	private $m_link;
	private $m_input;
	private $m_ctrl; //controller
	private $m_ajxfunc ;
	private $m_uri;
	private $m_frm;
	private $m_prop;
	private $m_search;
	private $m_TargetId;
	private $m_AJX;
	private $m_method;
	
	public function getMethod(){return $this->m_method; }
	public function setMethod($v){ $this->m_method = $v;  return $this;}
	public function getUri(){return $this->m_uri; }
	public function setUri($v){ $this->m_uri = $v;  return $this;}
	public function getTargetId(){return $this->m_TargetId;}
	public function setTargetId($v){$this->m_TargetId =$v;  return $this;}
	public function getAJX(){return $this->m_AJX;}
	public function setAJX($v){$this->m_AJX =$v; return $this;}
	public function setValue($v){$this->m_search =$v; return $this;}
	
	public function initView(){
		$uri = $this->m_uri;
		$tab = igk_getquery_args($uri);
		
		if (isset($tab["c"]))
		{
			$this->m_ctrl = igk_getctrl($tab["c"]);
			$f = igk_getv($tab, "f");
				$this->m_ajxfunc = null;
			if ($f)
			{
				$f = str_replace("-","_", $f);
				if (!IGKString::EndWith($f, "_ajx")	)
				{
					$f  = $f."_ajx";
					if (method_exists($this->m_ctrl, $f))
					{
						$this->m_ajxfunc  = $this->m_ctrl->getUri($f);
					}
				}
				else{
					$this->m_ajxfunc =	$this->m_ctrl->getUri($f);
				}
			}	
		}		
		$frm = $this->m_frm;
		$prop = $this->m_prop;
		$search = $this->m_search;
		if (!$frm || empty($uri))
		{
			return;
		}
		$frm->ClearChilds();
		$frm["action"] = $uri;
		$frm["id"]="search_item";
		$frm["method"] = new IGKValueListener($this, "Method");
		$frm->addDiv()->setClass("igk-underline-div");
		
		$frm->NoTitle = true;
		$frm->NoFoot = true;
		$d  = $frm->addDiv();
		$d["class"] = "disptable fitw";
		$d = $d->addDiv()->setClass("disptabr");
		$this->m_link = IGKHtmlUtils::AddImgLnk($d, $uri, "btn_search_16x16", "24px", "24px");
		$this["class"]="alignm";
		//$this["onclick"]= "javascript:return ns_igk.form.submitonclick(this);";
		$this->m_input = $d->addInput($prop, "text", igk_getr($prop, $search));		
		$this->m_input["class"] = "igk-form-control fitw";	
		$this->m_input["onkeypress"]="javascript:return ns_igk.form.keypress_validate(this,event);";
		
		
		if ($this->AJX || $this->m_ajxfunc)
		{
			$frm["igk-ajx-form"] = 1;
			$frm["igk-ajx-form-uri"] = $this->m_ajxfunc;
			$frm["igk-ajx-form-target"] = $this->m_TargetId;
		}
		else{
			$frm["igk-ajx-form"] = null;		
			$frm["igk-ajx-form-uri"] = null;
			$frm["igk-ajx-form-target"] = null;
		}	
	}	
	public function __construct($uri=null, $search=null, $prop="q", $ajx=0, $target=null)
	{		
		parent::__construct("div");
		$this["class"]="clsearch search_fcl";
		$this->m_AJX = $ajx;
		$this->m_method = "POST";
		$this->m_uri = $uri;
		$this->m_frm = $this->addForm();
		$this->m_prop = $prop;
		$this->m_search = $search;
		$this->m_TargetId = $target;
		$this->initView();
	}
	
}
class IGKHtmlImgItem extends IGKHtmlItem
{
	private $m_lnk;
	private $m_img;
	private $m_imageSrcEval;
	
	
	public function getSrc(){
		return $this->m_lnk; 
	}
	public function setSrc($value){
		if (empty($value))
			return $this;
		$this->m_lnk=$value;
		return $this;
	}
	public function offsetGet($key){
		if (strtolower($key)=="src")
			return $this->m_lnk;
		return $this->m_img->offsetGet($key);
	}
	/// offsetSet src
	public function offsetSet($key,$value) {	
		
		if (IGKString::StartWith($key, 'igk:'))
		{	
			return parent::offsetSet($key, $value);			
		}
		if (strtolower($key) =="src")
		{	
			//remove base uri to match system requirement if exists	
			
			$s = IGKValidator::IsUri($value)?$value: igk_html_rm_base_uri($value);					
			$this->setSrc($s);			
			return;
		}
		if ($value === $this->getStyle()){
			parent::offsetSet($key, $value);
		}
		else 	{	
			$this->m_img->offsetSet($key, $value);
		}
	}
	public function __construct(){
		$this->m_img = igk_html_node_innerImg();		
		parent::__construct("igk-html-image");				
		$this->m_imageSrcEval = new IGKHtmlImgEvalSrc($this, $this->m_img);//, "EvalSrc");
		$this->m_lnk = null;//"[IGKHtmlImgItemLnk]";
		$this->m_img["src"]=$this->m_imageSrcEval ;
		$this->m_img["alt"]=null;
	}

	protected function _p_isClosedTag($tag)
	{
		switch(strtolower($tag))
			{
				case "igk-html-image":
				case "igk-img":
				case "img":
				case "image":			
					return true;
			}
			return false;
	}
	
	public function IsLoadedClosedTag($loadedTag){//override if this is a loaded closed tag
		switch(strtolower($loadedTag))
		{
			case "igk-html-image":
			case "igk-img":
			case "img":
			case "image":			
				return true;
		}
		return false;
	}
	//public function Render($options=null){	
			// igk_log_write_i("for", $s);
			// igk_log_write_i("forlknk", $this->m_lnk);
		// $this->m_img["src"] = $s;
		// return $this->m_img->Render($options);
	// }
	public function __toString()
	{
		return get_class($this)."#[".$this->TagName."] Attributes : [".$this->Attributes->Count."] ; Childs : [ ".$this->ChildCount." ]";
	}
	public function getIsRenderTagName(){
		return false;
	}
	public function RenderComplete($option = null){
	}
	protected function __getRenderingChildren($o=null){	
		return array(
			$this->m_img
		);
	}
	
}

///<summary>Empty tag node item. for img private sample</summary>
final class IGKHtmlEmptyTagNodeItem extends IGKXmlNode{
	function __construct($n){	
		parent::__construct($n);
	}
}
final class IGKHtmlNothingItem extends IGKHtmlItem
{
	public function __construct(){
		parent::__construct("nothingitem");
	}
	//unable to add child to this item
	protected function _AddChild($item, $index=null){return false;}
	public function Render($options=null){//render htmlNothing Item
		return null;
	}
}

final class IGKHtmlCommentItem extends IGKHtmlItem
{
	private $forRendering;
	
	public function getNodeType(){
		return IGKXMLNodeType::COMMENT;
	}
	public function getHasAttributes(){return false;}
	public function __construct($value=null){
		parent::__construct("igk-comment");
		$this->Content = $value;
	}
	public function getIsRenderTagName(){
		return false;
	}
	public function getContent(){
		if ($this->forRendering)
			return '<!--  '.parent::getContent().' -->';
		return parent::getContent();
	}
	public function AcceptRender($o=null){
		$this->forRendering = parent::AcceptRender($o);
		return $this->forRendering;
	}
	public function RenderComplete($o=null){
		$this->forRendering = false;
	}
	
	public function __toString(){
		return "IGKHtmlComment[".$this->Content."]";
	}
	protected function _AddChild($item, $index=null){return false;}
}

///<summry> represent a select element </summary>
final class IGKHtmlSelectItem extends IGKHtmlItem
{
	private $m_keys;
	public function __construct(){
		parent::__construct("select");
		$this->m_keys = array();
		$this["class"]="clselect";
	}
	public function addOptions($tabarray){
		if (is_array($tabarray))
		{
			foreach($tabarray as $k=>$v)
			{
				$this->addOption($k)->setContent($v);
			}
		}
		
	}
	public function addOption($value){
		
		if (!isset($this->m_keys[$value]))
		{
			$p = $this->add("option");
			$p["value"] = $value;
			$this->m_keys[$value] = $p;
			return $p;
		}
		return null;
	}
	public function select($value){
		if (isset($this->m_keys[$value]))
		{			
			$this->m_keys[$value]->setAttribute("selected", "selected");			
		}
	}
}

///<summary>represent horizontal menu item</summary>
final class IGKHtmlHMenuItem extends IGKHtmlItem
{
	public function __construct(){
		parent::__construct("ul");
		$this["class"] = "igk-horizontal-menu";
	}
	public function addItem($name, $link=null, $content=null){
		$li = $this->add("li");
		
		$li->setId($name);
		if ($link){
			$li->addA($link)->setContent($content);
		}
		else if ($content){
			$li->setContent($content);
		}
		return $li;
	}
}

///<summary>row item</summary>
final class IGKHtmlRowItem extends IGKHtmlDiv
{
	public function __construct(){
		parent::__construct();
		$this["class"] = "igk-row";
	}
	public function addCell(){
		$d = $this->addDiv();
		$d->setClass("igk-row-cell");
		return $d;
	}
	public function addCol($classlist=null){
		return $this->addDiv()->setClass("igk-col ".$classlist);
	}
}
final class IGKHtmlPanelItem extends IGKHtmlDiv
{
	public function __construct(){
		parent::__construct();
		$this["class"] = "igk-panel";
	}
}
///<summary>represent a ajx button link</summary>
final class IGKHtmlAJXButtonItem extends IGKHtmlDiv
{
	private $m_method;
	private $m_uri;
	private $m_rep;
	public function getScript(){
		$s = 'javascript: ';
		if ($this->m_uri)
		{
			$rep = $this->m_rep;
			if ($this->m_method == 'POST'){
				$s .= "ns_igk.ajx.post('{$this->m_uri}', null, $rep);";
			}
			else{
				$s .= "ns_igk.ajx.get('{$this->m_uri}', null, $rep);";
			}
		}
		$s.= 'return false;';
		return $s;
	}
	public function __construct($uri=null, $reponse='null', $method='POST'){
		parent::__construct();
		$this->m_uri = $uri;
		$this->m_method = $method;
		$this->m_rep = $reponse;
		$this["class"] = "igk-btn igk-ajx-btn";
		$this["onclick"] = new IGKValueListener($this,'Script');
	}
}

///<summary>used to notify message </summary>
final class IGKHtmlNotifyBoxItem extends IGKHtmlDiv
{
	private $m_type;
	private $m_autohide;
	
	public function setAutohide($v){$this->m_autohide->IsVisible = $v; return $this;}
	public function getAutohide(){return $this->m_autohide->IsVisible; }
	public function __construct($type=null){
		parent::__construct();
		$this["class"] = "igk-notify-box";
		 if ($type)
			$this->setType($type);
		
		$this["igk-no-auto-hide"] = new IGKValueListener($this, "Autohide");
		$this->m_autohide = $this->addScript();
		$this->m_autohide->Content = <<<EOF
\$ns_igk.winui.notifyctrl.init(\$ns_igk.getParentScript());
EOF;
	}	
	public function setType($type)
	{
		if ($this->m_type)
			$this->setClass("+igk-notify-".$this->m_type);
		$this->setClass("+igk-notify-".$type);
		$this->m_type = $type;
		return $this;
	}
}
///<summary>represent a shortcut to notification node</summary>
///<remark>old version: igk_notifyctrl()->setNotifyHost({targetnode}) 
///new version: {targetnode}->addNotifyZone()
///</remark>
final class IGKHtmlNotifyZoneItem extends IGKHtmlDiv
{
	public function __construct(){
		parent::__construct();
	}
	public function AcceptRender($o=null){
		igk_notifyctrl()->setNotifyHost($this);
		return true;
	}
	public function RenderComplete($o=null){
	}
}


final class IGKHtmlDataEntryItem extends IGKHtmlDiv
{
	public function __construct(){
		parent::__construct();
	}	
	public function Render($options = null)
	{
		return parent::innerHTML($options);
	}
	public function LoadData($r, $visibleName=null){
		if ($r==null)
			return;
		if (method_exists(get_class($r), "getRows"))
		{
			foreach($r->Rows as $k=>$v)
			{
				$i = $this->add("item");
				$i->setAttributes($v);
				if ($visibleName)
					$i->Content = $v->$visibleName;
			}
		}
		else{
			$i = $this->add("item");
			$i->setAttributes($r);
			if ($visibleName)
				$i->Content = $v->$visibleName;
			
		}
	}
}
final class IGKHtmlXmlViewerItem extends IGKHtmlDiv
{
	public function __construct(){
		parent::__construct();
		$this["class"] = "igk-xml-viewer";
	}	
	public function Load($content, $context=IGKHtmlContext::XML){//xmlviewer	
		if (empty($content))
			return ;
		$c = IGKHtmlReader::Load($content,$context);
		
		
		//$r = igk_getv($c->Childs,0);		
		$root = null;
		// if ($r)
		// {
			foreach($c->Childs	as $k=>$v){
				$c = $this->loadItem($v, $this);
				if (!$root && ($v->NodeType == IGKXMLNodeType::ELEMENT)){
					$root = $v;
				}				
			}
		// }
		$this["igk:loaded"]=1;
	}
	private function __renderDepth($target, $depth){
		if ($depth>0)
		{
			for($i = 0 ; $i< $depth; $i++)
			{
				$target->add("span")->setClass("t")->addSpace();
			}
		}
	}
	public function loadItem($r, $target, $depth=0)
	{
		// $btarget = $target;
		// $target = $target->addDiv();
		$this->__renderDepth($target, $depth);
		
		$target->add("span")->setClass("s")->Content = "&lt;".$r->TagName;		
		
		//render attribute
		if ($r->HasAttributes)	
		{
			
			foreach($r->Attributes->ToArray() as $k=>$v)
			{
				$target->addSpace();
				$target->add("span")->setClass("attr")->Content = $k;
				$target->add("span")->setClass("o")->Content = "=";
				$target->add("span")->setClass("attrv")->Content = "\"".IGKHtmlUtils::GetValue($v)."\"";
			}
		}	
		$s = IGKHtmlUtils::GetValue($r->Content);
		
		if ($r->HasChilds || !empty($s))
		{
			
			$target->add("span")->setClass("s")->Content = "&gt;";
			
			if (!empty($s)){
				$target->add("span")->setClass("tx")->Content = $s;
			}
			
			foreach($r->Childs as $k=>$v){
				$target->addBr();
				switch($v->NodeType)
				{
					case IGKXMLNodeType::COMMENT:						
						$target->add("span")->setClass("c")->Content = "&lt;!--".IGKHtmlUtils::GetValue($v->Content)."--&gt;";						
						break;
					case IGKXMLNodeType::TEXT:
						$target->add("span")->setClass("tx")->Content = IGKHtmlUtils::GetValue($v->Content);
					break;
					default:
						$c = $this->loadItem($v, $this,$depth+1);
					break;
				}
			}
			if ($r->HasChilds){
			$target->addBr();
			$this->__renderDepth($target, $depth);
			}
			$target->add("span")->setClass("e")->Content = "&lt;/".$r->TagName."&gt;";
		}
		else{
			$target->add("span")->setClass("s")->Content = "/&gt;";
		}
		// $target = $btarget;
	}

	public function initDemo($t){
		$t->addDiv()->addSectionTitle(5)->Content = "Samples ";
		
		$t->addDiv()->addPhpCode()->Content = "\$t->addXmlViewer()->Load('[xml_content]');";
		$this->ClearChilds();
		//$this->addDiv()->Content = "XmlViewer Demo";
		
		$this->Load(<<<EOF
<demo attr_1="attrib_definition" >The viewer<i >sample</i></demo>
EOF
, IGKHtmlContext::XML);
	}
}

final class IGKHtmlAJXViewItem extends IGKHtmlDiv
{
	private $m_uri;
	private $m_script;
	private $m_scriptfunction;
	private $m_lineWaiter;
	
	public function getUri(){return $this->m_uri;}
	public function setUri($uri){$this->m_uri = $uri; return $this;}
	public function getScript(){return $this->m_scriptfunction;}
	public function setScript($script){$this->m_scriptfunction = $script; return $this;}
	
	public function __construct(){		
		parent::__construct("div");			
		$this->m_lineWaiter = $this->addLineWaiter();
		$this->m_script = $this->addScript();
	}
	public function AcceptRender($options = null){
	
		if (!empty($this->m_uri))
		{
			$expr = "";
			if ($this->m_scriptfunction){
				$expr = ", function(xhr) {{{$this->m_scriptfunction}}}";
			}
			$this->m_script->Content = "\$ns_igk.winui.ajxview.init('{$this->m_uri}' $expr);";
		}
		else
			return IGK_STR_EMPTY;
		return parent::AcceptRender($options);
	}
	// public function initDemo($t){
		//$this->m_uri = igk_io_baseUri();//"http:://www.google.be";
		// $this->m_uri = igk_io_baseUri()./;//."/api/info";
	// }
	
}

final class IGKHtmlToggleButtonItem extends IGKHtmlItem
{
	public function getClassProperty(){
		return $this["igk-toggle-class"];
	}
	public function setClassProperty($value){
		return $this["igk-toggle-class"] = $value;
	}
	public function getTarget(){
		return $this["igk-toggle-target"];
	}
	public function setTarget($target){
		if ($target==null)
		{
			$this["igk-toggle-target"] = null;
			return;
		}
		if (is_string($target)){
			$this["igk-toggle-target"] = $target;
		}
		else {
			$h = $this["igk-toggle-target"];
			if (is_object($h) && get_class($h) == "IGKHtmlTargetValue")
			{
				$h->setTarget($target);
			}
			else 
				$this["igk-toggle-target"] = new IGKHtmlTargetValue($target);
		}
	}
	public function __construct(){
		parent::__construct("button");
		$this["class"] = "igk-toggle-button";		
		$this["igk-toggle-button"] = true;
		$this["igk-toggle-state"] = "collapse";
	}
	public function addBar($c=1){
		$this->clearChilds();
		for($i=0; $i<$c;$i++)
			$this->add("span")->setClass("igk-iconbar dispb");
		return $this;
	}
}

//
final class IGKHtmlPaginationItem extends IGKHtmlItem
{
	var $CurrentPage;
	var $MaxPage;
	var $Offset;
	var $NextUri;
	var $PrevUri;
	private $m_first;
	private $m_last;
	
	public function SetUp($max, $current, $visible, $uri, $shift=0, $tag='form')
	{		
		$this->CurrentPage = $current;
		$this->MaxPage = $max;
		
		if ($current >= $visible){
			$r = (int) round($current % $visible);		
			$shift = $visible * (int)floor($current/$visible);		
			$visible = min($max, $shift+$visible);
		}
			
		for($i = $shift; $i < $visible; $i++)
		{
			$p = $this->addPage($i+1, "javascript: ns_igk.ajx.post('".$uri."&page=".$i."', null, ns_igk.ajx.getResponseNodeFunction(this, '{$tag}') ); return false;");
			if ($i == $current)
			{
				$p["class"] = "igk-active";
			}
		}
	}
	public function __construct(){
		parent::__construct("div");
		$this["class"]="igk-pagination";
		$this->Offset = 10;
		
		$b = $this->add("li");

		$b->addA("")->Content = "&laquo;";
		$b->setIndex(-0x0FFF);
		
		$this->m_first = $b;
		
		$b = $this->add("li");
		$b->setIndex(0xFFFF);
		$b->addA("")->Content = "&raquo;";
		$this->m_last = $b;
	}
	public function addPage($index, $uri){//add pagination page
		$li = $this->add("li");
		$li->addA($uri)->setContent( $index);
		return $li;
	}
	// public function Render($options=null){		
		// $r = parent::Render($options);
		// return $r;
	// }
	public function flush(){//generate properties
		if ($this->CurrentPage==0)
			$this->m_first->setClass("igk-inactive");
		else{
			$h = $this->m_first->getChildAtIndex(0);
			$h["href"] = $this->PrevUri;
		}
		if ($this->CurrentPage >= ($this->MaxPage-1)){
			$this->m_last->setClass("igk-inactive");
		}
		else{
			$h = $this->m_last->getChildAtIndex(0);
			$h["href"] = $this->NextUri;
		}
	}
	
	public function initDemo($t){
		$t->addDiv()->Content = "Panigation - Demonstration";
		$this->SetUp(10, 4, true, "?page=");
		$this->flush();
	}
}

final class IGKHtmlGroupControlItem extends IGKHtmlItem{
	public function __construct($name=null){
		parent::__construct("div");
		$this["class"] = "igk-form-group";
	}
}

final class IGKHtmlBreadCrumbsItem extends IGKHtmlItem{
	public function __construct(){
		parent::__construct("ol");
		$this["class"] = "igk-breadcrumbs";
	}
	public function addItem($uri, $content){		
		$li = $this->add("li");
		$li->addA($uri)->Content = $content;
		return $li;
	}
}



class IGKHtmlAuthorizationNodeItem extends IGKHtmlItem
{
	private $m_AccessKey; //authorization access key
	private $m_authCtrl; //authorization controller
	public function getAuthCtrl(){
		return $this->m_authCtrl;
	}
	public function setAuthCtrl($v){
		$this->m_authCtrl = $v;
		return $this;		
	}
	public function getAccessKey(){
		return $this->m_AccessKey;
	}
	public function setAccessKey($v){
		$this->m_AccessKey = $v;
		return $this;
	}
	public function getIsVisible(){
		$c = $this->m_AccessKey;
		if (empty($c))
			return false;
			
		return igk_sys_authorize($c, $this->m_authCtrl);
	}
	public function __construct($tag=null,  $accesskey=null,$authCtrl=null){
		parent::__construct($tag);		
		$this->m_authCtrl = $authCtrl;
		$this->m_AccessKey = $accesskey;
	}
}
//----------------------------------------------------------------------------
// BINDING
//----------------------------------------------------------------------------

//node that will be only render on web master node
class IGKHtmlBindDataNodeItem extends IGKHtmlCtrlNodeItemBase{
	private $m_File;
	private $m_Row;
	public function getFile(){ return $this->m_File; }
	public function getRow(){ return $this->m_Row; }
	
	public function setFile($value){ $this->m_File= $value; }
	public function setRow($value){  $this->m_Row = $value; }
	
	public function __construct(){
		parent::__construct("div");		
	}
	public function Render($options=null){	
		$this->ClearChilds();	
		igk_html_binddata($this->Ctrl, $this, $this->File, $this->Row, false);				
		return parent::Render($options);
	}
	public function initProperties($t){
		foreach($t as $k=>$v){
			$this->$k = $v;
		}
	}
}


///<summary>used to bind atricle from controller in ajx context</summary>
final class IGKHtmlAJXBindDataNodeItem extends IGKHtmlComponentNodeItem
{
	var $Ctrl;
	var $Article;
	var $Row;
	private $m_invoked;

	public function __construct(){
		parent::__construct("div");
	}
	public function invokeUri(){		
		$d = igk_createNode("div");
		igk_html_binddata($this->Ctrl, $d , $this->Article, $this->Row);
		$d->RenderAJX();
		$this->m_invoked = true;
		$this->add($d);
		igk_exit();
	}
	public function getCanAddChild(){
		return false;
	}
	public function Render($options =null){
		if (!$this->m_invoked){
			$this["igk-js-init-uri"] =  $this->getController()->getUri("invokeUri", $this);
		}
		return parent::Render($options);
	}
}
///<summary>represent the base contrtoller node. item</summary>
final class IGKHtmlCtrlViewNodeItem extends IGKHtmlCtrlNodeItemBase
{
	private $m_optionsTag;
	private $m_content;
	
	public function getOptionNode(){ return $this->m_optionsTag; }
	
	public function __construct($tagName=null){		
		if ($tagName == null)
			$tagName = igk_sys_getconfig("app_default_controller_tag_name", "div");
		
		parent::__construct($tagName);	
		$this->m_content = igk_html_node_NoTagNode();//igk_createNoTagNode();
		$this->m_optionsTag = igk_html_node_webMasterNode();//new IGKHtmlWebMasterNodeItem();
		$this->m_optionsTag->setClass("igk-ctrl-view-node-options");
		parent::add($this->m_optionsTag);
		parent::add($this->m_content);		
		
	}
	public function attachChild($child){
		$this->m_content->attachChild($child);
	}
	public function detachChild($child){
	$this->	m_content->detachChild($child);
	}
	public function ClearChilds(){//override clear content child
		$this->m_content->ClearChilds();
	}
	public function add($nameorchilds, $attributes=null, $index=null){
		return $this->m_content->add($nameorchilds, $attributes, $index);
	}
	public function remove($childs, $setParent=1){
		$this->m_content->remove($childs, $setParent);
	}
	protected function innerHTML(& $options=null){
		igk_wln("inner ...");
		$o = "";
		$o .= $this->m_content->getinnerHTML($options);
		if ($this->m_optionsTag->getIsVisible()){
			$o .= $this->m_optionsTag->getinnerHTML($options);
		}
		return $o;
	}
	public function AcceptRender($o=null){
		igk_wln("accept render .".$this->IsVisible);
		return $this->IsVisible;
	}
	public function RenderComplete($o=null){
	}
}

///<summary> Item parent for rollin child</summary>
final class IGKHtmlRollOwnerItem extends IGKHtmlItem
{
	private $m_content;
	private $m_rollin;
	
	public function getRoll(){
		return $this->m_rollin;
	}
	public function __construct(){		
		parent::__construct("div");
		$this->setClass("igk-roll-owner");		
		$this->m_rollin = parent::add("div")->setClass("igk-roll-in");
		$this->m_content = parent::add("div");
	}
	public function ClearChilds(){//override clear content child
		$this->m_content->ClearChilds();
	}
	public function add($s,$t=null,$b=null){
		return $this->m_content->add($s,$t,$b);
	}
	public function getContent(){
		return $this->m_content->Content;
	}
	public function setContent($value){
		$this->m_content->setContent($value);
		return $this;
	}
	protected function innerHTML(& $options = null ){
		$o  = "";
		$o .= $this->m_rollin->Render($options);
		$o .= $this->m_content->Render($options);
		return $o;
	}
	public function initDemo($t){
		$this->Content = "Please Pass your mouse here";
		$this->Roll->Content = "Info";
	}
}

///<summary> Item parent for rollin on touch screen</summary>
final class IGKHtmlTouchRollOwnerItem extends IGKHtmlItem
{
	private $m_content;
	private $m_rollin;
	
	public function getRoll(){
		return $this->m_rollin;
	}
	public function __construct(){		
		parent::__construct("div");
		$this->setClass("igk-touch-roll-owner");		
		$this->m_rollin = parent::add("div")->setClass("igk-roll-in");
		$this->m_content = parent::add("div");
	}
	public function ClearChilds(){//override clear content child
		$this->m_content->ClearChilds();
	}
	public function add($s,$t=null,$b=null){
		return $this->m_content->add($s,$t,$b);
	}
	public function getContent(){
		return $this->m_content->Content;
	}
	public function setContent($value){
		$this->m_content->setContent($value);
		return $this;
	}
	protected function innerHTML(& $options = null ){
		$o  = "";
		$o .= $this->m_rollin->Render($options);
		$o .= $this->m_content->Render($options);
		return $o;
	}
	public function initDemo($t){
		$this->Content = "Please Click here to show roll in";
		$this->Roll->Content = "Info";
	}
}

///<summary>Action bar that is fixed on the document</summary>
final class IGKHtmlFixedActionBarItem extends IGKHtmlItem
{
	public function __construct(){		
		parent::__construct("div");
		$this->setClass("igk-fixed-action-bar");
		$this->setAttribute("igk-js-fix-loc-scroll-width",1);
		$this->m_c = parent::add("div");
	}
	public function getTargetId(){
		return $this["igkns:fixed-target"];//"igk-fixed-action-bar-target"] ;
	}
	public function setTargetId($v){//value is a html selector expression
		$this["igk:fixed-target"]=
		//["igk-fixed-action-bar-target"]  = 
		$v;
		return $this;
	}
}

final class IGKHtmlAJXFormItem extends IGKHtmlItem
{
	public function __construct(){		
		parent::__construct("form");
		$this["method"] = "POST";
		$this["action"] = ".";
		$this->setClass("igk-col");		
		$this["onsubmit"] = "javascript: ns_igk.form.ajxform.submit(this); return false;";
	}
	public function getAction(){return $this["action"]; }
	public function setAction(){$this["action"] = $v; }
}

//JS CONTROL FOR AJX rendering

final class IGKHtmlJSMsBoxItem extends IGKHtmlScript
{
	var $m_title;
	var $m_content;
	
	public function getContent(){ return $this->m_content; }
	public function getTitle(){ return $this->m_title; }
	public function setTitle($v){ $this->m_title = $v; }
	public function __construct(){		
		parent::__construct();
		$this->setClass("igk-js-msbox");
		$this->m_content = igk_createNode("div");		
	}
	public function Render($o=null){
	
		$title = $this->Title;
		$content = $this->m_content->Render(null);
		return <<<OF
<script type="text/javascript" language="javascript" >
igk.winui.notify.showMsBox('{$title}', '{$content}');
</script>
OF;
	}
}

final class IGKHtmlGKDSNodeItem extends IGKHtmlItem
{		
	public function getUri(){ return $this["src"]; }
	public function setUri($v){ $this["src"] = $v; }
	public function __construct(){		
		parent::__construct("igk:gkds");
	}
	public function initDemo($t){
		$t->addA("#")->Content  = "PickImage";		
		$this["src"] = igk_html_uri( igk_io_baseUri()."/".igk_io_basePath(dirname(__FILE__)."/Data/R/demo.gkds"));
	}
}

final class IGKHtmlCanvaNodeItem extends IGKHtmlItem
{
	private $m_uri;
	public function getUri(){ return $this->m_uri; }
	public function setUri($v){ $this->m_uri = $v; }
	public function __construct($ctrl=null){
		parent::__construct("canvas");
		$this->m_ctrl = $ctrl;	
		$this["width"] = "320px";
		$this ["height"] = "500px;";
	}
	protected function innerHTML(& $xmlOption=null)
	{
		if ($this->m_uri){
		$o = parent::innerHTML($xmloption );
		$script =  igk_createNode("script");
		$script->Content = "window.igk.winui.canva.initctrl('".$this->m_uri."');";
		$o .= $script->Render();
		unset($script);
		return $o;
		}
		return null;
	}
}



//<summary>ArticleView</summary>
final class IGKHtmlArticleViewItem extends IGKHtmlCtrlNodeItemBase
{
	private $m_view;
	private $m_row;
	private $m_InnerOnly;
	private $m_iview;
	public function setFile($v){ $this->m_view = $v; $this->m_iview = true; return $this; }
	public function getFile(){ return $this->m_view;}
	public function getRow(){ return $this->m_row;}
	public function setRow($v){	$this->m_row = $v; $this->m_iview = true; return $this; }	
	public function setInnerOnly($v){ $this->m_InnerOnly = $v; $this->m_iview = true; return $this; }
	public function getInnerOnly(){ return $this->m_InnerOnly;}
	public function __construct($file=null, $ctrl=null, $row=null){
		parent::__construct("div");
		$this->m_view  = $file;
		$this->m_row  = $row;
		$this->Ctrl = $ctrl;
	}
	// protected function loadingComplete(){
		// $this->__setupArticle();
		// parent::loadingComplete();
	// }
	public function initView(){
		if (!$this->m_iview)
		{
			return;
		}
		$this->m_iview = false;	
		$this->ClearChilds();		
		$c = igk_getctrl($this->getCtrl());		
		$f = $this->m_view; 		
		$r = $this->m_row;
		
		if ($c && $f){			
			igk_html_binddata($c, $this, $f, $r, false, false);				
		}
		else {
			if (IGKViewMode::IsWebMaster())
				$this->Content = "no data to bind#[ctrl:{$this->getCtrl()},file:{$this->getFile()}, c:{$c}]";
		}
		
	}
	public function AcceptRender($o=null){				
		if ($this->m_iview){
			$this->initView();
		}
		// else{//error when init view in argument princible
			// igk_wln("bbb");
			// igk_wln(igk_show_trace());
		// }
		return $this->IsVisible;
	}
	protected function __getRenderingChildren($option=null){
		if ($this->m_InnerOnly){
			return $this->Childs->ToArray();
		}//igk_wln(igk_show_trace());
		return parent::__getRenderingChildren($option=null);
	}
}


///<summary> used in article to setup controller item
final class IGKHtmlMenuBarItem extends IGKHtmlCtrlNodeItemBase
{
	private $m_view;
	public function setView($v){ $this->m_view = $v;}
	
	public function __construct(){
		parent::__construct("div");
		$this->setClass("igk-menu-bar");		
	}
}

///<summary> used in article to setup controller item
final class IGKHtmlContactFormItem extends IGKHtmlComponentNodeItem
{
	private $m_uri;
	private $m_msbox ;
	public function getUri($v){ return $this->m_uri ;}
	public function setUri($v){ $this->m_uri = $v; return $this;}
	
	public function __construct(){
		parent::__construct("form");	
		$this["method"] = "POST";
		$this["enctype"]= "multipart/form-data";
		$this->m_msbox = $this->addDiv();
		$i = $this->addSLabelInput("clYourName","text");
		$i->input->setClass("igk-form-control igk-form-required");
		$i = $this->addSLabelInput("clEmail","email");
		$i->input->setClass("igk-form-control igk-form-required");
		$i =$this->addSLabelInput("clSubject","text");
		$i->input->setClass("igk-form-control igk-form-required");
		
		$i =$this->addSLabelTextarea("clMessage","text");
		$i->textarea->setClass("igk-form-control");		
		$this->addInput("btn_s", "submit", R::ngets("btn.sendMessage"))->setClass("igk-btn igk-btn-default floatr");
		
		$this["action"] = $this->getComponentUri("sendmsg");
		$this["igk-ajx-form"] = "1";
		$this["igk-ajx-form-target"]= "=";
	}
	public function sendmsg(){
		$o = igk_get_robj();
		$this->m_msbox->ClearChilds();
		if (!igk_html_validate($o, array("clEmail"=>"mail")))
		{
			$this->m_msbox->addNotifyBox("danger")->Content = igk_html_validate_error();
		}
		else
		{
			$this->m_msbox->Content = null;
			$conf = $this->getController()->App->Configs;
			$m = $conf->mail_contact;		
			$d = igk_createNode("div");
			$d->addDiv()->Content = "From : ".$o->clYourName;
			$d->addDiv()->Content = $o->clMessage;
			$smg = $d->Render(null);
			$b = igk_mail_sendmail( $m, "no-reply@".$conf->website_domain, $o->clSubject, $smg);
			if ($b){
				$this->m_msbox->addNotifyBox("success")
				->setAutohide(true)
				->Content = R::ngets("msg.message.send");
			}			
			igk_resetr();
		}
		$this->RenderAJX();
		exit;
	}

	
}

final class IGKHtmlConnectFormItem extends IGKHtmlCtrlComponentNodeItemBase
{
	private $m_frm;
	private $m_goodUri;
	private $m_badUri;
	
	
	public function getBadUri(){
		return $this->m_badUri;
	}
	public function setBadUri($v){
		$this->m_badUri = $v;
		return $this;
	}
	public function setGoodUri($v){
		$this->m_goodUri = $v; return $this;
	}
	public function getGoodUri(){
		return $this->m_goodUri;
	}
	public function getForm(){
		return $this->m_frm;
	}
	public function connect(){
		$c = $this->Ctrl;
		$e = '#!\e=';
		if ($c)
		{
			$b = $c->connect();
			if ($b){
				if ($this->m_goodUri)
					igk_navto($this->m_goodUri);
			}
			else{
				$e.=1;
				//igk_notifyctrl()->addError(R::ngets("e.login.failed_1", "#failed"));
				igk_notifyctrl("login")->addErrorr("e.login.failed_1", "#failed");
				if ($this->m_badUri)
				{
					igk_navto($this->m_badUri.$e);
				}
			}
		}
		else{
			igk_wln("no  controller setup");
		}
	}
	public function getloginType(){
		return $this->m_loginType;
	}
	public function setloginType($v){ $this->m_loginType = $value; return $this; }
	public function __construct($type="email"){
		parent::__construct("div");
		$this->Ctrl = IGK_USER_CTRL;
		$this->m_loginType = $type;
		$this->setClass("igk-connect-form");
		$this->m_frm = parent::addForm();
		$this->m_frm["action"] = $this->getComponentUri("connect");
		$this->m_frm["igk-form-validate"] = 1;
		$i = $this->m_frm->addDiv()->addSLabelInput("clLogin",  $type);
		$i->input["igk-input-focus"] = 1;
		$this->m_frm->addDiv()->addSLabelInput("clPwd","password");
	
		$d = $this->m_frm->addDiv();	
	
		$m = $d->addDiv();
		$m->addInput("clRememberMe", "checkbox", "rm-me")->setAttribute("checked", 1);
		$m->addLabel("lb.clRememberMe", "clRememberMe");
		$m->addA("signup")->setStyle("padding-left:8px")->Content = R::ngets("lb.register");
	
	
		$d = $this->m_frm->addDiv();	
	
		$d->addDiv()->addInput("bnt_connect", "submit", R::ngets("btn.connect"))
		->setClass("igk-btn igk-btn-default igk-sm-fitw");
			
	}
	public function ClearChilds(){
		$this->m_frm->ClearChilds();
	}
	public function add($nameortag, $attribute=null, $index=null){
		if ($this->m_frm!=null)
			return $this->m_frm->add($nameortag, $index);
		return parent::add($nameortag,$attribute, $index);
	}
}

///<summary>represent a tab control node where tab contains came from ajx query</summary>
final class IGKHtmlAJXTabControlItem extends IGKHtmlCtrlComponentNodeItemBase
{
	private $m_tablist;
	private $m_tabcontent;
	private $m_selected;
	private $m_tabViewListener;
	
	private static $demoComponent;
	
	public function setComponentListener($listener,$param){		
		$g = $param;
		if (!$g)return;		
		
		
		
		igk_exit();
	}
	public function setTabViewListener($o){
		$this->m_tabViewListener = $o;
	}
	public function __construct(){
		parent::__construct("div");
		
		// igk_wln("create item ");
		// throw new Exception("df");
		$this->setClass("igk-tabcontrol");
		$h = $this->addDiv()->setClass("igk-tab-h");
		$ul = $h->add("ul");
		$this->m_tablist = $ul;
		$c = $this->addDiv();
		$this->m_tabcontent = $c;
		$this->m_tabcontent->setClass("igk-tabcontent");
		
	}
	
	
	public function __AcceptRender($opt=null){
		
		if ($this->m_tabViewListener !== null){
			$this->m_tabViewListener->TabViewPage($this, $this->m_tablist, $this->m_tabcontent);
		}
		return parent::__AcceptRender($opt);
	}
	
	public function ClearChilds(){
		
		$this->m_tablist->ClearChilds();
		$this->m_tabcontent->ClearChilds();
	}
	public function initDemo($t){
		$s = igk_get_component(__METHOD__);
		if ($s){
			$s->Dispose();
			igk_wln("free compoent");
		}
		// igk_wln("init demo.......................----");
		// igk_log_write_i("initDemo", __CLASS__." init demo ".$this->getComponentId());
		// throw new Exception("i");
		$this->ClearChilds();		
		$this->addTabPage("page1", $this->getComponentUri("showpage/1"), true);
		$this->addTabPage("page2", $this->getComponentUri("showpage/2"), false);
		$this->addTabPage("page3", $this->getComponentUri("showpage/4"), false);
		$i = $this->m_selected ? $this->m_selected:1;
		$this->m_tabcontent->Content = igk_ob_get_func(array($this, "showpage"), array($i));
		
		$t->addDiv()->Content = "Code Sample";
		$t->addDiv()->addCodeViewer()->Content = <<<EOF
\$this->ClearChilds();		
\$this->addTabPage("page1", \$this->getComponentUri("showpage/1"), true);
\$this->addTabPage("page2", \$this->getComponentUri("showpage/2"), false);
\$this->addTabPage("page3", \$this->getComponentUri("showpage/4"), false);

EOF;
// \$i = \$this->m_selected ? \$this->m_selected:1;
// \$this->m_tabcontent->Content = igk_ob_get_func(array(\$this, "showpage"), array(\$i));
		igk_reg_component(__METHOD__, $this);
		
	}
	///<summary> , "for demonstration"</summary>
	public function showpage($index=0){
		if ($this->Ctrl)
		{
			$this->Ctrl->showTabPage($index);
		}
		else{
			$d = igk_createNode("div");
			$d->Content = "Demo page ".$index;
			$this->m_selected = $index;
			$d->RenderAJX();
		}
	}
	public function addTabPage($content=null, $uri=null, $active=false, $method="GET"){		
		$li = $this->m_tablist->add("li");
		$li->setParam("uri", $uri);
		$li->setParam("method", $method);
		
		$li->addA($uri)
		->setAttribute("igk-ajx-tab-lnk",1)		
		->setContent($content);
		if ($active){
			if ($this->m_selected){
				$this->m_selected->setClass("-igk-active");
			}
			$li->setClass("igk-active");
			$i = 0;
			if ($uri){
				$this->m_tabcontent->addAJXReplaceContent($uri, $method);
				
			}
			$this->m_selected = $li;
		}
		return $li;
	}	
	public function select($i){
		if ($this->m_selected){
			$this->m_selected->setClass("-igk-active");
		}
		$this->m_tabcontent->clearChilds();
		$li = $this->m_tablist->Childs[$i];
		if ($li){
			$uri = $li->getParam("uri");
			$method = $li->getParam("method");
			$li->setClass("igk-active");
			if ($uri){
				$this->m_tabcontent->addAJXReplaceContent($uri, $method);				
			}
		}
	}
}


final class IGKHtmlHomeButtonItem extends IGKHtmlItem
{
	private $m_targetid;
	public function getTargetId(){	return $this->m_targetid;}
	public function setTargetId($v){	$this->m_targetid = $v; return $this; }
	
	public function getTargetLink(){
		 return "#!/".$this->m_targetid;
	}
	public function __construct(){
		parent::__construct("div");
		$this->m_targetid = "home";
		$this->setClass("igk-gohome-btn igk-trans-all-200ms")
		->setAttribute("igk-js-eval",
		"igk.winui.fn.fixscroll.init({update: function(v){ if(v.scroll.y > 0) { this.addClass('igk-show'); }  else this.rmClass('igk-show') ;}});");

$this->addA("#")
->setClass("igk-glyph-home dispb fitw fith")
->setAttribute("igk-nav-link", $this->m_targetid)
->addCenterBox()->setClass("fith")->add("span")
->setClass("glyphicons")->Content = " &#xe021;";
	}
}


///<summary> represent a trackbar winui item </summary>
final class IGKHtmlTrackbarItem extends IGKHtmlItem
{	
	public function __construct(){
		parent::__construct("div");
		$this->setClass("igk-trb");
		$this->addDiv()->setClass("igk-trb-cur");
	}
	public function initDemo($t){
		$this["igk-trb-data"] = "{update: function(d){ if (typeof(d.bar.rep) == 'undefined'){ d.bar.rep = d.bar.target.add('div'); } d.bar.rep.setHtml(d.progress); }}";
	}
}

final class IGKHtmlCodeViewerItem extends IGKHtmlItem
{
	private $m_language;
	
	public function getLanguage(){	return $this->m_language; }
	public function setLanguage($v){ $this->m_language = $v;  return $this; }

	public function __construct(){
		parent::__construct("code");
		$this["lang"] = new IGKValueListener($this, "Language");
	}
	public function initDemo($t){
		$this->Language = "php";
		$this["class"] = "phpcode";
		$this->Content = <<<EOF
<?php 
echo 'hello the sample'; 
?>
EOF;

	}
}


final class IGKHtmlPagePreviewItem extends IGKHtmlItem
{
	private $m_uri;
	
	public function __construct($uri = null){
		parent::__construct("div");
		$this->m_uri = $uri;
	}
	public function View(){
		if ($this->m_uri )
		{
			$d = igk_io_basePath(dirname(__FILE__)."/cgi-bin/previewpage.cgi");			
			if (empty($d) == false)
			{
				$uri = igk_io_baseUri()."/".igk_html_uri($d);
				$s = igk_post_uri($uri, array("query"=> $this->m_uri));
				igk_wln("check for ". $uri ."<br />"
				.$this->m_uri."<br />"
				. " in : <br />out : ".$s);
			}
		}
	}
	public function initDemo($t){
	}
}

///<summary>component in charge of searching on the current page</summary>
final class IGKHtmlSearchBarItem extends IGKHtmlCtrlComponentNodeItemBase
{
	private $m_search;
	private $m_ajx;
	private $m_uri;
	public function getTargetId(){return $this->m_targetid; }
	public function setTargetId($v){ $this->m_targetid =$v; return $this; }
	public function getUri(){return $this->m_uri; }
	public function setUri($v){ $this->m_uri =$v; return $this; }
	public function getAJX(){return $this->m_ajx; }
	public function setAJX($v){ $this->m_ajx =$v; return $this; }
	
	public function __construct(){
		parent::__construct("div");
		
	}	
	public function View(){
		$this->ClearChilds();
		$s = $this->addSearch();		
		$s->Uri = $this->Uri;
		$s->TargetId = $this->TargetId;
		$s->AJX = $this->AJX;
		$s->loadingComplete();			
		$this->m_search = $s;
		
	}
}
final class IGKHtmlChildNodeViewItem extends IGKHtmlItem
{
	private $m_basic;
	public function getBasicTag(){	return $this->m_basic;}
	public function setBasicTag($v){	$this->m_basic = $v; return $this; }
	public function __construct($basicTag= null){
		parent::__construct("igk:childnodeview");
		$this->m_basic = $basicTag;
	}	
	public function AddChild(){
		if ($this->m_basic){
			return $this->add($this->m_basic);
		}
		return null;
	}
	public function Render($o=null){		
		$k = 0;
		return $this->__renderVisibleChild($this->__getRenderingChildren($option=null) , $o, $k);
	}
}


final class IGKHtmlFooterFixedBoxItem extends IGKHtmlItem
{
	public function __construct(){
		parent::__construct("div");
		$this->setClass("posfix loc_b loc_l loc_r");
		$this->setAttribute("igk-js-fix-loc-scroll-width", "1");
	}	
}

///<summary>Represent IGK App HeaderBar Item</summmary>
final class IGKHtmlIGKAppHeaderBarItem extends IGKHtmlItem
{
	private $m_apps;
	private $m_Box;
	
	public function getBox(){
		return $this->m_Box;
	}
	public function __construct($app){
		if ($app==null)
			igk_die("argument exception \$app is null");
		parent::__construct("div");		
		$this->m_apps = $app;
		$this["class"] = "igk-app-header-bar";
		$this->initView();

	}	
	public function initDemo($t){
		
	}
	public function initView(){
		$this->ClearChilds();
		$r = $this->addRow()->setClass("no-margin");
$h1 = $r->addDiv()
->setClass(" igk-col-lg-12-2 fith floatl presentation");
$title = $h1->addDiv()->addA(igk_io_baseUri())//igk_io_currentDomainUri())
->setClass("dispb no-decoration");
$title->add("span")
->setClass("dispib posr")
->setStyle("left:10px; top:12px;")
->Content = "igkdev";
$title->addDiv()->setClass("igk-title-4")->Content = $this->m_apps->AppTitle;
$this->m_Box = $r->addDiv();
$this->m_Box->setClass("igk-col-lg-12-10 .ibox");

	}
}

class IGKHtmlIGKHeaderBarItem extends IGKHtmlItem
{
	private $m_Box;
	private $m_title;
	private $m_uri;
	
	public function getTitle(){return $this->m_title;  }
	public function setTitle($v){$this->m_title = $v; return $this;}
	public function getUri(){return $this->m_uri; }
	public function setUri($v){ $this->m_uri = $v; return $this; }
	public function getCompanyTitle(){
		return igk_getv(IGKApp::getInstance()->Configs, "company_name", IGK_COMPANY);
	}
	public function getBox(){
		return $this->m_Box;
	}
	public function __construct(){
		parent::__construct("div");
		$this->setClass("igk-add-margin");		
		$this->m_uri = igk_io_baseDomainUri();
		$this->initView();
	}	
	public function initDemo($t){
	}
	public function initView(){
		$this->ClearChilds();
		$r = $this->addRow();
		$h1 = $r->addDiv()
		->setClass(" igk-col-lg-12-2 fith floatl presentation");
		$title = $h1->addDiv()->addA(new IGKValueListener($this, "Uri"))
		->setClass("dispb no-decoration");
		$title->add("span")
		->setClass("dispib posr")
		->setStyle("left:10px; top:12px;")
		->Content = new IGKValueListener($this,"CompanyTitle");
		$title->addDiv()->setClass("igk-title-4")->Content = new IGKValueListener($this, "Title");
		$this->m_Box = $r->addDiv();
		$this->m_Box->setClass("igk-col-lg-12-10 ibox");
	}
}
final class IGKHtmlIGKSysHeaderBarItem extends IGKHtmlIGKHeaderBarItem
{
	public function getCompanyTitle(){
		return IGK_COMPANY;
	}
	public function __construct(){
		parent::__construct();
	}
}



final class IGKHtmlCurrentUserInfoItem extends IGKHtmlItem
{
	private $m_display; 
	public function setDisplay($v){ $this->m_display =$v; return $this;}
	public function getDisplay(){return $this->m_display; }
	
	public function __construct(){
		parent::__construct("span");
		$this["class"] = "igk-u-inf";
		$this->m_display = "clLogin";
	}
	public function AcceptRender($o=null){
		$u = IGKApp::getInstance()->Session->User;		
		if ($u == null)
			return false;
		$d = $this->Display;
		$this->Content = $u->$d;
		return parent::getIsVisible();
	}
}

///<summary>used to select properties from data table</summary>
final class IGKHtmlCtrlSelectItem extends IGKHtmlCtrlNodeItemBase
{
	private $m_table;
	private $m_selected;	
	private $m_condition;
	public function getTable(){
		return $this->m_table;
	}	
	public function getSelected(){
		return $this->m_selected;
	}
	public function setSelected($v){
		$this->m_selected=$v;
		return $this;
	}	
	public function setTable($v){
	
		 $this->m_table  = $v; 
		 return $this;
	}	
	public function setCondition($cond){
		if (is_array($cond) || ($cond== null))
		{		
		 $this->m_condition  = $cond;
		}
		 return $this;
	}
	public function __construct(){
		parent::__construct("select");
		$this["class"] = "igk-form-control";
	}
	public function initView(){
		$this->ClearChilds();
		$t = $this->m_table;
		$c = $this->Ctrl;
		$r = igk_db_table_select_where($t, $this->m_condition, $c);	
	
		if ($r!=null && ($r->RowCount>0)){
			
			$display = "{\$row->clName}";
			$sel = $this->m_selected;
			$h = "";
			if ($sel){
				$h = "[func:\$row->clId== '$sel'?'selected=\"true\"' :null]";
			}
			$this->bindExpression("<option value=\"{\$row->clId}\" $h >{$display}</option>", $r->Rows);
		}
		
	}
	protected function bindExpression($expression, $entries){
		$c = new IGKHtmlItem("dummy");
		$c->LoadExpression($expression);
		igk_html_bind_node($this->Ctrl, $c, $this, $entries);	
	}
}
///<summary>use to add selection item</items>
final class IGKHtmlSelectNodeItem extends IGKHtmlItem
{
	public function __construct(){
		parent::__construct("select");
		$this["class"] = "igk-form-control";
	}
	public function addOption($i,$v, $selected=false){
		$o = $this->add("option")->setContent($v);
		$o["value"] = $i;
		if ($selected){
			$o["selected"]=1;
		}		
	}
	public function addOptions($tab, $selectedattributes=null, $selectedvalue=null){
		igk_html_build_select_option($this, $tab, $selectedattributes, $selectedvalue);		
	}
	public function initDemo($t){
	
		$this->addOptions(array("1","2","3"));
	}
}
///<summary>represent language selection options</items>
final class IGKHtmlSelectLangNodeItem extends IGKHtmlItem
{
	public function __construct(){
		parent::__construct("select");
		$this["onchange"]="javascript: ns_igk.ajx.get('?l='+this.value, null, ns_igk.ajx.fn.replace_body); return false;";
	}
	public function initView(){
		$this->ClearChilds();
		$ctrl = igk_getctrl(IGK_LANGUAGE_CTRL);
		$tab =array_merge($ctrl->Languages);	
		igk_html_build_select_option($this, $tab, array("allowEmpty"=>false, "keysupport"=>false), R::GetCurrentLang());		
	}
}

///<summary>represent language selection options</items>
final class IGKHtmlMemoryUsageInfoNodeItem extends IGKHtmlComponentNodeItem
{
	public function getMemoryInUsed(){return IGKNumber::GetMemorySize( memory_get_usage()); }
	public function getMemoryPeekInUsed(){return IGKNumber::GetMemorySize(memory_get_peak_usage()); }
	public function getComponents(){ return "Component : ". igk_count(igk_getctrl(IGK_COMPONENT_MANAGER_CTRL)->getComponents());}
	public function __construct(){
		parent::__construct("div");
		// igk_die("id");
		$this->add("div")->Content = new IGKValueListener($this, "MemoryInUsed");
		$this->add("div")->Content = new IGKValueListener($this, "MemoryPeekInUsed");
		$this->add("div")->Content = new IGKValueListener($this, "Components");		
		$b = $this->addActionBar();
		$b->addA($this->getComponentUri("clear_component"))->setClass("igk-btn")->Content = R::ngets("btn.clearComponent");
		$b->addAJXA($this->getComponentUri("component_info_ajx"))->setClass("igk-btn")->Content = R::ngets("btn.getComponentInfo");
		
		$uri = $this->getComponentUri("memoryinfo");
		
	}
	public function component_info_ajx(){
		$d = igk_createNode();
		$c = igk_getctrl(IGK_COMPONENT_MANAGER_CTRL)->getComponents();
		$tab = $d->add("table");
		foreach($c as $k=>$v){
			$r = $tab->add("tr");
			$r->add("td")->Content = $k;
			$r->add("td")->Content = get_class($v);
			
			$id = $v->getParam(IGK_DOC_ID_PARAM) ?? igk_getv($v, 'id');
			
			$r->add("td")->Content = "id: ".$id;//igk_getv($v, 'id');
			$r->add("td")->Content = method_exists($v, "getId") ? $v->getId() : "-";
			$r->add("td")->Content = method_exists($v, "getOwner")? $v->getOwner()->toString() : "-";
		}		
		igk_ajx_notify_dialog(R::gets("title.componentinfo"), $d);
		igk_exit();
	}
	public function memoryinfo(){		
		$this->RenderAJX();
		igk_exit();
	}
	public function clear_component(){		
		igk_getctrl(IGK_COMPONENT_MANAGER_CTRL)->DisposeAll();		
		session_destroy();
		igk_navtobaseuri();		
	}
	
}


///<summary>used to present defintion 
final class IGKHtmlPhpCodeItem extends IGKHtmlCtrlNodeItemBase
{
	public function __construct(){
		parent::__construct("code");
		$this["class"] = "igk-phpcodenode";
	}	
	public function _AddChild($d, $index=null){
		if ($this->IsLoading)
		{	
			if (is_object($d) && igk_reflection_class_extends($d, "IGKHtmlText"))
			{
				$this->m_content = $d->Content;
				return true;
			}
		}
		return parent::_AddChild($d, $index);
	}
}

///<summary>used to evaluate php code every time you demand for rendering</summary>
final class IGKHtmlPhpEvalCodeItem extends IGKHtmlCtrlNodeItemBase{
	
	var $params;	
	function __construct(){
		parent::__construct("div");
		$this["class"] = "igk-phpevalnode";
	}
	private function __evalin($context, $c)
	{
		extract($context);
		return eval($c);
	}
	public function __getRenderingChildren($options=null){		
		return array();		
	}
	public function Render($o=null){
		$bck = $this->Content;
		$this->Content = "";
		$c = "";
		$b = array_merge(array("ctrl"=>igk_getctrl($this->getCtrl()), "row"=>$this),  $this->params ?? array());
		
		$this->Content =  $this->__evalin($b, $bck);
		$this->NoOverride = 1;
		$c = igk_html_render_node($this, $options);//parent::Render($o);
		unset($this->NoOverride); 
		$this->Content = $bck;
		return $c;		
	}
}



final class IGKHtmlNotifyDialogBoxItem extends IGKHtmlItem
{
	private $m_Message;
	private $m_title;
	public function getTitle(){return $this->m_title; }
	public function getMessage(){return $this->m_Message; }
	
	public function __construct()
	{
		parent::__construct("div");
		$this["class"] = "igk-notify-box";
		$this->addDiv()->setClass("title")->Content = new IGKValueListener($this, 'Title');
		$this->addDiv()->setClass("msg")->Content = new IGKValueListener($this, 'Message');
		$this->addScript()->Content = <<<EOF
if(ns_igk)ns_igk.winui.notify.init();
EOF;
	}	
	public function show($title, $msg)
	{
		$this->m_title = $title;
		$this->m_Message = $msg;
		$this->setIsVisible(null);
		return $this;
	}
	public function RenderAJX(& $options=null){
		parent::RenderAJX($options);
		$this->setIsVisible(false);
	}
}

final class IGKHtmlNonEmptyNodeItem extends IGKHtmlItem{
	public function getIsVisible(){
		return ($this->ChildCount >0);
	}
	public function __construct($tag="div"){
		parent::__construct($tag);
	}
}



///</summary>used in ajx context to pass the controller node that will be replaced on client side</summary>
final class IGKHtmlAJXCtrlReplacementNode extends IGKHtmlItem
{
	private $m_ctrls;
	public function __construct(){
		parent::__construct("igk:replace-ctrl");
		$this->m_ctrls = array();
	}
	protected function getCanAddChild(){return false; }
	public function addCtrl($b){		
		$this->m_ctrls[$b->Name] = $b;
	}
	
	public function getChildCount(){return igk_count($this->m_nodes); }
	public function getChilds(){ return $this->m_nodes; }
	protected function __getRenderingChildren($option=null){
		$tab = array();
		foreach($this->m_ctrls as $k=>$v){
			if ( $v->TargetNode->IsVisible){
				$tab[] = $v->TargetNode;
			}
		}
		return $tab;
	}
	protected function innerHTML(& $o = null){
		$so = "";
		foreach($this->m_ctrls as $k=>$v){
			if ( $v->TargetNode->IsVisible){
			$so .= $v->TargetNode->Render($o);
			}
		}
		return $so;
	}
}
final class IGKHtmlAJXReplacementNode extends IGKHtmlItem{
	
	private $m_nodes;
	public function __construct(){
		parent::__construct("igk:replace-ctrl");	
		$this->m_nodes = array();
	}
	public function addNode($n,$tag=null){
		$this->m_nodes[] = $n;
	}
	protected function getCanAddChild(){return false; }
	public function getChildCount(){return igk_count($this->m_nodes); }
	public function getChilds(){ return $this->m_nodes; }
	protected function innerHTML(& $o = null){
		$so = "";
		foreach($this->m_nodes as $k=>$v){
			if ($v->IsVisible)
			$so .= $v->Render($o);
		}
		return $so;
	}
}
final class IGKHtmlPopWindowAItem extends IGKHtmlA{
	public function __construct($lnk=null){
		parent::__construct();
		$this["onclick"] = "javascript: ns_igk.winui.openUrl(this.href, this.wndName); return false;";
		$this["href"] = $lnk;
	}
}


final class IGKHtmlImgLnkItem extends IGKHtmlA{
	private $m_img;
	
	public function setAlt($v){ $this->m_img["alt"] = $v; return $this;  }
	public function getAlt(){return  $this->m_img["alt"] ;  }
	
	public function __construct($uri=null, $img=null, $width="16px", $height="16px", $desc=null){
		parent::__construct();
		$this["href"] = $uri;
		$this->m_img = $this->add("img", 
			array(
			"width"=>$width,
			"height"=>$height, 
			"src"=>R::GetImgUri(trim($img)),
			"alt"=>R::ngets($desc)));
			
	}
}



final class IGKHtmlDatePickerItem extends IGKHtmlItem{
	public function __construct($id=null){
		parent::__construct("div");
		if ($id){
			$this->_initview($id);			
		}
	}
	private function _initview($id){
		$this->addLabel($id);
		$this->addInput($id,"text", date('Y/m/d'));
	}
	public function initDemo($t){
		$this->_initview("sample");
	}
}
///for debugging
final class IGKHtmlRowColSpanNode extends IGKHtmlItem{
	private $m_rowid;
	public function __construct($id){
		parent::__construct("div");
		$this->m_rowid = $id;
	}
	public function add($n,$a=null,$i=null){		
		return parent::add($n,$a,$i);
	}	
	protected function _AddChild($i, $index=null){		
		$s = parent::_AddChild($i,$index);		
		return $s;
	}
	protected function _setUpChildIndex($child,$index=null){
		if ($index === null){			
			$i = $child->Index;		
			if (!is_numeric($i) || $child->AutoIndex){				
				$child->setIndex($this->ChildCount-1, true);
			}
		}
		else if(is_numeric($index)){ 				
			$child->setIndex($index,false);
		}			
	}
}
final class IGKHtmlRowColSpanItem extends IGKHtmlItem{
	private $m_cols;
	private $m_colindex;
	private $m_colsn;
	private $m_classstyle;
	
	public function __construct($cols=4, $classStyle="igk-col-lg-12-3" ){
		parent::__construct("div");
		$this["class"]="igk-row";
		$this->m_colsn = $cols;
		$this->m_classstyle = $classStyle;
		$this->_initRow($cols, $classStyle);
	}
	private function _initRow($cols, $classStyle){	
		$this->m_cols = array();
		$this->m_colindex = -1;
		for($i=0; $i < $cols ; $i++){
			$dv = (new IGKHtmlRowColSpanNode($i))->setClass($classStyle);
			$this->add($dv);
			$this->m_cols[] = $dv;
			
			
		}
		$this->m_colindex = $cols>0 ? 0:-1;
	}
	public function ClearChilds(){
		parent::ClearChils();
		$this->_initRow(
			$this->m_colsn,
			$this->m_classstyle
		);
	}
	
	public function _AddChild($item, $index=null){		
		if ($this->m_colindex == -1){			
			return parent::_AddChild($item, $index);
		}		
		$t = igk_getv($this->m_cols, $this->m_colindex);
		$s = $t->add($item,null, null);						
		$this->m_colindex = ($this->m_colindex+1) %  igk_count($this->m_cols);
		return $s;
	}
}

final class IGKHtmlRegistrationFormItem extends IGKHtmlItem
{
	private $m_action;
	private $m_gooduri;
	private $m_baduri;
	public function getAction(){return $this->m_action; }
	public function setAction($v){$this->m_action =$v; return $this; }
	
	public function getGoodUri(){return $this->m_gooduri; }
	public function setGoodUri($v){$this->m_gooduri =$v; return $this; }
	
	public function getBadUri(){return $this->m_baduri; }
	public function setBadUri($v){$this->m_baduri =$v; return $this; }
	
	public function __construct(){
		parent::__construct("div");
		$this["class"] = "igk-signup-form";
	}
	public function initView(){
		
		$this->ClearChilds();
		$frm = $this->addForm();
		$frm["action"] = new  IGKValueListener($this, "action");
		
		igk_notifyctrl()->setNotifyHost($frm->addDiv());
		
		$ul = $frm->add("ul");
		$ul->add("li")->addSLabelInput("clLogin","email");
		$ul->add("li")->addSLabelInput("clPwd","password");
		$ul->add("li")->addSLabelInput("clRePwd","password");
	
		$frm->addInput("confirm", "hidden" ,1);
		$frm->addInput("noNavigation","hidden", "1");
		
		$dd = $frm->addDiv()
		->setClass("disptable fitw");
		$dd->addDiv()->setClass("disptabc")->addInput("clAcceptCondition", "checkbox");
		
		//usage condition requirement
		$dd->addDiv()->setClass("disptabc fitw")->addDiv()->add("label")
		->setAttribute("for", "clAcceptCondition")
		->setStyle("padding-left:10px")
		->Content = new IGKHtmlUsageCondition();		
		$frm->addHSep();
		$frm->addInput("btn_reg", "submit", R::ngets("btn.register"))->setClass("dispb igk-sm-fitw");		
	
	}
}



///<summary> represent a component to callback actions</summary>
class IGKHtmlItemComponentCallbackItem extends IGKHtmlComponentNodeItem{
	var $callback;
	var $args;	
	public function __construct(){
		parent::__construct("div");
	}
	public function AcceptRender($o=null){
		return false;
	}
	public function doaction(){
		$c = $this->callback;		
		if ($c){			
			call_user_func_array($c, $this->args? $this->args : array());
		}
		if (igk_is_ajx_demand()){
			exit;
		}
	}
}

?>