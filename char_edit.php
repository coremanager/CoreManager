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


require_once "header.php";
require_once "libs/char_lib.php";
require_once "libs/item_lib.php";
require_once "libs/map_zone_lib.php";
valid_login($action_permission["delete"]);

//########################################################################################################################
//  PRINT  EDIT FORM
//########################################################################################################################
function edit_char()
{
  global $output, $logon_db, $characters_db, $realm_id, $corem_db, 
    $action_permission, $user_lvl, $item_datasite, $core, $sql;

  wowhead_tt();

  valid_login($action_permission["delete"]);
  if ( empty($_GET["id"]) )
    error(lang("global", "empty_fields"));

  $id = $sql["char"]->quote_smart($_GET["id"]);

  if ( $core == 1 )
    $result = $sql["char"]->query("SELECT acct FROM `characters` WHERE guid='".$id."'");
  else
    $result = $sql["char"]->query("SELECT account AS acct FROM `characters` WHERE guid='".$id."'");

  if ( $sql["char"]->num_rows($result) )
  {
    $char = $sql["char"]->fetch_assoc($result);

    // we get user permissions first
    $owner_acc_id = $sql["char"]->result($result, 0, "acct");

    if ( $core == 1 )
      $result = $sql["logon"]->query("SELECT login FROM accounts WHERE acct='".$char["acct"]."'");
    else
      $result = $sql["logon"]->query("SELECT username AS login FROM account WHERE id='".$char["acct"]."'");

    $owner_name = $sql["logon"]->result($result, 0, "login");
      
    $sec_res = $sql["mgr"]->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_name."'");
    $owner_gmlvl = $sql["mgr"]->result($sec_res, 0, "gm");

    if ( $user_lvl >= $owner_gmlvl )
    {
      if ( $core == 1 )
        $query = "SELECT guid, acct, data, name, race, class, positionx, positiony,
                  mapid, online, SUBSTRING_INDEX(SUBSTRING_INDEX(playedtime, ' ', 2),' ', -1) AS totaltime,
                  positionz, zoneid, level, gender
                  FROM `characters` WHERE guid='".$id."'";
      else
        $query = "SELECT guid, account AS acct, guid AS data, name, race, class, position_x AS positionx, position_y AS positiony,
                  map AS mapid, online, totaltime,
                  position_z AS positionz, zone AS zoneid, level, gender, totalHonorPoints, arenaPoints, totalKills, money
                  FROM `characters` WHERE guid='".$id."'";
      $result = $sql["char"]->query($query);
      $char = $sql["char"]->fetch_assoc($result);

      if ( $core == 1 )
        $char_data = explode(';', $char["data"]);
      else
      {
        $char_data[PLAYER_FIELD_COINAGE] = ( ( isset($char["money"]) ) ? $char["money"] : 0 );
        $char_data[PLAYER_FIELD_HONOR_CURRENCY] = ( ( isset($char["totalHonorPoints"]) ) ? $char["totalHonorPoints"] : 0 );
        $char_data[PLAYER_FIELD_ARENA_CURRENCY] = ( ( isset($char["arenaPoints"]) ) ? $char["arenaPoints"] : 0 );
        $char_data[PLAYER_FIELD_LIFETIME_HONORBALE_KILLS] = ( ( isset($char["totalKills"]) ) ? $char["totalKills"] : 0 );
      }

      if ( $char["online"] )
        $online = '<font class="error">'.lang("char", "edit_offline_only_char").'</font>';
      else
        $online = lang("char", "offline");

      if ( $core == 1 )
      {
        $char_data[PLAYER_GUILDID] = $sql["char"]->result($sql["char"]->query("SELECT guildid FROM guild_data WHERE playerid='".$char["guid"]."'"), 0);
        $char_data[PLAYER_GUILDRANK] = $sql["char"]->result($sql["char"]->query("SELECT guildRank FROM guild_data WHERE playerid='".$char["guid"]."'"), 0);
        $guild_name = $sql["char"]->result($sql["char"]->query("SELECT guildName FROM guilds WHERE guildid='".$guild_id."'"));
      }
      else
      {
        $char_data[PLAYER_GUILDID] = $sql["char"]->result($sql["char"]->query("SELECT guildid FROM guild_member WHERE guid='".$char["guid"]."'"), 0);
        $char_data[PLAYER_GUILDRANK] = $sql["char"]->result($sql["char"]->query("SELECT rank AS guildRank FROM guild_member WHERE guid='".$char["guid"]."'"), 0);
        $guild_name = $sql["char"]->result($sql["char"]->query("SELECT name AS guildName FROM guild WHERE guildid='".$char_data[PLAYER_GUILDID]."'"));
      }

      if ( $char_data[PLAYER_GUILDID] )
      {
        if ( $user_lvl > 0 )
          $guild_name = '<a href="guild.php?action=view_guild&amp;error=3&amp;id='.$char_data[PLAYER_GUILDID].'" >'.$guild_name.'</a>';
        if ( $char_data[PLAYER_GUILDRANK] )
        {
          if ( $core == 1 )
            $guild_rank_query = $sql["char"]->query("SELECT rankname A rname FROM guild_ranks WHERE guildid='".$char_data[PLAYER_GUILDID]."' AND rid='".$char_data[PLAYER_GUILDRANK]."'");
          else
            $guild_rank_query = $sql["char"]->query("SELECT rname FROM guild_rank WHERE guildid='".$char_data[PLAYER_GUILDID]."' AND rid='".$char_data[PLAYER_GUILDRANK]."'");
          $guild_rank = $sql["char"]->result($guild_rank_query, 0, "rname");
        }
        else
          $guild_rank = lang("char", "guild_leader");
      }
      else 
      {
        $guild_name = lang("global", "none");
        $guild_rank = lang("global", "none");
      }

      $output .= '
            <!-- start of char_edit.php -->
            <center>
              <form method="get" action="char_edit.php" name="form">
                <input type="hidden" name="action" value="do_edit_char" />
                <input type="hidden" name="id" value="'.$id.'" />
                <table class="lined">
                  <tr>
                    <td colspan="8">
                      <font class="bold">'.$char["name"].' - <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" /> <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" /> - '.lang("char", "level_short").char_get_level_color($char["level"]).'</font>
                      <br />
                      <span>'.lang("char", "location").': '.get_map_name($char["mapid"]).' - '.get_zone_name($char["zoneid"]).'</span>
                      <br />
                      <span>'.lang("char", "honor_points").': '.$char_data[PLAYER_FIELD_HONOR_CURRENCY].' | '.lang("char", "arena_points").': '.$char_data[PLAYER_FIELD_ARENA_CURRENCY].' | '.lang("char", "honor_kills").': '.$char_data[PLAYER_FIELD_LIFETIME_HONORBALE_KILLS].'</span>
                      <br />
                      <span>'.lang("char", "guild").': '.$guild_name.' | '.lang("char", "rank").': '.$guild_rank.'</span>
                      <br />
                      <span>'.lang("char", "online").': '.( ( $char["online"] ) ? '<img src="img/up.gif" onmousemove="oldtoolTip(\''.lang("char", "online").'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="online" />' : '<img src="img/down.gif" onmousemove="oldtoolTip(\''.lang("char", "offline").'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="offline" />' ).'</span>
                    </td>
                  </tr>';

      if ( $char["online"] )
        $output .= '
                  <tr>
                    <td colspan="8">
                      <font class="bold">'.$online.'</font>
                    </td>
                  </tr>';
      else
      {
        $output .= '
                <tr>
                  <td colspan="4">'.lang("char", "name").': <input type="text" name="cname" size="15" maxlength="12" value="'.$char["name"].'" /></td>
                  <td colspan="4">'.lang("char", "gold").': <input type="text" name="money" size="10" maxlength="10" value="'.$char_data[PLAYER_FIELD_COINAGE].'" /></td>
                </tr>
                <tr>
                  <tr>
                    <td colspan="2">'.lang("char", "honor_points").': <input type="text" name="honor_points" size="8" maxlength="6" value="'.$char_data[PLAYER_FIELD_HONOR_CURRENCY].'" /></td>
                    <td colspan="2">'.lang("char", "arena_points").': <input type="text" name="arena_points" size="8" maxlength="6" value="'.$char_data[PLAYER_FIELD_ARENA_CURRENCY].'" /></td>
                    <td colspan="4">'.lang("char", "honor_kills").': <input type="text" name="total_kills" size="8" maxlength="6" value="'.$char_data[PLAYER_FIELD_LIFETIME_HONORBALE_KILLS].'" /></td>
                  </tr>
                </table>';
      }
      $output .= '
                <br />
                <table class="hidden">
                  <tr>';
      if ( !$char["online"] )
      {
        $output .= '
                    <td>';
        makebutton(lang("char", "update"), 'javascript:do_submit()', 190);
        $output .= '
                    </td>';
      }
        $output .= '
                    <td>';
        makebutton(lang("char", "to_char_view"), 'char.php?id='.$id, 160);
        $output .= '
                    </td>
                    <td>';
        makebutton(lang("char", "del_char"), 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 160);
        $output .= '
                    </td>
                    <td>';
        makebutton(lang("global", "back"), 'javascript:window.history.back()', 160);
        $output .= '
                    </td>
                  </tr>
                </table>
                <br />
              </form>
            </center>';
    }
    else
      ;
  }
  else
    error(lang("char", "no_char_found"));
}


