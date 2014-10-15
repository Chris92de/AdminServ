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
	if( isset($_GET['s']) ){ $hideServerLines = $_GET['s']; }else{ $hideServerLines = false; }
	
	// DATA
	$out = null;
	if( AdminServ::initialize(false) ){
		$out = AdminServ::getChatServerLines($hideServerLines);
	}
	
	// OUT
	$client->Terminate();
	echo json_encode($out);
?>