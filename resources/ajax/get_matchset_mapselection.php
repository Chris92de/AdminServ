<?php
	// INCLUDES
	session_start();
	$configPath = '../../'.$_SESSION['adminserv']['path'].'config/';
	require_once $configPath.'adminserv.cfg.php';
	require_once '../core/adminserv.php';
	AdminServConfig::$PATH_RESOURCES = '../';
	AdminServ::getClass();
	AdminServUI::lang();
	
	// DATA
	if( !isset($_SESSION['adminserv']['matchset_maps_selected']) ){
		// Retourne "Aucune map"
		AdminServ::saveMatchSettingSelection();
	}
	else{
		// Enlever une map de la sélection
		if( isset($_GET['remove']) ){
			$removeSelection = intval($_GET['remove']);
			$mapsSelection = $_SESSION['adminserv']['matchset_maps_selected'];
			
			// Liste
			if( isset($mapsSelection['lst']) && is_array($mapsSelection['lst']) && count($mapsSelection['lst']) > 0 ){
				foreach($mapsSelection['lst'] as $id => $values){
					if($id == $removeSelection){
						unset($mapsSelection['lst'][$id]);
						break;
					}
				}
			}
			
			$_SESSION['adminserv']['matchset_maps_selected'] = $mapsSelection;
			AdminServ::saveMatchSettingSelection();
		}
	}
	
	echo json_encode($_SESSION['adminserv']['matchset_maps_selected']);
?>