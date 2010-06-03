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

//
// this_is_junk: TODO: Need to figure out what Status and Rewarded are for in MaNGOS
//


require_once 'header.php';
require_once 'libs/char_lib.php';
valid_login($action_permission['view']);

//########################################################################################################################
// SHOW CHARACTERS QUESTS
//########################################################################################################################
function char_quest()
{
  global $output, $realm_id, $world_db, $logon_db, $characters_db, $action_permission,
    $user_lvl, $user_name, $quest_datasite, $itemperpage, $sql;

  if (empty($_GET['id'])) error(lang('global', 'empty_fields'));

  if (empty($_GET['realm']))
    $realmid = $realm_id;
  else
  {
    $realmid = $sql['logon']->quote_smart($_GET['realm']);
    if (is_numeric($realmid))
      $sql['char']->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
    else
      $realmid = $realm_id;
  }

  $id = $sql['char']->quote_smart($_GET['id']);
  if (is_numeric($id)); else $id = 0;

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sql['char']->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sql['char']->quote_smart($_GET['order_by']) : 1;
  if (is_numeric($order_by)); else $order_by=1;

  $dir = (isset($_GET['dir'])) ? $sql['char']->quote_smart($_GET['dir']) : 0;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir=0;

  $order_dir = ($dir) ? 'ASC' : 'DESC';
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================

  $result = $sql['char']->query('SELECT acct, name, race, class, level, gender
    FROM characters WHERE guid = '.$id.' LIMIT 1');

  if ($sql['char']->num_rows($result))
  {
    $char = $sql['char']->fetch_assoc($result);

    $owner_acc_id = $sql['char']->result($result, 0, 'acct');
    $result = $sql['logon']->query('SELECT gm, login FROM accounts WHERE acct = '.$char['acct'].'');
    $owner_gmlvl = $sql['logon']->result($result, 0, 'gm');
    $owner_name = $sql['logon']->result($result, 0, 'login');

    if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name)||($user_lvl == gmlevel('4')))
    {
      $output .= '
          <center>
            <div id="tab">
              <ul>
                <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'char_sheet').'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'inventory').'</a></li>
                '.(($char['level'] < 10) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'talents').'</a></li>').'
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'achievements').'</a></li>
                <li id="selected"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'quests').'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'friends').'</a></li>
                <li><a href="char_view.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'view').'</a></li>
              </ul>
            </div>
            <div id="tab_content">
              <font class="bold">
                '.htmlentities($char['name']).' -
                <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif"
                  onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                <img src="img/c_icons/'.$char['class'].'.gif"
                  onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /> - '.lang('char', 'level_short').char_get_level_color($char['level']).'
              </font>
              <br /><br />
              <table class="lined" id="ch_que_quests">
                <tr>
                  <th width="10%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=0&amp;dir='.$dir.'"'.($order_by == 0 ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'quest_id').'</a></th>
                  <th width="7%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=1&amp;dir='.$dir.'"'.($order_by == 1 ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'quest_level').'</a></th>
                  <th width="78%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=2&amp;dir='.$dir.'"'.($order_by == 2 ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'quest_title').'</a></th>
                  <th width="5%"><img src="img/aff_qst.png" width="14" height="14" border="0" alt="" /></th>
                </tr>';
      // this_is_junk: I'm not sure what status and rewarded actually do in MaNGOS
      $result = $sql['char']->query("SELECT quest_id, completed FROM questlog WHERE player_guid = '".$id."'");

      $quests_1 = array();
      $quests_3 = array();

      if ($sql['char']->num_rows($result))
      {
        while ($quest = $sql['char']->fetch_assoc($result))
        {
          $deplang = get_lang_id();
          $query1 = $sql['char']->query('SELECT questlevel, title FROM `'.$world_db[$realmid]['name'].'`.`quests` WHERE `entry` = \''.$quest['quest_id'].'\'');
          $quest_info = $sql['char']->fetch_assoc($query1);
          if(1 == $quest['completed'])
            array_push($quests_1, array($quest['quest_id'], $quest_info['questlevel'], $quest_info['title'], $quest['rewarded']));
          else
            array_push($quests_3, array($quest['quest_id'], $quest_info['questlevel'], $quest_info['title']));
        }
        unset($quest);
        unset($quest_info);
        aasort($quests_1, $order_by, $dir);
        $orderby = $order_by;
        if (2 < $orderby)
          $orderby = 1;
        aasort($quests_3, $orderby, $dir);
        $all_record = count($quests_1);

        foreach ($quests_3 as $data)
        {
          $output .= '
                <tr>
                  <td>'.$data[0].'</td>
                  <td>('.$data[1].')</td>
                  <td align="left"><a href="'.$quest_datasite.$data[0].'" target="_blank">'.htmlentities($data[2]).'</a></td>
                  <td><img src="img/aff_qst.png" width="14" height="14" alt="" /></td>
                </tr>';
        }
        unset($quest_3);
        if(count($quests_1))
        {
          $output .= '
              </table>
              <table class="hidden" id="ch_que_quests">
                <tr align="right">
                  <td>';
          $output .= generate_pagination('char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by='.$order_by.'&amp;dir='.($dir ? 0 : 1), $all_record, $itemperpage, $start);
          $output .= '
                  </td>
                </tr>
              </table>
              <table class="lined" id="ch_que_quests">
                <tr>
                  <th width="10%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=0&amp;dir='.$dir.'"'.($order_by == 0 ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'quest_id').'</a></th>
                  <th width="7%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=1&amp;dir='.$dir.'"'.($order_by == 1 ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'quest_level').'</a></th>
                  <th width="68%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=2&amp;dir='.$dir.'"'.($order_by == 2 ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'quest_title').'</a></th>
                  <th width="10%"><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by=3&amp;dir='.$dir.'"'.($order_by == 3 ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'rewarded').'</a></th>
                  <th width="5%"><img src="img/aff_tick.png" width="14" height="14" border="0" alt="" /></th>
                </tr>';
          $i = 0;
          foreach ($quests_1 as $data)
          {
            if($i < ($start+$itemperpage) && $i >= $start)
            {
              $output .= '
                <tr>
                  <td>'.$data[0].'</td>
                  <td>('.$data[1].')</td>
                  <td align="left"><a href="'.$quest_datasite.$data[0].'" target="_blank">'.htmlentities($data[2]).'</a></td>
                  <td><img src="img/aff_'.($data[3] ? 'tick' : 'qst' ).'.png" width="14" height="14" alt="" /></td>
                  <td><img src="img/aff_tick.png" width="14" height="14" alt="" /></td>
                </tr>';
            }
            $i++;
          }
          unset($data);
          unset($quest_1);
          $output .= '
                <tr align="right">
                  <td colspan="5">';
          $output .= generate_pagination('char_quest.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'&amp;order_by='.$order_by.'&amp;dir='.($dir ? 0 : 1), $all_record, $itemperpage, $start);
          $output .= '
                  </td>
                </tr>';
        }
      }
      else
        $output .= '
                <tr>
                  <td colspan="4"><p>'.lang('char', 'no_act_quests').'</p></td>
                </tr>';
      //---------------Page Specific Data Ends here----------------------------
      //---------------Character Tabs Footer-----------------------------------
      $output .= '
              </table>
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
      if ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) )
      {
                  //makebutton($lang_char['edit_button'], 'char_edit.php?id='.$id.'&amp;realm='.$realmid.'', 130);
        $output .= '
                </td>
                <td>';
      }
      // only higher level GM with delete access, or character owner can delete character
      if ( ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) ) || ($owner_name === $user_name) )
      {
                  makebutton(lang('char', 'del_char'), 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 130);
        $output .= '
                </td>
                <td>';
      }
      // only GM with update permission can send mail, mail can send items, so update permission is needed
      if ($user_lvl >= $action_permission['update'])
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
          <!-- end of char_quest.php -->';
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

//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

//$lang_char = lang_char();

$output .= "
      <div class=\"bubble\">";

char_quest();

//unset($action);
unset($action_permission);
//unset($lang_char);

require_once 'footer.php';


?>
