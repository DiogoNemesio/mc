<?php

/**
 * @package: DHCUtil
 * @created: 27/11/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 *
 * Rotinas diversas
 */

class DHCUtil {


        /**
         * Construtor
         *
         */
        private function __construct() {

                /** Definindo Varáveis globais **/
                global $system;

                $system->log->debug->debug("DHCUtil: nova instância");

        }

        /**
         * Validar e-mail
         *
         * @param string $email
         * @return boolean
         */
        public static function validarEMail($email) {
                $validator = new Zend_Validate_EmailAddress();
                return $validator->isValid($email);
        }

        /**
         * Retorna o conteudo de um arquivo
         *
         * @param string $arquivo
         * @return string
         */
        public static function getConteudoArquivo ($arquivo) {

                /** Checar se o arquivo existe **/
                if (file_exists($arquivo)) {
                        try {
                                /** Abre o arquivo somente para leitura **/
                                $handle         = fopen($arquivo, "r");

                                /** Lê o conteudo do arquivo em uma variavel **/
                                $conteudo       = fread ($handle, filesize ($arquivo));

                                /** Fecha o arquivo **/
                        fclose($handle);

                        return($conteudo);

                        } catch (Exception $e) {
                                DHCErro::halt('Código do Erro: "getConteudoArquivo"');
                        }
                }else{
                        return null;
                }
        }


        /**
         * Implementação de 
         *  injeção de SQL
         *
         * @param string $string
         * @return string
         */
        public static function antiInjection($string) {

                /** remove palavras que contenham sintaxe sql **/
                $string = preg_replace("/(from|select|insert|delete|where|drop table|show tables|--|\\\\)/i","",$string);
                //$string = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i","",$string);

                /** limpa espaços vazio **/
                $string = trim($string);

                /** tira tags html e php **/
                $string = strip_tags($string);//

                /** Converte caracteres especiais para a realidade HTML **/
                $string = htmlspecialchars($string);

                if (!get_magic_quotes_gpc()) {
                        $string = addslashes($string);
                }

                return ($string);
        }

        /**
         * Retornar o mês por extenso
         *
         * @param int $mes
         * @return string
         */
        public static function mesPorExtenso($mes) {
                $mes    = (int) $mes;
                switch (fmod($mes,12)) {
                        case 1:
                                return('Janeiro');
                        case 2:
                                return('Fevereiro');
                        case 3:
                                return('Março');
                        case 4:
                                return('Abril');
                        case 5:
                                return('Maio');
                        case 6:
                                return('Junho');
                        case 7:
                                return('Julho');
                        case 8:
                                return('Agosto');
                        case 9:
                                return('Setembro');
                        case 10:
                                return('Outubro');
                        case 11:
                                return('Novembro');
                        default:
                                return('Dezembro');
                }
        }
        
        /**
         * Descobrir o mime type de um arquivo
         *
         * @param string $arquivo
         * @return string
         */
        public static function getMimeType($arquivo) {
        	return(MIME_Type::autoDetect($arquivo));
        }
        
        /**
         * Descompactar um arquivo, retornando o conteúdo descompactado
         *
         * @param string $arquivo
         * @return string $arquivo_descomprimido
         */
        public static function descompacta ($arquivo) {
        	
        	/** Verifica se o arquivo existe e pode ser lido **/
        	if ((!file_exists($arquivo)) || (!is_readable($arquivo))) return false;
        	
        	/** Verifica o mime type do arquivo **/
        	switch (DHCUtil::getMimeType($arquivo)) {
        		case 'application/x-bzip2':
        			try {
        				$bz = bzopen($arquivo, "r");
						while (!feof($bz)) {
	      					$arquivo_descomprimido .= bzread($bz, 4096);
						}
						bzclose($bz);
						return ($arquivo_descomprimido);
        			} catch (Exception $e) {
        				DHCErro::halt('Erro ao tentar descompactar o arquivo: '.$arquivo. ' Trace: '.$e->getTraceAsString());
        			}
        	}
        	
        }
        
