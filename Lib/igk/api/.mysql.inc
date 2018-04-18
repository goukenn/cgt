<?php


function igk_api_free_session(){
	if (!igk_server_request_onlocal_server()){
					if (igk_getr("clClearS")){
						igk_app_destroy();
						session_destroy();
					}
	}
	
}
//mysql utility 
// { function igk_api_mysql__doc($api, $f=null){ }
	// { $doc = igk_get_document("sysapp:mysql", true); }
	// { return $doc; }
// { } }

function igk_api_mysql_mysql_help($api){

	$doc = igk_get_document("sysapp:mysql", true);
	$doc->Title = "MysQL Utility - [BALFAFON]";	
	
	$bbox = $doc->body->addBodyBox();
	$dv = $bbox->addDiv()->addContainer();
	
	$ul = $dv->addUl();
	
	$c = file_get_contents(__FILE__);
	$tab = array();
	
	// preg_match_all("/function\\s+igk_api_mysql_(?P<name>([a-z]|[_])([a-z0-9_]*))\\s*/i",$c, $tab);
	preg_match_all("/function\\s+igk_api_mysql_(?P<name>([a-z])([a-z0-9_]*))\\s*/i",$c, $tab);
	
	foreach($tab["name"] as $k=>$v){
		$ul->addLi()->add("a")->setAttribute("href",$api->getAppUri("mysql/".$v))->Content = $v;
	}
	
	$mi = $doc->body->addmenu()->setAttributes(array(
		"type"=>"context",
		"id"=>"action"
	))->addmenuitem();
	
	$mi["accesskey"] = 1; 
	$mi["label"] = "Aller à la ligne" ;
	$mi["type"]="checkbox";
	
	// addObData($tab);
	// igk_wln($tab);
	// exit;
	
	$doc->RenderAJX();
}
function igk_api_mysql_about($api){
	$doc = igk_get_document("sysapp:mysql", true);	
	$doc->Title = "MySQL Utility - About - [BALAFON]";
	$bbox = $doc->body->addBodyBox();
	$dv = $bbox->addDiv()->addContainer();
	$dv->addDiv()->setClass("igk-title-4")->Content = "About";
	$ul = $dv->addDiv()->addUl();
	$ul->addLi()->Content = IGK_AUTHOR;
	$ul->addLi()->Content = "Version : 2.0";
	
	$doc->RenderAJX();
}
function igk_balafon_mysql_datatype($t){
	switch(strtolower($t)){
		case "int":
			return "Int";
		case "varchar":
			return "VARCHAR";
	}
	return strtoupper($t);
}
function igk_mysql_rename_table($api, $tbname, $ntbname){

	$mysql = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER, true);
		if($mysql->connect()){	
	$tbname = igk_db_escape_string($tbname);
	$ntbname = igk_db_escape_string($ntbname);
	$mysql->sendQuery("RENAME TABLE `{$tbname}` TO `{$ntbname}`");
	$mysql->close();
	}
}
function igk_api_mysql_init_db($api){
	//igk_wln("initdb");
	if (igk_is_conf_connected()){		
		igk_getctrl(IGK_DB_CTRL)->initSDb(false, false);
	}
	else{
		igk_createNode("response")->setContent("You must be logged as administrator")->RenderXml();
	}		
	exit;
}

function igk_api_mysql_resetsysdb(){
	igk_getctrl(IGK_SYSDB_CTRL)->resetDb();
}

