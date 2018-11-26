<?
/***************************************************************************
 *                                import_mock_draft_run.php
 *                            --------------------------------
 *   begin                : Friday, Aug 22, 2008
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
include "includes/fof7_export_columns.inc.php";

// This function imports the mock draft file, or processes it.
if (!$login->is_admin()) {
  header("Location: ./");
  exit;
}

// Make sure we have adjusted grades for the players
$statement = "select * from player where player_adj_score is not NULL";
if (!mysql_num_rows(mysql_query($statement))) {
  $statement = "select * from player where player_score is not NULL";
  if (!mysql_num_rows(mysql_query($statement))) {
    $_SESSION['message'] = "No adjusted scores have been uploaded for this draft, mock draft is not available.";
    header("Location: ./");
    exit;
  } else {
      //use the non-adjusted scores
      $statement = "update player set player_adj_score=player_score where player_score is not NULL";
      mysql_query($statement);
  }
}

// First see if we are uploading the file
if (file_exists($_FILES['player_to_team']['tmp_name']) && file_exists($_FILES['player_ratings']['tmp_name'])) {
  // Store the file contents in the session, we'll need it later
  $_SESSION['mock_upload'] = preg_split("/[\n\r]+/", file_get_contents($_FILES['player_to_team']['tmp_name']));
  $_SESSION['mock_ratings'] = preg_split("/[\n\r]+/", file_get_contents($_FILES['player_ratings']['tmp_name']));
} else {
  $_SESSION['message'] = "There was an error uploading the player files.";
  header("Location: import_mock_draft.php");
  return;
}

// Running the mock draft.

// Now we can run through all the lines in the upload
$lines = $_SESSION['mock_upload'];
$header = true;
$data = array();
foreach($lines as $line) {
  if ($header) {
    // Make sure we have the current version of Extractor coming in.
    if ($line != $valid_fof7_player_record) {
        echo $line;
      $_SESSION['message'] = "The file you imported does not appear to be a player record file.
Please verify that you are uploading the correct file.";
      header("Location: import_mock_draft.php");
    }
  }
  if ($line && !$header) {
    preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $line, $matches);
    $columns = $matches[0];
    foreach($columns as $key=>$value) {
      // Remove the field qualifiers, if any
      $columns[$key] = preg_replace("/^\"|\"$|\"?,$/", "", $value);
    }
    // Build the roster table
    if ( $columns[kpr_team]!=99 ){
        $position_id = $positions[trim($columns[kpr_position])];
        $statement = "select team_id from team where in_game_id=".$columns[kpr_team];
        $result = mysql_fetch_array(mysql_query($statement));
        
        $statement = "insert into roster (player_id,in_game_team_id,team_id,position_id) values (".$columns[kpr_player_id].",".$columns[kpr_team].",".$result["team_id"].",".$position_id.")";
        mysql_query($statement);
    }
    //still missing the ratings which are in the other file.
  }
  if ( $header )
    $header = false;

}

// Now we can run through all the lines in the ratings file to complete the roster table
$lines = $_SESSION['mock_ratings'];
$header = true;
$data = array();
foreach($lines as $line) {
  if ($header) {
    // Make sure we have the current version of Extractor coming in.
    if ($line != $valid_fof7_player_ratings) {
      $_SESSION['message'] = "The file you imported does not appear to be a player ratings file.
Please verify that you are uploading the correct file.";
      header("Location: import_mock_draft.php");
      exit;
    }
  }
  if ($line && !$header) {
    preg_match_all('/("(?:[^"]|"")*"|[^",\r\n]*)(,|\r\n?|\n)?/', $line, $matches);
    $columns = $matches[0];
    foreach($columns as $key=>$value) {
      // Remove the field qualifiers, if any
      $columns[$key] = preg_replace("/^\"|\"$|\"?,$/", "", $value);
    }
    // Build the roster table
    $statement = "update roster set current=".$columns[kprating_current].",future=".$columns[kprating_future]." where player_id=".$columns[kprating_player_id];
    mysql_query($statement);
    $statement = "select * from roster where player_id=".$columns[kprating_player_id];
    if ( $result = mysql_fetch_array(mysql_query($statement)) ){
        //now that the ratings are there, and while we are in a player loop already...
        // Store the best player at each position for each team.
        $position_id = $result["position_id"];
        $future = $result["future"];
        $team = $result["team_id"];
        if ($data[$team][$position_id] < $future) {
            $data[$team][$position_id] = $future;
        }
    }
  }
  if ( $header )
    $header = false;
}

// Ok, $data contains all the data we need to fill the team_need table.
// Truncate the team_need table
$statement = "truncate table team_need";
mysql_query($statement);
// First let's build an array of all the position_id's
$statement = "select * from position";
$result = mysql_query($statement);
$position_list = array();
while ($row = mysql_fetch_array($result)) {
  $position_list[] = $row['position_id'];
}
foreach($data as $team_id=>$team_data) {
  foreach($position_list as $position_id) {
    // The need_order is 100-the best player
    $need = 100-$team_data[$position_id];
    $statement = "insert into team_need (team_id, position_id, team_need_order)
values ('$team_id', '$position_id', '$need')";
    mysql_query($statement);
  }
}

// Now we go through all the picks and do the mock draft
$statement = "truncate table mock_draft";
mysql_query($statement);
$statement = "select * from pick where team_id > 0 order by pick_id";
$result = mysql_query($statement);
while($row = mysql_fetch_array($result)) {
  $team = new team($row['team_id']);
  $team->mock_pick($row['pick_id']);
}

// Lastly mark the any picks that have already run
fill_team_need();

header("Location: mock_draft.php");
?>
