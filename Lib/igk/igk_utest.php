<?php

if (!defined("IGK_FRAMEWORK")){
	require_once(dirname(__FILE__)."/igk_framework.php");
}

function igk_utest_append($status, $name, $message){
	
	$o = (object)array_combine(array('status', 'name', 'message'), func_get_args());
	igk_set_env_array("igk://utest/", $o);
}

function igk_utest_assert($cond, $message, $name=null){	
	if ($cond){
		igk_utest_append(1, $name,  'success');
		return 1;
	}
	igk_utest_append(2,$name,  $message);
	return 0;
}

function igk_utest_report($t){
	$at = igk_get_env("igk://utest/");
	if ($at)
	foreach($at as $k){
		$t->addDiv()->addObData(function()use($k){
			$c = igk_createNode("div");
			$tab = explode("|", "default|success|danger|warning|info");
			$c["class"] = "igk-btn +igk-".igk_getv($tab, $k->status, "default");		
				if (is_string($k->message)){
					$c->Content = ($k->name? $k->name.":":"").$k->message;
				}else if (igk_is_closure($k->message)){
					$c->addObData($k->message);
				}
			$c->RenderAJX();
		});
	}
}
///if testname==null run all test
function igk_utest_run($testname=null){
	
}


///--------------------------------------------------------------------------------------------------------
///IGK:: testing
///--------------------------------------------------------------------------------------------------------
final class IGKTestRunner extends IGKObject{
	public function RunTest($i){

		$classname = get_class($i);
		$rfclass = new ReflectionClass($classname);

		$rep = new IGKXmlNode("response");

		foreach(get_class_methods($classname) as $k=>$v)
		{
			$refmethod = new ReflectionMethod($classname, $v);
			$n = $refmethod->getName();
			switch($n)
			{
				case "__construct":
					continue;
				default:

			$t = $rep->add($n);
			if (!$i->$n($t)){
				$t["error"] = 1;
				$rep["haserror"]=1;
			}
				break;
				}
		}

		return $rep;

	}
}


?>