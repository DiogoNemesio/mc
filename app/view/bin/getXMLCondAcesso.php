<?php
header("Content-type:text/xml");
print("<?xml version=\"1.0\"?>");

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Resgatando valores postados **/
if (isset($_GET['id'])) {
	$id = DHCUtil::antiInjection($_GET["id"]);
}else{
	echo "Requisição inválida !!";
	exit;
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);


if (isset($codUsuario)) {
   /**
 	* Resgatar os condomínios
 	*/
	$conds		= MCUsuarios::getCondominiosComAcesso($codUsuario);
	$xmlObj			= new DHCXMLConDataView();
	$xmlObj->loadArray($conds);
	//$system->log->debug->debug("XML: ".$xmlObj->getXML());
	
	print $xmlObj->getXML();
}
