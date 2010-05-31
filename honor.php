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


require_once("header.php");
require_once("libs/char_lib.php");
valid_login($action_permission['view']);

global $output, $characters_db, $realm_id, $itemperpage, $core;

$output .= "
      <div class=\"bubble\">";

$start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
$order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) :"honor";

if ( $core == 1 )
  $query = $sqlc->query("SELECT
        guid, name, race, class,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ';', ".(PLAYER_FIELD_HONOR_CURRENCY+1)."), ';', -1) AS UNSIGNED) AS honor ,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ';', ".(PLAYER_FIELD_LIFETIME_HONORBALE_KILLS+1)."), ';', -1) AS UNSIGNED) AS kills,
        level,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ';', ".(PLAYER_FIELD_ARENA_CURRENCY+1)."), ';', -1) AS UNSIGNED) AS arena,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ';', ".(PLAYER_GUILDID+1)."), ';', -1) AS UNSIGNED) AS GNAME,
        gender
        FROM `characters`
        WHERE race IN (1,3,4,7,11)
        ORDER BY $order_by DESC LIMIT 25;");
else
  $query = $sqlc->query("SELECT characters.guid, characters.name, race, class, 
        totalHonorPoints AS honor , totalKills AS kills, level, arenaPoints AS arena, 
        guildid AS GNAME, gender 
        FROM `characters`,guild_member 
        WHERE race IN (1,3,4,7,11) AND guild_member.guid = characters.guid 
        ORDER BY $order_by DESC LIMIT 25;");
  

$this_page = $sqlc->num_rows($query);

$output .= "<script type=\"text/javascript\">
  answerbox.btn_ok='".lang('global', 'yes_low')."';
  answerbox.btn_cancel='".lang('global', 'no')."';
 </script>
 <center>
 <fieldset id=\"honor_faction\">
  <legend><img src='img/alliance.gif' /></legend>

 <table class=\"lined\" id=\"honor_faction_ranks\">

  <tr class=\"bold\">
    <td colspan=\"11\">".lang('honor', 'allied')." ".lang('honor ', 'browse_honor')."</td>
  </tr>

  <tr>
    <th width=\"30%\">".lang('honor', 'guid')."</th>
    <th width=\"7%\">".lang('honor', 'race')."</th>
    <th width=\"7%\">".lang('honor', 'class')."</th>
    <th width=\"7%\">".lang('honor', 'level')."</th>
  <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">".lang('honor', 'honor')."</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">".lang('honor', 'honor points')."</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=kills\"".($order_by=='kills' ? " class=DESC" : "").">Kills</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=arena\"".($order_by=='arena' ? " class=DESC" : "").">AP</a></th>
    <th width=\"30%\">".lang('honor', 'guild')."</th>
  </tr>";

while ($char = $sqlc->fetch_assoc($query)) 
{
  if ( $core == 1 )
  {
    $guild_id = $sqlc->result($sqlc->query("SELECT guildid FROM guild_data WHERE playerid = '".$char['guid']."'"), 0);
    $guild_name = $sqlc->result($sqlc->query("SELECT guildname FROM guilds WHERE guildid = '".$guild_id."'"), 0);
  }
  else
  {
    $guild_name = $sqlc->fetch_row($sql->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char['guid'].";"));
  }
  
  $output .= " <tr>
       <td><a href=\"char.php?id=".$char['guid']."\">".htmlentities($char['name'])."</a></td>
       <td><img src='img/c_icons/".$char['race']."-".$char['gender'].".gif' onmousemove='toolTip(\"".char_get_race_name($char['race'])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
         <td><img src='img/c_icons/".$char['class'].".gif' onmousemove='toolTip(\"".char_get_class_name($char['class'])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
       <td>".char_get_level_color($char['level'])."</td>
       <td><span onmouseover='toolTip(\"".char_get_pvp_rank_name($char['honor'], char_get_side_id($char['race']))."\",\"item_tooltip\")' onmouseout='toolTip()' id='honor_tooltip'><img src='img/ranks/rank".char_get_pvp_rank_id($char['honor'], char_get_side_id($char['race'])).".gif'></span></td>
       <td>".$char['honor']."</td>
       <td>".$char['kills']."</td>
       <td>".$char['arena']."</td>
       <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=".$char['GNAME']."\">".htmlentities($guild_name)."</a></td>
       </tr>";
}

