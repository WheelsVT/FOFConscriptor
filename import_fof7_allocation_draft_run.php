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
//include "includes/extractor_columns.inc.php";
include "includes/fof7_export_columns.inc.php";
// This function imports the draft file

ini_set("max_execution_time", "600"); 
set_time_limit( 600 );

global $settings;

if ($login->is_admin()) {
  $admin = true;
 } else {
  $admin = false;
 }

// Check to make sure we have a file
  if (!file_exists($_FILES['qbs']['tmp_name']) || !file_exists($_FILES['info']['tmp_name']) || !file_exists($_FILES['draft']['tmp_name']) || !file_exists($_FILES['team_info']['tmp_name']) 
          || !file_exists($_FILES['record']['tmp_name'])) {
    $_SESSION['message'] = "I did not receive the required files.  Be sure to upload the player ratings, the universe info, and team info csv files.";
    header("Location: ./import_draft.php");
    exit;
  }
  $player_ratings = $_FILES['draft']['tmp_name'];
  $player_record = $_FILES['record']['tmp_name'];
  $player_info = $_FILES['info']['tmp_name'];
  $qb_info = $_FILES['qbs']['tmp_name'];

// Ok, all is well, first let's clear out the existing draft data
  $settings->set_value(kSettingStaffDraftOn,0);
  $statement = "truncate table pick";
  mysql_query($statement);
  $statement = "truncate table player_temp";
  mysql_query($statement);
  $statement = "truncate table team_to_column";
  mysql_query($statement);
  $statement = "truncate table staff";
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



$file = file_get_contents($player_info);
$lines = preg_split("/[\n\r]+/", $file);
$header = true;
$upload_count = 0;
$staff = false;
foreach($lines as $line) {
  if ($header) {
    //check to see if we have a valid info  file
    if ($line != $valid_fof7_player_info ) {
      $_SESSION['message'] = "The file you imported does not appear to be a player info csv export file, or is the wrong version.
Please verify that you are uploading the correct file and that you have the current version of FOF7.";
      header("Location: import_draft.php");
      exit;
    }
  }  
  if ($line && !$header ) {
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
      $col["player_in_game_id"] = $columns[kInfoPlayer_ID];
      $col["player_name"] = addslashes(trim($columns[kInfoFirst_Name]));
      //if ( $columns[kInfoNickname]!=NULL )
          //$col["player_name"] .=' '.addslashes(trim($columns[kInfoNickname]));
      $col["player_name"] .=' '.addslashes(trim($columns[kInfoLast_Name]));
      if ( $columns[kInfoJunior_Flag]==1 )
          $col["player_name"] .=' Jr.';
      $statement = "select position_id from position_to_alias where alias_name='".trim($columns[kInfoPosition])."'";
      $result = mysql_fetch_array(mysql_query($statement));
      $col["position_id"] = $result["position_id"];
      $col["player_school"] = addslashes($columns[kInfoCollege]);
      $temp = (trim($columns[kInfoMonth_Born]).'/'.trim($columns[kInfoDay_Born]).'/'.trim($columns[kInfoYear_Born]));
      $col["player_dob"] = date("Y-m-d", strtotime(str_replace('-', '/', $temp)));
      $col["player_height"] = $columns[kInfoHeight];
      $col["player_weight"] = $columns[kInfoWeight];
      //$col["player_jersey"] = $columns[kJersey];
      //$col["player_vol"] = $columns[kVolatility];
      //$col["player_solec"] = $columns[kSolecismic];
      //$col["player_40"] = $columns[kForty]/100;
      //$col["player_bench"] = $columns[kBenchPress];
      //$col["player_agil"] = $columns[kAgility]/100;
      //$col["player_broad"] = $columns[kBroadJump];
      //$col["player_pos_drill"] = $columns[kPositionDrill];
      //$col["player_developed"] = $columns[kDeveloped];
//	 $col["player_score"] = $columns[kGrade]/10;
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
    
      $statement = "insert into player_temp (".implode(",",$tables).") values (".implode(",",$values).");";
      mysql_query($statement);
  } else {
    $header = false;
  }
}


