<?php

//remark : to avoid recursion don't use the same adding context

///<summary>create a thumbnail document</summary>
function igk_html_node_thumbNailDocument($id){
	$d = igk_get_document($id, 1);
	$d->body->setClass("thumbnail");
	return $d;
}

function igk_html_node_balafonJS(){//use script on balafon javascript context
	$c = igk_createXmlNode("script");
	$c["type"]="text/balafonjs";
	$c->setCallback("handleRender", "igk_js_production_minify_content_callback");
	return $c;
}


function igk_html_node_aClearSAndReload(){
	$n = igk_createXmlNode('a');
	$n["class"]="igk-btn";
	$n["href"] = igk_getctrl(IGK_SESSION_CTRL)->getUri("ClearS")."&r=".base64_encode(igk_io_currentUri());
	$n->Content = R::ngets("btn.clearSAndReload");
	return $n;	
}


function igk_html_node_repeatContent($number){
	$n = igk_createNode("div");
	$n["class"] = "igk-winui-rc";//repeat content
	$n["igk-repeat"] = $number;
	return $n;
}


function igk_html_node_Col($clname=null){	
	if ($clname){
		$clname = " ".$clname;
	}
	return igk_createNode("div")->setAttributes(array("class"=>"igk-col".$clname));
}
function igk_html_node_Frame(){	
	return igk_createNode("div")->setAttributes(array("class"=>"igk-ui-frame frame"));
}
function igk_html_node_Page(){
	return igk_createNode("div")->setAttributes(array("class"=>"igk-ui-page page"));
}


function igk_html_node_AJXReplaceSource($selection){
	$n = igk_createNode("div");
	$n["class"]="igk-ajx-replace-source";
	$n["igk:data"]=$selection;
	return $n;
}
////<summary>pick file</summary>
///<param name="options">JSON Options</param>
function igk_html_node_AJSpickfile($u, $options=null){
	$n= igk_createNode("a");
	$n["class"]="igk-js-pickfile";
	$js ="{uri:'".$u."'";
	if ($options){
		$js .=", options:".$options."";
	}	
	$js.="}";
	$n["href"]="#";
	$n["igk:data"] = $js;
	return $n;
}

function igk_html_node_AJXAButton($link){
	$n = igk_html_node_AJXA($link);
	$n["class"]="igk-btn";
	return $n;
}



///<summary>represent a css style element</summary>
function igk_html_node_cssStyle($id,$minfile=1){
	$o= igk_html_parent_node();//get_env(IGK_XML_CREATOR_PARENT_KEY);
	$k = __FUNCTION__.":/{$id}";
	if ($o){
		
		$g = $o->getParam($k);
		
		if ($g!=null)
			return $g;
	}
	$s = igk_createNode("style");
	$s["type"]="text/css";
	$s->minfile = $minfile;
	$s->setCallback("handleRender", "igk_html_handle_cssStyle");
	$o->setParam($k,$s);
	return $s;
}
///<summary>handle used to render css style</symmary>
function igk_html_handle_cssStyle($n){
	
	$s = $n->Content;
	$tab = array();
	$s  = preg_replace_callback("/@(?P<name>".IGK_IDENTIFIER_RX."):(?P<value>[^;]+);/i", function($m)use(&$tab){
		$tab[trim($m["name"])] = $m["value"];
		return "";
	}, $s);
	if (igk_count($tab)>0){
		foreach($tab as $k=>$v){
			$s = str_replace("@".$k, $v,$s);
		}
	}
	
	
	return $n->minfile? igk_min_script($s) : $s;
}
function igk_min_script($s){
	$s = preg_replace("/(\n|\t|\r)/i","",$s);
	return $s;
}

///<summary>column view item</summary>
function igk_html_node_ColViewBox(){
	$n = igk_createNode("div");
	$n->setClass("igk-col-view-box");
	return $n;
}
function igk_html_node_IGKSiteMap(){
	$n  = igk_createXmlNode("urlset");
	$n["xmlns"] =  "http://www.sitemaps.org/schemas/sitemap/0.9";
	$n["xmlns:sitemap"] =  "http://www.sitemaps.org/schemas/sitemap/0.9";

	
	$n->setCallback("lUri", igk_create_func_callback("igk_site_map_add_uri", array($n)) );
	return $n;
}

//site map function
function igk_site_map_add_uri($n, $uri=null){
	$c = $n->addNode("url");	
	$c->addNode("loc")->Content = igk_getv($uri, 0);
}


function igk_html_node_DataSchema(){
	$rep  = igk_createNode("data-schemas");
	$rep["Date"] = date('Y-m-d');
	$rep["Version"]="1.0";
	return $rep;
}

function igk_html_node_NavigationLink($target){
	$n = igk_createXmlNode("a");
	$n->setAttribute("igk-nav-link", $target);
	return $n;
}

function igk_css_link_callback($p, $key, $href){	
	$g = $p->getParam($key);
	if ($g)
	unset($g[$href]);
	$p->setParam($key, $g);
}
function igk_html_node_CssLink($href, $temp=0){
	$p = igk_html_parent_node();
	if (strtolower($p->TagName) !== "head")
		igk_die("/!\\ can't add css link to non header. ".strtolower($p->TagName) . " ".get_class($p));

	$key = "sys://cssLink/".__FUNCTION__;
	$g = $p->getParam($key, array());
	
	
	if ($temp){
		if (isset($g[$href])){
			return $g[$href];
		}		
		$m = new IGKHtmlItem("link");
		$m->setAttribute("href",$href);	
		$m->setAttribute("rel","stylesheet");
		$n = new IGKHtmlSingleNodeViewer($m, igk_create_func_callback("igk_css_link_callback", array($p, $key, $href)));
		
		//function()use( $p, $key, $href){			
		
		
		$g[$href] = $n;
		$p->setParam($key, $g);	
		return $n;
	}
	if (isset($g[$href])){
		return $g[$href];
	}	
	$n = new IGKHtmlItem("link");
	$n->setAttribute("href",$href);	
	$n->setAttribute("rel","stylesheet");	
	$g[$href] = $n;
	$p->setParam($key, $g);	
	return $n;
}
///<summary>utility to create a container section</summary>
///<return >primary row</summary>
function igk_html_create_container_section($t){
	$dv = $t->addDiv();
	$ct = $dv->addContainer();
	$r = $ct->addRow();
	return $r;
}

///<summary>build select node</summary>
function igk_html_node_BuildSelect($name, $rows, $idk, $callback=null, $selected=null){		
	$sl = igk_createNode("select")->setId($name)->setClass("igk-form-control");		
	foreach($rows as $k=>$v){
		$opt = $sl->add("option");
		$opt["value"] = $v->$idk;
		$opt->Content = $callback? $callback($v) : $v;
		
		if ($selected && ($v->$idk == $selected)){
			$opt["selected"] = 1;
		}
	}
	return $sl;
}
function igk_html_node_CommunityLink($name, $link){
	$s = igk_createNode("div");
	$s["class"] = "igk-comm-lnk";
	$s["igk:title"]=$name;
	$s["href"] = $link;	
	return $s;
}

///<summary>DataBase select component </summary>
function igk_html_node_DBSelect($name, $result, $callback=null, $valuekey=IGK_FD_ID ){
	$n = new IGKHtmlItem("select");
	$n->setClass("clselect");
	$n->setId($name);
	$callback  = $callback === null ? igk_get_env("sys://table/". igk_getv(array_keys($result->getTables()), 0)) : $callback;
	
	
	foreach($result->Rows as $k=>$v){
		$g = $n->add("option");
		$g->setAttribute("value", $v->$valuekey);
		if ($callback !==null){
			if (is_callable($callback)){
				$g->setContent($callback($v, $g));
				continue;
			}
		}
		$g->setContent(igk_display($v));
	}
	return $n;
}
function igk_html_node_DBResult($r, $max=-1){
	
	$n = igk_createNode("div");
	$n["class"] = "igk-db-r";
	if($r)
		$n->add(igk_db_view_result_node($r, $max));
	return $n;
}
function igk_html_node_DBEntriesCallback($target, $callback, $queryResult, $fallback=null){
	if ($queryResult && ($queryResult->RowCount>0)){
		foreach($queryResult->Rows as $k=>$v){
			$target->add($callback($v));
		}
	}
	else{
		if ($fallback)
		{
			$c  = $fallback();
			if ($c)
			$target->add($c);
		}
	}
	return $target;
}


