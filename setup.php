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


require_once("configs/config.php");

//#############################################################################
// HEADER SECTION
//#############################################################################

if ( isset($_COOKIE["lang"]) )
{
  $lang = $_COOKIE["lang"];
  if ( file_exists('../lang/'.$lang.'.php') )
    ;
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
    <title>'.lang("setup", "title").'</title>
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
  if ( strpos($_SERVER["SERVER_SOFTWARE"], 'Microsoft-IIS') === false )
  {
    header('Location: '.$url);
    exit();
  }
  else
    die('<meta http-equiv="refresh" content="0;URL='.$url.'" />');
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
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"]);

  // first we check that we even have a config_misc table...
  $check_for_table = $sqlm->fetch_assoc($sqlm->query("SHOW TABLES FROM ".$corem_db["name"]." LIKE 'config_misc'"));

  if ( $check_for_table["Tables_in_".$corem_db["name"]." (config_misc)"] != NULL )
  {
    // if we do have one, make sure we aren't installed
    $check_installed = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Installed'"));

    if ( $check_installed["Value"] == 1 )
      redirect("index.php");
  }

  $output .= '
    <center>
      <b>'.lang("setup", "welcome").' '.lang("setup", "title").'</b>
      <br />
      <br />
      <b id="setup_fields">'.lang("setup", "fields").'</b>
      <br />
      <br />';

  if ( isset($_GET["error"]) )
  {
    switch ( $_GET["error"] )
    {
      case 1:
      {
        $output .= '
      <h1 id="setup_emptyfields">'.lang("global", "empty_fields").'</h1>';
        break;
      }
    }
  }

  $output .= '
      <form name="form" action="setup.php" method="GET">
        <input type="hidden" name="action" value="save" />
        <div id="setup_logon_field" class="fieldset_border">
          <span class="legend">'.lang("setup", "logon_db").'</span>
          <table>
            <tr>
              <td>'.lang("setup", "host").': </td>
              <td>
                <input type="text" name="host" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang("setup", "port").': </td>
              <td>
                <input type="text" name="port" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang("setup", "user").': </td>
              <td>
                <input type="text" name="user" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang("setup", "pass").': </td>
              <td>
                <input type="text" name="pass" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang("setup", "name").': </td>
              <td>
                <input type="text" name="name" value="" />
              </td>
            </tr>
          </table>
        </div>
        <br />
        <div id="setup_logon_field" class="fieldset_border">
          <span class="legend">'.lang("setup", "dbc_db").'</span>
          <table>
            <tr>
              <td>'.lang("setup", "host").': </td>
              <td>
                <input type="text" name="dbchost" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang("setup", "port").': </td>
              <td>
                <input type="text" name="dbcport" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang("setup", "user").': </td>
              <td>
                <input type="text" name="dbcuser" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang("setup", "pass").': </td>
              <td>
                <input type="text" name="dbcpass" value="" />
              </td>
            </tr>
            <tr>
              <td>'.lang("setup", "name").': </td>
              <td>
                <input type="text" name="dbcname" value="" />
              </td>
            </tr>
          </table>
        </div>
        <br />
        <div id="setup_acp_field" class="fieldset_border">
          <span class="legend">'.lang("setup", "webadmin").'</span>
          <table>
            <tr>
              <td colspan="2"><span style="color:red">'.lang("setup", "acctinfo").'</span></td>
            </tr>
            <tr>
              <td align="right">'.lang("setup", "acctname").': </td>
              <td>
                <input type="text" name="acctname" value="" />
              </td>
            </tr>
            <tr>
              <td align="right">'.lang("setup", "screenname").': </td>
              <td>
                <input type="text" name="screenname" value="" />
              </td>
            </tr>
          </table>
        </div>
        <br />
        <input type="submit" name="save" value="'.lang("setup", "save").'" />
      </form>
    </center>';
}

