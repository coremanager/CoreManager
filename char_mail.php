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

//########################################################################################################################^M
// SHOW CHARACTER MAIL
//########################################################################################################################^M
function char_mail()
{
  global $output, $realm_id, $characters_db, $action_permission, $user_lvl, $user_name, $user_id, $sql, $core;

  if ( empty($_GET['id']) )
    error(lang('global', 'empty_fields'));

  if ( empty($_GET['realm']) )
    $realmid = $realm_id;
  else
  {
    $realmid = $sql['logon']->quote_smart($_GET['realm']);
    if ( is_numeric($realmid) )
      $sql['char']->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
    else
      $realmid = $realm_id;
  }

  $id = $sql['char']->quote_smart($_GET['id']);
  if ( is_numeric($id) )
    ;
  else
    $id = 0;

  if ( $core == 1 )
    $result = $sql['char']->query("SELECT acct, name, race, class, level, gender
      FROM characters WHERE guid='".$id."' LIMIT 1");
  else
    $result = $sql['char']->query("SELECT account AS acct, name, race, class, level, gender
      FROM characters WHERE guid='".$id."' LIMIT 1");

  if ( $sql['char']->num_rows($result) )
  {
    $char = $sql['char']->fetch_assoc($result);
    
    if ( $user_id <> $char['acct'] )
      error(lang('char', 'no_permission'));

    $owner_acc_id = $sql['char']->result($result, 0, 'acct');
    if ( $core == 1 )
      $result = $sql['logon']->query("SELECT login FROM accounts WHERE acct='".$char['acct']."'");
    else
      $result = $sql['logon']->query("SELECT username AS login FROM account WHERE id='".$char['acct']."'");
    $owner_name = $sql['logon']->result($result, 0, 'login');
    $result = $sql['mgr']->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_name."'");
    $owner_gmlvl = $sql['mgr']->result($result, 0, 'gm');

    if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == gmlevel('4') ) )
    {
      $output .= '
          <center>
            <div id="tab">
              <ul>
                <li id="selected"><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'char_sheet').'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'inventory').'</a></li>
                '.( ( $char['level'] < 10 ) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'talents').'</a></li>' ).'
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'achievements').'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'quests').'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'friends').'</a></li>
                <li><a href="char_view.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'view').'</a></li>
               </ul>
            </div>
            <div id="tab_content">
              <div id="tab">
                <ul>
                  <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'char_sheet').'</a></li>';
        if ( char_get_class_name($char['class']) === 'Hunter' )
          $output .= '
                  <li><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'pets').'</a></li>';
        $output .= '
                  <li><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'reputation').'</a></li>
                  <li><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'skills').'</a></li>';
        if ( $owner_name == $user_name )
          $output .= '
                  <li id="selected"><a href="char_mail.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'mail').'</a></li>';
        $output .= '
                </ul>
              </div>
              <div id="tab_content2">
                <font class="bold">
                  '.htmlentities($char['name']).' -
                  <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltipx\')" onmouseout="oldtoolTip()" alt="" />
                  <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltipx\')" onmouseout="oldtoolTip()" alt="" /> - '.lang('char', 'level_short').char_get_level_color($char['level']).'
                </font>
                <br /><br />';

      if ( $core == 1 )
        $result = $sql['char']->query("SELECT * FROM mailbox WHERE player_guid='".$id."' AND deleted_flag=0");
      else
        $result = $sql['char']->query("SELECT *, sender AS sender_guid, checked AS read_flag, id AS message_id FROM mail WHERE receiver='".$id."'");

      if ( $sql['char']->num_rows($result) )
      {
        $output .= '
                <table class="lined" id="ch_mail_table">
                  <tr>
                    <th width="10%">'.lang('char', 'status').'</th>
                    <th>'.lang('char', 'sender').'</th>
                    <th width="55%">'.lang('char', 'subject').'</th>
                  </tr>';
        while ( $mail = $sql['char']->fetch_assoc($result) )
        {
          $c_query = "SELECT name FROM characters WHERE guid = '".$mail['sender_guid']."'";
          $c_result = $sql['char']->query($c_query);
          $c_name = $sql['char']->fetch_assoc($c_result);
          
          $output .= '
                  <tr>
                    <td>';
          if ( $core == 1 )
          {
            if ( isset($mail['read_flag']) )
                $output .= '
                        <img src="img/flag_white.gif" />';
            else
                $output .= '
                        <img src="img/flag_green.gif" />';
          }
          else
          {
            if ( isset($mail['read_flag']) && ( $mail['read_flag'] <> 16 ) )
                $output .= '
                        <img src="img/flag_white.gif" />';
            else
                $output .= '
                        <img src="img/flag_green.gif" />';
          }
          $output .= '
                    </td>
                    <td><a href="char.php?id='.$mail['sender_guid'].'">'.$c_name['name'].'</a></td>
                    <td><a href="char_mail.php?id='.$id.'&amp;realm='.$realm_id.'&amp;action=readmail&amp;message='.$mail['message_id'].'">'.$mail['subject'].'</a></td>
                  </tr>';
        }
        $output .= '
                </table>';
      }
      $output .= '
              </div>
            </div>
            <br />
            <table class="hidden">
              <tr>
                <td>';
      // button to user account page, user account page has own security
      makebutton(lang('char', 'chars_acc'), 'user.php?action=edit_user&amp;id='.$owner_acc_id.'', 130);
      $output .= '
                </td>
                <td>';

      // only higher level GM with delete access can edit character
      //  character edit allows removal of character items, so delete permission is needed
      if ( ( $user_lvl > $owner_gmlvl ) && ( $user_lvl >= $action_permission['delete'] ) )
      {
                  //makebutton($lang_char['edit_button'], 'char_edit.php?id='.$id.'&amp;realm='.$realmid.'', 130);
        $output .= '
                </td>
                <td>';
      }
      // only higher level GM with delete access, or character owner can delete character
      if ( ( ( $user_lvl > $owner_gmlvl ) && ( $user_lvl >= $action_permission['delete'] ) ) || ( $owner_name === $user_name ) )
      {
        makebutton(lang('char', 'del_char'), 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 130);
        $output .= '
                </td>
                <td>';
      }
      // only GM with update permission can send mail, mail can send items, so update permission is needed
      if ( $user_lvl >= $action_permission['update'] )
      {
        makebutton(lang('char', 'send_mail'), 'mail.php?type=ingame_mail&amp;to='.$char['name'].'', 130);
        $output .= '
                </td>
                <td>';
      }
      makebutton(lang('global', 'back'), 'javascript:window.history.back()" type="def', 130);
      $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>
          <!-- end of char_mail.php -->';
    }
    else
      error(lang('char', 'no_permission'));
  }
  else
    error(lang('char', 'no_char_found'));

}

