<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}


$js	= new DHCHtmlTemplate();
$js->loadTemplate(JS_PATH . 'megaCondominio.js');
echo $js->getHtmlCode();

?>