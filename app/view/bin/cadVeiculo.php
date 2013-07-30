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

/** Listar Todos os veiculos de determinado condominio **/
$veiculos		= MCVeiculo::lista($codCondominio);
$grid	= new DHCGrid('GVeiculos');
$grid->setAutoHeight(true);
$grid->setAutoWidth(true);
$grid->setSkin($system->getSkinName());
$grid->adicionaColuna('UNIDADE	'	,140	,'center'	,'ro'	,'nome');
$grid->adicionaColuna('MARCA'		,160	,'center'	,'ro'	,'descMarca');
$grid->adicionaColuna('MODELO'		,200	,'center'	,'ro'	,'modelo');
$grid->adicionaColuna('COR'			,120	,'center'	,'ro'	,'cor');
$grid->adicionaColuna('PLACA'		,100	,'center'	,'ro'	,'placa');
$grid->adicionaColuna(''			,30		,'center'	,'img'	,'');
$grid->adicionaColuna(''			,30		,'center'	,'img'	,'');
$grid->setPaging(10,'pagingArea','recinfoArea');
$grid->setFilter('#select_filter,#select_filter,#select_filter,#select_filter,#text_filter,&nbsp;,#cspan');
$grid->loadObjectArray($veiculos);

for ($i = 0; $i < sizeof($veiculos); $i++) {
	$id 	= DHCUtil::encodeUrl('codVeiculo='.$veiculos[$i]->codVeiculo.'&codCondominio='.$codCondominio);
	$grid->setValorColuna($i,5,IMG_URL.'/edit.png^Editar Veículo^'.BIN_URL.'/editVeiculo.php?id='.$id.'^_self');
	$grid->setValorColuna($i,6,IMG_URL.'/remove.png^Excluir Veículo^'.BIN_URL.'/excVeiculo.php?id='.$id.'^_self');
}

$addID 	= DHCUtil::encodeUrl('codVeiculo=&codCondominio='.$codCondominio.'&codUnidade=');

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