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

//########################################################################################################################
// SHOW CHAR REPUTATION
//########################################################################################################################
function char_rep()
{
  global $output, $realm_id, $characters_db, $logon_db, $corem_db, $action_permission,
    $user_lvl, $user_name, $sql, $core;

  require_once 'libs/fact_lib.php';
  $reputation_rank = fact_get_reputation_rank_arr();
  $reputation_rank_length = fact_get_reputation_rank_length();

  if ( empty($_GET['id']) )
    error(lang('global', 'empty_fields'));

  // this is multi realm support, as of writing still under development
  //  this page is already implementing it
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
    $result = $sql['char']->query("SELECT acct, name, race, class, level, gender FROM characters WHERE guid='".$id."' LIMIT 1");
  else
    $result = $sql['char']->query("SELECT account AS acct, name, race, class, level, gender FROM characters WHERE guid='".$id."' LIMIT 1");

  if ( $sql['char']->num_rows($result) )
  {
    $char = $sql['char']->fetch_assoc($result);

    // we get user permissions first
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
      // this_is_junk: ArcEmu stores reputation in a single field
      //               [faction id][unk1][unk2][standing],
      //               I'm sure the two unk's are useful data, I just don't need it here.
      //               But, we're going to break the values into two arrays
      if ( $core == 1 )
      {
        $result = $sql['char']->query("SELECT reputation FROM characters WHERE guid='".$id."'");
        $result = $sql['char']->fetch_assoc($result);
        $result = $result['reputation'];
        $result = substr($result, 0, strlen($result) - 1);
        $result = explode(",", $result);
        $factions = array();
        $faction_ranks = array();
        $pick = 0;
        foreach ( $result as $t )
        {
          switch ( $pick )
          {
            case 0:
            {
              array_push($factions, $t);
              $pick = 1;
              break;
            }
            case 1:
            {
              // we skip this one
              $pick = 2;
              break;
            }
            case 2:
            {
              // we skip this one
              $pick = 3;
              break;
            }
            case 3:
            {
              array_push($faction_ranks, $t);
              $pick = 0;
              break;
            }
          }
        }
      }
      else
      {
        $result = $sql['char']->query("SELECT faction, standing FROM character_reputation WHERE guid='".$id."' AND (flags & 1 = 1)");
        $factions = array();
        $faction_ranks = array();
        
        while ( $fact = $sql['char']->fetch_assoc($result) )
        {
          array_push($factions, $fact['faction']);
          array_push($faction_ranks, $fact['standing']);
        }
      }

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
      if ( char_get_class_name($char['class']) == 'Hunter' )
        $output .= '
                  <li><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'pets').'</a></li>';
      $output .= '
                  <li id="selected"><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'reputation').'</a></li>
                  <li><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'skills').'</a></li>';
      if ( $owner_name == $user_name )
        $output .= '
                  <li><a href="char_mail.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'mail').'</a></li>';
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

      $temp_out = array
      (
        // this_is_junk: style left hardcoded because it's insane.
        1 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi1" onclick="expand(\'i1\', this, \'Alliance\')">[-] Alliance</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i1" class="lined" style="width: 535px; display: table;">', 0),
        2 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi2" onclick="expand(\'i2\', this, \'Horde\')">[-] Horde</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i2" class="lined" style="width: 535px; display: table;">', 0),
        3 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi3" onclick="expand(\'i3\', this, \'Alliance Forces\')">[-] Alliance Forces</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i3" class="lined" style="width: 535px; display: table;">', 0),
        4 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi4" onclick="expand(\'i4\', this, \'Horde Forces\')">[-] Horde Forces</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i4" class="lined" style="width: 535px; display: table;">', 0),
        5 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi5" onclick="expand(\'i5\', this, \'Steamwheedle Cartels\')">[-] Steamwheedle Cartel</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i5" class="lined" style="width: 535px; display: table;">', 0),
        6 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi6" onclick="expand(\'i6\', this, \'The Burning Crusade\')">[-] The Burning Crusade</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i6" class="lined" style="width: 535px; display: table;">', 0),
        7 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi7" onclick="expand(\'i7\', this, \'Shattrath City\')">[-] Shattrath City</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i7" class="lined" style="width: 535px; display: table;">', 0),
        8 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi8" onclick="expand(\'i8\', this, \'Alliance Vanguard\')">[-] Alliance Vanguard</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i8" class="lined" style="width: 535px; display: table;">', 0),
        9 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi9" onclick="expand(\'i9\', this, \'Horde Expedition \')">[-] Horde Expedition </div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i9" class="lined" style="width: 535px; display: table;">', 0),
       10 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi10" onclick="expand(\'i10\', this, \'Sholazar Basin\')">[-] Sholazar Basin</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i10" class="lined" style="width: 535px; display: table;">', 0),
       11 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi11" onclick="expand(\'i11\', this, \'Wrath of the Lich King\')">[-] Wrath of the Lich King</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i11" class="lined" style="width: 535px; display: table;">', 0),
       12 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi12" onclick="expand(\'i12\', this, \'Other\')">[-] Other</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i12" class="lined" style="width: 535px; display: table;">', 0),
        0 => array('
                <table class="lined" id="ch_rep_rep">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi13" onclick="expand(\'i13\', this, \'Unknown\')">[-] Unknown</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i13" class="lined" style="width: 535px; display: table;">', 0),
      );

      if ( count($factions) > 1 )
      {
        for ( $i = 0; $i < count($factions); $i++ )
        {
          $faction  = $factions[$i];
          $standing = $faction_ranks[$i];

          $rep_rank      = fact_get_reputation_rank($faction, $standing, $char['race']);
          $rep_rank_name = $reputation_rank[$rep_rank];
          $rep_cap       = $reputation_rank_length[$rep_rank];
          $rep           = fact_get_reputation_at_rank($faction, $standing, $char['race']);
          $faction_name  = fact_get_faction_name($faction);
          $ft            = fact_get_faction_tree($faction);

          // not show alliance rep for horde and vice versa:
          if ( ( ((1 << ($char['race'] - 1)) & 690) && ( ( $ft == 1 ) || ( $ft == 3 ) ) ) || ( ((1 << ($char['race'] - 1)) & 1101) && ( ( $ft == 2 ) || ( $ft == 4 ) ) ) )
            ;
          else
          {
            // this_is_junk: style left hardcoded because it's calculated.
            $temp_out[$ft][0] .= '
                        <tr>
                          <td width="30%" align="left">'.$faction_name.'</td>
                          <td width="55%" valign="top">
                            <div class="faction-bar">
                              <div class="rep'.$rep_rank.'">
                                <span class="rep-data">'.$rep.'/'.$rep_cap.'</span>
                                <div class="bar-color" style="width:'.(100*$rep/$rep_cap).'%"></div>
                              </div>
                            </div>
                          </td>
                          <td width="15%" align="left" class="rep'.$rep_rank.'">'.$rep_rank_name.'</td>
                        </tr>';
            $temp_out[$ft][1] = 1;
          }
        }
      }
      else
        $output .= '
                        <tr>
                          <td colspan="2"><br /><br />'.lang('global', 'err_no_records_found').'<br /><br /></td>
                        </tr>';

      foreach ( $temp_out as $out )
        if ( $out[1] )
          $output .= $out[0].'
                      </table>
                    </td>
                  </tr>
                </table>';
      $output .= '
                <br />
              </div>
              <br />
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
          <!-- end of char_achieve.php -->';
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

$output .= '
      <div class="bubble">';

char_rep();

unset($action_permission);

require_once 'footer.php';


?>
