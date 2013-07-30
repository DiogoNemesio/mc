<?php
header("Content-type:text/xml");
print("<?xml version=\"1.0\"?>");

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

if (isset($_GET['parent'])) 	$estado 	= DHCUtil::antiInjection($_GET['parent']);
if (isset($_GET['cidade'])) 	$cidade		= DHCUtil::antiInjection($_GET['cidade']);


if (isset($estado)) {
   /**
 	* Resgatar as cidades
 	*/
	$cidades			= $system->DBGetCidades($estado);
	$xml	= "<complete>\n";
	
	for ($i = 0; $i < sizeof($cidades); $i++) {
		if ($cidade == $cidades[$i]->codCidade) {
			$sel = " selected='true'";
		}else{
			$sel = "";
		}
		$xml .= "<option$sel value='".$cidades[$i]->codCidade."'>".$cidades[$i]->nomeCidade."</option>\n";
	}
	
	$xml	.= "</complete>\n";
}
echo $xml;
