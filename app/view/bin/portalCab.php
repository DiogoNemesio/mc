<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

if (isset($_GET["id"])) {
	$id	= DHCUtil::antiInjection($_GET["id"]);
}else{
	include(BIN_PATH . 'notFound.php');
	exit;
}

/** Descompacta o id **/
DHCUtil::descompactaId($id);

if (!isset($codCondominio)) {
	DHCErro::halt('Falta de parâmetros');
}

$info		= MCCondominio::getInfo($codCondominio);


/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('MENSAGEM'		,$info->nomeCondominio);
$template->assign('MCW'				,$w.'px');
$template->assign('MCH'				,$h.'px');
$template->assign('ID'				,$id);
$template->assign('COND_IMG'		,'predios.gif');

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();
?>