<?php
if (defined('SITE_ROOT')) {
	include_once(SITE_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(SITE_HTML_PATH . 'condominios.html');

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>