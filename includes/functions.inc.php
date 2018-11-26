<?
/***************************************************************************
 *                                functions.inc.php
 *                            -------------------
 *   begin                : Friday, Mar 28, 2008
 *   copyright            : (C) 2008 J. David Baker
 *   email                : me@jdavidbaker.com
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

function make_pick($pick_id, $player_id, $recursive = false) {
  global $settings;

  $staff = false;
  if ( $settings->get_value(kSettingStaffDraftOn)==1 )
     $staff = true;

  if ($pick_id) {
    $statement = "select * from pick where player_id = '$player_id'";
    // Make sure that the player has not already been picked
    if (!mysql_num_rows(mysql_query($statement))) {
      $statement = "update pick set player_id = '$player_id'
where pick_id = '$pick_id' and (player_id is NULL or player_id = '".kSkipPick."')";
      mysql_query($statement);

      $modified = false;
      if ( mysql_affected_rows() )
          $modified = true;
      // Delete the player from anyone's queue
      // Re-grab the player to make sure we don't have a race condition
      $statement = "select * from pick where pick_id = '$pick_id'";
      $row = mysql_fetch_array(mysql_query($statement));
      echo mysql_error();
      $player_id = $row['player_id'];
      $statement = "delete from selection where player_id = '$player_id'";
      mysql_query($statement);
      
      // If staff draft update
      // update picked guy's role so team he vacated has a hole
      // check if they've fired someone as a result
      // update the Amenable field for the new team that is on the clock.
      if ( $staff ){
         //update the guy that was picked so team he left has a hole in case they try to decline a pick
         $statement = "update staff set drafted=1, fired=0 where staff_id = ".$player_id;
         mysql_query($statement);
         $round = floor(($pick_id-1)/32)+1;
         $statement = "select team_id from `pick` where pick_id = '$pick_id'";
         $row = mysql_fetch_array(mysql_query($statement));
         if ( $tid = $row["team_id"] ){
            //we found the team
            $statement = "select in_game_id from team where team_id=".$tid.";";
            $row = mysql_fetch_array(mysql_query($statement));
            $tid = $row["in_game_id"];
            $statement = "select * from staff where drafted=0 and fired=0 and staff_curr_team_id = ".$tid." and staff_role_id=".$round;
            $result = mysql_fetch_array(mysql_query($statement));
            if ( $result["staff_name"]!='' ){
                //they DID have someone in this role!  Mark him fired
                $statement = "select * from staff_trans_history order by input_file_number desc limit 1";
                $row = mysql_fetch_array(mysql_query($statement));
                $filenumber = $row['input_file_number'];
                $statement = "update staff set fired=1 where staff_curr_team_id=".$tid." and staff_role_id=".$round;
                mysql_query($statement);
            }
         }
         //compute next pick number
         $round = floor(($pick_id)/32)+1;
	 $pick = (($pick_id)%32)+1;
         update_staff_amenable($round,$pick);
      }

      // Find the team that made this pick and process their queue accordingly
      if ( !$staff )
         $statement = "select team.team_id, team.team_multipos, player.position_id from
pick, team, player where pick.pick_id = '$pick_id' and team.team_id = pick.team_id and player.player_id = pick.player_id";
      else
         $statement = "select team.team_id, team.team_multipos from pick, team, staff where pick.pick_id = '$pick_id' and
team.team_id = pick.team_id and staff.staff_id = pick.player_id";
      $row = mysql_fetch_array(mysql_query($statement));
      $team_id = $row['team_id'];
      if ($row['team_multipos'] == '0') {
        // Clear out any other players from this team's queue that have the same position
        if ( $staff )
             $statement = "select staff.staff_id from staff, selection where
  staff.staff_id = selection.player_id and selection.team_id = '".$row['team_id']."'";
        else
             $statement = "select player.player_id from player, selection where
  player.position_id = '".$row['position_id']."' and player.player_id = selection.player_id and selection.team_id = '".$row['team_id']."'";

        $result = mysql_query($statement);
        while($row = mysql_fetch_array($result)) {
            $statement = "update selection set selection_priority = '0' where
  team_id = '".$team_id."' and player_id = '".$row['player_id']."'";
            mysql_query($statement);
          }
       }
      // Mark the team_need for this team at this position as being done
      if ( !$staff){
         $statement = "select position_id from player where player_id = '$player_id'";
         $row = mysql_fetch_array(mysql_query($statement));
         $statement = "update team_need set pick_id = '$pick_id' where team_id = '$team_id' and
position_id = '".$row['position_id']."'";
         mysql_query($statement);
      }


      
      // Send e-mail only if we actually modified a row and email is on
      if ($modified) {
	$statement = "select * from team where team_id = '".kAdminUser."'";
	$row = mysql_fetch_array(mysql_query($statement));
	$from = "FOF Draft Admin <".$row['team_email'].">";
        $fromaddress = $row['team_email'];
     if (!$staff)
   	   $statement = "select team.team_name, player.player_name, position.position_name from
pick, team, player, position where
pick.pick_id = '$pick_id' and
team.team_id = pick.team_id and
player.player_id = pick.player_id and
position.position_id = player.position_id";
	else
	   $statement = "select team.team_name, staff.staff_name from
pick, team, staff where pick.pick_id = '$pick_id' and team.team_id = pick.team_id and staff.staff_id = pick.player_id";

	$row = mysql_fetch_array(mysql_query($statement));
	$round = floor(($pick_id-1)/32)+1;
	$pick = (($pick_id-1)%32)+1;
	//Changing the subject to preserve GMAIL subject threading
	//$subject = $settings->get_value(kSettingLeagueName)." Draft Selection Notification: Round $round Pick $pick";
	$subject = $settings->get_value(kSettingLeagueName)." Draft Notification";
     $message = "message";
     if (!$staff)
  	  $message = "Round $round Pick $pick ".$row['team_name']." selects ".$row['position_name']." ".$row['player_name'];
     else
       $message = "Staff Round $round Pick $pick ".$row['team_name']." selects ".$row['staff_name'];

	//$message = $row['team_name']." selects ".$row['position_name']." ".$row['player_name'];
	// Who is on the clock?
	$statement = "select pick.pick_id, team.team_id, team.team_email, team.team_name, team.team_email_prefs
from pick, team
where pick.team_id = team.team_id
and pick.player_id is NULL
order by pick_id
limit 1";
        $result = mysql_query($statement);
	$row = mysql_fetch_array($result);
        
	if ($row['team_name']) {
	  $on_clock = $row['team_id'];
	  // Mark that they are on the clock
	  $team = new team($row['team_id']);
	  $new_start = $team->new_pick_time(time());
	  $statement = "update pick set pick.pick_time = '$new_start', pick_start = '".date("Y-m-d H:i:s")."'
where pick_id = '".$row['pick_id']."'";
	  mysql_query($statement);
	  $message .= '
'.$row['team_name'].' is on the clock.';

	  // Store the e-mail and sms of the team that's on the clock for the notice below
	  if ($row['team_email_prefs'] == kOptionNoEmail || is_null($row['team_email'])) {
	    $clock_email = '';
	  } else {
	    $clock_email = $row['team_email'];
	  }
	  if ($row['team_sms_setting'] == kOptionNoSMS || is_null($row['team_phone'])) {
	    $clock_sms = '';
	  } else {
	    $clock_sms = $row['team_phone'].$row['team_carrier'];
	  }
	} else {
	  // We are either done or halted
	  $statement = "select count(*) num from pick where player_id = '".kDraftHalt."'";
	  $row = mysql_fetch_array(mysql_query($statement));
	  if ($row['num']) {
	    // Draft is halted
	    $message .= '
Draft is stopped.';
	  } else {
	    $message .= '
Draft is complete.';
	  }
	}
	// Also send notifications to those who wish to receive them
	switch($settings->get_value(kSettingSendMails)) {
	case kEmailAll:
	  $statement = "select * from team where team_email is not NULL and
team_id != '".kAdminUser."' and team_email_prefs != ".kOptionNoEmail;
	  $result = mysql_query($statement);
	  $to = array();
	  while ($row = mysql_fetch_array($result)) {
	    // if the team only wants e-mails when they are going on the clock, then only send
	    // if they are on the clock.
	    if (($row['team_email_prefs'] == kOptionMyEmail && $row['team_id'] == $on_clock) ||
		$row['team_email_prefs'] == kOptionAllEmail) {
	      $to[] = $row['team_email'];
	    }
	  }
       $statement = "select * from team where team_phone is not NULL and
team_id != '".kAdminUser."' and team_sms_setting != ".kOptionNoSMS;
	  $result = mysql_query($statement);
	  while ($row = mysql_fetch_array($result)) {
	    // if the team only wants sms when they are going on the clock, then only send
	    // if they are on the clock.
	    if (($row['team_sms_setting'] == kOptionMySMS && $row['team_id'] == $on_clock) ||
		$row['team_sms_setting'] == kOptionAllSMS) {
		 $address = $row['team_phone'].$row['team_carrier'];
	      $to[] = $address;
	    }
	  }
	  if (defined('kNotificationEmail')) {
	    $to[] = kNotificationEmail;
	  }
	  break;
	case kEmailNextPick:
	  // Only send to the team that's on the clock if that is what is set
	  if ($clock_email) {
	    $to[] = $clock_email;
	  } else {
	    // No clock_email
	    $to = array();
	  }
	  if ($clock_sms) {
	    $to[] = $clock_sms;
	  }
	  break;
	case kEmailOff:
	default:
	  // Don't send any BCC's if the mail setting is off
	  $to = array();
	}
        
        require_once './includes/lib/swift_required.php';
        if ( $settings->get_value(kSettingEmailType)==kEmailTypeMail ){
            //mail ($from, $subject, $message, "From: $from\nBCC: ".implode(",",$to));
            $transport = Swift_MailTransport::newInstance();
        }
        else if ( $settings->get_value(kSettingEmailType)==kEmailTypeSendmail ){
            // Sendmail
            $transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
        }
        else if ( $settings->get_value(kSettingEmailType)==kEmailTypeSMTP ){
            // Create the Transport
            $transport = Swift_SmtpTransport::newInstance($settings->get_value(kSettingSMTPServer), $settings->get_value(kSettingSMTPPort))
              ->setUsername($settings->get_value(kSettingSMTPUser))
              ->setPassword($settings->get_value(kSettingSMTPPassword));
            if ( $settings->get_value(kSettingSMTPEncryptType)==kSettingSMTPEncryptTypeSSL )
                $transport->setEncryption('ssl');
            else if ( $settings->get_value(kSettingSMTPEncryptType)==kSettingSMTPEncryptTypeTLS )
                $transport->setEncryption('tls');
        }
        //if the admin wants notification add the admin address to the list  
        if ( $settings->get_value(kSettingAdminEmail)==kAdminEmailAll ){
            //insert at the beginning
            array_unshift($to,$fromaddress);
        }
        if ( count($to)>0 && $settings->get_value(kSettingEmailType)!=kEmailTypeOff && $settings->get_value(kSettingSendMails)!=kEmailOff){
            // Create the Mailer using your created Transport
            $mailer = Swift_Mailer::newInstance($transport);
            foreach ( $to as $individual ){
                // Create the message
                $messagetosend = Swift_Message::newInstance()
                  // Give the message a subject
                  ->setSubject($subject)
                  // Set the From address with an associative array
                  ->setFrom(array($fromaddress => 'FOF Draft Admin'))
                  // Set the To addresses with an associative array
                  ->setTo(array($individual)) 
                  // Give it a body
                  ->setBody($message);
                // Send the message
                try{
                    $emailresult = $mailer->send($messagetosend);
                } catch(Exception $e){
                    $_SESSION['message'] = "Error sending email: ".$e->getMessage();
                }
            }
        }
      } else {
	// The player needs to be removed from the queues
	// This can happen if the e-mails time out the selections
	$statement = "delete from selection where player_id = '$player_id'";
	mysql_query($statement);
      }
      

    }

    // Traverse the pick queue
    if ($recursive) {
      process_pick_queue();
    }
  }
}

function update_staff_amenable($round,$pick_of_new_on_clock_team){
    //first mark everyone amenable
    $statement="UPDATE `staff` SET `staff_amenable`='Y' WHERE `staff_retired`!=1";
    mysql_query($statement);

    //now process the reasons why someone would not be amenable to being drafted by a given team.
    ///calculate draft order of current team and the current team in game id
    //but we need to know the current team's ID number as well.  Get it from the pick number order
    $statement = "select team_id from `pick` where `player_id` is NULL or `player_id`=-100 order by pick_id asc limit 1";
    $row = mysql_fetch_array(mysql_query($statement));
    $tid = $row["team_id"];
    $statement = "select in_game_id from team where team_id=".$tid.";";
    $row = mysql_fetch_array(mysql_query($statement));
    $tid = $row["in_game_id"];

    //if you've ever been fired by this team you don't want the job
    $statement = "select staff_id from staff_trans_history where staff_team_id=".$tid." and (staff_trans_id=2 or staff_trans_id=4 or staff_trans_id=6 or staff_trans_id=8 or staff_trans_id=10)";
    $result = mysql_query($statement);
    while ( $result && $row = mysql_fetch_array($result) ){
        $wasfired = "update staff set staff_amenable='N' where staff_id=".$row["staff_id"].";";
        mysql_query($wasfired);
    }

    if ( $round==1 )//HC round
      $statement="UPDATE `staff` SET `staff_amenable`='N' WHERE `staff_retired`=1 or (`fired`=0 and `staff_recent_hire`=1) or (`fired`=0 and `staff_role_id`=1 and `staff_curr_team_id`!=99 and `staff_yrs_on_contract`>0) or 
(`fired`=0 and `staff_yrs_on_contract`>3) or (`fired`=0 and `staff_yrs_on_contract`=1 and `staff_role_id`=1 and (`staff_team_draft_order`-'$pick_of_new_on_clock_team'>3));";
    else if ( $round==2 )//OC round
           $statement="UPDATE `staff` SET `staff_amenable`='N' WHERE `staff_retired`=1 or (`fired`=0 and `staff_recent_hire`=1) or (`fired`=0 and `staff_role_id`=1 and `staff_curr_team_id`!=99 and `staff_yrs_on_contract`>0) or 
(`fired`=0 and `staff_yrs_on_contract`>3) or (`fired`=0 and `staff_yrs_on_contract`>1 and `staff_role_id`<4) or (`staff_role_id`=5 or `staff_role_id`=3 ) or 
(`fired`=0 and `staff_yrs_on_contract`=1 and `staff_role_id`=2 and (`staff_team_draft_order`-'$pick_of_new_on_clock_team'>3));";
    else if ( $round==3 )//DC round
      $statement="UPDATE `staff` SET `staff_amenable`='N' WHERE `staff_retired`=1 or (`fired`=0 and `staff_recent_hire`=1) or (`fired`=0 and `staff_role_id`=1 and `staff_curr_team_id`!=99 and `staff_yrs_on_contract`>0) or 
(`fired`=0 and `staff_yrs_on_contract`>3) or (`fired`=0 and `staff_yrs_on_contract`>1 and `staff_role_id`<4) or (`staff_role_id`=5 or `staff_role_id`=2 )or 
(`fired`=0 and `staff_yrs_on_contract`=1 and `staff_role_id`=3 and (`staff_team_draft_order`-'$pick_of_new_on_clock_team'>3));";
    else if ( $round==4 )//AC round
      $statement="UPDATE `staff` SET `staff_amenable`='N' WHERE `staff_retired`=1 or (`fired`=0 and `staff_recent_hire`=1) or (`fired`=0 and `staff_role_id`=1 and `staff_curr_team_id`!=99 and `staff_yrs_on_contract`>0) or 
(`fired`=0 and `staff_yrs_on_contract`>3) or (`fired`=0 and `staff_yrs_on_contract`>1 and `staff_role_id`<5) or (`fired`=0 and `staff_role_id`<4 and `staff_curr_team_id`!=99 )or (`fired`=0 and `staff_yrs_on_contract`=1 and `staff_role_id`=4 and (`staff_team_draft_order`-'$pick_of_new_on_clock_team'>3));";
    else if ( $round==5 )//STR round
      $statement="UPDATE `staff` SET `staff_amenable`='N' WHERE `staff_retired`=1 or (`fired`=0 and `staff_recent_hire`=1) or (`fired`=0 and `staff_role_id`=1 and `staff_curr_team_id`!=99 and `staff_yrs_on_contract`>0) or 
(`fired`=0 and `staff_yrs_on_contract`>3) or (`fired`=0 and `staff_curr_team_id`!=99) or (`staff_role_id`!=5 ) or (`fired`=0 and `staff_role_id`<5 and `staff_curr_team_id`!=99 ) or (`fired`=0 and `staff_yrs_on_contract`=1 and `staff_role_id`=5 and `staff_team_draft_order`-'$pick_of_new_on_clock_team'>3));";
    mysql_query($statement);
/*
SELECT * FROM `staff` WHERE `staff_retired`=1 or
(`staff_role_id`=0 and `staff_curr_team_id`!=99) or 
`staff_yrs_on_contract`>3 or
(`staff_yrs_on_contract`>1 and `staff_role_id`<3) or ROUND 2
(`staff_yrs_on_contract`>1 and `staff_role_id`<4) or ROUND 4
(`staff_curr_team_id`!=99) or ROUND 5
(`staff_role_id`=4 or `staff_role_id`=2 ) or ROUND 2
(`staff_role_id`=4 or `staff_role_id`=1 ) or ROUND 3
(`staff_role_id`!=4 ) or ROUND 5
(`staff_yrs_on_contract`=1 and `staff_role_id`=1 and DRAFTORDER) or ROUND 2
(`staff_yrs_on_contract`=1 and `staff_role_id`=2 and DRAFTORDER) or ROUND 3
(`staff_yrs_on_contract`=1 and `staff_role_id`=3 and DRAFTORDER) or ROUND 4
(`staff_yrs_on_contract`=1 and `staff_role_id`=4 and DRAFTORDER) or ROUND 5
*/
}

