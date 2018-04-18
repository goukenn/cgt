<?php
class IGKProductType extends IGKControllerBase
{
	public function getDataTableName(){
		return "tbigk_producttypes";
	}
	public function getIsVisible(){
		return false;
	}
	public function getIsSystemController(){
		return true;
	}
	public function getCanEditDataTableInfo()
	{
		return false;
	}
	public function getDataTableInfo()
	{
		return array(
			new IGKdbColumnInfo(array(IGK_FD_NAME=>"clId", IGK_FD_TYPE=>"Int", "clAutoIncrement"=>true)),
			new IGKdbColumnInfo(array(IGK_FD_NAME=>IGK_FD_NAME, IGK_FD_TYPE=>"VARCHAR", IGK_FD_TYPELEN=>255, "clIsUnique"=>true, "clIsPrimary"=>true))		
		);
	}
}
?>