function igk_html_node_label($for=null, $key=null){
	// igk_wln(igk_env_count(__FUNCTION__));
	
	// igk_wln(__FUNCTION__." / =".$for);
	// igk_wln(__FUNCTION__." / =".$key);
	// igk_die("b");
	$n = new IGKHtmlItem("label");
	$n["for"] = $for;
	$n["class"]="cllabel";
	$n->Content =(( $key==null) ? R::ngets("lb.".$for): R::ngets($key));
	$n->setTempFlag("replaceContentLoading", 1);
	return $n;
}
function igk_html_node_td($for=null, $key=null){
	$n = new IGKHtmlItem("td");
	return $n;
}
function igk_html_node_a($href=null, $attributes = null, $index=null){
		$a = new IGKHtmlA();//igk_createNode("a");
		$a["href"] = $href;
		$a->setIndex($index);
		if ($attributes){
			$a->AppendAttributes($attributes);
		}
		return $a;
}
function igk_html_node_circleWaiter(){
	$n = igk_createNode();
	$n->setClass("igk-circle-waiter");	
	return $n;

}

function igk_html_node_LineWaiter(){
		$n= igk_createNode();
		$n["class"] = "igk-line-waiter";
		$n["igk:count"] = "3";
		$n->addDiv()->setClass("igk-line-waiter-cur");
		$n->addDiv()->setClass("igk-line-waiter-cur");
		$n->addDiv()->setClass("igk-line-waiter-cur");
		$n->addBalafonJS()->Content = <<<EOF
ns_igk.readyinvoke('igk.winui.lineWaiter.init');
EOF;
		return $n;

}

function igk_html_node_contextMenu(){
	$n = igk_createNode();
	$n["class"]="igk-context-menu";
	$n->setCallback("addItem", "igk_html_add_context_menu_item");
	return $n;
}
function igk_html_add_context_menu_item($n, $uri, $display){
	$n->addLi()->addA($uri)->Content = $display;
}

function igk_html_node_backgroundLayer(){
	$n = igk_createNode();
	$n["class"]="igk-background-layer";
	$n->setCallback("addPic", " \$this->addImg(); return \$this;");
	return $n;
}

//<summary>under construction page</summary>
function igk_html_node_UnderConstructionPage(){
	$n = igk_createNode();
	$n->setCallback("getCommunityNode", "return null;");
	
	$n->setClass("fitw fith igk-under-construction");
	$src = igk_get_env("sys://underconstruction.bg");
	if (!$src)
		$src = igk_io_baseDir(igk_db_get_config("sys://underconstruct.bg", ""));
	if($src){
	$n->addBackgroundLayer()->addImg()->setSrc(
		$src
	);
	}
	$c = $n->addContainer();
	$c->setStyle("padding-top: 64px; padding-bottom; ;");
	$r = $c->addRow();
	$r->addCol()->addDiv()->setStyle("font-size:3em;color:#eee")->Content = R::ngets("title.pageUnderConstruction");
	$c = $r->addCol()->addDiv();
	
	$c->Content = "Vous souhaitez être informer lors de l'ouverture du site";
	
	$r->addCol()->addDiv()->addRegisterMailForm();
	return $n;
}
function igk_html_node_RegisterMailForm(){
	$n = igk_createNode("form");
	$n["action"] = igk_getctrl(IGK_MAIL_CTRL)->getUri("register");
	$n["method"] = "POST";
	igk_notify_sethost( $n->addRow()->addCol()->addDiv(), "sys://mailregisterform");
	$n->addRow()->addCol()->addDiv()->addInput("clEmail", "text")->setAttribute("placeholder", R::ngets("lb.yourmail"));
	$n->addRow()->addCol()->addDiv()->addInput("clSubmit", "submit", R::ngets("btn.send"));
	$n->addInput("cref", "hidden", IGKApp::getInstance()->Session->getCRef());
	return $n;
}


function igk_html_node_Symbol($code, $w=16, $h=16, $name='default' ){
		$n = igk_createNode();
		$n["class"] = "igk-symbol";
		$n->Content = is_integer($code)?"&#".$code.";":$code;
		$g = $name=='default' || ($name==null) ? '' : ", name:'$name'";
		$n["igk-symbol-data"] = "{w:'$w', h:'$h' $g}";
		return $n;
	
}
function igk_html_demo_symbol($tg){
	$tg->addDiv()->Content = "Load a symbol with integer code equal to 1 if registrated";
	$tg->addDiv()->setStyle("width:40px; height:40px")->addSymbol(1);
	
	$tg->addDiv()->Content = "Code of this sample:";
	$tg->addCodeViewer()->Content = <<<EOF
\$tg->addDiv()->setStyle("width:40px; height:40px")->addSymbol(1);
EOF;
}


function igk_html_node_titlenode($class, $text){
	if (!$class)
		$class = "igk-title";
	$n = igk_createNode("div");
	$n->setClass($class)->setContent($text);	
	return $n;
}
function igk_html_node_ReadOnlyTextZone($file){
	$n = igk_createNode();
	$n->setClass("igk-ro-txt-z");
	$c = $n->addTextArea()
	->setAttribute("readonly", true)
	->setClass("fitw fith")
	->setStyle("resize:none;white-space:pre;overflow-x:auto;word-wrap: break-word;")
	->setAttribute("onfocus", "javascript:event.preventDefault(); event.stopPropagation(); this.blur(); return false;")
	->Content = igk_create_func_callback("igk_file_content", array($file));
	$n->area = $c;
	// $n->addScript()->Content = <<<EOF
	// var q= \$igk(ns_igk.getParentScript()).select("textarea").getItemAt(0);
	// q.setHtml("test -------------------------------------cvxvxvcx--------------------------\\n information");
	// console.debug('ok'+q.o.value);
// EOF;
	return $n;
}
function igk_file_content($file){	
	return file_get_contents($file);
}

function igk_html_utility_aLinktnCallback($n){
// igk_wln("exit");
// igk_wln("".$n);
// exit;
	$n->setStyle("width: ".$n->getParam('data')->w."px; height: ".$n->getParam('data')->h."px;");
	$i = $n->getParam('data')->img;
	$i["src"] = $n->getParam('data')->src;
	$i["srcdata"] =$n->getParam('data')->src;
	return $n->IsVisible;
}

function igk_html_node_LinkBtn(/*$owner,*/ $uri, $img, $width=16, $height=16)
{	
	//TODO::
		$n = igk_createNode("a");
		$img = $n->add("img");
		
		$n->setCallback("AcceptRender", "igk_html_utility_aLinktnCallback");
		$n->setCallback("setUri", <<<EOF
\$this->getParam('data')->src  = \$value;
EOF
);
		$n->setParam("data", (object)array("img"=>$img, "w"=>$width,"h"=>$height, "src"=>$uri));		
		return $n;
	}
function igk_html_node_MsDialog($id=null){
	$n = igk_createNode();
	$n->setClass("igk-ms-dialog");
	$n->setId($id);	
	$n->addA("#")
	->setClass("igk-btn-close");
	return $n;
}
function igk_html_node_MsTitle($key){
	$n = igk_createNode();
	$n->setClass("igk-ms-dialog-title");
	$n->Content = R::ngets($key);
	return $n;
}

function igk_html_node_Separator($type='horizontal'){
	$n = igk_createNode();
	switch($type)
	{
		case "horizontal":
		$n["class"]="igk-horizontal-separator";
		break;
		case "vertical":
		$n["class"]="igk-vertical-separator";
		break;
	}
	return $n;
}

function &igk_html_node_NoTagNode(){
//throw new Exception("dd");
	$n = igk_createNode("igk-notag-node");	
	$n->setCallback("getIsRenderTagName", "return false; ");	
	return $n;
}
function &igk_html_node_BlockNode()
{
	$n = igk_createXmlNode("igk-block-viewitem");	
	$n->setCallback("getIsVisible", "return \$this->HasChilds ;");
	$n->setCallback("getIsRenderTagName", "return false;");
	
	return $n;
}
function &igk_html_node_rowcontainer(){
	$n = igk_createNode();
	$n["class"]="igk-row-container";
	return $n;
}
function igk_html_node_newsLetterRegistration($uri, $type="email", $ajx=1){
	$n = igk_createNode();
	$frm = $n->addForm();
	$frm["action"] = $uri;
	$frm["igk-ajx-form"] = $ajx;
	$frm->addInput("clEmail", $type)->setClass("igk-form-control")->setAttribute("placeholder", R::ngets("tip.yourmail"));
	$frm->addInput("btn.send", "submit")->setClass("igk-btn igk-btn-default");	
	return $n;
}

