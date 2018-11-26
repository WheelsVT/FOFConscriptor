<?
/***************************************************************************
 *                                team.inc.php
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

class team {
  function team($team_id) {
    $team_id = mysql_real_escape_string($team_id);
    $statement = "select * from team where team_id = '$team_id'";
    $this->data = mysql_fetch_array(mysql_query($statement));
  }

  function team_name() {
    return $this->data['team_name'];
  }

  function team_owner() {
	return $this->data['team_owner'];
  }
  
  function team_user_link() {
	return $this->data['team_user_link'];
  }
  
  function team_is_draft_admin() {
     return $this->data['draft_admin'];
  }

  function pick_method() {
    return $this->data['pick_method_id'];
  }

  function option_list() {
    $statement = "select * from team where team_id != '".kAdminUser."' order by team_name";
    $result = mysql_query($statement);
    while ($row = mysql_fetch_array($result)) {
      if ($row['team_id'] == $this->data['team_id']) {
	$selected = ' selected';
      } else {
	$selected = '';
      }
      $html .= '
<option value="'.$row['team_id'].'"'.$selected.'>'.$row['team_name'].'</option>';
    }
    return $html;
  }

  function is_xxx() {
    if ($this->data['team_name'] == 'xxx') {
      return true;
    } else {
      return false;
    }
  }

  function set_has_password() {
    // If we don't have a password, make a random one
    if (!$this->data['team_password']) {
      $password = md5(uniqid(rand()));
      $statement = "update team set team_password = '$password', team_clock_adj = '0'
where team_id = '".$this->data['team_id']."'";
      mysql_query($statement);
    }
  }

  function clear_password() {
    if ($this->data['team_password']) {
      $statement = "update team set team_password = NULL, team_email = NULL, team_clock_adj = '0', pick_method_id = '1'
 where team_id = '".$this->data['team_id']."'";
      mysql_query($statement);
    }
  }

  function set_autopick($is_on) {
    if ($is_on) {
      $value = '1';
    } else {
      $value = '0';
    }
    if ($value != $this->data['team_autopick']) {
      $statement = "update team set team_autopick = '$value' where team_id = '".$this->data['team_id']."'";
      mysql_query($statement);
    }
  }

  function set_autopick_method($value) {
     if ($value != $this->data['pick_method_id']) {
      $statement = "update team set pick_method_id = '$value' where team_id = '".$this->data['team_id']."'";
      mysql_query($statement);
    }
  }
  function set_draft_admin($is_on) {
    if ($is_on) {
      $value = '1';
    } else {
      $value = '0';
    }
    if ($value != $this->data['draft_admin']) {
      $statement = "update team set draft_admin = '$value' where team_id = '".$this->data['team_id']."'";
      mysql_query($statement);
    }
  }

  function set_clock_adj($adj) {
    $adj = $adj/100;
    if ($adj != $this->data['team_clock_adj']) {
      $statement = "update team set team_clock_adj = '$adj' where team_id = '".$this->data['team_id']."'";
      mysql_query($statement);
    }
  }

  function set_team_email($adj) {
    if ($adj != $this->data['team_email']) {
      $statement = "update team set team_email = '$adj' where team_id = '".$this->data['team_id']."'";
      mysql_query($statement);
    }
  }

  function set_team_owner($adj) {
    if ($adj != $this->data['team_owner']) {
      $statement = "update team set team_owner = '$adj' where team_id = '".$this->data['team_id']."'";
      mysql_query($statement);
    }
  }

  function set_team_user_link($adj) {
    if ($adj != $this->data['team_user_link']) {
        if ( strlen($adj)<=3 )
            $statement = "update team set team_user_link = NULL where team_id = '".$this->data['team_id']."'";
        else
            $statement = "update team set team_user_link = '$adj' where team_id = '".$this->data['team_id']."'";
      mysql_query($statement);
    }
  }
  function set_multipos($is_on) {
    // if $is_on is true, we will allow this team to take selections of the same position
    // If $is_on is false, when a position is selected, that position is zeroed in their priority list.
    if ($is_on) {
      $value = '1';
    } else {
      $value = '0';
    }
    $statement = "update team set team_multipos = '$value' where team_id = '".$this->data['team_id']."'";
    mysql_query($statement);
  }

  function next_player($time = 0) {
    global $settings;
    // If we have a clock and autopick while clock off is disabled then figure out if clock is off
/*    if ( $settings->get_value(kSettingEndTime) && $settings->get_value(kSettingAutoPickWhenClockOff)==0 ){
        //compute the end time
        $end_time = strtotime(date("Y-m-d ", $time).date("H:i:s", $settings->get_value(kSettingEndTime)));
        if (time() > $end_time) {
            // past the end time, return false - no pick
            return false;
        } elseif (time() < strtotime(date("Y-m-d ",$time).date("H:i:s", $settings->get_value(kSettingStartTime)))){
            // Before today's start time, return false - no pick
            return false;
        }
    }*/
    // If we have a player to select, return the player_id
    if ($this->data['team_autopick'] && (!$this->data['team_autopick_wait'] || 
	time() >= strtotime($time." +".$this->data['team_autopick_wait'].' minutes'))) {
      // Only if we have autopick turned on and we've waited the appropriate amount of time
      if ($this->data['pick_method_id'] == kPlayerQueue) {
	return $this->pick_priority();
      } elseif ($this->data['pick_method_id'] == kBPAQueue) {
	return $this->pick_bpa();
      } elseif ($this->data['pick_method_id'] == kPlayerThenBPA) {
	$player_id = $this->pick_priority();
	if (!$player_id) {
	  $player_id = $this->pick_bpa();
	}
	return $player_id;
      } else {
	return $this->scout_pick();
      }
    } else {
      return false;
    }
  }

  function pick_priority() {
    global $settings;
    if ( $settings->get_value(kSettingStaffDraftOn)!=1 )
        $statement = "select player_id from selection where team_id = '".$this->data['team_id']."' and
selection_priority != '0'
order by selection_priority limit 1";
    else {
        //have to make sure the player at the top of the list is suitable and amenable first!
        $statement = "select pick_id,team_id from `pick` where `player_id` is NULL order by pick_id asc limit 1";
        $row = mysql_fetch_array(mysql_query($statement));
        $pick_id = $row["pick_id"];
        $round = floor(($pick_id-1)/32)+1;
        if ( $round == 1 )
                $suitable = "staff_suitable_hc";
        if ( $round == 2 )
                $suitable = "staff_suitable_oc";
        if ( $round == 3 )
                $suitable = "staff_suitable_dc";
        if ( $round == 4 )
                $suitable = "staff_suitable_ac";
        if ( $round == 5 )
                $suitable = "staff_suitable_sc";
        
        $statement = "select player_id from selection where team_id = '".$this->data['team_id']."' and
selection_priority != '0'
order by selection_priority";
        $result = mysql_query($statement);
        while ($row = mysql_fetch_array($result) ){
            $statement = "select * from staff where staff_id = ".$row["player_id"];
            $test = mysql_fetch_array(mysql_query($statement));
            if ( $test["staff_amenable"]=='Y' && $test[$suitable]>0 )
                return $test["staff_id"];
        }
        //nobody in the list is amenable!
        return NULL;
    }
    $row = mysql_fetch_array(mysql_query($statement));
    return $row['player_id'];
  }

  function pick_bpa() {
    global $settings;
    if ( $settings->get_value(kSettingStaffDraftOn)==1 ){
        //in a staff situation we know the position based on the round and will just pick based on 
        //most suitable
        $statement = "select pick_id,team_id from `pick` where `player_id` is NULL order by pick_id asc limit 1";
        $row = mysql_fetch_array(mysql_query($statement));
        $pick_id = $row["pick_id"];
        $round = floor(($pick_id-1)/32)+1;
        if ( $round == 1 )
                $order = "staff_suitable_hc";
        if ( $round == 2 )
                $order = "staff_suitable_oc";
        if ( $round == 3 )
                $order = "staff_suitable_dc";
        if ( $round == 4 )
                $order = "staff_suitable_ac";
        if ( $round == 5 )
                $order = "staff_suitable_sc";
        "select staff_id from staff where staff_amenable='Y' and ".$order.">0 order by ".$order." limit 1";
        $row = mysql_fetch_array(mysql_query($statement));
        return $row["staff_id"];
    }
    // Looks in our bpa queue and sees if we have a player to pick
    $statement = "select * from bpa where team_id = '".$this->data['team_id']."' order by bpa_priority limit 1";
    $row = mysql_fetch_array(mysql_query($statement));
    $position_id = $row['position_id'];
    $bpa_id = $row['bpa_id'];
    $desc = '';
    if ($bpa_id) {
      $tables[] = "player";
      $wheres[] = "player.position_id = '".$row['position_id']."'";
      if ($row['bpa_max_experience']) {
	$wheres[] = "player.player_experience <= '".$row['bpa_max_experience']."'";
      }
      if ($row['attribute_id'] > 0) {
	// Using the list of attributes
	// See if we have our own extractor upload
	$statement = "select * from team_player_to_attribute where team_id = '".$this->data['team_id']."'";
	if (mysql_num_rows(mysql_query($statement))) {
	  $tables[] = "team_player_to_attribute";
	  $wheres[] = "team_player_to_attribute.player_id = player.player_id";
	  $wheres[] = "team_player_to_attribute.attribute_id = '".$row['attribute_id']."'";
	  $order = "order by team_player_to_attribute.player_to_attribute_high desc";
	} else {
	  $tables[] = "player_to_attribute";
	  $wheres[] = "player_to_attribute.player_id = player.player_id";
	  $wheres[] = "player_to_attribute.attribute_id = '".$row['attribute_id']."'";
	  $order = "order by player_to_attribute.player_to_attribute_high desc";
	}
      } elseif ($row['attribute_id'] == -1) {
	// Using the adjusted grade/future rating
	$wheres[] = "player.player_adj_score is not null";
	$order = "order by player.player_adj_score desc";
      } elseif ($row['attribute_id'] == -2) {
	// Using the adjusted grade/future rating
	$wheres[] = "player.player_future is not null";
	$order = "order by player.player_future desc";
      } elseif ($row['attribute_id'] == -3) {
	// Using the adjusted grade/future rating
	$wheres[] = "player.player_current is not null";
	$order = "order by player.player_current desc";
      }
      $joins[] = "left join pick on pick.player_id = player.player_id";
      $wheres[] = "pick.player_id is NULL";
      $statement = "select player.player_id from (".implode(",",$tables).")
".implode(" ",$joins)."
where ".implode(" and ",$wheres)."
".$order." limit 1";
      $row = mysql_fetch_array(mysql_query($statement));
      if (mysql_error()) {
	echo "<P>$statement<p>".mysql_error();
      }
      $statement = "delete from bpa where bpa_id = '$bpa_id'";
      mysql_query($statement);
      return $row['player_id'];
    }
  }

  function new_pick_time($time) {
    // Checks to see if there is enough time before the close of the day today for this pick, if not, set the start time
    // appropriately befor the start of tomorrow
    global $settings;
    // First see if we have a 24-hour clock
    if (!$settings->get_value(kSettingEndTime)) {
      return date("Y-m-d H:i:s", $time);
    }
    $end_time = strtotime(date("Y-m-d ", $time).date("H:i:s", $settings->get_value(kSettingEndTime)));
    $start_time = strtotime(date("Y-m-d ", strtotime("tomorrow",$time)).date("H:i:s", $settings->get_value(kSettingStartTime)));
    $limit = $settings->get_value(kSettingPickTimeLimit)*$this->data['team_clock_adj'];
    if ($time > $end_time) {
      // past the end time, return tomorrow's start time
      $return = date("Y-m-d H:i:s", $start_time);
    } elseif ($time < strtotime(date("Y-m-d ",$time).date("H:i:s", $settings->get_value(kSettingStartTime)))) {
      // Before today's start time, return today's start time
      $return = date("Y-m-d H:i:s",strtotime(date("Y-m-d ",$time).date("H:i:s", $settings->get_value(kSettingStartTime))));
    } else {
      $my_end = strtotime("+$limit minutes", $time);
      if ($settings->get_value(kSettingRolloverMethod) == kRollIntoTomorrow && $my_end > $end_time) {
	// Don't have enough time, let's see how far we go over
	$difference = $my_end - $end_time;
	$new_start = $start_time + $difference - ($limit*60);
	$return = date("Y-m-d H:i:s", $new_start);
      } else {
	// Have enough time, return $time
	$return = date("Y-m-d H:i:s", $time);
      }
    }
    return $return;
  }

  function lower_pick_limit() {
    // Reduce this team's pick limit by the percentage set for when they are autopicked
    global $settings;
    if ($this->data['team_clock_adj'] > 0) {
      $autopick_reduction = (100-$settings->get_value(kSettingAutopickReduction))/100;
      $new_autopick = $this->data['team_clock_adj'] * $autopick_reduction;
      if ($this->data['team_autopick_wait'] >= $new_autopick) {
	$wait = $new_autopick - 5;
      } else {
	$wait = $this->data['team_autopick_wait'];
      }
      $statement = "update team set
team_clock_adj = '$new_autopick',
team_autopick_wait = '$wait'
where team_id = '".$this->data['team_id']."'";
      mysql_query($statement);
    }
  }

  function force_pick() {
     global $settings;
     $player_id = NULL;

     //short circuit the player force pick if we're running a staff draft
     if ( $settings->get_value(kSettingStaffDraftOn)==1 ){      
      //first see if they can decline it because they don't need to pick
      $statement = "select pick_id,team_id from `pick` where `player_id` is NULL order by pick_id asc limit 1";
      $row = mysql_fetch_array(mysql_query($statement));
      $pick_id = $row["pick_id"];
      $round = floor(($pick_id)/32)+1;
      if ( $tid = $row["team_id"] ){
           //we found the team
           $statement = "select in_game_id from team where team_id=".$tid.";";
           $row = mysql_fetch_array(mysql_query($statement));
           $tid = $row["in_game_id"];
           $statement = "select * from staff where fired=0 and drafted=0 and staff_curr_team_id = ".$tid." and staff_role_id=".$round;
           $result = mysql_query($statement);
           $result = mysql_fetch_array($result);
           if ( $result["staff_name"]!='' ){
               //they DO have a staff member in this position and can decline it.
               $player_id = kDeclinePick;
           } else {
                if ( $round==1 )
                    $statement = "select staff_id from staff where staff_amenable='Y' and drafted=0 order by staff_suitable_hc desc limit 1";
                else if ( $round == 2 )
                    $statement = "select staff_id from staff where staff_amenable='Y' and drafted=0 order by staff_suitable_oc desc limit 1";
                else if ( $round == 3 )
                    $statement = "select staff_id from staff where staff_amenable='Y' and drafted=0 order by staff_suitable_dc desc limit 1";
                else if ( $round == 4 )
                    $statement = "select staff_id from staff where staff_amenable='Y' and drafted=0 order by staff_suitable_ac desc limit 1";
                else if ( $round == 5 )
                    $statement = "select staff_id from staff where staff_amenable='Y' and drafted=0 order by staff_suitable_sc desc limit 1";
                $row = mysql_query($statement);
                $result = mysql_fetch_array($row);
                $player_id = $result["staff_id"];
           }
      }
      return $player_id;

     }



    // See if there is something in the priority list we can use
    if ($this->data['pick_method_id'] == kPlayerQueue) {
	$player_id = $this->pick_priority();
    } elseif ($this->data['pick_method_id'] == kBPAQueue) {
	$player_id = $this->pick_bpa();
    } elseif ($this->data['pick_method_id'] == kPlayerThenBPA) {
	$player_id = $this->pick_priority();
        if (!$player_id) {
            $player_id = $this->pick_bpa();
        } else
           return $player_id;
    }
    if ( $player_id )
        return $player_id;

    // If we didn't get a player that way, we can check to see if we have the data for a scout pick
    $statement = "select * from mock_draft";
    if (mysql_num_rows(mysql_query($statement))) {
      return $this->scout_pick();
    }
    // If we are here, we can't do a scout pick, so we'll use the original BPA at a position we haven't picked yet
    $tables[] = "player";
    $joins[] = "left join pick as all_picks on all_picks.player_id = player.player_id";
    $col[] = "player.player_id";
    $wheres[] = "all_picks.player_id is NULL";
    $col[] = "player.position_id";
    $joins[] = "left join (select player.position_id from pick, player where pick.team_id = '".$this->data['team_id']."' and
player.player_id = pick.player_id) selected_pos on selected_pos.position_id = player.position_id";
    $col[] = "selected_pos.position_id";
    $wheres[] = "selected_pos.position_id is NULL";
    // Don't pick K or P
    $wheres[] = "player.position_id != '".kPositionK."'";
    $wheres[] = "player.position_id != '".kPositionP."'";

    $statement = "select ".implode(",",$col)." from (".implode(",",$tables).")
