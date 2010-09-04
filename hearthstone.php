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


require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/map_zone_lib.php';
valid_login($action_permission['view']);


//#############################################################################
// SELECT CHARACTER
//#############################################################################

function sel_char()
{
  global $output, $action_permission, $characters_db, $realm_id, $user_id, $sql, $core;

  valid_login($action_permission['view']);

  $output .= '
          <center>
            <div id="xname_fieldset" class="fieldset_border">
              <span class="legend">'.lang('unstuck', 'selectchar').'</span>
              <span class="xname_info">'.lang('unstuck', 'info').'</span>
              <br />
              <br />
              <form method="GET" action="hearthstone.php" name="form">
                <input type="hidden" name="action" value="approve" />
                <table class="lined" id="xname_char_table">
                  <tr>
                    <th class="xname_radio">&nbsp;</th>
                    <th class="xname_name">'.lang('unstuck', 'char').'</th>
                    <th class="xname_LRC">'.lang('unstuck', 'lvl').'</th>
                    <th class="xname_LRC">'.lang('unstuck', 'race').'</th>
                    <th class="xname_LRC">'.lang('unstuck', 'class').'</th>
                  </tr>';

  if ( $core == 1 )
    $chars = $sql['char']->query("SELECT * FROM characters WHERE acct='".$user_id."'");
  else
    $chars = $sql['char']->query("SELECT * FROM characters WHERE account='".$user_id."'");

  while ( $char = $sql['char']->fetch_assoc($chars) )
  {
    $output .= '
                  <tr>
                    <td>
                      <input type="radio" name="char" value="'.$char['guid'].'" '.($char['online'] <> 0 ? 'disabled="disabled"' : '').' />
                    </td>
                    <td>'.$char['name'].'</td>
                    <td>'.char_get_level_color($char['level']).'</td>
                    <td>
                      <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\',\'item_tooltip\')" onmouseout="toolTip()" alt="" />
                    </td>
                    <td>
                      <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\',\'item_tooltip\')" onmouseout="toolTip()" alt="" />
                    </td>
                  </tr>';
  }

  $output .= '
                  <tr>
                    <td colspan="5" class="hidden">';
  makebutton(lang('unstuck', 'selectchar'), "javascript:do_submit()",180);
  $output .= '
                    </td>
                  </tr>
                </table>
              </form>
            </div>
          </center>
          <br />';
}


//#############################################################################
// APPROVE UNSTUCK
//#############################################################################

function approve()
{
  global $output, $action_permission, $characters_db, $realm_id, 
    $arcm_db, $user_id, $sql, $core;

  valid_login($action_permission['view']);

  $guid = $sql['char']->quote_smart($_GET['char']);
  $new1 = '';
  if (isset($_GET['new1']))
    $new1 = $sql['char']->quote_smart($_GET['new1']);
  $new2 = '';
  if (isset($_GET['new2']))
    $new2 = $sql['char']->quote_smart($_GET['new2']);

  if ( $core == 1 )
    $query = "SELECT * FROM characters WHERE guid='".$guid."'";
  else
    $query = "SELECT *, characters.guid AS guid,
      characters.map AS mapId, characters.zone AS zoneId,
      character_homebind.map AS bindmapId, character_homebind.zone AS bindzoneId
      FROM characters LEFT JOIN character_homebind ON characters.guid = character_homebind.guid WHERE characters.guid='".$guid."'";

  $char = $sql['char']->fetch_assoc($sql['char']->query($query));

  // MaNGOS & Trinity don't automatically add a home bind location for a character.
  if ( $core != 1 )
  {
    if ( !isset($char['bindmapId']) )
    {
      $query = "SELECT * FROM playercreateinfo WHERE race='".$char['race']."' AND class='".$char['class']."'";
      $result = $sql['world']->query($query);
      $fields = $sql['world']->fetch_assoc($result);
      $char['bindmapId'] = $fields['map'];
      $char['bindzoneId'] = $fields['zone'];
    }
  }

  $output .= '
    <center>
      <div id="xname_fieldset" class="fieldset_border">
        <span class="legend">'.lang('unstuck', 'newloc_legend').'</span>
        <form method="GET" action="hearthstone.php" name="form">
          <input type="hidden" name="action" value="save" />
          <input type="hidden" name="guid" value="'.$char['guid'].'" />
          <table id="xname_char_table">
            <tr>
              <td rowspan="4"><img src="'.char_get_avatar_img($char['level'], $char['gender'],  $char['race'],  $char['class']).'" alt="" /></td>
              <td><span class="xname_char_name">'.$char['name'].'</span></td>
            </tr>
            <tr>
              <td>'.lang('unstuck', 'level').': '.$char['level'].'</td>
            </tr>
            <tr>
              <td>'.lang('unstuck', 'race').': '.char_get_race_name($char['race']).'</td>
            </tr>
            <tr>
              <td>'.lang('unstuck', 'class').': '.char_get_class_name($char['class']).'</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td><b>'.lang('unstuck', 'curloc').':</b></td>
            </tr>
            <tr>
              <td>'.get_map_name($char['mapId']).'</td>
              <td>'.get_zone_name($char['zoneId']).'</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"><b>'.lang('unstuck', 'newloc').':</b></td>
            </tr>
            <tr>
              <td>'.get_map_name($char['bindmapId']).'</td>
              <td>'.get_zone_name($char['bindzoneId']).'</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>';
  makebutton(lang('unstuck', 'save'), "javascript:do_submit()",180);
  $output .= '
              </td>
            </tr>
          </table>
        </form>
      </div>
    </center>
    <br />';
}


