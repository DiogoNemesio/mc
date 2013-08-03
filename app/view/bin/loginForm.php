<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Carregando o $tpl html **/
$tpl	= new \Zage\Template();
$tpl->load(\Zage\Util::getCaminhoCorrespondente(__FILE__, \Zage\ZWS::EXT_HTML));

if (!isset($mensagem)) {
	$mensagem	= null;
}


if (isset($_GET['info'])) {
	$info = \Zage\Util::antiInjection($_GET['info']);
}else{
	DHCErro::halt('Falta de Parâmetros');
}

/** Descompactar as variáveis **/
\Zage\Util::descompactaId($info);

if (!isset($URL_FORM)) {
	die('Falta de Parâmetros 2');
}

$xmlData	=	MegaCondominio::getXmlData(\Zage\Util::getCaminhoCorrespondente(__FILE__, \Zage\ZWS::EXT_XML,\Zage\ZWS::CAMINHO_ABSOLUTO));

/** Define os valores das variáveis **/
$tpl->set('XML_DATA'		,$xmlData);
$tpl->set('URL_FORM'		,$URL_FORM);
$tpl->set('NOME_SISTEMA'	,$system->config["nome"]);
$tpl->set('MENSAGEM'		,$mensagem);
$tpl->set('SB_MESSAGE'		,"Megacondomínio todos os direitos reservados");
$tpl->set('FORM_SKIN_NAME'	,$system->getFormSkinName());



/** Por fim exibir a página HTML **/
$tpl->show();

?>