<?php
//controller code class declaration
//file is a part of the controller tab list
abstract class IGKProductViewCtrl extends IGKCtrlTypeBase
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
	public function View()
	{
		extract($this->getSystemVars());
		$t->ClearChilds();
		$frm = $t->addDiv()->addForm();
		$frm["action"]="";
		$lb = $frm->add("label");
		$lb["for"] = "";
		$lb->Content = "";
		$sl = $frm->add("select");
		$tb = $igkproducttype->getDbEntries();
		if ($tb)
		foreach($tb->Rows as $k=>$v)
		{
			$sl->add("options")->Content = $v->clName;
		}
		
		$t->addDiv();
		
		$div = $t->addDiv();
		
		$div->Content = "DD";
		
	}
	protected function getDefaultDataTableInfo()
	{
		return array(
			new IGKdbColumnInfo(array(IGK_FD_NAME=>"clId", IGK_FD_TYPE=>"Int", "AutoIncrement"=>true)),
			new IGKdbColumnInfo(array(IGK_FD_NAME=>IGK_FD_NAME, IGK_FD_TYPE=>"VARCHAR", IGK_FD_TYPELEN=>255, "clIsUnique"=>true, "clIsPrimary"=>true))		
		);
	}
	public function getMoreInfo()
	{
	}

}
?>