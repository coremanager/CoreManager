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
require_once 'libs/skill_lib.php';
valid_login($action_permission["view"]);

//########################################################################################################################
// SHOW CHARACTERS SKILLS
//########################################################################################################################
function char_skill()
{
  global $output, $realm_id, $characters_db, $corem_db, $action_permission, $user_lvl,
    $site_encoding, $user_name, $base_datasite, $skill_datasite, $sql, $core;

  //wowhead_tt();

  if ( empty($_GET["id"]) )
    error(lang("global", "empty_fields"));

  // this is multi realm support, as of writing still under development
  //  this page is already implementing it
  if ( empty($_GET["realm"]) )
    $realmid = $realm_id;
  else
  {
    $realmid = $sql["logon"]->quote_smart($_GET["realm"]);
    if ( is_numeric($realmid) )
      $sql["char"]->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name'], $characters_db[$realmid]["encoding"]);
    else
      $realmid = $realm_id;
  }

  $id = $sql["char"]->quote_smart($_GET["id"]);
  if ( !is_numeric($id) )
    $id = 0;

  $order_by = ( ( isset($_GET["order_by"]) ) ? $sql["char"]->quote_smart($_GET["order_by"]) : 1 );

  $dir = ( ( isset($_GET["dir"]) ) ? $sql["char"]->quote_smart($_GET["dir"]) : 1 );
  if ( !preg_match('/^[01]{1}$/', $dir) )
    $dir = 1;

  $order_dir = ( ( $dir ) ? 'ASC' : 'DESC' );
  $dir = ( ( $dir ) ? 0 : 1 );

  if ( $core == 1 )
    $result = $sql["char"]->query('SELECT acct, name, race, class, level, gender FROM characters WHERE guid = '.$id.' LIMIT 1');
  else
    $result = $sql["char"]->query('SELECT account AS acct, name, race, class, level, gender FROM characters WHERE guid = '.$id.' LIMIT 1');

  if ( $sql["char"]->num_rows($result) )
  {
    $char = $sql["char"]->fetch_assoc($result);

    // we get user permissions first
    $owner_acc_id = $sql["char"]->result($result, 0, 'acct');
    if ( $core == 1 )
      $result = $sql["logon"]->query("SELECT login FROM accounts WHERE acct='".$char["acct"]."'");
    else
      $result = $sql["logon"]->query("SELECT username AS login FROM account WHERE id='".$char["acct"]."'");
    $owner_name = $sql["logon"]->result($result, 0, 'login');
    $result = $sql["mgr"]->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_name."'");
    $owner_gmlvl = $sql["mgr"]->result($result, 0, 'gm');

    if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
    {
      if ( $core == 1 )
      {
        $result = $sql["char"]->query("SELECT data, name, race, class, level, gender FROM characters WHERE guid = '".$id."'");
        $char = $sql["char"]->fetch_assoc($result);
        $char_data = explode(';',$char["data"]);
      }
      else
      {
        $result = $sql["char"]->query("SELECT name, race, class, level, gender FROM characters WHERE guid='".$id."'");
        $char = $sql["char"]->fetch_assoc($result);
        $result = $sql["char"]->query("SELECT * FROM character_skills WHERE guid='".$id."'");

        // make TC's skill data work like our treatment of Arc's
        $char_data = array();
        $i = 0;
        while ( $skill_row = $sql["char"]->fetch_assoc($result) )
        {
          $char_data[PLAYER_SKILL_INFO_1_1 + $i] = $skill_row["skill"];
          $char_data[PLAYER_SKILL_INFO_1_1 + $i+1] = $skill_row["value"];
          $char_data[PLAYER_SKILL_INFO_1_1 + $i+2] = $skill_row["max"];
          $i+=3;
        }
      }

      $output .= '
          <center>
            <div id="tab">
              <ul>
                <li id="selected"><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "char_sheet").'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "inventory").'</a></li>
                '.( ( $char["level"] < 10 ) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "talents").'</a></li>' ).'
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "achievements").'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "quests").'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "friends").'</a></li>
                <li><a href="char_view.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "view").'</a></li>
               </ul>
            </div>
            <div id="tab_content">
              <div id="tab">
                <ul>
                  <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "char_sheet").'</a></li>';
      if ( char_get_class_name($char["class"]) == 'Hunter' )
        $output .= '
                  <li><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "pets").'</a></li>';
      $output .= '
                  <li><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "reputation").'</a></li>
                  <li id="selected"><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "skills").'</a></li>
                  <li><a href="char_pvp.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "pvp").'</a></li>';
      if ( ( $owner_name == $user_name ) || ( $user_lvl >= get_page_permission("insert", "char_mail.php") ) )
        $output .= '
                  <li><a href="char_mail.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "mail").'</a></li>';
      $output .= '
                </ul>
              </div>
              <div id="tab_content2">
                <font class="bold">
                  '.htmlentities($char["name"], ENT_COMPAT, $site_encoding).' -
                  <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                  <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" /> - '.lang("char", "level_short").char_get_level_color($char["level"]).'
                </font>
                <br />
                <br />
                <table class="lined" id="ch_ski_main">
                  <tr>
                    <th class="title" colspan="'.( ( $user_lvl ) ? '3' : '2' ).'" align="left">'.lang("char", "skills").'</th>
                  </tr>
                  <tr>
                    '.( ( $user_lvl ) ? '<th><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=0&amp;dir='.$dir.'"'.( ( $order_by == 0 ) ? ' class="'.$order_dir.'"' : '' ).'>'.lang("char", "skill_id").'</a></th>' : '' ).'
                    <th align="right"><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=1&amp;dir='.$dir.'"'.( ( $order_by == 1 ) ? ' class="'.$order_dir.'"' : '' ).'>'.lang("char", "skill_name").'</a></th>
                    <th><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=2&amp;dir='.$dir.'"'.( ( $order_by == 2 ) ? ' class="'.$order_dir.'"' : '' ).'>'.lang("char", "skill_value").'</a></th>
                  </tr>';

      $skill_array = array();
      $class_array = array();
      $prof_1_array = array();
      $prof_2_array = array();
      $weapon_array = array();
      $armor_array = array();
      $language_array = array();

      $skill_rank_array = array(
         75 => lang("char", "apprentice"),
        150 => lang("char", "journeyman"),
        225 => lang("char", "expert"),
        300 => lang("char", "artisan"),
        375 => lang("char", "master"),
        450 => lang("char", "inherent"),
        385 => lang("char", "wise")
      );

      for ( $i = PLAYER_SKILL_INFO_1_1; $i <= PLAYER_SKILL_INFO_1_1+384; $i+=3 )
      {
        if ( ( $char_data[$i] ) && ( skill_get_name($char_data[$i] & 0x0000FFFF) ) )
        {
          $temp = unpack("S", pack("L", $char_data[$i+1]));
          $skill = ($char_data[$i] & 0x0000FFFF);

          if ( skill_get_type($skill) == 6 )
          {
            array_push($weapon_array , array(( ( $user_lvl ) ? $skill : '' ), skill_get_name($skill), $temp[1]));
          }
          elseif ( skill_get_type($skill) == 7 )
          {
            array_push($class_array , array(( ( $user_lvl ) ? $skill : '' ), skill_get_name($skill), $temp[1]));
          }
          elseif ( skill_get_type($skill) == 8 )
          {
            array_push($armor_array , array(( ( $user_lvl ) ? $skill : '' ), skill_get_name($skill), $temp[1]));
          }
          elseif ( skill_get_type($skill) == 9 )
          {
            array_push($prof_2_array , array(( ( $user_lvl ) ? $skill : '' ), skill_get_name($skill), $temp[1]));
          }
          elseif ( skill_get_type($skill) == 10 )
          {
            array_push($language_array , array(( ( $user_lvl ) ? $skill : '' ), skill_get_name($skill), $temp[1]));
          }
          elseif ( skill_get_type($skill) == 11 )
          {
            array_push($prof_1_array , array(( ( $user_lvl ) ? $skill : '' ), skill_get_name($skill), $temp[1]));
          }
          else
          {
            array_push($skill_array , array(( ( $user_lvl ) ? $skill : '' ), skill_get_name($skill), $temp[1]));
          }
        }
      }
      unset($char_data);

      aasort($skill_array, $order_by, $dir);
      aasort($class_array, $order_by, $dir);
      aasort($prof_1_array, $order_by, $dir);
      aasort($prof_2_array, $order_by, $dir);
      aasort($weapon_array, $order_by, $dir);
      aasort($armor_array, $order_by, $dir);
      aasort($language_array, $order_by, $dir);

      foreach ( $skill_array as $data )
      {
        // this_is_junk: style left hardcoded because it's calculated.
        $max = ( ( $data[2] < $char["level"]*5 ) ? $char["level"]*5 : $data[2] );
        $output .= '
                  <tr>
                    '.( ( $user_lvl ) ? '<td>'.$data[0].'</td>' : '' ).'
                    <td align="right">'.$data[1].'</td>
                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$max)-450).'px;">
                      <span>'.$data[2].'/'.$max.'</span>
                    </td>
                  </tr>';
      }

      if ( count($class_array) )
        $output .= '
                  <tr><th class="title" colspan="'.( ( $user_lvl ) ? '3' : '2' ).'" align="left">'.lang("char", "classskills").'</th></tr>';
      foreach ( $class_array as $data )
      {
        $max = ( ( $data[2] < $char["level"]*5 ) ? $char["level"]*5 : $data[2] );
        $output .= '
                  <tr>
                    '.( ( $user_lvl ) ? '<td>'.$data[0].'</td>' : '' ).'
                    <td align="right"><a href="'.$base_datasite.$skill_datasite.'7.'.$char["class"].'.'.$data[0].'" target="_blank">'.$data[1].'</td>
                    <td valign="center" class="bar skill_bar" id="ch_ski_bg_pos0">
                    </td>
                  </tr>';
      }

      if ( count($prof_1_array) )
        $output .= '
                  <tr><th class="title" colspan="'.( ( $user_lvl ) ? '3' : '2').'" align="left">'.lang("char", "professions").'</th></tr>';
      foreach ( $prof_1_array as $data )
      {
        // this_is_junk: style left hardcoded because it's calculated.
        $max = ( ( $data[2] < 76 ) ? 75 : ( ( $data[2] < 151 ) ? 150 : ( ( $data[2] < 226 ) ? 225 : ( ( $data[2] < 301 ) ? 300 : ( ( $data[2] < 376 ) ? 375 : ( ( $data[2] < 376 ) ? 375 : 450 ) ) ) ) ) );
        $output .= '
                  <tr>
                    '.( ( $user_lvl ) ? '<td>'.$data[0].'</td>' : '').'
                    <td align="right"><a href="'.$base_datasite.$skill_datasite.'11.'.$data[0].'" target="_blank">'.$data[1].'</a></td>
                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$max)-450).'px;">
                      <span>'.$data[2].'/'.$max.' ('.$skill_rank_array[$max].')</span>
                    </td>
                  </tr>';
      }

      if ( count($prof_2_array) )
        $output .= '
                  <tr><th class="title" colspan="'.( ( $user_lvl ) ? '3' : '2' ).'" align="left">'.lang("char", "secondaryskills").'</th></tr>';
      foreach ( $prof_2_array as $data )
      {
        // this_is_junk: style left hardcoded because it's calculated.
        $max = ( ( $data[2] < 76 ) ? 75 : ( ( $data[2] < 151 ) ? 150 : ( ( $data[2] < 226 ) ? 225 : ( ( $data[2] < 301 ) ? 300 : ( ( $data[2] < 376 ) ? 375 : ( ( $data[2] < 376 ) ? 375 : 450 ) ) ) ) ) );
        $output .= '
                  <tr>
                    '.( ( $user_lvl ) ? '<td>'.$data[0].'</td>' : '' ).'
                    <td align="right"><a href="'.$base_datasite.$skill_datasite.'9.'.$data[0].'" target="_blank">'.$data[1].'</a></td>
                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$max)-450).'px;">
                      <span>'.$data[2].'/'.$max.' ('.$skill_rank_array[$max].')</span>
                    </td>
                  </tr>';
      }

      if ( count($weapon_array) )
        $output .= '
                  <tr><th class="title" colspan="'.( ( $user_lvl ) ? '3' : '2' ).'" align="left">'.lang("char", "weaponskills").'</th></tr>';
      foreach ( $weapon_array as $data )
      {
        // this_is_junk: style left hardcoded because it's calculated.
        $max = ( ( $data[2] < $char["level"]*5 ) ? $char["level"]*5 : $data[2] );
        $output .= '
                  <tr>
                    '.( ( $user_lvl ) ? '<td>'.$data[0].'</td>' : '' ).'
                    <td align="right">'.$data[1].'</td>
                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$max)-450).'px;">
                      <span>'.$data[2].'/'.$max.'</span>
                    </td>
                  </tr>';
      }

      if ( count($armor_array) )
        $output .= '
                  <tr><th class="title" colspan="'.( ( $user_lvl ) ? '3' : '2' ).'" align="left">'.lang("char", "armorproficiencies").'</th></tr>';
      foreach ( $armor_array as $data )
      {
        $max = ( ( $data[2] < $char["level"]*5 ) ? $char["level"]*5 : $data[2] );
        $output .= '
                  <tr>
                    '.( ( $user_lvl ) ? '<td>'.$data[0].'</td>' : '' ).'
                    <td align="right">'.$data[1].'</td>
                    <td valign="center" class="bar skill_bar" id="ch_ski_bg_pos0">
                    </td>
                  </tr>';
      }

      if ( count($language_array) )
        $output .= '
                  <tr><th class="title" colspan="'.( ( $user_lvl ) ? '3' : '2' ).'" align="left">'.lang("char", "languages").'</th></tr>';
      foreach ( $language_array as $data )
      {
        // this_is_junk: style left hardcoded because it's calculated.
        $max = ( ( $data[2] < $char["level"]*5 ) ? $char["level"]*5 : $data[2] );
        $output .= '
                  <tr>
                    '.( ( $user_lvl ) ? '<td>'.$data[0].'</td>' : '' ).'
                    <td align="right">'.$data[1].'</td>
                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$max)-450).'px;">
                      <span>'.$data[2].'/'.$max.'</span>
                    </td>
                  </tr>';
      }

      $output .= '
                </table>
                <br />
              </div>
              <br />
            </div>
            <br />
            <table class="hidden">
              <tr>
                <td>';
      // button to user account page, user account page has own security
      makebutton(lang("char", "chars_acc"), 'user.php?action=edit_user&amp;id='.$owner_acc_id.'', 130);
      $output .= '
                </td>
                <td>';

      // only higher level GM with delete access can edit character
      //  character edit allows removal of character items, so delete permission is needed
      if ( ( $user_lvl > $owner_gmlvl ) && ( $user_lvl >= $action_permission["delete"] ) )
      {
                  //makebutton($lang_char["edit_button"], 'char_edit.php?id='.$id.'&amp;realm='.$realmid.'', 130);
        $output .= '
                </td>
                <td>';
      }
      // only higher level GM with delete access, or character owner can delete character
      if ( ( ( $user_lvl > $owner_gmlvl ) && ( $user_lvl >= $action_permission["delete"] ) ) || ( $owner_name === $user_name ) )
      {
        makebutton(lang("char", "del_char"), 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 130);
        $output .= '
                </td>
                <td>';
      }
      // only GM with update permission can send mail, mail can send items, so update permission is needed
      if ( $user_lvl >= $action_permission["update"] )
      {
        makebutton(lang("char", "send_mail"), 'mail.php?type=ingame_mail&amp;to='.$char["name"].'', 130);
        $output .= '
                </td>
                <td>';
      }
      makebutton(lang("global", "back"), 'javascript:window.history.back()" type="def', 130);
      $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>
          <!-- end of char_achieve.php -->';
    }
    else
      error(lang("char", "no_permission"));
  }
  else
    error(lang("char", "no_char_found"));

}


//########################################################################################################################
// MAIN
//########################################################################################################################

//$action = (isset($_GET["action"])) ? $_GET["action"] : NULL;

$output .= '
      <div class="bubble">';

char_skill();

unset($action_permission);

require_once 'footer.php';


?>
