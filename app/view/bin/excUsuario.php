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

if ( !isset($codUsuario) ) {
	DHCErro::halt('Falta de Parâmetros 2');
}

if ( !isset($codTipo) ) {
	$codTipo = null;
}

/** Resgata as informações do Usuário **/
$info	= MCUsuarios::getInfo($codUsuario);

$xmlData	=	DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Montar url para o botão voltar **/
$idVoltar	= DHCUtil::encodeUrl('codUsuario='.$codUsuario.'&codTipo='.$codTipo); 

/** Define os valores das variáveis **/
$template->assign('XML_DATA'	,$xmlData);
$template->assign('URL_FORM'	,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('NOME'		,$info->nome);
$template->assign('ID'			,$id);
$template->assign('VOLTAR'		,BIN_URL.'admCadUsuario.php?id='.$idVoltar.'&_codMenu_='.$_codMenu_.'&codCondominio='.$codCondominio);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>