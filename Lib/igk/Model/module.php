<?php

///<summary>used to install a module</summary>
$reg('install',function (){
	igk_wln("start install....!!!!!");
	igk_wln($this->Name);
});

//<summary>used to uninstall a module</summary>
$reg("uninstall", function (){ });

?>