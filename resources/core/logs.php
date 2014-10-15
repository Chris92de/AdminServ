<?php

/**
* Classe pour le traitement des logs AdminServ
*/
class AdminServLogs {
	/**
	* Globales
	*/
	public static $LOGS_PATH = './logs/';
	
	/**
	* Initialise les logs (vérification des droits, création des fichiers)
	* @return bool
	*/
	public static function initialize(){
		$out = false;
		$error = null;
		
		if( in_array(true, AdminServConfig::$LOGS) ){
			if( file_exists(self::$LOGS_PATH) ){
				if( is_writable(self::$LOGS_PATH) ){
					$out = true;
				}
				else{
					$error = Utils::t('The folder "logs" is not writable.');
				}
			}
			else{
				$error = Utils::t('The folder "logs" does not exist.');
			}
		}
		
		if($out){
			if( count(AdminServConfig::$LOGS) > 0 ){
				foreach(AdminServConfig::$LOGS as $file => $activate){
					$path = self::$LOGS_PATH.$file.'.log';
					if($activate && !file_exists($path) ){
						if( File::save($path) !== true ){
							$error = Utils::t('Unable to create log file:').' '.$file.'.';
							$out = false;
							break;
						}
					}
				}
			}
		}
		
		if($error){
			if($_SESSION['error'] != null){
				$_SESSION['error'] .= '<br />'.$error;
			}
			else{
				$_SESSION['error'] = $error;
			}
		}
		
		return $out;
	}
	
	
	/**
	* Ajoute un log au fichier correspondant
	*
	* @param string $type -> Type de log : access, action, etc
	* @param string $str  -> Ligne de log à écrire
	* @return bool
	*/
	public static function add($type, $str){
		$out = false;
		$type = strtolower($type);
		if( defined('USER_PAGE') ){ $userPage = USER_PAGE; }else{ $userPage = 'index'; }
		if( defined('SERVER_NAME') ){ $serverName = '['.utf8_decode(SERVER_NAME).'] '; }else{ $serverName = null; }
		$str = '['.date('d/m/Y H:i:s').'] ['.$_SERVER['REMOTE_ADDR'].'] : '.$serverName.'['.$userPage.'] '.utf8_decode($str)."\n";
		$path = self::$LOGS_PATH.$type.'.log';
		
		if( file_exists($path) ){
			if( File::save($path, utf8_encode($str) ) !== true ){
				$error = Utils::t('Unable to add log in file:').' '.$type.'.';
				if($_SESSION['error'] != null){
					$_SESSION['error'] .= '<br />'.$error;
				}
				else{
					$_SESSION['error'] = $error;
				}
			}
			else{
				$out = true;
			}
		}
		
		return $out;
	}
}
?>
