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


if (isset($_GET['info'])) {
	$info = DHCUtil::antiInjection($_GET["info"]);
}else{
	DHCErro::halt('Falta de Parâmetros (LoginWindow)');
}


/** Define os valores das variáveis **/
$template->assign('INFO'		,$info);
$template->assign('NOME_SISTEMA',$system->config->nome);
$template->assign('MENSAGEM'	,$mensagem);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>