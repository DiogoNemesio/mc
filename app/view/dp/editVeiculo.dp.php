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
if (isset($_POST['codVeiculo']))	$codVeiculo		= DHCUtil::antiInjection($_POST["codVeiculo"]);
if (isset($_POST['codUnidade']))	$codUnidade		= DHCUtil::antiInjection($_POST["codUnidade"]);
if (isset($_POST['codMarca']))		$codMarca		= DHCUtil::antiInjection($_POST["codMarca"]);
if (isset($_POST['modelo']))		$modelo			= DHCUtil::antiInjection($_POST["modelo"]);
if (isset($_POST['cor']))			$cor			= DHCUtil::antiInjection($_POST["cor"]);
if (isset($_POST['placa']))			$placa			= DHCUtil::antiInjection($_POST["placa"]);


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

/** Validação dos Campos **/
$placa	= strtoupper($placa);


$err	= null;

DHCUtil::retiraMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

if ($err == null) {
	$err = MCVeiculo::salva($codVeiculo,$codUnidade,$codMarca,$modelo,$cor,$placa);
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