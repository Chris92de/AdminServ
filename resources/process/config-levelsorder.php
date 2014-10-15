<?php
	// ACTIONS
	if( isset($_POST['save']) && isset($_POST['list']) && $_POST['list'] != null ){
		$adminLevelList = AdminLevelConfig::$ADMINLEVELS;
		$list = explode(',', $_POST['list']);
		
		$newAdminLevelList = array();
		foreach($list as $listLevelName){
			$newAdminLevelList[$listLevelName] = array(
				'adminlevel' => $adminLevelList[$listLevelName]['adminlevel'],
				'access' => $adminLevelList[$listLevelName]['access'],
				'permission' => $adminLevelList[$listLevelName]['permission'],
			);
		}
		
		AdminServAdminLevel::saveConfig(array(), -1, $newAdminLevelList);
		AdminServLogs::add('action', 'Order admin level list');
		Utils::redirection(false, '?p='.USER_PAGE);
	}
	
	
	// LEVELLIST
	$data['levels'] = array();
	if (AdminServAdminLevel::hasLevel()) {
		$data['levels'] = AdminLevelConfig::$ADMINLEVELS;
	}
?>