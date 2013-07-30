<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

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

if (!isset($codBloco))		{
	echo "Falta de Parâmetros";
	exit;
}

$err = MCBloco::exclui($codBloco);

if ($err) {
	echo $err;
	exit;
}

?>