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


//#############################################################################
//get skill type by its id

function skill_get_type($id)
{
  global $sql;

  //This table came from CSWOWD as its fields are named
  $skill_type = $sql["dbc"]->fetch_assoc($sql["dbc"]->query("SELECT SkillLineCategory FROM skillline WHERE id='".$id."' LIMIT 1"));
  return $skill_type["SkillLineCategory"];
}


//#############################################################################
//get skill name by its id

function skill_get_name($id)
{
  global $sql;

  //This table came from CSWOWD as its fields are named
  $skill_name = $sql["dbc"]->fetch_assoc($sql["dbc"]->query("SELECT Name FROM skillline WHERE id='".$id."' LIMIT 1"));
  return $skill_name["Name"];
}


?>
