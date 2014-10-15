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
	
	// ISSET
	if( isset($_POST['nic']) ){ $nickname = $_POST['nic']; }else{ $nickname = null; }
	if( isset($_POST['clr']) ){ $color = $_POST['clr']; }else{ $color = null; }
	if( isset($_POST['msg']) ){ $message = $_POST['msg']; }else{ $message = null; }
	if( isset($_POST['dst']) ){ $destination = $_POST['dst']; }else{ $destination = null; }
	
	// DATA
	$out = null;
	if($message != null && $destination != null){
		if( AdminServ::initialize(false) ){
			$out = AdminServ::addChatServerLine($message, $nickname, $color, $destination, true);
		}
		$client->Terminate();
	}
	
	// OUT
	echo json_encode($out);
?>