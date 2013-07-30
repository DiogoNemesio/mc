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

$info = base64_encode("URL_FORM=".$_SERVER['REQUEST_URI']."&mensagem=".$mensagem);

/** Define os valores das variáveis **/
$template->assign('PKG_PATH'	,PKG_PATH);
$template->assign('XML_PATH'	,XML_PATH);
$template->assign('INFO'		,$info);
$template->assign('NOME_SISTEMA',$system->config->nome);
$template->assign('MENSAGEM'	,$mensagem);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>