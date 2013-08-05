<?php

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
## Resgata os parâmetros passados pelo menu
#################################################################################
if (isset($_GET["id"])) 	{
	$id = \Zage\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST["id"])) 	{
	$id = \Zage\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\Util::antiInjection($id);
}else{
	die('Parâmetro inválido 1');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\Util::descompactaId($id);

if (isset($_GET['err'])){
	$err	= \Zage\Util::antiInjection($_GET["err"]);
}else{
	$err 	= '';
}


if (!isset($codCondominio)) {
	$codCondominio		= null;
}

if ($codCondominio != null) {
	$info		= \Condominio::getInfo($codCondominio);
	if (isset($info->codEstado)) {
		$codEstado		= $info->codEstado;
		$codCidade		= $info->codCidade;
		$nomeCondominio = $info->nomeCondominio;
		$idCondominio	= $info->condominio;
		$endereco 		= $info->endereco;
		$bairro 		= $info->bairro;
		$numero 		= $info->numero;
		$cep			= $info->cep;
		$numUnidades 	= $info->qtdeUnidades;
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
#################################################################################
## Resgatar os Estados
#################################################################################
$aEstados = $system->DBGetEstados();
$oEstados = '<?xml version="1.0" encoding="%CHARSET%"?><complete>';
$oEstados .= '<option value="">Selecione o Estado</option>';
foreach ($aEstados as $dados) {
	$selected = ($codEstado == $dados->codEstado) ? ' selected="true"' : '';
	$oEstados .= "<option $selected value=\"".$dados->codEstado."\">".$dados->codEstado. " - ".$dados->nomeEstado."</option>";
	//$oEstados .= "<option img_src=\"".BANDEIRAS_URL.$dados->bandeira."\" value=\"".$dados->codEstado."\">".$dados->codEstado. " - ".$dados->nomeEstado."</option>";
	//$oEstados .= "<option value=\"".$dados->codEstado."\">".$dados->codEstado. " - ".$dados->nomeEstado."</option>";
}
$oEstados	.= "</complete>";

#################################################################################
## Resgatar as cidades
#################################################################################
if (isset($codEstado)) {
	$aCidades 	= $system->DBGetCidades($codEstado);
	$oCidades 	= '<?xml version="1.0" encoding="%CHARSET%"?><complete>';
	foreach ($aCidades as $dados) {
		$selected = ($codCidade == $dados->codCidade) ? ' selected="true" ' : ' ';
		$oCidades .= "<option $selected value=\"".$dados->codCidade."\">".$dados->nomeCidade."</option>";
	}
	$oCidades	.= "</complete>";
}

#################################################################################
## Verifica se o usuário é administrador para Desabilitar o campo de número de unidades
#################################################################################
if ($system->ehAdmin($system->getUsuario()) == false) {
	$disable	= 'dhxForm.disableItem("numUnidades");';
}else{
	$disable	= null;
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\Template();
$tpl->load(\Zage\Util::getCaminhoCorrespondente(__FILE__, \Zage\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('ESTADOS'			,$oEstados);
$tpl->set('CIDADES'			,$oCidades);
$tpl->set('FORM_ACTION'		,BIN_URL.'editCondominio.php');
$tpl->set('ID'				,$id);
//$tpl->set('CONDOMINIOS_URL'	,CONDOMINIOS_URL);
$tpl->set('NOME_CONDOMINIO'	,$nomeCondominio);
$tpl->set('COD_CONDOMINIO'	,$codCondominio);
$tpl->set('ID_CONDOMINIO'	,$idCondominio);
$tpl->set('ENDERECO'		,$endereco);
$tpl->set('BAIRRO'			,$bairro);
$tpl->set('NUMERO'			,$numero);
$tpl->set('CEP'				,$cep);
$tpl->set('COD_ESTADO'		,$codEstado);
$tpl->set('COD_CIDADE'		,$codCidade);
$tpl->set('NUM_UNIDADES'	,$numUnidades);
$tpl->set('DISABLE'			,$disable);
$tpl->set('MENSAGEM'		,$err);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

?>