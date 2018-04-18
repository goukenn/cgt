<?php
//run script

require_once(dirname(__FILE__)."/igk_framework.php");
igk_display_error(1);
//configuration 
$header =<<<EOF
Balafon run_script command.
version : 1.0
author: C.A.D. BONDJE DOUE
EOF;

define("IGK_APP_DIR", realpath(dirname(__FILE__)."/../../"));
chdir(IGK_APP_DIR);
$argv = igk_getv($_SERVER, "argv");
if (!$argv){
	igk_wln("argument not valid");
	return -2;
}
$arg_c=0;
if (($arg_c = igk_count($argv))<2){
	igk_wln($header);
	igk_wln("usage : [argument not valid]");
	return -1;
}

$s = igk_getv($_SERVER,"SCRIPT_FILENAME")==__FILE__;
if ($s){
	igk_set_env("sys://func/igk_is_cmd",1);
}

$g=0;
for($i=1;$i<$arg_c;$i++){
	$f = igk_getv($argv, $i);
	if (file_exists($f)){
		$g = include_once($argv[$i]);
	}else{
		igk_wln("file not found :".$f);
	}
	if ($g)return $g;
}
unset($arg_c);

?>