///used to get relation </summary>
function get_relation($mysql, $tname, $clname,  $dbname=null){

	$r = $dbname==null? igk_mysql_db_selected_db($mysql) : $dbname;
	$n = $mysql->selectdb("information_schema");
	
	// $h = $mysql->sendQuery("SELECT * FROM `TABLE_CONSTRAINTS` WHERE `TABLE_NAME`='".igk_db_escape_string($tname)
		// ."' AND `TABLE_SCHEMA`='".igk_db_escape_string($r)."'");
		
	$h = $mysql->sendQuery("SELECT * FROM `KEY_COLUMN_USAGE` WHERE `TABLE_NAME`='".igk_db_escape_string($tname)
		."' AND `TABLE_SCHEMA`='".igk_db_escape_string($r)."' AND `COLUMN_NAME`='".igk_db_escape_string($clname)."' AND `REFERENCED_TABLE_NAME`!=''");
	
	//restore db
	$n = $mysql->selectdb($r);
	// if ($tname == "tbigk_users_reference_models")
		// igk_wln($h);
	return $h->getRowAtIndex(0);	
}
function igk_api_get_columninfo($row, $v, $table_n, $mysql){
	$cl = $row->addNode("Column");
	$cl["clName"] = $v->Field;
	$tab = array();
	preg_match_all("/^((?P<type>([^\(\))]+)))\\s*(\((?P<length>([0-9]+))\)){0,1}$/i", 
	trim($v->Type)
	//"Text"
	, $tab);
	
	// igk_wln($tab);
	// exit;
	// if ($table_n == "tbigk_humans"){
		// igk_wln($tab["type"]);
		// igk_wln("for : ".$v->Type);
		// igk_wln(igk_balafon_mysql_datatype( igk_getv($tab["type"],0, "Int")));
		// igk_wln(igk_getv($tab["length"],0, 0));
		// $e = true;
	// }
	
	
	$cl["clType"] = igk_balafon_mysql_datatype( igk_getv($tab["type"],0, "Int"));
	$cl["clTypeLength"] = igk_getv($tab["length"],0, 0);
	if ($v->Default)
		$cl["clDefault"] = $v->Default;
	if ($v->Comment){
		$cl["clDescription"] = $v->Comment;
	}
	$cl["clAutoIncrement"] = preg_match("/auto_increment/i", $v->Extra) ? "True" : null;
	$cl["clNotNull"] = preg_match("/NO/i", $v->Null) ? "True": null;
	$cl["clIsPrimary"] = preg_match("/PRI/i", $v->Key) ? "True": null;
	$cl["clIsUnique"] = preg_match("/UNI/i", $v->Key) ? "True": null;
	
	// if ($table_n=="tbsrv_be_product_types"){
		
		// igk_ilog((array)$v);
		
	// }
	
	
	if (preg_match("/(MUL|UNI)/i", $v->Key)){
		//link type type relation
		
		$rel = get_relation($mysql, $table_n,$v->Field);
		if ($rel){
			$cl["clLinkType"] = $rel->REFERENCED_TABLE_NAME;
		}
		// else{
			// igk_wln($v);
			// igk_wln("rel is null [{$t->$n}::{$v->Field}]");
			// exit;
		// }
	}
	return $cl;
}
function igk_api_mysql_get_data_schema($api, $render=1 , $param=null){
	$rep  = igk_createNode("data-schemas");
	$rep["Date"] = date('Y-m-d');
	$rep["Version"]="1.0";
	$mysql = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER, true);
	$tables = array();
	if($mysql->connect()){	
		$mysql->selectdb($api->App->Configs->db_name);
		$r = $mysql->listTables();//("Show Tables;");		
		if ($r){
				$n = $r->Columns[0]->name;
				$e = false;
				foreach($r->Rows as $t){
					if ($e)
					exit;	

					$table_n = $t->$n;
					$row = $rep->addNode("DataDefinition")->setAttributes(array(
						"TableName"=>$table_n
					));
					$tinfo = array();
					
					
					
					
					
					$tt = $mysql->sendQuery("SELECT Count(*) FROM `".$table_n."`;");
					$b = $tt->Columns[0]->name;
					$row["Entries"] = $tt->Rows[0]->$b;
					
					//$tt = $mysql->sendQuery("DESCRIBE `".$t->$n."`;");
					$tt = $mysql->sendQuery("SHOW FULL COLUMNS FROM `".$table_n."`;");
					
					foreach($tt->Rows as $m=>$v){	
						$cl = igk_api_get_columninfo($row, $v,$table_n, $mysql);						
						$tinfo[$v->Field] =new IGKDbColumnInfo( $cl->Attributes->ToArray());
						// if($table_n == "tbigk_humans"){
							//igk_wln($tables[$table_n]->tinfo[$v->Field]);
							//$e = true;
						// }
					}
			
					$tables[$table_n] = (object)array("tinfo"=>$tinfo, 'ctrl'=>"sys://mysql_db");
					// igk_wln("for table : ".$table_n);
					// igk_wln($tables[$table_n]);
					// igk_wln($tinfo);
				}
		}
		$mysql->close();
	}
	if ($render)
		igk_wl($rep->RenderXML());
		
	$check  = igk_getv($param, "check");
	if ($check)
		return $tables;
	return $rep;
}

