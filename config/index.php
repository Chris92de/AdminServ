<?php
	session_start();
	require_once './adminserv.cfg.php';
	if( file_exists('./servers.cfg.php') ){
		require_once './servers.cfg.php';
	}
	AdminServConfig::$PATH_RESOURCES = '../'.AdminServConfig::$PATH_RESOURCES;
	require_once AdminServConfig::$PATH_RESOURCES .'core/adminserv.php';
	AdminServ::getClass();
	AdminServUI::lang();
	
	// On vérifie qu'une configuration existe
	if( class_exists('ServerConfig') ){
		// Si on autorise la configuration en ligne
		if( OnlineConfig::ACTIVATE === true ){
			// Si il y a déjà un serveur configuré
			if( AdminServServerConfig::hasServer() ){
				$allowRedirect = false;
				if( OnlineConfig::ADDRESS == null && OnlineConfig::PASSWORD == null ){
					$allowRedirect = true;
				}
				else{
					// Test adresse IP
					if(OnlineConfig::ADDRESS != null){
						if(OnlineConfig::ADDRESS === 'localhost'){
							if( Utils::isLocalhostIP() ){
								$allowRedirect = true;
							}
							else{
								$allowRedirect = false;
							}
						}
						else{
							if( $_SERVER['REMOTE_ADDR'] === OnlineConfig::ADDRESS ){
								$allowRedirect = true;
							}
							else{
								$allowRedirect = false;
							}
						}
					}
					// Test password
					if(OnlineConfig::PASSWORD != null){
						if( isset($_POST['configcheckpassword']) ){
							if(md5($_POST['checkPassword']) === OnlineConfig::PASSWORD){
								$allowRedirect = true;
							}
							else{
								$allowRedirect = false;
							}
						}
						else{
							// Suppression des sessions existante et démarrage d'une nouvelle
							session_unset();
							session_destroy();
							session_start();
							
							// Demande de password
							$_SESSION['adminserv']['check_password'] = true;
							Utils::redirection(false, '..');
						}
					}
				}
			
				// Redirection vers les pages
				if($allowRedirect){
					session_unset();
					session_destroy();
					session_start();
					$_SESSION['adminserv']['allow_config_servers'] = true;
					
					if( OnlineConfig::ADD_ONLY === true || AdminServServerConfig::hasServer() === false ){
						Utils::redirection(false, '../?p=config-addserver');
					}
					else{
						Utils::redirection(false, '../?p=config-servers');
					}
				}
				else{
					AdminServ::error( Utils::t('You are not allowed to configure the servers') );
					Utils::redirection(false, '..');
				}
			}
			// Sinon on créer le mot de passe
			else{
				if( isset($_POST['configsavepassword']) ){
					$password = md5($_POST['savePassword']);
					if( ($result = AdminServServerConfig::savePasswordConfig('./adminserv.cfg.php', $password)) !== true ){
						AdminServ::error( Utils::t('Unable to save password.').' ('.$result.')');
						Utils::redirection(false, '..');
					}
					else{
						session_unset();
						session_destroy();
						session_start();
						$_SESSION['adminserv']['allow_config_servers'] = true;
						Utils::redirection(false, '../?p=config-addserver');
					}
				}
				else{
					// Création du mot de passe
					$_SESSION['adminserv']['get_password'] = true;
					Utils::redirection(false, '..');
				}
			}
		}
		else{
			AdminServ::info( Utils::t('The online configuration is disabled. Use "./config/servers.cfg.php" file.') );
			Utils::redirection(false, '..');
		}
	}
	else{
		Utils::redirection(false, '../?error='.urlencode( Utils::t('The servers configuration file isn\'t recognized by AdminServ.') ) );
	}
?>