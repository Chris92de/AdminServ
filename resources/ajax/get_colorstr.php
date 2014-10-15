<?php
	// INCLUDES
	require_once '../class/tmnick.class.php';
	
	// ISSET
	if( isset($_GET['t']) ){ $text = stripslashes($_GET['t']); }else{ $text = null; }
	
	// DATA
	$out = null;
	if($text != null){
		$out['str'] = TmNick::toHtml( nl2br($text), 10, false, false, '#666');
	}
	
	// OUT
	echo json_encode($out);
?>