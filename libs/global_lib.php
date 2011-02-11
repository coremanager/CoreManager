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
//global output string - hands off...
$output = '';


//#############################################################################
//to avoid Strict Standards notices in php 5.1
if ( function_exists ('date_default_timezone_set') )
{
  date_default_timezone_set(time_offset_to_zone($timezone));
}

function time_offset_to_zone($offset)
{
  switch ( $offset )
  {
    case "-12.0":
      $zone = "Pacific/Kwajalein";
      break;
    case "-11.0":
      $zone = "Pacific/Midway";
      break;
    case "-10.0":
      $zone = "Pacific/Honolulu";
      break;
    case "-9.0":
      $zone = "America/Anchorage";
      break;
    case "-8.0":
      $zone = "America/Los_Angeles";
      break;
    case "-7.0":
      $zone = "America/Boise";
      break;
    case "-6.0":
      $zone = "America/Chicago";
      break;
    case "-5.0":
      $zone = "America/New_York";
      break;
    case "-4.5":
      $zone = "America/Caracas";
      break;
    case "-4.0":
      $zone = "America/Aruba";
      break;
    case "-3.5":
      $zone = "America/St_Johns";
      break;
    case "-3.0":
      $zone = "America/Buenos_Aires";
      break;
    case "-2.0":
      $zone = "Atlantic/South_Georgia";
      break;
    case "-1.0":
      $zone = "Atlantic/Azores";
      break;
    case "0.0":
      $zone = "Europe/London";
      break;
    case "1.0":
      $zone = "Europe/Copenhagen";
      break;
    case "2.0":
      $zone = "Africa/Johannesburg";
      break;
    case "3.0":
      $zone = "Europe/Moscow";
      break;
    case "3.5":
      $zone = "Asia/Tehran";
      break;
    case "4.0":
      $zone = "Asia/Muscat";
      break;
    case "4.5":
      $zone = "Asia/Kabul";
      break;
    case "5.0":
      $zone = "Asia/Karachi";
      break;
    case "5.5":
      $zone = "Asia/Calcutta";
      break;
    case "5.75":
      $zone = "Asia/Kathmandu";
      break;
    case "6.0":
      $zone = "Asia/Colombo";
      break;
    case "7.0":
      $zone = "Asia/Bangkok";
      break;
    case "8.0":
      $zone = "Asia/Singapore";
      break;
    case "9.0":
      $zone = "Asia/Tokyo";
      break;
    case "9.5":
      $zone = "Australia/Darwin";
      break;
    case "10.0":
      $zone = "Pacific/Guam";
      break;
    case "11.0":
      $zone = "Pacific/Kosrae";
      break;
    case "12.0":
      $zone = "Pacific/Fiji";
      break;
  }
    
  return $zone;
}


//#############################################################################
// wowhead tooltip script location
$tt_script = 'http://www.wowhead.com/widgets/power.js';


//#############################################################################
// loading of wowhead tool tip script
function wowhead_tt()
{
  global $output, $tt_script;

  // check that we can reach wowhead
  // this should improve page load times when wowhead is unreachable
  if ( test_port("www.wowhead.com", 80) )
    $output .='
    <script type="text/javascript" src="'.$tt_script.'"></script>';

}


//#############################################################################
//validates sessions' vars and restricting access to given level
function valid_login($restrict_lvl, $info)
{
  if ( isset($_SESSION["user_lvl"]) && isset($_SESSION["user_id"]) && isset($_SESSION["realm_id"]) && isset($_SESSION["login"]) )
  {
    $user_lvl = $_SESSION["user_lvl"];
    $ip = ( isset($_SERVER["REMOTE_ADDR"]) ) ? $_SERVER["REMOTE_ADDR"] : getenv('REMOTE_ADDR');
    if ( $ip === $_SESSION["client_ip"] )
      ;
    else
      redirect('login.php?error=5'.( ( isset($info) ) ? '&info='.$info : '' ) );
  }
  else
    redirect('login.php?error=5'.( ( isset($info) ) ? '&info='.$info : '' ) );

  if ( $user_lvl < $restrict_lvl )
    redirect('login.php?error=5'.( ( isset($info) ) ? '&info='.$info : '' ) );
}


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
  //$err = addslashes($err);
  // pass the error via session cookie instead of url
  $_SESSION["pass_error"] = $err;
  redirect('error.php?err=oopsy');
}


//#############################################################################
//testing for open port
function test_port($server,$port)
{
  $sock = @fsockopen($server, $port, $ERROR_NO, $ERROR_STR, (float)0.5);
  if ( $sock )
  {
    @fclose($sock);
    return true;
  }
  else
    return false;
}


