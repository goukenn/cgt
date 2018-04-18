<?php
define("IGK_API_CTRL", "API");
define("IGK_API_VERSION", "1.0.0.0");
define("IGK_API_URI", "^/api/v2");

//will be used for api use
final class IGKApiFunctionCtrl extends IGKAppCtrl
{
	
	public $message =  array();
	
	public function getName(){return IGK_API_CTRL;}
	public function getVersion(){return IGK_API_VERSION; }
	public function getIsVisible(){ return false;}	
	public function getIsSystemController(){ return true; }	
	
	public function IsFunctionExposed( $function )
	{
		return true;
	}
	public function getRegUriAction(){
		return IGK_API_URI.IGK_REG_ACTION_METH;
	}
	public function request()
	{
	/*
	how to used exemple : http://localhost/IGKDEV/?c=API&f=request&u=admin&pwd=admin&q=P2M9bGFuZyZmPWdldGxhbmdrZXlzJmZvcm1hdD14bWw=
	
	*/
		$u = igk_getr("u");
		$pwd = igk_getr("pwd");
		$this->ConfigCtrl->logout(false, true);
		if (!$this->ConfigCtrl->IsConnected)
		{
			$this->ConfigCtrl->connect($u, $pwd, false);
		}
		
		if ($this->ConfigCtrl->IsConnected)
		{
			session_start();
			
			$q = base64_decode(igk_getr("q")); 
			igk_resetr();
			igk_loadr($q);
			$node->add("ExecutionResponse")->Content =  $this->App->ControllerManager->InvokeFunctionUri($q);
			$this->ConfigCtrl->logout(false, true);			
		}
		else{
			igk_debug("connection failed ");
		}		
		exit;
	}

