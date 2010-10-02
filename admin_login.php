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


// resuming login session if available, or start new one
if (ini_get('session.auto_start'));
else session_start();

require_once("configs/config.php");
require_once("libs/config_lib.php");

if (isset($_COOKIE['lang']))
{
  $lang = $_COOKIE['lang'];
  if (file_exists('../lang/'.$lang.'.php'));
  else
    $lang = $language;
}
else
  $lang = $language;

require_once 'lang/'.$lang.'.php';
require_once 'libs/lang_lib.php';
require_once 'libs/global_lib.php';


$output = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>'.lang('admin', 'title').'</title>
    <meta http-equiv="Content-Type" content="text/html; charset='.$site_encoding.'" />
    <meta http-equiv="Content-Type" content="text/javascript; charset='.$site_encoding.'" />
    <link rel="stylesheet" type="text/css" href="admin/admin.css" />
    <link rel="SHORTCUT ICON" href="img/favicon.ico" />
    <script type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="libs/js/general.js"></script>
  </head>

  <body>';

//#############################################################################
// Login
//#############################################################################
function dologin()
{
  global $corem_db, $logon_db, $core;

  $sql['logon'] = new SQL;
  $sql['logon']->connect($logon_db['addr'], $logon_db['user'], $logon_db['pass'], $logon_db['name']);

  $sql['mgr'] = new SQL;
  $sql['mgr']->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  if ( empty($_POST['login']) || empty($_POST['password']) )
    redirect('admin_login.php?error=2');

  $user_name  = $sql['mgr']->quote_smart($_POST['login']);
  $user_pass  = $sql['mgr']->quote_smart($_POST['password']);

  if ( ( strlen($user_name) > 255 ) || ( strlen($user_pass) > 255 ) )
    redirect('admin_login.php?error=1');

  // Users may log in using either their username or screen name
  // check for matching login
  if ( $core == 1 )
    $query = "SELECT * FROM accounts WHERE login='".$user_name."' AND password='".$user_pass."'";
  else
  {
    $pass_hash = sha1(strtoupper($user_name.":".$user_pass));
    $query = "SELECT * FROM account WHERE username='".$user_name."' AND sha_pass_hash='".$pass_hash."'";
  }

  $name_result = $sql['logon']->query($query);
  if ( !$sql['logon']->num_rows($name_result) )
  {
    // if we didn't find one, check for matching screen name
    $query = "SELECT * FROM config_accounts WHERE ScreenName='".$user_name."'";
    $name_result = $sql['mgr']->query($query);
    if ( $sql['mgr']->num_rows($name_result) )
    {
      $name = $sql['mgr']->fetch_assoc($name_result);
      $user_name = $name['Login'];
    }
  }
  else
  {
    // we'll still need the screen name if we have one
    $query = "SELECT * FROM config_accounts WHERE Login = '".$user_name."'";
    $name_result = $sql['mgr']->query($query);
    $name = $sql['mgr']->fetch_assoc($name_result);
  }
  // if we didn't find the name given for either entries, then the name will come up bad below

  if ( $core == 1 )
    $query = "SELECT * FROM accounts WHERE login='".$user_name."' AND password='".$user_pass."'";
  else
  {
    $pass_hash = sha1(strtoupper($user_name.":".$user_pass));
    $query = "SELECT * FROM account WHERE username='".$user_name."' AND sha_pass_hash='".$pass_hash."'";
  }

  $result = $sql['logon']->query($query);
  $s_result = $sql['mgr']->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$user_name."'");
  $temp = $sql['mgr']->fetch_assoc($s_result);
  $_SESSION['gm_lvl'] = $temp['gm'];

  if ( $sql['logon']->num_rows($result) == 1 )
  {
    if ( $core == 1 )
      $acct = $sql['logon']->result($result, 0, 'acct');
    else
      $acct = $sql['logon']->result($result, 0, 'id');

    if ( $core == 1 )
      $ban_query = "SELECT banned FROM accounts WHERE login='".$user_name."' AND password='".$user_pass."'";
    else
      $ban_query = "SELECT COUNT(*) FROM account_banned WHERE id='".$acct."' AND active=1";

    if ($sql['logon']->result($sql['logon']->query($ban_query), 0))
    {
      redirect('admin_login.php?error=3');
    }
    else
    {
      $_SESSION['user_id'] = $acct;
      if ( $core == 1 )
        $_SESSION['login'] = $sql['logon']->result($result, 0, 'login');
      else
        $_SESSION['login'] = $sql['logon']->result($result, 0, 'username');
      // if we got a screen name, we'll want it later.
      $_SESSION['screenname'] = $name['ScreenName'];
      //gets our numerical level based on Security Level.
      $_SESSION['user_lvl'] = gmlevel($temp['gm']);
      $_SESSION['realm_id'] = $sql['logon']->quote_smart($_POST['realm']);
      $_SESSION['client_ip'] = ( ( isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR') );
      $_SESSION['logged_in'] = true;

      redirect('admin.php');
    }
  }
  else
  {
    redirect('admin_login.php?error=1');
  }
}


//#################################################################################################
// Print login form
//#################################################################################################
function login()
{
  global $output, $characters_db, $server, $sql, $core;

  $output .= '
          <center>
            <script type="text/javascript" src="libs/js/sha1.js"></script>
            <script type="text/javascript">
              // <![CDATA[
                function dologin ()
                {
                  document.form.password.value = document.form.login_pass.value;
                  do_submit();
                }
              // ]]>
            </script>
            <fieldset class="half_frame">
              <legend>'.lang('login', 'login').'</legend>
              <form method="post" action="admin_login.php?action=dologin" name="form" onsubmit="return dologin()">
                <input type="hidden" name="password" value="" maxlength="256" />
                <table class="hidden" id="login_table">
                  <tr>
                    <td colspan="3">
                      <hr />
                    </td>
                  </tr>
                  <tr>
                    <td align="right">'.lang('login', 'username').' :</td>
										<td>&nbsp;</td>
                    <td align="left">
                      <input type="text" name="login" size="24" maxlength="16" />
                    </td>
                  </tr>
                  <tr>
                    <td align="right">'.lang('login', 'password').' :</td>
										<td>&nbsp;</td>
                    <td align="left">
                      <input type="password" name="login_pass" size="24" maxlength="40" />
                    </td>
                  </tr>
                  <input type="hidden" name="realm" value="1" />
                  <tr align="right">
                    <td colspan="3">
                      <input type="submit" value="" style="display:none" />';
  $output .= '
                      <div>
                        <a class="button" style="width:130px;" href="javascript:dologin()" type="def">'.lang('login', 'login').'</a>
                      </div>';
  $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3">
                      <hr />
                    </td>
                  </tr>
                </table>
                <script type="text/javascript">
                  // <![CDATA[
                    document.form.user.focus();
                  // ]]>
                </script>
              </form>
            </fieldset>
          </center>';
}


//#################################################################################################
// MAIN
//#################################################################################################
$err = ( ( isset($_GET['error']) ) ? $_GET['error'] : NULL );
$info = ( ( isset($_GET['info']) ) ? $_GET['info'] : NULL );

$output .= '
      <div class="bubble">
        <div class="top">';
$output .= "
          <center>
            <h1>".lang('admin', 'title')."</h1>
            <br />";

if ( $err == 1 )
  $output .=  '
            <h1><font class="error">'.lang('login', 'bad_pass_user').'</font></h1>';
elseif ( $err == 2 )
  $output .=  '
            <h1><font class="error">'.lang('login', 'missing_pass_user').'</font></h1>';
elseif ( $err == 3 )
  $output .=  '
            <h1><font class="error">'.lang('login', 'banned_acc').'</font></h1>';
elseif ( $err == 5 )
{
  $output .=  '
            <h1><font class="error">'.lang('login', 'no_permision').'</font></h1>';
  if ( isset($info) )
    $output .= '<h1><font class="error">'.lang('login', 'req_permision').': '.$info.'</font></h1>';
}
else
  $output .=  '
            <h1>'.lang('login', 'enter_valid_logon').'</h1>';

unset($err);

$output .= '
          </center>
        </div>';

$action = ( ( isset($_GET['action']) ) ? $_GET['action'] : NULL );

if ( $action == 'dologin' )
  dologin();
else
  login();

unset($action);

$output .= '
    </div><!-- bubble -->
  </body>';
echo $output;

?>