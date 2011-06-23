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
              <span class="legend">'.lang("xname", "selectchar").'</span>
              <span class="xname_info">'.lang("xname", "info").'</span>
              <br />
              <br />
              <form method="GET" action="change_char_name.php" name="form">
                <input type="hidden" name="action" value="choosename" />
                <table class="lined" id="xname_char_table">
                  <tr>
                    <th class="xname_radio">&nbsp;</th>
                    <th class="xname_name">'.lang("xname", "char").'</th>
                    <th class="xname_LRC">'.lang("xname", "lvl").'</th>
                    <th class="xname_LRC">'.lang("xname", "race").'</th>
                    <th class="xname_LRC">'.lang("xname", "class").'</th>
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
  makebutton(lang("xname", "selectchar"), "javascript:do_submit()",180);
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
// SELECT NEW NAME
//#############################################################################

function choosename()
{
  global $output, $action_permission, $characters_db, $realm_id, $user_id, $user_name, $name_credits,
    $sql, $core;

  valid_login($action_permission["view"]);

  $guid = $sql["char"]->quote_smart($_GET["char"]);
  $new1 = '';
  if ( isset($_GET["new1"]) )
    $new1 = $sql["char"]->quote_smart($_GET["new1"]);
  $new2 = '';
  if ( isset($_GET["new2"]) )
    $new2 = $sql["char"]->quote_smart($_GET["new2"]);

  $query = "SELECT * FROM characters WHERE guid='".$guid."'";
  $char = $sql["char"]->fetch_assoc($sql["char"]->query($query));

  // credits
  if ( $name_credits >= 0 )
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
              <span class="legend">'.lang("xname", "choosename").'</span>
              <form method="GET" action="change_char_name.php" name="form">
                <input type="hidden" name="action" value="getapproval" />
                <input type="hidden" name="guid" value="'.$char["guid"].'" />
                <table id="xname_char_table">
                  <tr>
                    <td rowspan="4"><img src="'.char_get_avatar_img($char["level"], $char["gender"],  $char["race"],  $char["class"]).'" alt="" /></td>
                    <td><span class="xname_char_name">'.$char["name"].'</span></td>
                  </tr>
                  <tr>
                    <td>'.lang("xname", "level").': '.$char["level"].'</td>
                  </tr>
                  <tr>
                    <td>'.lang("xname", "race").': '.char_get_race_name($char["race"]).'</td>
                  </tr>
                  <tr>
                    <td>'.lang("xname", "class").': '.char_get_class_name($char["class"]).'</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>';

  if ( $name_credits > 0 )
  {
    $cost_line = lang("xname", "credit_cost");
    $cost_line = str_replace("%1", '<b>'.$name_credits.'</b>', $cost_line);

    $output .= '
                  <tr>
                    <td colspan="2">'.$cost_line.'</td>
                  </tr>';

    if ( $credits >= 0 )
    {
      $credit_balance = lang("xname", "credit_balance");
      $credit_balance = str_replace("%1", '<b>'.(float)$credits.'</b>', $credit_balance);

      $output .= '
                  <tr>
                    <td colspan="2">'.$credit_balance.'</td>
                  </tr>';

      if ( $credits < $name_credits )
        $output .= '
                  <tr>
                    <td colspan="2">'.lang("xname", "insufficient_credits").'</td>
                  </tr>';
      else
        $output .= '
                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="2">'.lang("xname", "delay_warning").'</td>
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
                    <td colspan="2"><b>'.lang("xname", "entername").':</b></td>
                  </tr>
                  <tr>
                    <td>'.lang("xname", "newname").':</td>
                    <td><input type="text" name="new1" value="'.$new1.'" maxlength="12" /></td>
                  </tr>
                  <tr>
                    <td>'.lang("xname", "confirmname").':</td>
                    <td><input type="text" name="new2" value="'.$new1.'" maxlength="12" /></td>
                  </tr>';

    // if we have unlimited credits, then we fake our credit balance here
    $credits = ( ( $credits < 0 ) ? $name_credits : $credits );

    if ( ( $name_credits <= 0 ) || ( $credits >= $name_credits ) )
    {
      $output .= '
                  <tr>
                    <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                    <td>';
      makebutton(lang("xname", "save"), "javascript:do_submit()", 180);
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
// SUBMIT NAME CHANGE
//#############################################################################

function getapproval()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id, $user_id,
    $name_credits, $sql;

  valid_login($action_permission["view"]);

  $guid = $sql["mgr"]->quote_smart($_GET["guid"]);
  $new1 = $sql["mgr"]->quote_smart($_GET["new1"]);
  $new2 = $sql["mgr"]->quote_smart($_GET["new2"]);

  if ( $new1 <> $new2 )
    redirect("change_char_name.php?action=choosename&char=".$guid."&new1=".$new1."&new2=".$new2."&error=2");

  $count = $sql["mgr"]->num_rows($sql["mgr"]->query("SELECT * FROM char_changes WHERE guid='".$guid."'"));
  if ( $count )
    redirect("change_char_name.php?error=3");

  $count = $sql["char"]->num_rows($sql["char"]->query("SELECT * FROM characters WHERE name='".$new1."'"));
  if ( $count )
    redirect("change_char_name.php?error=4");

  // credits
  // we do a credit balance check here in case of URL insertion
  if ( $name_credits > 0 )
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
    $credits = ( ( $credits < 0 ) ? $name_credits : $credits );

    if ( $credits < $name_credits )
      redirect("change_char_name.php?error=6");
  }

  $result = $sql["mgr"]->query("INSERT INTO char_changes (guid, new_name) VALUES ('".$guid."', '".$new1."')");

  redirect("change_char_name.php?error=5");
}


//#############################################################################
// DENY NAME CHANGE
//#############################################################################

function denied()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id, $user_id, $sql, $core;

  valid_login($action_permission["update"]);

  $guid = $sql["mgr"]->quote_smart($_GET["guid"]);

  $result = $sql["mgr"]->query("DELETE FROM char_changes WHERE `guid`='".$guid."'");

  $char = $sql["char"]->fetch_assoc($sql["char"]->query("SELECT * FROM characters WHERE guid='".$guid."'"));

  // Localization
  $body = lang("xname", "body");
  $body = str_replace("%1", $char["name"], $body);

  redirect("mail.php?action=send_mail&type=ingame_mail&to=".$char["name"]."&subject=".lang("xname", "subject")."&body=".$body."&group_sign==&group_send=gm_level&money=0&att_item=0&att_stack=0&redirect=index.php");
}


//#############################################################################
// SAVE NEW NAME
//#############################################################################

function savename()
{
  global $output, $action_permission, $corem_db, $characters_db, $realm_id,
    $user_id, $name_credits, $sql, $core;

  valid_login($action_permission["update"]);

  $guid = $sql["mgr"]->quote_smart($_GET["guid"]);

  $name = $sql["mgr"]->fetch_assoc($sql["mgr"]->query("SELECT * FROM char_changes WHERE guid='".$guid."'"));

  $int_err = 0;

  // credits
  if ( $name_credits > 0 )
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
    if ( ( $credits >= 0 ) && ( $credits < $name_credits ) )
      $int_err = 1;

    if ( !$int_err )
    {
      // we don't charge credits if the account is unlimited
      if ( $credits >= 0 )
        $credits = $credits - $name_credits;

      $money_query = "UPDATE config_accounts SET Credits='".$credits."' WHERE Login='".$username."'";

      $money_result = $sql["mgr"]->query($money_query);
    }
  }

  if ( !$int_err )
    $result = $sql["char"]->query("UPDATE characters SET name='".$name["new_name"]."' WHERE guid='".$guid."'");

  $result = $sql["mgr"]->query("DELETE FROM char_changes WHERE guid='".$guid."'");

  redirect("index.php");
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
            <h1><font class="error">'.lang("xname", "nomatch").'</font></h1>';
elseif ( $err == 3 )
  $output .= '
            <h1><font class="error">'.lang("xname", "already").'</font></h1>';
elseif ( $err == 4 )
  $output .= '
            <h1><font class="error">'.lang("xname", "inuse").'</font></h1>';
elseif ( $err == 5 )
  $output .= '
            <h1>'.lang("xname", "done").'</h1>';
elseif ( $err == 6 )
  $output .= '
            <h1><font class="error">'.lang("xname", "insufficient_credits").'</font></h1>';
else
  $output .= '
            <h1>'.lang("xname", "changename").'</h1>';

unset($err);

$output .= '
          </div>';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

if ( $action == 'choosename' )
  choosename();
elseif ( $action == 'getapproval' )
  getapproval();
elseif ( $action == 'denied' )
  denied();
elseif ( $action == 'approve' )
  savename();
else
  sel_char();

unset($action);
unset($action_permission);

require_once 'footer.php';


?>
