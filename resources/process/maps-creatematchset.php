<?php
	// ENREGISTREMENT
	if( isset($_POST['savematchsetting']) && isset($_SESSION['adminserv']['matchset_maps_selected']) ){
		// Filename
		$matchSettingName = Str::replaceChars($_POST['matchSettingName']);
		$filename = $data['mapsDirectoryPath'].$args['directory'].$matchSettingName;
		if(File::getExtension($matchSettingName) != 'txt'){
			$filename .= '.txt';
		}
		
		$struct = array();
		
		// Gameinfos
		$gameinfos = AdminServ::getGameInfosStructFromPOST();
		$struct['gameinfos'] = array(
			'game_mode' => $gameinfos['GameMode'],
			'chat_time' => $gameinfos['ChatTime'],
			'finishtimeout' => $gameinfos['FinishTimeout'],
			'allwarmupduration' => $gameinfos['AllWarmUpDuration'],
			'disablerespawn' => $gameinfos['DisableRespawn'],
			'forceshowallopponents' => $gameinfos['ForceShowAllOpponents'],
			'rounds_pointslimit' => $gameinfos['RoundsPointsLimit'],
			'rounds_custom_points' => $gameinfos['RoundCustomPoints'],
			'rounds_usenewrules' => $gameinfos['RoundsUseNewRules'],
			'rounds_forcedlaps' => $gameinfos['RoundsForcedLaps'],
			'rounds_pointslimitnewrules' => $gameinfos['RoundsPointsLimitNewRules'],
			'team_pointslimit' => $gameinfos['TeamPointsLimit'],
			'team_maxpoints' => $gameinfos['TeamMaxPoints'],
			'team_usenewrules' => $gameinfos['TeamUseNewRules'],
			'team_pointslimitnewrules' => $gameinfos['TeamPointsLimitNewRules'],
			'timeattack_limit' => $gameinfos['TimeAttackLimit'],
			'timeattack_synchstartperiod' => $gameinfos['TimeAttackSynchStartPeriod'],
			'laps_nblaps' => $gameinfos['LapsNbLaps'],
			'laps_timelimit' => $gameinfos['LapsTimeLimit'],
			'cup_pointslimit' => $gameinfos['CupPointsLimit'],
			'cup_roundsperchallenge' => $gameinfos['CupRoundsPerMap'],
			'cup_nbwinners' => $gameinfos['CupNbWinners'],
			'cup_warmupduration' => $gameinfos['CupWarmUpDuration']
		);
		if(SERVER_VERSION_NAME != 'TmForever'){
			$struct['gameinfos']['script_name'] = $gameinfos['ScriptName'];
		}
		
		// HotSeat
		$struct['hotseat'] = array(
			'game_mode' => intval($_POST['hotSeatGameMode']),
			'time_limit' => TimeDate::secToMillisec( intval($_POST['hotSeatTimeLimit']) ),
			'rounds_count' => intval($_POST['hotSeatCountRound'])
		);
		
		// Filter
		$struct['filter'] = array(
			'is_lan' => array_key_exists('filterIsLan', $_POST),
			'is_internet' => array_key_exists('filterIsInternet', $_POST),
			'is_solo' => array_key_exists('filterIsSolo', $_POST),
			'is_hotseat' => array_key_exists('filterIsHotSeat', $_POST),
			'sort_index' => intval($_POST['filterSortIndex']),
			'random_map_order' => array_key_exists('filterRandomMaps', $_POST),
			'force_default_gamemode' => intval($_POST['filterDefaultGameMode']),
		);
		
		// ScriptSettings
		if( !$client->query('GetModeScriptInfo') ){
			AdminServ::error();
		}
		else{
			$scriptsettings = $client->getResponse();
			
			if( !empty($scriptsettings['ParamDescs']) ){
				foreach($scriptsettings['ParamDescs'] as $param){
					$struct['scriptsettings'][] = array(
						'name' => $param['Name'],
						'type' => $param['Type'],
						'value' => $param['Default']
					);
				}
			}
		}
		
		// Maps
		$struct['startindex'] = 1;
		$maps = $_SESSION['adminserv']['matchset_maps_selected']['lst'];
		if( isset($maps) && is_array($maps) && !empty($maps) ){
			$mapsField = (SERVER_VERSION_NAME == 'TmForever') ? 'challenge' : 'map';
			foreach($maps as $id => $values){
				$struct[$mapsField][$values['UId']] = $values['FileName'];
			}
		}
		
		
		// Enregistrement
		if( ($result = AdminServ::saveMatchSettings($filename, $struct)) !== true ){
			AdminServ::error(Utils::t('Unable to save the MatchSettings').' : '.$matchSettingName.' ('.$result.')');
		}
		else{
			$action = Utils::t('The MatchSettings "!matchSettingName" was successfully created in the folder', array('!matchSettingName' => $matchSettingName)).' : '.$data['mapsDirectoryPath'].$args['directory'];
			AdminServ::info($action);
			AdminServLogs::add('action', $action);
			Utils::redirection(false, '?p='.USER_PAGE .$hasDirectory);
		}
	}
	else{
		if( !isset($_GET['f']) ){
			unset($_SESSION['adminserv']['matchset_maps_selected']);
		}
	}
	
	
	// LECTURE
	$data['directoryList'] = Folder::getArborescence($data['mapsDirectoryPath'], AdminServConfig::$MAPS_HIDDEN_FOLDERS, substr_count($data['mapsDirectoryPath'], '/'));
	$data['matchSettings'] = array();
	// Édition
	if( isset($_GET['f']) && $_GET['f'] != null ){
		$data['pageTitle'] = Utils::t('Edit');
		$data['matchSettings']['name'] = $_GET['f'];
		$matchSettingsData = AdminServ::getMatchSettingsData($data['mapsDirectoryPath'].$args['directory'].$data['matchSettings']['name']);
		$data['gameInfos'] = array(
			'curr' => null,
			'next' => $matchSettingsData['gameinfos']
		);
		unset($matchSettingsData['gameinfos']);
		$data['matchSettings'] += $matchSettingsData;
		if( isset($data['matchSettings']['maps']) ){
			$maps = AdminServ::getMapListFromMatchSetting($data['matchSettings']['maps']);
			$data['matchSettings']['nbm'] = $maps['nbm']['count'];
			$_SESSION['adminserv']['matchset_maps_selected'] = $maps;
		}
		else{
			$data['matchSettings']['nbm'] = 0;
		}
	}
	else{
		$data['pageTitle'] = Utils::t('Create');
		$data['matchSettings']['name'] = 'match_settings';
		$gameInfos = AdminServ::getGameInfos();
		$data['gameInfos'] = array(
			'curr' => null,
			'next' => $gameInfos['next']
		);
		$data['matchSettings']['hotseat'] = array(
			'GameMode' => 1,
			'TimeLimit' => 300000,
			'RoundsCount' => 5
		);
		$data['matchSettings']['filter'] = array(
			'IsLan' => 1,
			'IsInternet' => 1,
			'IsSolo' => 0,
			'IsHotseat' => 1,
			'SortIndex' => 1000,
			'RandomMapOrder' => 0,
			'ForceDefaultGameMode' => 1
		);
		$data['matchSettings']['StartIndex'] = 0;
		$data['matchSettings']['nbm'] = 0;
	}
?>