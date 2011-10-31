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

//#############################################################################
// HEADER SECTION
//#############################################################################

if ( isset($_COOKIE["corem_lang"]) )
{
  $lang = $_COOKIE["corem_lang"];
  if ( !file_exists("lang/".$lang.".php") )
    $lang = $language;
}
else
  $lang = $language;

require_once "lang/".$lang.".php";

require_once "libs/lang_lib.php";

header("Content-Type: text/html; charset=".$site_encoding);
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$output .= '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>'.lang("admin", "title").'</title>
    <meta http-equiv="Content-Type" content="text/html; charset='.$site_encoding.'" />
    <meta http-equiv="Content-Type" content="text/javascript; charset='.$site_encoding.'" />
    <link rel="stylesheet" type="text/css" href="admin/admin.css" />
    <link rel="SHORTCUT ICON" href="img/favicon.ico" />
    <script type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="libs/js/general.js"></script>
  </head>

  <body>';

// get our current revision
if ( is_readable(".svn/entries") )
{
  $file_obj = new SplFileObject(".svn/entries");
  // line 4 is where svn revision is stored
  $file_obj->seek(3);
  $current = rtrim($file_obj->current());
  unset($file_obj);
}

if ( strlen($current) == 0 )
{
  // if we didn't get a revision number from the entries file then we might be using SVN 1.7+
  if ( is_readable(".svn/wc.db") )
  {
    class wcDB extends SQLite3
    {
      function __construct()
      {
        $this->open(".svn/wc.db");
      }
    }

    $db = new wcDB();
    $result = $db->query("SELECT MAX(revision) FROM `NODES`");
    $result = $result->fetchArray();
    $current = $result[0];

    unset($db);
  }
}

// detect latest revision
// first, we ask assembla's trac for the latest changeset
// and parse it into an array
$handle = fopen("http://trac6.assembla.com/coremanager/changeset", "r");
$data = fread($handle, 256);
$data = explode("\n", $data);

// search the array for the blessed line
for ( $i = 0; $i < count($data); $i++ )
{
  if ( strpos($data[$i], "Changeset") <> false )
    break;
}

// if we got the line containing the revision then we need just the number
if ( strpos($data[$i], "Changeset") <> false )
{
  // convert the line into an array
  $revision = explode(" ", $data[$i]);

  // find the number
  for ( $j = 0; $j < count($revision); $j++ )
  {
    if ( is_numeric($revision[$j]) )
      break;
  }

  // compare
  if ( !isset($current) )
    $current = "ERROR";

  if ( $current <> $revision[$j] )
  {
    $_GET["error"] = 3; // force an error
    $latest = $revision[$j];
  }
}

if ( isset($_GET["section"]) )
  $section = $_GET["section"];
else
  redirect("admin.php?section=databases");

if ( !isset($_GET["error"]) )
  $output .= '
    <div id="header">';
else
  $output .= '
    <div id="header_error">';

$output .= '
      <ul>
        <li'.( ( $section == "databases" ) ? ' class="current" ' : '' ).'>
          <a href="admin.php?section=databases">'.lang("admin", "database").'</a>
        </li>
        <li'.( ( $section == "general" ) ? ' class="current" ' : '' ).'>
          <a href="admin.php?section=general&amp;subsection=more">'.lang("admin", "general").'</a>
        </li>
        <li'.( ( $section == "servers" ) ? ' class="current" ' : '' ).'>
          <a href="admin.php?section=servers">'.lang("admin", "servers").'</a>
        </li>
        <li'.( ( $section == "gmlevels" ) ? ' class="current" ' : '' ).'>
          <a href="admin.php?section=gmlevels">'.lang("admin", "gm_levels").'</a>
        </li>
        <li'.( ( $section == "menus" ) ? ' class="current" ' : '' ).'>
          <a href="admin.php?section=menus">'.lang("admin", "menus").'</a>
        </li>
        <li'.( ( $section == "forum" ) ? ' class="current" ' : '' ).'>
          <a href="admin.php?section=forum">'.lang("admin", "forum").'</a>
        </li>
        <li'.( ( $section == "accounts" ) ? ' class="current" ' : '' ).'>
          <a href="admin.php?section=accounts">'.lang("admin", "accounts").'</a>
        </li>
        <li'.( ( $section == "pointsystem" ) ? ' class="current" ' : '' ).'>
          <a href="admin.php?section=pointsystem&amp;subsection=basic">'.lang("admin", "pointsystem").'</a>
        </li>
        <li>
          <a href="index.php">'.lang("admin", "main").'</a>
        </li>
      </ul>
    </div>';

?>
