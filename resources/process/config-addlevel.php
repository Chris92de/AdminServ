<?php
	// ENREGISTREMENT
	if (isset($_POST['savelevel'])) {
		// Variables
		$levelName = Str::replaceSpecialChars( htmlspecialchars(addslashes($_POST['addLevelName'])), false);
		$levelType = $_POST['addLevelType'];
		$levelAccess = array();
		if ($_POST['selectedAccessSortList']) {
			$levelAccess = explode(',', $_POST['selectedAccessSortList']);
		}
		$levelPermission = array();
		if ($_POST['selectedPermissionSortList']) {
			$levelPermission = explode(',', $_POST['selectedPermissionSortList']);
		}
		$levelData = array(
			'name' => $levelName,
			'adminlevel' => array(
				'type' => $levelType
			),
			'access' => $levelAccess,
			'permission' => $levelPermission,
		);
		
		// Édition
		if($args['id'] !== -1){
			if( ($result = AdminServAdminLevel::saveConfig($levelData, $args['id'])) !== true ){
				AdminServ::error( Utils::t('Unable to modify the admin level.').' ('.$result.')');
			}
			else{
				$action = Utils::t('This admin level has been modified.');
				AdminServ::info($action);
				AdminServLogs::add('action', $action);
				Utils::redirection(false, '?p=config-adminlevel');
			}
		}
		// Ajout
		else{
			if( ($result = AdminServAdminLevel::saveConfig($levelData)) !== true ){
				AdminServ::error( Utils::t('Unable to add the admin level.').' ('.$result.')');
			}
			else{
				$action = Utils::t('This admin level has been added.');
				AdminServ::info($action);
				AdminServLogs::add('action', $action);
				Utils::redirection(false, '?p='.USER_PAGE);
			}
		}
	}
	
	
	// LECTURE
	$defaultAccess = AdminServAdminLevel::getDefaultAccess();
	$defaultPermission = AdminServAdminLevel::getDefaultPermission();
	$data = array(
		'name' => null,
		'types' => AdminServAdminLevel::getDefaultType(),
		'adminlevel' => array(
			'type' => null,
		),
		'access' => array(
			'default' => array(),
			'selected' => $defaultAccess,
		),
		'permission' => array(
			'default' => array(),
			'selected' => $defaultPermission,
		)
	);
	if($args['id'] !== -1){
		define('IS_LEVEL_EDITION', true);
		$data['name'] = AdminServAdminLevel::getName($args['id']);
		if($data['name']){
			$levelData = AdminServAdminLevel::getData($data['name']);
			
			$data['adminlevel'] = array(
				'type' => $levelData['adminlevel']['type'],
			);
			$data['access'] = array(
				'default' => array_diff($defaultAccess, $levelData['access']),
				'selected' => $levelData['access'],
			);
			$data['permission'] = array(
				'default' => array_diff($defaultPermission, $levelData['permission']),
				'selected' => $levelData['permission'],
			);
		}
	}
?>