<?
/***************************************************************************
 *                                import_draft_bars_run.php
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
  if (!file_exists($_FILES['team_players']['tmp_name'])) {
    $_SESSION['message'] = "There was an error uploading the file.";
    header("Location: ./import_draft.php");
  }
  $players_file = $_FILES['team_players']['tmp_name'];

// Ok, all is well, first let's clear out the existing draft data
  $statement = "delete from team_player where team_id = '".$login->team_id()."'";
  mysql_query($statement);
  $statement = "delete from team_player_to_attribute where team_id = '".$login->team_id()."'";
  mysql_query($statement);

// Now for the import
include "includes/fof7_export_columns.inc.php";

$file = file_get_contents($players_file);
$lines = preg_split("/[\n\r]+/", $file);
$header = true;
$upload_count = 0;
foreach($lines as $line) {
  if ($header) {
    // Make sure we have the current version of Extractor coming in.
    if ($line != $valid_fof7_rookie_ratings) {
      $_SESSION['message'] = "The file you imported does not appeart to be a draft_personal ratings file, or is the wrong version.
Please verify that you are uploading the correct file and that you have the current version of FOF.";
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
   $col["player_in_game_id"] = $columns[kRecordPlayer_ID];

    //determine the position this player plays and the player_id
    $statement = "select player_id,player_in_game_id,position_id from player where player_in_game_id=".$col["player_in_game_id"] ;
    $result = mysql_fetch_array(mysql_query($statement));
    if ( $result['player_id'] )
		$upload_count++;
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
	if ( $row["fof7_rookie_attribute_column"]==-1 ){//check if this is the formations field
             $statement = "insert into team_player_to_attribute (team_id, player_id, attribute_id, player_to_attribute_low, player_to_attribute_high) values('".$login->team_id()."', ".$result["player_id"].", ".$row["attribute_id"].", '0', '0');";
        } else
             $statement = "insert into team_player_to_attribute (team_id, player_id, attribute_id, player_to_attribute_low, player_to_attribute_high) values('".$login->team_id()."', ".$result["player_id"].", ".$row["attribute_id"].", '".trim($columns[$row["fof7_attribute_column"]])."', '".trim($columns[$row["fof7_attribute_column"]+58])."');";
        mysql_query($statement);
    }
  } else {
    $header = false;
  }
}

  if ($upload_count) {
    $_SESSION['message'] = $upload_count." Player Profiles have been uploaded.";
    header("Location: players.php");
  } else {
    $_SESSION['message'] = "The file you uploaded appears to be an Extractor file, but I didn't find
any matches to the players in the system.  Are you sure you uploaded the correct file?";
    header("Location: import_draft.php");
  }

?>