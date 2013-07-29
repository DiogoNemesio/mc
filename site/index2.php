<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('include.php');
}


/**
 * Resgata a resolução
 */
if (isset($_GET["w"]))	$clientW	= $_GET["w"];
if (isset($_GET["h"]))	{
	$clientH	= $_GET["h"];
}else{
	$clientH	= 600;
}

$altura = $clientH - 425;
if ($altura < 360) $altura = 360;


/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(SITE_HTML_PATH . 'index2.html');

/** Define os valores das variáveis **/
$template->assign('ALTURA'		,$altura);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>
