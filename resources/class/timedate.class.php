<?php
abstract class TimeDate {
	
	/**
	* Retourne la date suivant la langue
	*
	* @param string $format    -> Le format de la fonction strftime()
	* @param int    $timestamp -> Le temps en sec
	* @param string $lang      -> La langue de la date retournée
	* @return string
	*/
	public static function date($format, $timestamp = null, $lang = 'fr'){
		if($timestamp === null){
			$timestamp = time();
		}
		setlocale(LC_ALL, $lang);
		return strftime($format, $timestamp);
	}
	
	
	/**
	* Rentourne la date au format INT
	*
	* @param string $date          -> La date sous la forme JJ/MM/AAAA, JJ.MM.AAAA, JJ-MM-AAAA ou JJ MM AAAA sinon mettre $use_strtotime sur true
	* @param bool   $use_strtotime -> Utiliser la méthode php strtotime pour une date d'un autre format
	* @return int
	*/
	public static function dateToTime($date, $use_strtotime = false){
		$out = 0;
		
		if($date){
			$date = trim($date);
			if( strstr($date, '/') && !$use_strtotime){
				$date_ex = explode('/', $date);
			}
			else if( strstr($date_ex, '.')  && !$use_strtotime){
				$date_ex = explode('.', $date_ex);
			}
			else if( strstr($date_ex, '-')  && !$use_strtotime){
				$date_ex = explode('-', $date_ex);
			}
			else if( strstr($date_ex, ' ')  && !$use_strtotime){
				$date_ex = explode(' ', $date_ex);
			}
			
			if( !$use_strtotime ){
				$out = mktime(0, 0, 0, $date_ex[1], $date_ex[0], $date_ex[2]); 
			}
			else{
				$out = strtotime($date);
			}
		}
		
		return $out;
	}
	
	
	/**
	* Retourne un temps au format INT
	*
	* @param string $time          -> Le temps sous la forme HH:MM:SS, HH:MM.SS, HH.MM.SS, HH-MM-SS ou HH MM SS sinon mettre $use_strtotime sur true
	* @param bool   $use_strtotime -> Utiliser la méthode php strtotime pour un temps d'un autre format
	* @return int
	*/
	public static function timeToSec($time, $use_strtotime){
		$out = 0;
		$h = 3600;
		$m = 60;
		$s = 1;
		
		if($time){
			$time = trim($time);
			if( strstr($time, ':') && !strstr($time, '.') && !$use_strtotime){
				$time_ex = explode(':', $time);
			}
			else if( strstr($time, ':') && strstr($time, '.') && !$use_strtotime){
				$time_ex = explode(':', $time);
				$time_ex2 = explode('.', $time);
				$out = ($time_ex[0] * $h) + ($time_ex[1] * $m) + ($time_ex2[1] * $s); 
			}
			else if( strstr($time, '.') && !strstr($time, ':') && !$use_strtotime){
				$time_ex = explode('.', $time);
			}
			else if( strstr($time, '-') && !$use_strtotime){
				$time_ex = explode('-', $time);
			}
			else if( strstr($time, ' ') && !$use_strtotime){
				$time_ex = explode(' ', $time);
			}
			
			
			if( !$use_strtotime && $out === 0){
				$out = ($time_ex[0] * $h) + ($time_ex[1] * $m) + ($time_ex[2] * $s); 
			}
			else{
				$out = strtotime($time);
			}
		}
		
		return $out;
	}
	
	
	/**
	* Formate une date pour une entrée MySQL
	*
	* @param string $type          -> Le type Mysql à retourner
	* @param string $date          -> La date sous la forme JJ/MM/AAAA, JJ.MM.AAAA, JJ-MM-AAAA ou JJ MM AAAA sinon mettre $use_strtotime sur true
	* @param string $time          -> Le temps sous la forme HH:MM:SS, HH:MM.SS, HH.MM.SS, HH-MM-SS ou HH MM SS
	* @param bool   $use_strtotime -> Utiliser la méthode php strtotime pour une date d'un autre format
	* @return string
	*/
	public static function formatDateForMySQL($type, $date, $time = '00:00:00', $use_strtotime = false){
		$out = '000-00-00 00:00:00';
		$type = strtoupper($type);
		$date = trim($date);
		
		switch($type){
			case 'DATE':
				$out = date('Y-m-d', self::dateToTime($date, $use_strtotime));
				break;
			case 'DATETIME':
				$datetotime = self::dateToTime($date, $use_strtotime);
				$timetosec = self::timeToSec($time, $use_strtotime);
				$out = date('Y-m-d H:i:s', $datetotime + $timetosec);
				break;
			case 'TIME':
				$out = date('H:i:s', self::timeToSec($time, $use_strtotime));
				break;
			case 'YEAR':
				$out = date('Y', self::dateToTime($date, $use_strtotime));
				break;
			default:
				$out = self::formatDateForMySQL('DATETIME', $date, $time, $use_strtotime);
		}
		return $out;
	}
	
	
	/**
	* Récupère l'âge à partir de l'année de naissance
	*
	* @param string $birthday -> Sous la forme jj/mm/aaaa ou autre
	*/
	public static function getYearOld($birthday){
		$out = null;
		$birthdayDate = getdate( self::dateToTime($birthday) );
		$currentDate = getdate();
		
		if( $birthdayDate['mon'] < $currentDate['mon'] || ($birthdayDate['mon'] == $currentDate['mon'] && $birthdayDate['mday'] <= $currentDate['mday']) ){
			$out = $currentDate['year'] - $birthdayDate['year'];
		}
		else{
			$out = $currentDate['year'] - $birthdayDate['year'] - 1;
		}
		
		return $out;
	}
	
	
	/**
	* Retourne une date relative sous la forme il y a x jours/heures/minutes/secondes
	*
	* @param int $time -> Temps en seconde
	* @return string
	*/
	public static function relativeTime($time){
		$out = null;
		$timeDifference = time() - $time;
		
		if($timeDifference > 0){
			$seconds = $timeDifference;
			$minutes = round($timeDifference/60);
			$hours = round($timeDifference/3600);
			$days = round($timeDifference/86400);
			$weeks = round($timeDifference/604800);
			$months = round($timeDifference/2419200);
			$years = round($timeDifference/29030400);
			
			if($seconds < 60){
				$out .= 'Il y a moins d\'une minute';
			}
			else if($minutes < 60){
				$out .= 'Il y a '.$minutes.' minute'; if($minutes > 1){ $out .= 's'; }
			}
			else if($hours < 24){
				$out .= 'Il y a '.$hours.' heure'; if($hours > 1){ $out .= 's'; }
			}
			else if($days < 7){
				$out .= 'Il y a '.$days.' jour'; if($days > 1){ $out .= 's'; }
			}
			else if($weeks < 4){
				$out .= 'Il y a '.$weeks.' semaine'; if($weeks > 1){ $out .= 's'; }
			}
			else if($months < 12){
				$out .= 'Il y a '.$months.' mois';
			}
			else{
				$out .= 'Il y a '.$years.' an'; if($years > 1){ $out .= 's'; }
			}
		}
		
		return $out;
	}
	
	
	/**
	* Transforme un temps en seconde sous forme 0 jour 0 heure 0 minute 0 seconde
	*
	* @param  int  $sec      -> Secondes à transformer
	* @param  bool $fullText -> Retourner le texte en entier
	* @return string
	*/
	public static function secToStringTime($sec, $fullText = true){
		$out = null;
		
		$year = 1*60*60*24*365;
		$day = 1*60*60*24;
		$hour = 1*60*60;
		$minute = 1*60;
		$second = 1;
		
		$timeDifference = intval($sec);
		$yearCount = floor($timeDifference / $year);
		$timeDifference -= $yearCount * $year;
		$dayCount = floor($timeDifference / $day);
		$timeDifference -= $dayCount * $day;
		$hourCount = floor($timeDifference / $hour);
		$timeDifference -= $hourCount * $hour;
		$minuteCount = floor($timeDifference / $minute);
		$timeDifference -= $minuteCount * $minute;
		$secondCount = floor($timeDifference / $second);
		$timeDifference -= $secondCount * $second;
		
		$strDay = $dayCount.'j'; if($fullText){ $strDay .= 'our'; if($dayCount > 1){ $strDay .= 's'; } }
		$strHour = $hourCount.'h'; if($fullText){ $strHour .= 'eure'; if($hourCount > 1){ $strHour .= 's'; } }
		$strMinute = $minuteCount.'min'; if($fullText){ $strMinute .= 'ute'; if($minuteCount > 1){ $strMinute .= 's'; } }
		$strSecond = $secondCount.'sec'; if($fullText){ $strSecond .= 'onde'; if($secondCount > 1){ $strSecond .= 's'; } }
		
		if($dayCount != 0){
			$out .= $strDay.', '.$strHour.' '.$strMinute.' '.$strSecond;
		}
		else if($dayCount == 0 && $hourCount != 0){
			$out .= $strHour.' '.$strMinute.' '.$strSecond;
		}
		else if($dayCount == 0 && $hourCount == 0){
			$out .= $strMinute.' '.$strSecond;
		}
		
		return $out;
	}
	
	
	/**
	* Forme le temps millisecondes en 00:00:00
	*
	* @param  string $millisec -> Temps en millisecondes
	* @return string
	*/
	public static function format($millisec){
		$cen = intval( ($millisec % 1000) / 10 );
		if($cen < 10){
			$cen = '0'.$cen;
		}
		$sec = intval( ($millisec / 1000) % 60 );
		if($sec < 10){
			$sec = '0'.$sec;
		}
		$min = intval($millisec / 60000);
		if($min < 10){
			$min = '0'.$min;
		}
		
		return $min.':'.$sec.':'.$cen;
	}
	
	
	/**
	* Méthodes de convertion du temps (Min, sec, millisec)
	*/
	public static function secToMillisec($sec){
		$sec = intval( round($sec) );
		$millisec = $sec * 1000;
		if($millisec > 0){ return $millisec; }
		else{ return 0; }
	}
	public static function millisecToSec($millisec){
		$millisec = intval( round($millisec) );
		$sec = $millisec / 1000;
		if($sec > 0){ return $sec; }
		else{ return 0; }
	}
	public static function secToMin($sec){
		$sec = intval( round($sec) );
		$min = $sec / 60;
		if($min > 0){ return $min; }
		else{ return 0; }
	}
	public static function minToSec($min){
		$min = intval( round($min) );
		$sec = $min * 60;
		if($sec > 0){ return $sec; }
		else{ return 0; }
	}
	public static function millisecToMin($millisec){
		$millisec = intval( round($millisec) );
		$sec = $millisec / 1000;
		$min = $sec / 60;
		if($min > 0){ return $min; }
		else{ return 0; }
	}
}
?>