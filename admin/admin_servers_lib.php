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

function servers()
{
  global $output, $corem_db, $get_icon_type, $get_timezone_type, $core;

  // we need $core to be set
  if ( $core == 0 )
    $core = detectcore();

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  $result = $sqlm->query("SELECT * FROM config_servers");

  $server_action = 0;
  if ( isset($_GET["editserver"]) )
    $server_action = "edit";
  if ( isset($_GET["delserver"]) )
    $server_action = "del";
  if ( isset($_GET["addserver"]) )
    $server_action = "add";

  if ( !$server_action )
  {
    $output .= '
        <center>
          <span style="color:red">'.lang("admin", "server_warn").'</span>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="servers" />
            <table class="simple" id="admin_servers">
              <tr>
                <th width="5%">'.lang("admin", "edit").'</th>
                <th width="5%">'.lang("admin", "remove").'</th>
                <th width="10%">'.lang("admin", "realm").'</th>
                <th width="10%">'.lang("admin", "name").'</th>
                <th width="20%">'.lang("admin", "hosti").'</th>
                <th width="20%">'.lang("admin", "hostp").'</th>
                <th width="1%">'.lang("admin", "port").'</th>
                <th width="10%">'.lang("admin", "icon").'</th>
                <th width="10%">'.lang("admin", "timezone").'</th>
                <th width="10%">'.lang("admin", "bothfactions").'</th>';
    if ( $core == 1 )
      $output .= '
                <th width="40%">'.lang("admin", "statsxml").'</th>';
    $output .= '
              </tr>';
    $color = "#EEEEEE";
    while ( $server = $sqlm->fetch_assoc($result) )
    {
      $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=servers&amp;sel_server='.$server["Index"].'&amp;editserver=editserver">
                      <img src="img/edit.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=servers&amp;sel_server='.$server["Index"].'&amp;delserver=deleteserver">
                      <img src="img/aff_cross.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$server["Index"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$server["Name"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$server["Address"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$server["External_Address"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$server["Port"].'</center>
                </td>';

      $icon = $get_icon_type[$server["Icon"]];
      $timezone = $get_timezone_type[$server["Timezone"]];
      $output .= '
                <td style="background-color:'.$color.'">
                  <center>'.lang("realm", $icon[1]).'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.lang("realm", $timezone[1]).'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.( ( $server["Both_Factions"] ) ? lang("global", "yes_low") : lang("global", "no_low") ).'</center>
                </td>';
      if ( $core == 1 )
        $output .= '
                <td style="background-color:'.$color.'">
                  <center>'.$server["Stats_XML"].'</center>
                </td>';
      $output .= '
              </tr>';

      $color = ( ( $color == "#EEEEEE" ) ? "#FFFFFF" : "#EEEEEE" );
    }
    $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <a href="admin.php?section=servers&amp;addserver=addserver">
                    <img src="img/add.png" alt="" />
                  </a>
                </td>
                <td style="background-color:'.$color.'" colspan="'.( ( $core == 1 ) ? '10' : '9' ).'">
                  <a href="admin.php?section=servers&amp;addserver=addserver">'.lang("admin", "addserver").'</a>
                </td>
              </tr>
            </table>
            <!-- input type="submit" name="editserver" value="'.lang("admin", "editserver").'">
            <input type="submit" name="addserver" value="'.lang("admin", "addserver").'">
            <input type="submit" name="delserver" value="'.lang("admin", "delserver").'" -->
          </form>
        </center>';
  }
  else
  {
    if ( $server_action == "edit" )
    {
      $server_id = $sqlm->quote_smart($_GET["sel_server"]);
      if ( is_numeric($server_id) )
      {
        $server = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_servers WHERE `Index`='".$server_id."'"));
        $output .= '
        <center>
          <form name="form" action="admin.php" method="get">
            <fieldset id="admin_edit_server">
              <input type="hidden" name="section" value="servers" />
              <input type="hidden" name="action" value="saveserver" />
              <input type="hidden" name="index" value="'.$server["Index"].'" />
              <table>
                <tr>
                  <td width="45%">'.lang("admin", "realm").': </td>
                  <td>
                    <input type="text" name="new_index" value="'.$server["Index"].'" />
                  </td>
                </tr>
                <tr>
                  <td width="45%">'.lang("admin", "name").': </td>
                  <td>
                    <input type="text" name="server_name" value="'.$server["Name"].'" />
                  </td>
                </tr>
                <tr>
                  <td width="45%" class="help">
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "hosti_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "hosti").'</a>:
                  </td>
                  <td>
                    <input type="text" name="server_hosti" value="'.$server["Address"].'" />
                  </td>
                </tr>
                <tr>
                  <td width="45%" class="help">
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "hostp_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "hostp").'</a>:
                  </td>
                  <td>
                    <input type="text" name="server_hostp" value="'.$server["External_Address"].'" />
                  </td>
                </tr>
                <tr>
                  <td>'.lang("admin", "port").': </td>
                  <td>
                    <input type="text" name="server_port" value="'.$server["Port"].'" />
                  </td>
                </tr>';
        if ( $core != 1 )
          $output .= '
                <tr>
                  <td>'.lang("admin", "telnetport_tip").':</td>
                  <td>
                    <input type="text" name="server_telnet_port" value="'.$server["Telnet_Port"].'" />
                  </td>
                </tr>
                <tr>
                  <td>'.lang("admin", "telnetuser_tip").':</td>
                  <td>
                    <input type="text" name="server_telnet_user" value="'.$server["Telnet_User"].'" />
                  </td>
                </tr>
                <tr>
                  <td>'.lang("admin", "telnetpass_tip").':</td>
                  <td>
                    <input type="text" name="server_telnet_pass" value="'.$server["Telnet_Pass"].'" />
                  </td>
                </tr>';
        $output .= '
                <tr>
                  <td>'.lang("admin", "icon").': </td>
                  <td>
                    <select name="server_type">';
        foreach ( $get_icon_type as $type )
        {
          $output .= '
                      <option value="'.$type[0].'" '.( ( $server["Icon"] == $type[0] ) ? 'selected="selected"' : '' ).'>'.lang("realm", $type[1]).'</option>';
        }
        $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>'.lang("admin", "color").': </td>
                  <td>
                    <input type="text" name="server_color" value="'.$server["Color"].'" />
                  </td>
                </tr>
                <tr>
                  <td>'.lang("admin", "timezone").': </td>
                  <td>
                    <select name="server_timezone">';
        foreach ( $get_timezone_type as $zone )
        {
          $output .= '
                      <option value="'.$zone[0].'" '.( ( $server["Timezone"] == $zone[0] ) ? 'selected="selected"' : '' ).'>'.lang("realm", $zone[1]).'</option>';
        }
        $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="help">
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "bothfactions_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "bothfactions").'</a>:
                  </td>
                  <td>
                    <input type="checkbox" name="server_both" value="1" '.( ( $server["Both_Factions"] ) ? 'checked="checked"' : '' ).' />
                  </td>
                </tr>';
        if ( $core == 1 )
          $output .= '
                <tr>
                  <td class="help">
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "statsxml_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "statsxml").'</a>:
                  </td>
                  <td>
                    <input type="text" name="server_stats" value="'.$server["Stats_XML"].'" />
                  </td>
                </tr>';
        $output .= '
              </table>
            </fieldset>
            <input type="submit" name="saveserver" value="'.lang("admin", "save").'" />
          </form>
        </center>';
      }
      else
        redirect("admin.php?section=servers&error=1");
    }
    elseif ( $server_action == "del" )
    {
      $server_id = $sqlm->quote_smart($_GET["sel_server"]);
      if ( is_numeric($server_id) )
      {
        $result = $sqlm->query("DELETE FROM config_servers WHERE `Index`='".$server_id."'");
        redirect("admin.php?section=servers");
      }
      else
        redirect("admin.php?section=servers&error=1");
    }
    else
    {
      switch ( $core )
      {
        case 1:
        {
          $name = "ArcEmu";
          $port = "8129";
          break;
        }
        case 2:
        {
          $name = "MaNGOS";
          $port = "8085";
          break;
        }
        case 3:
        {
          $name = "Trinity";
          $port = "8085";
          break;
        }
      }

      // get highest server index
      $i_query = "SELECT IFNULL(MAX(`Index`), 0) AS MaxID FROM config_servers";
      $i_result = $sqlm->query($i_query);
      $i_result = $sqlm->fetch_assoc($i_result);
      $max_index = $i_result["MaxID"];

      $result = $sqlm->query("INSERT INTO config_servers (`Index`, Port, Name, Both_Factions, Telnet_Port, Address) VALUES ('".($max_index+1)."', '".$port."', '".$name."', 1, 0, '127.0.0.1')");

      redirect("admin.php?section=servers");
    }
  }
}

