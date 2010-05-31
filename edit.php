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

//##############################################################################################################
// EDIT USER
//##############################################################################################################
function edit_user()
{
  global $output, $arcm_db, $logon_db, $characters_db, $arcm_db, $realm_id,
    $user_name, $user_id, $expansion_select, $server, $developer_test_mode, $multi_realm_mode,
    $sqlm, $sqll, $sqlc, $core;

  $refguid = $sqlm->result($sqlm->query('SELECT InvitedBy FROM point_system_invites WHERE PlayersAccount = \''.$user_id.'\''), 0, 'InvitedBy');
  $referred_by = $sqlc->result($sqlc->query('SELECT name FROM characters WHERE guid = \''.$refguid.'\''), 0, 'name');
  unset($refguid);
  
  if ( $core == 1 )
    $query = "SELECT email, flags, lastip FROM accounts WHERE login = '".$user_name."'";
  else
    $query = "SELECT email, expansion AS flags, last_ip AS lastip FROM account WHERE username = '".$user_name."'";

  if ($acc = $sqll->fetch_assoc($sqll->query($query)))
  {
    // if we have a screen name, we need to use it
    $screen_name_query = "SELECT * FROM config_accounts WHERE Login = '".$user_name."'";
    $screen_name = $sqlm->query($screen_name_query);
    $screen_name = $sqlm->fetch_assoc($screen_name);
    $output .= '
          <center>
            <script type="text/javascript" src="libs/js/sha1.js"></script>
            <script type="text/javascript">
              // <![CDATA[
                function do_submit_data ()
                {
                  //document.form.pass.value = hex_sha1(\''.strtoupper($user_name).':\'+document.form.user_pass.value.toUpperCase());
                  document.form.pass.value = document.form.user_pass.value;//.toUpperCase();
                  document.form.user_pass.value = \'0\';
                  do_submit();
                }
              // ]]>
            </script>
            <fieldset id="edit_fieldset">
              <legend>'.lang('edit', 'edit_acc').'</legend>
              <form method="post" action="edit.php?action=doedit_user" name="form">
                <input type="hidden" name="pass" value="" maxlength="256" />
                <table class="flat">
                  <tr>
                    <td>'.lang('edit', 'id').'</td>
                    <td>'.$user_id.'</td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'username').'</td>
                    <td>'.$user_name.'</td>
                  </tr>';
    if (!$screen_name['ScreenName'])
    {
      $output .= '
                  <tr>
                    <td>'.lang('edit', 'screenname').'</td>
                    <td><input type="text" name="screenname" size="42" maxlength="14" /></td>
                  </tr>';
    }
    else
    {
      $output .= '
                  <tr>
                    <td>'.lang('edit', 'screenname').'</td>
                    <td>'.$screen_name['ScreenName'].'</td>
                  </tr>';
    }
    $output .= '
                  <tr>
                    <td>'.lang('edit', 'password').'</td>
                    <td><input type="text" name="user_pass" size="42" maxlength="40" value="******" /></td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'mail').'</td>
                    <td><input type="text" name="mail" size="42" maxlength="225" value="'.$acc['email'].'" /></td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'invited_by').':</td>
                    <td>';
    if ($referred_by == NULL)
      $output .= '
                      <input type="text" name="referredby" size="42" maxlength="12" value="'.$referred_by.'" />';
    else
      $output .= '
                    '.$referred_by.'';
    $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'gm_level').'</td>
                    <td>'.id_get_gm_level($screen_name['SecurityLevel']).' ( '.$screen_name['SecurityLevel'].' )</td>
                  </tr>
                  <tr>
                    <td>'.lang('edit', 'last_ip').'</td>
                    <td>'.$acc['lastip'].'</td>
                  </tr>';
    if ($expansion_select)
    {
      if ( $core == 1 )
      {
        $output .= '
                     <tr>
                      <td >'.lang('edit', 'client_type').':</td>
                      <td>
                        <select name="expansion">
                          <option value="24" ';
        if($acc['flags'] == 24) $output .= 'selected="selected"';
        $output .= '>'.lang('edit', 'wotlktbc').'</option>
                          <option value="16" ';
        if($acc['flags'] == 16) $output .= 'selected="selected"';
        $output .= '>'.lang('edit', 'wotlk').'</option>
                          <option value="8" ';
        if($acc['flags'] == 8) $output .= 'selected="selected"';
        $output .= '>'.lang('edit', 'tbc').'</option>
                          <option value="0" ';
        if($acc['flags'] == 0) $output .= 'selected="selected"';
        $output .= '>'.lang('edit', 'classic').'</option>
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
                          <option value="2" ';
        if($acc['flags'] == 2) $output .= 'selected="selected"';
        $output .= '>'.lang('edit', 'wotlktbc').'</option>
                          <option value="1" ';
        if($acc['flags'] == 1) $output .= 'selected="selected"';
        $output .= '>'.lang('edit', 'tbc').'</option>
                          <option value="0" ';
        if($acc['flags'] == 0) $output .= 'selected="selected"';
        $output .= '>'.lang('edit', 'classic').'</option>
                        </select>
                      </td>
                    </tr>';
      }
    }
    
    foreach ( $characters_db as $db )
    {
      $sql = new SQL;
      $sql->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
      
      if ( $core == 1 )
        $query = "SELECT COUNT(*) FROM characters WHERE acct='".$user_id."'";
      else
        $query = "SELECT COUNT(*) FROM characters WHERE account='".$user_id."'";
      $result = $sql->query($query);
      $fields = $sql->fetch_assoc($result);
      
      $c_count += $fields['COUNT(*)'];
    }
    
    $output .= '
                  <tr>
                    <td>'.lang('edit', 'tot_chars').'</td>
                    <td>'.$c_count.'</td>
                  </tr>';
                  
    $realms = $sqlm->query('SELECT id, name FROM realmlist');
    if ( 1 < $sqlm->num_rows($realms) && (1 < count($server)) && (1 < count($characters_db)) )
    {
      while ($realm = $sqlm->fetch_assoc($realms))
      {
        $sqlc->connect($characters_db[$realm['id']]['addr'], $characters_db[$realm['id']]['user'], $characters_db[$realm['id']]['pass'], $characters_db[$realm['id']]['name']);
        if ( $core == 1 )
          $result = $sqlc->query('SELECT guid, name, race, class, level, gender
            FROM characters WHERE acct = '.$user_id.'');
        else
          $result = $sqlc->query('SELECT guid, name, race, class, level, gender
            FROM characters WHERE account = '.$user_id.'');

        $output .= '
                    <tr>
                      <td>'.lang('edit', 'characters').' '.$realm['name'].'</td>
                      <td>'.$sqlc->num_rows($result).'</td>
                    </tr>';

        while ($char = $sqlc->fetch_assoc($result))
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
        $result = $sqlc->query('SELECT guid, name, race, class, level, gender
          FROM characters WHERE acct = '.$user_id.'');
      else
        $result = $sqlc->query('SELECT guid, name, race, class, level, gender
          FROM characters WHERE account = '.$user_id.'');

      $output .= '
                  <tr>
                    <td>'.lang('edit', 'characters').'</td>
                    <td>'.$sqlc->num_rows($result).'</td>
                  </tr>';
      while ($char = $sqlc->fetch_assoc($result))
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
            </fieldset>
            <br />
            <fieldset id="edit_theme_fieldset">
              <legend>'.lang('edit', 'theme_options').'</legend>
              <table class="hidden" id="edit_theme_table">
                <tr>
                  <td align="left">'.lang('edit', 'select_layout_lang').' :</td>
                  <td align="right">
                    <form action="edit.php" method="get" name="form1">
                      <input type="hidden" name="action" value="lang_set" />
                      <select name="lang">
                        <optgroup label="'.lang('edit', 'language').'">';
    if (is_dir('./lang'))
    {
      if ($dh = opendir('./lang'))
      {
        while (($file = readdir($dh)) == true)
        {
          $lang = explode('.', $file);
          if(isset($lang[1]) && $lang[1] == 'php')
          {
            $output .= '
                        <option value="'.$lang[0].'"';
            if (isset($_COOKIE['lang']) && ($_COOKIE['lang'] == $lang[0]))
              $output .= ' selected="selected" ';
            $output .= '>'.$lang[0].'</option>';
          }
        }
        closedir($dh);
      }
    }
    $output .= '
                        </optgroup>
                      </select>&nbsp;&nbsp;&nbsp;&nbsp;
                    </form>
                  </td>
                  <td>';
                    makebutton(lang('edit', 'save'), 'javascript:do_submit(\'form1\',0)', 130);
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
    if (is_dir('./themes'))
    {
      if ($dh = opendir('./themes'))
      {
        while (($file = readdir($dh)) == true)
        {
          if (($file == '.') || ($file == '..') || ($file == '.htaccess') || ($file == 'index.html') || ($file == '.svn'));
          else
          {
            $output .= '
                          <option value="'.$file.'"';
            if (isset($_COOKIE['theme']) && ($_COOKIE['theme'] == $file))
              $output .= ' selected="selected" ';
            $output .= '>'.$file.'</option>';
          }
        }
        closedir($dh);
      }
    }
    $output .= '
                        </optgroup>
                      </select>&nbsp;&nbsp;&nbsp;&nbsp;
                    </form>
                  </td>
                  <td>';
                    makebutton(lang('edit', 'save'), 'javascript:do_submit(\'form2\',0)', 130);
    $output .= '
                  </td>
                </tr>
              </table>
            </fieldset>
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
  global $output, $user_name, $logon_db, $arcm_db, $sqlm, $sqll, $sqlc;

  if ( (empty($_POST['pass'])||($_POST['pass'] == ''))
    && (empty($_POST['mail'])||($_POST['mail'] == ''))
    && (empty($_POST['expansion'])||($_POST['expansion'] == ''))
    && (empty($_POST['referredby'])||($_POST['referredby'] == '')) )
    redirect('edit.php?error=1');

  //$new_pass = ($sqll->quote_smart($_POST['pass']) == sha1(strtoupper($user_name).':******')) ? '' : 'sha_pass_hash=\''.$sqll->quote_smart($_POST['pass']).'\', ';
  if ($_POST['pass'] <> "******")
    $new_pass = "password = '".$sqll->quote_smart($_POST['pass'])."',";
  $screenname = $sqll->quote_smart(trim($_POST['screenname']));
  $new_mail = $sqll->quote_smart(trim($_POST['mail']));
  $new_expansion = $sqll->quote_smart(trim($_POST['expansion']));
  $referredby = $sqll->quote_smart(trim($_POST['referredby']));

  // if we received a Screen Name, make sure it does not conflict with other Screen Names or with
  // ArcEmu login names.
  if ($screenname)
  {
    $query = "SELECT * FROM config_accounts WHERE ScreenName = '".$screenname."'";
    $sn_result = $sqlm->query($query);
    $sn = $sqlm->fetch_assoc($sn_result);
    if ($sn['Login'] <> $user_name)
    {
      if ($sqlm->num_rows($sn_result) <> 0)
        redirect('edit.php?error=6');
      $query = "SELECT * FROM accounts WHERE login = '".$screenname."'";
      $sn_result = $sqll->query($query);
      if ($sqll->num_rows($sn_result) <> 0)
        redirect('edit.php?error=6');
    }
  }

  //make sure the mail is valid mail format
  require_once 'libs/valid_lib.php';
  if ((valid_email($new_mail)) && (strlen($new_mail) < 225));
  else
    redirect('edit.php?error=2');

  // set screen name
  if ($screenname)
    $sqlm->query("INSERT INTO config_accounts (Login, ScreenName) VALUES ('".$user_name."', '".$screenname."')");

  // change other settings
  $query = "UPDATE accounts SET email = '".$new_mail."', ".$new_pass." flags = '".$new_expansion."' WHERE login = '".$user_name."'";
  $sqll->query($query);
  if (doupdate_referral($referredby) || $sqlm->affected_rows())
    redirect('edit.php?error=3');
  else
    redirect('edit.php?error=4');

}

function doupdate_referral($referredby)
{
  global $arcm_db, $logon_db, $user_id, $sqlm, $sqll, $sqlc;

  if (NULL == $sqlm->result($sqlm->query('SELECT InvitedBy FROM point_system_invites WHERE PlayersAccount = \''.$user_id.'\''), 0))
  {
    $referred_by = $sqlc->result($sqlc->query('SELECT guid FROM characters WHERE name = \''.$referredby.'\''), 0);

    if ($referred_by == NULL);
    else
    {
      $char = $sqlc->result($sqlc->query('SELECT acct FROM characters WHERE guid = \''.$referred_by.'\''), 0, 'account');
      $result = $sqll->result($sqll->query('SELECT acct FROM accounts WHERE acct = \''.$char.'\''), 0, 'id');
      if ($result == $user_id);
      else
      {
        $sqlm->query('INSERT INTO point_system_invites (PlayersAccount, InvitedBy, InviterAccount) VALUES (\''.$user_id.'\', \''.$referred_by.'\', \''.$result.'\')');
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
  if (empty($_GET['lang']))
    redirect('edit.php?error=1');
  else
    $lang = addslashes($_GET['lang']);

  if ($lang)
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
  if (empty($_GET['theme']))
    redirect('edit.php?error=1');
  else
    $tmpl = addslashes($_GET['theme']);

  if ($tmpl)
  {
    setcookie('theme', $tmpl, time()+3600*24*30*6); //six month
    redirect('edit.php');
  }
  else
    redirect('edit.php?error=1');
}


//###############################################################################################################
// MAIN
//###############################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
      <div class="bubble">
          <div class="top">';

//$lang_edit = lang_edit();

if(1 ==  $err)
  $output .= '
            <h1><font class="error\">'.lang('global', 'empty_fields').'</font></h1>';
else if(2 == $err)
  $output .= '
            <h1><font class="error\">'.lang('edit', 'use_valid_email').'</font></h1>';
else if(3 == $err)
  $output .= '
            <h1><font class="error\">'.lang('edit', 'data_updated').'</font></h1>';
else if(4 == $err)
  $output .= '
            <h1><font class="error\">'.lang('edit', 'error_updating').'</font></h1>';
else if(5 == $err)
  $output .= '
            <h1><font class="error\">'.lang('edit', 'del_error').'</font></h1>';
else if(6 == $err)
  $output .= '
            <h1><font class="error\">'.lang('edit', 'sn_error').'</font></h1>';
else
  $output .= '
            <h1>'.lang('edit', 'edit_your_acc').'</h1>';

unset($err);

$output .= '
          </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('doedit_user' == $action)
  doedit_user();
else if('lang_set' == $action)
  lang_set();
else if('theme_set' == $action)
  theme_set();
else
  edit_user();

unset($action);
unset($action_permission);
//unset($lang_edit);

require_once 'footer.php';


?>
