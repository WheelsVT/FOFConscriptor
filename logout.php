<?
/***************************************************************************
 *                                logout.php
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
session_destroy();
// Clear any stored cookies
setcookie("fof_draft_login_team_name", "", time() - 3600);
setcookie("fof_draft_login_team_password", "", time() - 3600);
header("Location: ./");
exit;
?>