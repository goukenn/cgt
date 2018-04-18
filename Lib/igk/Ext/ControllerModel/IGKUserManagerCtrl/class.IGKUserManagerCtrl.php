<?php
//represent a client user manager controller
abstract class IGKUserManagerCtrl extends IGKCtrlTypeBase
{	
	private $m_user;
	
	public function getUser(){return $this->m_user; }
	public function View(){
		parent::View();
	}
	protected function InitComplete(){
		parent::InitComplete();
	}
	
	public function getDataTableName(){
		return parent::getDataTableName();
	}
	protected function getDefaultDataTableInfo(){//get default data tbale infor
		return array(
		new IGKdbColumnInfo(array(IGK_FD_NAME=>"clId", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>11, "clIsUnique"=>true, "clIsPrimary"=>true, "clAutoIncrement"=>true)),
		new IGKdbColumnInfo(array(IGK_FD_NAME=>"clLogin", IGK_FD_TYPE=>"VARCHAR", IGK_FD_TYPELEN=>60, "clIsUnique"=>true,"clDescription"=>"email for login")),
		new IGKdbColumnInfo(array(IGK_FD_NAME=>"clPwd", IGK_FD_TYPE=>"VARCHAR", IGK_FD_TYPELEN=>40,"clDescription"=>"password")),
		new IGKdbColumnInfo(array(IGK_FD_NAME=>"clAvailable", IGK_FD_TYPE=>"Int", IGK_FD_TYPELEN=>1 ,"clDefault"=>1,"clDescription"=>"0=not available, 1=available, 2=blocked or other reason")),
		new IGKdbColumnInfo(array(IGK_FD_NAME=>"clFirstName", IGK_FD_TYPE=>"VARCHAR", IGK_FD_TYPELEN=>60,"clDescription"=>"the first name")),
		new IGKdbColumnInfo(array(IGK_FD_NAME=>"clLastName", IGK_FD_TYPE=>"VARCHAR", IGK_FD_TYPELEN=>60, "clDescription"=>"the last name")),
		new IGKdbColumnInfo(array(IGK_FD_NAME=>"clDescription", IGK_FD_TYPE=>"Text", "clDescription"=>"description that mark is user"))
		);
	}
	public function initDataEntry($dbman, $tbname=null)
	{
		$tb = $this->getDataTableName();
		$tabInfo = $this->getDataTableInfo();
		$obj = igk_db_getobj($tabInfo);
		$obj->clLogin = "bondje.doue@igkdev.be";
		$obj->clPwd = "e2e34fb624962538094758f2ac061637";
		$obj->clFirstName = "Charles";
		$obj->clLastName = "BONDJE DOUE";
		$obj->clAvailable = 1;		
		$dbman->Insert($tb, (array)$obj);	
		$obj = igk_db_getobj($tabInfo);
		$obj = igk_db_getobj($this->getDataTableInfo());
		$obj->clLogin = "info@igkdev.be";
		$obj->clPwd = "e2e34fb624962538094758f2ac061637";
		$obj->clAvailable = 1;		
		$dbman->Insert($tb, (array)$obj);
		$obj = igk_db_getobj($tabInfo);		
		$obj->clLogin = "test@igkdev.be";
		$obj->clPwd = "e2e34fb624962538094758f2ac061637";
		$obj->clAvailable = 0;		
		$dbman->Insert($tb, (array)$obj);		
	}
	public function getIsUserConnected(){
		return ($this->m_user != null);
	}
	public function connect($login=null, $pwd=null){
		$login = ($login==null)?igk_getp("clLogin", $login):$login;
		$pwd = ($pwd==null)?igk_getp("clPwd", $pwd):$pwd;
		$obj = igk_db_getobj($this->getDataTableInfo());
		$obj->clLogin = $login;
		$obj->clPwd = $pwd;
		
		IGKApp::$DEBUG = true;
		$s = igk_db_select_where($this, array("clLogin"=>$login, "clPwd"=>$pwd));
		igk_wln(mysql_error());
		
		
		
		if (($s) && ($s->getRowCount() == 1))
		{
			$obj = $s->getRowAtIndex(0);
			$this->m_user = $obj;
			$this->App->Session->User = $obj;
			return true;
		}
		else{
			igk_notifyctrl()->addErrorr("err.connectionfailed");
			igk_wln("connection failed");
		}
		return false;
	}

	public function logout(){
		if ($this->m_user !=null)
		{
			$this->m_user = null;
			$this->App->Session->User = null;
		}
	}
	public function resetPwd(){
		if ($this->getIsUserConnected())
		{
			$this->m_user->clPwd = md5("clPwd");
			igk_db_update($this, $this->getDataTableName(), (array)$this->m_user, array("clId"=>$this->m_user->clId));
		}
	}
	public function updateUserInfo()
	{
		if (!$this->m_user)
			return false;
		$obj = igk_getr_obj();
		foreach($obj as $k=>$v)
		{
			if (isset($this->m_user->$k))
			{
				$this->m_user->$k = $v;
			}
		}
		igk_show_prev($this->m_user);
		igk_db_update($this, $this->getDataTableName(), (array)$this->m_user, array("clId"=>$this->m_user->clId));		
		igk_notifyctrl()->addMsgr("msg.userinfo.updated");
		return true;
	}
	public function connect_frame(){
		$this->setCurrentView("connexion", true);
		$frame = igk_add_new_frame($this, "connexion");
		$frame->Title = R::ngets("title.connexion");
		$frame->Render();
	}
	public function register_frame(){
		$this->setCurrentView("register", true);
	}
}
?>