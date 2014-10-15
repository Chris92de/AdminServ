<?php
/**
* Class Str
*
* Méthodes de traitement de chaines de caractères
*/
abstract class Str {
	
	/**
	* Formatage de la taille
	* 
	* @param int $size -> Taille
	* @return string
	*/
	public static function formatSize($size){
		$u = array('octets', 'Ko', 'Mo', 'Go', 'To');
		$i = 0;
		$m = 0;
		
		while($size >= 1){
			$m = $size;
			$size /= 1024;
			$i++;
		}
		if(!$i){
			$i = 1;
		}
		$d = explode('.', $m);
		
		if($d[0] != $m){
			$m = number_format($m, 1, ',', ' ');
		}
		
		return $m.' '.$u[$i-1];
	}
	
	
	/**
	* Modifie les antislashes d'un lien local en slashes pour usage web
	*
	* @param string $path -> Le chemin à modifier
	* @return string
	*/
	public static function toSlash($path){
		$out = null;
		
		if( strstr($path, '\\') ){
			$newPath = explode('\\', $path);
			foreach($newPath as $part){
				$out .= $part.'/';
			}
			$out = substr($out, 0, -1);
		}
		else{
			$out = $path;
		}
		
		return $out;
	}
	
	
	/**
	* Remplace ou supprime les caractères spéciaux
	*
	* @param string $str -> La chaine de caractères
	*/
	public static function replaceSpecialChars($str, $replaceSpace = true){
		// Listes
		$toRemove = array(
			"'", '"', '&', '<', '>', '+', '=', '*', '/', '²', '~', '#', '{', '[', '(', '|', '`', '\\', '^', '@', ')', ']', '}', '¨', '$', '£', '¤',
			'!', '%', '§', ':', ';', ',', '?', '«', '»', '“', '”', '„', '…', '¡', '¿', '‘', '’', 'ˆ', '˜', '¸', '·', '•', '¯', '‾', '—', '¦', '†',
			'‡', '¶', '©', '®', '™', '◊', '♠', '♣', '♥', '♦', '←', '↑', '→', '↓', '↔', 'Γ', 'δ', 'Δ', 'ζ', 'θ', 'Θ', 'ι', 'Λ', 'ξ', 'Ξ', 'π', 'Π',
			'σ', 'ς', 'Σ', 'φ', 'Φ', 'ψ', 'Ψ', 'ω', 'Ω', '°', '≤', '≥', '≈', '≠', '≡', '±', '×', '÷', '⁄', '‰', '¼', '½', '¾', '¹', '³', 'º', 'ª',
			'′', '″', '∂', '∏', '∑', '√', '∞', '¬', '∩', '∫', '⇒', '⇔', '∀', '∃', '∇', '∈', '∋', '∝', '∠', '∧', '∨', '∴', '∼', '⊂', '⊃',
			'⊆', '⊇', '⊥'
		);
		if($replaceSpace){
			$toDash = array(
				' ', '  '
			);
		}
		
		// Remplacement
		$str = trim($str);
		$str = str_replace($toRemove, '', $str);
		if($replaceSpace){
			$str = str_replace($toDash, '-', $str);
		}
		else{
			$str = trim($str);
		}
		return $str;
	}
	
	
	/**
	* Remplace les caractères accentués par leurs caractères normal
	*
	* @param string $str -> La chaine de caractères
	*/
	public static function replaceAccentChars($str){
		$str = utf8_decode($str);
		$str = strtr($str, utf8_decode('ÀÁÂÃÄÅΑλàáâãäåαβþÐÒÓÔÕÖØòóôõöøÈÉÊËèéêë€εƒÇç¢ÌÍÎÏìíîïÙÚÛÜ∪ùúûüµυÿýýŸÝ¥ÑñηŔŕρτ'), 'AAAAAAAAaaaaaaaBbDOOOOOOooooooEEEEeeeeEefCccIIIIiiiiUUUUUuuuuuuyyyYYYNnnRrpt');
		return utf8_encode($str);
	}
	
	
	/**
	* Remplace ou supprime les caractères spéciaux et remplace les caractères accentués
	*
	* @param string $str -> La chaine de caractères
	*/
	public static function replaceChars($str){
		return self::replaceSpecialChars( self::replaceAccentChars($str) );
	}
	
	
	/**
	* Récupère le type d'une valeur
	*
	* @param  mixed $val -> La valeur à analyser
	* @return string
	*/
	public static function getValueType($val){
		$out = null;
		
		if( is_string($val) ){
			$out = 'string';
		}
		elseif( is_bool($val) ){
			$out = 'bool';
		}
		elseif( is_float($val) ){
			$out = 'float';
		}
		elseif( is_int($val) ){
			$out = 'int';
		}
		elseif( is_array($val) ){
			$out = 'array';
		}
		elseif( is_object($val) ){
			$out = 'object';
		}
		elseif( is_resource($val) ){
			$out = 'resource';
		}
		
		return $out;
	}
	
	
	/**
	* Converti le type d'une valeur
	*
	* @param mixed  $val  -> La valeur à convertir
	* @param string $type -> Le type à affecter
	*/
	public static function setValueType($val, $type){
		switch($type){
			case 'array':
				$val = (array)$val;
				break;
			case 'bool':
			case 'boolean':
				$val = (bool)$val;
				break;
			case 'float':
			case 'double':
				$val = (float)$val;
				break;
			case 'int':
			case 'integer':
				$val = (int)$val;
				break;
			default:
				$val = (string)$val;
		}
		
		return $val;
	}
}
?>