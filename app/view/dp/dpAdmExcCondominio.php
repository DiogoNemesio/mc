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

if (!isset($codCondominio))		{
	echo "Falta de Parâmetros";
	exit;
}

$err = MCCondominio::exclui($codCondominio);
if ($err) {
	echo $err;
	exit;
}

?>