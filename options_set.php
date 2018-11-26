<?
/***************************************************************************
 *                                options_set.php
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

if ( $_POST['team_owner'] )
    $col[] = "team_owner = '".$_POST['team_owner']."'";
if ( strlen($_POST['team_user_link'])>3 )
    $col[] = "team_user_link = '".$_POST['team_user_link']."'";
else
    $col[] = "team_user_link = NULL";
    
if (strlen(trim($_POST['team_email']))>3 && preg_match("/[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}/", $_POST['team_email'])) {
  $col[] = "team_email = '".$_POST['team_email']."'";
  $has_email = true;
 } else {
  $col[] = "team_email = NULL";
  $has_email = false;
 }

if ($_POST['team_autopick']) {
  $col[] = "team_autopick = '1'";
 } else {
  $col[] = "team_autopick = '0'";
 }

$col[] = "team_email_prefs = '".$_POST['team_email_prefs']."'";

$col[] = "team_autopick_wait = '".$_POST['team_autopick_wait']."'";

if ($_POST['team_multipos']) {
  // This is stored kind of backwards
  $col[] = "team_multipos = '0'";
 } else {
  $col[] = "team_multipos = '1'";
 }

if (strlen(trim($_POST['team_phone']))==10 && preg_match("/[2-9]{1}\d{9}/", trim($_POST['team_phone']))) {
  $col[] = "team_phone = '".trim($_POST['team_phone'])."'";
  $has_phone = true;
 } else {
     //if they're trying to set a phonenumber give them an error message
     if (strlen(trim($_POST['team_phone']))>4){
        $_SESSION['message'] = "Phone number invalid. Must be 10 digit, no leading 1 or dashes.";
     }
  $col[] = "team_phone = NULL";
  $has_phone = false;
 }

if ( $has_phone ){
	$col[] = "team_sms_setting = '".$_POST['team_sms_setting']."'";
	$col[] = "team_carrier = '".$_POST['team_carrier']."'";
}
else{
	$col[] = "team_sms_setting = '0'";
	$col[] = "team_carrier = NULL";
}


if ($_POST['pick_method_id']) {
  // Check to make sure that if we have a scout pick that we have a mock draft
  if ($_POST['pick_method_id'] == kScoutPick) {
    $statement = "select * from mock_draft";
    if (!mysql_num_rows(mysql_query($statement))) {
      $_SESSION['message'] = "Scout pick is not available for this draft.";
      $_POST['pick_method_id'] = kPlayerQueue;
    }
  }
  $col[] = "pick_method_id = '".$_POST['pick_method_id']."'";
 }

if ($login->is_site_admin() && !$has_email) {
  // Admin must have an e-mail address
  $_SESSION['message'] = "The admin account must have an e-mail address.";
 } else {
  $statement = "update team set ".implode(",",$col)." where team_id = '".$login->team_id()."'";
  mysql_query($statement);
  if (!$_SESSION['message']) {
    $_SESSION['message'] = "Options updated.";
  }
 }
// In case we have updated the autopick setting, let's run the queue
process_pick_queue();

header("Location: options.php");
?>