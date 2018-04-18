<?php
//author : C.A.D. BONDJE DOUE
//view: list


exit;


$g = igk_cgt_get_service("thesaurus", 1);

get_header();

$r = $n->addRow();
$dv = $r->addCol("fitw")->addDiv();
$dv->addRow()->addCol()->addDiv()->setStyle("font-size:12pt")->addObData(function(){
dynamic_sidebar("cgt_search_zone");
dynamic_sidebar("cgt_filter_zone");
});

$g->setContent(2);

$xmlc = $g->getAllTypes();



///manual object transformation
$tab = igk_cgt_q_to_object($xmlc, "thesaurus", function(){});
//select zone
$rdv = $dv->addRow();
$dv = $rdv->addCol("igk-col-4-1")->addDiv();
if ($tab){
	$dv->addObData(function()use($tab){
		
		$c = igk_createNode("select");
		$c["style"]="height:auto; border-radius: initial;";	
		$c["class"]="igk-btn";
		foreach($tab->spec as $k=>$v){		
			$o = $c->add("option");
			$o["value"]=$v->order;
			$o->Content = $v->label->value;
		}
		$srv=igk_cgt_get_service("query");
		$c->setAttribute("onchange", "javascript:ns_igk.ajx.get('".$srv->getUri("getOffers/")."'+this.value,null,'#result'); return false");
		$c->RenderAJX();
	});


}

//query result
$srv=igk_cgt_get_service("query");
$m = igk_cgt_get_env("cgt://offdata");
$dr = $rdv->addCol("igk-col-4-3")->addDiv()->setId("result");
$id = $page; //1;
if (empty($m)){
	
	$c = IGK_CGT_PLUGIN_DIR."/Articles/offers/search/home/{$id}";
	if (file_exists($c)){
		$dr->addObData(function()use($c,$page){					
			include($c);
			$d = igk_createNode("div");			
			//$d->addPaginationView("/List/?page=",100,10,$page)->setClass("igk-pull-right");
			$d->RenderAJX();
		});
	}
	else
		$dr->addAJXUriLoader($srv->getUri("viewBaseOffer/{$offertype}&page={$page}&buri=".base64_encode("/List/?page=")));
}
else{
	$dr->addObData($m);
}
?>