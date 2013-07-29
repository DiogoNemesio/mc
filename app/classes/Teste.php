<?php

/**
 * Classe para testar o ZWS
 * 
 * @package: Teste
 * @created: 17/07/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */
class Teste extends \Zage\ZWS {
	
	/**
	 * Objeto que irá guardar a Instância para implementar SINGLETON (http://www.php.net/manual/pt_BR/language.oop5.patterns.php)
	 */
	private static $instance;
	
	/**
	 * Construtor
	 *
	 * @return void
	 */
	protected function __construct() {
	/**
	 * Verificar função inicializaSistema() *
	 */
	}
	
	/**
	 * Construtor para implemetar SINGLETON
	 *
	 * @return object
	 */
	public static function getInstance() {
		if (! isset ( self::$instance )) {
			$c = __CLASS__;
			self::$instance = new $c ();
		}
		
		return self::$instance;
	}
	
	/**
	 * Refazer a função para não permitir a clonagem deste objeto.
	 */
	public function __clone() {
		\Zage\Erro::halt ( 'Não é permitido clonar ' );
	}
	
	
	public function inicializaSistema() {
		
		/**
		 * Chama o construtor da classe mae
		 */
		parent::__construct();
	}
}