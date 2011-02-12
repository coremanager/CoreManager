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

require_once 'header.php';
require_once 'libs/char_lib.php';
require_once("libs/map_zone_lib.php");
valid_login($action_permission["view"]);

//########################################################################################################################
//  CHAR TOOLS FORM
//########################################################################################################################
function char_tools_form()
{
  global $output, $characters_db, $realm_id, $action_permission, $site_encoding, $showcountryflag,
    $sql;

  valid_login($action_permission["delete"]);

  if ( isset($_GET["char"]) )
    $id = $_GET["char"];
  else
    error(lang("global", "empty_fields"));

  if ( $core == 1 )
  {
    $result = $sql["char"]->query("SELECT guid, name, race, class, level, zoneid, mapid, online, gender
      acct, data 
      FROM characters WHERE guid='".$id."'");
  }
  elseif ( $core == 2 )
  {
    $result = $sql["char"]->query("SELECT guid, name, race, class, level, zone AS zoneid, map AS mapid, 
      online, gender, totaltime, account AS acct,
      arenaPoints, totalHonorPoints, totalKills
      FROM characters WHERE guid='".$id."'");
  }
  else
  {
    $result = $sql["char"]->query("SELECT guid, name, race, class, level, zone AS zoneid, map AS mapid, 
      online, gender, totaltime, account AS acct, arenaPoints, totalHonorPoints, totalKills
      FROM characters WHERE guid='".$id."'");
  }
  $char = $sql["char"]->fetch_assoc($result);

  if ( $core == 1 )
  {
    $char_data = $char["data"];
    if ( empty($char_data) )
      $char_data = str_repeat("0;", PLAYER_END);
    $char_data = explode(";",$char_data);
  }
  else
  {
    $query = "SELECT * FROM characters
                LEFT JOIN character_stats ON characters.guid=character_stats.guid
              WHERE characters.guid='".$id."'";
    $char_data_result = $sql["char"]->query($query);
    $char_data_fields = $sql["char"]->fetch_assoc($char_data_result);

    $char_data[PLAYER_FIELD_HONOR_CURRENCY] = ( ( isset($char["totalHonorPoints"]) ) ? $char["totalHonorPoints"] : '&nbsp;' );
    $char_data[PLAYER_FIELD_ARENA_CURRENCY] = ( ( isset($char["arenaPoints"]) ) ? $char["arenaPoints"] : '&nbsp;' );
    $char_data[PLAYER_FIELD_LIFETIME_HONORBALE_KILLS] = ( ( isset($char["totalKills"]) ) ? $char["totalKills"] : '&nbsp;' );
  }

  if ( $core == 1 )
  {
    $guild_id = $sql["char"]->result($sql["char"]->query("SELECT guildid FROM guild_data WHERE playerid='".$char["guid"]."'"), 0);
    $guild_rank = $sql["char"]->result($sql["char"]->query("SELECT guildRank FROM guild_data WHERE playerid='".$char["guid"]."'"), 0);
    $guild_name = $sql["char"]->result($sql["char"]->query("SELECT guildName FROM guilds WHERE guildid='".$guild_id."'"));
  }
  else
  {
    $guild_id = $sql["char"]->result($sql["char"]->query("SELECT guildid FROM guild_member WHERE guid='".$char["guid"]."'"), 0);
    $guild_rank = $sql["char"]->result($sql["char"]->query("SELECT rank AS guildRank FROM guild_member WHERE guid='".$char["guid"]."'"), 0);
    $guild_name = $sql["char"]->result($sql["char"]->query("SELECT name AS guildName FROM guild WHERE guildid='".$guild_id."'"));
  }

  $online = ( ( $char["online"] ) ? lang("char", "online") : lang("char", "offline") );

  if ( $guild_id )
  {
    $guild_name = '<a href="guild.php?action=view_guild&amp;realm='.$realmid.'&amp;error=3&amp;id='.$guild_id.'" >'.$guild_name.'</a>';
    $mrank = $guild_rank;
    if ( $core == 1 )
      $guild_rank = $sql["char"]->result($sql["char"]->query('SELECT rankname FROM guild_ranks WHERE guildid='.$guild_id.' AND rankId='.$mrank.''), 0, 'rankname');
    else
      $guild_rank = $sql["char"]->result($sql["char"]->query('SELECT rname AS rankname FROM guild_rank WHERE guildid='.$guild_id.' AND rid='.$mrank.''), 0, 'rankname');
  }
  else
  {
    $guild_name = lang("global", "none");
    $guild_rank = lang("global", "none");
  }

  $output .= '
          <center>
            <table class="hidden char_list_char_tools">
              <tr>
                <td class="char_tools_avatar">
                  <div>
                    <img src="'.char_get_avatar_img($char["level"], $char["gender"], $char["race"], $char["class"], 0).'" alt="avatar" />
                  </div>
                </td>
                <td colspan="3">
                  <font class="bold">
                    '.htmlentities($char["name"], ENT_COMPAT, $site_encoding).' -
                    <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                    <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                   - '.lang("char", "level_short").char_get_level_color($char["level"]).'
                  </font>
                  <br />'.lang("char", "location").': '.get_map_name($char["mapid"]).' - '.get_zone_name($char["zoneid"]).'
                  <br />'.lang("char", "honor_points").': '.$char_data[PLAYER_FIELD_HONOR_CURRENCY].' | '.lang("char", "arena_points").': '.$char_data[PLAYER_FIELD_ARENA_CURRENCY].' | '.lang("char", "honor_kills").': '.$char_data[PLAYER_FIELD_LIFETIME_HONORBALE_KILLS].'
                  <br />'.lang("char", "guild").': '.$guild_name.' | '.lang("char", "rank").': '.htmlentities($guild_rank, ENT_COMPAT, $site_encoding).'
                  <br />'.lang("char", "online").': '.( ( $char["online"] ) ? '<img src="img/up.gif" onmousemove="oldtoolTip(\''.lang("char", "online").'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="online" />' : '<img src="img/down.gif" onmousemove="oldtoolTip(\''.lang("char", "offline").'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="offline" />' );
  if ( $showcountryflag )
  {
    require_once 'libs/misc_lib.php';
    $country = misc_get_country_by_account($char["acct"]);
    $output .= ' | '.lang("global", "country").': '.( ( $country["code"] ) ? '<img src="img/flags/'.$country["code"].'.png" onmousemove="oldtoolTip(\''.($country["country"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />' : '-' );
    unset($country);
  }
  $output .= '
                </td>
              </tr>
            </table>
            <br />
            <table class="hidden char_list_char_tools">
              <tr>
                <td>';
  makebutton(lang("xname", "changename"), "char_tools.php?char=".$id, 150);
  $output .= '
                </td>
                <td>';
  makebutton(lang("xrace", "changerace"), "char_tools.php?char=".$id, 150);
  $output .= '
                </td>
                <td>';
  makebutton(lang("unstuck", "unstuck"), "hearthstone.php?action=approve&char=".$id, 150);
  $output .= '
                </td>
              </tr>
              <tr>
                <td>';
  makebutton(lang("char_list", "transfer"), "change_char_account.php?action=chooseacct&priority=1&char=".$id, 150);
  $output .= '
                </td>
                <td>';
  makebutton(lang("global", "back"), "char_list.php", 150);
  $output .= '
                </td>
              </tr>
            </table>
          </center>';
}


//########################################################################################################################
// MAIN
//########################################################################################################################

$err = ( ( isset($_GET["error"]) ) ? $_GET["error"] : NULL );

$output .= '
      <div class="bubble">
        <div class="top">';

switch ( $err )
{
  default:
    $output .= '
          <h1>'.lang("char", "char_tools").'</h1>';
}

unset($err);

$output .= '
        </div>';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

char_tools_form();

require_once("footer.php");

?>