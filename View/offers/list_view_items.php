<?php
//
//view in list item
//resume offer type list 1
//
if (!function_exists("append_to"))
{
	function append_to(& $s, $v, $sep=','){
		if (empty($v)){
			return;
		}
		if (empty($s))
			$s .= $v;
		else
			$s = $sep.$v;
	}
}

$t = igk_createNode('div')->setclass("disptabr");

$img = igk_cgt_get_service("img");
$img->setContent(1);
// $g = $img->getImage($row->codeCgt.";w=64;h=48;");
// if ($g){
	$dv = $t->addDiv()->setStyle("width:120px");
	$dv->addXmlNode("img")->setAttribute("src", $img->getImageUri($row->codeCgt)); //igk_html_inlinedata("image/jpeg", $g));
// }

$d = $t->addDiv()->setClass("disptabc name");



if ($uri)
	$d = $d->addAJXA($uri, "#result", "GET");


// igk_wln($row);
// exit;
igk_html_title($d->addDiv(), $row->nom, 3);
$address = "";
append_to($address, igk_conf_get($row, "spec/[urn=:urn:fld:adr]/value"), ", ");

// igk_wln(igk_conf_get($row->adresse1, "[lang=de]/localite/value"));
// exit;
//$address = implode( ", ", array( $address, null));// igk_conf_get($row, "adresse1/cp")));
$address .=  ($f = igk_conf_get($row, "adresse1/localite/[lang=fr]/value")) ? ", {$f} " : "";
$address .=  ($f = igk_conf_get($row, "adresse1/commune/[lang=fr]/value")) ? "- ({$f}) " : "";

$t->addDiv()->setClass("disptabc address")->Content = $address;

// $t->addDiv()->setClass("desc")->Content = igk_conf_get($row, "spec/[urn=urn:fld:descmarket10]/value");


// $t->addDiv()->setClass("smallresume")->Content = igk_conf_get($row, "spec/[urn=urn:fld:adr]/value");


// var_dump(igk_conf_get($row,"spec"));
// exit;

$t->RenderAJX();

?>