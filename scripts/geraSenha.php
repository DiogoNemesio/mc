<?php

include_once('../app/include.php');

if ($_SERVER['DOCUMENT_ROOT']) {
	die('Script não pode ser excutado através de um Browser !!!');
}

/** Alterar para receber parâmetros da linha de comando **/
$string			= $argv[1];
$complemento	= $argv[2];
$crypt  		= new \Zage\Crypt();
$texto			= $crypt->encrypt($string,$complemento);
echo "Senha: ". $string . PHP_EOL;
echo "String Criptografada: ".$texto . PHP_EOL;
echo "teste de retorno: ".$crypt->decrypt($texto) . PHP_EOL;

?>