function igk_api_mysql_get_table_definition($api, $row, $table_n, $mysql, $tinfo=null, $entries=null){ // Note can't pass item call by ref in call_array_function callback
	// igk_wln("get table definition ");
	// igk_wln(func_get_args());
	// exit;
	$tt = $mysql->sendQuery("SELECT Count(*) FROM `".$table_n."`;");
	if ($tt->RowCount==0)
		return;
	//igk_wln($tt);
	// igk_ilog(igk_ob_get($tt));
	// igk_wln(igk_ob_get($tt->Columns[0]));
	
	$b = igk_getv($tt->Columns[0],"name");
	// igk_wln($b);
	
	$count = $tt->Rows[0]->$b;
	// igk_wln($count);
	// exit;
	$row["Entries"] = $count;
	
	//$tt = $mysql->sendQuery("DESCRIBE `".$t->$n."`;");
	$tt = $mysql->sendQuery("SHOW FULL COLUMNS FROM `".$table_n."`;");
	
	foreach($tt->Rows as $m=>$v){
		$cl = igk_api_get_columninfo($row, $v, $table_n, $mysql);						
		
		if ($tinfo)
		$tinfo[$v->Field] =new IGKDbColumnInfo( $cl->Attributes->ToArray());
		
	}

	if($entries && $count>0){
		//<Rows For="tbadman_userjobs">
		$n = $entries->addNode("Rows")->setAttribute("For", $table_n);
		foreach($mysql->select($table_n)->Rows as $k=>$v){
			$n->addNode("Row")->setAttributes($v);
		}
	}
}