//#############################################################################
// SAVE 'NEW' LOCATION
//#############################################################################

function saveloc()
{
  global $output, $action_permission, $characters_db, $realm_id, $user_id, $sql, $core;

  valid_login($action_permission['view']);

  $guid = $sql['char']->quote_smart($_GET['guid']);

  if ( $core == 1 )
    $query = "SELECT * FROM characters WHERE guid='".$guid."'";
  else
    $query = "SELECT *,
      characters.map AS mapId, characters.zone AS zoneId,
      character_homebind.map AS bindmapId, character_homebind.zone AS bindzoneId,
      character_homebind.position_x AS bindpositionX, character_homebind.position_y AS bindpositionY,
      character_homebind.position_z AS bindpositionZ
      FROM characters LEFT JOIN character_homebind ON characters.guid = character_homebind.guid WHERE characters.guid='".$guid."'";

  $char = $sql['char']->fetch_assoc($sql['char']->query($query));

  if ( $core != 1 )
  {
    if ( !isset($char['bindmapId']) )
    {
      $query = "SELECT * FROM playercreateinfo WHERE race='".$char['race']."' AND class='".$char['class']."'";
      $result = $sql['world']->query($query);
      $fields = $sql['world']->fetch_assoc($result);
      $char['bindmapId'] = $fields['map'];
      $char['bindzoneId'] = $fields['zone'];
      $char['bindpositionX'] = $fields['position_x'];
      $char['bindpositionY'] = $fields['position_y'];
      $char['bindpositionZ'] = $fields['position_z'];
    }
  }

  if ( $core == 1 )
    $query = "UPDATE characters SET positionX='".$char['bindpositionX']."', positionY='".$char['bindpositionY']."', positionZ='".$char['bindpositionZ']."', mapId='".$char['bindmapId']."', zoneId='".$char['bindzoneId']."' WHERE guid='".$guid."'";
  else
    $query = "UPDATE characters SET position_x='".$char['bindpositionX']."', position_y='".$char['bindpositionY']."', position_z='".$char['bindpositionZ']."', map='".$char['bindmapId']."', zone='".$char['bindzoneId']."' WHERE guid='".$guid."'";

  $result = $sql['char']->query($query);

  redirect("hearthstone.php?error=2");
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = ( ( isset($_GET['error']) ) ? $_GET['error'] : NULL );

$output .= '
        <div class="bubble">
          <div class="top">';

if ( $err == 1 )
  $output .= '
            <h1><font class="error">'.lang('global', 'empty_fields').'</font></h1>';
elseif ( $err == 2 )
  $output .= '
            <h1>'.lang('unstuck', 'done').'</h1>';
else
  $output .= '
            <h1>'.lang('unstuck', 'unstuck').'</h1>';

unset($err);

$output .= '
          </div>';

$action = ( ( isset($_GET['action']) ) ? $_GET['action'] : NULL );

if ( $action == 'approve' )
  approve();
elseif ( $action == 'save' )
  saveloc();
else
  sel_char();

unset($action);
unset($action_permission);

require_once 'footer.php';


?>
