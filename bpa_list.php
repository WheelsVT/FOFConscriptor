<?
/***************************************************************************
 *                                bpa_list.php
 *                            -------------------
 *   begin                : Friday, May 9, 2008
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

include 'includes/classes.inc.php';
$position_id = $_GET['position_id'];
$statement = "select * from position_to_attribute, attribute where
position_to_attribute.position_id = '$position_id' and
position_to_attribute.attribute_id = attribute.attribute_id
order by position_to_attribute_order";
$result = mysql_query($statement);
$html .= '
<select name="attribute_id">';
// Do we have adjusted grade?
$statement = "select * from player where player_adj_score is not null";
if (mysql_num_rows(mysql_query($statement))) {
  $html .= '
<option value="-1">Adjusted Grade</option>';
}
echo mysql_error();
// Do we have ratings?
$statement = "select * from player where player.player_future is not NULL";
if (mysql_num_rows(mysql_query($statement))) {
  $html .= '
<option value="-2">Future Rating</option>
<option value="-3">Current Rating</option>';
}
while ($row = mysql_fetch_array($result)) {
  $html .= '
  <option value="'.$row['attribute_id'].'">'.$row['attribute_name'].'</option>';
 }
$html .= '
</select>';
echo $html;
?>