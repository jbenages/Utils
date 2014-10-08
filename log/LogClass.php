<?php
	/**
	 * LogClass
	 *
	 * Clase para realizar Logs con niveles de mensaje y por tipos de almacenamiento (Por pantalla, archivo o email)
	 *
	 * @version 0.1a
	 */
	class LogClass{

		/**
		 * [$optionLog Acciones por defecto]
		 * 
		 * save = Almacenar para guardar en fichero en formato HTML
		 * send = Almacenar para enviar por mail en formato HTML
		 * show = Mostrar por consola con formato texto Plano
		 * 
		 * @var array
		 */
		private static $optionLog = array(
			"save",
			"send",
			"show"
			);

		/**
		 * [$customOptionLog Acciones Realizables para una sola línea]
		 * 
		 * save = Almacenar para guardar en fichero la linea específica
		 * send = Enviar por email la linea específica
		 * send = Mostrar por consola la linea específica
		 * sendMail = Enviar por mail únicamente la línea específica
		 * sendMailSince = Enviar por mail todo lo almacenado mas la línea especifica
		 * 
		 * @var array
		 */
		
		private static $customOptionLog = array(
			"save",
			"send",
			"show",
			"sendMail",
			"sendMailSince"
			);

		/**
		 * [$config Configuración principal]
		 *
		 * msgSubject = Asunto del email que se enviará
		 * mailAdmin =	Correo del administrador al que se enviará el mail
		 * fileLog = Ruta y nombre del fichero donde se guardará el log
		 * savewarning = Se guardan con fichero los mensajes tipo warning
		 * savealert = Se guardan con fichero los mensajes tipo alert
		 * saveinfo = Se guardan con fichero los mensajes tipo info
		 * savelog = Se guardan con fichero los mensajes tipo log
		 * sendwarning = Se envian con email los mensajes tipo warning
		 * sendalert = Se envian con email los mensajes tipo alert
		 * sendinfo = Se envian con email los mensajes tipo info
		 * sendlog = Se envian con email los mensajes tipo log
		 * showwarnings = Se envian con email los mensajes tipo warnings
		 * showalert = Se envian con email los mensajes tipo alert
		 * showinfo = Se envian con email los mensajes tipo info
		 * showlog = Se envian con email los mensajes tipo log
		 * 
		 * @var array
		 */
		private static $config = array(
			"msgSubject"	=>	"LogClass :",
			"mailAdmin" 	=>	"mail@mail.com",
			"fileLog"		=>	"./log.log",
			"savewarning"	=>	false,
			"savealert"		=>	false,
			"saveinfo"		=>	false,
			"savelog"		=>	false,
			"sendwarning"	=>	false,
			"sendalert"		=>	false,
			"sendinfo"		=>	false,
			"sendlog"		=>	false,
			"showwarning"	=>	true,
			"showalert"		=>	true,
			"showinfo"		=>	true,
			"showlog"		=>	true
			);

		/**
		 * [$msgType Tipo de mensajes]
		 *
		 * i = Info, mostrar información, ej: una fecha o versión de software
		 * w = Warning, mostrar un error que no afecta al funcionamiento global del script
		 * a = Alert, mostrat un error que si afecta al funcionamiento global del script
		 * l = Log, mostrar un mensaje común del log una acción
		 * 
		 * @var array
		 */
		private static $msgType = array(
			"i"	=>	array(
				"type"		=>	"info",
				"format"	=>	"[i]",
				"colorC"	=>	"\033[1;34m",
				"colorH"	=>	"blue"
				),
			"w"	=>	array(
				"type"		=>	"warning",
				"format"	=>	"[-]",
				"colorC"	=>	"\033[1;33m",
				"colorH"	=>	"orange"
				),
			"a"	=>	array(
				"type"		=>	"alert",
				"format"	=>	"[!]",
				"colorC"	=>	"\033[1;31m",
				"colorH"	=>	"red"
				),
			"l"	=>	array(
				"type"		=>	"log",
				"format"	=>	"[+]",
				"colorC"	=>	"",
				"colorH"	=>	"black"
				)
			);

		private static $logSend = "";

		public static function setConfig( $field = null,$value = null ){
			if( empty($field) || is_null($value) ){
				return false;
			}
			self::$config[$field] = (bool)$value;
			return true;
		}

		public static function setArrayConfig( $config = array() ){
			if( empty($config) ){
				return false;
			}
			self::$config = array_replace_recursive(self::$config,$config);
			return true;
		}

		private static function save( $msg = null ){
			if( empty($msg) ){
				return false;
			}
			$file = fopen(self::$config["fileLog"],"a+");
			if( fwrite( $file , $msg ) ){
				return true;
			}
			fclose($file);
			return false;
		}

		private static function send( $msg = null ){
			if( empty($msg) ){
				return false;
			}
			self::$logSend .= $msg;
		}

		private static function show( $msg = null ){
			if( empty($msg) ){
				return false;
			}
			echo $msg;
		}

		private static function sendMailSince(){
			self::sendMail(self::$logSend);
		}

		private static function sendMail( $msg = null ){
			if( !empty($msg) ){
				$body = $msg;
			}else{
				$body = self::$logSend;
			}
			$body = "<html>\n<head>\n<title>\nLog Class\n</title>\n</head>\n<body>\n".$body."\n</body>\n</html>";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			mail(self::$config["mailAdmin"],self::$config["msgSubject"].date("Y-m-d H:i:s"),$body,$headers);
		}

		/**
		 * [log Paso de string para tratar con el log]
		 * @param  string $msg
		 * @param  string $msgType
		 * @param  string $customAction
		 * @return bool
		 */
		public static function log($msg = null, $msgType = "l", $customAction = null ){

			if( empty($msg) ){
				return false;
			}
			if( !array_key_exists($msgType,self::$msgType) ){
				return false;
			}

			$msg = self::$msgType[$msgType]["format"]." ".$msg;
			$consoleMsg = self::$msgType[$msgType]["colorC"].$msg."\n\033[0m";
			$htmlMsg = '<font color="'.self::$msgType[$msgType]["colorH"].'" >'.$msg."</font><br/>\n";

			for( $i = 0; $i < sizeof(self::$optionLog) ; $i++ ){
				if(	self::$config[self::$optionLog[$i].self::$msgType[$msgType]["type"]] == true ){
					$option = self::$optionLog[$i];
					if( $option == "show" ){
						self::$option($consoleMsg);
					}else{
						self::$option($htmlMsg);
					}
				}
			}

			if( !empty($customAction) ){
				if( !in_array($customAction,self::$customOptionLog ) ){
					return false;
				}
				if( $customAction == "show" && self::$config["show".self::$msgType[$msgType]["type"]] == false ){
					self::$customAction($consoleMsg);
				}else if( $customAction != "show" ){
					self::$customAction($htmlMsg);
				}
			}

			return true;

		}

	}

?>