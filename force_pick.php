<?
/***************************************************************************
 *                                force_pick.php
 *                            -------------------
 *   begin                : Saturday, Apr 26, 2008
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

global $login;
global $team;
global $settings;

if ($login->is_admin()) {
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
  
  // This function sets the selected pick's player_id to kSkipPick to allow their pick to be skipped
  $player_id = NULL;
  $player_id = $team->force_pick();

    if ($player_id && $player_id!=kDeclinePick) {
      make_pick($_GET['pick_id'], $player_id, false);
    } else if ($settings->get_value(kSettingStaffDraftOn)==1) {
      $statement = "update pick set player_id = '".kDelinePick."' where pick_id = '".$_GET['pick_id']."'";
      mysql_query($statement);
      $player_id = true;
    } else {
      $statement = "update pick set player_id = '".kSkipPick."' where pick_id = '".$_GET['pick_id']."'";
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
    // Update the draft clock
    reset_current_pick_clock();
 }

process_pick_queue();

header("Location: selections.php");
?>