<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

if (!isset($mensagem)) {
	$mensagem	= null;
}


if (isset($_GET['id'])) {
	$id = DHCUtil::antiInjection($_GET['id']);
}else{
	DHCErro::halt('Falta de Parâmetros');
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);

if (!isset($codBloco)) {
	DHCErro::halt('Falta de Parâmetros 2');
}

/** Resgata as informações do Blocl **/
$info	= MCBloco::getInfo($codBloco);

$xmlData	=	DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Montar url para o botão voltar **/
$idVoltar	= base64_encode('codBloco='.$codBloco.'&codCondominio='.$codCondominio); 

/** Url botão voltar **/
$urlVoltar	= BIN_URL . "cadCondominioBlo.php?id=".$idVoltar;

/** Define os valores das variáveis **/
$template->assign('XML_DATA'	,$xmlData);
$template->assign('PKG_PATH'	,PKG_PATH);
$template->assign('XML_PATH'	,XML_PATH);
$template->assign('URL_FORM'	,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('NOME'		,$info->nomeBloco);
$template->assign('ID'			,$id);
$template->assign('VOLTAR'		,$urlVoltar);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>