///<summary>used to add notification node</summary>
function igk_html_node_notification($nodeType="div", $notifyName=null){
	$n = igk_createNode($nodeType);
	igk_notify_sethost($n, $notifyName);
	return $n;
}
///<summary>used to add a node with buffer content</summary>
function igk_html_node_ObData($data, $nodeType="div"){
	
	$n = igk_createNode($nodeType);
	if (is_callable($data)){
		IGKOb::Start();
		$s = $data();
		$g = IGKOb::Content();		
		IGKOb::Clear();
		$s = $g;
	}
	else if (is_object($data) || is_array($data)){
		$s = igk_wln_ob_get($data);
	}else 
		$s = $data;
	$t = new IGKHtmlSingleNodeViewer(igk_html_node_NoTagNode());
	$t->targetNode->Content = $s;
	$n->add($t);
	return $n;
}

///<summary>Hosted object data. will pass the current node to callback as first argument</summary>
function igk_html_node_HostObData($callback, $host=null){
	$q = igk_html_parent_node();
	$index=  1;
	if ($q==null){
		$p = $host ; //context 
		$index=2;
	}
	if ($p==null)
		igk_die("no parent to host");
	$tab = array_merge(array($p), array_slice(func_get_args(), $index));
	
	$n = igk_createNode("NoTagNode");	
	IGKOb::Start();
	call_user_func_array($callback, $tab);
	$s = IGKOb::Content();
	IGKOb::Clear();
	
	$n->addText()->Content = $s;
	
	return $n;
}
///<summary>shortcut to create ObData node with noTag to display</summary>
function igk_html_node_NoTagObData($content){
	return igk_html_node_ObData($content, "NoTagNode");
}


// ->setAttribute("value" , R::ngets("btn.addproduct"))
// ->setAttribute("onclick", "javascript:");

function igk_html_node_ABtn($uri){
	$n = igk_createNode("a");
	$n["class"] = "igk-btn";
	$n["href"] = $uri;
	return $n;
}

function igk_html_demo_ABtn($tg){
	$tg->addABtn("#")->Content = "Demo button";
}


	
//used to add picture zone
function igk_pic_zone($n, $r, $c, $base=4,  $tab=null, $offset=0){
	$tr = $r;
	$ct = 0;
	while($r>0){
		$r--;		
		$t = $n->addRow();
		$j = $c;
		while($j>0){
			$j--;
			$cl = $t->addCol();
			$cl->setClass("igk-col-$base-1");			
			$cl->addDiv()->setClass("pic")->Content = igk_getv($tab,$ct, IGK_HTML_SPACE);
			$ct++;
		}
	}
}

function igk_html_node_divContainer($attribs=null){
	$n  = igk_createNode();
	$n->addContainer();
	return $n;
}
//utility function to build CanvaBalafonScript
function &igk_html_node_CanvaBalafonScript($uri=null)
{
	$n = igk_createNode();
	$n["class"] = "igk-canva-gkds-obj";
	$n["uri"] = $uri;
	$n["igk-canva-gkds-obj-data"] = "{uri:'{$uri}',init:function(ctx){ctx.strokeStyle = 'transparent';ctx.fillStyle = this.getComputedStyle('color'); }}";
	$n->Content = "&nbsp;";
	return $n;
}



///<summary>bind article</summary>
function &igk_html_node_Article($ctrl, $name, $data=null, $showAdminOption=1){		
	$n = igk_html_node_NoTagNode();	
	igk_add_article($ctrl, trim($name), $n, $data, null, true, true, $showAdminOption);	
	return $n;
}
function &igk_html_node_BindArticle($ctrl, $name, $data=null, $showAdminOption=1){
	$n = igk_createNode();
	igk_html_binddata($ctrl, $n, $name, $data, true, true, $showAdminOption);
	return $n;
}

function igk_html_node_BindContent($content, $entries, $ctrl=null){
	$n = igk_html_node_NoTagNode();
	$n->Content = igk_html_bind_content($ctrl, $content, $entries);
	return $n;
}

///<summary>use to add a template node</summary>
function igk_html_node_Template($ctrl, $name, $row =null){
	$d = igk_createNode();
	igk_html_binddata($ctrl, $d, $name, $row, false, true);	
	return $d;
}

///<summary>used to add system article</summary>
function igk_html_node_SysArticle($name){
	$f = igk_io_get_article($name);
	//igk_wln($f);
	$n = igk_createNode();
	igk_add_article(igk_sys_ctrl(), $f, $n);
	return $n;
}


//<summary>demonstranction of article</summary>
// function igk_html_desc_Article($tg){	
	// $n = igk_createNode();	
	// $tg->add($n);
// }

// function igk_html_node_Validator(){}
// function igk_html_node_Search(){}
// function igk_html_node_Img(){}
// function igk_html_node_Nothing(){}
// function igk_html_node_Comment(){}
// function igk_html_node_Select(){}
// function igk_html_node_HMenu(){}
function igk_html_node_Row(){
	$n = igk_createNode('div');
	$n->setClass("igk-row");
	$n->setCallback("addCell", <<<EOF
\$d = \$this->addDiv();
\$d->setClass("igk-row-cell");
return \$d;
EOF
);

	$n->setCallback("addCol", <<<EOF
\$v_n = igk_getv(\$param, 0);
return \$this->add(igk_html_node_RowColumn(\$v_n));
EOF
);
	return $n;
}

///<summary> add a row column </summary>
///<param name="classLevel" > css classname that init the  column level</param>
function igk_html_node_RowColumn($classLevel=null){
	$n = igk_createNode("div");
	$n->setClass("igk-col".(($classLevel)? " ".$classLevel: ""));
	return $n;
	
}
///<summary>shortcut to call node->addRow()->addCol()-> and return the column</summary>
function igk_html_node_SingleRowCol($col=null){	
	$d =  igk_html_parent_node();
	if ($d){	
		$n = $d->addRow()->addCol($col);
		igk_set_env("sys://xml/no_add", 1);
		return $n;	
	}
	return null;
}
function igk_html_node_PanelBox(){
	$n = igk_createNode("div");
	$n["class"] = "igk-panel-box";
	return $n;
	
}
// function igk_html_node_NotifyBox(){}
// function igk_html_node_NotifyZone(){}
function igk_html_node_Container(){
	$n = igk_createNode('div');
	$n["class"] = "igk-container";
	return $n;
	
}
function igk_html_node_ResponseNode(){
	$n = igk_createNode('div');
	$n["class"] = "igk-response";
	return $n;
}
// function igk_html_node_DataEntry(){}
// function igk_html_node_AJXView(){}
// function igk_html_node_ToggleButton(){}
function igk_html_node_TopNavBar(){
	$n = igk_createNode("div");
	$n["igk-top-nav-bar"] = "1";
	$n["class"]="igk-navbar igk-top-nav-bar";
	return $n;
}

// function igk_html_node_GroupControl(){}
// function igk_html_node_BreadCrumbs(){}
function igk_html_node_Expo(){
	$n = igk_createNode("span");
	$n["class"] = "igk-expo";
	return $n;
}
function igk_html_node_ProgressBar(){
		$n = igk_createNode();
		$n["class"] = "igk-progressbar";
		$n["data"]="0";
		//define cursor
		$n->m_cur = $n->addDiv()->setClass("igk-progressbar-cur igk-progress-0");
		return $n;
}
function igk_html_demo_ProgressBar($t){
		$n = igk_createNode();
		$n["class"] = "igk-progressbar";
		$n["data-number"]="50.0";
		//define cursor
		$n->m_cur = $n->addDiv()->setClass("igk-progressbar-cur igk-progress-0");
		$t->add($n);
		return $n;
}
function igk_html_node_ClearTab(){
	$n = igk_createNode();
	$n["class"] = "igk-cleartab";
	return $n;
}
function igk_html_node_innerImg(){
	$n = new IGKHtmlItem("igk-img");	
	return $n;
}

