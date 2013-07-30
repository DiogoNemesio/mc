<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

if (isset($_GET["codCondominio"])) {
	$codCondominio	= DHCUtil::antiInjection($_GET["codCondominio"]);
}else{
	include(BIN_PATH . 'notFound.php');
	exit;
}
if (isset($_GET["w"])) {
	$w			= DHCUtil::antiInjection($_GET["w"]);
	$headerW	= round($w * 0.36) . 'px';
}else{
	$w			= "100%";
	$headerW	= "760px";
}
if (isset($_GET["h"])) {
	$h			= DHCUtil::antiInjection($_GET["h"]);
}else{
	$h	= "100%";
}

$info		= MCCondominio::getInfo($codCondominio);
$parametros	= MCCondParametro::lista($codCondominio);

/** Verificando o Skin do Condomínio **/
$codSkin		= $parametros->codSkin;
if (!$codSkin)	{
	$codSkin 	= MCParametro::getValor('CODSKIN');
}

$urlLogin		= ROOT_URL;
$infoUrl		= base64_encode("URL_FORM=$urlLogin");

$system->setSkin($codSkin);


$id 			= DHCUtil::encodeUrl('codCondominio='.$codCondominio.'&w='.$w.'&h='.$h);

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('NOME'			,$info->nomeCondominio);
$template->assign('MCW'				,$w.'px');
$template->assign('MCH'				,$h.'px');
$template->assign('HEADERW'			,$headerW);
$template->assign('INFO'			,$infoUrl);
$template->assign('ID'				,$id);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();
?>