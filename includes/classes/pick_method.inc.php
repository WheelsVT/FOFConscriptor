<?
/***************************************************************************
 *                                pick_method.inc.php
 *                            -------------------
 *   begin                : Monday, Apr 7, 2008
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

define (kPlayerQueue, 1);
define (kBPAQueue, 2);
define (kScoutPick, 3);
define (kPlayerThenBPA, 4);

class pick_method {
  function pick_method($pick_method_id) {
    $statement = "select * from pick_method where pick_method_id = '".mysql_real_escape_string($pick_method_id)."'";
    $this->data = mysql_fetch_array(mysql_query($statement));
  }

  function option_list() {
    $statement = "select * from pick_method order by pick_method_id";
    $result = mysql_query($statement);
    while($row = mysql_fetch_array($result)) {
      if ($row['pick_method_id'] == $this->data['pick_method_id']) {
	$selected = " selected";
      } else {
	$selected = "";
      }
      $html .= '
<option value="'.$row['pick_method_id'].'"'.$selected.'>'.$row['pick_method_name'].'</option>';
    }
    return $html;
  }
}
?>