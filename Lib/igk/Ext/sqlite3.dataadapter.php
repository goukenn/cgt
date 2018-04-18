<?php

define("IGK_SQL3LITE_KN", "sql3lite");
define("IGK_SQL3LITE_KN_TABLE_KEY", IGK_SQL3LITE_KN."::/tableName");
define("IGK_SQL3LITE_KN_QUERY_KEY", IGK_SQL3LITE_KN."::/query");
define("IGK_SQL3LITE_TYPE_NAME_INDEX", 2);
define("IGK_SQL3LITE_NAME_INDEX", 1);

class IGKSQLite3DataAdapter extends IGKSQLDataAdapter{
	
	private static $LENGTHDATA = array("int"=>"Int","varchar"=>"VarChar");
	private $m_sql;
	private $m_creator;
	static  $sm_list;
	private static $sm_connexions; 
	private $fname; //store the current filename
	private $m_current;//store current connection information
	public function getConnection($n){
		$r = igk_getv(self::$sm_connexions,$n);
		return $r;
	}
	public function getLastId(){
		return $this->sql->lastInsertRowID();
	}
	public function getVersion(){
		return $this->sql->version();
	}
	public function storeConnection($fname, $sql){
		if (self::$sm_connexions==null)
			self::$sm_connexions  = array();
		$this->m_current = (object)array(
			"filename"=>$fname,
			"openCount"=>1,
			"sql"=>$sql
		);
		self::$sm_connexions[$fname]= $this->m_current;
	}
	public function getSQL(){
		return $this->m_current!=null ? $this->m_current->sql : $this->m_sql;
	}
	public function OpenCount(){
		return $this->m_current!=null ? $this->m_current->openCount : $this->m_openCount;
	}
	public static function GetSchemaOptions($n){
		//$n = igk_createNode("div");
		$ul = $n->addUL();
		
		$li = $ul->add("li");
		$li->addLabel("clsqlite3_file")->setContent(null)->addSpan()->Content = "SQLite3 Db Filename";
		$li->addInput("clsqlite3_file", "text");
		return $n;
	}
	// public static function StoreSchemaOptions(){
		
	// }
	// public function select($tab, $condition=null){
		// throw new Exception("dk");
		
