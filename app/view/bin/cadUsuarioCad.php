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

if (!isset($codUsuario)) 	$codUsuario	= null;
if (!isset($codTipo)) 		$codTipo	= null;

/** Altera o nome da variável para evitar conflito de nomes **/
$codTipoList		= $codTipo;

if ($codUsuario != null) {
	$info			= MCUsuarios::getInfo($codUsuario);
	$usuario		= $info->usuario;
	$nome			= $info->nome;
	$email			= $info->email;
	$codTipo		= $info->codTipo;
	$codStatus		= $info->codStatus;
}else{
	$usuario		= '';
	$nome			= '';
	$email			= '';
	$codTipo		= null;
	$codStatus		= 'A';
}


/************************** Restagatar valores do banco **************************/
/** Resgatar os Tipos **/
$tipos	= MCUsuarios::listaTipoUsuario($codTipoList);
$oTipos	= MegaCondominio::geraXmlCombo($tipos, 'codTipo', 'descricao', $codTipo);

/** Resgatar os Status **/
$status		= MCUsuarios::listaTipoStatus();
$oStatus	= MegaCondominio::geraXmlCombo($status, 'codTipo', 'descricao', $codStatus);

/** Carregar arquivo XML do form **/
$xmlData	= DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Aplica a mascara nas variáveis **/
$system->mask->aplicaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));


/************************** Carregar template **************************/
/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('TIPOS'			,$oTipos);
$template->assign('STATUS'			,$oStatus);
$template->assign('XML_DATA'		,$xmlData);
$template->assign('URL_FORM'		,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
//$template->assign('FORM_ACTION'		,BIN_URL.'editUsuario.php');
$template->assign('ID'				,$id);
$template->assign('COD_USUARIO'		,$codUsuario);
$template->assign('USUARIO'			,$usuario);
$template->assign('NOME'			,$nome);
$template->assign('EMAIL'			,$email);
$template->assign('COD_TIPO'		,$codTipo);
$template->assign('MENSAGEM'		,$err);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>