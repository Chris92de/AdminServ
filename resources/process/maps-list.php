<?php
	// GAME
	if(SERVER_VERSION_NAME == 'TmForever'){
		$queries = array(
			'removeMap' => 'RemoveChallengeList',
			'chooseNextMap' => 'ChooseNextChallengeList'
		);
	}
	else{
		$queries = array(
			'removeMap' => 'RemoveMapList',
			'chooseNextMap' => 'ChooseNextMapList'
		);
	}
	
	$redirect=false;
	
	// ACTIONS
	if( isset($_POST['removeMap']) && isset($_POST['map']) && count($_POST['map']) > 0 ){
		if( !$client->query($queries['removeMap'], $_POST['map']) ){
			AdminServ::error();
		}
		else{
			AdminServLogs::add('action', 'Remove map ('.count($_POST['map']).')');
			$redirect=true;
		}
	}
	else if( isset($_POST['chooseNextMap']) && isset($_POST['map']) && count($_POST['map']) > 0 ){
		if( !$client->query($queries['chooseNextMap'], $_POST['map']) ){
			AdminServ::error();
		}
		else{
			AdminServLogs::add('action', 'Choose next map ('.count($_POST['map']).')');
			$redirect=true;
		}
	}
	
	//Niarfman - Save MatchSettings
	if( isset($_POST['SaveCurrentMatchSettings']) && array_key_exists('SaveCurrentMatchSettings', $_POST) ){
		if( !$client->query('SaveMatchSettings', $data['mapsDirectoryPath'] . SERVER_MATCHSET) ){
			AdminServ::error();
		}
	}
	
	if ($redirect){
		Utils::redirection(false, '?p='.USER_PAGE);
	}
	
	// MAPLIST
	$data['maps'] = AdminServ::getMapList();
?>