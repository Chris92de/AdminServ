<?php
	// EDITION
	if (isset($_POST['editlevel'])) {
		$levelId = AdminServAdminLevel::getId($_POST['level'][0]);
		Utils::redirection(false, '?p=config-addlevel&id='.$levelId);
	}
	
	// DUPLIQUER
	if (isset($_POST['duplicatelevel'])) {
		// GET
		$getLevelData = AdminServAdminLevel::getData($_POST['level'][0]);
		
		// SET
		$setLevelData = array(
			'name' => trim( htmlspecialchars( addslashes($_POST['level'][0] . ' - '.Utils::t('copy') ) ) ),
			'adminlevel' => $getLevelData['adminlevel'],
			'access' => $getLevelData['access'],
			'permission' => $getLevelData['permission'],
		);
		if (AdminServAdminLevel::saveConfig($setLevelData)) {
			$action = Utils::t('This admin level has been duplicated.');
			AdminServ::info($action);
			AdminServLogs::add('action', $action);
			Utils::redirection(false, '?p='.USER_PAGE);
		}
		else {
			AdminServ::error( Utils::t('Unable to duplicate admin level.') );
		}
	}
	
	// SUPPRESSION
	if (isset($_POST['deletelevel'])) {
		$levels = AdminLevelConfig::$ADMINLEVELS;
		unset($levels[$_POST['level'][0]]);
		if (($result = AdminServAdminLevel::saveConfig(array(), -1, $levels)) !== true) {
			AdminServ::error( Utils::t('Unable to delete admin level.').' ('.$result.')');
		}
		else {
			$action = Utils::t('The "!levelName" admin level has been deleted.', array('!levelName' => $_POST['level'][0]));
			AdminServ::info($action);
			AdminServLogs::add('action', $action);
			Utils::redirection(false, '?p='.USER_PAGE);
		}
	}
	
	// LEVELLIST
	$data['levels'] = array();
	if (AdminServAdminLevel::hasLevel()) {
		foreach (AdminLevelConfig::$ADMINLEVELS as $levelName => $levelData) {
			$data['levels'][] = array(
				'name' => $levelName,
				'type' => $levelData['adminlevel']['type'],
				'allowed_access' =>  count($levelData['access']).' '.Utils::t('authorized access'),
				'allowed_permissions' => count($levelData['permission']).' '.Utils::t('authorized permissions'),
			);
		}
	}
	$data['count'] = count($data['levels']);
?>