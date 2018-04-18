<?php

// if (!class_exists("FPDF")){
	
	// echo "class not exist ..... ";
	// return;
// }



final class IGKPDF extends IGKObject
{
	private $m_fpdf;
	private $m_title;
	private $m_author;
	private $m_subject;
	private $m_keywords;
	
	public function getFpdf (){
		return $this->m_fpdf;
	}
	public function __construct($type="P", $unit="mm", $format="A4")
	{		
		$this->init($type,$unit, $format);
	}
	//-------------------------------------------------------------------
	//set properties 
	//-------------------------------------------------------------------
	public function setTitle($value){$this->m_title = $value;}
	public function setAuthor($value){$this->m_author = $value;}
	public function setSubject($value){$this->m_subject = $value;}
	public function setKeywords($value){$this->m_keywords = $value;}
	public function setXY($x, $y){ $this->FPDF->SetXY($x, $y); }
	public function setMargin($left, $top, $right){		$this->FPDF->SetMargins($left, $top, $right);	}
	public function setWidth($w){	$this->FPDF->SetLineWidth($w);	}
	public function setFontSize($w){	$this->FPDF->SetFontSize($w);	}
	public function setFont($name, $type, $size){	$this->FPDF->SetFont($name, $type, $size);	}
	//get title
	//-------------------------------------------------------------------
	public function getTitle(){ return $this->m_title; }
	public function getAuthor(){ return $this->m_author; }
	public function getSubject(){ return $this->m_subject; }
	public function getKeywords(){ return $this->m_keywords; }
	public function getStringWidth($s){ return $this->FPDF->GetStringWidth($s); }
	public function getX(){ return $this->FPDF->GetX(); }
	public function getY(){ return $this->FPDF->GetY(); }
	public function getPageNo(){ return $this->FPDF->PageNo(); }

	
	public function save(){
		$this->m_fpdf->save();
	}
	public function restore(){
			$this->m_fpdf->restore();
	}
	public function setTransform($m11, $m12, $m21, $m22, $offsetx, $offsety){
		$this->m_fpdf->setTransform(array(
			$m11, $m12,
			$m21, $m22,
			$offsetx, $offsety));
	}
	public function rotate($angle){
		$this->m_fpdf->Rotate($angle);
	}
	public function init($type="P", $unit="mm", $format="A4", $firstpage=true)
	{
		$this->m_fpdf = new IGKFPDF($type, $unit,$format);
		$this->m_fpdf->SetFont("Arial", IGK_STR_EMPTY, 12);
		if ($firstpage){
		//add first page
		$this->addPage();
		}
	}
	
	public function addPage()
	{
		$this->m_fpdf->AddPage();
	}
	public function renderPage()
	{
		$this->m_fpdf->renderPage();
	}
	
	public function addCPage($type="P", $format="A4"){//add custom page. $type=P or L , $format = "A4" or size 
		$this->m_fpdf->AddPage($type,$format);
	}
	public function getScreenW(){
		return $this->m_fpdf->w - ($this->m_fpdf->rMargin + $this->m_fpdf->lMargin );
	}
	public function getScreenH(){
		return $this->m_fpdf->h - ($this->m_fpdf->tMargin + $this->m_fpdf->bMargin );
	}
	public function addBr($h=null)
	{
		$this->m_fpdf->Ln($h);
	}
	public function addFont($fn, $style=IGK_STR_EMPTY, $file=null)
	{
		$this->m_fpdf->AddFont($fn,$style, $file);
	}
	
	public function drawImg($file, $x=null, $y=null,$w=0,$h=0,$type=null,$link=null)
	{
		$this->FPDF->Image($file, $x,$y,$w,$h,$type,$link);
	}
	public function setfColorw($webcolor)
	{
		$cl = IGKColorf::FromString($webcolor);
		$this->setfColorf($cl->R, $cl->G, $cl->B);
	}
	public function setfColorf($r,$g=null,$b=null)
	{
		$this->FPDF->SetFillColor($r*255, $g?$g*255:$g,$b?$b*255:$b);
	}
	public function setdColorw($webcolor)
	{
		$cl = IGKColorf::FromString($webcolor);
		$this->setdColorf($cl->R, $cl->G, $cl->B);
	}
	public function setdColorf($r,$g=null,$b=null)
	{
		$this->FPDF->SetDrawColor($r*255, $g?$g*255:$g,$b?$b*255:$b);
	}
	public function settColorf($r,$g=null,$b=null)
	{
		$this->FPDF->SetTextColor($r*255, $g?$g*255:$g,$b?$b*255:$b);
	}
	public function drawLineSeparatorw($webcolor){
		$this->setdColorw($webcolor);
		// var_dump($this->FPDF);
				
		$this->drawLine($this->getX(), $this->getY(), $this->FPDF->w - $this->FPDF->rMargin, $this->getY());
	}
	public function settColorw($webcolor)
	{
		$cl = IGKColorf::FromString($webcolor);
		$this->settColorf($cl->R, $cl->G, $cl->B);
	}
	
