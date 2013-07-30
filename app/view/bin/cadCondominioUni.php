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

/** Listar Todos os Blocos de determinado condominio **/
$unidades		= MCUnidade::lista($codCondominio);
$grid	= new DHCGrid('GUnidades');
$grid->setAutoHeight(true);
$grid->setAutoWidth(true);
$grid->setSkin($system->getSkinName());
$grid->adicionaColuna('BLOCO'		,160	,'center'	,'ro'	,'nomeBloco');
$grid->adicionaColuna('UNIDADE	'	,160	,'center'	,'ro'	,'nome');
$grid->adicionaColuna('TIPO'		,130	,'center'	,'ro'	,'tipoUnidade');
$grid->adicionaColuna('RESPONSAVEL'	,180	,'center'	,'ro'	,'nomeResponsavel');
$grid->adicionaColuna('RAMAL'		,80		,'center'	,'ro'	,'ramal');
$grid->adicionaColuna(''			,30		,'center'	,'img'	,'');
$grid->adicionaColuna(''			,30		,'center'	,'img'	,'');
$grid->setPaging(10,'pagingArea','recinfoArea');
$grid->setFilter('#select_filter,#text_filter,#select_filter,#text_filter,#text_filter,&nbsp;,#cspan');
$grid->loadObjectArray($unidades);

for ($i = 0; $i < sizeof($unidades); $i++) {
	$id 	= base64_encode('codBloco='.$unidades[$i]->codBloco.'&codCondominio='.$codCondominio.'&codUnidade='.$unidades[$i]->codUnidade);
	$grid->setValorColuna($i,5,IMG_URL.'/edit.png^Editar Bloco^'.BIN_URL.'/cadCondominioUniEdit.php?id='.$id.'^_self');
	$grid->setValorColuna($i,6,IMG_URL.'/remove.png^Excluir Bloco^'.BIN_URL.'/excUnidade.php?id='.$id.'^_self');
}

$addID 	= base64_encode('codBloco=&codCondominio='.$codCondominio.'&codUnidade=');

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