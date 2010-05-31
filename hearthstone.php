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
  global $output, $action_permission, $characters_db, $realm_id, $user_id, $sqlc;

  valid_login($action_permission['view']);

  $output .= '
    <center>
      <fieldset id="xname_fieldset">
        <legend>'.lang('unstuck', 'selectchar').'</legend>
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
              <th class="xname_LRC">'.lang('unstuck', 'class').'</th>';
  $chars = $sqlc->query("SELECT * FROM characters WHERE acct='".$user_id."'");
  while ($char = $sqlc->fetch_assoc($chars))
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
          </table>
          <br />';
  makebutton(lang('unstuck', 'selectchar'), "javascript:do_submit()",180);
  $output .= '
        </form>
      </fieldset>
    </center>
    <br />';
}


//#############################################################################
// APPROVE UNSTUCK
//#############################################################################

function approve()
{
  global $output, $action_permission, $characters_db, $realm_id, 
    $arcm_db, $user_id, $sqlc, $sqlm, $sqld;

  valid_login($action_permission['view']);

  $guid = $sqlc->quote_smart($_GET['char']);
  $new1 = '';
  if (isset($_GET['new1']))
    $new1 = $sqlc->quote_smart($_GET['new1']);
  $new2 = '';
  if (isset($_GET['new2']))
    $new2 = $sqlc->quote_smart($_GET['new2']);

  $char = $sqlc->fetch_assoc($sqlc->query("SELECT * FROM characters WHERE guid='".$guid."'"));
  $output .= '
    <center>
      <fieldset id="xname_fieldset">
        <legend>'.lang('unstuck', 'newloc_legend').'</legend>
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
              <td>'.get_map_name($char['mapId'], $sqld).'</td>
              <td>'.get_zone_name($char['zoneId'], $sqld).'</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"><b>'.lang('unstuck', 'newloc').':</b></td>
            </tr>
            <tr>
              <td>'.get_map_name($char['bindmapId'], $sqld).'</td>
              <td>'.get_zone_name($char['bindzoneId'], $sqld).'</td>
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
      </fieldset>
    </center>
    <br />';
}


//#############################################################################
// SAVE 'NEW' LOCATION
//#############################################################################

function saveloc()
{
  global $output, $action_permission, $characters_db, $realm_id, $user_id, $sqlc;

  valid_login($action_permission['view']);

  $guid = $sqlc->quote_smart($_GET['guid']);

  $char = $sqlc->fetch_assoc($sqlc->query("SELECT * FROM characters WHERE `guid`='".$guid."'"));

  $result = $sqlc->query("UPDATE characters SET `positionX`='".$char['bindpositionX']."', `positionY`='".$char['bindpositionY']."', `positionZ`='".$char['bindpositionZ']."', `mapId`='".$char['bindmapId']."', `zoneId`='".$char['bindzoneId']."' WHERE `guid`='".$guid."'");

  redirect("hearthstone.php?error=2");
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
      <div class="bubble">
          <div class="top">';

if ($err == 1)
  $output .= '
            <h1><font class="error">'.lang('global', 'empty_fields').'</font></h1>';
elseif ($err == 2)
  $output .= '
            <h1>'.lang('unstuck', 'done').'</h1>';
else
  $output .= '
            <h1>'.lang('unstuck', 'unstuck').'</h1>';

unset($err);

$output .= '
          </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ($action == 'approve')
  approve();
elseif ($action == 'save')
  saveloc();
else
  sel_char();

unset($action);
unset($action_permission);

require_once 'footer.php';


?>
