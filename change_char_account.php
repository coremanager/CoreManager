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
valid_login($action_permission["view"]);


//#############################################################################
// SELECT CHARACTER
//#############################################################################

function sel_char()
{
  global $output, $action_permission, $characters_db, $corem_db, $realm_id, $user_id, $sql, $core;

  valid_login($action_permission["view"]);

  $output .= '
          <center>
            <div id="xname_fieldset" class="fieldset_border">
              <span class="legend">'.lang("xacct", "selectchar").'</span>
              <span class="xname_info">'.lang("xacct", "info").'</span>
              <br />
              <br />
              <form method="GET" action="change_char_account.php" name="form">
                <input type="hidden" name="action" value="chooseacct" />
                <table class="lined" id="xname_char_table">
                  <tr>
                    <th class="xname_radio">&nbsp;</th>
                    <th class="xname_name">'.lang("xacct", "char").'</th>
                    <th class="xname_LRC">'.lang("xacct", "lvl").'</th>
                    <th class="xname_LRC">'.lang("xacct", "race").'</th>
                    <th class="xname_LRC">'.lang("xacct", "class").'</th>
                  </tr>';

  if ( $core == 1 )
    $chars = $sql["char"]->query("SELECT * FROM characters WHERE acct='".$user_id."' AND guid NOT IN (SELECT guid FROM ".$corem_db["name"].".char_changes)");
  else
    $chars = $sql["char"]->query("SELECT * FROM characters WHERE account='".$user_id."' AND guid NOT IN (SELECT guid FROM ".$corem_db["name"].".char_changes)");

  while ( $char = $sql["char"]->fetch_assoc($chars) )
  {
    $output .= '
                  <tr>
                    <td>
                      <input type="radio" name="char" value="'.$char["guid"].'"/>
                    </td>
                    <td>'.$char["name"].'</td>
                    <td>'.char_get_level_color($char["level"]).'</td>
                    <td>
                      <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                    </td>
                    <td>
                      <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                    </td>
                  </tr>';
  }

  $output .= '
                  <tr>
                    <td class="hidden" colspan="5">';
  makebutton(lang("xacct", "selectchar"), "javascript:do_submit()",180);
  $output .= '
                    </td>
                  </tr>
                </table>
                <br />
              </form>
            </div>
          </center>
          <br />';
}


//#############################################################################
// SELECT NEW ACCOUNT
//#############################################################################

