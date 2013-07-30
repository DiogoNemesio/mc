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
if ($id) {
	$var    = base64_decode($id);
	$vars   = explode("&",$var);
	for ($i = 0; $i < sizeof($vars); $i++) {
		list($variavel,$valor)  = explode('=',$vars[$i]);
		eval('$'.$variavel.' = "'.$valor.'";');
	}
}

/************************** Resgatar valores do form  **************************/
if (isset($_POST['codBloco']))		$codBloco		= DHCUtil::antiInjection($_POST["codBloco"]);
if (isset($_POST['nome']))			$nomeBloco		= DHCUtil::antiInjection($_POST["nome"]);
if (isset($_POST['descricao']))		$descricao		= DHCUtil::antiInjection($_POST["descricao"]);
if (isset($_POST['codSindico']))	$codSindico		= DHCUtil::antiInjection($_POST["codSindico"]);

if (!isset($codCondominio))		{
	echo "Erro: falta de parâmetros";
	exit;
}


/** Validação dos Campos **/
$err	= null;

if ($nomeBloco == null) {
	$err	= "Erro:O Campo \"NOME\" é obrigatório !!!";
}

if (strlen($descricao) > 100) {
	$err	= "Erro:O Campo \"DESCRIÇÃO\" é muito grande !!!";
}

if ((!$codSindico) || ($codSindico == '') || ($codSindico = 'null')) $codSindico = null;

if ($err == null) {
	$err = MCBloco::salva($codBloco,$codCondominio,$nomeBloco, $descricao, $codSindico);
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