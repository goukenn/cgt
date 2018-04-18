<?php
//google Talk community type

abstract class IGKGoogleTalkCtrl extends IGKCtrlTypeBase
{
	public function getcanAddChild(){
		return false;
	}
	
	public static function GetAdditionalConfigInfo()
	{
		return array("clLink"=>igk_createAdditionalConfigInfo(array("clRequire"=>1)));
	}
	public static function SetAdditionalConfigInfo(& $t)
	{
		$t["clLink"] = igk_getr("clLink");
	}
	
	public function View()	
	{
		if (!$this->isVisible)
		{		
			igk_html_rm($this->TargetNode);
			return;
		}		
		$t = $this->TargetNode;		
		$t->ClearChilds();		
		$this->addGooglePlus($t);
	}
	public function addGooglePlus($target){
		$lnk = $this->app->Doc->addLink("googleplus:uri");
		$lnk["rel"] = "canonical";
		$lnk["href"] = $this->Configs->clLink;
		$src = $this->app->Doc->addScript("https://apis.google.com/js/plusone.js");
		$gdiv = $target->add("span");
		$gdiv->add("g:plusone")->addNothing();
	}
}



final class IGKHtmlGoogleTalkNodeItem extends IGKHtmlItem
implements IIGKHtmlUriItem
{
	private $m_uri;
	
	public function getUri(){ return $this->m_uri; }
	public function setUri($v) { $this->m_uri = $v; return $this;}
	
	public function View(){
		$this->ClearChilds();		
		$doc = $this->getParentDocument();
		if ($doc){
			$lnk = $doc->addLink("googleplus:uri");
			$lnk["rel"] = "canonical";
			$lnk["href"] = $this->getUri();//"http://www.igkdev.be";//$this->getUri();//Configs->clLink;
			$src = $doc->addScript("https://apis.google.com/js/plusone.js");
		}		
		$gdiv = $this->add("span");
		$gdiv->add("g:plusone")->addNothing();
		
	}
	protected function loadingComplete(){
		parent::loadingComplete();
	}
	public function __construct()
	{	
		parent::__construct("div");
		$this->m_uri = "http://".IGKApp::getInstance()->Configs->website_domain;
	}
}
?>