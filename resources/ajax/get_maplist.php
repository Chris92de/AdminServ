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
	AdminServUI::lang();
	
	// ISSET
	if( isset($_GET['mode']) ){ $mode = addslashes($_GET['mode']); }else{ $mode = null; }
	if( isset($_GET['sort']) ){ $sort = addslashes($_GET['sort']); }else{ $sort = null; }
	if($mode){
		$_SESSION['adminserv']['mode']['maps'] = $mode;
	}
	
	// DATA
	if( AdminServ::initialize() ){
		$out = AdminServ::getMapList($sort);
	}
	
	//Niarfman Karma research
	$db = new mysqli(SERVER_SERVER_CONTROLLER_MYSQL_HOST, SERVER_SERVER_CONTROLLER_MYSQL_USER, SERVER_SERVER_CONTROLLER_MYSQL_PASS, SERVER_SERVER_CONTROLLER_MYSQL_DB);

	$IsDBConnect=false;
	if($db->connect_errno == 0){
		$IsDBConnect=true;
	}	
		
	foreach ($out['lst'] as $key => $map)
	{
		switch(SERVER_SERVER_CONTROLLER_NAME){
			case 'ManiaControl':
				$sql = 'SELECT name, AVG(vote) AS avg_vote, COUNT(name) AS nb_votes FROM `mc_karma` INNER JOIN `mc_maps`  ON `mc_maps`.`index` = `mc_karma`.`mapIndex` GROUP BY `mc_maps`.`uid` HAVING `mc_maps`.`uid`="'.$map['UId'].'"';
				break;
			case 'Xaseco':
				$sql = 'SELECT Name, AVG(Score) AS avg_vote, COUNT(name) AS nb_votes FROM `rs_karma` INNER JOIN `challenges`  ON `challenges`.`Id` = `rs_karma`.`ChallengeId` GROUP BY `challenges`.`Uid` HAVING `challenges`.`Uid`="'.$map['UId'].'"';
				break;
			case 'Xaseco2':
				$sql = 'SELECT Name, AVG(Score) AS avg_vote, COUNT(name) AS nb_votes FROM `rs_karma` INNER JOIN `maps`  ON `maps`.`Id` = `rs_karma`.`MapId` GROUP BY `maps`.`Uid` HAVING `maps`.`Uid`="'.$map['UId'].'"';
				break;
			case 'None':
				$sql='None';
		}
		
		if($sql == NULL){
			$karma="Invalid server_controller_name";
		}
		elseif($sql == 'None'){
			$karma="-";
		}
		elseif($IsDBConnect){
			$result = $db->query($sql);
			if($result <> NULL){
				$row = $result->fetch_assoc();
				if($row <> NULL)
				{
					
					$out['lst'][$key]['karma']=round($row['avg_vote']*100,2) . '% - ' .$row['nb_votes'] .' vote(s)';
				}
				else
				{
					$out['lst'][$key]['karma']="No Vote";
				}
			$result->free();
			}
			else{
				$karma="Bad Ctrl query";
			}
		}
		else{
			$karma="No Database Set";
		}
	}
		
	$db->close();
	
	// OUT
	$client->Terminate();
	echo json_encode($out);
?>