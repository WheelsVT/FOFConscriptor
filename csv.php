<?
/***************************************************************************
 *                                analizer.php
 *                            -------------------
 *   begin                : Saturday, May 24, 2008
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

global $settings;
$staff = false;
if ( $settings->get_value(kSettingStaffDraftOn)==1 )
   $staff = true;

if ( !$staff ){
   $statement = "select * from pick, team, player where
   pick.team_id = team.team_id and player.player_id = pick.player_id order by pick_id";
   $result = mysql_query($statement);
   echo mysql_error();
   $line = array();
   while ($row = mysql_fetch_array($result)) {
     list($first, $last) = explode(" ",$row['player_name']);
     $line[] = ceil(($row['pick_id'])/32).','.($row['pick_id']-((ceil(($row['pick_id'])/32)-1)*32)).','.$row['in_game_id'].','.$row['player_in_game_id'];
   }
} else {
   $statement = "select * from pick, team, staff where
   pick.team_id = team.team_id and staff.staff_id = pick.player_id order by pick_id";
   $result = mysql_query($statement);
   echo mysql_error();
   $line = array();
   while ($row = mysql_fetch_array($result)) {
     list($first, $last) = explode(" ",$row['staff_name']);
     $line[] = ceil(($row['pick_id'])/32).','.$row['in_game_id'].','.$row['staff_in_game_id'];
   }
}
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=importdraft.csv");
header("Pragma: no-cache");
header("Expires: 0");

if (count($line)) {
  echo implode("\n",$line);
  echo "\n";
} else {
  echo "No drafted players";
}
?>