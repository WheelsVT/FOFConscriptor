<?
/***************************************************************************
 *                                priority_set.php
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
$count = 1;
if(is_array($_POST['player_id'])) {
  foreach($_POST['player_id'] as $player_id) {
    if ($_POST['value']) {
      $value = $count;
    } else if ($_POST['newvalue']){
	 foreach($_POST['newvalue'] as $k){
		$value = $k;
	 }
    } else{
      $value = 0;
    }
    $statement = "update selection set selection_priority = '$value'
where team_id = '".$login->team_id()."' and player_id = '$player_id'";
    mysql_query($statement);
    $count++;
  }
 }

// Delete any?
if (is_array($_POST['delete'])) {
  foreach($_POST['delete'] as $player_id) {
    $statement = "delete from selection where team_id = '".$login->team_id()."' and player_id = '$player_id'";
    mysql_query($statement);
  }
}

// Process the queue
process_pick_queue();

header("Location: priority.php");
?>