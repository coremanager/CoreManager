<?php
/*
    CoreManager, PHP Front End for ArcEmu, MaNGOS, and TrinityCore
    Copyright (C) 2010-2011  CoreManager Project

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once("Sulfur_config.php");

// set the header information
// will be output as css
header("Content-type: text/css");
// set an expiration date
$days_to_cache = 10;
header("Expires: ".gmdate("D, d M Y H:i:s", time() + (60 * 60 * 24 * $days_to_cache))." GMT");

// load tokenized stylesheet
$stylesheet = @file_get_contents("Sulfur_1024.css");

// process tokens
foreach ( $tokens as $token )
{
  $stylesheet = str_replace($token[0], $token[1], $stylesheet);
}

echo $stylesheet;

?>