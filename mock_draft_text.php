<?
/***************************************************************************
 *                                mock_draft_text.php
 *                            --------------------------
 *   begin                : Thursday, Aug 25, 2008
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
$statement = "select * from mock_draft, team, player, position where
team.team_id = mock_draft.team_id and
player.player_id = mock_draft.player_id and
position.position_id = player.position_id
order by pick_id
limit 32";
$result = mysql_query($statement);
while ($row = mysql_fetch_array($result)) {
  echo "<P>".calculate_pick($row['pick_id'])." - ".$row['team_name']." - [b]".$row['position_name']." ".$row['player_name'].
    "[/b] - ".$row['mock_draft_commentary']."</p>";
 }
?>