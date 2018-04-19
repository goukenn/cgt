<?php

//disable post request uri
global $wp_query;
unset($wp_query->queried_object);
$wp_query->parse_query(array("page"=>"", "pagename"=>"details", "post"=>""));
$wp_query->queried_object = (object)["post_type"=>""];

// igk_wln($wp_query->get_queried_object());

// igk_wln("is data ? ". !isset($wp_query->queried_object));
// igk_wln($wp_query);
// igk_exit();
get_header();

$uri = igk_io_request_entry();
$params = array_slice(explode("/", $uri), 2);

$t->clearChilds();
$t->add("style")->setAttribute("type", "text/css")->Content = <<<EOF
.igk-winui-gallery{	
	height:auto;
	overflow:hidden;
}
.igk-winui-gallery:after{
	display:table;
	clear:both;
	content:' ';
}
.igk-winui-gallery .bx{	
	width:25%;
	height:200px;
	padding:10px;
	transition: padding .2s ease-in;
	vertical-align:bottom;
	float:left;
}
.igk-winui-gallery img{	
	object-fit:cover;	
	float:left;
	width:100%;	
	vertical-align:bottom;
}
.igk-winui-gallery .bx:hover{
	padding:5px;
}
.offer.details{
	text-decoration:none;
}
.offer.details .inf.name{
	font-size:1.1em;
	font-weight:900;
}
EOF;

//view details of the offer
$srv = igk_cgt_get_service("crud");

$t->addObData(function()use($srv, $params){
	$code = igk_getv($params, 0);
	$srv->viewOfferDetails($code);		
});

?>