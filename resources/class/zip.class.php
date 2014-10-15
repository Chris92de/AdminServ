<?php
/**
* Class Zip
*
* Méthodes de traitement pour les archives ZIP
*/
class Zip extends ZipArchive {
	
	/**
	* Créer une archive ZIP
	*
	* @param string $filename -> Nom de l'archive
	* @param array  $struct   -> Tableau des données de l'archive :
	*  $struct = array(
	*    'dirname' => array(
	*       'dirname2' => array(
	*          'file3.txt'
	*        ),
	*        'file2.txt'
	*     ),
	*     'file1.txt'
	*  );
	* @return true si réussi, sinon false
	*/
	public function create($filename, $struct, &$errorMsg = null){
		$out = false;
		
		if( !file_exists($filename) ){
			$out = self::add($filename, $struct, $errorMsg);
		}
		else{
			$errorMsg = self::_getErrorMsg(ZIPARCHIVE::ER_EXISTS);
		}
		
		return $out;
	}
	
	
	/**
	* Ajoute les données de la structure dans une archive ZIP
	*
	* @param string $filename -> Nom de l'archive
	* @param array  $struct   -> Tableau des données de l'archive
	* @return true si réussi, sinon false
	*/
	public function add($filename, $struct, &$errorMsg = null){
		$out = false;
		$zip = new ZipArchive();
		
		if( $result = $zip->open($filename, ZIPARCHIVE::CREATE) ){
			self::_checkStructure($zip, $struct);
			$out = true;
			$zip->close();
		}
		else{
			$errorMsg = self::_getErrorMsg($result);
		}
		
		return $out;
	}
	
	
	/**
	* Extrait une archive dans un dossier
	*
	* @param string $filename -> Chemin de l'archive
	* @param string $path     -> Chemin d'extraction
	*/
	public function extract($filename, $path, &$errorMsg = null){
		$out = false;
		$zip = new ZipArchive();
		
		if( $result = $zip->open($filename) ){
			if( !$out = $zip->extractTo($path) ){
				$errorMsg = self::_getErrorMsg('ER_EXTRACT');
			}
			$zip->close();
		}
		else{
			$errorMsg = self::_getErrorMsg($result);
		}
		
		return $out;
	}
	
	
	/**
	* Liste et créer la structure des données
	*
	* @param object $object -> L'instance de ZipArchive
	* @param array  $struct -> Tableau des données de l'archive
	* @param string $path   -> Le chemin à partir de la racine de la structure
	*/
	private function _checkStructure($object, $struct, $path = null){
		if( count($struct) > 0 ){
			foreach($struct as $folder => $file){
				if( is_string($folder) ){
					$object->addEmptyDir($path.$folder);
					self::_checkStructure($object, $struct[$folder], $path.$folder.'/');
				}
				else{
					$filename = null;
					if( strstr($file, '/') ){
						$pathinfo = pathinfo($file);
						$filename = $pathinfo['basename'];
					}
					
					if($filename){
						$object->addFile($path.$file, $filename);
					}else{
						$object->addFile($path.$file);
					}
				}
			}
		}
	}
	
	
	/**
	* Retourne le message d'erreur de ZipArchive correspondant au code erreur
	*
	* @param int $errorCode -> Le code d'erreur
	* @return string
	*/
	private function _getErrorMsg($errorCode){
		$out = null;
		
		switch($errorCode){
			case ZIPARCHIVE::ER_EXISTS:
				$out = 'Le fichier existe déjà.';
				break;
			case ZIPARCHIVE::ER_INCONS:
				$out = 'L\'archive ZIP est inconsistante.';
				break;
			case ZIPARCHIVE::ER_INVAL:
				$out = 'Argument invalide.';
				break;
			case ZIPARCHIVE::ER_MEMORY:
				$out = 'Erreur de mémoire.';
				break;
			case ZIPARCHIVE::ER_NOENT:
				$out = 'Le fichier n\'existe pas.';
				break;
			case ZIPARCHIVE::ER_NOZIP:
				$out = 'N\'est pas une archive ZIP.';
				break;
			case ZIPARCHIVE::ER_OPEN:
				$out = 'Impossible d\'ouvrir le fichier.';
				break;
			case ZIPARCHIVE::ER_READ:
				$out = 'Erreur lors de la lecture.';
				break;
			case ZIPARCHIVE::ER_SEEK:
				$out = 'Erreur de position.';
				break;
			case 'ER_EXTRACT':
				$out = 'Erreur lors de l\'extraction.';
				break;
		}
		
		return $out;
	}
}
?>