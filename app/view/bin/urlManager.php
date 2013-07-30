<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

if (isset($_GET["url"])) {
	$url	= DHCUtil::antiInjection($_GET["url"]);
}else{
	include(BIN_PATH . 'notFound.php');
	exit;
}

$url = trim(str_replace("/", null, $url));
//$system->log->debug->debug("UrlManager: URL = $url");

if (!$url) {
	/** Acesso ao Site **/
	include_once (SITE_ROOT . 'index.php');
	exit;
}else{
	/** Verifica se a Url é de um condomínio existente **/
	$info	= MCCondominio::getInfo(null,$url);
}

if (!isset($info->codCondominio) ||  (!$info->codCondominio)) {
	include(BIN_PATH . 'notFound.php');
	exit;
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('IDENTIFICACAO'	,$info->condominio);
$template->assign('COD_CONDOMINIO'	,$info->codCondominio);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();
?>