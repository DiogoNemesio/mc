<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

if (isset($_GET["mensagem"])) {
	$mensagem	= trim(htmlspecialchars_decode($_GET["mensagem"])); 
}else{
	$mensagem	= null;
}

//$system->log->debug->debug("Mensagem: ".$mensagem);

if (substr(strtoupper($mensagem),0,4) == "ERRO") {
	$tabClass	= "MCTable";
	$textClass	= "MCErro";
}else{
	$tabClass	= "MCTable";
	$textClass	= "MCTexto";
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('FORM_ACTION'		,BIN_URL.'editCondominio.php');
$template->assign('MENSAGEM'		,$mensagem);
$template->assign('TABCL'			,$tabClass);
$template->assign('TEXTCL'			,$textClass);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>