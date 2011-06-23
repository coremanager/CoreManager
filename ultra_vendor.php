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


require_once("header.php");
require_once("libs/char_lib.php");
valid_login($action_permission["view"]);

//########################################################################################################################
// SHOW CHARACTER LIST
//########################################################################################################################
function show_list()
{
  global $realm_id, $output, $logon_db, $characters_db, $itemperpage, $action_permission, $user_lvl, $sql, $core;

  valid_login($action_permission["view"]);

  if ( $core == 1 )
    $query = "SELECT * FROM characters WHERE acct='".$_SESSION["user_id"]."'";
  else
    $query = "SELECT * FROM characters WHERE account='".$_SESSION["user_id"]."'";
  $result = $sql["char"]->query($query);
  $num_rows = $sql["char"]->num_rows($result);

  $output .= '
        <table class="top_hidden">
          <tr>
            <td>
              <center>
                <div class="half_frame fieldset_border">
                  <span class="legend">'.lang("ultra", "selectchar").'</span>';
  if ( $num_rows == 0 )
  {
    // Localization
    $nochars = lang("ultra", "nochars");
    $nochars = str_replace("%1", $_SESSION["login"], $nochars);

    $output .= '
                  <table>
                    <tr>
                      <td>
                        <b>'.$nochars.'</b>
                      </td>
                    </tr>
                    <tr>
                      <td>';
    makebutton(lang("global", "back"), "javascript:window.history.back()\" type=\"def", 130);
    $output .= '
                      </td>
                    </tr>
                  </table>';
  }
  else
  {
    $output .= '
                  <form method="get" action="ultra_vendor.php" name="form">
                    <input type="hidden" name="action" value="selected_char" />
                      <table class="lined" id="xname_char_table">
                        <tr>
                          <th class="xname_radio">&nbsp;</th>
                          <th class="xname_name">'.lang("xname", "char").'</th>
                          <th class="xname_LRC">'.lang("xname", "lvl").'</th>
                          <th class="xname_LRC">'.lang("xname", "race").'</th>
                          <th class="xname_LRC">'.lang("xname", "class").'</th>
                        </tr>';
    if ( $num_rows > 1 )
    {
      while ($field = $sql["char"]->fetch_assoc($result))
      {
        $output .= '
                        <tr>
                          <td>
                            <input type="radio" name="charname" value="'.$field["name"].'" />
                          </td>
                          <td>'.$field["name"].'</td>
                          <td>'.char_get_level_color($field["level"]).'</td>
                          <td>
                            <img src="img/c_icons/'.$field["race"].'-'.$field["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($field["race"]).'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                          </td>
                          <td>
                            <img src="img/c_icons/'.$field["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($field["class"]).'\',\'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                          </td>
                        </tr>';
      }
    }
    else
    {
      $field = $sql["char"]->fetch_assoc($result);
      $output .= '
                        <tr>
                          <td>
                            <input type="radio" name="charname" value="'.$field["name"].'" checked="true" />'.$field["name"].'
                          </td>
                        </tr>';
    }
    $output .= '
                        <tr>
                          <td class="hidden" colspan="3">';
    makebutton(lang("ultra", "select"), "javascript:do_submit()\" type=\"def",180);
    $output .= '
                          </td>
                          <td class="hidden" colspan="2">';
    makebutton(lang("global", "back"), "javascript:window.history.back()\" type=\"def",130);
    $output .= '
                          </td>
                        </tr>
                      </table>
                    </form>';
  }
  $output .= '
                  </div>
                </center>
              </td>
            </tr>
          </table>';

}


