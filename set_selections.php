<?
/***************************************************************************
 *                                set_selections.php
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
$team_id = $login->team_id();

// Initialize $count to time() to force new players to be added to the end of the queue
$count = time();

// Save the selections
foreach($_POST['player_id_selection'] as $player_id) {
  if ($_POST['select'][$player_id]) {
    $count++;
    // Check to make sure this player is not picked
    $statement = "select * from pick where player_id = '$player_id'";
    if (!mysql_num_rows(mysql_query($statement))) {
      $statement = "insert into selection set team_id = '$team_id', player_id = '$player_id',
selection_priority = '$count'";
      mysql_query($statement);
    }
  } else {
    $statement = "delete from selection where team_id = '$team_id' and player_id = '$player_id'";
    mysql_query($statement);
  }
}
$_SESSION['message'] = "Selections saved.";
if ( $settings->get_value(kSettingStaffDraftOn)!=1 )
    header("Location: players.php?position_id=".$_POST['position_id']."&show_attributes=".$_POST['show_attributes'].
       "&filter_overrated=".$_POST['filter_overrated']);
else
    header("Location: staff.php?staff_id=".$_POST['position_id']."&show_attributes=".$_POST['show_attributes'].
       "&filter_overrated=".$_POST['filter_overrated']);
?>