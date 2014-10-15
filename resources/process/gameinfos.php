<?php
	// ENREGISTREMENT
	if( isset($_POST['savegameinfos']) ){
		// Variables
		$struct = AdminServ::getGameInfosStructFromPOST();
		
		// Requêtes
		if(!$client->query('SetGameInfos', $struct) && $client->getErrorMessage() != 'Script not allowed for this title.'){
			AdminServ::error();
		}
		else{
			// Team info
			if(SERVER_VERSION_NAME == 'ManiaPlanet' && isset($_POST['teamInfo1Name']) ){
				$team1 = array(
					'name' => $_POST['teamInfo1Name'],
					'color' => $_POST['teamInfo1Color'],
					'colorhex' => $_POST['teamInfo1ColorHex'],
					'country' => $_POST['teamInfo1Country']
				);
				$team2 =  array(
					'name' => $_POST['teamInfo2Name'],
					'color' => $_POST['teamInfo2Color'],
					'colorhex' => $_POST['teamInfo2ColorHex'],
					'country' => $_POST['teamInfo2Country']
				);
				
				AdminServ::setTeamInfo($team1, $team2);
			}
			
			// RoundCustomPoints
			if( isset($_POST['NextRoundCustomPoints']) && $_POST['NextRoundCustomPoints'] != null){
				$NextRoundCustomPoints = explode(',', $_POST['NextRoundCustomPoints']);
				$NextRoundCustomPointsArray = array();
				if( count($NextRoundCustomPoints) > 0 ){
					foreach($NextRoundCustomPoints as $point){
						$NextRoundCustomPointsArray[] = intval( trim($point) );
					}
				}
				if( !$client->query('SetRoundCustomPoints', $NextRoundCustomPointsArray) ){
					AdminServ::error();
				}
			}
			
			// MatchSettings
			if(SERVER_MATCHSET){
				$mapsDirectory = AdminServ::getMapsDirectoryPath();
				if( array_key_exists('SaveCurrentMatchSettings', $_POST) ){
					if( !$client->query('SaveMatchSettings', $mapsDirectory . SERVER_MATCHSET) ){
						AdminServ::error();
					}
				}
			}
			
			AdminServLogs::add('action', 'Save game infos');
			Utils::redirection(false, '?p='.USER_PAGE);
		}
	}
	
	
	// LECTURE
	$gameInfos = AdminServ::getGameInfos();
	$data['gameInfos'] = array(
		'curr' => $gameInfos['curr'],
		'next' => $gameInfos['next']
	);
	$data['teamInfo'] = array(
		'team1' => array(
			'name' => Utils::t('Blue'),
			'color' => '0.667',
			'colorhex' => '#0000ff',
			'country' => 'World|France'
		),
		'team2' => array(
			'name' => Utils::t('Red'),
			'color' => '0',
			'colorhex' => '#ff0000',
			'country' => 'World|France'
		)
	);
	if( isset($_SESSION['adminserv']['teaminfo']) && count($_SESSION['adminserv']['teaminfo']) > 0 ){
		$data['teamInfo'] = array(
			'team1' => $_SESSION['adminserv']['teaminfo']['team1'],
			'team2' => $_SESSION['adminserv']['teaminfo']['team2']
		);
	}
	
	unset($gameInfos);
?>