<?
/***************************************************************************
 *                                pick.inc.php
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

define (kDraftHalt, '-100'); // Value to populate the player_id of picks to halt the draft
define (kSkipPick, '-200'); // Value to set a pick's player_id for it to be skipped
define (kDeclinePick, '-300');

class pick {
  function pick($pick_id) {
    $statement = "select * from pick where pick_id = '".mysql_real_escape_string($pick_id)."'";
    $this->data = mysql_fetch_array(mysql_query($statement));
  }

  function draw_edit() {
    global $login;
    if (!$this->data['pick_id']) {
      header("Location: selections.php");
      exit;
    }
    // Only admin or pick owner can do this
    if (!$login->is_admin() && $login->team_id() != $this->data['team_id']) {
      header("Location: selections.php");
      exit;
    }
    $round = floor(($this->data['pick_id']-1)/32)+1;
    $pick = (($this->data['pick_id']-1)%32)+1;
    $html .= '
<h3>Edit Pick</h3>
<p>In the event of a trade, use this page to change the team that is making this pick.</p>
<h2>Round '.$round.' Pick '.$pick.'</h2>
<form method="post" action="edit_pick_run.php">
  <input type="hidden" name="pick_id" value="'.$this->data['pick_id'].'">
  <p><select name="team_id">';
    $team = new team($this->data['team_id']);
    $html .= $team->option_list();
    $html .= '
  </select>
  <p><input type="submit" value="Save">
</form>';
    return $html;
  }

  function process_edit() {
    global $login;
    
    // Only admin or pick owner can do this
    if (!$login->is_admin() && $login->team_id() != $this->data['team_id']) {
      header("Location: selections.php");
      exit;
    }
    
    $old_team = new team($this->data['team_id']);
    $old_team_name = $old_team->team_name();
    $new_team = new team($_POST['team_id']);
    $new_team_name = $new_team->team_name();
    
    error_log("Pick ".$this->data['pick_id']." switched from ".$old_team_name." to ".$new_team_name." by ".
	      $login->team_name()." IP address ".$_SERVER['REMOTE_ADDR']);
    
    $col[] = "team_id = '".mysql_real_escape_string($_POST['team_id'])."'";
    
    // If the old team is xxx, set the player to NULL
    if ($old_team->is_xxx()) {
      $col[] = "player_id = NULL";
    }
    // If the new team is xxx, set the player to -1
    if ($new_team->is_xxx()) {
      $col[] = "player_id = '-1'";
    }
    $statement = "update pick set ".implode(",",$col)." where pick_id = '".mysql_real_escape_string($this->data['pick_id'])."'";
    mysql_query($statement);
    process_pick_queue();
    $_SESSION['message'] = "Pick updated.";
    header("Location: selections.php");
    exit;
  }
}
?>