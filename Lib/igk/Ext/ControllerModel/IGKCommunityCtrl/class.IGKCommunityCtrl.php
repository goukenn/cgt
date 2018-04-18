<?php
//controller code class declaration
//file is a part of the controller tab list
///<summary>used to manage comomunity site</summary>
abstract class IGKCommunityCtrl extends IGKCtrlTypeBase {
	public function getName(){return get_class($this);}
	protected function InitComplete(){
		parent::InitComplete();
		igk_db_reg_sys_ctrl("community", $this);
		//only one instance is allowed.
		igk_notification_reg_event("sys://events/community", "igk_community_init_node_callback");
	}
	public function dropController(){
		parent::dropController();
		igk_notification_unreg_event("sys://events/community", "igk_community_init_node_callback");
		igk_db_unreg_sys_ctrl("community");
	}
	//@@@ init target node
	protected function initTargetNode(){
		$node =  parent::initTargetNode();
		return $node;
	}	
	public function getCanAddChild(){
		return false;
	}
	public static function CanDbEditDataType(){
		return false;
	}
	public static function CanDbChangeDataSchema(){
		return false;
	}
	
	public function getUseDataSchema(){
		return 0;
	}
		
	public function getCanEditDataTableInfo(){
		return false;
	}
	public function getDataTableName(){
		return igk_db_get_table_name("%prefix%_site_community");
	}
	public function getDataTableInfo()
	{
		return array(
			new IGKdbColumnInfo(array(IGK_FD_NAME=>IGK_FD_ID, IGK_FD_TYPE=>"Int","clAutoIncrement"=>true,IGK_FD_TYPELEN=>10, "clIsUnique"=>true, "clIsPrimary"=>true)),
			new IGKdbColumnInfo(array(IGK_FD_NAME=>"clCommunity_Id", IGK_FD_TYPE=>"Int", "clIsUnique"=>true, "clLinkType"=>"tbigk_community"  )),
			new IGKdbColumnInfo(array(IGK_FD_NAME=>"clLink", IGK_FD_TYPE=>"Text", "clDescription"=>"Url to community")),
			new IGKdbColumnInfo(array(IGK_FD_NAME=>"clImageKey", IGK_FD_TYPE=>"VarChar", IGK_FD_TYPELEN=>30)),
			new IGKdbColumnInfo(array(IGK_FD_NAME=>"clAvailable", IGK_FD_TYPE=>"Int", "clNotNull"=>1))
			);
	}
	public function initDb(){
		igk_set_env("sys://db/constraint_key", "igk_com");		
		if (igk_is_conf_connected())		
			$this->initDbFromFunctions();
		
	}
	protected function getConfigFile()
	{
		$s = dirname(__FILE__)."/".IGK_DATA_FOLDER."/".IGK_CTRL_CONF_FILE;	
		return igk_io_dir($s);		
	}
	protected function getDBConfigFile()
	{
		return igk_io_dir(dirname(__FILE__)."/".IGK_DATA_FOLDER."/".IGK_CTRL_DBCONF_FILE);
	}
	//----------------------------------------
	//Please Enter your code declaration here
	//----------------------------------------
	//@@@ parent view control
	public function View(){
			return;
			// igk_wln(__METHOD__." is visible?" .$this->IsVisible);
		
			// $this->TargetNode->ClearChilds();
			// extract($this->getSystemVars());
			
			// $ul = $t->addDiv()->add("ul");			
			// $ul["class"]= "igk-community-list";
			// $e = $this->getDbEntries();
			// if ($e)
			// {
				// foreach($e->Rows as $k=>$v)
				// {
					// if (!$v || !$v->clAvailable)
						// continue;
					// $src = IGK_STR_EMPTY;
					// $src .= igk_getv($v, "clImageKey")!=null? $v->clImageKey : "com_".$v->clName;
					// $b = $ul->add("li")->add("a", array(
					// "href"=>$v->clLink, 
					// "target"=>"_blank",
					// "class"=>"igk-community-list"));				
					// $b->add("div", array("class"=>"igk-community-box igk-com-".$v->clName));		
					// igk_css_regclass("com_".$v->clName, "[res:".$src."]" );
				// }
			// }		
	}
	public function loadCommunityNode($n){
		$e = $this->getDbEntries();
		$ul = $n->add("ul");
		if ($e && ($e->RowCount>0)){
			$coms = igk_db_select($this, "tbigk_community");
			$n = "";
				// igk_wln($coms->Rows);
				// exit;
				foreach($e->Rows as $k=>$v)
				{
							if (!$v || !$v->clAvailable)
								continue;
							if (!isset($coms->Rows[$v->clCommunity_Id]))
								continue;
							$rn = $coms->Rows[$v->clCommunity_Id];
							$n = $rn->clName;
							// igk_ilog("load ".$n);
							$src = IGK_STR_EMPTY;
							$src .= igk_getv($v, "clImageKey")!=null? $v->clImageKey : "com_".$n;
							$b = $ul->add("li")->setClass("igk-community-i")->add("a", array(
							"href"=>$v->clLink, 
							"target"=>"_blank"));				
							$b->add("div", array("class"=>"igk-community-box igk-com-".$n))->addSvgSymbol($n);		
							//igk_css_regclass("com_".$v->clName, "[res:".$src."]" );
				}
			}else{
				$ul->addWebMasterNode()->addLi()->add("span")->Content = "No Community";
			}
	}
	
}


/*

final class IGKHtmlCommunityNodeItem extends IGKHtmlCtrlComponentNodeItemBase
{
	private $m_article;	
	private $m_init;
	private $m_comtable;
	
	public function getTable(){ return $this->m_comtable; }
	public function setTable($v){  $this->m_comtable = $v; return $this; }
	
	public function __construct(){
		
		parent::__construct("div");
		$this->m_init =false;
		$this["class"] = "igk-community-owner";
		$this["igk-js-init-uri"] =  $this->getController()->getUri("loadCommunity", $this);
	}
	public function getIsVisible(){
		$c = igk_getctrl($this->getCtrl(), false);
		return true; //parent::getIsVisible() && ($c !=null);
	}
	public function getArticle(){return $this->m_article; }
	
	public function setArticle($value){$this->m_article = $value; return $this; }
	
	public function loadingComplete(){
		parent::loadingComplete();
			$this->View();
	
	}
	public function View(){
		if ($this->m_init){
			$c = igk_getctrl($this->getCtrl(), false);		
			$a = $this->getArticle();			
			$this->ClearChilds();
			if ($c && $a){
				igk_html_binddata($c, $this,  $a, $this->getParam("db"));
			}
		}
	}
	public function loadCommunity(){
		$com = igk_getctrl($this->getCtrl());
		$this["igk-js-init-uri"] =  null;
		if ($com){
			if (method_exists($com, "getCommunities")){			
				$t = $com->getCommunities();
			}
			else {
				$t = igk_db_select_all($com);			
			}
			$this->m_init = true;
			$this->setParam("db", $t);
			$this->View();
			$this->RenderAJX();
		}
		else{
			igk_wln("<div class='igk-danger'> /!\ community controller not found.</div>");
		}
		exit;
	}	
}

*/
?>