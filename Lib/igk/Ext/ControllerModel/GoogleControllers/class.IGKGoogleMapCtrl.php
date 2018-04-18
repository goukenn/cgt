<?php




//<summary>used to add google map controller model on the web site</summary>
abstract class IGKGoogleMapCtrl extends IGKCtrlTypeBase
{
	public function getcanAddChild(){
		return false;
	}
	public static function GetAdditionalConfigInfo()
	{
		return array("clGoogleMapUrl",igk_createAdditionalConfigInfo(array("clRequire"=>1)));
	}
	public static function SetAdditionalConfigInfo(& $t)
	{
		$t["clGoogleMapUrl"] = igk_getr("clGoogleMapUrl");
	}
	public function View()
	{
		$t = $this->TargetNode;
		$t->ClearChilds();
		$lnk = igk_getv($this->Configs, "clGoogleMapUrl", "http://www.google.fr");
		$s = <<<EOF
<iframe class="noborder googlemap_map" src="{$lnk}"></iframe>
EOF;
	$t->Load($s);
		
	}
}

final class IGKHtmlGoogleMapNodeItem extends IGKHtmlItem{	
	private $m_type;
	private $m_query;
	private $m_key;
	private $m_location;
		
	public function getLocation(){ return $this->m_location; }
	public function setLocation($v) { $this->m_location = $v; return $this;}
	
	public function getType(){ return $this->m_type; }
	public function setType($v) { $this->m_type = $v; return $this;}
	
	public function getKey(){ return $this->m_key; }
	public function setKey($v) { $this->m_key = $v; return $this;}
	
	public function getQuery(){ return $this->m_query; }
	public function setQuery($v) { $this->m_query = $v; return $this;}
	
	public function initView(){
		$this->ClearChilds();
		$key = $this->getKey();//"AIzaSyDDOfGXjfMVZOFoAESJ3ON0bZyiJpnXBqc";
		$t = $this->getType();
		$q = $this->Location;
		//$lnk ="https://www.google.com/maps/embed/v1/{$t}?key={$key}&q=City+Hall,New+York,NY";
		$lnk ="https://www.google.com/maps/embed/v1/{$t}?key={$key}&q={$q}";
		//https://www.google.be/maps/place/Place+de+la+Grande+P%C3%AAcherie,+7000+Mons/@50.4481963,3.9503642,20z/data=!4m7!1m4!3m3!1s0x47c250005aa0b62f:0x570b81b551f46ea2!2sPlace+de+la+Grande+P%C3%AAcherie,+7000+Mons!3b1!3m1!1s0x47c250005aa0b62f:0x570b81b551f46ea2";
		//"http://www.google.com";// $this->getUri();
		
		if (igk_sys_env_production() && $lnk){		
		$s = <<<EOF
<iframe class="no-border1 googlemap_map fitw" src="{$lnk}" frameborder="0" ></iframe>
EOF;
			$this->Load($s);
			
		}
	}
	
	protected function __acceptRender($opt=null){
		$this->initView();
		return parent::__AcceptRender($opt);
	}
	
	public function __construct(){	
		parent::__construct("div");
		//my keys 
		$this["class"]="igk-winui-google-map";
		$this->m_type ="place";
		$this->m_key ="AIzaSyDDOfGXjfMVZOFoAESJ3ON0bZyiJpnXBqc";
		//$this->m_query = $this->Location; //"Place+de+la+Grande+Pêcherie+19,+7000+Mons,+Belgique";
	}
}

?>