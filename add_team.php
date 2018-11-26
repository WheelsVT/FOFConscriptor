<?
/***************************************************************************
 *                                add_team.php
 *                            -------------------
 *   begin                : Wednesday, May 14, 2008
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
if ($login->is_admin() && $_POST['team_name']) {
  $statement = "select * from team where team_name like '".$_POST['team_name']."'";
  if (!mysql_num_rows(mysql_query($statement))) {
    $statement = "insert into team (team_name) values ('".strtoupper($_POST['team_name'])."')";
    mysql_query($statement);
  } else {
    $_SESSION['message'] = "That team name already exists.";
  }
 }

header("Location: users.php");
?>