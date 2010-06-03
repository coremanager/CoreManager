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


/*
//#############################################################################
//get achievement name by its id

function achieve_get_name($id, &$sql['mgr'])
{
  $achievement_name = $sql['mgr']->fetch_assoc($sql['mgr']->query('SELECT name FROM achievement WHERE id= '.$id.' LIMIT 1'));
  return $achievement_name['name'];
}


//#############################################################################
//get achievement reward name by its id

function achieve_get_reward($id, &$sql['mgr'])
{
  $achievement_reward = $sql['mgr']->fetch_assoc($sql['mgr']->query('SELECT reward FROM achievement WHERE id ='.$id.' LIMIT 1'));
  return $achievement_reward['reward'];
}


//#############################################################################
//get achievement points name by its id

function achieve_get_points($id, &$sql['mgr'])
{
  $achievement_points = $sql['mgr']->fetch_assoc($sql['mgr']->query('SELECT points FROM achievement WHERE id = '.$id.' LIMIT 1'));
  return $achievement_points['points'];
}


//#############################################################################
//get achievement category name by its id

function achieve_get_category($id, &$sql['mgr'])
{
  $category_id= $sql['mgr']->fetch_assoc($sql['mgr']->query('SELECT category FROM achievement WHERE id = '.$id.' LIMIT 1'));
  $category_name = $sql['mgr']->fetch_assoc($sql['mgr']->query('SELECT Name FROM achievement_category WHERE ID = '.$category_id['category'].' LIMIT 1'));
  return $category_name['Name'];
}
*/


//#############################################################################
//get achievements by category id

function achieve_get_id_category($id)
{
  global $sql;

  $achieve_cat = array();
  $result = ($sql['dbc']->query('SELECT id, name, description, reward, points FROM achievement WHERE category = \''.$id.'\' ORDER BY `orderInGroup` DESC'));
  while ($achieve_cat[] = $sql['dbc']->fetch_assoc($result));
  return $achieve_cat;
}


//#############################################################################
//get achievement main category

function achieve_get_main_category()
{
  global $sql;

  $main_cat = array();
  $result = $sql['dbc']->query('SELECT ID, Name FROM achievement_category WHERE ParentID = -1 and ID != 1 ORDER BY `GroupID` ASC');
  while ($main_cat[] = $sql['dbc']->fetch_assoc($result));
  return $main_cat;
}


//#############################################################################
//get achievement sub category

function achieve_get_sub_category()
{
  global $sql;

  $sub_cat = array();
  $result = $sql['dbc']->query('SELECT ID, ParentID, Name FROM achievement_category WHERE ParentID != -1 ORDER BY `GroupID` ASC');
  $temp = $sql['dbc']->fetch_assoc($result);
  while ($sub_cat[$temp['ParentID']][$temp['ID']] = $temp['Name'])
  {
    $temp = $sql['dbc']->fetch_assoc($result);
  }
  return $sub_cat;
}


//#############################################################################
//get achievement details by its id

function achieve_get_details($id)
{
  global $sql;

  $result = ($sql['dbc']->query('SELECT id, name, description, reward, points FROM achievement WHERE id = \''.$id.'\' LIMIT 1'));
  $details = $sql['dbc']->fetch_assoc($result);
  return $details;
}


//#############################################################################
//get achievement icon - if icon not exists in item_icons folder D/L it from web.