    /**
     * Checar se um IP é válido
     *
     * @param string $ip
     * @return boolean
     */
	public static function validaIP ($ip) {
		/** Verificar se o IP está no format global do IPV4 **/
		if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip))  {
    		/** Separar cada bloco em uma array **/
    		$parts	= explode(".",$ip);
    		
    		/** Checar se cada bloco está correto **/
    		foreach($parts as $ip_parts) {
      			if (intval($ip_parts)>255 || intval($ip_parts)<0) {
      				return false;
    			} else {
    				return true;
    			}
			}
  		}else{
  			return false;
		}
	}
		
		

	/**
	 * Retornar um número em formato de moeda (BR)
	 *
	 * @param number
	 * @return string
	 */
	public static function to_money($n) {
		$temp = str_replace(",",".",$n);
		return('R$ '.number_format($temp, 2, ',', '.'));
	}

	/**
	 * Retornar um número formatado
	 *
	 * @param number
	 * @return string
	 */
	public static function to_number($n) {
		$temp = str_replace(".","",$n);
		$temp = str_replace(",",".",$temp);
		return(number_format($temp, 0, ',', '.'));
	}
	
	/**
	 * Retornar Primeiro dia do mês
	 *
	 * @param date (formato dd/mm/yyyy)
	 * @return date (formato dd/mm/yyyy)
	 */
	public static function getFirstDayOfMonth($data) {
		list($dia,$mes,$ano)	= split('/',$data);
		$timeStamp				= mktime(0,0,0,$mes,1,$ano); //Create time stamp of the first day
    	$firstDay				= date('d/m/Y',$timeStamp);  //get first day of the given month		
		return($firstDay);
	}

	/**
	 * Retornar último dia do mês
	 *
	 * @param date (formato dd/mm/yyyy)
	 * @return date (formato dd/mm/yyyy)
	 */
	public static function getLastDayOfMonth($data) {
		list($dia,$mes,$ano)	= split('/',$data);
		$timeStamp				= mktime(0,0,0,$mes,1,$ano);    		//Create time stamp of the first day
		list($t,$m,$a)			= split('/',date('t/m/Y',$timeStamp)); 	//Find the last date of the month and separating it
    	$lastDayTimeStamp		= mktime(0,0,0,$m,$t,$a);				//create time stamp of the last date of the give month
		$lastDay				= date('d/m/Y',$lastDayTimeStamp);
		return($lastDay);
	}
	
	/**
	 * Coloca uma mascara em uma string
	 *
	 * @param string (string)
	 * @param string (mascara)
	 * @return string (string com a mascara)
	 */
	function mask ($string, $mascara) {
		$z 		= 0;
		$chars 	= array('-', '_', '.', '/', '\\', ':', '|', '(', ')', '[', ']', '{', '}');
		$result	= '';
		
        for ($n=0; $n < strlen($mascara); $n++) {
            $mask_char = substr($mascara, $n, 1);
            $text_char = substr($string, $z, 1);
    
            if (in_array($mask_char, $chars)) {
                if ($z<strlen($string))
                    $result .= $mask_char;
            }else {
                $result .= $text_char;
                $z ++;
            }
        }
        return $result;
    }

	
	/**
	 * Retira a mascara de uma string
	 *
	 * @param string (string)
	 * @return string (string com a mascara)
	 */
	function unmask ($string) {

		$chars = array('-', '_', '.', '/', '\\', ':', '|', '(', ')', '[', ']', '{', '}');

        for ( $n=0; $n <= strlen($string); $n++) {
            $char = substr($string, $n, 1);
            if (!in_array($char, $chars)) {
                $result .= $char;
            }
        }
        return $result;
    }
	
	/**
	 * Retorna o conteudo do xml
	 *
	 * @param string $arquivo
	 * @return string
	 */
	public static function getXmlData ($arquivo) {
		$xmlData	= DHCUtil::getConteudoArquivo($arquivo);
		$xmlData	= str_ireplace("\'", "\"", $xmlData);
		$xmlData	= str_ireplace(PHP_EOL, null, $xmlData);
		return($xmlData);
	}
    
	
	
	
	/**
	 * 
	 * Procura por itens do tipo input em um Xml
	 * @param string $xml
	 * @return array $array
	 */
	public static function getXmlInputs($xml) {
		global $system;
		
		if (substr($xml,0,5) == '<?xml') {
			$xmlData	= $xml;
		}else{
			/** Coloca o arquivo XMl em uma string **/
			$xmlData	= DHCUtil::getXmlData($xml);
		}
		
		/** Ajusta o caracter set **/
		$xmlData 	= str_ireplace("%CHARSET%", $system->config->charset, $xmlData);
		
		//$system->log->debug->debug('XMLData: '.$xmlData);
		
		/** Carrega o Arquivo Xml em um objeto **/
		$xmlObj = simplexml_load_string($xmlData);

		$inputs	= array();
		DHCUtil::getXmlInputsItens($xmlObj,$inputs);
		return ($inputs);
	
	} 
	
	
	/**
	 * 
	 * Procura por itens do tipo input em um Xml
	 * @param string $xml
	 * @return array $array
	 */
	public static function getXmlInputsItens($obj,&$array) {
		global $system;
		foreach ($obj as $key => $value) {
			if (is_object($value) && isset($value->attributes()->type)) {
				switch ($value->attributes()->type) {
					case "checkbox":
					case "input":
					case "file":
					case "hidden":
					case "multiselect":
					case "password":
					case "radio":
					case "select":
					case "textarea":
					case "calendar":
					case "colorpicker":
					case "combo":
					case "editor":
						if (isset($value->attributes()->name) && isset($value->attributes()->className) && preg_match('#MCMask-(.+)#', $value->attributes()->className)) {
							$nome 	= $value->attributes()->name;
							//$system->log->debug->debug("Name = ".$nome);
							$array["$nome"]	= $value->attributes();
						} 
						break;
				}
			}

			/** Recursividade **/
			DHCUtil::getXmlInputsItens($value,$array);
		}
	
	} 
	
	/**
	 * Descompacta um id
	 * @param string $id
	 */
	public static function descompactaId($id) {
		if ($id != null) {
			$var    = base64_decode($id);
			//echo "Var: $var<BR>";
			$vars   = explode("&",$var);
			for ($i = 0; $i < sizeof($vars); $i++) {
				if ($vars[$i] != '') {
					list($variavel,$valor)  = explode('=',$vars[$i]);
					eval('global $'.$variavel.';');
					eval('$'.$variavel.' = "'.$valor.'";');
				}
			}
		}
	}
	

	/**
	 * Codifica uma string
	 * @param string $string
	 */
	public static function encodeUrl($string) {
		return(base64_encode($string));
	}
	
	/**
	 * Retira as tags de XML de uma string que será os options de um select
	 * @param string $string
	 */
	public static function retiraTagsXMLSelect($string) {
		$return		= $string;
		$return		= str_ireplace('<complete>', '', $return);
		$return		= str_ireplace('</complete>', '', $return);
		$return		= str_ireplace('<?xml version="1.0" encoding="UTF-8"?>', '', $return);
		return($return);
	}

}
