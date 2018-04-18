<?php


///<summary>represent a communication controller base</summary>
abstract class IGKCommunicationServerCtrl extends IGKAppCtrl{

	const HTTP_ACCEPT = "text/event-stream";
	
	///<summary>override this to handle server</summary>
	public abstract function handle();
	public abstract function sendmsg();
	
	private function getSocketFile(){
		return $this->getDataDir()."/server.socket";
	}
	
	
	
}
?>