// function igk_html_node_AuthorizationNode(){}
// function igk_html_node_BindDataNode(){}
// function igk_html_node_AJXBindDataNode(){}
function igk_html_node_CellRow(){
	$n = igk_createNode();
	$n["class"] = "igk-cell-row";
	return $n;
}
function igk_html_node_Cell(){
	$n = igk_createNode();
	$n["class"] = "disptabc";
	return $n;
}
function igk_html_node_VScrollBar($cibling=null, $initTarget=null){
	$n = igk_createNode();
	$n["class"] = "igk-vscroll";
	$n["igk:cibling"]=$cibling;
	$n["igk:target"]=$initTarget;
	return $n;
}
function igk_html_node_HScrollBar(){
	$n = igk_createNode();
	$n["class"] = "igk-hscroll";
	return $n;
}
function igk_html_node_ActionBar(){
	$n = igk_createNode();
	$n->setClass("igk-action-bar");
	return $n;
}
function igk_html_node_submitBtn($name="btn_", $key="btn.add"){
	$n = igk_createNode("input");
	$n->setId($name);
	$n["value"] =  R::ngets($key);
	$n["type"] ="submit";
	$n->setClass("igk-btn");
	return $n;
}
function igk_html_node_RollIn(){
	$n = igk_createNode();
	$n["class"] = "igk-roll-in";
	return $n;
}
function igk_html_node_CenterBox($content=null){
		$n = igk_createNode('div');
		$n->setClass("igk-centerbox disptable fitw");
		$n->m_c = $n->add("div");
		$n->m_c->setClass("disptabc alignc alignm");
		$n->m_c->setParentHost($n);
		$n->m_c->Content = $content;
		
		$n->setCallback("getBox", "return \$this->m_c;");
		return $n;
	
	
}
function igk_html_node_HSep(){
	return igk_html_node_Separator("horizontal");
}
function igk_html_node_VSep(){
	return igk_html_node_Separator("vertical");
}
// function igk_html_node_FixedActionBar(){}
function igk_html_node_BodyBox(){
	$n = igk_createNode();
	$n->setClass("igk-bodybox fit igk-parentscroll igk-powered-viewer overflow-y-a");
	return $n;
}

function igk_html_node_JSBtnShowDialog($id){
	$n=igk_createNode();
	$n->setClass("igk-btn igk-js-btn-show-dialog");
	$n->setAttribute("igk:dialog-id", $id);
	return $n;
}
// function igk_html_node_GKDSNode(){}
// function igk_html_node_CanvaNode(){}

///<summary>used to bind notify global ctrl message</summary>
function igk_html_node_NotifyHost($name=null, $autohide=1){
	$n = igk_html_node_NoTagNode();
	$c = igk_notifyctrl();
	$c->setNotifyHost($n, $name);
	$c->TargetNode->setAutohide($autohide);
	return $n;
}

function igk_html_node_notifyHostBind($name=null, $autohide=1){

	$o = igk_createNode('div');
	$o["class"] = "igk-notify-host-bind";	
	$o->addOnRenderCallback( igk_create_func_callback("igk_notifyhostbind_callback", array($o, $name, $autohide)));
	return $o;
}

function igk_notifyhostbind_callback($host, $name, $autohide, $options=null, $bind=null){	
	$bind = isset($this)? $this:  $bind;
	$c = igk_notifyctrl();
	$n = $c->getNotification($name) ?? $c->TargetNode;	 
	if ($n && $bind){	
		
		$bind->addObData(function()use($n, $autohide){
			$n->setAutohide($autohide);		
			$n->renderAJX();		
			$n->setAutohide(1);
		});	
		return 1;
	}
	return 0;
}
function igk_ctrlview_acceptrender_callback($n,$s){
	$n->ClearChilds();
	$d= $n->getParam("data");
	if ($d){
		$c = $d->ctrl;
		$v = $d->view;
		if ($c){			
			$c->getViewContent($v, $n,0, $d->params);				
			return true;
		}
	}else{
		igk_ilog('no data provide for node', __FILE__."::".__FUNCTION__."::".__LINE__);
	}
	return false;
}
function igk_html_node_CtrlView($view, $ctrl, $params=null){
	$n = igk_createNode("igk:ctrlview");
	$n->setCallback("getIsRenderTagName", "return false;");	
$n->setCallback("AcceptRender", "igk_ctrlview_acceptrender_callback");
	$n->setParam("data", (object)array("view"=>$view,"ctrl"=>$ctrl, "params"=>$params ));
	return $n;
}


function igk_html_node_WordCaseSplitter($v, $split=5){
	$n = igk_createNode();
	$n->setClass("igk-wc-splitter");
		if (is_string($v)){
			
		$o = igk_str_explode_upperCase($v);
		$w = 1;
		foreach($o as $k=>$sv){
			if (empty($sv))
			continue;
			$n->add("span")->setClass("w_".$w)->setContent($sv);
			$w = (++$w % $split);
		}
		}
	return $n;
}
function igk_html_node_CloneNode($node){
	if (($node==null) || !is_object($node))
		throw new IGKException("Can't clone node . {{node}} not valid");
	if (!is_subclass_of(get_class($node), "IGKHtmlItemBase"))	{
		throw new Exception("not a valid item");
	}
	$n = igk_createNode("igk-clone-node");
	$n->setParam("self::targetnode", $node);
	$n->setCallback("getIsRenderTagName", "return false;");
	$n->setCallback("getTargetNode", "return \$this->getParam('self::targetnode'); ");
	$n->setCallback("getIsVisible", "\$v =  \$this->getTargetNode() && \$this->getTargetNode()->IsVisible; return \$v;");
	$n->setCallback("GetRenderingChildren", "return array(\$this->getTargetNode()); ");
	return $n;
}

function igk_html_node_JSCloneNode($node){
	if (($node==null)|| !is_object($node))
		throw new Exception("Not valid");
	if (!is_subclass_of(get_class($node), "IGKHtmlItemBase"))	{
		throw new Exception("not a valid item");
	}
	$n = igk_createNode("igk-js-clone-node");
	$n["igk-js-cn"]= new IGKValueListener($n,'getTargetId');
	$n->setParam("self::targetnode", $node);
	$n->setCallback("getIsRenderTagName", "return true;");
	$n->setCallback("getTargetId", "return \$this->getParam('self::targetnode'); ");
	return $n;
}
// function igk_html_node_SearchBar(){}
// function igk_html_node_ChildNodeView(){}
// function igk_html_node_FooterFixedBox(){}
function igk_html_node_Button($id, $value=null, $type="default", $buttontype=0 ){
	$n  = igk_createNode("input");	
	$n["class"]="igk-btn";
	if ($type)
		$n["class"]="+igk-btn-${type}";
	$n["type"] = $buttontype? "submit":"button";
	$n->setId($id);
	$n["value"] = $value ?? R::ngets($id);
	$n->setCallback('setUri', igk_create_expression_callback(<<<EOF

	\$u= \$fc_args[0];
	\$n["onclick"] = "javascript: document.location = '\$u'; return false;";
EOF
, array("n"=>$n)));
	return $n; //func
}
function igk_html_node_headerBar($title, $baseuri=null){

	$baseuri =  $baseuri ? $baseuri :igk_io_baseDomainUri();
	$n = igk_createNode("div");
	$r = $n->addRow()->setClass("no-margin");
	$h1 = $r->addDiv()
	->setClass(" igk-col-lg-12-2 fith presentation");
	$t = $h1->addDiv()->addA($baseuri)
	->setClass("dispb no-decoration");
	$t->add("span")
	->setClass("dispib posr")
	->setStyle("left:10px; top:12px;")
	->Content = igk_web_get_config("company_name");//igk_sys_domain_name();
	$t->addDiv()->setClass("igk-title-4")->Content = $title;
	$n->m_Box = $r->addDiv();
	$n->m_Box->setClass("igk-col-lg-12-10 .ibox");
	return $n;
	
}


function igk_html_node_IGKCopyright(){
	$n = igk_createNode();
	$n->setClass("igk-copyright");
	$n->setCallback("getCopyright",  "return IGK_COPYRIGHT;");
	$g = new IGKValueListener($n, "getCopyright");
	$n->Content = $g;
	return $n;
}
function igk_init_renderingtheme_callback($n){//select node
	$n->clearChilds();
	if (!$n->IsVisible){
		return 0;
	}
	$gt = igk_web_get_config("globaltheme", 'default');
	// igk_ilog("gt is ".$gt);	
	foreach(igk_io_getfiles(igk_io_baseDir()."/R/Themes/", "/\.theme$/i") as $k=>$v){
		$op = $n->add("option");
		$r = igk_io_basenamewithoutext($v);
		$op->Content = $r;		
		if ($r == $gt){
			$op["selected"]="true";
		}		
	}	
	return 1;
}
function igk_html_node_IGKGlobalThemeSelector(){
	$dv = igk_createNode("div");//$t->addDiv();
	$sl = $dv->addSelect("theme")->setClass("-igk-control -clselect");
	

	$sl->setCallback('AcceptRender', "return igk_init_renderingtheme_callback(\$this); ");	
	// foreach(igk_io_getfiles(igk_io_baseDir()."/R/Themes/", "/\.theme$/i") as $k=>$v){
		// $op = $sl->add("option");
		// $r = igk_io_basenamewithoutext($v);
		// $op->Content = $r;
		
		// if ($r == $gt){
			// $op["selected"]="true";
		// }		
		
	// }
	$uri = igk_ctrl_get_cmd_uri(igk_sys_ctrl(),"changeTheme");
	$sl["onchange"] = "ns_igk.css.changeTheme('{$uri}', this.value); return false;";
	return $dv;
}
function igk_init_renderinglang($sl){	
	$sl->clearChilds();
	$gt = R::GetCurrentLang();// igk_app()->Configs->default_lang;//igk_web_get_config("globaltheme", 'default');
	$v_csvc = igk_getctrl(IGK_CSVLANGUAGE_CTRL);
	$tab =array_merge($v_csvc->Languages);	
	foreach($tab as $k=>$v){
		$op = $sl->add("option");
		
		$op->Content = $v;		
		if ($v == $gt){
			$op["selected"]="true";
		}				
	}
}

