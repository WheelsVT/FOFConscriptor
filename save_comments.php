<?
/***************************************************************************
 *                                save_comments.php
 *                            -------------------
 *   begin                : Tuesday, Apr 29, 2008
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

$player_id = $_POST['player_id'];
$team_id = $login->team_id();
if (!get_magic_quotes_gpc()) {
  $comments = addslashes($_POST['player_comments_text']);
 } else {
  $comments = $_POST['player_comments_text'];
 }
// Do we have a comment already?
$statement = "select * from player_comments where player_id = '$player_id' and team_id = '$team_id'";
if (!mysql_num_rows(mysql_query($statement))) {
  $statement = "insert into player_comments (player_id, team_id, player_comments_text)
values
('$player_id', '$team_id', '$comments')";
  mysql_query($statement);
 } else {
  $statement = "update player_comments set player_comments_text = '$comments' where
player_id = '$player_id' and team_id = '$team_id'";
  mysql_query($statement);
 }

echo stripslashes($comments);
?>