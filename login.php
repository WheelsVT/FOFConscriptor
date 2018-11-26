<?
/***************************************************************************
 *                                login.php
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
$_SESSION['fof_draft_login_team_name'] = $_POST['team_name'];
$_SESSION['fof_draft_login_team_password'] = md5($_POST['team_password']);
if ($_POST['save_login']) {
  setcookie("fof_draft_login_team_name", $_POST['team_name'], strtotime("+30 days"));
  setcookie("fof_draft_login_team_password", md5($_POST['team_password']), strtotime("+30 days"));
}
header("Location: ./selections.php");
exit;
?>