function igk_html_node_IGKGlobalLangSelector(){
	$dv = igk_createNode("div");//$t->addDiv();
	$sl = $dv->addSelect("lang")->setClass("-igk-control -clselect");
	$gt = igk_app()->Configs->default_lang;//igk_web_get_config("globaltheme", 'default');
	
	//igk_ilog("lang call : in lang :  ".$gt);
	// $v_csvc = igk_getctrl(IGK_CSVLANGUAGE_CTRL);
	// $tab =array_merge($v_csvc->Languages);	
	// foreach($tab as $k=>$v){
		// $op = $sl->add("option");
		
		// $op->Content = $v;
		
		// if ($v == $gt){
			// $op["selected"]="true";
		// }		
		
	// }
	$uri = igk_ctrl_get_cmd_uri(igk_sys_ctrl(),"changeLang_ajx");
	$sl["onchange"] = "ns_igk.ajx.get('{$uri}/'+this.value, null, ns_igk.ajx.fn.replace_or_append_to_body); return false;";	
	$sl->setCallback('AcceptRender', "igk_init_renderinglang(\$this); return \$this->IsVisible;");	
	return $dv;
}


function igk_html_node_Copyright(){
	$n = igk_createNode();
	$n->setClass("igk-copyright");
	$n->setCallback("getCopyright",  "return igk_getv(IGKApp::getInstance()->Configs,'copyright', IGK_COPYRIGHT);");
	$g = new IGKValueListener($n, "getCopyright");
	$n->Content = $g;
	return $n;
}
function igk_html_node_LBorder(){
	$n = igk_createNode();
	$n->setClass("igk-lborder");	
	return $n;
}
function igk_html_node_SectionTitle($level=null){
	$n = igk_createNode();
	$n["class"] = "igk-section-title";
	if ($level)
		$n->setClass("igk-title-".$level);	
	else
		$n->setClass("igk-title");	
	return $n;
}
function igk_html_node_Bullet(){
	$n = igk_createNode();
	$n->setClass("igk-bullet");	
	return $n;
}
// function igk_html_node_CurrentUserInfo(){}
// function igk_html_node_CtrlSelect(){}
// function igk_html_node_SelectNode(){}
// function igk_html_node_SelectLangNode(){}
// function igk_html_node_PhpCode(){}
// function igk_html_node_NotifyDialogBox(){}
// function igk_html_node_NonEmptyNode(){}
// function igk_html_node_PopWindowA(){}

//level form 1 to 6
function igk_html_node_TitleLevel($level=1){
		$n = igk_createNode();
		$n["class"] = "igk-title-".$level;
		return $n;
	
}
function igk_html_node_ImgLnk(){
	$n = igk_createNode();
	$n->img = $n->addImg();
	$n->setCallback("getAlt", "return \$this->img['alt'];");
	$n->setCallback("setAlt", "\$this->img['alt'] = \$value;");
	return $n;
}
function igk_html_node_RoundBullet(){
		$n = igk_createNode("span");		
		$n->setClass("badge igk-rd-bullet");
		return $n;
}
// function igk_html_node_DatePicker(){}
// function igk_html_node_RowColSpan(){}
function igk_html_node_CookieWarning(){
		$n = igk_createNode();
		$n["class"] = "igk-cookie-warning";
		return $n;
}

function igk_html_node_ImageNode(){
	$n = igk_createXmlNode("img");
	return $n;
}


function igk_html_node_formUsageCondition(){

		$dd = igk_createNode();
		$dd->setClass("disptable fitw");
		$dd->addDiv()->setClass("disptabc")->addInput("clAcceptCondition", "checkbox")->setAttribute("checked",1);
		
		//usage condition requirement
		$dd->addDiv()->setClass("disptabc fitw")->addDiv()->add("label")
		->setAttribute("for", "clAcceptCondition")
		->setStyle("padding-left:10px")
		->Content = new IGKHtmlUsageCondition();	
		return $dd;
}

function igk_html_node_scrollImg( $src){
	$n = igk_createNode("igk-img-js");
	$n["data"]= igk_create_attr_callback('igk_get_image_uri', array(null, $src));	
	return $n;
}
///<summary>used to load scroll Loader Item</summary>
///<remark>if visible will be replaced</remark>
function igk_html_node_scrollLoader($src){
	$n = igk_createNode("igk-scroll-loader");
	$n["data"]= $src;	
	return $n;
}

function igk_html_node_AJXA($lnk=null, $target="", $method="GET"){
	// igk_wln($lnk. ":".$target);
	
	$dn = new IGKHtmlItem("a");
	//$n = $dn->addDiv();
	$dn->setAttribute("igk-ajx-lnk", 1);
	$dn["href"] = $lnk;
	$dn["igk:method"] = $method!="GET"?"POST":null;
	$dn["igk:target"] = $target;

	return $dn;
}
///@append: add element after node content or replace.
function igk_html_node_AJXUriLoader($uri, $append=0){
	$n = igk_createNode("div");
	$n->setAttribute("igk:href", $uri);
	$n["class"] =  "igk-ajx-uri-loader";
	if ($append){
		$n["igk:append"] = $append;
	}
	return $n;
}

function igk_html_node_AJXReplaceContent($uri, $method="GET"){
	$n = igk_createNode("noTagNode");
	$n->method= $method;
	$n->uri = $uri;	
	$n->setCallback("AcceptRender", "igk_AJXReplaceContent_AcceptRenderCallback");
	return $n;
}
function igk_AJXReplaceContent_AcceptRenderCallback($n){
	if (!$n->isVisible)
		return 0;
	$n->clearChilds();
	$n->addBalafonJS()->Content =  <<<EOF
(function(q){igk.ready( function(){ ns_igk.ajx.fn.scriptReplaceContent('{$n->method}', '{$n->uri}', q);}); })(igk.getParentScript());
EOF;
	return 1;	
}



