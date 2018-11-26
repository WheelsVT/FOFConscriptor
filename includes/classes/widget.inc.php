<?
/***************************************************************************
 *                                widget.inc.php
 *                            ----------------------
 *   begin                : Monday, May 26, 2008
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

class widget {
  function draw() {
    global $settings;
    // Draws the widget
    $html .= '
<div class="widget">';
    $html .= '
<h1>'.$settings->get_value(kSettingLeagueName).' Draft Status ('.date("l, F d, Y, g:i a T").')</h1>';
    // Top menu
    $html .= '
  <div class="widget_top">
    <div class="widget_top_element">
      <a href="index.php" target="_blank">Draft Utility</a>
    </div>
    <div class="widget_top_element">
      <a href="javascript:popup(\'analyzer.php\', \'analyzer\', 400, 300)">Analyzer Dump</a>
    </div>
  </div>';

    // Widget elements
    // Last 5 selections
    $html .= '
  <div class="widget_item">
    <h1>Last Five Selections</h1>';
    $statement = "select * from pick, team, position, player where
pick.player_id is not NULL and
pick.team_id = team.team_id and
pick.player_id = player.player_id and
position.position_id = player.position_id
order by pick_id desc limit 5";
    $result = mysql_query($statement);
    $pick = array();
    while ($row = mysql_fetch_array($result)) {
      $pick[] = calculate_pick($row['pick_id']).'. ('.$row['team_name'].') - '.$row['player_name'].', '.$row['position_name'].', '.
	$row['player_school'];
    }
    $html .= implode("<br>",$pick);
    $html .= '
  </div>';

    // Who is on the clock
    $statement = "select * from pick,team where pick.team_id = team.team_id and
pick.player_id is NULL order by pick_id limit 1";
    $row = mysql_fetch_array(mysql_query($statement));
    if ($row['pick_id']) {
      $html .= '
  <div class="widget_item">
    <h1>On the Clock</h1>
    <h2>'.calculate_pick($row['pick_id']).'. '.$row['team_name'].'</h2>
  </div>';
    }

    // Next 5 picks
    $html .= '
  <div class="widget_item">
    <h1>Next Five Selections</h1>';
    $statement = "select * from pick,team where
pick.player_id is NULL and
pick.team_id = team.team_id
order by pick.pick_id limit 5";
    $result = mysql_query($statement);
    $pick = array();
    while($row = mysql_fetch_array($result)) {
      $pick[] = calculate_pick($row['pick_id']).' - '.$row['team_name'];
    }
    $html .= implode("<br>",$pick);

    $html .= '
</div>';
    return $html;
  }
}
?>