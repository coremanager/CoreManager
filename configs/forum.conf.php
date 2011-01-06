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


$maxqueries = 20; // Max topic / post by pages
$minfloodtime = 15; // Minimum time beetween two post
$enablesidecheck = true; // if you dont use side specific forum, desactive it, because it will do one less query.

//#############################################################################
//
// DO NOT CHANGE ANYTHING AFTER THIS POINT
//
//#############################################################################

$forum_array = array();
$temp = $sql['mgr']->query('SELECT * FROM config_forum_categories');
while ( $fcats = $sql['mgr']->fetch_assoc($temp) )
{
  $cat = array();
  $cat[0] = $fcats['Index'];
  $cat[1] = $fcats['Name'];

  $m = array();
  $temp_forums = $sql['mgr']->query("SELECT * FROM config_forums WHERE Category='".$fcats['Index']."'");
  while ( $forums = $sql['mgr']->fetch_assoc($temp_forums) )
  {
    $forum = array();
    array_push($forum, $forums['Index']);
    array_push($forum, $forums['Name']);
    array_push($forum, $forums['Desc']);
    array_push($forum, $forums['Side_Access']);
    array_push($forum, $forums['Min_Security_Level_Read']);
    array_push($forum, $forums['Min_Security_Level_Post']);
    array_push($forum, $forums['Min_Security_Level_Create_Topic']);
    array_push($m, $forum);
  }

  $cat[2] = $m;

  array_push($forum_array, $cat);
}

$forum_skeleton = array();
foreach ( $forum_array as $category )
{
  $cat = array();
  $cat['name'] = $category[1];
  $cat['forums'] = array();
  foreach ( $category[2] as $forums )
  {
    $cat['forums'][$forums[0]]['name'] = $forums[1];
    $cat['forums'][$forums[0]]['desc'] = $forums[2];
    $cat['forums'][$forums[0]]['side_access'] = $forums[3];
    $cat['forums'][$forums[0]]['level_read'] = $forums[4];
    $cat['forums'][$forums[0]]['level_post'] = $forums[5];
    $cat['forums'][$forums[0]]['level_post_topic'] = $forums[6];
  }
  
  array_push($forum_skeleton, $cat);
}

?>
