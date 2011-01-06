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
valid_login($action_permission["view"]);

//########################################################################################################################
// SHOW CHAR PVP
//########################################################################################################################
function char_pvp()
{
  global $output, $realm_id, $characters_db, $logon_db, $corem_db, $action_permission,
    $site_encoding, $user_lvl, $user_name, $sql, $core;

  if ( empty($_GET["id"]) )
    error(lang("global", "empty_fields"));

  if ( empty($_GET["realm"]) )
    $realmid = $realm_id;
  else
  {
    $realmid = $sql["logon"]->quote_smart($_GET["realm"]);
    if ( is_numeric($realmid) )
      $sql["char"]->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name'], $characters_db[$realmid]["encoding"]);
    else
      $realmid = $realm_id;
  }

  $id = $sql["char"]->quote_smart($_GET["id"]);
  if ( !is_numeric($id) )
    $id = 0;

  if ( $core == 1 )
    $result = $sql["char"]->query("SELECT acct, name, race, class, level, gender, arenaPoints,
      killsToday,
      killsYesterday,
      killsLifetime,
      honorToday,
      honorYesterday,
      honorPoints
      FROM characters WHERE guid='".$id."' LIMIT 1");
  else
    $result = $sql["char"]->query("SELECT account AS acct, name, race, class, level, gender, arenaPoints,
      todayKills AS killsToday,
      yesterdayKills AS killsYesterday,
      totalKills AS killsLifetime,
      todayHonorPoints AS honorToday,
      yesterdayHonorPoints AS honorYesterday,
      totalHonorPoints AS honorPoints
      FROM characters WHERE guid='".$id."' LIMIT 1");

  if ( $core == 1 )
  {
    // arenateams.data format: [week games] [week wins] [season games] [season wins]
    // arena team player structure [player_id] [week_played] [week_win] [season_played] [season_win] [rating]
    $query = "SELECT id, rating, type,
      SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', 2), ' ', 1) AS games, 
      SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', 2), ' ', -1) AS wins,
      SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', 1) AS played, 
      SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', -1) AS wins2,
      ranking,
      player_data1, player_data2, player_data3, player_data4, player_data5,
      player_data6, player_data7, player_data8, player_data9, player_data10
      FROM arenateams WHERE player1_id='".$id."' OR player2_id='".$id."' OR player3_id='".$id."' OR
         player4_id='".$id."' OR player5_id='".$id."' OR player6_id='".$id."' OR player7_id='".$id."' OR
          player8_id='".$id."' OR player9_id='".$id."' OR player10_id='".$id."'";

    $arena_team_query = $sql["char"]->query($query);
  }
  elseif ( $core == 2 )
  {
    $query = "SELECT *,arena_team.arenateamid AS id, rating, type,
      games_week AS games, wins_week AS wins, games_season AS played, wins_season AS wins2, rank AS ranking,
      (SELECT COUNT(*) FROM arena_team_member WHERE arenateamid=id) AS tot_chars
      FROM arena_team
        LEFT JOIN arena_team_stats ON arena_team_stats.arenateamid=arena_team.arenateamid
        LEFT JOIN arena_team_member ON arena_team_member.arenateamid=arena_team.arenateamid
      WHERE arena_team_member.guid='".$id."'";

    $arena_team_query = $sql["char"]->query($query);
  }
  else
  {
    $query = "SELECT *,arena_team.arenateamid AS id, rating, type,
      games, wins, played, wins2, rank AS ranking,
      (SELECT COUNT(*) FROM arena_team_member WHERE arenateamid=id) AS tot_chars
      FROM arena_team
        LEFT JOIN arena_team_stats ON arena_team_stats.arenateamid=arena_team.arenateamid
        LEFT JOIN arena_team_member ON arena_team_member.arenateamid=arena_team.arenateamid
      WHERE arena_team_member.guid='".$id."'";

    $arena_team_query = $sql["char"]->query($query);
  }

  while ( $arena_row = $sql["char"]->fetch_assoc($arena_team_query) )
  {
    // Trinity stores Team type as 2, 3, 5; ArcEmu & MaNGOS use 0, 1, 2
    if ( $core != 3 )
    {
      if ( $arena_row["type"] == 0 )
        $type = 2;
      elseif ( $arena_row["type"] == 1 )
        $type = 3;
      elseif ( $arena_row["type"] == 2 )
        $type = 5;
    }
    else
      $type = $arena_row["type"];

    if ( $type == 2 )
      $arena_team2 = $arena_row;
    elseif ( $type == 3 )
      $arena_team3 = $arena_row;
    elseif ( $type == 5 )
      $arena_team5 = $arena_row;
  }

  $arenateam_data2 = arenateam_data($arena_team2["id"]);
  $arenateam_data3 = arenateam_data($arena_team3["id"]);
  $arenateam_data5 = arenateam_data($arena_team5["id"]);

  if ( $sql["char"]->num_rows($result) )
  {
    $char = $sql["char"]->fetch_assoc($result);

    // we get user permissions first
    $owner_acc_id = $sql["char"]->result($result, 0, 'acct');
    if ( $core == 1 )
      $result = $sql["logon"]->query("SELECT login FROM accounts WHERE acct='".$char["acct"]."'");
    else
      $result = $sql["logon"]->query("SELECT username AS login FROM account WHERE id='".$char["acct"]."'");
    $owner_name = $sql["logon"]->result($result, 0, 'login');
    $result = $sql["mgr"]->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_name."'");
    $owner_gmlvl = $sql["mgr"]->result($result, 0, 'gm');

    if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
    {
      $output .= '
          <center>
            <div id="tab">
              <ul>
                <li id="selected"><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "char_sheet").'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "inventory").'</a></li>
                '.( ( $char["level"] < 10 ) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "talents").'</a></li>' ).'
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "achievements").'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "quests").'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "friends").'</a></li>
                <li><a href="char_view.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "view").'</a></li>
               </ul>
            </div>
            <div id="tab_content">
              <div id="tab">
                <ul>
                  <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "char_sheet").'</a></li>';
      if ( char_get_class_name($char["class"]) == 'Hunter' )
        $output .= '
                  <li><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "pets").'</a></li>';
      $output .= '
                  <li><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "reputation").'</a></li>
                  <li><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "skills").'</a></li>
                  <li id="selected"><a href="char_pvp.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "pvp").'</a></li>';
      if ( ( $owner_name == $user_name ) || ( $user_lvl >= get_page_permission("insert", "char_mail.php") ) )
        $output .= '
                  <li><a href="char_mail.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "mail").'</a></li>';
      $output .= '
                </ul>
              </div>
              <div id="tab_content2">
                <font class="bold">
                  '.htmlentities($char["name"], ENT_COMPAT, $site_encoding).' -
                  <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                  <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" /> - '.lang("char", "level_short").char_get_level_color($char["level"]).'
                </font>
                <br />
                <br />
                <table class="lined" id="ch_pvp_main">
                  <tr>
                    <td colspan="4">'.lang("char", "honor").': <span id="ch_pvp_highlight">'.$char["honorPoints"].'</span> <img src="img/money_'.( ( char_get_side_id($char["race"]) ) ? 'horde' : 'alliance' ).'.gif" /></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>'.lang("char", "today").'</td>
                    <td>'.lang("char", "yesterday").'</td>
                    <td>'.lang("char", "lifetime").'</td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "kills").'</td>
                    <td>'.$char["killsToday"].'</td>
                    <td>'.$char["killsYesterday"].'</td>
                    <td>'.$char["killsLifetime"].'</td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "honor").'</td>
                    <td>'.$char["honorToday"].'</td>
                    <td>'.$char["honorYesterday"].'</td>
                    <td>-</td>
                  </tr>
                </table>
                <br />
                <table class="lined" id="ch_pvp_main">
                  <tr>
                    <td colspan="5">'.lang("char", "arena").': <span id="ch_pvp_highlight">'.$char["arenaPoints"].'</span> <img src="img/money_arena.gif" /></td>
                  </tr>';
      // ArcEmu's storage of Arena Team Members is a nightmare...
      // until this is fixed, they won't get to see the Arena Team stuff
      if ( $core != 1 )
      {
        if ( $arena_team2 != NULL )
        {
          $output .= '
                    <tr>
                      <td rowspan="7" id="ch_pvp_banner_space">
                        <div class="arena_banner">
                          <img src="libs/banner_lib.php?action=banner&f='.$arenateam_data2["banner_style"].'&r='.$arenateam_data2["BackgroundColor"][1].'&g='.$arenateam_data2["BackgroundColor"][2].'&b='.$arenateam_data2["BackgroundColor"][3].'" class="banner_img" />
                          <img src="libs/banner_lib.php?action=border&f='.$arenateam_data2["BorderStyle"].'&f2='.$arenateam_data2["banner_style"].'&r='.$arenateam_data2["BorderColor"][1].'&g='.$arenateam_data2["BorderColor"][2].'&b='.$arenateam_data2["BorderColor"][3].'" class="border_img" />
                          <img src="libs/banner_lib.php?action=emblem&f='.$arenateam_data2["EmblemStyle"].'&r='.$arenateam_data2["EmblemColor"][1].'&g='.$arenateam_data2["EmblemColor"][2].'&b='.$arenateam_data2["EmblemColor"][3].'&s=0.55" class="emblem_img" />
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2"><a href="arenateam.php?action=view_team&error=3&id='.$arenateam_data2["id"].'">'.$arenateam_data2["name"].'</a></td>
                      <td colspan="2">'.lang("char", "team").' '.lang("char", "rating").': <span id="ch_pvp_highlight">'.$arena_team2["rating"].'</span></td>
                    </tr>
                    </tr>
                      <td><span id="ch_pvp_dim">'.lang("char", "team").'</span></td>
                      <td>'.lang("char", "games").'</td>
                      <td>'.lang("char", "winloss").'</td>
                      <td>'.lang("char", "played").'</td>
                    </tr>
                    </tr>
                      <td>'.lang("char", "thisweek").'</td>
                      <td>'.$arena_team2["games"].'</td>
                      <td>'.$arena_team2["wins"].'-'.($arena_team2["games"]-$arena_team2["wins"]).'</td>
                      <td>'.$arena_team2["wins"].' ('.(($arena_team2["wins"]/$arena_team2["games"])*100).'%)</td>
                    </tr>
                    </tr>
                      <td><span id="ch_pvp_dim">'.$char["name"].'</span></td>
                      <td>'.lang("char", "played").'</td>
                      <td>'.lang("char", "winloss").'</td>
                      <td>'.lang("char", "rating").'</td>
                    </tr>
                    </tr>
                      <td>'.lang("char", "thisweek").'</td>
                      <td>'.$arena_team2["played_week"].'</td>
                      <td>'.$arena_team2["wons_week"].'-'.($arena_team2["played_week"]-$arena_team2["wons_week"]).'</td>
                      <td>-</td>
                    </tr>
                    </tr>
                      <td>'.lang("char", "thisseason").'</td>
                      <td>'.$arena_team2["played_season"].'</td>
                      <td>'.$arena_team2["wons_season"].'-'.($arena_team2["played_season"]-$arena_team2["wons_season"]).'</td>
                      <td>-</td>
                    </tr>';
        }
        else
        {
          $output .= '
                    <tr>
                      <td rowspan="7" id="ch_pvp_banner_space">
                        <div class="arena_banner">
                          <img src="img/blank.gif" class="banner_img" />
                          <img src="img/blank.gif" class="border_img" />
                          <img src="img/blank.gif" class="emblem_img" />
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4"><span id="ch_pvp_dim">('.lang("arenateam", "2T").')</span></td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>';
        }

        if ( $arena_team3 != NULL )
        {
          $output .= '
                    <tr>
                      <td rowspan="7" id="ch_pvp_banner_space">
                        <div class="arena_banner">
                          <img src="libs/banner_lib.php?action=banner&f='.$arenateam_data3["banner_style"].'&r='.$arenateam_data3["BackgroundColor"][1].'&g='.$arenateam_data3["BackgroundColor"][2].'&b='.$arenateam_data3["BackgroundColor"][3].'" class="banner_img" />
                          <img src="libs/banner_lib.php?action=border&f='.$arenateam_data3["BorderStyle"].'&f2='.$arenateam_data3["banner_style"].'&r='.$arenateam_data3["BorderColor"][1].'&g='.$arenateam_data3["BorderColor"][2].'&b='.$arenateam_data3["BorderColor"][3].'" class="border_img" />
                          <img src="libs/banner_lib.php?action=emblem&f='.$arenateam_data3["EmblemStyle"].'&r='.$arenateam_data3["EmblemColor"][1].'&g='.$arenateam_data3["EmblemColor"][2].'&b='.$arenateam_data3["EmblemColor"][3].'&s=0.55" class="emblem_img" />
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2"><a href="arenateam.php?action=view_team&error=3&id='.$arenateam_data3["id"].'">'.$arenateam_data3["name"].'</a></td>
                      <td colspan="2">'.lang("char", "team").' '.lang("char", "rating").': <span id="ch_pvp_highlight">'.$arena_team3["rating"].'</span></td>
                    </tr>
                    </tr>
                      <td><span id="ch_pvp_dim">'.lang("char", "team").'</span></td>
                      <td>'.lang("char", "games").'</td>
                      <td>'.lang("char", "winloss").'</td>
                      <td>'.lang("char", "played").'</td>
                    </tr>
                    </tr>
                      <td>'.lang("char", "thisweek").'</td>
                      <td>'.$arena_team3["games"].'</td>
                      <td>'.$arena_team3["wins"].'-'.($arena_team3["games"]-$arena_team3["wins"]).'</td>
                      <td>'.$arena_team3["wins"].' ('.(($arena_team3["wins"]/$arena_team3["games"])*100).'%)</td>
                    </tr>
                    </tr>
                      <td><span id="ch_pvp_dim">'.$char["name"].'</span></td>
                      <td>'.lang("char", "played").'</td>
                      <td>'.lang("char", "winloss").'</td>
                      <td>'.lang("char", "rating").'</td>
                    </tr>
                    </tr>
                      <td>'.lang("char", "thisweek").'</td>
                      <td>'.$arena_team3["played_week"].'</td>
                      <td>'.$arena_team3["wons_week"].'-'.($arena_team3["played_week"]-$arena_team3["wons_week"]).'</td>
                      <td>-</td>
                    </tr>
                    </tr>
                      <td>'.lang("char", "thisseason").'</td>
                      <td>'.$arena_team3["played_season"].'</td>
                      <td>'.$arena_team3["wons_season"].'-'.($arena_team3["played_season"]-$arena_team3["wons_season"]).'</td>
                      <td>-</td>
                    </tr>';
        }
        else
        {
          $output .= '
                    <tr>
                      <td rowspan="7" id="ch_pvp_banner_space">
                        <div class="arena_banner">
                          <img src="img/blank.gif" class="banner_img" />
                          <img src="img/blank.gif" class="border_img" />
                          <img src="img/blank.gif" class="emblem_img" />
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4"><span id="ch_pvp_dim">('.lang("arenateam", "3T").')</span></td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>';
        }

        if ( $arena_team5 != NULL )
        {
          $output .= '
                    <tr>
                      <td rowspan="7" id="ch_pvp_banner_space">
                        <div class="arena_banner">
                          <img src="libs/banner_lib.php?action=banner&f='.$arenateam_data5["banner_style"].'&r='.$arenateam_data5["BackgroundColor"][1].'&g='.$arenateam_data5["BackgroundColor"][2].'&b='.$arenateam_data5["BackgroundColor"][3].'" class="banner_img" />
                          <img src="libs/banner_lib.php?action=border&f='.$arenateam_data5["BorderStyle"].'&f2='.$arenateam_data5["banner_style"].'&r='.$arenateam_data5["BorderColor"][1].'&g='.$arenateam_data5["BorderColor"][2].'&b='.$arenateam_data5["BorderColor"][3].'" class="border_img" />
                          <img src="libs/banner_lib.php?action=emblem&f='.$arenateam_data5["EmblemStyle"].'&r='.$arenateam_data5["EmblemColor"][1].'&g='.$arenateam_data5["EmblemColor"][2].'&b='.$arenateam_data5["EmblemColor"][3].'&s=0.55" class="emblem_img" />
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2"><a href="arenateam.php?action=view_team&error=3&id='.$arenateam_data5["id"].'">'.$arenateam_data5["name"].'</a></td>
                      <td colspan="2">'.lang("char", "team").' '.lang("char", "rating").': <span id="ch_pvp_highlight">'.$arena_team5["rating"].'</span></td>
                    </tr>
                    </tr>
                      <td><span id="ch_pvp_dim">'.lang("char", "team").'</span></td>
                      <td>'.lang("char", "games").'</td>
                      <td>'.lang("char", "winloss").'</td>
                      <td>'.lang("char", "played").'</td>
                    </tr>
                    </tr>
                      <td>'.lang("char", "thisweek").'</td>
                      <td>'.$arena_team5["games"].'</td>
                      <td>'.$arena_team5["wins"].'-'.($arena_team5["games"]-$arena_team5["wins"]).'</td>
                      <td>'.$arena_team5["wins"].' ('.(($arena_team5["wins"]/$arena_team5["games"])*100).'%)</td>
                    </tr>
                    </tr>
                      <td><span id="ch_pvp_dim">'.$char["name"].'</span></td>
                      <td>'.lang("char", "played").'</td>
                      <td>'.lang("char", "winloss").'</td>
                      <td>'.lang("char", "rating").'</td>
                    </tr>
                    </tr>
                      <td>'.lang("char", "thisweek").'</td>
                      <td>'.$arena_team5["played_week"].'</td>
                      <td>'.$arena_team5["wons_week"].'-'.($arena_team5["played_week"]-$arena_team5["wons_week"]).'</td>
                      <td>-</td>
                    </tr>
                    </tr>
                      <td>'.lang("char", "thisseason").'</td>
                      <td>'.$arena_team5["played_season"].'</td>
                      <td>'.$arena_team5["wons_season"].'-'.($arena_team5["played_season"]-$arena_team5["wons_season"]).'</td>
                      <td>-</td>
                    </tr>';
        }
        else
        {
          $output .= '
                    <tr>
                      <td rowspan="7" id="ch_pvp_banner_space">
                        <div class="arena_banner">
                          <img src="img/blank.gif" class="banner_img" />
                          <img src="img/blank.gif" class="border_img" />
                          <img src="img/blank.gif" class="emblem_img" />
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4"><span id="ch_pvp_dim">('.lang("arenateam", "5T").')</span></td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>
                    </tr>
                      <td colspan="4">&nbsp;</td>
                    </tr>';
        }
      }

      $output .= '
                </table>
                <br />
              </div>
              <br />
            </div>
            <br />
            <table class="hidden">
              <tr>
                <td>';
      // button to user account page, user account page has own security
      makebutton(lang("char", "chars_acc"), 'user.php?action=edit_user&amp;id='.$owner_acc_id.'', 130);
      $output .= '
                </td>
                <td>';

      // only higher level GM with delete access can edit character
      //  character edit allows removal of character items, so delete permission is needed
      if ( ( $user_lvl > $owner_gmlvl ) && ( $user_lvl >= $action_permission["delete"] ) )
      {
                  //makebutton($lang_char["edit_button"], 'char_edit.php?id='.$id.'&amp;realm='.$realmid.'', 130);
        $output .= '
                </td>
                <td>';
      }
      // only higher level GM with delete access, or character owner can delete character
      if ( ( ( $user_lvl > $owner_gmlvl ) && ( $user_lvl >= $action_permission["delete"] ) ) || ( $owner_name === $user_name ) )
      {
        makebutton(lang("char", "del_char"), 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 130);
        $output .= '
                </td>
                <td>';
      }
      // only GM with update permission can send mail, mail can send items, so update permission is needed
      if ( $user_lvl >= $action_permission["update"] )
      {
        makebutton(lang("char", "send_mail"), 'mail.php?type=ingame_mail&amp;to='.$char["name"].'', 130);
        $output .= '
                </td>
                <td>';
      }
      makebutton(lang("global", "back"), 'javascript:window.history.back()" type="def', 130);
      $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>
          <!-- end of char_achieve.php -->';
    }
    else
      error(lang("char", "no_permission"));
  }
  else
    error(lang("char", "no_char_found"));

}