//#############################################################################
function aasort(&$array, $field, $order = false)
{
  if ( is_string($field) )
    $field = "'".$field."'";
  $order = ( ( $order ) ? '<' : '>' );
  usort
  (
    $array,
    create_function('$a, $b',
      'return ($a['.$field.'] == $b['.$field.'] ? 0 :($a['.$field.'] '.$order.' $b['.$field.']) ? 1 : -1);')
  );
}


//#############################################################################
//making buttons - just to make them all look the same
function makebutton($xtext, $xlink, $xwidth)
{
  global $output;
  $output .= '
              <div>
                <a class="button" style="width:'.$xwidth.'px;" href="'.$xlink.'">'.$xtext.'</a>
              </div>';
}


//#############################################################################
// Get GM Level
function gmlevel($gm)
{
  return $gm;
}


//#############################################################################
// Get GM Level Name
function gmlevel_name($gm)
{
  global $gm_level_arr;
  
  return $gm_level_arr[$gm][0];
}


//#############################################################################
// Get GM Level Short
function gmlevel_short($gm)
{
  global $gm_level_arr;
  
  return $gm_level_arr[$gm][1];
}


//#############################################################################
//make javascript tooltip
function maketooltip($text, $link, $tip, $class, $target = 'target="_self"')
{
  global $output;
  //COMMENTED OUT SINCE WE WANT WOWHEAD TOOLTIPS ONLY
  //$output .='<a style="padding:2px;" href="$link" $target onmouseover="toolTip(\''.addslashes($tip).'\', \''.$class.'\')" onmouseout="toolTip()">'.$text.'</a>';

  $output .= '<a style="padding:2px;" href="'.$link.'" '.$target.'>'.$text.'</a>';
}