	public function drawLine($x1, $y1, $x2, $y2)
	{
		$this->m_fpdf->Line($x1, $y1, $x2, $y2);
	}
	///<summary>draw cell text with width and height specified. note that x and y update for next location</summary>
	public function drawCText($w, $h, $text, $border=0, $ln=1, $align= "L", $fill=false , $link=null)
	{
		$this->m_fpdf->Cell($w, $h, $text, $border, $ln, $align, $fill, $link);
	}
	///<summary>draw text at position</summary>
	public function drawText($text, $x, $y)	{
		$this->m_fpdf->Text($x, $y, $text);
	}
	public function drawWText($h, $text , $link=null)	{//draw wide text. $height, $text, link
		$this->m_fpdf->Write($h, $text, $link);
	}
	public function drawMultiTextCell($w, $h, $text , $border=0,  $align="L", $fillcolor=false)
	{
		$this->m_fpdf->MultiCell($w, $h, $text , $border,  $align, $fillcolor);
	}
	public function drawRect($x,$y,$w,$h){
		$this->FPDF->Rect($x,$y,$w,$h);
	}
	public function fillRect($x,$y,$w,$h){
		$this->FPDF->Rect($x,$y,$w,$h, 'F');
	}
	public function drawLink($x,$y,$w,$h, $link){
		$this->FPDF->Rect($x,$y,$w,$h, $link);
	}
	//link funciton
	//-
	public function createLink()
	{
		return $this->FPDF->AddLink();
	}
	public function setLink($id, $y=0, $p=-1 )
	{
		return $this->FPDF->SetLink($id, $y,$page);
	}
	public function drawImage($imgfileObject, $x, $y){
		if (file_exists($imgfileObject)){
			$this->FPDF->Image($imgfileObject, $x, $y);			
		}
	}
	//to end 
	public function Render($name="pdfdocument.pdf", $dest="I")
	{
		$this->m_fpdf->title = $this->Title;
		$this->m_fpdf->author = $this->Author;
		$this->m_fpdf->subject = $this->Subject;
		$this->m_fpdf->keywords = $this->Keywords;
		$this->m_fpdf->Output($name, $dest);		
	}
}


///<summary> used to write pdf</summary>
final class IGKPDFDbWriter extends IGKObject
{
	private $m_pdf;
	public function getPDF(){return $this->m_pdf; }
	public static function Create(){
		$pdf = new IGKPDF();
		$pdrr = new IGKPDFDbWriter($pdf);
		return $pdrr;
	}
	public function Render($name="pdfdocument.pdf", $dest="I"){
		$this->m_pdf->Render($name, $dest);
	}
	public function __construct($pdf){
		$this->m_pdf = $pdf;
	}
	public function addRow($r, $height=20, $measure=null){
		$i = 0;
		foreach($r as $k=>$v){
			$w = igk_getv($measure, $i, 12);
			$this->m_pdf->drawCText($w, $height, utf8_decode($v),0,0);
			$i++;
		}
		$this->m_pdf->addBr();
	}
}



class IGKFPDF extends FPDF
{

	function _putinfo(){//change the put info function
		$this->_out('/Producer '.$this->_textstring('IGKWEB '.IGK_VERSION));
		if(!empty($this->title))
			$this->_out('/Title '.$this->_textstring($this->title));
		if(!empty($this->subject))
			$this->_out('/Subject '.$this->_textstring($this->subject));
		if(!empty($this->author))
			$this->_out('/Author '.$this->_textstring($this->author));
		if(!empty($this->keywords))
			$this->_out('/Keywords '.$this->_textstring($this->keywords));
		if(!empty($this->creator))
			$this->_out('/Creator '.$this->_textstring($this->creator));
		$this->_out('/CreationDate '.$this->_textstring('D:'.@date('YmdHis')));
	}
	public function save(){
		//save the current graphic state
		$this->_out('q');
	}