//########################################################################################################################
// SHOW SELECT ITEM SCREEN
//########################################################################################################################
function select_item()
{
  global $world_db, $characters_db, $realm_id, $user_name, $output, $action_permission,
    $base_datasite, $name_datasite, $user_lvl, $sql;

  valid_login($action_permission["view"]);

  if ( empty($_GET["charname"]) )
    redirect("ultra_vendor.php?error=1");

  $output .= '
          <table class="top_hidden">
            <tr>
              <td>
                <center>
                  <div class="half_frame fieldset_border">
                    <span class="legend">'.lang("ultra", "selectitem").'</span>
                    <form method="get" action="ultra_vendor.php" name="form">
                      <input type="hidden" name="action" value="selected_item" />
                      <input type="hidden" name="charname" value="'.$_GET["charname"].'" />'
                      .lang("ultra", "itemline1").'.
                      <br />
                      <br />
                      <small>
                        ('.lang("ultra", "itemline2").' <a href="'.$base_datasite.'">'.$name_datasite.'</a>.
                        <br />'
                        .lang("ultra", "itemline3").'.)
                      </small>
                      <br />
                      <br />
                      <input name="myItem" type="text" />
                      <br />
                      <br />
                      <table>
                        <tr>
                          <td>';
  makebutton(lang("ultra", "select"), "javascript:do_submit()\" type=\"def", 180);
  $output .= '
                          </td>
                          <td>';
  makebutton(lang("global", "back"), "javascript:window.history.back()\" type=\"def", 130);
  $output .= '
                          </td>
                        </tr>
                      </table>
                    </form>
                  </div>
                </center>
              </td>
            </tr>
          </table>';
}


//########################################################################################################################
// SELECT QUANTITY OF ITEM
//########################################################################################################################
function select_quantity()
{
  global $world_db, $characters_db, $realm_id, $user_name, $output, $action_permission, $user_lvl,
    $locales_search_option, $ultra_mult, $ultra_base, $uv_credits, $uv_money, $credits_fractional,
    $sql, $core;

  valid_login($action_permission["view"]);

  if ( empty($_GET["myItem"]) )
    redirect("ultra_vendor.php?error=1");

  if ( $core == 1 )
    $iquery = "SELECT * FROM items "
                .( ( $locales_search_option != 0 ) ? "LEFT JOIN items_localized ON (items_localized.entry=items.entry AND language_code='".$locales_search_option."') " : " " ).
              "WHERE items.entry='".$_GET["myItem"]."'";
  else
    $iquery = "SELECT *,
              name AS name1, Quality AS quality, SellPrice AS sellprice, BuyPrice AS buyprice
              FROM item_template "
                .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_item ON locales_item.entry=item_template.entry " : " " ).
              "WHERE item_template.entry='".$_GET["myItem"]."'";
  $iresult = $sql["world"]->query($iquery);
  $item = $sql["world"]->fetch_assoc($iresult);

  // Localization
  if ( $locales_search_option != 0 )
  {
    if ( $core == 1 )
      $item["name1"] = $item["name"];
    else
      $item["name1"] = $item["name_loc".$locales_search_option];
  }
  else
    $item["name1"] = $item["name1"];

  if ( $core == 1 )
    $cquery = "SELECT guid, level, gold FROM characters WHERE name='".$_GET["charname"]."'";
  else
    $cquery = "SELECT guid, level, money AS gold FROM characters WHERE name='".$_GET["charname"]."'";
  $cresult = $sql["char"]->query($cquery);
  $char = $sql["char"]->fetch_assoc($cresult);

  $chargold = $char["gold"];
  $chargold = str_pad($chargold, 4, "0", STR_PAD_LEFT);
  $pg = substr($chargold,  0, -4);
  if ( $pg == '' )
    $pg = 0;
  $ps = substr($chargold, -4,  2);
  if ( ( $ps == '' ) || ( $ps == '00' ) )
    $ps = 0;
  $pc = substr($chargold, -2);
  if ( ( $pc == '' ) || ( $pc == '00' ) )
    $pc = 0;

  $mul = $ultra_mult[$item["quality"]];
  $qual = quality($item["quality"]);

  if ( $item["sellprice"] <> 0 )
    $base_price = $item["sellprice"];
  else
  {
    if ( $item["buyprice"] == 0 )
      $base_price = $ultra_base;
    else
      $base_price = $item["buyprice"];
  }

  $output .= '
          <table class="top_hidden">
            <tr>
              <td>
                <center>
                  <div class="half_frame fieldset_border">
                    <span class="legend">'.lang("ultra", "selectquantity").'</span>';

  $gold = $mul * $base_price;
  $gold = str_pad($gold, 4, "0", STR_PAD_LEFT);
  $cg = substr($gold,  0, -4);
  if ( $cg == '' )
    $cg = 0;
  $cs = substr($gold, -4,  2);
  if ( ( $cs == '' ) || ( $cs == '00' ) )
    $cs = 0;
  $cc = substr($gold, -2);
  if ( ( $cc == '' ) || ( $cc == '00' ) )
    $cc = 0;
  $gold = $mul * $base_price;

  $base_gold = $base_price;
  $base_gold = str_pad($base_gold, 4, "0", STR_PAD_LEFT);
  $bg = substr($base_gold,  0, -4);
  if ( $bg == '' )
    $bg = 0;
  $bs = substr($base_gold, -4,  2);
  if ( ( $bs == '' ) || ( $bs == '00' ) )
    $bs = 0;
  $bc = substr($base_gold, -2);
  if ( ( $bc == '' ) || ( $bc == '00' ) )
    $bc = 0;

  // Localization
  $isranked = lang("ultra", "isranked");
  $isranked = str_replace("%1", '<b>'.$item["name1"].'</b>', $isranked);
  $isranked = str_replace("%2", '<b>"'.$qual.'"</b>', $isranked);

  $output .= $isranked;
  $output .= '
                    <br />';

  // Localization
  $willcost = lang("ultra", "willcost");
  $willcost = str_replace("%1", '<span id="uv_mul">'.$mul.'</span>', $willcost);
  $cost_display = $bg.'<img src="img/gold.gif" alt="" align="middle" />'
                    .$bs.'<img src="img/silver.gif" alt="" align="middle" />'
                    .$bc.'<img src="img/copper.gif" alt="" align="middle" />';
  $willcost = str_replace("%2", $cost_display, $willcost);

  $output .= $willcost;
  $output .= '
                    <br />';

  // Localization
  $orcost = lang("ultra", "or");
  $or_display = $cg.'<img src="img/gold.gif" alt="" align="middle" />'
                .$cs.'<img src="img/silver.gif" alt="" align="middle" />'
                .$cc.'<img src="img/copper.gif" alt="" align="middle" />';
  $orcost = str_replace("%1", $or_display, $orcost);

  $output .= $orcost;
  $output .= '
                    <br />
                    <br />';

  // Localization
  $charhas = lang("ultra", "has");
  $charhas = str_replace("%1", '<b>'.$_GET["charname"].'</b>', $charhas);
  $money_display = $pg.'<img src="img/gold.gif" alt="" align="middle" />'
                  .$ps.'<img src="img/silver.gif" alt="" align="middle" />'
                  .$pc.'<img src="img/copper.gif" alt="" align="middle" />';
  $charhas = str_replace("%2", $money_display, $charhas);

  $output .= $charhas;
  $output .= '
                    <br />
                    <br />';

  // credits
  if ( $uv_money > 0 )
  {
    // get our credit balance
    $query = "SELECT Credits FROM config_accounts WHERE Login='".$user_name."'";
    $result = $sql["mgr"]->query($query);
    $result = $sql["mgr"]->fetch_assoc($result);
    $credits = $result["Credits"];

    if ( $credits < 0 )
    {
      // unlimited credits
      $output .= lang("global", "credits_unlimited");
      $output .= '
                    <br />
                    <br />';
    }
    elseif ( $credits >= 0 )
    {
      $credit_cost = $uv_credits * ($gold / $uv_money);

      // if Allow Fractional Credits is disabled then cost must be a whole number
      $credit_cost = ( ( !$credits_fractional ) ? ceil($credit_cost) : $credit_cost );

      $credits_per_item = lang("ultra", "credits_peritem");
      $credits_per_item = str_replace("%1", '<b>'.$credit_cost.'</b>', $credits_per_item);
      $credits_per_item = str_replace("%2", '<b>'.$item["name1"].'</b>', $credits_per_item);

      $output .= $credits_per_item;
      $output .= '
                    <br />
                    <br />';

      $credits_avail = lang("ultra", "credits_avail");
      $credits_avail = str_replace("%1", '<b>'.(float)$credits.'</b>', $credits_avail);

      $output .= $credits_avail;
      $output .= '
                    <br />
                    <br />';
    }
  }

  $output .= '
                    <br />
                    <br />
                    <form method="get" action="ultra_vendor.php" name="form">
                      <input type="hidden" name="action" value="selected_quantity" />
                      <input type="hidden" name="charname" value="'.$_GET["charname"].'" />
                      <input type="hidden" name="gold" value="'.$gold.'" />
                      <input type="hidden" name="item" value="'.$item["entry"].'" />'
                      .lang("ultra", "wanted").':
                      <input type="text" name="want" value="0" />
                      <br />
                      <br />
                      <table>
                        <tr>
                          <td>';
  makebutton(lang("ultra", "submit"), "javascript:do_submit()\" type=\"def",180);
  $output .= '
                          </td>
                          <td>';
  makebutton(lang("global", "back"), "javascript:window.history.back()\" type=\"def",130);
  $output .= '
                          </td>
                        </tr>
                      </table>
                    </form>
                  </div>
                </center>
              </td>
            </tr>
          </table>';
}


//########################################################################################################################
// APPROVE TOTAL COST AND PURCHASE
//########################################################################################################################
function approve()
{
  global $world_db, $characters_db, $realm_id, $user_name, $output, $action_permission, $user_lvl,
    $locales_search_option, $quest_item, $uv_credits, $uv_money, $credits_fractional, $sql, $core;

  valid_login($action_permission["view"]);

  if ( !( is_numeric($_GET["item"]) ) )
    redirect("ultra_vendor.php?error=1");
  if ( !( is_numeric($_GET["gold"]) ) )
    redirect("ultra_vendor.php?error=1");
  if ( !( is_numeric($_GET["want"]) ) )
    redirect("ultra_vendor.php?error=1");

  if ( $core == 1 )
    $query = "SELECT * FROM items "
                .( ( $locales_search_option != 0 ) ? "LEFT JOIN items_localized ON (items_localized.entry=items.entry AND language_code='".$locales_search_option."') " : " " ).
              "WHERE items.entry='".$_GET["item"]."'";
  else
    $query = "SELECT *, name AS name1 FROM item_template "
                .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_item ON locales_item.entry=item_template.entry " : " " ).
              "WHERE item_template.entry='".$_GET["item"]."'";
  $result = $sql["world"]->query($query);
  $item = $sql["world"]->fetch_assoc($result);

  // Localization
  if ( $locales_search_option != 0 )
  {
    if ( $core == 1 )
      $item["name1"] = $item["name"];
    else
      $item["name1"] = $item["name_loc".$locales_search_option];
  }
  else
    $item["name1"] = $item["name1"];

  $cquery = "SELECT *, money AS gold FROM characters WHERE name='".$_GET["charname"]."'";
  $cresult = $sql["char"]->query($cquery);
  $char = $sql["char"]->fetch_assoc($cresult);

  $total = $_GET["gold"] * $_GET["want"];
  $total = str_pad($total, 4, "0", STR_PAD_LEFT);
  $cg = substr($total,  0, -4);
  if ( $cg == '' )
    $cg = 0;
  $cs = substr($total, -4,  2);
  if ( ( $cs == '' ) || ( $cs == '00' ) )
    $cs = 0;
  $cc = substr($total, -2);
  if ( ( $cc == '' ) || ( $cc == '00' ) )
    $cc = 0;

  // credits
  if ( $uv_money > 0 )
  {
    // get our credit balance
    $cr_query = "SELECT Credits FROM config_accounts WHERE Login='".$user_name."'";
    $cr_result = $sql["mgr"]->query($cr_query);
    $cr_result = $sql["mgr"]->fetch_assoc($cr_result);
    $credits = $cr_result["Credits"];

    $credit_cost = $uv_credits * ($_GET["gold"] / $uv_money);

    // if Allow Fractional Credits is disabled then cost must be a whole number
    $credit_cost = ( ( !$credits_fractional ) ? ceil($credit_cost) : $credit_cost );

    // multiply by quantity desired
    $credit_cost = $credit_cost * $_GET["want"];
  }

  $output .= '
          <table class="top_hidden">
            <tr>
              <td>
                <center>
                  <div class="half_frame fieldset_border">
                    <span class="legend">'.lang("ultra", "approvecost").'</span>
                    <table>';

  if ( $_GET["want"] <> 0 )
  {
    if ( $total > $char["gold"] )
    {
      // Localization
      $poor = lang("ultra", "insufficientfunds");
      $poor = str_replace("%1", '<b>'.$char["name"].'</b>', $poor);
      $poor = str_replace("%2", '<span id="uv_insufficient_funds">'.$_GET["want"].'</span>',$poor);
      $poor = str_replace("%3", '<b>'.$item["name1"].'</b>', $poor);

      $output .= '
                      <tr>
                        <td colspan="3">
                          <center>';
      $output .= $poor;
      $output .= '
                          </center>
                        </td>
                      </tr>';
    }
    else
    {
      // Localization
      $purchase = lang("ultra", "purchase");
      $purchase = str_replace("%1", '<span id="uv_approve_quantity">'.$_GET["want"].'</span>', $purchase);
      $purchase = str_replace("%2", '<b>'.$item["name1"].'</b>', $purchase);
      $cost_display = $cg.'<img src="img/gold.gif" alt="" align="middle" /> '
                      .$cs.'<img src="img/silver.gif" alt="" align="middle" /> '
                      .$cc.'<img src="img/copper.gif" alt="" align="middle" />';
      $purchase = str_replace("%3", $cost_display, $purchase);

      $output .= '
                        <tr>
                          <td colspan="3">
                            <center>';
      $output .= $purchase;
      $output .= '
                            </center>
                          </td>
                        </tr>';
    }

    if ( ( $total > $char["gold"] ) && ( ( $credit_cost > $credits ) && ( $credits >= 0 ) ) )
    {
      $output .= '
                      <tr>
                        <td colspan="3">
                          <center>'.lang("ultra", "and_credits").'</center>
                        </td>
                      </tr>';
    }
    else
    {
      $output .= '
                      <tr>
                        <td colspan="3">
                          <center>'.lang("ultra", "or_credits").'</center>
                        </td>
                      </tr>';
    }

    if ( $credits >= 0 )
    {
      if ( $credit_cost > $credits )
      {
        // Localization
        $poor = lang("ultra", "insufficient_credits");
        $poor = str_replace("%1", '<span id="uv_insufficient_funds">'.$_GET["want"].'</span>', $poor);
        $poor = str_replace("%2", '<b>'.$item["name1"].'</b>', $poor);

        $output .= '
                      <tr>
                        <td colspan="3">
                          <center>';
        $output .= $poor;
        $output .= '
                          </center>
                        </td>
                      </tr>';
      }
      else
      {
        // Localization
        $purchase = lang("ultra", "credits_purchase");
        $purchase = str_replace("%1", '<span id="uv_approve_quantity">'.$_GET["want"].'</span>', $purchase);
        $purchase = str_replace("%2", '<b>'.$item["name1"].'</b>', $purchase);
        $purchase = str_replace("%3", $credit_cost, $purchase);

        $output .= '
                      <tr>
                        <td colspan="3">
                          <center>';
        $output .= $purchase;
        $output .= '
                          </center>
                        </td>
                      </tr>';
      }
    }
    else
    {
      // Unlimited Credits
      $output .= '
                      <tr>
                        <td colspan="3">
                          <center>'.lang("ultra", "credits_unlimited").'</center>
                        </td>
                      </tr>';

      // to make deciding whether to display the Use Credits button easier,
      // we'll fake our credit balance...
      $credits = $credit_cost;
    }

    if ( ( $total > $char["gold"] ) && ( $credit_cost > $credits ) )
    {
      $output .= '
                      <tr>
                        <td align="left">';
      makebutton(lang("global", "back"), "javascript:window.history.back()\" type=\"def", 130);
      $output .= '
                        </td>
                      </tr>';
    }
    else
    {
      $output .= '
                        <tr>';
      if ( $total <= $char["gold"] )
      {
        $output .= '
                          <td>';
        makebutton(lang("ultra", "submit_money"), "ultra_vendor.php?action=purchase&amp;mode=money&amp;char=".$char["name"]."&amp;item=".$item["entry"]."&amp;want=".$_GET["want"]."&amp;total=".$total."\" type=\"def", 180);
        $output .= '
                          </td>';
      }
      else
      {
        $output .= '
                          <td></td>';
      }

      if ( $credit_cost <= $credits )
      {
        $output .= '
                          <td>';
        makebutton(lang("ultra", "submit_credits"), "ultra_vendor.php?action=purchase&amp;mode=credits&amp;char=".$char["name"]."&amp;item=".$item["entry"]."&amp;want=".$_GET["want"]."&amp;total=".$credit_cost."\" type=\"def", 180);
        $output .= '
                          </td>';
      }
      else
      {
        $output .= '
                          <td></td>';
      }

      $output .= '
                          <td>';
      makebutton(lang("global", "back"), "javascript:window.history.back()\" type=\"def", 130);
      $output .= '
                          </td>
                        </tr>';
    }
  }
  else
  {
    $output .= '
                      <tr>
                        <td>'.lang("ultra", "insufficientquantity").'</td>
                      </tr>
                      <tr>
                        <td>';
    makebutton(lang("global", "back"), "javascript:window.history.back()\" type=\"def", 130);
    $output .= '
                        </td>
                      </tr>';
  }

  $output .= '
                    </table>
                  </div>
                </center>
              </td>
            </tr>
          </table>';
}


//########################################################################################################################
// CHARGE THE CHARACTER AND SEND THE ITEM
//########################################################################################################################
function purchase()
{
  global $world_db, $characters_db, $realm_id, $user_name, $output, $action_permission, $user_lvl,
    $from_char, $stationary, $sql, $core;

  valid_login($action_permission["view"]);

  if ( empty($_GET["item"]) )
    redirect("ultra_vendor.php?error=1");
  if ( empty($_GET["total"]) )
    redirect("ultra_vendor.php?error=1");
  if ( empty($_GET["want"]) )
    redirect("ultra_vendor.php?error=1");

  $mode = $_GET["mode"];

  if ( $core == 1 )
    $iquery = "SELECT * FROM items "
                .( ( $locales_search_option != 0 ) ? "LEFT JOIN items_localized ON (items_localized.entry=items.entry AND language_code='".$locales_search_option."') " : " " ).
              "WHERE items.entry='".$_GET["item"]."'";
  else
    $iquery = "SELECT * FROM item_template "
                .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_item ON locales_item.entry=item_template.entry " : " " ).
              "WHERE item_template.entry='".$_GET["item"]."'";
  $iresult = $sql["world"]->query($iquery);
  $item = $sql["world"]->fetch_assoc($iresult);

  // Localization
  if ( $locales_search_option != 0 )
  {
    if ( $core == 1 )
      $item["name1"] = $item["name"];
    else
      $item["name1"] = $item["name_loc".$locales_search_option];
  }
  else
    $item["name1"] = $item["name1"];

  $cquery = "SELECT *, money AS gold FROM characters WHERE name='".$_GET["char"]."'";
  $cresult = $sql["char"]->query($cquery);
  $char = $sql["char"]->fetch_assoc($cresult);

  if ( $mode == "money" )
  {
    $char_money = $char["gold"];
    $char_money = $char_money - $_GET["total"];

    if ( $core == 1 )
      $money_query = "UPDATE characters SET gold='".$char_money."' WHERE guid='".$char["guid"]."'";
    else
      $money_query = "UPDATE characters SET money='".$char_money."' WHERE guid='".$char["guid"]."'";

    $money_result = $sql["char"]->query($money_query);
  }
  else
  {
    // get our credit balance
    $cr_query = "SELECT Credits FROM config_accounts WHERE Login='".$user_name."'";
    $cr_result = $sql["mgr"]->query($cr_query);
    $cr_result = $sql["mgr"]->fetch_assoc($cr_result);
    $credits = $cr_result["Credits"];

    // we don't charge credits if the account is unlimited
    if ( $credits >= 0 )
      $credits = $credits - $_GET["total"];

    $money_query = "UPDATE config_accounts SET Credits='".$credits."' WHERE Login='".$user_name."'";

    $money_result = $sql["mgr"]->query($money_query);
  }

  if ( $core == 1 )
  {
    $mail_query = "INSERT INTO mailbox_insert_queue VALUES ('".$from_char."', '".$char["guid"]."', '".lang("ultra", "questitems")."', ".chr(34).$_GET["want"]."x ".$item["name1"].chr(34).", '".$stationary."', '0', '".$_GET["item"]."', '".$_GET["want"]."')";
    redirect("ultra_vendor.php&moneyresult=".$money_result);
  }
  else
  {
    // we need to be able to bypass mail.php's normal permissions to send mail
    $_SESSION['vendor_permission'] = 1;
    redirect("mail.php?action=send_mail&type=ingame_mail&to=".$char["name"]."&subject=".lang("ultra", "questitems")."&body=".$_GET["want"]."x ".$item["name"]."&group_sign==&group_send=gm_level&money=0&att_item=".$_GET["item"]."&att_stack=".$_GET["want"]."&redirect=ultra_vendor.php&moneyresult=".$money_result);
  }
}

function showresults()
{
  global $sql, $core;

  $mail_result = $sql["char"]->quote_smart($_GET["mailresult"]);
  $money_result = $sql["char"]->quote_smart($_GET["moneyresult"]);

  if ( $mail_result && $money_result )
    redirect("ultra_vendor.php?error=3");
  else
    redirect("ultra_vendor.php?error=2");
}

function quality($val)
{
  switch( $val )
  {
    case 0:
      return '<span id="uv_poor_quality">'.lang("ultra_quality", $val).'</span>';
      break;
    case 1:
      return lang("ultra_quality", $val);
      break;
    case 2:
      return '<span id="uv_uncommon_quality">'.lang("ultra_quality", $val).'</span>';
      break;
    case 3:
      return '<span id="uv_rare_quality">'.lang("ultra_quality", $val).'</span>';
      break;
    case 4:
      return '<span id="uv_epic_quality">'.lang("ultra_quality", $val).'</span>';
      break;
    case 5:
      return '<span id="uv_legendary_quality">'.lang("ultra_quality", $val).'</span>';
      break;
    case 6:
      return '<span id="uv_artifact_quality">'.lang("ultra_quality", $val).'</span>';
      break;
    case 7:
      return '<span id="uv_heirloom_quality">'.lang("ultra_quality", $val).'</span>';
      break;
  }
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = ( ( isset($_GET["error"]) ) ? $_GET["error"] : NULL );

$output .= '
        <div class="bubble">
          <div class="top">';

switch ( $err )
{
  case 1:
    $output .= '
          <h1><font class="error">'.lang("global", "empty_fields").'</font></h1>';
    break;
  case 2:
    $output .= '
          <h1><font class="error">'.lang("ultra", "failed").'</font></h1>';
    break;
  case 3:
    $output .= '
          <h1>'.lang("ultra", "done").'</h1>';
    break;
  default: //no error
    $output .= '
          <h1>'.lang("ultra", "title").'</h1>';
}
unset($err);

$output .= '
        </div>';

// this is a pre-filter because mail from outside mail.php is priority
if ( $_GET['moneyresult'] )
  showresults();

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

switch ( $action )
{
  case "purchase":
    purchase();
    break;
  case "selected_quantity":
    approve();
    break;
  case "selected_item":
    select_quantity();
    break;
  case "selected_char":
    select_item();
    break;
  default:
    show_list();
}

unset($action);
unset($action_permission);

require_once("footer.php");

?>