//now factor in the player_record file
$file = file_get_contents($player_record);
$lines = preg_split("/[\n\r]+/", $file);
$header = true;
$upload_count = 0;
$staff = false;
foreach($lines as $line) {
  if ($header) {
    //check to see if we have a valid ratings file
    if ($line != $valid_fof7_player_record ) {
      $_SESSION['message'] = "The file you imported does not appear to be a player record csv export file, or is the wrong version.
Please verify that you are uploading the correct file and that you have the current version of FOF7.";
      header("Location: import_draft.php");
      exit;
    }
  }  
  if ($line && !$header ) {
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
      $col["player_in_game_id"] = $columns[kRecordPlayer_ID];
      $col["player_jersey"] = $columns[kRecordUniform_Number];
      $col["player_vol"] = $columns[kRecordVolatility];
      //$col["player_solec"] = $columns[kSolecismic];
      //$col["player_40"] = $columns[kForty]/100;
      //$col["player_bench"] = $columns[kBenchPress];
      //$col["player_agil"] = $columns[kAgility]/100;
      //$col["player_broad"] = $columns[kBroadJump];
      //$col["player_pos_drill"] = $columns[kPositionDrill];
      //$col["player_developed"] = $columns[kDeveloped];
//	 $col["player_score"] = $columns[kGrade]/10;
    $col["player_loyalty"] = $columns[kRecordLoyalty];
    $col["player_winner"] = $columns[kRecordPlay_for_Winner];
    $col["player_leader"] = $columns[kRecordLeadership];
    $col["player_intelligence"] = $columns[kRecordIntelligence];
    $col["player_personality"] = $columns[kRecordPersonality_Strength];
    $col["player_popularity"] = $columns[kRecordPopularity];
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
    
      $statement = "update player_temp set player_jersey=".$columns[kRecordUniform_Number].",
           player_vol=".$columns[kRecordVolatility].",
           player_experience=".$columns[kRecordExperience].",
           player_loyalty=".$columns[kRecordLoyalty].",
           player_winner=".$columns[kRecordPlay_for_Winner].",
           player_leader=".$columns[kRecordLeadership].",
           player_intelligence=".$columns[kRecordIntelligence].",
           player_personality=".$columns[kRecordPersonality_Strength].",
           player_popularity=".$columns[kRecordPopularity]." where player_in_game_id=".$columns[kRecordPlayer_ID];
      mysql_query($statement);
      $player_id = mysql_insert_id();

  } else {
    $header = false;
  }
}


//now for player ratings - first to get current/future for sorting purposes
$file = file_get_contents($player_ratings);
$lines = preg_split("/[\n\r]+/", $file);
$header = true;
$upload_count = 0;
$staff = false;
foreach($lines as $line) {
  if ($header) {
    //check to see if we have a valid ratings file
    if ($line != $valid_fof7_player_personal_ratings ) {
      $_SESSION['message'] = "The file you imported does not appear to be a player rating csv export file, or is the wrong version.
Please verify that you are uploading the correct file and that you have the current version of FOF7.";
      header("Location: import_draft.php");
      exit;
    }
  }  
  if ($line && !$header ) {
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
    $col["player_in_game_id"] = $columns[0];
    $col["player_current"] = $columns[59];
    $col["player_future"] = $columns[59+58];
    $statement = "update player_temp set player_current=".$col["player_current"].", player_future=".$col["player_future"]."
            where player_in_game_id=".$col["player_in_game_id"];
    mysql_query($statement);
  } else {
    $header = false;
  }
}


//now reorder based on current rating
if ($admin) {
  $statement = "insert into player select * from player_temp order by player_current desc";
  mysql_query($statement);
}


//ok we have to dig into the qbs file to find the formations this guy knows.
$qbfile = file_get_contents($qb_info);
$lines = preg_split("/[\n\r]+/", $qbfile);
$header = true;
$upload_count = 0;
$qb_id[] = array();
$qb_formations[] = array();
foreach($lines as $line) {
  if ($header) {
    //check to see if we have a valid ratings file
    if ($line != $valid_fof7_active_qb ) {
      $_SESSION['message'] = "The file you imported does not appear to be a active qb csv export file, or is the wrong version.
Please verify that you are uploading the correct file and that you have the current version of FOF7.";
      header("Location: import_draft.php");
      exit;
    }
  }  
  if ($line && !$header ) {
    preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $line, $matches);
    $columns = $matches[0];
    foreach($columns as $key=>$value) {
      // Remove the field qualifiers, if any
      $columns[$key] = trim(preg_replace("/^\"|\"$|\"?,$/", "", $value));
    }
    //$qb_id[] = $columns[0];
    $qb_formations[$columns[0]] = $columns[1];
  }
  if ($header)
    $header = false;
}
    
    
    
