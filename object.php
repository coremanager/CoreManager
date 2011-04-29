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

$go_types = array
(
  0 => array(0, lang("game_object", "DOOR")),
  1 => array(1, lang("game_object", "BUTTON")),
  2 => array(2, lang("game_object", "QUESTGIVER")),
  3 => array(3, lang("game_object", "CHEST")),
  4 => array(4, lang("game_object", "BINDER")),
  5 => array(5, lang("game_object", "GENERIC")),
  6 => array(6, lang("game_object", "TRAP")),
  7 => array(7, lang("game_object", "CHAIR")),
  8 => array(8, lang("game_object", "SPELL_FOCUS")),
  9 => array(9, lang("game_object", "TEXT")),
  10 => array(10, lang("game_object", "GOOBER")),
  11 => array(11, lang("game_object", "TRANSPORT")),
  12 => array(12, lang("game_object", "AREADAMAGE")),
  13 => array(13, lang("game_object", "CAMERA")),
  14 => array(14, lang("game_object", "MAP_OBJECT")),
  15 => array(15, lang("game_object", "MO_TRANSPORT")),
  16 => array(16, lang("game_object", "DUEL_FLAG")),
  17 => array(17, lang("game_object", "FISHING_BOBBER")),
  18 => array(18, lang("game_object", "RITUAL")),
  19 => array(19, lang("game_object", "MAILBOX")),
  20 => array(20, lang("game_object", "AUCTIONHOUSE")),
  21 => array(21, lang("game_object", "GUARDPOST")),
  22 => array(22, lang("game_object", "SPELLCASTER")),
  23 => array(23, lang("game_object", "MEETING_STONE")),
  24 => array(24, lang("game_object", "BG_Flag")),
  25 => array(25, lang("game_object", "FISHING_HOLE")),
  26 => array(26, lang("game_object", "FLAGDROP")),
  27 => array(27, lang("game_object", "CUSTOM_TELEPORTER")),
  28 => array(28, lang("game_object", "LOTTERY_KIOSK")),
  29 => array(29, lang("game_object", "CAPTURE_POINT")),
  30 => array(30, lang("game_object", "AURA_GENERATOR")),
  31 => array(31, lang("game_object", "DUNGEON_DIFFICULTY")),
  32 => array(32, lang("game_object", "BARBER_CHAIR")),
  33 => array(33, lang("game_object", "DESTRUCTIBLE_BUILDING")),
  34 => array(34, lang("game_object", "GUILD_BANK"))
);

function get_go_type($flag)
{
  global $go_types;

  if ( isset($go_types[$flag]) )
    return $go_types[$flag][1];
  else
    return lang("game_object", "unknown");
}

