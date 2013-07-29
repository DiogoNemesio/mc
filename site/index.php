<?php
if (defined('SITE_ROOT')) {
	include_once(SITE_ROOT . 'include.php');
}else{
	include_once('include.php');
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(SITE_HTML_PATH . 'index.html');

/** Define os valores das variáveis **/
$template->assign('SITE_URL'	,SITE_URL);
$template->assign('SITE_IMG_URL',SITE_IMG_URL);
$template->assign('ICON_IMG'	,ICON_IMG);
$template->assign('CHARSET'		,$system->config->charset);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();
?>