function process_pick_queue() {
  // Lock the database
  //$statement = "flush tables with read lock";
  //mysql_query($statement);
  // First check for any skipped team's picks
  $statement = "select pick.pick_id, team.team_id, pick.pick_time from pick, team where
pick.player_id = '".kSkipPick."'
and pick.team_id = team.team_id
order by pick.pick_id";
  $result = mysql_query($statement);
  while ($row = mysql_fetch_array($result)) {
    if ($row['team_id']) {
      $team = new team($row['team_id']);
      $player_id = $team->next_player($row['pick_start']);
      if ($player_id) {
	make_pick($row['pick_id'], $player_id);
	// make_pick will recursively call this function so we can just bail if we get this far
	return;
      }
    }
  }
  // See if the next team in the queue has a selection and make it if they do.
  $tables[] = "pick";
  $col[] = "pick.pick_id";
  $col[] = "pick.pick_time";
  $col[] = "pick.pick_start";
  $tables[] = "team";
  $col[] = "team.team_id";
  $wheres[] = "team.team_id = pick.team_id";
  $wheres[] = "pick.player_id is NULL";
  
  $statement = "select ".implode(",",$col)." from (".implode(",",$tables).")
where ".implode(" and ",$wheres)."
order by pick.pick_id limit 1";

  $row = mysql_fetch_array(mysql_query($statement));
  $team = new team($row['team_id']);
  $player_id = $team->next_player($row['pick_start']);
  if ($player_id) {
    make_pick($row['pick_id'], $player_id);
  }
}

