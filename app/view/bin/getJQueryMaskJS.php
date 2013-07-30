<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

$js	= new DHCHtmlTemplate();
$js->loadTemplate(JS_PATH . 'jquery.meio.mask.js');
$js->assign('MASK_CONFIG', $system->mask->geraConfigMeioMask());
$js->assign('FIXED_CHARS', $system->mask->geraCaracteresFixos());
$js->assign('STARS', $system->mask->geraCaracteresEstrelas());

echo $js->getHtmlCode();

?>