<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

$mensagem	= 'Página não encontrada';


/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('MENSAGEM'		,$mensagem);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>