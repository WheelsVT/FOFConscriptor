<?
/***************************************************************************
 *                                bpa_set.php
 *                            -------------------
 *   begin                : Monday, Apr 7, 2008
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

if(is_array($_POST['bpa_priority'])) {
  foreach($_POST['bpa_priority'] as $bpa_id=>$bpa_priority) {
    $statement = "update bpa set bpa_priority = '$bpa_priority'
where team_id = '".$_SESSION["selected_team_id"]."' and bpa_id = '$bpa_id'";
    mysql_query($statement);
  }
 }

// Delete any?
if (is_array($_POST['delete'])) {
  foreach($_POST['delete'] as $bpa_id) {
    $statement = "delete from bpa where team_id = '".$_SESSION["selected_team_id"]."' and bpa_id = '$bpa_id'";
    mysql_query($statement);
  }
}

// Process the queue
process_pick_queue();

$_SESSION['message'] = "Priority set successfully.";

header("Location: ".$_SESSION["origURL"]);
?>