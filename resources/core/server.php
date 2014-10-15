<?php

/**
* Classe pour la gestion de la configuration serveur
*/
class AdminServServerConfig {
	
	/**
	* Constantes
	*/
	private static $CONFIG_PATH = './config/';
	private static $CONFIG_FILENAME = 'servers.cfg.php';
	private static $CONFIG_START_TEMPLATE = "<?php\nclass ServerConfig {\n\tpublic static \$SERVERS = array(\n\t\t/********************* SERVER CONFIGURATION *********************/\n\t\t\n";
	private static $CONFIG_END_TEMPLATE =  "\t);\n}\n?>";
	
	
	/**
	* Détermine si il y a au moins un serveur disponible
	*
	* @param string $serverName -> Tester si le serveur est présent
	* @return bool
	*/
	public static function hasServer($serverName = null){
		$out = false;
		
		if (class_exists('ServerConfig')) {
			$serverList = ServerConfig::$SERVERS;
			
			if (isset($serverList) && !empty($serverList) && !isset($serverList['new server name']) && !isset($serverList[''])) {
				if ($serverName) {
					foreach ($serverList as $serverListName => $serverListData) {
						if ($serverListName === $serverName) {
							$out = true;
							break;
						}
					}
				}
				else {
					$out = true;
				}
			}
		}
		
		return $out;
	}
	
	
	/**
	* Récupère les données d'un serveur
	*
	* @param string $serverName -> Le nom du serveur dans la config
	* @return array
	*/
	public static function getServer($serverName){
		$out = null;
		
		if( self::hasServer() ){
			if( isset(ServerConfig::$SERVERS[$serverName]) ){
				$out = ServerConfig::$SERVERS[$serverName];
			}
			else{
				$out = Utils::t('This server does not exist');
			}
		}
		else{
			$out = Utils::t('No server available');
		}
		
		return $out;
	}
	
	
	/**
	* Retourne l'identifiant du serveur dans la config
	*
	* @param  string $serverName -> Le nom du serveur dans la config
	* @return int
	*/
	public static function getServerId($serverName){
		$id = 0;
		$servers = ServerConfig::$SERVERS;
		$countServers = count($servers);
		
		// On cherche la position du serveur à partir de son nom
		if( $countServers > 0 ){
			foreach($servers as $server_name => $server_values){
				if($server_name == $serverName){
					break;
				}
				else{
					$id++;
				}
			}
		}
		
		// Si l'id = le nb total de serveur -> pas trouvé
		if($id == $countServers ){
			return -1;
		}
		else{
			return $id;
		}
	}
	
	
	/**
	* Retourne le nom du serveur dans la config
	*
	* @param  int $serverId -> L'id du serveur dans la config
	* @return string
	*/
	public static function getServerName($serverId){
		$out = null;
		$servers = ServerConfig::$SERVERS;
		
		if( count($servers) > 0 ){
			$i = 0;
			foreach($servers as $serverName => $serverValues){
				if($i == $serverId){
					$out = $serverName;
					break;
				}
				else{
					$i++;
				}
			}
		}
		
		return $out;
	}
	
	
	/**
	* Créer le template d'un serveur
	*
	* @param array $serverData -> assoc array(name, address, port, mapsbasepath, matchsettings, adminlevel => array(SuperAdmin, Admin, User));
	*/
	public static function getServerTemplate($serverData){
		$out = "\t\t'".$serverData['name']."' => array(\n"
			."\t\t\t'address'\t\t=> '".$serverData['address']."',\n"
			."\t\t\t'port'\t\t\t=> ".$serverData['port'].",\n"
            ."\t\t\t'ds_pw'\t\t\t=> '".$serverData['ds_pw']."',\n"
			."\t\t\t'mapsbasepath'\t=> '".$serverData['mapsbasepath']."',\n"
			."\t\t\t'matchsettings'\t=> '".$serverData['matchsettings']."',\n"
			."\t\t\t'adminlevel'\t=> array(";
			$adminlevelId = 0;
			$adminlevelCount = count($serverData['adminlevel']);
			foreach($serverData['adminlevel'] as $admLvlId => $admLvlValue){
				$out .= "'$admLvlId' => ";
				if( is_array($serverData['adminlevel'][$admLvlId]) ){
					$out .= "array('".implode("', '", str_replace(' ', '', $serverData['adminlevel'][$admLvlId]))."')";
				}
				else{
					$out .= "'".$serverData['adminlevel'][$admLvlId]."'";
				}
				
				if($adminlevelId < $adminlevelCount-1){
					$out .= ', ';
				}
				$adminlevelId++;
			}
		$out .= ")\n\t\t),\n";
		
		return $out;
	}
	
	
	/**
	* Sauvegarde le mot de passe de la configuration serveur
	*
	* @param string $filename -> Le chemin vers le fichier de config
	* @param string $password -> Le mot de passe à écrire
	* @return true or text error
	*/
	public static function savePasswordConfig($filename, $password){
		$out = false;
		$seek = null;
		
		// Récupération du pointeur
		$search = 'const PASSWORD = \'';
		$seek = File::getSeekFromString($filename, $search, true);
		
		// Écriture dans le fichier
		if($seek !== null){
			if( ($result = File::saveAtSeek($filename, $password, $seek)) !== true ){
				$out = $result;
			}
			else{
				$out = true;
			}
		}
		
		return $out;
	}
	
	
	/**
	* Sauvegarde le fichier de configuration des serveurs
	*
	* @param array $serverData -> assoc array(name, address, port, matchsettings, adminlevel => array(SuperAdmin, Admin, User));
	* @param int   $editServer -> Id du serveur à éditer
	* @param array $serverList -> Liste des serveurs de la config
	* @return bool or string error
	*/
	public static function saveServerConfig($serverData = array(), $editServer = -1, $serverList = array() ){
		// Liste des serveurs
		if( isset($serverList) && count($serverList) > 0 ){
			$servers = $serverList;
		}else{
			$servers = ServerConfig::$SERVERS;
		}
		
		// Template
		$fileTemplate = self::$CONFIG_START_TEMPLATE;
		$i = 0;
		foreach($servers as $serverName => $serverValues){
			// Édition
			if($i == $editServer && isset($serverData) && count($serverData) > 0 ){
				$fileTemplate .= self::getServerTemplate($serverData);
			}
			else{
				// Récupération des données des serveurs existant
				$getServerValues = array(
					'name' => $serverName,
					'address' => $serverValues['address'],
					'port' => $serverValues['port'],
                    'ds_pw' => $serverValues['ds_pw'],
					'mapsbasepath' => isset($serverValues['mapsbasepath']) ? $serverValues['mapsbasepath'] : '',
					'matchsettings' => $serverValues['matchsettings'],
					'adminlevel' => array()
				);
				foreach($serverValues['adminlevel'] as $admLvlId => $admLvlValue){
					$getServerValues['adminlevel'][$admLvlId] = $admLvlValue;
				}
				
				// Ajout des données au template
				$fileTemplate .= self::getServerTemplate($getServerValues);
			}
			$i++;
		}
		
		// Ajout d'un nouveau
		if($editServer === -1 && isset($serverData) && count($serverData) > 0 ){
			if( self::getServerId($serverData['name']) === -1 ){
				$fileTemplate .= self::getServerTemplate($serverData);
			}
			else{
				return Utils::t('The server already exist! Change the name.');
			}
		}
		$fileTemplate .= self::$CONFIG_END_TEMPLATE;
		
		// Enregistrement
		return File::save(self::$CONFIG_PATH.self::$CONFIG_FILENAME, $fileTemplate, false);
	}
}

?>