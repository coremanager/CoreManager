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
require_once 'libs/bb2html_lib.php';
require_once 'libs/char_lib.php';
require_once 'libs/map_zone_lib.php';
require_once 'libs/get_uptime_lib.php';
require_once 'libs/forum_lib.php';
require_once 'libs/data_lib.php';
valid_login($action_permission["view"]);

//#############################################################################
// COREMANAGER FRONT PAGE
//#############################################################################
function front()
{
  global $output, $realm_id, $world_db, $logon_db, $corem_db, $server,
    $action_permission, $user_lvl, $user_id, $site_encoding,
    $showcountryflag, $gm_online_count, $gm_online, $itemperpage,
    $hide_max_players, $hide_avg_latency, $hide_plr_latency, $hide_server_mem, $sql, $core;

  $output .= '
          <div class="top">';

//---------------------Information for Explorer Users--------------------------
if ( preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"]) )
  $msie = "<br /><center><span id='index_explorer_warning'>
             Notice: This site will NOT function correctly on Microsoft Internet Explorer.
           </span></center><br />";
else
  $msie = "";
//-----------------------------------------------------------------------------

  if ( test_port($server[$realm_id]['addr'], $server[$realm_id]['game_port']) )
  {
    if ( $core == 1 )
    {
      $stats = get_uptime($server[$realm_id]['stats.xml']);
      
      $staticUptime = ' <em>'.htmlentities(get_realm_name($realm_id), ENT_COMPAT, $site_encoding).'</em> <br />'.$stats["platform"][4].' '.$stats["platform"][5].' '.$stats["platform"][6].'<br />'.lang("index", "online").' for '.$stats["uptime"];
      $output .= '
            <div id="uptime">'.$msie.'
              <h1>
                <font id="index_realm_info">'
                  .$staticUptime;

      if ( !$hide_max_players )
        $output .= '
                  <br />'
                  .lang("index", "maxplayers").
                  ': <font id="index_realm_info_value">'
                  .$stats["peak"].'</font>';
      if ( !$hide_avg_latency )
        $output .= '
                  <br />'
                  .lang("index", "avglat").
                  ': <font id="index_realm_info_value">'
                  .$stats["avglat"].'</font>';
        $output .= '
                  <br />';
      if ( $hide_server_mem <> 0 )
      {
        if ( ( $hide_server_mem == 2 ) || ( $user_lvl == $action_permission["delete"] ) )
        {
          $output .= 
                  lang("index", "cpu").
                  ': <font id="index_realm_info_value">'
                  .$stats["cpu"].'%</font>, ';
          $output .= 
                  lang("index", "ram").
                  ': <font id="index_realm_info_value">'
                  .$stats["ram"].' MB</font>, ';
          $output .= 
                  lang("index", "threads").
                  ': <font id="index_realm_info_value">'
                  .$stats["threads"].'</font>';
        }
      }
      $output .= '
               </font>
              </h1>
            </div>';
    }
    else
    {
      $stats = $sql["logon"]->fetch_assoc($sql["logon"]->query('SELECT starttime, maxplayers FROM uptime WHERE realmid = '.$realm_id.' ORDER BY starttime DESC LIMIT 1'), 0);
      $uptimetime = time() - $stats["starttime"];

      function format_uptime($seconds)
      {
        $secs  = intval($seconds % 60);
        $mins  = intval($seconds / 60 % 60);
        $hours = intval($seconds / 3600 % 24);
        $days  = intval($seconds / 86400);
        if ( $days > 365 )
        {
          $days  = intval($seconds / 86400 % 365.24);
          $years = intval($seconds / 31556926);
        }

        $uptimeString = '';

        if ( $years )
        {
          // we have a server that has been up for over a year? O_o
          // actually, it's probably because the server didn't write a useful
          // value to the uptime table's starttime field.
          $uptimeString .= $years;
          $uptimeString .= ( ( $years == 1 ) ? ' year' : ' years' );
          if ( $days )
          {
            $uptimeString .= ( ( $years > 0 ) ? ', ' : '' ).$days;
            $uptimeString .= ( ( $days == 1 ) ? ' day' : ' days');
          }
        }
        else
        {
          if ( $days )
          {
            $uptimeString .= $days;
            $uptimeString .= ( ( $days == 1 ) ? ' day' : ' days' );
          }
        }
        if ( $hours )
        {
          $uptimeString .= ( ( $days > 0 ) ? ', ' : '' ).$hours;
          $uptimeString .= ( ( $hours == 1 ) ? ' hour' : ' hours' );
        }
        if ( $mins )
        {
          $uptimeString .= ( ( $days > 0 || $hours > 0 ) ? ', ' : '' ).$mins;
          $uptimeString .= ( ( $mins == 1) ? ' minute' : ' minutes' );
        }
        if ( $secs )
        {
          $uptimeString .= ( ( $days > 0 || $hours > 0 || $mins > 0 ) ? ', ' : '' ).$secs;
          $uptimeString .= ( ( $secs == 1 ) ? ' second' : ' seconds' );
        }
        return $uptimeString;
      }

      $staticUptime = ' <em>'.htmlentities(get_realm_name($realm_id), ENT_COMPAT, $site_encoding).'</em> ';

      if ( $stats["starttime"] <> 0 )
        $staticUptime .= '<br />'.lang("index", "online").' for '.format_uptime($uptimetime);
      else
        $staticUptime .= '<br /><span style="color:orange">The current time difference since the Unix Epoch is: <br>'.format_uptime($uptimetime).'</span><br><span style="color:red">(meaning: a minor server error has occured)</span>';

      unset($uptimetime);
      $output .= '
            <div id="uptime">'.$msie.'
              <h1>
                <font id="index_realm_info">'
                  .$staticUptime;

      if ( !$hide_max_players )
        $output .= '
                  <br />'
                  .lang("index", "maxplayers").
                  ': <font id="index_realm_info_value">'
                  .$stats["maxplayers"].'</font>';
      // this_is_junk: MaNGOS doesn't store player latency. :/
      if ( $core == 3 )
      {
        if ( !$hide_avg_latency )
        {
          $lat_query = "SELECT SUM(latency), COUNT(*) FROM characters WHERE online=1 OR logout_time>'".$stats["starttime"]."'";
          $lat_result = $sql["char"]->query($lat_query);
          $lat_fields = $sql["char"]->fetch_assoc($lat_result);
          $avglat = number_format($lat_fields["SUM(latency)"] / $lat_fields["COUNT(*)"], 3);
          
          $output .= '
                    <br />'
                    .lang("index", "avglat").
                    ': <font id="index_realm_info_value">'
                    .$avglat.'</font>';
        }
      }
      $output .= '
                </font>
              </h1>
            </div>';
      unset($stats);
      $online = true;
    }
    
    unset($staticUptime);
    //unset($stats);
    $online = true;
  }
  else
  {
    $output .= '
            <h1><font class="error">'.lang("index", "realm").' <em>'.htmlentities(get_realm_name($realm_id), ENT_COMPAT, $site_encoding).'</em> '.lang("index", "offline_or_let_high").'</font></h1>';
    $online = false;
  }

  //close the div
  $output .= '
          </div>';

  // count pending character changes
  $char_change_count = $sql["mgr"]->result($sql["mgr"]->query("SELECT COUNT(*) FROM char_changes"), 0);

  //MOTD/GM Tickets part
  $start_m = ( ( isset($_GET["start_m"]) ) ? $sql["char"]->quote_smart($_GET["start_m"]) : 0 );
  if ( is_numeric($start_m) )
    ;
  else
    $start_m = 0;

  if ( $core == 1 )
    $all_record_m = $sql["char"]->result($sql["char"]->query('SELECT COUNT(*) FROM gm_tickets WHERE deleted=0'), 0);
  elseif ( $core == 2 )
    $all_record_m = $sql["char"]->result($sql["char"]->query('SELECT COUNT(*) FROM character_ticket'), 0);
  else
    $all_record_m = $sql["char"]->result($sql["char"]->query('SELECT COUNT(*) FROM gm_tickets WHERE closed=0'), 0);

  // get our MotDs...
  $motd = "";
  $motd_result = $sql["mgr"]->query("SELECT * FROM motd WHERE Enabled<>0 AND (Target='".$user_id."' OR Target=0) ORDER BY Priority ASC");
  // if we don't get any MotDs, it'll stay empty

  if ( $user_lvl >= $action_permission["update"] )
    $output .= '
          <script type="text/javascript">
            // <![CDATA[
              answerbox.btn_ok="'.lang("global", "yes_low").'";
              answerbox.btn_cancel="'.lang("global", "no").'";
              var del_motd = "motd.php?action=delete_motd&amp;id=";
            // ]]>
          </script>';

  $output .= '
          <center>';

  $output .= '
            <table class="lined">';
  if ( $sql["mgr"]->num_rows($motd_result) > 0 )
    $output .= '
              <tr><th>'.lang("index", "motd").'</th></tr>';

  while ( $temp = $sql["mgr"]->fetch_assoc($motd_result) )
  {
    $motd = bb2html($temp["Message"])."<br /><br />";
    if ( $motd )
    {
      if ( $temp["Target"] != 0 )
        $output .= '
              <tr>
                <td align="left">'.lang("motd", "private").'</td>
              </tr>';

      $output .= '
              <tr>
                <td align="left">';
      $output .= $motd;
      $output .= '
                  <br />';
      $output .= $temp["By"];
      $output .= '
                </td>
              </tr>';

      if ( $user_lvl >= $action_permission["update"] )
        $output .= '
              <tr>
                <td align="right">
                  <img src="img/cross.png" width="12" height="12" onclick="answerBox(\''.lang("global", "delete").': &lt;font color=white&gt;'.$temp["ID"].'&lt;/font&gt;&lt;br /&gt;'.lang("global", "are_you_sure").'\', del_motd + '.$temp["ID"].');" id="index_delete_cursor" alt="" />';
      if ( $user_lvl >= $action_permission["update"] )
        $output .= '
                  <a href="motd.php?action=edit_motd&amp;error=3&amp;id='.$temp["ID"].'">
                    <img src="img/edit.png" width="14" height="14" alt="" />
                  </a>
                 </td>
                </tr>';
      $output .= '
                <th></th>';
    }
  }
  if ( $sql["mgr"]->num_rows($motd_result) )
    $output = substr($output, 0, strlen($output) - 9);

  if ( $user_lvl >= $action_permission["insert"] )
  {
    $output .= '
                <table class="lined">
                  <tr>
                    <td align="right">
                      <a href="motd.php?action=add_motd&error=4">'.lang("index", "add_motd").'</a>
                    </td>
                  </tr>
                </table>';
  }
  else
    $output .= '
            </table>';

  // show gm tickets
  $output .= '
            <br />
            <table class="lined">';
  if ( $user_lvl >= $action_permission["insert"] )
  {
    if ( $all_record_m )
    {
      $output .= '
              <th>'.lang("index", "tickets").'</th>';
      if ( $core == 1 )
        $result = $sql["char"]->query("SELECT ticketid, level, message, name, deleted,
          timestamp, gm_tickets.playerGuid, acct
          FROM gm_tickets
            LEFT JOIN characters ON characters.guid=gm_tickets.playerGuid
          ORDER BY ticketid DESC LIMIT '".$start_m."', 3");
      elseif ( $core == 2 )
        $result = $sql["char"]->query("SELECT character_tickets.guid AS ticketid, characters.level, message,
          character_tickets.name, closed AS deleted, timestamp, character_tickets.playerGuid, account AS acct
          FROM character_tickets
            LEFT JOIN characters ON characters.guid=character_tickets.playerGuid
          ORDER BY ticketid DESC LIMIT '".$start_m."', 3");
      else
        $result = $sql["char"]->query("SELECT gm_tickets.guid AS ticketid, characters.level, message,
          gm_tickets.name, closed AS deleted, timestamp, gm_tickets.playerGuid, account AS acct
          FROM gm_tickets
            LEFT JOIN characters ON characters.guid=gm_tickets.playerGuid
          ORDER BY ticketid DESC LIMIT '".$start_m."', 3");

      while ( $post = $sql["char"]->fetch_assoc($result) )
      {
        if ( !$post["deleted"] )
        {
          if ( $core == 1 )
            $login_result = $sql["logon"]->query("SELECT * FROM accounts WHERE acct='".$post["acct"]."'");
          else
            $login_result = $sql["logon"]->query("SELECT *, username AS login FROM account WHERE id='".$post["acct"]."'");
          $login = $sql["logon"]->fetch_assoc($login_result);
          $gm_result = $sql["mgr"]->query("SELECT SecurityLevel FROM config_accounts WHERE Login='".$login["login"]."'");
          $gm = $sql["mgr"]->fetch_assoc($gm_result);
          $gm = $gm["SecurityLevel"];
          if ( ( $user_lvl > 0 ) && ( ( $user_lvl >= gmlevel($gm) ) || ( $user_lvl == $action_permission["delete"] ) ) )
            $output .= '<tr>
                  <td align="left">
                    <a href="char.php?id='.$post["playerGuid"].'">
                        <span onmousemove="oldtoolTip(\''.$login["username"].' ('.id_get_gm_level($gm).')'.'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()">'.htmlentities($post["name"], ENT_COMPAT, $site_encoding).'</span>
                    </a>
                 </td>
              </tr>
              <tr>
                <td align="left">
                  <span>'.$post["message"].'</span>
                 </td>
              </tr>
              <tr>
                <td align="right">';
          $output .= lang("index", "submitted").": ".date('G:i:s m-d-Y', $post["timestamp"]);

          $output .= '
                </td>
              </tr>
              <tr>
                <td align="right">';
          if ( $user_lvl >= $action_permission["update"] )
            $output .= '
                  <a href="ticket.php?action=edit_ticket&amp;error=4&amp;id='.$post["ticketid"].'">
                    <img src="img/edit.png" width="14" height="14" alt="" />
                  </a>';
          $output .= '
                </td>
              </tr>
              <tr>
                <td class="hidden"></td>
              </tr>';
        }
      }
      if ( $online )
        $output .= '%%REPLACE_TAG%%';
      else
        $output .= '
              <tr>
                <td align="right" class="hidden">'.generate_pagination('index.php?start=0', $all_record_m, 3, $start_m, 'start_m').'</td>
              </tr>';
    }
  }
  $output .= '
            </table>';

  
  // show pending character changes
  $output .= '
            <br />
            <table class="lined">';
  if ( $user_lvl >= $action_permission["update"] )
  {
    if ( $char_change_count )
    {
      $output .= '
              <th>'.lang("index", "pendingchanges").'</th>';
      $result = $sql["mgr"]->query("SELECT * FROM char_changes");
      while ( $change = $sql["mgr"]->fetch_assoc($result) )
      {
        if ( $core == 1 )
          $change_char_query = "SELECT * FROM characters WHERE guid='".$change["guid"]."'";
        else
          $change_char_query = "SELECT *, account AS acct FROM characters WHERE guid='".$change["guid"]."'";
        $change_char = $sql["char"]->fetch_assoc($sql["char"]->query($change_char_query));

        if ( $core == 1 )
          $change_acct_query = "SELECT * FROM accounts WHERE acct='".$change_char["acct"]."'";
        else
          $change_acct_query = "SELECT *, username AS login FROM account WHERE id='".$change_char["acct"]."'";

        $change_acct = $sql["logon"]->fetch_assoc($sql["logon"]->query($change_acct_query));
        if ( isset($change["new_name"]) )
        {
          // Localization
          $namechange = lang("xname", "playerhasreq");
          $namechange = str_replace("%1", $change_acct["login"], $namechange);
          $namechange = str_replace("%2", $change_char["name"], $namechange);
          $namechange = str_replace("%3", $change["new_name"], $namechange);

          $output .= '
              <tr>
                <td align="left" class="large">
                  <span>'.$namechange.'</span>';
        }

        if ( isset($change["new_race"]) )
        {
          // Localization
          $racechange = lang("xrace", "playerhasreq");
          $racechange = str_replace("%1", $change_acct["login"], $racechange);
          $racechange = str_replace("%2", $change_char["name"], $racechange);
          $racechange = str_replace("%3", char_get_race_name($change["new_race"]), $racechange);

          $output .= '
              <tr>
                <td align="left" class="large">
                  <span>'.$racechange.'</span>';
        }

        if ( isset($change["new_acct"]) )
        {
          if ( $core == 1 )
            $new_acct_query = "SELECT login FROM accounts WHERE acct='".$change["new_acct"]."'";
          else
            $new_acct_query = "SELECT username AS login FROM account WHERE id='".$change["new_acct"]."'";
          $new_acct_result = $sql["logon"]->query($new_acct_query);
          $new_acct_result = $sql["logon"]->fetch_assoc($new_acct_result);
          $new_acct_name = $new_acct_result["login"];

          // Localization
          $acctchange = lang("xacct", "playerhasreq");
          $acctchange = str_replace("%1", $change_acct["login"], $acctchange);
          $acctchange = str_replace("%2", $change_char["name"], $acctchange);
          $acctchange = str_replace("%3", $new_acct_name, $acctchange);

          $output .= '
              <tr>
                <td align="left" class="large">
                  <span>'.$acctchange.'</span>';
        }

        if ( $change_char["online"] )
           $output .= '
                  <br />
                  <br />
                  <img src="img/aff_warn.gif" />
                  <font class="error">'.lang("xname", "online").'</font>';

        $output .= '
                </td>
              </tr>';
        if ( isset($change["new_name"]) )
          $file = 'change_char_name.php';
        elseif ( isset($change["new_race"]) )
          $file = 'change_char_race.php';
        else
          $file = 'change_char_account.php';

        $output .= '
              <tr>
                <td align="right">
                  <a href="'.$file.'?action=denied&amp;guid='.$change["guid"].'">
                    <img src="img/cross.png" width="12" height="12" alt="" />
                  </a>';
        if ( !$change_char["online"] )
          $output .= '
                  <a href="'.$file.'?action=approve&amp;guid='.$change["guid"].'">
                    <img src="img/aff_tick.png" width="14" height="14" alt="" />
                  </a>';
        $output .= '
                </td>
              </tr>
              <tr>
                <td class="hidden"></td>
              </tr>';
      }
      if ( $online )
        $output .= '%%REPLACE_TAG%%';
      else
        $output .= '
              <tr>
                <td align="right" class="hidden">'.generate_pagination('index.php?start=0', $char_change_count, 3, $start_m, 'start_m').'</td>
              </tr>';
    }
  }
  $output .= '
            </table>';

  //print online chars
  if ( $online )
  {
    //==========================$_GET and SECURE=================================
    $start = ( ( isset($_GET["start"]) ) ? $sql["char"]->quote_smart($_GET["start"]) : 0 );
    if ( is_numeric($start) )
      ;
    else
      $start = 0;

    $order_by = ( ( isset($_GET["order_by"]) ) ? $sql["char"]->quote_smart($_GET["order_by"]) : 'name' );
    if ( preg_match('/^[_[:lower:]]{1,12}$/', $order_by) )
      ;
    else
      $order_by = 'name';

    $dir = ( ( isset($_GET["dir"]) ) ? $sql["char"]->quote_smart($_GET["dir"]) : 1 );
    if ( preg_match('/^[01]{1}$/', $dir) )
      ;
    else
      $dir = 1;

    $order_dir = ( ( $dir ) ? 'ASC' : 'DESC' );
    $dir = ( ( $dir ) ? 0 : 1 );
    //==========================$_GET and SECURE end=============================

    if ( $order_by === 'mapid' )
      $order_by = 'mapid '.$order_dir.', zoneid';
    elseif ( $order_by === 'zoneid' )
      $order_by = 'zoneid '.$order_dir.', mapid';

    $order_side = '';
    if ( $user_lvl || $server[$realm_id]['both_factions'] );
    else
    {
      $result = $sql["char"]->query("SELECT race FROM characters WHERE acct = ".$user_id."
        AND SUBSTRING_INDEX(SUBSTRING_INDEX(playedtime, ' ', 2), ' ', -1) = (SELECT MAX(SUBSTRING_INDEX(SUBSTRING_INDEX(playedtime, ' ', 2), ' ', -1)) FROM characters WHERE acct = ".$user_id.") LIMIT 1");
      if ( $sql["char"]->num_rows($result) )
        $order_side = ( ( in_array($sql["char"]->result($result, 0),array(2,5,6,8,10)) ) ? " AND race IN (2,5,6,8,10) " : " AND race IN (1,3,4,7,11) " );
    }
    if ( $order_by == 'ip' )
      //this_is_junk: oops, cross referencing character & account works for me because I mix the logon & character databases :/
      //hmmm.... this will work as long as the logon & character databases are on the same MySQL server.
      $result = $sql["logon"]->query("SELECT acct, lastip FROM accounts WHERE acct=any(SELECT acct FROM ".$character_db[$realm_id]['name']."characters WHERE online=1) ORDER BY lastip ".$order_dir." LIMIT ".$start.", ".$itemperpage);
    else
    {
      if ( $core == 1 )
        $result = $sql["char"]->query("SELECT guid, name, race, class, zoneid, mapid, level, acct, gender,
                              CAST( SUBSTRING_INDEX( SUBSTRING_INDEX( data, ';', ".(PLAYER_FIELD_HONOR_CURRENCY+1)." ), ';', -1 ) AS UNSIGNED ) AS highest_rank
                              FROM characters WHERE online=1 ".$order_side." ORDER BY ".$order_by." ".$order_dir." LIMIT ".$start.", ".$itemperpage);
      elseif ( $core == 2 ) // this_is_junk: MaNGOS doesn't store player latency
        $result = $sql["char"]->query("SELECT guid, name, race, class, zone AS zoneid, map AS mapid, level, account AS acct, gender,
                              totalHonorPoints AS highest_rank
                              FROM characters WHERE online=1 ".$order_side." ORDER BY ".$order_by." ".$order_dir." LIMIT ".$start.", ".$itemperpage);
      else
        $result = $sql["char"]->query("SELECT guid, name, race, class, zone AS zoneid, map AS mapid, level, account AS acct, gender,
                              totalHonorPoints AS highest_rank, latency
                              FROM characters WHERE online=1 ".$order_side." ORDER BY ".$order_by." ".$order_dir." LIMIT ".$start.", ".$itemperpage);
    }
    $total_online = $sql["char"]->result($sql["char"]->query("SELECT count(*) FROM characters WHERE online= 1"), 0);
    $replace = '
              <tr>
                <td align="right" class="hidden">'.generate_pagination('index.php?start='.$start.'&amp;order_by='.$order_by.'&amp;dir='.( ( $dir ) ? 0 : 1 ).'', $all_record_m, 3, $start_m, 'start_m').'</td>
              </tr>';
    unset($all_record_m);
    $output = str_replace('%%REPLACE_TAG%%', $replace, $output);
    unset($replace);
    $output .= '
            <font class="bold">'.lang("index", "tot_users_online").': '.$total_online.'</font>';
    if ( $total_online )
    {
    $output .= '
            <table class="lined">
              <tr>
                <td colspan="'.(9-$showcountryflag).'" align="right" class="hidden" width="25%">';
    $output .= generate_pagination('index.php?start_m='.$start_m.'&amp;order_by='.$order_by.'&amp;dir='.( ( $dir ) ? 0 : 1 ), $total_online, $itemperpage, $start);
    $output .= '
                </td>
              </tr>
              <tr>
                <th width="15%"><a href="index.php?start='.$start.'&amp;start_m='.$start_m.'&amp;order_by=name&amp;dir='.$dir.'"'.( $order_by==='name' ? ' class="'.$order_dir.'"' : '' ).'>'.lang("index", "name").'</a></th>
                <th width="1%"><a href="index.php?start='.$start.'&amp;start_m='.$start_m.'&amp;order_by=race&amp;dir='.$dir.'"'.( $order_by==='race' ? ' class="'.$order_dir.'"' : '' ).'>'.lang("index", "race").'</a></th>
                <th width="1%"><a href="index.php?start='.$start.'&amp;start_m='.$start_m.'&amp;order_by=class&amp;dir='.$dir.'"'.( $order_by==='class' ? ' class="'.$order_dir.'"' : '' ).'>'.lang("index", "class").'</a></th>
                <th width="5%"><a href="index.php?start='.$start.'&amp;start_m='.$start_m.'&amp;order_by=level&amp;dir='.$dir.'"'.( $order_by==='level' ? ' class="'.$order_dir.'"' : '' ).'>'.lang("index", "level").'</a></th>
                <th width="1%"><a href="index.php?start='.$start.'&amp;start_m='.$start_m.'&amp;order_by=highest_rank&amp;dir='.$dir.'"'.( $order_by==='highest_rank' ? ' class="'.$order_dir.'"' : '' ).'>'.lang("index", "rank").'</a></th>
                <th width="15%"><a href="index.php?start='.$start.'&amp;start_m='.$start_m.'&amp;order_by=gname&amp;dir='.$dir.'"'.( $order_by==='gname' ? ' class="'.$order_dir.'"' : '' ).'>'.lang("index", "guild").'</a></th>
                <th width="20%"><a href="index.php?start='.$start.'&amp;start_m='.$start_m.'&amp;order_by=mapid&amp;dir='.$dir.'"'.( $order_by==='mapid '.$order_dir.', zoneid' ? ' class="'.$order_dir.'"' : '' ).'>'.lang("index", "map").'</a></th>
                <th width="25%"><a href="index.php?start='.$start.'&amp;start_m='.$start_m.'&amp;order_by=zoneid&amp;dir='.$dir.'"'.( $order_by==='zoneid '.$order_dir.', mapid' ? ' class="'.$order_dir.'"' : '' ).'>'.lang("index", "zone").'</a></th>';
    if ( $core == 1 )
      $output .= '
                <th width="25%">'.lang("index", "area").'</a></th>';
    
    // this_is_junk: MaNGOS doesn't store player latency
    if ( $core != 2 )
    {
      if ( !$hide_plr_latency )
      {
        $output .= '
                  <th width="1%">'.lang("index", "latency").'</th>';
      }
    }

    if ( $showcountryflag )
    {
      require_once 'libs/misc_lib.php';
      $output .= '
                <th width="1%">'.lang("global", "country").'</th>';
    }

    $output .= '
              </tr>';
    }

    while ( $char = $sql["char"]->fetch_assoc($result) )
    {
      if ( $order_by == 'ip' )
      {
        $temp = $sql["char"]->fetch_assoc($sql["char"]->query("SELECT guid, name, race, class, zoneid, mapid, level, acct, gender FROM characters WHERE online=1 ".$order_side." AND acct=".$char["id"]));
        $char = $temp;
      }

      if ( $core == 1 )
        $ca_query = "SELECT accounts.login AS name FROM `".$logon_db["name"]."`.accounts LEFT JOIN `".$corem_db["name"]."`.config_accounts ON accounts.login = `".$corem_db["name"]."`.config_accounts.Login WHERE acct='".$char["acct"]."'";
      else
        $ca_query = "SELECT *, username AS name FROM `".$logon_db["name"]."`.account LEFT JOIN `".$corem_db["name"]."`.config_accounts ON account.username = `".$corem_db["name"]."`.config_accounts.Login WHERE id='".$char["acct"]."'";
        
      $ca_result = $sql["mgr"]->query($ca_query);
      $char_acct = $sql["mgr"]->fetch_assoc($ca_result);

      $gm = $char_acct["SecurityLevel"];
      if ( !isset($gm) )
        $gm = 0;
	
	    if ( $core == 1 )
        $guild_id = $sql["char"]->result($sql["char"]->query("SELECT guildid FROM guild_data WHERE playerid='".$char["guid"]."'"), 0);
      else
        $guild_id = $sql["char"]->result($sql["char"]->query("SELECT guildid FROM guild_member WHERE guid='".$char["guid"]."'"), 0);
      
      if ( $core == 1 )
        $guild_name_query = "SELECT guildName FROM guilds WHERE guildid='".$guild_id."'";
      else
        $guild_name_query = "SELECT name AS guildName FROM guild WHERE guildid='".$guild_id."'";
        
      $guild_name_result = $sql["char"]->query($guild_name_query);
      $guild_name = $sql["char"]->fetch_assoc($guild_name_result);
      $guild_name = $guild_name["guildName"];

      $output .= '
              <tr>
                <td>';
      if ( ( $user_lvl > 0 ) && ( ( $user_lvl >= gmlevel($gm) ) || ( $user_lvl == $action_permission["delete"] ) ) )
        $output .= '
                  <a href="char.php?id='.$char["guid"].'">
                    <span onmousemove="oldtoolTip(\''.$char_acct["name"].' ('.id_get_gm_level($gm).')'.'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()">'.htmlentities($char["name"], ENT_COMPAT, $site_encoding).'</span>
                  </a>';
      else
        $output .='
                  <span>'.htmlentities($char["name"], ENT_COMPAT, $site_encoding).'</span>';
      $output .= '
                  </td>
                  <td>
                    <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                  </td>
                  <td>
                    <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                  </td>
                  <td>'.char_get_level_color($char["level"]).'</td>
                  <td>
                    <span onmouseover="oldtoolTip(\''.char_get_pvp_rank_name($char["highest_rank"], char_get_side_id($char["race"])).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" id="index_delete_cursor"><img src="img/ranks/rank'.char_get_pvp_rank_id($char["highest_rank"], char_get_side_id($char["race"])).'.gif" alt="" /></span>
                  </td>
                  <td>
                    <a href="guild.php?action=view_guild&amp;error=3&amp;id='.$guild_id.'">'.htmlentities($guild_name, ENT_COMPAT, $site_encoding).'</a>
                  </td>
                  <td><span onmousemove="oldtoolTip(\'MapID:'.$char["mapid"].'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()">'.get_map_name($char["mapid"]).'</span></td>
                  <td><span onmousemove="oldtoolTip(\'ZoneID:'.$char["zoneid"].'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()">'.get_zone_name($char["zoneid"]).'</span></td>';
      // display player area, if available
      if ( $core == 1 )
      {
        for ( $i = 0; $i < count($stats["plrs_area"]); $i++ )
        {
          if ($stats["plrs_area"][$i][0] == $char["name"])
          {
            $output .= '
                  <td><span onmousemove="toolTip(\'AreaID:'.$stats["plrs_area"][$i][1].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_zone_name($stats["plrs_area"][$i][1]).'</span></td>';
          }
          if ( !isset( $stats["plrs_lat"][$i][1] ) )
            $output .= '
              <td>-</td>';
        }
      }
      
      // display player latency, if enabled, and if available
      if ( !$hide_plr_latency )
      {
        if ( $core == 1 )
        {
          for ( $i = 0; $i < count($stats["plrs_lat"]); $i++ )
          {
            if ( $stats["plrs_lat"][$i][0] == $char["name"] )
            {
              $output .= '
                <td>'.$stats["plrs_lat"][$i][1].'</td>';
            }
            if ( !isset($stats["plrs_lat"][$i][1]) )
              $output .= '
                <td>-</td>';
          }
        }
        else
        {
          // this_is_junk: MaNGOS doesn't store player latency
          if ( $core == 3 )
            $output .= '
                <td>'.$char["latency"].'</td>';
        }
      }

      if ( $showcountryflag )
      {
        $country = misc_get_country_by_account($char["acct"]);
        $output .='
                <td>'.( ( $country["code"] ) ? '<img src="img/flags/'.$country["code"].'.png" onmousemove="oldtoolTip(\''.($country["country"]).( ( $user_lvl >= $action_permission["update"] ) ? '<br />'.$country["actualip"] : '' ).'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />' : '-' ).'</td>';
      }
      $output .='
              </tr>';
    }
    $output .= '
              <tr>';
    $output .= '
                <td colspan="'.(9-$showcountryflag).'" align="right" class="hidden" width="25%">';
    $output .= generate_pagination('index.php?start_m='.$start_m.'&amp;order_by='.$order_by.'&amp;dir='.( ( $dir ) ? 0 : 1 ), $total_online, $itemperpage, $start);
    unset($total_online);
    $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>';
  }

}

//#############################################################################
// MAIN
//#############################################################################

$output .= "
        <div class=\"bubble\">";

front();

unset($action_permission);

require_once 'footer.php';


?>
