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

/*
   Based on the modifications by 'xclanet' to code submitted to Trinity Core Forums by 'bombillero'
   http://www.trinitycore.org/forum/character-3d-preview-t7108p2.html?amp;
*/


require_once 'header.php';
require_once("libs/data_lib.php");
require_once 'libs/char_lib.php';
valid_login($action_permission["view"]);

function char_racegender($race, $gender)
{
  $char_race = array(
    1 => 'human',
    2 => 'orc',
    3 => 'dwarf',
    4 => 'nightelf',
    5 => 'scourge',
    6 => 'tauren',
    7 => 'gnome',
    8 => 'troll',
    10 => 'bloodelf',
    11 => 'draenei');
        
  $char_gender = array(
    0 => 'male',
    1 => 'female');

  return $char_race[$race].$char_gender[$gender];
}

function wowhead_did($item)
{
  global $sql, $core;

  if ( $core == 1)
    $query = $sql["world"]->query("SELECT `displayid` FROM items WHERE `entry`='".$item."' LIMIT 1");
  else
    $query = $sql["world"]->query("SELECT `displayid` FROM item_template WHERE `entry`='".$item."' LIMIT 1");

  $result = $sql["world"]->fetch_assoc($query);

  $displayid = $result["displayid"];

  return $displayid;
}

//########################################################################################################################
// SHOW CHARACTER
//########################################################################################################################

