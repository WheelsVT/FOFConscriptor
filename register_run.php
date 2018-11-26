<?
/***************************************************************************
 *                                register_run.php
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

session_name('FOFCONSCRIPTOR');
session_start();
include "includes/config.inc.php";

// Process the registration
if (!$_POST['team_password']) {
  $_SESSION['message'] = "You did not enter a password.";
  header("Location: register.php");
  exit;
 }
$col[] = "team_password = '".md5($_POST['team_password'])."'";
if (preg_match("/[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}/", $_POST['team_email'])) {
  $col[] = "team_email = '".$_POST['team_email']."'";
 }
if ($_POST['team_owner']) {
  $col[] = "team_owner = '".$_POST['team_owner']."'";
 }
if (strlen($_POST['team_phone'])==10 && preg_match("/[2-9]{1}\d{8}/", $_POST['team_phone'])) {
  $col[] = "team_phone = '".$_POST['team_phone']."'";
 }
if ($_POST['team_carrier']) {
  $col[] = "team_carrier = '".$_POST['team_carrier']."'";
 }
$col[] = "team_clock_adj = '1'";
$col[] = "team_autopick = '0'";
$statement = "update team set ".implode(",",$col)." where team_name like '".$_POST['team_name']."' and
team_password is NULL";
mysql_query($statement);
echo mysql_error();
if (mysql_affected_rows() > 0) {
  $_SESSION['message'] = "Account created successfully.";
  $_SESSION['fof_draft_login_team_name'] = $_POST['team_name'];
  $_SESSION['fof_draft_login_team_password'] = md5($_POST['team_password']);
  if ($_POST['save_login']) {
    setcookie("fof_draft_login_team_name", $_POST['team_name'], strtotime("+30 days"));
    setcookie("fof_draft_login_team_password", md5($_POST['team_password']), strtotime("+30 days"));    
  }
  header("Location: options.php");
 } else {
  $_SESSION['message'] = "Account creation failed.  Either the team name does not exist or it is already registered.";
  header("Location: register.php");
 }
?>