	public function ScaleX($s_x, $x='', $y=''){
	$this->Scale($s_x, 100, $x, $y);
	}
	public function ScaleY($s_y, $x='', $y=''){
	$this->Scale(100, $s_y, $x, $y);
	}
	public function ScaleXY($s, $x='', $y=''){
	$this->Scale($s, $s, $x, $y);
	}
	function Scale($s_x, $s_y, $x='', $y=''){
	if($x === '')
	$x=$this->x;
	if($y === '')
	$y=$this->y;
	if($s_x == 0 || $s_y == 0)
	$this->Error('Please use values unequal to zero for Scaling');
	$y=($this->h-$y)*$this->k;
	$x*=$this->k;
	//calculate elements of transformation matrix
	$s_x/=100;
	$s_y/=100;
	$tm[0]=$s_x;
	$tm[1]=0;
	$tm[2]=0;
	$tm[3]=$s_y;
	$tm[4]=$x*(1-$s_x);
	$tm[5]=$y*(1-$s_y);
	//scale the coordinate system
	$this->setTransform($tm);
	}

	function MirrorH($x=''){
	$this->Scale(-100, 100, $x);
	}
	function MirrorV($y=''){
	$this->Scale(100, -100, '', $y);
	}
	function MirrorP($x='',$y=''){
	$this->Scale(-100, -100, $x, $y);
	}
	function MirrorL($angle=0, $x='',$y=''){
	$this->Scale(-100, 100, $x, $y);
	$this->Rotate(-2*($angle-90),$x,$y);
	}

	function TranslateX($t_x){
	$this->Translate($t_x, 0, $x, $y);
	}
	function TranslateY($t_y){
	$this->Translate(0, $t_y, $x, $y);
	}
	function Translate($t_x, $t_y){
		//calculate elements of transformation matrix
		$tm[0]=1;
		$tm[1]=0;
		$tm[2]=0;
		$tm[3]=1;
		$tm[4]=$t_x*$this->k;
		$tm[5]=$t_y*$this->k;
		//translate the coordinate system
		$this->setTransform($tm);
	}

	public function Rotate($angle, $x='', $y=''){
	if($x === '')
	$x=$this->x;
	if($y === '')
	$y=$this->y;
	$y=($this->h-$y)*$this->k;
	$x*=$this->k;
	//calculate elements of transformation matrix
	$tm[0]=cos(deg2rad($angle));
	$tm[1]=sin(deg2rad($angle));
	$tm[2]=-$tm[1];
	$tm[3]=$tm[0];
	$tm[4]=$x+ ($tm[1]*$y)-($tm[0]*$x);
	$tm[5]=$y- ($tm[0]*$y)-($tm[1]*$x);
	//rotate the coordinate system around ($x,$y)
	$this->setTransform($tm);
	}

	function SkewX($angle_x, $x='', $y=''){
	$this->Skew($angle_x, 0, $x, $y);
	}
	function SkewY($angle_y, $x='', $y=''){
	$this->Skew(0, $angle_y, $x, $y);
	}
	function Skew($angle_x, $angle_y, $x='', $y=''){
	if($x === '')
	$x=$this->x;
	if($y === '')
	$y=$this->y;
	if($angle_x <= -90 || $angle_x >= 90 || $angle_y <= -90 || $angle_y >= 90)
	$this->Error('Please use values between -90° and 90° for skewing');
	$x*=$this->k;
	$y=($this->h-$y)*$this->k;
	//calculate elements of transformation matrix
	$tm[0]=1;
	$tm[1]=tan(deg2rad($angle_y));
	$tm[2]=tan(deg2rad($angle_x));
	$tm[3]=1;
	$tm[4]=-$tm[2]*$y;
	$tm[5]=-$tm[1]*$x;
	//skew the coordinate system
	$this->setTransform($tm);
	}

	public function setTransform($tm){	
		//igk_wln("backkkkk");
		//$tm[5] is ajusted flor gdi management
		$s = sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', $tm[0],$tm[1],$tm[2],$tm[3],$tm[4], -$tm[5]);
		//igk_show_prev($tm);
		//igk_wln($s);
		$this->_out($s);//sprintf('%.3F %.3F %.3F %.3F %.3F %.3F '.$unit, $tm[0],$tm[1],$tm[2],$tm[3],$tm[4],$tm[5]));
		//$this->renderPage();
	}
	public function renderPage(){
		igk_wln("buffer :".$this->buffer);
		igk_wln("page :".	$this->pages[$this->page] );
	}
	public function _out($s)
	{
		parent::_out($s);
		
	}

	public function restore(){
		//restore previous graphic state
		$this->_out('Q');
	}
}
?>