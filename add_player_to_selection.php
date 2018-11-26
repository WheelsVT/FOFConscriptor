<?
/***************************************************************************
 *                                add_player_to_selection.php
 *                            ---------------------------------
 *   begin                : Friday, May 16, 2008
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
$player_id = $_GET['player_id'];
$statement = "select * from pick where player_id = '$player_id'";
if (!mysql_num_rows(mysql_query($statement))) {
  $statement = "insert into selection (player_id, team_id, selection_priority)
values ('$player_id', '".$login->team_id()."', '".time()."')";
  mysql_query($statement);
 }
header("Location: ".$_SERVER['HTTP_REFERER']);
?>