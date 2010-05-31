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

//#############################################################################
// Login
//#############################################################################
function dologin()
{
  global $arcm_db, $sqlm, $sqll, $core;

  if (empty($_POST['login']) || empty($_POST['password']))
    redirect('login.php?error=2');

  $user_name  = $sqlm->quote_smart($_POST['login']);
  $user_pass  = $sqlm->quote_smart($_POST['password']);

  if (255 < strlen($user_name) || 255 < strlen($user_pass))
    redirect('login.php?error=1');

  // Users may log in using either their username or screen name
  // check for matching login
  if ( $core == 1 )
    $query = "SELECT * FROM accounts WHERE login = '".$user_name."' AND password = '".$user_pass."'";
  else
    $query = "SELECT * FROM account WHERE username = '".$user_name."' AND sha_pass_hash = '".$user_pass."'";

  $name_result = $sqll->query($query);
  if (!$sqll->num_rows($name_result))
  {
    // if we didn't find one, check for matching screen name
    $query = "SELECT * FROM config_accounts WHERE ScreenName = '".$user_name."'";
    $name_result = $sqlm->query($query);
    if ($sqlm->num_rows($name_result))
    {
      $name = $sqlm->fetch_assoc($name_result);
      $user_name = $name['Login'];
    }
  }
  else
  {
    // we'll still need the screen name if we have one
    $query = "SELECT * FROM config_accounts WHERE Login = '".$user_name."'";
    $name_result = $sqlm->query($query);
    $name = $sqlm->fetch_assoc($name_result);
  }
  // if we didn't find the name given for either entries, then the name will come up bad below

  if ( $core == 1 )
    $query = "SELECT * FROM accounts WHERE login = '".$user_name."'";
  else
    $query = "SELECT * FROM account WHERE username = '".$user_name."'";

  $result = $sqll->query($query);
  $s_result = $sqlm->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE login = '".$user_name."'");
  $temp = $sqlm->fetch_assoc($s_result);
  $_SESSION['gm_lvl'] = $temp['gm'];

  //we need this later
  //unset($user_name);

  if ( $sqll->num_rows($result) == 1 )
  {
    if ( $core == 1 )
      $acct = $sqll->result($result, 0, 'acct');
    else
      $acct = $sqll->result($result, 0, 'id');

    if ( $core == 1 )
      $ban_query = "SELECT banned FROM accounts WHERE login = '".$user_name."' AND password = '".$user_pass."'";
    else
      $ban_query = "SELECT COUNT(*) FROM account_banned WHERE id = '".$acct."' AND active = 1";

    if ($sqll->result($sqll->query($ban_query), 0))
    {
      redirect('login.php?error=3');
    }
    else
    {
      $_SESSION['user_id']   = $acct;
      if ( $core == 1 )
        $_SESSION['login']     = $sqll->result($result, 0, 'login');
      else
        $_SESSION['login']     = $sqll->result($result, 0, 'username');
      // if we got a screen name, we'll want it later.
      $_SESSION['screenname']     = $name['ScreenName'];
      //gets our numerical level based on ArcEmu level.
      $_SESSION['user_lvl']  = gmlevel($temp['gm']);
      $_SESSION['realm_id']  = $sqll->quote_smart($_POST['realm']);
      $_SESSION['client_ip'] = (isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
      $_SESSION['logged_in'] = true;

      if (isset($_POST['remember']) && $_POST['remember'] != '')
      {
        setcookie(   'login', $_SESSION['login'], time()+60*60*24*30);
        setcookie('realm_id', $_SESSION['realm_id'], time()+60*60*24*30);
        setcookie(  'password', $user_pass, time()+60*60*24*30);
      }
      redirect('index.php');
    }
  }
  else
  {
    redirect('login.php?error=1');
  }
}


//#################################################################################################
// Print login form
//#################################################################################################
function login()
{
  global $output, $lang_login, $characters_db, $server, $remember_me_checked, $sqlm, $core;

  $output .= '
          <center>
            <script type="text/javascript" src="libs/js/sha1.js"></script>
            <script type="text/javascript">
              // <![CDATA[
                function dologin ()
                {';
  if ( $core == 1 )
    $output .= '
                  document.form.password.value = document.form.login_pass.value;';
  else
    $output .= '
                  document.form.password.value = hex_sha1(document.form.login.value.toUpperCase()+":"+document.form.login_pass.value.toUpperCase());
                  document.form.login_pass.value = "0";';
  $output .= '
                  do_submit();
                }
              // ]]>
            </script>
            <fieldset class="half_frame">
              <legend>'.lang('login', 'login').'</legend>
              <form method="post" action="login.php?action=dologin" name="form" onsubmit="return dologin()">
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
                  </tr>';

  $result = $sqlm->query('SELECT id, name FROM realmlist LIMIT 10');

  if ($sqlm->num_rows($result) > 1 && (count($server) > 1) && (count($characters_db) > 1))
  {
    $output .= '
                  <tr align="right">
                    <td>'.lang('login', 'select_realm').' :</td>
										<td>&nbsp;</td>
                    <td align="left">
                      <select name="realm" id="login_realm">';
    while ($realm = $sqlm->fetch_assoc($result))
      if(isset($server[$realm['id']]))
        $output .= '
                        <option value="'.$realm['id'].'" '.( $_SESSION['realm_id'] == $realm['id'] ? 'selected="selected"' : '' ).'>'.htmlentities($realm['name']).'</option>';
    $output .= '
                      </select>
                    </td>
                  </tr>';
  }
  else
    $output .= '
                  <!-- input type="hidden" name="realm" value="1" / -->
                  <input type="hidden" name="realm" value="'.$sqlm->result($result, 0, 'id').'" />';
  $output .= '
                  <!-- tr>
                    <td>
                    </td>
                  </tr -->
                  <tr>
                    <td align="right">'.lang('login', 'remember_me').' : </td>
										<td>&nbsp;</td>
                    <td align="left"><input type="checkbox" name="remember" value="1"';
  if ($remember_me_checked)
    $output .= ' checked="checked"';
  // this_is_junk: the hardcoded CSS here is for an input doing nothing.
  $output .= ' /></td>
                  </tr>
                  <tr>
                    <td>
                    </td>
                  </tr>
                  <tr align="right">
                    <td colspan="3">
                      <input type="submit" value="" style="display:none" />';
                        makebutton(lang('login', 'not_registrated'), 'register.php" type="wrn', 130);
                        makebutton(lang('login', 'login'), 'javascript:dologin()" type="def', 130);
  $output .= '
                    </td>
                  </tr>
                  <tr align="center">
                    <td colspan="3"><a href="register.php?action=pass_recovery">'.lang('login', 'pass_recovery').'</a></td>
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
              <br />
            </fieldset>
            <br /><br />
          </center>';
}


//#################################################################################################
// Login via set cookie
//#################################################################################################
function do_cookie_login()
{
  global $arcm_db, $sqlm, $sqll, $core;
  
  if (empty($_COOKIE['login']) || empty($_COOKIE['password']) || empty($_COOKIE['realm_id']))
    redirect('login.php?error=2');

  $user_name = $sqll->quote_smart($_COOKIE['login']);
  $user_pass = $sqll->quote_smart($_COOKIE['password']);

  // Users may log in using either their username or screen name
  // check for matching login
  if ( $core == 1 )
    $query = "SELECT * FROM accounts WHERE login = '".$user_name."'";
  else
    $query = "SELECT * FROM account WHERE username = '".$user_name."'";
  $name_result = $sqll->query($query);
  if (!$sqll->num_rows($name_result))
  {
    // if we didn't find one, check for matching screen name
    $query = "SELECT * FROM config_accounts WHERE ScreenName = '".$user_name."'";
    $name_result = $sqlm->query($query);
    if ($sqlm->num_rows($name_result))
    {
      $name = $sqlm->fetch_assoc($name_result);
      $user_name = $name['Login'];
    }
  }
  else
  {
    // we'll still need the screen name if we have one
    $query = "SELECT * FROM config_accounts WHERE Login = '".$user_name."'";
    $name_result = $sqlm->query($query);
    $name = $sqlm->fetch_assoc($name_result);
  }
  // if we didn't find the name given for either entries, then the name will come up bad below
  
  if ( $core == 1 )
    $query = "SELECT * FROM accounts WHERE login = '".$user_name."'";
  else
    $query = "SELECT *, username AS login FROM account WHERE username = '".$user_name."'";

  $result = $sqll->query($query);

  $s_result = $sqlm->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE login = '".$user_name."'");
  $temp = $sqlm->fetch_assoc($s_result);
  $_SESSION['gm_lvl'] = $temp['gm'];

  /*$result = $sqll->query('SELECT login, gm, acct FROM accounts WHERE login = \''.$user_name.'\' AND password = \''.$user_pass.'\'');
  $temp = $sqll->fetch_assoc($result);
  //store the ArcEmu value for other functions
  $_SESSION['gm_lvl'] = $temp['gm'];*/


  //we need these later
  //unset($user_name);
  //unset($user_pass);

  if ($sqll->num_rows($result))
  {
    if ( $core == 1)
      $acct = $sqll->result($result, 0, 'acct');
    else
      $acct = $sqll->result($result, 0, 'id');
    
    if ( $core == 1 )
      $ban_query = "SELECT COUNT(*) FROM accounts WHERE acct='".$acct."' AND banned='1'";
    else
      $ban_query = "SELECT COUNT(*) FROM account_banned WHERE id = '".$acct."' AND active = 1";
    if ($sqll->result($sqll->query($ban_query), 0))
    {
      redirect('login.php?error=3');
    }
    else
    {
      $_SESSION['user_id']   = $acct;
      $_SESSION['login']     = $sqll->result($result, 0, 'login');
      // if we got a screen name, we'll want it later.
      $_SESSION['screenname']     = $name['ScreenName'];
      //gets our numerical level based on ArcEmu level.
      $_SESSION['user_lvl']  = gmlevel($temp['gm']);
      $_SESSION['realm_id']  = $sqll->quote_smart($_COOKIE['realm_id']);
      $_SESSION['client_ip'] = (isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
      $_SESSION['logged_in'] = true;
      redirect('index.php');
    }
  }
  else
  {
    setcookie (   'login', '', time() - 3600);
    setcookie ('realm_id', '', time() - 3600);
    setcookie (  'password', '', time() - 3600);
    redirect('login.php?error=1');
  }
}


//#################################################################################################
// MAIN
//#################################################################################################
if (isset($_COOKIE["login"]) && isset($_COOKIE["password"]) && isset($_COOKIE["realm_id"]) && empty($_GET['error']))
  do_cookie_login();

$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;
$info = (isset($_GET['info'])) ? $_GET['info'] : NULL;

//$lang_login = lang_login();

$output .= '
      <div class="bubble">
          <div class="top">';

if (1 == $err)
  $output .=  '
            <h1><font class="error">'.lang('login', 'bad_pass_user').'</font></h1>';
elseif (2 == $err)
  $output .=  '
            <h1><font class="error">'.lang('login', 'missing_pass_user').'</font></h1>';
elseif (3 == $err)
  $output .=  '
            <h1><font class="error">'.lang('login', 'banned_acc').'</font></h1>';
elseif (5 == $err)
{
  $output .=  '
            <h1><font class="error">'.lang('login', 'no_permision').'</font></h1>';
  if (isset($info))
    $output .= '<h1><font class="error">'.lang('login', 'req_permision').': '.$info.'</font></h1>';
}
elseif (6 == $err)
  $output .=  '
            <h1><font class="error">'.lang('login', 'after_registration').'</font></h1>';
else
  $output .=  '
            <h1>'.lang('login', 'enter_valid_logon').'</h1>';

unset($err);

$output .= '
          </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('dologin' === $action)
  dologin();
else
  login();

unset($action);
unset($action_permission);
//unset($lang_login);

require_once 'footer.php';


?>
