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
if (isset($_POST['codBloco']))			$codBloco		= DHCUtil::antiInjection($_POST["codBloco"]);
if (isset($_POST['nome']))				$nome			= DHCUtil::antiInjection($_POST["nome"]);
if (isset($_POST['codTipo']))			$codTipo		= DHCUtil::antiInjection($_POST["codTipo"]);
if (isset($_POST['codResponsavel']))	$codResponsavel	= DHCUtil::antiInjection($_POST["codResponsavel"]);
if (isset($_POST['fone']))				$fone			= DHCUtil::antiInjection($_POST["fone"]);
if (isset($_POST['celular']))			$celular		= DHCUtil::antiInjection($_POST["celular"]);
if (isset($_POST['codVencimento'])) 	$codVencimento	= DHCUtil::antiInjection($_POST["codVencimento"]);
if (isset($_POST['ramal'])) 			$ramal			= DHCUtil::antiInjection($_POST["ramal"]);

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
$err	= null;

if ($nome == null) {
	$err	= "O Campo 'NOME' é obrigatório !!!";
}

if (($ramal != null) && (!is_numeric($ramal))) {
	$err	= "O Campo 'RAMAL' deve ser numérico !!!";
}

DHCUtil::retiraMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

if ($err == null) {
	$err = MCUnidade::salva($codUnidade,$codBloco,$nome,$codResponsavel,$fone,$celular,$codTipo,$codVencimento,$ramal);
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