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


$maxqueries = 20; // Max topic / post by pages
$minfloodtime = 15; // Minimum time beetween two post
$enablesidecheck = true; // if you dont use side specific forum, desactive it, because it will do one less query.

$forum_skeleton = Array(
	1 => Array(
		"name" => "servcat",
		"forums" => Array(
			1 => Array(
				"name" => "news",
				"desc" => "newsdesc",
				"level_post_topic" => 3
			),
			2 => Array(
				"name" => "general",
				"desc" => "generaldesc"
			)
		)
	),
	2 => Array(
		"name" => "gamecat",
		"forums" => Array(
			3 => Array(
				"name" => "bugs",
				"desc" => "bugsdesc",
			),
			4 => Array(
				"name" => "both",
				"desc" => "bothdesc"
			),
			5 => Array(
				"name" => "horde",
				"desc" => "Only horde players can see this",
				"side_access" => "H"
			),
			6 => Array(
				"name" => "alliance",
				"desc" => "alliancedesc",
				"side_access" => "A"
			),
			7 => Array(
				"name" => "admin",
				"desc" => "admindesc",
				"level_read" => "3",
				"level_post" => "3"
			)
		)
	)
);
?>