function height_convert($inches) {
  if ($inches) {
    // Returns $inches in the feet and inches
    $feet = floor($inches/12);
    $inches = $inches%12;
    return "$feet' $inches\"";
  }
}

function calculate_pick($pick) {
  return (floor(($pick-1)/32)+1).' - '.((($pick-1)%32)+1).' ('.$pick.')';
}

function calculate_round(){
    $statement = "select pick_id,team_id from `pick` where `player_id` is NULL order by pick_id asc limit 1";
    $row = mysql_fetch_array(mysql_query($statement));
    $pick_id = $row["pick_id"];
    return floor(($pick_id)/32)+1;
}

function process_expired_picks() {
  global $settings;
  $limit = $settings->get_value(kSettingPickTimeLimit);
  if (!$limit) {
    // No limit, nothing to do
    return;
  }
  $tables[] = "pick";
  $col[] = "pick.pick_id";
  $col[] = "team.team_id";
  $tables[] = "team";
  $wheres[] = "team.team_id = pick.team_id";
  $wheres[] = "pick.player_id is NULL";
  $col[] = "pick.pick_time";
  $col[] = "if(team.team_clock_adj != '0', 
time_to_sec(timediff(date_add(pick.pick_time, interval (".$limit."*team.team_clock_adj) minute), '".date("Y-m-d H:i:s")."')),
-1) time_left";
  $col[] = "team.team_clock_adj";
  $statement = "select ".implode(",",$col)." from ".implode(",",$tables)." where ".implode(" and ",$wheres)."
