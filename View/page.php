<?php
///
///file: page.php
///desc: used to handle page view of cgt plugins
///


//get offert type in query section 



// igk_wln($qsrv);
// $n->RenderXml();

//test : offer types from criteria
// igk_cgt_detect_criteria_offer_types('QRY-1G-0000-0003');
// igk_wln(igk_show_trace());
// igk_exit();


$pagename = get_query_var('pagename'); 		// get query page name view
$page = max(1, get_query_var("page", 1)); 	// get query page for pagination
$offertype=1; 								// default offer type


// igk_wln($pagename);
// igk_exit();

		
//build view
$t = igk_createNode("div");
$t["class"]= "wrap";
$d = ["avail"=>1];
$fname = strtolower($pagename);


switch($fname){
	default:
		$f = dirname(__FILE__)."/".$fname.".php";
		igk_cgt_set_env(IGK_CGT_GROUPNAME, $fname);
		if (file_exists($f)){			
			include($f);
		}else{
			//default view page
			get_header();
			// igk_wln("call");
			//initial page
			$c = igk_getv(igk_cgt_get_group(), $fname);
			igk_html_title($t->addDiv(), $fname);
			$row = $t->addRow();
			
			
			//search zone
			$d = igk_createNode("NoTagNode");
			$d->addObData(function()  use(& $content){
				igk_cgt_invoke_sidebar("cgt_search_zone");
				$content = IGKOb::Content();
			});
			$full=4;
			if (!empty($content)){
				$d = $row->addCol("igk-col-4-1 no-print")->addDiv()->add($d);				
				$full--;
			}
			
			// igk_wln("data".$content);
			// exit;
			
			$d = $row->addCol("igk-col-4-".$full);
			if ($c && isset($c->offers)){
				$g = explode("," , $c->offers);
			}
			igk_cgt_set_env("cgt://current_group", $fname);
			$d->addDiv()->addObData(function(){				
				igk_cgt_invoke_sidebar("cgt_resume_zone");
			});
			
		}
		break;
}

$t->RenderAJX();
igk_js_init();
get_footer();
igk_exit();
?>