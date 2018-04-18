<?php

//define inclusion
define("IGK_INC_APP_INITDB", IGK_LIB_DIR."/".IGK_INC_FOLDER."/igk_initapp_db.pinc");
/*
global authorisation : system/[apps]/[name]
*/


function igk_app_is_appuser($ctrl){
	return ($u = $ctrl->User) && $u->clLogin == $ctrl->Configs->{'app.DefaultUser'};
}

abstract class IGKAppCtrl extends IGKPageCtrlBase
implements IIGKUriActionRegistrableController
{
	static $sm_apps;//register applications	
	private static $INIT; //init application 
	
	private $m_Exposed;	
	private $m_db;
	const IGK_CTRL_APPS_KEY = IGKSession:: BASE_SESS_PARAM +0xA00;
	public static function & GetApps(){
		if (IGKAppCtrl::$sm_apps===null){
			$m = igk_app()->Session->getParam(__METHOD__);
			if ($m===null)
			{
				$m  = (object)array('_'=>array()); //bind object to have the possibility to update data
				igk_app()->Session->setParam(self::IGK_CTRL_APPS_KEY, $m);					
			}
			IGKAppCtrl::$sm_apps = & $m;
		}
		return IGKAppCtrl::$sm_apps;
	}
	public function getDb(){
		if($this->m_db == null){
			$this->m_db = $this->_p_createDbManager();
			if ($this->m_db == null)
				throw new Exception("no db uility created");
		}
		return $this->m_db;
	}
	public function View(){
		
		$c = "app";	
		if ($this->RegisterToViewMecanism && !$this->IsActive()){
			//init as default document for parent			
			$c = "docview";
			$this->setEnvParam(IGK_CURRENT_DOC_PARAM_KEY, null);
			$doc = $this->Doc;
			
			if ($doc !== null){				
				igk_html_add($this->TargetNode, $doc->body->addBodyBox()->ClearChilds());
			}
			else{
				igk_wln("Session probably destroyed".$this->Doc);
				igk_wln("create at : ".igk_app()->Session->getParam(IGK_KEY_PARAM_SESSION_START_AT));
				igk_wln("session time out ". ini_get('session.gc_maxlifetime'));
				igk_die("Session kill");				
			}
		}		
		$this->setEnvParam(IGK_CTRL_VIEW_CONTEXT_PARAM_KEY, $c);	
		parent::View();
	}
	public function getSystemVars(){
		$doc = $this->getEnvParam(IGK_CURRENT_DOC_PARAM_KEY);		
		if ($doc===null)
		{
			if (igk_sys_is_subdomain()  && ($this->App->SubDomainCtrl == $this)){
					$doc = $this->AppDocument;
			}else{
					$doc = igk_app()->Doc;		
			}
			$this->setEnvParam(IGK_CURRENT_DOC_PARAM_KEY, $doc);
		}
		return parent::getSystemVars();		
	}
	
	///<summary>can be invoked in platform's view mecanism</summary>
	public function getRegisterToViewMecanism(){
		$f = igk_app()->Session->PageFolder;		
		return ($this === igk_get_defaultwebpagectrl()) && ($f == IGK_HOME_PAGEFOLDER);//; IGK_CONFIG_PAGEFOLDER); 
	} 
	
	///<summary>override to create the application db utility intance </summary>
	protected function _p_createDbManager(){
		return new IGKDbUtility($this);
	}
	
	///<summary>get exposed functions list</summary>
	public function getExposed(){
		if ($this->m_Exposed===null)
		{
			$this->m_Exposed = array(
				"about"=>1,
				"logout"=>1
			);
		}
		return $this->m_Exposed;
	}
	
	///<summary>get authorisation key</summary>
	public function getAuthKey($k=null){	
		return igk_ctrl_auth_key($this, $k);
	}
	public function isAuthKeys($k){
		if (preg_match("/^(".$this->getAuthKey().")/", $k))
			return true;
		return false;
	}
	
	
	///<summary>check before controller add</summary>
	public static function CheckBeforeAddControllerInfo($request){
		// igk_ilog("chec app");
		// igk_ilog($request);
		// igk_ilog(IGK_IS_FQN_NS_REGEX);
		
		$title = igk_getv($request, IGK_CTRL_CNF_TITLE); 
		$appname = strtolower(igk_getv($request,IGK_CTRL_CNF_APPNAME));
		$c = self::GetApps();
		//on reloading apps 
		if (isset($c->apps[$appname]) && igk_is_class_incomplete($c->apps[$appname])){
			unset($c->apps[$appname]);
		}
		
		//igk_wln("?".!preg_match(IGK_IS_FQN_NS_REGEX, $appname));
		//check registrated application by title
		if (empty($title) || empty($appname) || 
			//!preg_match(IGK_ISIDENTIFIER_REGEX, $appname)
			!preg_match(IGK_IS_FQN_NS_REGEX, $appname)
			|| isset($c->apps[$appname])
			)
		{		
			return false;
		}
		return true;
	}
	///<summary>check init and init user to this apps </summary>
	protected function checkUser($nav=true, $uri=null)
	{
		$r = true;
	
		$u = $this->App->Session->User;
		$ku = $this->User;
	
		// if (($ku== null) || ($ku !== $u))
		if ($ku==null) 
		{			
			if ($u != null)
			{
				$this->User = $this->initUserFromSysUser($u);
			}
			else 
				$r = false;
		}
		if ($nav && !$r)
		{
			$s = "q=".base64_encode(igk_io_baseRequestUri());
			$u = ($uri==null?$this->getAppUri(""): $uri);
			$u .= ((strpos($u, "?") === false)?
				"?" : "&").$s;
			
			igk_navto($u);
		}
		return $r;
	}
	
	public static function GetAdditionalDefaultViewContent(){
	return <<<EOF
<?php
\$t->ClearChilds();
\$t->addDiv()->addSectionTitle(4)->Content = R::ngets("Title.App_1", \$this->AppTitle);
\$t->inflate(igk_io_dir(\$dir."/".\$fname));
?>
EOF;

// \$r = \$t->addContainer();
// \$r->addDiv()->setClass("igk-title")->Content = R::ngets("Title.App_1", \$this->AppTitle);
// \$r->add("blockquote")->Content = R::ngets("msg.welcome_1",\$this->AppTitle);
// igk_add_article(\$this , 'default', \$t);
	}
	
	public static function InitEnvironment($ctrl){
		//init application environment
		IGKIO::CreateDir($ctrl->getDataDir());
		IGKIO::CreateDir($ctrl->getDataDir()."/R");
		
		
		//create dummy image app_app_ico
		$s = IGKGD::Create(256,128);		
		IGKIO::WriteToFileAsUtf8WBOM($ctrl->getDataDir().IGK_APP_LOGO,  $s->RenderText(), true);	
IGKIO::WriteToFileAsUtf8WBOM($ctrl->getDataDir().IGK_APP_LOGO.".gkds", <<<EOF
<gkds>
  <Project>
    <SurfaceType>IconSurface</SurfaceType>
  </Project>
  <Documents>
    <LayerDocument PixelOffset="None" BackgroundTransparent="True" Width="256" Height="128" Id="LayerDocument_918761">
      <Layer Id="Layer_17003755">       
      </Layer>
    </LayerDocument>
  </Documents>
</gkds>
EOF
	, true);
		return true;
	}
	
	public function getAppImgUri(){
		//return igk_html_uri(igk_io_baseUri()."/".igk_io_basePath($this->getDataDir().IGK_APP_LOGO));
		return igk_html_resolv_img_uri($this->getDataDir().IGK_APP_LOGO);
	}
	///<summary>check user auth demand level</summary>
	public function IsUserAllowedTo($authDemand=null)
	{				
		if ($this->User === null){
			return false;
		}
		if ($this->User->clLevel == -1)
			return true;
		return igk_sys_isuser_authorize($this->User, $authDemand);
	}
	public function getAppTitle(){
		return  igk_getv($this->Configs, IGK_CTRL_CNF_TITLE);	
	}
	public function getAppName(){
		return  igk_getv($this->Configs, IGK_CTRL_CNF_APPNAME);	
	}
	public function getBasicUriPattern(){
		return  igk_getv($this->Configs, IGK_CTRL_CNF_BASEURIPATTERN);
	}
	public function getDataTablePrefix(){
		return  igk_getv($this->Configs, IGK_CTRL_CNF_TABLEPREFIX);
	}
	public function getcanAddChild(){
		return false;
	}	
	public function IsActive(){//get if this app is currently active from uri	
		$inf = igk_sys_ac_getpatterninfo();	
		// igk_wln("pattern info");
		// igk_wln($inf);
		//exit;
		return (($inf!=null) && preg_match( igk_sys_ac_getpattern( $this->getBasicUriPattern()),  igk_io_rootBaseRequestUri()));	
	}
	public function getIsVisible(){					
		$v = $this->RegisterToViewMecanism;
		$c = $v  || (!$this->getAppNotActive() &&  $this->IsActive());		
		return $c;
	}
	///<summary>get if this application is not active</summary>
	public function getAppNotActive(){//activate from data
		return igk_getv($this->Configs, IGK_CTRL_CNF_APPNOTACTIVE);
	}
	public static function GetAdditionalConfigInfo(){			
		return array(
		IGK_CTRL_CNF_TITLE=>igk_createAdditionalConfigInfo(array("clRequire"=>1)),
		IGK_CTRL_CNF_APPNAME=>igk_createAdditionalConfigInfo(array("clRequire"=>1)),		
		IGK_CTRL_CNF_BASEURIPATTERN=>igk_createAdditionalConfigInfo(array("clRequire"=>1)),
		IGK_CTRL_CNF_TABLEPREFIX=>igk_createAdditionalConfigInfo(array("clRequire"=>1,"clDefaultValue"=>"tbigk_")),
		IGK_CTRL_CNF_APPNOTACTIVE=>(object)array("clType"=>"bool", "clDefaultValue"=>"0"));
	}
	public static function SetAdditionalConfigInfo(& $t)
	{
		$t[IGK_CTRL_CNF_BASEURIPATTERN] = igk_getr(IGK_CTRL_CNF_BASEURIPATTERN); 
		$t[IGK_CTRL_CNF_TITLE] = igk_getr(IGK_CTRL_CNF_TITLE); 
		$t[IGK_CTRL_CNF_APPNAME] = strtolower(igk_getr(IGK_CTRL_CNF_APPNAME)); 		
		$t[IGK_CTRL_CNF_APPNOTACTIVE] = igk_getr(IGK_CTRL_CNF_APPNOTACTIVE); 
		$t[IGK_CTRL_CNF_TABLEPREFIX] = igk_getr(IGK_CTRL_CNF_TABLEPREFIX);
	}
	public function storeConfigSettings(){
		parent::storeConfigSettings();
		$this->register_action();
	}
	
	public function SetupCtrl($param){
		parent::SetUpCtrl($param);
		//create group for all api registrated to system
		$t = strtolower(str_replace(' ','_',$this->AppName));
		if (empty($t))
			throw new Exception("Can't setup controller");
		$c = array($t, $t."_administrator");
		foreach($c as $k){		
			$e = igk_db_table_select_where(IGK_TB_GROUPS, array(IGK_FD_NAME=>$k), $this);
			if ($e && !$e->Success){
				// $s = $e->Success;		
				// if (!$s){
					//register basics group name
					igk_db_insert($this, IGK_TB_GROUPS, array(IGK_FD_NAME=>$k));
				// }
			}			
		}
		// $this->Configs->AppName = $this->AppTitle;
		// $this->storeConfigSettings();
	}
	protected function InitComplete(){
		parent::InitComplete();
		$n = str_replace("\\",".",$this->AppName);
		$c = self::GetApps();				
		if (empty($n)){
			$n = str_replace("\\",".",$this->Name);
		}
		if (preg_match(IGK_IS_FQN_NS_REGEX, $n)			 
			&& !isset($c->_[$n]))
		{
			$c->_[$n] = $this->getName();
		}
		else {	

			// igk_ilog("reloading ctrl ? ".igk_get_env("sys://reloadingCtrl", 1));		
			// igk_ilog("reloading isset ? ".isset($c->apps[$n] ));		
			// igk_ilog("try to get ".$n);
			
			igk_assert_die(!igk_get_env("sys://reloadingCtrl"), "Error : Application or identifier is not valid :: ".igk_getv(self::$sm_apps, $n)
			. " :: n = ".$n
			. " :: ".get_class($this)
			);
		}
		$this->register_action();
		if (!isset(self::$INIT)){
			igk_notification_reg_event(IGK_EVENT_DROP_CTRL, "igk_app_ctrl_dropped_callback");
			//$c->init =true;
			self::$INIT = true;
		}		
		//all apps must handle it's view invocation view
		IGKOwnViewCtrl::RegViewCtrl($this, 0);
	}
	protected final function register_action(){	
		$k = $this->getParam("appkeys");
		//unregiter diseable registration
		if (!empty($k))
		{
			igk_sys_ac_unregister($k);
		}
		//enable key registration		
		//register app contains
		$k = $this->getRegUriAction();			
		if (!empty($k)){
			igk_sys_ac_register($k, $this->getRegInvokeUri());
			$this->setParam("appkeys", $k);
		}
	}
	public function getRegUriAction(){
		$primary = $this->getBasicUriPattern();
		if (empty($primary))
			return null;
		return "".$primary.IGK_REG_ACTION_METH;
	}
	
	public function getRegInvokeUri(){
		return $this->getUri("evaluateUri");
	}
	///<summary>check that if the controller handle base uri</summary>
	public function is_handle_uri($uri=null){
		if (igk_const('IGK_REDIRECTION')==1){
			//disable uri for redirection
			if (preg_match("#^/!@#", igk_io_request_uri()))
				return false;	
		}
		//igk_wln("handle ? ".$this->RegisterToViewMecanism);
		return $this->RegisterToViewMecanism;
	}
	
	protected function HandleError($code=0){
		return 0;
	}
	protected function getAllowViewDirectAccess(){
		return 0;
	}
	
	
	
	
	public function handle_redirection_uri($u){
		
		igk_sys_handle_uri();
		//init variables
		extract(array("page","k","pattern", "p","c", "param","viewdefault"));
		
		if (is_string($u)){
			$page = explode("?", $u);		
			$k="^(/)?(/:function(/|/:params+)?)?";
			$pattern = igk_pattern_matcher_get_pattern($k);
			$p = igk_pattern_get_matches($pattern, $page[0],igk_str_get_pattern_keys($k));	
			$c = igk_page(igk_getv($p, "function"));
			$param = igk_getv($p, "params");		
		}
		else{
			// igk_wln(array_keys((array)$u));
			// igk_wln($u->value);
			// igk_wln($this->RegisterToViewMecanism);
			unset($u->ctrl);
			// igk_wln($u);
			$page = explode("?", $u->uri);		
			$pattern = $u->pattern;
			$p = $u->getParams();//igk_pattern_get_matches($pattern, $page[0],$u->keys);	
			// igk_wln($p);
			//igk_wln($u->getParams());
			$c = igk_page(igk_getv($p, "function"));
			$param = igk_getv($p, "params");		
			$viewdefault = 1;
			//exit;
		}
		// igk_wln("icn site map");
		include(IGK_LIB_DIR."/Inc/igk_sitemap.pinc");
		$tn = $this->TargetNode;
		// igk_html_rm($tn);
		// igk_wln("1".$tn->getParentNode());
		
		if ($this->_handle_uri_param($c, $param))
			igk_exit();
		

		
		$this->regSystemVars(null); //reset var parameters 

		
		// igk_ilog($c);
		
		if (empty($param))
			$param = array();
		
		if (empty($c) && ($this->RegisterToViewMecanism || $viewdefault)){
			$this->renderDefaultDoc(igk_conf_get($this->Configs, "/default/document",'default'));
			igk_exit();
			
		}
		$doc = $this->AppDocument;
		$this->setEnvParam(IGK_CURRENT_DOC_PARAM_KEY, $doc);
		//$doc = igk_get_document($this, true);	
		//if function or view exists 
		$fnc = $this->getViewFile("default");
	// igk_wln($c);
	// igk_wln( strstr(basename($fnc), $c));
	// igk_exit();
	$handle = 0;
	if (( file_exists($fnc) && !empty(strstr(basename($fnc), $c))) 
		|| ! ($handle = $this->handle_func($c, $param, $doc, 0)))
		{
	
		if (preg_match(IGK_VIEW_FILE_END_REGEX , $c)){				
			// igk_wln($fnc);
			// igk_exit();
			$this->setCurrentView($c, true, null, $param);					 

			$c = $this->getEnvParam(IGK_CTRL_VIEW_CONTEXT_PARAM_KEY);

			if (igk_is_ajx_demand()){
				igk_ajx_replace_node($tn);						
			}else{
				if ($c == "docview"){
					igk_app()->Doc->RenderAJX();
				}else{							
					$bbox = $doc->body->addBodyBox();
					$bbox->clearChilds()->add($tn);
					$doc->RenderAJX();
				}
			}
			//reset current view
			$this->resetCurrentView(null);	
			igk_exit();
		}
			
		// if (!$this->handle_func($c, $param, $doc, 0)){
		// }
		
		}
		
		
		if ($handle){
			return 1;
		}
		
		
		$actionctrl = igk_getctrl(IGK_SYSACTION_CTRL) ?? igk_die("no action ctrl found");		
		$ck = $this->getParam("appkeys");
		$m = $actionctrl->matche($page[0]);
	
		if ($m){//try to get if match internal uri system
			if (igk_sys_is_subdomain() && ($m->action === $ck)){
				//can't show data with the saved app model
				$d = igk_createNode("div"); 
				$d->Content = "/!\\ subsequent call to controller when it's a default controller is not allowed.=== {$m->action}"; 
				// igk_ilog("/!\\ subsequent call to controller when it's a default controller is not allowed"); 				
				igk_set_header(302);
				$d->RenderAJX();
				// igk_wln($ck);
				// igk_wln($page);
				// igk_exit();
				return true;
			}
			igk_app()->Session->RedirectionContext = 1;				
			$actionctrl->invokeUriPattern($m);
		}else{			
			igk_sys_show_error_doc(igk_getr('code',505), $this, null);
			igk_exit();			
		}				
		return false;
	}
	
	public final function evaluateUri(){		
		$inf = igk_sys_ac_getpatterninfo();	
		if ($inf===null){
			return;
		}	
		$this->handle_redirection_uri($inf);
		igk_exit();
	}		
	
	///<summary> override this method to handle shortcut evaluationUri according to function and param</summary>
	///<return> true if handled otherwise false</return>
	protected function _handle_uri_param($fc, $param){
		return false;
	}
	
	
	protected function getRootPattern(){
		$t = array();
		$broot=$this->getBasicUriPattern();
		
	
		$s = preg_match_all("/(\^\/)?(?P<name>(([^\/]+)(\/([^\/]+)\/?)?))/i", $broot, $t);	
		
		if ($s>0){
			$o = $t["name"][0];
			return $o;
		}
		return null;
	}
	///<summary>return application uri</summary>
	public function getAppUri($function=null){
		
		$srv = igk_getv($_SERVER,"SERVER_NAME");
		$f = null;
		$v_sd = $this->App->SubDomainCtrl;
		if (($v_sd!=null) && ($v_sd === $this)){			 
			$f = igk_sys_srv_uri_scheme()."://".$srv;
		}else{
		
			if  (IGKValidator::IsIpAddress($srv) || ($v_sd == null))		
				$f = igk_str_rm_last(igk_io_baseUri(),'/');
			else
				$f = igk_str_rm_last(igk_io_currentBaseDomainUri(),'/');
			
			$def= igk_get_defaultwebpagectrl();
			if (($v_sd !== $this) && ($def !==$this) && ($rt = $this->getRootPattern()))
				$f .= "/".$rt;		
		}		
		if ($function )
			$f .= "/".$function; //"/".str_replace("_","/", $function);		
		
	
		return $f;
	}
	///<summary>getApp Uri by transforming to balafon requirement</summary>
	public final function getAppUris($function=null){
		return $this->getAppUri(igk_str_view_uri($function));
		
		
	}
	
	// public final function getBaseAppUri($function=null){
		// $f = IGKIO::GetBaseUri();
		
		// igk_str_rm_last(igk_io_currentBaseDomainUri(),'/');
		// if ($rt = $this->getRootPattern())
			// $f .= "/".$rt;
		// if ($function )
			// $f .= "/".igk_str_view_uri($function);
		// return $f;
	// }
	
	
	public function load_data(){
	
	
		$doc = new IGKHtmlDoc($this->App, true);
		$d = $doc->Body->add("div");
		$frm = $d->addForm();
		$frm["action"]= $this->getAppUri("load_data_files");
		$frm["method"]="POST";
		$i = $frm->addInput("clFileName", "file");
		$i["class"]="dispn";
	//	$i["style"]="display:none;";
		$i["multiple"]="false";
		$i["accept"] = "text/xml";
		$i["onchange"] = "this.form.submit(); return false;";
		$frm->addInput("clRuri", "hidden", $this->getAppUri(""));
		//$frm->addInput("clLoad", "submit");
		$frm->addScript()->Content = 
<<<EOF
(function(){var f = \$ns_igk.getParentScriptByTagName('form'); f.clFileName.click();})();
EOF;

		$doc->RenderAJX();
		igk_exit();
	}
	
	protected final function checkFunc($funcname){
		if (igk_is_conf_connected() || $this->UserAllowedTo($funcname))
			return true;
		igk_notifyctrl()->addWarning(R::ngets("warning.usernotallowed_1", $funcname));
		igk_navto($this->getAppUri(""));
		
		igk_exit();
		return false;
	}
	///<summary> save data schema</summary>
	public function save_data_schemas($exit=1){
		$this->checkFunc(__FUNCTION__);
		//must be admin for that functions		
		$dom =  IGKHtmlItem::CreateWebNode(IGK_SCHEMA_TAGNAME);	
		$dom["ControllerName"]=$this->Name;
		$dom["Platform"] = IGK_PLATEFORM_NAME;
		$dom["PlatformVersion"] =IGK_WEBFRAMEWORK;
		
		$e =  IGKHtmlItem::CreateWebNode("Entries");
		//build schemas
		$d = $this->loadDataFromSchemas();
		if ($d ){
			$tabs = array();
			foreach($d as $k=>$v)
			{
				// igk_wln($v);
				// igk_exit();
				$b = $dom->add("DataDefinition");
				$b["TableName"] = $k;
				$b["Description"] = $v["Description"];
				$tabs[] = $k;
				foreach($v["ColumnInfo"] as $cinfo){
					$col = $b->add(IGK_COLUMN_TAGNAME);
					$tb =  (array)$cinfo;
					$col->setAttributes($cinfo);
					// igk_wln($cinfo);
					// igk_wln("Tb");
					// igk_wln($tb);
					// igk_wln("Render");
					// igk_wln($col->Render());
				}
				// igk_wln($v);
				// $b->RenderAJX();
				// igk_exit();
			}			
			$db = igk_get_data_adapter($this);
			$r = null;
			if ($db){
				
				$db->connect();
			
					foreach($tabs as $tabname)
					{
						try{
							$r = $db->selectAll($tabname);
							if ($r->RowCount>0)
							{
								$s = $e->add($tabname);
								foreach($r->Rows as $c=>$cc)
								{
									$irow = $s->addXMLNode(IGK_ROW_TAGNAME);
									$irow->setAttributes($cc);
								}
							}
						}
						catch(Exception $ex)
						{
						}
					}				
				$db->close();				
			}
		}
		//build data entries
		if ($e->HasChilds){
			$dom->add($e);
		}
		if ($exit)
			header("Content-Type: application/xml");
		$dom->RenderAJX();
		if ($exit)
		igk_exit();
	}
	public function get_data_schemas(){
		$u = $this->App->Session->User;
		if (!igk_is_conf_connected() && !$this->IsUserAllowedTo("system/:".__FUNCTION__)){
			igk_wln("user not allowed to");
			igk_exit();
		}	
		$f = $this->getDataDir()."/data.schema.xml";
		if (file_exists($f))
		{
			$s = IGKHtmlReader::LoadFile($f);
			$s->RenderXML();
		}
		else{
			$d =  IGKHtmlItem::CreateWebNode(IGK_SCHEMA_TAGNAME);
			$d->RenderXML();
		}
		igk_exit();
	}
	public function load_data_files(){		
		if (isset($_FILES["clFileName"]))
		{
		$f = $this->getDataDir()."/data.schema.xml";
		$dom =  IGKHtmlItem::CreateWebNode("dummy");
		$dom->Load(IGKIO::ReadAllText($_FILES["clFileName"]["tmp_name"]));
				$d = new IGKHtmlDoc($this->App, true);
			$div = $d->Body->add("div");
			
			
		if (igk_count($dom->getElementsByTagName(IGK_SCHEMA_TAGNAME)) == 1){
			igk_io_move_uploaded_file($_FILES["clFileName"]["tmp_name"], $f);
			$div->add("div", array("class"=>"igk-title"))->Content = R::ngets("Title.GoodJOB");
			$div->add("div", array("class"=>"igk-notify igk-notify-success"))->Content = R::ngets("msg.fileuploaded");
		}		
		else{
			$div->add("div", array("class"=>"igk-title"))->Content = R::ngets("Title.Error");
			$div->add("div", array("class"=>"igk-notify igk-notify-danger"))->Content = R::ngets("error.msg.filenotvalid");
		}
		$d->RenderAJX();
		unset($d);
		unset($dom);
		}
		else{
			igk_navtobase("/");
		}
		igk_exit();
	}
	
	///<summary> synchronize the current user data to target server</summary>
	public function sync_user_data($login=null){
		if (($login == null) && ($this->User!=null))
			$login = $this->User->clLogin;
		$c = igk_get_user_bylogin($login);
		$d = igk_createNode("response");
		if ($c == null){			
			$d->addXmlNode("error")->Content = "LoginNotFound";
			$d->addXmlNode("msg")->Content = "login . not present on our database"; 
			igk_wln($d);
			igk_exit();
		}
		ob_start();
		$this->save_data_schemas(0);
		$s = ob_get_contents();
		ob_end_clean();		
		igk_wl($s);		
		igk_exit();		
	}
	public function sync_from_user_data(){
		igk_wln(__FUNCTION__." Not implements");
	}
	
	
	protected function bind_func($func, $args){		
		if ($func)
		{		
			$cl = get_class($this);
			
			if (method_exists($cl, $func) )
			{
				call_user_func_array(array($this,$func), $args);
				return true;
			}
		}
		return false;
	}
	///<summary>get if function is available</summary>
	function IsFuncUriAvailable(& $func){
		//igk_wln(__FUNCTION__);
		// throw new Exception("ds");
		//grant all access to config administrative user
		if (igk_is_conf_connected() || isset($this->Exposed[$func]))		
			return true;		
		//check app configuration function list
		$lst = $this->_getfunclist(false, $func);
		
	
		if (igk_array_value_exist($lst, $func))
			return true;
		return false;
	}
	
	public function administration(){
		$doc = new IGKHtmlDoc($this->App, true);
		$div = $doc->body->addDiv();
		$div["class"] = "igk-notify igk-notify-warning";
		$div["style"] = "display:block; position:absolute; top:50%; min-height:96px; margin-top:-48px;";		
		$div->Content = "No administration page";	
	
		
		$div = $doc->body->addDiv();		
		$div["style"] = "font-size: 3em; ";
		$div->addA($this->getAppUri(""))->setClass("glyphicons no-decoration")->Content = "&#xe021;";
		
		$doc->RenderAJX();
	}
	protected function renderError($c){
			//render error
			$f = igk_io_baseDir("Pages/error_404.html");		
			if (file_exists($f))
			{
				include($f);
			}
			else{
			$d = new IGKHtmlDoc($this->App, true);			
			
			$d->Title = R::ngets("title.app_2", igk_getv($this->Configs, IGK_CTRL_CNF_TITLE), $this->App->Configs->website_title);
			$div = $d->Body->add("div");
			$div->add("div", array("class"=>"igk-title"))->Content = R::ngets("Title.Error");
			$div->add("div", array("class"=>"igk-notify igk-notify-danger"))->Content = "No function $c found";
			$d->RenderAJX();
			unset($d);
			}
	}
	///<summary> get a application document. getDoc return the global document </summary>
	protected function getAppDocument($newdoc=false){		
		return igk_get_document($this, $newdoc);
	}
	///<summary>get the application current document</summary>
	public  function getCurrentDoc(){
		//check from view context	
		return $this->AppDocument;
	}
	
	protected function __viewDoc($view='default', $doc=null, $render=true){
		$d = $doc;
		if ($d == null){
			//igk_app()->Session->setParam(IGK_KEY_DOCUMENTS, null);			
			$d = $this->getAppDocument(true);
		}	
		//igk_wln("? ".($d === igk_app()->Doc));
		if (($d === igk_app()->Doc))
		throw new Exception("/!\\ app document match the global document. That is not allowed");
	
	
			$d->Title = R::ngets("title.app_2", igk_getv($this->Configs, IGK_CTRL_CNF_TITLE), $this->App->Configs->website_title);
		//igk_exit();
		$this->setEnvParam(IGK_CURRENT_DOC_PARAM_KEY, $d);
		$bbox = $d->Body->addBodyBox();	
		// $bbox->ClearChilds();
		igk_doc_set_favicon($d, $this->getDataDir()."/R/Img/favicon.ico");					
		
		$this->setCurrentView($view, true);
		$bbox->add($this->TargetNode);
		if ($render){
			$d->RenderAJX();
		}		
	}
	protected function renderDefaultDoc($view='default', $doc=null, $render=true){
		$this->__viewDoc($view, $doc, $render);
	}

	//---------------------------------------------------------------------------
	//app:system view functions
	//---------------------------------------------------------------------------
	public function about(){
		$doc = $this->createNewDoc();
		
		$doc->Title =  R::ngets("title.about_1", $doc->Title);
		
		$f = $this->getViewFile("about");		
		if (file_exists($f))
		{
			$this->__viewDoc('about', $doc, false);	
		}
		else{
			$bbox = $doc->body->addBodyBox();
			$bbox->ClearChilds();
			$t = $bbox;
			$t->addContainer()->addCol()->addDiv()->setClass("igk-fsl-4")->Content = R::ngets("title.about");
			$ct = $t->addDiv()->addContainer();

			$ct->addCol()->addDiv()->Content = "Version : ".igk_getv($this->Configs, "Version", "1.0");
			$ct->addCol()->addDiv()->Content = "Author : ".IGK_AUTHOR;
			$ct->addCol()->addDiv()->Content = "CONTACT : ".IGK_AUTHOR_CONTACT;
			$dv = $ct->addWebMasterNode()->addCol()->addDiv();
			$dv->Content = "Location : ".$this->getDeclaredFileName();
		}	
		
		$doc->RenderAJX();
		$doc->RemoveChilds();
		$doc->Dispose();
		unset($doc);
	}
	
	
	//---------------------------------------------------------------------------
	//app: system db functions
	//---------------------------------------------------------------------------
	
	public function initDb($s=null){
		parent::initDb();
		if ($s){
			igk_navto($this->getAppUri());			
		}		
		if (igk_uri_is_match(igk_io_currentUri(),$this->getAppUri(__FUNCTION__)))
		{
			igk_ilog("notify message :  ".IGK_NOTIFICATION_DB_CHANGED);
			igk_notification_push_event(IGK_NOTIFICATION_DB_CHANGED, $this, null);
			igk_navto($this->getAppUri());			
		}
	}
	public final function resetDb(){		
		$s_d = igk_app_is_uri_demand($this, __FUNCTION__);
		if (!$s_d){
			$s = igk_is_conf_connected() || $this->IsUserAllowedTo($this->Name.":".__FUNCTION__);
			if (!$s){
				igk_notifyctrl()->addError("not allowed");
				return;
			}
			
		}else{
			$s = igk_is_conf_connected() || $this->IsUserAllowedTo($this->Name.":".__FUNCTION__);
		}
		if (!$s){			
			if ($s_d){
				igk_navto($this->getAppUri());
			}
			return;
		}
		
		//igk_ilog_w("reset db for ".__CLASS__);		
		$this->dropdb();		
		$ad = igk_get_data_adapter($this);
		$ad->initForInitDb();
		$this->initDb();				
		$ad->flushForInitDb();			
		$this->logout(0);	
		if (igk_uri_is_match(igk_io_currentUri(),$this->getAppUri(__FUNCTION__))){
		
			igk_notification_push_event(IGK_NOTIFICATION_DB_CHANGED, $this, null);
			igk_navto($this->getAppUri());
			igk_exit();
		}
	}
	
	
	///<summary>drop application table from system config</summary>
	public final function dropdb(){
		//$this->User = null;		
		//igk_wln($this->Db->select(IGK_TB_USERS, array("clLogin"=>"bondje.doue@igkdev.com"))->getRowAtIndex(0));
		//$this->User = null;// $this->Db->select(IGK_TB_USERS, array("clLogin"=>"bondje.doue@igkdev.com"))->getRowAtIndex(0);
		$s = igk_is_conf_connected() || $this->IsUserAllowedTo($this->Name.":".__FUNCTION__);
		if (!$s){
			//igk_html_wln_log("Error", "User Not allowed to ".__FUNCTION__);
			if (igk_app_is_uri_demand($this, __FUNCTION__)){
				igk_navto($this->getAppUri());
			}
			return;
		}
		
		
		if(!igk_getv($this->getConfigs(), "clDataSchema"))
		{
				$db = igk_get_data_adapter($this, true);
				
				if ($db && $db->connect()){
					$db->dropTable($this->getDataTableName());
					$db->close();
					
			
			}
		
		}
		else{
				$tb = $this->loadDataFromSchemas();	
				
				$db = igk_get_data_adapter($this, true);
				if ($db){			
					if ($db->connect()){
						// igk_wln("connect...=".$db->getDbName());
						// igk_db_dump_result($db->sendQuery('SELECT DATABASE()'));			
						//$db->selectdb($this->App->Configs->db_name);
						$v_tblist = array();
						foreach($tb as $k=>$v)
						{		
							$v_tblist [$k]=$k;						
						}				
						//igk_wln("drop table ".$k);
						//igk_html_wln_log("DropTableList", 
						$db->dropTable($v_tblist);
						$db->close();
						
				}	
		}
		}	

		if (igk_app_is_uri_demand($this, __FUNCTION__)){
				igk_navto($this->getAppUri());
		}
		// igk_createNode()->addA($this->getAppUri())->setContent("Go To Main page")->getRootNode()->RenderAJX();//renderRootNode();
		// igk_exit();
	
	}
	
	public final function dbinitentries(){
		$s = igk_is_conf_connected() || $this->IsUserAllowedTo(igk_ctrl_auth_key($this, __FUNCTION__));
		if (!$s){
			igk_notifyctrl()->addErrorr("err.operation.notallowed");
			igk_navto($this->getAppUri());
			igk_exit();
		}
		
		if ($this->getUseDataSchema()){
		$r = $this->loadDataAndNewEntriesFromSchemas();	
		$tb = $r->Data; //$this->loadDataFromSchemas();
		$etb = $r->Entries; //$this->loadDataNewEntriesFromSchemas();		
		$ee = igk_createNode();
		$db = igk_get_data_adapter($this, true);
		if ($db){			
			if ($db->connect()){
				// igk_wln("connect...=".$db->getDbName());
				// igk_db_dump_result($db->sendQuery('SELECT DATABASE()'));			
				//$db->selectdb($this->App->Configs->db_name);
				
				foreach($tb as $k=>$v)
				{
					$n = igk_db_get_table_name($k);
					// igk_wln("table ".$n);
					$data = igk_getv($etb, $k);
					if (igk_count($data)==0)
						continue;
						
					if ($db->tableExists($n)){
						// igk_wln("table exists ".$n);
						foreach($data as $kk=>$vv){
							// igk_wln("try to insert ");
							// var_dump($vv);
							igk_db_insert_if_not_exists($db, $n, $vv);
							// igk_db_insert($db, $n, $vv);
							if ($db->getHasError()){
								$dv= $ee->addDiv();
								$dv->addNode("div")->Content = "Code : ".$db->getErrorCode();
								$dv->addNode("div")->setClass("error_msg")->Content = $db->getError();
							}
						}
					}else{
						$r = $db->createTable($n, 
							igk_getv($v, 'ColumnInfo'), 
							$data, 
							igk_getv($v, 'Description'),
							$db->DbName
						);	
					}
					
				}			
				$db->close();
			}
		}
		}
		if ($ee->HasChilds){
			igk_notifyctrl()->addError($ee->Render());
		}else{
		igk_notification_push_event(igk_get_event_key($this, "dbchanged"), $this);
		$this->logout();
		}
		igk_navto($this->getAppUri());			
		igk_exit();
	}
	
	
	
	protected function setDefaultFavicon($doc){
		$d = $this->getDataDir()."/R/Img/favicon.ico";	
		igk_doc_set_favicon($doc, $d);		
	}
	public function  createNewDoc($clear=false){	
		$doc = $this->getParam("app/document");
		if ($doc == null)
		{
			$doc = new IGKHtmlDoc($this->App, true);
			$this->setParam("app/document", $doc);
		}
		$doc->Title = $this->AppTitle;
		$this->setDefaultFavicon($doc);				
		if ($clear)
			$doc->body->ClearChilds();
		else
			$doc->body->addBodyBox()->ClearChilds();
		return $doc;
	}

	// private function _loadclass_method($cName, & $func, $listnode, $rlist){	
		// $sf = $this->getDeclaredFileName();	
		// foreach(get_class_methods($cName) as $k=>$v)
		// {
			// $refmethod = new ReflectionMethod($cName, $v);
			// $n = $refmethod->getName();
			// switch($n)
			// {
				// case "__construct":
				// case __FUNCTION__:
					// continue;					
				// default:	
				// $fname = $refmethod->getFileName();
				// if (isset($rlist[$n]))
					// continue;					
				// if ($refmethod->isPublic() && !$refmethod->isStatic() && 
					// (($fname == $sf) ||
					// IGKString::EndWith($refmethod->getName(), "_contract" )))
				// {
					// $func[] = $refmethod->getName();
					// $b->addDiv()->setContent($refmethod->getFileName());
					// $listnode->add("function")
					// ->setAttribute("name",$refmethod->getName())
					// ->setAttribute("available",1);
				// }
				// break;
			// }	
		// }
	// }
	
	private function _getfunclist($news=false, $funcrequest=null){
		return igk_sys_getfunclist($this, $news, $funcrequest);		
	}
	
	///<summary>List Exposed Functions</summary>
	public function functions($n=false){
		if (!igk_server_is_local() && !igk_is_conf_connected() )
		{
			igk_notifyctrl()->addWarningr("warn.noaccessto_1", __FUNCTION__);
			igk_navto($this->getAppUri());
			igk_exit();
		}
	
		$doc = $this->createNewDoc();	
		$doc->Title = R::ngets("title.app_2", "Functions ".igk_getv($this->Configs, IGK_CTRL_CNF_TITLE), $this->App->Configs->website_title);		
		$d = $bodybox = $doc->body->addBodyBox();	
		$d->ClearChilds();		
		$m = $d->addDiv()->addDiv()->addContainer();
		$r = $m->addRow();
		$cl = get_class($this);
		$ref = new ReflectionClass($cl);		
		$sf = $this->getDeclaredFileName();
		$r->addDiv()->setClass("fc_h")->setStyle("font-size:1.4em")->Content = "File : ".igk_io_basepath($sf);
		
		$m = $d->addDiv()->addDiv()->addContainer();
		$r = $m->addRow();
		$func = $this->_getfunclist($n);
		
		usort($func, function($a,$b){
				return strcmp(strtolower($a), strtolower($b));
			});			
			foreach($func as $k){
					$b = $r->addCol("igk-col-12-2 igk-sm-list-item")
					->setStyle("padding-top:8px; padding-bottom:8px")
					->addDiv();
				$b->addA($this->getAppUri($k))
				->setContent($k);
			}
		$bodybox->setStyle("position:relative; color: #eee; margin-bottom:300px;padding-bottom:0px; overflow-y:auto; color:indigo;");
		
		$bodybox->addDiv()->setClass("posfix loc_b loc_r loc_l dispb footer-box igk-fixfitw")
		->setId("fbar")
		->setAttribute("igk-js-fix-loc-scroll-width", "1")
		->setStyle("min-height:80px;background-color:#ddd; z-index: 10; width: auto;");//->Content= "footer";	
	
	
		$bodybox->addDiv()->setClass("no-visibity dispb")
		//->setStyle("height:140px")
		->setAttribute("igk-js-fix-height","#fbar");//->setContent("vikkk");
	
		$b = $bodybox->addActionBar();
		$u = $this->getAppUri("functions/1");
		$b->addButton("btn.init")->setAttribute("value", "init function list")->setAttribute("onclick", "javascript: ns_igk.form.posturi('".$u."'); return false;");
	
		$doc->RenderAJX();
	}


	public function conffunctions($node =null){
			if (!igk_is_conf_connected())
			{
				igk_navto($this->getAppUri());
				igk_exit();
			}
			
			if ($node==null)
			{
				$doc = $this->createNewDoc();
				$doc->Title = "Configure Functions - [".$this->App->Configs->website_domain."]";
				$bbox = $doc->body->addBodyBox();
				$bbox->addIGKAppHeaderBar($this);
				$bbox->addMenuBar();
				$this->conffunctions($bbox);
				$doc->RenderAJX();
				return;
			}
			
			
			$d = $node->addDiv();
			$tab = $d->addTable();
			
			foreach(igk_sys_getall_funclist($this) as $k=>$v)
			{
				$tr = $tab->add("tr");
				$tr->add("td")->addSpace();
				$tr->add("td")->Content = $v->clName;
				
				$i = $tr->add("td")->addInput("meth[]", "checkbox");
				$i["value"] = $v->clName;				
				if ($v->clAvailable){
					$i["checked"] = "checked";
					
				}
			}
		}

	///<summary>shortcut to global view function</summary>
	public function v($view='default', $forceview=false){
		$this->setCurrentView($view, $forceview);	
	}	
}



