<?php


///<summary>Use to edit a template</summary>
final class IGKTemplateEditor extends IGKControllerBase{

		public function __construct(){
			parent::__construct();
		}
		
		///<summary>get tempory folder</summary>
		public function getTempFolder(){
			return $this->getDeclaredDir()."/temp";
		}
		///<summary>call this function edit a controller</summary>
		public function Edit($ctrl){
			//
			if(!$this->can_edit($ctrl)){
				return;
			};
			//
			
			
		}
		///<summary>cancel edition of the controller</summary>
		public function Cancel($ctrl){
			
		}
}




?>