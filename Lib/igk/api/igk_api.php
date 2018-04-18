<?php
define("IGK_API_CTRL", "API");
define("IGK_API_VERSION", "2.1.1.0921");
define("IGK_API_URI", "^/api/v2");


require_once(dirname(__FILE__)."/.igk.api.func.inc");

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
	public function getBasicUriPattern(){
		return IGK_API_URI;
	}
	public function sysversion(){
		ob_clean();
		igk_wl(IGK_VERSION);
		igk_exit();
	}
	public function request(){
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
	
	///<summary>represent a function database function list</summary>
	public function datadb($cmd=null){
		///TODO: PROTECT CALL
		//inclue .mysql.inc
		$file = IGKIO::GetDir(dirname(__FILE__)."/.mysql.inc");
		if (file_exists($file)){
			include_once($file);
		}
		
		$args = array_slice(func_get_args(), 1);
		$_data= null;
		$_api = $this;
		$_data = array(
		"gentoken"=>function($cmd, $args)use($_api){
			if(igk_server_request_onlocal_server()){
				igk_wln("/!\\ Request on local server");
				return null;
			}
			$s = igk_new_id();
			$_api->setParam("var::OpToken", $s);
			igk_wl($s);
			//exit without killing the session
			igk_exit();
		},
		"syncfrom"=>function($cmd, $args)use($_api){
			
			//sample:  datadb/syncfrom/127.0.0.1/app_Controller/bondje.doue@igkdev.be
			$rep = igk_createNode("response");
			$error = false;
			$ctrl = igk_getctrl(igk_getv($args, 1));
			$u = igk_get_user_bylogin(igk_getv($args, 2));
			$srv = ($srv = igk_getv($args, 0)) ? $srv : igk_getr("clServer");
			
			if (!$ctrl || !$u || empty($srv) 
				//|| igk_srv_is_local($srv)
				){
				$error = true;
				$rep->addNode("Status")->Content = -1;
				$rep->addNode("message")->Content = "Ctrl, Server or User is not found";
			}
			else{
				
				//igk_wln("get sync data ....");
				$c = null;
				$token = igk_post_uri(igk_str_rm_last($srv, '/')."/api/v2/datadb/gentoken");
				// igk_wln("Token");
				// igk_wln($token);
				// exit;
				if ($token !== false){
				$c = igk_post_uri(igk_str_rm_last($srv, '/')."/api/v2/datadb/syncdata",array("clCtrl"=>$ctrl->Name, 
				"clLogin"=>$u->clLogin, 
				"clClearS"=>1,
				"clToken"=>$token));
				}
				// $this->datadb("syncdata", $ctrl->Name, $u->clLogin);//mysql(igk_post_uri("http://192.168.1.52/igkdev/api/v2/datadb/loadsyncdata",				
				// $c = IGKOB::Content();
				// IGKOb::Clear();
				
				//igk_wln("donz...");
				header("Content-Type: application/xml");
				//igk_wl($c);
				
				if(empty($c)){
					$rep->addNode("Message")->Content = "can't get server response";
					$v = igk_post_uri_last_error();
					if($v){
					//$rep->addNode("Status")->addObData($v);
					$rep->addNode("ErrorCode")->Content=  $v["type"];
					$rep->addNode("ErrorMessage")->Content=  $v["message"];
					}
				}else{
					//igk_wln($c);
					
					$this->datadb("loadsyncdata", 
					$c, 
					$u->clLogin,
					$ctrl->Name);
				}
				//$rep->RenderAJX();
				exit;
					// $g = igk_post_uri($srv."/api/v2/datadb/loadsyncdata", array(
					// "data"=>$c, 
					// "login"=>$u->clLogin,
					// "ctrl"=>$ctrl->Name)); 
					
					header("Content-Type: application/xml");
					igk_wl($g); 
					exit;
			
			}
			if (!$error){
				$rep->addNode("Status")->Content = 0;
			}else{
				$rep->add(igk_debuggerview());
			}
			$rep->RenderXML();
			return !$error;
		},
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
				$g = igk_post_uri($srv."/api/v2/datadb/loadsyncdata", array(
				"data"=>$c, 
				"login"=>$u->clLogin,
				"ctrl"=>$ctrl->Name)); 
				
				header("Content-Type: application/xml");
				igk_wl($g); 
				exit;
			
			}
			if (!$error){
				$rep->addNode("Status")->Content = 0;
			}else{
				$rep->add(igk_debuggerview());
			}
			// igk_xml_render_header(1);
			igk_wl("<?xml version=\"1.0\" enctype=\"utf-8\" ?>");
			$rep->RenderXML();
			return !$error;
		},
		"syncdata"=>function($cmd,$args) use($_api){//return sync data of a user in a spécific controller
			
				//request from external serverr 
				if (!igk_server_request_onlocal_server()){
					$t = igk_getr("clToken");
					if(empty($t) || ($t != $_api->getParam("var::OpToken"))){
						igk_wl("<response><erro>1</erro><message>request not form you must have a valid token</message></response>");
						igk_api_free_session();
						igk_exit();
					}
					//igk_wln("tokend ::: ".$t);
					// igk_wln( $_api->getParam("var::OpToken"));
				// exit;
					// igk_wln(igk_getv($_SERVER, "REQUEST_URI"));
					// return null;
				}
				// igk_wln("syncdata");
				// exit;
				
					//2 agrs requires
					//0 : ctrl name
					//1 : user id
					// igk_wln($args);
					$sync = igk_createNode("igk-sync");
					$ctrl = igk_getctrl(($c = igk_getv($args, 0))? $c : igk_getr("clCtrl"));
					$uid = igk_get_user_bylogin(($c = igk_getv($args, 1))? $c : (($c = igk_getr("clLogin"))?$c:
						(($ctrl && ($c=$ctrl->User->clLogin))?$c:null)
					));
					// igk_wln($uid);
					// igk_wln("".$ctrl);
					if ($ctrl && $uid){
						$u = $uid;
						$tb = igk_db_get_ctrl_tables($ctrl);
						// sort($tb);
						$apt = igk_get_data_adapter($ctrl->getDataAdapterName());
						$sync["Controller"] = $ctrl->Name;
						$sync["namespace"] = $ctrl->Configs->Namespace;
						$sync["xmlns:igk"] = IGK_WEB_SITE;
						//igk_wln($apt);
						if ($apt->connect()){
							
							$tables = (object)array("list"=>array(), "values"=>array());//tables info list 
							$entries = $sync->addNode("Entries"); // entries nodes
							foreach($tb as $k=>$v_tablen){
								if (!isset($tables->list[$v_tablen]) && $ctrl->Db->getCanSyncDataTable($v_tablen)){
									$rep = $sync->addNode("DataDefinition")->setAttributes(array(
										"TableName"=>$v_tablen
									));
									$_api->datadb("get_sync_definition", $rep, $v_tablen, $u, $apt, $ctrl->Db,null, $tables);									
								}
							}
							//to evaluate global entries defintions
							//igk_wln($tables);
							//to avoid recursivity
							
							foreach($tables->list as $ktb=>$def){								
								//igk_wln($def);
								$d = (object)$def;
								if ($d->count>0){									
									// igk_wln('zy : entries for : '.$ktb);
									igk_api_sync_def_evaluate_entries($entries, $ktb, $apt, $ctrl->Db, $tables);
								}
							}
							// igk_wln("done");
							// exit;
							
					
						
							$apt->close();
							
							//raise request for sync
							$vd = igk_createNode();
							igk_notification_push_event("system/notify/syncdata/".$ctrl->Name, $_api, array("node"=>$vd, "user"=>$uid));
							if ($vd->HasChilds){
								foreach($vd->Childs->ToArray() as $l){
									switch($l->TagName){
										case "DataDefinition":
											$sync->add($l);
										break;
										case "Entries":
											$entries->addRange($l->Childs->ToArray());
											break;
									}
										
									
								}
							}
						}
					}else{
						igk_wln("/!\\ Args don't match or user not found");						
						igk_exit();
				}
				$sync->RenderAJX();
				igk_api_free_session();
				
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
					
					$schema = igk_db_backup_ctrl($ctrl);
					// // igk_wln("backup controller for : ".$ctrl->Name);
					// // igk_wln($ctrl->getDataAdapterName());
					// $tb = igk_db_get_ctrl_tables($ctrl);
					// $schema = igk_html_node_DataSchema();
					// //$igk_api_mysql_get_table_definition = igk_getctrl("api")->mysql("get_table_definition");
							// // $apt = igk_get_data_adapter($ctrl->getDataAdapterName());
					// $apt = igk_get_data_adapter($ctrl->getDataAdapterName());
					// //igk_wln($apt);
					// if ($apt->connect()){
						 // $entries = $schema->addNode("Entries");
						// // igk_wln("ok");
						// foreach($tb as $k=>$v){
							// $rep = $schema->addNode("DataDefinition")->setAttributes(array(
								// "TableName"=>$v
							// ));
							// igk_getctrl(IGK_API_CTRL)->mysql("get_table_definition", $rep, $v, $apt, null, $entries);
							
						
						// }
						// $apt->close();
					// }					
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
				
				if (igk_server_request_onlocal_server()){
					$c = igk_getv($args,0);
					$login = igk_getv($args,1);
					$u = igk_get_user_bylogin($login);				
					$ctrl = igk_getctrl(igk_getv($args,2));
					// igk_wln("on local ");
					// exit;
				}else{
					$c = igk_getr("data");
					$login = igk_getr("login");
					$u = igk_get_user_bylogin($login);				
					$ctrl = igk_getctrl(igk_getr("ctrl")); //register a component ctrl 
				}
				
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
		
		if ($p==null){
			$error = true;
			$rep->addNode("Error")->Content = 2;
			$rep->addNode("Message")->Content = "No data entry found";
			$rep->RenderAJX();
			igk_exit();
			return;
		}
		
		// igk_wln(array_keys((array)$p));//->Entries);
		// igk_wln($p->Relations);//->Entries);
		// header("Content-Type: text/html");
		// igk_wln("donnnnnnne");
		//tables info list 
		// $tableinfo = (object)array("list"=>array(), $values=>array()); 
		// igk_wln($p->Entries);//->Entries);
		//exit;
		
		// igk_wln($p->Entries["tbadman_userjobs"]);//->Entries);
		
		// foreach($p->Entries["tbadman_userjobs"] as $sss=>$rrr){
			// igk_wln("MMODE ".$rrr["clEnterprise_Id"] );
			// $rrr["clEnterprise_Id"] = "@:/dd4654654";
			// $p->Entries["tbadman_userjobs"][$sss] = $rrr;
			// break;
		// }
		
		// exit;
		//add extra properties
		$p->User = $u;
		//build rows list to insert in case requires
		$rowslist = array();
		foreach($p->Entries as $n=>$e){
			$rtab = array();
			foreach($e as $kirow=>$irow){
				//get adress or kirow so that if change made to row it will raise a propagation
				$cirow  = & $p->Entries[$n][$kirow];
				$id = $cirow["igk:id"];
				unset($cirow["igk:id"]);
				
				$rtab[$id ? $id : $ctrl->Db->getSyncIdentificationId($n, $irow, $p)] =
array("index"=>$kirow, "row"=>				& $cirow); // get reference row to get data
				
				// );
			}
			
			$rowslist [$n] =  $rtab;  
		}
		$p->Rows = $rowslist;

		//demonstrate the propagation
		//$p->Rows["tbadman_userjobs"]["@:/"]["row"]["clEnterprise_Id"] = "GOY=====";
		//igk_wln($p->Entries["tbadman_userjobs"]);
		
		
		// igk_wln("treat entries ");
		$refs = array(); //to limit data base access
		if ($ctrl->Db->Connect()){
		foreach($p->Entries as $k=>$v){
				//$k: tablename
				//$v: rows
			if (isset($p->Relations[$k])){
				//igk_wln("there is link for relation ".$k);
				//igk_wln("treat relation");
				$tb = $p->Relations[$k];
				foreach($v as $krow=>$row){
					foreach($tb as $km=>$sm){
						//igk_wln("treat ".$k);
						// igk_wln($sm);
						//igk_wln("value ".$row[$km]);
						$key = strtolower($sm["Table"]."/".$row[$km]);
						$i = igk_getv($refs, $key);
						if ($i==null){
							$i =  $ctrl->Db->getSyncDataID($sm["Table"], $row[$km], $p);
							if (empty($i)){
								igk_log_write_i(__FUNCTION__, " data not found for ".$sm["Table"]. ":::".$row[$km]);
							}
							$refs[$key] = $i;
						}
						$row[$km] = $i;
					}
					$v[$krow] = $row;
				}
				//update change to entries
				$p->Entries[$k] = $v;
			}
			
			
			// igk_wln("insert ... ");
			// igk_wln($p->Entries["tbadman_userjobs"]);
			// exit;
			//3. insert data 
			$ajx = 0;
			foreach($v as $rr=>$row){
				if ($ajx)igk_flush_write_data("insert in $k");
				///insert to db				
				$ctrl->Db->insert_if_not_exists($k,$row);
			}	
			if ($ajx)igk_flush_data();			
		}
			
			$ctrl->Db->close();
		}
			if (!$error){
				$rep->addNode("Status")->Content = 0;
				
			}	
				$rep->add(igk_debuggerview());
				//igk_wln("debugger view ::: loadsyncdata");
				//igk_debuggerview()->RenderAJX();
				$rep->RenderAJX();	
				// igk_wln("finish loading");
				// igk_wln($p->Entries);
				exit;
				// igk_wln($args);
				// igk_wln($_REQUEST);
			},			
		"help"=>function() use(& $_data, $_api) {
				$doc = igk_get_document($_api);
				$doc->Title = "Api - MYSQL ";
				$bbox = $doc->body->addBodyBox()->ClearChilds();
				$b = $bbox ->addDiv();
				$b->addSectionTitle()->Content = "Command list";
				$b = $bbox->addDiv()->addContainer()->addRow();
				$buri = $this->getAppUri();
				foreach(array_keys($_data) as $k){
					$b->addCol()->addDiv()->addA($buri.'/datadb/'.$k)->Content = $k;
				}
				$bbox->addDiv()->addContainer()->addRow()->addCol()->addSectionTitle(4)->setContent("MySQL");
				
				$row = $bbox->addDiv()->addContainer()->addRow();
				$fcs = get_defined_functions();
				//igk_wln($fcs);
				
				foreach($fcs["user"] as $b=>$m){
					if (preg_match("/^igk_api_mysql_(?P<name>(.)+)$/i", $m, $tab)){
						$row->addCol()->addDiv()->addA($buri.'/datadb/'.$tab["name"])->setContent($tab["name"]);
					}
					// else
						// $row->addCol()->addDiv()->setContent($m);
				}
				// $row->addDiv()->Content ="ok ".igk_count($fcs);
				
				$doc->RenderAJX();
				igk_exit();
			});
			
		if (isset($_data[$cmd])){
			$f = $_data[$cmd];
			return call_user_func_array($f, array($cmd, $args));
		}
		else{
				if (empty($cmd)){
					$help = $_data["help"];
					return call_user_func_array($help , array());
				}else{
				$f = "igk_api_mysql_".str_replace("-", "_", $cmd);
				if (!function_exists($f)){
					igk_log_write_i(__FUNCTION__."::", "function {$f} not exists");
					//$f = "igk_api_mysql_help";
					igk_wln("function not exists ".$file . " ".$f);
					exit;
				}else{
				$tab = array();
				$tab[] = $this;
				$tab = array_merge($tab, $args);
				return call_user_func_array($f, $tab);
				}	
				}
				//igk_wln("command  $cmd not found");
			
		}
		igk_exit();
		
	}

	
	public function ctrl($cmd=null){
		// igk_wln("gen ".$cmd);
		$args = array_slice(func_get_args(), 1);		
		$_api = $this;
		$_data= array();
		$n = igk_createNode("div");
		$_data["geninstall"]=function($ctrl) use($n, $_api){
			// igk_wln($ctrl);
			$v = igk_getctrl($ctrl);
			if (!$v){
			$n->addDiv()->Content = "/!\\ Controller ".$ctr." not found";
			return false;
			}
			$folder = $v->getDeclaredDir();	
			// igk_wln($folder);
			//create a zip file
			$zip = new ZipArchive();
			$tempdir = $_api->getDeclaredDir()."/temp";
			IGKIO::CreateDir($tempdir);
			$ftempdir = IGKIO::GetDir( $tempdir."/".igk_new_id().".iczip");
			if ($zip->open($ftempdir, ZIPARCHIVE::CREATE)){
				$h = igk_zip_dir($folder, $zip);
				$inf = igk_createNode("ctrl");
				igk_api_build_ctrl_manifest($ctrl, $inf);
				$opt = igk_xml_create_render_option();
				$opt->Context = "xml";
				$zip->addFromString("ctrl.manifest", $inf->Render($opt));
				
				
				
				$zip->close();
				$n->Content = " zip archive created ";
				
				
				igk_download_file($v->Name.".iczip", $ftempdir);
				unlink($ftempdir);
				igk_exit();
				
			}else{
				$n->Content = "not created";
			}
		
		};
		
		
		//init ctrl db
		$_data["initDb"]=function($ctrl) use($n, $_api){			
			$ctrl = igk_getctrl($ctrl);
			if ($ctrl && igk_is_conf_connected()){
				$ctrl->initDb();
				igk_notifyctrl()->addSuccess("init done");				
			}
			// igk_wln("acll init db".$ctrl);			
			// igk_wln(func_num_args());
			igk_nav_session();
		};
		
		$_data["resetDb"]=function($ctrl) use($n, $_api){			
			$ctrl = igk_getctrl($ctrl);
			if ($ctrl && igk_is_conf_connected()){
				$s = $_api->datadb("resetctrldb", $ctrl);
				igk_notifyctrl()->addSuccess("reset db init done ?".$s);				
			}
			// igk_wln("acll init db".$ctrl);			
			// igk_wln(func_num_args());
			igk_nav_session();
		};
		
				
		$file = IGKIO::GetDir(dirname(__FILE__)."/.ctrl.inc");
		if (file_exists($file)){
			include_once($file);
		}
		
		if (isset($_data[$cmd])){
			$f = $_data[$cmd];
			return call_user_func_array($f, $args);
		}
		else{
			$fclist = $_data;
			include(IGK_LIB_DIR."/.igk.fc.call.inc");
		}
		
		$doc = igk_get_document($this, true);
		$doc->body->addBodyBox()->ClearChilds()->add($n);
		$doc->RenderAJX();
		
	}
	public function setup($cmd=null){
		igk_wln(__FUNCTION__." command");
	}
}
?>