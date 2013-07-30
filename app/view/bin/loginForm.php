<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

if (!isset($mensagem)) {
	$mensagem	= null;
}


if (isset($_GET['info'])) {
	$info = DHCUtil::antiInjection($_GET['info']);
}else{
	DHCErro::halt('Falta de Parâmetros');
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($info);

if (!isset($URL_FORM)) {
	DHCErro::halt('Falta de Parâmetros 2');
}

$xmlData	=	DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Define os valores das variáveis **/
$template->assign('XML_DATA'	,$xmlData);
$template->assign('URL_FORM'	,$URL_FORM);
$template->assign('NOME_SISTEMA',$system->config->nome);
$template->assign('MENSAGEM'	,$mensagem);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>