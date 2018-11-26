<?
/***************************************************************************
 *                                player.inc.php
 *                            -------------------
 *   begin                : Monday, May 12, 2008
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

class player {
  function player($player_id) {
    global $settings;
    $this->in_game_id = mysql_real_escape_string($player_id);
    if ( $settings->get_value(kSettingStaffDraftOn)==0 ){
       $statement = "select player_id from player where player_in_game_id = ".$this->in_game_id;
       $row = mysql_fetch_array(mysql_query($statement));
       $this->player_id = $row['player_id'];
    } else {
       $statement = "select staff_id from staff where staff_in_game_id = ".$this->in_game_id;
       $row = mysql_fetch_array(mysql_query($statement));
       $this->player_id = $row['staff_id'];
    }
  }

  function draw() {
    global $login;
    $tables[] = "player";
    $wheres[] = "player.player_id = '".$this->player_id."'";
    $col[] = "player.player_name";
    $tables[] = "position";
    $wheres[] = "position.position_id = player.position_id";
    $tables[] = "combine_ratings";
    $wheres[] = "combine_ratings.position_id = position.position_id";
    $tables[] = "height_weight";
    $wheres[] = "height_weight.position_id = player.position_id";
    if (!$login->is_admin()) {
      $joins[] = "left join selection on selection.player_id = player.player_id and
selection.team_id = '".$login->team_id()."'";
      $col[] = "selection.player_id is_selected";
      $col[] = "selection.selection_priority";
    }
    $col[] = "player_score";
    $col[] = "player_adj_score";
    $col[] = "height_weight.*";
    $col[] = "player.position_id";
    $col[] = "position.position_name";
    $col[] = "player_school";
    $col[] = "player_height";
    $col[] = "player_weight";
    $col[] = "if (player_experience > 1, player_experience, 'R') player_experience";
    $col[] = "player.player_intelligence";
    $col[] = "player.player_loyalty";
    $col[] = "player.player_winner";
    $col[] = "player.player_leader";
    $col[] = "player.player_personality";
    $col[] = "player.player_popularity";
    $col[] = "player.player_dob";
    $col[] = "team_player.player_mentor_to";
    $col[] = "team_player.player_interviewed";
    $col[] = "team_player.player_impression";
    $col[] = "team_player.player_affinities";
    $col[] = "player.player_character";
    $col[] = "player.player_vol";
    $col[] = "player.player_solec";
    $col[] = "if (player.player_solec <= combine_ratings.combine_low_sole,
'color: #0b0; font-weight: bold',
if (player.player_solec >= combine_ratings.combine_high_sole,
'color: #00f; font-weight: bold', NULL)) player_solec_style";
    $col[] = "player.player_40";
    $col[] = "if (player.player_40 >= combine_ratings.combine_low_40, 'color: #0b0; font-weight: bold',
if (player.player_40 <= combine_ratings.combine_high_40, 'color: #00f; font-weight: bold', NULL)) player_40_style";
    $col[] = "player.player_bench";
    $col[] = "if (player.player_bench <= combine_ratings.combine_low_strength,
'color: #0b0; font-weight: bold',
if (player.player_bench >= combine_ratings.combine_high_strength,
'color: #00f; font-weight: bold', NULL)) player_bench_style";
    $col[] = "player.player_agil";
    $col[] = 'if (player.player_agil >= combine_ratings.combine_low_agil,
"color: #0b0; font-weight: bold",

if (player.player_agil <= combine_ratings.combine_high_agil,
"color: #00f; font-weight: bold", NULL)

) player_agil_style';
    $col[] = "player.player_broad";
    $col[] = 'if (player.player_broad <= combine_ratings.combine_low_broad,
"color: #0b0; font-weight: bold",

if (player.player_broad >= combine_ratings.combine_high_broad,
"color: #00f; font-weight: bold", NULL)

) player_broad_style';
    $col[] = "player.player_pos_drill";
    $col[] = 'if (combine_ratings.combine_high_pos is not null,

if (player.player_pos_drill <= combine_ratings.combine_low_pos,
"color: #0b0; font-weight: bold",

if (player.player_pos_drill >= combine_ratings.combine_high_pos,
"color: #00f; font-weight: bold", NULL)

)

,

NULL) player_pos_drill_style';
    $col[] = "player.player_developed";
    $joins[] = "left join player_comments on player_comments.team_id = '".mysql_real_escape_string($login->team_id())."' and
player_comments.player_id = player.player_id";
    $col[] = "player_comments.player_comments_text";

    $joins[] = "left join team_player on team_player.player_id = player.player_id and team_player.team_id = '".mysql_real_escape_string($login->team_id())."'";
    $joins[] = "left join pick on pick.player_id = player.player_id";
    $joins[] = "left join team on team.team_id = pick.team_id";
    $col[] = "team.team_name";
    $col[] = "pick.pick_id";

    $statement = "select ".implode(",",$col)." from (".implode(",",$tables).")
".implode(" ",$joins)."
where ".implode(" and ",$wheres);
    $row = mysql_fetch_array(mysql_query($statement));
    $html = file_get_contents("includes/html/player.html");
    
    $html = str_replace("%player_name%", "#".$this->player_id." ".$row['player_name'], $html);
    $html = str_replace("%position_name%", $row['position_name'], $html);
    $this->position_id = $row['position_id'];
    $html = str_replace("%combine%", $this->draw_combine($row), $html);
    $html = str_replace("%attributes%", $this->draw_attributes($row['position_id']), $html);
    $html = str_replace("%hiddenattributes%", $this->draw_hiddenattributes($row), $html);
    if ($row['pick_id']) {
      $extra = "Selected by ".$row['team_name'].", pick ".calculate_pick($row['pick_id']);
      $selection = $row['pick_id'];
    } else {
      if (!$login->is_site_admin()) {
	if ($login->can_pick()) {
	  $extra = '<a href="make_pick.php?player_id='.$this->player_id.'">Select this player</a>';
	}
	if ($login->team_id()) {
	  if (!$row['is_selected']) {
	    $extra .= '
<p><form method="get" action="add_player_to_selection.php">
<input type="hidden" name="player_id" value="'.$this->player_id.'">
<input type="submit" value="Add to Priority List">
</form>';
	  } else {
	    $extra .= '
<p>Currently in your selection set, priority '.$row['selection_priority'];
	  }
	}
      }
    }
    $html = str_replace("%extra%", $extra, $html);
    $html = str_replace("%id%", $this->player_id, $html);
    if ($login->team_id()) {
      $html = str_replace("%comments%", file_get_contents('includes/html/player_comments.html'), $html);
      $html = str_replace("%id%", $this->player_id, $html);
    } else {
      $html = str_replace("%comments%", "", $html);
    }
    $html = str_replace("%comments%", $row['player_comments_text'], $html);
    $html = str_replace("%left_nav%", $this->left_nav($selection), $html);
    $html = str_replace("%right_nav%", $this->right_nav($selection), $html);
    
    return $html;
  }

  
function draw_staff() {
    global $login;
    $tables[] = "staff";
    $wheres[] = "staff.staff_id = '".$this->player_id."'";
    $col[] = "staff.staff_name";
    $tables[] = "staff_roles";
    $wheres[] = "staff_roles.staff_role_id = staff.staff_role_id";
    $tables[] = "staff_pri_group";
    $wheres[] = "staff_pri_group.staff_pri_group_id = staff.staff_pri_group_id";
    if (!$login->is_admin()) {
      $joins[] = "left join selection on selection.player_id = staff.staff_id and
selection.team_id = '".$login->team_id()."'";
      $col[] = "selection.player_id is_selected";
      $col[] = "selection.selection_priority";
    }
    
    $col[] = "staff_role_name";
    $col[] = "staff_pri_group_name";
    $col[] = "staff_curr_team_id";
    $col[] = "staff_salary";
    $col[] = "staff_player_dev";
    $col[] = "staff_young_player_dev";
    $col[] = "staff_motivation";
    $col[] = "staff_discipline";
    $col[] = "staff_play_calling";
    $col[] = "staff_str_training";
    $col[] = "staff_conditioning";
    $col[] = "staff_intelligence";
    $col[] = "staff_scouting";
    $col[] = "staff_interviewing";
    $col[] = "staff_age";
    $col[] = "staff_yrs_on_contract";
    $col[] = "staff_suitable_hc";
    $col[] = "staff_suitable_oc";
    $col[] = "staff_suitable_dc";
    $col[] = "staff_suitable_ac";
    $col[] = "staff_suitable_sc";

    $joins[] = "left join player_comments on player_comments.team_id = '".mysql_real_escape_string($login->team_id())."' and
player_comments.player_id = staff.staff_id";
    $col[] = "player_comments.player_comments_text";

    $joins[] = "left join team_player on team_player.player_id = staff.staff_id and team_player.team_id = '".mysql_real_escape_string($login->team_id())."'";
    $joins[] = "left join pick on pick.player_id = staff.staff_id";
    $joins[] = "left join team on team.team_id = pick.team_id";
    $col[] = "team.team_name";
    $col[] = "pick.pick_id";

    $statement = "select ".implode(",",$col)." from (".implode(",",$tables).")
".implode(" ",$joins)."
where ".implode(" and ",$wheres);
    $row = mysql_fetch_array(mysql_query($statement));
    $html = file_get_contents("includes/html/player.html");
    
    $html = str_replace("%player_name%", "#".$this->player_id." ".$row['staff_name'], $html);
    $html = str_replace("%position_name%", $row['staff_role_name'], $html);
    $this->position_id = $row['staff_role_id'];
    $html = str_replace("%combine%", $this->draw_staff_data($row), $html);
    $html = str_replace("%attributes%", '', $html);
    $html = str_replace("%hiddenattributes%", '', $html);
    if ($row['pick_id']) {
      $extra = "Selected by ".$row['team_name'].", pick ".calculate_pick($row['pick_id']);
      $selection = $row['pick_id'];
    } else {
      if (!$login->is_site_admin()) {
	if ($login->can_pick()) {
	  $extra = '<a href="make_pick.php?staff_id='.$this->player_id.'">Select this staff member</a>';
	}
	if ($login->team_id()) {
	  if (!$row['is_selected']) {
	    $extra .= '
<p><form method="get" action="add_player_to_selection.php">
<input type="hidden" name="player_id" value="'.$this->player_id.'">
<input type="submit" value="Add to Priority List">
</form>';
	  } else {
	    $extra .= '
<p>Currently in your selection set, priority '.$row['selection_priority'];
	  }
	}
      }
    }
    $html = str_replace("%extra%", $extra, $html);
    $html = str_replace("%id%", $this->player_id, $html);
    if ($login->team_id()) {
      $html = str_replace("%comments%", file_get_contents('includes/html/player_comments.html'), $html);
      $html = str_replace("%id%", $this->player_id, $html);
    } else {
      $html = str_replace("%comments%", "", $html);
    }
    $html = str_replace("%comments%", $row['player_comments_text'], $html);
    $html = str_replace("%left_nav%", $this->left_nav($selection), $html);
    $html = str_replace("%right_nav%", $this->right_nav($selection), $html);
    
    return $html;
  }
  
  
  
  
    function get_affinity($dob){
        $html ="<p>Invalid DOB?</p>";
       if ($dob ){
           $dobarray = explode( '-',$dob);
           //'Year:'.$dobarray[0].' Month '.$dobarray[1].' Day '.$dobarray[2];
           
           $html='This player is likely to have affinities with position\nand team leaders that have birthdays between:\n\n';
           if ( ($dobarray[1]==3 && $dobarray[2]>20 ) || ($dobarray[1]==4 && $dobarray[2]<21) ){
               $html.='5/22-6/21 and also 10/24-11/22.';//Aries
           } else if ( ($dobarray[1]==4 && $dobarray[2]>20 ) || ($dobarray[1]==5 && $dobarray[2]<22) ){
               $html.='2/19-3/20 and also 6/22-7/23.';//Taurus
           } else if ( ($dobarray[1]==5 && $dobarray[2]>21 ) || ($dobarray[1]==6 && $dobarray[2]<22) ){
               $html.='3/21-4/20 and also 10/24-11/22.';//Gemini
           } else if ( ($dobarray[1]==6 && $dobarray[2]>21 ) || ($dobarray[1]==7 && $dobarray[2]<24) ){
               $html.='2/19-3/20 and also 4/21-5/21.';//Cancer
           } else if ( ($dobarray[1]==7 && $dobarray[2]>23 ) || ($dobarray[1]==8 && $dobarray[2]<24) ){
               $html.='11/23-12/22 and also 8/24-9/23.';//Leo
           } else if ( ($dobarray[1]==8 && $dobarray[2]>23 ) || ($dobarray[1]==9 && $dobarray[2]<24) ){
               $html.='11/23-12/22 and also 7/24-8/23.';//Virgo
           } else if ( ($dobarray[1]==9 && $dobarray[2]>23 ) || ($dobarray[1]==10 && $dobarray[2]<24) ){
               $html.='1/21-2/20 and also 12/23-1/20.';//Libra
           } else if ( ($dobarray[1]==10 && $dobarray[2]>23 ) || ($dobarray[1]==11 && $dobarray[2]<23) ){
               $html.='3/21-4/20 and also 5/22-6/21.';//Scorpio
           } else if ( ($dobarray[1]==11 && $dobarray[2]>22 ) || ($dobarray[1]==12 && $dobarray[2]<23) ){
               $html.='7/24-8/23 and also 8/24-9/23.';//Sagitarius
           } else if ( ($dobarray[1]==12 && $dobarray[2]>22 ) || ($dobarray[1]==1 && $dobarray[2]<21) ){
               $html.='1/21-2/20 and also 9/24-10/23.';//Capricorn
           } else if ( ($dobarray[1]==1 && $dobarray[2]>20 ) || ($dobarray[1]==2 && $dobarray[2]<19) ){
               $html.='12/23-1/20 and also 9/24-10/23.';//Aquarius
           } else if ( ($dobarray[1]==2 && $dobarray[2]>18 ) || ($dobarray[1]==3 && $dobarray[2]<21) ){
               $html.='4/21-5/21 and also 6/22-7/23.';//Pisces
           } else
               $s = -1;
           return $html;
       }
       return $html;
  }

  function draw_combine(&$row) {
    $dobmessage = $this->get_affinity($row['player_dob']);
    $html .= '<script>
function affinityMessage()
{
alert("'.$dobmessage.'");
}
</script>
<table align="center">
  <tr>
    <td align="right">Date of Birth:</td>
    <td><a onclick="affinityMessage()">'.$row['player_dob'].'</a></td>
  </tr>
  <tr>
    <td align="right">School:</td>
    <td>'.$row['player_school'].'</td>
  </tr>
  <tr>
    <td align="right">Height:</td>';
    if ($row['player_height'] <= $row['height_short']) {
      $comment = "Short";
    } elseif ($row['player_height'] <= $row['height_wba']) {
      $comment = "Way Below Average";
    } elseif ($row['player_height'] <= $row['height_ba']) {
      $comment = "Below Average";
    } elseif ($row['player_height'] <= $row['height_avg']) {
      $comment = "Average";
    } elseif ($row['player_height'] <= $row['height_aa']) {
      $comment = "Above Average";
    } else {
      $comment = "Way Above Average";
    }
    $html .= '
    <td>'.height_convert($row['player_height']).' ('.$comment.')</td>
  </tr>
  <tr>
    <td align="right">Weight:</td>';
    if ($row['player_height'] <= $row['height_short']) {
      $comment = "Light";
    } elseif ($row['player_weight'] <= $row['weight_wba']) {
      $comment = "Way Below Average";
    } elseif ($row['player_weight'] <= $row['weight_ba']) {
      $comment = "Below Average";
    } elseif ($row['player_weight'] <= $row['weight_avg']) {
      $comment = "Average";
    } elseif ($row['player_weight'] <= $row['weight_aa']) {
      $comment = "Above Average";
    } elseif ($row['player_weight'] <= $row['weight_waa']) {
      $comment = "Way Above Average";
    } else {
      $comment = "Heavy";
    }
    $html .= '
    <td>'.$row['player_weight'].' lbs. ('.$comment.')</td>
  </tr>
  <tr>
    <td align="right">Experience:</td>
    <td>'.$row['player_experience'].'</td>
  </tr>';
    $text_array = array("player_interviewed"=>"Interviewed",
			"player_impression"=>"Impression",
			"player_solec"=>"Solecismic Score",
			"player_40"=>"40 Time",
			"player_bench"=>"Bench Reps",
			"player_agil"=>"Agility",
			"player_broad"=>"Broad Jump",
			"player_pos_drill"=>"Position Drill",
			"player_score"=>"Grade",
			"player_adj_score"=>"Adjusted Grade");
    foreach($text_array as $key=>$title) {
      if ($row[$key]) {
            if ($key == "player_broad") {
              $row[$key] = height_convert($row[$key]);
            }
            $html .= '
      <tr>
        <td align="right">'.$title.':</td>
        <td><span style="'.$row[$key.'_style'].'">'.$row[$key].'</td>
      </tr>';
      }
    }	       
    $bar_array = array("Popularity"=>'player_popularity',
		       "Volatility"=>'player_vol',
		       "Developed"=>'player_developed');
    $html .= $this->draw_single_val_boxes($bar_array, $row);
		       
    $html .= '
</table>';
    $html .= '
</table>';
    return $html;
  }

  
  
function draw_staff_data(&$row) {
    //$dobmessage = $this->get_affinity($row['player_dob']);
    if ( $row['staff_curr_team_id'] ){
       $statement = "select team_name from team where team.in_game_id=".$row['staff_curr_team_id'];
       $result = mysql_query($statement);
       $row2 = mysql_fetch_array($result);
    } else
      $row2['team_name']="None";
    $html .= '<script>
function affinityMessage()
{
alert("'.$dobmessage.'");
}
</script>
<table align="center">
  <tr>
    <td align="right">Age:</td>
    <td>'.$row['staff_age'].'</a></td>
  </tr>
  <tr>
    <td align="right">Current Team:</td>
    <td>'.$row2['team_name'].'</td>
  </tr>
  <tr>
    <td align="right">Years on Contract:</td>
    <td>'.$row['staff_yrs_on_contract'].'</td>
  </tr>
  <tr>
    <td align="right">Salary:</td>
    <td>'.$row['staff_salary'].'</td>
  </tr>
  <tr>
    <td align="right">Primary Pos Group:</td>
    <td>'.$row['staff_pri_group_name'].'</td>
  </tr>';
    $text_array = array("staff_player_dev"=>"Player Development",
			"staff_young_player_dev"=>"Young Player Development",
			"staff_motivation"=>"Motivation",
			"staff_discipline"=>"Discipline",
			"staff_play_calling"=>"Play Calling",
			"staff_str_training"=>"Strength Training",
			"staff_conditioning"=>"Conditioning",
			"staff_intelligence"=>"Intelligence",
			"staff_scouting"=>"Scouting",
                        "staff_interviewing"=>"Interviewing",
			"staff_suitable_hc"=>"Suitable Head Coach",
                        "staff_suitable_oc"=>"Suitable Off. Coord",
			"staff_suitable_dc"=>"Suitable Def. Coord",
        		"staff_suitable_ac"=>"Suitable Ast. Coach",
			"staff_suitable_sc"=>"Suitable Str. Coach");
    foreach($text_array as $key=>$title) {
      if ($row[$key]) {
            if (calculate_round()==1 && (strcmp($title,"Suitable Head Coach")==0 || strcmp($title,"Young Player Development")==0 || strcmp($title,"Player Development")==0 || 
                    strcmp($title,"Motivation")==0 || strcmp($title,"Discipline")==0 || strcmp($title,"Scouting")==0 || strcmp($title,"Interviewing")==0 ) )
                $head='<div class="important_staff">';
            else if (calculate_round()==2 && (strcmp($title,"Suitable Off. Coord")==0 || strcmp($title,"Player Development")==0 || 
                    strcmp($title,"Play Calling")==0 || strcmp($title,"Scouting")==0 || strcmp($title,"Interviewing")==0 ) )
                $head='<div class="important_staff">';
            else if (calculate_round()==3 && (strcmp($title,"Suitable Def. Coord")==0 || strcmp($title,"Player Development")==0 || 
                    strcmp($title,"Play Calling")==0 || strcmp($title,"Scouting")==0 || strcmp($title,"Interviewing")==0 ) )
                $head='<div class="important_staff">';
            else if (calculate_round()==4 && (strcmp($title,"Suitable Ast. Coach")==0 || strcmp($title,"Player Development")==0 || 
                    strcmp($title,"Scouting")==0 || strcmp($title,"Interviewing")==0 ) )
                $head='<div class="important_staff">';
            else if (calculate_round()==5 && (strcmp($title,"Suitable Str. Coord")==0 || strcmp($title,"Strength Training")==0 || 
                    strcmp($title,"Conditioning")==0) )
                $head='<div class="important_staff">';
            else
                $head='<div class="staff">';
            $html .= '
      <tr>
        <td align="right">'.$head.$title.':</div></td>          
     <td>';

            $html.='
      <div class="bar">
        <div class="bar_fill" style="width: '.$row[$key].'%"></div>
      </div>'.$head.$row[$key].'</div>
    </td>
      </tr>';
      }
    }	       
/*    $bar_array = array("Popularity"=>'player_popularity',
		       "Volatility"=>'player_vol',
		       "Developed"=>'player_developed');
    $html .= $this->draw_single_val_boxes($bar_array, $row);*/
		       
    $html .= '