	// }
	public static function GetCurrent(){
		if (self::$sm_list==null)
			self::$sm_list =array();
		return  igk_getv(self::$sm_list, 0);
	}
	public static function StoreStack($l){
		
		self::GetCurrent();
		array_unshift(self::$sm_list, $l);
	}
	public function setCreatorListener($listener){
		$this->m_creator = $listener;
	}
	///<summary>mixed data value<summary>
	public function connect($dbname=null){
		if (igk_is_controller($dbname)){
			$f = igk_getv($dbname->Configs, "clSQLite3DataFile");
			$fulln = IGKIO::GetDir(igk_io_ctrl_db_dir($dbname)."/{$f}");
			$ctn = $this->getConnection($fulln);
			if (($ctn == null) || ($ctn->openCount<=0)){
					$sql = new SQLite3($fulln);
					$this->initConfig();
					$this->m_openCount = 1;
					$this->storeConnection($fulln, $sql);
					$this->fname = $fulln;
					$this->makeCurrent();
					return 1;
			}
			$ctn->openCount++;
			return 1;
		}
		
		$sql = $this->m_sql;
		if (!$sql){
			if ($this->m_creator){
				//you setup a global creator
				$o = $this->m_creator;
			
				if (igk_is_callback_obj($o)){
					$sql= igk_invoke_callback_obj(null,$o);
				}
				else if (is_object($o))
					$sql= $o->createDb($this);
			}
			else{
				if (igk_is_controller($dbname)){
					
					$f = igk_getv($dbname->Configs, "clSQLite3DataFile");
					if (empty($f)){
						igk_die("no file setup");
					}
					$fulln = IGKIO::GetDir(igk_io_ctrl_db_dir($dbname)."/{$f}");
					$sql = new SQLite3($fulln);
					$this->initConfig();
					$this->m_openCount = 1;
					$this->storeConnection($fulln, $sql);
					$this->fname = $fulln;
					return 1;
				}				
			}
			
			if ($sql==null){
				igk_die("no creator set to init the sql connection");
			}
			$this->m_sql = $sql;
			$this->initConfig();
			$thi->m_openCount = 1;
			$this->storeConnection("", $sql);
			$this->fname = "";
			return 1;
		}
		$thi->m_openCount++;
		return 0;
	}
	public function close(){
		// igk_wln("call close");
		// igk_wln(self::$sm_connexions);
		
		if (self::$sm_connexions==null)
			return;
		
		if ($this->m_current){
			$this->m_current->openCount--;
			
			if ($this->m_current->openCount<=0){
				
				$this->m_current->sql->close();
				array_shift(self::$sm_connexions);
				$this->m_current = igk_getv(self::$sm_connexions, 0);//array_shift($this->
			}
			return;
		}
		// igk_wln("after");
		// igk_wln(self::$sm_connexions);
		// return;
		// if (!empty($this->fname)){
			
			// $fulln = $this->fname;
			// $ctn = $this->getConnection($fulln);
			// $ctn->openCount--;
			
			// if ($ctn->openCount<=0){
				
				// $ctn->sql->close();
			// }
			// exit;
			
		// }
		$this->m_openCount--;
		if (($this->m_openCount>0) && ($this->m_sql)){
			$this->m_sql->close();
			$this->m_sql = null;
		}
	}
	public function CreateEmptyResult($result=null, $query=null, $info=null){

		$r =  IGKMySQLQueryResult::CreateResult($result, $query, $info);
		return $r;
	}
	public  function initSystableRequired($tablename){
		
	}
	protected function initConfig(){
		// igk_wln("init config");
		// if (igk_env_count("test_3") >1)
		// throw new Exception("kd");
		$this->makeCurrent();
		//IGKSQLManager::$Config["db"] = IGK_SQL3LITE_KN;
		//IGKSQLManager::$Config[IGK_SQL3LITE_KN]["res"] = $this->sql;
		self::StoreStack($this);
	}
	
