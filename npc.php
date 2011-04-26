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


require_once("header.php");
require_once 'libs/char_lib.php';
valid_login($action_permission["view"]);

$creature_types = Array(
  0 => array(0, lang('creature', 'other')),
  1 => array(1, lang('creature', 'beast')),
  2 => array(2, lang('creature', 'dragonkin')),
  3 => array(3, lang('creature', 'demon')),
  4 => array(4, lang('creature', 'elemental')),
  5 => array(5, lang('creature', 'giant')),
  6 => array(6, lang('creature', 'undead')),
  7 => array(7, lang('creature', 'humanoid')),
  8 => array(8, lang('creature', 'critter')),
  9 => array(9, lang('creature', 'mechanical')),
  10 => array(10, lang('creature', 'not_specified')),
);

$creature_ranks = Array(
  0 => array(0, lang('creature', 'normal')),
  1 => array(1, lang('creature', 'elite')),
  2 => array(2, lang('creature', 'rare_elite')),
  3 => array(3, lang('creature', 'world_boss')),
  4 => array(4, lang('creature', 'rare'))
);

$creature_families = Array(
  0 => array(0, lang('creature', 'other')),
  1 => array(1, lang('creature', 'wolf')),
  2 => array(2, lang('creature', 'cat')),
  3 => array(3, lang('creature', 'spider')),
  4 => array(4, lang('creature', 'bear')),
  5 => array(5, lang('creature', 'boar')),
  6 => array(6, lang('creature', 'crocolisk')),
  7 => array(7, lang('creature', 'carrion_bird')),
  8 => array(8, lang('creature', 'crab')),
  9 => array(9, lang('creature', 'gorilla')),
  11 => array(11, lang('creature', 'raptor')),
  12 => array(12, lang('creature', 'tallstrider')),
  13 => array(13, lang('creature', 'other')),
  14 => array(14, lang('creature', 'other')),
  15 => array(15, lang('creature', 'felhunter')),
  16 => array(16, lang('creature', 'voidwalker')),
  17 => array(17, lang('creature', 'succubus')),
  18 => array(18, lang('creature', 'other')),
  19 => array(19, lang('creature', 'doomguard')),
  20 => array(20, lang('creature', 'scorpid')),
  21 => array(21, lang('creature', 'turtle')),
  22 => array(22, lang('creature', 'scorpid')),
  23 => array(23, lang('creature', 'imp')),
  24 => array(24, lang('creature', 'bat')),
  25 => array(25, lang('creature', 'hyena')),
  26 => array(26, lang('creature', 'owl')),
  27 => array(27, lang('creature', 'wind_serpent')),
);

function get_creature_type($type)
{
  global $creature_types;

  if ( isset($creature_types[$type]) )
    return $creature_types[$type][1];
  else
    return lang("creature", "unknown");
}

