<?php

/**
 * Constantes do Sistema
 */

/**
 * Checa se a constante DOC_ROOT está definida
 */
if (! defined ( 'DOC_ROOT' )) {
	die ( 'Constante DOC_ROOT não definida !!! (constants)' );
}

/**
 * URL Raiz
 */
if ($_SERVER ['DOCUMENT_ROOT']) {
	define ( 'PROTO', strtolower ( substr ( $_SERVER ["SERVER_PROTOCOL"], 0, strpos ( $_SERVER ["SERVER_PROTOCOL"], '/' ) ) ) . "://" );
	define ( 'ROOT_URL', PROTO . $_SERVER ["SERVER_NAME"] . '/' );
}else{
	define ( 'ROOT_URL', null );
}

/**
 * Caminho onde ficam as classes
 */
define ( 'CLASS_PATH', DOC_ROOT . '/classes/' );

/**
 * Caminho onde ficam as packages
 */
define ( 'PKG_PATH', DOC_ROOT . '/view/packages/' );
define ( 'PKG_URL', ROOT_URL . 'packages/' );

/**
 * Caminho onde ficam os arquivos PHP executáveis
 */
define ( 'BIN_PATH', DOC_ROOT . '/view/bin/' );
define ( 'BIN_URL', ROOT_URL . 'bin/' );

/**
 * Caminho onde ficam os arquivos de configuração
 */
define ( 'CONFIG_PATH', DOC_ROOT . '/etc/' );
define ( 'CONFIG_URL', ROOT_URL . 'etc/' );

/**
 * Caminho onde ficam os arquivos html
 */
define ( 'HTML_PATH', DOC_ROOT . '/html/' );
define ( 'HTML_URL', ROOT_URL . 'html/' );

/**
 * Caminho onde ficam os arquivos de log
 */
define ( 'LOG_PATH', DOC_ROOT . '/log/' );

/**
 * Caminho onde ficam as imagens
 */
define ( 'IMG_PATH', DOC_ROOT . '/view/imgs/' );
define ( 'IMG_URL', ROOT_URL . 'imgs/' );

/**
 * Caminho onde ficam os CSS
 */
define ( 'CSS_PATH', DOC_ROOT . '/view/css/' );
define ( 'CSS_URL', ROOT_URL . 'css/' );

/**
 * Caminho onde ficam os Javascripts
 */
define ( 'JS_PATH', DOC_ROOT . '/view/js/' );
define ( 'JS_URL', ROOT_URL . 'js/' );

/**
 * Caminho do dataProcessor
 */
define ( 'DP_PATH', DOC_ROOT . '/view/dp/' );
define ( 'DP_URL', ROOT_URL . 'dp/' );

