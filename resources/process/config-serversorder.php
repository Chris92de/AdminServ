<?php
	// ACTIONS
	if( isset($_POST['save']) && isset($_POST['list']) && $_POST['list'] != null ){
		$serverList = ServerConfig::$SERVERS;
		$list = explode(',', $_POST['list']);
		
		$newServerList = array();
		foreach($list as $listServerName){
			$newServerList[$listServerName] = array(
				'address' => $serverList[$listServerName]['address'],
				'port' => $serverList[$listServerName]['port'],
				'mapsbasepath' => (isset($serverList[$listServerName]['mapsbasepath'])) ? $serverList[$listServerName]['mapsbasepath'] : '',
				'matchsettings' => $serverList[$listServerName]['matchsettings'],
				'adminlevel' => $serverList[$listServerName]['adminlevel']
			);
		}
		
		AdminServServerConfig::saveServerConfig(array(), -1, $newServerList);
		AdminServLogs::add('action', 'Order server list');
		Utils::redirection(false, '?p='.USER_PAGE);
	}
	
	
	// SERVERLIST
	$data['servers'] = array();
	if (is_array(ServerConfig::$SERVERS) && !empty(ServerConfig::$SERVERS)) {
		$data['servers'] = ServerConfig::$SERVERS;
	}
?>