//now for the rest of the player ratings
$file = file_get_contents($player_ratings);
$lines = preg_split("/[\n\r]+/", $file);
$header = true;
$upload_count = 0;
$staff = false;
foreach($lines as $line) {
  if ($header) {
    //check to see if we have a valid ratings file
    if ($line != $valid_fof7_player_personal_ratings ) {
      $_SESSION['message'] = "The file you imported does not appear to be a player rating csv export file, or is the wrong version.
Please verify that you are uploading the correct file and that you have the current version of FOF7.";
      header("Location: import_draft.php");
      exit;
    }
  }  
  if ($line && !$header ) {
    // Get the data from this line
    // The following regex is from
    // http://www.bennadel.com/blog/976-Regular-Expressions-Make-CSV-Parsing-In-ColdFusion-So-Much-Easier-And-Faster-.htm
    // It will split the fields taking embedded quotes and commas into account
    // The resulting values will still have the quotes and commas in the data, which are removed by the preg_replace line
    preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $line, $matches);
    $columns = $matches[0];
    foreach($columns as $key=>$value) {
      // Remove the field qualifiers, if any
      $columns[$key] = trim(preg_replace("/^\"|\"$|\"?,$/", "", $value));
    }
    $col = array();
    $col["player_in_game_id"] = $columns[kRecordPlayer_ID];

    //determine the position this player plays and the player_id
    $statement = "select player_id,player_in_game_id,position_id from player where player_in_game_id=".$columns[kRecordPlayer_ID];
    $result = mysql_fetch_array(mysql_query($statement));

    //determine the attributes we need for this position
    $statement = "select * from attribute, position_to_attribute, fof7_rookie_attribute_map where
attribute.attribute_id = position_to_attribute.attribute_id and attribute.attribute_id= fof7_rookie_attribute_map.attribute_id 
and position_to_attribute.position_id = '".$result['position_id']."'
order by position_to_attribute_order";
    $result2 = mysql_query($statement);
    $i=0;
    $attributes = array();
    while ($row = mysql_fetch_array($result2)) {
        //if we don't have this attribute exported (currently only Formations?) we need to add it anyway!
	if ( $row["fof7_attribute_column"]==-1 ){//check if this is the formations field
            //for ( $i=0; $qb_id[$i]!=$result["player_in_game_id"]; $i++ ){}
            if ( $qb_formations[$result["player_in_game_id"]]>0 ){
               $formations = $qb_formations[$result["player_in_game_id"]];
               $statement = "insert into player_to_attribute (player_id, attribute_id, player_to_attribute_low, player_to_attribute_high) values(".$result["player_id"].", ".$row["attribute_id"].", ".$formations.", NULL);";
            } else
               $statement = "insert into player_to_attribute (player_id, attribute_id, player_to_attribute_low, player_to_attribute_high) values(".$result["player_id"].", ".$row["attribute_id"].", '0', '0');";
        } else
             $statement = "insert into player_to_attribute (player_id, attribute_id, player_to_attribute_low, player_to_attribute_high) values(".$result["player_id"].", ".$row["attribute_id"].", '".trim($columns[$row["fof7_attribute_column"]-1])."', '".trim($columns[$row["fof7_attribute_column"]-1+58])."');";
        mysql_query($statement);
    }
  } else {
    $header = false;
  }
}

  // Now for the draft order and preview file
  $teams = array();
  $tids = array();
  $pick_order = array('xxx'); // Initialize to the "no pick" value
  // Add the "no pick" value to the database
  $statement = "select * from team where team_name = 'xxx'";
  if (!mysql_num_rows(mysql_query($statement))) {
    $statement = "insert into team (team_name, team_password) values ('xxx', '".md5(uniqid(rand()))."')";
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
            // Populate the default colmuns, 1-12
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 1, 1)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 3, 2)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 4, 3)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 16, 4)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 25, 5)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values ('$teams[$city]', 26, 6)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 1, 1)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 3, 2)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 4, 3)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 16, 4)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 25, 5)";
            mysql_query($statement);
            $statement = "insert into team_to_column (team_id, column_id, team_to_column_order) values (1, 26, 6)";
            mysql_query($statement);

       }//end new team name handle
    }//end team row
  }//end team file for loop

  //time to generate a draft order
  $pick = 1;
  
  //check for snake draft
  $snake = false;
  if ( isset($_POST['snake']))
      $snake = true;
  
  //check for randomized team order
  for ($i=0; $i<32; $i++)
      $order[$i]=$tids[$i];
  if ( isset($_POST['randomize']))
      shuffle($order);
  
  
  for ($i=0; $i<53; $i++){
      if ( $i%2==0 && $snake ){
        for ($k=31; $k>=0; $k--){
            $pick_order[$pick]=$order[$k];
            $pick+=1;
        }
      } else {
        for ($k=0; $k<32; $k++){
            $pick_order[$pick]=$order[$k];
            $pick+=1;
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
 


  // Draft import is complete!
  $_SESSION['message'] = "Draft import complete.";
  header("Location: options.php");

?>