	public static  function CreateTableQuery($tbname, $columninfo, $entries =null){
		//create a mysql table query
		$query = "CREATE TABLE IF NOT EXISTS `".igk_mysql_db_tbname($tbname)."`(";
		$tb = false;
		$primary = ""; //primary key
		$unique =""; //unique row
		$funique = ""; //unique row
		$findex ="";
		$uniques = array();
		//$unique_entity = "";
		$primkey = "";
		$tinf = array();
		$foreignkey = "";
		foreach($columninfo as $k=>$v)
		{
		
			if (($v==null) || !is_object($v)){
				igk_die(__CLASS__." ::: Error table column info is not an object error for ".$tbname);
			}
			$primkey = "noprimkey://".$v->clName;
			if ($tb)
				$query.=",";
			$v_name = igk_db_escape_string($v->clName);
			$query .= "`".$v_name."` ";
			$type = igk_getev($v->clType, "Int");//default type
			$query .= igk_db_escape_string($type);
			$s = strtolower($type);
			$number =false;
			if (isset(self::$LENGTHDATA[$s]))
			{
			
				if ($v->clTypeLength>0)
				{
					$number = true;
					$query .="(".igk_db_escape_string($v->clTypeLength).") ";
				}
				else 
					$query .= " ";
			}
			else 
				$query .= " ";
			
			//number data
			if (!$number)
			{
				if(($v->clNotNull) || ($v->clAutoIncrement))
					$query .= "NOT NULL ";
				else 
					$query .= "NULL ";				
			}
			else if ($v->clNotNull){
				$query .= "NOT NULL ";
			}
			if ($v->clAutoIncrement){
				$query .= IGKSQLManager::GetValue("auto_increment_word", $v, $tinf)." ";// "AUTO_INCREMENT ";
				
			}				
			$tb = true;
			//igk_wln("default : ".$v_name . " : ".$v->clDefault);
			if ($v->clDefault || $v->clDefault==='0'){
				$query .= "DEFAULT '".igk_db_escape_string($v->clDefault)."' ";
				//igk_wln("set ok ");
			}
			
			// if ($v->clDescription && !$nocomment)
			// {
				// $query .= "COMMENT '".igk_db_escape_string($v->clDescription)."' ";
			// }
			
			if ($v->clIsUnique){
				if (!empty($unique))
					$unique .= ",";				
					
				$unique .= "CONSTRAINT ".strtolower("constraint_".$v_name)." UNIQUE (`".$v_name."`)";
				//"UNIQUE KEY `".$v_name."` (`".$v_name."`)";
			}
			//to merge all unique column members
			if ($v->clIsUniqueColumnMember)
			{
				if (isset($v->clColumnMemberIndex))
				{
					
					$tindex = explode("-", $v->clColumnMemberIndex);
					$indexes = array();
					foreach($tindex as $kindex){
						//$ck = 'unique_'.$v->clColumnMemberIndex;
						if (!is_numeric($kindex) || isset($indexes[$kindex]))
							continue;
						// igk_wln($kindex);
						$indexes[$kindex] = 1;
						$ck = 'unique_'. $kindex;
						
						//igk_wln("for ".$ck);
						$bf ="";
						if (!isset($uniques[$ck]))
						{
							// $bf  .= "UNIQUE KEY `clUC_".$ck."_index`(`".$v_name."`";
							$bf  .= "CONSTRAINT  `clUC_".$ck."_index` UNIQUE(`".$v_name."`";
						}
						else{ 
							$bf = $uniques[$ck];
							$bf  .= ", `".$v_name."`";
						}
						$uniques[$ck] = $bf;
					}
				}
				else{
					if (empty($funique))
					{
						$funique = "CONSTRAINT `clUnique_Column_".$v_name."_idx` UNIQUE (`".$v_name."`";
						//$funique  = "UNIQUE KEY `clUnique_Column_".$v_name."_index`(`".$v_name."`";
					}
					else
						$funique  .= ", `".$v_name."`";
				}
			}
			if ($v->clIsPrimary && !isset($tinf[$primkey]))
			{
				// igk_wln("isset ".isset($tinf[$primkey]));
				// igk_wln($primkey);
				// igk_wln($tinf);
				if (!empty($primary))
				$primary .= ",";
				$primary .= "`".$v_name."`";
			}
			
			if (($v->clIsIndex || $v->clLinkType) && !$v->clIsUnique && !$v->clIsUniqueColumnMember && $v->clIsPrimary ){
				if (!empty($findex))
					$findex .= ",";
				$findex .= "KEY `".$v_name."_index` (`".$v_name."`)";
			}
			unset($tinf[$primkey]);
			
			if  ($v->clLinkType){
				$cl = igk_getv($v, "clLinkColumn", "clId");
				if (!empty($foreignkey ))
					$foreignkey .=",";
				$foreignkey .= "FOREIGN KEY ({$v_name}) REFERENCES {$v->clLinkType}({$cl})";
			
				// igk_wln($foreignkey);
				// exit;
			}
			
		}
		if (!empty($primary))
		{
			$query .=", PRIMARY KEY  (".$primary.") ";
		}
		if (!empty($unique))
		{
			$query .=", ".$unique." ";
		}
		if (!empty($funique))
		{
			$funique .= ")";
			$query .=", ".$funique." ";
		}
		if (igk_count($uniques)>0)
		{
			foreach($uniques as $k=>$v){
				$v .= ")";
				$query .=", ".$v." ";	
			}
		}
		
		if (!empty($findex))
			$query .=", ".$findex ;
		if (!empty($foreignkey)){
			
			$query .=", ".$foreignkey ;
		}
		
		$query .=")";
		// if (!$noengine)
		// $query .= ' ENGINE=InnoDB ';
		// if ($desc){
			// $query .= "COMMENT='".igk_db_escape_string($desc)."' ";
		// }
		$query.=";";
		return $query;
	}
	public function createTable($tbname, $columninfo, $entries =null, $desc=null){
		//$this->initConfig();
		$unique = array();
		$query = self::CreateTableQuery( $tbname, $columninfo, $entries);
		
		//unique contraint
		//CONSTRAINT constraint_name UNIQUE (uc_col1, uc_col2, ... uc_col_n)
		//igk_wln($query. "<br />");
		return $this->sendQuery($query,$tbname);
	}
	
