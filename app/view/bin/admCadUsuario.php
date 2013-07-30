<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');

/** Resgatando valores postados **/
if (isset($_GET['codTipo'])) {
	$codTipo 	= DHCUtil::antiInjection($_GET["codTipo"]);
}else{
	$codTipo	= null;
}

if (isset($_GET['id'])) {
	$id = DHCUtil::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_POST["id"]);
}else{
	$id	= null;
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

if ($codTipo !== null) {
	$usuarios		= MCUsuarios::listaPorTipo($codCondominio, $codTipo);
}else{
	$usuarios		= MCUsuarios::lista();
}

if (!isset($codCondominio))	$codCondominio	= null;

$grid	= new DHCGrid('GUsuarios');
$grid->setAutoWidth(true);
$grid->setSkin($system->getSkinName());
$grid->adicionaColuna('USUARIO'		,200	,'center'	,'ro'	,'usuario');
$grid->adicionaColuna('NOME'		,360	,'center'	,'ed'	,'nome');
$grid->adicionaColuna('TIPO'		,180	,'center'	,'ed'	,'descricao');
$grid->adicionaColuna('STATUS'		,100	,'center'	,'ro'	,'status');
$grid->adicionaColuna(''			,40		,'center'	,'img'	,'');
$grid->adicionaColuna(''			,40		,'center'	,'img'	,'');
$grid->setPaging(10,'pagingArea','recinfoArea');
$grid->setFilter('#text_filter,#text_filter,#select_filter,#select_filter,&nbsp;,#cspan');
$grid->loadObjectArray($usuarios);

for ($i = 0; $i < sizeof($usuarios); $i++) {
	$id     = base64_encode("codUsuario=".$usuarios[$i]->codUsuario."&_codMenu_=".$_GET['_codMenu_']."&codTipo=".$codTipo.'&codCondominio='.$codCondominio);
	$grid->setValorColuna($i,4,IMG_URL.'/edit.png^Editar Usuário^'.BIN_URL.'/editUsuario.php?id='.$id.'^_self');
	$grid->setValorColuna($i,5,IMG_URL.'/remove.png^Excluir Usuário^'.BIN_URL.'/excUsuario.php?id='.$id.'^_self');
}

$addID	= base64_encode("_codMenu_=".$_GET['_codMenu_']."&codTipo=".$codTipo);

/** Define os valores das variáveis **/
$template->assign('FORM_ACTION'		,$_SERVER['REQUEST_URI']);
$template->assign('JS_CODE'			,$grid->getHtmlCode());
$template->assign('ID'				,$addID);
$template->assign('MENSAGEM'		,null);


//$template->assign('JS_CODE'			,null);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();