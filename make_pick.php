<?
/***************************************************************************
 *                                make_pick.php
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

// If it's our turn to pick, make the pick
if ($login->can_pick()) {
  $statement = "select pick_id, team.team_id, team_name from pick, team where pick.team_id = '".$login->team_id()."'
and (pick.player_id is NULL or pick.player_id = '".kSkipPick."' )
order by pick_id
limit 1";
  $row = mysql_fetch_array(mysql_query($statement));
  
  if ($row['pick_id'] && $_GET['player_id']) {
    make_pick($row['pick_id'], $_GET['player_id']);
  }
  if ($row['pick_id'] && $_GET['staff_id']) {
    $statement = "select * from staff where staff.staff_id = '".$_GET['staff_id']."'";
    $row2 = mysql_fetch_array(mysql_query($statement));
    if ( $row2['staff_amenable']=='Y' )
      make_pick($row['pick_id'], $_GET['staff_id']);
  }
 }
if ($login->is_admin()) {
  $statement = "select pick_id, team.team_id, team_name from pick, team where pick.team_id = '".$_SESSION['on_clock_team_id']."'
and (pick.player_id is NULL or pick.player_id = '".kSkipPick."' )
order by pick_id
limit 1";
  $row = mysql_fetch_array(mysql_query($statement));
  
  if ($row['pick_id'] && $_GET['player_id']) {
    make_pick($row['pick_id'], $_GET['player_id']);
  }
  if ($row['pick_id'] && $_GET['staff_id']) {
    $statement = "select * from staff where staff.staff_id = '".$_GET['staff_id']."'";
    $row2 = mysql_fetch_array(mysql_query($statement));
    if ( $row2['staff_amenable']=='Y' )
      make_pick($row['pick_id'], $_GET['staff_id']);
  }
 }

process_pick_queue();
header("Location: ".$_SERVER['HTTP_REFERER']);
exit;
?>