<?

/***************************************************************************
 *                                import_draft_run.php
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

include "includes/classes.inc.php";
// This function imports the draft file

global $settings;

if ($login->is_admin()) {
  $admin = true;
 } else {
  $admin = false;
 }

// Check to make sure we have a file
if ($admin) {
  if (!file_exists($_FILES['draft']['tmp_name']) || !file_exists($_FILES['draft_order']['tmp_name']) || !file_exists($_FILES['team_info']['tmp_name']) ) {
    $_SESSION['message'] = "I did not receive the required files.  Be sure to upload the staff/rookies, the universe info, and team info csv files.";
    header("Location: ./import_draft.php");
    exit;
  }
  $players_file = $_FILES['draft']['tmp_name'];
 } else {
  if (!file_exists($_FILES['team_players']['tmp_name'])) {
    $_SESSION['message'] = "There was an error uploading the file.";
    header("Location: ./import_draft.php");
  }
  $players_file = $_FILES['team_players']['tmp_name'];
 }

// Ok, all is well, first let's clear out the existing draft data
if ($admin) {
  $settings->set_value(kSettingStaffDraftOn,0);
  $statement = "truncate table pick";
  mysql_query($statement);
  $statement = "truncate table player_temp";
  mysql_query($statement);
  $statement = "truncate table team_to_column";
  mysql_query($statement);
  $statement = "truncate table staff";
  mysql_query($statement);
  $statement = "truncate table staff_selection";
  mysql_query($statement);
  $statement = "truncate table player";
  mysql_query($statement);
  $statement = "truncate table player_comments";
  mysql_query($statement);
  $statement = "truncate table team_player";
  mysql_query($statement);
  $statement = "truncate table player_to_attribute";
  mysql_query($statement);
  $statement = "truncate table team_player_to_attribute";
  mysql_query($statement);
  $statement = "truncate table selection";
  mysql_query($statement);
  $statement = "truncate table bpa";
  mysql_query($statement);
  $statement = "truncate table team_need";
  mysql_query($statement);
  $statement = "truncate table mock_draft";
  mysql_query($statement);
  $col[] = "team_clock_adj = '1'";
  $col[] = "team_autopick = '0'";
  $col[] = "pick_method_id = '1'";
  $col[] = "team_autopick_wait = '30'";
  $col[] = "team_email_prefs = '0'";
  $col[] = "team_sms_setting = '0'";
  $statement = "update team set ".implode(",",$col)." where team_id != '1'";
  mysql_query($statement);
 } else {
  $statement = "delete from team_player where team_id = '".$login->team_id()."'";
  mysql_query($statement);
  $statement = "delete from team_player_to_attribute where team_id = '".$login->team_id()."'";
  mysql_query($statement);
 }

// Now for the import
//include "includes/extractor_columns.inc.php";
include "includes/fof7_export_columns.inc.php";

// Interrogator contstants
//replaced in the export columns file
//define (kInterrogatorPlayerID, 1);
//define (kInterrogatorLastName, 3);
//define (kInterrogatorFirstName, 4);
//define (kInterrogatorPopularity, 17);
//define (kInterrogatorLoyalty, 8);
//define (kInterrogatorPlaysToWin, 9);
//define (kInterrogatorPersonality, 10);
//define (kInterrogatorLeadership, 11);
//define (kInterrogatorIntel, 12);
//define (kInterrogatorRedFlag, 13);
//define (kInterrogator, 17);




$file = file_get_contents($players_file);
$lines = preg_split("/[\n\r]+/", $file);
$header = true;
$upload_count = 0;
$staff = false;
foreach($lines as $line) {
  if ($header) {
    //check to see if we have a rookie file or a staff file
    if ($line != $valid_fof7_rookie  && $line != $valid_fof7_staff) {
      $_SESSION['message'] = "The file you imported does not appear to be a player or staff csv export file, or is the wrong version.
Please verify that you are uploading the correct file and that you have the current version of FOF7.";
      header("Location: import_draft.php");
      exit;
    }
    if ($line == $valid_fof7_staff && !file_exists($_FILES['transactions']['tmp_name']) ){
      $_SESSION['message'] = "It looks like you are running a staff draft, but I did not get last season's transaction CSV file.
Please upload the transaction file as well.";
      header("Location: import_draft.php");
      exit;
    }
    if ($line == $valid_fof7_rookie && !file_exists($_FILES['player_info']['tmp_name']) ){
      $_SESSION['message'] = "It looks like you are running a rookie draft, but I did not get the player_information CSV file.
Please upload that file as well.";
      header("Location: import_draft.php");
      exit;
    }
    if ($line == $valid_fof7_staff){
      $staff = true;
      $trans_file = $_FILES['transactions']['tmp_name'];
      //set the settings value so we can reference it in the utility
      $settings->set_value(kSettingStaffDraftOn,1);
	 $settings->set_value(kSettingExpiredPick,kExpireMakePick);
    }
    if ($line == $valid_fof7_rookie){
      $player_info_file = $_FILES['player_info']['tmp_name'];
      $firstpass = true;
    }
  }

  //if we are dealing with a staff draft
  if ($line && !$header && $staff) {
     //time to import the staff data
    preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $line, $matches);
    $columns = $matches[0];
    foreach($columns as $key=>$value) {
      // Remove the field qualifiers, if any
      $columns[$key] = preg_replace("/^\"|\"$|\"?,$/", "", $value);
    }
    $col = array();
    $col["staff_in_game_id"] = $columns[kstaff_id];
    $col["staff_name"] = addslashes(trim($columns[kstaff_first_name])).' '.addslashes(trim($columns[kstaff_last_name]));
    $col["staff_curr_team_id"] = $columns[kstaff_curr_team_id];
    $rolestatement = "select staff_role_id from staff_roles where staff_role_name='".trim($columns[kstaff_role])."'";
    $roleid = mysql_fetch_array(mysql_query($rolestatement));
    $col["staff_role_id"] = $roleid["staff_role_id"];
    $pristatement = "select staff_pri_group_id from staff_pri_group where staff_pri_group_name='".trim($columns[kstaff_pri_group])."'";
    $priid = mysql_fetch_array(mysql_query($pristatement));
    $col["staff_pri_group_id"] = $priid["staff_pri_group_id"];
    $col["staff_salary"] = $columns[kstaff_salary];
    $col["staff_player_dev"] = $columns[kstaff_player_dev];
    $col["staff_young_player_dev"] = $columns[kstaff_young_player_dev];
    $col["staff_motivation"] = $columns[kstaff_motivation];
    $col["staff_discipline"] = $columns[kstaff_discipline];
    $col["staff_play_calling"] = $columns[kstaff_play_calling];
    $col["staff_str_training"] = $columns[kstaff_str_training];
    $col["staff_conditioning"] = $columns[kstaff_conditioning];
    $col["staff_intelligence"] = $columns[kstaff_intelligence];
    $col["staff_scouting"] = $columns[kstaff_scouting];
    $col["staff_interviewing"] = $columns[kstaff_interviewing];
    $col["staff_age"] = $columns[kstaff_age];
    $col["staff_retired"] = $columns[kstaff_retired];
    $col["staff_yrs_on_contract"] = $columns[kstaff_yrs_on_contract];
    $col["staff_suitable_hc"] = $columns[kstaff_suitable_hc];
    $col["staff_suitable_oc"] = $columns[kstaff_suitable_oc];
    $col["staff_suitable_dc"] = $columns[kstaff_suitable_dc];
    $col["staff_suitable_ac"] = $columns[kstaff_suitable_ac];
    $col["staff_suitable_sc"] = $columns[kstaff_suitable_sc];

    $tables = array();
    $values = array();
    foreach($col as $key=>$value) {
      $tables[] = $key;
      if ($value || $value=='0') {
	$values[] = "'".$value."'";
      } else {
	$values[] = "'0'";
      }
    }
    //as long as they are not retired add them to the database.
    if ( strcmp(trim($col["staff_retired"]),"1")!=0 ){
        $statement = "insert into staff (".implode(",",$tables).") values (".implode(",",$values).")";
        mysql_query($statement);
        $staff_id = mysql_insert_id();
    }
  }
  
  if ($line && !$header && !$staff && $firstpass){
    $firstpass = false;
    //Compute DOB using the player_info file
    $infofile = file_get_contents($player_info_file);
    $infolines = preg_split("/[\n\r]+/", $infofile);
    $infoheader = true;
    $player_dob[] = array();
    foreach($infolines as $infoline) {
      if ($header) {
        //check to see if we have a valid ratings file
        if ($infoline != $valid_fof7_player_info ) {
          $_SESSION['message'] = "The file you imported does not appear to be a valid player info csv export file, or is the wrong version.
    Please verify that you are uploading the correct file and that you have the current version of FOF7.";
          header("Location: import_draft.php");
          exit;
        }
      }  
      if ($infoline && !$infoheader ) {
        preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $infoline, $infomatches);
        $infocolumns = $infomatches[0];
        foreach($infocolumns as $key=>$value) {
          // Remove the field qualifiers, if any
          $infocolumns[$key] = trim(preg_replace("/^\"|\"$|\"?,$/", "", $value));
        }
        $temp = (trim($infocolumns[kInfoMonth_Born]).'/'.trim($infocolumns[kInfoDay_Born]).'/'.trim($infocolumns[kInfoYear_Born]));
        $player_dob[$infocolumns[kInfoPlayer_ID]] = date("Y-m-d", strtotime(str_replace('-', '/', $temp)));
      }
      if ($infoheader)
        $infoheader = false;
    }
  }
  //if we are dealing with a player draft and we've handled the DOB already
  if ($line && !$header && !$staff) {
    // Get the data from this line
    // The following regex is from
    // http://www.bennadel.com/blog/976-Regular-Expressions-Make-CSV-Parsing-In-ColdFusion-So-Much-Easier-And-Faster-.htm
    // It will split the fields taking embedded quotes and commas into account
    // The resulting values will still have the quotes and commas in the data, which are removed by the preg_replace line
      


    preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $line, $matches);
    $columns = $matches[0];
    foreach($columns as $key=>$value) {
      // Remove the field qualifiers, if any
      $columns[$key] = preg_replace("/^\"|\"$|\"?,$/", "", $value);
    }
    $col = array();
    if ($admin) {
      $col["player_in_game_id"] = $columns[kInGameID];
      $col["player_name"] = addslashes(trim($columns[kFirstName])).' '.addslashes(trim($columns[kLastName]));
	 $posstatement = "select position_id from position where position_name='".trim($columns[kPosGrp])."'";
      $posid = mysql_fetch_array(mysql_query($posstatement));
      $col["position_id"] = $posid["position_id"];
      $col["player_school"] = addslashes($columns[kSchool]);
      $col["player_dob"] = $player_dob[$columns[kInGameID]];
      //$col["player_hometown"] = addslashes($columns[kHomeTown]);
      //$col["player_agent"] = addslashes($columns[kAgent]);
      //$col["player_designation"] = addslashes($columns[kDesignation]);
      $col["player_height"] = $columns[kHeight];
      $col["player_weight"] = $columns[kWeight];
      $col["player_experience"] = $columns[kExperience];
      //$col["player_jersey"] = $columns[kJersey];
      //$col["player_vol"] = $columns[kVolatility];
      $col["player_solec"] = $columns[kSolecismic];
      $col["player_40"] = $columns[kForty]/100;
      $col["player_bench"] = $columns[kBenchPress];
      $col["player_agil"] = $columns[kAgility]/100;
      $col["player_broad"] = $columns[kBroadJump];
      $col["player_pos_drill"] = $columns[kPositionDrill];
      $col["player_developed"] = $columns[kDeveloped];
	 $col["player_score"] = $columns[kGrade]/10;
    } else {
      // Look up the player
      $statement = "select * from player where player_name = '".addslashes($columns[kName])."' and
position_id = '".$positions[$columns[kPosition]]."' and
player_dob = '".date("Y-m-d", strtotime(str_replace("-", "/", $columns[kBorn])))."'";
      $row = mysql_fetch_array(mysql_query($statement));
      $col["player_id"] = $row['player_id'];
      $player_id = $row['player_id'];
      if ($row['player_id']) {
	$upload_count++;
      }
      $col["team_id"] = $login->team_id();
    }
    //$col["player_loyalty"] = $columns[kLoyalty];
    //$col["player_winner"] = $columns[kWinner];
    //$col["player_leader"] = $columns[kLeader];
    //$col["player_intelligence"] = $columns[kIntelligence];
    //$col["player_personality"] = $columns[kPersonality];
    //$col["player_popularity"] = $columns[kPopularity];
    //$col["player_mentor_to"] = $columns[kMentorTo];
    //$col["player_interviewed"] = $columns[kInterviewed];
    //$col["player_impression"] = $columns[kImpression];
    //$col["player_current"] = $columns[kCurrent];
    //$col["player_future"] = $columns[kFuture];
    //$col["player_conflicts"] = $columns[kConflicts];
    //$col["player_affinities"] = $columns[kAffinities];
    //$col["player_character"] = $columns[kCharacter];
    
    $tables = array();
    $values = array();
    foreach($col as $key=>$value) {
      $tables[] = $key;
      if ($value && $value != '0.00') {
	$values[] = "'".$value."'";
      } else {
	$values[] = "NULL";
      }
    }
    
    if ($login->is_admin()) {
      $statement = "insert into player_temp (".implode(",",$tables).") values (".implode(",",$values).")";
      mysql_query($statement);
      $player_id = mysql_insert_id();
    } else {
      $statement = "insert into team_player(".implode(",",$tables).") values (".implode(",",$values).")";
      mysql_query($statement);
    }
/*
    // Attributes
    if ($positions[$columns[kPosition]] == 1) {
      // QB, the first attribute only has one value
      $qb = true;
    } else {
      $qb = false;
    }
    $cur_col = 35;
    $statement = "select * from position_to_attribute where position_id = '".$positions[$columns[kPosition]]."'
order by position_to_attribute_order";
    $result = mysql_query($statement);
    while ($row = mysql_fetch_array($result)) {
      if ($qb && $row['attribute_id'] == 1) {
	// This is the odd attribute, formations, with only one value
	if ($admin) {
	  $statement = "insert into player_to_attribute
(player_id, attribute_id, player_to_attribute_low, player_to_attribute_high)
values
('$player_id', '1', '".$columns[$cur_col]."', NULL)";
	} else {
	  $statement = "insert into team_player_to_attribute
(team_id, player_id, attribute_id, player_to_attribute_low, player_to_attribute_high)
values
('".$login->team_id()."', '$player_id', '1', '".$columns[$cur_col]."', NULL)";
	}
	$cur_col++;
      } else {
	$low = $columns[$cur_col];
	$cur_col++;
	$high = $columns[$cur_col];
	$cur_col++;
	if ($admin) {
	  $statement = "insert into player_to_attribute
(player_id, attribute_id, player_to_attribute_low, player_to_attribute_high)
values
('$player_id', '".$row['attribute_id']."', '$low', '$high')";
	} else {
	  $statement = "insert into team_player_to_attribute
(team_id, player_id, attribute_id, player_to_attribute_low, player_to_attribute_high)
values
('".$login->team_id()."','$player_id', '".$row['attribute_id']."', '$low', '$high')";
	}
      }
      mysql_query($statement);
    }
*/
  } else {
    $header = false;
  }
}