	public function enableForeignKey($b){
		$s = $b?'ON': 'OFF';
		$this->sql->exec('PRAGMA foreign_keys='.$s);		
	}
	public function getDatabaseFileName(){
		$r = $this->sendQuery('PRAGMA database_list', ':global:');
		// igk_wln($r);
		$f = null;
		if ($r && ($r->RowCount == 1)){
			$f = $r->getRowAtIndex(0)->file;
		}		
		return $f;
	}
	
	public function dropAllTables(){
		$r = $this->listTables();
		if ($r){
				$this->sql->exec('PRAGMA foreign_keys=OFF');
			
				foreach($r->Rows as $k=>$v){
					if ($v->name == "sqlite_sequence"){
						$this->deleteAll("sqlite_sequence");
						continue;				
					}
					$this->dropTable($v->name);
					//igk_wln($v);
				}		
		}
		//exit;	
	}
	public function beginTransaction(){
		$this->sql->exec("BEGIN TRANSACTION");
	}
	public function rollback(){
	$this->sql->exec("ROLLBACK");
	}
	public function commit(){
		$this->sql->exec("COMMIT");		
	}	
	public function sendQuery($query, $tbname=null){
		// igk_log_write_i(__FILE__."::".__FUNCTION__."::".__LINE__, $query);
		// igk_wln("query : ".$query);
		if ($tbname == null)
			igk_die("tbname is null ");
		$r = null;
		if (preg_match("/^(INSERT|UPDATE|DELETE|CREATE|DROP)/i",$query)){
			//igk_wln($query);
			$r = @$this->sql->exec($query) ?? igk_die("query failed . ".$query . " error ".$this->sql->lastErrorMsg());
			return $r;
		}
		else
			$r =  @$this->sql->query($query) ?? igk_debug_or_local_die($this->sql->lastErrorMsg());
		if ($r && is_object($r)){
			igk_setv($r , IGK_SQL3LITE_KN_QUERY_KEY, $query);
			igk_setv($r , IGK_SQL3LITE_KN_TABLE_KEY, $tbname);
			return $this->createResult($r,$query);
		}
		if ($this->sql->lastErrorCode()==0)
			return null;
		$obj=  self::CreateEmptyResult(false, $query, array(
		"error"=>1, 
		"errormsg"=>$this->sql->lastErrorMsg()));
		return $obj;
		// return null;
	}
	
	public function escapeString ($str){
		return $this->sql->escapeString ($str);		
	}
	public function numRows(){
		return $this->sql->numRows($str);
	}
	public function dieNotConnect(){
		if ($this->sql == null)
			throw new Exception("sql3lite no connection available ");
	}
	public function createResult($r,$query=null){
		return IGKMySQLQueryResult::CreateResult($r, $query, array("source"=>$this));
	}
	public function dropTable($tbname){		
		return $this->sendQuery("Drop Table IF EXISTS   `{$tbname}`", $tbname);//, $this->DbName);			
	}
	// public function delete($tbname, $entry){
		// if (is_numeric($entry))
			// return $this->sendQuery("DELETE FROM `{$tbname}` WHERE `clId`='{$entry}';");//, $this->DbName);	
		