function igk_api_mysql_get_sync_definition($api, $row, $table_n, $u, $mysql, $db=null, $entries=null, $tables=null){
					$tt = $mysql->sendQuery("SELECT Count(*) FROM `".$table_n."`;");
					$b = $tt->Columns[0]->name;
					$count = $tt->Rows[0]->$b;
					$row["Entries"] = $count;
					
					//$tt = $mysql->sendQuery("DESCRIBE `".$t->$n."`;");
					$tt = $mysql->sendQuery("SHOW FULL COLUMNS FROM `".$table_n."`;");
					$links = array();//links in tables
					$auto = null;
					foreach($tt->Rows as $m=>$v){	
					
						$cl = igk_api_get_columninfo($row, $v,$table_n, $mysql);	
						
						if ($cl["clLinkType"] ){
							$lnk =$cl["clLinkType"];
							$g = igk_getv($links, $lnk, null);								
							if ($g==null){
								$g = array();
							}								
							$g[] = array("Column"=>$v->Field, "Table"=> $lnk);
							$links[$lnk] = $g;
						}
					
						if ($cl["clAutoIncrement"]){
							$auto = $v->Field;
						}
					}
					
					//treat entries
					$g = array("Links"=> $links, "auto"=>$auto, "count"=>$count);
					if ($tables !== null){						
						$tables->list[$table_n] = $g;
					
					}
					if(($entries !=null) && ($count>0)){
						// igk_wln("entries ..... ");
						//TODO: request for entries	
						igk_api_sync_def_evaluate_entries($entries, $table_n, $mysql,$db, $tables!==null? $tables : (object)array("list"=>array($table_n=>$g), "value"=>array()));
						
					}
	
}
///<summary> evaluate entries</summary>
function igk_api_sync_def_evaluate_entries($entries, $table_n, $mysql, $db, $tables){
	//<Rows For="tbadman_userjobs">
						$n = $entries->addNode("Rows")->setAttribute("For", $table_n);
						$list = $tables->list[$table_n];
						$links = $list["Links"];
						$auto = $list["auto"];
						
						$fc_update = function($tn)use(&$auto, $table_n, $db){
							if ($auto){
									//reset the auto increment column
									$tn[$auto]=null;
								}
								//mark indentification to row
								$v = $tn->getParam("dbRow");
								
								$tn->setAttr("igk:id", $db->getSyncIdentificationId($table_n, $v));
						};
						if (igk_count($links)>0){
							$rows = $mysql->select($table_n)->Rows;
							//igk_wln("other link ".$lnk);
							foreach($rows as $k=>$v){
								$tn = $n->addNode("Row")->setAttributes($v);
								$tn->setParam("dbRow", $v);
								
								
								
								$continu = false;
								foreach($links as $lnk=>$vlnk){
									//treat each column
									foreach($vlnk as $g){
										$cn = $g["Column"];
										$ftn = $g["Table"];
										// igk_wln($cn);
										// if ($ftn== IGK_TB_USERS){
										
											// if ($tn[$cn] != $u->clId){
												// $tn->remove();
												// $continu = true;
													// igk_wln("for user .... remove ");
												// break;
											// }
											
										// }
										$clvalue = $tn[$cn];
										$bck = igk_getv($tables->list, $ftn);
										$v_mk =  $ftn."/".$clvalue;
										if ($bck && (igk_count($bck["Links"])>0)){
											//igk_wln("cibling contains links ".$table_n. " ::: ".$ftn);
											
											$v_data = ($v_data = igk_getv($tables->values, $v_mk ))?$v_data : $db->getSyncDataValueDisplay($ftn ,$clvalue, $tables);
											$tn[$cn] = $v_data;											
											$tables->values[$v_mk] = $v_data;
											// igk_wln("data : ".$v_data);
											// igk_exit();
										}
										else{
											$v_data = ($v_data = igk_getv($tables->values, $v_mk ))?$v_data : $db->getSyncDataValueDisplay($ftn ,$clvalue);
											$tn[$cn] = $v_data;											
											$tables->values[$v_mk] = $v_data;
										}
									}
									if ($continu)break;
								}
								$fc_update($tn);
								
							
						}
						}
						else{
							foreach($mysql->select($table_n)->Rows as $k=>$v){
								$tn = $n->addNode("Row")->setAttributes($v);
								$tn->setParam("dbRow", $v);
								$fc_update($tn);							
							}
						}
						
						
}
function igk_api_mysql_get_config_data_schema($api, $render=1, $param=null){
	$ctrl = $api->App->getControllerManager()->getControllers();
	$rep = igk_createNode("data-schemas");
	$uri = igk_getv(igk_getv(igk_get_ns(), "ns"), "igk");
	$rep->setAttribute("xmlns:igk", $uri);
	$msg = $rep->add("warning-message");
	$err = $rep->add("error-message");
	$not = $rep->add("notice-message");
	$msg->setCallback("getIsVisible", "return \$this->ChildCount>0;");
	$not->setCallback("getIsVisible", "return \$this->ChildCount>0;");
	$err->setCallback("getIsVisible", "return \$this->ChildCount>0;");	
	$tables = array();
	$count = 0;
	
	foreach($ctrl as $k=>$v){
	
		if ($v->getDataAdapterName() == IGK_MYSQL_DATAADAPTER){
		
			if (!$v->UseDataSchema){
				//fonction list
				$tname = $v->getDataTableName();
				$tinfo = $v->getDataTableInfo();
				
				if (!empty($tname) && ($tinfo!==null))
				{
					if (isset($tables[$tname]))
					{
						$msg->add("warning")->Content = "Table $tname already found. [".$v->Name. "] get from ".$tables[$tname]->ctrl->Name. " no schema";						
						continue;
					}
					$tables[$tname] = (object)array("info"=>$tinfo, "ctrl"=>$v);
					$row = $rep->addNode("DataDefinition")->setAttributes(array(
						"TableName"=>$tname,
						"igk:ctrlName"=>$v->Name
					));
					
					foreach($tinfo as $kk=>$vv){
						$cl = $row->addNode("Column");
						$cl->setAttributes($vv);
					}
					$count ++;
				}
			}
			else{	
				
			
				$tschema =igk_db_load_data_schemas($v->getDataDir()."/data.schema.xml");	
				if ($tschema){
				// igk_wln($tschema);
				// exit;
				foreach($tschema as $ck=>$cv){
				
					if (isset($tables[$ck]))
					{
						$msg->add("msg")->Content = "Table $ck already found. [".$v->Name. "] get from ".$tables[$ck]->ctrl->Name." with schema";
						
						continue;
					}
					
						$row = $rep->addNode("DataDefinition")->setAttributes(array(
						"TableName"=>$ck,
						"igk:ctrlName"=>$v->Name
					));
					$row["clDescription"] = igk_getv($cv, "clDescription");
					$cinfo =  igk_getv($cv, "ColumnInfo");
					
					$tables[$ck] =(object) array("info"=>$cinfo, "ctrl"=>$v);
					
					foreach($cinfo as $ckk=>$ckv){
					$cl = $row->addNode("Column");
					$cl->setAttributes($ckv);
					}
					$count++;
				}
				}
			}
		
		}
	}
	$rep["tableCount"] = $count;
	$rep["Version"] = "1.0";
	$rep["Warning"] = $msg->ChildCount >0?$msg->ChildCount:null;
	$rep["Notice"] = $not->ChildCount >0?$not->ChildCount:null;
	$rep["Error"] = $err->ChildCount >0?$error->ChildCount:null;
	
	if ($render){
		igk_wl($rep->RenderXML());
	}
	$check  = igk_getv($param, "check");
	if ($check)
		return $tables;
	return $rep;
}
function igk_api_mysql_who_use($api, $tablename=null){
	if (!isset($tablename))
	{
		igk_navto($api->getAppUri("mysql"));
	}
	$c = igk_db_getdatatable_owner($tablename);
	if ($c){
		$n = igk_createNode("response");
		$n->addNode("info")->setContent("".$c);
		$n->addNode("class")->Content = get_class($c);
		if (igk_server_is_local()){
			$n->addNode("file")->Content = (new ReflectionClass(get_class($c)))->getFileName();
		}
		$n->RenderXML();
	}
}


