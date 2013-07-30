<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');

/** Resgatando valores postados **/
if (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_POST["id"]);
}else{
	echo "Requisição inválida !!";
	exit;
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);

/************************** Resgatar valores do form  **************************/
if (isset($_POST['codMorador']))	$codMorador		= DHCUtil::antiInjection($_POST["codMorador"]);
if (isset($_POST['codUnidade']))	$codUnidade		= DHCUtil::antiInjection($_POST["codUnidade"]);
if (isset($_POST['nome']))			$nome			= DHCUtil::antiInjection($_POST["nome"]);
if (isset($_POST['fone']))			$fone			= DHCUtil::antiInjection($_POST["fone"]);
if (isset($_POST['codTipoSexo']))	$codTipoSexo	= DHCUtil::antiInjection($_POST["codTipoSexo"]);
if (isset($_POST['codUsuario']))	$codUsuario		= DHCUtil::antiInjection($_POST["codUsuario"]);

if (!isset($codCondominio))		{
	echo "falta de parâmetros";
	exit;
}

/** Validação das máscaras **/
$valido		= DHCUtil::validaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));
if ($valido !== true) {
	echo "O campo '".$valido."' está inválido !!!";
	exit;
}

if (strtoupper($codUsuario) == "NULL") $codUsuario	= null;

/** Validação dos Campos **/
$err	= null;

DHCUtil::retiraMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

if ($err == null) {
	$err = MCMorador::salva($codMorador,$codUnidade,$nome,$fone,$codTipoSexo,$codUsuario);
	if (is_numeric($err)) {
		$err			= null;
	}
	echo $err;
	exit;
}else{
	echo $err;
	exit;
}	

?>