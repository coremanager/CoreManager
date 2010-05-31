<?php
/*
    CoreManager, PHP Front End for ArcEmu, MaNGOS, and TrinityCore
    Copyright (C) 2010  CoreManager Project

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


//#############################################################################
//get map name by its id

function get_map_name($id, &$sqld)
{
  $map_name = $sqld->fetch_assoc($sqld->query("SELECT name FROM map WHERE id=".$id." LIMIT 1"));
  return $map_name['name'];
}


//#############################################################################
//get zone name by its id

function get_zone_name($id, &$sqld)
{
  //This table does not exist on dbc files, it was taken from CSWOWD
  $zone_name = $sqld->fetch_assoc($sqld->query('SELECT name FROM areatable WHERE id='.$id.' LIMIT 1'));
  return $zone_name['name'];
}


?>
