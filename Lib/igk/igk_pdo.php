<?php
/*
represent a pdo data adapter. for management file
*/
if (!defined("IGK_MSQL_DB_Adapter"))
{
	include_once(dirname(__FILE__)."/igk_mysql_db.pthml");
	if (!defined("IGK_MSQL_DB_Adapter"))
		return;
}

class IGKPDODataAdpater  extends IGKDataAdapter
{
	private $m_pdo;
	
	private function __construct($pdo){
		$this->m_pdo = $pdo;
	}
	public static function Create($server, $dbname, $login, $pwd)
	{		
		return null;
	}
	public  function initSystableRequired($tablename){}
	public  function initSystablePushInitItem($tablename, $callback){}
	public  function connect($ctrl=null){
		
	}
	public  function close(){}
	
	public function CreateEmptyResult(){
		return null;
	}
}
?>