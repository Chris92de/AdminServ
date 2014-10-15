<?php
	// INCLUDES
	session_start();
	$configPath = '../../'.$_SESSION['adminserv']['path'].'config/';
	require_once $configPath.'adminlevel.cfg.php';
	require_once $configPath.'adminserv.cfg.php';
	require_once $configPath.'servers.cfg.php';
	require_once '../core/adminserv.php';
	AdminServConfig::$PATH_RESOURCES = '../';
	AdminServ::getClass();
	
	// ISSET
	if( isset($_GET['srv']) ){ $serverName = $_GET['srv']; }else{ $serverName = null; }
	
	$out = array();
	if($serverName != null){
		$out = AdminServAdminLevel::getServerList($serverName);
	}
	
	echo json_encode($out);
?>