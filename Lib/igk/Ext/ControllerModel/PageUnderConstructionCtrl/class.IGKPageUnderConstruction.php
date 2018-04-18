<?php
//controller code class declaration
//file is a part of the controller tab list
/*abstract class PageUnderConstructionCtrl extends IGKCtrlTypeBase
{
	public function getName(){return get_class($this);}
	protected function InitComplete(){
		parent::InitComplete();		
		//please enter your controller declaration complete here
		
	}
	public static function GetAdditionnalConfigInfo()
	{
		return null;
	}
	//@@@ init target node
	protected function initTargetNode(){
		$node =  parent::initTargetNode();
		return $node;
	}
	public function getCanAddChild(){
		return false;
	}
	//----------------------------------------
	//Please Enter your code declaration here
	//----------------------------------------
	//@@@ parent view control
	public function View(){
		extract($this->getSystemVars());
		$t->ClearChilds();
		//init default view
		$t["class"]="dispb alignc posr overflow_none fith fitw";
		$t["style"]="";
		$t  = $t->addDiv();
		$t["class"]="under_construction_box dispib alignl";
		$t["style"]="width:1024px; max-width:1024px; margin-top:32px; margin-bottom:32px; ";
		
		$d = $t->addDiv();//header
		$d["class"]="under_construction_header";
		
		$d = $t->addDiv();//body
		$d["class"]="under_construction_body";
		$d->addForm(array(
		"class"=>"posab fitw alignt", 
		"style"=>"top:50%; background-color:#30B5FF; height:4px; margin-top:-2px;"
		))->addDiv(array("class"=>"info"))->Content = R::ngets("msg.pageunderconstruction_1", $this->app->Configs->website_title);
		
		
		$m = $d->addDiv(array("class"=>"timer_zone"));
		$m->addScript()->Content= <<<EOF
igk.system.createNS("igk.pageunderconstruction", {
init : function(q){
var m_timeout = null;
function _date(n)
{
	if (n<10)
		return "0"+n;
	return n;
}
function __updateTime(){
	var m_date = new Date();
	q.innerHTML = m_date.getHours()+":"+_date(m_date.getMinutes())+":"+_date(m_date.getSeconds());
	setTimeout(__updateTime, 500);	
}
setTimeout(__updateTime, 500);
}
});
igk.pageunderconstruction.init(igk.getParentScript());
EOF;
		$m->Content =IGK_HTML_SPACE;
		
		//footer
		//--------------------------------------------------------------------------------------------------------------
		$d = $t->addDiv();//footer
		$d["class"]="under_construction_footer";
		$ul = $d->add("ul");
		
		//partenaires list
		$t_p = $this->getPartenaires();
		
		foreach($t_p as $k=>$v)
		{
			$ul->add("li")->add("a", array("href"=>$this->getUri("open&n=".$k)))->addResImg("partenaire_".$k);
		}
		igk_css_regclass(".under_construction_box", "box-shadow: 0px 0px 2px black; background-color:#CACACA;");		
		igk_css_regclass(".under_construction_box .under_construction_header", "min-height:130px;background-color:#30B6FE;");
		igk_css_regclass(".under_construction_box .under_construction_body", "min-height:330px;");
		igk_css_regclass(".under_construction_box .info", "position : absolute; top:50%; margin-top:-1.7em; display:inline-block; text-align:right; font-size: 3.1em; text-shadow: 1px 1px 2px black; min-width:300px; color:white; vertical-align: middle;");		
		igk_css_regclass(".under_construction_box .under_construction_footer", "min-height:48px; background-color:#EAEAEA;");
		igk_css_regclass(".under_construction_box .under_construction_footer ul", "padding:16px;");
		igk_css_regclass(".under_construction_box .under_construction_footer li", "{sys:dispib} width:120px; height:32px;");
		igk_css_regclass(".under_construction_box .timer_zone", "color:#EAEAEA; font-size:5em; position:absolute; right:0px; bottom:-20px; vertical-align:bottom");
		
		
	}
	function getPartenaires()
	{
		$ctrl = igk_get_regctrl("partners");
		if ($ctrl!=null){
			return $ctrl->getPartnerList();
		}
		///parter list
		return array(
		"igkdev"=>"http://www.igkdev.com", 
		"molomolo"=>"http://www.molomolo.net",
		"kms"=>"http://www.kmultiservices.be",
		"gsi"=>"http://www.gsi.be",
		"togotech"=>"http://www.togotechsservices.be",
		"underground"=>"http://www.underground.cm"
		//"soleilfrance"=>"http://www.soleilfrance.fr"
		);
	}
	function open()
	{
		$n = igk_getr("n");
		header("Location: ".igk_getv($this->getPartenaires(), $n));
		exit;
	}
}*/

?>