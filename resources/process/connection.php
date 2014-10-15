<?php
	// Si on demande le mot de passe pour la config en ligne
	if( !isset($_SESSION['adminserv']['check_password']) && !isset($_SESSION['adminserv']['get_password']) ){
		// On vérifie qu'une configuration existe, sinon on la créer
		if( class_exists('ServerConfig') ){
			// Si la configuration contient au moins 1 serveur et qu'il n'est pas l'exemple
			if( AdminServServerConfig::hasServer() ){
				// Connexion
				if( isset($_POST['as_server']) && isset($_POST['as_password']) && isset($_POST['as_adminlevel']) ){
					// Récupération des valeurs
					$serverName = $_POST['as_server'];
					$password = addslashes( htmlspecialchars( trim($_POST['as_password']) ) );
					if(AdminServConfig::MD5_PASSWORD){
						$password = md5($password);
					}
					$adminLevel = addslashes( htmlspecialchars($_POST['as_adminlevel']) );
					
					// Vérification des valeurs
					if($password == null){
						AdminServ::error( Utils::t('Please put a password.') );
					}
					else{
						// Sessions & Cookies
						$_SESSION['adminserv']['sid'] = AdminServServerConfig::getServerId($serverName);
						$_SESSION['adminserv']['name'] = $serverName;
						$_SESSION['adminserv']['password'] = $password;
						$_SESSION['adminserv']['adminlevel'] = $adminLevel;
						Utils::addCookieData('adminserv', array($_SESSION['adminserv']['sid'], $adminLevel), AdminServConfig::COOKIE_EXPIRE);
						
						// Redirection
						if($_SESSION['adminserv']['sid'] != -1 && $_SESSION['adminserv']['name'] != null && $_SESSION['adminserv']['password'] != null && $_SESSION['adminserv']['adminlevel'] != null){
							Utils::redirection();
						}
						else{
							AdminServ::error( Utils::t('Connection error: invalid session.') );
						}
					}
				}
			}
			else{
				if(OnlineConfig::ACTIVATE === true){
					Utils::redirection(false, './config/');
				}
				else{
					AdminServ::info( Utils::t('No server available. To add one, configure "config/servers.cfg.php" file.') );
				}
			}
		}
		else{
			if(OnlineConfig::ACTIVATE === true && !isset($_GET['error']) ){
				Utils::redirection(false, './config/');
			}
			else{
				AdminServ::error( Utils::t('The servers configuration file isn\'t recognized by AdminServ.') );
			}
		}
	}
	else if( isset($_SESSION['adminserv']['get_password']) ){
		AdminServ::info( Utils::t('It\'s your first connection and no server configured. Choose a password to configure your servers.') );
	}
	
	// HTML
	if( isset($_GET['error']) ){
		AdminServ::error($_GET['error']);
	}
	else if( isset($_GET['info']) ){
		AdminServ::info($_GET['info']);
	}
?>