function chooseacct()
{
  global $output, $action_permission, $characters_db, $corem_db, $realm_id, $user_name,
    $transfer_credits, $user_id, $user_lvl, $sql, $core;

  valid_login($action_permission["view"]);

  $guid = $sql["char"]->quote_smart($_GET["char"]);
  $new = '';
  if ( isset($_GET["new"]) )
    $new = $sql["char"]->quote_smart($_GET["new"]);

  // if we came here from char_list.php (and have permission)
  // then we need to skip the approval process
  if ( ( $_GET["priority"] == 1 ) && ( $user_lvl >= $action_permission["update"] ) )
    $priority = 1;

  if ( $core == 1 )
    $accts_query = "SELECT acct, accounts.login, IFNULL(`".$corem_db["name"]."`.config_accounts.ScreenName, '') AS ScreenName
    FROM accounts
      LEFT JOIN `".$corem_db["name"]."`.config_accounts ON config_accounts.Login=accounts.login
    WHERE acct<>(SELECT acct FROM `".$characters_db[$realm_id]['name']."`.characters WHERE guid='".$guid."') ORDER BY ScreenName ASC";
  else
    $accts_query = "SELECT id AS acct, username AS login, IFNULL(`".$corem_db["name"]."`.config_accounts.ScreenName, '') AS ScreenName
    FROM account
      LEFT JOIN `".$corem_db["name"]."`.config_accounts ON config_accounts.Login=account.username
    WHERE id<>(SELECT account FROM `".$characters_db[$realm_id]['name']."`.characters WHERE guid='".$guid."') ORDER BY ScreenName ASC";
  $accts = $sql["logon"]->query($accts_query);

  $query = "SELECT * FROM characters WHERE guid='".$guid."'";
  $char = $sql["char"]->fetch_assoc($sql["char"]->query($query));

  // credits
  if ( $transfer_credits >= 0 )
  {
    // get our credit balance
    $cr_query = "SELECT Credits FROM config_accounts WHERE Login='".$user_name."'";
    $cr_result = $sql["mgr"]->query($cr_query);
    $cr_result = $sql["mgr"]->fetch_assoc($cr_result);
    $credits = $cr_result["Credits"];
  }

  $output .= '
          <center>
            <div id="xname_choose" class="fieldset_border">
              <span class="legend">'.lang("xacct", "chooseacct").'</span>
              <form method="get" action="change_char_account.php" name="form">
                <input type="hidden" name="action" value="'.( ( $priority != 1 ) ? 'getapproval' : 'direct' ).'" />
                <input type="hidden" name="guid" value="'.$char["guid"].'" />
                <table id="xname_char_table">
                  <tr>
                    <td rowspan="4"><img src="'.char_get_avatar_img($char["level"], $char["gender"],  $char["race"],  $char["class"]).'" alt="" /></td>
                    <td><span class="xname_char_name">'.$char["name"].'</span></td>
                  </tr>
                  <tr>
                    <td>'.lang("xacct", "level").': '.$char["level"].'</td>
                  </tr>
                  <tr>
                    <td>'.lang("xacct", "race").': '.char_get_race_name($char["race"]).'</td>
                  </tr>
                  <tr>
                    <td>'.lang("xacct", "class").': '.char_get_class_name($char["class"]).'</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>';

  if ( $transfer_credits > 0 )
  {
    $cost_line = lang("xacct", "credit_cost");
    $cost_line = str_replace("%1", '<b>'.$transfer_credits.'</b>', $cost_line);

    $output .= '
                  <tr>
                    <td colspan="2">'.$cost_line.'</td>
                  </tr>';

    if ( $credits >= 0 )
    {
      $credit_balance = lang("xacct", "credit_balance");
      $credit_balance = str_replace("%1", '<b>'.(float)$credits.'</b>', $credit_balance);

      $output .= '
                  <tr>
                    <td colspan="2">'.$credit_balance.'</td>
                  </tr>';

      if ( $credits < $transfer_credits )
        $output .= '
                  <tr>
                    <td colspan="2">'.lang("xacct", "insufficient_credits").'</td>
                  </tr>';
      else
        $output .= '
                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="2">'.lang("xacct", "delay_warning").'</td>
                  </tr>';
    }
    else
      $output .= '
                  <tr>
                    <td colspan="2">'.lang("global", "credits_unlimited").'</td>
                  </tr>';

    $output .= '
                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>';
  }

  $output .= '
                  <tr>
                    <td colspan="2"><b>'.lang("xacct", "enteracct").':</b></td>
                  </tr>
                  <tr>
                    <td>'.lang("xacct", "newacct").':</td>
                    <td>
                      <select name="new">';

  while ( $row = $sql["logon"]->fetch_assoc($accts) )
  {
    $output .= '
                        <option value="'.$row["acct"].'">';
    // GM's see account name
    // Players see Screen Name if available
    if ( $user_lvl < 4 )
    {
      if ( $row["ScreenName"] == '' )
        $output .= $row["login"];
      else
        $output .= $row["ScreenName"];
    }
    else
      $output .= $row["login"];

    $output .= '
                        </option>';
  }

  $output .= '
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>'.lang("xacct", "newacct1").':</td>
                    <td><input type="text" name="new1" value="" /></td>
                  </tr>';

    // if we have unlimited credits, then we fake our credit balance here
    $credits = ( ( $credits < 0 ) ? $transfer_credits : $credits );

    if ( ( $transfer_credits <= 0 ) || ( $credits >= $transfer_credits ) )
    {
      $output .= '
                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                    <td>';
      makebutton(lang("xacct", "save"), "javascript:do_submit()", 180);
      $output .= '
                    </td>
                    <td>&nbsp;</td>
                  </tr>';
    }

    $output .= '
                </table>
              </form>
            </div>
          </center>
          <br />';
}


//#############################################################################
// SUBMIT ACCOUNT CHANGE
//#############################################################################

