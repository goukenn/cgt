<?php

final class IGKGoogleConfigurationSetting extends IGKConfigCtrlBase{
	const API_KEY = "google.ApiKey";
	
	public function getConfigPage(){return "google.sdk";}
	public function getConfigGroup(){return "google";}
	public function getName(){return "com.igkdev.googleapi"; }
	public function initConfigMenu(){
		return array(
			(new IGKMenuItem($this->ConfigPage, $this->ConfigPage, $this->getUri("showConfig")))->setGroup($this->ConfigGroup),
		);
	}
	protected function getConfigFile()
	{
		return igk_io_dir($this->getDataDir()."/google.".IGK_CTRL_CONF_FILE);
	}
	public function showConfig(){
		parent::showConfig();
		
		$cnf = $this->ConfigNode;
		$cnf->addDiv()->addSectionTitle(5)->Content = "Google Settings";
		$frm = $cnf->addDiv()->addForm();
		$frm["action"] = $this->getUri("storeApiKey");
		$frm->add("label")->Content = "DEV API KEY";
		$frm->addInput("clApiKey", "text", igk_google_apikey()
		// igk_ctrl_get_setting($this, self::API_KEY)
		)->setAttribute("placeholder", "google api key");
	}
	public function storeApiKey(){
		if (!igk_is_conf_connected()){
			return;
		}
		$key = igk_getr("clApiKey");
		$k = self::API_KEY;
		$this->Configs->$k = $key;
		$this->storeConfigSettings();
	}
	
	function initComplete(){
		parent::initComplete();		
	}
}
function igk_google_apikey(){
	$k = IGKGoogleConfigurationSetting::API_KEY;
	$ctrl = igk_getctrl("com.igkdev.googleapi");
	if ($ctrl)
		return igk_ctrl_get_setting($ctrl,$k);
	return null;
} 
?>