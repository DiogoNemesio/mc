<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

if (isset($_GET["id"])) {
	$id	= DHCUtil::antiInjection($_GET["id"]);
}else{
	include(BIN_PATH . 'notFound.php');
	exit;
}

/** Descompacta o id **/
DHCUtil::descompactaId($id);

if (!isset($codCondominio)) {
	DHCErro::halt('Falta de parâmetros');
}

/** Resgatando as informações do condomínio **/
$info		= MCCondominio::getInfo($codCondominio);

/** Definindo a url inicial do sistema **/
$URL_FORM	= BIN_URL . 'portalSystem.php?id='.$id;

$xmlData	=	DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('XML_DATA'	,$xmlData);
$template->assign('URL_FORM'	,$URL_FORM);
$template->assign('NOME_SISTEMA',$system->config->nome);
$template->assign('MENSAGEM'	,null);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();
?>