function getapproval()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id, $user_id,
    $transfer_credits, $sql;

  valid_login($action_permission["view"]);

  $guid = $sql["mgr"]->quote_smart($_GET["guid"]);
  $new = $sql["mgr"]->quote_smart($_GET["new"]);

  if ( $_GET["new1"] != "" )
  {
    $new = $sql["mgr"]->quote_smart($_GET["new1"]);

    if ( !is_numeric($new) )
    {
      if ( $core == 1 )
        $acct_query = "SELECT acct, accounts.login, IFNULL(`".$corem_db["name"]."`.config_accounts.ScreenName, '')
        FROM accounts
          LEFT JOIN `".$corem_db["name"]."`.config_accounts ON config_accounts.Login=accounts.login
        WHERE accounts.login='".$new."' OR config_accounts.Login='".$new."'";
      else
        $acct_query = "SELECT id AS acct, username AS login, IFNULL(`".$corem_db["name"]."`.config_accounts.ScreenName, '')
        FROM account
          LEFT JOIN `".$corem_db["name"]."`.config_accounts ON config_accounts.Login=account.username
        WHERE account.username='".$new."' OR config_accounts.ScreenName='".$new."'";

      $acct_result = $sql["logon"]->query($acct_query);
      $acct_result = $sql["logon"]->fetch_assoc($acct_result);
      $new = $acct_result["acct"];
    }
  }

  $count = $sql["mgr"]->num_rows($sql["mgr"]->query("SELECT * FROM char_changes WHERE guid='".$guid."'"));
  if ( $count )
    redirect("change_char_account.php?error=3");

  // credits
  // we do a credit balance check here in case of URL insertion
  if ( $transfer_credits > 0 )
  {
    // we need the player's account
    if ( $core == 1 )
      $acct_query = "SELECT login AS username FROM accounts WHERE acct=(SELECT acct FROM ".$characters_db[$realm_id]["name"].".characters WHERE guid='".$guid."')";
    else
      $acct_query = "SELECT username FROM account WHERE id=(SELECT account FROM ".$characters_db[$realm_id]["name"].".characters WHERE guid='".$guid."')";

    $acct_result = $sql["logon"]->query($acct_query);
    $acct_result = $sql["logon"]->fetch_assoc($acct_result);
    $username = $acct_result["username"];

    // now we get the user's credit balance
    $cr_query = "SELECT Credits FROM config_accounts WHERE Login='".$username."'";
    $cr_result = $sql["mgr"]->query($cr_query);
    $cr_result = $sql["mgr"]->fetch_assoc($cr_result);
    $credits = $cr_result["Credits"];

    // we fake how many credits the account has if the account is unlimited
    $credits = ( ( $credits < 0 ) ? $transfer_credits : $credits );

    if ( $credits < $transfer_credits )
      redirect("change_char_acount.php?error=6");
  }

  $result = $sql["mgr"]->query("INSERT INTO char_changes (guid, new_acct) VALUES ('".$guid."', '".$new."')");

  redirect("change_char_account.php?error=4");
}


//#############################################################################
// DENY ACOUNT CHANGE
//#############################################################################

function denied()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id, $user_id, $sql, $core;

  valid_login($action_permission["update"]);

  $guid = $sql["mgr"]->quote_smart($_GET["guid"]);

  $result = $sql["mgr"]->query("DELETE FROM char_changes WHERE `guid`='".$guid."'");

  $char = $sql["char"]->fetch_assoc($sql["char"]->query("SELECT * FROM characters WHERE guid='".$guid."'"));

  // Localization
  $body = lang("xacct", "body");
  $body = str_replace("%1", $char["name"], $body);

  redirect("mail.php?action=send_mail&type=ingame_mail&to=".$char["name"]."&subject=".lang("xacct", "subject")."&body=".$body."&group_sign==&group_send=gm_level&money=0&att_item=0&att_stack=0&redirect=index.php");
}


//#############################################################################
// SAVE NEW ACCOUNT
//#############################################################################

