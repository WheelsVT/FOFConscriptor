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

if ($login->is_admin()) {
  $admin = true;
 } else {
  $admin = false;
 }

// Check to make sure we have a file
if ($admin) {
  if (!file_exists($_FILES['draft']['tmp_name']) || !file_exists($_FILES['draft_order']['tmp_name'])) {
    $_SESSION['message'] = "I did not receive the required files.  Be sure to upload both the players and draft order.";
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
  $statement = "truncate table pick";
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
  $col[] = "pick_method_id = '3'";
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
include "includes/extractor_columns.inc.php";

// Interrogator contstants
define (kInterrogatorPlayerID, 1);
define (kInterrogatorLastName, 3);
define (kInterrogatorFirstName, 4);
define (kInterrogatorPopularity, 17);
define (kInterrogatorLoyalty, 8);
define (kInterrogatorPlaysToWin, 9);
define (kInterrogatorPersonality, 10);
define (kInterrogatorLeadership, 11);
define (kInterrogatorIntel, 12);
define (kInterrogatorRedFlag, 13);
define (kInterrogator, 17);

$file = file_get_contents($players_file);
$lines = preg_split("/[\n\r]+/", $file);
$header = true;
$upload_count = 0;
foreach($lines as $line) {
  if ($header) {
    // Make sure we have the current version of Extractor coming in.
    if ($line != $valid_extractor) {
      $_SESSION['message'] = "The file you imported does not appeart to be an Extractor file, or is the wrong version.
Please verify that you are uploading the correct file and that you have the current version of Extractor.";
      header("Location: import_draft.php");
      exit;
    }
  }
  if ($line && !$header) {
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
      $col["player_name"] = addslashes($columns[kName]);
      $col["position_id"] = $positions[$columns[kPosition]];
      $col["player_school"] = addslashes($columns[kSchool]);
      $col["player_dob"] = date("Y-m-d", strtotime(str_replace("-", "/", $columns[kBorn])));
      $col["player_hometown"] = addslashes($columns[kHomeTown]);
      $col["player_agent"] = addslashes($columns[kAgent]);
      $col["player_designation"] = addslashes($columns[kDesignation]);
      $col["player_height"] = $columns[kHeight];
      $col["player_weight"] = $columns[kWeight];
      $col["player_experience"] = $columns[kExperience];
      $col["player_jersey"] = $columns[kJersey];
      $col["player_vol"] = $columns[kVolatility];
      $col["player_solec"] = $columns[kSolecismic];
      $col["player_40"] = $columns[kForty];
      $col["player_bench"] = $columns[kBenchPress];
      $col["player_agil"] = $columns[kAgility];
      $col["player_broad"] = $columns[kBroadJump];
      $col["player_pos_drill"] = $columns[kPositionDrill];
      $col["player_developed"] = $columns[kDeveloped];
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
    $col["player_loyalty"] = $columns[kLoyalty];
    $col["player_winner"] = $columns[kWinner];
    $col["player_leader"] = $columns[kLeader];
    $col["player_intelligence"] = $columns[kIntelligence];
    $col["player_personality"] = $columns[kPersonality];
    $col["player_popularity"] = $columns[kPopularity];
    $col["player_mentor_to"] = $columns[kMentorTo];
    $col["player_interviewed"] = $columns[kInterviewed];
    $col["player_impression"] = $columns[kImpression];
    $col["player_current"] = $columns[kCurrent];
    $col["player_future"] = $columns[kFuture];
    $col["player_conflicts"] = $columns[kConflicts];
    $col["player_affinities"] = $columns[kAffinities];
    $col["player_character"] = $columns[kCharacter];
    
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
      $statement = "insert into player (".implode(",",$tables).") values (".implode(",",$values).")";
      mysql_query($statement);
      $player_id = mysql_insert_id();
    } else {
      $statement = "insert into team_player(".implode(",",$tables).") values (".implode(",",$values).")";
      mysql_query($statement);
    }

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
  } else {
    $header = false;
  }
}

// Now for the draft order and preview file
if ($admin) {
  $teams = array();
  unset ($file);
  $file = file_get_contents($_FILES['draft_order']['tmp_name']);
  $lines = array();
  $lines = preg_split("/[\n\r]+/", $file);
  $round = 1;
  $pick_order = array('xxx'); // Initialize to the "no pick" value
  // Add the "no pick" value to the database
  $statement = "select * from team where team_name = 'xxx'";
  if (!mysql_num_rows(mysql_query($statement))) {
    $statement = "insert into team (team_name, team_password) values ('xxx', '".md5(uniqid(rand()))."')";
    mysql_query($statement);
  }
  foreach($lines as $line) {
    $pick = $round;
    if (preg_match("/^[0-9]/", $line)) {
      // We have a pick line, process it
      $columns = preg_split("/\s+/", $line);
      // Convert everything to the 3-character city abbreviation
      foreach($columns as $key=>$value) {
	$value = preg_replace("/[^A-Zx]/", "", $value);
	if ($value) {
	  if (!array_key_exists($value, $teams)) {
	    $statement = "select * from team where team_name = '$value'";
	    $row = mysql_fetch_array(mysql_query($statement));
	    if (!$row['team_id']) {
	      $statement = "insert into team (team_name) values ('$value')";
	      mysql_query($statement);
	      $teams[$value] = mysql_insert_id();
	      $team_id = $teams[$value];
	      // Populate the default colmuns, 1-12
	      $i=1;
	      while ($i<=12) {
		$statement = "insert into team_to_column (team_id, column_id, team_to_column_order)
values ('$team_id', '$i', '$i')";
		mysql_query($statement);
		$i++;
	      }
	    } else {
	      $teams[$value] = $row['team_id'];
	    }
	  }
	  $pick_order[$pick] = $value;
	  $pick += 32;
	}
      }
      $round++;
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

  // Now process the player_active and player_historical files
  if (file_exists($_FILES['player_active']['tmp_name']) && file_exists($_FILES['player_historical']['tmp_name'])) {
    $file = file_get_contents($_FILES['player_historical']['tmp_name']);
    $lines = preg_split("/[\n\r]+/", $file);
    $interrogator_players = array();
    foreach($lines as $line) {
      preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $line, $matches);
      $columns = $matches[0];
      foreach($columns as $key=>$value) {
	// Remove the field qualifiers, if any
	$columns[$key] = preg_replace("/^\"|\"$|\"?,$/", "", $value);
      }
      // See if the player exists
      $name = addslashes($columns[kInterrogatorFirstName].' '.$columns[kInterrogatorLastName]);
      $statement = "select * from player where player_name like '$name'";
      $row = mysql_fetch_array(mysql_query($statement));
      if ($row['player_id']) {
	$interrogator_players[$columns[kInterrogatorPlayerID]] = $row['player_id'];
      }
    }
    // $interrogator_players is built, now read the player_active file
    $file = file_get_contents($_FILES['player_active']['tmp_name']);
    $lines = preg_split("/[\n\r]+/", $file);
    foreach($lines as $line) {
      preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $line, $matches);
      $columns = $matches[0];
      foreach($columns as $key=>$value) {
	// Remove the field qualifiers, if any
	$columns[$key] = preg_replace("/^\"|\"$|\"?,$/", "", $value);
      }
      $player_id = $interrogator_players[$columns[kInterrogatorPlayerID]];
      if ($player_id) {
	$statement = "update player set player_popularity = '".$columns[kInterrogatorPopularity]."'
where player_id = '$player_id'";
        mysql_query($statement);
        $statement = "update player set player_winner = '".$columns[kInterrogatorPlaysToWin]."'
where player_id = '$player_id'";
        mysql_query($statement);
        $statement = "update player set player_loyalty = '".$columns[kInterrogatorLoyalty]."'
where player_id = '$player_id'";
        mysql_query($statement);
        $statement = "update player set player_personality = '".$columns[kInterrogatorPersonality]."'
where player_id = '$player_id'";
        mysql_query($statement);
        $statement = "update player set player_leader = '".$columns[kInterrogatorLeadership]."'
where player_id = '$player_id'";
        mysql_query($statement);
        $statement = "update player set player_intelligence = '".$columns[kInterrogatorIntel]."'
where player_id = '$player_id'";
        mysql_query($statement);
        $statement = "update player set player_character = '".$columns[kInterrogatorRedFlag]."'
where player_id = '$player_id'";
        mysql_query($statement);
      }
    }
  }

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