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
require_once 'libs/spell_lib.php';
require_once 'libs/data_lib.php';
valid_login($action_permission["view"]);

//########################################################################################################################^M
// SHOW CHARACTER PETS
//########################################################################################################################^M
function char_pets()
{
  global $output, $realm_id, $characters_db, $arcm_db, $action_permission, $user_lvl, $user_name, 
    $site_encoding, $spell_datasite, $pet_ability, $sql, $core;

  //wowhead_tt();

  if ( empty($_GET["id"]) )
    error(lang("global", "empty_fields"));

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
  if ( is_numeric($id) )
    ;
  else
    $id = 0;

  if ( $core == 1 )
    $result = $sql["char"]->query("SELECT acct, name, race, class, level, gender
      FROM characters
      WHERE guid='".$id."' LIMIT 1");
  else
    $result = $sql["char"]->query("SELECT account AS acct, name, race, class, level, gender
      FROM characters
      WHERE guid='".$id."' LIMIT 1");

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
                  <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "char_sheet").'</a></li>
                  <li id="selected"><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "pets").'</a></li>
                  <li><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "reputation").'</a></li>
                  <li><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "skills").'</a></li>
                  <li><a href="char_pvp.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "pvp").'</a></li>';
      if ( ( $owner_name == $user_name ) || ( $user_lvl >= $action_permission["insert"] ) )
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
              <br />';

      if ( $core == 1 )
        $result = $sql["char"]->query("SELECT petnumber, level, fields,
          SUBSTRING_INDEX(SUBSTRING_INDEX(`fields`, ' ', 77), ' ', -1) AS cur_xp,
          SUBSTRING_INDEX(SUBSTRING_INDEX(`fields`, ' ', 78), ' ', -1) AS next_level_xp,
          name, happinessupdate
          FROM playerpets
          WHERE ownerguid='".$id."'");
      else
        $result = $sql["char"]->query("SELECT id AS petnumber, level, abdata AS fields,
          exp AS cur_xp,
          SUBSTRING_INDEX(SUBSTRING_INDEX(`abdata`, ' ', 78), ' ', -1) AS next_level_xp,
          name, curhappiness AS happinessupdate
          FROM character_pet 
          WHERE owner='".$id."'");

      if ( $sql["char"]->num_rows($result) )
      {
        while ( $pet = $sql["char"]->fetch_assoc($result) )
        {
          $pet_data = explode(' ',$pet["fields"]);
          $happiness = floor($pet_data[UNIT_FIELD_MAXPOWER3]/333000);
          if ( $happiness == 1)
          {
            $hap_text = 'Content';
            $hap_val = 1;
          }
          elseif ( $happiness == 2)
          {
            $hap_text = 'Happy';
            $hap_val = 2;
          }
          else
          {
            $hap_text = 'Unhappy';
            $hap_val = 0;
          }

          if ( $core == 1 )
            $pet_next_lvl_xp = $pet["next_level_xp"];
          else
            $pet_next_lvl_xp = floor(char_get_xp_to_level($pet["level"])/4);

          // this_is_junk: style left hardcoded because it's calculated.
          $output .= '
                <font class="bold">'.$pet["name"].' - lvl '.char_get_level_color($pet["level"]).'
                  <a id="ch_pet_padding" onmouseover="oldtoolTip(\''.$hap_text.'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()"><img src="img/pet/happiness_'.$hap_val.'.jpg" alt="" /></a>
                  <br /><br />
                </font>
                <table class="lined" id="ch_pet_xp">
                  <tr>
                    <td align="right">Exp:</td>
                    <td valign="top" class="bar skill_bar" style="background-position: '.(round(385*$pet["cur_xp"]/$pet_next_lvl_xp)-385).'px;">
                      <span>'.$pet["cur_xp"].'/'.$pet_next_lvl_xp.'</span>
                    </td>
                  </tr>
                  <tr>
                    <td align="right">Pet Abilities:</td>
                    <td align="left">';
          if ( $core == 1 )
            $ability_results = $sql["char"]->query("SELECT spellid FROM playerpetspells WHERE petnumber='".$pet["petnumber"]."' AND flags > 1");
          else
            $ability_results = $sql["char"]->query("SELECT spell AS spellid FROM pet_spell WHERE guid='".$pet["petnumber"]."' AND active > 1");
          // active = 0 is unused and active = 1 probably some passive auras, i dont know diference between values 129 and 193, need to check mangos source
          if ( $sql["char"]->num_rows($ability_results) )
          {
            while ( $ability = $sql["char"]->fetch_assoc($ability_results) )
            {
              $output .= '
                      <a id="ch_pet_padding" href="'.$spell_datasite.$ability["spellid"].'" target="_blank">
                        <img src="'.spell_get_icon($ability["spellid"]).'" alt="'.$ability["spellid"].'" class="icon_border_0" />
                      </a>';
            }
          }
          $output .= '
                    </td>
                  </tr>
                </table>
                <br /><br />';
        }
        unset($ability_results);
        unset($pet_next_lvl_xp);
        unset($happiness);
        unset($pet);
      }
      $output .= '
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
          </center>
          <!-- end of char_pets.php -->';
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

char_pets();

unset($action_permission);

require_once 'footer.php';


?>
