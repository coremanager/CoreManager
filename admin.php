<?php
/*
    CoreManager, PHP Front End for ArcEmu, MaNGOS, and TrinityCore
    Copyright (C) 2010-2012  CoreManager Project

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


$time_start = microtime(true);
// resuming login session if available, or start new one
if ( !ini_get("session.auto_start") )
  session_start();

require_once("configs/config.php");
require_once("libs/config_lib.php");
require_once("admin/admin_lib.php");
require_once("libs/lang_lib.php");

valid_login_webadmin(0);

$output = '';

//------------------Detect and Announce Empty DBC Tables-----------------------
$sql["dbc"] = new SQL;
$sql["dbc"]->connect($dbc_db["addr"], $dbc_db["user"], $dbc_db["pass"], $dbc_db["name"], $dbc_db["encoding"]);

$achievement = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM achievement"), 0);
$achievement_category = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM achievement_category"), 0);
$achievement_criteria = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM achievement_criteria"), 0);
$areatable = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM areatable"), 0);
$faction = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM faction"), 0);
$factiontemplate = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM factiontemplate"), 0);
$gemproperties = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM gemproperties"), 0);
$glyphproperties = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM glyphproperties"), 0);
$item = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM item"), 0);
$itemdisplayinfo = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM itemdisplayinfo"), 0);
$itemextendedcost = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM itemextendedcost"), 0);
$itemrandomproperties = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM itemrandomproperties"), 0);
$itemrandomsuffix = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM itemrandomsuffix"), 0);
$itemset = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM itemset"), 0);
$map = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM map"), 0);
$skillline = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM skillline"), 0);
$skilllineability = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM skilllineability"), 0);
$skillraceclassinfo = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM skillraceclassinfo"), 0);
$spell = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM spell"), 0);
$spellicon = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM spellicon"), 0);
$spellitemenchantment = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM spellitemenchantment"), 0);
$talent = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM talent"), 0);
$talenttab = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM talenttab"), 0);
$worldmaparea = $sql["dbc"]->result($sql["dbc"]->query("SELECT COUNT(*) FROM worldmaparea"), 0);
unset($sql);

if ( ( $achievement == 0 ) || ( $achievement_category == 0 ) || ( $achievement_criteria == 0 ) || ( $areatable == 0 ) 
  || ( $faction == 0 ) || ( $factiontemplate == 0 ) || ( $gemproperties == 0 ) || ( $glyphproperties == 0 ) 
  || ( $item == 0 ) || ( $itemdisplayinfo == 0 ) || ( $itemextendedcost == 0 ) || ( $itemrandomproperties == 0 ) 
  || ( $itemrandomsuffix == 0 ) || ( $itemset == 0 ) || ( $map == 0 ) || ( $skillline == 0 ) 
  || ( $skilllineability == 0 ) || ( $skillraceclassinfo == 0 ) || ( $spell == 0 ) || ( $spellicon == 0 ) 
  || ( $spellitemenchantment == 0 ) || ( $talent == 0 ) || ( $talenttab == 0 ) || ( $worldmaparea == 0 ) )
  $_GET["error"] = 4; // force an error
//-----------------------------------------------------------------------------

require_once("admin/header.php");


//#############################################################################
// Fix reditection error under MS-IIS fuckedup-servers.
function redirect($url)
{
  if ( strpos($_SERVER["SERVER_SOFTWARE"], "Microsoft-IIS") === false )
  {
    header("Location: ".$url);
    exit();
  }
  else
    die('<meta http-equiv="refresh" content="0;URL='.$url.'" />');
}


//#############################################################################
// MAIN
//#############################################################################
$err = ( ( isset($_GET["error"]) ) ? $_GET["error"] : NULL );

$output .= '
        <div class="top">
          <center>
            <h1>'.lang("admin", "title").'</h1>';

switch ( $err )
{
  case 1:
    $output .= '
            <h1>
              <font class="error">'.lang("global", "empty_fields").'</font>
            </h1>';
    break;
  case 2:
    $output .= '
            <h1>
              <font class="error">'.lang("admin", "nocarets").'</font>
            </h1>';
    break;
  case 3:
  {
    if ( $current != "ERROR" )
      $output .= '
            <h1>
              <font class="error">'.lang("admin", "newer_revision").' '.lang("admin", "current_rev").': '.$current.'; '.lang("admin", "latest_rev").': '.$latest.'</font>
            </h1>';
    else
      $output .= '
            <h1>
              <font class="error">'.lang("admin", "missing_svn").'</font>
            </h1>';
    break;
  }
  case 4:
    $output .= '
            <h1>
              <font class="error">'.lang("admin", "dbc_warn").'</font>
            </h1>';
    break;
  default:
    $output .= '
            <h1></h1>';
}

$output .= '
          </center>
        </div>
        <br />
        <br />';

unset($err);

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

switch ( $action )
{
  case "savedbs":
    require_once("admin/admin_databases_lib.php");
    savedbs();
    break;
  case "saveserver":
    require_once("admin/admin_servers_lib.php");
    saveserver();
    break;
  case "saveacct":
    require_once("admin/admin_accounts_lib.php");
    saveacct();
    break;
  case "savemenu":
    require_once("admin/admin_menus_lib.php");
    savemenu();
    break;
  case "saveforum":
    require_once("admin/admin_forum_lib.php");
    saveforum();
    break;
}

$section = ( ( isset($_GET["section"]) ) ? $_GET["section"] : NULL );

switch ( $section )
{
  case "databases":
    require_once("admin/admin_databases_lib.php");
    database();
    break;
  case "servers":
    require_once("admin/admin_servers_lib.php");
    servers();
    break;
  case "menus":
    require_once("admin/admin_menus_lib.php");
    menus();
    break;
  case "forum":
    require_once("admin/admin_forum_lib.php");
    forum();
    break;
  case "accounts":
    require_once("admin/admin_accounts_lib.php");
    accounts();
    break;
  case "pointsystem":
    require_once("admin/admin_pointsystem_lib.php");
    pointsystem();
    break;
  default:
    require_once("admin/admin_general_lib.php");
    general();
    break;
}

require_once("admin/footer.php");
?>