//########################################################################################################################^M
// READ MAIL MESSAGE
//########################################################################################################################^M
function read_mail()
{
  global $output, $realm_id, $characters_db, $action_permission, $user_lvl, $user_name, $user_id, $sql, $core;

  if ( empty($_GET['id']) )
    error(lang('global', 'empty_fields'));
    
  if ( empty($_GET['message']) )
    error(lang('global', 'empty_fields'));

  if ( empty($_GET['realm']) )
    $realmid = $realm_id;
  else
  {
    $realmid = $sql['logon']->quote_smart($_GET['realm']);
    if ( is_numeric($realmid) )
      $sql['char']->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
    else
      $realmid = $realm_id;
  }

  $id = $sql['char']->quote_smart($_GET['id']);
  if ( is_numeric($id) )
    ;
  else
    $id = 0;

  $message = $sql['char']->quote_smart($_GET['message']);
  if ( is_numeric($message) )
    ;
  else
    $message = 0;

  if ( $core == 1 )
    $result = $sql['char']->query("SELECT acct, name, race, class, level, gender
      FROM characters WHERE guid='".$id."' LIMIT 1");
  else
    $result = $sql['char']->query("SELECT account AS acct, name, race, class, level, gender
      FROM characters WHERE guid='".$id."' LIMIT 1");

  if ( $sql['char']->num_rows($result) )
  {
    $char = $sql['char']->fetch_assoc($result);
    
    if ( $user_id <> $char['acct'] )
      error(lang('char', 'no_permission'));

    $owner_acc_id = $sql['char']->result($result, 0, 'acct');
    if ( $core == 1 )
      $result = $sql['logon']->query("SELECT login FROM accounts WHERE acct='".$char['acct']."'");
    else
      $result = $sql['logon']->query("SELECT username AS login FROM account WHERE id='".$char['acct']."'");
    $owner_name = $sql['logon']->result($result, 0, 'login');
    $result = $sql['mgr']->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_name."'");
    $owner_gmlvl = $sql['mgr']->result($result, 0, 'gm');

    if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == gmlevel('4') ) )
    {
      $output .= '
          <center>
            <div id="tab">
              <ul>
                <li id="selected"><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'char_sheet').'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'inventory').'</a></li>
                '.( ( $char['level'] < 10 ) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'talents').'</a></li>' ).'
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'achievements').'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'quests').'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'friends').'</a></li>
               </ul>
            </div>
            <div id="tab_content">
              <div id="tab">
                <ul>
                  <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'char_sheet').'</a></li>';
        if ( char_get_class_name($char['class']) === 'Hunter' )
          $output .= '
                  <li><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'pets').'</a></li>';
        $output .= '
                  <li><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'reputation').'</a></li>
                  <li><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'skills').'</a></li>';
        if ( $owner_name == $user_name )
          $output .= '
                  <li id="selected"><a href="char_mail.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'mail').'</a></li>';
        $output .= '
                </ul>
              </div>
              <div id="tab_content2">
                <font class="bold">
                  '.htmlentities($char['name']).' -
                  <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltipx\')" onmouseout="oldtoolTip()" alt="" />
                  <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltipx\')" onmouseout="oldtoolTip()" alt="" /> - lvl '.char_get_level_color($char['level']).'
                </font>
                <br />';

      if ( $core == 1 )
        $result = $sql['char']->query("SELECT * FROM mailbox WHERE message_id='".$message."'");
      else
        $result = $sql['char']->query("SELECT *, sender AS sender_guid FROM mail WHERE id='".$message."'");
      $mail = $sql['char']->fetch_assoc($result);

      if ( $sql['char']->num_rows($result) )
      {
        $c_query = "SELECT name FROM characters WHERE guid='".$mail['sender_guid']."'";
        $c_result = $sql['char']->query($c_query);
        $c_name = $sql['char']->fetch_assoc($c_result);
          
        $output .= '
                <div id="ch_read_mail_field" class="fieldset_border">
                  <table class="hidden" id="ch_read_mail">
                    <tr>
                      <td align="left"><b>'.lang('char', 'sender').':</b></td>
                      <td align="left"><a href="char.php?id='.$mail['sender_guid'].'">'.$c_name['name'].'</a></td>
                      <td width="40%"></td>
                    </tr>
                    <tr>
                      <td align="left"><b>'.lang('char', 'subject').':</b></td>
                      <td align="left">'.$mail['subject'].'</td>
                      <td width="40%"></td>
                    </tr>
                    <tr>
                      <td align="left"><b>'.lang('char', 'body').':</b></td>
                      <td></td>
                      <td width="40%"></td>
                    </tr>
                    <tr>
                      <td colspan="3" align="left">'.$mail['body'].'</td>
                    </tr>
                    <tr>
                      <td></td>
                    </tr>
                    <tr>';
        if ( $mail['money'] <> 0 )
        {
          $attgold = str_pad($mail['money'], 4, "0", STR_PAD_LEFT);
          $pg = substr($attgold,  0, -4);
          if ( $pg == '' )
            $pg = 0;
          $pg = $pg * 1;
          $ps = substr($attgold, -4,  2);
          if ( ( $ps == '' ) || ( $ps == '00' ) )
            $ps = 0;
          $ps = $ps * 1;
          $pc = substr($attgold, -2);
          if ( ( $pc == '' ) || ( $pc == '00' ) )
            $pc = 0;
          $pc = $pc * 1;
          $output .= '
                      <td colspan="3">'.lang('char', 'messagehas').' '.
                        ( ( $pg ) ? $pg.'<img src="img/gold.gif" alt="" align="middle" />' : '' ).
                        ( ( $ps ) ? $ps.'<img src="img/silver.gif" alt="" align="middle" />' : '' ).
                        ( ( $pc ) ? $pc.'<img src="img/copper.gif" alt="" align="middle" />' : '' ).
                      ' '.lang('char', 'attached').'.</td>';
        }
        if ( $mail['cod'] <> 0 )
        {
          $codgold = str_pad($mail['cod'], 4, "0", STR_PAD_LEFT);
          $pg = substr($codgold,  0, -4);
          if ( $pg == '' )
            $pg = 0;
          $pg = $pg * 1;
          $ps = substr($codgold, -4,  2);
          if ( ( $ps == '' ) || ( $ps == '00' ) )
            $ps = 0;
          $ps = $ps * 1;
          $pc = substr($codgold, -2);
          if ( ( $pc == '' ) || ( $pc == '00' ) )
            $pc = 0;
          $pc = $pc * 1;
          $output .= '
                      <td colspan="3">'.lang('char', 'cod').'; '.
                        ( ( $pg ) ? $pg.'<img src="img/gold.gif" alt="" align="middle" />' : '' ).
                        ( ( $ps ) ? $ps.'<img src="img/silver.gif" alt="" align="middle" />' : '' ).
                        ( ( $pc ) ? $pc.'<img src="img/copper.gif" alt="" align="middle" />' : '' ).
                      ' '.lang('char', 'isdue').'.</td>';
        };
        if ( $mail['attached_item_guids'] <> 0 )
        {
          $items = $mail['attached_item_guids'];
          $items = explode(',', $items);
          $i_count = count($items);
          $output .= '
                      <td colspan="3">'.lang('char', 'messagehas').' '.$i_count.' '.lang('char', 'itemsattached').'.</td>';
        }
        $output .= '
                    </tr>
                  </table>
                </div>
                <br />';
      }
      $output .= '
              </div>
            </div>
            <br />
            <br />
          </center>
          <!-- end of char_mail.php -->';
    }
    else
      error(lang('char', 'no_permission'));
  }
  else
    error(lang('char', 'no_char_found'));

}

//########################################################################################################################
// MAIN
//########################################################################################################################

$action = ( ( isset($_GET['action']) ) ? $_GET['action'] : NULL );

$output .= '
      <div class="bubble">';

switch ( $action )
{
  case 'readmail':
  {
    read_mail();
    break;
  }
  default:
    char_mail();
}

unset($action_permission);

require_once 'footer.php';


?>
