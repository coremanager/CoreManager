<?php
/*
    ArcManager, PHP Front End for ArcEmu
    Copyright (C) 2009-2010  ArcManager Project

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

$character_race = Array(
  1 => 'Human',
  2 => 'Orc',
  3 => 'Dwarf',
  4 => 'Night Elf',
  5 => 'Undead',
  6 => 'Tauren',
  7 => 'Gnome',
  8 => 'Troll',
  9 => 'Goblin',
  10 => 'Blood Elf',
  11 => 'Draenai');

$character_class = Array(
  1 => 'Warrior',
  2 => 'Paladin',
  3 => 'Hunter',
  4 => 'Rogue',
  5 => 'Priest',
  6 => 'Death Knight',
  7 => 'Shaman',
  8 => 'Mage',
  9 => 'Warlock',
  11 => 'Druid');

$lang_defs = Array(
  'maps_names' => Array('Azeroth','Outland','Northrend'),
  'total' => 'Total',
  'faction' => Array('Alliance', 'Horde'),
  'name' => 'Name',
  'race' => 'Race',
  'class' => 'Class',
  'level' => 'Level',
  'click_to_next' => 'Click: go to next',
  'click_to_first' => 'Click: go to first'
);

include "zone_names_".$lang.".php";
?>
