<?php
	// GAMEDATA
	if( AdminServAdminLevel::isType('Admin') ){
		if( !$client->query('GameDataDirectory') ){
			AdminServ::error();
		}
		else{
			$gameDataDirectory = $client->getResponse();
			define('IS_LOCAL', file_exists($gameDataDirectory));
			
			if(IS_LOCAL){
				$srvConfigDirectory = $gameDataDirectory.'Config/';
				$srvoptsConfigDirectory = $srvConfigDirectory.'AdminServ/ServerOptions/';
				if( !Utils::isWinServer() ){
					AdminServ::checkRights(array($srvConfigDirectory => 777));
				}
				
				if( !file_exists($srvoptsConfigDirectory) ){
					if( ($result = Folder::create($srvoptsConfigDirectory)) !== true ){
						AdminServ::error(Utils::t('Unable to create the folder').' : '.$srvoptsConfigDirectory.' ('.$result.')');
					}
				}
				
				$data['srvoptsConfigFiles'] = Folder::read($srvoptsConfigDirectory, array(), array(), intval(AdminServConfig::RECENT_STATUS_PERIOD * 3600) );
			}
		}
	}
	
	
	// ENREGISTREMENT
	if( isset($_POST['savesrvopts']) ){
		// Récupération des données
		$struct = AdminServ::getServerOptionsStruct();
		$ChangeAuthPassword = null;
		if( isset($_POST['ChangeAuthPassword']) && $_POST['ChangeAuthPassword'] != null ){
			$ChangeAuthLevel = $_POST['ChangeAuthLevel'];
			$ChangeAuthPassword = trim($_POST['ChangeAuthPassword']);
		}
		$srvoptsImportExport = false;
		if( array_key_exists('srvoptsImportExport', $_POST) ){
			$srvoptsImportExport = $_POST['srvoptsImportExport'];
		}
		
		// Enregistrement
		if($ChangeAuthPassword){
			if(USER_ADMINLEVEL === $ChangeAuthLevel){
				$_SESSION['adminserv']['password'] = $ChangeAuthPassword;
			}
			AdminServ::info( Utils::t('You changed the password "!authLevel", remember it at the next connection!', array('!authLevel' => $ChangeAuthLevel)) );
			AdminServLogs::add('action', 'Change authentication password for '.$ChangeAuthLevel.' level');
		}
		elseif($srvoptsImportExport){
			// Import
			if($srvoptsImportExport == 'Import'){
				$srvoptsImportName = $_POST['srvoptsImportName'];
				if($srvoptsImportName != 'none'){
					$struct = AdminServ::importServerOptions($srvoptsConfigDirectory.$srvoptsImportName);
					if( AdminServ::setServerOptions($struct) ){
						AdminServLogs::add('action', 'Import server options from '.$srvoptsConfigDirectory.$srvoptsImportName);
					}
				}
			}
			// Export
			elseif($srvoptsImportExport == 'Export'){
				$srvoptsExportName = Str::replaceChars($_POST['srvoptsExportName']);
				AdminServ::exportServerOptions($srvoptsConfigDirectory.$srvoptsExportName.'.txt', $struct);
			}
		}
		elseif( AdminServ::setServerOptions($struct) ){
			AdminServLogs::add('action', 'Save server options');
		}
		Utils::redirection(false, '?p='.USER_PAGE);
	}
	
	
	// LECTURE
	$data['srvOpt'] = AdminServ::getServerOptions();
	$data['adminLevels'] = AdminServAdminLevel::getServerList();
?>