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

require_once 'header.php';
require_once 'libs/char_lib.php';
require_once("libs/map_zone_lib.php");
valid_login($action_permission["view"]);

//########################################################################################################################
//  BROWSE CHARS
//########################################################################################################################
function browse_chars()
{
  global $output, $logon_db, $corem_db, $corem_db, $characters_db, $realm_id, $site_encoding,
    $action_permission, $user_lvl, $user_name, $showcountryflag, $itemperpage, $timezone, $sql, $core;

  //==========================$_GET and SECURE========================
  $start = ( ( isset($_GET["start"]) ) ? $sql["logon"]->quote_smart($_GET["start"]) : 0 );
  if ( !is_numeric($start) )
    $start = 0;

  $order_by = ( ( isset($_GET["order_by"]) ) ? $sql["logon"]->quote_smart($_GET["order_by"]) : 'guid' );
  if ( !preg_match('/^[_[:lower:]]{1,12}$/', $order_by) )
    $order_by = 'guid';

  $dir = ( ( isset($_GET["dir"]) ) ? $sql["logon"]->quote_smart($_GET["dir"]) : 1 );
  if ( !preg_match('/^[01]{1}$/', $dir) )
    $dir = 1;

  $order_dir = ( ( $dir ) ? "ASC" : "DESC" );
  $dir = ( ( $dir ) ? 0 : 1 ) ;
  //==========================$_GET and SECURE end========================

  if ( $order_by == "mapid" )
  {
    $order_by = 'mapid, zoneid';
    $order_hold = 'mapid';
  }
  elseif ( $order_by == "zoneid" )
  {
    $order_by = 'zoneid, mapid';
    $order_hold = 'zoneid';
  }
  else
    $order_hold = $order_by;

  switch ( $_GET["symbol"] )
  {
    case 'equal':
    {
      $symbol = '=';
      break;
    }
    case 'greater_equal':
    {
      $symbol = '>=';
      break;
    }
    case 'greater':
    {
      $symbol = '>';
      break;
    }
    case 'less_equal':
    {
      $symbol = '<=';
      break;
    }
    case 'less':
    {
      $symbol = '<';
      break;
    }
    case 'not_equal':
    {
      $symbol = '<>';
      break;
    }
  }

  $search_by = '';
  $search_value = '';
  if ( isset($_GET["search_value"]) && isset($_GET["search_by"]) )
  {
    $search_value = $sql["logon"]->quote_smart($_GET["search_value"]);
    $search_by = ( ( isset($_GET["search_by"]) ) ? $sql["logon"]->quote_smart($_GET["search_by"]) : 'name' );
    $search_menu = array('name', 'guid', 'account', 'level', 'greater_level', 'guild', 'race', 'class', 'mapid', 'highest_rank', 'greater_rank', 'online', 'gold', 'item');

    if ( !in_array($search_by, $search_menu) )
      $search_by = 'name';

    unset($search_menu);

    switch ( $search_by )
    {
      //need to get the acc id from other table since input comes as name
      case "account":
        if ( preg_match('/^[\t\v\b\f\a\n\r\\\"\'\? <>[](){}_=+-|!@#$%^&*~`.,0123456789\0]{1,30}$/', $search_value) )
          redirect("charlist.php?error=2");

        if ( $core == 1 )
          $result = $sql["logon"]->query("SELECT acct FROM accounts WHERE login LIKE '%".$search_value."%' LIMIT ".$start.", ".$itemperpage);
        else
          $result = $sql["logon"]->query("SELECT id AS acct FROM account WHERE username LIKE '%".$search_value."%' LIMIT ".$start.", ".$itemperpage);

        if ( $core == 1 )
          $where_out = " acct IN (0 ";
        else
          $where_out = " account IN (0 ";

        while ( $char = $sql["logon"]->fetch_row($result) )
        {
          $where_out .= ", ";
          $where_out .= $char[0];
        };
        $where_out .= ") ";

        unset($result);

        break;

      case "level":
        if ( !is_numeric($search_value) )
          $search_value = 1;

        $where_out = "level".$symbol.$search_value;

      break;

      case "gold":
        if ( !is_numeric($search_value) )
          $search_value = 1;

        if ( $core == 1 )
          $where_out = "gold".$symbol.$search_value;
        else
          $where_out = "money".$symbol.$search_value;

      break;

      case "guild":
        if ( preg_match('/^[\t\v\b\f\a\n\r\\\"\'\? <>[](){}_=+-|!@#$%^&*~`.,0123456789\0]{1,30}$/', $search_value) )
          redirect("charlist.php?error=2");

        if ( $core == 1 )
          $result = $sql["char"]->query("SELECT guildid FROM guilds WHERE guildname LIKE '%".$search_value."%'");
        else
          $result = $sql["char"]->query("SELECT guildid FROM guild WHERE name LIKE '%".$search_value."%'");

        $guildid = $sql["char"]->result($result, 0, 'guildid');

        if ( !$search_value )
          $guildid = 0;

        if ( $core == 1 )
          $Q1 = "SELECT playerid FROM guild_data WHERE guildid=".$guildid;
        else
          $Q1 = "SELECT guid AS playerid FROM guild_member WHERE guildid=".$guildid;

        $result = $sql["char"]->query($Q1);

        unset($guildid);
        unset($Q1);

        $where_out = "guid IN (0 ";
        while ( $char = $sql["char"]->fetch_row($result) )
        {
          $where_out .= ", ";
          $where_out .= $char[0];
        };
        $where_out .= ") ";

        unset($result);

      break;

      case "item":
        if ( !is_numeric($search_value) )
          $search_value = 0;

        if ( $core == 1 )
          $result = $sql["char"]->query("SELECT ownerguid
          FROM playeritems
          WHERE entry".$symbol."'".$search_value."'");
        elseif ( $core == 2 )
          $result = $sql["char"]->query("SELECT owner_guid AS ownerguid
          FROM character_inventory
            LEFT JOIN item_instance ON character_inventory.item=item_instance.guid
          WHERE item_template".$symbol."'".$search_value."'");
        else
          $result = $sql["char"]->query("SELECT owner_guid AS ownerguid
          FROM character_inventory
            LEFT JOIN item_instance ON character_inventory.item=item_instance.guid
          WHERE itemEntry".$symbol."'".$search_value."'");

        $where_out = "guid IN (0 ";
        while ($char = $sql["char"]->fetch_row($result))
        {
          if ( $char[0] != NULL )
          {
            $where_out .= ", ";
            $where_out .= $char[0];
          }
        };
        $where_out .= ") ";

        unset($result);

      break;

      case "highest_rank":
        if ( !is_numeric($search_value) )
          $search_value = 0;

        if ( $core == 1 )
          $where_out = "SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ';', ".(PLAYER_FIELD_LIFETIME_HONORBALE_KILLS+1)."), ';', -1)".$symbol.$search_value;
        else
          $where_out = "totalKills".$symbol.$search_value;

      break;

      case "mapid":
        if ( !is_numeric($search_value) )
          $search_value = 0;

        if ( $core == 1 )
          $where_out = "mapid".$symbol.$search_value;
        else
          $where_out = "map".$symbol.$search_value;

      break;

      case "online":
        if ( $search_value != 0 )
          $search_value = 1;
        else
          $search_value = 0;

        $where_out = "online=".$search_value;

      break;

      default:
        if ( preg_match('/^[\t\v\b\f\a\n\r\\\"\'\? <>[](){}_=+-|!@#$%^&*~`.,0123456789\0]{1,30}$/', $search_value) )
          redirect("charlist.php?error=2");

        if ( !is_numeric($search_value) )
          $where_out = $search_by." LIKE '%".$search_value."%'";
        else
          $where_out = $search_by.$symbol."'".$search_value."'";
    }

    if ( $core == 1 )
      $sql_query = "SELECT guid, name, acct, race, class, zoneid, mapid,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ';', ".(PLAYER_FIELD_LIFETIME_HONORBALE_KILLS+1)."), ';', -1) AS UNSIGNED) AS highest_rank,
        online, level, gender, timestamp
        FROM `characters`
        WHERE ".$where_out." ORDER BY ".$order_by." ".$order_dir." LIMIT ".$start.", ".$itemperpage;
    else
      $sql_query = "SELECT guid, name, account AS acct, race, class, zone AS zoneid, map AS mapid,
        totalKills AS highest_rank,
        online, level, gender, logout_time AS timestamp
        FROM `characters`
        WHERE ".$where_out." ORDER BY ".$order_by." ".$order_dir." LIMIT ".$start.", ".$itemperpage;

    $query_1 = $sql["char"]->query("SELECT COUNT(*) FROM `characters` WHERE ".$where_out);
    $query = $sql["char"]->query($sql_query);
  }
  else
  {
    $query_1 = $sql["char"]->query("SELECT COUNT(*) FROM `characters`");
    if ( $core == 1 )
      $query = $sql["char"]->query("SELECT guid, name, acct, race, class, zoneid, mapid,
        online, level, gender, timestamp,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ';', ".(PLAYER_FIELD_LIFETIME_HONORBALE_KILLS+1)."), ';', -1) AS UNSIGNED) AS highest_rank
        FROM `characters` ORDER BY ".$order_by." ".$order_dir." LIMIT ".$start.", ".$itemperpage);
    else
      $query = $sql["char"]->query("SELECT guid, name, account AS acct, race, class, zone AS zoneid, map AS mapid,
        online, level, gender, logout_time AS timestamp,
        totalKills AS highest_rank
        FROM `characters` ORDER BY ".$order_by." ".$order_dir." LIMIT ".$start.", ".$itemperpage);
  }

  $all_record = $sql["char"]->result($query_1,0);
  unset($query_1);

  $this_page = $sql["char"]->num_rows($query) or die(error(lang("global", "err_no_result")));

  //==========================top tage navigaion starts here========================
  $output .= '
        <script type="text/javascript" src="libs/js/check.js"></script>
        <center>
          <table class="top_hidden">
            <tr>
              <td>';
  // cleanup unknown working condition
  //if($user_lvl >= $action_permission["delete"])
  //              makebutton($lang_char_list["cleanup"], 'cleanup.php', 130);
  makebutton(lang("global", "back"), 'javascript:window.history.back()', 130);
  ( ( $search_by && $search_value ) ? makebutton(lang("char_list", "characters"), 'char_list.php" type="def', 130) : $output .= '' );
  $output .= '
              </td>
              <td align="right" width="25%" rowspan="2">';
  $output .= generate_pagination('char_list.php?order_by='.$order_hold.'&amp;dir='.( ($dir) ? 0 : 1 ).( ( $search_value && $search_by ) ? '&amp;symbol='.$_GET["symbol"].'&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ), $all_record, $itemperpage, $start);
  $output .= '
              </td>
            </tr>
            <tr align="left">
              <td>
                <table class="hidden">
                  <tr>
                    <td>
                      <form action="char_list.php" method="get" name="form">
                        <input type="hidden" name="error" value="3" />
                        <select name="search_by">
                          <option value="name"'.( ( $search_by == 'name' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_name").'</option>
                          <option value="guid"'.( ( $search_by == 'guid' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_id").'</option>
                          <option value="account"'.( ( $search_by == 'account' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_account").'</option>
                          <option value="level"'.( ( $search_by == 'level' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_level").'</option>
                          <option value="guild"'.( ( $search_by == 'guild' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_guild").'</option>
                          <option value="race"'.( ( $search_by == 'race' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_race_id").'</option>
                          <option value="class"'.( ( $search_by == 'class' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_class_id").'</option>
                          <option value="mapid"'.( ( $search_by == 'mapid' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_map_id").'</option>
                          <option value="highest_rank"'.( ( $search_by == 'highest_rank' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_honor_kills").'</option>
                          <option value="online"'.( ( $search_by == 'online' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_online").'</option>
                          <option value="gold"'.( ( $search_by == 'gold' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "chars_gold").'</option>
                          <option value="item"'.( ( $search_by == 'item' ) ? ' selected="selected"' : '' ).'>'.lang("char_list", "by_item").'</option>
                        </select>
                        <select name="symbol">
                          <option value="equal"'.( ( $symbol == '=' ) ? ' selected="selected"' : '' ).'>=</option>
                          <option value="greater_equal"'.( ( $symbol == '>=' ) ? ' selected="selected"' : '' ).'>&gt;=</option>
                          <option value="greater"'.( ( $symbol == '>' ) ? ' selected="selected"' : '' ).'>&gt;</option>
                          <option value="less_equal"'.( ( $symbol == '<=' ) ? ' selected="selected"' : '' ).'>&lt;=</option>
                          <option value="less"'.( ( $symbol == '<' ) ? ' selected="selected"' : '' ).'>&lt;</option>
                          <option value="not_equal"'.( ( $symbol == '<>' ) ? ' selected="selected"' : '' ).'>!=</option>
                        </select>
                        <input type="text" size="24" maxlength="50" name="search_value" value="'.$search_value.'" />
                      </form>
                    </td>
                    <td>';
  makebutton(lang("global", "search"), 'javascript:do_submit()', 80);
  $output .= '
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>';
  //==========================top tage navigaion ENDS here ========================
  $output .= '
          <form method="get" action="char_list.php" name="form1">
            <input type="hidden" name="action" value="del_char_form" />
            <input type="hidden" name="start" value="'.$start.'" />
            <table class="lined">
              <tr>
                <th width="1%"><input name="allbox" type="checkbox" value="Check All" onclick="CheckAll(document.form1);" /></th>
                <th width="1%"><a href="char_list.php?order_by=guid&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'guid' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "id").'</a></th>
                <th width="1%"><a href="char_list.php?order_by=name&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'name' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "char_name").'</a></th>
                <th width="1%"><a href="char_list.php?order_by=acct&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'acct' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "account").'</a></th>
                <th width="1%"><a href="char_list.php?order_by=race&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'race' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "race").'</a></th>
                <th width="1%"><a href="char_list.php?order_by=class&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'class' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "class").'</a></th>
                <th width="1%"><a href="char_list.php?order_by=level&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'level' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "level").'</a></th>
                <th width="10%"><a href="char_list.php?order_by=mapid&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'mapid, zoneid' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "map").'</a></th>
                <th width="10%"><a href="char_list.php?order_by=zoneid&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'zoneid, mapid' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "zone").'</a></th>
                <th width="1%"><a href="char_list.php?order_by=highest_rank&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'highest_rank' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "honor_kills").'</a></th>
                <th width="10%"><!-- a href="char_list.php?order_by=guild&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'" -->'.( ( $order_by == 'guild' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "guild").'<!-- /a --></th>
                <th width="1%"><a href="char_list.php?order_by=timestamp&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'logout_time' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "lastseen").'</a></th>
                <th width="1%"><a href="char_list.php?order_by=online&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;symbol='.$_GET["symbol"].'&amp;search_value='.$search_value : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'online' ) ? '<img src="img/arr_'.( ( $dir ) ? "dw" : "up" ).'.gif" alt="" /> ' : '' ).lang("char_list", "online").'</a></th>';

  if ( $showcountryflag )
  {
    require_once 'libs/misc_lib.php';
    $output .= '
                <th width="1%">'.lang("global", "country").'</th>';
  }

  if ( $user_lvl >= $action_permission["update"] )
  {
    $output .= '
                <th width="1%"><img src="img/arrow_switch.png" onmousemove="oldtoolTip(\''.lang("char_list", "transfer").'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" /></th>';
  }

  $output .= '
              </tr>';

  $looping = ( ( $this_page < $itemperpage ) ? $this_page : $itemperpage );

  for ( $i = 1; $i <= $looping; $i++ )
  {
    // switched to fetch_assoc because using record indexes is for morons
    $char = $sql["char"]->fetch_assoc($query, 0) or die(error(lang("global", "err_no_user")));
    // to disalow lower lvl gm to  view accounts of other GMs
    if ( $core == 1 )
      $a_query = $sql["logon"]->query("SELECT login FROM accounts WHERE acct='".$char["acct"]."'");
    else
      $a_query = $sql["logon"]->query("SELECT username as login FROM account WHERE id='".$char["acct"]."'");
    $owner_acc_name = $sql["logon"]->result($a_query, 0, 'login');

    $gm_query = $sql["mgr"]->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_acc_name."'");
    $owner_gmlvl = $sql["mgr"]->result($gm_query, 0, 'gm');
      
    $time_offset = $timezone * 3600;
      
    if ( $char["timestamp"] <> 0 )
      $lastseen = date("F j, Y @ Hi", $char["timestamp"] + $time_offset);
    else
      $lastseen = '-';

    if ( $core == 1 )
    {
      $guild_id = $sql["char"]->result($sql["char"]->query("SELECT guildid FROM guild_data WHERE playerid='".$char["guid"]."'"), 0);
      $guild_name = $sql["char"]->result($sql["char"]->query("SELECT guildName FROM guilds WHERE guildid='".$guild_id."'"));
    }
    else
    {
      $guild_id = $sql["char"]->result($sql["char"]->query("SELECT guildid FROM guild_member WHERE guid='".$char["guid"]."'"), 0);
      $guild_name = $sql["char"]->result($sql["char"]->query("SELECT name FROM guild WHERE guildid='".$guild_id."'"));
    }

    // we need the screen name here
    // but first, we need the user name
    if ( $core == 1 )
      $un_query = "SELECT * FROM accounts WHERE acct='".$char["acct"]."'";
    else
      $un_query = "SELECT * FROM account WHERE id='".$char["acct"]."'";
    $un_results = $sql["logon"]->query($un_query);
    $un = $sql["logon"]->fetch_assoc($un_results);
    $sn_query = "SELECT * FROM config_accounts WHERE Login='".$un["login"]."'";
    $sn_result = $sql["mgr"]->query($sn_query);
    $sn = $sql["mgr"]->fetch_assoc($sn_result);    

    if ( ( $user_lvl >= $owner_gmlvl ) || ( $owner_acc_name == $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
    {
      $output .= '
              <tr>
                <td>';
      if ( ( $user_lvl >= $action_permission["delete"] ) || ( $owner_acc_name == $user_name ) )
        $output .= '
                  <input type="checkbox" name="check[]" value="'.$char["guid"].'" onclick="CheckCheckAll(document.form1);" />';
      $output .= '
                </td>
                <td>'.$char["guid"].'</td>
                <td><a href="char.php?id='.$char["guid"].'">'.htmlentities($char["name"], ENT_COMPAT, $site_encoding).'</a></td>';
      if ( $sn["ScreenName"] )
        $output .= '
                <td><a href="user.php?action=edit_user&amp;error=11&amp;acct='.$char["acct"].'">'.htmlentities($sn["ScreenName"], ENT_COMPAT, $site_encoding).'</a></td>';
      else
        $output .= '
                <td><a href="user.php?action=edit_user&amp;error=11&amp;acct='.$char["acct"].'">'.htmlentities($owner_acc_name, ENT_COMPAT, $site_encoding).'</a></td>';
      $output .= '
                <td><img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" /></td>
                <td><img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" /></td>
                <td>'.char_get_level_color($char["level"]).'</td>
                <td class="small"><span onmousemove="oldtoolTip(\'MapID:'.$char["mapid"].'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()">'.get_map_name($char["mapid"]).'</span></td>
                <td class="small"><span onmousemove="oldtoolTip(\'ZoneID:'.$char["zoneid"].'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()">'.get_zone_name($char["zoneid"]).'</span></td>
                <td>'.$char["highest_rank"].'</td>
                <td class="small"><a href="guild.php?action=view_guild&amp;error=3&amp;id='.$guild_id.'">'.htmlentities($guild_name, ENT_COMPAT, $site_encoding).'</a></td>
                <td class="small">'.$lastseen.'</td>
                <td>'.( ( $char["online"] ) ? '<img src="img/up.gif" alt="" />' : '<img src="img/down.gif" alt="" />' ).'</td>';
      if ( $showcountryflag )
      {
        $country = misc_get_country_by_account($char["acct"]);
        $output .= '
                <td>'.( ( $country["code"] ) ? '<img src="img/flags/'.$country["code"].'.png" onmousemove="oldtoolTip(\''.($country["country"]).'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />' : '-').'</td>';
      }
      if ( $user_lvl >= $action_permission["update"] )
        $output .= '
                <td><a href="change_char_account.php?action=chooseacct&priority=1&char='.$char["guid"].'"><img src="img/arrow_switch.png"  /></a></td>';
      $output .= '
              </tr>';
    }
    else
    {
      $output .= '
              <tr>
                <td>*</td><td>***</td><td>***</td><td>You</td><td>Have</td><td>No</td><td class="small">Permission</td><td>to</td><td>View</td><td>this</td><td>Data</td><td>***</td><td>*</td>';
      if ( $showcountryflag )
        $output .= '<td>*</td>';
      $output .= '
              </tr>';
    }
  }
  unset($char);
  unset($result);

  $output .= '
              <tr>
                <td colspan="13" align="right" class="hidden" width="25%">';
  $output .= generate_pagination('char_list.php?order_by='.$order_by.'&amp;dir='.( ( $dir ) ? 0 : 1 ).( ( $search_value && $search_by ) ? '&amp;symbol='.$_GET["symbol"].'&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ), $all_record, $itemperpage, $start);
  $output .= '
                </td>
              </tr>
              <tr>
                <td colspan="6" align="left" class="hidden">';
  if ( ( $user_lvl >= $action_permission["delete"] ) || ( $owner_acc_name == $user_name ) )
    makebutton(lang("char_list", "del_selected_chars"), 'javascript:do_submit(\'form1\',0)" type="wrn', 220);
  $output .= '
                </td>
                <td colspan="7" align="right" class="hidden">'.lang("char_list", "tot_chars").' : '.$all_record.'</td>
              </tr>
            </table>
          </form>
        </center>';
}


//########################################################################################################################
//  DELETE CHAR
//########################################################################################################################
function del_char_form()
{
  global $output, $characters_db, $realm_id, $action_permission, $sql;

  valid_login($action_permission["delete"]);

  if ( isset($_GET["check"]) )
    $check = $_GET["check"];
  else
    redirect('char_list.php?error=1');

  $output .= '
          <center>
            <img src="img/warn_red.gif" width="48" height="48" alt="" />
              <h1>
                <font class="error">'.lang("global", "are_you_sure").'</font>
              </h1>
              <br />
              <font class="bold">'.lang("char_list", "char_ids").': ';

  $pass_array = '';
  $n_check = count($check);
  for ( $i=0; $i<$n_check; ++$i )
  {
    $name = $sql["char"]->result($sql["char"]->query('SELECT name FROM characters WHERE guid = '.$check[$i].''), 0);
    $output .= '
                <a href="char.php?id='.$check[$i].'" target="_blank">'.$name.', </a>';
    $pass_array .= '&amp;check%5B%5D='.$check[$i].'';
  }
  unset($name);
  unset($n_check);
  unset($check);

  $output .= '
                <br />'.lang("global", "will_be_erased").'
              </font>
              <br /><br />
              <table width="300" class="hidden">
                <tr>
                  <td>';
  makebutton(lang("global", "yes"), "char_list.php?action=dodel_char".$pass_array, 130);
  makebutton(lang("global", "no"), "char_list.php", 130);
  unset($pass_array);
  $output .= '
                  </td>
                </tr>
              </table>
            </center>';
}


//########################################################################################################################
//  DO DELETE CHARS
//########################################################################################################################
function dodel_char()
{
  global $output, $characters_db, $realm_id, $action_permission, $tab_del_user_characters, $sql;

  valid_login($action_permission["delete"]);

  if ( isset($_GET["check"]) )
    $check = $sql["char"]->quote_smart($_GET["check"]);
  else
    redirect('char_list.php?error=1');

  $deleted_chars = 0;
  require_once 'libs/del_lib.php';

  $n_check = count($check);
  for ( $i=0; $i<$n_check; ++$i )
  {
    if ( $check[$i] == '' )
      ;
    else
      if ( del_char($check[$i], $realm_id) )
        $deleted_chars++;
  }
  unset($n_check);
  unset($check);

  $output .= '
          <center>';
  if ( $deleted_chars )
    $output .= '
            <h1><font class="error">'.lang("char_list", "total").' <font color=blue>'.$deleted_chars.'</font> '.lang("char_list", "chars_deleted").'</font></h1>';
  else
    $output .= '
            <h1><font class="error">'.lang("char_list", "no_chars_del").'</font></h1>';
  unset($deleted_chars);
  $output .= '
            <br /><br />';
  $output .= '
            <table class="hidden">
              <tr>
                <td>';
                  makebutton(lang("char_list", "back_browse_chars"), 'char_list.php', 220);
  $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>';
}


//########################################################################################################################
// MAIN
//########################################################################################################################

$err = ( ( isset($_GET["error"]) ) ? $_GET["error"] : NULL );

$output .= '
      <div class="bubble" id="char_list_bubble">
          <div class="top">';

switch ( $err )
{
  case 1:
    $output .= '
          <h1><font class="error">'.lang("global", "empty_fields").'</font></h1>';
    break;
  case 2:
    $output .= '
          <h1><font class="error">'.lang("global", "err_no_search_passed").'</font></h1>';
    break;
  case 3:
    $output .= '
          <h1><font class="error">'.lang("char_list", "search_results").':</font></h1>';
    break;
  default:
    $output .= '
          <h1>'.lang("char_list", "browse_chars").'</h1>';
}

unset($err);

$output .= '
          </div>';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

switch ( $action )
{
  case "del_char_form":
    del_char_form();
    break;
  case "dodel_char":
    dodel_char();
    break;
  default:
    browse_chars();
}

unset($action);
unset($action_permission);

require_once("footer.php");

?>