function igk_html_node_appLoginForm($app, $baduri=null, $goodUri=null){
	$n = igk_createNode("div");
	igk_app_login_form($app, $n, $baduri, $goodUri);
	return $n;
}
function igk_app_load_login_form($app, $node, $fname, $goodUri=null){
	$u = $goodUri;
	if ($u == null){
		$q = igk_getr("q");
		$u = $app->getAppUri();
		if (!empty($q)){
			$u = igk_io_baseUri()."/".base64_decode($q);
		}
	}
	$frm = $node->addAppLoginForm($app, $app->getAppUri($fname), $u);
	
}
//-------------------------------------------------------------
//utility function
//-------------------------------------------------------------
//default login form 
function igk_app_login_form($app, $div, $badUri=null, $goodUri=null){
	$frm = $div->addForm();			
	$frm["action"] = $app->getUri("login");//get uri bypass the checking of exposed functions list
	$frm["class"] = "igk-login-form";		
	$frm["igk-form-validate"] = 1;
	
	igk_notify_sethost($frm->addDiv(), "notify/app/login");	
	
	$cd = $frm->addDiv()->setClass("form-group");
	$cd->addRow()->addCol()->addDiv()->addInput("clLogin", "text")
	->setClass("igk-form-control")
	->setAttribute("placeholder", R::ngets("tip.login"))
	->setAttribute("igk-input-focus", 1)
	->setAttribute("autocomplete",'off') //diseable auto completion
	->setAttribute("autocorrect","off") //diseable auto character keybord 
	->setAttribute("autocapitalize","none")
	;
	
	$row = $cd->addRow();
	$row->addCol()->addDiv()->addInput("clPwd", "password")->setClass("igk-form-control")
	->setAttribute("placeholder", R::ngets("tip.pwd"))
	->setAttribute("autocomplete", 'current-password');
	
	$row = $cd->addRow();
	$row->addCol()->addDiv()->setClass("alignc")
	->addInput("btn_connect", "submit", R::ngets("btn.connect"))->setClass("igk-btn igk-btn-default igk-form-control igk-btn-connect");
	
	$row = $cd->addRow();
	$dv = $row->addCol()->addDiv();
	$dv->Content = igk_get_string_format(<<<EOF
<input type="checkbox" name="remember_me" id="remember_me" value="1" checked="1" /><span>{1}</span>
<span class="separator" >&middot;</span>
<a href="#" >{0}</a>
EOF
, 
R::ngets("lb.q.forgotpwd"),
R::ngets("lb.remember_me")
);

$t = array();
$t["badUri"]=array(
				"type"=>"hidden", "attribs"=>array("value"=>$badUri)
				);
if ($goodUri){
	$t["goodUri"] =array(
				"type"=>"hidden", "attribs"=>array("value"=>$goodUri)
				); 
}
				
	igk_html_buildform($frm, $t);

}
///<summary>get if application is on uri demand</summary>
function igk_app_is_uri_demand($app, $function){
	return (igk_io_currentUri() == $app->getAppUri($function));
}

///<summary>get all application controller</summary>
function igk_get_app_ctrl(){

	$app = IGKApp::getInstance();	
	$v_ruri = igk_io_baseRequestUri();	
	$tab = explode('?',$v_ruri);
	$uri = igk_getv($tab, 0);
	$params = igk_getv( $tab, 1);
	$page = "/".$uri;	
	//primitive actions	
	$actionctrl = igk_getctrl(IGK_SYSACTION_CTRL);	
	if ($actionctrl  && ($e = $actionctrl->matche($page)))
	{
		$n = igk_getv(igk_getquery_args( $e->value), "c");	
		$ctrl = igk_getctrl($n, false);
		if ($ctrl)
		{
			if (igk_reflection_class_extends( $ctrl,  "IGKAppCtrl"))
			{
				return $ctrl;
			}
		}			
	}
	return null;	
}
function igk_app_ctrl_dropped_callback($ctrl, $n){	
	//reset session app on dropped
	$c = IGKAppCtrl::GetApps();
	$c = array();	
}


///<summary>get application authorization key</summary>
function igk_get_app_auth($app, $name){
	return $app->Name.":/".$name;
}
?>