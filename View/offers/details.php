<?php
//details
if ($row == null){
	return;
}




$specs = igk_cgt_get_spec($row->offre);

// igk_wln($tab);
// exit;


$dv = igk_createNode("div");
$dv["class"]="offer details";
// $dv->addDiv()->setStyle("width:100%; height:400px; background-color:#444")->addImg()->setSrc();
$img_z = $dv->addDiv();
$tab = $dv->add('div');


$lang = igk_cgt_current_lang();

//primary name details
foreach($keys as $k=>$v){
	$tab->addDiv()->setClass("inf ".strtolower($k))->Content = igk_conf_get($row, $v);
}
$dv->add("span")->Content = igk_conf_get($row, "offre/typeOffre/label/[lang={$lang}]/value");	

$img_o = $dv->addGallery();

// $mail = igk_conf_get($row->offre, "spec/[urn=urn:fld:urlweb]/value");
// if ($mail){
	// $tab->addA($mail)->Content = $mail;
// }

// ob_start();
// igk_wln($row->offre);
// $c = IGKOb::Content();
// igk_io_save_file_as_utf8_wbom("d:/temp/type.txt", $c);
// ob_clean();


$type = $row->offre->typeOffre->idTypeOffre;
$c = igk_cgt_get_conf(
"widget/offers/{$type}/details/spec", //detail specification from configuration
(object)array(	//default specification
	"urn:fld:descmarket10"=>["class"=>'desc'],
	"urn:fld:adr"=>["class"=>'adr'],
	"urn:fld:urlweb"=>["class"=>'uri'],
	"urn:fld:class"=>["class"=>'cl']	
)
);

// igk_wln($c);
// exit;



foreach($c as $k=>$v){
	igk_cgt_append_block ($dv,  igk_cgt_get_view_spec($type, igk_getv($specs, $k)), "spec ".igk_getv($v, "class"));
}

if (isset($row->offre->relOffre)){
	$specs = $row->offre->relOffre;
	$img = igk_cgt_get_service("img");
	$img->setContent(1);
	
	
	if (is_array($specs)){//spect
		foreach($specs as $k=>$v){				
			$s = $this->toObject($this->getOfferDetails($v->offre->codeCgt));				
			$g = igk_getv(igk_cgt_get_spec($s->offre),"urn:fld:url");
			if ($g){
				$url = $g->value;
				// $img_z->add("li")->addObData(function(){
					 // echo do_shortcode("[gallery ids='10']");//10{$url}']";
				// });
				$img_o->addPicture($url, basename($url));			
			}
		}
	
	}else{
		//$s = $img->getImage($row->offre->codeCgt);	
		$g = $img->getImage($row->offre->codeCgt);
		if (!empty($g)){
			$img_o->addPicture(igk_html_inlinedata("image/jpeg", $g));
		}
		else 
			igk_html_rm($img_o);
	}
}
$c = igk_ob_get_func(array($dv, 'renderAJX'));
igk_wl($c);		
?>