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
require_once 'libs/forum_lib.php';
require_once("libs/bb2html_lib.php");
valid_login($action_permission['view']);

//##############################################################################################################
// EDIT USER
//##############################################################################################################
function edit_user()
{
  global $output, $corem_db, $logon_db, $characters_db, $corem_db, $realm_id,
    $user_name, $user_id, $expansion_select, $server, $developer_test_mode, $multi_realm_mode,
    $remember_me_checked, $sql, $core;

  $refguid = $sql['mgr']->result($sql['mgr']->query("SELECT InvitedBy FROM point_system_invites WHERE PlayersAccount='".$user_id."'"), 0, 'InvitedBy');
  $referred_by = $sql['char']->result($sql['char']->query("SELECT name FROM characters WHERE guid='".$refguid."'"), 0, 'name');
  unset($refguid);
  
  if ( $core == 1 )
    $query = "SELECT email, flags, lastip FROM accounts WHERE login='".$user_name."'";
  else
    $query = "SELECT email, expansion AS flags, last_ip AS lastip FROM account WHERE username='".$user_name."'";

  if ( $acc = $sql['logon']->fetch_assoc($sql['logon']->query($query)) )
  {
    // if we have a screen name, we need to use it
    $screen_name_query = "SELECT *,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 1), ' ', -1) AS avatarsex,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 2), ' ', -1) AS avatarrace,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 3), ' ', -1) AS avatarclass,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 4), ' ', -1) AS avatarlevel
        FROM config_accounts WHERE Login='".$user_name."'";
    $screen_name = $sql['mgr']->query($screen_name_query);
    $screen_name = $sql['mgr']->fetch_assoc($screen_name);
    $output .= '
          <center>
            <script type="text/javascript" src="libs/js/sha1.js"></script>
            <script type="text/javascript">
              // <![CDATA[
                function do_submit_data ()
                {';
    if ( $core == 1 )
      $output .= '
                  document.form.pass.value = document.form.user_pass.value;';
    else
      $output .= '
                  document.form.pass.value = hex_sha1("'.strtoupper($user_name).':"+document.form.user_pass.value.toUpperCase());
                  document.form.pass.value = document.form.pass.value.toUpperCase();
                  do_submit();
                }
              // ]]>
            </script>
            <div id="edit_fieldset" class="fieldset_border">
              <span class="legend">'.lang('edit', 'edit_acc').'</span>
              <form method="post" action="edit.php?action=doedit_user" name="form">
                <input type="hidden" name="pass" value="" maxlength="256" />
                <table class="flat">
                  <tr>
                    <td>'.lang('edit', 'id').':</td>
                    <td>'.$user_id.'</td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'username').':</td>
                    <td>'.$user_name.'</td>
                  </tr>';
    if ( !$screen_name['ScreenName'] )
    {
      $output .= '
                  <tr>
                    <td>'.lang('edit', 'screenname').':</td>
                    <td><input type="text" name="screenname" size="42" maxlength="14" /></td>
                  </tr>';
    }
    else
    {
      $output .= '
                  <tr>
                    <td>'.lang('edit', 'screenname').':</td>
                    <td>'.$screen_name['ScreenName'].'</td>
                  </tr>';
    }
    $output .= '
                  <tr>
                    <td>'.lang('edit', 'password').':</td>
                    <td><input type="text" name="user_pass" size="42" maxlength="40" value="******" /></td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'mail').':</td>
                    <td><input type="text" name="mail" size="42" maxlength="225" value="'.$acc['email'].'" /></td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'invited_by').':</td>
                    <td>';
    if ( $referred_by == NULL )
      $output .= '
                      <input type="text" name="referredby" size="42" maxlength="12" value="'.$referred_by.'" />';
    else
      $output .= '
                    '.$referred_by.'';
    $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'gm_level').':</td>
                    <td>'.id_get_gm_level($screen_name['SecurityLevel']).' ( '.$screen_name['SecurityLevel'].' )</td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'last_ip').':</td>
                    <td>'.$acc['lastip'].'</td>
                  </tr>';
    if ( $expansion_select )
    {
      if ( $core == 1 )
      {
        $output .= '
                     <tr>
                      <td >'.lang('edit', 'client_type').':</td>
                      <td>
                        <select name="expansion">
                          <option value="24" '.( ( $acc['flags'] == 24 ) ? 'selected="selected"' : '' ).'>'.lang('edit', 'wotlktbc').'</option>
                          <option value="16" '.( ( $acc['flags'] == 16 ) ? 'selected="selected"' : '' ).'>'.lang('edit', 'wotlk').'</option>
                          <option value="8" '.( ( $acc['flags'] == 8 ) ? 'selected="selected"' : '' ).'>'.lang('edit', 'tbc').'</option>
                          <option value="0" '.( ( $acc['flags'] == 0 ) ? 'selected="selected"' : '' ).'>'.lang('edit', 'classic').'</option>
                        </select>
                      </td>
                    </tr>';
      }
      else
      {
        $output .= '
                     <tr>
                      <td >'.lang('edit', 'client_type').':</td>
                      <td>
                        <select name="expansion">
                          <option value="2" '.( ( $acc['flags'] == 2 ) ? 'selected="selected"' : '' ).'>'.lang('edit', 'wotlktbc').'</option>
                          <option value="1" '.( ( $acc['flags'] == 1 ) ? 'selected="selected"' : '' ).'>'.lang('edit', 'tbc').'</option>
                          <option value="0" '.( ( $acc['flags'] == 0 ) ? 'selected="selected"' : '' ).'>'.lang('edit', 'classic').'</option>
                        </select>
                      </td>
                    </tr>';
      }
    }
    
    foreach ( $characters_db as $db )
    {
      $sqlt = new SQL;
      $sqlt->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
      
      if ( $core == 1 )
        $query = "SELECT COUNT(*) FROM characters WHERE acct='".$user_id."'";
      else
        $query = "SELECT COUNT(*) FROM characters WHERE account='".$user_id."'";
      $result = $sqlt->query($query);
      $fields = $sqlt->fetch_assoc($result);
      
      $c_count += $fields['COUNT(*)'];
    }
    
    $output .= '
                  <tr>
                    <td>'.lang('edit', 'tot_chars').':</td>
                    <td>'.$c_count.'</td>
                  </tr>';
                  
    $realms = $sql['mgr']->query("SELECT id, name FROM realmlist");
    if ( ( 1 < $sql['mgr']->num_rows($realms) ) && ( 1 < count($server) ) && ( 1 < count($characters_db) ) )
    {
      while ( $realm = $sql['mgr']->fetch_assoc($realms) )
      {
        $sql['char']->connect($characters_db[$realm['id']]['addr'], $characters_db[$realm['id']]['user'], $characters_db[$realm['id']]['pass'], $characters_db[$realm['id']]['name']);
        if ( $core == 1 )
          $result = $sql['char']->query("SELECT guid, name, race, class, level, gender
            FROM characters WHERE acct='".$user_id."'");
        else
          $result = $sql['char']->query("SELECT guid, name, race, class, level, gender
            FROM characters WHERE account='".$user_id."'");

        $output .= '
                    <tr>
                      <td>'.lang('edit', 'characters').' '.$realm['name'].':</td>
                      <td>'.$sql['char']->num_rows($result).'</td>
                    </tr>';

        while ( $char = $sql['char']->fetch_assoc($result) )
        {
          $output .= '
                    <tr>
                      <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\'---></td>
                      <td>
                        <a href="char.php?id='.$char['guid'].'&amp;realm='.$realm['id'].'">'.$char['name'].'</a> -
                        <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                        <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt=""/> - '.lang('char', 'level_short').char_get_level_color($char['level']).'
                      </td>
                    </tr>';
        }
      }
      unset($realm);
    }
    else
    {
      if ( $core == 1 )
        $result = $sql['char']->query("SELECT guid, name, race, class, level, gender
          FROM characters WHERE acct='".$user_id."'");
      else
        $result = $sql['char']->query("SELECT guid, name, race, class, level, gender
          FROM characters WHERE account='".$user_id."'");

      $output .= '
                  <tr>
                    <td>'.lang('edit', 'characters').':</td>
                    <td>'.$sql['char']->num_rows($result).'</td>
                  </tr>';
      while ( $char = $sql['char']->fetch_assoc($result) )
      {
        $output .= '
                  <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\'---></td>
                    <td>
                      <a href="char.php?id='.$char['guid'].'">'.$char['name'].'</a> -
                      <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                      <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt=""/> - '.lang('char', 'level_short').char_get_level_color($char['level']).'
                    </td>
                  </tr>';
      }
    }
    unset($result);
    unset($realms);

    $override_remember_me = $_COOKIE['override_remember_me'];
    if ( !isset($override_remember_me) )
      $override_remember_me = 1;

    if ( $remember_me_checked )
      $output .= '
                  <tr>
                    <td>'.lang('edit','override').':</td>
                    <td><input type="checkbox" name="override" value="1" '.( ( $override_remember_me ) ? 'checked="checked"' : '' ).' />
                  </tr>';

    $output .= '
                  <tr>
                    <td>';
    makebutton(lang('edit', 'update'), 'javascript:do_submit_data()" type="wrn', 130);
    $output .= '
                    </td>
                    <td>';
    makebutton(lang('global', 'back'), 'javascript:window.history.back()" type="def', 130);
    $output .= '
                    </td>
                  </tr>
                </table>
              </form>
            </div>
            <br />
            <div id="edit_theme_fieldset" class="fieldset_border">
              <span class="legend">'.lang('edit', 'profile_options').'</span>
              <form action="edit.php" method="get" name="form3">
                <input type="hidden" name="action" value="profile_set" />
                <table class="hidden" id="edit_profile_table">
                  <tr>
                    <td align="left" colspan="3">'.lang('edit', 'profile_info').'</td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">
                      <textarea name="profileinfo" rows="6" cols="65">'.$screen_name['Info'].'</textarea>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">'.lang('edit', 'signature').'</td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">';
    bbcode_add_editor();
    $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">
                      <textarea id="msg" name="signature" rows="6" cols="65">'.$screen_name['Signature'].'</textarea>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td align="left" colspan="3">'.lang('edit', 'prefavatar').'</td>
                  </tr>';
    if ( $screen_name['SecurityLevel'] == 0 )
    {
      if ( $screen_name['Avatar'] == '' )
      {
        if ( $core == 1 )
          $avatar_query = "SELECT acct, name, gender, race, class, level,
            (SELECT gm FROM `".$logon_db['name']."`.accounts WHERE `".$logon_db['name']."`.accounts.acct=`".$characters_db[$realm_id]['name']."`.characters.acct) AS gmlevel,
            (SELECT login FROM `".$logon_db['name']."`.accounts WHERE `".$logon_db['name']."`.accounts.acct=`".$characters_db[$realm_id]['name']."`.characters.acct) AS login
            FROM `".$characters_db[$realm_id]['name']."`.characters
            WHERE level IN (SELECT MAX(level) FROM `".$characters_db[$realm_id]['name']."`.characters WHERE acct='".$user_id."')";
        else
          $avatar_query = "SELECT account AS acct, name, gender, race, class, level,
            (SELECT gmlevel FROM `".$logon_db['name']."`.account_access WHERE `".$logon_db['name']."`.account_access.id=`".$characters_db[$realm_id]['name']."`.characters.account) AS gmlevel,
            (SELECT username FROM `".$logon_db['name']."`.account WHERE `".$logon_db['name']."`.account.id=`".$characters_db[$realm_id]['name']."`.characters.account) AS login
            FROM `".$characters_db[$realm_id]['name']."`.characters
            WHERE level IN (SELECT MAX(level) FROM `".$characters_db[$realm_id]['name']."`.characters WHERE account='".$user_id."') AND account='".$user_id."'";

        $avatar_result = $sql['char']->query($avatar_query);
        $avatar_fields = $sql['char']->fetch_assoc($avatar_result);

        $avatar = gen_avatar_panel($avatar_fields['level'], $avatar_fields['gender'], $avatar_fields['race'], $avatar_fields['class'], 1, 0);

        $screen_name['avatarlevel'] = $avatar_fields['level'];
        $screen_name['avatarrace'] = $avatar_fields['race'];
        $screen_name['avatarclass'] = $avatar_fields['class'];
        $screen_name['avatarsex'] = $avatar_fields['gender'];
      }
      else
        $avatar = gen_avatar_panel($screen_name['avatarlevel'], $screen_name['avatarsex'], $screen_name['avatarrace'], $screen_name['avatarclass'], 1, $screen_name['SecurityLevel']);

      $output .= '
                  <tr>
                    <td id="forum_topic_avatar" rowspan="6">
                      <center>'.$avatar.'</center>
                    </td>
                    <tr>
                      <td>'.lang('edit', 'usedefault').':</td>
                      <td><input type="checkbox" name="use_default" value="1" '.( ( $screen_name['Avatar'] == '' ) ? 'checked="checked"' : '' ).' />&nbsp;'.lang('edit', 'usedefaultinfo').'</td>
                    </tr>
                    <td>'.lang('edit', 'gender').':</td>
                    <td>
                      <select name="avatargender">
                        <option value="0" '.( ( $screen_name['avatarsex'] == 0 ) ? 'selected="selected"' : '' ).' >'.lang('edit', 'male').'</option>
                        <option value="1" '.( ( $screen_name['avatarsex'] == 1 ) ? 'selected="selected"' : '' ).' >'.lang('edit', 'female').'</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'race').':</td>
                    <td>
                      <select name="avatarrace">';

      $races = array
      (
         1 => array( 1, lang('id_tab', 'human')),
         2 => array( 2, lang('id_tab', 'orc')),
         3 => array( 3, lang('id_tab', 'dwarf')),
         4 => array( 4, lang('id_tab', 'nightelf')),
         5 => array( 5, lang('id_tab', 'undead')),
         6 => array( 6, lang('id_tab', 'tauren')),
         7 => array( 7, lang('id_tab', 'gnome')),
         8 => array( 8, lang('id_tab', 'troll')),
        10 => array(10, lang('id_tab', 'bloodelf')),
        11 => array(11, lang('id_tab', 'draenei')),
      );

      foreach ( $races as $race )
      {
        $output .= '
                          <option value="'.$race[0].'" '.( ( $screen_name['avatarrace'] == $race[0] ) ? 'selected="selected"' : '' ).' >'.$race[1].'</option>';
      }

      $output .= '
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'class').':</td>
                    <td>
                      <select name="avatarclass">';

      $classes = $class_names = array
      (
         1  => array( 1, lang('id_tab', 'warrior')),
         2  => array( 2, lang('id_tab', 'paladin')),
         3  => array( 3, lang('id_tab', 'hunter')),
         4  => array( 4, lang('id_tab', 'rogue')),
         5  => array( 5, lang('id_tab', 'priest')),
         6  => array( 6, lang('id_tab', 'death_knight')),
         7  => array( 7, lang('id_tab', 'shaman')),
         8  => array( 8, lang('id_tab', 'mage')),
         9  => array( 9, lang('id_tab', 'warlock')),
         11 => array(11, lang('id_tab', 'druid')),
      );

      foreach ( $classes as $class )
      {
        $output .= '
                          <option value="'.$class[0].'" '.( ( $screen_name['avatarclass'] == $class[0] ) ? 'selected="selected"' : '' ).' >'.$class[1].'</option>';
      }

      $output .= '
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'level').':</td>
                    <td>
                      <input type="text" name="avatarlevel" value="'.$screen_name['avatarlevel'].'" />
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td>';
      makebutton(lang('edit', 'save'), 'javascript:do_submit(\'form3\', 0)', 130);
      $output .= '
                    </td>
                  </tr>';
    }
    else
    {
      $output .= '
                  <tr>
                    <td id="forum_topic_avatar" rowspan="4">
                      <center>'.gen_avatar_panel($screen_name['avatarlevel'], $screen_name['avatarsex'], $screen_name['avatarrace'], $screen_name['avatarclass'], 0, $screen_name['SecurityLevel']).'</center>
                    </td>
                    <td>'.lang('edit', 'gender').':</td>
                    <td>'.lang('edit', 'unavailable').'</td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'race').':</td>
                    <td>'.lang('edit', 'unavailable').'</td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'class').':</td>
                    <td>'.lang('edit', 'unavailable').'</td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'level').':</td>
                    <td>'.lang('edit', 'unavailable').'</td>
                  </tr>
                  <tr>
                    <td></td>
                  </tr>
                  <tr>
                    <td>';
      makebutton(lang('edit', 'save'), 'javascript:do_submit(\'form3\', 0)', 130);
      $output .= '
                    </td>
                  </tr>';
    }
    $output .= '
                </table>
              </form>
            </div>
            <br />
            <div id="edit_theme_fieldset" class="fieldset_border">
              <span class="legend">'.lang('edit', 'theme_options').'</span>
              <table class="hidden" id="edit_theme_table">
                <tr>
                  <td align="left">'.lang('edit', 'select_layout_lang').' :</td>
                  <td align="right">
                    <form action="edit.php" method="get" name="form1">
                      <input type="hidden" name="action" value="lang_set" />
                      <select name="lang">
                        <optgroup label="'.lang('edit', 'language').'">';
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
                        <option value="'.$lang[0].'"'.( ( isset($_COOKIE['lang']) && ( $_COOKIE['lang'] == $lang[0] ) ) ? ' selected="selected" ' : '' ).'>'.$lang[0].'</option>';
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
    makebutton(lang('edit', 'save'), 'javascript:do_submit(\'form1\', 0)', 130);
    $output .= '
                  </td>
                </tr>
                <tr>
                  <td align="left">'.lang('edit', 'select_theme').' :</td>
                  <td align="right">
                    <form action="edit.php" method="get" name="form2">
                      <input type="hidden" name="action" value="theme_set" />
                      <select name="theme">
                        <optgroup label="'.lang('edit', 'theme').'">';
    if ( is_dir('./themes') )
    {
      if ( $dh = opendir('./themes') )
      {
        while ( ( $file = readdir($dh) ) == true )
        {
          if ( ( $file == '.' ) || ( $file == '..' ) || ( $file == '.htaccess' ) || ( $file == 'index.html' ) || ( $file == '.svn' ) );
          else
          {
            $output .= '
                          <option value="'.$file.'"'.( ( isset($_COOKIE['theme'] ) && ( $_COOKIE['theme'] == $file) ) ? ' selected="selected" ' : '' ).'>'.$file.'</option>';
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
    makebutton(lang('edit', 'save'), 'javascript:do_submit(\'form2\',0)', 130);
    $output .= '
                  </td>
                </tr>
              </table>
            </div>
            <br />
          </center>';
  }
  else
    error(lang('global', 'err_no_records_found'));

}


//#############################################################################################################
//  DO EDIT USER
//#############################################################################################################
function doedit_user()
{
  global $output, $user_name, $logon_db, $corem_db, $sql, $core;

  if ( ( empty($_POST['pass']) || ( $_POST['pass'] == '' ) )
    && ( empty($_POST['mail']) || ( $_POST['mail'] == '' ) )
    && ( empty($_POST['expansion']) || ( $_POST['expansion'] == '' ) )
    && ( empty($_POST['referredby']) || ( $_POST['referredby'] == '' ) ) )
    redirect('edit.php?error=1');

  // password
  if ( $_POST['user_pass'] != "******" )
    if ( $core == 1 )
      $new_pass = "password='".$sql['logon']->quote_smart($_POST['pass'])."', ";
    else
      $new_pass = "sha_pass_hash='".$sql['logon']->quote_smart($_POST['pass'])."', ";

  // other
  $screenname = $sql['logon']->quote_smart(trim($_POST['screenname']));
  $new_mail = $sql['logon']->quote_smart(trim($_POST['mail']));
  $new_expansion = $sql['logon']->quote_smart(trim($_POST['expansion']));
  $referredby = $sql['logon']->quote_smart(trim($_POST['referredby']));

  // if we received a Screen Name, make sure it does not conflict with other Screen Names or with
  // the game server's login names.
  if ( $screenname )
  {
    $query = "SELECT * FROM config_accounts WHERE ScreenName='".$screenname."'";
    $sn_result = $sql['mgr']->query($query);
    $sn = $sql['mgr']->fetch_assoc($sn_result);
    if ( $sn['Login'] <> $user_name )
    {
      if ( $sql['mgr']->num_rows($sn_result) <> 0 )
        redirect('edit.php?error=6');

      if ( $core == 1 )
        $query = "SELECT * FROM accounts WHERE login='".$screenname."'";
      else
        $query = "SELECT * FROM account WHERE username='".$screenname."'";

      $sn_result = $sql['logon']->query($query);
      if ( $sql['logon']->num_rows($sn_result) <> 0 )
        redirect('edit.php?error=6');
    }
  }

  //make sure the mail is valid mail format
  require_once 'libs/valid_lib.php';
  if ( ( valid_email($new_mail) ) && ( strlen($new_mail) < 225 ) )
    ;
  else
    redirect('edit.php?error=2');

  // set screen name
  if ( $screenname )
    $sql['mgr']->query("INSERT INTO config_accounts (Login, ScreenName) VALUES ('".$user_name."', '".$screenname."')");
    
  // Overriding Remember Me is done via a cookie
  // usage is backward from the name
  // 1 = show check box
  // 0 = hide
  if ( !isset($_POST['override']) )
    $override = 0;
  else
    $override = 1;

  if ( ( $override != $_COOKIE['override_remember_me'] ) || ( !isset($_COOKIE['override_remember_me']) ) )
  {
    if ( $override )
      setcookie('override_remember_me', '1', time()+60*60*24*30);
    else
      setcookie('override_remember_me', '0', time()+60*60*24*30);

    $other_changes = 1;
  }

  // change other settings
  if ( $core == 1 )
    $query = "UPDATE accounts SET email='".$new_mail."', ".$new_pass." flags='".$new_expansion."' WHERE login='".$user_name."'";
  else
    $query = "UPDATE account SET email='".$new_mail."', ".$new_pass." expansion='".$new_expansion."' WHERE username='".$user_name."'";

  $sql['logon']->query($query);

  if ( doupdate_referral($referredby) || $sql['logon']->affected_rows() || $sql['mgr']->affected_rows() || $other_changes )
    redirect('edit.php?error=3');
  else
    redirect('edit.php?error=4');

}

function doupdate_referral($referredby)
{
  global $corem_db, $logon_db, $user_id, $sql;

  if ( NULL == $sql['mgr']->result($sql['mgr']->query("SELECT InvitedBy FROM point_system_invites WHERE PlayersAccount='".$user_id."'"), 0))
  {
    $referred_by = $sql['char']->result($sql['char']->query("SELECT guid FROM characters WHERE name='".$referredby."'"), 0);

    if ( $referred_by == NULL )
      ;
    else
    {
      $char = $sql['char']->result($sql['char']->query("SELECT acct FROM characters WHERE guid='".$referred_by."'"), 0, 'account');
      $result = $sql['logon']->result($sql['logon']->query("SELECT acct FROM accounts WHERE acct='".$char."'"), 0, 'id');
      if ( $result == $user_id );
      else
      {
        $sql['mgr']->query("INSERT INTO point_system_invites (PlayersAccount, InvitedBy, InviterAccount) VALUES ('".$user_id."', '".$referred_by."', '".$result."')");
        return true;
      }
    }

  }
  return false;
}


//###############################################################################################################
// SET DEFAULT INTERFACE LANGUAGE
//###############################################################################################################
function lang_set()
{
  if ( empty($_GET['lang']) )
    redirect('edit.php?error=1');
  else
    $lang = addslashes($_GET['lang']);

  if ( $lang )
  {
    setcookie('lang', $lang, time()+60*60*24*30*6); //six month
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
  if ( empty($_GET['theme']) )
    redirect('edit.php?error=1');
  else
    $tmpl = addslashes($_GET['theme']);

  if ( $tmpl )
  {
    setcookie('theme', $tmpl, time()+3600*24*30*6); //six month
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

  $info = $sql['logon']->quote_smart($_GET['profileinfo']);
  $signature = $sql['logon']->quote_smart($_GET['signature']);
  $gender = $sql['logon']->quote_smart($_GET['avatargender']);
  $race = $sql['logon']->quote_smart($_GET['avatarrace']);
  $class = $sql['logon']->quote_smart($_GET['avatarclass']);
  $level = $sql['logon']->quote_smart($_GET['avatarlevel']);

  // gm's can't change their avatars
  if ( $user_lvl == 0 )
  {
    if ( !is_numeric($level) && ( ( $level < 1 ) || ( $level > 80 ) ) )
      redirect('edit.php?error=7');

    if ( $_GET['use_default'] )
      $avatar = '';
    else
      $avatar = $gender.' '.$race.' '.$class.' '.$level;
  }

  $query = "UPDATE config_accounts SET Avatar='".$avatar."', Info='".$info."', Signature='".$signature."' WHERE Login='".$user_name."'";

  $sql['mgr']->query($query);

  if ( $sql['mgr']->affected_rows() )
    redirect('edit.php?error=3');
  else
    redirect('edit.php?error=4');
}


//###############################################################################################################
// MAIN
//###############################################################################################################
$err = ( ( isset($_GET['error']) ) ? $_GET['error'] : NULL );

$output .= '
      <div class="bubble">
          <div class="top">';

if ( $err == 1 )
  $output .= '
            <h1><font class="error">'.lang('global', 'empty_fields').'</font></h1>';
elseif ( $err == 2 )
  $output .= '
            <h1><font class="error">'.lang('edit', 'use_valid_email').'</font></h1>';
elseif ( $err == 3 )
  $output .= '
            <h1><font class="error">'.lang('edit', 'data_updated').'</font></h1>';
elseif ( $err == 4 )
  $output .= '
            <h1><font class="error">'.lang('edit', 'error_updating').'</font></h1>';
elseif ( $err == 5 )
  $output .= '
            <h1><font class="error">'.lang('edit', 'del_error').'</font></h1>';
elseif ( $err == 6 )
  $output .= '
            <h1><font class="error">'.lang('edit', 'sn_error').'</font></h1>';
elseif ( $err == 7 )
  $output .= '
            <h1><font class="error">'.lang('edit', 'use_valid_level').'</font></h1>';
else
  $output .= '
            <h1>'.lang('edit', 'edit_your_acc').'</h1>';

unset($err);

$output .= '
          </div>';

$action = ( ( isset($_GET['action']) ) ? $_GET['action'] : NULL );

if ( $action == 'doedit_user' )
  doedit_user();
elseif ( $action == 'lang_set' )
  lang_set();
elseif ( $action == 'theme_set' )
  theme_set();
elseif ( $action == 'profile_set' )
  profile_set();
else
  edit_user();

unset($action);
unset($action_permission);

require_once 'footer.php';

?>