</table>';
    $html .= '
</table>';
    return $html;
  }
  
  
function draw_hiddenattributes(&$row) {
    if ( $row["player_intelligence"]==NULL )
        return ' ';
    $html .= '
<table align="center" padding="5">
                        <tr>
				<th align="center">Intel</th>
				<th align="center">Leader</th>
				<th align="center">Personality</th>
				<th align="center">Play4Winner</th>
				<th align="center">Loyalty</th>
			</tr>
			<tr>
                        	<td align="center">'.$row["player_intelligence"].'</td>
                                <td align="center">'.$row["player_leader"].'</td>
				<td align="center">'.$row["player_personality"].'</td>
				<td align="center">'.$row["player_winner"].'</td>
                                <td align="center">'.$row["player_loyalty"].'</td>
			</tr>
			</table>
   <table align="center">';
    $text_array = array("player_character"=>"Character Issues");
    foreach($text_array as $key=>$title) {
      if ($row[$key]) {
        if ($key == "player_character"){
            if ( $row[$key]!='0'){
                $html.= '<tr>
        <td align="right">'.$title.':</td>
        <td><span style="color: #f00; font-weight: bold">RED FLAG</td>
      </tr>';
            }
        }
        else{
            $html .= '
      <tr>
        <td align="right">'.$title.':</td>
        <td><span style="'.$row[$key.'_style'].'">'.$row[$key].'</td>
      </tr>';
          }
      }
    }
    $html .= '
</table>';
    return $html;
  }
  
  function draw_single_val_boxes($array, $row) {
    foreach($array as $title=>$id) {
      if ($row[$id]) {
	$html .= '
  <tr>
    <td align="right">'.$title.':</td>
    <td>
      <div class="bar">
        <div class="bar_fill" style="width: '.$row[$id].'%"></div>
      </div>'.$row[$id].'
    </td>
  </tr>';
      }
    }
    return $html;
  }

  function draw_attributes($position_id) {
    $position_id = mysql_real_escape_string($position_id);
    global $login;
    // See if we have imported our own data
    $statement = "select * from team_player_to_attribute where team_id = '".$login->team_id()."' limit 1";
    if (mysql_num_rows(mysql_query($statement))) {
      $uploaded = true;
    } else {
      $uploaded = false;
    }
    if ($uploaded) {
      $statement = "select * from team_player where player_id = '".$this->player_id."' and team_id = '".$login->team_id()."'";
    } else {
        $html.=' <p>As a GM you must upload output from Extractor regarding this draft class to see your staff\'s estimate for 
            player skill bar values.</p>';
      $statement = "select * from player where player_id = '".$this->player_id."'";
    }
    $html .='
<table align="center">';
    $row = mysql_fetch_array(mysql_query($statement));
    if ($row['player_current'] || $row['player_future']) {
        $aston_low = $row['score_low_'.$alias];
        $aston_high = $row['score_high_'.$alias];
        $aston_width = $aston_high - $aston_low;
        $aston_bar_style = "width: $aston_width%; margin-left: $aston_low%;";
      $html .= "
    <tr>
        <td align='right'>Rating</td>
        <td>
            <div class='aston_bar'>
                <p style='$aston_bar_style'>$aston_low - $aston_high</p>
            </div> $aston_low - $aston_high";
      /*$html .= '
  <tr>
    <td align="right">Rating</td>
    <td>
      <div class="aston_bar_background">
        <div class="aston_bar" style="width: '.$row['player_future'] - $row['player_current'].'%"></div>
      </div>'.$row['player_current'].' - '.$row['player_future'];*/
      $html .= '
    </td>
  </tr>';
    }
    $statement = "select * from attribute, position_to_attribute where
attribute.attribute_id = position_to_attribute.attribute_id and
position_to_attribute.position_id = '".$position_id."'
order by position_to_attribute_order";
    $result = mysql_query($statement);
    $i=0;
    $attributes = array();
    while ($row = mysql_fetch_array($result)) {
      $alias = "pa_$i";
      if ($uploaded) {
	$tables[] = "team_player_to_attribute $alias";
	$wheres[] = "$alias.team_id = '".$login->team_id()."'";
      } else {
	$tables[] = "player_to_attribute $alias";
      }
      $wheres[] = "$alias.player_id = '".$this->player_id."'";
      $wheres[] = "$alias.attribute_id = '".$row['attribute_id']."'";
      if ($row['attribute_id'] == 1) {
	$col[] = "$alias.player_to_attribute_low score_low_$i";
      } else {
	$col[] = "$alias.player_to_attribute_low score_low_$i";
	$col[] = "$alias.player_to_attribute_high score_high_$i";
      }
      $attributes[$i] = $row['attribute_name'];
      $i++;
    }
    $statement = "select ".implode(",",$col)." from ".implode(",",$tables)." where ".implode(" and ",$wheres);
    $result = mysql_query($statement);
    while ($row = mysql_fetch_array($result)) {
      foreach($attributes as $alias=>$name) {
        if($name == "Formations") { continue; } /* No formations */
	$html .= '
  <tr>
    <td align="right">'.$name.'</td>
    <td>
      ';
	if ($name == "Formations") {/* removing this as no player formations are imported
	  $html .= '
        <div class="bar">
        <div class="bar_fill" style="width: '.round($row['score_low_'.$alias]*100/17).'%"></div>
      </div>'.$row['score_low_'.$alias]; */
	} else {
	  /*$html .= '
        <div class="bar_light" style="width: '.$row['score_high_'.$alias].'%"></div>
        <div class="bar_fill" style="width: '.$row['score_low_'.$alias].'%"></div>
      </div>'.$row['score_low_'.$alias].'/'.$row['score_high_'.$alias];*/
      $aston_low = $row['score_low_'.$alias];
      $aston_high = $row['score_high_'.$alias];
      $aston_width = $aston_high - $aston_low;
      $aston_bar_style = "width: $aston_width%; margin-left: $aston_low%;";
      $html .= "
        <div class='aston_bar'>
        <p style='$aston_bar_style'>$aston_low - $aston_high</p>
      </div> $aston_low - $aston_high";
      
	}
	$html .= '
    </td>
  </tr>';
      }
    }
    $html .= '
</table>';
    return $html;
  }
  
  function left_nav($selection) {
    global $login;
    // If we are a selection, show the previous pick
    $html .= '
<div style="padding: 5px">';
    $link = array();
    if ($selection) {
      $statement = "select * from pick, player, position, team where
pick_id < '$selection' and
pick.player_id = player.player_id and
position.position_id = player.position_id and
team.team_id = pick.team_id
order by pick_id desc limit 1";
      $row = mysql_fetch_array(mysql_query($statement));
      if ($row['player_id']) {
	$link[] = '
<a href="show_player.php?in_game_id='.$row['player_in_game_id'].'">&laquo; Previous Pick:
'.$row['player_name'].' ('.$row['position_name'].') - '.$row['team_name'].'</a>';
      }
    }
    // If we aren't a team, this is as far as we go
    if ($login->team_id()) {
      // Previous available same position
      $statement = "select player.*, position.* from (player, position)
left join pick on pick.player_id = player.player_id
where pick.player_id is NULL and
position.position_id = player.position_id and
player.player_id < '".$this->player_id."' and
player.position_id = '".$this->position_id."'
order by player_id desc";
      $row = mysql_fetch_array(mysql_query($statement));
      if ($row['player_id']) {
	$link[] = '
<a href="show_player.php?in_game_id='.$row['player_in_game_id'].'">&laquo; Previous Available '.$row['position_name'].':
#'.$row['player_id'].' '.$row['player_name'].'</a>';
      }
      // Previous available any position
      $statement = "select player.*, position.* from (player, position)
left join pick on pick.player_id = player.player_id
where pick.player_id is NULL and
position.position_id = player.position_id and
player.player_id < '".$this->player_id."'
order by player_id desc";
      $row = mysql_fetch_array(mysql_query($statement));
      if ($row['player_id']) {
	$link[] = '
<a href="show_player.php?in_game_id='.$row['player_in_game_id'].'">&laquo; Previous Available Player:
#'.$row['player_id'].' '.$row['player_name'].' ('.$row['position_name'].')</a>';
      }
      // If in our priority queue, previous in list
      $statement = "select * from selection where team_id = '".$login->team_id()."' and player_id = '".$this->player_id."'";
      $row = mysql_fetch_array(mysql_query($statement));
      if ($row['selection_priority']) {
	$statement = "select * from selection, player, position where
team_id = '".$login->team_id()."' and
position.position_id = player.position_id and
selection_priority < '".$row['selection_priority']."' and
selection.player_id = player.player_id
order by selection_priority desc limit 1";
	$row = mysql_fetch_array(mysql_query($statement));
	if ($row['player_id']) {
	  $link[] = '
<a href="show_player.php?in_game_id='.$row['player_in_game_id'].'">&laquo; Previous In Queue (priority '.$row['selection_priority'].'):
#'.$row['player_id'].' '.$row['player_name'].' ('.$row['position_name'].')</a>';
	}
      }
    }

    $html .= implode("<br>",$link);
    $html .= '&nbsp;';
    $html .= '</div>';
    return $html;
  }

  function right_nav($selection) {
    global $login;
    // If we are a selection, show the next pick
    $html .= '
<div style="padding: 5px">';
    $link = array();
    if ($selection) {
      $statement = "select * from pick, player, position, team where
pick_id > '$selection' and
pick.player_id = player.player_id and
position.position_id = player.position_id and
team.team_id = pick.team_id
order by pick_id limit 1";
      $row = mysql_fetch_array(mysql_query($statement));
      if ($row['player_id']) {
	$link[] = '
<a href="show_player.php?in_game_id='.$row['player_in_game_id'].'">Next Pick:
'.$row['player_name'].' ('.$row['position_name'].') - '.$row['team_name'].' &raquo;</a>';
      }
    }
    // If we aren't a team, this is as far as we go
    if ($login->team_id()) {
      // Next available same position
      $statement = "select player.*, position.* from (player, position)
left join pick on pick.player_id = player.player_id
where pick.player_id is NULL and
position.position_id = player.position_id and
player.player_id > '".$this->player_id."' and
player.position_id = '".$this->position_id."'
order by player_id";
      $row = mysql_fetch_array(mysql_query($statement));
      if ($row['player_id']) {
	$link[] = '
<a href="show_player.php?in_game_id='.$row['player_in_game_id'].'">Next Available '.$row['position_name'].':
#'.$row['player_id'].' '.$row['player_name'].' &raquo;</a>';
      }
      // Next available any position
      $statement = "select player.*, position.* from (player, position)
left join pick on pick.player_id = player.player_id
where pick.player_id is NULL and
position.position_id = player.position_id and
player.player_id > '".$this->player_id."'
order by player_id";
      $row = mysql_fetch_array(mysql_query($statement));
      if ($row['player_id']) {
	$link[] = '
<a href="show_player.php?in_game_id='.$row['player_in_game_id'].'">Next Available Player:
#'.$row['player_id'].' '.$row['player_name'].' ('.$row['position_name'].') &raquo;</a>';
      }
      // If in our priority queue, next in list
      $statement = "select * from selection where team_id = '".$login->team_id()."' and player_id = '".$this->player_id."'";
      $row = mysql_fetch_array(mysql_query($statement));
      if ($row['selection_priority']) {
	$statement = "select * from selection, player, position where
team_id = '".$login->team_id()."' and
position.position_id = player.position_id and
selection_priority > '".$row['selection_priority']."' and
selection.player_id = player.player_id
order by selection_priority limit 1";
	$row = mysql_fetch_array(mysql_query($statement));
	if ($row['player_id']) {
	  $link[] = '
<a href="show_player.php?in_game_id='.$row['player_in_game_id'].'">Next In Queue (priority '.$row['selection_priority'].'):
#'.$row['player_id'].' '.$row['player_name'].' ('.$row['position_name'].') &raquo;</a>';
	}
      }
    }

    $html .= implode("<br>",$link);
    $html .= '&nbsp;';
    $html .= '</div>';
    return $html;
  }
}
?>