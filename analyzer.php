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
    /*$statement = "select * from pick, player, position where
    pick.player_id = player.player_id and
    position.position_id = player.position_id
    order by pick_id";
    $result = mysql_query($statement);
    echo mysql_error();
    $line = array();
    while ($row = mysql_fetch_array($result)) {
      list($first, $last) = explode(" ",$row['player_name']);
      $line[] = $row['pick_id'].'. - '.$last.', '.$first.', '.$row['position_name'].', '.$row['player_school'];
    }*/

    $statement = "SELECT DISTINCT p.pick_id, s.position_name, c.player_name, c.player_school, t.team_name, p.pick_time from pick p LEFT JOIN player c on c.player_id = p.player_id LEFT JOIN position s on s.position_id = c.position_id LEFT JOIN team t on t.team_id = p.team_id where p.player_id>0 order by p.pick_id asc";

    $result = mysql_query($statement) or die("Bad query: ".mysql_error());

    while ($row = mysql_fetch_array($result)) {
      $player_name = $row['player_name'];
      $last = substr($player_name, strpos($player_name, ' '));
      $first = substr($player_name, 0, strpos($player_name, ' '));
      $line[] = 'Pick #' . $row['pick_id'] . ' - ' . $row['team_name'] . ' - ' . $last.', '.$first.', '.$row['position_name'].', '.$row['player_school'];

    }

} else {
   $statement = "select * from pick, team, staff, staff_roles where
   pick.team_id = team.team_id and staff.staff_id = pick.player_id and staff.staff_role_id=staff_roles.staff_role_id order by pick_id";
   $result = mysql_query($statement);
   echo mysql_error();
   $line = array();
   while ($row = mysql_fetch_array($result)) {
     list($first, $last) = explode(" ",$row['staff_name']);
     $line[] = $row['pick_id'].'. - '.$last.', '.$first.', '.$row['staff_role_name'];
   }
}
if (count($line)) {
  echo implode("\n<br>",$line);
  echo "\n";
} else {
  echo "No drafted players";
}
?>