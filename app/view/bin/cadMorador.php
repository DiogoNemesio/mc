<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');


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

if (!isset($codCondominio)){
	echo "<script> alert('Não existe condomínio cadastrado');</script>";
	DHCErro::halt('Falta de Parâmetros 2');
}

/************************** Restagatar valores do banco **************************/

/** Listar Todos os moradores de determinado condominio **/
$moradores		= MCMorador::lista($codCondominio);
$grid	= new DHCGrid('GMoradores');
$grid->setAutoHeight(true);
$grid->setAutoWidth(true);
$grid->setSkin($system->getSkinName());
$grid->adicionaColuna('UNIDADE'		,140	,'center'	,'ro'	,'descUnidade');
$grid->adicionaColuna('NOME'		,340	,'center'	,'ro'	,'nome');
$grid->adicionaColuna('FONE'		,140	,'center'	,'ro'	,'fone');
$grid->adicionaColuna('SEXO'		,120	,'center'	,'ro'	,'descSexo');
$grid->adicionaColuna(''			,30		,'center'	,'img'	,'');
$grid->adicionaColuna(''			,30		,'center'	,'img'	,'');
$grid->setPaging(10,'pagingArea','recinfoArea');
$grid->setFilter('#select_filter,#text_filter,#text_filter,#select_filter,&nbsp;,#cspan');
$grid->loadObjectArray($moradores);

for ($i = 0; $i < sizeof($moradores); $i++) {
	$id 	= DHCUtil::encodeUrl('codMorador='.$moradores[$i]->codMorador.'&codCondominio='.$codCondominio);
	$grid->setValorColuna($i,4,IMG_URL.'/edit.png^Editar Morador^'.BIN_URL.'/editMorador.php?id='.$id.'^_self');
	$grid->setValorColuna($i,5,IMG_URL.'/remove.png^Excluir Morador^'.BIN_URL.'/excMorador.php?id='.$id.'^_self');
}

$addID 	= DHCUtil::encodeUrl('codMorador=&codCondominio='.$codCondominio);

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('URL_FORM'		,$_SERVER['REQUEST_URI']);
$template->assign('JS_CODE'			,$grid->getHtmlCode());
$template->assign('ID'				,$id);
$template->assign('ADDID'			,$addID);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>