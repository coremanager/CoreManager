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
require_once 'libs/char_lib.php';
require_once 'libs/map_zone_lib.php';
valid_login($action_permission['view']);

//########################################################################################################################
// SHOW CHARACTERS ACHIEVEMENTS
//########################################################################################################################
function char_friends()
{
  global $output,
    $realm_id, $logon_db, $arcm_db, $characters_db,
    $action_permission, $user_lvl, $user_name, $sql, $core;

  if (empty($_GET['id']))
    error(lang('global', 'empty_fields'));
  else
    $id = $_GET['id'];

  // this is multi realm support, as of writing still under development
  //  this page is already implementing it
  if (empty($_GET['realm']))
    $realmid = $realm_id;
  else
  {
    $realmid = $sql['logon']->quote_smart($_GET['realm']);
    if (is_numeric($realmid))
      $sql['char']->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
    else
      $realmid = $realm_id;
  }

  //==========================$_GET and SECURE========================
  if (is_numeric($id)); else $id = 0;

  $order_by = (isset($_GET['order_by'])) ? $sql['char']->quote_smart($_GET['order_by']) : 'name';
  if (preg_match('/^[[:lower:]]{1,6}$/', $order_by)); else $order_by = 'name';

  $dir = (isset($_GET['dir'])) ? $sql['char']->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir = 1;

  $order_dir = ($dir) ? 'ASC' : 'DESC';
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end========================

  if ($order_by === 'map')
    $order_by = 'map '.$order_dir.', zone';
  elseif ($order_by === 'zone')
    $order_by = 'zone '.$order_dir.', map';

  // getting character data from database
  if ( $core == 1 )
    $result = $sql['char']->query('SELECT acct, name, race, class, level, gender
      FROM characters WHERE guid='.$id.' LIMIT 1');
  else
    $result = $sql['char']->query('SELECT account AS acct, name, race, class, level, gender
      FROM characters WHERE guid='.$id.' LIMIT 1');

  if ($sql['char']->num_rows($result))
  {
    $char = $sql['char']->fetch_assoc($result);

    // we get user permissions first
    $owner_acc_id = $sql['char']->result($result, 0, 'acct');

    if ( $core == 1 )
      $result = $sql['logon']->query("SELECT login FROM accounts WHERE acct='".$char['acct']."'");
    else
      $result = $sql['logon']->query("SELECT username AS login FROM account WHERE id='".$char['acct']."'");

    $owner_name = $sql['logon']->result($result, 0, 'login');
      
    $sec_res = $sql['mgr']->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_name."'");
    $owner_gmlvl = $sql['mgr']->result($sec_res, 0, 'gm');

    if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name)||($user_lvl == gmlevel('4')))
    {
      //------------------------Character Tabs---------------------------------
      // we start with a lead of 10 spaces,
      //  because last line of header is an opening tag with 8 spaces
      //  keep html indent in sync, so debuging from browser source would be easy to read
      $output .= '
          <center>
            <script type="text/javascript">
              // <![CDATA[
                function wrap()
                {
                  if (getBrowserWidth() > 1024)
                  document.write(\'</table></td><td><table class="lined" id="ch_fri_large_screen">\');
                }
              // ]]>
            </script>
            <div id="tab">
              <ul>
                <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'char_sheet').'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'inventory').'</a></li>
                '.(($char['level'] < 10) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'talents').'</a></li>').'
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'achievements').'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'quests').'</a></li>
                <li id="selected"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'friends').'</a></li>
                <li><a href="char_view.php?id='.$id.'&amp;realm='.$realmid.'">'.lang('char', 'view').'</a></li>
              </ul>
            </div>
            <div id="tab_content">
              <font class="bold">
                '.htmlentities($char['name']).' -
                <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif"
                  onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                <img src="img/c_icons/'.$char['class'].'.gif"
                  onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /> - '.lang('char', 'level_short').char_get_level_color($char['level']).'
              </font>
              <br /><br />
              <table class="hidden"  id="ch_fri_unk_1">
                <tr valign="top">
                  <td>
                    <table class="lined" id="ch_fri_unk_2">';

      // get friends
      if ( $core == 1 )
        $result = $sql['char']->query("SELECT name, race, class, mapid, zoneid, level, gender, online, acct, guid
          FROM characters WHERE guid IN (SELECT friend_guid FROM social_friends WHERE character_guid='".$id."') ORDER BY '".$order_by."' '".$order_dir."'");
      else
        $result = $sql['char']->query("SELECT name, race, class, map AS mapid, zone AS zoneid, level, gender, online, account AS acct, guid
          FROM characters WHERE guid IN (SELECT friend FROM character_social WHERE guid='".$id."' AND flags=1) ORDER BY '".$order_by."' '".$order_dir."'");

      if ($sql['char']->num_rows($result))
      {
        $output .= '
                      <tr>
                        <th colspan="7" align="left">'.lang('char', 'friends').'</th>
                      </tr>
                      <tr>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=name&amp;dir='.$dir.'"'.($order_by==='name' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'name').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=race&amp;dir='.$dir.'"'.($order_by==='race' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'race').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=class&amp;dir='.$dir.'"'.($order_by==='class' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'class').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=level&amp;dir='.$dir.'"'.($order_by==='level' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'level').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=mapid&amp;dir='.$dir.'"'.($order_by==='map '.$order_dir.', zone' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'map').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=zoneid&amp;dir='.$dir.'"'.($order_by==='zone '.$order_dir.', map' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'zone').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=online&amp;dir='.$dir.'"'.($order_by==='online' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'online').'</a></th>
                      </tr>';
        while ($data = $sql['char']->fetch_assoc($result))
        {
          if ( $core == 1 )
            $char_owner = $sql['logon']->result($sql['logon']->query("SELECT login FROM accounts WHERE acct='".$data['acct']."'"), 0, 'gmlevel');
          else
            $char_owner = $sql['logon']->result($sql['logon']->query("SELECT username AS login FROM account WHERE id='".$data['acct']."'"), 0, 'login');

          $char_gm_level = $sql['mgr']->result($sql['mgr']->query("SELECT SecurityLevel AS gmlevel FROM config_accounts WHERE Login='".$char_owner."'"), 0, 'gmlevel');

          $output .= '
                      <tr>
                        <td>';
          if ($user_lvl >= $char_gm_level)
            $output .= '<a href="char.php?id='.$data['guid'].'">'.$data['name'].'</a>';
          else
            $output .=$data['name'];
          $output .='</td>
                        <td><img src="img/c_icons/'.$data['race'].'-'.$data['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($data['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td><img src="img/c_icons/'.$data['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($data['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td>'.char_get_level_color($data['level']).'</td>
                        <td class="small"><span onmousemove="toolTip(\'MapID:'.$data['mapid'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_map_name($data['mapid']).'</span></td>
                        <td class="small"><span onmousemove="toolTip(\'ZoneID:'.$data['zoneid'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_zone_name($data['zoneid']).'</span></td>
                        <td>'.(($data['online']) ? '<img src="img/up.gif" alt="" />' : '<img src="img/down.gif" alt="" />').'</td>
                      </tr>';
        }
      }

      // get is friend of
      if ( $core == 1 )
        $result = $sql['char']->query("SELECT name, race, class, mapid, zoneid, level, gender, online, acct, guid
          FROM characters WHERE guid IN (SELECT character_guid FROM social_friends WHERE friend_guid = '".$id."') ORDER BY '".$order_by."' '".$order_dir."'");
      else
        $result = $sql['char']->query("SELECT name, race, class, map AS mapid, zone AS zoneid, level, gender, online, account AS acct, guid
          FROM characters WHERE guid IN (SELECT friend FROM character_social WHERE guid = '".$id."' AND flags=1) ORDER BY '".$order_by."' '".$order_dir."'");

      if ($sql['char']->num_rows($result))
      {
        $output .= '
                      <tr>
                        <th colspan="7" align="left">'.lang('char', 'friendof').'</th>
                      </tr>
                      <tr>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=name&amp;dir='.$dir.'"'.($order_by==='name' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'name').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=race&amp;dir='.$dir.'"'.($order_by==='race' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'race').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=class&amp;dir='.$dir.'"'.($order_by==='class' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'class').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=level&amp;dir='.$dir.'"'.($order_by==='level' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'level').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=mapid&amp;dir='.$dir.'"'.($order_by==='map '.$order_dir.', zone' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'map').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=zoneid&amp;dir='.$dir.'"'.($order_by==='zone '.$order_dir.', map' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'zone').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=online&amp;dir='.$dir.'"'.($order_by==='online' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'online').'</a></th>
                      </tr>';
        while ($data = $sql['char']->fetch_assoc($result))
        {
          if ( $core == 1 )
            $char_owner = $sql['logon']->result($sql['logon']->query("SELECT login FROM accounts WHERE acct='".$data['acct']."'"), 0, 'gmlevel');
          else
            $char_owner = $sql['logon']->result($sql['logon']->query("SELECT username AS login FROM account WHERE id='".$data['acct']."'"), 0, 'login');

          $char_gm_level = $sql['mgr']->result($sql['mgr']->query("SELECT SecurityLevel AS gmlevel FROM config_accounts WHERE Login='".$char_owner."'"), 0, 'gmlevel');

          $output .= '
                      <tr>
                        <td>';
          if ($user_lvl >= $char_gm_level)
            $output .= '<a href="char.php?id='.$data['guid'].'">'.$data['name'].'</a>';
          else
            $output .=$data['name'];
          $output .='</td>
                        <td><img src="img/c_icons/'.$data['race'].'-'.$data['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($data['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td><img src="img/c_icons/'.$data['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($data['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td>'.char_get_level_color($data['level']).'</td>
                        <td class="small"><span onmousemove="toolTip(\'MapID:'.$data['mapid'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_map_name($data['mapid']).'</span></td>
                        <td class="small"><span onmousemove="toolTip(\'ZoneID:'.$data['zoneid'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_zone_name($data['zoneid']).'</span></td>
                        <td>'.(($data['online']) ? '<img src="img/up.gif" alt="" />' : '<img src="img/down.gif" alt="" />').'</td>
                      </tr>';
        }
      }

      $output .= '
                      <script type="text/javascript">
                        // <![CDATA[
                          wrap();
                        // ]]>
                      </script>';

      // get ignores
      if ( $core == 1 )
        $result = $sql['char']->query("SELECT name, race, class, mapid, zoneid, level, gender, online, acct, guid
          FROM characters WHERE guid IN (SELECT ignore_guid FROM social_ignores WHERE character_guid = '".$id."') ORDER BY '".$order_by."' '".$order_dir."'");
      else
        $result = $sql['char']->query("SELECT name, race, class, map AS mapid, zone AS zoneid, level, gender, online, account AS acct, guid
          FROM characters WHERE guid IN (SELECT friend FROM character_social WHERE guid = '".$id."' AND flags=2) ORDER BY '".$order_by."' '".$order_dir."'");

      if ($sql['char']->num_rows($result))
      {
        $output .= '
                      <tr>
                        <th colspan="7" align="left">'.lang('char', 'ignored').'</th>
                      </tr>
                      <tr>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=name&amp;dir='.$dir.'"'.($order_by==='name' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'name').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=race&amp;dir='.$dir.'"'.($order_by==='race' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'race').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=class&amp;dir='.$dir.'"'.($order_by==='class' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'class').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=level&amp;dir='.$dir.'"'.($order_by==='level' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'level').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=mapid&amp;dir='.$dir.'"'.($order_by==='mapid '.$order_dir.', zone' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'map').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=zoneid&amp;dir='.$dir.'"'.($order_by==='zoneid '.$order_dir.', map' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'zone').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=online&amp;dir='.$dir.'"'.($order_by==='online' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'online').'</a></th>
                      </tr>';
        while ($data = $sql['char']->fetch_assoc($result))
        {
          if ( $core == 1 )
            $char_owner = $sql['logon']->result($sql['logon']->query("SELECT login FROM accounts WHERE acct='".$data['acct']."'"), 0, 'gmlevel');
          else
            $char_owner = $sql['logon']->result($sql['logon']->query("SELECT username AS login FROM account WHERE id='".$data['acct']."'"), 0, 'login');

          $char_gm_level = $sql['mgr']->result($sql['mgr']->query("SELECT SecurityLevel AS gmlevel FROM config_accounts WHERE Login='".$char_owner."'"), 0, 'gmlevel');

          $output .= '
                      <tr>
                        <td>';
          if ($user_lvl >= $char_gm_level)
            $output .= '<a href="char.php?id='.$data['guid'].'">'.$data['name'].'</a>';
          else
            $output .=$data['name'];
          $output .='</td>
                        <td><img src="img/c_icons/'.$data['race'].'-'.$data['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($data['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td><img src="img/c_icons/'.$data['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($data['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td>'.char_get_level_color($data['level']).'</td>
                        <td class="small"><span onmousemove="toolTip(\'MapID:'.$data['mapid'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_map_name($data['mapid']).'</span></td>
                        <td class="small"><span onmousemove="toolTip(\'ZoneID:'.$data['zoneid'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_zone_name($data['zoneid']).'</span></td>
                        <td>'.(($data['online']) ? '<img src="img/up.gif" alt="" />' : '<img src="img/down.gif" alt="" />').'</td>
                      </tr>';
        }
      }

      // get ignored by
      if ( $core == 1 )
        $result = $sql['char']->query("SELECT name, race, class, mapid, zoneid, level, gender, online, acct, guid
          FROM characters WHERE guid IN (SELECT ignore_guid FROM social_ignores WHERE character_guid = '".$id."') ORDER BY '".$order_by."' '".$order_dir."'");
      else
        $result = $sql['char']->query("SELECT name, race, class, map AS mapid, zone AS zoneid, level, gender, online, account AS acct, guid
          FROM characters WHERE guid IN (SELECT friend FROM character_social WHERE guid = '".$id."' AND flags=2) ORDER BY '".$order_by."' '".$order_dir."'");

      if ($sql['char']->num_rows($result))
      {
        $output .= '
                      <tr>
                        <th colspan="7" align="left">'.lang('char', 'ignoredby').'</th>
                      </tr>
                      <tr>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=name&amp;dir='.$dir.'"'.($order_by==='name' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'name').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=race&amp;dir='.$dir.'"'.($order_by==='race' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'race').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=class&amp;dir='.$dir.'"'.($order_by==='class' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'class').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=level&amp;dir='.$dir.'"'.($order_by==='level' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'level').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=mapid&amp;dir='.$dir.'"'.($order_by==='map '.$order_dir.', zone' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'map').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=zoneid&amp;dir='.$dir.'"'.($order_by==='zone '.$order_dir.', map' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'zone').'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=online&amp;dir='.$dir.'"'.($order_by==='online' ? ' class="'.$order_dir.'"' : '').'>'.lang('char', 'online').'</a></th>
                      </tr>';
        while ($data = $sql['char']->fetch_assoc($result))
        {
          if ( $core == 1 )
            $char_owner = $sql['logon']->result($sql['logon']->query("SELECT login FROM accounts WHERE acct='".$data['acct']."'"), 0, 'gmlevel');
          else
            $char_owner = $sql['logon']->result($sql['logon']->query("SELECT username AS login FROM account WHERE id='".$data['acct']."'"), 0, 'login');

          $char_gm_level = $sql['mgr']->result($sql['mgr']->query("SELECT SecurityLevel AS gmlevel FROM config_accounts WHERE Login='".$char_owner."'"), 0, 'gmlevel');

          $output .= '
                      <tr>
                        <td>';
          if ($user_lvl >= $char_gm_level)
            $output .= '<a href="char.php?id='.$data['guid'].'">'.$data['name'].'</a>';
          else
            $output .=$data['name'];
          $output .='</td>
                        <td><img src="img/c_icons/'.$data['race'].'-'.$data['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($data['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td><img src="img/c_icons/'.$data['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($data['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td>'.char_get_level_color($data['level']).'</td>
                        <td class="small"><span onmousemove="toolTip(\'MapID:'.$data['mapid'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_map_name($data['mapid']).'</span></td>
                        <td class="small"><span onmousemove="toolTip(\'ZoneID:'.$data['zoneid'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_zone_name($data['zoneid']).'</span></td>
                        <td>'.(($data['online']) ? '<img src="img/up.gif" alt="" />' : '<img src="img/down.gif" alt="" />').'</td>
                      </tr>';
        }
      }
      $output .= '
                    </table>
                  </td>';
      //---------------Page Specific Data Ends here----------------------------
      //---------------Character Tabs Footer-----------------------------------
      $output .= '
                </tr>
              </table>
            </div>
            <br />
            <table class="hidden">
              <tr>
                <td>';
                  // button to user account page, user account page has own security
                  makebutton(lang('char', 'chars_acc'), 'user.php?action=edit_user&amp;id='.$owner_acc_id.'', 130);
      $output .= '
                </td>
                <td>';

      // only higher level GM with delete access can edit character
      //  character edit allows removal of character items, so delete permission is needed
      if ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) )
      {
                  //makebutton($lang_char['edit_button'], 'char_edit.php?id='.$id.'&amp;realm='.$realmid.'', 130);
        $output .= '
                </td>
                <td>';
      }
      // only higher level GM with delete access, or character owner can delete character
      if ( ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) ) || ($owner_name === $user_name) )
      {
                  makebutton(lang('char', 'del_char'), 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 130);
        $output .= '
                </td>
                <td>';
      }
      // only GM with update permission can send mail, mail can send items, so update permission is needed
      if ($user_lvl >= $action_permission['update'])
      {
                  makebutton(lang('char', 'send_mail'), 'mail.php?type=ingame_mail&amp;to='.$char['name'].'', 130);
        $output .= '
                </td>
                <td>';
      }
                  makebutton(lang('global', 'back'), 'javascript:window.history.back()" type="def', 130);
      $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>
          <!-- end of char_friends.php -->';
    }
    else
      error(lang('char', 'no_permission'));
  }
  else
    error(lang('char', 'no_char_found'));

}


//########################################################################################################################
// MAIN
//########################################################################################################################

//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

//$lang_char = lang_char();

$output .= "
      <div class=\"bubble\">";

char_friends();

//unset($action);
unset($action_permission);
//unset($lang_char);

require_once 'footer.php';


?>
