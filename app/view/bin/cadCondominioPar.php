<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');


/** Resgatando valores postados **/
if (isset($_GET['id'])) {
	$id = DHCUtil::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_GET["id"]);
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
	echo "<script>alert('Erro variável codCondominio perdida !!!!');</script>";
	DHCErro::halt('Falta de Parâmetros (COD_CONDOMINIO)');
}

$info		= MCCondParametro::lista($codCondominio);
if (!$info)	{
	$diaPagSal		= null;
	$codSkin		= null;
	$codPlanoUni	= null;
}else{
	$diaPagSal		= $info->diaPagamentoSalario;
	$codSkin		= $info->codSkin;
	$codPlanoUni	= $info->codPlanoContaUnidades;
}

if (!$codSkin)		$codSkin 	= MCParametro::getValor('CODSKIN');


/************************** Restagatar valores do banco **************************/
/** Resgatar os dados da Combo de Skin **/
$aSkins = $system->DBGetSkins();
$oSkins	= MegaCondominio::geraXmlCombo($aSkins, 'codSkin', 'descricao', $codSkin, null);

/** Resgatar os dados da Combo do Plano de Contas **/
$aContas = MCPlanoContasCondominio::lista($codCondominio);
$oContas = MegaCondominio::geraXmlCombo($aContas, 'codPlanoConta', 'conta', $codPlanoUni, null);

/** Carregar arquivo XML do form **/
$xmlData	= DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Aplica a mascara nas variáveis **/
$system->mask->aplicaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/************************** Carregar template **************************/

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('OSKINS'			,$oSkins);
$template->assign('OCONTAS'			,$oContas);
$template->assign('XML_DATA'		,$xmlData);
$template->assign('URL_FORM'		,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('ID'				,$id);
$template->assign('DIA_PAG_SAL'		,$diaPagSal);
$template->assign('MENSAGEM'		,$err);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>