".implode(" ",$joins)." where ".implode(" and ",$wheres)."
order by player.player_adj_score desc, player.player_current desc, player.player_future desc, player.player_id limit 1";
    $row = mysql_fetch_array(mysql_query($statement));
    if ($row['player_id']) {
      return $row['player_id'];
    } else {
      // Get the best player available
      // At this point, K and P are OK
      $statement = "select player.player_id from player left join pick on pick.player_id = player.player_id
where pick.player_id is NULL
order by player.player_adj_score desc, player.player_future desc, player.player_id limit 1";
      $row = mysql_fetch_array(mysql_query($statement));
      return $row['player_id'];
    }
  }

  function mock_pick($mock_pick_id) {
    // Find the highest need compared to the best score
    $tables[] = "team_need";
    $col[] = "team_need.team_need_id";
    $wheres[] = "team_need.team_id = '".$this->data['team_id']."'";
    $wheres[] = "team_need.mock_pick_id is NULL";
    $tables[] = "player";
    $tables[] = "(select position_id, max(player_adj_score) max_score from player
left join mock_draft on mock_draft.player_id = player.player_id
where mock_draft.player_id is NULL
group by position_id) max_table";
    $wheres[] = "player.position_id = max_table.position_id";
    $col[] = "max_table.max_score";
    $col[] = "player.position_id";
    $col[] = "team_need.team_need_order";
    $col[] = "player.player_id";
    $col[] = "player.player_name";
    $col[] = "player.player_adj_score";
    $score_weight = rand(150,250)/100;
    $need_weight = rand(75,100)/100;
    $col[] = "pow(player.player_adj_score,".$score_weight.") * (team_need.team_need_order*".$need_weight.
      ") * position.position_scout_weight pick_order";
    $wheres[] = "player.position_id = team_need.position_id";
    $joins[] = "left join mock_draft on mock_draft.player_id = player.player_id";
    $wheres[] = "mock_draft.player_id is NULL";
    $having[] = "player.player_adj_score = max_score";
    $tables[] = "position";
    $wheres[] = "position.position_id = player.position_id";
    $col[] = "position.position_name";
    
    $statement = "select ".implode(",",$col)." from (".implode(",",$tables).")".implode(" ",$joins)."