//now reorder based on grade
if ($admin) {
  $statement = "insert into player select * from player_temp order by player_score desc";
  mysql_query($statement);
  
}

// Now for the draft order and preview file
if ($admin) {
  $teams = array();
  $tids = array();
  $pick_order = array('xxx'); // Initialize to the "no pick" value
  // Add the "no pick" value to the database
  $statement = "select * from team where team_name = 'xxx'";
  if (!mysql_num_rows(mysql_query($statement))) {
    $statement = "insert into team (team_name, team_password, in_game_id) values ('xxx', '".md5(uniqid(rand()))."', '99')";
    mysql_query($statement);
  }
  //now build the team table from the team info csv
  unset ($teamfile);
  $teamfile = file_get_contents($_FILES['team_info']['tmp_name']);
  $lines = array();
  $lines = preg_split("/[\n\r]+/", $teamfile);
  foreach($lines as $line) {
    $re1='(\\d+)';	# Integer Number 1
    $re2='.*?';	# Non-greedy match on filler
    $re3='(?:[a-z][a-z]+)';	# Uninteresting: word
    $re4='.*?';	# Non-greedy match on filler
    $re5='(?:[a-z][a-z]+)';	# Uninteresting: word
    $re6='.*?';	# Non-greedy match on filler
    $re7='(?:[a-z][a-z]+)';	# Uninteresting: word
    $re8='.*?';	# Non-greedy match on filler
    $re9='(.*?),';	# Word 1
    if ( preg_match_all ("/".$re1.$re2.$re3.$re4.$re5.$re6.$re7.$re8.$re9."/is", $line, $matches))
    {
      $fields = preg_split("/,/",$line);
      $city = trim($fields[14]);
      $id=trim($fields[0]);
      //put in -100 for decline pick where this team currently has staff hired as 1st priority, and
      //if they don't have someone hired insert -200 for scout selection.
      $statement = "select * from staff where staff_curr_team_id = ".$id." and staff_role_id = 1";
      if ( mysql_fetch_array(mysql_query($statement)) ){
          $statement = "insert into staff_selection (team_id,staff_id,staff_role,selection_priority) VALUES (".$id.",-100,1,1)";
          mysql_query($statement);
      } else {
          $statement = "insert into staff_selection (team_id,staff_id,staff_role,selection_priority) VALUES (".$id.",-200,1,1)";
          mysql_query($statement);
      }
      $statement = "select * from staff where staff_curr_team_id = ".$id." and staff_role_id = 2";
      if ( mysql_fetch_array(mysql_query($statement)) ){
          $statement = "insert into staff_selection (team_id,staff_id,staff_role,selection_priority) VALUES (".$id.",-100,2,1)";
          mysql_query($statement);
      } else {
          $statement = "insert into staff_selection (team_id,staff_id,staff_role,selection_priority) VALUES (".$id.",-200,2,1)";
          mysql_query($statement);
      }
      $statement = "select * from staff where staff_curr_team_id = ".$id." and staff_role_id = 3";
      if ( mysql_fetch_array(mysql_query($statement)) ){
          $statement = "insert into staff_selection (team_id,staff_id,staff_role,selection_priority) VALUES (".$id.",-100,3,1)";
          mysql_query($statement);
      } else {
          $statement = "insert into staff_selection (team_id,staff_id,staff_role,selection_priority) VALUES (".$id.",-200,3,1)";
          mysql_query($statement);
      }
      $statement = "select * from staff where staff_curr_team_id = ".$id." and staff_role_id = 4";
      if ( mysql_fetch_array(mysql_query($statement)) ){
          $statement = "insert into staff_selection (team_id,staff_id,staff_role,selection_priority) VALUES (".$id.",-100,4,1)";
          mysql_query($statement);
      } else {
          $statement = "insert into staff_selection (team_id,staff_id,staff_role,selection_priority) VALUES (".$id.",-200,4,1)";
          mysql_query($statement);
      }
      $statement = "select * from staff where staff_curr_team_id = ".$id." and staff_role_id = 5";
      if ( mysql_fetch_array(mysql_query($statement)) ){
          $statement = "insert into staff_selection (team_id,staff_id,staff_role,selection_priority) VALUES (".$id.",-100,5,1)";
          mysql_query($statement);
      } else {
          $statement = "insert into staff_selection (team_id,staff_id,staff_role,selection_priority) VALUES (".$id.",-200,5,1)";
          mysql_query($statement);
      }
      //$city=trim($matches[2][0]);
      if (!array_key_exists($city, $teams)) {
	    $statement = "select * from team where team_name = '$city'";
	    $row = mysql_fetch_array(mysql_query($statement));
	    if (!$row['team_id']) {
	      $statement = "insert into team (team_name, in_game_id) values ('$city','$id')";
	      mysql_query($statement);
	      $teams[$city] = mysql_insert_id();
	      $tids[$id] = $city;
	    } else {
	      $teams[$city] = $row['team_id'];
           $tids[$row['in_game_id']]=$city;
	    }
         if (!$staff){
            // Populate the default colmuns, 1-12
            $i=1;
	       while ($i<=12) {
               if ( $i==5 )
                  $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', ', 12, '$i')";
               else if ( $i==12 )
                  $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', ', 29, '$i')";
               else
	            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', '$i', '$i')";
               mysql_query($statement);
               $i++;
            }
		 // Populate the default colmuns, 1-12
            $i=1;
	       while ($i<=12) {
               if ( $i==5 )
                  $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 12, '$i')";
               else if ( $i==12 )
                  $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 29, '$i')";
               else
	            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, '$i', '$i')";
               mysql_query($statement);
               $i++;
            }
         } else {
            // Populate the default STAFF colmuns, 1-12
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 31, 1)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 32, 2)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 47, 3)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 33, 4)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 36, 5)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 43, 6)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 48, 7)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 49, 8)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 50, 9)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 51, 10)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 52, 11)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 53, 12)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 31, 1)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 32, 2)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 47, 3)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 33, 4)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 36, 5)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 43, 6)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 48, 7)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 49, 8)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 50, 9)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 51, 10)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 52, 11)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 53, 12)";
            mysql_query($statement);
         }
       }//end new team name handle
    }//end team row
  }//end team file for loop

  //time to process the draft order from the universe csv
  //if we're looking at the staff draft then the regex needs to be different
  unset ($file);
  $file = file_get_contents($_FILES['draft_order']['tmp_name']);
  $lines = array();
  $lines = preg_split("/[\n\r]+/", $file);
  $round = 1;
  $pick = 1;

  foreach($lines as $line) {
    $fields = preg_split("/,/",$line);
    if (!$staff && strcmp($fields[0],"Draft Order")==0){
      //we have a pick line, process it
      $fields = preg_split("/,/",$line);
      $team_id = trim($fields[3]);
      $pick_order[$pick] = $tids[$team_id];
      $orig_pick_order[$pick] = $tids[$team_id]; //aston fix: original state of pick_order must be preserved
      $pick+=1;
    }
    if (!$staff && strcmp($fields[0],"Draft Pick Owner - This Year")==0){
        //now see if the pick owner is actually different.
        $round = $fields[1];
        $orig_team = $fields[2]-1;
        if ( $orig_team!=$fields[3] ){
            //they traded that pick away
            $start = ($round-1)*32 + 1; //aston fix; $pick_order runs from index 1, not index 0.
            /* aston fix: must compare to $orig_pick_order, not $pick_order which is being modified as we go */
            for ( $i=$start; $orig_pick_order[$i]!=$tids[$orig_team]; $i++){}
            
            //aston fix for cap violation-removed picks
            if(strcmp($fields[3],"89") == 0) {
                $pick_order[$i] = 'xxx';
            }
            
            else {
                $pick_order[$i] = $tids[$fields[3]];
            }
        }
    }
    if ($staff && strcmp($fields[0],"Staff Draft Order")==0){
      //we have a pick line, process it
      $fields = preg_split("/,/",$line);
      $team_id = trim($fields[3]);
      $pick_order[$pick] = $tids[$team_id];
      $pick+=1;
    }
    if ($staff && strcmp($fields[0],"Staff Draft Pick Owner")==0){
        //now see if the pick owner is actually different.
        $round = $fields[1];
        $orig_team = $fields[2]-1;
        if ( $orig_team!=$fields[3] ){
            //they traded that pick away
            $start = ($round-1)*32;
            for ( $i=$start; $pick_order[$i]!=$tids[$orig_team]; $i++){}
            $pick_order[$i] = $tids[$fields[3]];
            //update the staff table with the draft order for this team for applicable staff
            $statement = "UPDATE staff set staff.staff_team_draft_order='$pick' where staff.staff_curr_team_id='$fields[3]'";
            mysql_query($statement);
        } else {
            //update the staff table with the draft order for this team for applicable staff
            $statement = "UPDATE staff set staff.staff_team_draft_order='$pick' where staff.staff_curr_team_id='$team_id'";
            mysql_query($statement);
        }
    }
  }

  if (!is_array($pick_order)) {
    $_SESSION['message'] = "Unable to parse the draft order file.";
    header("Location: import_draft.php");
    exit;
  }
  
  // Store the processed data into the database
  foreach($pick_order as $key=>$value) {
    if ($value == 'xxx') {
      $player_id = '-1';
    } else {
      $player_id = kDraftHalt;
    }
    $statement = "insert into pick (pick_id, team_id, player_id) values ('$key', '".$teams[$value]."', $player_id)";
    mysql_query($statement);
  }
 


  if ( $staff ){
     //we need to process transactions then update the recently hired field
     $file = file_get_contents($trans_file);
     $lines = preg_split("/[\n\r]+/", $file);
     $header = true;
     $upload_count = 0;
     $staff = false;
     foreach($lines as $line) {
       if ($header) {
         //check to see if we have a rookie file or a staff file
         if ($line != $valid_fof7_transaction) {
           $_SESSION['message'] = "The file you imported does not appear to be a transaction csv export file, or is the wrong version.
Please verify that you are uploading the correct file and that you have the current version of FOF7.";
           header("Location: import_draft.php");
           exit;
         }
       }
       //if we are dealing with a transaction file
       if ($line && !$header) {
          //time to import the trans data
         preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $line, $matches);
         $columns = $matches[0];
         foreach($columns as $key=>$value) {
           // Remove the field qualifiers, if any
           $columns[$key] = preg_replace("/^\"|\"$|\"?,$/", "", $value);
         }
         if ( strcmp($columns[ktrans_stage],"Staff Draft")==0 ){
            //we are dealing with a staff draft transaction that we want to record
            $col = array();
            $col["staff_trans_year"] = $columns[ktrans_year];
            $col["staff_id"] = $columns[ktrans_player_id];
            //convert the transaction text to an id number
            $transstatement = "select staff_trans_id from staff_trans_types where staff_trans_name='".trim($columns[ktrans_trans])."'";
            $transid = mysql_fetch_array(mysql_query($transstatement));
            $col["staff_trans_id"] = $transid["staff_trans_id"];
            $col["staff_team_id"] = $columns[ktrans_team_id];
            $tables = array();
            $values = array();
            foreach($col as $key=>$value) {
              $tables[] = $key;
              if ($value || $value=='0') {
                $values[] = "'".$value."'";
              } else {
                $values[] = "'0'";
              }
            }
            $statement = "insert into staff_trans_history (".implode(",",$tables).") values (".implode(",",$values).")";
            mysql_query($statement);

            //update the recently hired field
            if ( $col["staff_trans_id"]==1 || $col["staff_trans_id"]==3 || $col["staff_trans_id"]==5 || $col["staff_trans_id"]==7 || $col["staff_trans_id"]==9 ){           
              $statement = "UPDATE `staff` SET `staff_recent_hire`=1 WHERE `staff_in_game_id`='".$col["staff_id"]."'";
              mysql_query($statement);
            }
         }
       } else {
         $header = false;
       }
     }//end file for loop     

     //update the amenable field
     update_staff_amenable (1,1);
  }
  
  
  