function igk_api_mysql_check_data_structure($api){

	//get configuration data schema
	// igk_wln("configuration data schema");
	$tconfig = igk_api_mysql_get_config_data_schema($api, false, (object)array("check"=>1));
	//get mysql data schema
	
	// igk_wln("mysql >  data schema");
	$tdb     = igk_api_mysql_get_data_schema($api, false, (object)array("check"=>1));
	// igk_wln($tdb["tbigk_humans"]);
	// exit;
	$rep = igk_createNode("data_rep");
	$err = $rep->add("error-message");
	$err->setCallback("getIsVisible", "return \$this->ChildCount>0;");	
	
	$notused = $rep->add("TableNotUsed")->setCallback("getIsVisible", "return \$this->ChildCount>0;");	
	 // igk_wln(array_keys($tconfig));
	$ubind = (array_keys($tdb));
	
	foreach($tconfig as $k=>$v){
		$tbname = $k;
		if (!isset($tdb[$k]))
		{
			$err->add("Error")->add("Message")->Content = "Table $k doesn't exists";
			continue;
		}
		$v_b = $v->info;
		$v_testb  = $tdb[$k]->tinfo;
		// igk_wln($v_b);
		//check structure
		// igk_wln("config");
		// igk_wln($tconfig[$k]);
		//igk_wln("base");
		// igk_wln($tdb[$k]);
		// exit;
		$v_err = 1;
		foreach($v_b as $kk=>$vv){		
			if (!isset($v_testb[$vv->clName])){
				$err->add("msg")->Content = "Column [{$tbname}::{$vv->clName}] Not found";
				$v_err=0;
				continue;
			}
			$v_btest = $v_testb[$vv->clName];
			if (strtolower($vv->clType) != strtolower($v_btest->clType)){
				$err->add("msg")->Content = "Column [{$tbname}::{$vv->clName}] Type Not match. Expected {$vv->clType} but {$v_btest->clType} found";
				$v_err=0;
			}else if (igk_db_is_typelength($vv->clType) &&  ($vv->clTypeLength != $v_btest->clTypeLength)){
				$err->add("msg")->Content = "Column [{$tbname}::{$vv->clName}] Type length not match. Expected {$vv->clTypeLength} but {$v_btest->clTypeLength} found";
				$v_err=0;
			}
			
			if (($vv->clLinkType) != strtolower($v_btest->clLinkType)){
				$err->add("msg")->Content = "/!\\ Column [{$tbname}::{$vv->clName}] Linktype not match. Expected {$vv->clLinkType} but {$v_btest->clLinkType} found!!!";
				$v_err=0;
			}
		}
		if ($v_err)
		unset($ubind[$k]);
	}
	
	foreach($ubind as $k=>$v){
		$notused->addNode("Item")->Content = $v;
	}
	
	if ($err->ChildCount>0)
		$rep["HasError"] = 1;
	
	igk_wl($rep->RenderXML());
	//igk_wl($rep->RenderXML());
}