function saveacct()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id,
    $user_id, $transfer_credits, $sql, $core;

  valid_login($action_permission["update"]);

  $guid = $sql["mgr"]->quote_smart($_GET["guid"]);

  $acct = $sql["mgr"]->fetch_assoc($sql["mgr"]->query("SELECT * FROM char_changes WHERE guid='".$guid."'"));

  $int_err = 0;

  // credits
  if ( $transfer_credits > 0 )
  {
    // we need the player's account
    if ( $core == 1 )
      $acct_query = "SELECT login AS username FROM accounts WHERE acct=(SELECT acct FROM ".$characters_db[$realm_id]["name"].".characters WHERE guid='".$guid."')";
    else
      $acct_query = "SELECT username FROM account WHERE id=(SELECT account FROM ".$characters_db[$realm_id]["name"].".characters WHERE guid='".$guid."')";

    $acct_result = $sql["logon"]->query($acct_query);
    $acct_result = $sql["logon"]->fetch_assoc($acct_result);
    $username = $acct_result["username"];

    // now we get the user's credit balance
    $cr_query = "SELECT Credits FROM config_accounts WHERE Login='".$username."'";
    $cr_result = $sql["mgr"]->query($cr_query);
    $cr_result = $sql["mgr"]->fetch_assoc($cr_result);
    $credits = $cr_result["Credits"];

    // since this action is delayed, we have to make sure the account still has sufficient funds
    // if the account doesn't have enough, we just delete the change request
    if ( ( $credits >= 0 ) && ( $credits < $transfer_credits ) )
      $int_err = 1;

    if ( !$int_err )
    {
      // we don't charge credits if the account is unlimited
      if ( $credits >= 0 )
        $credits = $credits - $transfer_credits;

      $money_query = "UPDATE config_accounts SET Credits='".$credits."' WHERE Login='".$username."'";

      $money_result = $sql["mgr"]->query($money_query);
    }
  }

  if ( !$int_err )
  {
    if ( $core == 1 )
      $result = $sql["char"]->query("UPDATE characters SET acct='".$acct["new_acct"]."' WHERE guid='".$guid."'");
    else
      $result = $sql["char"]->query("UPDATE characters SET account='".$acct["new_acct"]."' WHERE guid='".$guid."'");
  }

  $result = $sql["mgr"]->query("DELETE FROM char_changes WHERE guid='".$guid."'");

  redirect("index.php");
}


//#############################################################################
// SAVE NEW ACCOUNT (DIRECT APPROVAL VIA CHAR_LIST.PHP)
//#############################################################################

function saveacct_direct()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id,
    $user_id, $sql, $core;

  valid_login($action_permission["update"]);

  $guid = $sql["mgr"]->quote_smart($_GET["guid"]);
  $new = $sql["mgr"]->quote_smart($_GET["new"]);

  if ( $_GET["new1"] != "" )
  {
    $new = $sql["mgr"]->quote_smart($_GET["new1"]);

    if ( !is_numeric($new) )
    {
      if ( $core == 1 )
        $acct_query = "SELECT acct, accounts.login, IFNULL(`".$corem_db["name"]."`.config_accounts.ScreenName, '')
        FROM accounts
          LEFT JOIN `".$corem_db["name"]."`.config_accounts ON config_accounts.Login=accounts.login
        WHERE accounts.login='".$new."' OR config_accounts.Login='".$new."'";
      else
        $acct_query = "SELECT id AS acct, username AS login, IFNULL(`".$corem_db["name"]."`.config_accounts.ScreenName, '')
        FROM account
          LEFT JOIN `".$corem_db["name"]."`.config_accounts ON config_accounts.Login=account.username
        WHERE account.username='".$new."' OR config_accounts.ScreenName='".$new."'";

      $acct_result = $sql["logon"]->query($acct_query);
      $acct_result = $sql["logon"]->fetch_assoc($acct_result);
      $new = $acct_result["acct"];
    }
  }

  if ( $core == 1 )
    $result = $sql["char"]->query("UPDATE characters SET acct='".$new."' WHERE guid='".$guid."'");
  else
    $result = $sql["char"]->query("UPDATE characters SET account='".$new."' WHERE guid='".$guid."'");

  redirect("char_list.php");
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = ( ( isset($_GET["error"]) ) ? $_GET["error"] : NULL );

$output .= '
        <div class="bubble">
          <div class="top">';

if ( $err == 1 )
  $output .= '
            <h1><font class="error">'.lang("global", "empty_fields").'</font></h1>';
elseif ( $err == 2 )
  $output .= '
            <h1><font class="error">'.lang("xacct", "nomatch").'</font></h1>';
elseif ( $err == 3 )
  $output .= '
            <h1><font class="error">'.lang("xacct", "already").'</font></h1>';
elseif ( $err == 4 )
  $output .= '
            <h1>'.lang("xacct", "done").'</h1>';
elseif ( $err == 6 )
  $output .= '
            <h1><font class="error">'.lang("xacct", "insufficient_credits").'</font></h1>';
else
  $output .= '
            <h1>'.lang("xacct", "changename").'</h1>';

unset($err);

$output .= '
          </div>';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

if ( $action == 'chooseacct' )
  chooseacct();
elseif ( $action == 'getapproval' )
  getapproval();
elseif ( $action == 'denied' )
  denied();
elseif ( $action == 'approve' )
  saveacct();
elseif ( $action == 'direct' )
  saveacct_direct();
else
  sel_char();

unset($action);
unset($action_permission);

require_once 'footer.php';


?>
