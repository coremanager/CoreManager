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

function accounts()
{
  global $output, $corem_db, $logon_db, $itemperpage, $core;

  // we need $core to be set
  if ( $core == 0 )
    $core = detectcore();

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  $sqll = new SQL;
  $sqll->connect($logon_db["addr"], $logon_db["user"], $logon_db["pass"], $logon_db["name"], $logon_db["encoding"]);

  $start = ( ( isset($_GET["start"]) ) ? $sqll->quote_smart($_GET["start"]) : 0 );
  if ( !is_numeric($start) )
    $start = 0;

  $order_by = ( ( isset($_GET["order_by"]) ) ? $sqll->quote_smart($_GET["order_by"]) : "acct" );
  if ( $order_by == "login" )
  {
    if ( $core == 1 )
      $order_by = "login";
    else
      $order_by = "username";
  }

  $dir = ( ( isset($_GET["dir"]) ) ? $sqll->quote_smart($_GET["dir"]) : 1 );
  if ( !preg_match('/^[01]{1}$/', $dir) )
    $dir = 1;

  $accts_per_page = ( ( isset($_GET["perpage"]) ) ? $sqll->quote_smart($_GET["perpage"]) : $itemperpage );
  if ( !is_numeric($accts_per_page) )
    $accts_per_page = $itemperpage;

  $order_dir = ( ( $dir ) ? "ASC" : "DESC" );
  $dir = ( ( $dir ) ? 0 : 1 );

  $search_value = ( ( isset($_GET["search_value"]) ) ? $sqll->quote_smart($_GET["search_value"]) : "" );
  $search_by = ( ( isset($_GET["search_by"]) ) ? $sqll->quote_smart($_GET["search_by"]) : "" );

  if ( $core == 1 )
    $search_menu = array(
      array("login",          "by_name"),
      array("acct",           "by_id"),
      array("ScreenName",     "by_sn"),
      array("SecurityLevel",  "by_sl"),
      array("WebAdmin",       "by_web"));
  else
    $search_menu = array(
      array("username",       "by_name"),
      array("id",             "by_id"),
      array("ScreenName",     "by_sn"),
      array("SecurityLevel",  "by_sl"),
      array("WebAdmin",       "by_web"));

  $search = "";
  if ( ( $search_value != "" ) && ( $search_by != "" ) )
  {
    if ( $search_by == "WebAdmin" )
      $search = "WHERE SecurityLevel>='1073741824'"; // WebAdmin column removed r197
    else
      $search = "WHERE ".$search_by." LIKE '%".$search_value."%'";
  }

  if ( $core == 1 )
  {
    $query = "SELECT *, (SecurityLevel & 1073741824) AS WebAdmin
              FROM accounts
                LEFT JOIN `".$corem_db["name"]."`.config_accounts ON accounts.login=`".$corem_db["name"]."`.config_accounts.Login COLLATE utf8_general_ci
              ".$search."
              ORDER BY ".$order_by." ".$order_dir." LIMIT ".$start.", ".$accts_per_page;
    $count_query = "SELECT COUNT(*) FROM accounts
                      LEFT JOIN `".$corem_db["name"]."`.config_accounts ON accounts.login=`".$corem_db["name"]."`.config_accounts.Login COLLATE utf8_general_ci
                    ".$search;
  }
  else
  {
    $query = "SELECT *, id AS acct, username AS login, (SecurityLevel & 1073741824) AS WebAdmin
              FROM account
                LEFT JOIN `".$corem_db["name"]."`.config_accounts ON account.username=`".$corem_db["name"]."`.config_accounts.Login
              ".$search."
              ORDER BY ".$order_by." ".$order_dir." LIMIT ".$start.", ".$accts_per_page;
    $count_query = "SELECT COUNT(*) FROM account
                      LEFT JOIN `".$corem_db["name"]."`.config_accounts ON account.username=`".$corem_db["name"]."`.config_accounts.Login
                    ".$search;
  }

  $result = $sqll->query($query);

  $count_result = $sqll->query($count_query);
  $all_record = $sqll->result($count_result, 0);

  $accounts_action = 0;
  if ( isset($_GET["editacct"]) )
    $accounts_action = "edit";

  if ( !$accounts_action )
  {
    $output .= '
        <center>
          <table class="hidden">
            <tr>
              <td>
                <form action="admin.php" method="get" name="form">
                  <input type="hidden" name="section" value="accounts" />
                  <input type="text" size="24" maxlength="50" name="search_value" value="'.$search_value.'" />
                  <select name="search_by">';
  foreach ( $search_menu as $row )
  {
    $output .= '
                            <option value="'.$row[0].'"'.( ( $search_by === $row[0] ) ? ' selected="selected"' : '' ).'>'.lang("admin", $row[1]).'</option>';
  }
  $output .= '
                  </select>
                  <input type="submit" name="search" value="'.lang("global", "search").'" />
                </form>
              </td>
            </tr>
          </table>';

    $output .= '
          <a href="admin.php?section=accounts&amp;order_by='.$order_by.'&amp;start='.$start.'&amp;dir='.( ( $dir ) ? 0 : 1 ).'&perpage='.$accts_per_page.'">'.lang("admin", "clearsearch").'</a>
          <br />
          <br />';

    if ( $order_by == "username" )
      $order_by = "login";

    $output .= '
          <table class="hidden admin_accounts">
            <tr>
              <td colspan="2" align="left">
                '.lang("admin", "per_page").': ';
    $per_page_choices = array(25, 50, 100, 200);

    for ( $i = 0; $i < count($per_page_choices); $i++ )
    {
      if ( $accts_per_page != $per_page_choices[$i] )
        $output .= '<a href="admin.php?section=accounts&amp;order_by='.$order_by.'&amp;start='.$start.'&amp;dir='.( ( $dir ) ? 0 : 1 ).( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;perpage='.$per_page_choices[$i].'">'.$per_page_choices[$i].'</a>';
      else
        $output .= $per_page_choices[$i];

      if ( $i < (count($per_page_choices)-1) )
        $output .= ',&nbsp;';
    }
    $output .= '
              </td>
            </tr>
            <tr>
              <td align="left">'.lang("admin", "total").': '.$all_record.'</td>
              <td align="right">';

    $output .= generate_pagination('admin.php?section=accounts&amp;order_by='.$order_by.'&amp;start='.$start.'&amp;dir='.( ( $dir ) ? 0 : 1 ).( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;perpage='.$accts_per_page, $all_record, $accts_per_page, $start);

    $output .= '
              </td>
            </tr>
          </table>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="accounts" />
            <table class="simple admin_accounts">
              <tr>
                <th width="10%">'.lang("admin", "edit").'</th>
                <th>
                  <a href="admin.php?section=accounts&amp;order_by=acct&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'&amp;perpage='.$accts_per_page.'"'.( ( $order_by == 'acct' ) ? ' class="'.$order_dir.'"' : '' ).'>'.lang("user", "acct").'</a>
                </th>
                <th>
                  <a href="admin.php?section=accounts&amp;order_by=login&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'&amp;perpage='.$accts_per_page.'"'.( ( $order_by == 'login' ) ? ' class="'.$order_dir.'"' : '' ).'>'.lang("admin", "login").'</a>
                </th>
                <th>
                  <a href="admin.php?section=accounts&amp;order_by=ScreenName&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'&amp;perpage='.$accts_per_page.'"'.( ( $order_by == 'ScreenName' ) ? ' class="'.$order_dir.'"' : '' ).'>'.lang("admin", "screenname").'</a>
                </th>
                <th width="20%">
                  <a href="admin.php?section=accounts&amp;order_by=SecurityLevel&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'&amp;perpage='.$accts_per_page.'"'.( ( $order_by == 'SecurityLevel' ) ? ' class="'.$order_dir.'"' : '' ).'>'.lang("admin", "seclvl").'</a>
                </th>
                <th width="15%">
                  <a href="admin.php?section=accounts&amp;order_by=WebAdmin&amp;start='.$start.( ( $search_value && $search_by ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'&amp;perpage='.$accts_per_page.'"'.( ( $order_by == 'WebAdmin' ) ? ' class="'.$order_dir.'"' : '' ).'>'.lang("admin", "acpaccess").'</a>
                </th>
              </tr>';
    $color = "#EEEEEE";
    while ( $acct = $sqll->fetch_assoc($result) )
    {
      $acct["SecurityLevel"] = ( ( isset($acct["SecurityLevel"]) ) ? $acct["SecurityLevel"] : 0 );
      $acct["WebAdmin"] = ( ( isset($acct["WebAdmin"]) ) ? $acct["WebAdmin"] : 0 );
      $acct["ScreenName"] = ( ( isset($acct["ScreenName"]) ) ? $acct["ScreenName"] : "" );

      if ( $acct["SecurityLevel"] >= 1073741824 )
        $acct["SecurityLevel"] -= 1073741824;

      $sl_query = "SELECT * FROM config_gm_level_names WHERE Security_Level='".$acct["SecurityLevel"]."'";
      $sl_result = $sqlm->query($sl_query);
      $sl = $sqlm->fetch_assoc($sl_result);

      $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=accounts&amp;acct='.$acct["login"].'&amp;editacct=editaccount">
                      <img src="img/edit.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$acct["acct"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.ucfirst(strtolower($acct["login"])).'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$acct["ScreenName"].'</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>'.$sl["Full_Name"].' ('.$acct["SecurityLevel"].')</center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>
                    <img src="img/'.( ( $acct["WebAdmin"] ) ? 'up' : 'down' ).'.gif" alt="" />
                  </center>
                </td>
              </tr>';
      $color = ( ( $color == "#EEEEEE" ) ? "#FFFFFF" : "#EEEEEE" );
    }
    $output .= '
            </table>
            <!-- input type="submit" name="editacct" value="'.lang("admin", "editacct").'" -->
          </form>
        </center>';
  }
  else
  {
    if ( isset($_GET["acct"]) )
      $acct = $sqlm->quote_smart($_GET["acct"]);
    else
      redirect("admin.php?section=accounts&error=1");

    if ( $core == 1 )
      $logon_acct = $sqll->fetch_assoc($sqll->query("SELECT * FROM accounts WHERE login='".$acct."'"));
    else
      $logon_acct = $sqll->fetch_assoc($sqll->query("SELECT *, username AS login FROM account WHERE username='".$acct."'"));

    $sl_query = "SELECT * FROM config_gm_level_names";
    $sl_result = $sqlm->query($sl_query);

    $sn_acct = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_accounts WHERE Login='".$acct."'"));

    $sec_level_only = ( ( $sn_acct["SecurityLevel"] ) ? $sn_acct["SecurityLevel"] : 0 );
    if ( $sec_level_only >= 1073741824 )
      $sec_level_only -= 1073741824;

    $web_admin_only = ($sn_acct["SecurityLevel"] & 1073741824);

    $output .= '
        <center>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="accounts" />
            <input type="hidden" name="action" value="saveacct" />
            <fieldset id="admin_edit_account">
              <table>
                <tr>
                  <td width="50%">'.lang("admin", "login").': </td>
                  <td>
                    <input type="text" readonly="readonly" name="login" value="'.$logon_acct["login"].'" />
                  </td>
                </tr>
                <tr>
                  <td>'.lang("admin", "screenname").': </td>
                  <td>
                    <input type="text" name="sn" value="'.$sn_acct["ScreenName"].'" />
                  </td>
                </tr>
                <tr>
                  <td>'.lang("admin", "seclvl").': </td>
                  <td>
                    <select name="sec">';
    while ( $row = $sqlm->fetch_assoc($sl_result) )
    {
      $output .= '
                      <option value="'.$row["Security_Level"].'" '.( ( $sec_level_only == $row["Security_Level"] ) ? 'selected="selected"' : '' ).'>'.$row["Full_Name"].' ('.$row["Security_Level"].')</option>';
    }
    $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="help">
                    <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "acpaccess_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "acpaccess").'</a>:
                  </td>
                  <td>
                    <input type="checkbox" name="acp" '.($web_admin_only ? 'checked' : '').' />
                  </td>
                </tr>
              </table>
            </fieldset>
            <input type="submit" name="saveacct" value="'.lang("admin", "save").'" />
          </form>
        </center>';
  }
}

function saveacct()
{
  global $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  $acct = $sqlm->quote_smart($_GET["login"]);
  $sn = $sqlm->quote_smart($_GET["sn"]);
  $sec = ( ( isset($_GET["sec"]) ) ? $sqlm->quote_smart($_GET["sec"]) : 0 );
  $acp = ( ( isset($_GET["acp"]) ) ? 1 : 0 );

  if ( $acp )
    $sec += 1073741824;

  $result = $sqlm->query("SELECT * FROM config_accounts WHERE Login='".$acct."'");
  if ( $sqlm->num_rows($result) )
    $result = $sqlm->query("UPDATE config_accounts SET ScreenName='".$sn."', SecurityLevel='".$sec."' WHERE Login='".$acct."'");
  else
    $result = $sqlm->query("INSERT INTO config_accounts (Login, ScreenName, SecurityLevel) VALUES ('".$acct."', '".$sn."', '".$sec."')");

  redirect("admin.php?section=accounts");
}

?>
