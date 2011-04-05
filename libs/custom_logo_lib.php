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


// NOTE: THIS IS NOT A NORMAL LIB FILE.
//
// Do not attempt to pre-load it with require/include.
// It is intended to be used as the src for an <img> tag.

require_once "../configs/config.php";
require_once "../libs/config_lib.php";

$logo_query = "SELECT * FROM custom_logos WHERE id='".$custom_logo."'";
$logo_result = $sqlm->query($logo_query);

$image = $sqlm->fetch_assoc($logo_result);

header("Content-type: ".$image["mime_type"]);
header("Content-length: ".$image["file_size"]);

echo $image["file_data"];

?>