//########################################################################################################################
//  PRINT CREATURE SEARCH FORM
//########################################################################################################################
function search()
{
  global $output, $world_db, $realm_id, $base_datasite, $creature_datasite, $sql_search_limit, $locales_search_option,
    $itemperpage, $creature_types, $creature_ranks, $creature_families, $sql, $core;

  //-------------------SQL Injection Prevention--------------------------------
  $start = ( ( isset($_GET["start"]) ) ? $sql["logon"]->quote_smart($_GET["start"]) : 0 );
  if ( !is_numeric($start) )
    $start = 0;

  $order_by = ( ( isset($_GET["order_by"]) ) ? $sql["logon"]->quote_smart($_GET["order_by"]) : "acct" );
  if ( !preg_match('/^[_[:lower:]]{1,15}$/', $order_by) )
    $order_by = "acct";

  $dir = ( ( isset($_GET["dir"]) ) ? $sql["logon"]->quote_smart($_GET["dir"]) : 1 );
  if ( !preg_match('/^[01]{1}$/', $dir) )
    $dir = 1;

  $order_dir = ( ( $dir ) ? "ASC" : "DESC" );
  $dir = ( ( $dir ) ? 0 : 1 );

  /*if ( ( !isset($_GET["entry"]) || $_GET["entry"] === "" ) &&
      ( !isset($_GET["name"]) || $_GET["name"] === "" ) &&
      ( !isset($_GET["faction"]) || $_GET["faction"] === "" ) &&
      ( $_GET["type"] == -1 ) )
  {
    redirect("npc.php?error=1");
  }*/

  if ( $_GET["entry"] != "" )
    $entry = $sql["world"]->quote_smart($_GET["entry"]);
  if ( $_GET["name"] != "" )
    $name = $sql["world"]->quote_smart($_GET["name"]);
  if ( $_GET["level"] != "" )
    $level = $sql["world"]->quote_smart($_GET["level"]);
  if ( ( $_GET["type"] != "" ) && ( $_GET["type"] != -1 ) )
    $type = $sql["world"]->quote_smart($_GET["type"]);
  if ( ( $_GET["family"] != "" ) && ( $_GET["family"] != -1 ) )
    $family = $sql["world"]->quote_smart($_GET["family"]);
  if ( ( $_GET["rank"] != "" ) && ( $_GET["rank"] != -1 ) )
    $rank = $sql["world"]->quote_smart($_GET["rank"]);
  if ( $_GET["displayid1"] != "" )
    $displayId1 = $sql["world"]->quote_smart($_GET["displayid1"]);
  if ( $_GET["displayid2"] != "" )
    $displayId2 = $sql["world"]->quote_smart($_GET["displayid2"]);
  if ( $_GET["displayid3"] != "" )
    $displayId3 = $sql["world"]->quote_smart($_GET["displayid3"]);
  if ( $_GET["displayid4"] != "" )
    $displayId4 = $sql["world"]->quote_smart($_GET["displayid4"]);
  if ( $core == 1 )
  {
    if ( $_GET["factionA"] != "" )
      $factionA = $sql["world"]->quote_smart($_GET["factionA"]);
  }
  else
  {
    if ( $_GET["factionA"] != "" )
      $factionA = $sql["world"]->quote_smart($_GET["factionA"]);
    if ( $_GET["factionH"] != "" )
      $factionH = $sql["world"]->quote_smart($_GET["factionH"]);
  }

  if ( $_GET["flags"] != "" )
    $flags = $sql["world"]->quote_smart($_GET["flags"]);

  // tally flags
  if ( !isset($flags) )
  {
    if ( isset($_GET["flag_gossip"]) ||  isset($_GET["flag_quest_giver"]) ||
      isset($_GET["flag_trainer"]) || isset($_GET["flag_vendor"]) ||
      isset($_GET["flag_armorer"]) || isset($_GET["flag_taxi"]) || isset($_GET["flag_spirit_healer"]) ||
      isset($_GET["flag_inn_keeper"]) || isset($_GET["flag_banker"]) ||
      isset($_GET["flag_retitioner"]) || isset($_GET["flag_tabard_vendor"]) ||
      isset($_GET["flag_battlemaster"]) || isset($_GET["flag_auctioneer"]) ||
      isset($_GET["flag_stable_master"]) || isset($_GET["flag_guard"]) )
    {
      $flags = 0;
      $flags += $_GET["flag_gossip"];
      $flags += $_GET["flag_quest_giver"];
      $flags += $_GET["flag_trainer"];
      $flags += $_GET["flag_vendor"];
      $flags += $_GET["flag_armorer"];
      $flags += $_GET["flag_taxi"];
      $flags += $_GET["flag_spirit_healer"];
      $flags += $_GET["flag_inn_keeper"];
      $flags += $_GET["flag_banker"];
      $flags += $_GET["flag_retitioner"];
      $flags += $_GET["flag_tabard_vendor"];
      $flags += $_GET["flag_battlemaster"];
      $flags += $_GET["flag_auctioneer"];
      $flags += $_GET["flag_stable_master"];
      $flags += $_GET["flag_guard"];
    }
  }

  // a little XSS prevention
  if ( htmlspecialchars($entry) != $entry )
    $entry = "";
  if ( htmlspecialchars($name, ENT_COMPAT, $site_encoding) != $name )
    $name = "";
  if ( htmlspecialchars($level, ENT_COMPAT, $site_encoding) != $level )
    $level = "";
  if ( htmlspecialchars($type) != $type )
    $type = -1;
  if ( htmlspecialchars($family) != $family )
    $family = -1;
  if ( htmlspecialchars($rank) != $rank )
    $rank = -1;
  if ( htmlspecialchars($displayid1) != $displayid1 )
    $displayid1 = "";
  if ( htmlspecialchars($displayid2) != $displayid2 )
    $displayid2 = "";
  if ( htmlspecialchars($displayid3) != $displayid3 )
    $displayid3 = "";
  if ( htmlspecialchars($displayid4) != $displayid4 )
    $displayid4 = "";
  if ( $core == 1 )
  {
    if ( htmlspecialchars($factionA) != $factionA )
      $factionA = "";
  }
  else
  {
    if ( htmlspecialchars($factionA) != $factionA )
      $factionA = "";
    if ( htmlspecialchars($factionH) != $factionH )
      $factionH = "";
  }

  //wowhead_tt();

  //require_once("./libs/get_lib.php");
  //$deplang = get_lang_id();

  // Filters
  if ( $core == 1 )
    $query = "SELECT COUNT(*) FROM creature_names";
  else
    $query = "SELECT COUNT(*) FROM creature_template";

  $result = $sql["world"]->query($query);
  $tot_go = $sql["world"]->result($result, 0);

  // we need $type, $family, and $rank to be set so the <select> will show correctly
  if ( !isset($type) )
    $type = -1;
  if ( !isset($family) )
    $family = -1;
  if ( !isset($rank) )
    $rank = -1;

  $output .= '
        <center>
          <div class="fieldset_border">
            <span class="legend">'.lang("creature", "search_template").'</span>
            <br />
            <form action="npc.php" method="get" name="form">
              <!-- input type="hidden" name="action" value="do_search" / -->
              <input type="hidden" name="error" value="2" />
              <table class="hidden">
                <tr>
                  <td>'.lang("creature", "entry").':</td>
                  <td>
                    <input type="text" size="14" maxlength="11" name="entry" value="'.$entry.'" />
                  </td>
                  <td>'.lang("creature", "name").':</td>
                  <td colspan="3">
                    <input type="text" size="45" maxlength="100" name="name" value="'.$name.'" />
                  </td>
                </tr>
                <tr>
                  <td colspan="4">'.lang("creature", "npc_flag").':</td>
                  <td>'.lang("creature", "level").':</td>
                  <td align="left">
                    <input type="text" size="15" maxlength="11" name="level" value="'.$level.'" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type="checkbox" name="flag_gossip" value="1" '.( ( $flags & 1 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "gossip").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_quest_giver" value="2" '.( ( $flags & 2 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "quest_giver").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_trainer" value="16" '.( ( $flags & 16 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "trainer").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_vendor" value="128" '.( ( $flags & 128 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "vendor").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_armorer" value="4096" '.( ( $flags & 4096 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "armorer").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_taxi" value="8192" '.( ( $flags & 8192 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "taxi").'
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type="checkbox" name="flag_spirit_healer" value="16384" '.( ( $flags & 16384 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "spirit_healer").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_inn_keeper" value="65536" '.( ( $flags & 65536 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "inn_keeper").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_banker" value="131072" '.( ( $flags & 131072 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "banker").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_retitioner" value="262144" '.( ( $flags & 262144 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "retitioner").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_tabard_vendor" value="524288" '.( ( $flags & 524288 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "tabard_vendor").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_battlemaster" value="1048576" '.( ( $flags & 1048576 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "battlemaster").'
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type="checkbox" name="flag_auctioneer" value="2097152" '.( ( $flags & 2097152 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "auctioneer").'
                  </td>
                  <td>
                    <input type="checkbox" name="flag_stable_master" value="4194304" '.( ( $flags & 4194304 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "stable_master").'
                  </td>
                  <td colspan="4" align="left">
                    <input type="checkbox" name="flag_guard" value="268435456" '.( ( $flags & 268435456 ) ? 'checked="checked"' : '' ).' />'.lang("creature", "guard").'
                  </td>
                </tr>
                <tr>
                  <td>'.lang("creature", "type").':</td>
                  <td align="left">
                    <select name="type">
                      <option value="-1"'.( ( $type == -1 ) ? ' selected="selected" ' : '' ).'>'.lang("creature", "select").'</option>';
  foreach ( $creature_types as $row )
    $output .= '
                      <option value="'.$row[0].'"'.( ( $type == $row[0] ) ? ' selected="selected" ' : '' ).'>'.$row[0].' '.$row[1].'</option>';
  $output .= '
                    </select>
                  </td>
                  <td>'.lang("creature", "family").':</td>
                  <td align="left">
                    <select name="family">
                      <option value="-1"'.( ( $family == -1 ) ? ' selected="selected" ' : '' ).'>'.lang("creature", "select").'</option>';
  foreach ( $creature_families as $row )
    $output .= '
                      <option value="'.$row[0].'"'.( ( $family == $row[0] ) ? ' selected="selected" ' : '' ).'>'.$row[0].' '.$row[1].'</option>';
  $output .= '
                    </select>
                  </td>
                  <td>'.lang("creature", "rank").':</td>
                  <td align="left">
                    <select name="rank">
                      <option value="-1"'.( ( $rank == -1 ) ? ' selected="selected" ' : '' ).'>'.lang("creature", "select").'</option>';
  foreach ( $creature_ranks as $row )
    $output .= '
                      <option value="'.$row[0].'"'.( ( $rank == $row[0] ) ? ' selected="selected" ' : '' ).'>'.$row[0].' '.$row[1].'</option>';
  $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>'.lang("creature", "displayId1").':</td>
                  <td>
                    <input type="text" size="14" maxlength="11" name="displayid1" value="'.$displayid1.'" />
                  </td>
                  <td>'.lang("creature", "displayId2").':</td>
                  <td align="left">
                    <input type="text" size="15" maxlength="11" name="displayid2" value="'.$displayid2.'" />
                  </td>';
  if ( $core == 1 )
    $output .= '
                  <td>'.lang("creature", "faction").':</td>
                  <td align="left">
                    <input type="text" size="14" maxlength="11" name="factionA" value="'.$factionA.'" />
                  </td>';
  else
    $output .= '
                  <td>'.lang("creature", "faction_A").':</td>
                  <td align="left">
                    <input type="text" size="14" maxlength="11" name="factionA" value="'.$factionA.'" />
                  </td>
                </tr>
                <tr>
                  <td>'.lang("creature", "displayId1").':</td>
                  <td>
                    <input type="text" size="14" maxlength="11" name="displayid3" value="'.$displayid3.'" />
                  </td>
                  <td>'.lang("creature", "displayId2").':</td>
                  <td align="left">
                    <input type="text" size="15" maxlength="11" name="displayid4" value="'.$displayid4.'" />
                  </td>
                  <td>'.lang("creature", "faction_H").':</td>
                  <td align="left">
                    <input type="text" size="14" maxlength="11" name="factionH" value="'.$factionH.'" />
                  </td>';
  $output .= '
                </tr>
                <tr>
                  <td colspan="3">';
  makebutton(lang("creature", "search"), "javascript:do_submit()", 150);
  $output .= '
                  </td>
                  <td colspan="3">'.lang("creature", "tot_creature_templ").': '.$tot_go.'</td>
                </tr>
              </table>
            </form>
          </div>
          <br />
          <br />';

  // now we only want $type if it has REAL content
  if ( $type == -1 )
    unset($type);

  // Show filtered game object list
  if ( $core == 1 )
    $where = "creature_names.entry>0 ";
  else
    $where = "creature_template.entry>0 ";

  $base_where = $where;

  if ( isset($entry) )
  {
    if ( $core == 1 )
      $where .= "AND creature_names.entry='".$entry."' ";
    else
      $where .= "AND creature_template.entry='".$entry."' ";
  }
  if ( isset($name) )
    $where .= "AND `name` LIKE '%".$name."%' ";
  if ( isset($type) )
    $where .= "AND type='".$type."' ";
  if ( $core == 1 )
  {
    if ( isset($displayId1) )
      $where .= "AND male_displayid='".$displayId1."' ";
    if ( isset($displayId2) )
      $where .= "AND female_displayid='".$displayId2."' ";
    if ( isset($displayId3) )
      $where .= "AND male_displayid2='".$displayId3."' ";
    if ( isset($displayId4) )
      $where .= "AND female_displayid2='".$displayId4."' ";
  }
  else
  {
    if ( isset($displayId1) )
      $where .= "AND modelid1='".$displayId1."' ";
    if ( isset($displayId2) )
      $where .= "AND modelid2='".$displayId2."' ";
    if ( isset($displayId3) )
      $where .= "AND modelid3='".$displayId3."' ";
    if ( isset($displayId4) )
      $where .= "AND modelid4='".$displayId4."' ";
  }
  if ( $core == 1 )
  {
    if ( isset($factionA) )
      $where .= "AND creature_template.faction='".$factionA."' ";
  }
  else
  {
    if ( isset($factionA) )
      $where .= "AND creature_template.faction_A='".$factionA."' ";
    if ( isset($factionH) )
      $where .= "AND creature_template.faction_H='".$factionH."' ";
  }
  if ( $core == 1 )
  {
    if ( isset($flags ) )
      $where .= "AND flags1&&'".$flags."' ";
  }
  else
  {
    if ( isset($flags ) )
      $where .= "AND npcflag&&'".$flags."' ";
  }

  //if ( $where == $base_where )
    //redirect("object.php?error=1");

  if ( $core == 1 )
  {
    $query = "SELECT *, Type AS type, creature_names.Name AS name1".( ( $locales_search_option != 0 ) ? ", creature_names_localized.name AS name" : "" )."
              FROM creature_names "
                .( ( $locales_search_option != 0 ) ? "LEFT JOIN creature_names_localized ON creature_names.entry=creature_names_localized.entry AND language_code='".$locales_search_option."') " : " " ).
              "WHERE ".$where."
              ORDER BY creature_names.entry
              LIMIT ".$start.", ".$itemperpage;
    $query1 = "SELECT COUNT(*) FROM creature_names WHERE ".$where;
  }
  else
  {
    $query = "SELECT *
              FROM creature_template "
                .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_creature ON creature_template.entry=locales_creature.entry " : "" ).
              "WHERE ".$where."
              ORDER BY creature_template.entry
              LIMIT ".$start.", ".$itemperpage;
    $query1 = "SELECT COUNT(*) FROM creature_template WHERE ".$where;
  }

  $result = $sql["world"]->query($query);
  $page_total = $sql["world"]->num_rows($result);

  $total_result = $sql["world"]->query($query1);
  $total_result = $sql["world"]->fetch_assoc($total_result);
  $total_found = $total_result["COUNT(*)"];

  $output .= '
          <table class="top_hidden">
            <tr>
              <td>';
  makebutton(lang("creature", "new_search"), "npc.php", 160);
  $output .= '
              </td>
              <td align="right">'.lang("creature", "tot_found").' : '.$total_found.'</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td align="right">';
  $output .= generate_pagination('npc.php?order_by='.$order_by.'&amp;dir='.( ($dir) ? 0 : 1 ).( ( $name ) ? '&amp;name='.$name : '' ).( ( $level ) ? '&amp;level='.$level : '' ).( ( $type ) ? '&amp;type='.$type : '' ).( ( $family ) ? '&amp;family='.$family : '' ).( ( $rank ) ? '&amp;rank='.$rank : '' ).( ( $displayid1 ) ? '&amp;displayid1='.$displayid1 : '' ).( ( $displayid2 ) ? '&amp;displayid2='.$displayid2 : '' ).( ( $displayid3 ) ? '&amp;displayid3='.$displayid3 : '' ).( ( $displayid4 ) ? '&amp;displayid4='.$displayid4 : '' ).( ( $factionA ) ? '&amp;factionA='.$factionA : '' ).( ( $factionH ) ? '&amp;factionH='.$factionH : '' ).( ( $flags ) ? '&amp;flags='.$flags : '' ), $total_found, $itemperpage, $start);
  $output .= '
              </td>
            </tr>
          </table>
          <table class="lined">
            <tr>
              <th width="10%">'.lang("creature", "entry").'</th>
              <th width="40%">'.lang("creature", "name").'</th>
              <th width="15%">'.lang("creature", "type").'</th>';
  if ( $core == 1 )
    $output .= '
              <th width="20%">'.lang("creature", "faction").'</th>';
  else
    $output .= '
              <th width="20%">'.lang("creature", "faction_A").'</th>
              <th width="20%">'.lang("creature", "faction_H").'</th>';
  $output .= '
            </tr>';

  for ( $i = 1; $i <= $page_total; $i++ )
  {
    $creature = $sql["world"]->fetch_assoc($result);

    // localization
    if ( $core == 1 )
      $creature["name"] = ( ( $locales_search_option ) ? $creature["name"] : $creature["name1"] );
    else
      $creature["name"] = ( ( $locales_search_option ) ? $creature["name_loc".$locales_search_option] : $creature["name"] );

    $output .= '
            <tr>
              <td>
                <a href="npc.php?action=view&amp;entry='.$creature["entry"].( ( $name ) ? '&amp;name='.$name : '' ).( ( $level ) ? '&amp;level='.$level : '' ).( ( $type ) ? '&amp;type='.$type : '' ).( ( $family ) ? '&amp;family='.$family : '' ).( ( $rank ) ? '&amp;rank='.$rank : '' ).( ( $displayid1 ) ? '&amp;displayid1='.$displayid1 : '' ).( ( $displayid2 ) ? '&amp;displayid2='.$displayid2 : '' ).( ( $displayid3 ) ? '&amp;displayid3='.$displayid3 : '' ).( ( $displayid4 ) ? '&amp;displayid4='.$displayid4 : '' ).( ( $factionA ) ? '&amp;factionA='.$factionA : '' ).( ( $factionH ) ? '&amp;factionH='.$factionH : '' ).( ( $flags ) ? '&amp;flags='.$flags : '' ).'&amp;error=3">'.$creature["entry"].'</a>
              </td>
              <td>
                <a href="npc.php?action=view&amp;entry='.$creature["entry"].( ( $name ) ? '&amp;name='.$name : '' ).( ( $level ) ? '&amp;level='.$level : '' ).( ( $type ) ? '&amp;type='.$type : '' ).( ( $family ) ? '&amp;family='.$family : '' ).( ( $rank ) ? '&amp;rank='.$rank : '' ).( ( $displayid1 ) ? '&amp;displayid1='.$displayid1 : '' ).( ( $displayid2 ) ? '&amp;displayid2='.$displayid2 : '' ).( ( $displayid3 ) ? '&amp;displayid3='.$displayid3 : '' ).( ( $displayid4 ) ? '&amp;displayid4='.$displayid4 : '' ).( ( $factionA ) ? '&amp;factionA='.$factionA : '' ).( ( $factionH ) ? '&amp;factionH='.$factionH : '' ).( ( $flags ) ? '&amp;flags='.$flags : '' ).'&amp;error=3">'.htmlspecialchars($creature["name"], ENT_COMPAT, $site_encoding).'</a>
              </td>
              <td>'.get_creature_type($creature["type"]).'</td>';
    if ( $core == 1 )
      $output .= '
              <td>'.$creature["faction"].'</td>';
    else
      $output .= '
              <td>'.$creature["faction_A"].'</td>
              <td>'.$creature["faction_H"].'</td>';
    $output .= '
            </tr>';
  }
  $output .= '
          </table>
          <table class="top_hidden">
            <tr>
              <td align="right">';
  $output .= generate_pagination('npc.php?order_by='.$order_by.'&amp;dir='.( ($dir) ? 0 : 1 ).( ( $name ) ? '&amp;name='.$name : '' ).( ( $level ) ? '&amp;level='.$level : '' ).( ( $type ) ? '&amp;type='.$type : '' ).( ( $family ) ? '&amp;family='.$family : '' ).( ( $rank ) ? '&amp;rank='.$rank : '' ).( ( $displayid ) ? '&amp;displayid='.$displayid : '' ).( ( $factionA ) ? '&amp;factionA='.$factionA : '' ).( ( $factionH ) ? '&amp;factionH='.$factionH : '' ).( ( $flags ) ? '&amp;flags='.$flags : '' ), $total_found, $itemperpage, $start);
  $output .= '
              </td>
            </tr>
          </table>
        </center>
        <br />';
}


//########################################################################################################################
// SHOW CREATURE
//########################################################################################################################
function view_creature()
{
  global $output, $corem_db, $dbc_db, $locales_search_option, $user_id, $sql, $core;

  // SQL injection prevention
  $entry = ( ( isset($_GET["entry"]) ) ? $sql["world"]->quote_smart($_GET["entry"]) : NULL );

  // retain the other filter values
  $filter_name = $_GET["name"];
  /*$filter_type = $_GET["type"];
  $filter_displayid = $_GET["displayid"];*/
  $filter_flags = $_GET["flags"];
  /*$filter_faction = $_GET["faction"];*/

  if ( !is_numeric($entry) )
    error(lang("creature", "NAN"));

  $show = ( ( isset($_GET["show"]) ) ? $sql["world"]->quote_smart($_GET["show"]) : NULL );
  $floor = ( ( isset($_GET["floor"]) ) ? $sql["world"]->quote_smart($_GET["floor"]) : NULL );

  if ( !is_numeric($show) && isset($show) )
    error(lang("creature", "NAN"));

  // Make sure we have correct values if we're showing Dalaran & The Underbelly
  if ( $show == 4395 )
  {
    if ( $floor == 505 )
      $display_floor = ' AND worldmaparea_fine.ID=505';
    else
      // we default to Dalaran
      $display_floor = ' AND worldmaparea_fine.ID=504';
  }
  else
    // we aren't showing Dalaran or The Underbelly
    $display_floor = '';

  // object info
  if ( $core == 1 )
    $creature_info_query = "SELECT *, Type AS type, DisplayID AS displayId, creature_names.Name AS name1".( ( $locales_search_option != 0 ) ? ", creature_names_localized.name AS name" : "" )."
                      FROM creaturenames "
                        .( ( $locales_search_option != 0 ) ? "LEFT JOIN creature_names_localized ON creature_names.entry=creature_names_localized.entry AND language_code='".$locales_search_option."') " : " " ).
                      "WHERE creature_names.entry='".$entry."'";
  else
    $creature_info_query = "SELECT * FROM creature_template "
                        .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_creature ON creature_template.entry=locales_creature.entry " : "" ).
                      "WHERE creature_template.entry='".$entry."'";

  $creature_info_result = $sql["world"]->query($creature_info_query);
  $creature_info = $sql["world"]->fetch_assoc($creature_info_result);

  // localization
  if ( $core == 1 )
    $creature_info["name"] = ( ( $locales_search_option ) ? $creature_info["name"] : $creature_info["name1"] );
  else
    $creature_info["name"] = ( ( $locales_search_option ) ? $creature_info["name_loc".$locales_search_option] : $creature_info["name"] );

  // counts & areas
  if ( $core == 1 )
  {
    $query_count = "SELECT COUNT(*) FROM creature_spawns
                      LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND creature_spawns.map=worldmaparea_fine.Map)
                    WHERE creature_spawns.entry='".$entry."' AND (creature_spawns.map=0 OR creature_spawns.map=1 OR creature_spawns.map=530 OR creature_spawns.map=571) ORDER BY worldmaparea_fine.AreaTable ASC";
    $query_areas = "SELECT DISTINCT(worldmaparea_fine.AreaTable), worldmaparea_fine.ID, RefCon, Name FROM creature_spawns
                      LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND creature_spawns.map=worldmaparea_fine.Map)
                    WHERE creature_spawns.entry='".$entry."' AND (creature_spawns.map=0 OR creature_spawns.map=1 OR creature_spawns.map=530 OR creature_spawns.map=571) ORDER BY worldmaparea_fine.AreaTable ASC";
  }
  else
  {
    $query_count = "SELECT COUNT(*) FROM creature
                      LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND creature.map=worldmaparea_fine.Map)
                    WHERE creature.id='".$entry."' AND (creature.map=0 OR creature.map=1 OR creature.map=530 OR creature.map=571) ORDER BY worldmaparea_fine.AreaTable ASC";
    $query_areas = "SELECT DISTINCT(worldmaparea_fine.AreaTable), worldmaparea_fine.ID, RefCon, Name FROM creature
                      LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND creature.map=worldmaparea_fine.Map)
                    WHERE creature.id='".$entry."' AND (creature.map=0 OR creature.map=1 OR creature.map=530 OR creature.map=571) ORDER BY worldmaparea_fine.AreaTable ASC";
  }

  $result = $sql["world"]->query($query_count);
  $result = $sql["world"]->fetch_assoc($result);
  $total = $result["COUNT(*)"];

  $result = $sql["world"]->query($query_areas);

  $output .= '
        <div class="gob_header">
          <span class="gob_name">'.$creature_info["name"].'</span>
          <hr />
        </div>
        <center>
          <div>
            <span>'.lang("creature", "foundin").'</span>';

  while ( $row = $sql["world"]->fetch_assoc($result) )
  {
    if ( !isset($show) )
    {
      // no map selected, we'll show the first
      $show = $row["AreaTable"];
      $show_map = $row;
    }
    else
    {
      // map selected, we'll show it
      if ( !isset($show_map) && ( $show == $row["AreaTable"] ) )
        $show_map = $row;
    }

    if ( $core == 1 )
      $query_count_by_area = "SELECT COUNT(*) FROM creature_spawns
                                LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND creature_spawns.map=worldmaparea_fine.Map)
                              WHERE creature_spawns.entry='".$entry."' AND (creature_spawns.map=0 OR creature_spawns.map=1 OR creature_spawns.map=530 OR creature_spawns.map=571) AND worldmaparea_fine.AreaTable='".$row["AreaTable"]."'";
    else
      $query_count_by_area = "SELECT COUNT(*) FROM creature
                                LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND creature.map=worldmaparea_fine.Map)
                              WHERE creature.id='".$entry."' AND (creature.map=0 OR creature.map=1 OR creature.map=530 OR creature.map=571) AND worldmaparea_fine.AreaTable='".$row["AreaTable"]."'";

    $result_count_by_area = $sql["world"]->query($query_count_by_area);
    $result_count_by_area = $sql["world"]->fetch_assoc($result_count_by_area);
    $total_by_area = $result_count_by_area["COUNT(*)"];

    // prevent Zone names from breaking across lines
    $row["Name"] = str_replace(" ", "&nbsp;", $row["Name"]);

    // show zones with spawns and counts
    if ( $row["AreaTable"] != $show )
      $output .= '
            <a href="npc.php?action=view&amp;entry='.$entry.'&amp;show='.$row["AreaTable"].( ( $row["AreaTable"] == 4395 ) ? '&amp;floor='.$row["ID"] : '' ).'&amp;error=3">'.$row["Name"]. "</a>&nbsp;(".$total_by_area.') ';
    else
      $output .= '
            <span class="zone_active">'.$row["Name"]. "</span>&nbsp;(".$total_by_area.') ';
  }

  $output .= '
          </div>';

  if ( $core == 1 )
    $query = "SELECT creature_spawns.id AS guid, Yw, Xw, position_y, position_x, position_z, worldmaparea.X1 AS XBase, worldmaparea.Y1 AS YBase, Z1, Z2 FROM creature_spawns
                LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND creature_spawns.map=worldmaparea_fine.Map)
                LEFT JOIN `".$dbc_db["name"]."`.worldmaparea ON worldmaparea_fine.AreaTable=worldmaparea.AreaTable
              WHERE creature_spawns.entry='".$entry."' AND (creature_spawns.map=0 OR creature_spawns.map=1 OR creature_spawns.map=530 OR creature_spawns.map=571) AND worldmaparea_fine.AreaTable='".$show."'".$display_floor;
  else
    $query = "SELECT guid, Yw, Xw, position_y, position_x, position_z, worldmaparea.X1 AS XBase, worldmaparea.Y1 AS YBase, Z1, Z2 FROM creature
                LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND creature.map=worldmaparea_fine.Map)
                LEFT JOIN `".$dbc_db["name"]."`.worldmaparea ON worldmaparea_fine.AreaTable=worldmaparea.AreaTable
              WHERE creature.id='".$entry."' AND (creature.map=0 OR creature.map=1 OR creature.map=530 OR creature.map=571) AND worldmaparea_fine.AreaTable='".$show."'".$display_floor;

  $result = $sql["world"]->query($query);

  // Dalaran or The Underbelly
  if ( isset($floor) )
  {
    if ( $floor == 505 )
      $show_map["RefCon"] = "Underbelly";
    else
      $show_map["RefCon"] = "Dalaran";
  }

  // show selected map
  $output .= '
        </center>
        <div class="mini_map">
          <img src="img/map/area_small/'.$show_map["RefCon"].'.png" alt="'.$show_map["Name"].'" />';

  // temp storage for Area Z's
  $area_Z1 = 0;
  $area_Z2 = 0;

  // draw object pins
  while ( $row = $sql["world"]->fetch_assoc($result) )
  {
    $x_scale = 488 / $row["Yw"];
    $y_scale = 326 / $row["Xw"];

    // Dalaran & The Underbelly don't have proper entries
    if ( $show == 4395 )
    {
      $row["YBase"] = 1051;
      $row["XBase"] = 6073;
    }

    $x_loc_scaled = (floor(abs($row["position_y"] - $row["YBase"]) * $y_scale));
    $y_loc_scaled = (floor(abs($row["position_x"] - $row["XBase"]) * $x_scale));

    $gps_x = round(($x_loc_scaled / 488) * 100, 2);
    $gps_y = round(($y_loc_scaled / 326) * 100, 2);

    $x_relative = $x_loc_scaled;// + 152;
    $y_relative = $y_loc_scaled;

    $area_Z1 = $row["Z1"];
    $area_Z2 = $row["Z2"];

    $output .= '
          <div class="gps_tooltip" id="tooltip'.$row["guid"].'" style="left: '.($x_relative+10).'px; top:'.($y_relative+10).'px;">
            <table>
              <tr>
                <td>'.$gps_x.', '.$gps_y.'</td>
              </tr>
            </table>
          </div>
          <img src="img/map/pin.gif" alt="'.$gps_x." ".$gps_y.'" style="position: absolute; left: '.$x_relative.'px; top: '.$y_relative.'px;" onmouseover="ShowTooltip(this,'.$row["guid"].');" onmouseout="HideTooltip('.$row["guid"].');" />';
  }

  // get our characters
  if ( $core == 1 )
    $query_chars = "SELECT guid, race, class, level, gender, characters.name AS cname, Yw, Xw, positionY AS position_y, positionX AS position_x, positionZ AS position_z, worldmaparea.X1 AS XBase, worldmaparea.Y1 AS YBase FROM characters
                      LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((positionX<=X1 AND positionX>=X2) AND (positionY<=Y1 AND positionY>=Y2) AND (positionZ<=Z1 AND positionZ>=Z2) AND characters.mapId=worldmaparea_fine.Map)
                      LEFT JOIN `".$dbc_db["name"]."`.worldmaparea ON worldmaparea_fine.AreaTable=worldmaparea.AreaTable
                    WHERE acct='".$user_id."' AND (positionZ<='".$area_Z1."' AND positionZ>='".$area_Z2."') AND (characters.mapId=0 OR characters.mapId=1 OR characters.mapId=530 OR characters.mapId=571) AND worldmaparea_fine.AreaTable='".$show."'";
  else
    $query_chars = "SELECT guid, race, class, level, gender, characters.name AS cname, Yw, Xw, position_y, position_x, position_z, worldmaparea.X1 AS XBase, worldmaparea.Y1 AS YBase FROM characters
                      LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND characters.map=worldmaparea_fine.Map)
                      LEFT JOIN `".$dbc_db["name"]."`.worldmaparea ON worldmaparea_fine.AreaTable=worldmaparea.AreaTable
                    WHERE account='".$user_id."' AND (position_z<='".$area_Z1."' AND position_z>='".$area_Z2."') AND (characters.map=0 OR characters.map=1 OR characters.map=530 OR characters.map=571) AND worldmaparea_fine.AreaTable='".$show."'";

  $result_chars = $sql["char"]->query($query_chars);

  // draw character pins
  while ( $row = $sql["char"]->fetch_assoc($result_chars) )
  {
    $x_scale = 488 / $row["Yw"];
    $y_scale = 326 / $row["Xw"];

    // Dalaran & The Underbelly don't have proper entries
    if ( $show == 4395 )
    {
      $row["YBase"] = 1051;
      $row["XBase"] = 6073;
    }

    $x_loc_scaled = (floor(abs($row["position_y"] - $row["YBase"]) * $y_scale));
    $y_loc_scaled = (floor(abs($row["position_x"] - $row["XBase"]) * $x_scale));

    $gps_x = round(($x_loc_scaled / 488) * 100, 2);
    $gps_y = round(($y_loc_scaled / 326) * 100, 2);

    $x_relative = $x_loc_scaled;// + 152;
    $y_relative = $y_loc_scaled;

    $output .= '
          <div class="map_tooltip" id="tooltip'.$row["guid"].'" style="left: '.($x_relative+10).'px; top:'.($y_relative+10).'px;">
            <table>
              <tr>
                <td class="name_level" colspan="2">
                  '.$row["cname"].' ('.char_get_level_color($row["level"]).')
                </td>
              </tr>
              <tr>
                <td class="race">
                  <img src="img/c_icons/'.$row["race"].'-'.$row["gender"].'.gif" />
                </td>
                <td>
                  '.char_get_race_name($row["race"]).'
                </td>
              </tr>
              <tr>
                <td class="race">
                  <img src="img/c_icons/'.$row["class"].'.gif" />
                </td>
                <td>
                  '.char_get_class_name($row["class"]).'
                </td>
              </tr>
              <tr>
                <td class="zone" colspan="2">'.$gps_x.', '.$gps_y.'</td>
              </tr>
            </table>
          </div>
          <img src="img/map/'.( char_get_side_id($row["race"]) ? 'horde' : 'allia' ).'.gif" alt="'.$gps_x." ".$gps_y.'" style="position: absolute; left: '.$x_relative.'px; top: '.$y_relative.'px;" onmouseover="ShowTooltip(this,'.$row["guid"].');" onmouseout="HideTooltip('.$row["guid"].');" />';
  }

  $output .= '
        </div>
        <table>
          <tr>
            <td>';
  makebutton(lang("global", "back"), "npc.php?error=2&amp;entry=".$entry."&amp;name=".$filter_name."&amp;type=".$filter_type."&amp;displayid=".$filter_displayid."&amp;flags=".$filter_flags."&amp;faction=".$filter_faction, 130);
  $output .= '
            </td>
          </tr>
        </table>
        <br />';
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
          <h1>
            <font class="error">'.lang("global", "empty_fields").'</font>
          </h1>';
    break;
  case 2:
    $output .= '
          <h1>
            <font class="error">'.lang("creature", "search_results").'</font>
          </h1>';
    break;
  case 3:
    $output .= '
          <h1>
            <font class="error">'.lang("creature", "view_creature").'</font>
          </h1>';
    break;
  default: //no error
    $output .= '
          <h1>'.lang("creature", "search_creatures").'</h1>';
}

$output .= '
        </div>';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

switch ( $action )
{
  case "search":
    search();
    break;
  case "view":
    view_creature();
    break;
  default:
    search();
}

require_once("footer.php");
?>
