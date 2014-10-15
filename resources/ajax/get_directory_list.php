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
	
	// DATA
	$out = array();
	if( AdminServ::initialize() ){
		$path = AdminServ::getMapsDirectoryPath();
		$out = Folder::getArborescence($path, AdminServConfig::$MAPS_HIDDEN_FOLDERS, substr_count($path, '/'));
	}
	
	// OUT
	$client->Terminate();
	echo json_encode($out);
?>