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
valid_login($action_permission['view']);


//#############################################################################
// SELECT CHARACTER
//#############################################################################

function sel_char()
{
  global $output, $action_permission, $characters_db, $realm_id, $user_id, $sql;

  valid_login($action_permission['view']);

  $output .= '
    <center>
      <fieldset id="xname_fieldset">
        <legend>'.lang('xrace', 'selectchar').'</legend>
        <span class="xname_info">'.lang('xrace', 'info').'</span>
        <br />
        <br />
        <form method="GET" action="change_char_race.php" name="form">
          <input type="hidden" name="action" value="chooserace" />
          <table class="lined" id="xname_char_table">
            <tr>
              <th class="xname_radio">&nbsp;</th>
              <th class="xname_name">'.lang('xrace', 'char').'</th>
              <th class="xname_LRC">'.lang('xrace', 'lvl').'</th>
              <th class="xname_LRC">'.lang('xrace', 'race').'</th>
              <th class="xname_LRC">'.lang('xrace', 'class').'</th>';
  $chars = $sql['char']->query("SELECT * FROM characters WHERE acct='".$user_id."'");
  while ($char = $sql['char']->fetch_assoc($chars))
  {
    $output .= '
            <tr>
              <td>
                <input type="radio" name="char" value="'.$char['guid'].'"/>
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
  makebutton(lang('xrace', 'selectchar'), "javascript:do_submit()",180);
  $output .= '
        </form>
      </fieldset>
    </center>
    <br />';
}


//#############################################################################
// SELECT NEW RACE
//#############################################################################

$Class_Races       =  array
                      (
1                  => array( 1, 2, 3, 4, 5, 6, 7, 8,     11,),
2                  => array( 1,    3,                10, 11,),
3                  => array(    2, 3, 4,    6,    8, 10, 11,),
4                  => array( 1, 2, 3, 4, 5,    7, 8, 10,    ),
5                  => array( 1,    3, 4, 5,       8, 10, 11,),
6                  => array( 1, 2, 3, 4, 5, 6, 7, 8, 10, 11,),
7                  => array(    2,          6,    8,     11,),
8                  => array( 1,          5,    7, 8, 10, 11,),
9                  => array( 1, 2,       5,    7,    10,    ),
11                 => array(          4,    6,              ),
                      );

function chooserace()
{
  global $output, $action_permission, $characters_db, $realm_id, $user_id,
    $Class_Races, $sql;

  valid_login($action_permission['view']);

  $guid = $sql['char']->quote_smart($_GET['char']);
  $new1 = '';
  if (isset($_GET['new1']))
    $new1 = $sql['char']->quote_smart($_GET['new1']);
  $new2 = '';
  if (isset($_GET['new2']))
    $new2 = $sql['char']->quote_smart($_GET['new2']);

  $char = $sql['char']->fetch_assoc($sql['char']->query("SELECT * FROM characters WHERE guid='".$guid."'"));
  $output .= '
    <center>
      <fieldset id="xname_fieldset">
        <legend>'.lang('xrace', 'chooserace').'</legend>
        <form method="GET" action="change_char_race.php" name="form">
          <input type="hidden" name="action" value="getapproval" />
          <input type="hidden" name="guid" value="'.$char['guid'].'" />
          <table id="xname_char_table">
            <tr>
              <td rowspan="4"><img src="'.char_get_avatar_img($char['level'], $char['gender'],  $char['race'],  $char['class']).'" alt="" /></td>
              <td><span class="xname_char_name">'.$char['name'].'</span></td>
            </tr>
            <tr>
              <td>'.lang('xrace', 'level').': '.$char['level'].'</td>
            </tr>
            <tr>
              <td>'.lang('xrace', 'race').': '.char_get_race_name($char['race']).'</td>
            </tr>
            <tr>
              <td>'.lang('xrace', 'class').': '.char_get_class_name($char['class']).'</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"><b>'.lang('xrace', 'enterrace').':</b></td>
            </tr>
            <tr>
              <td>'.lang('xrace', 'newrace').':</td>
              <td>
                <select name="newrace">';
  $races = $Class_Races[$char['class']];
  for ($i = 0; $i < count($races); $i++)
  {
    if ( !($races[$i] == $char['race']) )
    {
      if ( char_get_side_id($races[$i]) == char_get_side_id($char['race']) )
      {
        $output .= '
                  <option value="'.$races[$i].'">'.char_get_race_name($races[$i]).'</option>';
      }
    }
  }
  $output .= '
                </select>
              </td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>';
  makebutton(lang('xrace', 'save'), "javascript:do_submit()",180);
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
// SUBMIT RACE CHANGE
//#############################################################################

function getapproval()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id,
    $user_id, $Class_Races, $sql;

  valid_login($action_permission['view']);

  $guid = $sql['mgr']->quote_smart($_GET['guid']);
  $newrace = $sql['mgr']->quote_smart($_GET['newrace']);

  $count = $sql['mgr']->num_rows($sql['mgr']->query("SELECT * FROM char_changes WHERE `guid`='".$guid."'"));
  if ($count)
    redirect("change_char_race.php?error=3");

  $char = $sql['char']->fetch_assoc($sql['char']->query("SELECT * FROM characters WHERE `guid`='".$guid."'"));
  if ( !in_array($newrace, $Class_Races[$char['class']]) )
    redirect("change_char_race.php?error=2");

  $result = $sql['mgr']->query("INSERT INTO char_changes (guid,new_race) VALUES ('".$guid."', '".$newrace."')");

  redirect("change_char_race.php?error=5");
}


//#############################################################################
// DENY RACE CHANGE
//#############################################################################

function denied()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id, $user_id, $sql;

  valid_login($action_permission['update']);

  $guid = $sql['mgr']->quote_smart($_GET['guid']);

  $result = $sql['mgr']->query("DELETE FROM char_changes WHERE `guid`='".$guid."'");

  $char = $sql['char']->fetch_assoc($sql['char']->query("SELECT * FROM characters WHERE guid='".$guid."'"));

  // send denial letter
  redirect("mail.php?action=send_mail&type=ingame_mail&to=".$char['name']."&subject=".lang('xrace', 'subject')."&body=".lang('xrace', 'body1').$char['name'].lang('xrace', 'body2')."&group_sign==&group_send=gm_level&money=0&att_item=0&att_stack=0&redirect=index.php");
}


