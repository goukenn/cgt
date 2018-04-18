<?php

//author: C.A.D. BONDJE DOUE
//Description: Use to add extra module to system. that module include function declared on .module.pinc file with the $reg array
//exemple to registrer extra function to module: 
//$reg["data"]= function(){}
//all functions are callable that will be call by system or a module consumer.


///<summary>represent application module class </summary>
final class IGKAppModule extends IGKControllerBase{
	private $m_dir;
	private $m_src;
	private $m_fclist;
	private $m_listener;
	private function bindError($msg){
		$this->setParam(__METHOD__, $msg);
	}
	public function getName(){
		return strtolower(str_replace("/",".", igk_html_uri(substr($this->m_dir, strlen(igk_get_module_dir())))));
	}
	///<summary>get the inline calling function</summary>
	public function getCaller(){
		return $this->m_caller;
	}
	public function getCallee(){
		return igk_peek_env(__CLASS__."/callee"); //$c = igk_count($this->m_caller))>0 ? $this->m_caller[0] : "not : ".$c;
	}
	public function getListener(){
		return $this->m_listener ?? igk_ctrl_current_view_ctrl();
	}
	public function setListener($v){
		$this->m_listener = $v;
	}
	public function getDeclaredDir(){
		return $this->m_dir;
	}
	public function getDeclaredFileName(){
		return realpath($this->getDeclaredDir()."/.module.pinc");
	}
	public function __construct($dir){
		parent::__construct();
		$this->m_dir = IGKIO::GetDir($dir);
		$this->mm_fclist = array();		
		
		$c = realpath($dir."/.module.pinc");
		if (file_exists($c))
		$this->_init($c);
		
	}
	
	public function getUri($c=null){
		return $this->getAppUri($c);
	}
	public function getAppUri($c=null){
		$q = "";
		if ($this->Listener)
			$q="ctrl=".$this->Listener->Name;
		
		$u = "n=".$this->Name.($q?"&".$q:"")."".($c ? "&q=".$c:"");
		$s = base64_encode($u); 
		return igk_getctrl(IGK_SESSION_CTRL)->getUri("invmodule&q=".$s);
	}
	private function _init($c=null){	
		$s = igk_io_read_allfile($c ?? $this->m_dir."/.module.pinc");
		$reg = function($name, $callback){
			$this->reg_function($name, $callback);
		};	
		eval("?>".$s);
		$this->m_src = $s;
	}

	function __sleep(){
		$this->m_fclist = array();
		$this->m_src =null;
		return array("m_dir");
	}
	function __wakeup(){		
		$this->_init();//$this->m_dir);
	}
	function __call($n,$args){		
		$fc = igk_getv($this->m_fclist,$n);
		if ($fc){
			igk_push_env(__CLASS__."/callee", $n);			
			$o =  call_user_func_array($fc, $args);		
			$dc = igk_pop_env(__CLASS__."/callee");
			return $o;
		}
		igk_die("/!\\ function {$n} not define");
	}
	public function methodExists($n){
		return isset($this->m_fclist[$n]);
	}
	protected function reg_function($n,$fc){		
		$this->m_fclist[$n]=$fc;
	}
	
	public function setParam($n, $v){
		$l = $this->Listener;
		if ($l){
			$l->setParam($this->Name."/{$n}", $v);
		}
	}
	public function & getParam($n, $def=null, $register=false){
		$l = $this->Listener;
		$h = null;
		if ($l){
			$h = $l->getParam($n, $def, $register);
		}
		return $h;
	}
}

?>