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


if (isset($_GET['nomeCondominio'])) 		$nomeCondominio			= DHCUtil::antiInjection($_GET['nomeCondominio']);


if ((isset($codUsuario)) and (isset($nomeCondominio))) {
   /**
 	* Resgatar os condomínios
 	*/
	$conds		= MCUsuarios::getCondominiosSemAcesso($codUsuario,$nomeCondominio);
	$xmlObj			= new DHCXMLConDataView();
	$xmlObj->loadArray($conds);
	//$system->log->debug->debug("XML: ".$xmlObj->getXML());
	
	print $xmlObj->getXML();
}
