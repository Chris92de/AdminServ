<?php
	// ENREGISTREMENT
	if( isset($_POST['saveserver']) ){
		// Variables
        $displayServPassword = trim($_POST['addDisplayServPassword']);
		$serverName = Str::replaceSpecialChars( htmlspecialchars(addslashes($_POST['addServerName'])), false);
		$serverAddress = trim($_POST['addServerAddress']);
		$serverPort = intval($_POST['addServerPort']);
		$serverMapsBasePath = trim($_POST['addServerMapsBasePath']);
		$serverMatchSet = trim($_POST['addServerMatchSet']);
		$serverAdmLvl = array(
			'SuperAdmin' => $_POST['addServerAdmLvlSA'],
			'Admin' => $_POST['addServerAdmLvlADM'],
			'User' => $_POST['addServerAdmLvlUSR']
		);
		$isNotAnArray = array('all', 'local', 'none');
		foreach($serverAdmLvl as $admLvlId => $admLvlValue){
			if( !in_array($admLvlValue, $isNotAnArray) ){
				$serverAdmLvl[$admLvlId] = explode(',', $admLvlValue);
			}
			else{
				$serverAdmLvl[$admLvlId] = trim($admLvlValue);
			}
		}
		$serverData = array(
			'name' => $serverName,
			'address' => $serverAddress,
			'port' => $serverPort,
			'mapsbasepath' => $serverMapsBasePath,
			'matchsettings' => $serverMatchSet,
			'adminlevel' => array(),
            'ds_pw' => $displayServPassword
		);
		foreach($serverAdmLvl as $admLvlId => $admLvlValue){
			$serverData['adminlevel'][$admLvlId] = $admLvlValue;
		}
		
		// Édition
		if($args['id'] !== -1){
			if( ($result = AdminServServerConfig::saveServerConfig($serverData, $args['id'])) !== true ){
				AdminServ::error( Utils::t('Unable to modify the server.').' ('.$result.')');
			}
			else{
				$action = Utils::t('This server has been modified.');
				AdminServ::info($action);
				AdminServLogs::add('action', $action);
				Utils::redirection(false, '?p=config-servers');
			}
		}
		// Ajout
		else{
			if( ($result = AdminServServerConfig::saveServerConfig($serverData)) !== true ){
				AdminServ::error( Utils::t('Unable to add the server.').' ('.$result.')');
			}
			else{
				$action = Utils::t('This server has been added.');
				AdminServ::info($action);
				AdminServLogs::add('action', $action);
				Utils::redirection(false, '?p='.USER_PAGE);
			}
		}
	}
	
	
	// LECTURE
	$data = array(
		'name' => null,
		'address' => 'localhost',
		'port' => 5000,
		'mapsbasepath' => null,
		'matchsettings' => 'MatchSettings/',
		'adminlevel' => array(
			'SuperAdmin' => 'all',
			'Admin' => 'all',
			'User' => 'all'
		),
        'ds_pw' => 'User'
	);
	if($args['id'] !== -1){
		define('IS_SERVER_EDITION', true);
		$data['name'] = AdminServServerConfig::getServerName($args['id']);
		if($data['name']){
			$serverData = AdminServServerConfig::getServer($data['name']);
			$data['address'] = $serverData['address'];
			$data['port'] = $serverData['port'];
            $data['ds_pw'] = $serverData['ds_pw'];
			$data['mapsbasepath'] = (isset($serverData['mapsbasepath'])) ? $serverData['mapsbasepath'] : '';
			$data['matchsettings'] = $serverData['matchsettings'];
			foreach($serverData['adminlevel'] as $admLvlId => $admLvlValue){
				if( is_array($admLvlValue) ){
					$data['adminlevel'][$admLvlId] = implode(', ', $admLvlValue);
				}
				else{
					$data['adminlevel'][$admLvlId] = $admLvlValue;
				}
			}
		}
	}
?>