//########################################################################################################################
//  DO EDIT CHARACTER
//########################################################################################################################
function do_edit_char()
{
  global $output, $logon_db, $characters_db, $realm_id, $action_permission,
    $user_lvl, $world_db, $sql, $core;

  valid_login($action_permission["delete"]);

  if ( empty($_GET["id"]) )
    error($lang_global["empty_fields"]);

  $id = $sql["char"]->quote_smart($_GET["id"]);

  if ( !is_numeric($id) )
    error(lang("char", "use_numeric"));

  if ( $core == 1 )
    $query = "SELECT acct AS account, online FROM `characters` WHERE guid='".$id."'";
  else
    $query = "SELECT account, online FROM `characters` WHERE guid='".$id."'";

  $result = $sql["char"]->query($query);

  $online = $sql["char"]->result($result, 0, "online");

  if ( $sql["char"]->num_rows($result) )
  {
    // we cannot edit online chars
    if ( !$online )
    {
      //resrict by owner's gmlvl
      $owner_acc_id = $sql["char"]->result($result, 0, "acct");
      if ( $core == 1 )
        $query = $sql["logon"]->query("SELECT login FROM accounts WHERE acct='".$owner_acc_id."'");
      else
        $query = $sql["logon"]->query("SELECT username as login FROM account WHERE id='".$owner_acc_id."'");
      $owner_name = $sql["logon"]->result($query, 0, "login");

      $query = $sql["mgr"]->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_name."'");
      $owner_gmlvl = $sql["mgr"]->result($query, 0, "gm");

      if ( $user_lvl >= $owner_gmlvl )
      {
        $new_money = ( ( isset($_GET["money"]) ) ? $sql["char"]->quote_smart($_GET["money"]) : 0 );
        $new_arena_points = ( ( isset($_GET["arena_points"]) ) ? $sql["char"]->quote_smart($_GET["arena_points"]) : 0 );
        $new_honor_points = ( ( isset($_GET["honor_points"]) ) ? $sql["char"]->quote_smart($_GET["honor_points"]) : 0 );
        $new_total_kills = ( ( isset($_GET["total_kills"]) ) ? $sql["char"]->quote_smart($_GET["total_kills"]) : 0 );

        if ( !is_numeric($new_money) || !is_numeric($new_arena_points) || !is_numeric($new_honor_points) || !is_numeric($new_total_kills) )
          error(lang("char", "use_numeric"));

        $new_name = $sql["char"]->quote_smart($_GET["cname"]);
        $new_name = htmlspecialchars($new_name);

        if ( $new_name != $_GET["cname"] )
          redirect("char_edit.php?action=edit_char&id=".$id."&error=4");

        if ( $core == 1 )
        {
          $result = $sql["char"]->query("SELECT data FROM `characters` WHERE guid='".$id."'");
          $char = $sql["char"]->fetch_row($result);

          $char_data = explode(' ', $char[0]);

          $char_data[CHAR_DATA_OFFSET_GOLD] = $new_money;
          $char_data[CHAR_DATA_OFFSET_ARENA_POINTS] = $new_arena_points;
          $char_data[CHAR_DATA_OFFSET_HONOR_POINTS] = $new_honor_points;
          $char_data[CHAR_DATA_OFFSET_HONOR_KILL] = $new_total_kills;
        
          $data = implode(" ", $char_data);
        }

        if ( $core == 1 )
          $query = "UPDATE `characters` SET name='".$new_name."', data='".$data."' WHERE guid='".$id."'";
        else
          $query = "UPDATE `characters` SET name='".$new_name."', money=".$new_money.", arenaPoints=".$new_arena_points.", totalHonorPoints=".$new_honor_points.", totalKills=".$new_total_kills." WHERE guid='".$id."'";

        $result = $sql["char"]->query($query);

        if ( $result )
          redirect("char_edit.php?action=edit_char&id=".$id."&error=3");
        else
          redirect("char_edit.php?action=edit_char&id=".$id."&error=4");
      }
      else
        ;
    }
    else
      redirect("char_edit.php?action=edit_char&id=".$id."&error=2");
  }
  else
    error(lang("char", "no_char_found"));
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
  case 1:
    $output .= '
            <h1><font class="error">'.lang("global", "empty_fields").'</font></h1>';
    break;
  case 2:
    $output .= '
            <h1><font class="error">'.lang("char", "err_edit_online_char").'</font></h1>';
    break;
  case 3:
    $output .= '
            <h1><font class="error">'.lang("char", "updated").'</font></h1>';
    break;
  case 4:
    $output .= '
            <h1><font class="error">'.lang("char", "update_err").'</font></h1>';
    break;
  case 5:
    $output .= '
            <h1><font class="error">'.lang("char", "max_acc").'</font></h1>';
    break;
  default: //no error
    $output .= '
            <h1>'.lang("char", "edit_char").'</h1>';
}
$output .= '
          </div>';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

switch ( $action )
{
  case "edit_char":
    edit_char();
    break;
  case "do_edit_char":
    do_edit_char();
    break;
  default:
    edit_char();
}

unset($action_permission);

require_once("footer.php");
?>
