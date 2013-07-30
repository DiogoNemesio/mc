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
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');


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
## Verifica se algumas variáveis estão OK
#################################################################################
if (!isset($codCondominio)) {
	echo "<script>alert('Erro variável codCondominio perdida !!!!');</script>";
	DHCErro::halt('Falta de Parâmetros (COD_CONDOMINIO)');
}

#################################################################################
## Resgatar as reservas existentes
#################################################################################
$gridData		= MCReserva::lista($codCondominio);

#################################################################################
## Criar o Grid
#################################################################################
$grid	= new DHCGrid('MCGrid');
$grid->setAutoHeight(true);
$grid->setAutoWidth(true);
$grid->setSkin($system->getSkinName());
$grid->adicionaColuna('UNIDADE'		,100	,'center'	,'ro'	,'NOME_UNIDADE');
$grid->adicionaColuna('ESPACO'		,200	,'center'	,'ro'	,'NOME_ESPACO');
$grid->adicionaColuna('DATA INICIAL',100	,'center'	,'ro'	,'DATA_INICIAL');
$grid->adicionaColuna('DATA FINAL'	,100	,'center'	,'ro'	,'DATA_FINAL');
$grid->adicionaColuna('USUARIO'		,150	,'center'	,'ro'	,'NOME_USUARIO');
$grid->adicionaColuna('CONFIRMADO'	,100	,'center'	,'ch'	,'indConfirmado');
$grid->adicionaColuna(''			,40		,'center'	,'img'	,'');
$grid->adicionaColuna(''			,40		,'center'	,'img'	,'');
$grid->setPaging(10,'pagingArea','recinfoArea');
$grid->setFilter('#select_filter,#select_filter,#select_filter,&nbsp;,#cspan,#cspan');
$grid->loadObjectArray($gridData);

for ($i = 0; $i < sizeof($gridData); $i++) {
	$id 	= base64_encode('codReserva='.$gridData[$i]->codReserva.'&codCondominio='.$codCondominio);
	$grid->setValorColuna($i,6,IMG_URL.'/edit.png^Editar Reserva^'.BIN_URL.'/editReserva.php?id='.$id.'^_self');
	$grid->setValorColuna($i,7,IMG_URL.'/remove.png^Excluir Reserva^'.BIN_URL.'/excReserva.php?id='.$id.'^_self');
}

$id 	= base64_encode('edit=1&codCondominio='.$codCondominio);

/** Carregando o template html **/
$mensagem = null;
$template	= new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH.$system->config->defGridHtml);

/** Define os valores das variáveis **/
$template->assign('URLADD'			,BIN_URL."editReserva.php?id=".$id);
$template->assign('COD_CONDOMINIO'	,$codCondominio);
$template->assign('MENSAGEM'		,$mensagem);
$template->assign('GRID_OBJ'		,'MCGrid');
$template->assign('GRID_TITLE'		,'Cadastro de Reservas');
$template->assign('GRID_LARGURA'	,836);
$template->assign('GRID_ALTURA'		,400);
$template->assign('JS_CODE'			,$grid->getHtmlCode());

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();
		

?>