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

if (!isset($codCondominio)){
	echo "<script>alert('Erro variável codCondominio perdida !!!!');</script>";
	DHCErro::halt('Falta de Parâmetros (COD_CONDOMINIO)');
}

/** Listar Todos os Espaços de determinado condominio **/
$espacos		= MCEspaco::lista($codCondominio);
$grid	= new DHCGrid('MCGrid');
$grid->setAutoHeight(true);
$grid->setAutoWidth(true);
$grid->setSkin($system->getSkinName());
$grid->adicionaColuna('NOME'		,120	,'center'	,'ro'	,'nome'					);
$grid->adicionaColuna('DESCRIÇÃO'	,250	,'center'	,'ro'	,'descricao'			);
$grid->adicionaColuna('TEMPO MÁX'	,100	,'center'	,'ro'	,'tempoMaximo'			,'MCMask-tempo');
$grid->adicionaColuna('VALOR'		,150	,'center'	,'ro'	,'valor'				,'MCMask-money');
$grid->adicionaColuna(''			,40		,'center'	,'img'	,''						);
$grid->adicionaColuna(''			,40		,'center'	,'img'	,''						);
$grid->setPaging(10,'pagingArea','recinfoArea');
$grid->setFilter('#text_filter,#text_filter,&nbsp;,#cspan,#cspan');
$grid->loadObjectArray($espacos);

for ($i = 0; $i < sizeof($espacos); $i++) {
	$id 	= base64_encode('codEspaco='.$espacos[$i]->codEspaco.'&codCondominio='.$codCondominio);
	$grid->setValorColuna($i,4,IMG_URL.'/edit.png^Editar Espaço^'.BIN_URL.'/editEspaco.php?id='.$id.'^_self');
	$grid->setValorColuna($i,5,IMG_URL.'/remove.png^Excluir Espaço^'.BIN_URL.'/excEspaco.php?id='.$id.'^_self');
}

$id 	= base64_encode('codCondominio='.$codCondominio);

/** Carregando o template html **/
$mensagem = null;
$template	= new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH.$system->config->defGridHtml);

/** Define os valores das variáveis **/
$template->assign('URLADD'			,BIN_URL.'editEspaco.php?id='.$id);
$template->assign('COD_CONDOMINIO'	,$codCondominio);
$template->assign('MENSAGEM'		,$mensagem);
$template->assign('GRID_OBJ'		,'MCGrid');
$template->assign('GRID_TITLE'		,'Cadastro de Espaço');
$template->assign('GRID_LARGURA'	,836);
$template->assign('GRID_ALTURA'		,400);
$template->assign('JS_CODE'			,$grid->getHtmlCode());


/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>