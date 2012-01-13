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

function forum()
{
  global $output, $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  $forum_action = "start";
  if ( isset($_GET["editforum"]) )
    $forum_action = "edit";
  if ( isset($_GET["delforum"]) )
    $forum_action = "delforum";
  if ( isset($_GET["addforum"]) )
    $forum_action = "addforum";
  if ( isset($_GET["editforum_item"]) )
    $forum_action = "edititem";
  if ( isset($_GET["delforum_item"]) )
    $forum_action = "delitem";
  if ( isset($_GET["addforum_item"]) )
    $forum_action = "additem";
  if ( isset($_GET["saveforum"]) )
    $forum_action = "saveforum";

  switch ( $forum_action )
  {
    case "start";
    {
      $cats = $sqlm->query("SELECT * FROM config_forum_categories");
      $output .= '
        <center>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="forum" />
            <table class="simple admin_top_menus">
              <tr>
                <th colspan="2">'.lang("admin", "cats").'</th>
              </tr>
            </table>
            <table class="simple admin_top_menus">
              <tr>
                <th width="15%">'.lang("admin", "edit").'</th>
                <th width="5%">'.lang("admin", "remove").'</th>
                <th>'.lang("admin", "name").'</th>
              </tr>';
      $color = "#EEEEEE";
      while ( $cat = $sqlm->fetch_assoc($cats) )
      {
        $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=forum&amp;category='.$cat["Index"].'&amp;editforum=editforum">
                      <img src="img/edit.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=forum&amp;category='.$cat["Index"].'&amp;delforum=delforum">
                      <img src="img/aff_cross.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$cat["Name"].'</center>
                </td>
              </tr>';
        $color = ( ( $color == "#EEEEEE" ) ? "#FFFFFF" : "#EEEEEE" );
      }
      $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=forum&amp;addforum=addforum">
                      <img src="img/add.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'" colspan="2">
                  <a href="admin.php?section=forum&amp;addforum=addforum">'.lang("admin", "addforum").'</a>
                </td>
              </tr>
            </table>
            <!-- input type="submit" name="editforum" value="'.lang("admin", "editforum").'">
            <input type="submit" name="addforum" value="'.lang("admin", "addforum").'">
            <input type="submit" name="delforum" value="'.lang("admin", "delforum").'" -->
          </form>
        </center>';
      break;
    }
    case "edit":
    {
      $cat_id = $sqlm->quote_smart($_GET["category"]);
      $cat = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_forum_categories WHERE `Index`='".$cat_id."'"));
      $sec_levels = sec_level_list();
      $output .= '
        <center>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="forum" />
            <input type="hidden" name="category" value="'.$cat_id.'" />
            <table class="simple" id="admin_edit_top_menu_nameaction">
              <tr>
                <th colspan="2">'.lang("admin", "cat").'</th>
              </tr>
              <tr>
                <td>'.lang("admin", "name").': </td>
                <td>
                  <input type="text" name="cat_name" value="'.$cat["Name"].'" id="admin_edit_top_menu_action" />
                </td>
              </tr>
            </table>
            <table class="simple" id="admin_edit_top_menu_submenus">
              <tr>
                <th>'.lang("admin", "edit").'</th>
                <th>'.lang("admin", "remove").'</th>
                <th>'.lang("admin", "name").'</th>
                <th>'.lang("admin", "desc").'</th>
                <th>'.lang("admin", "sideaccess").'</th>
                <th>'.lang("admin", "secread").'</th>
                <th>'.lang("admin", "secpost").'</th>
                <th>'.lang("admin", "sectopic").'</th>
              </tr>';
      $forums = $sqlm->query("SELECT * FROM config_forums WHERE Category='".$cat_id."'");
      $color = "#EEEEEE";
      while ( $forum = $sqlm->fetch_assoc($forums) )
      {
        $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=forum&amp;category='.$cat_id.'&amp;forum_item='.$forum["Index"].'&amp;editforum_item=editforumsection">
                      <img src="img/edit.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=forum&amp;category='.$cat_id.'&amp;forum_item='.$forum["Index"].'&amp;delforum_item=delforumsection">
                      <img src="img/aff_cross.png" alt="" />
                    </a>
                  </center>
                </td>
                <td width="25%" style="background-color:'.$color.'">
                <center>'.$forum["Name"].'</center>
                </td>
                <td width="25%" style="background-color:'.$color.'">
                  <center>'.$forum["Desc"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$forum["Side_Access"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.sec_level_name($forum["Min_Security_Level_Read"]).'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.sec_level_name($forum["Min_Security_Level_Post"]).'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.sec_level_name($forum["Min_Security_Level_Create_Topic"]).'</center>
                </td>
              </tr>';
        $color = ( ( $color == "#EEEEEE" ) ? "#FFFFFF" : "#EEEEEE" );
      }
      $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=forum&amp;category='.$cat_id.'&amp;addforum_item=addforumsection">
                      <img src="img/add.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'" colspan="7">
                  <a href="admin.php?section=forum&amp;category='.$cat_id.'&amp;addforum_item=addforumsection">'.lang("admin", "addforum_item").'</a>
                </td>
              </tr>
            </table>
            <!-- input type="submit" name="editforum_item" value="'.lang("admin", "editforum_item").'">
            <input type="submit" name="addforum_item" value="'.lang("admin", "addforum_item").'" -->
            <input type="submit" name="saveforum" value="'.lang("admin", "save").'" />
            <!-- input type="submit" name="delforum_item" value="'.lang("admin", "delforum_item").'" -->
          </form>
        </center>';
      break;
    }
    case "edititem":
    {
      $forum_item = $sqlm->quote_smart($_GET["forum_item"]);
      $forum = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_forums WHERE `Index`='".$forum_item."'"));
      $sec_list = sec_level_list();
      $cat_list_query = "SELECT * FROM config_forum_categories";
      $cat_list_result = $sqlm->query($cat_list_query);

      $output .= '
        <center>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="forum" />
            <input type="hidden" name="action" value="saveforum" />
            <input type="hidden" name="forum_item" value="'.$forum_item.'" />
            <fieldset id="admin_edit_forum_field">
              <table class="help" id="admin_edit_forum_item">
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "cat_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "cat").'</a>:
                  </td>
                  <td>
                    <select name="category">';
                    //<input type="text" name="category" value="'.$forum["Category"].'" id="admin_edit_menu_fields">
      while ( $row = $sqlm->fetch_assoc($cat_list_result) )
      {
        $output .= '
                      <option value="'.$row["Index"].'" '.( ( $row["Index"] == $forum["Category"] ) ? 'selected="selected"' : '').' class="admin_edit_menu_fields">'.$row["Name"].'</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "forumname_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "name").'</a>:
                  </td>
                  <td>
                    <input type="text" name="name" value="'.$forum["Name"].'" class="admin_edit_menu_fields" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "desc_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "desc").'</a>:
                  </td>
                  <td>
                    <input type="text" name="desc" value="'.$forum["Desc"].'" class="admin_edit_menu_fields" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "sideaccess_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "sideaccess2").'</a>:
                  </td>
                  <td>
                    <input type="text" name="sideaccess" value="'.$forum["Side_Access"].'" class="admin_edit_menu_fields" />
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "secread_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "secread2").'</a>:
                  </td>
                  <td>
                    <select name="min_security_level_read">';
      foreach ( $sec_list as $row )
      {
        $output .= '
                      <option value="'.$row["Sec"].'" '.( ( $row["Sec"] == $forum["Min_Security_Level_Read"] ) ? 'selected="selected"' : '' ).'>'.$row["Name"].' ('.$row["Sec"].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "secpost_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "secpost2").'</a>:
                  </td>
                  <td>
                    <select name="min_security_level_post">';
      foreach ( $sec_list as $row )
      {
        $output .= '
                      <option value="'.$row["Sec"].'" '.( ( $row["Sec"] == $forum["Min_Security_Level_Post"] ) ? 'selected="selected"' : '' ).'>'.$row["Name"].' ('.$row["Sec"].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "sectopic_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "sectopic2").'</a>:
                  </td>
                  <td>
                    <select name="min_security_level_create_topic">';
      foreach ( $sec_list as $row )
      {
        $output .= '
                      <option value="'.$row["Sec"].'" '.( ( $row["Sec"] == $forum["Min_Security_Level_Create_Topic"] ) ? 'selected="selected"' : '' ).'>'.$row["Name"].' ('.$row["Sec"].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
              </table>
            </fieldset>
            <input type="submit" name="save_forum_item" value="'.lang("admin", "save").'" />
          </form>
        </center>';
      break;
    }
    case "addforum":
    {
      $max = $sqlm->fetch_assoc($sqlm->query("SELECT MAX(`Index`) FROM config_forum_categories"));
      $max = $max["MAX(`Index`)"] + 1;
      $result = $sqlm->query("INSERT INTO config_forum_categories (`Index`, Name) VALUES ('".$max."', '')");
      redirect("admin.php?section=forum");
      break;
    }
    case "delforum":
    {
      $category = $sqlm->quote_smart($_GET["category"]);
      if ( is_numeric($category) )
      {
        $result = $sqlm->query("DELETE FROM config_forum_categories WHERE `Index`='".$category."'");
        redirect("admin.php?section=forum");
      }
      else
        redirect("admin.php?section=forum&error=1");
      break;
    }
    case "saveforum":
    {
      $category = $sqlm->quote_smart($_GET["category"]);
      $category_name = $sqlm->quote_smart($_GET["top_name"]);
      $result = $sqlm->query("UPDATE config_forum_categories SET Name='".$category_name."' WHERE `Index`='".$category."'");
      redirect("admin.php?section=forum");
      break;
    }
    case "additem":
    {
      $category = $sqlm->quote_smart($_GET["category"]);
      $result = $sqlm->query("INSERT INTO config_forums (Category, Name, `Desc`, Side_Access) VALUES ('".$category."', '', '', '')");
      redirect("admin.php?section=forum&category=".$category."&editforum=editforum");
      break;
    }
    case "delitem":
    {
      $category = $sqlm->quote_smart($_GET["category"]);
      $forum_item = $sqlm->quote_smart($_GET["forum_item"]);
      if ( is_numeric($forum_item) )
      {
        $result = $sqlm->query("DELETE FROM config_forums WHERE `Index`='".$forum_item."'");
        redirect("admin.php?section=forum&category=".$category."&editforum=editforum");
      }
      else
        redirect("admin.php?section=forum&error=1");
      break;
    }
    default:
      redirect("admin.php?section=forum&error=1");
      break;
  }
}

function saveforum()
{
  global $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  $forum_item = $sqlm->quote_smart($_GET["forum_item"]);
  $forum = $sqlm->quote_smart($_GET["category"]);
  $name = $sqlm->quote_smart($_GET["name"]);
  $desc = $sqlm->quote_smart($_GET["desc"]);
  $sideaccess = $sqlm->quote_smart($_GET["sideaccess"]);
  $min_security_level_read = $sqlm->quote_smart($_GET["min_security_level_read"]);
  $min_security_level_post = $sqlm->quote_smart($_GET["min_security_level_post"]);
  $min_security_level_create_topic = $sqlm->quote_smart($_GET["min_security_level_create_topic"]);

  $result = $sqlm->query("SELECT * FROM config_forums WHERE `Index`='".$forum_item."'");
  if ( $sqlm->num_rows($result) )
    $result = $sqlm->query("UPDATE config_forums SET Category='".$forum."', Name='".$name."', `Desc`='".$desc."', Side_Access='".$sideaccess."', Min_Security_Level_Read='".$min_security_level_read."', Min_Security_Level_Post='".$min_security_level_post."', Min_Security_Level_Create_Topic='".$min_security_level_create_topic."' WHERE `Index`='".$forum_item."'");
  else
    $result = $sqlm->query("INSERT INTO config_forums (Category, Name, Desc, Side_Access, Min_Security_Level_Read, Min_Security_Level_Post, Min_Security_Level_Create_Topic) VALUES ('".$forum."', '".$name."', '".$desc."', '".$sideaccess."', '".$min_security_level_read."', '".$min_security_level_post."', '".$min_security_level_create_topic."')");

  redirect("admin.php?section=forum");
}

?>
