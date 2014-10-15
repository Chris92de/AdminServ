<?php
	// EDITION
	if( isset($_POST['editserver']) ){
		$serverId = AdminServServerConfig::getServerId($_POST['server'][0]);
		Utils::redirection(false, '?p=config-addserver&id='.$serverId);
	}
	
	// DUPLIQUER
	if( isset($_POST['duplicateserver']) ){
		// GET
		$getServerData = AdminServServerConfig::getServer($_POST['server'][0]);
		
		// SET
		$setServerData = array(
			'name' => trim( htmlspecialchars( addslashes($_POST['server'][0] . ' - '.Utils::t('copy') ) ) ),
			'address' => trim($getServerData['address']),
			'port' => intval($getServerData['port']),
			'matchsettings' => trim($getServerData['matchsettings']),
			'adminlevel' => array(
				'SuperAdmin' => $getServerData['adminlevel']['SuperAdmin'],
				'Admin' => $getServerData['adminlevel']['Admin'],
				'User' => $getServerData['adminlevel']['User'],
			)
		);
		if( AdminServServerConfig::saveServerConfig($setServerData) ){
			$action = Utils::t('This server has been duplicated.');
			AdminServ::info($action);
			AdminServLogs::add('action', $action);
			Utils::redirection(false, '?p='.USER_PAGE);
		}
		else{
			AdminServ::error( Utils::t('Unable to duplicate server.') );
		}
	}
	
	// SUPPRESSION
	if( isset($_POST['deleteserver']) ){
		$servers = ServerConfig::$SERVERS;
		unset($servers[$_POST['server'][0]]);
		if( ($result = AdminServServerConfig::saveServerConfig(array(), -1, $servers)) !== true ){
			AdminServ::error( Utils::t('Unable to delete server.').' ('.$result.')');
		}
		else{
			$action = Utils::t('The "!serverName" server has been deleted.', array('!serverName' => $_POST['server'][0]));
			AdminServ::info($action);
			AdminServLogs::add('action', $action);
			Utils::redirection(false, '?p='.USER_PAGE);
		}
	}
	
	// SERVERLIST
	$data['servers'] = array();
	if (is_array(ServerConfig::$SERVERS) && !empty(ServerConfig::$SERVERS)) {
		$data['servers'] = ServerConfig::$SERVERS;
	}
	$data['count'] = count($data['servers']);
	$data['adminLevelsType'] = AdminServAdminLevel::getDefaultType();
?>