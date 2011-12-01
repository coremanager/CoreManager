<?php
/*
    CoreManager, PHP Front End for ArcEmu, MaNGOS, and TrinityCore
    Copyright (C) 2010-2011  CoreManager Project

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
valid_login($action_permission["view"]);

//########################################################################################################################
// SHOW CHAR REPUTATION
//########################################################################################################################
function char_rep()
{
  global $output, $realm_id, $characters_db, $logon_db, $corem_db, $action_permission,
    $site_encoding, $user_lvl, $user_name, $sql, $core;

  require_once 'libs/fact_lib.php';
  $reputation_rank = fact_get_reputation_rank_arr();
  $reputation_rank_length = fact_get_reputation_rank_length();

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

  if ( $core == 1 )
    $result = $sql["char"]->query("SELECT acct, name, race, class, level, gender FROM characters WHERE guid='".$id."' LIMIT 1");
  else
    $result = $sql["char"]->query("SELECT account AS acct, name, race, class, level, gender FROM characters WHERE guid='".$id."' LIMIT 1");

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

    $s_query = "SELECT *, SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_name."'";
    $s_result = $sql["mgr"]->query($s_query);
    $s_fields = $sql["mgr"]->fetch_assoc($s_result);
    $owner_gmlvl = $s_fields["gm"];
    $view_mod = $s_fields["View_Mod_Rep"];

    if ( $owner_gmlvl >= 1073741824 )
      $owner_gmlvl -= 1073741824;

    // owner configured overrides
    $view_override = false;
    if ( $view_mod > 0 )
    {
      if ( $view_mod == 1 )
        ;// TODO: Add friends limit
      elseif ( $view_mod == 2 )
      {
        // only registered users may view this page
        if ( $user_lvl > -1 )
          $view_override = true;
      }
    }

    // visibility overrides for specific tabs
    $view_inv_override = false;
    if ( $s_fields["View_Mod_Inv"] > 0 )
    {
      if ( $s_fields["View_Mod_Inv"] == 1 )
        ;// TODO: Add friends limit
      elseif ( $s_fields["View_Mod_Inv"] == 2 )
      {
        // only registered users may view this tab
        if ( $user_lvl > -1 )
          $view_inv_override = true;
      }
    }
    else
    {
      if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
        $view_inv_override = true;
    }

    $view_talent_override = false;
    if ( $s_fields["View_Mod_Talent"] > 0 )
    {
      if ( $s_fields["View_Mod_Talent"] == 1 )
        ;// TODO: Add friends limit
      elseif ( $s_fields["View_Mod_Talent"] == 2 )
      {
        // only registered users may view this tab
        if ( $user_lvl > -1 )
          $view_talent_override = true;
      }
    }
    else
    {
      if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
        $view_talent_override = true;
    }

    $view_achieve_override = false;
    if ( $s_fields["View_Mod_Achieve"] > 0 )
    {
      if ( $s_fields["View_Mod_Achieve"] == 1 )
        ;// TODO: Add friends limit
      elseif ( $s_fields["View_Mod_Achieve"] == 2 )
      {
        // only registered users may view this tab
        if ( $user_lvl > -1 )
          $view_achieve_override = true;
      }
    }
    else
    {
      if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
        $view_achieve_override = true;
    }

    $view_quest_override = false;
    if ( $s_fields["View_Mod_Quest"] > 0 )
    {
      if ( $s_fields["View_Mod_Quest"] == 1 )
        ;// TODO: Add friends limit
      elseif ( $s_fields["View_Mod_Quest"] == 2 )
      {
        // only registered users may view this tab
        if ( $user_lvl > -1 )
          $view_quest_override = true;
      }
    }
    else
    {
      if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
        $view_quest_override = true;
    }

    $view_friends_override = false;
    if ( $s_fields["View_Mod_Friends"] > 0 )
    {
      if ( $s_fields["View_Mod_Friends"] == 1 )
        ;// TODO: Add friends limit
      elseif ( $s_fields["View_Mod_Friends"] == 2 )
      {
        // only registered users may view this tab
        if ( $user_lvl > -1 )
          $view_friends_override = true;
      }
    }
    else
    {
      if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
        $view_friends_override = true;
    }

    $view_view_override = false;
    if ( $s_fields["View_Mod_View"] > 0 )
    {
      if ( $s_fields["View_Mod_View"] == 1 )
        ;// TODO: Add friends limit
      elseif ( $s_fields["View_Mod_View"] == 2 )
      {
        // only registered users may view this tab
        if ( $user_lvl > -1 )
          $view_view_override = true;
      }
    }
    else
    {
      if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
        $view_view_override = true;
    }

    $view_pets_override = false;
    if ( $s_fields["View_Mod_Pets"] > 0 )
    {
      if ( $s_fields["View_Mod_Pets"] == 1 )
        ;// TODO: Add friends limit
      elseif ( $s_fields["View_Mod_Pets"] == 2 )
      {
        // only registered users may view this tab
        if ( $user_lvl > -1 )
          $view_pets_override = true;
      }
    }
    else
    {
      if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
        $view_pets_override = true;
    }

    $view_skill_override = false;
    if ( $s_fields["View_Mod_Skill"] > 0 )
    {
      if ( $s_fields["View_Mod_Skill"] == 1 )
        ;// TODO: Add friends limit
      elseif ( $s_fields["View_Mod_Skill"] == 2 )
      {
        // only registered users may view this tab
        if ( $user_lvl > -1 )
          $view_skill_override = true;
      }
    }
    else
    {
      if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
        $view_skill_override = true;
    }

    $view_pvp_override = false;
    if ( $s_fields["View_Mod_PvP"] > 0 )
    {
      if ( $s_fields["View_Mod_PvP"] == 1 )
        ;// TODO: Add friends limit
      elseif ( $s_fields["View_Mod_PvP"] == 2 )
      {
        // only registered users may view this tab
        if ( $user_lvl > -1 )
          $view_pvp_override = true;
      }
    }
    else
    {
      if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
        $view_pvp_override = true;
    }

    if ( ( $view_override ) || ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
    {
      // this_is_junk: ArcEmu stores reputation in a single field
      //               [faction id][unk1][unk2][standing],
      //               I'm sure the two unk's are useful data, I just don't need it here.
      //               But, we're going to break the values into two arrays
      if ( $core == 1 )
      {
        $result = $sql["char"]->query("SELECT reputation FROM characters WHERE guid='".$id."'");
        $result = $sql["char"]->fetch_assoc($result);
        $result = $result["reputation"];
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
        $result = $sql["char"]->query("SELECT faction, standing FROM character_reputation WHERE guid='".$id."' AND (flags & 1 = 1)");
        $factions = array();
        $faction_ranks = array();
        
        while ( $fact = $sql["char"]->fetch_assoc($result) )
        {
          array_push($factions, $fact["faction"]);
          array_push($faction_ranks, $fact["standing"]);
        }
      }

      $output .= '
          <center>
            <div class="tab">
              <ul>
                <li class="selected"><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "char_sheet").'</a></li>';

      if ( $view_inv_override )
        $output .= '
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "inventory").'</a></li>';

      if ( $view_talent_override )
        $output .= '
                '.( ( $char["level"] < 10 ) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "talents").'</a></li>' ).'';

      if ( $view_achieve_override )
        $output .= '
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "achievements").'</a></li>';

      if ( $view_quest_override )
        $output .= '
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "quests").'</a></li>';

      if ( $view_friends_override )
        $output .= '
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "friends").'</a></li>';

      if ( $view_view_override )
        $output .= '
                <li><a href="char_view.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "view").'</a></li>';

      $output .= '
               </ul>
            </div>
            <div class="tab_content">
              <div class="tab">
                <ul>
                  <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "char_sheet").'</a></li>';

      if ( ( char_get_class_name($char["class"]) == "Hunter" ) && ( $view_pets_override ) )
        $output .= '
                  <li><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "pets").'</a></li>';

      $output .= '
                  <li class="selected"><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "reputation").'</a></li>';

      if ( $view_skill_override )
        $output .= '
                  <li><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "skills").'</a></li>';

      if ( $view_pvp_override )
        $output .= '
                  <li><a href="char_pvp.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "pvp").'</a></li>';

      if ( ( $owner_name == $user_name ) || ( $user_lvl >= get_page_permission("insert", "char_mail.php") ) )
        $output .= '
                  <li><a href="char_mail.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "mail").'</a></li>';

      $output .= '
                </ul>
              </div>
              <div class="tab_content2">
                <font class="bold">
                  '.htmlentities($char["name"], ENT_COMPAT, $site_encoding).' -
                  <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                  <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" /> - '.lang("char", "level_short").char_get_level_color($char["level"]).'
                </font>
                <br />
                <br />';

      $temp_out = array
      (
        // this_is_junk: style left hardcoded because it's insane.
        1 => array('
                <table class="lined" id="ch_rep_rep_alliance">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi1" onclick="expand(\'i1\', this, \'Alliance\')">[-] '.lang("char", "rep_alliance").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i1" class="lined" style="width: 535px; display: table;">', 0),
        2 => array('
                <table class="lined" id="ch_rep_rep_horde">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi2" onclick="expand(\'i2\', this, \'Horde\')">[-] '.lang("char", "rep_horde").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i2" class="lined" style="width: 535px; display: table;">', 0),
        3 => array('
                <table class="lined" id="ch_rep_rep_alliance_forces">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi3" onclick="expand(\'i3\', this, \'Alliance Forces\')">[-] '.lang("char", "rep_alliance_forces").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i3" class="lined" style="width: 535px; display: table;">', 0),
        4 => array('
                <table class="lined" id="ch_rep_rep_horde_forces">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi4" onclick="expand(\'i4\', this, \'Horde Forces\')">[-] '.lang("char", "rep_horde_forces").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i4" class="lined" style="width: 535px; display: table;">', 0),
        5 => array('
                <table class="lined" id="ch_rep_rep_steamwheedle_cartel">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi5" onclick="expand(\'i5\', this, \'Steamwheedle Cartels\')">[-] '.lang("char", "rep_steamwheedle_cartel").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i5" class="lined" style="width: 535px; display: table;">', 0),
        6 => array('
                <table class="lined" id="ch_rep_rep_the_burning_crusade">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi6" onclick="expand(\'i6\', this, \'The Burning Crusade\')">[-] '.lang("char", "rep_the_burning_crusade").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i6" class="lined" style="width: 535px; display: table;">', 0),
        7 => array('
                <table class="lined" id="ch_rep_rep_shattrath_city">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi7" onclick="expand(\'i7\', this, \'Shattrath City\')">[-] '.lang("char", "rep_shattrath_city").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i7" class="lined" style="width: 535px; display: table;">', 0),
        8 => array('
                <table class="lined" id="ch_rep_rep_alliance_vanguard">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi8" onclick="expand(\'i8\', this, \'Alliance Vanguard\')">[-] '.lang("char", "rep_alliance_vanguard").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i8" class="lined" style="width: 535px; display: table;">', 0),
        9 => array('
                <table class="lined" id="ch_rep_rep_horde_expedition">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi9" onclick="expand(\'i9\', this, \'Horde Expedition \')">[-] '.lang("char", "rep_horde_expedition").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i9" class="lined" style="width: 535px; display: table;">', 0),
       10 => array('
                <table class="lined" id="ch_rep_rep_sholazar_basin">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi10" onclick="expand(\'i10\', this, \'Sholazar Basin\')">[-] '.lang("char", "rep_sholazar_basin").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i10" class="lined" style="width: 535px; display: table;">', 0),
       11 => array('
                <table class="lined" id="ch_rep_rep_wrath_of_the_lich_king">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi11" onclick="expand(\'i11\', this, \'Wrath of the Lich King\')">[-] '.lang("char", "rep_wrath_of_the_lich_king").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i11" class="lined" style="width: 535px; display: table;">', 0),
       12 => array('
                <table class="lined" id="ch_rep_rep_other">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi12" onclick="expand(\'i12\', this, \'Other\')">[-] '.lang("char", "rep_other").'</div>
                    </th>
                  </tr>
                  <tr>
                    <td>
                      <table id="i12" class="lined" style="width: 535px; display: table;">', 0),
        0 => array('
                <table class="lined" id="ch_rep_rep_unknown">
                  <tr>
                    <th colspan="3" align="left">
                      <div id="divi13" onclick="expand(\'i13\', this, \'Unknown\')">[-] '.lang("char", "rep_unknown").'</div>
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

          $rep_rank      = fact_get_reputation_rank($faction, $standing, $char["race"]);
          $rep_rank_name = $reputation_rank[$rep_rank];
          $rep_cap       = $reputation_rank_length[$rep_rank];
          $rep           = fact_get_reputation_at_rank($faction, $standing, $char["race"]);
          $faction_name  = fact_get_faction_name($faction);
          $ft            = fact_get_faction_tree($faction);

          // not show alliance rep for horde and vice versa:
          if ( ( ((1 << ($char["race"] - 1)) & 690) && ( ( $ft == 1 ) || ( $ft == 3 ) ) ) || ( ((1 << ($char["race"] - 1)) & 1101) && ( ( $ft == 2 ) || ( $ft == 4 ) ) ) )
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
                          <td colspan="2"><br /><br />'.lang("global", "err_no_records_found").'<br /><br /></td>
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

char_rep();

unset($action_permission);

require_once 'footer.php';


?>
