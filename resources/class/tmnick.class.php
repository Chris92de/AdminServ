<?php
/**
* Cette classe gère les chaines TMF, c'est à dire que cette classe permet de convertir une chaine TM en:
*   - html (ombre et lien sont géré)
*   - texte brut -> sans la mise en forme
*   - image (tout format supporté, image d'arrière plan ou pas)
*
* @author Mr. Das - Gildas de Cadoudal - gdecad@gmail.com
* @package framework
*/
class TmNick {
	/**
	* Ces codes sont valables dans l'ensemble des jeux TrackMania :
	* 
	* $t Majuscule
	* $i Italique
	* $s Ombré
	* $w Espacements larges
	* $n Espacements courts
	* $m Espacements normaux
	* $g Couleur par défaut
	* $z Tout réinitialiser
	* $$ Ecrire un "$"
	* $o Gras avec espacements semi-larges
	* $h Lien interne vers un Manialink
	* $p Lien interne avec info joueur
	* $l Lien externe vers un site web
	*/
	public  static $m_fontPath = 'fonts/';
	public  static $linkProtocol = 'tmtp';
	private static $m_pattern = '/\\$\\$|\\$[0-9a-fA-F][0-9a-zA-Z]{2}|\\$[twmgonishplzTWMGONISHLZ]/';
	private static $m_patternLink = '/(?:\\[(?P<url>[^\\[\\]]*)\\]){0,1}(?P<label>.+){0,1}/';
	private static $m_lstTags = array('h','p','l','o','g','t','w','m','n','i','s','z','color');
	private static $m_css = array(
		'h' => 'tmnick_internallink',
		'p' => 'tmnick_internallink_withlogin',
		'l' => 'tmnick_externallink',
		'o' => 'tmnick_bold',
		'g' => 'tmnick_defaultcolor',
		't' => 'tmnick_upper',
		'w' => 'tmnick_wide',
		'm' => 'tmnick_normal',
		'n' => 'tmnick_short',
		'i' => 'tmnick_italic',
		's' => 'tmnick_shadowed',
		'z' => 'tmnick_raz'
	);
	private static $m_fonts = array(
		'h' => '',
		'l' => '',
		'o' => 'DM',
		'g' => '',
		't' => '',
		'w' => '',
		'm' => '',
		'n' => 'CN',
		'i' => 'IT',
		's' => '',
		'z' => 'MD'
	);
	private static $m_defaultHexaOnError = '0';
	private static $m_colorNameToHtml = array(
		'aliceblue' => '#f0f8ff', 'antiquewhite' => '#faebd7', 'aqua' => '#00ffff', 'aquamarine' => '#7fffd4', 'azure' => '#f0ffff', 'beige' => '#f5f5dc', 'bisque' => '#ffe4c4', 'black' => '#000000', 'blanchedalmond' => '#ffebcd', 'blue' => '#0000ff',
		'blueviolet' => '#8a2be2', 'brown' => '#a52a2a', 'burlywood' => '#deb887', 'cadetblue' => '#5f9ea0', 'chartreuse' => '#7fff00', 'chocolate' => '#d2691e', 'coral' => '#ff7f50', 'cornflowerblue' => '#6495ed', 'cornsilk' => '#fff8dc', 'crimson' => '#dc143c',
		'cyan' => '#00ffff', 'darkblue' => '#00008b', 'darkcyan' => '#008b8b', 'darkgoldenrod' => '#b8860b', 'darkgray' => '#a9a9a9', 'darkgrey' => '#a9a9a9', 'darkgreen' => '#006400', 'darkkhaki' => '#bdb76b', 'darkmagenta' => '#8b008b', 'darkolivegreen' => '#556b2f',
		'darkorange' => '#ff8c00', 'darkorchid' => '#9932cc', 'darkred' => '#8b0000', 'darksalmon' => '#e9967a', 'darkseagreen' => '#8fbc8f', 'darkslateblue' => '#483d8b', 'darkslategray' => '#2f4f4f', 'darkslategrey' => '#2f4f4f', 'darkturquoise' => '#00ced1', 'darkviolet' => '#9400d3',
		'deeppink' => '#ff1493', 'deepskyblue' => '#00bfff', 'dimgray' => '#696969', 'dimgrey' => '#696969', 'dodgerblue' => '#1e90ff', 'firebrick' => '#b22222', 'floralwhite' => '#fffaf0', 'forestgreen' => '#228b22', 'fuchsia' => '#ff00ff', 'gainsboro' => '#dcdcdc',
		'ghostwhite' => '#f8f8ff', 'gold' => '#ffd700', 'goldenrod' => '#daa520', 'gray' => '#808080', 'grey' => '#808080', 'green' => '#008000', 'greenyellow' => '#adff2f', 'honeydew' => '#f0fff0', 'hotpink' => '#ff69b4', 'indianred' => '#cd5c5c',
		'indigo' => '#4b0082', 'ivory' => '#fffff0', 'khaki' => '#f0e68c', 'lavender' => '#e6e6fa', 'lavenderblush' => '#fff0f5', 'lawngreen' => '#7cfc00', 'lemonchiffon' => '#fffacd', 'lightblue' => '#add8e6', 'lightcoral' => '#f08080', 'lightcyan' => '#e0ffff',
		'lightgoldenrodyellow' => '#fafad2', 'lightgray' => '#d3d3d3', 'lightgrey' => '#d3d3d3', 'lightgreen' => '#90ee90', 'lightpink' => '#ffb6c1', 'lightsalmon' => '#ffa07a', 'lightseagreen' => '#20b2aa', 'lightskyblue' => '#87cefa', 'lightslategray' => '#778899', 'lightslategrey' => '#778899',
		'lightsteelblue' => '#b0c4de', 'lightyellow' => '#ffffe0', 'lime' => '#00ff00', 'limegreen' => '#32cd32', 'linen' => '#faf0e6', 'magenta' => '#ff00ff', 'maroon' => '#800000', 'mediumaquamarine' => '#66cdaa', 'mediumblue' => '#0000cd', 'mediumorchid' => '#ba55d3',
		'mediumpurple' => '#9370d8', 'mediumseagreen' => '#3cb371', 'mediumslateblue' => '#7b68ee', 'mediumspringgreen' => '#00fa9a', 'mediumturquoise' => '#48d1cc', 'mediumvioletred' => '#c71585', 'midnightblue' => '#191970', 'mintcream' => '#f5fffa', 'mistyrose' => '#ffe4e1', 'moccasin' => '#ffe4b5',
		'navajowhite' => '#ffdead', 'navy' => '#000080', 'oldlace' => '#fdf5e6', 'olive' => '#808000', 'olivedrab' => '#6b8e23', 'orange' => '#ffa500', 'orangered' => '#ff4500', 'orchid' => '#da70d6', 'palegoldenrod' => '#eee8aa', 'palegreen' => '#98fb98',
		'paleturquoise' => '#afeeee', 'palevioletred' => '#d87093', 'papayawhip' => '#ffefd5', 'peachpuff' => '#ffdab9', 'peru' => '#cd853f', 'pink' => '#ffc0cb', 'plum' => '#dda0dd', 'powderblue' => '#b0e0e6', 'purple' => '#800080', 'red' => '#ff0000',
		'rosybrown' => '#bc8f8f', 'royalblue' => '#4169e1', 'saddlebrown' => '#8b4513', 'salmon' => '#fa8072', 'sandybrown' => '#f4a460', 'seagreen' => '#2e8b57', 'seashell' => '#fff5ee', 'sienna' => '#a0522d', 'silver' => '#c0c0c0', 'skyblue' => '#87ceeb',
		'slateblue' => '#6a5acd', 'slategray' => '#708090', 'slategrey' => '#708090', 'snow' => '#fffafa', 'springgreen' => '#00ff7f', 'steelblue' => '#4682b4', 'tan' => '#d2b48c', 'teal' => '#008080', 'thistle' => '#d8bfd8', 'tomato' => '#ff6347',
		'turquoise' => '#40e0d0', 'violet' => '#ee82ee', 'wheat' => '#f5deb3', 'white' => '#ffffff', 'whitesmoke' => '#f5f5f5', 'yellow' => '#ffff00', 'yellowgreen' => '#9acd32'
	);
	
	
	/**
	* Convertie une chaine TM en HTML
	*
	* @param string $nick                   -> La chaine TM pas de limite de longueur, et peut être multiligne
	* @param int    $fontSize               -> Taille de la police
	* @param bool   $forcedDefault          -> Force la couleur par défaut à être utilise à la place de la mise en forme
	* @param string $defaultColor           -> La couleur par défaut (la couleur peut être au format HTML, nommé, array RGB, ou TM)
	* @param string $defaultBackgroundColor -> La couleur de fond (transparent est possible) (la couleur peut être au format HTML, nommé, array RGB, ou TM)
	* @return string chaine HTML sans le CSS
	*/
	public static function toHtml($nick, $fontSize = 11, $forcedShadow = false, $forcedDefault = false, $defaultColor = 'white', $defaultBackgroundColor = 'transparent'){
		if($forcedShadow){ $nick = '$s'.str_replace(array('$s', '$S'), '', $nick); }
		$linkProtocol = strtolower(self::$linkProtocol);
		
		$defaultHTMLColor = $defaultColor;
		if( empty($defaultColor) ){
			$defaultHTMLColor = 'white';
		}
		else if($defaultColor[0] == '$'){
			$defaultHTMLColor = self::colorToHtml($defaultColor);
		}
		
		$defaultHTMLBackgroundColor = $defaultBackgroundColor;
		if( empty($defaultBackgroundColor) ){
			$defaultHTMLBackgroundColor = 'black';
		}
		else if($defaultBackgroundColor[0] == '$'){
			$defaultHTMLBackgroundColor = self::colorToHtml($defaultBackgroundColor);
		}
		
		$strHtml = '<span class="tmnick_global" style="background-color:'.$defaultHTMLBackgroundColor.'">';
		$lastLink = false;
		$lastLinkTag = false;
		$stack = self::_parse($nick, $defaultColor);
		foreach($stack as $k => $ss){
			$lstClass = $lstStyle = array();
			$prefix = $suffix = '';
			$preContent = $postContent = '';
			foreach($ss['tagsOpen'] as $tag => $open){
				if( (is_string($open) and !empty($open)) or (is_bool($open) and $open) ){
					if( ($tag == 'h' or $tag == 'p' or $tag == 'l') and $lastLink !== $open ){
						$scheme = parse_url($open, PHP_URL_SCHEME);
						if( is_null($scheme) ){
							if($tag == 'l'){
								$open = 'http://'.$open;
							}
							else if($tag == 'h'){
								$open = $linkProtocol.':///:'.$open;
							}
							else if($tag == 'p'){
								$open = $linkProtocol.':///:'.$open.'?playerlogin=&lang=&nickname=&path=';
							}
						}
						
						$prefix .= '<a href="'.$open.'" target="_blank" class="'.self::$m_css[$tag].'">';
						$lastLinkTag = $tag;
						$lastLink = $open;
					}
					else if($tag == 'color'){
						if($forcedDefault){
							$lstStyle[] = 'color:'.$defaultHTMLColor;
						}
						else{
							$lstStyle[] = 'color:'.$open;
						}
					}
					else{
						$lstClass[] = self::$m_css[$tag];
					}
				}
				else{
					if($tag == $lastLinkTag and $lastLink !== $open){
						$prefix .= '</a>';
						$lastLink = false;
					}
				}
			}
			
			$strHtml .= $prefix;
			if( !empty($ss['content']) ){
				$strHtml .= '<span class="{$class$}" style="font-size: '.$fontSize.'pt;{$style$}">';
				$strHtml .= $preContent.'{$content$}'.$postContent;
				$strHtml .= '</span>';
			}
			$strHtml .= $suffix;
			
			$strHtml = str_replace('{$content$}', $ss['content'], $strHtml);
			$strHtml = str_replace('{$class$}', implode(' ', $lstClass), $strHtml);
			$strHtml = str_replace('{$style$}', implode(';', $lstStyle), $strHtml);
		}
		
		$tags = array('h', 'p', 'l');
		foreach($tags as $k => $tag){
			if( isset($ss) and $ss['tagsOpen'][$tag] ){
				$strHtml .= '</a>';
			}
		}
		
		$strHtml .= '</span>';
		
		return $strHtml;
	}
	
	
	/**
	* Affiche une chaine TM en image
	*
	* @param string $nick                 -> La chaine TM pas de limite de longueur, et peut être multiligne
	* @param string $type                 -> Le type de l'image (gif, jpg, jpeg, png, ico, bmp, wbmp, xpm)
	* @param int    $fontSize             -> Taille de la police
	* @param bool   $forceDefault         -> Force la couleur par défaut à être utilise à la place de la mise en forme
	* @param string $defaultColor         -> La couleur par défaut (la couleur peut être au format HTML, nommé, array RGB, ou TM)
	* @param string $defaultImgBackground -> Le chemin vers l'image de fond si vous en voulez une
	* @param array  $arrRelativePosition  -> Tableau assoc avec les index 'x' et 'y', donnant la position du texte par rapport au bord gauche et/ou haut de l'image de fond, ces valeur peuvent être négative, dans ce cas la valeur represente la position depuis le bord droite et/ou bas
	* @return bool
	*/
	public static function toImg($nick, $type = 'png', $fontSize = 12, $forceDefault = false, $defaultColor = 'white', $defaultImgBackground = null, $arrRelativePosition = array() ){
		if(self::_isSupportedImageType($type) === false){
			$imErr = self::_getErrorRessource('Le type "'.$type.'" n\'est pas supporté.');
			return self::_getImageFromRessource($imErr, 'png');
		}
		
		$res = true;
		$nickText = self::toText($nick);
		
		$lines = str_replace('\r\n', '\n', $nick);
		$lines = str_replace('\r', '\n', $lines);
		$lines = explode('\n', $lines);
		
		// this gets the length of the longest string, in characters to determine
		// the width of the output image
		$xMax = -1;
		$len = 0;
		$count_lines =  count($lines);
		for($x = 0; $x < $count_lines; $x++){
			if(strlen($lines[$x]) > $len){
				$len = strlen(self::toText($lines[$x]));
				$xMax = $x;
			}
		}
		
		$defaultRGBColor = self::colorToRGB($defaultColor);
		
		$width = $height = 0;
		
		// initialization
		$fontFile = self::$m_fontPath.'FRADMIT.TTF';
		$box = self::_imagettfbboxextended($fontSize, 0, $fontFile, self::toText($lines[$xMax]));
		
		// next we turn the height and width into pixel values
		$width = $box['width'] * 2;
		$height = $box['height'] * 2;
		$baseline = $box['y'];
		
		// create image with dimensions to fit text, plus two extra rows and
		// two extra columns for border
		$hasBgImd = false;
		$im = null;
		if( !is_null($defaultImgBackground) and file_exists($defaultImgBackground) ){
			$infos = getimagesize($defaultImgBackground);
			$width = $infos[0];
			$height = $infos[0];
			
			$imgString = file_get_contents($defaultImgBackground);
			$im = imagecreatefromstring($imgString);
			$hasBgImd = true;
		}else{
			$im = imagecreate($width, $height);
		}
		
		if($im){
			// image creation success
			$backColor = imagecolorallocatealpha($im, 254, 254, 254, 127);
			$shadowColor = imagecolorallocatealpha($im, 105, 105, 105, 0);
			if(!$hasBgImd){
				imagefill($im, 0, 0, $backColor);
			}
			
			// this loop outputs the error message to the image
			// imagestring(image, font, x, y, msg, color);
			$x = $y = 0;
			
			if(is_array($arrRelativePosition) and count($arrRelativePosition) > 0){
				if( isset($arrRelativePosition['y']) ){
					if($arrRelativePosition['y'] < 0){
						$y = $width;
					}
					$y += $arrRelativePosition['y'];
				}
				if( isset($arrRelativePosition['x']) ){
					if($arrRelativePosition['x'] < 0){
						$x = $height;
					}
					$x += $arrRelativePosition['x'];
				}
			}
			
			$newX = 0;
			$newY = 0;
			for($i = 0; $i < count($lines); $i++){
				$stack = self::_parse($lines[$i]);
				$xl = $x;
				
				foreach($stack as $k => $ss){
					if($forceDefault){
						$color = $defaultRGBColor;
					}else{
						$color = self::colorToRGB($ss['tagsOpen']['color']);
					}
					$textColor = imagecolorallocatealpha($im, $color[0], $color[1], $color[2], 0);
					$fontText = self::$m_fontPath.'FRA';
					if($ss['tagsOpen']['o'] === true){
						$fontText .= self::$m_fonts['o'];
					}else{
						$fontText .= self::$m_fonts['z'];
					}
					foreach($ss['tagsOpen'] as $t => $o){
						if($o === true and $t != 'o' and $t != 'z'){
							$fontText .= self::$m_fonts[$t];
						}
					}
					$fontText .= '.TTF';
					
					$tbox = self::_imagettfbboxextended($fontSize, 0, $fontText, $ss['content']);
					
					if($ss['tagsOpen']['s'] === true){
						imagettftext($im, $fontSize, 0, $xl + $tbox['x'] + 1, $y + $baseline + ($i * ($tbox['height'] + 5)) + 1, $shadowColor, $fontText, $ss['content']);
					}
					imagettftext($im, $fontSize, 0, $xl + $tbox['x'], $y + $baseline + ($i * ($tbox['height'] + 5)), $textColor, $fontText, $ss['content']);
					
					$xl += $tbox['width'];
					
					if($newX < $xl){
						$newX = $xl;
					}
					if( $newY < ($y + $baseline + ($i * ($tbox['height'] + 5))) + (abs($baseline - $tbox['y'])) ){
						$newY = ($y + $baseline + ($i * ($tbox['height'] + 5))) + (abs($baseline - $tbox['y']));
					}
					if($ss['tagsOpen']['s'] === true){
						$newX++;
						$newY++;
					}
				}
			}
			
			// now, render your image using your favorite image* function
			// (imagejpeg, for instance)
			if(!$hasBgImd){
				$im2 = imagecreate($newX, $newY);
				imagecopyresized($im2, $im, 0, 0, 0, 0, $newX, $newY, $newX, $newY);
				
				$res = self::_getImageFromRessource($im2, $type);
				imagedestroy($im2);
			}else{
				$res = self::_getImageFromRessource($im, $type);
			}
			imagedestroy($im);
		}else{
			// image creation failed, so just dump the array along with extra error
			$error[] = 'Is GD Installed ?';
			die(var_dump($error));
		}
		
		return $res;
	}
	
	
	/**
	* Convertie une chaine TM en text brut
	*
	* @param string $nick -> La chaine TM pas de limite de longueur, et peut être multiligne
	* @return string
	*/
	public static function toText($nick){
		
		if( !is_string($nick) or empty($nick) ){
			return '';
		}
		
		$nb = preg_match_all(self::$m_pattern, $nick, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
		$matches = $matches[0];
		
		$strText = '';
		
		$lastPos = 0;
		foreach($matches as $num => $matche){
			list($code, $pos) = $matche;
			
			if($code != '$$'){
				$strText .= substr($nick, $lastPos, $pos - $lastPos);
				$lastPos = $pos + strlen($matche[0]);
			}
		}
		
		$strText .= substr($nick, $lastPos, strlen($nick) - $lastPos);
		$strText = preg_replace('/(\\[[^\\[\\]]*\\])/', '', $strText);
		
		return $strText;
	}
	
	
	/**
	* Convertie une couleur au format HTML, TM, array RGB, ou nommé en format HTML
	*
	* @param string $color
	* @return string
	*/
	public static function colorToHtml($color){
		$colorHtml = '#';
		
		if( is_string($color) and !empty($color) and strlen($color) == 4 and ($color[0] == '$' or $color[0] == '#') ){
			$color = preg_replace('/[g-z]/', self::$m_defaultHexaOnError, strtolower($color));
			$colorHtml .= $color[1];
			$colorHtml .= $color[1];
			$colorHtml .= $color[2];
			$colorHtml .= $color[2];
			$colorHtml .= $color[3];
			$colorHtml .= $color[3];
		}
		else if(is_array($color) and count($color) == 3){
			$colorHtml .= sprintf('#%02X%02X%02X', $color[1], $color[2], $color[3]);
		}
		else if(is_string($color) and !empty($color) and strlen($color) == 7 and $color[0] == '#'){
			$color = preg_replace('/[g-z]/', self::$m_defaultHexaOnError, strtolower($color));
			$colorHtml = $color;
		}
		else{
			$color = strtolower($color);
			$colorHtml = self::$m_colorNameToHtml[$color];
		}
		
		return $colorHtml;
	}
	
	
	/**
	* Convertie une couleur au format HTML, TM, array RGB, ou nommé en format RGB
	*
	* @param string $color
	* @return string
	*/
	public static function colorToRGB($color){
		$colorRGB = array(0, 0, 0);
		
		if( is_string($color) and !empty($color) and strlen($color) == 4 and ($color[0] == '$' or $color[0] == '#') ){
			$color = preg_replace('/[g-z]/', self::$m_defaultHexaOnError, strtolower($color));
			$hex = $color[1];
			$hex .= $color[1];
			$colorRGB[0] = hexdec($hex);
			$hex = $color[2];
			$hex .= $color[2];
			$colorRGB[1] = hexdec($hex);
			$hex = $color[3];
			$hex .= $color[3];
			$colorRGB[2] = hexdec($hex);
		}
		else if(is_array($color) and count($color) == 3){
			$colorRGB = $color;
		}
		else if(is_string($color) and !empty($color) and strlen($color) == 7 and $color[0] == '#'){
			$color = preg_replace('/[g-z]/', self::$m_defaultHexaOnError, strtolower($color));
			$hex = $color[1];
			$hex .= $color[2];
			$colorRGB[0] = hexdec($hex);
			$hex = $color[3];
			$hex .= $color[4];
			$colorRGB[1] = hexdec($hex);
			$hex = $color[5];
			$hex .= $color[6];
			$colorRGB[2] = hexdec($hex);
		}
		else{
			$color = strtolower($color);
			$colorRGB = self::colorToRGB(self::$m_colorNameToHtml[$color]);
		}
		
		return $colorRGB;
	}
	
	
	/**
	* Retourne le code css nécessaire à la mise en forme du code html
	*
	* @return string
	*/
	public static function getCss(){
		$css = '<style type="text/css">
			.tmnick_global {
				font-style: normal;
				font-weight: none;
				letter-spacing: 0;
			}
			.tmnick_normal { letter-spacing: 0; }
			.tmnick_short { letter-spacing: -1px; }
			.tmnick_wide { letter-spacing: 2px; }
			.tmnick_raz { font-style: normal; font-weight: none; letter-spacing: 0; }
			.tmnick_italic { font-style: italic; }
			.tmnick_bold { font-weight: bold; letter-spacing: 1px; }
			.tmnick_upper { text-transform: uppercase; }
			span.tmnick_shadow { text-shadow: 0 1px 1px #333; }
			.tmnick_shadowed { position: relative; }
			.tmnick_shadowed span { position: relative; }
			span.tmnick_shadow { position: absolute; }
			.tmnick_defaultcolor { }
			a.tmnick_internallink, a.tmnick_internallink:visited {
				color: inherit;
				text-decoration: none;
				border-bottom: thin solid Black;
			}
			a.tmnick_internallink:hover {
				color: inherit;
				text-decoration: none;
				border-bottom: thin solid Blue;
			}
			a.tmnick_internallink_withlogin, a.tmnick_internallink_withlogin:visited  {
				color: inherit;
				text-decoration: none;
				border-bottom: thin solid #F0E68C;
			}
			a.tmnick_internallink_withlogin:hover {
				color: inherit;
				text-decoration: none;
				border-bottom: thin solid Gold;
			}
			a.tmnick_externallink, a.tmnick_externallink:visited {
				color: inherit;
				text-decoration: none;
				border-bottom: thin solid Black;
			}
			a.tmnick_externallink:hover {
				color: inherit;
				text-decoration: none;
				border-bottom: thin solid Orange;
			}
		</style>';
		
		return $css;
	}
	
	
	/**
	* Enlève tous les codes de trackmania
	*
	* @param string $str   -> Texte à traiter
	* @param array $select -> Un ou plusieurs codes à enlever. Par exemple, si on veut enlever seulement le code "$s"
	* @return string texte sans codes $s, $o, etc ...
	*/
	public static function stripNadeoCode($str, $select = array() ){
		if( !empty($select) ){
			$stripNadeoCode = $select;
		}
		else{
			$stripNadeoCode = array('$w', '$W', '$n', '$N', '$o', '$O', '$i', '$I', '$t', '$T', '$s', '$S', '$g', '$G', '$z', '$Z', '$<');
		}
		
		return str_ireplace($stripNadeoCode, '', $str);
	}
	
	
	/**
	* Parse une chaine TM dans une structure de donnée interne utilisé par les méthode toHtml, toImg, et toText
	*
	* @param string $nick la chaine TM pas de limite de longueur, et peut être multiligne
	* @param string $defaultColor la couleur par défaut (la couleur peut être au format HTML, nommé, array RGB, ou TM)
	* @return array
	*/
	private static function _parse($nick, $defaultColor = 'white'){
		
		if( !is_string($nick) or empty($nick) ){
			return array();
		}
		
		$defaultHTMLColor = $defaultColor;
		if( empty($defaultColor) ){
			$defaultHTMLColor = 'white';
		}
		else if($defaultColor[0] == '$'){
			$defaultHTMLColor = self::colorToHtml($defaultColor);
		}
		
		$nb = preg_match_all(self::$m_pattern, $nick, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
		$matches = $matches[0];
		
		$strContent = '';
		$lastPos = 0;
		
		$tagsOpen = array(
			'h' => false,
			'p' => false,
			'l' => false,
			'o' => false,
			'g' => false,
			't' => false,
			'w' => false,
			'm' => false,
			'n' => false,
			'i' => false,
			's' => false,
			'color' => $defaultColor
		);
		
		$stack = array();
		foreach($matches as $num => $matche){
			list($code, $pos) = $matche;
			
			$tag = strtolower(substr($code, 1));
			$param = null;
			if(strlen($tag) == 3){
				// color
				$param = $tag;
				$tag = 'color';
			}
			
			if( !in_array($tag, self::$m_lstTags) ){
				continue;
			}
			
			$strContent = substr($nick, $lastPos, $pos - $lastPos);
			if( !empty($strContent) ){
				array_push($stack, array('content' => $strContent, 'tagsOpen' => $tagsOpen));
			}
			
			switch($tag){
				// raz (close)
				case 'z':
					$tagsOpen = array_merge($tagsOpen,
						array(
							'o' => false,
							'g' => false,
							't' => false,
							'w' => false,
							'm' => false,
							'n' => false,
							'i' => false,
							's' => false,
							'color' => $defaultColor
						)
					);
					break;
				
				// defaultcolor (close)
				case 'g':
					$tagsOpen['color'] = $defaultColor;
					break;
				
				// internal link (open)
				case 'h':
				// internal link with login (open)
				case 'p':
				// external link (open)
				case 'l':
					if(!$tagsOpen[$tag]){
						// open
						$tagsOpen[$tag] = true;
					}
					else{
						// close
						$strLink = '';
						$i = count($stack) - 1;
						while($i >= 0 and $stack[$i]['tagsOpen'][$tag]){
							$strLink = $stack[$i]['content'] . $strLink;
							$i--;
						}
						
						$url = null;
						$nb = preg_match(self::$m_patternLink, $strLink, $regs, PREG_OFFSET_CAPTURE);
						
						if( !empty($regs['label'][0]) ){
							if( empty($regs['url'][0]) ){
								$url = $regs['label'][0];
							}else{
								$url = $regs['url'][0];
							}
						}
						
						$nb = count($stack) - 1;
						$lastMax = strlen($strLink);
						for($j = $nb; $j >= ($i+1); $j--){
							if( !is_null($url) ){
								$subLabel = $stack[$j]['content'];
								$max = max($lastMax - strlen($subLabel), $regs['label'][1]);
								$subLabel = substr($strLink, $max, $lastMax - $max);
								$lastMax = $max;
								$stack[$j]['content'] = $subLabel;
								
								$stack[$j]['tagsOpen'][$tag] = $url;
							}else{
								// poubelle
								unset($stack[$j]);
							}
						}
						
						$tagsOpen[$tag] = false;
					}
					break;
					
				// shadow (open)
				case 's':
				// upper
				case 't':
				// bold
				case 'o':
				// italic
				case 'i':
					if(!$tagsOpen[$tag]){
						// open
						$tagsOpen[$tag] = true;
					}else{
						// close
						$tagsOpen[$tag] = false;
					}
					break;
				
				// wide (open)
				case 'w':
				// normal (open)
				case 'm':
				// short (open)
				case 'n':
					if(!$tagsOpen[$tag]){
						// open
						$tagsOpen['w'] = false;
						$tagsOpen['m'] = false;
						$tagsOpen['n'] = false;
						$tagsOpen[$tag] = true;
					}else{
						// close
						$tagsOpen[$tag] = false;
					}
					break;
				
				case 'color':
					// color (open)
					$tagsOpen[$tag] = self::colorToHtml($code);
					break;
				
				default:
					$stack[(count($stack) - 1)]['content'] .= $code;
					break;
			}
			
			foreach($stack as $s => $st){
				if( empty($st['content']) ){
					unset($stack[$s]);
				}
			}
			
			$lastPos = $pos + strlen($matche[0]);
		}
		
		$strContent = substr($nick, $lastPos);
		if( !empty($strContent) ){
			array_push($stack, array('content' => $strContent, 'tagsOpen' => $tagsOpen));
		}
		
		$tags = array('h', 'p', 'l');
		foreach($tags as $k => $tag){
			if($tagsOpen[$tag]){
				// close
				$strLink = '';
				$i = count($stack) - 1;
				while($i >= 0 and $stack[$i]['tagsOpen'][$tag]){
					$strLink = $stack[$i]['content'] . $strLink;
					$i--;
				}
				
				$url = null;
				$nb = preg_match(self::$m_patternLink, $strLink, $regs, PREG_OFFSET_CAPTURE);
				
				if(!empty($regs['label'][0]) ){
					if(empty($regs['url'][0]) ){
						$url = $regs['label'][0];
					}else{
						$url = $regs['url'][0];
					}
				}
				
				$nb = count($stack) - 1;
				$lastMax = strlen($strLink);
				for($j = $nb; $j >= ($i+1); $j--){
					if( !is_null($url) ){
						$subLabel = $stack[$j]['content'];
						$max = max($lastMax - strlen($subLabel), $regs['label'][1]);
						$subLabel = substr($strLink, $max, $lastMax - $max);
						$lastMax = $max;
						$stack[$j]['content'] = $subLabel;
						
						$stack[$j]['tagsOpen'][$tag] = $url;
					}else{
						// poubelle
						unset($stack[$j]);
					}
				}
				
				$tagsOpen[$tag] = false;
			}
		}
		
		return $stack;
	}
	
	
	/**
	* This function extends imagettfbbox and includes within the returned array
	* the actual text width and height as well as the x and y coordinates the
	* text should be drawn from to render correctly.  This currently only works
	* for an angle of zero and corrects the issue of hanging letters e.g. jpqg
	*
	* @param float $size
	* @param float $angle
	* @param string $fontfile
	* @param string $text
	* @return array
	*/
	private static function _imagettfbboxextended($size, $angle, $fontfile, $text){
		$bbox = imagettfbbox($size, $angle, $fontfile, $text);
	
		//calculate x baseline
		if($bbox[0] >= -1){
			$bbox['x'] = abs($bbox[0] + 1) * -1;
		}else{
			//$bbox['x'] = 0;
			$bbox['x'] = abs($bbox[0] + 2);
		}
	
		//calculate actual text width
		$bbox['width'] = abs($bbox[2] - $bbox[0]);
		if($bbox[0] < -1){
			$bbox['width'] = abs($bbox[2]) + abs($bbox[0]) - 1;
		}
		
		//caculate y baseline
		$bbox['y'] = abs($bbox[5] + 1);
		
		//calculate actaul text height
		$bbox['height'] = abs($bbox[7]) - abs($bbox[1]);
		if($bbox[3] > 0){
			$bbox['height'] = abs($bbox[7] - $bbox[1]) - 1;
		}
		
		return $bbox;
	}
	
	
	/**
	* Construit une ressource d'image d'erreur affcihant toute les erreur de $errors
	*
	* @param mixed $errors string of error or array of string of error
	* @return ressource
	*/
	private static function _getErrorRessource($errors){
		// $error is an array of error messages, each taking up one line
		if( is_string($errors) ){
			$errors = str_replace('\r\n', '\n', $errors);
			$errors = str_replace('\r', '\n', $errors);
			$errors = explode('\n', $errors);
		}
		
		$error = array();
		foreach($errors as $k => $err){
			$err = str_replace('\r\n', '\n', $err);
			$err = str_replace('\r', '\n', $err);
			$error = array_merge($error, explode('\n', $err));
		}
		
		// initialization
		$fontSize = 12;
		
		// this gets the length of the longest string, in characters to determine
		// the width of the output image
		$xMax = -1;
		$len = 0;
		for($x = 0; $x < count($error); $x++){
			if(strlen($error[$x]) > $len){
				$len = strlen($error[$x]);
				$xMax = $x;
			}
		}
		
		$fontFile = self::$m_fontPath.'FRAMD.TTF';
		$box = imagettfbbox($fontSize, 0, $fontFile, $error[$xMax]);
		
		// next we turn the height and width into pixel values
		$width = abs($box[2] - $box[0]);
		$textHeight = abs($box[1] - $box[7]);
		$height = ($textHeight + 2) * count($error);
		
		$im = imagecreate($width, $height);
		
		if($im){
			// image creation success
			$textColor = imagecolorallocatealpha($im, 255, 0, 0, 0);
			$backColor = imagecolorallocatealpha($im, 255, 255, 255, 127);
			imagefill($im, 0, 0, $backColor);
			// this loop outputs the error message to the image
			for($y = 0; $y < count($error); $y++){
				// imagestring(image, font, x, y, msg, color);
				imagettftext($im, $fontSize, 0, 0, ($textHeight + 5) * $y + $textHeight, $textColor, $fontFile, $error[$y]);
			}
			// now, render your image using your favorite image* function
			// (imagejpeg, for instance)
			return $im;
		}else{
			// image creation failed, so just dump the array along with extra error
			$error[] = "Is GD Installed?";
			die(var_dump($error));
		}
	}
	
	
	/**
	* Détermine si le type passé est supporté
	*
	* @param string $type type supporté (gif, jpg, jpeg, png, ico, bmp, wbmp, xpm)
	* @return bool
	*/
	private static function _isSupportedImageType($type){
		$type = strtoupper($type);
		switch($type){
			case 'GIF':
				return IMG_GIF;
				break;
			case 'JPG':
				return IMG_JPG;
				break;
			case 'JPEG':
				return IMG_JPEG;
				break;
			case 'PNG':
				return IMG_PNG;
				break;
			case 'ICO':
			case 'BMP':
			case 'WBMP':
				return IMG_WBMP;
				break;
			case 'XPM':
				return IMG_XPM;
				break;
			default:
				return false;
				break;
		}
	}
	
	
	/**
	* Affiche l'image correspondant à la ressource dans le format souhaité
	*
	* @param ressource $im ressource de l'image
	* @param string $type type de l'image de sortie
	* @return bool
	*/
	private static function _getImageFromRessource($im, $type){
		if( ($imgtype = self::_isSupportedImageType($type)) !== false ){
			header('Content-type:'.image_type_to_mime_type($imgtype).';');
			switch($imgtype){
				case IMG_GIF:
					return imagegif($im);
					break;
				case IMG_JPG:
				case IMG_JPEG:
					return imagejpeg($im);
					break;
				case IMG_PNG:
					return imagepng($im);
					break;
				case IMG_WBMP:
					return imagewbmp($im);
					break;
				case IMG_XPM:
					return imagexbm($im);
					break;
			}
		}
		header('Content-type:'.image_type_to_mime_type(IMG_PNG).';');
		return imagepng(self::_getErrorRessource('Le format "'.$type.'" n\'est pas supporté.'));
	}
}
?>