///<summary>used to load manually script tag</summary>
function igk_html_node_JSScript($file, $minify=false){
	if (file_exists($file)){
		$d = igk_createNode("script");
		
		$s = file_get_contents($file);
		if ($minify)
			$s = $s;
		$d->Content = $s;
		
		return $d;
	}
	return null;
}


	function igk_html_node_SLabelCheckbox($id, $value=false, $attributes =null, $require=false)
	{		
		$n = igk_html_node_NoTagNode();
		$tab = $n->addLabelInput($id, R::ngets("lb.".$id), $type="checkbox",$value, $attributes, $require);
		if ($value)
		{
			$tab->input["checked"] = true;
		}		
		return $n;
	}
	
	function igk_html_node_tooltip(){
		$n = igk_createXmlNode("igk:tooltip")->setAttribute("style", "display:none;");
		return $n;
	}
	
	///<summary>set placehost for input</summary>
	function igk_html_set_ToolTip($n, $m){
		 $n->input->setAttribute("placeholder",$m);
		 return $n;
	}
	//add system label input
	function igk_html_node_SLabelInput($id, $type="text",$value=null, $attributes=null, $require=false, $description=null){	
		return igk_html_node_LabelInput($id, R::ngets("lb.".$id), $type, $value, $attributes, $require, $description);
	}
	function igk_html_node_SLabelSelect($id, $values, $valuekey = false, $defaultCallback=null, $required=false)
	{
			$i = $this->add("label");
			$i["for"]=$id;
			if ($required)
			{
				$i["class"]="clrequired";
			}
			$i->Content =  R::ngets("lb.".$id);
			$h = $this->add("select");
			$h->setId($id);
			if (is_array($values))
			{
				
				foreach($values as $k=>$v)
				{
					$opt = $h->add("option");
					$opt["value"] = IGK_STR_EMPTY.$k;
					$opt->Content = $valuekey? R::ngets("option.".$v) :$v;
					if (($defaultCallback ) && $defaultCallback($k,$v))
						$opt["selected"] = true;
				}
			}
			return (object)array("label"=>$i, "input"=>$h);
	}
	function igk_html_node_LabelInput($id, $text, $type="text",$value=null, $attributes=null, $require=false, $description=null){
			$o = igk_html_parent_node();//("owner");
			//if ($o==null)
			$o = igk_createNode("igk:lable-input");
			$o->setCallback("getIsRenderTagName", "return false;");
			$o->setCallback("getinput", "return \$this->input;");
			$i = $o->add("label");
			$i["for"]=$id;
			$i->Content = $text;
			if ($require)
			{		
				$i["class"]="clrequired";			
			}
			switch($type)
			{
				case "checkbox":
				case "radio":
					$h = $o->addInput($id, $type,$value, $attributes);
					if ($value)
					{
						$h["checked"] = "true";
					}
				break;
			default:
					$h = $o->addInput($id, $type,$value, $attributes);					
				break;
			}
			
			$desc = null;
			if ($description){
				$desc = $o->add("span");
				$desc->Content = $description;
			}
			$o->input = $h;
			return $o;//(object)array("label"=>$i, "input"=>$h, "desc"=>$desc);
	}
	
	function igk_html_node_SLabelTextarea($id, $attributes=null, $require=false, $description=null)
	{
			//$o = igk_html_parent_node();
			$i = igk_createNode("label");
			$i["for"]=$id;
			
			$i->Content = R::ngets("lb.".$id);
			if ($require)
			{
				$i->setClass("clrequired");
			}			
			$h = igk_html_node_TextArea($id);
			$h->AppendAttributes($attributes);
			$desc = null;
			if ($description){
				$desc = $i->add("span");
				$desc->Content = $description;
			}
			// $o->add($h);
			// $o->textarea = $h;
			return  (object)array("label"=>$i, "textarea"=>$h, "desc"=>$desc);
	}
	
	function igk_html_node_Btn($name, $value, $type="submit", $attributes=null){
		$btn = igk_createNode("input");
		$btn["id"] = $btn["name"] = $name;
		$btn["value"] = $value;
		$btn["type"]=$type;
		$btn["class"]="cl".$type;		
		// $this->add($btn, $attributes);
		return $btn;
	}
	
	function igk_html_TextAreaV_Callback(){
		igk_die("value not implement");
		return;
	}
	function igk_html_node_TextArea($name=null, $content =null, $attributes =null){
		$tx = new IGKHtmlItem("textarea");		
		if ($name)
			$tx->setId($name);		
		$tx["class"] ="+cltextarea";
		$tx->setAttributes($attributes);
		
		$tx->setParam("p:_useWTiny", true);
		$tx->setParam("p:_escapeChar", false);
		$tx->setCallback("setContent","igk_html_TextAreaV_Callback");
	
	
// \$m = \$value;
		// if (is_string(\$m)){
			// if (strlen(\$m)>0)
			// {
				// \$m = htmlentities(\$m, ENT_QUOTES | ENT_IGNORE, "UTF-8");		
				// return parent::setContent( \$m);		
			// }
		// }
	     // return parent::setContent( \$m);		
// EOF
// );
		
		
		if ($content == null){
			
			$tx->Content = igk_getr($name);
		}
		else 
			$tx->Content = $content;
		return $tx;
	}
	
	///<summary>create base php code</summary>
	function igk_html_node_Code($type='php'){
		$n = igk_createNode("php");
		$n["igk-code"] = $type;
		return $n;
	}//end code

	
	///<summary>create a node that will only be visible on webmaster mode context</summary>
	function igk_html_node_webMasterNode(){
		$n = igk_createNode("webmaster-node");
		$n->setCallback("getIsRenderTagName", "return false;");
		$n->setCallback("getIsVisible", "igk_is_webmaster_callback");
		return $n;
	}
	
	function igk_html_visibleConditionalNode(){		
		return igk_is_conf_connected();
	}
	
	///<summary>create a node that will only be visible on conditional callback is evaluted to true</summary>
	function igk_html_node_ConditionalNode($conditioncallback){
		$n = igk_createNode(__FUNCTION__);
		$n->setCallback("getIsRenderTagName", "return false;");	
		$n->setCallback("getIsVisible", "igk_html_visibleConditionalNode");//, $conditioncallback));
		return $n;
	}
	
	
	
	function igk_html_node_cardId( $src=null, $ctrl=null){	
	$n = igk_createNode("div");
	$n->setClass("igk-card-id");
	if ($src){
		if (!IGKValidator::IsUri($src))
		{
			if (file_exists($src)){
				$src = new IGKHtmlRelativeUriValueAttribute(igk_io_baseRelativeUri($src));
			}else 
				$src = new IGKHtmlRelativeUriValueAttribute( igk_io_baseRelativeUri(dirname($ctrl->getDeclaredFileName())."/".$src));			
		}			
	}
	
	// igk_ilog(get_class($src));
	
	
	$n["igk:link"]=$src;
	return $n;
 }
 
	function igk_html_node_domainLink($src){
		$n = igk_createNode("a");
		$n->domainLink=1;
		// $n->setHrefListener();
		$n["href"] = $src;
		// igk_wln(get_class($n));
		// igk_wln($n);
		// exit;
		$n->setParam("lnk", $src);
		return $n;
	
	}
	
	function igk_html_node_HorizontalPageView(){
		$n = igk_createNode("div");
		$n["class"] = "igk-hpageview";
		return $n;
	}
	
	
	//-----------------------------------------------------------------------------------	
	//SVG
	//-----------------------------------------------------------------------------------
	function igk_html_node_SvgA($uri, $svgname){
	$n = igk_html_node_a($uri);//igk_createNode("a");
	$n->setClass("svg-a");
	$n->addSvgSymbol($svgname);//setStyle("width:32px; height:32px;")
	return $n;
	}

	
	function igk_html_node_svgSymbol($name=null){
		$n = igk_createNode("div");
		$n["class"] = "igk-svg-symbol";
		$n["igk:svg-name"] = $name;
		return $n;
	}
	function igk_html_node_SvgLnkBtn($uri, $svgname){
		$n = igk_html_node_a($uri);//igk_createNode("a");
		$n->setClass("svg-a igk-from-sbtn");
		$n->addSvgSymbol($svgname);//setStyle("width:32px; height:32px;")
		return $n;
	}
	function igk_html_node_SvgAJXFormBtn($uri, $svgname){
		$n = igk_html_node_a($uri);
		$n->setClass("svg-a igk-from-sbtn-ajx");
		$n->addSvgSymbol($svgname);
		return $n;
	}
	
	
	///AJX 
	
	///<summary>ajx div component used to load a file</summary>
	function igk_html_node_ajxPickFile($uri, $param=null){
		$u = igk_createNode('input');//igk_html_node_noTagnode();
		$u["type"]="file";
		$u->setClass("igk-ajx-pickfile");
		$u->Attributes->Set("igk:uri", $uri);		
		$param && $u->Attributes->Set("igk:param",$param);
		return $u;
	}
	
	function igk_html_node_xslTranform($xmluri, $xsluri,$target=null){
		if (!isset($xmluri) || empty($xmluri))
			throw new Exception("xmluri not specified");
		if (!isset($xmluri) || empty($xmluri))
			throw new Exception("xsluri not specified");
		$n= igk_createNode('div');
		$n->setClass("igk-xsl-node");
		if ($target)
			$target = ", target:'$target'";
		$n->Attributes->Set("igk:xslt-data","{xml:'$xmluri', xsl:'$xsluri'{$target} }");
		return $n;
	}
	
	///<summary>create a canva editor surface</summary>
	function igk_html_node_CanvaEditorSurface(){
		$n = igk_createNode();
		$n->setClass("igk-canva-editor");
		return $n;
	}
	
	//
	//HTML5 : canvas objects
	//
	function igk_html_node_WebGLGameSurface($listener=null){
		$n = igk_createNode("div");
		$n["class"] = "igk-webgl-game-surface";
		if ($listener)
			$n["igk-webgl-game-attr-listener"] = $listener;
		return $n;		
	}

///<summary>for toast message</summary>	
function igk_html_node_Toast(){
	$n = igk_createNode("div");
	$n["class"] = "igk-winui-toast";
	return $n;
}

function igk_html_demo_Toast($t){
	$g = $t->addDiv();
	
	$g->addA("#")
	->setClass("igk-btn")
	->setAttribute("onclick", "javascript: ns_igk.winui.controls.toast.initDemo(); return false;")
	->Content = "Show Toast";
}


