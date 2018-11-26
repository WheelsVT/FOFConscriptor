<?
/***************************************************************************
 *                                unskip_pick.php
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

if ($login->is_admin()) {
  // This function sets the selected pick's player_id to kSkipPick to allow their pick to be skipped
  $statement = "select * from pick where pick_id = '".$_GET['pick_id']."'";
  $row = mysql_fetch_array(mysql_query($statement));
  $team = new team($row['team_id']);
  $statement = "update pick set player_id = NULL,
pick_time = '".$team->new_pick_time(time())."'
where pick_id = '".$_GET['pick_id']."'
and player_id = '".kSkipPick."'";
  mysql_query($statement);
  // We might have a stopped draft, if so let's mark any high NULLs as stopped draft
  $statement = "select * from pick where player_id = '".kDraftHalt."' order by pick_id limit 1";
  $row = mysql_fetch_array(mysql_query($statement));
  if ($row['pick_id']) {
    $statement = "update pick set player_id = '".kDraftHalt."' where
player_id is NULL and pick_id > '".$row['pick_id']."'";
    mysql_query($statement);
  }
 }

process_pick_queue();

header("Location: selections.php");
?>