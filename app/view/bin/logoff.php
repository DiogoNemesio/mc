<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('MENSAGEM'	,'Obrigado por utilizar o sistema !!!');
$template->assign('URL_FORM'	,ADM_URL);
$template->assign('NOME_SISTEMA',$system->config->nome);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

session_unset();
session_destroy();
unset($system);
