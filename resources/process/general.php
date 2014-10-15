<?php
	// ACTIONS
	if( isset($_GET['stop']) ){
		if( !$client->query('StopServer') ){
			AdminServ::error();
		}
		else{
			$client->Terminate();
			Utils::redirection(false, '?logout');
		}
	}
	else if( isset($_POST['BanLoginList']) && count($_POST['player']) > 0 ){
		foreach($_POST['player'] as $player){
			if( !$client->query('Ban', $player) ){
				AdminServ::error();
				break;
			}
			else{
				AdminServLogs::add('action', 'Ban player: '.$player);
			}
		}
	}
	else if( isset($_POST['KickLoginList']) && count($_POST['player']) > 0 ){
		foreach($_POST['player'] as $player){
			if( !$client->query('Kick', $player) ){
				AdminServ::error();
				break;
			}
			else{
				AdminServLogs::add('action', 'Kick player: '.$player);
			}
		}
	}
	else if( isset($_POST['IgnoreLoginList']) && count($_POST['player']) > 0 ){
		foreach($_POST['player'] as $player){
			if( !$client->query('Ignore', $player) ){
				AdminServ::error();
				break;
			}
			else{
				AdminServLogs::add('action', 'Ignore player: '.$player);
			}
		}
	}
	else if( isset($_POST['GuestLoginList']) && count($_POST['player']) > 0 ){
		foreach($_POST['player'] as $player){
			if( !$client->query('AddGuest', $player) ){
				AdminServ::error();
				break;
			}
			else{
				AdminServLogs::add('action', 'Add Guest player: '.$player);
			}
		}
	}
	else if( isset($_POST['ForcePlayerList']) && count($_POST['player']) > 0 ){
		foreach($_POST['player'] as $player){
			if( !$client->query('ForceSpectator', $player, 2) ){
				AdminServ::error();
				break;
			}
			else{
				if( !$client->query('ForceSpectator', $player, 0) ){
					AdminServ::error();
					break;
				}
				else{
					AdminServLogs::add('action', 'Force player mode: '.$player);
				}
			}
		}
	}
	else if( isset($_POST['ForceSpectatorList']) && count($_POST['player']) > 0 ){
		foreach($_POST['player'] as $player){
			if( !$client->query('ForceSpectator', $player, 1) ){
				AdminServ::error();
				break;
			}
			else{
				if(!$client->query('ForceSpectator', $player, 0) ){
					AdminServ::error();
					break;
				}
				else{
					AdminServLogs::add('action', 'Force spectator mode: '.$player);
				}
			}
		}
	}
	else if( (isset($_POST['ForceBlueTeam']) || isset($_POST['ForceRedTeam'])) && count($_POST['player']) > 0 ){
		if( isset($_POST['ForceBlueTeam']) ){
			$teamId = 0;
		}else{
			$teamId = 1;
		}
		
		foreach($_POST['player'] as $player){
			if( !$client->query('ForcePlayerTeam', $player, $teamId) ){
				AdminServ::error();
				break;
			}
			else{
				if($teamId == 0){ $color = 'blue'; }else{ $color = 'red'; }
				AdminServLogs::add('action', 'Force player in '.$color.' team: '.$player);
			}
		}
	}
	else if( isset($_POST['ForceScores']) && isset($_POST['ScoreTeamBlue']) && isset($_POST['ScoreTeamRed']) ){
		$scoreTeamBlue = intval($_POST['ScoreTeamBlue']);
		$scoreTeamRed = intval($_POST['ScoreTeamRed']);
		$scores = array(
			array(
				'PlayerId' => 0,
				'Score' => $scoreTeamBlue
			),
			array(
				'PlayerId' => 1,
				'Score' => $scoreTeamRed
			)
		);
		if( !$client->query('ForceScores', $scores, true) ){
			AdminServ::error();
		}else{
			$action = '[Admin] '.Utils::t('The scores have been modified : $00fblue team $fffhas !scoreTeamBlue and $f00red team $fffhas !scoreTeamRed', array('!scoreTeamBlue' => $scoreTeamBlue, '!scoreTeamRed' => $scoreTeamRed));
			if( !$client->query('ChatSendServerMessage', $action) ){
				AdminServ::error();
			}
			else{
				AdminServLogs::add('action', $action);
			}
		}
	}
	else if( isset($_POST['CancelVote']) ){
		if( !$client->query('CancelVote') ){
			AdminServ::error();
		}
		else{
			Utils::redirection();
		}
	}
	
	
	// Info serveur
	$data['serverInfo'] = AdminServ::getCurrentServerInfo();
	$data['isTeamGameMode'] = AdminServ::checkDisplayTeamMode($data['serverInfo']['srv']['gameModeId'], $data['serverInfo']['srv']['gameModeScriptName']);
	// Si on est en mode équipe, on force l'affichage en mode détail
	if($data['isTeamGameMode']){
		$_SESSION['adminserv']['mode']['general'] = 'detail';
	}
	if( defined('IS_RELAY') && IS_RELAY ){
		// @deprecated $mainServerLogin = AdminServ::getMainServerLoginFromRelay();
		$data['mainServerLogin'] = null;
	}
?>