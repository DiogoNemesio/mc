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

if (!isset($codCondominio)) {
	echo "<script>alert('Erro variável codCondominio perdida !!!!');</script>";
	DHCErro::halt('Falta de Parâmetros (COD_CONDOMINIO)');
}

/** Listar Todos os Blocos de determinado condominio **/
$blocos		= MCBloco::lista($codCondominio);
//print_r($blocos);
$grid	= new DHCGrid('GBlocos');
$grid->setAutoHeight(true);
$grid->setAutoWidth(true);
$grid->setSkin($system->getSkinName());
$grid->adicionaColuna('NOME'		,120	,'center'	,'ro'	,'nomeBloco');
$grid->adicionaColuna('DESCRIÇÃO'	,250	,'center'	,'ed'	,'descricao');
$grid->adicionaColuna('SÍNDICO'		,250	,'center'	,'ed'	,'nomeSindico');
$grid->adicionaColuna(''			,40		,'center'	,'img'	,'');
$grid->adicionaColuna(''			,40		,'center'	,'img'	,'');
$grid->setPaging(10,'pagingArea','recinfoArea');
$grid->setFilter('#text_filter,#text_filter,&nbsp;,#cspan,#cspan');
$grid->loadObjectArray($blocos);

for ($i = 0; $i < sizeof($blocos); $i++) {
	$id 	= base64_encode('codBloco='.$blocos[$i]->codBloco.'&codCondominio='.$codCondominio);
	$grid->setValorColuna($i,3,IMG_URL.'/edit.png^Editar Bloco^'.BIN_URL.'/cadCondominioBloEdit.php?id='.$id.'^_self');
	$grid->setValorColuna($i,4,IMG_URL.'/remove.png^Excluir Bloco^'.BIN_URL.'/excBloco.php?id='.$id.'^_self');
}

$id 	= base64_encode('codCondominio='.$codCondominio);

/** Carregando o template html **/
$mensagem = null;
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('URL_FORM'		,$_SERVER['REQUEST_URI']);
$template->assign('CONDOMINIOS_URL'	,CONDOMINIOS_URL);
$template->assign('COD_CONDOMINIO'	,$codCondominio);
$template->assign('MENSAGEM'		,$mensagem);
$template->assign('ID'				,$id);
$template->assign('JS_CODE'			,$grid->getHtmlCode());

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>