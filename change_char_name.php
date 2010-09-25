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
  global $output, $action_permission, $characters_db, $corem_db, $realm_id, $user_id, $sql, $core;

  valid_login($action_permission['view']);

  $output .= '
          <center>
            <div id="xname_fieldset" class="fieldset_border">
              <span class="legend">'.lang('xname', 'selectchar').'</span>
              <span class="xname_info">'.lang('xname', 'info').'</span>
              <br />
              <br />
              <form method="GET" action="change_char_name.php" name="form">
                <input type="hidden" name="action" value="choosename" />
                <table class="lined" id="xname_char_table">
                  <tr>
                    <th class="xname_radio">&nbsp;</th>
                    <th class="xname_name">'.lang('xname', 'char').'</th>
                    <th class="xname_LRC">'.lang('xname', 'lvl').'</th>
                    <th class="xname_LRC">'.lang('xname', 'race').'</th>
                    <th class="xname_LRC">'.lang('xname', 'class').'</th>
                  </tr>';

  if ( $core == 1 )
    $chars = $sql['char']->query("SELECT * FROM characters WHERE acct='".$user_id."' AND guid NOT IN (SELECT guid FROM ".$corem_db['name'].".char_changes)");
  else
    $chars = $sql['char']->query("SELECT * FROM characters WHERE account='".$user_id."' AND guid NOT IN (SELECT guid FROM ".$corem_db['name'].".char_changes)");

  while ( $char = $sql['char']->fetch_assoc($chars) )
  {
    $output .= '
                  <tr>
                    <td>
                      <input type="radio" name="char" value="'.$char['guid'].'"/>
                    </td>
                    <td>'.$char['name'].'</td>
                    <td>'.char_get_level_color($char['level']).'</td>
                    <td>
                      <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char['race']).'\',\'item_tooltipx\')" onmouseout="oldtoolTip()" alt="" />
                    </td>
                    <td>
                      <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char['class']).'\',\'item_tooltipx\')" onmouseout="oldtoolTip()" alt="" />
                    </td>
                  </tr>';
  }

  $output .= '
                  <tr>
                    <td class="hidden" colspan="5">';
  makebutton(lang('xname', 'selectchar'), "javascript:do_submit()",180);
  $output .= '
                    </td>
                  </tr>
                </table>
                <br />
              </form>
            </div>
          </center>
          <br />';
}


//#############################################################################
// SELECT NEW NAME
//#############################################################################

