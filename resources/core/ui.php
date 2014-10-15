<?php

/**
* Classe pour l'interface d'AdminServ
*/
class AdminServUI {
	
	/**
	* Récupère le titre de l'application
	*
	* @param string $type -> Retourner "str" ou "html"
	* @return string
	*/
	public static function getTitle($type = 'str'){
		$out = null;
		$title = AdminServConfig::TITLE;
		if(!$title){
			$title = 'Admin,Serv';
		}
		
		if($type == 'html'){
			if( strstr($title, ',') ){
				$out = str_replace(',', '<span class="title-color">', $title).'</span>';
			}
			else{
				$out = $title;
			}
		}
		else{
			$out = str_replace(',', '', $title);
		}
		
		return $out;
	}
	
	
	/**
	* Vérifie s'il y a bien une config de theme
	*/
	public static function hasTheme(){
		$out = false;
		
		if( class_exists('ExtensionConfig') && isset(ExtensionConfig::$THEMES) && count(ExtensionConfig::$THEMES) > 0 ){
			$out = true;
		}
		
		return $out;
	}
	
	
	/**
	* Récupère le thème courant ou
	*
	* @param string $setTheme -> Utiliser un autre thème
	* @return $_SESSION['theme']
	*/
	public static function theme($setTheme = null){
		$saveCookie = false;
		
		if($setTheme){
			$_SESSION['theme'] = $setTheme;
			$saveCookie = true;
		}
		else{
			if( !isset($_SESSION['theme']) ){
				if( isset($_COOKIE['adminserv_user']) ){
					$_SESSION['theme'] = Utils::readCookieData('adminserv_user', 0);
				}
				else{
					if(AdminServConfig::DEFAULT_THEME){
						$_SESSION['theme'] = AdminServConfig::DEFAULT_THEME;
					}
					else{
						$_SESSION['theme'] = 'red';
					}
					$saveCookie = true;
				}
			}
		}
		
		if($saveCookie){
			$cookieData = array(
				$_SESSION['theme'],
				self::lang(),
				Utils::readCookieData('adminserv_user', 2),
				Utils::readCookieData('adminserv_user', 3)
			);
			
			Utils::addCookieData('adminserv_user', $cookieData, AdminServConfig::COOKIE_EXPIRE);
		}
		
		if($setTheme){
			if(USER_PAGE == 'index'){
				Utils::redirection();
			}
			else{
				Utils::redirection(false, '?p='.USER_PAGE);
			}
		}
		
		return strtolower($_SESSION['theme']);
	}
	
	
	/**
	* Récupère la couleur définie dans le thème
	*
	* @param int $indexColor -> Index de la couleur à récupérer
	* @return string code hexa
	*/
	public static function getThemeColor($indexColor = 0){
		$out = null;
		
		if( self::hasTheme() && ($theme = self::theme()) ){
			if( isset(ExtensionConfig::$THEMES[$theme]) && isset(ExtensionConfig::$THEMES[$theme][$indexColor]) ){
				$out = ExtensionConfig::$THEMES[$theme][$indexColor];
			}
		}
		
		return $out;
	}
	
	
	/**
	* Récupère la liste des thèmes
	*/
	public static function getThemeList($currentTheme = array() ){
		$out = null;
		$list = array();
		if( self::hasTheme() ){
			$list = ExtensionConfig::$THEMES;
		}
		$countList = count($list);
		
		// Page courante
		if(USER_PAGE && USER_PAGE != 'index'){
			$param = '?p='.USER_PAGE .'&amp;th=';
		}
		else{
			$param = '?th=';
		}
		
		if( $countList > 0 ){
			$out .= '<ul>';
			// S'il y a un thème courant, on le place en 1er
			if( count($currentTheme) > 0 ){
				$currentThemeName = key($currentTheme);
				$currentThemeColor = current($currentTheme);
				unset($list[$currentThemeName]);
				$out .= '<li><a tabindex="-1" class="theme-color" style="background-color: '.$currentThemeColor[0].';" href="'.$param.$currentThemeName.'" title="'.Utils::t( ucfirst($currentThemeName) ).'"></a></li>';
			}
			foreach($list as $name => $color){
				$out .= '<li><a tabindex="-1" class="theme-color" style="background-color: '.$color[0].';" href="'.$param.$name.'" title="'.Utils::t( ucfirst($name) ).'"></a></li>';
			}
			$out .= '</ul>';
		}
		
		return $out;
	}
	
	
	/**
	* Vérifie s'il y a bien une config de langue
	*/
	public static function hasLang(){
		$out = false;
		
		if( class_exists('ExtensionConfig') && isset(ExtensionConfig::$LANG) && count(ExtensionConfig::$LANG) > 0 ){
			$out = true;
		}
		
		return $out;
	}
	
	
	/**
	* Récupère la langue courante
	*
	* @param string $setLang -> Forcer l'utilisation de la langue
	* @return $_SESSION['lang']
	*/
	public static function lang($setLang = null){
		global $translate;
		$saveCookie = false;
		
		if($setLang){
			$_SESSION['lang'] = $setLang;
			$saveCookie = true;
		}
		else{
			if( !isset($_SESSION['lang']) ){
				if( isset($_COOKIE['adminserv_user']) ){
					$_SESSION['lang'] = Utils::readCookieData('adminserv_user', 1);
				}
				else{
					if( AdminServConfig::DEFAULT_LANGUAGE == 'auto' ){
						$_SESSION['lang'] = 'en';
						$autoLangCode = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
						if( self::hasLang() ){
							if( in_array($autoLangCode, ExtensionConfig::$LANG) ){
								$_SESSION['lang'] = $autoLangCode;
							}
						}
					}
					else{
						if(AdminServConfig::DEFAULT_LANGUAGE){
							$_SESSION['lang'] = AdminServConfig::DEFAULT_LANGUAGE;
						}
						else{
							$_SESSION['lang'] = 'en';
						}
					}
					$saveCookie = true;
				}
			}
		}
		
		if($saveCookie){
			$cookieData = array(
				self::theme(),
				$_SESSION['lang'],
				Utils::readCookieData('adminserv_user', 2),
				Utils::readCookieData('adminserv_user', 3)
			);
			
			Utils::addCookieData('adminserv_user', $cookieData, AdminServConfig::COOKIE_EXPIRE);
		}
		
		if($setLang){
			if(USER_PAGE == 'index'){
				Utils::redirection();
			}
			else{
				Utils::redirection(false, '?p='.USER_PAGE);
			}
		}
		
		$langCode = strtolower($_SESSION['lang']);
		$langFile = AdminServConfig::$PATH_RESOURCES .'lang/'.$langCode.'.php';
		if( file_exists($langFile) ){
			require_once $langFile;
		}
		
		return $langCode;
	}
	
	
	/**
	* Récupère la liste des langues
	*/
	public static function getLangList($currentLang = array() ){
		$out = null;
		$list = array();
		if( self::hasLang() ){
			$list = ExtensionConfig::$LANG;
		}
		$countList = count($list);
		
		// Page courante
		if(USER_PAGE && USER_PAGE != 'index'){
			$param = '?p='.USER_PAGE .'&amp;lg=';
		}
		else{
			$param = '?lg=';
		}
		
		// Liste de toutes les langues
		if( $countList > 0 ){
			$out .= '<ul>';
			// S'il y a une langue courante, on la place en 1er
			if( count($currentLang) > 0 ){
				$currentLangCode = key($currentLang);
				$currentLangName = current($currentLang);
				unset($list[$currentLangCode]);
				$out .= '<li><a tabindex="-1" class="lang-flag" style="background-image: url('. AdminServConfig::$PATH_RESOURCES .'images/lang/'.$currentLangCode.'.png);" href="'.$param.$currentLangCode.'" title="'.$currentLangName.'"></a></li>';
			}
			foreach($list as $code => $name){
				$out .= '<li><a tabindex="-1" class="lang-flag" style="background-image: url('. AdminServConfig::$PATH_RESOURCES .'images/lang/'.$code.'.png);" href="'.$param.$code.'" title="'.$name.'"></a></li>';
			}
			$out .= '</ul>';
		}
		
		return $out;
	}
	
	
	/**
	* Récupère le header/footer du site
	*/
	public static function getHeader(){
		global $args;
		
		if( !isset($GLOBALS['body_class']) ){
			$GLOBALS['body_class'] = null;
		}
		if(USER_PAGE != 'servers-online-config' && !self::isPageType('config') ){
			if( defined('SERVER_NAME') ){
				$GLOBALS['page_title'] = SERVER_NAME;
				$GLOBALS['body_class'] .= ' not-front';
			}
			else{
				$GLOBALS['body_class'] .= ' front';
			}
		}
		else{
			$GLOBALS['body_class'] .= ' config';
		}
		if( defined('USER_PAGE') && USER_PAGE ){
			$GLOBALS['body_class'] .= ' section-'.USER_PAGE;
		}
		if( defined('CURRENT_PLUGIN') && CURRENT_PLUGIN ){
			$GLOBALS['body_class'] .= ' plugin-'.CURRENT_PLUGIN;
		}
		$GLOBALS['body_class'] = trim($GLOBALS['body_class']);
		
		require_once AdminServConfig::$PATH_RESOURCES . 'templates/header.tpl.php';
	}
	public static function getFooter(){
		require_once AdminServConfig::$PATH_RESOURCES . 'templates/footer.tpl.php';
	}
	
	
	/**
	* Récupère le CSS/JS du site
	*/
	public static function getCss(){
		$path = AdminServConfig::$PATH_RESOURCES .'css/';
		$out = '<link rel="stylesheet" href="'.$path.'fileuploader.css" />'."\n\t\t"
		.'<link rel="stylesheet" href="'.$path.'global.css" />'."\n\t\t"
		.'<!--[if IE]><link rel="stylesheet" href="'.$path.'ie.css" /><![endif]-->'."\n\t\t";
		if( defined('USER_THEME') && USER_THEME ){
			$out .= '<link rel="stylesheet" href="'.$path.'jqueryui/'. USER_THEME .'.css" />'."\n\t\t"
			.'<link rel="stylesheet" href="'.$path.'theme.php?th='. USER_THEME .'" />'."\n\t\t";
		}
		$out .= '<link rel="stylesheet" media="screen and (max-width: 1000px) and (min-width: 335px)" href="'.$path.'mobile.css" />'."\n";
		
		return $out;
	}
	public static function getJS(){
		$path = AdminServConfig::$PATH_RESOURCES .'js/';
		$out = '<script src="'.$path.'jquery.js"></script>'."\n\t\t"
		.'<script src="'.$path.'jquery-ui.js"></script>'."\n\t\t"
		.'<script src="'.$path.'colorpicker.js"></script>'."\n\t\t"
		.'<script src="'.$path.'fileuploader.js"></script>'."\n\t\t"
		.'<script src="'.$path.'adminserv_funct.js"></script>'."\n\t\t"
		.'<script src="'.$path.'adminserv_event.js"></script>'."\n";
		
		return $out;
	}
	
	
	/**
	* Récupère la liste des serveurs configurés
	*
	* @return string
	*/
	public static function getServerList(){
		$out = null;
		
		if( class_exists('ServerConfig') && AdminServServerConfig::hasServer() ){
			if( isset($_GET['server']) && $_GET['server'] != null ){
				$currentServerId = intval($_GET['server']);
			}
			else{
				$currentServerId = Utils::readCookieData('adminserv', 0);
			}
			
			foreach(ServerConfig::$SERVERS as $server => $values){
				$selected = (AdminServServerConfig::getServerId($server) == $currentServerId) ? ' selected="selected"' : null;
				$out .= '<option value="'.$server.'"'.$selected.'>'.$server.'</option>';
			}
		}
		else{
			$out = '<option value="null">'.Utils::t('No server available').'</option>';
		}
		
		return $out;
	}
	
	
	/**
	* Récupère la liste des modes de jeu
	*
	* @param int $currentGameMode -> Le mode de jeu à sélectionner
	* @return string
	*/
	public static function getGameModeList($currentGameMode = null){
		$out = null;
		
		if( class_exists('ExtensionConfig') && isset(ExtensionConfig::$GAMEMODES) && count(ExtensionConfig::$GAMEMODES) > 0 ){
			foreach(ExtensionConfig::$GAMEMODES as $gameModeId => $gameModeName){
				$selected = ($gameModeId == $currentGameMode) ? ' selected="selected"' : null;
				$out .= '<option value="'.$gameModeId.'"'.$selected.'>'.$gameModeName.'</option>';
			}
		}
		else{
			$out = '<option value="null">'.Utils::t('No game mode available').'</option>';
		}
		
		return $out;
	}
	
	
	/**
	* Récupère le formulaire pour un champ
	*
	* @param array  $gameinfos -> Informations de jeu courantes et suivantes
	* @param string $name      -> Le nom du champ affiché dans un label
	* @param string $id        -> L'id du champ du tableau GameInfos
	* @return string HTML
	*/
	public static function getGameInfosField($gameinfos, $name, $id){
		global $data;
		$data['gameInfosField'] = array(
			'gameInfos' => $gameinfos,
			'name' => $name,
			'id' => $id
		);
		
		self::getTemplate('gameinfos-field');
		unset($data['gameInfosField']);
	}
	
	
	/**
	* Récupère la liste des joueurs
	*
	* @param string $currentPlayerLogin -> Le login joueur à sélectionner
	* @return string
	*/
	public static function getPlayerList($currentPlayerLogin = null){
		global $client;
		$out = '<option value="null">'.Utils::t('No player available').'</option>';
		
		if( !$client->query('GetPlayerList', AdminServConfig::LIMIT_PLAYERS_LIST, 0, 1) ){
			AdminServ::error();
		}
		else{
			$playerList = $client->getResponse();
			if( count($playerList) > 0 ){
				$out = null;
				foreach($playerList as $player){
					if($currentPlayerLogin == $player['Login']){ $selected = ' selected="selected"'; }
					else{ $selected = null; }
					$out .= '<option value="'.$player['Login'].'"'.$selected.'>'.TmNick::toText($player['NickName']).'</option>';
				}
			}
		}
		
		return $out;
	}
	
	
	/**
	* Retourne le menu pour les pages maps
	*
	* @return template html
	*/
	public static function getMapsMenuList(){
		global $data, $args;
		$list = ExtensionConfig::$MAPSMENU;
		$excludeLocalPage = array('maps-local', 'maps-matchset', 'maps-creatematchset');
		if( !IS_LOCAL ){
			foreach($excludeLocalPage as $page){
				unset($list[$page]);
			}
		}
		
		$data += array(
			'menuList' => $list,
		);
		self::getTemplate('maps-menu');
	}
	
	
	/**
	* Récupère la liste des dossiers du répertoire "Maps"
	*
	* @require class "Folder"
	*
	* @param string $path        -> Le chemin du dossier "Maps"
	* @param string $currentPath -> Le chemin à partir de "Maps"
	* @param bool   $showOptions -> Afficher les options (nouveau, renommer, déplacer, supprimer)
	* @return template maps-directorylist
	*/
	public static function getMapsDirectoryList($directory, $currentPath = null, $showOptions = true){
		global $data;
		
		if (class_exists('Folder')) {
			$data += array(
				'folders' => array(),
				'currentPath' => $currentPath,
				'parentPath' => null,
				'showOptions' => $showOptions,
			);
			
			// Liste des dossiers
			if (is_array($directory)) {
				// Dossier parent
				if ($currentPath) {
					$params = null;
					$parentPathEx = explode('/', $currentPath);
					array_pop($parentPathEx);
					array_pop($parentPathEx);
					if (!empty($parentPathEx)) {
						$parentPath = null;
						foreach ($parentPathEx as $part) {
							$parentPath .= $part.'/';
						}
						$data['parentPath'] = $parentPath;
					}
				}
				
				// Dossiers
				if (!empty($directory['folders'])) {
					$data['folders'] = $directory['folders'];
				}
				
				// Template
				self::getTemplate('maps-directorylist');
			}
			else{
				AdminServ::error($directory);
			}
		}
		else{
			AdminServ::error('Class "Folder" not exists');
		}
	}
	
	
	/**
	* Ititialise une page en back office
	*/
	public static function initBackPage() {
		global $client, $data, $args;
		
		// Pages list
		$pagesList = array(
			'general',
			'srvopts',
			'gameinfos',
			'chat',
			'plugins-list',
			'guestban',
		);
		$pagesList = array_merge($pagesList, array_keys(ExtensionConfig::$MAPSMENU) );
		$firstPage = array_shift($pagesList);
		
		// Render page
		if (in_array(USER_PAGE, $pagesList)) {
			$pageKey = array_search(USER_PAGE, $pagesList);
			if (AdminServAdminLevel::hasAccess($pagesList[$pageKey])) {
				self::renderPage($pagesList[$pageKey]);
			}
			else {
				$data = array(
					'errorTitle' => Utils::t('Erreur d\'accès à la page'),
					'errorMessage' => Utils::t('Vous n\'avez pas les droits requis pour accéder à cette page. Veuillez contacter votre administrateur.'),
				);
				self::renderPage('page-error');
			}
		}
		else {
			if (self::isPageType('config')) {
				session_unset();
				session_destroy();
				Utils::redirection(false, './config/');
			}
			elseif (USER_PLUGIN) {
				AdminServPlugin::renderPlugin();
			}
			else {
				self::renderPage($firstPage);
			}
		}
	}
	
	
	/**
	* Ititialise une page en front office
	*/
	public static function initFrontPage() {
		// Render page
		if (self::isPageType('config')) {
			$pageTitle = 'Configuration';
			$pageName = USER_PAGE;
		}
		else {
			$pageTitle = 'Connection';
			$pageName = 'connection';
		}
		$GLOBALS['page_title'] = Utils::t($pageTitle);
		self::renderPage($pageName);
	}
	
	
	/**
	* Inclue les fichiers pour le rendu d'une page
	*/
	public static function renderPage($pageName) {
		global $client, $data, $args;
		
		// Preprocess
		if (strstr($pageName, '-')) {
			$pageNameEx = explode('-', $pageName);
			$pagePreprocess = AdminServConfig::$PATH_RESOURCES.'process/'.$pageNameEx[0].'.preprocess.php';
			if (file_exists($pagePreprocess)) {
				require_once $pagePreprocess;
			}
		}
		
		// Process
		$pageProcess = AdminServConfig::$PATH_RESOURCES.'process/'.$pageName.'.php';
		if (file_exists($pageProcess)) {
			require_once $pageProcess;
		}
		
		// Terminate client
		if (isset($client) && $client->socket != null) {
			$client->Terminate();
		}
		
		// Header
		self::getHeader();
		
		// Template
		self::getTemplate($pageName);
		
		// Footer
		self::getFooter();
		
		AdminServLogs::add('access', (isset($GLOBALS['page_title'])) ? $GLOBALS['page_title'] : $pageName);
	}
	
	
	/**
	* Vérifie si la page est du type demandé
	*
	* @param string $pageType -> Type de page à tester (config, plugin)
	* @return bool
	*/
	public static function isPageType($pageType, $pageName = USER_PAGE){
		$out = false;
		
		if (strstr($pageName, '-')) {
			$pageNameEx = explode('-', $pageName);
			if ($pageNameEx[0] === $pageType) {
				$out = true;
			}
		}
		
		return $out;
	}
	
	
	/**
	* Inclue un fichier template
	*
	* @param string $templateName -> Nom du fichier template sans les extensions
	* @return template HTML ou null si le template n'existe pas
	*/
	public static function getTemplate($templateName){
		global $data, $args;
		
		$tplFile = AdminServConfig::$PATH_RESOURCES.'templates/'.$templateName.'.tpl.php';
		if (file_exists($tplFile)) {
			require $tplFile;
		}
	}
}
?>