order by pick_id
limit 1";
  $row = mysql_fetch_array(mysql_query($statement));
  if ($row['time_left'] < 0) {
    // Adjust the team's autopick value
    $team = new team($row['team_id']);
    $team->lower_pick_limit();
    // Pick has expired!
    $old_start = $row['pick_time'];
    $limit = $limit * $row['team_clock_adj'];
    if ($settings->get_value(kSettingExpiredPick) == kExpireMakePick) {
      $player_id = $team->force_pick();
      // Mark that this pick has expired
      $statement = "update pick set pick_expired = '1' where pick_id = '".$row['pick_id']."'";
      mysql_query($statement);
      if ($player_id && $player_id!=kDeclinePick) {
	make_pick($row['pick_id'], $player_id, false);
      } else if ( $settings->get_value(kSettingStaffDraftOn)==1 ) {
          $statement = "update pick set player_id = '".kDeclinePick."' where pick_id = '".$row['pick_id']."'";
          mysql_query($statement);
          $player_id = true;
      } else {
	$statement = "update pick set player_id = '".kSkipPick."' where pick_id = '".$row['pick_id']."'";
	mysql_query($statement);
	$player_id = true;
      }
    } else if ( $settings->get_value(kSettingStaffDraftOn)==1 ) {
          $statement = "update pick set player_id = '".kDeclinePick."' where pick_id = '".$row['pick_id']."'";
          mysql_query($statement);
          $player_id = true;
    } else {
      $statement = "update pick set player_id = '".kSkipPick."' where pick_id = '".$row['pick_id']."'";
      mysql_query($statement);
      $player_id = true;
    }
    if ($player_id) {
      // Next pick starts where this one expired
      reset_current_pick_clock();
      // Re-process the pick queue
      process_pick_queue();
      // Recursively call to see if this next pick is also expired
      process_expired_picks();
    }
  }
}

