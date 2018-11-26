<?
/***************************************************************************
 *                                import_draft_run.php
 *                            -------------------
 *   begin                : Tuesday, January 25, 2011
 *   copyright            : (C) 2011 J. David Baker
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
// This function imports the draft order file from Draft Utility

// Check to make sure we have a file
if (!file_exists($_FILES['draft_order']['tmp_name'])) {
  $_SESSION['message'] = "There was an error uploading the file.";
  header("Location: ./import_draft_order.php");
 }
$players_file = $_FILES['draft_order']['tmp_name'];


// Ok, all is well, first let's clear out the existing priority list
$statement = "delete from selection where team_id = '".$login->team_id()."'";
mysql_query($statement);

// Now for the import
define (kName,0);
define (kPosition, 1);
define (kBorn, 2);

$file = file_get_contents($players_file);
$lines = preg_split("/[\n\r]+/", $file);
$upload_count = 0;

// Preload the position mapping
$positions = array();
$statement = "select * from position_to_alias";
$result = mysql_query($statement);
while ($row = mysql_fetch_array($result)) {
  $positions[$row['alias_name']] = $row['position_id'];
 }

foreach($lines as $line) {
  if ($line) {
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

    // Look up the player
    $statement = "select player.player_id from player
left join pick using (player_id)
where player_name = '".mysql_real_escape_string($columns[kName])."' and
position_id = '".$positions[$columns[kPosition]]."' and
pick.player_id is NULL";
    $row = mysql_fetch_array(mysql_query($statement));
    $col[] = "player_id = {$row['player_id']}";
    $player_id = $row['player_id'];
    if ($row['player_id']) {
      $upload_count++;

      $col[] = "team_id = {$login->team_id()}";
      $col[] = "selection_priority = {$upload_count}";
      
      $statement = "insert into selection set ".implode(",",$col);
      mysql_query($statement);
    }
  }
}

if ($upload_count) {
  // Make sure we are set on priority queue
  $statement = "update team set pick_method_id = 1 where team_id = '{$login->team_id()}'";
  mysql_query($statement);
  // Give the user the 'success' message.
  $_SESSION['message'] = $upload_count." Players have been set into your selection queue.";
  header("Location: priority.php");
 } else {
  $_SESSION['message'] = "No players were found in the uploaded file.";
  header("Location: import_draft_order.php");
 }
?>