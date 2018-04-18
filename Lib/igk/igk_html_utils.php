<?php
///<summary>render directly to content</summary>
function igk_html_submit_button($value="submit", $id="submit"){
	$n = igk_createNode("input");
	$n["value"] = $value;
	$n["type"] = "submit";
	$n["class"] = "igk-btn igk-btn-default";
	$n->setId($id);	
	$n->RenderAJX();
}

function igk_html_textarea($id, $value=null){
	$g = igk_createNode("textarea");
	$g->setId($id);	
	$g->Content = $value;
	$g->RenderAJX();
}

function igk_html_title($t,$s, $level=2){
	return $t->add('h'.$level)->setContent($s);

}
function igk_html_password($t, $id ,$auto="current-password"){
	$i = $t->addInput($id, "password");
	$i["autocomplete"] = $auto;
	return $i;

}
function igk_html_login_form($ctrl, $d, $source=null){
	igk_app_load_login_form($ctrl, $d, $source);
}

function igk_html_paginate($target, $pagingHost, $tab, $maxperpage, $callback, $uri, $selected=1){
	
	$max = $maxperpage;
	$count = igk_count($tab);
	$epagination = $max<$count;
	$it = new IGKIterator($tab);
	$it->setRewindStart($maxperpage* ($selected - 1));
	foreach($it as $k=>$v){
		$callback($target, $k,$v);
		$max--;
		if ($max<0)
			break;
	}
	
	if ($epagination){
		//igk_wln("add pagination {$count}, {$perpage}, {$selected}");
		$pagingHost->addDiv()->addAJXPaginationView($uri, $count, $maxperpage, $selected);
	}		

}
///<summary>repalce uri</summary>
function igk_html_replace_uri($d, $uri){
	$d->addBalafonJS()->Content = "igk.winui.history.replace('{$uri}');";
}

function igk_html_toast($doc, $message, $type="igk-default"){
	$t = igk_createNode("singlenodeviewer", null, array("NoTagNode"));
	
	
	$n=$t->targetNode->addToast();
	$n["class"]="{$type}";
	$n->Content = $message;
	$doc->body->add($t);	
	return $t;
}


function igk_html_dump($obj){
	$t = igk_createNode("div");
	$t->addDiv()->Content = "Object: ";
	
	$tq = array(array($obj, $t));
	while($q = array_pop($tq)){
		
		$dv = $q[1]->addDiv();
		foreach($q[0] as $k=>$s){
			if (is_object($s) || is_array($s)){
				$dv->addLabel()->Content = $k;
				$ts = $dv->addDiv()->setStyle("margin-Left:32px; position:relative;");				
				array_push($tq, array($s, $ts->addDiv()));
			}else{
				$ul = $dv->add("ul");
				$li = $ul->add("li");
				$li->addLabel()->Content = $k." : ";
				$li->addSpan()->Content = $s;
			}
		}
		
	}
	
	return $t;
}

///<include view inline>
// function igk_html_include_view($target, $viewfile, $args){
	// extract($args);
	// $t = $target;
	// include($viewfile);
// }

function igk_html_form_initfield($frm){
	$frm->addInput("confirm", "hidden", "1");
	$frm->addInput("cref", "hidden", igk_app()->Session->getCRef());
}

function igk_html_server_info(){
	
	//R::ngets(
	$srv = "<div style='font-size:1.6em; padding:10px; background-color:#fefefe; border:1px solid :#ddd; color:#444;' >".igk_s('Server Info')."</div>";
	
	$srv.="<table>";
	foreach($_SERVER as $k=>$v){
		$srv.="<tr>";
		$srv.="<td>".$k;
		$srv.="</td>";
		$srv.="<td>".$v;
		$srv.="</td>";
		$srv.="</tr>";					
	}
	$srv.="</table>";
	return $srv;
}



?>