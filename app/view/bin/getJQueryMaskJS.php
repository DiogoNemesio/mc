<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

$js	= new \Zage\Template();
$js->load(JS_PATH . 'jquery.meio.mask.js');
$js->set('MASK_CONFIG', $system->mask->geraConfigMeioMask());
$js->set('FIXED_CHARS', $system->mask->geraCaracteresFixos());
$js->set('STARS', $system->mask->geraCaracteresEstrelas());

$js->show();

?>