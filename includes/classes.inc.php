<?
/***************************************************************************
 *                                classes.inc.php
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

define (kVersion, '2.8.1');
define (kYear, '2017');

// Classes
include "includes/classes/page.inc.php";
include "includes/classes/login.inc.php";
include "includes/classes/list.inc.php";
include 'includes/classes/pick.inc.php';
include 'includes/classes/team.inc.php';
include "includes/classes/pick_method.inc.php";
include "includes/classes/position.inc.php";
include "includes/classes/settings.inc.php";
include "includes/classes/player.inc.php";
include "includes/classes/widget.inc.php";
// Functions
include "includes/functions.inc.php";

// In PHP 5.2 or higher we don't need to bring this in
if (!function_exists('json_encode')) {
  require_once 'includes/jsonwrapper_inner.php';
 } 

// Connection to the database, install if the file does not exist yet
if (!file_exists("includes/config.inc.php")) {
  $page = new page("install");
  echo $page->draw();
  exit;
 } else {
  include "includes/config.inc.php";
 }

// Encrypt legacy password
encrypt_passwords();

$settings = new settings();

$time_zone = $settings->get_value(kSettingTimeZone);
if ($time_zone) {
  $statement = "select * from time_zone where time_zone_id = '$time_zone'";
  $row = mysql_fetch_array(mysql_query($statement));
  putenv("TZ=".$row['time_zone_php']);
  /*
    // Taking out the MySQL time zone, we have removed all "now()" instances
  // If the server is set to DST, it will automatically convert XST to XDT, so we'll need to
  // re-load it for mysql
  $statement = "select * from time_zone where time_zone_php = '".date("T")."'";
  $row = mysql_fetch_array(mysql_query($statement));
  if ($row['time_zone_mysql']) {
    mysql_query("set time_zone = '".$row['time_zone_mysql']."'");
  } else {
    $_SESSION['message'] = "WARNING: Bad time zone!!!";
  }
  */
}

$login = new login();

process_expired_picks();
?>