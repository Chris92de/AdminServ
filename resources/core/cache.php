<?php

/**
* Classe pour la gestion du cache
*/
class AdminServCache {
	private $folder;
	
	/**
	* Initialisation du cache
	*/
	function __construct(){
		$this->folder = AdminServConfig::$PATH_RESOURCES.'cache/';
		
		if( !file_exists($this->folder) ){
			Folder::create($this->folder);
		}
	}
	
	
	/**
	* Enregistre la valeur dans un fichier
	*
	* @param string $key   -> Clef du cache
	* @param array  $value -> Valeur à enregistrer
	* @return bool
	*/
	public function set($key, $value){
		$out = false;
		$file = $this->folder . $key . '.json';
		$data = json_encode($value);
		
		if( file_exists($file) ){
			if( File::save($file, $data, false) ){
				$out = true;
			}
		}
		else{
			if( File::save($file) ){
				self::set($key, $value);
			}
		}
		
		$error = self::getErrorMsg('set');
		if($error){
			$out = false;
			AdminServ::error($error);
		}
		
		return $out;
	}
	
	
	/**
	* Récupère la valeur depuis un fichier
	*
	* @param string $key -> Clef du cache à récupérer
	* @return array()
	*/
	public function get($key){
		$out = array();
		$file = $this->folder . $key . '.json';
		
		if( file_exists($file) ){
			$data = file_get_contents($file);
			$out = json_decode($data, true);
		}
		
		$error = self::getErrorMsg('get');
		if($error){
			$out = array();
			AdminServ::error($error);
		}
		
		return $out;
	}
	
	
	/**
	* Supprime un fichier de cache
	*
	* @param string $key -> Clef du cache à supprimer
	* @return bool
	*/
	public function delete($key){
		$out = false;
		$file = $this->folder . $key . '.json';
		
		if( file_exists($file) ){
			if( ($result = File::delete($file)) !== true ){
				AdminServ::error($result);
			}
			else{
				$out = true;
			}
		}
		
		return $out;
	}
	
	
	/**
	* Récupère le message d'erreur lors de l'encodage/décodage du JSON
	*/
	public static function getErrorMsg($type){
		switch( json_last_error() ){
			case JSON_ERROR_DEPTH:
				return 'CACHE ('.$type.') - Profondeur maximale atteinte';
				break;
			case JSON_ERROR_STATE_MISMATCH:
				return 'CACHE ('.$type.') - Inadéquation des modes ou underflow';
				break;
			case JSON_ERROR_CTRL_CHAR:
				return 'CACHE ('.$type.') - Erreur lors du contrôle des caractères';
				break;
			case JSON_ERROR_SYNTAX:
				return 'CACHE ('.$type.') - Erreur de syntaxe ; JSON malformé';
				break;
			case JSON_ERROR_UTF8:
				return 'CACHE ('.$type.') - Caractères UTF-8 malformés, probablement une erreur d\'encodage';
				break;
		}
	}
}
?>