where ".implode(" and ",$wheres)."
having ".implode(" and ",$having)."
order by pick_order desc
limit 2
";

    $result = mysql_query($statement);
    // Get two rows to drive the commentary
    $row = mysql_fetch_array($result);
    $row2 = mysql_fetch_array($result);
    if ($row['player_id']) {
      // Generate the commentary
      $commentary = addslashes($this->mock_commentary($row, $row2));
      // Clear that we've picked this need
      $statement = "update team_need set mock_pick_id = '$mock_pick_id' where
team_need_id = '".$row['team_need_id']."'";
      mysql_query($statement);
      // Insert it into the mock draft
      $statement = "insert into mock_draft (pick_id, team_id, player_id, mock_draft_commentary) values
('".$mock_pick_id."', '".$this->data['team_id']."', '".$row['player_id']."', '$commentary')";
      mysql_query($statement);
      echo mysql_error();
      return;
    }
    // If we are still here, we are out of needs, so just returt the BPA that has not yet been selected
    $statement = "select player.player_id from player left join mock_draft on mock_draft.player_id = player.player_id
where mock_draft.player_id is NULL
order by player.player_adj_score desc, player.player_id limit 1";
    $row = mysql_fetch_array(mysql_query($statement));
    $commentary = $this->data['team_name']." has already picked at all positions, and this is the best player