/*
///<summary>execute a mysql command</summary>
function igk_api_mysql_cmd($api, $cmd=null){
	$args = array_slice(func_get_args(), 2);
	// igk_wln($cmd);
	// igk_wln($args);
	// exit;
	switch($cmd){
			case "syncdata":
					//2 agrs requires
					//0 : ctrl name
					//1 : user id
					$sync = igk_createNode("igk-sync");
					$ctrl = igk_getctrl(igk_getv($args, 0));
					$uid = igk_get_user(igk_getv($args, 1));
					if ($ctrl && $uid){
						$api->setParam("syncdata:info", array());
						$u = $uid;
						$tb = igk_db_get_ctrl_tables($ctrl);
						$apt = igk_get_data_adapter($ctrl->getDataAdapterName());
						//igk_wln($apt);
						if ($apt->connect()){
							 $entries = $sync->addNode("Entries");
							foreach($tb as $k=>$v){
						$rep = $sync->addNode("DataDefinition")->setAttributes(array(
							"TableName"=>$v
						));
						$api->mysql("get_sync_definition", $rep, $v, $u, $apt, $ctrl->Db, $entries);
						
					
					}
							$apt->close();
							
							$api->setParam("syncdata:info", null);
						}
					}else{
						igk_wln("args don't match");
					}
				$sync->RenderXML();
				return;
			break;
			case "backupdb":
			$ctrl = igk_getctrl(igk_getv($args, 1));
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
						igk_getctrl("app_api")->mysql("get_table_definition", $rep, $v, $apt, null, $entries);
						
					
					}
					$apt->close();
				}
				
				$schema->RenderXML();
				
				return $schema;
				
				
			}
				break;
		}
}
*/


///<summary>used to reset database for a controller</summary>
function igk_api_mysql_resetctrldb($api, $ctrlname=null){
	if (!igk_is_conf_connected()){
		igk_ilog("'admin' must be connected in order to resetdb ctrl");
		return 0;
	}
	$ctrl = igk_getctrl($ctrlname);
	$r = 0;
	if ($ctrl !=null){
		if ($ctrl->UseDataSchema){	
			//igk_notification_reg_event('sys://db/init_table', $callable);		
			$ctrl->Invoke("dropDbFromSchemas");
			$ctrl->Invoke("initDb");
			igk_notification_push_event("system/dbchanged", $ctrl);
			igk_wln("/!\\ Database reset for ".$ctrl->Name);
			$ctrl->setParam(IGK_CTRL_TABLE_INFO_KEY,null);
			//igk_wln($ctrl->getParam(IGK_CTRL_TABLE_INFO_KEY));
			//igk_notification_unreg_event('sys://db/init_table', $callable);	
			$r=1;
		}
	}
	if (!$r){
		igk_wln("no configuraton found");
	}
	return $r;
}


function igk_api_mysql_ctrl_gen_schema($api, $ctrlname){
	if (!igk_is_conf_connected()){
		igk_ilog("'admin must be connected in order to generate data schema");
		return 0;
	}
	
	
	$rep  = igk_createNode("data-schemas");
	$rep["Date"] = date('Y-m-d');
	$rep["Version"]="1.0";
	$n =igk_html_node_noTagNode();
	$rep->add($n);
	$e = $rep->add("Entries");
	$e->setCallback("getIsVisible", "return \$this->ChildCount>0;");
	
	
	$c = igk_getctrl($ctrlname);
	$tables = igk_db_get_ctrl_tables($c);
	
	if ($c->Db->connect()){
		$rep->addXmlNode("class")->setContent(get_class($c));
		$rep->addXmlNode("table")->setAttribute("count", igk_count($tables));
		
		
		$mysql = $c->Db->Ad;
	foreach($tables as $k=>$v){
		$row = $n->addNode("DataDefinition")->setAttributes(array(
						"TableName"=>$v
		));	
		
		//$tt = $mysql->sendQuery("DESCRIBE `".$t->$n."`;");
					$tt = $mysql->sendQuery("SHOW FULL COLUMNS FROM `".$v."`;");
					
					foreach($tt->Rows as $m=>$s){	
						$cl = igk_api_get_columninfo($row, $s,$v, $mysql);						
						//$tinfo[$v->Field] =new IGKDbColumnInfo( $cl->Attributes->ToArray());
						// if($table_n == "tbigk_humans"){
							//igk_wln($tables[$table_n]->tinfo[$v->Field]);
							//$e = true;
						// }
					}
					
					
					$tt = $c->Db->select($v);
					
					if ($tt->RowCount>0){
						$erow = $e->addNode("Rows");
						$erow["For"] = $v;
						foreach($tt->Rows as $i=>$l){
							$erow->addNode("Row")->setAttributes($l);
						}
						
						// <Rows For="tbsrv_country_continents">
			// <Row clName="Africa" />
			// <Row clName="Europe" />
			// <Row clName="NorthAmerica" />
			// <Row clName="SouthAmerica" />
			// <Row clName="Asia" />
			// <Row clName="Antarctic" />
			// <Row clName="Oceanic" />
		// </Rows>
						
					}
		
	}
	$c->Db->close();	
	}
	$rep->renderXML();
}
?>