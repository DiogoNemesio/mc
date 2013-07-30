<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');


//$system->log->debug->debug("_GET: ".serialize($_GET));
//$system->log->debug->debug("_POST: ".serialize($_POST));


$params 	= MCParametro::lista();

$system->log->debug->debug("PARAMS: ".sizeof($params));
for ($i = 0; $i < sizeof($params); $i++) {
	
	if (array_key_exists($params[$i]->parametro, $_POST)) {
		$parametro	= $params[$i]->parametro;
		/** Salva os dados no banco **/
		$err	= MCParametro::salva($parametro,$_POST["$parametro"]);
		
		$system->log->debug->debug("ERR: ".$err);
		if ($err) {
			echo "Não foi possível salvar o campo '".$parametro."' erro: ".$err;
			exit;
		}
		
	} 
}
?>