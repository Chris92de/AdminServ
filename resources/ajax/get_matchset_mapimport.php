<?php
	// INCLUDES
	session_start();
	if( !isset($_SESSION['adminserv']['sid']) ){ exit; }
	$configPath = '../../'.$_SESSION['adminserv']['path'].'config/';
	require_once $configPath.'adminlevel.cfg.php';
	require_once $configPath.'adminserv.cfg.php';
	require_once $configPath.'extension.cfg.php';
	require_once $configPath.'servers.cfg.php';
	require_once '../core/adminserv.php';
	AdminServConfig::$PATH_RESOURCES = '../';
	AdminServ::getClass();
	AdminServUI::lang();
	
	// ISSET
	if( isset($_GET['path']) ){ $path = addslashes($_GET['path']); }else{ $path = null; }
	if( isset($_GET['d']) ){ $directory = addslashes($_GET['d']); }else{ $directory = null; }
	if( isset($_GET['op']) ){ $operation = addslashes($_GET['op']); }else{ $operation = null; }
	if( isset($_GET['select']) ){ $selection = $_GET['select']; }else{ $selection = null; }
	
	// DATA
	$out = null;
	if( AdminServ::initialize() && $path != null ){
		// Maps
		if($path == 'currentServerSelection'){
			$mapsImport = AdminServ::getMapList();
		}
		else{
			$currentDir = Folder::read($path, AdminServConfig::$MATCHSET_HIDDEN_FOLDERS, AdminServConfig::$MATCHSET_EXTENSION, intval(AdminServConfig::RECENT_STATUS_PERIOD * 3600) );
			$mapsImport = AdminServ::getLocalMapList($currentDir, $directory);
		}
		
		// Faire une sélection
		if($operation == 'setSelection'){
			// On supprime les maps non sélectionnées
			if( $selection != null && count($selection) > 0 ){
				foreach($mapsImport['lst'] as $id => $values){
					if( !in_array($id, $selection) ){
						unset($mapsImport['lst'][$id]);
					}
				}
			}
			else{
				foreach($mapsImport['lst'] as $id => $values){
					unset($mapsImport['lst'][$id]);
				}
			}
		}
		
		// Enregistrement de la sélection du MatchSettings
		if($operation != 'getSelection'){
			AdminServ::saveMatchSettingSelection($mapsImport);
		}
		
		$client->Terminate();
	}
	
	// OUT
	if($operation == 'getSelection'){
		echo json_encode($mapsImport);
	}
	else{
		echo json_encode($_SESSION['adminserv']['matchset_maps_selected']);
	}
?>