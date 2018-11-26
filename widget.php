<?
/***************************************************************************
 *                                widget.php
 *                            -------------------
 *   begin                : Monday, May 26, 2008
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

$html = file_get_contents("includes/html/widget.html");
$html = str_replace("%version%", kVersion, $html);
$html = str_replace("%year%", kYear, $html);
$html = str_replace("%league%", $settings->get_value(kSettingLeagueName), $html);
$widget = new widget();
$html = str_replace("%content%", $widget->draw(), $html);
echo $html;
?>