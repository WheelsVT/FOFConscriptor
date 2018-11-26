<?
/***************************************************************************
 *                                clear_inactives.php
 *                            ----------------------------
 *   begin                : Saturday, June 11, 2011
 *   copyright            : (C) 2011 J. David Baker
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
$statement = "delete from selection where team_id = '".$login->team_id()."'
and selection_priority = '0'";
mysql_query($statement);
header("Location: ".$_SERVER['HTTP_REFERER']);
?>