function save()
{
  global $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"]);


  // then we get the config data
  if ( $_GET["host"] <> "" )
    $host = $sqlm->quote_smart($_GET["host"]);
  else
    redirect("setup.php?error=1");

  if ( $_GET["port"] <> "" )
    $port = $sqlm->quote_smart($_GET["port"]);
  else
    redirect("setup.php?error=1");

  if ( $_GET["user"] <> "" )
    $user = $sqlm->quote_smart($_GET["user"]);
  else
    redirect("setup.php?error=1");

  if ( $_GET["pass"] <> "" )
    $pass = $sqlm->quote_smart($_GET["pass"]);
  else
    redirect("setup.php?error=1");

  if ( $_GET["name"] <> "" )
    $name = $sqlm->quote_smart($_GET["name"]);
  else
    redirect("setup.php?error=1");


  if ( $_GET["dbchost"] <> "" )
    $dbchost = $sqlm->quote_smart($_GET["dbchost"]);
  else
    redirect("setup.php?error=1");

  if ( $_GET["dbcport"] <> "" )
    $dbcport = $sqlm->quote_smart($_GET["dbcport"]);
  else
    redirect("setup.php?error=1");

  if ( $_GET["dbcuser"] <> "" )
    $dbcuser = $sqlm->quote_smart($_GET["dbcuser"]);
  else
    redirect("setup.php?error=1");

  if ( $_GET["dbcpass"] <> "" )
    $dbcpass = $sqlm->quote_smart($_GET["dbcpass"]);
  else
    redirect("setup.php?error=1");

  if ( $_GET["dbcname"] <> "" )
    $dbcname = $sqlm->quote_smart($_GET["dbcname"]);
  else
    redirect("setup.php?error=1");


  if ( $_GET["acctname"] <> "" )
    $acctname = $sqlm->quote_smart($_GET["acctname"]);
  else
    redirect("setup.php?error=1");

  if ( $_GET["screenname"] <> "" )
    $screenname = $sqlm->quote_smart($_GET["screenname"]);
  else
    redirect("setup.php?error=1");


  // first, we import databases
  import_db($dbchost, $dbcport, $dbcuser, $dbcpass, $dbcname);


  // save logon database configs
  $logon_count = $sqlm->fetch_assoc($sqlm->query("SELECT COUNT(*) FROM config_logon_database"));
  if ( $logon_count["COUNT(*)"] == 1 )
  {
    $logon_upper = $sqlm->fetch_assoc($sqlm->query("SELECT MAX(`Index`) FROM config_logon_database"));
    $result = $sqlm->query("UPDATE config_logon_database SET Address='".$host."', Port='".$port."', Name='".$name."', User='".$user."', Password='".$pass."', Encoding='utf8' WHERE `Index`='".$logon_upper["MAX(`Index`)"]."'");
  }
  elseif ( $logon_count["COUNT(*)"] > 1 )
  {
    $result = $sqlm->query("TRUNCATE TABLE config_logon_database");
    $result = $sqlm->query("INSERT INTO config_logon_database (Address, Port, User, Name, Password, Encoding) VALUES ('".$host."', '".$port."', '".$user."', '".$name."', '".$pass."', 'utf8')");
  }
  else
  {
    $result = $sqlm->query("INSERT INTO config_logon_database (Address, Port, User, Name, Password, Encoding) VALUES ('".$host."', '".$port."', '".$user."', '".$name."', '".$pass."', 'utf8')");
  }


  // save dbc database configs
  $dbc_count = $sqlm->fetch_assoc($sqlm->query("SELECT COUNT(*) FROM config_dbc_database"));
  if ( $dbc_count["COUNT(*)"] == 1)
  {
    $dbc_upper = $sqlm->fetch_assoc($sqlm->query("SELECT MAX(`Index`) FROM config_dbc_database"));
    $result = $sqlm->query("UPDATE config_dbc_database SET Address='".$dbchost."', Port='".$dbcport."', Name='".$dbcname."', User='".$dbcuser."', Password='".$dbcpass."', Encoding='utf8' WHERE `Index`='".$dbc_upper["MAX(`Index`)"]."'");
  }
  elseif ( $dbc_count["COUNT(*)"] > 1 )
  {
    $result = $sqlm->query("TRUNCATE TABLE config_dbc_database");
    $result = $sqlm->query("INSERT INTO config_dbc_database (Address, Port, User, Name, Password, Encoding) VALUES ('".$dbchost."', '".$dbcport."', '".$dbcuser."', '".$dbcname."', '".$dbcpass."', 'utf8')");
  }
  else
  {
    $result = $sqlm->query("INSERT INTO config_dbc_database (Address, Port, User, Name, Password, Encoding) VALUES ('".$dbchost."', '".$dbcport."', '".$dbcuser."', '".$dbcname."', '".$dbcpass."', 'utf8')");
  }


  // set up web admin account
  $account = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_accounts WHERE Login='".$acctname."'"));
  if ( $account["Login"] <> '' )
    $result = $sqlm->query("UPDATE config_accounts SET ScreenName='".$screenname."', SecurityLevel='4', WebAdmin='1' WHERE Login='".$acctname."'");
  else
    $result = $sqlm->query("INSERT INTO config_accounts (Login, ScreenName, SecurityLevel, WebAdmin) VALUES (UPPER('".$acctname."'), '".$screenname."', '4', '1')");

  $result = $sqlm->query("UPDATE config_misc SET Value='1' WHERE `Key`='Installed'");

  redirect("admin.php");
}