still on the board.";
    $statement = "insert into mock_draft (pick_id, team_id, player_id, mock_draft_commentary) values
('".$mock_pick_id."', '".$this->data['team_id']."', '".$row['player_id']."', '$commentary')";
    mysql_query($statement);
    return;
  }

  function mock_commentary($row1, $row2) {
    $tough = array("This was a tough one to call.",
		   "Tough one to call.",
		   "Hard to know for sure what they'll do here.",
		   "Not sure what they'll do here.",
		   "Not sure what I'd do here.",
		   "Very tough one here.",
		   "Tough one here.",
		   "This one's tough.",
		   "Not sure here.",
		   "Well, this is a toss-up.",
		   "This one's a toss-up.",
		   "Your guess is as good as mine.",
		   "Very difficult choice here.");
    $justaseasy = array("might just as easily go with",
			"may just as easily go with",
			"could just as easily go with",
			"may choose",
			"might choose",
			"could choose",
			"may want",
			"might want",
			"could want",
			"may also consider",
			"might also consider",
			"could also consider",
			"may look at",
			"might look at",
			"could look at",
			"may also look at",
			"might also look at",
			"could also look at",
			"could also think about",
			"may also think about",
			"might also think about");
    $edgesout = array("edges out",
		      "might be a better pick than",
		      "may be a better pick than",
		      "could be a better pick than",
		      "might only be a slightly better pick than",
		      "may only be a slightly better pick than",
		      "could only be a slightly better pick than",
		      "is probably better than",
		      "seems better than",
		      "seems to make a better choice than",
		      "probably makes a better choice than");
    $hardtopass = array("would be hard to pass up here.",
			"would be pretty tempting at this point.",
			"would be hard to pass up at this point.",
			"would be pretty tempting here.",
			"would be tough to overlook here.",
			"may be worth picking here.",
			"may be worth this pick.",
			" - how do you pass him up?",
			"would still make a good choice.");
    if ($row1['pick_order'] < ($row2['pick_order']*1.05)) {
      if ($row1['position_name'] == $row2['position_name']) {
	$same_name = true;
      } else {
	$same_name = false;
      }
      $commentary = $tough[rand(0,count($tough)-1)]." ".$this->close_comment($row2, $same_name);
    } elseif ($row1['pick_order'] < ($row2['pick_order']*1.1)) {
      $commentary = $this->data['team_name']." ".$justaseasy[rand(0,count($justaseasy)-1)]." ".$row2['position_name']." ".
	$row2['player_name']." here.";
    } elseif ($row1['pick_order'] < ($row2['pick_order']*1.2)) {
      $commentary = $row1['player_name']." ".$edgesout[rand(0,count($edgesout)-1)]." ".
	$row2['position_name']." ".$row2['player_name']." here, but ".$this->data['team_name']." could go either way.";
    } elseif ($row1['team_need_order'] > 75) {
      $commentary = $this->data['team_name']." desperately needs a ".$row1['position_name'].", and ".$row1['player_name']."
is the best available at this point.";
    } elseif ($row1['team_need_order'] > 60) {
      $commentary = $this->data['team_name']." needs a ".$row1['position_name'].", and ".$row1['player_name']."
is probably the most logical choice.";
    } elseif ($row1['team_need_order'] > 50) {
      $commentary = $this->data['team_name']." could use a ".$row1['position_name']." and ".$row1['player_name']."
would be hard to pass up here.";
    } elseif ($row1['team_need_order'] > 40) {
      if ($row1['max_score'] > 6.5) {
	$commentary = $row1['position_name']." isn't really a huge need for ".$this->data['team_name'].", but
how can you pass up ".$row1['player_name']."?";
      } else {
	$commentary = $row1['position_name']." isn't really a huge need for ".$this->data['team_name'].", but
".$row1['player_name']." ".$hardtopass[rand(0,count($hardtopass)-1)];
      }
    } else { 
      if ($row1['max_score'] > 6.5) {
	$commentary = $this->data['team_name']." doesn't need another ".$row1['position_name'].", but
".$row1['player_name']." ".$hardtopass[rand(0,count($hardtopass)-1)];
      } else {
	$commentary = $this->data['team_name']." really doesn't need another ".$row1['position_name'].", but
there really isn't a better choice at this point.";
      }
    }
    return $commentary;
  }

  function close_comment($row, $same_name) {
    $could_have = array("they could have easily gone with",
		     "they might go with",
		     "they might also consider",
		     "another option they might look at is",
		     "it wouldn't surprise me if they go with",
		     "it makes just as much sense to pick");
    
    if ($same_name) {
      if ($row['team_need_order'] > 75) {
	$comment = $this->data['team_name']." desperately needs a ".$row['position_name']."; ".
	  $could_have[rand(0,count($could_have)-1)]." ".$row['player_name'];
      } elseif ($row['team_need_order'] > 60) {
	$comment = $this->data['team_name']." needs a ".$row['position_name']."; ".
	  $could_have[rand(0,count($could_have)-1)]." ".$row['player_name'];
      } elseif ($row['team_need_order'] > 50) {
	$comment = $this->data['team_name']." could use a ".$row['position_name']."; ".
	  $could_have[rand(0,count($could_have)-1)]." ".$row['player_name'];
      } else {
	$comment = "While ".$this->data['team_name']." doesn't necessarily need a ".$row['position_name'].", ".
	  $could_have[rand(0,count($could_have)-1)]." ".$row['player_name'];
      }
    } else {
      if ($row['team_need_order'] > 75) {
	$comment = $this->data['team_name']." desperately needs a ".$row['position_name']." as well, ".
	  $could_have[rand(0,count($could_have)-1)]." ".$row['player_name'];
      } elseif ($row['team_need_order'] > 60) {
	$comment = $this->data['team_name']." needs a ".$row['position_name']." as well, ".
	  $could_have[rand(0,count($could_have)-1)]." ".$row['player_name'];
      } elseif ($row['team_need_order'] > 50) {
	$comment = $this->data['team_name']." could use a ".$row['position_name']." as well, ".
	  $could_have[rand(0,count($could_have)-1)]." ".$row['player_name'];
      } else {
	$comment = "While ".$this->data['team_name']." doesn't necessarily need a ".$row['position_name'].", ".
	  $could_have[rand(0,count($could_have)-1)]." ".$row['player_name'];
      }
    }
    return $comment;
  }

  function scout_pick() {
   global $settings;
    if ( $settings->get_value(kSettingStaffDraftOn)==1 ){
        //in a staff situation we know the position based on the round and will just pick based on 
        //most suitable
        $statement = "select pick_id,team_id from `pick` where `player_id` is NULL order by pick_id asc limit 1";
        $row = mysql_fetch_array(mysql_query($statement));
        $pick_id = $row["pick_id"];
        $round = floor(($pick_id-1)/32)+1;
        if ( $round == 1 )
                $order = "staff_suitable_hc";
        if ( $round == 2 )
                $order = "staff_suitable_oc";
        if ( $round == 3 )
                $order = "staff_suitable_dc";
        if ( $round == 4 )
                $order = "staff_suitable_ac";
        if ( $round == 5 )
                $order = "staff_suitable_sc";
        "select staff_id from staff where staff_amenable='Y' order by ".$order." limit 1";
        $row = mysql_fetch_array(mysql_query($statement));
        return $row["staff_id"];
    }
    // Find the highest need compared to the best score
    // This is a copy of mock_pick but with some changes for the live draft
    $tables[] = "team_need";
    $col[] = "team_need.team_need_id";
    $wheres[] = "team_need.team_id = '".$this->data['team_id']."'";
    $wheres[] = "team_need.pick_id is NULL";
    $tables[] = "player";
    $tables[] = "(select position_id, max(player_adj_score) max_score from player
left join pick on pick.player_id = player.player_id
where pick.player_id is NULL
group by position_id) max_table";
    $wheres[] = "player.position_id = max_table.position_id";
    $col[] = "max_table.max_score";
    $col[] = "player.position_id";
    $col[] = "team_need.team_need_order";
    $col[] = "player.player_id";
    $col[] = "player.player_adj_score";
    $score_weight = rand(150,250)/100;
    $need_weight = rand(75,100)/100;
    $col[] = "pow(player.player_adj_score,".$score_weight.") * (team_need.team_need_order*".$need_weight.
      ") * position.position_scout_weight pick_order";
    $wheres[] = "player.position_id = team_need.position_id";
    $joins[] = "left join pick on pick.player_id = player.player_id";
    $wheres[] = "pick.player_id is NULL";
    $having[] = "player.player_adj_score = max_score";
    $tables[] = "position";
    $wheres[] = "position.position_id = player.position_id";
    $col[] = "position.position_name";
    
    $statement = "select ".implode(",",$col)." from (".implode(",",$tables).")".implode(" ",$joins)."
where ".implode(" and ",$wheres)."
having ".implode(" and ",$having)."
order by pick_order desc
limit 1
";
    $row = mysql_fetch_array(mysql_query($statement));
    if ($row['player_id']) {
      return $row['player_id'];
    }

    //error_log("Scout pick failed for team ".$this->data['team_id']);
    //error_log($statement);

    // If we are still here, we are out of needs, so just returt the BPA that has not yet been selected
	$statement = "select player.player_id from player left join pick on pick.player_id = player.player_id
where pick.player_id is NULL
order by player.player_adj_score desc, player.player_id limit 1";
    $row = mysql_fetch_array(mysql_query($statement));

    return $row['player_id'];
  }
}
?>