function achieve_get_icon($achieveid)
{
  global $proxy_cfg, $get_icons_from_web, $item_icons, $sql;

  $result = $sql['dbc']->query('SELECT spellIcon FROM achievement WHERE id = \''.$achieveid.'\' LIMIT 1');


  if ($result)
    $displayid = $sql['dbc']->result($result, 0);
  else
    $displayid = 0;

  if ($displayid)
  {
    $result = $sql['dbc']->query('SELECT Name FROM spellicon WHERE ID = '.$displayid.' LIMIT 1');

    if($result)
    {
      $achieve = $sql['dbc']->result($result, 0);
      // this_is_junk: we now extract data unaltered from the DBCs, so the spell icons have paths,
      // we remove them.
      // The parser reads \ as starting an escape sequence, so we have to
      // escape to make \
      $temp = explode("\\", $achieve);
      // get the last field
      $achieve = $temp[count($temp) - 1];
      

      if ($achieve)
      {
        if (file_exists(''.$item_icons.'/'.$achieve.'.png'))
        {
          if (filesize(''.$item_icons.'/'.$achieve.'.png') > 349)
          {
            return ''.$item_icons.'/'.$achieve.'.png';
          }
          else
          {
            // this_is_junk: disabled deletion from DBC data
            //$sql['mgr']->query('DELETE FROM spellicon WHERE id = '.$displayid.'');
            //if (file_exists(''.$item_icons.'/'.$achieve.'.png'))
            //  unlink(''.$item_icons.'/'.$achieve.'.png');
            //$achieve = '';
          }
        }
        else
          $achieve = '';
      }
      else
        $achieve = '';
    }
    else
      $achieve = '';
  }
  else
    $achieve = '';

  // this_is_junk: yeah, this doesn't work, live with it. :)
  /*if($get_icons_from_web)
  {
    $xmlfilepath='http://www.wowhead.com/?achievement=';
    $proxy = $proxy_cfg['addr'];
    $port = $proxy_cfg['port'];

    if (empty($proxy_cfg['addr']))
    {
      $proxy = 'www.wowhead.com';
      $xmlfilepath = '?achievement=';
      $port = 80;
    }

    if ($achieve == '')
    {
      //get the icon name
      $fp = @fsockopen($proxy, $port, $errno, $errstr, 0.5);
      if ($fp);
      else
        return 'img/INV/INV_blank_32.gif';
      $out = "GET /$xmlfilepath$achieveid HTTP/1.0\r\nHost: www.wowhead.com\r\n";
      if (isset($proxy_cfg['user']))
        $out .= "Proxy-Authorization: Basic ". base64_encode ("{$proxy_cfg['user']}:{$proxy_cfg['pass']}")."\r\n";
      $out .="Connection: Close\r\n\r\n";

      $temp = '';
      fwrite($fp, $out);
      while ($fp && !feof($fp))
        $temp .= fgets($fp, 4096);
      fclose($fp);

      $wowhead_string = $temp;
      $temp_string1 = strstr($wowhead_string, 'Icon.create(');
      $temp_string2 = substr($temp_string1, 12, 50);
      $temp_string3 = strtok($temp_string2, ',');
      $temp_string4 = substr($temp_string3, 1, strlen($temp_string3) - 2);
      $achieve_icon_name = $temp_string4;

      $achieve = $achieve_icon_name;
    }

    if (file_exists(''.$item_icons.'/'.$achieve.'.jpg'))
    {
      if (filesize(''.$item_icons.'/'.$achieve.'.jpg') > 349)
      {
        $sql['mgr']->query('REPLACE INTO dbc_spellicon (id, name) VALUES (\''.$displayid.'\', \''.$achieve.'\')');
        return ''.$item_icons.'/'.$achieve.'.jpg';
      }
      else
      {
        $sql['mgr']->query('DELETE FROM dbc_spellicon WHERE id = '.$displayid.'');
        if (file_exists(''.$item_icons.'/'.$achieve.'.jpg'))
          unlink(''.$item_icons.'/'.$achieve.'.jpg');
      }
    }

    //get the icon itself
    if (empty($proxy_cfg['addr']))
    {
      $proxy = 'static.wowhead.com';
      $port = 80;
    }
    $fp = @fsockopen($proxy, $port, $errno, $errstr, 0.5);
    if ($fp);
    else
      return 'img/INV/INV_blank_32.gif';
    $iconfilename = strtolower($achieve);
    $file = 'http://static.wowhead.com/images/icons/medium/'.$iconfilename.'.jpg';
    $out = "GET $file HTTP/1.0\r\nHost: static.wowhead.com\r\n";
    if (isset($proxy_cfg['user']))
      $out .= "Proxy-Authorization: Basic ". base64_encode ("{$proxy_cfg['user']}:{$proxy_cfg['pass']}")."\r\n";
    $out .="Connection: Close\r\n\r\n";
    fwrite($fp, $out);

    //remove header
    while ($fp && !feof($fp))
    {
      $headerbuffer = fgets($fp, 4096);
      if (urlencode($headerbuffer) == '%0D%0A')
        break;
    }

    if (file_exists(''.$item_icons.'/'.$achieve.'.jpg'))
    {
      if (filesize(''.$item_icons.'/'.$achieve.'.jpg') > 349)
      {
        $sql['mgr']->query('REPLACE INTO dbc_spellicon (id, name) VALUES (\''.$displayid.'\', \''.$achieve.'\')');
        return ''.$item_icons.'/'.$achieve.'.jpg';
      }
      else
      {
        $sql['mgr']->query('DELETE FROM dbc_spellicon WHERE id = '.$displayid.'');
        if (file_exists(''.$item_icons.'/'.$achieve.'.jpg'))
          unlink(''.$item_icons.'/'.$achieve.'.jpg');
      }
    }

    $img_file = fopen(''.$item_icons.'/'.$achieve.'.jpg', 'wb');
    while (!feof($fp))
      fwrite($img_file,fgets($fp, 4096));
    fclose($fp);
    fclose($img_file);

    if (file_exists(''.$item_icons.'/'.$achieve.'.jpg'))
    {
      if (filesize(''.$item_icons.'/'.$achieve.'.jpg') > 349)
      {
        $sql['mgr']->query('REPLACE INTO dbc_spellicon (id, name) VALUES (\''.$displayid.'\', \''.$achieve.'\')');
        return ''.$item_icons.'/'.$achieve.'.jpg';
      }
      else
      {
        $sql['mgr']->query('DELETE FROM dbc_spellicon WHERE id = '.$displayid.'');
        if (file_exists(''.$item_icons.'/'.$achieve.'.jpg'))
          unlink(''.$item_icons.'/'.$achieve.'.jpg');
      }
    }
    else
      return 'img/INV/INV_blank_32.gif';
  }
  else
    return 'img/INV/INV_blank_32.gif';*/
}


?>
