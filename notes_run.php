<?
/***************************************************************************
 *                                notes.php
 *                            -------------------
 *   begin                : Friday, May 23, 2008
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
$statement = "update team set team_comments = '".addslashes($_POST['team_comments'])."' where
team_id = '".$login->team_id()."'";
mysql_query($statement);
$_SESSION['message'] = "Saved";
header("Location: notes.php");
exit;
?>