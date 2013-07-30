<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');

/** Resgatando valores postados **/
if (isset($_GET['id'])) {
	$id = DHCUtil::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_POST["id"]);
}else{
	DHCErro::halt('Falta de Parâmetros');
}


/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);
	
if (isset($_GET['err'])){
	$err	= DHCUtil::antiInjection($_GET["err"]);
}else{
	$err 	= '';
}


if (!isset($codUsuario)) {
	DHCErro::halt('Falta de Parâmetros 2');
}

$info			= MCUsuarios::getInfo($codUsuario);
$usuario		= $info->usuario;
$nome			= $info->nome;
$email			= $info->email;
$codTipo		= $info->codTipo;


/************************** Resgatar valores do banco **************************/
/** Resgatar os Condominios **/
$conds	= MCUsuarios::getCondominiosComAcesso($codUsuario);
$oConds	= MegaCondominio::geraXmlSelect($conds, 'codCondominio', 'nomeCondominio',null);

/** Carregar arquivo XML do form **/
$xmlData	= DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/************************** Carregar template **************************/
/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('XML_DATA'		,$xmlData);
$template->assign('NOME_CONDOMINIO'	,null);
$template->assign('COND_SIM'		,$oConds);
$template->assign('COD_USUARIO'		,$codUsuario);
$template->assign('URL_FORM'		,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('ID'				,$id);
$template->assign('RECARREGAR'		,$_SERVER['REQUEST_URI']);
$template->assign('MENSAGEM'		,$err);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>