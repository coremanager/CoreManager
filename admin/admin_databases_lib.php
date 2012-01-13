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


function database()
{
  global $output, $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  $dbc_db = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_dbc_database"));
  $logon_db = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_logon_database"));
  $char_dbs = $sqlm->query("SELECT * FROM config_character_databases");
  $world_dbs = $sqlm->query("SELECT * FROM config_world_databases");

  $output .= '
        <span style="color:red">'.lang("admin", "db_warn").'</span>
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="databases" />
          <input type="hidden" name="action" value="savedbs" />
          <table>
            <tr>
              <td>
                <fieldset class="admin_editdb_field">
                  <legend>'.lang("admin", "host").'</legend>
                  <table>
                    <tr>
                      <td colspan="4">
                        <span style="color:red">'.lang("admin", "host_info").'</span>
                      </td>
                    </tr>
                    <tr>
                      <td width="75px">'.lang("admin", "host").': </td>
                      <td>
                        <input type="text" name="host" value="'.$dbc_db["Address"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "port").': </td>
                      <td>
                        <input type="text" name="port" value="'.$dbc_db["Port"].'" size="10%" />
                      </td>
                    </tr>
                    <tr>
                      <td width="75px">'.lang("admin", "user").': </td>
                      <td>
                        <input type="text" name="user" value="'.$dbc_db["User"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "pass").': </td>
                      <td>
                        <input type="text" name="pass" value="'.$dbc_db["Password"].'" size="10%" />
                      </td>
                    </tr>
                  </table>
                </fieldset>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>
            <tr>
              <td>
                <fieldset class="admin_editdb_field">
                  <legend>'.lang("admin", "arcm").'</legend>
                  <table>
                    <!-- tr>
                      <td width="75px">'.lang("admin", "host").': </td>
                      <td>
                        <input type="text" name="dbc_host" value="'.$dbc_db["Address"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "port").': </td>
                      <td>
                        <input type="text" name="dbc_port" value="'.$dbc_db["Port"].'" size="10%" />
                      </td>
                    </tr>
                    <tr>
                      <td width="75px">'.lang("admin", "user").': </td>
                      <td>
                        <input type="text" name="dbc_user" value="'.$dbc_db["User"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "pass").': </td>
                      <td>
                        <input type="text" name="dbc_pass" value="'.$dbc_db["Password"].'" size="10%" />
                      </td>
                    </tr -->
                    <tr>
                      <td width="75px">'.lang("admin", "name").': </td>
                      <td>
                        <input type="text" name="dbc_name" value="'.$dbc_db["Name"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "db_enc").': </td>
                      <td>
                        <input type="text" name="dbc_encoding" value="'.$dbc_db["Encoding"].'" size="10%" />
                      </td>
                    </tr>
                  </table>
                </fieldset>
              </td>
              <td>
                <fieldset class="admin_editdb_field">
                  <legend>'.lang("admin", "logon").'</legend>
                  <table>
                    <!-- tr>
                      <td width="75px">'.lang("admin", "host").': </td>
                      <td>
                        <input type="text" name="logon_host" value="'.$logon_db["Address"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "port").': </td>
                      <td>
                        <input type="text" name="logon_port" value="'.$logon_db["Port"].'" size="10%" />
                      </td>
                    </tr>
                    <tr>
                      <td width="75px">'.lang("admin", "user").': </td>
                      <td>
                        <input type="text" name="logon_user" value="'.$logon_db["User"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "pass").': </td>
                      <td>
                        <input type="text" name="logon_pass" value="'.$logon_db["Password"].'" size="10%" />
                      </td>
                    </tr -->
                    <tr>
                      <td width="75px">'.lang("admin", "name").': </td>
                      <td>
                        <input type="text" name="logon_name" value="'.$logon_db["Name"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "db_enc").': </td>
                      <td>
                        <input type="text" name="logon_encoding" value="'.$logon_db["Encoding"].'" size="10%" />
                      </td>
                    </tr>
                  </table>
                </fieldset>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <span style="color:red">'.lang("admin", "realm_info").'</span>
              </td>
            </tr>
            <tr>';
  while ( $char = $sqlm->fetch_assoc($char_dbs) )
  {
    $output .= '
              <td>
                <input type="hidden" name="char_realm[]" value="'.$char["Index"].'" />
                <fieldset class="admin_editdb_field">
                  <legend>'.lang("admin", "char").' ('.lang("admin", "realm").' '.$char["Index"].')</legend>
                  <table>
                    <!-- tr>
                      <td width="75px">'.lang("admin", "host").': </td>
                      <td>
                        <input type="text" name="char_host[]" value="'.$char["Address"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "port").': </td>
                      <td>
                        <input type="text" name="char_port[]" value="'.$char["Port"].'" size="10%" />
                      </td>
                    </tr>
                    <tr>
                      <td width="75px">'.lang("admin", "user").': </td>
                      <td>
                        <input type="text" name="char_user[]" value="'.$char["User"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "pass").': </td>
                      <td>
                        <input type="text" name="char_pass[]" value="'.$char["Password"].'" size="10%" />
                      </td>
                    </tr -->
                    <tr>
                      <td width="75px">'.lang("admin", "name").': </td>
                      <td>
                        <input type="text" name="char_name[]" value="'.$char["Name"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "db_enc").': </td>
                      <td>
                        <input type="text" name="char_encoding[]" value="'.$char["Encoding"].'" size="10%" />
                      </td>
                    </tr>
                    <tr>
                      <td width="75px">'.lang("admin", "realm").': </td>
                      <td>
                        <input type="text" name="char_new_realm[]" value="'.$char["Index"].'" size="10%" />
                      </td>
                      <td colspan="2">
                        <a href="admin.php?section=databases&amp;action=savedbs&amp;remove_char[]='.$char["Index"].'">
                          <img src="img/aff_cross.png" alt="" /> '.lang("admin", "remove").'
                        </a>
                      </td>
                    </tr>
                  </table>
                </fieldset>
              </td>';
  }
  $output .= '
            </tr>
            <tr>
              <td>
                <a href="admin.php?section=databases&amp;action=savedbs&amp;addchar=addchar">
                  <img src="img/add.png" alt="" /> <b>'.lang("admin", "addchar").'</b>
                </a>
              </td>
            </tr>
            <tr>';
  while ( $world = $sqlm->fetch_assoc($world_dbs) )
  {
    $output .= '
              <td>
                <input type="hidden" name="world_realm[]" value="'.$world["Index"].'" />
                <fieldset class="admin_editdb_field">
                  <legend>'.lang("admin", "world").' ('.lang("admin", "realm").' '.$world["Index"].')</legend>
                  <table>
                    <!-- tr>
                      <td width="75px">'.lang("admin", "host").': </td>
                      <td>
                        <input type="text" name="world_host[]" value="'.$world["Address"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "port").': </td>
                      <td>
                        <input type="text" name="world_port[]" value="'.$world["Port"].'" size="10%" />
                      </td>
                    </tr>
                    <tr>
                      <td width="75px">'.lang("admin", "user").': </td>
                      <td>
                        <input type="text" name="world_user[]" value="'.$world["User"].'" size="10%" />
                      </td>
                      <td>'.lang("admin", "pass").': </td>
                      <td>
                        <input type="text" name="world_pass[]" value="'.$world["Password"].'" size="10%" />
                      </td>
                    </tr -->
                    <tr>
                      <td width="75px">'.lang("admin", "name").': </td>
                      <td>
                        <input type="text" name="world_name[]" value="'.$world["Name"].'" size="10%" />
                      </td>
                      <td width="75px">'.lang("admin", "db_enc").': </td>
                      <td>
                        <input type="text" name="world_encoding[]" value="'.$world["Encoding"].'" size="10%" />
                      </td>
                    </tr>
                    <tr>
                      <td width="75px">'.lang("admin", "realm").': </td>
                      <td>
                        <input type="text" name="world_new_realm[]" value="'.$world["Index"].'" size="10%" />
                      </td>
                      <td colspan="2">
                        <a href="admin.php?section=databases&amp;action=savedbs&amp;remove_world[]='.$world["Index"].'">
                          <img src="img/aff_cross.png" alt="" /> '.lang("admin", "remove").'
                        </a>
                      </td>
                    </tr>
                  </table>
                </fieldset>
              </td>';
  }
  $output .= '
            </tr>
            <tr>
              <td>
                <a href="admin.php?section=databases&amp;action=savedbs&amp;addworld=addworld">
                  <img src="img/add.png" alt="" /> <b>'.lang("admin", "addworld").'</b>
                </a>
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
}

