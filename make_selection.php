<?
/***************************************************************************
 *                                make_selection.php
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

// Makes the selection for the current team
// Only if admin!
if ($login->is_admin()) {
  $player_id = $_POST['selection'];
  $pick_id = $_POST['pick_id'];
  make_pick($pick_id, $player_id);
 }

header("Location: players.php");
?>