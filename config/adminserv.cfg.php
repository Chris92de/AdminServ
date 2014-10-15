<?php
class OnlineConfig {
	/********************* ONLINE CONFIGURATION *********************/
	
	const ACTIVATE = true; // Activate the online configuration
	const PASSWORD = '0b28a5799a32c687dad2c5183718ceac'; // Checking password. This password is generated in MD5
	const ADDRESS = ''; // Checking address. Can be localhost or IP address
	const ADD_ONLY = false; // Add only server. Unable to modify or delete
	const ADMINLEVEL = true; // Activate the admin level configuration
}

class AdminServConfig {
	/********************* OPTIONAL CONFIGURATION *********************/
	
	/* GENERAL */
	const TITLE = 'Admin,Serv'; // The comma seperates the color
	const SUBTITLE = 'For maniaplanet servers'; // if null it's hidden
	const LOGO = 'logo.png'; // if null it's hidden
	const DEFAULT_THEME = 'red'; // The first theme to be loaded
	const DEFAULT_LANGUAGE = 'auto'; // Can be language code (fr, en, etc) or auto = automatic detection
	const USE_DISPLAYSERV = true; // Show DisplayServ tool on connection page
	const AUTOSAVE_MATCHSETTINGS = true; // Save the MatchSettings in the server config (possibility to disable online)
	
	/* ADVANCED */
	const MD5_PASSWORD = false; // if true, the dedicated server password is checked in MD5
	const LIMIT_PLAYERS_LIST = 250; // Display limit for lines in player list
	const LIMIT_MAPS_LIST = 1000; // Display limit for lines in maps list
	const LOCAL_GET_MAPS_ON_SERVER = true; // if true, the current server maps list is loaded in local maps page for easy compare with icon change. But if you have a lot of maps in server, it is better to disable it
	const RECENT_STATUS_PERIOD = 24; // Recent status period in hour for maps/matchsettings/guestban
	const SERVER_CONNECTION_TIMEOUT = 3; // Dedicated server connection timeout in second
	const COOKIE_EXPIRE = 90; // Expiration time for cookies, in days
	
	/* FILES AND FOLDERS */
	public static $MAPS_HIDDEN_FOLDERS = array('MatchSettings', 'Replays'); // Folders to be hidden in maps page
	public static $MATCHSET_HIDDEN_FOLDERS = array('Campaigns', 'Replays'); // Folders to be hidden in matchsettings page
	public static $MAP_EXTENSION = array('map.gbx', 'challenge.gbx', 'gbx'); // Double extensions used in maps page
	public static $MATCHSET_EXTENSION = array('txt'); // MatchSettings extensions used in matchsettings page
	public static $PLAYLIST_EXTENSION = array('playlist.txt'); // Playlist extensions used in guestban page
	
	/* UPLOAD */
	public static $ALLOWED_EXTENSIONS = array('gbx'); // Extensions allowed for upload
	const SIZE_LIMIT = 'auto'; // Limit size per file in MB. If auto, the limit size in php.ini config file is used
	const UPLOAD_ONLINE_FOLDER = 'AdminServ/'; // Path from "Maps" folder for upload online mode
	
	/* LOGS */
	public static $LOGS = array(
		'access' => true,
		'action' => true,
		'error' => true
	);
	
	/* MULTI ADMINSERV */
	const MULTI_ADMINSERV = false; // Use many instances of AdminServ
	public static $PATH_RESOURCES = './resources/'; // You can change the folders location
	public static $PATH_PLUGINS = './plugins/';
	
	/* PLUGINS */
	const PLUGINS_LIST = ''; // The filename for alternative plugin configuration. In the file: $PLUGINS = array('pluginfoldername', 'etc');
	const PLUGINS_LIST_TYPE = 'replace'; // add or replace method
}

class DataBaseConfig {
	/********************* DATABASE CONFIGURATION *********************/
	
	const DB_HOST = '';
	const DB_PORT = '';
	const DB_USER = '';
	const DB_PASS = '';
	const DB_NAME = '';
	const DB_DRIVER = '';
	const DB_TABLE_PREFIX = '';
}

class FTPConfig {
	/********************* FTP CONFIGURATION *********************/
	
	const FTP_HOST = '';
	const FTP_USER = '';
	const FTP_PASS = '';
	const FTP_PORT = 21;
}
?>