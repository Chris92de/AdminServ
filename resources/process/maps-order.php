<?php
	// GAME VERSION
	if(SERVER_VERSION_NAME == 'TmForever'){
		$queries = array(
			'chooseNextMap' => 'ChooseNextChallengeList'
		);
	}
	else{
		$queries = array(
			'chooseNextMap' => 'ChooseNextMapList'
		);
	}
	
	// ACTIONS
	if( isset($_POST['save']) && isset($_POST['list']) && $_POST['list'] != null ){
		$list = explode(',', $_POST['list']);
		
		if( !$client->query($queries['chooseNextMap'], $list) ){
			AdminServ::error();
		}
		else{
			AdminServLogs::add('action', 'Order map list');
			Utils::redirection(false, '?p='.USER_PAGE);
		}
	}
	
	
	// MAPLIST
	$data['maps'] = AdminServ::getMapList();
	unset($data['maps']['lst'][$data['maps']['cid']]);
?>