function import_db($dbchost, $dbcport, $dbcuser, $dbcpass, $dbcname)
{
  global $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"]);

  $sqld = new SQL;
  $sqld->connect($dbchost.":".$dbcport, $dbcuser, $dbcpass, $dbcname);


  // revision of current structure
  $base_rev = '72';


  //#############################################################################
  // IMPORT COREMANAGER DB STRUCTURE

  $file = fopen("SQL/".$base_rev."_coremanager_db_structure.sql", "r") or exit("Unable to open file!");

  $out = '';
  $holding = 0;

  while( !feof($file) )
  {
    $line = fgets($file);
    $check = explode(' ', $line);

    if ( $check[0] == 'CREATE' )
    {
      $holding = 1;
      $out .= $line;
    }
    elseif ( $check[0] == ')' )
      $out .= $line;
    elseif ( $check[0] == '/*!40101' )
      $out .= '';
    else
      $out .= $line;

    if ( $holding && ($check[0] == ')') )
    {
      $holding = 0;
      $sqlm->query($out);
    }

    if ( !$holding )
      $out = '';
  }
  fclose($file);


  //#############################################################################
  // IMPORT COREMANAGER DB DATA

  $file = fopen("SQL/".$base_rev."_coremanager_db_data.sql", "r") or exit("Unable to open file!");

  while( !feof($file) )
  {
    $line = fgets($file);

    if ( strlen($line) > 2 )
      $sqlm->query($line);
  }
  fclose($file);


  //#############################################################################
  // DO COREMANAGER DB UPDATES

  // open updates directory 
  $myDirectory = opendir("SQL/update/coremanager_database_updates");

  // get only the updates that we need
  $dirArray = array();
  while ( $entryName = readdir($myDirectory) )
  {
    if ( !is_dir($entryName) )
    {
      $check = explode("_", $entryName);

      if ( $check[0] > $base_rev )
        $dirArray[] = $entryName;
    }
  }

  // close directory
  closedir($myDirectory);

  // sort 'em
  sort($dirArray);

  // execute the updates
  foreach ( $dirArray as $entry )
  {
    $file = fopen("SQL/update/coremanager_database_updates/".$entry, "r") or exit("Unable to open file: ".$entry."!");

    while( !feof($file) )
    {
      $line = fgets($file);
      $check = explode(' ', $line);

      if ( ( $check[0] != 'INSERT' ) && ( $check[0] != 'UPDATE' ) && ( $check[0] != 'ALTER' ) )
      {
        if ( $check[0] == 'CREATE' )
        {
          $holding = 1;
          $out .= $line;
        }
        elseif ( $check[0] == ')' )
          $out .= $line;
        elseif ( $check[0] == '/*!40101' )
          $out .= '';
        else
          $out .= $line;

        if ( $holding && ( $check[0] == ')' ) )
        {
          $holding = 0;
          $sqlm->query($out);
        }
      }
      else
      {
        if ( strlen($line) > 2 )
          $sqlm->query($line);
      }

      if ( !$holding )
        $out = '';
    }
    fclose($file);
  }


  //#############################################################################
  // IMPORT COREMANAGER DBC DB STRUCTURE

  $file = fopen("SQL/".$base_rev."_coremanager_dbc_structure.sql", "r") or exit("Unable to open file!");

  $out = '';
  $holding = 0;

  while( !feof($file) )
  {
    $line = fgets($file);
    $check = explode(' ', $line);
    if ( $check[0] == 'CREATE' )
    {
      $holding = 1;
      $out .= $line;
    }
    elseif ( $check[0] == ')' )
      $out .= $line;
    elseif ( $check[0] == '/*!40101' )
      $out .= '';
    else
      $out .= $line;

    if ( $holding && ($check[0] == ')') )
    {
      $holding = 0;
      $sqld->query($out);
    }

    if ( !$holding )
      $out = '';
  }
  fclose($file);
}


//#############################################################################
// MAIN
//#############################################################################

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

switch ( $action )
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
