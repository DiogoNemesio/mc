<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}


$js	= new \Zage\Template();
$js->load(JS_PATH . 'megaCondominio.js');
$js->show();

?>