<?php
if (defined('SITE_ROOT')) {
	include_once(SITE_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

$grid	= new DHCGrid('GServicos');
$grid->setAutoWidth(true);
$grid->setAutoHeight(true);
$grid->setSkin($system->config->skin);
$grid->adicionaColuna('PLANO'			,520	,'left'	,'ro'	,'Serviços');


/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(SITE_HTML_PATH . 'servicos.html');

/** Define os valores das variáveis **/
$template->assign('GRID'		,$grid->getHtmlCode());

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>