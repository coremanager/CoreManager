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
require_once("./libs/char_lib.php");
valid_login($action_permission['view']);

//########################################################################################################################
// BROWSE ARENA TEAMS
//########################################################################################################################
function browse_teams()
{
  global $output, $characters_db, $realm_id, $itemperpage,
    $action_permission, $user_lvl, $user_id, $sqlc, $core;

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : "atid";
  if (!preg_match("/^[_[:lower:]]{1,17}$/", $order_by)) $order_by="atid";

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================
  //==========================Browse/Search CHECK==============================
  $search_by ='';
  $search_value = '';
  if(isset($_GET['search_value']) && isset($_GET['search_by']))
  {
    $search_value = $sqlc->quote_smart($_GET['search_value']);
    $search_by = $sqlc->quote_smart($_GET['search_by']);
    $search_menu = array('atname', 'leadername', 'atid');
    if (!in_array($search_by, $search_menu)) $search_by = 'atid';

    // arenateams.data format: [week games] [week wins] [season games] [season wins]
    // because I don't want to add another two columns to the display, we'll use season stats
    switch($search_by)
    {
      case "atname":
      {
        if ( $core == 1 )
        {
          $query = $sqlc->query("SELECT arenateams.id AS atid, arenateams.name AS atname,
            arenateams.leader AS lguid, arenateams.type AS attype,
            (SELECT name FROM `characters` WHERE guid = lguid) AS lname,
            rating AS atrating, SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', 1) AS atgames, 
            SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', -1) As atwins,
            SUBSTRING_INDEX(player_data1, ' ', 1) AS p1, 
            SUBSTRING_INDEX(player_data2, ' ', 1) AS p2, 
            SUBSTRING_INDEX(player_data3, ' ', 1) AS p3, 
            SUBSTRING_INDEX(player_data4, ' ', 1) AS p4, 
            SUBSTRING_INDEX(player_data5, ' ', 1) AS p5, 
            SUBSTRING_INDEX(player_data6, ' ', 1) AS p6, 
            SUBSTRING_INDEX(player_data7, ' ', 1) AS p7, 
            SUBSTRING_INDEX(player_data8, ' ', 1) AS p8, 
            SUBSTRING_INDEX(player_data9, ' ', 1) AS p9, 
            SUBSTRING_INDEX(player_data10, ' ', 1) AS p10
            FROM arenateams
            WHERE arenateams.name LIKE '%$search_value%'
            ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
          $query_1 = $sqlc->query("SELECT count(*) FROM arenateams
            WHERE arenateams.name LIKE '%$search_value%'");
        }
        else
        {
          $query = $sqlc->query("SELECT arena_team.arenateamid AS atid, arena_team.name AS atname,
            arena_team.captainguid AS lguid, arena_team.type AS attype, 
            (SELECT name FROM `characters` WHERE guid = lguid) AS lname, 
            (SELECT COUNT(*) FROM  arena_team_member WHERE arenateamid = atid) AS tot_chars, 
            rating AS atrating, games as atgames, wins as atwins FROM arena_team, arena_team_stats 
            WHERE arena_team.arenateamid = arena_team_stats.arenateamid AND arena_team.name LIKE '%$search_value%' 
            ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
          $query_1 = $sqlc->query("SELECT count(*) FROM arena_team 
            WHERE arena_team.name LIKE '%$search_value%'");
        }
      }
      case "leadername":
      {
        if ( $core == 1 )
        {
          $query = $sqlc->query("SELECT arenateams.id AS atid, arenateams.name AS atname, arenateams.leader AS lguid,
            arenateams.type AS attype,
            (SELECT name FROM `characters` WHERE guid = lguid) AS lname,
            rating AS atrating, SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', 1) AS atgames, 
            SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', -1) AS atwins,
            SUBSTRING_INDEX(player_data1, ' ', 1) AS p1, 
            SUBSTRING_INDEX(player_data2, ' ', 1) AS p2, 
            SUBSTRING_INDEX(player_data3, ' ', 1) AS p3, 
            SUBSTRING_INDEX(player_data4, ' ', 1) AS p4, 
            SUBSTRING_INDEX(player_data5, ' ', 1) AS p5, 
            SUBSTRING_INDEX(player_data6, ' ', 1) AS p6, 
            SUBSTRING_INDEX(player_data7, ' ', 1) AS p7, 
            SUBSTRING_INDEX(player_data8, ' ', 1) AS p8, 
            SUBSTRING_INDEX(player_data9, ' ', 1) AS p9, 
            SUBSTRING_INDEX(player_data10, ' ', 1) AS p10
            FROM arenateams
            WHERE arenateams.leader in
            (SELECT guid FROM characters WHERE name LIKE '%$search_value%')
            ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
          $query_1 = $sqlc->query("SELECT count(*) FROM arenateams
            WHERE arenateams.leader IN (SELECT guid FROM characters WHERE name LIKE '%$search_value%')");
        }
        else
        {
          $query = $sqlc->query("SELECT arena_team.arenateamid AS atid, arena_team.name AS atname, 
            arena_team.captainguid AS lguid, arena_team.type AS attype, 
            (SELECT name FROM `characters` WHERE guid = lguid) AS lname,
            (SELECT COUNT(*) FROM  arena_team_member WHERE arenateamid = atid) AS tot_chars, 
            rating AS atrating, games as atgames, wins as atwins 
            FROM arena_team, arena_team_stats 
            WHERE arena_team.arenateamid = arena_team_stats.arenateamid AND arena_team.captainguid in (SELECT guid from characters where name like '%$search_value%') 
            ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
          $query_1 = $sqlc->query("SELECT count(*) FROM arena_team 
            WHERE arena_team.captainguid IN (SELECT guid FROM characters WHERE name LIKE '%$search_value%')");
        }
      }
      case "atid":
      {
        if ( $core == 1 )
        {
          $query = $sqlc->query("SELECT arenateams.id AS atid, arenateams.name AS atname, arenateams.leader AS lguid,
            arenateams.type AS attype,
            (SELECT name FROM `characters` WHERE guid = lguid) AS lname,
            rating AS atrating, SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', 1) AS atgames, 
            SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', -1) AS atwins,
            SUBSTRING_INDEX(player_data1, ' ', 1) AS p1, 
            SUBSTRING_INDEX(player_data2, ' ', 1) AS p2, 
            SUBSTRING_INDEX(player_data3, ' ', 1) AS p3, 
            SUBSTRING_INDEX(player_data4, ' ', 1) AS p4, 
            SUBSTRING_INDEX(player_data5, ' ', 1) AS p5, 
            SUBSTRING_INDEX(player_data6, ' ', 1) AS p6, 
            SUBSTRING_INDEX(player_data7, ' ', 1) AS p7, 
            SUBSTRING_INDEX(player_data8, ' ', 1) AS p8, 
            SUBSTRING_INDEX(player_data9, ' ', 1) AS p9, 
            SUBSTRING_INDEX(player_data10, ' ', 1) AS p10
            FROM arenateams
            WHERE arenateams.id ='$search_value'
            ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
          $query_1 = $sqlc->query("SELECT count(*) FROM arenateams
            WHERE arenateams.id = '$search_value'");
        }
        else
        {
          $query = $sqlc->query("SELECT arena_team.arenateamid AS atid, arena_team.name AS atname, 
            arena_team.captainguid AS lguid, arena_team.type AS attype, 
            (SELECT name FROM `characters` WHERE guid = lguid) AS lname,
            (SELECT COUNT(*) FROM  arena_team_member WHERE arenateamid = atid) AS tot_chars, 
            rating AS atrating, games as atgames, wins as atwins 
            FROM arena_team, arena_team_stats 
            WHERE arena_team.arenateamid = arena_team_stats.arenateamid AND arena_team.arenateamid ='$search_value' 
            ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
          $query_1 = $sqlc->query("SELECT count(*) FROM arena_team 
            WHERE arena_team.arenateamid ='$search_value'");
        }
        break;
      }
    }
  }
  else
  {
    if ( $core == 1)
    {
      $query = $sqlc->query("SELECT arenateams.id AS atid, arenateams.name AS atname, arenateams.leader AS lguid,
        arenateams.type AS attype,
        (SELECT name FROM `characters` WHERE guid = lguid) AS lname,
        rating AS atrating, SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', 1) AS atgames, 
        SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', -1) AS atwins,
            SUBSTRING_INDEX(player_data1, ' ', 1) AS p1, 
            SUBSTRING_INDEX(player_data2, ' ', 1) AS p2, 
            SUBSTRING_INDEX(player_data3, ' ', 1) AS p3, 
            SUBSTRING_INDEX(player_data4, ' ', 1) AS p4, 
            SUBSTRING_INDEX(player_data5, ' ', 1) AS p5, 
            SUBSTRING_INDEX(player_data6, ' ', 1) AS p6, 
            SUBSTRING_INDEX(player_data7, ' ', 1) AS p7, 
            SUBSTRING_INDEX(player_data8, ' ', 1) AS p8, 
            SUBSTRING_INDEX(player_data9, ' ', 1) AS p9, 
            SUBSTRING_INDEX(player_data10, ' ', 1) AS p10
        FROM arenateams
        ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
      $query_1 = $sqlc->query("SELECT count(*) FROM arenateams");
    }
    else
    {
      $query = $sqlc->query("SELECT arena_team.arenateamid AS atid, arena_team.name AS atname, 
        arena_team.captainguid AS lguid, arena_team.type AS attype, 
        (SELECT name FROM `characters` WHERE guid = lguid) AS lname,
        (SELECT COUNT(*) FROM  arena_team_member WHERE arenateamid = atid) AS tot_chars, 
        rating AS atrating, games as atgames, wins as atwins, 
        (SELECT count(*) AS GCNT  
        FROM `arena_team_member`, `characters`, `arena_team` 
        WHERE arena_team.arenateamid = atid AND arena_team_member.arenateamid = arena_team.arenateamid AND arena_team_member.guid = characters.guid AND characters.online = 1) as arenateam_online FROM arena_team, arena_team_stats WHERE arena_team.arenateamid = arena_team_stats.arenateamid 
        ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
      $query_1 = $sqlc->query("SELECT count(*) FROM arena_team");
    }
  }
  $all_record = $sqlc->result($query_1,0);
  unset($query_1);
  $this_page = $sqlc->num_rows($query);

  $member_count = 0;
  $members_online = 0;

//==========================top page navigation starts here====================
  $output .="
        <center>
          <table class=\"top_hidden\">
            <tr>
              <td>";
                makebutton(lang('global', 'back'), "javascript:window.history.back()", 130);
  ($search_by &&  $search_value) ? makebutton(lang('arenateam', 'arenateams'), "arenateam.php", 130) : $output .= "";
  $output .= "
              </td>
            </tr>
            <tr>
              <td>
                <table class=\"hidden\">
                  <tr>
                    <td>
                      <form action=\"arenateam.php\" method=\"get\" name=\"form\">
                        <input type=\"hidden\" name=\"error\" value=\"4\" />
                        <input type=\"text\" size=\"24\" name=\"search_value\" value=\"{$search_value}\"/>
                        <select name=\"search_by\">
                          <option value=\"atname\"".($search_by == 'atname' ? " selected=\"selected\"" : "").">".lang('arenateam', 'by_name')."</option>
                          <option value=\"leadername\"".($search_by == 'leadername' ? " selected=\"selected\"" : "").">".lang('arenateam', 'by_team_leader')."</option>
                          <option value=\"atid\"".($search_by == 'atid' ? " selected=\"selected\"" : "").">".lang('arenateam', 'by_id')."</option>
                        </select>
                      </form>
                    </td>
                    <td>";
                      makebutton(lang('global', 'search'), "javascript:do_submit()",80);
  $output .= "
                    </td>
                  </tr>
                </table>
              </td>
              <td align=\"right\">";
  $output .= generate_pagination("arenateam.php?order_by=$order_by".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=".!$dir, $all_record, $itemperpage, $start);
  $output .= "
              </td>
            </tr>
          </table>";
//==========================top page navigation ENDS here =====================

  $output .= "
          <table class=\"lined\">
            <tr>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=atid&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='atid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "").lang('arenateam', 'id')."</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=atname&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='atname' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "").lang('arenateam', 'arenateam_name')."</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=lname&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='lname' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "").lang('arenateam', 'captain')."</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=attype&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='attype' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "").lang('arenateam', 'type')."</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=tot_chars&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='tot_chars' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "").lang('arenateam', 'members')."</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=arenateam_online&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='arenateam_online' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "").lang('arenateam', 'arenateam_online')."</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=rating&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='rating' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "").lang('arenateam', 'rating')."</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=games&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='games' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "").lang('arenateam', 'games')."</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=wins&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='wins' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "").lang('arenateam', 'wins')."</a></th>
            </tr>";
  while ($data = $sqlc->fetch_assoc($query))
  {
    //$gonline = $sqlc->query("SELECT count(*) AS GCNT  FROM `arenateams`,`characters` WHERE arenateams.id = ".$data['atid']." AND characters.online = 1;");
    //$arenateam_online = $sqlc->result($gonline,"GCNT");
    for($i = 1; $i < 11; $i++)
    {
      if($data['p'.$i])
      {
        $member_count += 1;

        $online = $sqlc->query("SELECT * FROM characters WHERE online = 1 AND guid = '".$data['p'.$i]."'");
        if ($sqlc->num_rows($online))
          $members_online += 1;
      }
    }

    $output .= "
            <tr>
              <td>".$data['atid']."</td>
              <td><a href=\"arenateam.php?action=view_team&amp;error=3&amp;id=".$data['atid']."\">".htmlentities($data['atname'])."</a></td>
              <td><a href=\"char.php?id=".$data['lguid']."\">".htmlentities($data['lname'])."</a></td>
              <td>{$lang_arenateam[$data['attype']]}</td>
              <td>".$member_count."</td>
              <td>".$members_online."</td>
              <td>".$data['atrating']."</td>
              <td>".$data['atgames']."</td>
              <td>".$data['atwins']."</td>
            </tr>";
  }
  $output .= "
            <tr><td colspan=\"9\" class=\"hidden\" align=\"right\">".lang('arenateam', 'tot_teams')." : $all_record</td></tr>
          </table>
        </center>";

}


function count_days( $a, $b ) {
  $gd_a = getdate( $a );
  $gd_b = getdate( $b );
  $a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
  $b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );
  return round( abs( $a_new - $b_new ) / 86400 );
}

//########################################################################################################################
// VIEW ARENA TEAM
//########################################################################################################################
function view_team()
{
  global $output, $characters_db, $realm_id, $arcm_db, $logon_db,
    $action_permission, $user_lvl, $user_id, $showcountryflag, $sqlc, $sqll, $sqlm;

  if(!isset($_GET['id'])) redirect("arenateam.php?error=1");

  $arenateam_id = $sqlc->quote_smart($_GET['id']);

  $query = $sqlc->query("SELECT id, name, type FROM arenateams WHERE id = '$arenateam_id'");
  $arenateam_data = $sqlc->fetch_row($query);

  // arenateams.data format: [week games] [week wins] [season games] [season wins]
  $query = $sqlc->query("SELECT id, rating,
    SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', 2), ' ', 1) AS games, 
    SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', 2), ' ', -1) AS wins,
    SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', 1) AS played, 
    SUBSTRING_INDEX(SUBSTRING_INDEX(data, ' ', -2), ' ', -1) AS wins2,
    ranking, player_data1, player_data2, player_data3, player_data4, player_data5,
    player_data6, player_data7, player_data8, player_data9, player_data10
    FROM arenateams WHERE id = '$arenateam_id'");
  $arenateamstats_data = $sqlc->fetch_row($query);

  $rating_offset = 1550;
  if ($arenateam_data[2] == 3)
    $rating_offset += 6;
  else if ($arenateam_data[2] == 5)
    $rating_offset += 12;

  $total_members = 0;

  for ($m = 0; $m < 10; $m++)
  {
    $temp = explode(' ', $arenateamstats_data[$m+7]);
    if ($temp[0])
      $members[$m] = $temp;
    if ($members[$m][0])
      $total_members += 1;
  }

  $losses_week = $arenateamstats_data[2]-$arenateamstats_data[3];
  if($arenateamstats_data[2])
    $winperc_week = round((10000 * $arenateamstats_data[3]) / $arenateamstats_data[2]) / 100;
  else
    $winperc_week = $arenateamstats_data[2];
  $losses_season = $arenateamstats_data[4]-$arenateamstats_data[5];
  if($arenateamstats_data[4])
    $winperc_season = round((10000 * $arenateamstats_data[5]) / $arenateamstats_data[4]) / 100;
  else
    $winperc_season = $arenateamstats_data[4];
  $output .= "
        <script type=\"text/javascript\">
          answerbox.btn_ok='".lang('global', 'yes_low')."';
          answerbox.btn_cancel='".lang('global', 'no')."';
        </script>
        <center>
          <fieldset>
            <legend>".lang('arenateam', 'arenateam')." (".lang('arenateam', $arenateam_data[2]).")</legend>
            <table class=\"lined\">
              <tr class=\"bold\">
                <td colspan=\"".($showcountryflag ? 14 : 13 )."\">".htmlentities($arenateam_data[1])."</td>
              </tr>
              <tr>
                <td colspan=\"".($showcountryflag ? 14 : 13 )."\">".lang('arenateam', 'tot_members').": $total_members</td>
              </tr>
              <tr>
                <td colspan=\"4\">".lang('arenateam', 'this_week')."</td>
                <td colspan=\"2\">".lang('arenateam', 'games_played')." : $arenateamstats_data[2]</td>
                <td colspan=\"2\">".lang('arenateam', 'games_won')." : $arenateamstats_data[3]</td>
                <td colspan=\"2\">".lang('arenateam', 'games_lost')." : $losses_week</td>
                <td colspan=\"".($showcountryflag ? 4 : 3 )."\">".lang('arenateam', 'ratio')." : $winperc_week %</td>
              </tr>
              <tr>
                <td colspan=\"4\">".lang('arenateam', 'this_season')."</td>
                <td colspan=\"2\">".lang('arenateam', 'games_played')." : $arenateamstats_data[4]</td>
                <td colspan=\"2\">".lang('arenateam', 'games_won')." : $arenateamstats_data[5]</td>
                <td colspan=\"2\">".lang('arenateam', 'games_lost')." : $losses_season</td>
                <td colspan=\"".($showcountryflag ? 4 : 3 )."\">".lang('arenateam', 'ratio')." : $winperc_season %</td>
              </tr>
              <tr>
                <td colspan=\"".($showcountryflag ? 14 : 13 )."\">".lang('arenateam', 'standings')." {$arenateamstats_data[6]} ({$arenateamstats_data[1]})</td>
              </tr>
              <tr>
                <th width=\"1%\">".lang('arenateam', 'remove')."</th>
                <th width=\"1%\">".lang('arenateam', 'name')."</th>
                <th width=\"1%\">Race</th>
                <th width=\"1%\">Class</th>
                <th width=\"1%\">Personal Rating</th>
                <th width=\"1%\">Last Login (Days)</th>
                <th width=\"1%\">Online</th>
                <th width=\"1%\">".lang('arenateam', 'played_week')."</th>
                <th width=\"1%\">".lang('arenateam', 'wons_week')."</th>
                <th width=\"5%\">Win %</th>
                <th width=\"1%\">".lang('arenateam', 'played_season')."</th>
                <th width=\"1%\">".lang('arenateam', 'wons_season')."</th>
                <th width=\"5%\">Win %</th>";

    if ($showcountryflag)
    {
      $output .="
                <th width=\"1%\">".lang('global', 'country')."</th>";
    }

    $output .="
              </tr>";

    // arena team player structure [player_id] [week_played] [week_win] [season_played] [season_win] [rating]

    foreach ($members as $member)
    {
      $query = "SELECT acct, name, level, race, class, online, timestamp, gender
                FROM characters WHERE guid = '".$member[0]."'";
      $result = $sqlc->query($query);
      $member_char = $sqlc->fetch_row($result);

      $accid = $member_char[0];
      $output .= "
              <tr>";
      if($user_lvl >= $action_permission['delete'] || $accid == $user_id)
        $output .= "
                <td><img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('".lang('global', 'delete').": <font color=white>{$member[1]}</font><br />".lang('global', 'are_you_sure')."', 'arenateam.php?action=rem_char_from_team&amp;id=$member[0]&amp;arenateam_id=$arenateam_id');\" id=\"arenateam_delete_cursor\" /></td>";
      else
        $output .= "
                <td>&nbsp;
                </td>";

      if($member[1])
        $ww_pct = round((10000 * $member[2]) / $member[1]) / 100;
      else
        $ww_pct = $member[1];

      if($member[3])
        $ws_pct = round((10000 * $member[4]) / $member[3]) / 100;
      else
        $ws_pct = $member[3];

      $output .= "
                <td><a href=\"char.php?id=$member[0]\">".htmlentities($member_char[1])."</a></td>
                <td><img src='img/c_icons/{$member_char[3]}-{$member_char[7]}.gif' onmousemove='toolTip(\"".char_get_race_name($member_char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
                <td><img src='img/c_icons/{$member_char[4]}.gif' onmousemove='toolTip(\"".char_get_class_name($member_char[4])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
                <td>$member[5]</td>
                <td>".get_days_with_color($member_char[6])."</td>
                <td>".(($member_char[5]) ? "<img src='img/up.gif' alt='' />" : "<img src='img/down.gif' alt='' />")."</td>
                <td>$member[1]</td>
                <td>$member[2]</td>
                <td>$ww_pct %</td>
                <td>$member[3]</td>
                <td>$member[4]</td>
                <td>$ws_pct %</td>";

      if ($showcountryflag)
      {
        require_once './libs/misc_lib.php';

        $country = misc_get_country_by_account($member_char[0]);
        $output .="
                <td>".(($country['code']) ? "<img src='img/flags/".$country['code'].".png' onmousemove='toolTip(\"".($country['country'])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />" : "-")."</td>";
      }

      $output .="
              </tr>";
    }

    $output .= "
            </table>
            <br />
            <table class=\"hidden\">
              <tr>
                <td>";
    if($user_lvl >= $action_permission['delete'])
    {
      makebutton(lang('arenateam', 'del_team'), "arenateam.php?action=del_team&amp;id=$arenateam_id\" type=\"wrn", 180);
      $output .= "
                </td>
                <td>";
                  makebutton(lang('arenateam', 'arenateams'), "arenateam.php\" type=\"def", 130);
    $output .= "
                </td>
              </tr>
              <tr>
              </tr>";
    }
    else
    {
    makebutton(lang('arenateam', 'arenateams'), "arenateam.php", 130);
    $output .= "
                </td>
              </tr>";
    }
    $output .= "
            </table>
          </fieldset>
        </center>
";

}


//########################################################################################################################
// ARE YOU SURE  YOU WOULD LIKE TO OPEN YOUR AIRBAG?
//########################################################################################################################
function del_team()
{
  global $output;

  if(isset($_GET['id'])) $id = $_GET['id'];
  else redirect("arenateam.php?error=1");

  $output .= "
        <center>
          <h1><font class=\"error\">".lang('global', 'are_you_sure')."</font></h1>
          <br />
          <font class=\"bold\">".lang('arenateam', 'arenateam_id').": $id ".lang('global', 'will_be_erased')."</font><br /><br />
          <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
            <input type=\"hidden\" name=\"type\" value=\"arenateam\" />
            <input type=\"hidden\" name=\"check\" value=\"-$id\" />
            <table class=\"hidden\">
              <tr>
                <td>";
                  makebutton(lang('global', 'yes'), "javascript:do_submit()",130);
                  makebutton(lang('global', 'no'), "arenateam.php?action=view_team&amp;id=$id",130);
  $output .= "
                </td>
              </tr>
            </table>
          </form>
          <br />
        </center>
";

}


//##########################################################################################
//REMOVE CHAR FROM TEAM
//##########################################################################################
function rem_char_from_team()
{
  global $characters_db, $realm_id, $user_lvl, $sqlc;

  if(isset($_GET['id'])) $guid = $_GET['id'];
    else redirect("arenateam.php?error=1");
  if(isset($_GET['arenateam_id'])) $arenateam_id = $_GET['arenateam_id'];
    else redirect("arenateam.php?error=1");

    // must be checked that this user can delete it
  //$sql->query("DELETE FROM arena_team_member WHERE guid = '$guid'");

  redirect("arenateam.php?action=view_team&id=$arenateam_id");
}

//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
      <div class=\"bubble\">
        <div class=\"top\">";

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">".lang('global', 'empty_fields')."</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">".lang('global', 'err_no_search_passed')."</font></h1>";
     break;
 case 3:
    $output .= "
          <h1><font class=\"error\">".lang('arenateam', 'arenateam')."</font></h1>";
    break;
  case 4:
    $output .= "
          <h1>{$lang_arenateam ['team_search_result']}:</h1>";
    break;
  default: //no error
    $output .= "
          <h1>{$lang_arenateam ['browse_teams']}</h1>";
}

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "view_team":
    view_team();
    break;
  case "del_team":
    del_team();
    break;
  case "rem_char_from_team":
    rem_char_from_team();
    break;
  default:
    browse_teams();
}

require_once("footer.php");

?>
