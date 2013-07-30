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

$condominios		= MCCondominio::lista();

$grid	= new DHCGrid('GCondominio');
$grid->setAutoWidth(true);
$grid->setSkin($system->getSkinName());
$grid->adicionaColuna('IDENTIFICADOR'	,220	,'center'	,'ro'	,'condominio');
$grid->adicionaColuna('NOME'			,380	,'center'	,'ed'	,'nomeCondominio');
$grid->adicionaColuna('UNIDADES'		,120	,'center'	,'ed'	,'qtdeUnidades');
$grid->adicionaColuna(''				,40		,'center'	,'img'	,'');
$grid->adicionaColuna(''				,40		,'center'	,'img'	,'');
$grid->setPaging(10,'pagingArea','recinfoArea');
$grid->setFilter('#text_filter,#text_filter,#select_filter,&nbsp;,#cspan');
$grid->loadObjectArray($condominios);

for ($i = 0; $i < sizeof($condominios); $i++) {
	$id     = base64_encode("codCondominio=".$condominios[$i]->codCondominio."&_codMenu_=".$_GET['_codMenu_']);
	$grid->setValorColuna($i,3,IMG_URL.'/edit.png^Editar Condomínio^'.BIN_URL.'/editCondominio.php?id='.$id.'^_self');
	$grid->setValorColuna($i,4,IMG_URL.'/remove.png^Excluir Condomínio^'.BIN_URL.'/admExcCondominio.php?id='.$id.'^_self');
}

$addID	= base64_encode("_codMenu_=".$_GET['_codMenu_']);

/** Define os valores das variáveis **/
$template->assign('FORM_ACTION'		,$_SERVER['REQUEST_URI']);
$template->assign('JS_CODE'			,$grid->getHtmlCode());
$template->assign('ID'				,$addID);
$template->assign('MENSAGEM'		,null);


//$template->assign('JS_CODE'			,null);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();
