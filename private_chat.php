<?
/***************************************************************************
 *                                private_chat.php
 *                            -----------------------
 *   begin                : Thursday, May 15, 2008
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
$chat_id = md5(uniqid(rand()));
$html = file_get_contents("includes/html/private_chat.html");
$html = str_replace("%league%", $settings->get_value(kSettingLeagueName), $html);
$html = str_replace("%version%", kVersion, $html);
$html = str_replace("%chat_id%", $chat_id, $html);
if ($_GET['chat_room_id']) {
  $value = $login->latest_message()+1;
  $statement = "update last_update set latest_message='".$value."'";
  mysql_query($statement);
  $html = str_replace("%chat_room_id%", $_GET['chat_room_id'], $html);
  $statement = "update chat_room set team_2_arrived = '1' where team_2_id = '".$login->team_id()."' and
chat_room_id = '".$_GET['chat_room_id']."'";
  mysql_query($statement);
  $chat_room_id = $_GET['chat_room_id'];
  $statement = "select team.* from team, chat_room where team.team_id = chat_room.team_1_id and
chat_room_id = '$chat_room_id'";
  $row = mysql_fetch_array(mysql_query($statement));
  $html = str_replace("%team_name%", $row['team_name'], $html);
 } else {
  $value = $login->latest_message()+1;
  $statement = "update last_update set latest_message='".$value."'";
  mysql_query($statement);
  $statement = "select * from chat_room where team_1_id = '".$login->team_id()."' and
team_2_id = '".$_GET['team_id']."' and
team_2_arrived is NULL";
  $row = mysql_fetch_array(mysql_query($statement));
  if ($row['chat_room_id']) {
    $chat_room_id = $row['chat_room_id'];
  } else {
    $statement = "insert into chat_room (team_1_id, team_2_id, team_1_arrived) values 
('".$login->team_id()."', '".$_GET['team_id']."', '1')";
    mysql_query($statement);
    $chat_room_id = mysql_insert_id();
  }
  $statement = "select * from team where team_id = '".$_GET['team_id']."'";
  $row = mysql_fetch_array(mysql_query($statement));
  $html = str_replace("%team_name%", $row['team_name'], $html);
 }
$html = str_replace("%chat_room_id%", $chat_room_id, $html);
echo $html;
?>