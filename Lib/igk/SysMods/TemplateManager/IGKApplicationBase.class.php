<?php

///<summary>represent a template application base class</summary>
abstract class IGKApplicationBase extends IGKAppCtrl{
	private $m_manifest;
	protected  function onUninstal($context){
		igk_die(__METHOD__." not implement");
	}
	protected function onInstall($context=null){
		igk_die(__METHOD__." not implement");//wln("onInstall not implement");
	}
	///<summary></summary>
	public final  function getManifest(){
		if ($this->m_manifest == null){
			$this->m_manifest = igk_createOBJ();
			$this->m_manifest->clTitle = "";
			
			$f = $this->getDataDir()."/.manifest";
			if (file_exists($f)){
				//load manifest file
				$o = IGKHtmlReader::LoadXmlFile($f);
				igk_wln($o);
				igk_exit();
			}
			
		}
		return $this->m_manifest;
	}
	///<summary>return an array of templates view</summary>
	public function getViews(){
		return igk_io_getfiles($this->getViewDir(), "/\.xtphtml$/");
	}
	public function __toString(){
		return strtolower("IGKTemplateApplication://".$this->getName());
	}
	
	public function View(){
		parent::View();
	}
	public function notify_view(){
		if ($this->isVisible){
			$this->View();
		}
	}
	protected function InitComplete(){
		parent::InitComplete();
		igk_notification_reg_event(IGK_FORCEVIEW_EVENT, array($this, "notify_view"));
	}
	///<summary>override this method to uninstall this package</summary>
	public function uninstall(){
		$this->_unregisterEvents();
	}
	
	//-------------------------------------------------------------------------------------------------
	///template entries folder
	//-------------------------------------------------------------------------------------------------
	public function getDeclaredFileName(){
		return igk_get_reg_class_file(get_class($this));
	}
	public function getDataDir(){
		return igk_io_dir(dirname($this->getDeclaredFileName()).DIRECTORY_SEPARATOR.IGK_DATA_FOLDER);
	}
	public function getDeclaredDir(){
		return igk_io_dir(dirname($this->getDeclaredFileName()));
	}
	public function getScriptDir(){
		return igk_io_dir(dirname($this->getDeclaredFileName()).DIRECTORY_SEPARATOR.IGK_SCRIPT_FOLDER);
	}
	public function getViewDir(){
		return igk_io_dir(dirname($this->getDeclaredFileName()).DIRECTORY_SEPARATOR.IGK_VIEW_FOLDER);
	}
	public function getStylesDir(){
		return igk_io_dir(dirname($this->getDeclaredFileName()).DIRECTORY_SEPARATOR.IGK_STYLE_FOLDER);
	}
}
?>