//#############################################################################
// Generate paging navigation.
// Original from PHPBB with some modifications to make them more simple
function generate_pagination($base_url, $num_items, $per_page, $start_item, $start_tag = 'start', $add_prevnext_text = TRUE)
{
  if ( $num_items )
    ;
  else
    return '';

  $total_pages = ceil($num_items/$per_page);
  if ( $total_pages == 1)
  {
    return '';
  }
  $on_page = floor($start_item / $per_page)+1;
  $page_string = '';
  if ( $total_pages > 10 )
  {
    $init_page_max = (3 < $total_pages) ? 3 : $total_pages;
    $count = $init_page_max+1;
    for ( $i=1; $i<$count; ++$i )
    {
      $page_string .= ( ( $i == $on_page ) ? '<b>'.$i.'</b>' : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>' );
      if ( $i < $init_page_max )
      {
        $page_string .= ', ';
      }
    }
    if ( $total_pages > 3 )
    {
      if ( ( $on_page > 1 ) && ( $on_page < $total_pages ) )
      {
        $page_string  .= ( ( $on_page > 5 ) ? ' ... ' : ', ' );
        $init_page_min = ( ( $on_page > 4 ) ? $on_page : 5 );
        $init_page_max = ( ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4 );

        $count = $init_page_max+2;
        for ( $i=$init_page_min-1; $i<$count; ++$i )
        {
          $page_string .= ( ( $i === $on_page ) ? '<b>'.$i.'</b>' : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>' );
          if ( $i <  $init_page_max+1 )
          {
            $page_string .= ', ';
          }
        }
        $page_string .= ( ( $on_page < $total_pages-4 ) ? ' ... ' : ', ' );
      }
      else
      {
        $page_string .= ' ... ';
      }
      $count = $total_pages+1;
      for ( $i=$total_pages-2; $i<$count; ++$i )
      {
        $page_string .= ( ( $i == $on_page ) ? '<b>'.$i.'</b>'  : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>' );
        if ( $i < $total_pages )
        {
          $page_string .= ', ';
        }
      }
    }
  }
  else
  {
    $count = $total_pages+1;
    for ( $i=1; $i<$count; ++$i )
    {
      $page_string .= ( ( $i == $on_page ) ? '<b>'.$i.'</b>' : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>' );
      if ( $i <  $total_pages )
      {
        $page_string .= ', ';
      }
    }
  }
  if ( $add_prevnext_text )
  {
    if ( $on_page > 1 )
    {
      $page_string = '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($on_page-2)*$per_page).'">Prev</a>&nbsp;&nbsp;'.$page_string;
    }
    if ( $on_page < $total_pages )
    {
      $page_string .= '&nbsp;&nbsp;<a href="'.$base_url.'&amp;'.$start_tag.'='.($on_page*$per_page).'">Next</a>';
    }
  }
  $page_string = 'Page: '.$page_string;

  return $page_string;

}


//#############################################################################
// core number to name
function core_name($core_id)
{
  switch ( $core_id )
  {
    case 1:
    {
      return 'ArcEmu';
    }
    case 2:
    {
      return 'MaNGOS';
    }
    case 3:
    {
      return 'Trinity';
    }
  }
}


//#############################################################################
// get specific permission of specific page
function get_page_permission($restrict_lvl, $page)
{
  global $sql;

  $menu_array = array();
  $temp = $sql["mgr"]->query("SELECT * FROM config_top_menus");
  while ( $tmenus = $sql["mgr"]->fetch_assoc($temp) )
  {
    $top = array();
    $top[0] = $tmenus["Action"];
    $top[1] = $tmenus["Name"];

    $m = array();
    $temp_menus = $sql["mgr"]->query("SELECT * FROM config_menus WHERE Menu='".$tmenus["Index"]."' ORDER BY `Order`");
    while ( $menus = $sql["mgr"]->fetch_assoc($temp_menus) )
    {
      if ( $menus["Enabled"] )
      {
        $menu = array();
        array_push($menu,$menus["Action"]);
        array_push($menu,$menus["Name"]);
        array_push($menu,$menus["View"]);
        array_push($menu,$menus["Insert"]);
        array_push($menu,$menus["Update"]);
        array_push($menu,$menus["Delete"]);
        array_push($m, $menu);
      }
    }

    $top[2] = $m;

    array_push($menu_array, $top);
  }

  $action_permission = array();
  foreach ( $menu_array as $trunk )
  {
    // ignore "invisible array" this is for setting security read/write values
    // for not accessible elements not in the navbar!
    if ( $trunk[1] == "invisible")
    {
      foreach ( $trunk[2] as $branch )
      {
        if ( $branch[0] === $page )
        {
          $action_permission["view"]   = $branch[2];
          $action_permission["insert"] = $branch[3];
          $action_permission["update"] = $branch[4];
          $action_permission["delete"] = $branch[5];
        }
      }
    }
    else
    {
      foreach ( $trunk[2] as $branch )
      {
        if ( $branch[0] === $page )
        {
          $action_permission["view"]   = $branch[2];
          $action_permission["insert"] = $branch[3];
          $action_permission["update"] = $branch[4];
          $action_permission["delete"] = $branch[5];
        }
      }
    }
  }
  unset($branch);
  unset($trunk);
  unset($menu_array);

  return $action_permission[$restrict_lvl];
}


//#############################################################################
// Create Invited By entry in point_system_invites table
function doupdate_referral($referredby, $our_acct)
{
  global $corem_db, $logon_db, $user_id, $sql;

  if ( $referredby != NULL )
  {
    if ( isset($our_acct) )
      // we got here from a new registration
      $referred_acct = $our_acct;
    else
      // an existing account is declaring their Invited By
      $referred_acct = $user_id;

    $query = "SELECT InvitedBy FROM point_system_invites WHERE PlayersAccount='".$referred_acct."'";
    $result = $sql["mgr"]->query($query);
    $result = $sql["mgr"]->fetch_assoc($result);
    $result = $result["InvitedBy"];

    if ( $result == NULL )
    {
      $query = "SELECT guid FROM characters WHERE name='".$referredby."'";
      $referred_by_result = $sql["char"]->query($query);
      $referred_by = $sql["char"]->fetch_assoc($referred_by_result);
      $referred_by = $referred_by["guid"];

      if ( $referred_by != NULL )
      {
        // get the account to which the character belongs
        if ( $core == 1 )
          $query = "SELECT acct FROM characters WHERE guid='".$referred_by."'";
        else
          $query = "SELECT account AS acct FROM characters WHERE guid='".$referred_by."'";
        $c_acct = $sql["char"]->fetch_row($sql["char"]->query($query));

        // check that the account actually exists (that we don't have an orphan character)
        if ( $core == 1 )
          $query = "SELECT acct FROM accounts WHERE acct='".$c_acct[0]."'";
        else
          $query = "SELECT id AS acct FROM account WHERE id='".$c_acct[0]."'";
        $result = $sql["logon"]->query($query);
        $result = $sql["logon"]->fetch_assoc($result);
        $result = $result["acct"];

        // save
        if ( $result != $user_id )
        {
          $query = "INSERT INTO point_system_invites (PlayersAccount, InvitedBy, InviterAccount) VALUES ('".$referred_acct."', '".$referred_by."', '".$result."')";
          $sql["mgr"]->query($query);
          return true;
        }
        else
          return false;
      }
      return false;
    }
  }
  else
    return true; // Invited By was left blank
}


?>
