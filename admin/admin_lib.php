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
  global $arcm_db, $sqlm;

  if (isset($_SESSION['login']))
  {
    /*$sqlm = new SQL;
    $sqlm->connect($arcm_db['addr'], $arcm_db['user'], $arcm_db['pass'], $arcm_db['name']);*/

    $query = "SELECT * FROM config_accounts WHERE Login = '".$_SESSION['login']."'";
    $user = $sqlm->fetch_assoc($sqlm->query($query));

    $user_lvl = $user['WebAdmin'];
    $ip = ( isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
    if ($ip === $_SESSION['client_ip'])
      ;
    else
      header('Location: '.'login.php');
  }
  else
    header('Location: '.'login.php');

  if ($user_lvl < 1)
    header('Location: '.'login.php?error=5');
}


//#############################################################################
// Get Security Level Name
function sec_level_name($sec)
{
  global $arcm_db, $sqlm;
  
  /*$sqlm = new SQL;
  $sqlm->connect($arcm_db['addr'], $arcm_db['user'], $arcm_db['pass'], $arcm_db['name']);*/
  
  $query = "SELECT * FROM config_gm_level_names WHERE Security_Level = '".$sec."'";
  $fields = $sqlm->fetch_assoc($sqlm->query($query));
  
  return $fields['Full_Name'];
}


//#############################################################################
// Get Security Levels List
function sec_level_list()
{
  global $arcm_db, $sqlm;
  
  /*$sqlx = new SQL;
  $sqlx->connect($config_db['addr'], $config_db['user'], $config_db['pass'], $config_db['name']);*/
  
  $query = "SELECT Security_Level, Full_Name FROM config_gm_level_names";
  $fields = $sqlm->query($query);
  
  $out = array();
  
  while ($row = $sqlm->fetch_assoc($fields))
  {
    $outrow = array();
    $outrow['Sec'] = $row['Security_Level'];
    $outrow['Name'] = $row['Full_Name'];
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
?>
