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

/** Resgatando as informações do condomínio **/
$info		= MCCondominio::getInfo($codCondominio);

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('ALTURA'		,($h-320));
$template->assign('MENSAGEM'	,null);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();
?>