function igk_html_node_frameDialog($id, $ctrl, $closeuri=".", $reloadcallback=null){
	$frame = igk_getctrl(IGK_FRAME_CTRL)->createFrame($id, $ctrl, $closeuri, $reloadcallback);
	return $frame;
}
function igk_html_node_clearFloatBox($t='b'){
	$n=igk_createNode("br");
	$n->setClass("clear".$t);
	return $n;
}

function igk_html_node_AJXLnkReplace($target="::"){
	$n = igk_createNode("div");
	$n["class"] = "igk-winui-ajx-lnk-replace";
	$n["igk-lnk-target"]=$target; //by default target is parent
	$n->setCallback("setTarget", "__igk_html_fn_ajx_lnk_settarget");
	return $n;
}
function __igk_html_fn_ajx_lnk_settarget($n, $p){
	$n["igk-lnk-target"] = $p;
}
///<summary> add a visibility server node</summary>
///<param name="cond" ></param>
function igk_html_node_visible($cond){
	$n = igk_html_node_noTagNode();
	if (igk_is_callable($cond))
		$f=$cond;
	else 
		$f = "return {$cond}";
	
	$n->setCallback("getIsVisible", $f);//return {$cond};");
	return $n;
}


///<summary> parallax node view</summary>
///<exemple> $t->addParallaxNode(igk_html_resolv_img_uri($this->getDataDir()."/R/parallax/img1.jpg"))->addDiv()->setClass("slide_inside")->Content = "Page 1"; </exemple>
function igk_html_node_parallaxNode($uri=null){	
	$n = igk_createNode("div");
	$n["class"] = "igk-winui-parallax";
	$n->Attributes->Set("igk:param", "{$uri}");
	return $n;
	
}


function igk_html_node_JSAExtern($method, $args=null){
	$n = igk_createNode("div");
	$n["class"] = "igk-winuin-jsa-ex dispn";
	if ($args)
		$args = ", args:'".$args."'";
	else
		$args = "";
	$n["igk:data"] = "{m:'{$method}' {$args}}";
	return $n;	
}



function igk_html_node_DialogBox($title){
	$n = igk_createNode("div");
	$n["class"] = "igk-winui-dialogbox";
	
	$t = $n->addDiv()->setClass("title");
	$t->addDiv()->setClass("cls")->addSvgSymbol("close_btn_2");
	$t->addSectionTitle(4)->Content = $title;
	$t->addDiv()->setClass("opts")->addSvgSymbol("v_dot_3");
	return $n;
}
function igk_html_node_DialogBoxContent(){
	$n = igk_createNode("div");
	$n["class"] = "dialog-c";
	return $n;
}

function igk_html_node_DialogBoxOptions(){
	$o = igk_html_parent_node();
	$k = "sys://component/".__FUNCTION__;
	$s = $o->getParam($k);//for single
	if ($s===null){
	$n = igk_createNode("ul");
	$n["class"] = "d-opts dispn";
	
	$o->getParam($k, $n);
	return $n;
	}
	return $s;
}

///<summary>renderging Expression</summary>
function igk_html_node_renderingExpression($callback){
	if (!igk_is_callable($callback))
		return null;
//array_keys

	
	$n = igk_createNode("NoTagNode");
	$n->__callback = $callback;
	$n->setCallback("AcceptRender", "igk_invoke_callback_obj(\$this, \$this->__callback,\$param);  return true;");
	
	return $n;
}
///<summary> create node on callback. create a callback object to send to this </summary>
function igk_html_node_OnRenderCallback($callbackObj){
	if (!igk_is_callable($callbackObj)){
		return null;
	}
	$n = igk_createNode("NoTagNode");
	$n->__callback = $callbackObj;
	$n->setCallback("AcceptRender", "return igk_invoke_callback_obj(\$this, \$this->__callback, \$param);");	
	return $n;
}


//--------------------------------------------------
//HTML5

function igk_html_node_videoFileStream($location, $auth=false){
	$n = igk_createNode("video");
	$n["controls"]=1;
	//$n->activate("controls");
	$n["preload"]="auto";
	$n["src"] = $location;
	return $n;
}

///<summary>create a font symbol.</summary>
function igk_html_node_FontSymbol($name, $code){
	$nc = igk_font_get_code($name);
	$n  = igk_createNode("span");
	$n["class"]="+igk-font-symbol "."ft-".$name;
	$o = "";
	if (is_string($code)){
		$code = trim($code);
		if (preg_match("/^(0x|#)/", $code)){
			$code = preg_replace_callback("/^(0x|#)/", function(){return ""; }, $code);
			//$code = hexdec($code);
		}
		$o= '&#x'.$code.';';//$nc;
	}else{
		$o = '&#x'.IGKNumber::ToBase($code,16,4).';';//$nc;
	}
	//igk_ilog_assert(igk_is_debug(), "coder : ".$code . " ".(int) $code. " === ".$o);

	$n->Content = $o;
	return $n;	
}


function igk_html_node_CssComponentStyle($file, $host=null){	
	$n = IGKCssComponentStyle::getInstance()->regFile($file, $host);
	return $n;
}

function igk_html_node_trackbarNode($id, $value, $min=0, $max=100){
	$n = igk_createNode("input");
	
	$n->setId($id);
	$n["type"]="range";
	$n["class"]="igk-winui-trackbar";
	$n["min"]=$min;
	$n["max"]=$max;
	$n["value"]=$value;
	return $n;
	
}

function igk_html_node_spanGroup(){
	$n=igk_createNode("div");
	$n["class"]="igk-winui-span-group";
	return $n;
}

function igk_html_node_HLineSeparator(){
	$n=igk_createNode("div");
	$n["class"]="igk-hline-sep";
	return $n;
}



function igk_html_node_WEBGLScriptSurface($scriptFile, $shaderFolder=null){
	if (!igk_is_module_present("bge")){
		igk_die("/!\\ module :  bge is required ");
	}
	$c = igk_createNode("script");
	$c["type"]="balafon/bge-script";
	$c["language"]="";
	$c["class"]="igk-winui-bge-script";
	$c->Content =  igk_bge_get_shaders($shaderFolder) . " ".(file_exists($scriptFile)?file_get_contents($scriptFile): null);
	return $c;
}
function igk_html_node_JSBtn($script, $value=null){
	$n = igk_createNode("input");
	$n["type"] = "button";
	$n["class"] = "igk-btn";
	$n["onclick"]= "javascript: ".$script." return false";
	$n["value"]=$value;
	return $n;
}
//by replacing the content
function igk_html_node_AJXUpdateView($cibling){
	//igk-ajx-update-view
	$n = igk_createNode("div");	
	$n["class"] = "igk-ajx-update-view";
	$n["igk:target"]=$cibling;
	return $n;
}
//by append to current view
function igk_html_node_AJXAppendTo($cibling){	
	$n = igk_createNode("div");	
	$n["class"] = "igk-ajx-append-view";
	$n["igk:target"]=$cibling;
	return $n;
}


function igk_html_node_Word($v, $cl){
	$n = igk_createNode("span");
	$n->Content = $v;
	$n["class"] = "wd w-".$cl;
	return $n;
}

function igk_html_node_WordSplitView(){
	$n = igk_createNode("div");
	$n["class"] = "igk-ui-wplitview";
	return $n;
}
function igk_html_node_HtmlNode($tag){
	return  new IGKHtmlItem($tag);
}

///<summary>used to render data</summary>
function igk_html_node_arrayData($tab){	
	if (!is_array($tab)){
		igk_die("\$data must must be an array");
	}
	$n = igk_createNode("div");
	$n["class"]="+igk-array-data";
	foreach($tab as $k=>$v){
		$cv = $n->addDiv()->setClass("r")->setStyle("display:table-row");
		$cv->addDiv()->setClass("k")->setStyle("display:table-cell")->Content = $k;
		$cv->addDiv()->setClass("v")->setStyle("display:table-cell")->addObData( $v);
		
	}
	return $n;
}

function igk_html_node_ComponentNodeCallback($listener, $name, $callback){
		//$n = igk_html_node_NoTagNode();
		$c = $listener->getParam(IGK_NAMED_NODE_PARAM, array());
		if (isset($c[$name])){
			 $f = $c[$name];
			// $n->add($f);
			 return $f;
		}
		$h = $callback($listener, $name);
		if ($h){
			$c[$name] = $h;
			$h->setParam(IGK_NAMED_ID_PARAM, $name);
			$listener->setParam(IGK_NAMED_NODE_PARAM, $c);
			return $h;
		}		
		igk_die("failed to created component");
		return null;
}