function saveserver()
{
  global $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  $server_id = $sqlm->quote_smart($_GET["index"]);
  $new_server_id = $sqlm->quote_smart($_GET["new_index"]);
  $server_name = $sqlm->quote_smart($_GET["server_name"]);
  $server_hosti = $sqlm->quote_smart($_GET["server_hosti"]);
  $server_hostp = $sqlm->quote_smart($_GET["server_hostp"]);
  $server_port = $sqlm->quote_smart($_GET["server_port"]);
  $server_telnet_port = ( ( isset($_GET["server_telnet_port"]) ) ? $sqlm->quote_smart($_GET["server_telnet_port"]) : NULL );
  $server_telnet_user = ( ( isset($_GET["server_telnet_user"]) ) ? strtoupper($sqlm->quote_smart($_GET["server_telnet_user"])) : NULL );
  $server_telnet_pass = ( ( isset($_GET["server_telnet_pass"]) ) ? $sqlm->quote_smart($_GET["server_telnet_pass"]) : NULL );
  $server_type = $sqlm->quote_smart($_GET["server_type"]);
  $server_color = $sqlm->quote_smart($_GET["server_color"]);
  $server_timezone = $sqlm->quote_smart($_GET["server_timezone"]);
  $server_factions = ( ( isset($_GET["server_both"]) ) ? 1 : 0 );
  $server_stats = ( ( isset($_GET["server_stats"]) ) ? $sqlm->quote_smart($_GET["server_stats"]) : NULL );

  $result = $sqlm->query("UPDATE config_servers SET `Index`='".$new_server_id."', Address='".$server_hosti."', Port='".$server_port."', Telnet_Port='".$server_telnet_port."', Telnet_User='".$server_telnet_user."', Telnet_Pass='".$server_telnet_pass."', Both_Factions='".$server_factions."', Stats_XML='".$server_stats."', Name='".$server_name."', External_Address='".$server_hostp."', Port='".$server_port."', Icon='".$server_type."', Color='".$server_color."', Timezone='".$server_timezone."' WHERE `Index`='".$server_id."'");
  redirect("admin.php?section=servers");
}

?>
