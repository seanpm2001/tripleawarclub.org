<?php

/*
 * Created on Sep 16, 2006
 */
include '../../mainfile.php';
$xoopsOption['template_main'] = "players.html";
include XOOPS_ROOT_PATH.'/header.php';
// Get the parameters
$comp_id = $_GET['lid'];
$page_num = isset($_GET['pagenum']) ? $_GET['pagenum'] : 1;
$players_per_page = isset($_GET['pppg']) ? $_GET['pppg'] : 20;
// Verify comp_id parameter
if( !isset($comp_id) ){
	redirect_header("index.php", 3, ucfirst(_COMP_ERRORS_MISSING_VALUE));
}
elseif( !is_numeric($comp_id) ){
	redirect_header("index.php", 3, ucfirst(_COMP_ERRORS_INVALID_VALUE));
}
else{

	// Get the players
	$player_handler = xoops_getmodulehandler('player');
	$players = $player_handler->getActivePlayers($comp_id);
	// Check for a valid competition id
	$num_players = count($players);
	if( $num_players > 0 ){
		// Get the organization
		include "include/functions.inc.php";
		$org = getPageOrganization($num_players, $players_per_page, $page_num);
		if( count($org) < 1 ){
			redirect_header("index.php", 3, ucfirst(_COMP_ERRORS_INVALID_VALUE));
		}

		// Get the competition name
		$ladder_handler = xoops_getmodulehandler('ladder','comp');
		$ladders = $ladder_handler->getAllLadders();
		$params = array('name'=>$ladders[$comp_id]['comp_name'],
					'comp_id'=>$comp_id,
					'players'=>$players,
					'num_pages'=>$org['num_pages'],
					'page_num'=>$org['page_num'],
					'start_player'=>$org['start_item'],
					'end_player'=>$org['end_item']);
		// Send to template
		
		$xoopsTpl->assign('params', $params);
	}
	else{
		redirect_header("index.php", 3, ucfirst(_COMP_ERRORS_NO_PLAYERS_FOUND));
	}
}

include_once XOOPS_ROOT_PATH.'/footer.php';
?>
