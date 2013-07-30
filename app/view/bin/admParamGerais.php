<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');


/** Carregar arquivo XML do form **/
$xmlData		= MCParametro::geraXmlForm();

/** Aplica a mascara nas variáveis **/
$system->mask->aplicaMascarasForm($xmlData);

/************************** Carregar template **************************/
/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('XML_DATA'		,$xmlData);
$template->assign('URL_FORM'		,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('MENSAGEM'		,null);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>