	public function beginRequest(){
		$u = igk_getr("u");
		$pwd = igk_getr("pwd");
		//$this->ConfigCtrl->logout(false, false);
		if (!$this->ConfigCtrl->IsConnected)
		{
			$this->ConfigCtrl->connect($u, $pwd, false);
		}
		
		$node =  IGKHtmlItem::CreateWebNode("APIResponse");
		if ($this->ConfigCtrl->IsConnected)
		{
			//session_start();			
			$node->add("status")->Content = "OK";
			$this->setParam("api:u", $u);
			$this->setParam("api:pwd", $pwd);
			$node->add("SessionId")->Content = session_id();
			igk_show_prev(getallheaders());
		}
		else{
			igk_debug("connection failed ");			
			$node->add("status")->Content = "NOK";
			$node->add("message")->Content = $this->message[0];
		}	
		$node->renderAJX();		
		exit;
	}
	public function sendRequest()
	{
		
		
		$node =  IGKHtmlItem::CreateWebNode("APIResponse");
		$q = base64_decode(igk_getr("q")); 		
		// $node->add("user")->Content = $this->getParam("api:u");
		// $node->add("pwd")->Content = $this->getParam("api:pwd");
		$node->add("Connected")->Content = igk_parsebool($this->ConfigCtrl->IsConnected);		
		$node->add("Request")->Content = $q;
		if ($q){
			igk_resetr();
			igk_loadr($q);
			$node->add("ExecutionResponse")->Content = $this->App->ControllerManager->InvokeFunctionUri($q);
		}
		$node->renderAJX();		
		exit;
	}
	public function endRequest(){
		//igk_show_prev(getallheaders());
		$node =  IGKHtmlItem::CreateWebNode("APIResponse");
		if ($this->ConfigCtrl->IsConnected)
		{
			$this->ConfigCtrl->logout(false, true);				
			$node->Content = "OK";		
		}
		$node->renderAJX();
		exit;
	}
	
	
	public function datadb($cmd=null){
		$args = array_slice(func_get_args(), 1);
		$_data= null;
		$_api = $this;
		$_data = array(
		"syncto"=>function($cmd,$args) use($_api){
			// igk_wln($args);
			$rep = igk_createNode("response");
			$error = false;
			$ctrl = igk_getctrl(igk_getv($args, 1));
			$u = igk_get_user_bylogin(igk_getv($args, 2));
			$srv = ($srv = igk_getv($args, 0)) ? $srv : igk_getr("clServer") ;
			
			if (!$ctrl || !$u || empty($srv) 
				//|| igk_srv_is_local($srv)
				){
				$error = true;
				$rep->addNode("Status")->Content = -1;
				$rep->addNode("message")->Content = "Ctrl, Server or User is not found";
			}
			else{
				IGKOb::Start();		
				$this->datadb("syncdata", $ctrl->Name, $u->clLogin);//mysql(igk_post_uri("http://192.168.1.52/igkdev/api/v2/datadb/loadsyncdata",				
				$c = IGKOB::Content();
				IGKOb::Clear();
				
				igk_wl($c);
				exit;
				
					$g = igk_post_uri($srv."/api/v2/datadb/loadsyncdata", array(
					"data"=>$c, 
					"login"=>$u->clLogin,
					"ctrl"=>$ctrl->Name)); 
					igk_wln($g);  
					
					
					// exit;
			
			}
			if (!$error){
				$rep->addNode("Status")->Content = 0;
			}
			igk_wln("DEBUGGER VIEW ::>>>>");
			igk_debuggerview()->RenderAJX();
			$rep->RenderAJX();
			return !$error;
		},
			"syncdata"=>function($cmd,$args) use($_api){//return sync data of a user in a spécific controller
					//2 agrs requires
					//0 : ctrl name
					//1 : user id
					//igk_wln($args);
					$sync = igk_createNode("igk-sync");
					$ctrl = igk_getctrl(igk_getv($args, 0));
					$uid = igk_get_user_bylogin(igk_getv($args, 1));
					if ($ctrl && $uid){
						$_api->setParam("syncdata:info", array());
						$u = $uid;
						$tb = igk_db_get_ctrl_tables($ctrl);
						// sort($tb);
						$apt = igk_get_data_adapter($ctrl->getDataAdapterName());
						$sync["Controller"] = $ctrl->Name;
						$sync["namespace"] = "igk://".$ctrl->Configs->Namespace;
						//igk_wln($apt);
						if ($apt->connect()){
							$tables = (object)array("list"=>array());
							 $entries = $sync->addNode("Entries");
							foreach($tb as $k=>$v_tablen){
								if (!isset($tables->list[$v_tablen])){
									$rep = $sync->addNode("DataDefinition")->setAttributes(array(
										"TableName"=>$v_tablen
									));
									$_api->datadb("get_sync_definition", $rep, $v_tablen, $u, $apt, $ctrl->Db,$entries);									
								}
					
					}
					
						$_api->setParam("syncdata:info", null);
							$apt->close();
						}
					}else{
						igk_wln("/!\\ Args don't match or user not found");
						igk_exit();
					}
				$sync->RenderXML();
				return;
				
				
			},
			"backupdb"=>function($cmd,$args){
				if (!igk_is_conf_connected()){
					igk_wln("/!\\ not allowed");
					exit;
				}
				
				$n = igk_getv($args, 0);
				$ctrl = igk_getctrl($n);
				// igk_wln($args);
				// igk_wln($args);
				if ($ctrl){
					// igk_wln("backup controller for : ".$ctrl->Name);
					// igk_wln($ctrl->getDataAdapterName());
					$tb = igk_db_get_ctrl_tables($ctrl);
					$schema = igk_html_node_DataSchema();
					//$igk_api_mysql_get_table_definition = igk_getctrl("api")->mysql("get_table_definition");
							// $apt = igk_get_data_adapter($ctrl->getDataAdapterName());
					$apt = igk_get_data_adapter($ctrl->getDataAdapterName());
					//igk_wln($apt);
					if ($apt->connect()){
						 $entries = $schema->addNode("Entries");
						// igk_wln("ok");
						foreach($tb as $k=>$v){
							$rep = $schema->addNode("DataDefinition")->setAttributes(array(
								"TableName"=>$v
							));
							igk_getctrl(IGK_API_CTRL)->mysql("get_table_definition", $rep, $v, $apt, null, $entries);
							
						
						}
						$apt->close();
					}
					
					$schema->RenderXML();
					
					return $schema;				
					
				}else{
					igk_wln("No Ctrl {$n} found ");
				}
				
			},
			"loadsyncdata"=>function($cmd, $args)use($_api){
				//igk_wln("sync data ....");
				igk_debuggerview()->ClearChilds();
				$rep = igk_createNode("reponse");
				$c = igk_getr("data");
				$login = igk_getr("login");
				$u = igk_get_user_bylogin($login);
				$ctrl = igk_getctrl(igk_getr("ctrl")); //register a component ctrl 
				if (!$u || !$ctrl){
					$rep->addNode("Status")->Content = -1;
					$rep->addNode("Error")->Content= "user not found or ctrl with that name or namespace not found";
					$rep->RenderXML();
					return;
				}
				$error = false;
				//1. replace all login occurence the the id of the server login
		$c = preg_replace_callback("#\+@id:/{$u->clLogin}#", function($m) use($u){
			return $u->clId;
		},$c);
		
		//convert igk-sync data to data-schemas
		$c = preg_replace_callback("#igk-sync#", function($m) use($u){
			return IGK_SCHEMA_TAGNAME;
		},$c);
		//load xlm node
		// igk_io_save_file_as_utf8("d:\\temp\\out.xml",$c);
		// exit;
		$n = IGKHtmlReader::LoadXML($c);
		// igk_wln($n);
		// exit;
		$p = igk_db_load_data_and_entries_schemas_node($n);
		// igk_wln(array_keys((array)$p));//->Entries);
		// igk_wln($p->Relations);//->Entries);
		igk_wln("donnnnnnnne");
		igk_wln($p->Entries);//->Entries);
		exit;
		
		// igk_wln("treat entries ");
		$refs = array(); //to limit data base access
		foreach($p->Entries as $k=>$v){
			if (isset($p->Relations[$k])){
				//igk_wln("there is link for relation ".$k);
				$tb = $p->Relations[$k];
				foreach($v as $rr=>$row){
					foreach($tb as $km=>$sm){
						//igk_wln("treat ".$k);
						// igk_wln($sm);
						//igk_wln("value ".$row[$km]);
						$key = strtolower($sm["Table"]."/".$row[$km]);
						$i = igk_getv($refs, $key);
						if ($i==null){
							$i =  $ctrl->Db->getSyncDataID($sm["Table"], $row[$km], $v);
							if (empty($i)){
								igk_log_write_i(__FUNCTION__, " data not found for ".$sm["Table"]. ":::".$row[$km]);
								
							}
							//igk_wln("sync :  ".$i);
							$refs[$key] = $i;
						}
						$row[$km] = $i;
					}
					$v[$rr] = $row;
				}
				//update change to entries
				$p->Entries[$k] = $v;
			}
			
			//3. insert data 	
			foreach($v as $rr=>$row){
				$ctrl->Db->insert_if_not_exists($k,$row);
			}			
		}
			if (!$error){
				$rep->addNode("Status")->Content = 0;
			}	
			
				igk_wln("debugger view ::: loadsyncdata");
			igk_debuggerview()->RenderAJX();
			$rep->RenderAJX();	
				// igk_wln($args);
				// igk_wln($_REQUEST);
			},			
			"help"=>function() use(& $_data){
				$doc = igk_get_document(__FUNCTION__);
				$doc->Title = "Api - MYSQL ";
				$bbox = $doc->body->addBodyBox()->ClearChilds();
				$b = $bbox ->addDiv();
				$b->addSectionTitle()->Content = "Command list";
				$b = $bbox->addDiv()->addContainer()->addRow();
				foreach(array_keys($_data) as $k){
					$b->addCol()->addDiv()->addA("#")->Content = $k;
				}
				$doc->RenderAJX();
				igk_exit();
			});
			
			
		if (isset($_data[$cmd])){
			$f = $_data[$cmd];
			return call_user_func_array($f, array($cmd, $args));
		}
		else{
				$file = IGKIO::GetDir(dirname(__FILE__)."/.mysql.inc");
				if (file_exists($file)){
					include_once($file);
				}
				$f = "igk_api_mysql_".str_replace("-", "_", $cmd);
				if (!function_exists($f)){
					igk_log_write_i(__FUNCTION__."::", "function $c not exists");
					//$f = "igk_api_mysql_help";
					igk_wln("function not exists ".$file . " ".$f);
					exit;
				}else{
				$tab = array();
				$tab[] = $this;
				$tab = array_merge($tab, $args);
				return call_user_func_array($f, $tab);
				}				
				//igk_wln("command  $cmd not found");
			
		}
		exit;
		
	}

	public function setup($cmd=null){
		igk_wln(__FUNCTION__." command");
	}
}
?>