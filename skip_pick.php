<?
/***************************************************************************
 *                                skip_pick.php
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
  $statement = "update pick set player_id = '".kSkipPick."' where pick_id = '".$_GET['pick_id']."'
and player_id is NULL";
  mysql_query($statement);
  // Update the draft clock
  reset_current_pick_clock();
 }

process_pick_queue();

header("Location: selections.php");
?>