	// }
	public function listTables(){
		//igk_wln("list atable ");
		return $this->sendQuery("SELECT name FROM sqlite_master WHERE type='table';", "sqlite_master");
		
	}
	public function countTable($tb){
		return $this->sendQuery("SELECT Count(*) FROM `".$tb."`;", $tb);
	}
	public function getDataSchema(){
		$rep  = igk_createNode("data-schemas");
		$rep["Date"] = date('Y-m-d');
		$rep["Version"]="1.0";
		$rep["Name"]= basename($this->getDatabaseFileName());
		
		$r = $this->listTables();//("Show Tables;");	
	
		if ($r)
		{
				$n = $r->Columns[0]->name;
				$e = false;
				foreach($r->Rows as $t){
					if ($e)
					exit;	

					$table_n = $t->$n;
					if ($table_n == "sqlite_sequence")continue;
					// igk_wln("for ".$table_n);
					
					$row = $rep->addNode("DataDefinition")->setAttributes(array(
						"TableName"=>$table_n
					));
					// if ($table_n != "users")continue;
					$tinfo = array();
					$tt = $this->countTable($table_n);
					$b = $tt->Columns[0]->name;
					$row["Entries"] = $tt->Rows[0]->$b;					
					//$tt = $mysql->sendQuery("DESCRIBE `".$t->$n."`;");
					// $tt = $mysql->sendQuery("SHOW FULL COLUMNS FROM `".$table_n."`;");
					$tt = $this->sql->query("pragma table_info('{$table_n}')");
					$itt = $this->sql->query("pragma index_list('{$table_n}')");
					$ift = $this->sql->query("pragma foreign_key_list('{$table_n}')");
					//load index info
				
					$tinfo = array();
					$clinfo = (object)array();
					$uc_index = 1;
					while ($d = $itt->fetchArray(SQLITE3_NUM)){
							$info = (object)array();
							$cn = igk_getv($d , 1);
							$clt = igk_getv($d, 3);
							
							
							// igk_wln("name = ".$cn);
							// igk_wln("cltype = ".$clt);
							$por =  $this->sql->query("PRAGMA index_info('{$cn}')");
							$cl_count = 0;
							$cln = null;
							while ($rc = $por->fetchArray(SQLITE3_NUM)){
								$cln = igk_getv($rc, 2);
								
								if(!empty($info->$cln ))
									$info->$cln .="|".$clt;
								else 
									$info->$cln = $clt;
								$cl_count ++;
								if (!isset($clinfo->$cln))
								$clinfo->$cln = (object)array(
								"is_Unique"=>0,
								"is_UniqueColumnMember"=>0,
								"cl_member_index"=>0,
								"clRefType"=>null,
								"clRefColumn"=>null,
								"clInfo"=>$info);
								else{
									if (is_array($clinfo->$cln->clInfo))
										$clinfo->$cln->clInfo[] = $info;
									else{
										$clinfo->$cln->clInfo = array($clinfo->$cln->clInfo, $info);
									}
								}
							}
							
							if ($cl_count>1){
								$info->clUniqueColumnMember = 	$uc_index++;
								foreach(array_keys((array)$info) as $rmm=>$rtt){
									// igk_wln("rtt " .$rtt);
									if ($rtt=="clUniqueColumnMember")
										continue;
									$clinfo->$rtt->is_UniqueColumnMember = 1;
									
									$clinfo->$rtt->cl_member_index = (empty($clinfo->$rtt->cl_member_index) ? $info->clUniqueColumnMember : 
									$clinfo->$rtt->cl_member_index."-".$info->clUniqueColumnMember);
								}
							}else{
								if ($clt=="u"){
									$clinfo->$cln->is_Unique = 1;
								}
							}
							$tinfo[] = $info;
					}
				
					
					// while($d = $ift->fetchArray(SQLITE3_NUM)){
						// $cln = igk_getv($d, 3);//column name
						// $tn = igk_getv($d, 2);//table name
						// $refcl = igk_getv($d, 4);//ref column
						
						
						// if (isset($clinfo->$cln)){
							// $clinfo->$cln->clRefType = $tn;
							// $clinfo->$cln->clRefColumn = $refcl == "clId"?null:  $refcl;
						// }
						// else{
							
							// igk_die("not define $cln");
						// }
					// }
					
					
					//igk_wln($tinfo);
					// igk_wln($clinfo);
					//if (igk_env_count("data_c")>2) 
						//exit;
					// igk_wln("continue");
					// continue;
					
					
					$fields = array();
					if ($tt){					
						while ($d = $tt->fetchArray(SQLITE3_NUM)){
								// igk_wln($d);
						// exit;
							$fi = (object)array(
								"clName"=>igk_getv($d, 1),
								"clType"=>igk_getv($d, IGK_SQL3LITE_TYPE_NAME_INDEX),
								"clComment"=>"",
								"clAutoIncrement"=>igk_getv($d,5),
								"clDefault"=>igk_getv($d,4),
								"clNotNull"=>igk_getv($d,3)
							);
							
							
							$v = $fi;
							$cl = $row->addNode("Column");
							$cl["clName"] = $fi->clName;
							$tab = array();
							preg_match_all("/^((?P<type>([^\(\))]+)))\\s*(\((?P<length>([0-9]+))\)){0,1}$/i", 
							trim($fi->clType)
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
							$cl["clType"] = igk_sql_data_type( igk_getv($tab["type"],0, "Int"));
							$cl["clTypeLength"] = igk_getv($tab["length"],0, 0);
							// if ($v->Default)
							// $cl["clDefault"] = $v->Default;
							// if ($v->Comment){
							// $cl["clDescription"] = $v->Comment;
							$cl["clAutoIncrement"] =$v->clAutoIncrement?$v->clAutoIncrement:null;//preg_match("/auto_increment/i", $v->Extra) ? "True" : null;
							$cl["clNotNull"] = $v->clNotNull?$v->clNotNull:null;//preg_match("/NO/i", $v->Null) ? "True": null;
							$cl["clIsPrimary"] =$v->clAutoIncrement?$v->clAutoIncrement:null;// preg_match("/PRI/i", $v->Key) ? "True": null;
							
							$rinf = igk_getv($clinfo,  $fi->clName);
							$cl["clColumnMemberIndex"] = $rinf && $rinf->is_UniqueColumnMember?$rinf->cl_member_index: null;
							$cl["clIsUnique"] = $rinf && $rinf->is_Unique?1: null;
							$cl["clIsUniqueColumnMember"] = $rinf && $rinf->is_UniqueColumnMember?1: null;
							$cl["clLinkType"] = $rinf && isset($rinf->clRefType) ?$rinf->clRefType: null;
							$cl["clLinkTypeColumn"] = $rinf && $rinf->clRefColumn ?$rinf->clRefColumn: null;
						}	
					}
					$tinfo[$fi->clName] = new IGKDbColumnInfo((array)$fi);
					$fields[] = $fi;
					$tables[$table_n] = (object)array("tinfo"=>$tinfo, 'ctrl'=>"sys://mysql_db");
				}	
		}
		return $rep;
		
	}
	public function getDbIdentifier(){
		return IGK_SQL3LITE_KN;
	}
}

function igk_sql3lite_escapestring($str){	
	$sq = IGKSQLite3DataAdapter::GetCurrent();//::$Config[IGK_SQL3LITE_KN]["res"] ;
	igk_assert_die($sq->sql ===null, 'sql line is null');
	return $sq->sql->escapeString($str);	
}
function igk_sql3lite_num_rows($t){
	igk_sql3lite_fetch_row($t); //required
	//igk_sql3lite_fetch_row($t); //required
	if ($t->numColumns() && $t->columnType(0) != SQLITE3_NULL)
		{
			
			return 1;
    // have rows
		} else {
			// zero rows
			return 0;
		} 
	
}
function igk_sql3lite_num_fields($r){
	return $r->numColumns();
}
function igk_sql3lite_tosql_data($d){
// igk_wln("??... data {$d}");
if (!preg_match_all("/(?P<type>([^\(\)])+)(\((?P<number>[0-9]+)\))?/i", $d, $tab))
	return "unknown";
// igk_wln($tab);
// exit;
$d = igk_getv($tab["type"],0);

	
	switch(strtolower($d)){
		case "integer":
		case "int":
			return MYSQLI_TYPE_SHORT;
		case "text":
		case "string":
			return  MYSQLI_TYPE_STRING;
		default:
		break;
			
	}
	igk_wln("error : " .$d);
	exit;

// switch($d){
	// case SQLITE3_NULL:	
	// igk_wln("null ... data");
	// throw new Exception("kd");
			// return MYSQLI_TYPE_NULL;
		// break;
	// case SQLITE3_INTEGER:
		// return MYSQLI_TYPE_SHORT;
	// case SQLITE3_TEXT:
	// case SQLITE3_FLOAT:
			// return MYSQLI_TYPE_FLOAT;
		// break;
	
	// default:
		
		// break;
// }
// igk_wln($d);
// igk_wln(MYSQLI_TYPE_SHORT);
// igk_wln(SQLITE3_FLOAT);
	// exit;

}
function igk_sql3lite_fetch_field($r){
	$index = 0;
	$v_k = "field::/index";
	$v_inf_k = "field::/index_info";
	$index = igk_getv($r,$v_k,0);
	$v_inf = igk_getv($r,$v_inf_k,0);
	$tb = igk_getv($r, IGK_SQL3LITE_KN_TABLE_KEY);
	$ctx = IGKSQLite3DataAdapter::GetCurrent();
	$k = null;
	if ($index < $r->numColumns() ){
		
		if (!$v_inf){
			
			$v_inf = $ctx->sql->query("pragma table_info('{$tb}')");
			// igk_wln($v_inf);
			// igk_wln($tb);
			$r->$v_inf_k = $v_inf;
			// while($d = $r->$v_inf_k->fetchArray(SQLITE3_NUM)){
				// igk_wln("i ");
				// igk_wln($d);
			// }
			// throw new Exception("id");
			// exit;
		}
		$tab = $r->$v_inf_k->fetchArray(SQLITE3_NUM);
		 // igk_wln(".ok");
		// igk_wln($tab);
		// igk_wln("ok");
		//exit;
		// igk_wln("name : ".$r->columnName($index));
		$k= (object)array(
		"name"=>$r->columnName($index),
		"type"=>igk_sql3lite_tosql_data(igk_getv($tab, IGK_SQL3LITE_TYPE_NAME_INDEX)),
		"flags"=>igk_getv($tab, 5),
		"table"=>$tb,
		"primary_key"=>igk_getv($tab, 5),//null
		);
		$index++;		
		$r->$v_k = $index;
		//igk_wln($r);
	}else{
		$r->reset();
		unset($r->$v_k);
	}
	return $k;
}
function igk_sql3lite_fetch_row($r){
	//throw new Exception("d");
	return $r->fetchArray(SQLITE3_NUM);
}
function igk_sql3lite_close(){
	$sq = IGKSQLite3DataAdapter::GetCurrent();//::$Config[IGK_SQL3LITE_KN]["res"] ;
	return $sq && $sq->close();
}
function igk_sql3lite_lastid(){
	return -1;
}
function igk_sql3lite_error(){
	$c = IGKSQLite3DataAdapter::GetCurrent();
	if ($c) return $c->sql->lastErrorMsg();
	return 0;
	
}
function igk_sql3lite_error_code(){
	$c = IGKSQLite3DataAdapter::GetCurrent();
	if ($c) return $c->sql->lastErrorCode();
	return 0;
	
}

function igk_sql3lite_autoincrement($r, & $info =null){
	if ((strtolower($r->clType) == "integer") &&
		($r->clIsPrimary)){
			if ($info!==null){
				$primkey="noprimkey://".$r->clName;
				$info[$primkey] = 1;
			}
			return "primary key autoincrement";
		}
	return null;
}

//<summary>global sql 3 lite connection</summary>
function igk_sql3lite_connect(){
	igk_wln(func_get_args());
	throw new Exception("not permitted");
	
}
//init sql settings
IGKSQLManager::Init(function(& $conf){
	$n =IGK_SQL3LITE_KN;
	$conf["db"]=IGK_SQL3LITE_KN;
	$conf[$n]["func"]= array();
	$conf[$n]["auto_increment_word"] = "igk_sql3lite_autoincrement";
	
	$t = array();
	$t["connect"] = "igk_sql3lite_connect";
	$t["selectdb"] = "";
	$t["check_connect"] = "";
	$t["query"] = "igk_sql3lite_fetch_query";
	$t["escapestring"] = "igk_sql3lite_escapestring";
	$t["num_rows"] = "igk_sql3lite_num_rows";
	$t["num_fields"] = "igk_sql3lite_num_fields";
	$t["fetch_field"] = "igk_sql3lite_fetch_field";
	$t["fetch_row"] = "igk_sql3lite_fetch_row";
	$t["close"] =  "igk_sql3lite_close";
	$t["error"] =  "igk_sql3lite_error";
	$t["errorc"] = "igk_sql3lite_error_code";
	$t["lastid"] = "igk_sql3lite_lastid";
	$conf[$n]["func"]= $t;
	// $conf[$n]["func"] = $t;
	// igk_wln($conf);
	// igk_wln($t);
	// exit;
	
	
});


?>