function arenateam_data($arenateam_id)
{
  global $sql, $core;

  if ( $core == 1 )
    $query = $sql["char"]->query("SELECT id, name, type,
    INET_NTOA(backgroundcolour) AS BackgroundColor,
    INET_NTOA(bordercolour) AS BorderColor,
    INET_NTOA(emblemcolour) AS EmblemColor,
    emblemstyle AS EmblemStyle, borderstyle AS BorderStyle
    FROM arenateams
    WHERE id='".$arenateam_id."'");
  else
    $query = $sql["char"]->query("SELECT arenateamid AS id, name, type,
    INET_NTOA(BackgroundColor) AS BackgroundColor,
    INET_NTOA(BorderColor) AS BorderColor,
    INET_NTOA(EmblemColor) AS EmblemColor,
    EmblemStyle, BorderStyle
    FROM arena_team
    WHERE arenateamid='".$arenateam_id."'");

  $arenateam_data = $sql["char"]->fetch_assoc($query);

  // extract banner colors
  $arenateam_data["BackgroundColor"] = explode(".", $arenateam_data["BackgroundColor"]);
  $arenateam_data["BorderColor"] = explode(".", $arenateam_data["BorderColor"]);
  $arenateam_data["EmblemColor"] = explode(".", $arenateam_data["EmblemColor"]);

  // Trinity stores Team type as 2, 3, 5; ArcEmu & MaNGOS use 0, 1, 2
  if ( $core != 3 )
  {
    if ( $arenateam_data["type"] == 0 )
      $arenateam_data["banner_style"] = 2;
    elseif ( $arenateam_data["type"] == 1 )
      $arenateam_data["banner_style"] = 3;
    elseif ( $arenateam_data["type"] == 2 )
      $arenateam_data["banner_style"] = 5;
  }
  else
  {
    if ( $arenateam_data["type"] == 2 )
      $arenateam_data["banner_style"] = 2;
    elseif ( $arenateam_data["type"] == 3 )
      $arenateam_data["banner_style"] = 3;
    elseif ( $arenateam_data["type"] == 5 )
      $arenateam_data["banner_style"] = 5;
  }

  return $arenateam_data;
}


//########################################################################################################################
// MAIN
//########################################################################################################################

//$action = (isset($_GET["action"])) ? $_GET["action"] : NULL;

$output .= '
      <div class="bubble">';

char_pvp();

unset($action_permission);

require_once 'footer.php';


?>