/*
  // draftpreview.html
  if (file_exists($_FILES['draft_preview']['tmp_name'])) {
    $file = file_get_contents($_FILES['draft_preview']['tmp_name']);
    $lines = array();
    $lines = preg_split("/[\n\r]+/", $file);
    foreach($lines as $line) {
      preg_match_all("/<TD ?[A-Z=]*>([^<]+)<\/TD>/", $line, $data, PREG_PATTERN_ORDER);
      if ($data[1][0]) {
	$name = $data[1][0];
	$position_id = $positions[$data[1][1]];
	$grade = $data[1][3];
	$adj_grade = $data[1][4];
	$statement = "update player set player_score = '$grade', player_adj_score = '$adj_grade' where
player_name = '".addslashes($name)."' and position_id = '$position_id'";
	mysql_query($statement);
      }
    }
  }
*/

  // Draft import is complete!
  $_SESSION['message'] = "Draft import complete.";
  header("Location: options.php");
 } else {
  if ($upload_count) {
    $_SESSION['message'] = $upload_count." Player Profiles have been uploaded.";
    header("Location: players.php");
  } else {
    $_SESSION['message'] = "The file you uploaded appears to be an Extractor file, but I didn't find
any matches to the players in the system.  Are you sure you uploaded the correct file?";
    header("Location: import_draft.php");
  }
 }
?>