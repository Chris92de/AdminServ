<?php
	// LECTURE
	$data['serverLines'] = AdminServ::getChatServerLines();
	
	$lastNicknameUsed = Utils::readCookieData('adminserv_user', 2);
	$data['nickname'] = ($lastNicknameUsed != null) ? $lastNicknameUsed : Utils::t('Nickname');
	
	$colorList = array(
		'$ff0' => Utils::t('Color'),
		'$000' => Utils::t('Black'),
		'$f00' => Utils::t('Red'),
		'$0f0' => Utils::t('Green'),
		'$00f' => Utils::t('Blue'),
		'$f80' => Utils::t('Orange'),
		'$f0f' => Utils::t('Pink'),
		'$888' => Utils::t('Grey'),
		'$fff' => Utils::t('White')
	);
	$data['colorOptions'] = null;
	$lastColorUsed = Utils::readCookieData('adminserv_user', 3);
	foreach($colorList as $colorCode => $colorName){
		$selected = ($colorCode == $lastColorUsed) ? ' selected="selected"' : null;
		$data['colorOptions'] .= '<option value="'.$colorCode.'"'.$selected.'>'.$colorName.'</option>';
	}
	
	$lastDestination = null;
	$lastDestinationTitle = Utils::t('server');
	if( isset($_SESSION['adminserv']['chat_dst']) ){
		$lastDestination = $lastDestinationTitle = $_SESSION['adminserv']['chat_dst'];
	}
	$data['destination']['list'] = AdminServUI::getPlayerList($lastDestination);
	$data['destination']['title'] = Utils::t('Message destination').' : '.$lastDestinationTitle;
?>