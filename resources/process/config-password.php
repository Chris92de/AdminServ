<?php
	// ENREGISTREMENT
	if( isset($_POST['savepassword']) ){
		$current = md5($_POST['changePasswordCurrent']);
		$new = md5($_POST['changePasswordNew']);
		if( isset($_SESSION['adminserv']['path']) ){ $adminservPath = $_SESSION['adminserv']['path']; }else{ $adminservPath = null; }
		$pathConfig = $adminservPath.'config/';
		
		if(OnlineConfig::PASSWORD !== $current){
			AdminServ::error( Utils::t('The current password doesn\'t match.') );
		}
		else{
			if( ($result = AdminServServerConfig::savePasswordConfig($pathConfig.'adminserv.cfg.php', $new)) !== true ){
				AdminServ::error( Utils::t('Unable to save password.').' ('.$result.')');
			}
			else{
				$info = Utils::t('The password has been changed.');
				AdminServ::info($info);
				AdminServLogs::add('action', $info);
			}
		}
		
		Utils::redirection(false, '?p='.USER_PAGE);
	}
?>