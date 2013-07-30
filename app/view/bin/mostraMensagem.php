<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

if (isset($_GET["mensagem"])) {
	$mensagem	= DHCUtil::antiInjection($_GET["mensagem"]); 
}else{
	$mensagem	= null;
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('MENSAGEM'		,$mensagem);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>