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
require_once 'libs/get_uptime_lib.php';
valid_login($action_permission["view"]);

function stats($action)
{
  global $output, $realm_id, $logon_db, $server, $theme, $sql, $core;

  $race = Array
  (
    1  => array(1, 'human','',''),
    2  => array(2, 'orc','',''),
    3  => array(3, 'dwarf','',''),
    4  => array(4, 'nightelf','',''),
    5  => array(5, 'undead','',''),
    6  => array(6, 'tauren','',''),
    7  => array(7, 'gnome','',''),
    8  => array(8, 'troll','',''),
    10 => array(10,'bloodelf','',''),
    11 => array(11,'draenei','','')
  );

  $class = Array
  (
    1  => array(1, 'warrior','',''),
    2  => array(2, 'paladin','',''),
    3  => array(3, 'hunter','',''),
    4  => array(4, 'rogue','',''),
    5  => array(5, 'priest','',''),
    6  => array(6, 'death_knight','',''),
    7  => array(7, 'shaman','',''),
    8  => array(8, 'mage','',''),
    9  => array(9, 'warlock','',''),
    11 => array(11,'druid','','')
  );

  $level = Array
  (
    1 => array(1,1,9,'',''),
    2 => array(2,10,19,'',''),
    3 => array(3,20,29,'',''),
    4 => array(4,30,39,'',''),
    5 => array(5,40,49,'',''),
    6 => array(6,50,59,'',''),
    7 => array(7,60,69,'',''),
    8 => array(8,70,79,'',''),
    9 => array(9,80,80,'','')
  );
  
  function format_uptime($seconds)
  {
    $secs  = intval($seconds % 60);
    $mins  = intval($seconds / 60 % 60);
    $hours = intval($seconds / 3600 % 24);
    $days  = intval($seconds / 86400);

    $uptimeString='';

    if ( $days )
    {
      $uptimeString .= $days;
      $uptimeString .= ( ( $days === 1 ) ? ' day' : ' days' );
    }
    if ( $hours )
    {
      $uptimeString .= ( ( $days > 0 ) ? ', ' : '' ).$hours;
      $uptimeString .= ( ( $hours === 1 ) ? ' hour' : ' hours');
    }
    if ( $mins )
    {
      $uptimeString .= ( ( ( $days > 0 ) || ( $hours > 0 ) ) ? ', ' : '' ).$mins;
      $uptimeString .= ( ( $mins === 1 ) ? ' minute' : ' minutes' );
    }
    if ( $secs )
    {
      $uptimeString .= ( ( ( $days > 0 ) || ( $hours > 0 ) || ( $mins > 0 ) ) ? ', ' : '' ).$secs;
      $uptimeString .= ( ( $secs === 1 ) ? ' second' : ' seconds' );
    }
    return $uptimeString;
  }

  $total_chars = $sql["char"]->result($sql["char"]->query("SELECT COUNT(*) FROM characters".( ( $action ) ? " WHERE online=1" : "" )), 0);

  if ( $core == 1 )
  {
    $stats = get_uptime($server[$realm_id]['stats.xml']);
    $stat_uptime = explode(' ', $stats["uptime"]);
  }
  else
  {
    $up_query = "SELECT * FROM uptime WHERE realmid='".$realm_id."' ORDER BY starttime DESC LIMIT 1";
    $up_results = $sql["logon"]->query($up_query);
    $uptime = $sql["logon"]->fetch_assoc($up_results);
    $stats["uptime"] = time() - $uptime["starttime"];
    $stats["uptime"] = "    ".format_uptime($stats["uptime"]);
    $stat_uptime = explode(' ', $stats["uptime"]);
    
    $stats["peak"] = $uptime["maxplayers"];
  }

    $output .= '
          <center>
            <div class="tab">
              <ul>
                <li'.( ( $action ) ? '' : ' class="selected"' ).'>
                  <a href="stat.php">'.lang("stat", "srv_statistics").'</a>
                </li>
                <li'.( ( $action ) ? ' class="selected"' : '' ).'>
                  <a href="stat.php?action=true">'.lang("stat", "on_statistics").'</a>
                </li>
              </ul>
            </div>
            <div class="tab_content">
              <div class="top"><h1>'.( ( $action ) ? lang("stat", "on_statistics") : lang("stat", "srv_statistics") ).'</h1></div>
              <center>
                <table class="hidden">
                  <tr>
                    <td align="left">
                      <h1>'.lang("stat", "general_info").'</h1>
                    </td>
                  </tr>
                  <tr align="left">
                    <td class="large">';
    if ( $action )
      $output .= '
                      <font class="bold">'.lang("index", "tot_users_online").' : '.$total_chars.'</font><br /><br />';
    else
    {
      if ( $core == 1 )
        $query = $sql["logon"]->query("SELECT COUNT(*) FROM accounts UNION SELECT COUNT(*) FROM accounts WHERE gm<>'0'");
      elseif ( $core == 2 )
        $query = $sql["logon"]->query("SELECT COUNT(*) FROM account UNION SELECT COUNT(*) FROM account WHERE gmlevel<>'0'");
      else
        $query = $sql["logon"]->query("SELECT COUNT(*) FROM account UNION SELECT COUNT(*) FROM account_access WHERE gmlevel<>'0'");
      $total_acc = $sql["logon"]->result($query, 0);
      $total_gms = $sql["logon"]->result($query, 1);
      unset($query);

      $data = date('Y-m-d H:i:s');
      $data_1 = mktime(date('H'), date('i'), date('s'), date('m'), date('d')-1, date('Y'));
      $data_1 = date('Y-m-d H:i:s', $data_1);

      if ( $core == 1 )
        $uni_query = "SELECT DISTINCT COUNT(lastip) FROM accounts WHERE lastlogin>'".$data_1."' AND lastlogin<'".$data."'";
      else
        $uni_query = "SELECT DISTINCT COUNT(last_ip) FROM account WHERE last_login>'".$data_1."' AND last_login<'".$data."'";
      $uniqueIPs = $sql["logon"]->result($sql["logon"]->query($uni_query), 0);
      unset($data_1);
      unset($data);

      //$max_ever = $sql["mgr"]->result($sql["mgr"]->query('SELECT peakcount FROM uptime WHERE realmid = '.$realm_id.' ORDER BY peakcount DESC LIMIT 1'), 0);
      $max_restart = $stats["peak"];

      // Mangos uptime table doesn't have an uptime field. O_o
      //$uptime = $sqlr->fetch_row($sqlr->query('SELECT AVG(uptime)/60, MAX(uptime)/60, ( 100*SUM(uptime)/( UNIX_TIMESTAMP()-MIN(starttime) ) ) FROM uptime WHERE realmid = '.$realm_id.''));

      $output .= '
                      <table>
                        <tr valign="top">
                          <td align="left">
                            '.lang("stat", "max_uptime").':<br />
                            <br />
                            '.lang("stat", "tot_accounts").':<br />
                            '.lang("stat", "tot_chars_on_realm").':<br />
                          </td>
                          <td align="right">
                            '.$stat_uptime[4].'d '.$stat_uptime[6].'h '.$stat_uptime[8].'m<br />
                            <br />
                            '.$total_acc.'<br />
                             '.$total_chars.'<br />
                          </td>
                          <td>&nbsp;&nbsp;
                          </td>
                          <td align="left">
                            '.lang("stat", "unique_ip").':<br />
                            <br />
                            '.lang("stat", "max_players").' &nbsp;<br />
                            '.lang("stat", "max_restart").' :<br />
                          </td>
                          <td align="right">
                            '.$uniqueIPs.'<br />
                            <br />
                            <br />
                            '.$max_restart.'<br />
                          </td>
                        </tr>
                        <tr align="left">
                          <td colspan="2">
                            '.lang("stat", "average_of").' '.round($total_chars/$total_acc, 1).' '.lang("stat", "chars_per_acc").'<br />
                            '.lang("stat", "total_of").' '.$total_gms.' '.lang("stat", "gms_one_for").' '.round($total_acc/$total_gms, 1).' '.lang("stat", "players").'
                          </td>
                          <td colspan="2">
                          </td>
                        </tr>
                      </table>
                      <br />';
      unset($uptime);
      unset($uniqueIPs);
      unset($max_restart);
      unset($max_ever);
      unset($total_gms);
      unset($total_acc);
    }

    // get counts and percentages for alliance and horde characters
    $horde_chars  = $sql["char"]->result($sql["char"]->query("SELECT COUNT(guid) FROM characters WHERE race IN(2, 5, 6, 8, 10)".( ( $action ) ? " AND online=1" : "" )), 0);
    $horde_pros   = round(($horde_chars*100)/$total_chars, 1);
    $allies_chars = $total_chars - $horde_chars;
    $allies_pros  = 100 - $horde_pros;

    // if we have zero of both, we'll show half red, half blue
    if ( ( $horde_chars == 0 ) && ( $allies_chars == 0 ) )
    {
      $horde_pros = 50;
      $allies_pros = 50;
    }

    $output .= '
                      <table class="tot_bar">
                        <tr>
                          <td class="tot_bar_horde" style="width: '.$horde_pros.'%; height: 40px;"><a href="stat.php?action='.$action.'&amp;side=h">'.lang("stat", "horde").': '.$horde_chars.' ('.$horde_pros.'%)</a></td>
                          <td class="tot_bar_alliance" style="width: '.$allies_pros.'%; height: 40px;"><a href="stat.php?action='.$action.'&amp;side=a">'.lang("stat", "alliance").': '.$allies_chars.' ('.$allies_pros.'%)</a></td>
                        </tr>
                      </table>
                      <hr/>
                    </td>
                  </tr>';
    unset($horde_chars);
    unset($horde_pros);
    unset($allies_chars);
    unset($allies_pros);

    $order_race = ( ( isset($_GET["race"]) ) ? 'AND race='.$sql["char"]->quote_smart($_GET["race"]) : '' );
    $order_class = ( ( isset($_GET["class"]) ) ? 'AND class='.$sql["char"]->quote_smart($_GET["class"]) : '' );

    if ( isset($_GET["level"]) )
    {
      $lvl_min = $sql["char"]->quote_smart($_GET["level"]);
      $lvl_max = $lvl_min + 4;
      $order_level = 'AND level>='.$lvl_min.' AND level<='.$lvl_max.'';
    }
    else
      $order_level = '';

    if ( isset($_GET["side"]) )
    {
      if ( $sql["char"]->quote_smart($_GET["side"]) == 'h' )
        $order_side = 'AND race IN(2, 5, 6, 8, 10)';
      elseif ( $sql["char"]->quote_smart($_GET["side"]) == 'a' )
        $order_side = 'AND race IN (1, 3, 4, 7, 11)';
    }
    else
      $order_side = '';

    // RACE
    foreach ( $race as $id )
    {
      $race[$id[0]][2] = $sql["char"]->result($sql["char"]->query("SELECT COUNT(guid) FROM characters
        WHERE race=".$id[0]." ".$order_class." ".$order_level." ".$order_side.( ( $action ) ? " AND online=1" : "" )), 0);
      $race[$id[0]][3] = round((($race[$id[0]][2])*100)/$total_chars, 1);
    }
    $output .= '
                  <tr align="left">
                    <td>
                      <h1>'.lang("stat", "chars_by_race").'</h1>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table class="bargraph">
                        <tr>';
    foreach ( $race as $id )
    {
      $height = ($race[$id[0]][3])*4;
      $output .= '
                          <td>
                            <a href="stat.php?action='.$action.'&amp;race='.$id[0].'" class="graph_link">'.$race[$id[0]][3].'%<img src="themes/'.$theme.'/column.gif" width="69" height="'.$height.'" alt="'.$race[$id[0]][2].'" /></a>
                          </td>';
    }
    $output .= '
                        </tr>
                        <tr>';
    foreach ( $race as $id )
    {
      $output .= '
                          <th>'.lang("id_tab", $race[$id[0]][1]).'<br />'.$race[$id[0]][2].'</th>';
    }
    unset($race);
    $output .= '
                        </tr>
                      </table>
                      <br />
                    </td>
                  </tr>';
    // RACE END
    // CLASS
    foreach ( $class as $id )
    {
      $class[$id[0]][2] = $sql["char"]->result($sql["char"]->query("SELECT COUNT(guid) FROM characters
        WHERE class=".$id[0]." ".$order_race." ".$order_level." ".$order_side.( ( $action ) ? " AND online=1" : "" )), 0);
      $class[$id[0]][3] = round((($class[$id[0]][2])*100)/$total_chars,1);
    }
    unset($order_level);
    $output .= '
                  <tr align="left">
                    <td>
                      <h1>'.lang("stat", "chars_by_class").'</h1>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table class="bargraph">
                        <tr>';
    foreach ( $class as $id )
    {
      $height = ($class[$id[0]][3])*4;
      $output .= '
                          <td>
                            <a href="stat.php?action='.$action.'&amp;class='.$id[0].'" class="graph_link">'.$class[$id[0]][3].'%<img src="themes/'.$theme.'/column.gif" width="69" height="'.$height.'" alt="'.$class[$id[0]][2].'" /></a>
                          </td>';
    }
    $output .= '
                        </tr>
                        <tr>';
    foreach ( $class as $id )
    {
      $output .= '
                          <th>'.lang("id_tab", $class[$id[0]][1]).'<br />'.$class[$id[0]][2].'</th>';
    }
    unset($class);
    $output .= '
                        </tr>
                      </table>
                      <br />
                    </td>
                  </tr>';
    // CLASS END
    // LEVEL
    foreach ( $level as $id )
    {
      $level[$id[0]][3] = $sql["char"]->result($sql["char"]->query("SELECT COUNT(guid) FROM characters
        WHERE level>=".$id[1]." AND level<=".$id[2]."
        ".$order_race." ".$order_class." ".$order_side.( ( $action ) ? " AND online=1" : "" )), 0);
      $level[$id[0]][4] = round((($level[$id[0]][3])*100)/$total_chars,1);
    }
    unset($order_level);
    unset($order_class);
    unset($order_race);
    unset($total_chars);
    unset($order_side);
    $output .= '
                  <tr align="left">
                    <td>
                      <h1>'.lang("stat", "chars_by_level").'</h1>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table class="bargraph">
                        <tr>';
    foreach ( $level as $id )
    {
      $height = ($level[$id[0]][4])*4;
      $output .= '
                          <td><a href="stat.php?action='.$action.'&amp;level='.$id[1].'" class="graph_link">'.$level[$id[0]][4].'%<img src="themes/'.$theme.'/column.gif" width="77" height="'.$height.'" alt="'.$level[$id[0]][3].'" /></a></td>';
    }
    unset($height);
    $output .= '
                        </tr>
                        <tr>';
    foreach ( $level as $id )
      $output .= '
                          <th>'.$level[$id[0]][1].'-'.$level[$id[0]][2].'<br />'.$level[$id[0]][3].'</th>';
    unset($id);
    unset($level);
    $output .= '
                        </tr>
                      </table>
                      <br />
                      <hr/>
                    </td>
                  </tr>
                  <tr>
                    <td>';
    // LEVEL END
    makebutton(lang("stat", "reset"), 'stat.php', 720);
    $output .= '
                    </td>
                  </tr>
                </table>
              </center>
            </div>
            <br />
          </center>';

}


//#############################################################################
// MAIN
//#############################################################################
//$err = (isset($_GET["error"])) ? $_GET["error"] : NULL;

//unset($err);

$output .= '
      <div class="bubble">';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

stats($action);

unset($action);
unset($action_permission);

require_once 'footer.php';


?>
