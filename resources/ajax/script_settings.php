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
	if( isset($_POST['method']) ){ $method = $_POST['method']; }else{ $method = null; }
	if( isset($_POST['params']) ){ $params = $_POST['params']; }else{ $params = null; }
	
	// DATA
	$out = null;
	if( AdminServ::initialize() ){
		if(SERVER_VERSION_NAME == 'ManiaPlanet'){
			if($method == 'set' && $params != null){
				$scriptSettings = array();
				if( count($params) > 0 ){
					foreach($params as $param){
						$scriptSettings[$param['name']] = Str::setValueType($param['value'], $param['type']);
					}
				}
				
				if( !$client->query('SetModeScriptSettings', $scriptSettings) ){
					$out = '['.$client->getErrorCode().'] '.$client->getErrorMessage();
				}
				else{
					$out = true;
				}
			}
			else{
				if( !$client->query('GetModeScriptInfo') ){
					$out = '['.$client->getErrorCode().'] '.$client->getErrorMessage();
				}
				else{
					$out = $client->getResponse();
				}
			}
		}
	}
	
	// OUT
	$client->Terminate();
	echo json_encode($out);
?>