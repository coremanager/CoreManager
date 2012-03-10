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
require_once 'libs/item_lib.php';
require_once 'libs/forum_lib.php';
require_once("libs/bb2html_lib.php");
valid_login($action_permission["view"]);

//##############################################################################################################
// EDIT USER
//##############################################################################################################
function edit_user()
{
  global $output, $corem_db, $logon_db, $characters_db, $corem_db, $realm_id, $invite_only, $timezone_offset,
    $user_name, $user_id, $expansion_select, $server, $developer_test_mode, $multi_realm_mode,
    $remember_me_checked, $sql, $core;

  $refguid = $sql["mgr"]->result($sql["mgr"]->query("SELECT InvitedBy FROM point_system_invites WHERE PlayersAccount='".$user_id."'"), 0, 'InvitedBy');
  $referred_by = $sql["char"]->result($sql["char"]->query("SELECT name FROM characters WHERE guid='".$refguid."'"), 0, 'name');
  unset($refguid);
  
  if ( $core == 1 )
    $query = "SELECT email, flags, lastip FROM accounts WHERE login='".$user_name."'";
  else
    $query = "SELECT email, expansion AS flags, last_ip AS lastip FROM account WHERE username='".$user_name."'";

  if ( $acc = $sql["logon"]->fetch_assoc($sql["logon"]->query($query)) )
  {
    // if we have a screen name, we need to use it
    $screen_name_query = "SELECT *,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 1), ' ', -1) AS avatarsex,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 2), ' ', -1) AS avatarrace,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 3), ' ', -1) AS avatarclass,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 4), ' ', -1) AS avatarlevel
        FROM config_accounts WHERE Login='".$user_name."'";
    $screen_name = $sql["mgr"]->query($screen_name_query);
    $screen_name = $sql["mgr"]->fetch_assoc($screen_name);

    if ( $screen_name["SecurityLevel"] >= 1073741824 )
      $screen_name["SecurityLevel"] -= 1073741824;

    // ArcEmu: find out if we're using an encrypted password for this account
    if ( $core == 1 )
    {
      $pass_query = "SELECT * FROM accounts WHERE login='".$user_name."' AND encrypted_password<>''";
      $pass_result = $sql["logon"]->query($pass_query);
      $arc_encrypted = $sql["logon"]->num_rows($pass_result);
    }

    $output .= '
          <center>
            <script type="text/javascript" src="libs/js/sha1.js"></script>
            <script type="text/javascript">
              // <![CDATA[
                function do_submit_data ()
                {';
    if ( $core == 1 )
    {
      if ( $arc_encrypted )
        $output .= '
                  document.form.pass.value = hex_sha1("'.strtoupper($user_name).':"+document.form.user_pass.value.toUpperCase());';
      else
        $output .= '
                  document.form.pass.value = document.form.user_pass.value;';
    }
    else
      $output .= '
                  document.form.pass.value = hex_sha1("'.strtoupper($user_name).':"+document.form.user_pass.value.toUpperCase());';

    $output .= '
                  document.form.pass.value = document.form.pass.value.toUpperCase();
                  do_submit();
                }
              // ]]>
            </script>
            <div id="edit_fieldset" class="fieldset_border">
              <span class="legend">'.lang("edit", "edit_acc").'</span>
              <form method="post" action="edit.php?action=doedit_user" name="form">
                <input type="hidden" name="pass" value="" maxlength="256" />
                <table class="flat">
                  <tr>
                    <td>'.lang("edit", "id").':</td>
                    <td colspan="2">'.htmlspecialchars($user_id).'</td>
                  </tr>
                  <tr>
                    <td>'.lang("edit", "username").':</td>
                    <td colspan="2">'.htmlspecialchars($user_name).'</td>
                  </tr>';
    if ( !$screen_name["ScreenName"] )
    {
      $output .= '
                  <tr>
                    <td>'.lang("edit", "screenname").':</td>
                    <td colspan="2"><input type="text" name="screenname" size="42" maxlength="14" /></td>
                  </tr>';
    }
    else
    {
      $output .= '
                  <tr>
                    <td>'.lang("edit", "screenname").':</td>
                    <td colspan="2">'.htmlspecialchars($screen_name["ScreenName"]).'</td>
                  </tr>';
    }
    $output .= '
                  <tr>
                    <td>'.lang("edit", "password").':</td>
                    <td colspan="2">
                      <input type="text" name="user_pass" size="39" maxlength="40" value="******" />
                      <img src="img/information.png" onmousemove="oldtoolTip(\''.lang("edit", "pass_warning").'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" />
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("edit", "mail").':</td>';
    if ( $screen_name["TempEmail"] )
      $output .= '
                    <td colspan="2">
                      <a href="edit.php?action=cancel_email_change" >
                        <img src="img/aff_warn.gif" onmousemove="oldtoolTip(\''.lang("edit", "email_changed").'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" />
                      </a>
                      <input type="text" name="mail" size="39" maxlength="225" value="'.$acc["email"].'" />
                    </td>';
    else
      $output .= '
                    <td colspan="2"><input type="text" name="mail" size="42" maxlength="225" value="'.$acc["email"].'" /></td>';
    $output .= '
                  </tr>
                  <tr>
                    <td>'.lang("edit", "invited_by").':</td>
                    <td colspan="2">';
    if ( $referred_by == NULL )
      $output .= '
                      <input type="text" name="referredby" size="20" maxlength="12" value="'.$referred_by.'" /> ('.lang("user", "charname").')';
    else
      $output .= '
                    '.htmlspecialchars($referred_by).'';
    $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("edit", "gm_level").':</td>
                    <td colspan="2">'.id_get_gm_level($screen_name["SecurityLevel"]).' ( '.$screen_name["SecurityLevel"].' )</td>
                  </tr>
                  <tr>
                    <td>'.lang("edit", "last_ip").':</td>
                    <td colspan="2">'.htmlspecialchars($acc["lastip"]).'</td>
                  </tr>';
    if ( $expansion_select )
    {
      if ( $core == 1 )
      {
        $output .= '
                    <tr>
                      <td>'.lang("edit", "client_type").':</td>
                      <td colspan="2">
                        <select name="expansion">
                          <option value="24" '.( ( $acc["flags"] == 24 ) ? 'selected="selected"' : '' ).'>'.lang("edit", "wotlktbc").'</option>
                          <option value="16" '.( ( $acc["flags"] == 16 ) ? 'selected="selected"' : '' ).'>'.lang("edit", "wotlk").'</option>
                          <option value="8" '.( ( $acc["flags"] == 8 ) ? 'selected="selected"' : '' ).'>'.lang("edit", "tbc").'</option>
                          <option value="0" '.( ( $acc["flags"] == 0 ) ? 'selected="selected"' : '' ).'>'.lang("edit", "classic").'</option>
                        </select>
                      </td>
                    </tr>';
      }
      else
      {
        $output .= '
                    <tr>
                      <td>'.lang("edit", "client_type").':</td>
                      <td colspan="2">
                        <select name="expansion">
                          <option value="2" '.( ( $acc["flags"] == 2 ) ? 'selected="selected"' : '' ).'>'.lang("edit", "wotlktbc").'</option>
                          <option value="1" '.( ( $acc["flags"] == 1 ) ? 'selected="selected"' : '' ).'>'.lang("edit", "tbc").'</option>
                          <option value="0" '.( ( $acc["flags"] == 0 ) ? 'selected="selected"' : '' ).'>'.lang("edit", "classic").'</option>
                        </select>
                      </td>
                    </tr>';
      }
    }

    $output .= '
                    <tr>
                      <td>'.lang("edit", "credits").':</td>
                      <td colspan="2">'.( ( $screen_name["Credits"] < 0 ) ? lang("edit", "unlimited") : (float)$screen_name["Credits"] ).'</td>
                    </tr>';
    
    foreach ( $characters_db as $db )
    {
      $sqlt = new SQL;
      $sqlt->connect($db["addr"], $db["user"], $db["pass"], $db["name"], $db["encoding"]);
      
      if ( $core == 1 )
        $query = "SELECT COUNT(*) FROM characters WHERE acct='".$user_id."'";
      else
        $query = "SELECT COUNT(*) FROM characters WHERE account='".$user_id."'";
      $result = $sqlt->query($query);
      $fields = $sqlt->fetch_assoc($result);
      
      $c_count += $fields["COUNT(*)"];
    }
    
    $output .= '
                  <tr>
                    <td>'.lang("edit", "tot_chars").':</td>
                    <td colspan="2">'.$c_count.'</td>
                  </tr>';
                  
    $realms = $sql["mgr"]->query("SELECT `Index` AS id, Name AS name FROM config_servers");
    if ( ( 1 < $sql["mgr"]->num_rows($realms) ) && ( 1 < count($server) ) && ( 1 < count($characters_db) ) )
    {
      while ( $realm = $sql["mgr"]->fetch_assoc($realms) )
      {
        $sql["char"]->connect($characters_db[$realm["id"]]['addr'], $characters_db[$realm["id"]]['user'], $characters_db[$realm["id"]]['pass'], $characters_db[$realm["id"]]['name'], $characters_db[$realm["id"]]['encoding']);
        if ( $core == 1 )
          $result = $sql["char"]->query("SELECT guid, name, race, class, level, gender, timestamp
            FROM characters WHERE acct='".$user_id."'");
        else
          $result = $sql["char"]->query("SELECT guid, name, race, class, level, gender, logout_time AS timestamp
            FROM characters WHERE account='".$user_id."'");
      
        // calculate timezone offset
        $time_offset = $timezone_offset * 3600;

        $output .= '
                    <tr>
                      <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="3">'.lang("index", "realm").': '.$realm["name"].'</td>
                    </tr>
                    <tr>
                      <td>'.lang("edit", "characters").':</td>
                      <td>'.$sql["char"]->num_rows($result).'</td>
                    </tr>';

        while ( $char = $sql["char"]->fetch_assoc($result) )
        {
          if ( $char["timestamp"] <> 0 )
            $lastseen = date("F j, Y @ Hi", $char["timestamp"] + $time_offset);
          else
            $lastseen = '-';

          $output .= '
                    <tr>
                      <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\'---></td>
                      <td>
                        <a href="char.php?id='.$char["guid"].'&amp;realm='.$realm["id"].'">'.$char["name"].'</a> -
                        <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                        <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt=""/> - '.lang("char", "level_short").char_get_level_color($char["level"]).'
                      </td>
                      <td>'.$lastseen.'</td>
                    </tr>';
        }
      }
      unset($realm);
    }
    else
    {
      if ( $core == 1 )
        $result = $sql["char"]->query("SELECT guid, name, race, class, level, gender, timestamp
          FROM characters WHERE acct='".$user_id."'");
      else
        $result = $sql["char"]->query("SELECT guid, name, race, class, level, gender, logout_time AS timestamp
          FROM characters WHERE account='".$user_id."'");
      
      // calculate timezone offset
      $time_offset = $timezone_offset * 3600;

      $output .= '
                  <!-- tr>
                    <td>'.lang("edit", "characters").':</td>
                    <td>'.$sql["char"]->num_rows($result).'</td>
                  </tr -->';
      while ( $char = $sql["char"]->fetch_assoc($result) )
      {
        if ( $char["timestamp"] <> 0 )
          $lastseen = date("F j, Y @ Hi", $char["timestamp"] + $time_offset);
        else
          $lastseen = '-';

        $output .= '
                  <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\'---></td>
                    <td>
                      <a href="char.php?id='.$char["guid"].'">'.$char["name"].'</a> -
                      <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                      <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt=""/> - '.lang("char", "level_short").char_get_level_color($char["level"]).'
                    </td>
                    <td>'.$lastseen.'</td>
                  </tr>';
      }
    }
    unset($result);
    unset($realms);

    $override_remember_me = $_COOKIE["corem_override_remember_me"];
    if ( !isset($override_remember_me) )
      $override_remember_me = 1;

    if ( $remember_me_checked )
      $output .= '
                  <tr>
                    <td>'.lang("edit", "override").':</td>
                    <td><input type="checkbox" name="override" value="1" '.( ( $override_remember_me ) ? 'checked="checked"' : '' ).' />
                  </tr>';

    $output .= '
                  <tr>
                    <td>';
    makebutton(lang("edit", "update"), 'javascript:do_submit_data()" type="wrn', 130);
    $output .= '
                    </td>
                    <td colspan="2">';
    makebutton(lang("global", "back"), 'javascript:window.history.back()" type="def', 130);
    $output .= '
                    </td>
                  </tr>
                </table>
              </form>
            </div>
            <br />
            <div id="edit_theme_fieldset" class="fieldset_border">
              <span class="legend">'.lang("edit", "profile_options").'</span>
              <form action="edit.php" method="get" name="form3">
                <input type="hidden" name="action" value="profile_set" />
                <table class="hidden" id="edit_profile_table">
                  <tr>
                    <td align="left" colspan="3">'.lang("edit", "profile_info").'</td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">
                      <textarea name="profileinfo" rows="6" cols="65">'.$screen_name["Info"].'</textarea>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">'.lang("edit", "signature").'</td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">';
    bbcode_add_editor();
    $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">
                      <textarea id="msg" name="signature" rows="6" cols="65">'.$screen_name["Signature"].'</textarea>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">'.lang("edit", "prefavatar").'</td>
                  </tr>';
    if ( $screen_name["SecurityLevel"] == 0 )
    {
      if ( $screen_name["Avatar"] == '' )
      {
        if ( $core == 1 )
          $avatar_query = "SELECT acct, name, gender, race, class, level,
            (SELECT gm FROM `".$logon_db["name"]."`.accounts WHERE `".$logon_db["name"]."`.accounts.acct=`".$characters_db[$realm_id]['name']."`.characters.acct) AS gmlevel,
            (SELECT login FROM `".$logon_db["name"]."`.accounts WHERE `".$logon_db["name"]."`.accounts.acct=`".$characters_db[$realm_id]['name']."`.characters.acct) AS login
            FROM `".$characters_db[$realm_id]['name']."`.characters
            WHERE level IN (SELECT MAX(level) FROM `".$characters_db[$realm_id]['name']."`.characters WHERE acct='".$user_id."')";
        elseif ( $core == 2 )
          $avatar_query = "SELECT account AS acct, name, gender, race, class, level,
            (SELECT gmlevel FROM `".$logon_db["name"]."`.account WHERE `".$logon_db["name"]."`.account.id=`".$characters_db[$realm_id]['name']."`.characters.account) AS gmlevel,
            (SELECT username FROM `".$logon_db["name"]."`.account WHERE `".$logon_db["name"]."`.account.id=`".$characters_db[$realm_id]['name']."`.characters.account) AS login
            FROM `".$characters_db[$realm_id]['name']."`.characters
            WHERE level IN (SELECT MAX(level) FROM `".$characters_db[$realm_id]['name']."`.characters WHERE account='".$user_id."') AND account='".$user_id."'";
        else
          $avatar_query = "SELECT account AS acct, name, gender, race, class, level,
            (SELECT gmlevel FROM `".$logon_db["name"]."`.account_access WHERE `".$logon_db["name"]."`.account_access.id=`".$characters_db[$realm_id]['name']."`.characters.account) AS gmlevel,
            (SELECT username FROM `".$logon_db["name"]."`.account WHERE `".$logon_db["name"]."`.account.id=`".$characters_db[$realm_id]['name']."`.characters.account) AS login
            FROM `".$characters_db[$realm_id]['name']."`.characters
            WHERE level IN (SELECT MAX(level) FROM `".$characters_db[$realm_id]['name']."`.characters WHERE account='".$user_id."') AND account='".$user_id."'";

        $avatar_result = $sql["char"]->query($avatar_query);
        $avatar_fields = $sql["char"]->fetch_assoc($avatar_result);

        $avatar = gen_avatar_panel($avatar_fields["level"], $avatar_fields["gender"], $avatar_fields["race"], $avatar_fields["class"], 1, 0);

        $screen_name["avatarlevel"] = $avatar_fields["level"];
        $screen_name["avatarrace"] = $avatar_fields["race"];
        $screen_name["avatarclass"] = $avatar_fields["class"];
        $screen_name["avatarsex"] = $avatar_fields["gender"];
      }
      else
        $avatar = gen_avatar_panel($screen_name["avatarlevel"], $screen_name["avatarsex"], $screen_name["avatarrace"], $screen_name["avatarclass"], 1, $screen_name["SecurityLevel"]);

      $output .= '
                  <tr>
                    <td id="forum_topic_avatar" rowspan="6">
                      <center>'.$avatar.'</center>
                    </td>
                    <tr>
                      <td>'.lang("edit", "usedefault").':</td>
                      <td><input type="checkbox" name="use_default" value="1" '.( ( $screen_name["Avatar"] == '' ) ? 'checked="checked"' : '' ).' />&nbsp;'.lang("edit", "usedefaultinfo").'</td>
                    </tr>
                    <td>'.lang("edit", "gender").':</td>
                    <td>
                      <select name="avatargender">
                        <option value="0" '.( ( $screen_name["avatarsex"] == 0 ) ? 'selected="selected"' : '' ).' >'.lang("edit", "male").'</option>
                        <option value="1" '.( ( $screen_name["avatarsex"] == 1 ) ? 'selected="selected"' : '' ).' >'.lang("edit", "female").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("edit", "race").':</td>
                    <td>
                      <select name="avatarrace">';

      $races = array
      (
         1 => array( 1, lang("id_tab", "human")),
         2 => array( 2, lang("id_tab", "orc")),
         3 => array( 3, lang("id_tab", "dwarf")),
         4 => array( 4, lang("id_tab", "nightelf")),
         5 => array( 5, lang("id_tab", "undead")),
         6 => array( 6, lang("id_tab", "tauren")),
         7 => array( 7, lang("id_tab", "gnome")),
         8 => array( 8, lang("id_tab", "troll")),
        10 => array(10, lang("id_tab", "bloodelf")),
        11 => array(11, lang("id_tab", "draenei")),
      );

      foreach ( $races as $race )
      {
        $output .= '
                          <option value="'.$race[0].'" '.( ( $screen_name["avatarrace"] == $race[0] ) ? 'selected="selected"' : '' ).' >'.$race[1].'</option>';
      }

      $output .= '
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("edit", "class").':</td>
                    <td>
                      <select name="avatarclass">';

      $classes = $class_names = array
      (
         1  => array( 1, lang("id_tab", "warrior")),
         2  => array( 2, lang("id_tab", "paladin")),
         3  => array( 3, lang("id_tab", "hunter")),
         4  => array( 4, lang("id_tab", "rogue")),
         5  => array( 5, lang("id_tab", "priest")),
         6  => array( 6, lang("id_tab", "death_knight")),
         7  => array( 7, lang("id_tab", "shaman")),
         8  => array( 8, lang("id_tab", "mage")),
         9  => array( 9, lang("id_tab", "warlock")),
         11 => array(11, lang("id_tab", "druid")),
      );

      foreach ( $classes as $class )
      {
        $output .= '
                          <option value="'.$class[0].'" '.( ( $screen_name["avatarclass"] == $class[0] ) ? 'selected="selected"' : '' ).' >'.$class[1].'</option>';
      }

      $output .= '
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("edit", "level").':</td>
                    <td>
                      <input type="text" name="avatarlevel" value="'.$screen_name["avatarlevel"].'" />
                    </td>
                  </tr>';
    }
    else
    {
      $output .= '
                  <tr>
                    <td id="forum_topic_avatar" rowspan="4">
                      <center>'.gen_avatar_panel($screen_name["avatarlevel"], $screen_name["avatarsex"], $screen_name["avatarrace"], $screen_name["avatarclass"], 0, $screen_name["SecurityLevel"]).'</center>
                    </td>
                    <td>'.lang("edit", "gender").':</td>
                    <td>'.lang("edit", "unavailable").'</td>
                  </tr>
                  <tr>
                    <td>'.lang("edit", "race").':</td>
                    <td>'.lang("edit", "unavailable").'</td>
                  </tr>
                  <tr>
                    <td>'.lang("edit", "class").':</td>
                    <td>'.lang("edit", "unavailable").'</td>
                  </tr>
                  <tr>
                    <td>'.lang("edit", "level").':</td>
                    <td>'.lang("edit", "unavailable").'</td>
                  </tr>';
    }
    $output .= '
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">'.lang("edit", "viewmods").'</td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "char_sheet").':</td>
                    <td colspan="2">
                      <select name="viewmod_sheet">
                        <option value="0" '.( ( $screen_name["View_Mod_Sheet"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_Sheet"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_Sheet"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "achievements").':</td>
                    <td colspan="2">
                      <select name="viewmod_achieve">
                        <option value="0" '.( ( $screen_name["View_Mod_Achieve"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_Achieve"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_Achieve"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "friends").':</td>
                    <td colspan="2">
                      <select name="viewmod_friends">
                        <option value="0" '.( ( $screen_name["View_Mod_Friends"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_Friends"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_Friends"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "inventory").':</td>
                    <td colspan="2">
                      <select name="viewmod_inv">
                        <option value="0" '.( ( $screen_name["View_Mod_Inv"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_Inv"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_Inv"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "pets").':</td>
                    <td colspan="2">
                      <select name="viewmod_pets">
                        <option value="0" '.( ( $screen_name["View_Mod_Pets"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_Pets"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_Pets"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "pvp").':</td>
                    <td colspan="2">
                      <select name="viewmod_pvp">
                        <option value="0" '.( ( $screen_name["View_Mod_PvP"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_PvP"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_PvP"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "quests").':</td>
                    <td colspan="2">
                      <select name="viewmod_quests">
                        <option value="0" '.( ( $screen_name["View_Mod_Quest"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_Quest"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_Quest"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "reputation").':</td>
                    <td colspan="2">
                      <select name="viewmod_rep">
                        <option value="0" '.( ( $screen_name["View_Mod_Rep"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_Rep"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_Rep"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "skills").':</td>
                    <td colspan="2">
                      <select name="viewmod_skills">
                        <option value="0" '.( ( $screen_name["View_Mod_Skill"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_Skill"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_Skill"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "talents").':</td>
                    <td colspan="2">
                      <select name="viewmod_talents">
                        <option value="0" '.( ( $screen_name["View_Mod_Talent"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_Talent"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_Talent"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("char", "view").':</td>
                    <td colspan="2">
                      <select name="viewmod_view">
                        <option value="0" '.( ( $screen_name["View_Mod_View"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "onlyme").'</option>
                        <!-- option value="1" '.( ( $screen_name["View_Mod_View"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "friends").'</option -->
                        <option value="2" '.( ( $screen_name["View_Mod_View"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("edit", "reg_users").'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td>';
      makebutton(lang("edit", "save"), 'javascript:do_submit(\'form3\', 0)', 130);
      $output .= '
                    </td>
                  </tr>
                </table>
              </form>
            </div>
            <div id="edit_theme_fieldset" class="fieldset_border">
              <span class="legend">'.lang("edit", "invite_options").'</span>
              <table class="hidden" id="edit_theme_table">
                <tr>
                  <td align="left">'.lang("edit", "invite_email").': </td>
                  <td align="right">
                    <form action="edit.php" method="get" name="form4">
                      <input type="hidden" name="action" value="send_invite" />
                      <input type="text" name="invite_email" value="" size="30" />
                    </form>
                  </td>
                </tr>
                <tr>
                  <td colspan="2">';
    makebutton(lang("edit", "sendinvite"), 'javascript:do_submit(\'form4\', 0)', 130);
    $output .= '
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td align="left" colspan="2">'.lang("edit", "active_invites").': </td>
                </tr>
                <tr>
                  <td colspan="2">
                    <table class="lined" id="active_invites_table">
                      <tr>
                        <th width="15%">Delete</th>
                        <th>Email</th>
                        <th width="15%">Resend</th>
                      </tr>';

    $invites_query = "SELECT * FROM invitations WHERE issuer_acct_id='".$user_id."'";
    $invites_result = $sql["mgr"]->query($invites_query);

    while ( $row = $sql["mgr"]->fetch_assoc($invites_result) )
    {
      $output .= '
                      <tr>
                        <td>
                          <a href="edit.php?action=delete_invite&key='.$row["invitation_key"].'">
                            <img src="img/aff_cross.png" alt="Delete" />
                          </a>
                        </td>
                        <td>'.$row["invited_email"].'</td>
                        <td>
                          <a href="edit.php?action=resend_invite&key='.$row["invitation_key"].'">
                            <img src="img/add.png" alt="Resend" />
                          </a>
                        </td>
                      </tr>';
    }

    $output .= '
                    </table>
                  </td>
                </tr>
              </table>
            </div>
            <br />
            <div id="edit_theme_fieldset" class="fieldset_border">
              <span class="legend">'.lang("edit", "my_bags").'</span>
              <table class="hidden" id="edit_theme_table">';

    // 
    $bag_query = "SELECT * FROM point_system_prize_bags WHERE owner='".$user_id."'";
    $bag_result = $sql["mgr"]->query($bag_query);

    while ( $bag = $sql["mgr"]->fetch_assoc($bag_result) )
    {
      $output .= '
                <tr>
                  <td align="right">
                    <a href="point_system.php?action=view_bag&amp;bag_id='.$bag["entry"].'">
                      <img src="'.get_item_icon(1725).'" alt="" />
                    </a>
                  </td>
                  <td align="left">
                    <span>&nbsp;'.$bag["slots"].' '.lang("edit", "bag_slots").'</span>
                  </td>
                </tr>';
    }

    $output .= '
              </table>
            </div>
            <br />
            <div id="edit_theme_fieldset" class="fieldset_border">
              <span class="legend">'.lang("edit", "theme_options").'</span>
              <table class="hidden" id="edit_theme_table">
                <tr>
                  <td align="left">'.lang("edit", "select_layout_lang").': </td>
                  <td align="right">
                    <form action="edit.php" method="get" name="form1">
                      <input type="hidden" name="action" value="lang_set" />
                      <select name="lang">
                        <optgroup label="'.lang("edit", "language").'">';
    if ( is_dir('./lang') )
    {
      if ( $dh = opendir('./lang') )
      {
        while ( ( $file = readdir($dh) ) == true )
        {
          $lang = explode('.', $file);
          if ( isset($lang[1]) && ( $lang[1] == 'php' ) )
          {
            $output .= '
                        <option value="'.$lang[0].'"'.( ( isset($_COOKIE["corem_lang"]) && ( $_COOKIE["corem_lang"] == $lang[0] ) ) ? ' selected="selected" ' : '' ).'>'.lang("edit", $lang[0]).'</option>';
          }
        }
        closedir($dh);
      }
    }
    $output .= '
                        </optgroup>
                      </select>
                    </form>
                  </td>
                  <td>';
    makebutton(lang("edit", "save"), 'javascript:do_submit(\'form1\', 0)', 130);
    $output .= '
                  </td>
                </tr>
                <tr>
                  <td align="left">'.lang("edit", "select_theme").': </td>
                  <td align="right">
                    <form action="edit.php" method="get" name="form2">
                      <input type="hidden" name="action" value="theme_set" />
                      <select name="theme">
                        <optgroup label="'.lang("edit", "theme").'">';
    if ( is_dir('./themes') )
    {
      if ( $dh = opendir('./themes') )
      {
        while ( ( $file = readdir($dh) ) == true )
        {
          if ( ( $file == '.' ) || ( $file == '..' ) || ( $file == '.htaccess' ) || ( $file == 'index.html' ) || ( $file == '.svn' ) )
            ;
          else
          {
            $output .= '
                          <option value="'.$file.'"'.( ( isset($_COOKIE["corem_theme"] ) && ( $_COOKIE["corem_theme"] == $file) ) ? ' selected="selected" ' : '' ).'>'.$file.'</option>';
          }
        }
        closedir($dh);
      }
    }
    $output .= '
                        </optgroup>
                      </select>
                    </form>
                  </td>
                  <td>';
    makebutton(lang("edit", "save"), 'javascript:do_submit(\'form2\',0)', 130);
    $output .= '
                  </td>
                </tr>
              </table>
            </div>
            <br />
          </center>';
  }
  else
    error(lang("global", "err_no_records_found"));

}


//#############################################################################################################
//  DO EDIT USER
//#############################################################################################################
function doedit_user()
{
  global $output, $user_name, $logon_db, $corem_db, $send_mail_on_email_change, $lang, $defaultoption,
    $url_path, $format_mail_html, $GMailSender, $smtp_cfg, $title, $sql, $core;

  if ( ( empty($_POST["pass"]) || ( $_POST["pass"] == '' ) )
    && ( empty($_POST["mail"]) || ( $_POST["mail"] == '' ) )
    && ( empty($_POST["expansion"]) || ( $_POST["expansion"] == '' ) )
    && ( empty($_POST["referredby"]) || ( $_POST["referredby"] == '' ) ) )
    redirect('edit.php?error=1');

  // ArcEmu: find out if we're using an encrypted password for this account
  if ( $core == 1 )
  {
    $pass_query = "SELECT * FROM accounts WHERE login='".$user_name."' AND encrypted_password<>''";
    $pass_result = $sql["logon"]->query($pass_query);
    $arc_encrypted = $sql["logon"]->num_rows($pass_result);
  }

  // password
  if ( $_POST["user_pass"] != "******" )
    if ( $core == 1 )
    {
      if ( $arc_encrypted )
        $new_pass = "encrypted_password='".$sql["logon"]->quote_smart($_POST["pass"])."', ";
      else
        $new_pass = "password='".$sql["logon"]->quote_smart($_POST["pass"])."', ";
    }
    else
      $new_pass = "sha_pass_hash='".$sql["logon"]->quote_smart($_POST["pass"])."', ";

  // other
  $screenname = $sql["logon"]->quote_smart(trim($_POST["screenname"]));
  $new_mail = $sql["logon"]->quote_smart(trim($_POST["mail"]));
  $new_expansion = ( ( isset($_POST["expansion"]) ) ? $sql["logon"]->quote_smart(trim($_POST["expansion"])) : $defaultoption );
  $referredby = $sql["logon"]->quote_smart(trim($_POST["referredby"]));

  // if we received a Screen Name, make sure it does not conflict with other Screen Names or with
  // the game server's login names.
  if ( $screenname )
  {
    $query = "SELECT * FROM config_accounts WHERE ScreenName='".$screenname."'";
    $sn_result = $sql["mgr"]->query($query);
    $sn = $sql["mgr"]->fetch_assoc($sn_result);
    if ( $sn["Login"] <> $user_name )
    {
      if ( $sql["mgr"]->num_rows($sn_result) <> 0 )
        redirect('edit.php?error=6');

      if ( $core == 1 )
        $query = "SELECT * FROM accounts WHERE login='".$screenname."'";
      else
        $query = "SELECT * FROM account WHERE username='".$screenname."'";

      $sn_result = $sql["logon"]->query($query);
      if ( $sql["logon"]->num_rows($sn_result) <> 0 )
        redirect('edit.php?error=6');
    }
  }

  // set screen name
  if ( $screenname )
  {
    $sn_check_query = "SELECT * FROM config_accounts WHERE Login='".$user_name."'";
    $sn_check_result = $sql["mgr"]->query($sn_check_query);

    // don't add a new entry if we already have one
    if ( $sql["mgr"]->num_rows($sn_check_result) == 0 )
      $sn_result = $sql["mgr"]->query("INSERT INTO config_accounts (Login, ScreenName) VALUES ('".$user_name."', '".$screenname."')");
    else
      $sn_result = $sql["mgr"]->query("UPDATE config_accounts SET ScreenName='".$screenname."' WHERE Login='".$user_name."'");
  }

  //make sure the mail is valid mail format
  require_once 'libs/valid_lib.php';
  if ( !( ( valid_email($new_mail) ) && ( strlen($new_mail) < 225 ) ) )
    redirect('edit.php?error=2');

  // find out if our email changed
  if ( $core == 1 )
    $email_query = "SELECT email FROM accounts WHERE login='".$user_name."'";
  else
    $email_query = "SELECT email FROM account WHERE username='".$user_name."'";
  $email_result = $sql["logon"]->query($email_query);
  $email = $sql["logon"]->fetch_assoc($email_result);

  // if it did change, then save it
  // if we didn't have an email address already, we just accept the new one
  if ( ( $email["email"] != '' ) && ( $email["email"] != $new_mail ) )
  {
    // if we have to send a confirm message, do so
    // if not, we're clear to just save it as usual
    if ( $send_mail_on_email_change )
    {
      // generate a private key based on the new email
      $new_mail_sha = sha1($new_mail);

      // prepare our confirmation message
      if ( $format_mail_html )
        $file_name = "lang/mail_templates/".$lang."/change_email.tpl";
      else
        $file_name = "lang/mail_templates/".$lang."/change_email_nohtml.tpl";
      $fh = fopen($file_name, "r");
      $subject = fgets($fh, 4096);
      $body = fread($fh, filesize($file_name));
      fclose($fh);

      $mail = $email["email"];

      $subject = str_replace("<title>", $title, $subject);
      if ( $format_mail_html )
      {
        $body = str_replace("\n", "<br />", $body);
        $body = str_replace("\r", " ", $body);
      }
      $body = str_replace("<username>", $user_name, $body);
      $body = str_replace("<email>", $new_mail, $body);
      $body = str_replace("<key>", $new_mail_sha, $body);
      $body = str_replace("<title>", $title, $body);

      $server_addr = ( ( $_SERVER["SERVER_PORT"] != 80 ) ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"] : $_SERVER["SERVER_NAME"] );

      // if we aren't installed in / then append the path to $server_addr
      $server_addr .= ( ( $url_path != "" ) ? $url_path : "" );

      $body = str_replace("<base_url>", $server_addr, $body);

      if ( $GMailSender )
      {
        require_once("libs/mailer/authgMail_lib.php");

        $fromName = $title." Admin";
        authgMail($from_mail, $fromName, $mail, $mail, $subject, $body, $smtp_cfg);
      }
      else
      {
        require_once("libs/mailer/class.phpmailer.php");
        $mailer = new PHPMailer();
        $mailer->Mailer = $mailer_type;
        if ( $mailer_type == "smtp" )
        {
          $mailer->Host = $smtp_cfg["host"];
          $mailer->Port = $smtp_cfg["port"];
          if( $smtp_cfg["user"] != "" )
          {
            $mailer->SMTPAuth  = true;
            $mailer->Username  = $smtp_cfg["user"];
            $mailer->Password  =  $smtp_cfg["pass"];
          }
        }

        $mailer->WordWrap = 50;
        $mailer->From = $from_mail;
        $mailer->FromName = $title." Admin";
        $mailer->Subject = $subject;
        $mailer->IsHTML($format_mail_html);
        $mailer->Body = $body;
        $mailer->AddAddress($mail);
        $mailer->Send();
        $mailer->ClearAddresses();
      }

      // save new email
      $temp_email_query = "UPDATE config_accounts SET TempEmail='".$new_mail."' WHERE Login='".$user_name."'";
      $temp_email_result = $sql["mgr"]->query($temp_email_query);

      // save OLD email back for now
      $new_mail = $email["email"];
    }
  }
    
  // Overriding Remember Me is done via a cookie
  // usage is backward from the name
  // 1 = show check box
  // 0 = hide
  if ( !isset($_POST["override"]) )
    $override = 0;
  else
    $override = 1;

  if ( ( $override != $_COOKIE["corem_override_remember_me"] ) || ( !isset($_COOKIE["corem_override_remember_me"]) ) )
  {
    if ( $override )
      setcookie("corem_override_remember_me", "1", time()+60*60*24*30);
    else
      setcookie("corem_override_remember_me", "0", time()+60*60*24*30);

    $other_changes = 1;
  }

  // change other settings
  if ( $core == 1 )
    $query = "UPDATE accounts SET email='".$new_mail."', ".$new_pass." flags='".$new_expansion."' WHERE login='".$user_name."'";
  else
    $query = "UPDATE account SET email='".$new_mail."', ".$new_pass." expansion='".$new_expansion."', v=0, s=0 WHERE username='".$user_name."'";

  $acct_result = $sql["logon"]->query($query);

  if ( doupdate_referral($referredby) || $acct_result || $sn_result || $other_changes )
    redirect('edit.php?error=3');
  else
    redirect('edit.php?error=4');

}


//###############################################################################################################
// SET DEFAULT INTERFACE LANGUAGE
//###############################################################################################################
function lang_set()
{
  if ( empty($_GET["lang"]) )
    redirect('edit.php?error=1');
  else
    $lang = addslashes($_GET["lang"]);

  if ( $lang )
  {
    setcookie('corem_lang', $lang, time()+60*60*24*30*6); //six month
    redirect('edit.php');
  }
  else
    redirect('edit.php?error=1');
}


//###############################################################################################################
// SET DEFAULT INTERFACE THEME
//###############################################################################################################
function theme_set()
{
  if ( empty($_GET["theme"]) )
    redirect('edit.php?error=1');
  else
    $tmpl = addslashes($_GET["theme"]);

  if ( $tmpl )
  {
    setcookie('corem_theme', $tmpl, time()+3600*24*30*6); //six month
    redirect('edit.php');
  }
  else
    redirect('edit.php?error=1');
}


//###############################################################################################################
// SET PROFILE INFO
//###############################################################################################################
function profile_set()
{
  global $user_name, $user_lvl, $sql;

  $info = $sql["logon"]->quote_smart($_GET["profileinfo"]);
  $signature = $sql["logon"]->quote_smart($_GET["signature"]);
  $gender = $sql["logon"]->quote_smart($_GET["avatargender"]);
  $race = $sql["logon"]->quote_smart($_GET["avatarrace"]);
  $class = $sql["logon"]->quote_smart($_GET["avatarclass"]);
  $level = $sql["logon"]->quote_smart($_GET["avatarlevel"]);

  // Character Sheet Page visibility overrides
  $view_mod_sheet = $sql["logon"]->quote_smart($_GET["viewmod_sheet"]);
  $view_mod_achieve = $sql["logon"]->quote_smart($_GET["viewmod_achieve"]);
  $view_mod_friends = $sql["logon"]->quote_smart($_GET["viewmod_friends"]);
  $view_mod_inv = $sql["logon"]->quote_smart($_GET["viewmod_inv"]);
  $view_mod_pets = $sql["logon"]->quote_smart($_GET["viewmod_pets"]);
  $view_mod_pvp = $sql["logon"]->quote_smart($_GET["viewmod_pvp"]);
  $view_mod_quests = $sql["logon"]->quote_smart($_GET["viewmod_quests"]);
  $view_mod_rep = $sql["logon"]->quote_smart($_GET["viewmod_rep"]);
  $view_mod_skills = $sql["logon"]->quote_smart($_GET["viewmod_skills"]);
  $view_mod_talents = $sql["logon"]->quote_smart($_GET["viewmod_talents"]);
  $view_mod_view = $sql["logon"]->quote_smart($_GET["viewmod_view"]);

  // the main Character Sheet's override must be the same as the most permissive of the others
  if ( $view_mod_sheet < $view_mod_achieve )
    $view_mod_sheet = $view_mod_achieve;
  if ( $view_mod_sheet < $view_mod_friends )
    $view_mod_sheet = $view_mod_friends;
  if ( $view_mod_sheet < $view_mod_inv )
    $view_mod_sheet = $view_mod_inv;
  if ( $view_mod_sheet < $view_mod_pets )
    $view_mod_sheet = $view_mod_pets;
  if ( $view_mod_sheet < $view_mod_pvp )
    $view_mod_sheet = $view_mod_pvp;
  if ( $view_mod_sheet < $view_mod_quests )
    $view_mod_sheet = $view_mod_quests;
  if ( $view_mod_sheet < $view_mod_rep )
    $view_mod_sheet = $view_mod_rep;
  if ( $view_mod_sheet < $view_mod_skills )
    $view_mod_sheet = $view_mod_skills;
  if ( $view_mod_sheet < $view_mod_talents )
    $view_mod_sheet = $view_mod_talents;
  if ( $view_mod_sheet < $view_mod_view )
    $view_mod_sheet = $view_mod_view;

  // for a little more XSS coverage we'll compare the
  // mysql_real_escape_string (quote_smart we used above) of info & signatures with their htmlspecialchars
  // if we fail, we set them to empty strings
  if ( !( htmlspecialchars($_GET["profileinfo"]) == $info ) )
    $info = "";

  if ( !( htmlspecialchars($_GET["signature"]) == $signature ) )
    $signature = "";

  // gm's can't change their avatars
  if ( $user_lvl == 0 )
  {
    if ( !is_numeric($level) && ( ( $level < 1 ) || ( $level > 80 ) ) )
      redirect("edit.php?error=7");

    if ( $_GET["use_default"] )
      $avatar = "";
    else
      $avatar = $gender.' '.$race.' '.$class.' '.$level;
  }

  // profile
  $query = "UPDATE config_accounts SET Avatar='".$avatar."', Info='".$info."', Signature='".$signature."' WHERE Login='".$user_name."'";

  $sql["mgr"]->query($query);

  // view mods
  $query = "UPDATE config_accounts SET View_Mod_Sheet='".$view_mod_sheet."', View_Mod_Achieve='".$view_mod_achieve."', View_Mod_Friends='".$view_mod_friends."', View_Mod_Inv='".$view_mod_inv."', View_Mod_Pets='".$view_mod_pets."', View_Mod_PvP='".$view_mod_pvp."', View_Mod_Quest='".$view_mod_quests."', View_Mod_Rep='".$view_mod_rep."', View_Mod_Skill='".$view_mod_skills."', View_Mod_Talent='".$view_mod_talents."', View_Mod_View='".$view_mod_view."' WHERE Login='".$user_name."'";

  $sql["mgr"]->query($query);

  if ( $sql["mgr"]->affected_rows() )
    redirect("edit.php?error=3");
  else
    redirect("edit.php?error=4");
}


//###############################################################################################################
// CONFIRM or CANCEL EMAIL CHANGE
//###############################################################################################################
function confirm_email()
{
  global $user_name, $sql, $core;

  // get our confirmation key
  $key = $sql["mgr"]->quote_smart($_GET["key"]);

  // check that we have an account with that key
  $check_email_query = "SELECT TempEmail FROM config_accounts WHERE SHA(TempEmail)='".$key."'";
  $check_email_result = $sql["mgr"]->query($check_email_query);

  if ( $sql["mgr"]->num_rows($check_email_result) != 0 )
  {
    // get our new email
    $temp_email = $sql["mgr"]->fetch_assoc($check_email_result);
    $temp_email = $temp_email["TempEmail"];

    // save the email
    if ( $core == 1 )
      $set_email_query = "UPDATE accounts SET email='".$temp_email."' WHERE login='".$user_name."'";
    else
      $set_email_query = "UPDATE account SET email='".$temp_email."' WHERE username='".$user_name."'";
    $sql["logon"]->query($set_email_query);

    // clear our temp
    $clear_temp_query = "UPDATE config_accounts SET TempEmail='' WHERE Login='".$user_name."'";
    $sql["mgr"]->query($clear_temp_query);

    redirect("edit.php?error=3");
  }
  else
    redirect("edit.php?error=8");
}

function cancel_email_change()
{
  global $user_name, $sql;

  $cancel_query = "UPDATE config_accounts SET TempEmail='' WHERE Login='".$user_name."'";
  $sql["mgr"]->query($cancel_query);

  redirect("edit.php");
}


//###############################################################################################################
// INVITATIONS
//###############################################################################################################
function send_invite($resend = false)
{
  global $lang, $GMailSender, $smtp_cfg, $title, $format_mail_html, $user_name, $user_id,
    $url_path, $sql, $core;

  if ( !$resend )
  {
    if ( empty($_GET["invite_email"]) )
      redirect("edit.php?error=1");

    $invited = $sql["mgr"]->quote_smart($_GET["invite_email"]);

    // a little XSS prevention
    if ( $invited != htmlspecialchars($_GET["invite_email"]) )
      redirect("edit.php?error=1");

    // make sure we're not inviting someone who already has an account here
    if ( $core == 1 )
      $check_mail_query = "SELECT * FROM accounts WHERE email='".$invited."'";
    else
      $check_mail_query = "SELECT * FROM account WHERE email='".$invited."'";

    $check_mail_result = $sql["logon"]->query($check_mail_query);

    if ( $sql["logon"]->num_rows($check_mail_result) > 0 )
      redirect("edit.php?error=2");

    // make sure we're not inviting someone who already has an invitation
    $check_mail_query = "SELECT * FROM invitations WHERE invited_email='".$invited."'";

    $check_mail_result = $sql["mgr"]->query($check_mail_query);

    if ( $sql["mgr"]->num_rows($check_mail_result) > 0 )
      redirect("edit.php?error=2");

    // generate a private key based on our user name and the target's email
    $key = sha1($user_name.":".$invited);

    // get the name of one of our characters
    if ( $core == 1 )
      $char_query = "SELECT name FROM characters WHERE acct='".$user_id."' LIMIT 1";
    else
      $char_query = "SELECT name FROM characters WHERE account='".$user_id."' LIMIT 1";

    $char_result = $sql["char"]->query($char_query);
    $char = $sql["char"]->fetch_assoc($char_result);
    $char = $char["name"];

    // prepare our invitation message
    if ( $format_mail_html )
      $file_name = "lang/mail_templates/".$lang."/invite.tpl";
    else
      $file_name = "lang/mail_templates/".$lang."/invite_nohtml.tpl";
    $fh = fopen($file_name, "r");
    $subject = fgets($fh, 4096);
    $body = fread($fh, filesize($file_name));
    fclose($fh);

    $mail = $invited;

    $subject = str_replace("<title>", $title, $subject);
    if ( $format_mail_html )
    {
      $body = str_replace("\n", "<br />", $body);
      $body = str_replace("\r", " ", $body);
    }
    $body = str_replace("<username>", $user_name, $body);
    $body = str_replace("<key>", $key, $body);
    $body = str_replace("<title>", $title, $body);
    $body = str_replace("<char>", $char, $body);
    $body = str_replace("<core>", core_name($core), $body);

    $server_addr = ( ( $_SERVER["SERVER_PORT"] != 80 ) ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"] : $_SERVER["SERVER_NAME"] );

    // if we aren't installed in / then append the path to $server_addr
    $server_addr .= ( ( $url_path != "" ) ? $url_path : "" );

    $body = str_replace("<base_url>", $server_addr, $body);

    if ( $GMailSender )
    {
      require_once("libs/mailer/authgMail_lib.php");

      $fromName = $title." Admin";
      authgMail($from_mail, $fromName, $mail, $mail, $subject, $body, $smtp_cfg);
    }
    else
    {
      require_once("libs/mailer/class.phpmailer.php");
      $mailer = new PHPMailer();
      $mailer->Mailer = $mailer_type;
      if ( $mailer_type == "smtp" )
      {
        $mailer->Host = $smtp_cfg["host"];
        $mailer->Port = $smtp_cfg["port"];
        if( $smtp_cfg["user"] != "" )
        {
          $mailer->SMTPAuth  = true;
          $mailer->Username  = $smtp_cfg["user"];
          $mailer->Password  =  $smtp_cfg["pass"];
        }
      }

      $mailer->WordWrap = 50;
      $mailer->From = $from_mail;
      $mailer->FromName = $title." Admin";
      $mailer->Subject = $subject;
      $mailer->IsHTML($format_mail_html);
      $mailer->Body = $body;
      $mailer->AddAddress($mail);
      $mailer->Send();
      $mailer->ClearAddresses();
    }

    // create entry in invitations table
    $create_query = "INSERT INTO invitations (issuer_acct_id, invited_email, invitation_key) VALUES ('".$user_id."', '".$invited."', '".$key."')";
    $create_result = $sql["mgr"]->query($create_query);
  }
  else
  {
    if ( empty($_GET["key"]) )
      redirect("edit.php?error=1");

    $key = $sql["mgr"]->quote_smart($_GET["key"]);

    // a little XSS prevention
    if ( $key != htmlspecialchars($_GET["key"]) )
      redirect("edit.php?error=1");

    // get the invitation we need to resend
    $invite_query = "SELECT invited_email FROM invitations WHERE invitation_key='".$key."'";
    $invite_result = $sql["mgr"]->query($invite_query);
    $invite_result = $sql["mgr"]->fetch_assoc($invite_result);

    $invited = $invite_result["invited_email"];

    // get the name of one of our characters
    if ( $core == 1 )
      $char_query = "SELECT name FROM characters WHERE acct='".$user_id."' LIMIT 1";
    else
      $char_query = "SELECT name FROM characters WHERE account='".$user_id."' LIMIT 1";

    $char_result = $sql["char"]->query($char_query);
    $char = $sql["char"]->fetch_assoc($char_result);
    $char = $char["name"];

    // prepare our invitation message
    if ( $format_mail_html )
      $file_name = "lang/mail_templates/".$lang."/invite.tpl";
    else
      $file_name = "lang/mail_templates/".$lang."/invite_nohtml.tpl";
    $fh = fopen($file_name, "r");
    $subject = fgets($fh, 4096);
    $body = fread($fh, filesize($file_name));
    fclose($fh);

    $mail = $invited;

    $subject = str_replace("<title>", $title, $subject);
    if ( $format_mail_html )
    {
      $body = str_replace("\n", "<br />", $body);
      $body = str_replace("\r", " ", $body);
    }
    $body = str_replace("<username>", $user_name, $body);
    $body = str_replace("<key>", $key, $body);
    $body = str_replace("<title>", $title, $body);
    $body = str_replace("<char>", $char, $body);
    $body = str_replace("<core>", core_name($core), $body);

    $server_addr = ( ( $_SERVER["SERVER_PORT"] != 80 ) ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"] : $_SERVER["SERVER_NAME"] );

    $body = str_replace("<base_url>", $server_addr, $body);

    if ( $GMailSender )
    {
      require_once("libs/mailer/authgMail_lib.php");

      $fromName = $title." Admin";
      authgMail($from_mail, $fromName, $mail, $mail, $subject, $body, $smtp_cfg);
    }
    else
    {
      require_once("libs/mailer/class.phpmailer.php");
      $mailer = new PHPMailer();
      $mailer->Mailer = $mailer_type;
      if ( $mailer_type == "smtp" )
      {
        $mailer->Host = $smtp_cfg["host"];
        $mailer->Port = $smtp_cfg["port"];
        if( $smtp_cfg["user"] != "" )
        {
          $mailer->SMTPAuth  = true;
          $mailer->Username  = $smtp_cfg["user"];
          $mailer->Password  =  $smtp_cfg["pass"];
        }
      }

      $mailer->WordWrap = 50;
      $mailer->From = $from_mail;
      $mailer->FromName = $title." Admin";
      $mailer->Subject = $subject;
      $mailer->IsHTML($format_mail_html);
      $mailer->Body = $body;
      $mailer->AddAddress($mail);
      $mailer->Send();
      $mailer->ClearAddresses();
    }
  }

  redirect("edit.php");
}

function delete_invite()
{
  global $sql;

  if ( empty($_GET["key"]) )
    redirect("edit.php?error=1");

  $key = $sql["mgr"]->quote_smart($_GET["key"]);

  // a little XSS prevention
  if ( $key != htmlspecialchars($_GET["key"]) )
    redirect("edit.php?error=1");

  $delete_query = "DELETE FROM invitations WHERE invitation_key='".$key."'";
  $delete_result = $sql["mgr"]->query($delete_query);

  redirect("edit.php");
}

//###############################################################################################################
// MAIN
//###############################################################################################################
$err = ( ( isset($_GET["error"]) ) ? $_GET["error"] : NULL );

$output .= '
      <div class="bubble">
          <div class="top">';

if ( $err == 1 )
  $output .= '
            <h1><font class="error">'.lang("global", "empty_fields").'</font></h1>';
elseif ( $err == 2 )
  $output .= '
            <h1><font class="error">'.lang("edit", "use_valid_email").'</font></h1>';
elseif ( $err == 3 )
  $output .= '
            <h1><font class="error">'.lang("edit", "data_updated").'</font></h1>';
elseif ( $err == 4 )
  $output .= '
            <h1><font class="error">'.lang("edit", "error_updating").'</font></h1>';
elseif ( $err == 5 )
  $output .= '
            <h1><font class="error">'.lang("edit", "del_error").'</font></h1>';
elseif ( $err == 6 )
  $output .= '
            <h1><font class="error">'.lang("edit", "sn_error").'</font></h1>';
elseif ( $err == 7 )
  $output .= '
            <h1><font class="error">'.lang("edit", "use_valid_level").'</font></h1>';
elseif ( $err == 8 )
  $output .= '
            <h1><font class="error">'.lang("edit", "email_change_failed").'</font></h1>';
else
  $output .= '
            <h1>'.lang("edit", "edit_your_acc").'</h1>';

unset($err);

$output .= '
          </div>';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

if ( $action == "doedit_user" )
  doedit_user();
elseif ( $action == "lang_set" )
  lang_set();
elseif ( $action == "theme_set" )
  theme_set();
elseif ( $action == "profile_set" )
  profile_set();
elseif ( $action == "confirm_email" )
  confirm_email();
elseif ( $action == "cancel_email_change" )
  cancel_email_change();
elseif ( $action == "send_invite" )
  send_invite();
elseif ( $action == "delete_invite" )
  delete_invite();
elseif ( $action == "resend_invite" )
  send_invite(true);
else
  edit_user();

unset($action);
unset($action_permission);

require_once "footer.php";

?>
