<?
/***************************************************************************
 *                                scout_weights_run.php
 *                            ----------------------------
 *   begin                : Friday, Aug 29, 2008
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

// Process the scout weight adjustment
if ($login->is_admin()) {
  if (is_array($_POST['position_scout_weight'])) {
    foreach($_POST['position_scout_weight'] as $key=>$value) {
      $statement = "update position set position_scout_weight = '$value' where position_id = '$key'";
      mysql_query($statement);
    }
  }
  $_SESSION['message'] = "Scout weights updated.";
}
header("Location: scout_weights.php");
?>