function reset_current_pick_clock() {
  // Checks the current pick and sets the clock if the clock is not yet set
  $statement = "select * from pick where player_id is NULL order by pick_id limit 1";
  $row = mysql_fetch_array(mysql_query($statement));
  if ($row['pick_id'] && !$row['pick_time']) {
    $team = new team($row['team_id']);
    $statement = "update pick set pick_time = '".$team->new_pick_time(time())."',
pick_start = '".date("Y-m-d H:i:s")."'
where pick_id = '".$row['pick_id']."'";
    mysql_query($statement);
  }
}

function fill_team_need() {
  $statement = "select pick_id, position_id, team_id from pick, player where
player.player_id = pick.player_id";
  $result = mysql_query($statement);
  while ($row = mysql_fetch_array($result)) {
    $team_id = $row['team_id'];
    $pick_id = $row['pick_id'];
    $position_id = $row['position_id'];
    $statement = "update team_need set pick_id = '$pick_id' where team_id = '$team_id' and position_id = '$position_id'";
    mysql_query($statement);
  }
}

// This last bit of code converts plaintext passwords in the database to encrypted
function encrypt_passwords() {
  $statement = "select * from team where team_password is not NULL and char_length(team_password) < 32";
  $result = mysql_query($statement);
  echo mysql_error();
  while ($row = mysql_fetch_array($result)) {
    $statement = "update team set team_password = '".md5($row['team_password'])."' where team_id = '".$row['team_id']."'";
    mysql_query($statement);
  }
}
?>