<?php 

#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

#################################################################################
## Verifica se o usuário está autenticado e eh administrador
#################################################################################
include_once(BIN_PATH . 'auth.php');
if (!$system->ehAdmin($system->getUsuario())) DHCErro::halt('Permissão Negada !!!');


#################################################################################
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = DHCUtil::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_POST["id"]);
}else{
	DHCErro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
DHCUtil::descompactaId($id);

#################################################################################
## Resgatar as aplicações existentes
#################################################################################
$app		= new sysApp();
$gridData	= $app->lista();

#################################################################################
## Criar o Grid
#################################################################################
$grid	= new DHCGrid('MCGrid');
$grid->setAutoHeight(true);
$grid->setAutoWidth(true);
$grid->setSkin($system->getSkinName());
$grid->adicionaColuna('CÓDIGO DO MENU'	,140		,'center'	,'ro'	,'codMenu');
$grid->adicionaColuna('DESCRIÇÃO'		,360	,'center'	,'ro'	,'descricao');
$grid->adicionaColuna('TIPO'			,160	,'center'	,'ro'	,'TIPO_APP');
$grid->adicionaColuna(''				,40		,'center'	,'img'	,'');
$grid->adicionaColuna(''				,40		,'center'	,'img'	,'');
$grid->setPaging(10,'pagingArea','recinfoArea');
$grid->setFilter('#text_filter,#text_filter,#select_filter,&nbsp;,#cspan');
$grid->loadObjectArray($gridData);

for ($i = 0; $i < sizeof($gridData); $i++) {
	$id 	= base64_encode('codMenu='.$gridData[$i]->codMenu);
	$grid->setValorColuna($i,3,IMG_URL.'/edit.png^Editar Aplicação^'.BIN_URL.'/editApp.php?id='.$id.'^_self');
	$grid->setValorColuna($i,4,IMG_URL.'/remove.png^Excluir Aplicação^'.BIN_URL.'/excApp.php?id='.$id.'^_self');
}

/** Carregando o template html **/
$mensagem = null;
$template	= new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH.$system->config->defGridHtml);

/** Define os valores das variáveis **/
$template->assign('URLADD'			,BIN_URL."editApp.php?id=".$id);
$template->assign('MENSAGEM'		,$mensagem);
$template->assign('GRID_OBJ'		,'MCGrid');
$template->assign('GRID_TITLE'		,'sysApp :: Cadastro de Aplicações');
$template->assign('GRID_LARGURA'	,746);
$template->assign('GRID_ALTURA'		,400);
$template->assign('JS_CODE'			,$grid->getHtmlCode());

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();
		
?>