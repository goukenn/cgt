<?php

$n = igk_createNode("div");

igk_html_title($n->addDiv(), "Edit filter demonstration");

// $n->addDiv()->addObData(function(){
	// echo "<code >";
	// var_dump(igk_cgt_get_group_array());
	// echo "</code >";
// });



//filter list of group








$n->addObData(function(){

include(IGK_CGT_INSTALLDIR."/Test/test_editfilter.php");
});

$doc = igk_app()->Doc;

$uf = igk_io_baseUri(IGK_BALAFON_JS_CORE_FILE);

$doc->addScript(IGK_BALAFON_JS_CORE_FILE);
$doc->addTempScript(IGK_CGT_INSTALLDIR."/Scripts/.cgt.script.js");


$doc->addTempStyle(IGK_CGT_INSTALLDIR."/Styles/cgt.css");


//$doc->body->addScript($uf);
$doc->body->addBodyBox()
->clearChilds()
->add($n);

$doc->RenderAJX();

$doc->Dispose();
igk_exit();
?>