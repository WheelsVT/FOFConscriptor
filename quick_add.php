<?
/***************************************************************************
 *                                quick_add.php
 *                            -------------------
 *   begin                : Thursday, November 5, 2009
 *   copyright            : (C) 2009 J. David Baker
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
if ($_POST['quick_add']) {
  $statement = "select player.player_id from player
left join pick on pick.player_id = player.player_id
where pick.pick_id is NULL and
player.player_name like '".mysql_real_escape_string($_POST['quick_add'])."%'";
  $result = mysql_query($statement);
  while ($row = mysql_fetch_array($result)) {
    $statement = "insert into selection set player_id = '".$row['player_id']."', team_id = '".$_SESSION["selected_team_id"]."',
selection_priority = ".time();
    mysql_query($statement);
  }
}
header("Location: ".$_SESSION["origURL"]);
?>