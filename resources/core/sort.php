<?php

/**
* Classe pour le traitement des tris AdminServ
*/
class AdminServSort {
	
	
	public static function sortByNickName($a, $b){
		$a = TmNick::toText($a['NickName']);
		$b = TmNick::toText($b['NickName']);
		
		if($a == $b){
			return 0;
		}
		return ($a < $b) ? -1 : 1;
	}
	public static function sortByLadderRanking($a, $b){
		if($a['LadderRanking'] == $b['LadderRanking']){
			return 0;
		}
		return ($a['LadderRanking'] < $b['LadderRanking']) ? -1 : 1;
	}
	public static function sortByLogin($a, $b){
		if($a['Login'] == $b['Login']){
			return 0;
		}
		return ($a['Login'] < $b['Login']) ? -1 : 1;
	}
	public static function sortByStatus($a, $b){
		if($a['SpectatorStatus'] == $b['SpectatorStatus']){
			return 0;
		}
		return ($a['SpectatorStatus'] < $b['SpectatorStatus']) ? -1 : 1;
	}
	public static function sortByTeam($a, $b){
		if($a['TeamId'] == 0){
			$a['TeamId'] = 'blue';
		}else if($a['TeamId'] == 1){
			$a['TeamId'] = 'red';
		}else{
			$a['TeamId'] = 'spectator';
		}
		if($b['TeamId'] == 0){
			$b['TeamId'] = 'blue';
		}else if($b['TeamId'] == 1){
			$b['TeamId'] = 'red';
		}else{
			$b['TeamId'] = 'spectator';
		}
		
		if($a['TeamId'] == $b['TeamId']){
			return 0;
		}
		return ($a['TeamId'] < $b['TeamId']) ? -1 : 1;
	}
	
	/* Maps-list */
	public static function sortByFileName($a, $b){
		if($a['FileName'] == $b['FileName']){
			return 0;
		}
		return ($a['FileName'] < $b['FileName']) ? -1 : 1;
	}
	public static function sortByName($a, $b){
		$a = strip_tags($a['Name']);
		$b = strip_tags($b['Name']);
		
		if($a == $b){
			return 0;
		}
		return ($a < $b) ? -1 : 1;
	}
	public static function sortByEnviro($a, $b){
		if($a['Environment'] == 'Speed'){
			$a['Environment'] = 'Desert';
		}
		if($b['Environment'] == 'Speed'){
			$b['Environment'] = 'Desert';
		}
		if($a['Environment'] == 'Alpine'){
			$a['Environment'] = 'Snow';
		}
		if($b['Environment'] == 'Alpine'){
			$b['Environment'] = 'Snow';
		}
		
		if($a['Environment'] == $b['Environment']){
			return 0;
		}
		return ($a['Environment'] < $b['Environment']) ? -1 : 1;
	}
	public static function sortByType($a, $b){
		if($a['Type']['Name'] == $b['Type']['Name']){
			return 0;
		}
		return ($a['Type']['Name'] < $b['Type']['Name']) ? -1 : 1;
	}
	public static function sortByAuthor($a, $b){
		$a = strtolower($a['Author']);
		$b = strtolower($b['Author']);
		
		if($a == $b){
			return 0;
		}
		return ($a < $b) ? -1 : 1;
	}
	public static function sortByGoldTime($a, $b){
		if($a['GoldTime'] == $b['GoldTime']){
			return 0;
		}
		return ($a['GoldTime'] < $b['GoldTime']) ? -1 : 1;
	}
	public static function sortByPrice($a, $b){
		if($a['CopperPrice'] == $b['CopperPrice']){
			return 0;
		}
		return ($a['CopperPrice'] < $b['CopperPrice']) ? -1 : 1;
	}
	public static function sortByRank($a, $b){
		if($a['Rank'] == $b['Rank']){
			return 0;
		}
		return ($a['Rank'] < $b['Rank']) ? -1 : 1;
	}
}
?>