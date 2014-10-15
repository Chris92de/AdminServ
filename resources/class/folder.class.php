<?php
/**
* Class Folder
*
* Méthodes de traitement de dossier
*/
abstract class Folder {
	
	/**
	* Liste les dossiers et fichiers d'un répertoire
	*
	* @param  string $path                 -> Le chemin du répertoire à lister
	* @param  mixed  $hidden_folders       -> Les dossiers à masquer : array('dossier1', 'dossier2); ou 'all' pour ne pas charger les dossiers
	* @param  mixed  $hidden_files         -> Les fichiers ou extension à masquer : array('Thumbs.db', 'index.php', 'exe');  ou 'all' pour ne pas charger les fichiers
	* @param  int    $recent_status_period -> Période pour afficher le statut "récent"
	* @return array ['folders'], ['files']
	*/
	public static function read($path = '.', $hiddenFolders = array(), $hiddenFiles = array(), $recentStatusPeriod = 86400){
		$out = array();
		$midnight = time() - (date('H') * 3600) - (date('i') * 60);
		if( substr($path, -1, 1) != '/'){ $path .= '/'; }
		
		if( !class_exists('File') && !class_exists('Str') ){
			$out = 'Class "File" or "Str" not exists';
		}
		else{
			if( !is_dir($path) ){
				$out = 'Not directory';
			}
			else{
				$dir = scandir($path);
				foreach($dir as $entry){
					$entry = utf8_encode($entry);
					if($entry != '.' && $entry != '..'){
						$pathToEntry = $path.'/'.$entry;
						
						if($hiddenFolders !== 'all' && is_dir($pathToEntry) ){
							if( !in_array($entry, $hiddenFolders) ){
								$out['folders'][$entry]['size'] = Str::formatSize(self::getSize($pathToEntry));
								$out['folders'][$entry]['nb_file'] = self::countFiles($pathToEntry, $hiddenFiles);
							}
						}
						else if($hiddenFiles !== 'all' && !in_array($entry, $hiddenFiles) ){
							$extension = File::getExtension($entry);
							
							if( !in_array($extension, $hiddenFiles) ){
								$out['files'][$entry] = @array_slice(@stat($pathToEntry), 20, 3);
								$out['files'][$entry]['size'] = Str::formatSize($out['files'][$entry]['size']);
								$out['files'][$entry]['filename'] = $entry;
								$out['files'][$entry]['extension'] = $extension;
								$out['files'][$entry]['recent'] = (isset($out['files'][$entry]['mtime']) && ($out['files'][$entry]['mtime'] > ($midnight - $recentStatusPeriod))) ? 1 : 0;
							}
						}
					}
				}
				
				if( empty($out['folders']) ){
					$out['folders'] = null;
				}
				if( empty($out['files']) ){
					$out['files'] = null;
				}
			}
		}
		return $out;
	}
	
	
	/**
	* Créer un dossier
	*
	* @param string $dirname -> Chemin du dossier à créer
	* @return true si réussi sinon message d'erreur
	*/
	public static function create($dirname){
		$out = null;
		
		if( !file_exists($dirname) ){
			umask(0000);
			if( mkdir($dirname, 0777, true) ){
				$out = true;
			}
			else{
				$out = 'Create folder failed';
			}
		}
		else{
			$out = 'Folder already exists';
		}
		
		return $out;
	}
	
	
	/**
	* Renomme un dossier
	*
	* @param string $oldname -> Chemin de l'ancien dossier
	* @param string $newname -> Chemin du nouveau dossier
	* @return true si réussi sinon message d'erreur
	*/
	public static function rename($oldname, $newname){
		$out = null;
		
		if( file_exists($oldname) ){
			if( is_dir($oldname) ){
				if( rename($oldname, $newname) ){
					$out = true;
				}
				else{
					$out = 'Rename '.$oldname.' failed';
				}
			}
			else{
				$out = 'Not directory';
			}
		}
		else{
			$out = 'Folder no exists';
		}
		
		return $out;
	}
	
	
	/**
	* Supprime un dossier avec son contenu (dossiers + fichiers)
	*
	* @param string $dirname -> Chemin du dossier à supprimer
	* @return true si réussi sinon message d'erreur
	*/
	public static function delete($dirname){
		$out = null;
		if( substr($dirname, -1, 1) != '/'){ $dirname .= '/'; }
		
		if( class_exists('File') ){
			if( file_exists($dirname) ){
				$dir = scandir($dirname);
				foreach($dir as $file){
					if($file != '.' && $file != '..'){
						$pathToFile = $dirname.$file;
						
						if( is_dir($pathToFile) ){
							self::delete($pathToFile);
						}
						else{
							File::delete($pathToFile);
						}
					}
				}
				
				if( @rmdir($dirname) !== true ){
					$out = 'Unable to delete '.$dirname;
				}
				else{
					$out = true;
				}
			}
			else{
				$out = 'Dir not exists';
			}
		}
		else{
			$out = 'Class "File" not exists';
		}
		
		return $out;
	}
	
	
	/**
	* Compte le nombre de fichier présent dans un dossier
	*
	* @param string $path        -> Le chemin du dossier
	* @param array  $hiddenFiles -> Les fichiers ou extensions à ne pas prendre en compte
	* @return int
	*/
	public static function countFiles($path, $hiddenFiles = array() ){
		$out = 0;
		if( substr($path, -1, 1) != '/'){ $path .= '/'; }
		
		if( class_exists('File') ){
			if( file_exists($path) ){
				$dir = scandir($path);
				foreach($dir as $file){
					$pathToFile = $path.$file;
					
					if( is_dir($pathToFile) && $file != '.' && $file != '..' ){
						if( is_numeric( $result = self::countFiles($pathToFile, $hiddenFiles) ) ){
							$out += $result;
						}
						else{
							$out = $result;
							break;
						}
					}
					else{
						if($file != '.' && $file != '..'){
							if( !in_array($file, $hiddenFiles) ){
								if( !in_array(File::getExtension($file), $hiddenFiles) ){
									$out++;
								}
							}
						}
					}
				}
			}
			else{
				$out = 'Dir not exists';
			}
		}
		else{
			$out = 'Class "File" not exists';
		}
		
		return $out;
	}
	
	
	/**
	* Taille d'un dossier
	*
	* @param string $path -> Le chemin du dossier
	* @return int
	*/
	public static function getSize($path){
		$out = 0;
		if( substr($path, -1, 1) != '/'){ $path .= '/'; }
		
		if( file_exists($path) ){
			$dir = scandir($path);
			foreach($dir as $file){
				if( $file != '..' && $file != '.' ){
					$pathToFile = $path.'/'.$file;
					if( is_dir($pathToFile) ){
						$out += self::getSize($pathToFile);
					}
					else{
						$out += @filesize($pathToFile);
					}
				}
			}
		}
		else{
			$out = 'Dir not exists';
		}
		
		return $out;
	}
	
	
	/**
	* Retourne l'arborescence complete du chemin avec un décalage du dossier
	*
	* @param string $path          -> Le chemin du dossier
	* @param array  $hiddenFiles   -> Les fichiers ou extensions à ne pas prendre en compte
	* @param int    $hidePathLevel -> Nombre de niveau du chemin à masquer pour l'affichage
	* @return array
	*/
	public static function getArborescence($path = '.', $hiddenFolders = array(), $hidePathLevel = 0){
		$out = array();
		$struct = self::read($path, $hiddenFolders);
		
		// Décalage selon la profondeur de l'arborescence
		$countSlash = substr_count($path, '/') - $hidePathLevel;
		$pathLevel = null;
		for($i = 0; $i < $countSlash; $i++){
			$pathLevel .= '&#9474;&nbsp;';
		}
		$pathLevel .= '&#9500;&nbsp;';
		
		if( isset($struct['folders']) && count($struct['folders']) > 0 ){
			foreach($struct['folders'] as $dir => $values){
				$out[] = array(
					'path' => $path.$dir.'/',
					'name' => $dir,
					'level' => $pathLevel
				);
				$out = array_merge($out, self::getArborescence($path.$dir.'/', $hiddenFolders, $hidePathLevel) );
			}
		}
		
		return $out;
	}
	
	
	/**
	* Vérifie les droits par rapport au chmod
	*/
	public static function checkRights($path, $minChmod){
		$out = array();
		$chars = str_split($minChmod);
		
		$i = 0;
		foreach($chars as $char){
			if($i == 0){ $group = 'owner'; }
			else if($i == 1){ $group = 'group'; }
			else{ $group = 'other'; }
			$out[$group]['request'] = $char;
			
			switch($char){
				case 7:
					$out[$group]['result']['r'] = is_readable($path);
					$out[$group]['result']['w'] = is_writable($path);
					$out[$group]['result']['x'] = is_executable($path);
					break;
				case 6:
					$out[$group]['result']['r'] = is_readable($path);
					$out[$group]['result']['w'] = is_writable($path);
					break;
				case 5:
					$out[$group]['result']['r'] = is_readable($path);
					$out[$group]['result']['x'] = is_executable($path);
					break;
				case 4:
					$out[$group]['result']['r'] = is_readable($path);
					break;
				case 3:
					$out[$group]['result']['w'] = is_writable($path);
					$out[$group]['result']['x'] = is_executable($path);
					break;
				case 2:
					$out[$group]['result']['w'] = is_writable($path);
					break;
				case 1:
					$out[$group]['result']['x'] = is_executable($path);
					break;
				default:
					$out[$group]['result']['r'] = false;
					$out[$group]['result']['w'] = false;
					$out[$group]['result']['x'] = false;
			}
			$i++;
		}
		
		return $out;
	}
	
	
	/**
	* Converti un chmod int (777) en string (rwxrwxrwx)
	*/
	public static function chmodToStr($chmod){
		$out = null;
		$chars = str_split($chmod);
		
		foreach($chars as $char){
			switch($char){
				case 7:
					$out .= 'rwx';
					break;
				case 6:
					$out .= 'rw-';
					break;
				case 5:
					$out .= 'r-x';
					break;
				case 4:
					$out .= 'r--';
					break;
				case 3:
					$out .= '-wx';
					break;
				case 2:
					$out .= '-w-';
					break;
				case 1:
					$out .= '--x';
					break;
			}
		}
		
		return $out;
	}
}
?>