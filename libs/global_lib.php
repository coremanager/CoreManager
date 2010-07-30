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
//global output string - hands off...
$output = '';


//#############################################################################
//to avoid Strict Standards notices in php 5.1
if (function_exists ('date_default_timezone_set'))
{
  date_default_timezone_set($timezone);
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
  if (isset($_SESSION['user_lvl']) && isset($_SESSION['user_id']) && isset($_SESSION['realm_id']) && isset($_SESSION['login']))
  {
    $user_lvl = $_SESSION['user_lvl'];
    $ip = ( isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
    if ($ip === $_SESSION['client_ip']);
    else redirect('login.php?error=5'.( (isset($info)) ? '&info='.$info : '') );
  }
  else redirect('login.php?error=5'.( (isset($info)) ? '&info='.$info : '') );

  if ($user_lvl < $restrict_lvl)
    redirect('login.php?error=5'.( (isset($info)) ? '&info='.$info : '') );
}


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
  //$err = addslashes($err);
  // pass the error via session cookie instead of url
  $_SESSION['pass_error'] = $err;
  redirect('error.php?err=oopsy');
}


//#############################################################################
//testing for open port
function test_port($server,$port)
{
  $sock = @fsockopen($server, $port, $ERROR_NO, $ERROR_STR, (float)0.5);
  if($sock)
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
  if (is_string($field))
    $field = "'$field'";
  $order = ($order ? '<' : '>');
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

  /*global $gm_level_arr;

  switch ($gm)
  {
    case '-1':
    case '0':
    case 'a':
    case 'z':
    case 'az':
      return $gm_level_arr[$gm][0];
      break;
    default:
      return $gm_level_arr['1'][0];
      break;
  }*/
}


//#############################################################################
// Get GM Level Name
function gmlevel_name($gm)
{
  global $gm_level_arr;
  
  return $gm_level_arr[$gm][0];

  /*switch ($gm)
  {
    case '-1':
    case '0':
    case 'a':
    case 'z':
    case 'az':
      return $gm_level_arr[$gm][1];
      break;
    default:
      return $gm_level_arr['1'][1];
      break;
  }*/
}


//#############################################################################
// Get GM Level Short
function gmlevel_short($gm)
{
  global $gm_level_arr;
  
  return $gm_level_arr[$gm][1];

  /*switch ($gm)
  {
    case '-1':
    case '0':
    case 'a':
    case 'z':
    case 'az':
      return $gm_level_arr[$gm][2];
      break;
    default:
      return $gm_level_arr['1'][2];
      break;
  }*/
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
  if ($num_items);
  else return '';
  $total_pages = ceil($num_items/$per_page);
  if (1 == $total_pages)
  {
    return '';
  }
  $on_page = floor($start_item / $per_page)+1;
  $page_string = '';
  if (10 < $total_pages)
  {
    $init_page_max = (3 < $total_pages) ? 3 : $total_pages;
    $count = $init_page_max+1;
    for($i=1; $i<$count; ++$i)
    {
      $page_string .= ($i == $on_page) ? '<b>'.$i.'</b>' : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>';
      if ($i < $init_page_max)
      {
        $page_string .= ', ';
      }
    }
    if (3 < $total_pages)
    {
      if (1 < $on_page && $on_page < $total_pages)
      {
        $page_string  .= (5 < $on_page) ? ' ... ' : ', ';
        $init_page_min = (4 < $on_page) ? $on_page : 5;
        $init_page_max = ($on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

        $count = $init_page_max+2;
        for($i=$init_page_min-1; $i<$count; ++$i)
        {
          $page_string .= ($i === $on_page) ? '<b>'.$i.'</b>' : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>';
          if ($i <  $init_page_max+1)
          {
            $page_string .= ', ';
          }
        }
        $page_string .= ($on_page < $total_pages-4) ? ' ... ' : ', ';
      }
      else
      {
        $page_string .= ' ... ';
      }
      $count = $total_pages+1;
      for($i=$total_pages-2; $i<$count; ++$i)
      {
        $page_string .= ($i == $on_page) ? '<b>'.$i.'</b>'  : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>';
        if($i < $total_pages)
        {
          $page_string .= ', ';
        }
      }
    }
  }
  else
  {
    $count = $total_pages+1;
    for($i=1; $i<$count; ++$i)
    {
      $page_string .= ($i == $on_page) ? '<b>'.$i.'</b>' : '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($i-1)*$per_page).'">'.$i.'</a>';
      if ($i <  $total_pages)
      {
        $page_string .= ', ';
      }
    }
  }
  if ($add_prevnext_text)
  {
    if (1 < $on_page)
    {
      $page_string = '<a href="'.$base_url.'&amp;'.$start_tag.'='.(($on_page-2)*$per_page).'">Prev</a>&nbsp;&nbsp;'.$page_string;
    }
    if ($on_page < $total_pages)
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


?>
