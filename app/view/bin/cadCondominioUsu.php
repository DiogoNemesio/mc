<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');


if ($system->ehAdmin($system->getUsuario()) == false) {
	$RO	= 'readonly';
}else{
	$RO	= '';
}

if (isset($_GET['err'])){
	$err	= DHCUtil::antiInjection($_GET["err"]);
}else{
	$err 	= '';
}

if (isset($_GET['codCondominio'])){
	$codCondominio		= DHCUtil::antiInjection($_GET["codCondominio"]);
}else{
	$codCondominio		= null;
}

if ($codCondominio == null) {
	echo "<script> alert('Não existe condomínio cadastrado');</script>";
	exit();
}
/** Listar Todos os Blocos de determinado condominio **/
$usuario		= MCUsuarios::lista($codCondominio);
//print_r($blocos);
$grid	= new DHCGrid('GBlocos');
//$grid->setAutoHeight(true);
$grid->setAutoWidth(true);
$grid->setSkin($system->getSkinName());
$grid->adicionaColuna('NOME'		,335	,'center'	,'ro'	,'nome');
$grid->adicionaColuna('USUÁRIO	'	,180	,'center'	,'ed'	,'usuario');
$grid->adicionaColuna(''			,40		,'center'	,'img'	,'');
$grid->adicionaColuna(''			,40		,'center'	,'img'	,'');
$grid->setPaging(10,'pagingArea','recinfoArea');
$grid->setFilter('#text_filter,#text_filter,&nbsp;,#cspan,#cspan');
$grid->loadObjectArray($usuario);

for ($i = 0; $i < sizeof($usuario); $i++) {
	$grid->setValorColuna($i,2,IMG_URL.'/edit.png^Editar Bloco^'.BIN_URL.'/cadCondominioUsuNovo.php?codUsuario='.$usuario[$i]->codUsuario.'&codCondominio='.$codCondominio.'^_self');
	$grid->setValorColuna($i,3,IMG_URL.'/remove.png^Excluir Bloco^'.BIN_URL.'/admExcBloco.php?codUsuario='.$usuario[$i]->codUsuario.'^_self');
}


/** Carregando o template html **/
$mensagem = null;
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('URL_FORM'		,$_SERVER['REQUEST_URI']);
$template->assign('CONDOMINIOS_URL'	,CONDOMINIOS_URL);
$template->assign('RO'				,$RO);
$template->assign('COD_CONDOMINIO'	,$codCondominio);
$template->assign('MENSAGEM'		,$mensagem);
$template->assign('JS_CODE'			,$grid->getHtmlCode());

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>