<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');


if (isset($_GET['id'])){
	$id	= DHCUtil::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])){
	$id	= DHCUtil::antiInjection($_POST["id"]);
}else{
	DHCErro::halt('Falta de Parâmetros');
}

if (isset($_GET['err'])){
	$err	= DHCUtil::antiInjection($_GET["err"]);
}else{
	$err 	= '';
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);

/************* Restagar o condominio **************/
if (!isset($codCondominio)){
	DHCErro::halt('Falta de Parâmetros 2');
}

/**** Resgatar dados do banco ****/
if ((!isset($codVeiculo)) || (!$codVeiculo)) {
	$codVeiculo		= null;
	$codUnidade		= null;
	$codMarca 		= null;
	$modelo			= null;
	$cor			= null;
	$placa			= null;
}else{
	$info 			= MCVeiculo::getInfo($codVeiculo);
	$codUnidade		= $info->codUnidade;
	$codMarca 		= $info->codMarca;
	$modelo			= $info->modelo;
	$cor			= $info->cor;
	$placa			= $info->placa;
}


/********* Resgatar as unidades **********/
$unidades	= MCUnidade::lista($codCondominio);
$oUnidades	= MegaCondominio::geraXmlCombo($unidades, 'codUnidade', 'nome', $codUnidade, null);

/********* Resgatar as marcas **********/
$tipos	= MCVeiculo::listaMarcas();
$oTipos	= MegaCondominio::geraXmlCombo($tipos, 'codMarca', 'descricao', $codMarca, null);

/** Carregar arquivo XML do form **/
$xmlData	= DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Aplica a mascara nas variáveis **/
$system->mask->aplicaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Montar url para o botão voltar **/
$idVoltar	= DHCUtil::encodeUrl('codVeiculo='.$codVeiculo.'&codCondominio='.$codCondominio); 

/** Define os valores das variáveis **/
$template->assign('UNIDADES'		,$oUnidades);
$template->assign('MARCAS'			,$oTipos);
$template->assign('XML_DATA'		,$xmlData);
$template->assign('URL_FORM'		,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('COD_VEICULO'		,$codVeiculo);
$template->assign('MODELO'			,$modelo);
$template->assign('COR'				,$cor);
$template->assign('PLACA'			,$placa);
$template->assign('ID'				,$id);
$template->assign('MENSAGEM'		,null);
$template->assign('VOLTAR'			,BIN_URL.'cadVeiculo.php?id='.$idVoltar);


/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>