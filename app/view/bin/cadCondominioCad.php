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


if (!isset($codCondominio)) {
	$codCondominio		= null;
}

if ($codCondominio != null) {
	$info		= MCCondominio::lista($codCondominio);
	$tam		= (int) sizeof($info);
	if ($tam == '1') {
		$codEstado		= $info[0]->codEstado;
		$codCidade		= $info[0]->codCidade;
		$nomeCondominio = $info[0]->nomeCondominio;
		$idCondominio	= $info[0]->condominio;
		$endereco 		= $info[0]->endereco;
		$bairro 		= $info[0]->bairro;
		$numero 		= $info[0]->numero;
		$cep			= $info[0]->cep;
		$numUnidades 	= $info[0]->qtdeUnidades;
	}else{
		$err		= "Erro:Condomínio não encontrado !!!";
	}
}else{
	$codEstado		= 'AL';
	$codCidade		= null;
	$nomeCondominio = null;
	$idCondominio	= null;
	$endereco		= null;
	$bairro 		= null;
	$numero 		= null;
	$cep			= null;
	$numUnidades 	= null;
}


/************************** Restagatar valores do banco **************************/
/** Resgatar os Estados **/
$aEstados = $system->DBGetEstados();
$oEstados = '<?xml version="1.0" encoding="%CHARSET%"?><complete>';
$oEstados .= '<option value="">Selecione o Estado</option>';
for($i=0; $i<sizeof($aEstados);$i++) {
	$selected = ($codEstado == $aEstados[$i]->codEstado) ? ' selected="true"' : '';
	$oEstados .= "<option $selected img_src=\"".BANDEIRAS_URL.$aEstados[$i]->bandeira."\" value=\"".$aEstados[$i]->codEstado."\">".$aEstados[$i]->codEstado. " - ".$aEstados[$i]->nomeEstado."</option>";
	//$oEstados .= "<option img_src=\"".BANDEIRAS_URL.$aEstados[$i]->bandeira."\" value=\"".$aEstados[$i]->codEstado."\">".$aEstados[$i]->codEstado. " - ".$aEstados[$i]->nomeEstado."</option>";
	//$oEstados .= "<option value=\"".$aEstados[$i]->codEstado."\">".$aEstados[$i]->codEstado. " - ".$aEstados[$i]->nomeEstado."</option>";
}
$oEstados	.= "</complete>";

/** Resgatar as cidades **/
if (isset($codEstado)) {
	$aCidades 	= $system->DBGetCidades($codEstado);
	$oCidades 	= '<?xml version="1.0" encoding="%CHARSET%"?><complete>';
	for($i=0; $i<sizeof($aCidades);$i++) {
		$selected = ($codCidade == $aCidades[$i]->codCidade) ? ' selected="true" ' : ' ';
		$oCidades .= "<option $selected value=\"".$aCidades[$i]->codCidade."\">".$aCidades[$i]->nomeCidade."</option>";
	}
	$oCidades	.= "</complete>";
}

/** Carregar arquivo XML do form **/
$xmlData	= DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Aplica a mascara nas variáveis **/
$system->mask->aplicaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));


/** Verifica se o usuário é administrador para Desabilitar o campo de número de unidades **/
if ($system->ehAdmin($system->getUsuario()) == false) {
	$disable	= 'dhxForm.disableItem("numUnidades");';
}else{
	$disable	= null;
}

/************************** Carregar template **************************/

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('ESTADOS'			,$oEstados);
$template->assign('CIDADES'			,$oCidades);
$template->assign('XML_DATA'		,$xmlData);
$template->assign('URL_FORM'		,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('FORM_ACTION'		,BIN_URL.'editCondominio.php');
$template->assign('ID'				,$id);
$template->assign('CONDOMINIOS_URL'	,CONDOMINIOS_URL);
$template->assign('NOME_CONDOMINIO'	,$nomeCondominio);
$template->assign('COD_CONDOMINIO'	,$codCondominio);
$template->assign('ID_CONDOMINIO'	,$idCondominio);
$template->assign('ENDERECO'		,$endereco);
$template->assign('BAIRRO'			,$bairro);
$template->assign('NUMERO'			,$numero);
$template->assign('CEP'				,$cep);
$template->assign('COD_ESTADO'		,$codEstado);
$template->assign('COD_CIDADE'		,$codCidade);
$template->assign('NUM_UNIDADES'	,$numUnidades);
$template->assign('DISABLE'			,$disable);
$template->assign('MENSAGEM'		,$err);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>