///<summary>used to create component</summary>
function igk_html_node_Component($listener, $typename, $regName, $unregister = 0){
	if ($unregister){
		$b = $listener->getParam(IGK_NAMED_NODE_PARAM);
		$h =  igk_getv($b, $regName);

		if ($h){			
			$h->dispose();
			unset($b[$regName]);
			$listener->setParam(IGK_NAMED_NODE_PARAM,$b);
		}
	}
	return igk_html_node_ComponentNodeCallback($listener, $regName, function($l,$n)use($typename){		
		$c = igk_createNode($typename);	
		$c->setComponentListener($l, $l->getParam("sys://component/params/{$n}"));
		return $c;
	});
}

///<summary>used to evaluate the content. in xpthml file the content will be evaluated</summary>
function igk_html_node_viewContent($listener,$data=null){
	
	$d = igk_html_node_noTagNode();
	$d->listener = $listener; //setParam("listener", $listener);
	$d->setCallback("AcceptRender", "igk_html_viewContentAcceptRender");
	return $d;	
}
function igk_html_viewContentAcceptRender($a,$b){
	//"igk:FontSymbol class="+logo" igk:args="baobabtv, #F002" style="margin:0px 16px; padding-top:4px; display:inline-block; font-size:2em;" onclick="javascript: ns_igk.baobabtv.gohome(); return false"/>
	$a->clearChilds();	
	if ($a->listener){
		$a->add($a->listener);
		return 1;
	}
	return 0;
}
//<summary>javascript button
function igk_html_node_JSButton($js){
	$n  = igk_createNode("a");
	$n["href"]="#";
	$n["class"]="igk-btn igk-js-button";
	$n->addScript()->setContent(
<<<EOF
(function(q){
q.reg_event('click',function(evt){
{$js}
evt.stopPropagation();
evt.preventDefault();
});
q.select('script').remove();
})(\$igk(ns_igk.getParentScript()));
EOF
);
	return $n;
}

///<summary>print button</summary>
function igk_html_node_PrintBtn($uri=null){
	$s = igk_createNode("div");
	$s["class"] = "igk-btn igk-ptr-btn"; //print button
	$s["igk:data"]=$uri;
	return $s;
}

function igk_html_node_PopupMenu(){
	$n = igk_createNode("div");
	$n["class"] = "igk-winui-popup-menu";
	return $n;
}

function igk_html_node_SvgUse($name){
	$n = igk_html_node_NoTagNode();
	$n->Content = igk_svg_use($name);
	return $n;
}


function igk_html_node_AJXPaginationView($baseuri, $total, $perpage, $selected=1){
	return igk_html_node_paginationView($baseuri, $total, $perpage, $selected, 1);
}
function igk_html_node_paginationView($baseuri, $total, $perpage, $selected=1, $ajx=0, $cookiepath=null){
	//$baseuri:://mixed value string or callback
	$e = "";
	if ($ajx)
		$e.=", ajx:1";
	if ($cookiepath)
		$e.=", cookie:'{$cookiepath}'";
	if ($selected)
		$e.=", selected:'{$selected}'";
	 $s_o = (object)[
			"total"=>0,
			"selected"=>0,
			"maxButton"=>10
		]; 
	$settings = is_object($total)?
		igk_create_filterObject($total, $s_o):
		$s_o; 
		
		
	
$n = igk_createNode("div");
$n["class"]="igk-winui-pagination";
$n["igk:data"]="{baseuri:'{$baseuri}',min:1,max:10 {$e}}";
$n->addObData(function()use($baseuri, $total, $perpage, $selected){
	$min  = 0;
	$cmax = ceil($total / ($perpage));	//number of page visible
	$max = min(10, $cmax); //max visible button button item
	
	if ($selected <= $cmax){
		
		$min = max(0, $selected - 5);
		$max = min($cmax,  $selected + 5);//cmax;
		
		if (($max - $min)<10){
			if ($min ==0){
				//$max = 10;
			}else{
				$max = $cmax;
				$min = max(0,$cmax-10);
			}
		}
	}
	
	// igk_ilog("min: ".$min);
	
	$s = igk_createNode("div");
	
	if ($min>0)
		$s->add("span")->setClass("igk-btn")->Content = 1;
	if ($selected>1)
	$s->add("span")->setAttribute("rol", "prev")->Content = R::gets("Previous Page");
	
	for($i=$min; $i<$max; $i++){
		$p = ($i+1);
		$st = $s->add("span");
		$st["class"]="+igk-btn";
		if ($p == $selected){
			$st["class"]="+igk-selected";
		}		
		$st->Content = ($i+1);
	}
	if ($selected != $cmax)
	$s->add("span")->setAttribute("rol", "next")->Content = R::gets("Next Page");
	if ($max<$cmax)
		$s->add("span")->setClass("igk-btn")->Content = $cmax;
	$s->RenderAJX();
	
}, "div", array_slice(func_get_args(),1));
$n->addBalafonJS()->Content = "igk.winui.paginationview.init()";
return $n;
}

///<summary>shortcut to single node viewer</summary>
function igk_html_node_singleNodeViewer($tag=null){
	$s = 0;
	if ($tag!=null)
		$s = igk_createNode($tag);
	else 
		$s = igk_html_node_noTagNode();
	return new IGKHtmlSingleNodeViewer($s);
}


function igk_html_node_xslt($xml, $xslt, $global=0, $options=null){
	$o = $global ? $xslt : <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
xmlns="http://www.w3.org/1999/xhtml"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
<xsl:template match="/">{$xslt}</xsl:template>
</xsl:stylesheet>
EOF;

	
	$n  = igk_createNode("NoTagNode");
	$n->addObData("<!--".$xml."-->",'div')->setClass("xml")->setStyle("display:none");
	$n->addObData("<!--".$o."-->",'div')->setClass("xslt")->setAttribute("xslt:data", $options)->setStyle("display:none");
	$n->addBalafonJS()->Content = "igk.dom.xslt.initTransform();";
	
	// igk_wln(htmlentities($o));
	// exit;
	return $n;	
}
function igk_html_demo_xslt($t){
	
	$s=<<<EOF
<books>
	<book>
		<title>Balafon</title>
		<desc>452</desc>
	</book>
	<book>
		<title>DirectX</title>
		<desc>452</desc>
	</book>
	<book>
		<title>OpenGL</title>
		<desc>452</desc>
	</book>
</books>
EOF;
$f1 = <<<EOF
<div>
	<h2>Book Collection - Data</h2>		
	<xsl:for-each select="books/book">
	<div>
		<xsl:value-of select='title' />
	</div>
	</xsl:for-each>
</div>
EOF;
	$n = igk_html_node_xslt($s, $f1);

	igk_html_title($t, "xml" , 3);
	$t->addCode()->setAttribute("lang","php")->Content = $s;
	igk_html_title($t, "xslt", 3);
	$t->addCode()->setAttribute("lang","php")->Content = $f1;
	$t->add($n);
	return $t;	
}

///<summary>used to render a pick a huebar value</summary>
function igk_html_node_huebar(){
	$n = igk_createNode("div");
	$n["class"] = "igk-winui-huebar";
	$n->addDiv()->setClass("cur");
	$n->addBalafonJS()->Content = "igk.winui.huebar.init(); ";
	return $n;
}

function igk_html_demo_huebar($t){
	$t->addDiv()->Content = "Hubar -";
	$hbar = $t->add('huebar')->setStyle("width:200px; height:16px; border:1px solid black;");
	$t->addSpan()->setClass("huev")->Content= 'hsv';
}

///<summary>represent a zone node for text edition</summary>
function igk_html_node_textEdit($id, $uri, $c=null){
	$n = igk_createNode("span");
	$n["class"]="igk-textedit";
	$n["igk:data"]="{uri:'{$uri}', id:'{$id}'}";
	$n->Content = $c;
	
	return $n;
}

///<summary>represent the loremIpSum zone</summary>
///<param name="mode">verbose node</param>
function igk_html_node_LoremIpSum($mode=1){
	$n = igk_createNode("NoTagNode");
	switch($mode){
		default:
	$n->Content = <<<EOF
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum
EOF;
	break;
	}
	return $n;	
}


//-----------------------------------------------------------------------------------------
//Default page node Component
//-----------------------------------------------------------------------------------------
function igk_html_node_error404($title, $m){
	$n=igk_createNode("div");
	$n["class"]="error404";
	igk_html_title($n, $title);
	$box = $n->addPanelBox();
	$box->add($m);
	$n->Box = $box;
	return $n;
}



?>