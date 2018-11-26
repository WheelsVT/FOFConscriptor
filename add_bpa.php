<?
/***************************************************************************
 *                                add_pba.php
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

if ($_POST['position_id']) {
  $statement = "select max(bpa_priority) from bpa where team_id = '".$_SESSION["selected_team_id"]."'";
  $row = mysql_fetch_array(mysql_query($statement));
  $priority = $row['max(bpa_priority)'] + 1;
  $statement = "insert into bpa (team_id, position_id, bpa_priority, attribute_id, bpa_max_experience)
values
('".$_SESSION["selected_team_id"]."', '".$_POST['position_id']."', '$priority', '".$_POST['attribute_id']."', '".$_POST['bpa_max_experience']."')";
  mysql_query($statement);
  echo mysql_error();
 }

process_pick_queue();

$_SESSION['message'] = "Priority set successfully.";

header("Location: ".$_SESSION["origURL"]);
?>