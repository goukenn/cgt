<?php
abstract class IGKAppleIconCtrl extends IGKCtrlTypeBase
{
	public function getCanAddChild(){
		return false;
	}
	public function getisVisisble(){
		return true;
	}
	protected function initTargetNode()
	{
		
	}
	public static function GetAdditionalConfigInfo()
	{
		return array(
			"clAppleIconUri"=>igk_createAdditionalConfigInfo(array("clRequire"=>1)),
			"clAppleTouchIconType"=> new IGKAdditionCtrlInfo("select", array(
				"apple-touch-icon"=>"apple-touch-icon",
				"apple-touch-icon-precomposed"=>"apple-touch-icon-precomposed"
			))
			
		);
	}
	public static function SetAdditionalConfigInfo(& $t)
	{
		$t["clAppleIconUri"] = igk_getr("clAppleIconUri");
		$t["clAppleTouchIconType"] = igk_getr("clAppleTouchIconType");
	}
	protected function InitComplete()
	{
		parent::InitComplete();
		
		$tab = $this->getAppleIcon();
		$c = igk_count($tab) ;
		$regex = "/\.(?P<name>(([0-9]+)x([0-9]+))*)/i";
		if ($c == 1)
		{
			$lnk = $this->app->Doc->addLink("apple-touch-icon");
			$lnk["rel"] = $this->Configs->clAppleTouchIconType;	
			$v = $this->Configs->clAppleIconUri;
			
				if (preg_match( $regex, $v))
				{
					preg_match_all($regex, $v, $t);
					$lnk["sizes"]= $t["name"][0];
				}		
				
			$lnk["href"]= "?vimg=".$v;
		}
		else{
			$i = 0;
			
			foreach($tab as $k=>$v)
			{
				$lnk = $this->app->Doc->addLink("apple-touch-icon:".$i);
				$lnk["rel"] = $this->Configs->clAppleTouchIconType;		
				if (preg_match( $regex, $v))
				{
					preg_match_all($regex, $v, $t);
					$lnk["sizes"]= $t["name"][0];
				}
				$lnk["href"]= "?vimg=".$v;
				$i++;
			}
		}
	}
	public function getAppleIcon()
	{
		$tb = explode(',', $this->Configs->clAppleIconUri);
		return $tb;
	}
	public function View()
	{
		
	}
}
?>