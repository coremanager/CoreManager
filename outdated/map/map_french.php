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
  1 => 'Humain',
  2 => 'Orc',
  3 => 'Nain',
  4 => 'Elfe de la Nuit',
  5 => 'Mort-vivant',
  6 => 'Tauren',
  7 => 'Gnome',
  8 => 'Troll',
  9 => 'Goblin',
  10 => 'Elfe de sang',
  11 => 'Draenaï');

$character_class = Array(
  1 => 'Guerrier',
  2 => 'Paladin',
  3 => 'Chasseur',
  4 => 'Voleur',
  5 => 'Prêtre',
  6 => 'Chevalier de la mort',
  7 => 'Chaman',
  8 => 'Mage',
  9 => 'Démoniste',
  11 => 'Druide');

$lang_defs = Array(
  'maps_names' => Array('Azeroth','Outreterre','Norfendre'),
  'total' => 'Total',
  'faction' => Array('Alliance', 'Horde'),
  'name' => 'Nom',
  'race' => 'Race',
  'class' => 'Classe',
  'level' => 'lvl',
  'click_to_next' => 'Click: go to next',
  'click_to_first' => 'Click: go to first'
);

include "zone_names_".$lang.".php";
?>
