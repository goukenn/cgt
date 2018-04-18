<?php


echo "Unit test : \n";
echo "Author: C.A.D. BONDJE DOUE : \n";

$tab[] = (object)array(
	"Name"=>"InitPlugins",
	"func"=>function(){
		return 0;
	},
	"error"=>"Failed to initialize"
);

function cons_cl($cl){//set console color 
	array("green"=>"");
	
	echo chr(27)."[32m ". "babab kj d ".chr(27)."[0m";
}
function igk_utest_wln($msg, $lf="\n"){
	echo($msg.$lf);
}
function igk_utest_wl($msg){
	echo($msg." ");
}


foreach($tab as $k=>$v){	
	$fc = $v->func;
	igk_utest_wln("running : {$v->Name} ", "") ;
	
	$r = $fc();
	if ($r){
		cons_cl("green");
		igk_utest_wln("OK");
	}else{
		cons_cl("red");
		igk_utest_wln($v->error);
	}
	
}


?>