//########################################################################################################################
//  PRINT GO SEARCH FORM
//########################################################################################################################
function search()
{
  global $output, $world_db, $realm_id, $base_datasite, $go_datasite, $sql_search_limit, $locales_search_option,
    $itemperpage, $go_types, $sql, $core;

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

  if ( ( !isset($_GET["entry"]) || $_GET["entry"] === "" ) &&
      ( !isset($_GET["name"]) || $_GET["name"] === "" ) &&
      ( !isset($_GET["displayId"]) || $_GET["displayId"] === "" ) &&
      ( !isset($_GET["faction"]) || $_GET["faction"] === "" ) &&
      ( !isset($_GET["flags"]) || $_GET["flags"] === "" ) &&
      ( $_GET["type"] == -1 ) )
  {
    redirect("object.php?error=1");
  }

  if ( $_GET["entry"] != "" )
    $entry = $sql["world"]->quote_smart($_GET["entry"]);
  if ( $_GET["name"] != "" )
    $name = $sql["world"]->quote_smart($_GET["name"]);
  if ( ( $_GET["type"] != "" ) && ( $_GET["type"] != -1 ) )
    $type = $sql["world"]->quote_smart($_GET["type"]);
  if ( $_GET["displayid"] != "" )
    $displayId = $sql["world"]->quote_smart($_GET["displayid"]);
  if ( $_GET["faction"] != "" )
    $faction = $sql["world"]->quote_smart($_GET["faction"]);
  if ( $_GET["flags"] != "" )
    $flags = $sql["world"]->quote_smart($_GET["flags"]);

  // a little XSS prevention
  if ( htmlspecialchars($entry) != $entry )
    $entry = "";
  if ( htmlspecialchars($name, ENT_COMPAT, $site_encoding) != $name )
    $name = "";
  if ( htmlspecialchars($type) != $type )
    $type = -1;
  if ( htmlspecialchars($displayid) != $displayid )
    $displayid = "";
  if ( htmlspecialchars($faction) != $faction )
    $faction = "";
  if ( htmlspecialchars($flags) != $flags )
    $flags = "";

  //wowhead_tt();

  //require_once("./libs/get_lib.php");
  //$deplang = get_lang_id();

  // Filters
  if ( $core == 1 )
    $query = "SELECT COUNT(*) FROM gameobject_names";
  else
    $query = "SELECT COUNT(*) FROM gameobject_template";

  $result = $sql["world"]->query($query);
  $tot_go = $sql["world"]->result($result, 0);

  // we need $type to be set so the <select> will show correctly
  if ( !isset($type) )
    $type = -1;

  $output .= '
        <center>
          <div class="fieldset_border">
            <span class="legend">'.lang("game_object", "search_template").'</span>
            <br />
            <form action="object.php" method="get" name="form">
              <!-- input type="hidden" name="action" value="do_search" / -->
              <input type="hidden" name="error" value="2" />
              <table class="hidden">
                <tr>
                  <td>'.lang("game_object", "entry").':</td>
                  <td>
                    <input type="text" size="14" maxlength="11" name="entry" value="'.$entry.'" />
                  </td>
                  <td>'.lang("game_object", "name").':</td>
                  <td colspan="3">
                    <input type="text" size="45" maxlength="100" name="name" value="'.$name.'" />
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>'.lang("game_object", "type").':</td>
                  <td colspan="3" align="left">
                    <select name="type">
                      <option value="-1"'.( ( $type == -1 ) ? ' selected="selected" ' : '' ).'>'.lang("game_object", "select").'</option>';
  foreach ( $go_types as $row )
    $output .= '
                      <option value="'.$row[0].'"'.( ( $type == $row[0] ) ? ' selected="selected" ' : '' ).'>'.$row[0].' '.$row[1].'</option>';
  $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>'.lang("game_object", "displayId").':</td>
                  <td>
                    <input type="text" size="14" maxlength="11" name="displayid" value="'.$displayid.'" />
                  </td>';
    $output .= '
                  <td>'.lang("game_object", "flags").':</td>
                  <td align="left">
                    <input type="text" size="15" maxlength="11" name="flags" value="'.$flags.'" />
                  </td>';
  if ( $core != 1 )
    $output .= '
                  <td>'.lang("game_object", "faction").':</td>
                  <td align="left">
                    <input type="text" size="14" maxlength="11" name="faction" value="'.$faction.'" />
                  </td>';
  else
    $output .= '
                  <td colspan="2">&nbsp;</td>';
  $output .= '
                </tr>
                <tr>
                  <td colspan="3">';
  makebutton(lang("game_object", "search"), "javascript:do_submit()", 150);
  $output .= '
                  </td>
                  <td colspan="3">'.lang("game_object", "tot_go_templ").': '.$tot_go.'</td>
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
    $where = "gameobject_names.entry>0 ";
  else
    $where = "gameobject_template.entry>0 ";

  $base_where = $where;

  if ( isset($entry) )
  {
    if ( $core == 1 )
      $where .= "AND gameobject_names.entry='".$entry."' ";
    else
      $where .= "AND gameobject_template.entry='".$entry."' ";
  }
  if ( isset($name) )
    $where .= "AND `name` LIKE '%".$name."%' ";
  if ( isset($type) )
    $where .= "AND type='".$type."' ";
  if ( isset($displayId) )
    $where .= "AND displayId='".$displayId."' ";
  if ( isset($faction) )
    $where .= "AND gameobject_template.faction='".$faction."' ";
  if ( isset($flags ) )
    $where .= "AND flags='".$flags."' ";

  //if ( $where == $base_where )
    //redirect("object.php?error=1");

  if ( $core == 1 )
  {
    $query = "SELECT *, Type AS type, DisplayID AS displayId, gameobject_names.Name AS name1".( ( $locales_search_option != 0 ) ? ", gameobject_names_localized.name AS name" : "" )."
              FROM gameobject_names "
                .( ( $locales_search_option != 0 ) ? "LEFT JOIN gameobject_names_localized ON gameobject_names.entry=gameobject_names_localized.entry AND language_code='".$locales_search_option."') " : " " ).
              "WHERE ".$where."
              ORDER BY gameobject_names.entry
              LIMIT ".$start.", ".$itemperpage;
    $query1 = "SELECT COUNT(*) FROM gameobject_names WHERE ".$where;
  }
  else
  {
    $query = "SELECT *
              FROM gameobject_template "
                .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_gameobject ON gameobject_template.entry=locales_gameobject.entry " : "" ).
              "WHERE ".$where."
              ORDER BY gameobject_template.entry
              LIMIT ".$start.", ".$itemperpage;
    $query1 = "SELECT COUNT(*) FROM gameobject_template WHERE ".$where;
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
  makebutton(lang("game_object", "new_search"), "object.php", 160);
  $output .= '
              </td>
              <td align="right">'.lang("game_object", "tot_found").' : '.$total_found.'</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td align="right">';
  $output .= generate_pagination('object.php?order_by='.$order_by.'&amp;dir='.( ($dir) ? 0 : 1 ).( ( $name ) ? '&amp;name='.$name : '' ).( ( $type ) ? '&amp;type='.$type : '' ).( ( $displayid ) ? '&amp;displayid='.$displayid : '' ).( ( $faction ) ? '&amp;faction='.$faction : '' ).( ( $flags ) ? '&amp;flags='.$flags : '' ), $total_found, $itemperpage, $start);
  $output .= '
              </td>
            </tr>
          </table>
          <table class="lined">
            <tr>
              <th width="10%">'.lang("game_object", "entry").'</th>
              <th width="40%">'.lang("game_object", "name").'</th>
              <th width="20%">'.lang("game_object", "type").'</th>
              <th width="15%">'.lang("game_object", "displayId").'</th>';
  if ( $core != 1 )
    $output .= '
              <th width="15%">'.lang("game_object", "faction").'</th>';
  $output .= '
              <th>'.lang("game_object", "spawncount").'</th>
            </tr>';

  for ( $i = 1; $i <= $page_total; $i++ )
  {
    $go = $sql["world"]->fetch_assoc($result);

    // localization
    if ( $core == 1 )
      $go["name"] = ( ( $locales_search_option ) ? $go["name"] : $go["name1"] );
    else
      $go["name"] = ( ( $locales_search_option ) ? $go["name_loc".$locales_search_option] : $go["name"] );

    // individual spawn counts
    if ( $core == 1 )
      $count_query = "SELECT COUNT(*) FROM gameobject_spawns WHERE Entry='".$go["entry"]."'";
    else
      $count_query = "SELECT COUNT(*) FROM gameobject WHERE id='".$go["entry"]."'";

    $count_result = $sql["world"]->query($count_query);
    $count_result = $sql["world"]->fetch_assoc($count_result);
    $spawn_count = $count_result["COUNT(*)"];

    $output .= '
            <tr>
              <td>
                <a href="object.php?action=view&amp;entry='.$go["entry"].( ( $name ) ? '&amp;name='.$name : '' ).( ( $type ) ? '&amp;type='.$type : '' ).( ( $displayid ) ? '&amp;displayid='.$displayid : '' ).( ( $faction ) ? '&amp;faction='.$faction : '' ).( ( $flags ) ? '&amp;flags='.$flags : '' ).'&amp;error=3">'.$go["entry"].'</a>
              </td>
              <td>
                <a href="object.php?action=view&amp;entry='.$go["entry"].( ( $name ) ? '&amp;name='.$name : '' ).( ( $type ) ? '&amp;type='.$type : '' ).( ( $displayid ) ? '&amp;displayid='.$displayid : '' ).( ( $faction ) ? '&amp;faction='.$faction : '' ).( ( $flags ) ? '&amp;flags='.$flags : '' ).'&amp;error=3">'.htmlspecialchars($go["name"], ENT_COMPAT, $site_encoding).'</a>
              </td>
              <td>'.get_go_type($go["type"]).'</td>
              <td>'.$go["displayId"].'</td>';
    if ( $core != 1 )
      $output .= '
              <td>'.$go["faction"].'</td>';
    $output .= '
              <td>'.$spawn_count.'</td>
            </tr>';
  }
  $output .= '
          </table>
          <table class="top_hidden">
            <tr>
              <td align="right">';
  $output .= generate_pagination('object.php?order_by='.$order_by.'&amp;dir='.( ($dir) ? 0 : 1 ).( ( $name ) ? '&amp;name='.$name : '' ).( ( $type ) ? '&amp;type='.$type : '' ).( ( $displayid ) ? '&amp;displayid='.$displayid : '' ).( ( $faction ) ? '&amp;faction='.$faction : '' ).( ( $flags ) ? '&amp;flags='.$flags : '' ), $total_found, $itemperpage, $start);
  $output .= '
              </td>
            </tr>
          </table>
        </center>
        <br />';
}


//########################################################################################################################
// SHOW GAME OBJECT
//########################################################################################################################
function view_go()
{
  global $output, $corem_db, $dbc_db, $locales_search_option, $user_id, $sql, $core;

  // SQL injection prevention
  $entry = ( ( isset($_GET["entry"]) ) ? $sql["world"]->quote_smart($_GET["entry"]) : NULL );

  // retain the other filter values
  $filter_name = $_GET["name"];
  $filter_type = $_GET["type"];
  $filter_displayid = $_GET["displayid"];
  $filter_flags = $_GET["flags"];
  $filter_faction = $_GET["faction"];

  if ( !is_numeric($entry) )
    error(lang("game_object", "NAN"));

  $show = ( ( isset($_GET["show"]) ) ? $sql["world"]->quote_smart($_GET["show"]) : NULL );
  $floor = ( ( isset($_GET["floor"]) ) ? $sql["world"]->quote_smart($_GET["floor"]) : NULL );

  if ( !is_numeric($show) && isset($show) )
    error(lang("game_object", "NAN"));

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
    $go_info_query = "SELECT *, Type AS type, DisplayID AS displayId, gameobject_names.Name AS name1".( ( $locales_search_option != 0 ) ? ", gameobject_names_localized.name AS name" : "" )."
                      FROM gameobject_names "
                        .( ( $locales_search_option != 0 ) ? "LEFT JOIN gameobject_names_localized ON gameobject_names.entry=gameobject_names_localized.entry AND language_code='".$locales_search_option."') " : " " ).
                      "WHERE gameobject_names.entry='".$entry."'";
  else
    $go_info_query = "SELECT * FROM gameobject_template "
                        .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_gameobject ON gameobject_template.entry=locales_gameobject.entry " : "" ).
                      "WHERE gameobject_template.entry='".$entry."'";

  $go_info_result = $sql["world"]->query($go_info_query);
  $go_info = $sql["world"]->fetch_assoc($go_info_result);

  // localization
  if ( $core == 1 )
    $go_info["name"] = ( ( $locales_search_option ) ? $go_info["name"] : $go_info["name1"] );
  else
    $go_info["name"] = ( ( $locales_search_option ) ? $go_info["name_loc".$locales_search_option] : $go_info["name"] );

  // counts & areas
  if ( $core == 1 )
  {
    $query_count = "SELECT COUNT(*) FROM gameobject_spawns
                      LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND gameobject_spawns.map=worldmaparea_fine.Map)
                    WHERE gameobject_spawns.entry='".$entry."' AND (gameobject_spawns.map=0 OR gameobject_spawns.map=1 OR gameobject_spawns.map=530 OR gameobject_spawns.map=571) ORDER BY worldmaparea_fine.AreaTable ASC";
    $query_areas = "SELECT DISTINCT(worldmaparea_fine.AreaTable), worldmaparea_fine.ID, RefCon, Name FROM gameobject_spawns
                      LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND gameobject_spawns.map=worldmaparea_fine.Map)
                    WHERE gameobject_spawns.entry='".$entry."' AND (gameobject_spawns.map=0 OR gameobject_spawns.map=1 OR gameobject_spawns.map=530 OR gameobject_spawns.map=571) ORDER BY worldmaparea_fine.AreaTable ASC";
  }
  else
  {
    $query_count = "SELECT COUNT(*) FROM gameobject
                      LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND gameobject.map=worldmaparea_fine.Map)
                    WHERE gameobject.id='".$entry."' AND (gameobject.map=0 OR gameobject.map=1 OR gameobject.map=530 OR gameobject.map=571) ORDER BY worldmaparea_fine.AreaTable ASC";
    $query_areas = "SELECT DISTINCT(worldmaparea_fine.AreaTable), worldmaparea_fine.ID, RefCon, Name FROM gameobject
                      LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND gameobject.map=worldmaparea_fine.Map)
                    WHERE gameobject.id='".$entry."' AND (gameobject.map=0 OR gameobject.map=1 OR gameobject.map=530 OR gameobject.map=571) ORDER BY worldmaparea_fine.AreaTable ASC";
  }

  $result = $sql["world"]->query($query_count);
  $result = $sql["world"]->fetch_assoc($result);
  $total = $result["COUNT(*)"];

  $result = $sql["world"]->query($query_areas);

  $output .= '
        <div class="gob_header">
          <span class="gob_name">'.$go_info["name"].'</span>
          <hr />
        </div>
        <center>
          <div>
            <span>'.lang("game_object", "foundin").'</span>';

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
      $query_count_by_area = "SELECT COUNT(*) FROM gameobject_spawns
                                LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND gameobject_spawns.map=worldmaparea_fine.Map)
                              WHERE gameobject_spawns.entry='".$entry."' AND (gameobject_spawns.map=0 OR gameobject_spawns.map=1 OR gameobject_spawns.map=530 OR gameobject_spawns.map=571) AND worldmaparea_fine.AreaTable='".$row["AreaTable"]."'";
    else
      $query_count_by_area = "SELECT COUNT(*) FROM gameobject
                                LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND gameobject.map=worldmaparea_fine.Map)
                              WHERE gameobject.id='".$entry."' AND (gameobject.map=0 OR gameobject.map=1 OR gameobject.map=530 OR gameobject.map=571) AND worldmaparea_fine.AreaTable='".$row["AreaTable"]."'";

    $result_count_by_area = $sql["world"]->query($query_count_by_area);
    $result_count_by_area = $sql["world"]->fetch_assoc($result_count_by_area);
    $total_by_area = $result_count_by_area["COUNT(*)"];

    // prevent Zone names from breaking across lines
    $row["Name"] = str_replace(" ", "&nbsp;", $row["Name"]);

    // show zones with spawns and counts
    if ( $row["AreaTable"] != $show )
      $output .= '
            <a href="object.php?action=view&amp;entry='.$entry.'&amp;show='.$row["AreaTable"].( ( $row["AreaTable"] == 4395 ) ? '&amp;floor='.$row["ID"] : '' ).'&amp;error=3">'.$row["Name"]. "</a>&nbsp;(".$total_by_area.') ';
    else
      $output .= '
            <span class="zone_active">'.$row["Name"]. "</span>&nbsp;(".$total_by_area.') ';
  }

  $output .= '
          </div>';

  if ( $core == 1 )
    $query = "SELECT gameobject_spawns.id AS guid, Yw, Xw, position_y, position_x, position_z, worldmaparea.X1 AS XBase, worldmaparea.Y1 AS YBase, Z1, Z2 FROM gameobject_spawns
                LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND gameobject_spawns.map=worldmaparea_fine.Map)
                LEFT JOIN `".$dbc_db["name"]."`.worldmaparea ON worldmaparea_fine.AreaTable=worldmaparea.AreaTable
              WHERE gameobject_spawns.entry='".$entry."' AND (gameobject_spawns.map=0 OR gameobject_spawns.map=1 OR gameobject_spawns.map=530 OR gameobject_spawns.map=571) AND worldmaparea_fine.AreaTable='".$show."'".$display_floor;
  else
    $query = "SELECT guid, Yw, Xw, position_y, position_x, position_z, worldmaparea.X1 AS XBase, worldmaparea.Y1 AS YBase, Z1, Z2 FROM gameobject
                LEFT JOIN `".$corem_db["name"]."`.worldmaparea_fine ON ((position_x<=X1 AND position_x>=X2) AND (position_y<=Y1 AND position_y>=Y2) AND (position_z<=Z1 AND position_z>=Z2) AND gameobject.map=worldmaparea_fine.Map)
                LEFT JOIN `".$dbc_db["name"]."`.worldmaparea ON worldmaparea_fine.AreaTable=worldmaparea.AreaTable
              WHERE gameobject.id='".$entry."' AND (gameobject.map=0 OR gameobject.map=1 OR gameobject.map=530 OR gameobject.map=571) AND worldmaparea_fine.AreaTable='".$show."'".$display_floor;

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
  makebutton(lang("global", "back"), "object.php?error=2&amp;entry=".$entry."&amp;name=".$filter_name."&amp;type=".$filter_type."&amp;displayid=".$filter_displayid."&amp;flags=".$filter_flags."&amp;faction=".$filter_faction, 130);
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
            <font class="error">'.lang("game_object", "search_results").'</font>
          </h1>';
    break;
  case 3:
    $output .= '
          <h1>
            <font class="error">'.lang("game_object", "view_go").'</font>
          </h1>';
    break;
  default: //no error
    $output .= '
          <h1>'.lang("game_object", "search_go").'</h1>';
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
    view_go();
    break;
  default:
    search();
}

require_once("footer.php");
?>
