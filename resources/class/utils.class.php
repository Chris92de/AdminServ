<?php
/*
* Classe utilitaire générale
*/
abstract class Utils {
	
	/**
	* Redirection
	*
	* @param bool   $auto  -> Mode automatique, redirige vers la page en cours
	* @param string $page  -> Page à rediriger
	* @param int    $sleep -> Temps en seconde à attendre avec d'exécuter la redirection
	*/
	public static function redirection($auto = true, $page = null, $sleep = 0){
		if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != null){ $protocol = 'https'; }
		else{ $protocol = 'http'; }
		$host = $_SERVER['HTTP_HOST'];
		$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		if($auto){
			$page = basename($_SERVER['PHP_SELF']);
			if($page == 'index.php'){
				$page = null;
			}
		}
		
		// Redirection
		if($sleep !== 0){
			header("Refresh: $sleep; URL=$protocol://$host$uri/$page");
		}
		else{
			header("Location: $protocol://$host$uri/$page");
		}
		exit;
	}
	
	
	/**
	* Remplace une url texte en url cliquable
	*
	* @param string $str -> La chaine de caractères contenant une ou plusieurs url
	* @param bool $bbcode -> Convertit en HTML par defaut, true pour le BBcode
	* @return la chaine de caractères avec les url remplacées
	*/
	public static function replaceTextURL($str, $bbcode = false){
		$regex = '$(?:https?|ftp)://(?:www\.|ssl\.)?[a-z0-9._%-]+(?:/[a-z0-9._/%-]*(?:\?[a-z0-9._/%-]+=[a-z0-9._/%+-]+(?:&(?:amp;)?[a-z0-9._/%-]+=[a-z0-9._/%+-]+)*)?)?(?:#[a-z0-9._-]*)?$i';
		
		if($bbcode){
			$code = '[url]$0[/url]';
		}
		else{
			$code = '<a href="$0">$0</a>';
		}
		
		return preg_replace($regex, $code, $str);
	}
	
	
	/**
	* Vérifie si l'adresse email est dans un format valide
	*
	* @param string $email -> L'adresse email à tester
	* @return bool
	*/
	public static function isValidEmail($email){
		$out = false;
		$regex = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#';
		
		if( preg_match($regex, $email) ){
			$out = true;
		}
		
		return $out;
	}
	
	
	/**
	* Vérifie si le numero de téléphone est dans un format valide
	*
	* @param string $tel -> Le numero de téléphone à tester
	* @return bool
	*/
	public static function isValidTel($tel){
		$out = false;
		$regex = '#^0[0-9]([ .-]?[0-9]{2}){4}$#';
		
		if( preg_match($regex, $tel) ){
			$out = true;
		}
		
		return $out;
	}
	
	
	/**
	* Retourne l'image du Captcha
	*
	* @require showcaptcha.php dans un dossier "./includes/captcha/"
	*/
	public static function showCaptcha(){
		// Chaine de caractères pour créer le code
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		$code = null;
		for($i = 0; $i < 4; $i++) {
			$code .= $chars[mt_rand(0, 35)];
		}
		// On stocke le code dans la session
		$_SESSION['captcha'] = $code;
		session_write_close();
		return '<img src="./includes/captcha/showcaptcha.php" height="40" width="145" alt="Impossible d\'afficher le code !" />';
	}
	
	
	/**
	* Lis les données d'un cookie
	*
	* @param string $cookie_name -> Le nom du cookie
	* @param int    $data_pos    -> La position de la donnée à retourner, null pour toutes les données
	* @return string si une position est choisie, sinon return array de toutes les données
	*/
	public static function readCookieData($cookie_name, $data_pos = null){
		$separator = '|';
		
		if( isset($_COOKIE[$cookie_name]) ){
			$out =  explode($separator, $_COOKIE[$cookie_name]);
			
			if( is_numeric($data_pos) ){
				if( isset($out[$data_pos]) && $out[$data_pos] != null){
					return $out[$data_pos];
				}
			}
			else{
				if($out[0] != null){
					return $out;
				}
			}
		}else{
			return null;
		}
	}
	
	
	/**
	* Ajoute des données dans un cookie
	*
	* @param string $cookieName   -> Le nom du cookie
	* @param array  $data         -> Les données du cookie
	* @param int    $cookieExpire -> Le nombre de jours avant que le cookie expire
	* @return true si l'écriture du cookie à réussi, sinon false
	*/
	public static function addCookieData($cookieName, $data, $cookieExpire = 15){
		$separator = '|';
		
		$newCookieData = null;
		for($i = 0; $i < count($data); $i++){
			if($i == count($data)-1){
				$newCookieData .= $data[$i];
			}
			else{
				$newCookieData .= $data[$i].$separator;
			}
		}
		
		if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != null){ $isHttps = true; }
		else{ $isHttps = false; }
		if( setcookie($cookieName, $newCookieData, time()+60*60*24*$cookieExpire, '/', $isHttps) ){
			return true;
		}
		else{
			return false;
		}
	}
	
	
	/**
	* Récupère la langue du navigateur
	*
	* @param string $forceLang -> Forcer l'utilisation du langue
	* @return $_SESSION['lang']
	*/
	public static function getLang($forceLang = null){
		if($forceLang){
			$_SESSION['lang'] = $forceLang;
		}
		else{
			if( !isset($_SESSION['lang']) ){
				$_SESSION['lang'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
			}
		}
		
		return $_SESSION['lang'];
	}
	
	
	/**
	* Retourne la valeur du terme
	*
	* @param array $args -> Arugments dans la clé. Ex: t('key word n°!numero', array('!numero' => $numero) );
	*/
	public static function t($key, $args = array() ){
		global $translate;
		$out = $key;
		
		if( isset($_SESSION['lang']) && $_SESSION['lang'] !== 'en' ){
			if( isset($translate[$key]) ){
				$out = $translate[$key];
			}
		}
		
		if( count($args) > 0 ){
			$out = str_replace( array_keys($args), array_values($args), $out);
		}
		
		return $out;
	}
	
	
	/**
	* Récupère le navigateur internet du visiteur
	*/
	public static function getBrowser(){
		$out = null;
		$http_user_agent = $_SERVER['HTTP_USER_AGENT'];
		
		if( strstr($http_user_agent, 'Firefox') ){ $out = 'Firefox'; }
		else if( strstr($http_user_agent, 'Chrome') ){ $out = 'Chrome'; }
		else if( strstr($http_user_agent, 'Opera') ){ $out = 'Opera'; }
		else if( strstr($http_user_agent, 'MSIE') ){ $out = 'IE'; }
		else if( strstr($http_user_agent, 'Safari') ){ $out = 'Safari'; }
		else if( strstr($http_user_agent, 'Konqueror') ){ $out = 'Konqueror'; }
		else if( strstr($http_user_agent, 'Netscape') ){ $out = 'Netscape'; }
		else{ $out = 'Others'; }
		
		return $out;
	}
	
	
	/**
	* Récupère le système d'exploitation du visiteur
	*/
	public static function getOperatingSystem(){
		$out = null;
		$http_user_agent = $_SERVER['HTTP_USER_AGENT'];
		
		if( strstr($http_user_agent, 'Win') ){ $out = 'Windows'; }
		else if( (strstr($http_user_agent, 'Mac')) || (strstr('PPC', $http_user_agent)) ){ $out = 'Mac'; }
		else if( strstr($http_user_agent, 'Linux') ){ $out = 'Linux'; }
		else if( strstr($http_user_agent, 'FreeBSD') ){ $out = 'FreeBSD'; }
		else if( strstr($http_user_agent, 'SunOS') ){ $out = 'SunOS'; }
		else if( strstr($http_user_agent, 'IRIX') ){ $out = 'IRIX'; }
		else if( strstr($http_user_agent, 'BeOS') ){ $out = 'BeOS'; }
		else if( strstr($http_user_agent, 'OS/2') ){ $out = 'OS/2'; }
		else if( strstr($http_user_agent, 'AIX') ){ $out = 'AIX'; }
		else{ $out = 'Others'; }
		
		return $out;
	}
	
	
	/**
	* Détermine si le serveur est sous Windows, par défaut Unix
	*
	* @return bool
	*/
	public static function isWinServer(){
		if( stripos(PHP_OS, 'WIN') === 0 ){
			return true;
		}
		else{
			return false;
		}
	}
	
	
	/**
	* Détermine si l'adresse ip du visiteur est dans le réseau local
	*
	* @param string $addr -> L'adresse IP à utiliser
	* @return bool
	*/
	public static function isLocalhostIP($addr = null){
		$out = false;
		
		$serverIPEx = explode('.', $_SERVER['SERVER_ADDR']);
		$serverIP = $serverIPEx[0].'.'.$serverIPEx[1].'.'.$serverIPEx[2];
		
		if($addr){
			$userIPEx = explode('.', $addr);
		}
		else{
			$userIPEx = explode('.', $_SERVER['REMOTE_ADDR']);
		}
		$userIP = $userIPEx[0].'.'.$userIPEx[1].'.'.$userIPEx[2];
		
		if($userIP === $userIP){
			$out = true;
		}
		
		return $out;
	}
}
?>