function char_view()
{
  global $output, $action_permission, $user_lvl, $user_name, $sql, $core;

  if ( empty($_GET["id"]) )
    error(lang("global", "empty_fields"));
  else
    $id = $_GET["id"];

  if ( $core == 1 )
    $query = $sql["char"]->query("SELECT * FROM characters WHERE `guid`='".$id."'");
  else
    $query = $sql["char"]->query("SELECT *, account AS acct FROM characters WHERE `guid`='".$id."'");
  $char = $sql["char"]->fetch_assoc($query);

  // we get owner permissions first
  $owner_acc_id = $char["acct"];
  if ( $core == 1 )
    $aresult = $sql["logon"]->query("SELECT login FROM accounts WHERE acct='".$owner_acc_id."'");
  else
    $aresult = $sql["logon"]->query("SELECT username AS login FROM account WHERE id='".$owner_acc_id."'");
  $owner = $sql["logon"]->fetch_assoc($aresult);
  $owner_name = $owner["login"];
  $s_query = "SELECT SecurityLevel FROM config_accounts WHERE Login='".$owner_name."'";
  $s_result = $sql["mgr"]->query($s_query);
  $s_fields = $sql["mgr"]->fetch_assoc($s_result);
  $owner_gmlvl = $s_fields["gm"];
  
  if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
  {
    if ( $core == 1 )
    {
      $data = explode(';',$char["data"]);

      $item_head = $data[PLAYER_VISIBLE_ITEM_1_ENTRYID];
      $item_neck = $data[PLAYER_VISIBLE_ITEM_2_ENTRYID];
      $item_shoulder = $data[PLAYER_VISIBLE_ITEM_3_ENTRYID];
      $item_shirt = $data[PLAYER_VISIBLE_ITEM_4_ENTRYID];
      $item_chest = $data[PLAYER_VISIBLE_ITEM_5_ENTRYID];
      $item_belt = $data[PLAYER_VISIBLE_ITEM_6_ENTRYID];
      $item_legs = $data[PLAYER_VISIBLE_ITEM_7_ENTRYID];
      $item_feet = $data[PLAYER_VISIBLE_ITEM_8_ENTRYID];
      $item_wrist = $data[PLAYER_VISIBLE_ITEM_9_ENTRYID];
      $item_gloves = $data[PLAYER_VISIBLE_ITEM_10_ENTRYID];
      $item_finger1 = $data[PLAYER_VISIBLE_ITEM_11_ENTRYID];
      $item_finger2 = $data[PLAYER_VISIBLE_ITEM_12_ENTRYID];
      $item_trinket1 = $data[PLAYER_VISIBLE_ITEM_13_ENTRYID];
      $item_trinket2 = $data[PLAYER_VISIBLE_ITEM_14_ENTRYID];
      $item_back = $data[PLAYER_VISIBLE_ITEM_15_ENTRYID];
      $item_main_hand = $data[PLAYER_VISIBLE_ITEM_16_ENTRYID];
      $item_off_hand = $data[PLAYER_VISIBLE_ITEM_17_ENTRYID];
      $item_ranged_slot = $data[PLAYER_VISIBLE_ITEM_18_ENTRYID];
      // don't bother showing guild tabards
      if ( $data[PLAYER_VISIBLE_ITEM_19_ENTRYID] <> 5976 )
        $item_tabard = $data[PLAYER_VISIBLE_ITEM_19_ENTRYID];

      $b = $data[PLAYER_BYTES];
      $b2 = $data[PLAYER_BYTES_2];
      $ha = ($b>>16)%256;
      $hc = ($b>>24)%256;
      $fa = ($b>>8)%256;
      $sk = $b%256;
      $fh = $b2%256;
    }
    else
    {
      $inv_query = "SELECT * FROM character_inventory WHERE guid='".$id."'";
      $inv_result = $sql["char"]->query($inv_query);

      while ( $inv_row = $sql["char"]->fetch_assoc($inv_result) )
      {
        if ( $inv_row["bag"] == 0 )
        {
          switch ( $inv_row["slot"] )
          {
            case 0:
            {
              $item_head = $inv_row["item_template"];
              break;
            }
            case 1:
            {
              $item_neck = $inv_row["item_template"];
              break;
            }
            case 2:
            {
              $item_shoulder = $inv_row["item_template"];
              break;
            }
            case 3:
            {
              $item_shirt = $inv_row["item_template"];
              break;
            }
            case 4:
            {
              $item_chest = $inv_row["item_template"];
              break;
            }
            case 5:
            {
              $item_belt = $inv_row["item_template"];
              break;
            }
            case 6:
            {
              $item_legs = $inv_row["item_template"];
              break;
            }
            case 7:
            {
              $item_feet = $inv_row["item_template"];
              break;
            }
            case 8:
            {
              $item_wrist = $inv_row["item_template"];
              break;
            }
            case 9:
            {
              $item_gloves = $inv_row["item_template"];
              break;
            }
            case 10:
            {
              $item_finger1 = $inv_row["item_template"];
              break;
            }
            case 11:
            {
              $item_finger2 = $inv_row["item_template"];
              break;
            }
            case 12:
            {
              $item_trinket1 = $inv_row["item_template"];
              break;
            }
            case 13:
            {
              $item_trinket2 = $inv_row["item_template"];
              break;
            }
            case 14:
            {
              $item_back = $inv_row["item_template"];
              break;
            }
            case 15:
            {
              $item_main_hand = $inv_row["item_template"];
              break;
            }
            case 16:
            {
              $item_off_hand = $inv_row["item_template"];
              break;
            }
            case 17:
            {
              $item_ranged_slot = $inv_row["item_template"];
              break;
            }
            case 18:
            {
              // don't bother showing guild tabards
              if ( $inv_row["item_template"] <> 5976 )
                $item_tabard = $inv_row["item_template"];
              break;
            }
          }
        }
      }

      $b = $char["playerBytes"];
      $b2 = $char["playerBytes2"];
      $ha = ($b>>16)%256;
      $hc = ($b>>24)%256;
      $fa = ($b>>8)%256;
      $sk = $b%256;
      $fh = $b2%256;
    }

    //------------------------Character Tabs---------------------------------
    // we start with a lead of 10 spaces,
    //  because last line of header is an opening tag with 8 spaces
    //  keep html indent in sync, so debuging from browser source would be easy to read
    
    // this_is_junk: style hard coded just to hide box around 3D display.
    $output .= '
          <center>
            <script type="text/javascript">
              // <![CDATA[
                function wrap()
                {
                  if (getBrowserWidth() > 1024)
                  document.write(\'</table></td><td><table class="lined" id="ch_fri_large_screen">\');
                }
              // ]]>
            </script>
            <div id="tab">
              <ul>
                <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "char_sheet").'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "inventory").'</a></li>
                '.( ( $char["level"] < 10 ) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "talents").'</a></li>' ).'
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "achievements").'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "quests").'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "friends").'</a></li>
                <li id="selected"><a href="char_view.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "view").'</a></li>
              </ul>
            </div>
            <div id="tab_content">
              <font class="bold">
                '.htmlentities($char["name"]).' -
                <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\', \'item_tooltipx\')" onmouseout="oldtoolTip()" alt="" />
                <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\', \'item_tooltipx\')" onmouseout="oldtoolTip()" alt="" /> - '.lang("char", "level_short").char_get_level_color($char["level"]).'
              </font>
              <div id="model_scene" align="center">
                <object id="wowhead" type="application/x-shockwave-flash"
                    data="http://static.wowhead.com/modelviewer/ModelView.swf"
                    height="400px" width="400px" style="outline:none;">
                  <param name="quality" value="high">
                  <param name="allowscriptaccess" value="always">
                  <param name="menu" value="false">
                  <param value="transparent" name="wmode">
                  <param name="flashvars" value="model='.char_racegender($char["race"], $char["gender"]).'
                    &modelType=16&ha='.$ha.'&hc='.$hc.'&fa='.$fa.'&sk='.$sk.'&fh='.$fh.'&fc=0
                    &contentPath=http://static.wowhead.com/modelviewer/&blur=1&equipList=';
    if ( $item_head )
      $output .= '
                    1,'.wowhead_did($item_head).',';
    if ( $item_shoulder )
      $output .= '
                    3,'.wowhead_did($item_shoulder).',';
    if ( $item_back )
      $output .= '
                    16,'.wowhead_did($item_back).',';
    if ( $item_shirt )
      $output .= '
                    4,'.wowhead_did($item_shirt).',';
    if ( $item_chest )
      $output .= '
                    5,'.wowhead_did($item_chest).',';
    if ( $item_wrist )
      $output .= '
                    9,'.wowhead_did($item_wrist).',';
    if ( $item_gloves )
      $output .= '
                    10,'.wowhead_did($item_gloves).',';
    if ( $item_belt )
      $output .= '
                    6,'.wowhead_did($item_belt).',';
    if ( $item_legs )
      $output .= '
                    7,'.wowhead_did($item_legs).',';
    if ( $item_feet )
      $output .= '
                    8,'.wowhead_did($item_feet).',';
    if ( $item_off_hand )
      $output .= '
                    14,'.wowhead_did($item_off_hand).',';
    if ( $item_main_hand )
      $output .= '
                    21,'.wowhead_did($item_main_hand).',';
    if ( $item_tabard )
      $output .= '
                    19,'.wowhead_did($item_tabard).',';
                    
    // remove extra , at end of line if there is one
    if ( substr( $output, strlen( $output ) - 1, 1 ) == ',' )
      $output = substr( $output, 0, strlen( $output ) - 1 );
      
    $output .= '">
                  <param name="movie" value="http://static.wowhead.com/modelviewer/ModelView.swf">
                </object>
              </div>
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
          </center>';
  }
  else
    error(lang("char", "no_permission"));
}


//########################################################################################################################
// MAIN
//########################################################################################################################

$output .= '
      <div class="bubble">';

char_view();

unset($action_permission);

require_once 'footer.php';


?>
