<?php
//--@
//author:C.A.D. BONDJE DOUE
//version:1.0
//--

///<summary>used to generate file</summary>
class IGKWsdlFile extends IGKObject
{
	private $m_def;
	private $m_message;
	private $m_binding;
	private $m_service;
	private $m_porttype;	
	private $uri;
	private $m_attributes;
	private $m_srv;//main service
	private $m_uri;
	private $m_cservice; //current service
	
	public function getNSPrefix(){ return igk_getv($this->m_attributes,"nsprefix", "igkns");  }
	public function getNSUri(){return igk_getv($this->m_attributes, "nsuri", "http://www.igkdev.com");}
	public function getTargetNS(){return igk_getv($this->m_attributes, "targetns", "http://www.igkdev.com");}
	public function getDocumentation(){return igk_getv($this->m_attributes, "doc", "service documentation");}
	
	public function __construct($name, $uri, $attributes=null){
		$this->m_uri = $uri;
		$this->m_attributes = $attributes;
		$this->m_def = new IGKHtmlItem("definitions");
		$this->m_def["name"]=$name;
		$this->m_def["targetNamespace"]= $this->TargetNS;
		$this->m_def["xmlns"]="http://schemas.xmlsoap.org/wsdl/";
		$this->m_def["xmlns:soap"]="http://schemas.xmlsoap.org/wsdl/soap/";		
		$this->m_def["xmlns:xsd"]="http://www.w3.org/2001/XMLSchema";
		$this->m_def["xmlns:".$this->NSPrefix]= $this->getNSUri();
		
		//message
		$this->m_message = $this->m_def->addChildNodeView("message");
		//prototype
		$this->m_porttype = $this->m_def->addChildNodeView("portType");
		//binding
		$this->m_binding = $this->m_def->addChildNodeView("binding");
		//binding
		$this->m_service = $this->m_def->addChildNodeView("service");
		
		
		
	// $this->addMethod("m1", array("i1"=>"xsd:string"),array("o1"=>"xsd:string"));
	// $srv = $this->addBindingService("m1_bindingsrv", "rpc", "igkns:m1_porttype");
	// $this->addService("demo", "that is the doc", $srv, "http://localhost/webservice/server.php");
		
		
		//$this->m_srv = $this->addBindingService("binding_service");
//		$this->addService($name, $this->Documentation,  $this->m_srv, $this->m_uri);	
		
	}
	public function initService($n, $attrs=null ){
		$this->m_srv = $this->addBindingService($n."_bindingService");	
		$this->addService($n, igk_getv($attrs, "doc"),  $this->m_srv, $this->m_uri);	
	}
	
