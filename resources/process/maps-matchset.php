<?php
	// ACTIONS
	if( isset($_POST['saveMatchset']) && isset($_POST['matchset']) && count($_POST['matchset']) > 0 ){
		foreach($_POST['matchset'] as $matchset){
			if( !$client->query('SaveMatchSettings', $data['mapsDirectoryPath'].$matchset) ){
				AdminServ::error();
			}
			else{
				AdminServLogs::add('action', 'Save matchsettings: '.$matchset);
			}
		}
		Utils::redirection(false, '?p='.USER_PAGE.$data['hasDirectory']);
	}
	else if( isset($_POST['loadMatchset']) && isset($_POST['matchset']) && count($_POST['matchset']) > 0 ){
		foreach($_POST['matchset'] as $matchset){
			if( !$client->query('LoadMatchSettings', $data['mapsDirectoryPath'].$matchset) ){
				AdminServ::error();
			}
			else{
				AdminServLogs::add('action', 'Load matchsettings: '.$matchset);
			}
		}
		Utils::redirection(false, '?p='.USER_PAGE.$data['hasDirectory']);
	}
	else if( isset($_POST['addMatchset']) && isset($_POST['matchset']) && count($_POST['matchset']) > 0 ){
		foreach($_POST['matchset'] as $matchset){
			if( !$client->query('AppendPlaylistFromMatchSettings', $data['mapsDirectoryPath'].$matchset) ){
				AdminServ::error();
			}
			else{
				AdminServLogs::add('action', 'Append playlist from matchsettings: '.$matchset);
			}
		}
	Utils::redirection(false, '?p='.USER_PAGE.$data['hasDirectory']);
	}
	else if( isset($_POST['insertMatchset']) && isset($_POST['matchset']) && count($_POST['matchset']) > 0 ){
		foreach($_POST['matchset'] as $matchset){
			if( !$client->query('InsertPlaylistFromMatchSettings', $data['mapsDirectoryPath'].$matchset) ){
				AdminServ::error();
			}
			else{
				AdminServLogs::add('action', 'Insert playlist from matchsettings: '.$matchset);
			}
		}
		Utils::redirection(false, '?p='.USER_PAGE.$data['hasDirectory']);
	}
	else if( isset($_POST['editMatchset']) && isset($_POST['matchset']) && count($_POST['matchset']) > 0 ){
		AdminServLogs::add('action', 'Edit matchsettings: '.$_POST['matchset'][0]);
		// Redirection sur la page de création d'un matchsettings
		Utils::redirection(false, '?p=maps-creatematchset'.$data['hasDirectory'].'&f='.$_POST['matchset'][0]);
	}
	else if( isset($_POST['deleteMatchset']) && isset($_POST['matchset']) && count($_POST['matchset']) > 0 ){
		foreach($_POST['matchset'] as $matchset){
			if( !File::delete($data['mapsDirectoryPath'].$matchset) ){
				AdminServ::error(Utils::t('Unable to delete the playlist').' : '.$matchset);
			}
			else{
				AdminServLogs::add('action', 'Delete matchsettings: '.$matchset);
			}
		}
		Utils::redirection(false, '?p='.USER_PAGE.$data['hasDirectory']);
	}
	
	
	// MATCH SETTINGS LIST
	$data['matchsettingsList'] = AdminServ::getLocalMatchSettingList($data['currentDir'], $args['directory']);
?>