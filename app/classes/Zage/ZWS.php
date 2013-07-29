<?php

namespace Zage;

/**
 * Zage Web System: Sistema para desenvolvimento de softwares web
 *
 * @package \Zage\ZWS
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 17/07/2013
 */
abstract class ZWS {
	
	const EXT_HTML	= 'html';
	const EXT_XML	= 'xml';
	const EXT_PHP	= 'php';
	const EXT_DP	= 'dp.php';
	
	const CAMINHO_ABSOLUTO	= 1;
	const CAMINHO_RELATIVO	= 2;
	
	/**
	 * Array com as configurações do sistema
	 *
	 * @var array
	 */
	public $config;
	
	/**
	 * Instância do Zend\Mail
	 *
	 * @var object
	 */
	public $mail;
	
	/**
	 * Indica se o sistema já foi iniciado
	 * @var boolean
	 */
	private $iniciado;
	
	/**
	 * Construtor: Inicializa os objetos
	 *
	 * @return void
	 */
	protected function __construct() {
		global $db,$log;
		
		/**
		 * Instânciando o objeto de configuração
		 */
		$config 		= new \Zage\Config ( CONFIG_PATH . "/config.xml" );
		$this->config 	= $config->load ();

		/**
		 * Define o Timezone padrão
		 */
		date_default_timezone_set($this->config["data"]["timezone"]);
		
		
		/** 
		 * Instânciando o objeto de e-mail
		 **/
		$this->mail			= new \Zend\Mail\Message();
		$this->mail->setEncoding($this->config["charset"]);
		
		/** 
		 * Definindo atributos globais a Instância de e-mail (Podem ser alterados no momento do envio do e-mail)
		 **/
		$this->mail->addFrom($this->config["mail"]["remetente"],$this->config["mail"]["nomeRemetente"]);
		$this->mail->addTo($this->config["mail"]["admin"],$this->config["mail"]["nomeAdmin"]);
		
		/**
		 * Iniciar recursos 
		 */
		$this->iniciaRecursos();
		
		/**
		 * Defini o indicador de sistema iniciado 
		 */
		$this->inicia();
	}
	
	/**
	 * Instanciar os objetos que não podem ser serializados (resources)
	 */
	public function iniciaRecursos() {
		global $log,$db;
		
		/**
		 * Instânciando o objeto de log
		 */
		$log		= Log::getInstance();
		
		/** 
		 * Fazendo a conexão ao banco de dados 
		 **/
		$db		= DB::getInstance();
		$db->conectar(null,null,null,null,$this->config["database"]["indSenhaCript"]);
	}
	
	/**
	 * Define o indicador de sistema inicializado
	 */
	protected function inicia() {
		$this->iniciado		= true;
	}
	
	
	/**
	 * Retorna o indicador de sistema inicializado
	 * @return boolean
	 */
	public function estaIniciado() {
		return ($this->iniciado);
	} 
	
}