	public function registerClass($className, $srvName, $attrs=null){
		$cl = is_object($className)? get_class($className) : (class_exists($className)? $className : null);		
		if ($cl == null)
			return;
		$r = new ReflectionClass($cl);
		if ($r->isAbstract())
			return;
			
		$port = $this->m_porttype->AddChild();
		$port["name"] = $cl."_porttype";
			
		if ($this->m_srv == null){
			$this->m_srv = $this->addBindingService($cl."_binding_service");	
			$this->addService($srvName, igk_getv($attrs, "doc"),  $this->m_srv, $this->m_uri);		
		}
		
		$this->m_srv["type"] = $this->getNSPrefix().":".$port["name"];
		
		foreach( get_class_methods($cl) as $k=>$n)
		{
			$m = new ReflectionMethod($cl, $n);
			if ($m->isPublic() && !$m->isStatic() && !$m->isConstructor())
			{
				$i = array();
				foreach($m->getParameters() as $p){
					$i[$p->name] = "xsd:string";
				}
				$o = array();
				$o[$n."_result"] = "xsd:string";
			
				$this->addMethod($n, $i, $o, $port);
				$this->addServiceOperation($this->m_srv, $n);	
							
			}			
		}
	}
	///<summary>register methods </summary>
	///<param name="classname" >class name</param>
	///<param name="srvName" >service name </param>
	///<param name="funclist" >array list of available functions</param>
	public function registerMethod($className, $srvName, $funclist){
		$cl = is_object($className)? get_class($className) : (class_exists($className)? $className : null);		
		if ($cl == null)
			return;
		$r = new ReflectionClass($cl);
		if ($r->isAbstract())
			return;
			
		$port = $this->m_porttype->AddChild();
		$port["name"] = $cl."_porttype";
			
		if ($this->m_srv == null){
			$this->m_srv = $this->addBindingService($cl."_binding_service");	
			$this->addService($srvName, igk_getv($attrs, "doc"),  $this->m_srv, $this->m_uri);		
		}
		
		$this->m_srv["type"] = $this->getNSPrefix().":".$port["name"];
		$tlist = "i4|f4|b";
		$v_match = "/_(?P<type>(".$tlist."))$/";
		foreach( $funclist as $n)
		{
			$m = new ReflectionMethod($cl, $n);
			if ($m->isPublic() && !$m->isStatic() && !$m->isConstructor())
			{
				$i = array();
				foreach($m->getParameters() as $p){
					$v_rt = "xsd:string"; //set type
					if (preg_match_all($v_match,$p->name, $tab)){
						$v_rt = $this->getXSDType($tab["type"][0]);
					}
					$i[$p->name] = $v_rt;
				}
				$o = array();
				$o[$n."_result"] = "xsd:string";
			
				$this->addMethod($n, $i, $o, $port);
				$this->addServiceOperation($this->m_srv, $n);								
			}			
		}
	}
	protected final function getXSDType($t){
		$v_rt = "xsd:string";
		$args = array(
		"i1"=>"xsd:boolean",
		"i4"=>"xsd:int",
		"f4"=>"xsd:float",
		"sb"=>"xsd:byte",
		"b"=>"xsd:unsignedByte",
		"ub"=>"xsd:unsignedByte",
		"d1"=>"xsd:date",
		"t1"=>"xsd:time",
		"dt"=>"xsd:dateTime"
		);
		if (isset($args[$t]))
			$v_rt = $args[$t];
	
		return $v_rt;
	}
	//name: operation name
	protected function addServiceOperation($srv, $name, $type="encoded", $urn="sample:demo")
	{
		$op = $srv->addNode("operation");
		$op["name"] = $name;
		$op->addNode("soap:operation")->setAttribute("soapAction",$name);
		$input = $op->addXmlNode("input");
		$output = $op->addXmlNode("output");
		$input->Content =<<<EOF
            <soap:body
               encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
               namespace="urn:{$urn}"
               use="{$type}"/>         
EOF;
		
		// igk_wln("input...");
		// igk_wln(get_class($input));
		// $input->RenderAJX();
		// exit;
         $output->Content =<<<EOF
            <soap:body
               encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
               namespace="urn:{$urn}"
               use="{$type}"/>
EOF;
		return $op;
	}
	public function addBindingService($name, $style="rpc", $porttype=null, $enctype='encoded'){
		$c = $this->m_binding->AddChild();
		$c["name"] = $name;
		$c["type"] = is_object($porttype)? 
		"igkns:".$porttype["name"] : (
		is_string($porttype)? $porttype :null);
		
		$c->addNode("soap:binding")
		->setAttribute("style",$style)
		->setAttribute("transport","http://schemas.xmlsoap.org/soap/http");
		
		if($porttype){
			$this->addServiceOperation($c,"m1",  $enctype, $porttype);
		}
		
	
		return $c;
	}
	public function addService($srvname, $doc, $srv,$loc){
		
		$d = $this->m_service->AddChild();
		$d["name"] = $srvname;
		$d->addNode("documentation")->Content = $doc;
		$p = $d->addNode("port");
		$p["name"] = "port";
		$p["binding"] = is_object($srv)? "igkns:".$srv["name"] : $srv;
		$p->addNode("soap:address")->setAttribute("location", $loc);//->Content = "";
		$this->m_cservice = $d;
		return $d;
	}
	public function addMethod($n, $input, $output=null, $porttype=null){
		$m = $this->m_message->AddChild();
		$m["name"] = $n."Request";
		if ($input)
		foreach($input as $k=>$v){
			$p = $m->addNode("part");
			$p["name"]=$k;
			$p["type"] = $v;
		}
		$m = $this->m_message->AddChild();
		$m["name"] = $n."Response";
		
		if ($output)
		foreach($output as $k=>$v){
			$p = $m->addNode("part");
			$p["name"]=$k;
			$p["type"] = $v;
		}
		if ($porttype ==null)
		{
			$p = $this->m_porttype->AddChild();
			$p["name"] = $n."_porttype";
		}
		else 
			$p = $porttype;
			
		$op = $p->addNode("operation");
		$op["name"] = $n;
		$op->addNode("input")->setAttribute("message","igkns:".$n."Request");
		$op->addNode("output")->setAttribute("message","igkns:".$n."Response");
	}
	public function Save($f){
		igk_set_env(IGK_ENV_NO_TRACE_KEY,1);
		IGKIO::WriteToFileAsUtf8WBOM($f, igk_html_render_xml($this->m_def), true);
		igk_set_env(IGK_ENV_NO_TRACE_KEY,null);
	}
}



?>