<?php
	/* INCLUDES */
	$path = AdminServPlugin::getPluginPath();
	$langFile = $path.'lang/'. USER_LANG .'.php';
	if( file_exists($langFile) ){
		include_once $langFile;
	}
	
	
	/* ACTIONS */
	if( isset($_POST['transfercoppers']) ){
		// Server > Server
		if( isset($_POST['serverToServerAmout']) && isset($_POST['serverToServerLogin']) ){
			$serverToServerAmout = intval($_POST['serverToServerAmout']);
			$serverToServerLogin = trim($_POST['serverToServerLogin']);
			
			if($serverToServerAmout > 0 && $serverToServerLogin != Utils::t('Server login') ){
				if( !$client->query('Pay', $serverToServerLogin, $serverToServerAmout, Utils::t('Transfered by AdminServ')) ){
					AdminServ::error();
				}
				else{
					$_SESSION['adminserv']['transfer_billid'] = $client->getResponse();
					AdminServLogs::add('action', 'Transfer '.$serverToServerAmout.' coppers to '.$serverToServerLogin.' server login');
				}
			}
		}
		
		// Server > Player
		if( isset($_POST['serverToPlayerAmount']) && isset($_POST['serverToPlayerLogin']) ){
			$serverToPlayerAmount = intval($_POST['serverToPlayerAmount']);
			$serverToPlayerMessage = trim($_POST['serverToPlayerMessage']);
			$serverToPlayerLogin = trim($_POST['serverToPlayerLogin']);
			$serverToPlayerLogin2 = trim($_POST['serverToPlayerLogin2']);
			
			if( $serverToPlayerAmount > 0 ){
				// Message
				if($serverToPlayerMessage == Utils::t('Optionnal') ){
					$serverToPlayerMessage = Utils::t('Transfered by AdminServ');
				}
				// Player login
				if($serverToPlayerLogin2 != Utils::t('Player login') ){
					$serverToPlayerLogin = $serverToPlayerLogin2;
				}
				
				// Pay
				if( !$client->query('Pay', $serverToPlayerLogin2, $serverToPlayerAmount, $serverToPlayerMessage) ){
					AdminServ::error();
				}
				else{
					$_SESSION['adminserv']['transfer_billid'] = $client->getResponse();
					AdminServLogs::add('action', 'Transfer '.$serverToPlayerAmount.' coppers to '.$serverToPlayerLogin2.' player login');
				}
			}
		}
		
		// Server < Player
		if( isset($_POST['playerToServerAmount']) && isset($_POST['playerToServerLogin']) ){
			$playerToServerAmount = intval($_POST['playerToServerAmount']);
			$playerToServerLogin = trim($_POST['playerToServerLogin']);
			
			if( $playerToServerAmount > 0 ){
				if( !$client->query('SendBill', $playerToServerLogin, $playerToServerAmount, Utils::t('Confirmation of the transfer by AdminServ'), SERVER_LOGIN) ){
					AdminServ::error();
				}
				else{
					$_SESSION['adminserv']['transfer_billid'] = $client->getResponse();
					AdminServLogs::add('action', 'Transfer '.$playerToServerAmount.' coppers from to '.$playerToServerLogin.' player login');
				}
			}
		}
		
		Utils::redirection(false, '?p='.USER_PAGE);
	}
	
	
	/* GET */
	$client->addCall('GetServerCoppers');
	if( isset($_SESSION['adminserv']['transfer_billid']) && $_SESSION['adminserv']['transfer_billid'] != null){
		$client->addCall('GetBillState', array($_SESSION['adminserv']['transfer_billid']) );
	}
	
	if( !$client->multiquery() ){
		AdminServ::error();
	}
	else{
		$queriesData = $client->getMultiqueryResponse();
		
		// Coppers number
		$nbCoppers = $queriesData['GetServerCoppers'];
		
		// Transfer status
		if( isset($queriesData['GetBillState']) ){
			$billState = $queriesData['GetBillState'];
			$transferState = Utils::t('Transaction').' #'.$billState['TransactionId'].' : '.$billState['StateName'];
		}
		else{
			$transferState = '<i>'.Utils::t('No transfer made.').'</i>';
		}
	}
	
	// Players
	$playerCount = AdminServ::getNbPlayers();
	$getPlayerListUI = AdminServUI::getPlayerList();
	
	$client->Terminate();
?>