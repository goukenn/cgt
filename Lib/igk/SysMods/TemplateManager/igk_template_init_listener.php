<?php
class igk_template_init_listener implements IIGKControllerInitListener{
	private $m_zip;
	public function __construct($zip){
		$this->m_zip = $zip;
	}
	public function addDir($dir){
		$this->m_zip->addEmptyDir("src/".$dir);
	}
	public function addSource($name, $content){
		$this->m_zip->addFromString("src/".$name, $content);
	} 
}
?>