$output .= "</table><br /></fieldset>";

if ( $core == 1 )
  $query = $sqlc->query("SELECT
        guid,name,race,class,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(PLAYER_FIELD_HONOR_CURRENCY+1)."), ' ', -1) AS UNSIGNED) AS honor ,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(PLAYER_FIELD_LIFETIME_HONORBALE_KILLS+1)."), ' ', -1) AS UNSIGNED) AS kills, level,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(PLAYER_FIELD_ARENA_CURRENCY+1)."), ' ', -1) AS UNSIGNED) AS arena,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(PLAYER_GUILDID+1)."), ' ', -1) AS UNSIGNED) AS GNAME, gender
        FROM `characters`
        WHERE race NOT IN (1,3,4,7,11)
        ORDER BY $order_by DESC
        LIMIT 25;");
else
  $query = $sqlc->query("SELECT characters.guid, characters.name, race,class, 
        totalHonorPoints AS honor , totalKills AS kills, 
        level, arenaPoints AS arena, guildid AS GNAME, gender 
        FROM `characters`,guild_member WHERE race NOT IN (1,3,4,7,11) AND guild_member.guid = characters.guid 
        ORDER BY $order_by DESC LIMIT 25;");


$this_page = $sqlc->num_rows($query);

$output .= "<script type=\"text/javascript\">
  answerbox.btn_ok='".lang('global', 'yes_low')."';
  answerbox.btn_cancel='".lang('global', 'no')."';
 </script>
 <center>
 <fieldset id=\"honor_faction\">
  <legend><img src='img/horde.gif' /></legend>
 <table class=\"lined\" id=\"honor_faction_ranks\">
  <tr class=\"bold\">
    <td colspan=\"11\">".lang('honor', 'horde')." ".lang('honor ', 'browse_honor')."</td>
  </tr>

  <tr>
    <th width=\"30%\">".lang('honor', 'guid')."</th>
    <th width=\"7%\">".lang('honor', 'race')."</th>
    <th width=\"7%\">".lang('honor', 'class')."</th>
    <th width=\"7%\">".lang('honor', 'level')."</th>
  <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">".lang('honor', 'honor')."</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">".lang('honor', 'honor points')."</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=kills\"".($order_by=='kills' ? " class=DESC" : "").">Kills</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=arena\"".($order_by=='arena' ? " class=DESC" : "").">AP</a></th>
    <th width=\"30%\">".lang('honor', 'guild')."</th>
  </tr>";

while ($char = $sqlc->fetch_assoc($query)) 
{
  if ( $core == 1 )
  {
    $guild_id = $sqlc->result($sqlc->query("SELECT guildid FROM guild_data WHERE playerid = '".$char['guid']."'"), 0);
    $guild_name = $sqlc->result($sqlc->query("SELECT guildname FROM guilds WHERE guildid = '".$guild_id."'"), 0);
  }
  else
  {
    $guild_name = $sqlc->fetch_assoc($sqlc->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char['GNAME'].";"));
    $guild_name = $guild_name['name'];
  }

  $output .= " <tr>
       <td><a href=\"char.php?id=".$char['guid']."\">".htmlentities($char['name'])."</a></td>
       <td><img src='img/c_icons/".$char['race']."-".$char['gender'].".gif' onmousemove='toolTip(\"".char_get_race_name($char['race'])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
         <td><img src='img/c_icons/".$char['class'].".gif' onmousemove='toolTip(\"".char_get_class_name($char['class'])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
       <td>".char_get_level_color($char['level'])."</td>
         <td><span onmouseover='toolTip(\"".char_get_pvp_rank_name($char['honor'], char_get_side_id($char['race']))."\",\"item_tooltip\")' onmouseout='toolTip()' id='honor_tooltip'><img src='img/ranks/rank".char_get_pvp_rank_id($char['honor'], char_get_side_id($char['race'])).".gif'></span></td>
       <td>".$char['honor']."</td>
       <td>".$char['kills']."</td>
       <td>".$char['arena']."</td>
       <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=".$char['GNAME']."\">".htmlentities($guild_name)."</a></td>
       </tr>";
}

$output .= "</table><br /></fieldset>";

require_once("footer.php");
?>
