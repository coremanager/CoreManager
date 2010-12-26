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

//#############################################################################
//validates sessions' vars and restricting access to given level
function valid_login_webadmin($restrict_lvl)
{
  global $sqlm;

  if ( isset($_SESSION["login"]) )
  {
    $query = "SELECT * FROM config_accounts WHERE Login='".$_SESSION["login"]."'";
    $user = $sqlm->fetch_assoc($sqlm->query($query));

    $user_lvl = $user["WebAdmin"];
    $ip = ( ( isset($_SERVER["REMOTE_ADDR"]) ) ? $_SERVER["REMOTE_ADDR"] : getenv("REMOTE_ADDR") );
    if ( !$ip === $_SESSION["client_ip"] )
      header("Location: admin_login.php");
  }
  else
    header("Location: admin_login.php");

  if ( $user_lvl < 1 )
    header("Location: admin_login.php?error=5");
}


//#############################################################################
// Get Security Level Name
function sec_level_name($sec)
{
  global $sqlm;
  
  $query = "SELECT * FROM config_gm_level_names WHERE Security_Level='".$sec."'";
  $fields = $sqlm->fetch_assoc($sqlm->query($query));
  
  return $fields["Full_Name"];
}


//#############################################################################
// Get Security Levels List
function sec_level_list()
{
  global $sqlm;
  
  $query = "SELECT Security_Level, Full_Name FROM config_gm_level_names";
  $fields = $sqlm->query($query);
  
  $out = array();
  
  while ( $row = $sqlm->fetch_assoc($fields) )
  {
    $outrow = array();
    $outrow["Sec"] = $row["Security_Level"];
    $outrow["Name"] = $row["Full_Name"];
    array_push($out, $outrow);
  }
  
  return $out;
}


//#############################################################################
//redirects to error page with error code
function error($err)
{
  die($err);
}


//#############################################################################
// Realm Icons for Servers Tab
$get_icon_type = array
(
  0 => array( 0, "normal"),
  1 => array( 1, "pvp"),
  6 => array( 6, "rp"),
  8 => array( 8, "rppvp"),
);


//#############################################################################
// Realm Timezones for Servers Tab
$get_timezone_type = array
(
  0 => array( 0, "undefined"),
  1 => array( 1, "development"),
  2 => array( 2, "united_states"),
  3 => array( 3, "oceanic"),
  4 => array( 4, "latin_america"),
  5 => array( 5, "tournament"),
  6 => array( 6, "korea"),
  8 => array( 8, "english"),
  9 => array( 9, "german"),
 10 => array(10, "french"),
 11 => array(11, "spanish"),
 12 => array(12, "russian"),
 14 => array(14, "taiwan"),
 16 => array(16, "china"),
 26 => array(26, "test_server"),
 28 => array(28, "qa_server"),
);


?>
