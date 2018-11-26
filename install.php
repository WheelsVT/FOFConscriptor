<?
/***************************************************************************
 *                                install.php
 *                            -------------------
 *   begin                : Friday, Mar 28, 2008
 *   copyright            : (C) 2008 J. David Baker
 *   email                : me@jdavidbaker.com
 *
 *   $Id: install.php,v 1.13 2009/11/05 22:34:20 jonbaker Exp $
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

// Don't do this if the config file already exists!
if (file_exists("includes/config.inc.php") || !is_writable("includes")) {
  header("Location: ./");
  exit;
 }

// This function attempts to install the database using the parameters passed in the POST command.
$league_page = $_POST['league_page'];
$user = $_POST['user'];
$password = $_POST['password'];
$database = $_POST['database'];
$host = $_POST['host'];
if (!mysql_connect ($host, $user, $password)) {
  $_SESSION['error'] = 'I cannot connect to the database server because: ' . mysql_error();
  header("Location: ./");
  exit;
 }
if (!mysql_select_db ($database)) {
  $_SESSION['error'] = 'I cannot connect to the database because: ' . mysql_error();
  header("Location: ./");
  exit;
 }

// Make sure we have admin info
if (!$_POST['admin_user'] || !$_POST['admin_password'] || !$_POST['admin_email']) {
  $_SESSION['error'] = "You must enter information for the admin account.";
  header("Location: ./");
  exit;
 }

// Got here, so let's run the sql script
$queries = explode(";\n", file_get_contents("includes/mysql/install.sql"));
foreach($queries as $query) {
  if ($query) {
    mysql_query($query);
    if (mysql_error()) {
      echo "<P>".$query;
      echo "<P>".mysql_error();
      exit;
    }
  }
}

// Then store the admin password
$statement = "insert into team (team_name, team_password, team_email, in_game_id)
values
('".$_POST['admin_user']."', '".md5($_POST['admin_password'])."', '".$_POST['admin_email']."', -1)";
mysql_query($statement);
$admin_user_id = mysql_insert_id();
// Populate the default colmuns, 1-12
$i=1;
while ($i<=12) {
  $statement = "insert into team_to_column (team_id, column_id, team_to_column_order)
values ('$admin_user_id', '$i', '$i')";
  mysql_query($statement);
  $i++;
 }


// Now create the config.inc.php file
$config = '<?
$user = "'.$user.'";
$password = "'.$password.'";
$database = "'.$database.'";
$host = "'.$host.'";
$league_page = "'.$league_page.'";
mysql_connect($host, $user, $password) or die ("I cannot connect to the database server because: ".mysql_error());
mysql_select_db($database) or die ("I cannot connect to the database because: ".mysql_error());
define (kAdminUser, \''.$admin_user_id.'\');
?>';
file_put_contents("includes/config.inc.php", $config);

$_SESSION['message'] = "Installation successful!";

$_SESSION['fof_draft_login_team_name'] = $_POST['admin_user'];
$_SESSION['fof_draft_login_team_password'] = md5($_POST['admin_password']);
header("Location: ./import_draft.php");
exit;
?>