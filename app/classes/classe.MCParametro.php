<?php

/**
 * Parâmetro
 * 
 * @package: MCParametro
 * @created: 08/10/2010
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCParametro {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
		global $system;

		$system->log->debug->debug(__CLASS__.": nova Instância");
	}
	
    /**
     * Resgata os menus por tipo de usuário
     *
     * @param integer $usuario
     * @return array
     */
    public static function lista ($parametro = null) {
		global $system;
		
    	return (
    		$system->db->extraiTodos("
				SELECT	P.*
				FROM	PARAMETROS P
				WHERE	P.parametro LIKE '%".$parametro."%'
				ORDER	BY parametro
			")
   		);
    }
    

    /**
	 * Salva o valor de um parâmetro
     */
    public function salva($parametro,$valor) {
		global $system;
		$system->log->debug->debug("Parametro: ".$parametro.' Valor: '.$valor);
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("UPDATE PARAMETROS P SET P.valor = ? WHERE parametro = ?",
				array($valor,$parametro)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }
    
    /**
     * Resgata o valor do parâmetro
     *
     * @param varchar $parametro
     * @return array
     */
    public static function getValor ($parametro) {
		global $system;
		
    	$info	= $system->db->extraiPrimeiro("
			SELECT	P.valor
			FROM	PARAMETROS P
			WHERE	P.parametro = '".$parametro."'
		");
    	
    	if (isset($info->valor)) {
    		return $info->valor;
    	}else{
    		return null;
    	}
   	
    }
	
    /**
     * 
     * Resgata o código para carregamento dinâmico de códigos html
     */
    public static function getDinamicHtmlLoad () {
		global $system;
		
    	$html	= $system->db->extraiTodos("
			SELECT	H.url
			FROM	CONFIG_LOAD_HTML H
			WHERE	ativo 	= 1
			ORDER 	BY H.ordem
		");
    	
    	$return = '<!-- Carregado dinamicamente através do dinamicHtmlLoad -->'.PHP_EOL;
		for ($i = 0; $i < sizeof($html); $i++) {
			$return .= $html[$i]->url.PHP_EOL;
		}
		$return .= '<!-- Fim do carregamento dinâmico (dinamicHtmlLoad) -->'.PHP_EOL;
		return ($return);
    }
    
    /**
     * 
     * Gerar o XML do formulário para a tabela de parâmetros
     */
    public static function geraXmlForm () {
		global $system;
		
		$params 	= MCParametro::lista();
    	
		/** Inicializa o XML **/
		$xml		= '<?xml version="1.0" encoding="'.$system->config->charset.'"?><items><item type="settings" position="label-left"/><item type="fieldset" name="Parametros" label="Parâmetros">';
		for ($i = 0; $i < sizeof($params); $i++) {
			
			/** Coloca tag de block **/
			if (($i%2) == 0) $xml .= '<item type="block" name="Block'.$i.'">';
			
			/** Coloca tag de divisão **/
			if (($i%2) == 1) $xml .= '<item type="newcolumn" offset="20"/>';
			
			/** Validação **/
			if ($params[$i]->codTipo == 'N') {
				$validacao	= ' validate="ValidInteger" ';
			}else{
				$validacao	= ' ';
			}
			
			/** Imprime o campo **/
			$xml .= '<item type="input" name="'.$params[$i]->parametro.'" label="'.$params[$i]->descricao.':" maxLength="'.$params[$i]->tamanho.'" labelWidth="200" inputWidth="'.($params[$i]->tamanho*8).'" value="'.$params[$i]->valor.'" '.$validacao.'/>';
			
			/** Finaliza a tag de block **/
			if (($i%2) == 1) $xml .= '</item>';
		}
		/** Finaliza a tag de block **/
		if (sizeof($params)%2 == 1) $xml .= '</item>';

		/** Finaliza a tag fieldset **/
		$xml .= '</item>';
		
		/** Coloca as tags do botão salvar **/
		$xml .= '<item type="block" name="BlockS"><item type="button" name="salvar" value="salvar" width="80"/></item>';
		
		/** Finaliza a tag de itens **/
		$xml .= '</items>';
		return ($xml);
    }
}