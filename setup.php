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

$corem_db['addr']     = '127.0.0.1:3306';         // SQL server IP:port your CoreManager DB is located on
$corem_db['user']     = 'root';                   // SQL server login your CoreManager DB is located on
$corem_db['pass']     = 'password';               // SQL server pass your CoreManager DB is located on
$corem_db['name']     = 'db name';                // CoreManager DB name
$corem_db['encoding'] = 'utf8';                   // SQL connection encoding

//#############################################################################
//---- SQL Configuration ----
//
//  SQL server type  :
//  'MySQL'   - Mysql
//  'PgSQL'   - PostgreSQL
//  'MySQLi'  - MySQLi
//  'SQLLite' - SQLite

$db_type          = 'MySQL';

//#############################################################################
//
// DO NOT CHANGE ANYTHING AFTER THIS POINT
//
//#############################################################################


//#############################################################################
// HEADER SECTION
//#############################################################################

if (isset($_COOKIE['lang']))
{
  $lang = $_COOKIE['lang'];
  if (file_exists('../lang/'.$lang.'.php'));
  else
    $lang = 'english';
}
else
  $lang = 'english';

require_once 'lang/'.$lang.'.php';

require_once 'libs/db_lib.php';
require_once 'libs/lang_lib.php';

$output = '';

header('Content-Type: text/html; charset=utf-8');
header('Expires: Tue, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
$output .= '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>'.lang('setup', 'title').'</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/javascript; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="admin/admin.css" />
    <link rel="SHORTCUT ICON" href="img/favicon.ico" />
    <script type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="libs/js/general.js"></script>
  </head>

  <body>';


//#############################################################################
// Fix reditection error under MS-IIS fuckedup-servers.
function redirect($url)
{
  if (strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') === false)
  {
    header('Location: '.$url);
    exit();
  }
  else die('<meta http-equiv="refresh" content="0;URL='.$url.'" />');
}

//#############################################################################
//redirects to error page with error code
function error($err)
{
  die($err);
}

//#############################################################################
// FUNCTIONS SECTION
//#############################################################################

function show()
{
  global $output, $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $check_installed = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key` = 'Installed'"));

  if ($check_installed['Value'] == 1)
    redirect("index.php");

  $output .= '
    <center>
      <b>'.lang('setup', 'welcome').' '.lang('setup', 'title').'</b>
      <br />
      <br />
      <b id="setup_fields">'.lang('setup', 'fields').'</b>
      <br />
      <br />';

  if (isset($_GET['error']))
  {
    switch ($_GET['error'])
    {
      case 1:
      {
        $output .= '
      <h1 id="setup_emptyfields">'.lang('global', 'empty_fields').'</h1>';
        break;
      }
    }
  }

  $output .= '
      <form name="form" action="setup.php" method="GET">
        <input type="hidden" name="action" value="save" />
        <fieldset id="setup_logon_field">
          <legend>'.lang('setup', 'logon_db').'</legend>
          <table>
            <tr>
              <td>'.lang('setup', 'host').': </td>
              <td>
                <input type="text" name="host" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang('setup', 'port').': </td>
              <td>
                <input type="text" name="port" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang('setup', 'user').': </td>
              <td>
                <input type="text" name="user" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang('setup', 'pass').': </td>
              <td>
                <input type="text" name="pass" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang('setup', 'name').': </td>
              <td>
                <input type="text" name="name" value="" />
              </td>
            </tr>
          </table>
        </fieldset>
        <br />
        <fieldset id="setup_acp_field">
          <legend>'.lang('setup', 'webadmin').'</legend>
          <table>
            <tr>
              <td colspan="2"><span style="color:red">'.lang('setup', 'acctinfo').'</span></td>
            </tr>
            <tr>
              <td>'.lang('setup', 'acctname').': </td>
              <td>
                <input type="text" name="acctname" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang('setup', 'screenname').': </td>
              <td>
                <input type="text" name="screenname" value="" />
              </td>
            </tr>
          </table>
        </fieldset>
        <br />
        <input type="submit" name="save" value="'.lang('setup', 'save').'" />
      </form>
    </center>';
}

function save()
{
  global $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  if ($_GET['host'] <> "")
    $host = $sqlm->quote_smart($_GET['host']);
  else
    redirect("setup.php?error=1");

  if ($_GET['port'] <> "")
    $port = $sqlm->quote_smart($_GET['port']);
  else
    redirect("setup.php?error=1");

  if ($_GET['user'] <> "")
    $user = $sqlm->quote_smart($_GET['user']);
  else
    redirect("setup.php?error=1");

  if ($_GET['pass'] <> "")
    $pass = $sqlm->quote_smart($_GET['pass']);
  else
    redirect("setup.php?error=1");

  if ($_GET['name'] <> "")
    $name = $sqlm->quote_smart($_GET['name']);
  else
    redirect("setup.php?error=1");

  if ($_GET['acctname'] <> "")
    $acctname = $sqlm->quote_smart($_GET['acctname']);
  else
    redirect("setup.php?error=1");

  if ($_GET['screenname'] <> "")
    $screenname = $sqlm->quote_smart($_GET['screenname']);
  else
    redirect("setup.php?error=1");

  $logon_count = $sqlm->fetch_assoc($sqlm->query("SELECT COUNT(*) FROM config_logon_database"));
  if ($logon_count['COUNT(*)'] == 1)
  {
    $logon_upper = $sqlm->fetch_assoc($sqlm->query("SELECT MAX(`Index`) FROM config_logon_database"));
    $result = $sqlm->query("UPDATE config_logon_database SET Address='".$host."', Port='".$port."', Name='".$name."', User='".$user."', Password='".$pass."', Encoding='utf8' WHERE `Index`='".$logon_upper['MAX(`Index`)']."'");
  }
  elseif ($logon_count['COUNT(*)'] > 1)
  {
    $result = $sqlm->query("TRUNCATE TABLE config_logon_database");
    $result = $sqlm->query("INSERT INTO config_logon_database (Address, Port, User, Name, Password, Encoding) VALUES ('".$host."', '".$port."', '".$user."', '".$name."', '".$pass."', 'utf8')");
  }
  else
  {
    $result = $sqlm->query("INSERT INTO config_logon_database (Address, Port, User, Name, Password, Encoding) VALUES ('".$host."', '".$port."', '".$user."', '".$name."', '".$pass."', 'utf8')");
  }

  $account = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_accounts WHERE Login='".$acctname."'"));
  if ($account['Login'] <> '')
    $result = $sqlm->query("UPDATE config_accounts SET ScreenName='".$screenname."', SecurityLevel='4', WebAdmin='1' WHERE Login='".$acctname."'");
  else
    $result = $sqlm->query("INSERT INTO config_accounts (Login, ScreenName, SecurityLevel, WebAdmin) VALUES ('".$acctname."', '".$screenname."', '4', '1')");

  $result = $sqlm->query("UPDATE config_misc SET Value='1' WHERE `Key`='Installed'");
  redirect("admin.php?");
}


//#############################################################################
// MAIN
//#############################################################################

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "save":
    save();
    break;
  default:
    show();
    break;
}


//#############################################################################
// FOOTER SECTION
//#############################################################################

$output .= '
  </body>
</html>';

echo $output;
?>