function savedbs()
{
  global $output, $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  if ( isset($_GET["addchar"]) )
  {
    // Add new Character Database
    // get highest index
    $i_query = "SELECT IFNULL(MAX(`Index`), 0) AS MaxID FROM config_character_databases";
    $i_result = $sqlm->query($i_query);
    $i_result = $sqlm->fetch_assoc($i_result);
    $max_index = $i_result["MaxID"];

    $result_addchar = $sqlm->query("INSERT INTO config_character_databases (`Index`, Encoding) VALUES ('".($max_index+1)."', 'utf8')");
  }

  if ( isset($_GET["addworld"]) )
  {
    // Add new World Database
    // get highest index
    $i_query = "SELECT IFNULL(MAX(`Index`), 0) AS MaxID FROM config_world_databases";
    $i_result = $sqlm->query($i_query);
    $i_result = $sqlm->fetch_assoc($i_result);
    $max_index = $i_result["MaxID"];

    $result_addworld = $sqlm->query("INSERT INTO config_world_databases (`Index`, Encoding) VALUES ('".($max_index+1)."', 'utf8')");
  }

  if ( isset($_GET["dbc_name"]) )
  {
    $dbc_host = $sqlm->quote_smart($_GET["host"]);
    $dbc_port = $sqlm->quote_smart($_GET["port"]);
    $dbc_user = $sqlm->quote_smart($_GET["user"]);
    $dbc_pass = $sqlm->quote_smart($_GET["pass"]);
    $dbc_name = $sqlm->quote_smart($_GET["dbc_name"]);
    $dbc_encoding = $sqlm->quote_smart($_GET["dbc_encoding"]);

    $dbc_count = $sqlm->fetch_assoc($sqlm->query("SELECT COUNT(*) FROM config_dbc_database"));

    if ( $dbc_count["COUNT(*)"] == 1 )
    {
      $dbc_upper = $sqlm->fetch_assoc($sqlm->query("SELECT MAX(`Index`) FROM config_dbc_database"));
      $result = $sqlm->query("UPDATE config_dbc_database SET Address='".$dbc_host."', Port='".$dbc_port."', Name='".$dbc_name."', User='".$dbc_user."', Password='".$dbc_pass."', Encoding='".$dbc_encoding."' WHERE `Index`='".$dbc_upper["MAX(`Index`)"]."'");
    }
    elseif ( $dbc_count["COUNT(*)"] > 1 )
    {
      $result = $sqlm->query("TRUNCATE TABLE config_dbc_database");
      $result = $sqlm->query("INSERT INTO config_dbc_database (Address, Port, User, Name, Password, Encoding) VALUES ('".$dbc_host."', '".$dbc_port."', '".$dbc_user."', '".$dbc_name."', '".$dbc_pass."', '".$dbc_encoding."')");
    }
    else
    {
      $result = $sqlm->query("INSERT INTO config_dbc_database (Address, Port, User, Name, Password, Encoding) VALUES ('".$dbc_host."', '".$dbc_port."', '".$dbc_user."', '".$dbc_name."', '".$dbc_pass."', '".$dbc_encoding."')");
    }
  }

  if ( isset($_GET["logon_name"]) )
  {
    $logon_host = $sqlm->quote_smart($_GET["host"]);
    $logon_port = $sqlm->quote_smart($_GET["port"]);
    $logon_user = $sqlm->quote_smart($_GET["user"]);
    $logon_pass = $sqlm->quote_smart($_GET["pass"]);
    $logon_name = $sqlm->quote_smart($_GET["logon_name"]);
    $logon_encoding = $sqlm->quote_smart($_GET["logon_encoding"]);

    $result_logon = $sqlm->query("UPDATE config_logon_database SET Address='".$logon_host."', Port='".$logon_port."', User='".$logon_user."', Password='".$logon_pass."', Name='".$logon_name."', Encoding='".$logon_encoding."' WHERE `Index`=1");
  }

  if ( isset($_GET["char_realm"]) )
  {
    $char_realms = ( ( isset($_GET["char_realm"]) ) ? $sqlm->quote_smart($_GET["char_realm"]) : NULL );
    $char_new_realms = ( ( isset($_GET["char_new_realm"]) ) ? $sqlm->quote_smart($_GET["char_new_realm"]) : NULL );
    $char_hosts = ( ( isset($_GET["host"]) ) ? $sqlm->quote_smart($_GET["host"]) : NULL );
    $char_ports = ( ( isset($_GET["port"]) ) ? $sqlm->quote_smart($_GET["port"]) : NULL );
    $char_users = ( ( isset($_GET["user"]) ) ? $sqlm->quote_smart($_GET["user"]) : NULL );
    $char_passes = ( ( isset($_GET["pass"]) ) ? $sqlm->quote_smart($_GET["pass"]) : NULL );
    $char_names = ( ( isset($_GET["char_name"]) ) ? $sqlm->quote_smart($_GET["char_name"]) : NULL );
    $char_encodings = ( ( isset($_GET["char_encoding"]) ) ? $sqlm->quote_smart($_GET["char_encoding"]) : NULL );

    for ( $i = 0; $i <= count($char_hosts); $i++ )
    {
      $result_char = $sqlm->query("UPDATE config_character_databases SET `Index`='".$char_new_realms[$i]."', Address='".$char_hosts."', Port='".$char_ports."', User='".$char_users."', Password='".$char_passes."', Name='".$char_names[$i]."', Encoding='".$char_encodings[$i]."' WHERE `Index`='".$char_realms[$i]."'");
    }
  }

  if ( isset($_GET["remove_char"]) )
  {
    $remove_chars = ( ( isset($_GET["remove_char"]) ) ? $sqlm->quote_smart($_GET["remove_char"]) : NULL );

    for ( $i = 0; $i <= count($remove_chars); $i++ )
    {
      $result_char = $sqlm->query("DELETE FROM config_character_databases WHERE `Index`='".$remove_chars[$i]."'");
    }
  }

  if ( isset($_GET["world_realm"]) )
  {
    $world_realms = ( ( isset($_GET["world_realm"]) ) ? $sqlm->quote_smart($_GET["world_realm"]) : NULL );
    $world_new_realms = ( ( isset($_GET["world_new_realm"]) ) ? $sqlm->quote_smart($_GET["world_new_realm"]) : NULL );
    $world_hosts = ( ( isset($_GET["host"]) ) ? $sqlm->quote_smart($_GET["host"]) : NULL );
    $world_ports = ( ( isset($_GET["port"]) ) ? $sqlm->quote_smart($_GET["port"]) : NULL );
    $world_users = ( ( isset($_GET["user"]) ) ? $sqlm->quote_smart($_GET["user"]) : NULL );
    $world_passes = ( ( isset($_GET["pass"]) ) ? $sqlm->quote_smart($_GET["pass"]) : NULL );
    $world_names = ( ( isset($_GET["world_name"]) ) ? $sqlm->quote_smart($_GET["world_name"]) : NULL );
    $world_encodings = ( ( isset($_GET["world_encoding"]) ) ? $sqlm->quote_smart($_GET["world_encoding"]) : NULL );

    for ( $i = 0; $i <= count($world_hosts); $i++ )
    {
      $result_world = $sqlm->query("UPDATE config_world_databases SET `Index`='".$world_new_realms[$i]."', Address='".$world_hosts."', Port='".$world_ports."', User='".$world_users."', Password='".$world_passes."', Name='".$world_names[$i]."', Encoding='".$world_encodings[$i]."' WHERE `Index`='".$world_realms[$i]."'");
    }
  }

  if ( isset($_GET["remove_world"]) )
  {
    $remove_worlds = ( ( isset($_GET["remove_world"]) ) ? $sqlm->quote_smart($_GET["remove_world"]) : NULL );

    for ( $i = 0; $i <= count($remove_worlds); $i++ )
    {
      $remove_query = "DELETE FROM config_world_databases WHERE `Index`='".$remove_worlds[$i]."'";
      $result_world = $sqlm->query($remove_query);
    }
  }

  redirect("admin.php?section=databases");
}
?>