//#############################################################################
// SAVE NEW RACE
//#############################################################################

function saverace()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id, $sql, $user_id;

  valid_login($action_permission['update']);

  $guid = $sql['mgr']->quote_smart($_GET['guid']);

  $name = $sql['mgr']->fetch_assoc($sql['mgr']->query("SELECT * FROM char_changes WHERE `guid`='".$guid."'"));

  $result = $sql['char']->query("UPDATE characters SET `race`='".$name['new_race']."' WHERE `guid`='".$guid."'");

  $result = $sql['mgr']->query("DELETE FROM char_changes WHERE `guid`='".$guid."'");

  // this_is_junk: The retail version of this swaps the character's old home faction reputation with
  // their reputation with the new faction.  So, an Orc wanting to become a Blood Elf would have
  // her reputation with Orgrimmar swapped with their rep for Silvermoon.  Because of how ArcEmu stores
  // reputation, I don't want to have to mess with this atm.  It's not life-or-death because you can only
  // change within Horde or Alliance not between, so, you can just build up your 'new' home rep.
  // They also swap the mounts too, but that's silly. ^_^

  redirect("index.php");
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
            <h1><font class="error">'.lang('xrace', 'nomatch').'</font></h1>';
elseif ($err == 3)
  $output .= '
            <h1><font class="error">'.lang('xrace', 'already').'</font></h1>';
elseif ($err == 5)
  $output .= '
            <h1>'.lang('xrace', 'done').'</h1>';
else
  $output .= '
            <h1>'.lang('xrace', 'changerace').'</h1>';

unset($err);

$output .= '
          </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ($action == 'chooserace')
  chooserace();
elseif ($action == 'getapproval')
  getapproval();
elseif ($action == 'denied')
  denied();
elseif ($action == 'approve')
  saverace();
else
  sel_char();

unset($action);
unset($action_permission);

require_once 'footer.php';


?>
