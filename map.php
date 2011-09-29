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
require_once 'libs/map_zone_lib.php';
valid_login($action_permission["view"]);

function show_map()
{
  global $output, $map_status_gm_include_all, $map_gm_add_suffix, $user_id, $realm_id, $logon_db,
    $sql, $core;

  // if the user selected a specific map, we'll show it, otherwise we show Azeroth
  $showmap = (isset($_GET["map"])) ? $_GET["map"] : -1;

  // get the user's selection for whether to view only characters that are online, offline, or both
  // (the default is online only)
  $online = (isset($_GET["online"])) ? $_GET["online"] : 1;

  // get both factions for this realm
  $bf_query = "SELECT Both_Factions FROM config_servers WHERE `Index`='".$realm_id."'";
  $bf_result = $sql["mgr"]->query($bf_query);
  $bf_result = $sql["mgr"]->fetch_assoc($bf_result);
  $both_factions = $bf_result["Both_Factions"];

  // if both factions is disabled then we need to know what faction the player is
  // we'll count the number of characters of each faction the player has and base
  // the players faction on the higher of the two
  if ( !$both_factions )
  {
    if ( $core == 1 )
    {
      $q_horde = "SELECT COUNT(*) FROM characters WHERE race NOT IN (1, 3, 4, 7, 11) AND acct='".$user_id."'";
      $q_alliance = "SELECT COUNT(*) FROM characters WHERE race IN (1, 3, 4, 7, 11) AND acct='".$user_id."'";
    }
    else
    {
      $q_horde = "SELECT COUNT(*) FROM characters WHERE race NOT IN (1, 3, 4, 7, 11) AND account='".$user_id."'";
      $q_alliance = "SELECT COUNT(*) FROM characters WHERE race IN (1, 3, 4, 7, 11) AND account='".$user_id."'";
    }

    $r_horde = $sql["char"]->query($q_horde);
    $r_alliance = $sql["char"]->query($q_alliance);

    $c_horde = $sql["char"]->fetch_assoc($r_horde);
    $c_alliance = $sql["char"]->fetch_assoc($r_alliance);

    $c_horde = $c_horde["COUNT(*)"];
    $c_alliance = $c_alliance["COUNT(*)"];

    if ( $c_horde != $c_alliance )
    {
      if ( $c_horde < $c_alliance )
        $faction = 0;
      else
        $faction = 1;
    }
    else
    {
      // if the player has an equal number of horde and alliance characters then the first character returned in this
      // query will be the players faction
      if ( $core == 1 )
        $f_query = "SELECT race FROM characters WHERE acct='".$user_id."' LIMIT 1";
      else
        $f_query = "SELECT race FROM characters WHERE account='".$user_id."' LIMIT 1";

      $f_result = $sql["char"]->query($f_query);
      $f_result = $sql["char"]->fetch_assoc($f_result);
      $race = $f_result["race"];

      $alliance = array(1, 3, 4, 7, 11);

      if ( in_array($race, $alliance) )
        $faction = 0;
      else
        $faction = 1;
    }
  }

  // if we created the faction variable then we need to insert a further limit on where clauses of the character queries below
  if ( isset($faction) )
  {
    // horde = 1, alliance = 0
    $faction_append = " AND race ".( ( $faction ) ? "NOT " : "" )."IN (1, 3, 4, 7, 11)";
  }
  else
    $faction_append = ""; // otherwise, we'll just insert nothing

  $output .= '
          <table class="hidden">
            <tr>';

  // we don't want to show the button for the map we're currently viewing
  $output .= '
              <td>';

  if ( $showmap <> -1 )
    makebutton(lang("map", "azeroth"), 'map.php?map=-1&online='.$online, 150);
  else
    $output .= '
              <div class="dis_button" style="width: 150px;">'
                .lang("map", "azeroth").
              '</div>';

  $output .= '
              </td>
              <td>';

  if ( $showmap <> 530 )
    makebutton(lang("map", "outland"), 'map.php?map=530&online='.$online, 150);
  else
    $output .= '
              <div class="dis_button" style="width: 150px;">'
                .lang("map", "outland").
              '</div>';

  $output .= '
              </td>
            </tr>
            <tr>
              <td>';

  if ( $showmap <> 0 )
    makebutton(lang("map", "ek"), 'map.php?map=0&online='.$online, 150);
  else
    $output .= '
              <div class="dis_button" style="width: 150px;">'
                .lang("map", "ek").
              '</div>';

  $output .= '
              </td>
              <td>';

  if ( $showmap <> 1 )
    makebutton(lang("map", "k"), 'map.php?map=1&online='.$online, 150);
  else
    $output .= '
              <div class="dis_button" style="width: 150px;">'
                .lang("map", "k").
              '</div>';

  $output .= '
              </td>
              <td>';

  if ( $showmap <> 571 )
    makebutton(lang("map", "nr"), 'map.php?map=571&online='.$online, 150);
  else
    $output .= '
              <div class="dis_button" style="width: 150px;">'
                .lang("map", "nr").
              '</div>';

  $output .= '
              </td>
            </tr>
            <tr>
              <td colspan="3">'
              .lang("map", "display").
              '</td>
            </tr>
            <tr>';

  // make the buttons for online/offline/both viewing
  $output .= '
              <td>';

  if ( $online <> 1 )
    makebutton(lang("map", "online"), 'map.php?map='.$showmap.'&online=1', 150);
  else
    $output .= '
              <div class="dis_button" style="width: 150px;">'
                .lang("map", "online").
              '</div>';

  $output .= '
              </td>
              <td>';

  if ( $online <> 0 )
    makebutton(lang("map", "offline"), 'map.php?map='.$showmap.'&online=0', 150);
  else
    $output .= '
              <div class="dis_button" style="width: 150px;">'
                .lang("map", "offline").
              '</div>';

  $output .= '
              </td>
              <td>';

  if ( $online <> -1 )
    makebutton(lang("map", "both"), 'map.php?map='.$showmap.'&online=-1', 150);
  else
    $output .= '
              <div class="dis_button" style="width: 150px;">'
                .lang("map", "both").
              '</div>';

  $output .= '
              </td>
            </tr>
          </table>';

  // show the appropriate map
  switch ( $showmap )
  {
    case -1:
    {
      $mapfilename = "world";
      break;
    }
    case 0:
    {
      $mapfilename = "easternkingdoms";
      break;
    }
    case 1:
    {
      $mapfilename = "kalimdor";
      break;
    }
    case 530:
    {
      $mapfilename = "outland";
      break;
    }
    case 571:
    {
      $mapfilename = "northrend";
      break;
    }
  }

  // if we're only showing one faction, we should make the viewer aware of that fact
  if ( isset($faction) )
  {
    $fact_color = ( ( $faction ) ? "map_horde" : "map_alliance" );

    $limited = str_replace("%1", '<span class="'.$fact_color.'">'.char_get_side_name($faction).'</span>', lang("map", "only"));

    $output .= '
          <br />';
    $output .= '<b>('.$limited.')</b>';
    $output .= '
          <br />';
  }

  $output .= '
          <div class="map_map">
            <br />
            <img src="img/map/'.$mapfilename.'1.png" id="map_image" />
            <img src="img/map/'.$mapfilename.'2.png" id="map_image" />
            <img src="img/map/'.$mapfilename.'3.png" id="map_image" />';

  // generate the queries based on which map we're viewing
  // GM status here is based on the core's opinion of who is a GM, NOT on CoreManager's Security Level
  // we filter out anyone on GM island also
  // if we're viewing map 530 we don't want to get characters in:
  //   Eversong Woods, Ghostlands, Azuremyst Isle, or Bloodmyst Isle
  if ( $core == 1)
  {
    if ( $showmap == -1 )
    {
      $query = "SELECT *, gm
        FROM characters
          LEFT JOIN `".$logon_db["name"]."`.accounts ON characters.acct=accounts.acct
        WHERE ".( ( $online <> -1 ) ? "online='".$online."' AND " : "" )."mapid IN (0,1,571) AND zoneid<>876".$faction_append;
    }
    else
      $query = "SELECT *, gm
        FROM characters
          LEFT JOIN `".$logon_db["name"]."`.accounts ON characters.acct=accounts.acct
        WHERE ".( ( $online <> -1 ) ? "online='".$online."' AND " : "" )."mapid='".$showmap."'".( ( $showmap == 1 ) ? " AND zoneid<>876" : "" ).( ( $showmap == 530 )  ? "  AND positionY>0"  : "" ).$faction_append;

    // don't want this query at all if we're viewing Outland or Northrend
    if ( ( $showmap <> 530 ) && ( $showmap <> 571 ) )
      $out_query = "SELECT *, gm
        FROM characters
          LEFT JOIN `".$logon_db["name"]."`.accounts ON characters.acct=accounts.acct
        WHERE ".( ( $online <> -1 ) ? "online='".$online."' AND " : "" )."mapid='530' AND positionY<-5000".( ($showmap == 0) ? " AND positionX>0" : "" ).( ( $showmap == 1 ) ? " AND positionX<0" : "" ).$faction_append;
  }
  elseif ( $core == 2 )
  {
    if ( $showmap == -1 )
      $query = "SELECT *, position_x AS positionX, position_y AS positionY, gmlevel AS gm
      FROM characters
        LEFT JOIN `".$logon_db["name"]."`.account ON characters.account=account.id
      WHERE ".( ( $online <> -1 ) ? "online='".$online."' AND " : "" )."map IN (0,1,571) AND zone<>876".$faction_append;
    else
      $query = "SELECT *, position_x AS positionX, position_y AS positionY, gmlevel AS gm
      FROM characters
        LEFT JOIN `".$logon_db["name"]."`.account ON characters.account=account.id
      WHERE ".( ( $online <> -1 ) ? "online='".$online."' AND " : "" )."map='".$showmap."'".( ( $showmap == 1 ) ? " AND zone<>876" : "" ).( ( $showmap == 530 )  ? "  AND position_y>0"  : "" ).$faction_append;

    // don't want this query at all if we're viewing Outland or Northrend
    if ( ( $showmap <> 530 ) && ( $showmap <> 571 ) )
      $out_query = "SELECT *, position_x AS positionX, position_y AS positionY, gmlevel AS gm
      FROM characters
        LEFT JOIN `".$logon_db["name"]."`.account ON characters.account=account.id
      WHERE ".( ($online <> -1) ? "online='".$online."' AND " : "" )."map='530' AND position_y<-5000".( ( $showmap == 0 ) ? " AND position_x>0" : "" ).( ( $showmap == 1 ) ? " AND position_x<0" : "" ).$faction_append;
  }
  else
  {
    if ( $showmap == -1 )
      $query = "SELECT *, position_x AS positionX, position_y AS positionY, gmlevel AS gm
      FROM characters
        LEFT JOIN `".$logon_db["name"]."`.account_access ON characters.account=account_access.id
      WHERE ".( ( $online <> -1 ) ? "online='".$online."' AND " : "" )."map IN (0,1,571) AND zone<>876".$faction_append;
    else
      $query = "SELECT *, position_x AS positionX, position_y AS positionY, gmlevel AS gm
      FROM characters
        LEFT JOIN `".$logon_db["name"]."`.account_access ON characters.account=account_access.id
      WHERE ".( ( $online <> -1 ) ? "online='".$online."' AND " : "" )."map='".$showmap."'".( ( $showmap == 1 ) ? " AND zone<>876" : "" ).( ( $showmap == 530 )  ? "  AND position_y>0"  : "" ).$faction_append;

    // don't want this query at all if we're viewing Outland or Northrend
    if ( ( $showmap <> 530 ) && ( $showmap <> 571 ) )
      $out_query = "SELECT *, position_x AS positionX, position_y AS positionY, gmlevel AS gm
      FROM characters
        LEFT JOIN `".$logon_db["name"]."`.account_access ON characters.account=account_access.id
      WHERE ".( ( $online <> -1 ) ? "online='".$online."' AND " : "" )."map='530' AND position_y<-5000".( ( $showmap == 0 ) ? " AND position_x>0" : "" ).( ( $showmap == 1 ) ? " AND position_x<0" : "" ).$faction_append;
  }

  // normal map characters
  $result = $sql["char"]->query($query);

  // map 530 characters (if we even need it)
  if ( ( $showmap <> 530 ) && ( $showmap <> 571 ) )
    $out_result = $sql["char"]->query($out_query);

  while ( $row = $sql["char"]->fetch_assoc($result) )
  {
    $hide = 0;

    if ( !isset($row["gm"]) )
      $row["gm"] = 0;

    if ( $map_status_gm_include_all == 0 )
      if ( $row["gm"] <> 0 )
        $hide = 1;

    if ( !$hide )
    {
      // wow's X & Y axes are weird
      $x = -1 * ($row["positionY"]);
      $y = -1 * ($row["positionX"]);
      
      $map = $row["map"];

      // each major map has a different scale & origin
      if ( $showmap == -1 )
      {
        if ( $map == 0 )
        {
          $x_scaled = floor($x * 0.023);
          $y_scaled = floor($y * 0.021);
          // 0,0 = 771,311
          $x_relative = $x_scaled + 771;
          $y_relative = $y_scaled + 311;
        }
        elseif ( $map == 1 )
        {
          $x_scaled = floor($x * 0.020);
          $y_scaled = floor($y * 0.022);
          // 0,0 = 177,387
          $x_relative = $x_scaled + 177;
          $y_relative = $y_scaled + 387;
        }
        else // 571
        {
          $x_scaled = floor($x * 0.021);
          $y_scaled = floor($y * 0.022);
          // 0,0 = 530,232
          $x_relative = $x_scaled + 530;
          $y_relative = $y_scaled + 232;
        }
      }
      elseif ( $showmap == 0)
      {
        $x_scaled = floor($x * 0.025);
        $y_scaled = floor($y * 0.0245);
        // 0,0 = 447,274
        $x_relative = $x_scaled + 447;
        $y_relative = $y_scaled + 279;
      }
      elseif ( $showmap == 1)
      {
        $x_scaled = floor($x * 0.027);
        $y_scaled = floor($y * 0.027);
        // 0,0 = 464,347
        $x_relative = $x_scaled + 464;
        $y_relative = $y_scaled + 350;
      }
      elseif ( $showmap == 530)
      {
        $x_scaled = floor($x * 0.0575);
        $y_scaled = floor($y * 0.0585);
        // 0,0 = 744,334
        $x_relative = $x_scaled + 744;
        $y_relative = $y_scaled + 334;
      }
      elseif ( $showmap == 571)
      {
        $x_scaled = floor($x * 0.057);
        $y_scaled = floor($y * 0.057);
        // 0,0 = 520,597
        $x_relative = $x_scaled + 520;
        $y_relative = $y_scaled + 597;
      }
      
      // build the tooltip for this character
      $output .= '
            <div class="map_tooltip" id="tooltip'.$row["guid"].'" style="left: '.($x_relative+10).'px; top:'.($y_relative+10).'px;">
              <table>
                <tr>
                  <td class="name_level" colspan="2">
                    '.( ( $map_gm_add_suffix && $row["gm"] ) ? '<img src="img/star.png" /> ' : '' ).$row["name"].' ('.char_get_level_color($row["level"]).')
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
                  <td class="zone" colspan="2">
                    '.get_zone_name($row["zone"]).'
                  </td>
                </tr>
              </table>
            </div>';

      // draw a dot for the character
      $output .= '
            <a href="char.php?id='.$row["guid"].'" onmouseover="ShowTooltip(this,'.$row["guid"].');" onmouseout="HideTooltip('.$row["guid"].');"><!-- X'.$x.' Y'.$y.' Map'.$map.' -->
              <img src="img/map/'.( char_get_side_id($row["race"]) ? 'horde' : 'allia' ).'.gif" style="position: absolute; left: '.$x_relative.'px; top: '.($y_relative+10).'px;" />
            </a>';
    }
  }

  // process our Azeroth characters who's map is 530
  // first, make sure we need to do this section at all
  if ( ( $showmap <> 530 ) && ( $showmap <> 571 ) )
  {
    while ( $row = $sql["char"]->fetch_assoc($out_result) )
    {
      $hide = 0;

      if ( !isset($row["gm"]) )
        $row["gm"] = 0;

      if ( $map_status_gm_include_all == 0 )
        if ( $row["gm"] <> 0 )
          $hide = 1;

      if ( !$hide )
      {
        // wow's X & Y axes are weird
        $x = -1 * ($row["positionY"]);
        $y = -1 * ($row["positionX"]);

        $map = $row["map"]; // should always be 530

        if ( $showmap == -1 )
        {
          if ( $y < 0 )
          {
            // Eversong Woods & Ghostlands
            // Eastern Kingdoms
            $x_scaled = floor($x * 0.023);
            $y_scaled = floor($y * 0.021);
            // 0,0 = 771,311
            $x_relative = $x_scaled + 705;
            $y_relative = $y_scaled + 360;
          }
          else
          {
            // Azuremyst Isle & Bloodmyst Isle
            // Kalimdor
            $x_scaled = floor($x * 0.020);
            $y_scaled = floor($y * 0.022);
            // 0,0 = 177,387
            $x_relative = $x_scaled - 183;
            $y_relative = $y_scaled + 178;
          }
        }
        elseif ( $showmap == 0 )
        {
          // Eversong Woods & Ghostlands
          // Eastern Kingdoms
          $x_scaled = floor($x * 0.025);
          $y_scaled = floor($y * 0.0245);
          // 0,0 = 447,274
          $x_relative = $x_scaled + 380;
          $y_relative = $y_scaled + 335;
        }
        elseif ( $showmap == 1 )
        {
          $x_scaled = floor($x * 0.027);
          $y_scaled = floor($y * 0.027);
          // 0,0 = 464,347
          $x_relative = $x_scaled - 20;
          $y_relative = $y_scaled + 70;
        }
      }
      
      // build the tooltip for this character
      $output .= '
            <div class="map_tooltip" id="tooltip'.$row["guid"].'" style="left: '.($x_relative+10).'px; top:'.($y_relative+10).'px;">
              <table>
                <tr>
                  <td class="name_level" colspan="2">
                    '.( ( $map_gm_add_suffix && $row["gm"] ) ? '<img src="img/star.png" /> ' : '' ).$row["name"].' ('.char_get_level_color($row["level"]).')
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
                  <td class="zone" colspan="2">
                    '.get_zone_name($row["zone"]).'
                  </td>
                </tr>
              </table>
            </div>';

      // draw a dot for the character
      $output .= '
            <a href="char.php?id='.$row["guid"].'" onmouseover="ShowTooltip(this,'.$row["guid"].');" onmouseout="HideTooltip('.$row["guid"].');"><!-- X'.$x.' Y'.$y.' Map'.$map.' -->
              <img src="img/map/'.( char_get_side_id($row["race"]) ? 'horde' : 'allia' ).'.gif" style="position: absolute; left: '.$x_relative.'px; top: '.($y_relative+10).'px;" />
            </a>';
    }
  }

  $output .= '
          </div>';
}


//####################################################################################################
// MAIN
//####################################################################################################
$err = (isset($_GET["error"])) ? $_GET["error"] : NULL;

$output .= '
        <div class="bubble" id="map_bubble">
          <div class="top">';

$output .= '
            <h1>'.lang("map", "pmap").'</h1>';

unset($err);

$output .= '
          </div><!-- top -->';

show_map();


unset($action);
unset($action_permission);

require_once 'footer.php';

?>