function choosename()
{
  global $output, $action_permission, $characters_db, $realm_id, $user_id, $sql, $core;

  valid_login($action_permission['view']);

  $guid = $sql['char']->quote_smart($_GET['char']);
  $new1 = '';
  if ( isset($_GET['new1']) )
    $new1 = $sql['char']->quote_smart($_GET['new1']);
  $new2 = '';
  if ( isset($_GET['new2']) )
    $new2 = $sql['char']->quote_smart($_GET['new2']);

  $query = "SELECT * FROM characters WHERE guid='".$guid."'";
  $char = $sql['char']->fetch_assoc($sql['char']->query($query));
  $output .= '
          <center>
            <div id="xname_choose" class="fieldset_border">
              <span class="legend">'.lang('xname', 'choosename').'</span>
              <form method="GET" action="change_char_name.php" name="form">
                <input type="hidden" name="action" value="getapproval" />
                <input type="hidden" name="guid" value="'.$char['guid'].'" />
                <table id="xname_char_table">
                  <tr>
                    <td rowspan="4"><img src="'.char_get_avatar_img($char['level'], $char['gender'],  $char['race'],  $char['class']).'" alt="" /></td>
                    <td><span class="xname_char_name">'.$char['name'].'</span></td>
                  </tr>
                  <tr>
                    <td>'.lang('xname', 'level').': '.$char['level'].'</td>
                  </tr>
                  <tr>
                    <td>'.lang('xname', 'race').': '.char_get_race_name($char['race']).'</td>
                  </tr>
                  <tr>
                    <td>'.lang('xname', 'class').': '.char_get_class_name($char['class']).'</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="2"><b>'.lang('xname', 'entername').':</b></td>
                  </tr>
                  <tr>
                    <td>'.lang('xname', 'newname').':</td>
                    <td><input type="text" name="new1" value="'.$new1.'" maxlength="12" /></td>
                  </tr>
                  <tr>
                    <td>'.lang('xname', 'confirmname').':</td>
                    <td><input type="text" name="new2" value="'.$new1.'" maxlength="12" /></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>';
  makebutton(lang('xname', 'save'), "javascript:do_submit()",180);
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
// SUBMIT NAME CHANGE
//#############################################################################

function getapproval()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id, $user_id, $sql;

  valid_login($action_permission['view']);

  $guid = $sql['mgr']->quote_smart($_GET['guid']);
  $new1 = $sql['mgr']->quote_smart($_GET['new1']);
  $new2 = $sql['mgr']->quote_smart($_GET['new2']);

  if ( $new1 <> $new2 )
    redirect("change_char_name.php?action=choosename&char=".$guid."&new1=".$new1."&new2=".$new2."&error=2");

  $count = $sql['mgr']->num_rows($sql['mgr']->query("SELECT * FROM char_changes WHERE guid='".$guid."'"));
  if ( $count )
    redirect("change_char_name.php?error=3");

  $count = $sql['char']->num_rows($sql['char']->query("SELECT * FROM characters WHERE name='".$new1."'"));
  if ( $count )
    redirect("change_char_name.php?error=4");

  $result = $sql['mgr']->query("INSERT INTO char_changes (guid, new_name) VALUES ('".$guid."', '".$new1."')");

  redirect("change_char_name.php?error=5");
}


//#############################################################################
// DENY NAME CHANGE
//#############################################################################

function denied()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id, $user_id, $sql, $core;

  valid_login($action_permission['update']);

  $guid = $sql['mgr']->quote_smart($_GET['guid']);

  $result = $sql['mgr']->query("DELETE FROM char_changes WHERE `guid`='".$guid."'");

  $char = $sql['char']->fetch_assoc($sql['char']->query("SELECT * FROM characters WHERE guid='".$guid."'"));

  redirect("mail.php?action=send_mail&type=ingame_mail&to=".$char['name']."&subject=".lang('xname', 'subject')."&body=".lang('xname', 'body1').$char['name'].lang('xname', 'body2')."&group_sign==&group_send=gm_level&money=0&att_item=0&att_stack=0&redirect=index.php");
}


//#############################################################################
// SAVE NEW NAME
//#############################################################################

function savename()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id,
    $user_id, $sql;

  valid_login($action_permission['update']);

  $guid = $sql['mgr']->quote_smart($_GET['guid']);

  $name = $sql['mgr']->fetch_assoc($sql['mgr']->query("SELECT * FROM char_changes WHERE guid='".$guid."'"));

  $result = $sql['char']->query("UPDATE characters SET name='".$name['new_name']."' WHERE guid='".$guid."'");

  $result = $sql['mgr']->query("DELETE FROM char_changes WHERE guid='".$guid."'");

  redirect("index.php");
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
            <h1><font class="error">'.lang('xname', 'nomatch').'</font></h1>';
elseif ( $err == 3 )
  $output .= '
            <h1><font class="error">'.lang('xname', 'already').'</font></h1>';
elseif ( $err == 4 )
  $output .= '
            <h1><font class="error">'.lang('xname', 'inuse').'</font></h1>';
elseif ( $err == 5 )
  $output .= '
            <h1>'.lang('xname', 'done').'</h1>';
else
  $output .= '
            <h1>'.lang('xname', 'changename').'</h1>';

unset($err);

$output .= '
          </div>';

$action = ( ( isset($_GET['action']) ) ? $_GET['action'] : NULL );

if ( $action == 'choosename' )
  choosename();
elseif ( $action == 'getapproval' )
  getapproval();
elseif ( $action == 'denied' )
  denied();
elseif ( $action == 'approve' )
  savename();
else
  sel_char();

unset($action);
unset($action_permission);

require_once 'footer.php';


?>
