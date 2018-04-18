<?php

$d = igk_createNode("div");
$d->addDiv()->addSectionTitle(5)->Content = "get available searching query list";

function show_spec($ul, $ta){
	$i = 0;
	foreach($ta as $k=>$v){
		$ul->add('li')->Content = $i ."/{$k}: ".igk_conf_get($v , "label/[lang=fr]/value"). " ".igk_getv($v,"urn");
		$i++;
	}
}


$d->addObData(function(){
	
	$g = igk_cgt_get_service("thesaurus");
	
	$g->setContent(0);
	
	
	// $tab = $g->getUrnList(268);
	// igk_wln($tab);
	// return;
	
	
	
	// $gh = $g->sendQuery("typeofr/1/urn:cat:resume");
	// $gh = $g->sendQuery("typeofr/1/urn:fld:adr");
	//$gh = $g->sendQuery("typeofr/268/urn:typ:268");
	$gh = $g->sendQuery("typeofr/268/urn:val:etatedit:20");
	$ob = $g->toObject($gh);
	igk_wl("<div style='padding-bottom:32px'>");
	igk_html_dump($ob)->RenderAJX();
	igk_wl("</div>");
	//var_dump($ob);
	
	
	// //$g->get
	// $s = $g->getTypeof(1);
	// //$s = $g->getFieldList();
	
	
	
	// $ob = $g->toObject($s);
	// //var_dump($ob);
	// // igk_wln(igk_conf_get($ob, "spec/label"));
	
	// $d = igk_createNode("ul");
	// $d->add("li")->Content = igk_conf_get($ob, "spec/label/[lang=fr]/value");
	
	// $spec =  igk_conf_get($ob, "spec/spec");
	// $i = 0;
	// foreach($spec as $k){
		// $c = -1;
		// $ta = igk_conf_get($k, "spec");
		// if ($ta){
			// $c = igk_count($ta);
		// }
		
		// $li = $d->add("li");
		// $li->Content = $i . " : ".igk_conf_get($k , "label/[lang=fr]/value"). "(".$c.") - ".$k->urn;
		
		// if ($c!=-1){
			// $tul = $li->add("ul");
			// show_spec($tul, $ta);
		// }
		
		
		// $i++;
	// }
	
	// $d->RenderAJX();
	
	
	
});


// $d->RenderAJX();

$doc = igk_get_document("avail", true);
$doc->body->addBodyBox()->add($d);


$doc->RenderAJX();

igk_unset_document($doc);

?>