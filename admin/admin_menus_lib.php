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

function menus()
{
  global $output, $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  $menu_action = "start";
  if ( isset($_GET["editmenu"]) )
    $menu_action = "edit";
  if ( isset($_GET["delmenu"]) )
    $menu_action = "delmenu";
  if ( isset($_GET["addmenu"]) )
    $menu_action = "addmenu";
  if ( isset($_GET["editmenu_item"]) )
    $menu_action = "edititem";
  if ( isset($_GET["delmenu_item"]) )
    $menu_action = "delitem";
  if ( isset($_GET["addmenu_item"]) )
    $menu_action = "additem";
  if ( isset($_GET["savemenu"]) )
    $menu_action = "savemenu";

  switch ( $menu_action )
  {
    case "start";
    {
      $top_menus = $sqlm->query("SELECT * FROM config_top_menus");
      $output .= '
        <center>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="menus" />
            <table class="simple" id="admin_top_menus">
              <tr>
                <th>'.lang("admin", "edit").'</th>
                <th>'.lang("admin", "remove").'</th>
                <th>'.lang("admin", "internalname").'</th>
                <th>'.lang("admin", "action").'</th>
                <th>'.lang("admin", "enabled").'</th>
              </tr>';
      $color = "#EEEEEE";
      while ( $top_menu = $sqlm->fetch_assoc($top_menus) )
      {
        $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=menus&amp;top_menu='.$top_menu["Index"].'&amp;editmenu=editmenu">
                      <img src="img/edit.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=menus&amp;top_menu='.$top_menu["Index"].'&amp;delmenu=delmenu">
                      <img src="img/aff_cross.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$top_menu["Name"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$top_menu["Action"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center><img src="img/'.( ( $top_menu["Enabled"] ) ? 'up' : 'down' ).'.gif" alt="" /></center>
                </td>
              </tr>';
        $color = ( ( $color == "#EEEEEE" ) ? "#FFFFFF" : "#EEEEEE" );
      }
      $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=menus&amp;addmenu=addmenu">
                      <img src="img/add.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'" colspan="4">
                  <a href="admin.php?section=menus&amp;addmenu=addmenu">'.lang("admin", "addmenu").'</a>
                </td>
              </tr>
            </table>
            <!-- input type="submit" name="editmenu" value="'.lang("admin", "editmenu").'">
            <input type="submit" name="addmenu" value="'.lang("admin", "addmenu").'">
            <input type="submit" name="delmenu" value="'.lang("admin", "delmenu").'" -->
          </form>
        </center>';
      break;
    }
    case "edit":
    {
      $top_menu = $sqlm->quote_smart($_GET["top_menu"]);
      $top = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_top_menus WHERE `Index`='".$top_menu."'"));
      $output .= '
        <center>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="menus" />
            <input type="hidden" name="top_index" value="'.$top_menu.'" />';

      if ( ( $top["Name"] == "main" ) || ( $top["Name"] == "invisible" ) )
        $output .= '
            <input type="hidden" name="enabled" value="1" />';

      $output .= '
            <table class="simple" id="admin_edit_top_menu_nameaction">
              <tr>
                <th colspan="2">'.lang("admin", "top_menu").'</th>
              </tr>
              <tr>
                <td>'.lang("admin", "internalname2").': </td>
                <td>
                  <input type="text" name="top_name" value="'.$top["Name"].'" class="admin_edit_top_menu_action" />
                </td>
              </tr>
              <tr>
                <td>'.lang("admin", "action").': </td>
                <td>
                  <textarea name="menu_action" class="admin_edit_top_menu_action" rows="2" cols="32">'.$top["Action"].'</textarea>
                </td>
              </tr>
              <tr>
                <td>'.lang("admin", "enabled").': </td>
                <td>';

      if ( ( $top["Name"] != "main" ) && ( $top["Name"] != "invisible" ) )
        $output .= '
                  <input type="checkbox" name="enabled"'.( ( $top["Enabled"] ) ? ' checked="checked"' : '' ).' />';
      else
        $output .= '<img src="img/lock.png" alt="" /> ('.lang("admin", "nodisable").')';

      $output .= '
                </td>
              </tr>
            </table>
            <table class="simple" id="admin_edit_top_menu_submenus">
              <tr>
                <th>'.lang("admin", "edit").'</th>
                <th>'.lang("admin", "remove").'</th>
                <th>'.lang("admin", "order").'</th>
                <th>'.lang("admin", "internalname").'</th>
                <th>'.lang("admin", "action").'</th>
                <th>'.lang("admin", "view").'</th>
                <th>'.lang("admin", "insert").'</th>
                <th>'.lang("admin", "update").'</th>
                <th>'.lang("admin", "delete").'</th>
                <th>'.lang("admin", "enabled").'</th>
              </tr>';
      $menus = $sqlm->query("SELECT * FROM config_menus WHERE Menu='".$top_menu."'");
      $color = "#EEEEEE";
      while ( $menu = $sqlm->fetch_assoc($menus) )
      {
        $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=menus&amp;top_index='.$top_menu.'&amp;menu_item='.$menu["Index"].'&amp;editmenu_item=editmenuitem">
                      <img src="img/edit.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=menus&amp;top_index='.$top_menu.'&amp;menu_item='.$menu["Index"].'&amp;delmenu_item=delmenuitem">
                      <img src="img/aff_cross.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$menu["Order"].'</center>
                </td>
                <td width="15%" style="background-color:'.$color.'">
                  <center>'.$menu["Name"].'</center>
                </td>
                <td width="25%" style="background-color:'.$color.'">
                  <center>'.$menu["Action"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.sec_level_name($menu["View"]).' ('.$menu["View"].')'.'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.sec_level_name($menu["Insert"]).' ('.$menu["Insert"].')'.'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.sec_level_name($menu["Update"]).' ('.$menu["Update"].')'.'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.sec_level_name($menu["Delete"]).' ('.$menu["Delete"].')'.'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>
                    <img src="img/'.( ( $menu["Enabled"] ) ? 'up' : 'down' ).'.gif" alt="" />
                  </center>
                </td>
              </tr>';
        $color = ( ( $color == "#EEEEEE" ) ? "#FFFFFF" : "#EEEEEE" );
      }
      $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=menus&amp;top_index='.$top_menu.'&amp;addmenu_item=addmenuitem">
                      <img src="img/add.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'" colspan="8">
                  <a href="admin.php?section=menus&amp;top_index='.$top_menu.'&amp;addmenu_item=addmenuitem">'.lang("admin", "addmenu_item").'</a>
                </td>
              </tr>
            </table>
            <!-- input type="submit" name="editmenu_item" value="'.lang("admin", "editmenu_item").'">
            <input type="submit" name="addmenu_item" value="'.lang("admin", "addmenu_item").'" -->
            <input type="submit" name="savemenu" value="'.lang("admin", "save").'" />
            <!-- input type="submit" name="delmenu_item" value="'.lang("admin", "delmenu_item").'" -->
          </form>
        </center>';
      break;
    }
    case "edititem":
    {
      $menu_item = $sqlm->quote_smart($_GET["menu_item"]);
      $menu = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_menus WHERE `Index`='".$menu_item."'"));
      $sec_list = sec_level_list();
      $top_menu_query = "SELECT * FROM config_top_menus";
      $top_menu_result = $sqlm->query($top_menu_query);

      $output .= '
        <center>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="menus" />
            <input type="hidden" name="action" value="savemenu" />
            <input type="hidden" name="menu_item" value="'.$menu_item.'" />
            <fieldset id="admin_edit_menu_field">
              <table class="help" id="admin_edit_menu">
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "menu_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "menu").'</a>:
                  </td>
                  <td>
                    <select name="menu">';
                    //<input type="text" name="menu" value="'.$menu["Menu"].'" id="admin_edit_menu_fields">
      while ( $row = $sqlm->fetch_assoc($top_menu_result) )
      {
        $output .= '
                      <option value="'.$row["Index"].'" '.( ( $row["Index"] == $menu["Menu"] ) ? 'selected="selected"' : '').' class="admin_edit_menu_fields">'.$row["Name"].'</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "order_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "order").'</a>:
                  </td>
                  <td>
                    <input type="text" name="order" value="'.$menu["Order"].'" class="admin_edit_menu_fields" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "menuname_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "internalname2").'</a>:
                  </td>
                  <td>
                    <input type="text" name="name" value="'.$menu["Name"].'" class="admin_edit_menu_fields" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "action_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "action").'</a>:
                  </td>
                  <td>
                    <textarea name="menu_action" style="width:260px" rows="2" cols="32">'.$menu["Action"].'</textarea>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "view_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "view").'</a>:
                  </td>
                  <td>
                    <select name="view">';
      foreach ( $sec_list as $row )
      {
        $output .= '
                      <option value="'.$row["Sec"].'" '.( ( $row["Sec"] == $menu["View"] ) ? 'selected="selected"' : '' ).'>'.$row["Name"].' ('.$row["Sec"].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "insert").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "insert").'</a>:
                  </td>
                  <td>
                    <select name="insert">';
      foreach ( $sec_list as $row )
      {
        $output .= '
                      <option value="'.$row["Sec"].'" '.( ( $row["Sec"] == $menu["Insert"] ) ? 'selected="selected"' : '' ).'>'.$row["Name"].' ('.$row["Sec"].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "update_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "update").'</a>:
                  </td>
                  <td>
                    <select name="update">';
      foreach ( $sec_list as $row )
      {
        $output .= '
                      <option value="'.$row["Sec"].'" '.( ( $row["Sec"] == $menu["Update"] ) ? 'selected="selected"' : '' ).'>'.$row["Name"].' ('.$row["Sec"].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "delete").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "delete").'</a>:
                  </td>
                  <td>
                    <select name="delete">';
      foreach ( $sec_list as $row )
      {
        $output .= '
                      <option value="'.$row["Sec"].'" '.( ( $row["Sec"] == $menu["Delete"] ) ? 'selected="selected"' : '' ).'>'.$row["Name"].' ('.$row["Sec"].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>';
      if ( $menu_item <> 8 )
        $output .= '
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "enabled_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "enabled").'</a>:
                  </td>
                  <td>
                    <input type="checkbox" name="enabled" '.( ( $menu["Enabled"] ) ? 'checked="checked"' : '' ).' />
                  </td>
                </tr>';
      else
        $output .= '
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "enabled_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "enabled").'</a>:
                  </td>
                  <td>
                    <input type="checkbox" name="enabled" '.( ( $menu["Enabled"] ) ? 'checked="checked' : '' ).' disabled="disabled" />
                  </td>
                </tr>';
      $output .= '
              </table>
            </fieldset>
            <input type="submit" name="save_menu_item" value="'.lang("admin", "save").'" />
          </form>
        </center>';
      break;
    }
    case "addmenu":
    {
      $max = $sqlm->fetch_assoc($sqlm->query("SELECT MAX(`Index`) FROM config_top_menus"));
      $max = $max["MAX(`Index`)"] + 1;
      $result = $sqlm->query("INSERT INTO config_top_menus (`Index`, Action, Name, Enabled) VALUES ('".$max."', '', '', '0')");
      redirect("admin.php?section=menus");
      break;
    }
    case "delmenu":
    {
      $top_menu = $sqlm->quote_smart($_GET["top_menu"]);
      if ( is_numeric($top_menu) )
      {
        $result = $sqlm->query("DELETE FROM config_top_menus WHERE `Index`='".$top_menu."'");
        redirect("admin.php?section=menus");
      }
      else
        redirect("admin.php?section=menus&error=1");
      break;
    }
    case "savemenu":
    {
      $top_index = $sqlm->quote_smart($_GET["top_index"]);
      $top_name = $sqlm->quote_smart($_GET["top_name"]);
      $top_action = $sqlm->quote_smart($_GET["menu_action"]);
      $enabled = ( ( isset($_GET["enabled"]) ) ? 1 : 0 );
      $result = $sqlm->query("UPDATE config_top_menus SET Name='".$top_name."', Action='".$top_action."', Enabled='".$enabled."' WHERE `Index`='".$top_index."'");
      redirect("admin.php?section=menus");
      break;
    }
    case "additem":
    {
      $top_index = $sqlm->quote_smart($_GET["top_index"]);
      $result = $sqlm->query("INSERT INTO config_menus (Menu, Action, Name) VALUES ('".$top_index."', '','')");
      redirect("admin.php?section=menus&top_menu=".$top_index."&editmenu=editmenu");
      break;
    }
    case "delitem":
    {
      $menu_item = $sqlm->quote_smart($_GET["menu_item"]);
      $top_index = $sqlm->quote_smart($_GET["top_index"]);
      if ( is_numeric($menu_item) )
      {
        $result = $sqlm->query("DELETE FROM config_menus WHERE `Index`='".$menu_item."'");
        redirect("admin.php?section=menus&top_menu=".$top_index."&editmenu=editmenu");
      }
      else
        redirect("admin.php?section=menus&error=1");
      break;
    }
    default:
      redirect("admin.php?section=menus&error=1");
      break;
  }
}

function savemenu()
{
  global $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  $menu_item = $sqlm->quote_smart($_GET["menu_item"]);
  $menu = $sqlm->quote_smart($_GET["menu"]);
  $order = $sqlm->quote_smart($_GET["order"]);
  $name = $sqlm->quote_smart($_GET["name"]);
  $action = $sqlm->quote_smart($_GET["menu_action"]);
  $view = $sqlm->quote_smart($_GET["view"]);
  $insert = $sqlm->quote_smart($_GET["insert"]);
  $update = $sqlm->quote_smart($_GET["update"]);
  $delete = $sqlm->quote_smart($_GET["delete"]);
  $enabled = ( ( isset($_GET["enabled"]) ) ? 1 : 0 );

  if ( empty($order) || !isset($order) )
    redirect("admin.php?section=menus&error=1");
  

  $result = $sqlm->query("SELECT * FROM config_menus WHERE `Index`='".$menu_item."'");
  if ( $sqlm->num_rows($result) )
    $result = $sqlm->query("UPDATE config_menus SET Menu='".$menu."', `Order`='".$order."', Name='".$name."', Action='".$action."', View='".$view."', `Insert`='".$insert."', `Update`='".$update."', `Delete`='".$delete."', Enabled='".$enabled."' WHERE `Index`='".$menu_item."'");
  else
    $result = $sqlm->query("INSERT INTO config_menus (Menu, `Order`, Name, Action, View, Insert, Update, Delete, Enabled) VALUES ('".$menu."', '".$order."', '".$name."', '".$action."', '".$view."', '".$insert."', '".$update."', '".$delete."', '".$enabled."')");

  redirect("admin.php?section=menus");
}

?>
