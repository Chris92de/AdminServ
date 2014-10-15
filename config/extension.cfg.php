<?php
class ExtensionConfig {
	/********************* EXTENSION CONFIGURATION *********************/
	
	// Plugins list installed
	public static $PLUGINS = array(
		'planets',
		'coppers',
	);
	
	
	// Themes color list
	public static $THEMES = array(
		'red' => array(
			'#ea4f4f',
			'#ffc0c0'
		),
		'blue' => array(
			'#5e9cd5',
			'#d9e8ff'
		),
		'green' => array(
			'#8aca1b',
			'#d1e9a8'
		),
		'orange' => array(
			'#ffa600',
			'#ffe3b0'
		),
		'purple' => array(
			'#b15cd5',
			'#e5c4f3'
		),
		'black' => array(
			'#727272',
			'#dedede'
		)
	);
	
	
	// Available language list
	public static $LANG = array(
		'fr' => 'Français',
		'en' => 'English',
		'de' => 'Deutsch',
		'es' => 'Español',
		'no' => 'Norsk',
		'dk' => 'Danish',
		'pl' => 'Polish',
		'nl' => 'Dutch'
	);
	
	
	// Game modes list
	public static $GAMEMODES = array(
		0 => 'Script',
		1 => 'Rounds',
		2 => 'TimeAttack',
		3 => 'Team',
		4 => 'Laps',
		5 => 'Cup',
		6 => 'Stunts'
	);
	public static $TEAMSCRIPTS = array(
		'Elite',
		'Battle',
		'Siege',
		'Heroes',
		'SpeedBall'
	);
	
	
	// Menu list in maps page
	public static $MAPSMENU = array(
		'maps-list' => 'List',
		'maps-local' => 'Local',
		'maps-upload' => 'Send',
		'maps-order' => 'Order',
		'maps-matchset' => 'MatchSettings',
		'maps-creatematchset' => 'Create a MatchSettings'
	);
}
?>