<?php


// if (isset($_REQUEST["cgt-e-filter"])){
// igk_wln($_REQUEST);
// igk_exit();	
// }


	
$tab= array();
$tab["urn:fld:typeofr"] = igk_cgt_q_op("in", explode(',', "11")); 
$tab["urn:fld:adrloc"] = igk_cgt_q_op("equal", "Liege");


// $g = igk_cgt_get_offers_from_key('QRY-1G-0000-000A', $tab, "cgt:offres/cgt:offre/cgt:spec", "current()/@urn");
// igk_wln($g);

// igk_wln("list theaurus data");

$admin = CGT_Admin::getInstance();
$groupname  = 'hebergement';


// igk_wln(" ff: ".igk_cgt_js_filter_data($groupname));

// igk_html_view("sectiontitle", "the data ".$s);
// igk_html_view("panelBox", function()use($mv){
	// igk_html_view("code", $mv);
// });

// igk_html_view("div", function()use($bn){
	// igk_html_view("code", function()use($bn){
		// var_dump($bn);
	// });
// });

	


igk_wln("<div>".$groupname."</div>");
$admin->action_getfilter_form(1, $groupname);




?>