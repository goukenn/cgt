<?php
/*
file: class.PageZoneCtrl.php
author : cad BONDJE DOUE 
copyright : IGKDEV all right reserved 2008-2014
filecreated :  09-05-2014
description: this a page zone that have a view zone node that can't host child
*/
//controller code class declaration
//file is a part of the controller tab list
abstract class IGKPageZoneCtrl extends IGKCtrlTypeBase
{
	private $m_viewZone;
	public function getName(){return get_class($this);}
	public function getViewZone(){return $this->m_viewZone;}
	protected function InitComplete(){
		parent::InitComplete();		
		//please enter your controller declaration complete here
		
	}
	public static function GetAdditionalConfigInfo()
	{
		return null;
	}
	//@@@ init target node
	protected function initTargetNode(){
		$node =  parent::initTargetNode();
		$node["class"]="alignc alignt dispb";
		$this->m_viewZone = $node->addDiv();
		$this->m_viewZone["class"]="page_zone";
		return $node;
	}
	public function getCanAddChild(){
		return true;
	}
	public function View()
	{
		parent::View();
	}
	protected function _showViewFile()
	{
		parent::_showViewFile();
	}
	protected function _showChild($targetnode=null){	
	
		//maintain the change the view
		$t = $targetnode ? $targetnode: $this->TargetNode;
		igk_html_add($this->m_viewZone, $t, 1000);
		if ($this->hasChild){			
			foreach($this->getChilds() as $k=>$v)
			{			
				if ($v->isVisible)
				{
					igk_html_add($v->TargetNode, $this->m_viewZone);
					$v->View();
				}
				else {
					igk_html_rm($v->TargetNode);
				}
			}
		}
	}

}
?>