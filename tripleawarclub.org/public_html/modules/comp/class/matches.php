<?php
/*
 * Created on 04.10.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class CompMatchesHandler {
	
	var $db;
	var $availablePlayers;
	
	//constructor
	function CompMatchesHandler() {
		// get database connection
		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
	}

	/**
	 * Returns matches for the given competition, optionally filtered to a certain
	 * user and a certain winning side.
	 * 
	 * @var $comp_id competition id to retrieve matches from
	 * @var $user_id user id to return matches about
	 * @var $side winning side to filter results on ("axis" 0 , "allies" 1 , or "both")
	 * @return mixed array of matches from the competition sorted by date
	 */
	function getMatches($comp_id, $user_id = NULL, $side = "both") {
		// Just return matches about one player
		if( isset($user_id) ){
			$player_handler =& xoops_getmodulehandler('player');
			return $player_handler->getPlayerMatches($user_id, $comp_id, $side);
		}
		else{
			$matches = array();

			$challenge_table = $this->db->prefix('comp_challenges');
			$match_table = $this->db->prefix('comp_matches');
			$xoops_user_table = $this->db->prefix('users');
			$comp_global_table = $this->db->prefix('comp_user_global');
			$sql = "SELECT $match_table.*, winner.uname AS winner_name, loser.uname AS loser_name, " .
						"winner.country AS winner_country, loser.country AS loser_country " .
					"FROM $match_table, $challenge_table, " .
						"(SELECT uname, match_id, country FROM $match_table, $xoops_user_table, $comp_global_table " .
							"WHERE $match_table.winner_id = $xoops_user_table.uid " .
								"AND $xoops_user_table.uid = $comp_global_table.xoops_user_id " .
								"AND $match_table.comp_id = $comp_id) AS winner, " .
						"(SELECT uname, match_id, country FROM $match_table, $xoops_user_table, $comp_global_table " .
							"WHERE $match_table.loser_id = $xoops_user_table.uid " .
								"AND $xoops_user_table.uid = $comp_global_table.xoops_user_id " .
								"AND $match_table.comp_id = $comp_id) AS loser " .
					"WHERE winner.match_id = loser.match_id " .
						"AND $match_table.match_id = winner.match_id " .
						"AND $challenge_table.challenge_id = $match_table.challenge_id " .
						"AND $challenge_table.chall_status < 3 ";
						if( $side == "axis" ){
							$sql .= "AND $match_table.side == 0 ";
						}
						elseif( $side == "allies" ){
							$sql .= "AND $match_table.side == 1 ";
						}
					$sql .= "ORDER BY $match_table.match_date DESC";
			$result = $this->db->query($sql);
			while( $row = $this->db->fetchArray($result) ){
				// Get the side that won
				if( $row['side'] == 0 ){
					$row['side_name'] = 'Axis';
				}
				else{
					$row['side_name'] = 'Allies';
				}

				$matches[] = $row;
			}
			unset($result);

			return $matches;
		}
	}

	/**
	* Get matches only for matches page, not games
	*
	* @var $comp_id competition id to retrieve matches from
	* @return mixed array with the information
	*/
	function getMatchesOnly($user_id,$comp_id){
		$matches = array();
		$player_handler =& xoops_getmodulehandler('player');
		$match_table = $this->db->prefix('comp_matches');
		$xoops_user_table = $this->db->prefix('users');
		$sql = "SELECT DISTINCT challenge_id FROM $match_table WHERE comp_id = $comp_id AND (winner_id = $user_id OR loser_id = $user_id) ORDER BY match_date DESC";	
		$result = $this->db->query($sql);
		unset($sql);

		while( $row = $this->db->fetchArray($result) ){

			$challenge_id = $row['challenge_id'];		

			$user=$player_handler->getPlayerProfile($user_id);
			$matches[$challenge_id]['axis_result']=$user['uname']." ";
			$matches[$challenge_id]['allies_result']=$user['uname']." ";
		
			$sql = "SELECT $match_table.* FROM $match_table WHERE challenge_id = ".$challenge_id." LIMIT 2";
			$result2 = $this->db->query($sql);
			
			while( $game = $this->db->fetchArray($result2) ){
				
				/* Array ( [match_id] => 873 [comp_id] => 6 [winner_id] => 2 
				**		  [loser_id] => 1163 [side] => 0 [map] => 1 
				**	     [luck] => 3 [nos] => 3 [match_date] => 2010-07-05 
				**		  [challenge_id] => 676 [ratingchange] => 20 
				**		 )
				**
				** Want:
				**
				** Ladder |<{ Map | Nos | Luck |}> Opponent Country | Opponent Name | As Allies | As Axis | Match Date | Rating Change
				**
				*/ 
				
				// "Opponent Country | Opponent Name | As Allies | As Axis"				
				
				if($game['side']==0){ // axis
					if($user_id==$game['winner_id']){ // the current user won as axis
						$matches[$challenge_id]['axis_result'].="wins";
						$opponent_id=$game['loser_id'];
					} else { // the current user lost as allies		
						$matches[$challenge_id]['allies_result'].="loses";
						$opponent_id=$game['winner_id'];
					}
				}
	
				if($game['side']==1){ // allies
					if($user_id==$game['winner_id']){ // the current user won as allies
						$matches[$challenge_id]['allies_result'].="wins";
						$opponent_id=$game['loser_id'];
					} else { // the current user lost as allies 		
						$matches[$challenge_id]['axis_result'].="loses";
						$opponent_id=$game['winner_id'];
					}
				}

				$opponent = $player_handler->getPlayerProfile($opponent_id);
				$matches[$challenge_id]['opponent_id']=$opponent_id;
				$matches[$challenge_id]['opponent_uname']=$opponent['uname'];
				$matches[$challenge_id]['opponent_country']=$opponent['country'];

				// Ladder |<{ Map | Nos | Luck |}>  Match Date | Rating Change

				$matches[$challenge_id]['comp_id']=$game['comp_id'];
				$matches[$challenge_id]['ratingchange']=$game['ratingchange'];
				$matches[$challenge_id]['match_date']=$game['match_date'];			
				
				if($comp_id==6){
				
					if($game['map']==1){
						$matches[$challenge_id]['map']=_COMP_1941;
					} elseif($game['map']==3) {
						$matches[$challenge_id]['map']=_COMP_1942;
					}
					
					if($game['nos']==1){
						$matches[$challenge_id]['nos']=_COMP_OFF;
					} elseif($game['nos']==3) {
						$matches[$challenge_id]['nos']=_COMP_ON;
					}
					
					if($game['luck']==1){
						$matches[$challenge_id]['luck']=_COMP_REGULARLUCK;
					} elseif($game['luck']==3) {
						$matches[$challenge_id]['luck']=_COMP_LL;
					}	
									
				}

			} // end while

		} // end foreach
		
		return $matches;
		
	}
	
	
	/**
	 * Search for all available players who match the options
	 * 
	 * @param $max_diff integer this is the maximum rating difference to be allowed
	 * @param $opponent_id integer this checks if a specific player matches the criteria
	 * @return mixed array with the information
	 */
	function getAvailablePlayers($comp_id,$uid,$luck,$rules,$mode,$nos,$map,$max_diff=600,$opponent_id=NULL,$invitation=null) {
		$user_handler =& xoops_getmodulehandler('user');
		$profile = $user_handler->getUserProfile($uid);
		
		// find players with specific luck options
		switch($luck) {
			//random luck
			case "1":
				$luckparam = "<='2'";
				break;
			
			//low luck
			case "3":
				$luckparam = ">='2'";
				break;
			
			//both/any
			case "2":
				$luckparam = "<4";
				break;
		}

		// find players with specific rules options
		switch($rules) {
			//box rules (4thed / 5thed)
			case "1":
				$rulesparam = "<='2'";
				break;
			
			//LHTR rules
			case "3":
				$rulesparam = ">='2'";
				break;
			
			//both/any
			case "2":
				$rulesparam = "<4";
				break;
		}		
		
		// find players with specific mode options
		switch($mode) {
			//pbem
			case "1":
				$modeparam = "<='2'";
				break;
			
			//online
			case "3":
				$modeparam = ">='2'";
				break;
			
			//both/any
			case "2":
				$modeparam = "<4";
				break;
		}		
	
		if(isset($nos)){
			// find players with specific NOs options
			switch($nos) {
				//off
				case "1":
					$nosparam = "<='2'";
					break;
				
				//on
				case "3":
					$nosparam = ">='2'";
					break;
				
				//both/any
				case "2":
					$nosparam = "<4";
					break;
			}	
		}
		
		if(isset($map)){
			// find players with specific NOs options
			switch($map) {
				//off
				case "1":
					$mapparam = "<='2'";
					break;
				
				//on
				case "3":
					$mapparam = ">='2'";
					break;
				
				//both/any
				case "2":
					$mapparam = "<4";
					break;
			}	
		}
		
		$sql = "SELECT comp_global.*, comp_local.*, xoops_user.uname, xoops_user.email 
				FROM " . $this->db->prefix('comp_user_local') ." comp_local,
					" . $this->db->prefix('comp_user_global') ." comp_global,
					" . $this->db->prefix('users') ." xoops_user 
				WHERE ";
		
					if(!isset($invitation) && $invitation != true) {
						$userrating = $user_handler->getRating($comp_id);
						$max_rating = $userrating + $max_diff;
						$min_rating = $userrating - $max_diff;
						
						$sql .= "(comp_local.rating<$max_rating AND comp_local.rating>$min_rating) AND ";		
					}
					
					$sql .= "	xoops_user.uid = comp_local.xoops_user_id
								AND xoops_user.uid = comp_global.xoops_user_id
								AND comp_local.comp_id = $comp_id 
								AND comp_local.xoops_user_id != $user_handler->user_id ";
					if(!isset($invitation) && $invitation != true) {
						$sql .= "AND comp_local.challengeslot = '0' ";
					}
					$sql .= 	"AND comp_global.status = '0' 
								AND comp_local.status = '0' 
								AND comp_local.option_luck $luckparam		
								AND comp_local.option_mode $modeparam		
								AND comp_local.option_rules $rulesparam ";
					if(isset($nos) && $nos!=0){
						$sql .= "AND comp_local.nos ". $nosparam . " ";
					}
					if(isset($map) && $map!=0){
						$sql .= "AND comp_local.map ". $mapparam. " ";
					}
					if(isset($opponent_id)) {
						$sql .= "AND comp_local.xoops_user_id = $opponent_id";
					}
							
					$sql .=	" ORDER BY comp_local.rating DESC	";
					$result = $this->db->query($sql);
					$available_players=array();
					while ($row=$this->db->fetchArray($result)) {
						$available_players[$row['xoops_user_id']] = $row;
					}
		
		// this is not needed for invitations
		if($invitation==null) {		
			//get players 10 positions up in the ladder
			$sql = "SELECT comp_global.*, comp_local.*, xoops_user.uname, xoops_user.email 
					FROM " . $this->db->prefix('comp_user_local') ." comp_local,
						" . $this->db->prefix('comp_user_global') ." comp_global,
						" . $this->db->prefix('users') ." xoops_user
					WHERE comp_local.rating > $userrating
						AND comp_local.rating < $max_rating
						AND xoops_user.uid = comp_local.xoops_user_id
						AND xoops_user.uid = comp_global.xoops_user_id
						AND comp_local.comp_id = $comp_id 
						AND comp_local.xoops_user_id != $user_handler->user_id
						AND comp_global.status = '0' 
						AND comp_local.status = '0' 
						AND comp_local.option_luck $luckparam		
						AND comp_local.option_mode $modeparam		
						AND comp_local.option_rules $rulesparam ";
			if(isset($opponent_id)) {
				$sql .= "AND comp_local.xoops_user_id = $opponent_id";
			}
			$sql .= " ORDER BY comp_local.rating ASC	
					LIMIT 10";
			$result = $this->db->query($sql);
			while ($row=$this->db->fetchArray($result)) {
				// add player if his challengeslot is open && he is not already in the list
				if($row['challengeslot'] == 0 && !in_array($row['xoops_user_id'], $available_players) ) {
					$available_players[$row['xoops_user_id']] = $row;
				}
			}
		}
		
		$player_handler =& xoops_getmodulehandler('player');
		foreach ($available_players as $key => $value) {
			$available_players[$key]['options'] = $player_handler->getPlayerOptions($value['option_rules'],$value['option_luck'],$value['option_mode'],$value['nos'],$value['map'],null);
			
			// get possible ratings gain/loses		
			include_once('include.elo.php');
			
				if($comp_id==6){
				// Calculate the points for each match
				$konstantArr[0] = array(2000,40);
				$konstantArr[1] = array(2400,20);
				$konstantArr[2] = array(2400,10);
				} else {
				// Calculate the points for each match
				$konstantArr[0] = array(2000,150);
				$konstantArr[1] = array(2400,100);
				$konstantArr[2] = array(2400,50);			
				}	
			


			$newRatings1 = calculateEloRatings( $profile[$comp_id]['rating'], $available_players[$key]['rating'], 0, 0, 350, 1, 
$konstantArr);
			$newPossibleRating["win"] = $newRatings1[0];
			$newRatings2 = calculateEloRatings( $profile[$comp_id]['rating'], $available_players[$key]['rating'], 1, 0, 350, 1, 
$konstantArr);
		
			$newPossibleRating["draw"] = $newRatings2[0];
			$newRatings3 = calculateEloRatings( $profile[$comp_id]['rating'], $available_players[$key]['rating'], 1, 1, 350, 1, 
$konstantArr);
			$newPossibleRating["lose"] = $newRatings3[0];
			
			$available_players[$key]['pointsGain'] = $newPossibleRating;
		}
		
		$this->availablePlayers = $available_players;
		return($available_players);
	}

	/**
	 * Check if a specific player matches the search criteria for a challenge
	 * 
	 * @param $max_diff integer this is the maximum rating difference to be allowed
	 * @param $opponent_id integer this checks if a specific player matches the criteria
	 * @return bool
	 */
	function CheckSpecificPlayer($comp_id,$uid,$luck,$rules,$mode,$nos,$map,$max_diff=300,$opponent_id) {

		$check = array();
		$check = $this->getAvailablePlayers($comp_id,$uid,$luck,$rules,$mode,$nos,$map,$max_diff,$opponent_id);
		if (count($check) != 1) {
			return false;
		}
		else{
			return true;
		}
	}
	
	/**
	 * Return the Axis/Allies winner for a match
	 * 
	 * @param $player_id the player ID to show relative result (won/loss) for both games
	 *      note: the match table shows results depending on who won. 
					if A won both games, there will be winner_id = A side =0  side =1 rows
					if B won both games, there will be winner_id = B side =0  side =1 rows
					
	 * @param $challenge_id the challenge ID number
	 * @return array of names, 0 axis winner 1 allies winner
	 */
	function getMatchResult($player_id, $challenge_id) {

	$matches_table = $this->db->prefix('comp_matches');
	$sql = "SELECT * from $matches_table WHERE challenge_id = $challenge_id;";
	$result = $this->db->query($sql);
	
	while ($row=$this->db->fetchArray($result)) {

		if($row['winner_id']==$player_id){
			if($row['side']==0){
				$return[0]="Won";
			} else {
				$return[1]="Won";
			}
		} else { // player lost
			if($row['side']==0){
				$return[1]="Lost";
			} else {
				$return[0]="Lost";
			}	
		}

	}
	
	return $return;
	
	}

	/**
	 * Send a challenge 
	 * database entry and send information to the opponent
	 * 
	 */	
	function SendChallenge($challenger_id, $challenged_id, $comp_id) {
		global $xoopsUser;
		$sql = "INSERT INTO " . $this->db->prefix('comp_challenges') ."
				SET challenger_id = $challenger_id, challenged_id = $challenged_id, comp_id = $comp_id, chall_date = NOW()";
		$result = $this->db->queryF($sql);
		$challenge_id = $this->db->getInsertId();
		
		echo "<h2 class=\"siteheader\">Send Challenge</h2>";
		
		if($result) {
			// close challengeslot of challenged and update local profiles of both players
			$sql = "UPDATE " . $this->db->prefix('comp_user_local') ."
					SET challengeslot = 1, challenges_received = challenges_received+1
					WHERE xoops_user_id = $challenged_id
						AND comp_id = $comp_id";
			$result = $this->db->queryF($sql);
			
			$sql = "UPDATE " . $this->db->prefix('comp_user_local') ."
					SET challenges_sent = challenges_sent+1
					WHERE xoops_user_id = $challenger_id
						AND comp_id = $comp_id";
			$result = $this->db->queryF($sql);

			
			// send private message
			$ladder_handler =& xoops_getmodulehandler('ladder');
			$comp_name = $ladder_handler->getLadderName($comp_id);
			
			$subject = $comp_name . ": " . $this->availablePlayers[$challenged_id]['uname'] . _COMP_CHALLENGE_CHALLENGED_BY . $xoopsUser->getVar('uname');
			$message = _COMP_CHALLENGE_RECEIVED_FOR . $comp_name ." ladder ". _COMP_FROM ." ". $xoopsUser->getVar('uname').".";
			$message .= _COMP_CHALLENGE_SUCCESS_CHALLENGE_ID . " " .$challenge_id;
				
/*			//xoops handler returns error message because the data isn't posted *aaargh!*

			$pm_handler =& xoops_gethandler('privmessage');
	        $pm =& $pm_handler->create();
	        $pm->setVar("subject", $subject);
	        $pm->setVar("msg_text", $message);
	        $pm->setVar("to_userid", $challenger_id);
	        $pm->setVar("from_userid", $challenged_id);
	        if (!$pm_handler->insert($pm)) {
	        	echo $pm->getHtmlErrors();
	        }
*/        
			$msg_id = $this->db->genId('priv_msgs_msg_id_seq');
			$sql = sprintf("INSERT INTO %s (msg_id, msg_image, subject, from_userid, to_userid, msg_time, msg_text, read_msg) VALUES (%u, %s, %s, %u, %u, %u, %s, %u)", $this->db->prefix('priv_msgs'), $msg_id, $this->db->quoteString("icon1.gif"), $this->db->quoteString($subject), $challenger_id, $challenged_id, time(), $this->db->quoteString($message), 0);
			$result = $this->db->queryF($sql);
	
			$user_handler =& xoops_getmodulehandler('player');
			$challenged_profile = $user_handler->getPlayerProfile($challenged_id);
			$challenger_profile = $user_handler->getPlayerProfile($challenger_id);
			// send email
			global $xoopsConfig;
			include XOOPS_ROOT_PATH."/class/xoopsmailer.php";
			$to = array($challenged_profile['email'], $xoopsUser->getVar('email'));
			
			$xoopsMailer =& getMailer();
	    	$xoopsMailer->useMail(); 
	    	$xoopsMailer->setTemplateDir(XOOPS_ROOT_PATH.'/modules/comp/language/'.$xoopsConfig['language'].'/mail_template');
	    	$xoopsMailer->setTemplate("comp_send_challenge.tpl");
	    	
	    	$xoopsMailer->setToEmails($to);
			//$xoopsMailer->ReplyTo();
			$xoopsMailer->setFromEmail($challenger_profile['email']);
			$xoopsMailer->setFromName($challenger_profile['uname']." from TripleA WarClub");
			$xoopsMailer->setSubject($subject);
			
			//assign variables 
			$xoopsMailer->assign("CHALLENGER", $xoopsUser->getVar('uname'));
			$xoopsMailer->assign("CHALLENGER_EMAIL", $xoopsUser->getVar('email'));
			$xoopsMailer->assign("CHALLENGED", $challenged_profile['uname']);
			$xoopsMailer->assign("CHALLENGED_EMAIL", $challenged_profile['email']);
			$xoopsMailer->assign("COMPETITION", $comp_name);
			$xoopsMailer->assign("CHALLENGE_ID", $challenge_id);		
			$xoopsMailer->assign("SITENAME", $xoopsConfig['sitename']);
			$xoopsMailer->assign("SITEURL", XOOPS_URL."/");
			
			$xoopsMailer->send();
			
			echo "<p>" . _COMP_CHALLENGE_SUCCESS.".</p>";
			echo "<p>" . _COMP_CHALLENGE_SUCCESS_CHALLENGE_ID. " <b>" . $challenge_id . "</b>.</p>";
		}
		else {
			echo "<p>" . _COMP_ERRORS_CHALLENGE_NOT_SENT . ".</p>";
		}
	}
	
	
	/**
	 * check if a player can send an invitation to a specific player
	 */
/*	function checkPlayerForInvitation($comp_id,$luck,$rules,$mode,$opponent_id) { 
		$check = $this->getAvailablePlayers($comp_id,$luck,$rules,$mode,$max_diff=9999,$opponent_id,$invitation=true);
		if (count($check) != 1) {
			return false;
		}
		else{
			return true;
		}
	}
*/	

}
?>
