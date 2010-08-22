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
	1 => 'Человек',
	2 => 'Орк',
	3 => 'Дворф',
	4 => 'Ночной эльф',
	5 => 'Нежить',
	6 => 'Таурен',
	7 => 'Гном',
	8 => 'Тролль',
	9 => 'Гоблин',
	10 => 'Эльф крови',
	11 => 'Дреней');

$character_class = Array(
	1 => 'Воин',
	2 => 'Паладин',
	3 => 'Охотник',
	4 => 'Разбойник',
	5 => 'Жрец',
	6 => 'Рыцарь смерти',
	7 => 'Шаман',
	8 => 'Маг',
	9 => 'Чернокнижник',
	11 => 'Друид');

$lang_defs = Array(
	'maps_names' => Array('Азерот','Запределье','Нордскол'),
	'total' => 'Всего',
	'faction' => Array('Альянс', 'Орда'),
	'name' => 'Имя',
	'race' => 'Расса',
	'class' => 'Класс',
	'level' => 'ур',
	'click_to_next' => 'жми кнопку',
	'click_to_first' => 'жми кнопку'
);

include "zone_names_".$lang.".php";

?>