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


require_once "header.php";
require_once "libs/item_lib.php";
require_once "libs/global_lib.php";
require_once "libs/char_lib.php";
require_once "libs/mail_lib.php";
valid_login($action_permission["view"]);

//########################################################################################################################
// REDEEM COUPON
//########################################################################################################################
function redeem_coupon()
{
  global  $output, $coupon_id, $raffle_id, $bag_id, $characters_db, $user_id, $base_datasite, $item_datasite, $sql, $core;

  points_tabs();

  if ( !isset($_GET["redeemed"]) )
  {
    $query = "SELECT * FROM point_system_coupons WHERE entry='".$coupon_id."'";
    $result = $sql["mgr"]->query($query);
    $coupon = $sql["mgr"]->fetch_assoc($result);

    $usage_query = "SELECT * FROM point_system_coupon_usage WHERE coupon='".$coupon_id."' AND user='".$user_id."'";
    $usage_result = $sql["mgr"]->query($usage_query);

    $usage_count = $sql["mgr"]->num_rows($usage_result);

    // get characters
    $char_list = array();
    $realm_list = array();

    foreach ( $characters_db as $db )
    {
      $sqlt = new SQL;
      $sqlt->connect($db["addr"], $db["user"], $db["pass"], $db["name"], $db["encoding"]);

      $realm_char_list = array();

      // store the realm id for later
      $realm_list[] = $db["id"];

      if ( $core == 1 )
        $char_query = "SELECT guid FROM characters WHERE acct='".$user_id."' ORDER BY guid ASC";
      else
        $char_query = "SELECT guid FROM characters WHERE account='".$user_id."' ORDER BY guid ASC";

      $char_result = $sqlt->query($char_query);

      while ( $row = $sqlt->fetch_assoc($char_result) )
        $realm_char_list[] = $row["guid"];

      $char_list[] = $realm_char_list;
    }

    $output .= '
              <div class="tab_content">';

    // make sure we're allowed to use this coupon
    if ( ( ( $coupon["target"] != 0 ) && ( $coupon["target"] != $user_id ) ) || ( ( $usage_count >= $coupon["usage_limit"] ) && ( $coupon["usage_limit"] != -1 ) ) )
      $output .= '
                <span class="error">'.lang("points", "not_allowed").'</span>';
    else
    {
          $output .= '
                <form action="point_system.php" name="form1">
                  <input type="hidden" name="action" value="do_redeem" />
                  <input type="hidden" name="coupon_id" value="'.$coupon_id.'" />
                  <table class="lined" id="coupon_table">
                    <tr>
                      <td align="left">'.$coupon["title"].'</td>
                    </tr>';

          if ( $coupon["text"] != "" )
            $output .= '
                    <tr>
                      <td align="left">'.$coupon["text"].'</td>
                    </tr>';

          if ( ( $coupon["credits"] != 0 ) || ( $coupon["money"] != 0 ) || ( $coupon["item_id"] != 0 ) || ( $coupon["raffle_id"] != 0 ) )
          {
            $output .= '
                    <tr>
                      <td>
                        <div>
                          <div class="coupon_parts">'.lang("points", "coupon_value_claim").':</div>';

            if ( $coupon["credits"] != 0 )
            {
              if ( $coupon["credits"] > 1 )
                $tip = lang("points", "coupon_credits");
              else
                $tip = lang("points", "coupon_credit");

              $output .= '
                          <div class="coupon_parts">
                            <input type="checkbox" name="claim_credits" checked="checked"/>
                            <span>'.$coupon["credits"].'</span>
                            <span>'.$tip.'</span>
                          </div>';
            }

            if ( $coupon["money"] != 0 )
            {
              // extract gold/silver/copper from single gold number
              $coupon["money"] = str_pad($coupon["money"], 4, "0", STR_PAD_LEFT);
              $coupon_g = substr($coupon["money"],  0, -4);
              if ( $coupon_g == '' )
                $coupon_g = 0;
              $coupon_s = substr($coupon["money"], -4,  2);
              if ( ( $coupon_s == '' ) || ( $coupon_s == '00' ) )
                $coupon_s = 0;
              $coupon_c = substr($coupon["money"], -2);
              if ( ( $coupon_c == '' ) || ( $coupon_c == '00' ) )
                $coupon_c = 0;

              $output .= '
                          <div class="coupon_parts">
                            <hr />
                            <input type="checkbox" name="claim_money" checked="checked"/>
                            <span>'.$coupon_g.'</span>
                            <img src="img/gold.gif" alt="gold" />
                            <span>'.$coupon_s.'</span>
                            <img src="img/silver.gif" alt="gold" />
                            <span>'.$coupon_c.'</span>
                            <img src="img/copper.gif" alt="gold" />
                          </div>';

              $output .= '
                          <div class="coupon_part_title">
                            <span>'.lang("points", "choose_char_money").':</span>
                          </div>';

              for ( $i = 0; $i < count($char_list); $i++ )
              {
                $realm_chars = $char_list[$i];

                $cur_realm = $realm_list[$i];
                $realm_name_query = "SELECT * FROM config_servers WHERE `Index`='".$cur_realm."'";
                $realm_name_result = $sql["mgr"]->query($realm_name_query);
                $realm_name_result = $sql["mgr"]->fetch_assoc($realm_name_result);
                $cur_realm_name = $realm_name_result["Name"];

                $sqlt = new SQL;
                $sqlt->connect($characters_db[$cur_realm]["addr"], $characters_db[$cur_realm]["user"], $characters_db[$cur_realm]["pass"], $characters_db[$cur_realm]["name"], $characters_db[$cur_realm]["encoding"]);

                if ( count($realm_list) > 1 )
                  $output .= '
                          <div class="coupon_part_chars">
                            <span>'.$cur_realm_name.'</span>
                          </div>';

                $first = true; // we want the first character to be selected

                foreach ( $realm_chars as $row)
                {
                  $row_name_query = "SELECT * FROM characters WHERE guid='".$row."'";
                  $row_name_result = $sqlt->query($row_name_query);
                  $row_name_result = $sqlt->fetch_assoc($row_name_result);

                  $output .= '
                          <div class="coupon_part_chars">
                            <input type="radio" name="money_character" value="'.($i + 1)."-".$row.'-'.$row_name_result["name"].'" '.( ( isset($first) ) ? 'checked="checked" ' : '' ).'/>
                            <a href="char.php?id='.$row.'&amp;realm='.$cur_realm.'">'.$row_name_result["name"].'</a> - <img src="img/c_icons/'.$row_name_result["race"].'-'.$row_name_result["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($row_name_result["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                            <img src="img/c_icons/'.$row_name_result["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($row_name_result["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt=""/> - '.lang("char", "level_short").char_get_level_color($row_name_result["level"]).'
                          </div>';

                  unset($first);
                }
              }
            }

            if ( $coupon["item_id"] != 0 )
            {
              if ( $coupon["item_id"] > 0 )
              {
                // get item data
                if ( $core == 1 )
                {
                  $i_query = "SELECT 
                    *, description AS description1, name1 AS name, quality AS Quality, inventorytype AS InventoryType, 
                    socket_color_1 AS socketColor_1, socket_color_2 AS socketColor_2, socket_color_3 AS socketColor_3,
                    requiredlevel AS RequiredLevel, allowableclass AS AllowableClass,
                    sellprice AS SellPrice, itemlevel AS ItemLevel
                    FROM items "
                      .( ( $locales_search_option != 0 ) ? "LEFT JOIN items_localized ON (items_localized.entry=items.entry AND language_code='".$locales_search_option."') " : " " ).
                    "WHERE items.entry='".$coupon["item_id"]."'";
                }
                else
                {
                  $i_query = "SELECT *, description AS description1 FROM item_template "
                      .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_item ON locales_item.entry=item_template.entry " : " " ).
                    "WHERE item_template.entry='".$coupon["item_id"]."'";
                }

                $i_result = $sql["world"]->query($i_query);
                $i = $sql["world"]->fetch_assoc($i_result);

                $output .= '
                          <div class="coupon_parts">
                            <hr />
                            <input type="checkbox" name="claim_item" checked="checked"/>
                            <div class="coupon_item">
                              <div>
                                <a id="ch_inv_padding" href="'.$base_datasite.$item_datasite.$coupon["item_id"].'" target="_blank" onmouseover="ShowTooltip(this,\'_b\');" onmouseout="HideTooltip(\'_b\');">
                                  <img src="'.get_item_icon($coupon["item_id"]).'" alt="" />
                                </a>';

                if ( $coupon["item_count"] > 1 )
                  $output .= '
                                <div id="coupon_item_quantity_shadow">'.$coupon["item_count"].'</div>
                                <div id="coupon_item_quantity">'.$coupon["item_count"].'</div>';

                $output .= '
                              </div>';

                // build a tooltip object for this item
                $output .= '
                              <div class="item_tooltip" id="tooltip_b" style="left: -129px; top: 42px;">
                                <table>
                                  <tr>
                                    <td>'.get_item_tooltip($i, $item[4], $item[5], $item[6], $item[7], $item[8]).'</td>
                                  </tr>
                                </table>
                              </div>';

                $output .= '
                            </div>
                          </div>';

                $output .= '
                          <div class="coupon_part_title">
                            <span>'.lang("points", "choose_char_item").':</span>
                          </div>';

                for ( $i = 0; $i < count($char_list); $i++ )
                {
                  $realm_chars = $char_list[$i];

                  $cur_realm = $realm_list[$i];
                  $realm_name_query = "SELECT * FROM config_servers WHERE `Index`='".$cur_realm."'";
                  $realm_name_result = $sql["mgr"]->query($realm_name_query);
                  $realm_name_result = $sql["mgr"]->fetch_assoc($realm_name_result);
                  $cur_realm_name = $realm_name_result["Name"];

                  $sqlt = new SQL;
                  $sqlt->connect($characters_db[$cur_realm]["addr"], $characters_db[$cur_realm]["user"], $characters_db[$cur_realm]["pass"], $characters_db[$cur_realm]["name"], $characters_db[$cur_realm]["encoding"]);

                  if ( count($realm_list) > 1 )
                    $output .= '
                          <div class="coupon_part_chars">
                            <span>'.$cur_realm_name.'</span>
                          </div>';

                  $first = true; // we want the first character to be selected

                  foreach ( $realm_chars as $row)
                  {
                    $row_name_query = "SELECT * FROM characters WHERE guid='".$row."'";
                    $row_name_result = $sqlt->query($row_name_query);
                    $row_name_result = $sqlt->fetch_assoc($row_name_result);

                    $output .= '
                          <div class="coupon_part_chars">
                            <input type="radio" name="item_character" value="'.($i + 1)."-".$row.'-'.$row_name_result["name"].'" '.( ( isset($first) ) ? 'checked="checked" ' : '' ).'/>
                            <a href="char.php?id='.$row.'&amp;realm='.$cur_realm.'">'.$row_name_result["name"].'</a> - <img src="img/c_icons/'.$row_name_result["race"].'-'.$row_name_result["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($row_name_result["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                            <img src="img/c_icons/'.$row_name_result["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($row_name_result["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt=""/> - '.lang("char", "level_short").char_get_level_color($row_name_result["level"]).'
                          </div>';

                    unset($first);
                  }
                }
              }
              else
              {
                $output .= '
                    <div class="coupon_parts">
                      <hr />
                      <input type="checkbox" name="claim_item" checked="checked"/>
                      <div>
                        <a href="point_system.php?action=view_bag&amp;bag_id='.($coupon["item_id"]*-1).'" onmousemove="oldtoolTip(\''.lang("points", "prize_bag").'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()">
                          <img src="'.get_item_icon(1725).'" alt="" />
                        </a>
                      </div>
                    </div>';
              }
            }

            if ( $coupon["raffle_id"] != 0 )
            {
              if ( $coupon["redemption_option"] == 0 )
                $output .= '
                          <div class="coupon_parts">
                            <hr />
                            <input type="checkbox" name="claim_raffle" checked="checked"/><span>'.lang("points", "and_raffle").'</span>
                          </div>';
              else
                $output .= '
                          <div class="coupon_parts">
                            <hr />
                            <input type="checkbox" name="claim_raffle" /><span>'.lang("points", "or_raffle").'</span>
                          </div>';

              // get our raffle(s)
              if ( $coupon["raffle_id"] == -1 )
                $query = "SELECT * FROM point_system_raffles";
              else
                $query = "SELECT * FROM point_system_raffles WHERE entry='".$coupon["raffle_id"]."'";

              $result = $sql["mgr"]->query($query);

              $first = true; // we want the first raffle to be selected

              while ( $row = $sql["mgr"]->fetch_assoc($result) )
              {
                // get the number of tickets we've already purchased
                $tickets_query = "SELECT COUNT(*) FROM point_system_raffle_tickets WHERE raffle='".$row["entry"]."' AND user='".$user_id."'";
                $tickets_result = $sql["mgr"]->query($tickets_query);
                $tickets_result = $sql["mgr"]->fetch_assoc($tickets_result);
                $tickets = $tickets_result["COUNT(*)"];

                // for disabling the radio button(s) below
                $limit_reached = false;
                if ( ( $row["tickets_per_user"] > 0 ) && ( $tickets >= $row["tickets_per_user"] ) )
                  $limit_reached = true;

                $output .= '
                          <div class="coupon_part_chars">
                            <input type="radio" name="raffle_choice" value="'.$row["entry"].'" '.( ( isset($first) ) ? 'checked="checked" ' : '' ).' '.( ( $limit_reached ) ? 'disabled="disabled" ' : '' ).'/>
                            <a href="point_system.php?action=view_raffle&amp;raffle_id='.$row["entry"].'&amp;coupon_id='.$coupon_id.( ( $bag_id != 0 ) ? '&amp;bag_id='.$bag_id : '' ).'">'.$row["title"].'</a>
                            <span>('.$tickets.( ( $row["tickets_per_user"] > 0 ) ? "/".$row["tickets_per_user"] : '' ).' '.lang("points", "tickets_purchased").')</span>
                          </div>';

                unset($first);
              }
            }

            $output .= '
                        </div>
                      </td>
                    </tr>';
          }

          $output .= '
                    <tr>
                      <td align="right">
                        <a href="javascript:do_submit(\'form1\',0)">
                          <span><img src="img/aff_tick.png" width="16" height="16" alt="" />&nbsp;'.lang("points", "redeem_coupon").'</span>
                        </a>
                      </td>
                    </tr>
                  </table>
                </form>';
    }
  }
  else
  {
    $output .= '
              <div class="tab_content">';

    $output .= '
                <span>'.lang("points", "redeemed").'</span>';
  }

  $output .= '
            </div>
            <br />
          </center>
          <!-- end of point_system.php -->';
}

function do_redeem()
{
  global  $output, $bag_id, $coupon_id, $user_name, $user_id, $sql, $core;

  // get our coupon
  $query = "SELECT * FROM point_system_coupons WHERE entry='".$coupon_id."'";
  $result = $sql["mgr"]->query($query);
  $coupon = $sql["mgr"]->fetch_assoc($result);

  // get our usage
  $query = "SELECT * FROM point_system_coupon_usage WHERE coupon='".$coupon_id."' AND user='".$user_id."'";
  $usage = $sql["mgr"]->query($query);

  // check whether this coupon is available for us to use
  if ( ( $coupon["target"] != 0 ) && ( $user_id != $coupon["target"] ) )
    redirect("point_system.php?action=redeem_coupon".( ( $coupon_id != 0 ) ? "&coupon_id=".$coupon_id : "" ).( ( $bag_id != 0 ) ? "&bag_id=".$bag_id : "" ).( ( $raffle_id != 0 ) ? '&amp;raffle_id='.$raffle_id : '' )."&error=2");

  // check whether we've already used this use would exceed the allowed number of uses
  if ( ( $coupon["usage_limit"] > -1 ) && ( $sql["mgr"]->num_rows($usage) == $coupon["usage_limit"] ) )
    redirect("point_system.php?action=redeem_coupon".( ( $coupon_id != 0 ) ? "&coupon_id=".$coupon_id : "" ).( ( $bag_id != 0 ) ? "&bag_id=".$bag_id : "" ).( ( $raffle_id != 0 ) ? '&amp;raffle_id='.$raffle_id : '' )."&error=2");

  // raffle
  // we have to do the raffle first for the Value OR Ticket method
  $allow_value_claims = true; // possibly changed later
  if ( isset($_GET["claim_raffle"]) )
  {
    // get the raffle we selected
    $raffle_choice = $_GET["raffle_choice"];
    if ( !is_numeric($raffle_choice) ) // prevent injection
      redirect("point_system.php?action=redeem_coupon".( ( $coupon_id != 0 ) ? "&coupon_id=".$coupon_id : "" ).( ( $bag_id != 0 ) ? "&bag_id=".$bag_id : "" ).( ( $raffle_id != 0 ) ? '&amp;raffle_id='.$raffle_id : '' )."&error=1");

    $query = "SELECT * FROM point_system_raffles WHERE entry='".$raffle_choice."'";
    $result = $sql["mgr"]->query($query);
    $raffle = $sql["mgr"]->fetch_assoc($result);

    // get the count of tickets we've purchased for this raffle
    $query = "SELECT COUNT(*) FROM point_system_raffle_tickets WHERE raffle='".$raffle_choice."' AND user='".$user_id."'";
    $result = $sql["mgr"]->query($query);
    $result = $sql["mgr"]->fetch_assoc($result);
    $ticket_count = $result["COUNT(*)"];

    // check if we've already purchased the maximum number of tickets
    $limit_reached = false;
    if ( ( $raffle["tickets_per_user"] > 0 ) && ( $ticket_count >= $raffle["tickets_per_user"] ) )
      $limit_reached = true;

    // if the coupon method is Value OR Raffle then we block claiming the values in the rest of this function
    if ( $coupon["redemption_option"] == 1 )
    {
      // assuming we haven't reached the purchase limit. if so, show a minor error
      if ( !$limit_reached )
        $allow_value_claims = false;
      else
        redirect("point_system.php?action=redeem_coupon".( ( $coupon_id != 0 ) ? "&coupon_id=".$coupon_id : "" ).( ( $bag_id != 0 ) ? "&bag_id=".$bag_id : "" ).( ( $raffle_id != 0 ) ? '&amp;raffle_id='.$raffle_id : '' )."&error=3");
    }

    // record the ticket purchase
    $query = "INSERT INTO point_system_raffle_tickets (raffle, user, date_purchased) VALUES ('".$raffle_choice."', '".$user_id."', NOW())";
    $result = $sql["mgr"]->query($query);
  }

  if ( $allow_value_claims ) // were we blocked by the Value OR Raffle method?
  {
    // if we chose to do so, add the coupon's credits to our credits
    if ( isset($_GET["claim_credits"]) )
    {
      // get our credits
      $query = "SELECT Credits FROM config_accounts WHERE Login='".$user_name."'";
      $result = $sql["mgr"]->query($query);
      $result = $sql["mgr"]->fetch_assoc($result);
      $credits = $result["Credits"];

      // if we have unlimited credits, keep it that way
      // else, add the coupons credits to ours
      if ( $credits >= 0 )
        $credits = $credits + $coupon["credits"];

      // save our credits
      $query = "UPDATE config_accounts SET Credits='".$credits."' WHERE Login='".$user_name."'";
      $credits_result = $sql["mgr"]->query($query);
    }

    // money and items
    // prepare
    $money_temp = explode("-", $_GET["money_character"]);
    $money_realm_id = $money_temp[0];
    $money_receiver = $money_temp[1];
    $money_to = $money_temp[2];

    $item_temp = explode("-", $_GET["item_character"]);
    $item_realm_id = $item_temp[0];
    $item_receiver = $item_temp[1];
    $item_to = $item_temp[2];

    $mails = array();

    if ( isset($_GET["claim_money"]) && isset($_GET["claim_item"]) )
    {
      // we're claiming both money and item
      if ( $_GET["money_character"] == $_GET["item_character"] )
      {
        // if the money & item characters are the same, we only need to send one mail
        $mail["receiver"] = $money_receiver;
        $mail["subject"] = lang("points", "mail_subject");
        $mail["body"] = lang("points", "mail_body");
        $mail["att_gold"] = $coupon["money"];
        $mail["att_item"] = array($coupon["item_id"]);
        $mail["att_stack"] = array($coupon["item_count"]);
        $mail["receiver_name"] = $money_to;

        array_push($mails, $mail);

        // send
        if ( $core == 1 )
          $result = send_ingame_mail_A($money_realm_id, $mails, true);
        else
          $result = send_ingame_mail_MT($money_realm_id, $mails, true);
      }
      else
      {
        // if they are different, we need to send two mails
        $mail["receiver"] = $money_receiver;
        $mail["subject"] = lang("points", "mail_subject");
        $mail["body"] = lang("points", "mail_body");
        $mail["att_gold"] = $coupon["money"];
        $mail["receiver_name"] = $money_to;

        array_push($mails, $mail);

        // send
        if ( $core == 1 )
          $result = send_ingame_mail_A($money_realm_id, $mails, true);
        else
          $result = send_ingame_mail_MT($money_realm_id, $mails, true);

        unset($mail);

        $mail["receiver"] = $item_receiver;
        $mail["subject"] = lang("points", "mail_subject");
        $mail["body"] = lang("points", "mail_body");
        $mail["att_item"] = array($coupon["item_id"]);
        $mail["att_stack"] = array($coupon["item_count"]);
        $mail["receiver_name"] = $item_to;

        array_push($mails, $mail);

        // send
        if ( $core == 1 )
          $result = send_ingame_mail_A($item_realm_id, $mails, true);
        else
          $result = send_ingame_mail_MT($item_realm_id, $mails, true);
      }
    }
    elseif ( isset($_GET["claim_money"]) )
    {
      // we're only claiming the money
      $mail["receiver"] = $money_receiver;
      $mail["subject"] = lang("points", "mail_subject");
      $mail["body"] = lang("points", "mail_body");
      $mail["att_gold"] = $coupon["money"];
      $mail["receiver_name"] = $money_to;

      array_push($mails, $mail);

      // send
      if ( $core == 1 )
        $result = send_ingame_mail_A($money_realm_id, $mails, true);
      else
        $result = send_ingame_mail_MT($money_realm_id, $mails, true);
    }
    elseif ( isset($_GET["claim_item"]) )
    {
      if ( $coupon["item_id"] > 0 )
      {
        // we're only claiming the item
        $mail["receiver"] = $item_receiver;
        $mail["subject"] = lang("points", "mail_subject");
        $mail["body"] = lang("points", "mail_body");
        $mail["att_item"] = array($coupon["item_id"]);
        $mail["att_stack"] = array($coupon["item_count"]);
        $mail["receiver_name"] = $item_to;

        array_push($mails, $mail);

        // send
        if ( $core == 1 )
          $result = send_ingame_mail_A($item_realm_id, $mails, true);
        else
          $result = send_ingame_mail_MT($item_realm_id, $mails, true);
      }
      else
      {
        // the 'item' is a prize bag, assign ownership
        $own_bag_query = "UPDATE point_system_prize_bags SET owner='".$user_id."' WHERE entry='".($coupon["item_id"] * -1)."'";
        $sql["mgr"]->query($own_bag_query);
      }
    }
  }

  // make an entry for our usage
  $query = "INSERT INTO point_system_coupon_usage (coupon, user, date_used) VALUES ('".$coupon_id."', '".$user_id."', NOW())";
  $result = $sql["mgr"]->query($query);

  redirect("point_system.php?action=redeem_coupon&redeemed=1");
}

function coupons()
{
  global  $output, $coupon_id, $raffle_id, $user_id, $locales_search_option, $base_datasite,
    $item_datasite, $sql, $core;

  points_tabs();

  $output .= '
            <div class="tab_content">';

  $coupon_query = "SELECT * FROM point_system_coupons WHERE (target='0' OR target='".$user_id."') AND enabled='1'";
  $coupon_result = $sql["mgr"]->query($coupon_query);

  if ( $sql["mgr"]->num_rows($coupon_result) > 0 )
  {
    $output .= '
            <table class="lined" id="coupon_table">';

    while ( $coupon = $sql["mgr"]->fetch_assoc($coupon_result) )
    {
      $usage_query = "SELECT * FROM point_system_coupon_usage WHERE coupon='".$coupon["entry"]."' AND user='".$user_id."'";
      $usage_result = $sql["mgr"]->query($usage_query);

      if ( ( $sql["mgr"]->num_rows($usage_result) < $coupon["usage_limit"] ) || ( $coupon["usage_limit"] == -1 ) )
      {
        $output .= '
              <tr>
                <td align="left">'.$coupon["title"].'</td>
              </tr>';

        if ( $coupon["text"] != "" )
          $output .= '
              <tr>
                <td align="left">'.$coupon["text"].'</td>
              </tr>';

        if ( ( $coupon["credits"] != 0 ) || ( $coupon["money"] != 0 ) || ( $coupon["item_id"] != 0 ) || ( $coupon["raffle_id"] != 0 ) )
        {
          $output .= '
              <tr>
                <td align="left">
                  <span>'.lang("points", "coupon_value").':</span>';

          if ( $coupon["credits"] != 0 )
          {
            if ( $coupon["credits"] > 1 )
              $tip = lang("index", "coupon_credits");
            else
              $tip = lang("index", "coupon_credit");

            $output .= '
                  <br />
                  <br />
                  <span>'.$coupon["credits"].'</span>
                  <span>'.$tip.'</span>';
          }

          if ( $coupon["money"] != 0 )
          {
            // extract gold/silver/copper from single gold number
            $coupon["money"] = str_pad($coupon["money"], 4, "0", STR_PAD_LEFT);
            $coupon_g = substr($coupon["money"],  0, -4);
            if ( $coupon_g == '' )
              $coupon_g = 0;
            $coupon_s = substr($coupon["money"], -4,  2);
            if ( ( $coupon_s == '' ) || ( $coupon_s == '00' ) )
              $coupon_s = 0;
            $coupon_c = substr($coupon["money"], -2);
            if ( ( $coupon_c == '' ) || ( $coupon_c == '00' ) )
              $coupon_c = 0;

            $output .= '
                  <br />
                  <br />
                  <span>'.$coupon_g.'</span>
                  <img src="img/gold.gif" alt="gold" />
                  <span>'.$coupon_s.'</span>
                  <img src="img/silver.gif" alt="gold" />
                  <span>'.$coupon_c.'</span>
                  <img src="img/copper.gif" alt="gold" />';
          }

          if ( $coupon["item_id"] != 0 )
          {
            if ( $coupon["item_id"] > 0 )
            {
              // get item data
              if ( $core == 1 )
              {
                $i_query = "SELECT 
                  *, description AS description1, name1 AS name, quality AS Quality, inventorytype AS InventoryType, 
                  socket_color_1 AS socketColor_1, socket_color_2 AS socketColor_2, socket_color_3 AS socketColor_3,
                  requiredlevel AS RequiredLevel, allowableclass AS AllowableClass,
                  sellprice AS SellPrice, itemlevel AS ItemLevel
                  FROM items "
                    .( ( $locales_search_option != 0 ) ? "LEFT JOIN items_localized ON (items_localized.entry=items.entry AND language_code='".$locales_search_option."') " : " " ).
                  "WHERE items.entry='".$coupon["item_id"]."'";
              }
              else
              {
                $i_query = "SELECT *, description AS description1 FROM item_template "
                    .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_item ON locales_item.entry=item_template.entry " : " " ).
                  "WHERE item_template.entry='".$coupon["item_id"]."'";
              }

              $i_result = $sql["world"]->query($i_query);
              $i = $sql["world"]->fetch_assoc($i_result);

              $output .= '
                    <br />
                    <br />
                    <div class="coupon_item">
                      <div>
                        <a href="'.$base_datasite.$item_datasite.$coupon["item_id"].'" target="_blank" onmouseover="ShowTooltip(this,\'_b'.$coupon["entry"].'\');" onmouseout="HideTooltip(\'_b'.$coupon["entry"].'\');">
                          <img src="'.get_item_icon($coupon["item_id"]).'" alt="" />
                        </a>';

              if ( $coupon["item_count"] > 1 )
                $output .= '
                        <div class="ch_inv_quantity_shadow">'.$coupon["item_count"].'</div>
                        <div class="ch_inv_quantity">'.$coupon["item_count"].'</div>';

              $output .= '
                      </div>';

              // build a tooltip object for this item
              $output .= '
                      <div class="item_tooltip" id="tooltip_b'.$coupon["entry"].'" style="left: -129px; top: 42px;">
                        <table>
                          <tr>
                            <td>'.get_item_tooltip($i, $item[4], $item[5], $item[6], $item[7], $item[8]).'</td>
                          </tr>
                        </table>
                      </div>';

              $output .= '
                    </div>';
            }
            else
            {
              $output .= '
                    <br />
                    <br />
                    <div class="coupon_item">
                      <div>
                        <a href="point_system.php?action=view_bag&amp;bag_id='.($coupon["item_id"]*-1).'" onmousemove="oldtoolTip(\''.lang("points", "prize_bag").'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()">
                          <img src="'.get_item_icon(1725).'" alt="" />
                        </a>
                      </div>
                    </div>';
            }
          }

          if ( $coupon["raffle_id"] != 0 )
          {
            // find out how many entries per user the raffle allows and whether the raffle is enabled
            if ( $coupon["raffle_id"] != -1 )
            {
              $query = "SELECT tickets_per_user, enabled FROM point_system_raffles WHERE entry='".$coupon["raffle_id"]."'";
              $result = $sql["mgr"]->query($query);
              $result = $sql["mgr"]->fetch_assoc($result);
              $per_user = $result["tickets_per_user"];
              $raffle_enabled = $result["enabled"];

              // if tickets_per_user is -1 then its unlimited, fake it with a reasonably high number
              $per_user = 999999999;
            }
            else
            {
              // if it allows any raffle, then fake it
              $per_user = 999999999;
              $raffle_enabled = 1;
            }

            if ( $raffle_enabled )
            {
              // find out how many time we've entered
              $query = "SELECT COUNT(*) FROM point_system_raffle_tickets WHERE raffle='".$coupon["raffle_id"]."' AND user='".$user_id."'";
              $result = $sql["mgr"]->query($query);
              $result = $sql["mgr"]->fetch_assoc($result);
              $tickets = $result["COUNT(*)"];

              // if we haven't already purchased the maximum number of tickets
              // or the raffle allows purchase of tickets from any raffle
              if ( ( $tickets < $per_user ) || ( $coupon["raffle_id"] == -1 ) )
              {
                if ( $coupon["redemption_option"] == 0 )
                  $output .= '
                    <br />
                    <br />
                    <span>'.lang("points", "and_raffle").'</span>';
                else
                  $output .= '
                    <br />
                    <br />
                    <span>'.lang("points", "or_raffle").'</span>';
              }
            }
          }

          $output .= '
                  </td>
                </tr>';
        }

        $output .= '
              <tr>
                <td align="right">
                  <a href="point_system.php?action=redeem_coupon&amp;coupon_id='.$coupon["entry"].'">
                    <span><img src="img/star.png" width="16" height="16" alt="" />&nbsp;'.lang("points", "use_coupon").'</span>
                  </a>
                </td>
              </tr>';

        $output .= '
              <tr>
                <th></th>
              </tr>';
      }
    }

    if ( $sql["mgr"]->num_rows($coupon_result) )
      $output = substr($output, 0, strlen($output) - 68);

    $output .= '
            </table>';
  }

  $output .= '
            </div>
            <br />
          </center>
          <!-- end of point_system.php -->';
}

function raffles()
{
  global  $output, $user_id, $sql, $core;

  points_tabs();

  $output .= '
            <div class="tab_content">';

  $raffle_query = "SELECT * FROM point_system_raffles WHERE enabled='1'";
  $raffle_result = $sql["mgr"]->query($raffle_query);

  if ( $sql["mgr"]->num_rows($raffle_result) > 0 )
  {
    $output .= '
            <table class="lined" id="coupon_table">';

    while ( $raffle = $sql["mgr"]->fetch_assoc($raffle_result) )
    {
      $usage_query = "SELECT * FROM point_system_raffle_tickets WHERE raffle='".$raffle["entry"]."'";
      $usage_result = $sql["mgr"]->query($usage_query);

      $my_usage_query = "SELECT * FROM point_system_raffle_tickets WHERE raffle='".$raffle["entry"]."' AND user='".$user_id."'";
      $my_usage_result = $sql["mgr"]->query($my_usage_query);

      // fake a high limit if it's unlimited
      if ( $raffle["ticket_limit"] == -1 )
        $raffle["ticket_limit"] = 999999999;

      // fake a high per user limit if it's unlimited
      if ( $raffle["tickets_per_user"] == -1 )
        $raffle["tickets_per_user"] = 999999999;

      if ( ( $sql["mgr"]->num_rows($usage_result) < $raffle["ticket_limit"] ) && ( $sql["mgr"]->num_rows($my_usage_result) < $raffle["tickets_per_user"] ) )
      {
        $output .= '
              <tr>
                <td align="left">'.$raffle["title"].'</td>
              </tr>';

        if ( $raffle["text"] != "" )
          $output .= '
              <tr>
                <td align="left">'.$raffle["text"].'</td>
              </tr>';

        $output .= '
              <tr>
                <td align="left"><span>'.lang("points", "drawing").'</span>:&nbsp;'.$raffle["drawing"].'</td>
              </tr>';

        if ( ( $raffle["credits"] != 0 ) || ( $raffle["money"] != 0 ) || ( $raffle["item_id"] != 0 ) || ( $raffle["raffle_id"] != 0 ) )
        {
          $output .= '
              <tr>
                <td align="left">
                  <span>'.lang("points", "raffle_prizes").':</span>';

          if ( $raffle["credits"] != 0 )
          {
            if ( $raffle["credits"] > 1 )
              $tip = lang("points", "coupon_credits");
            else
              $tip = lang("points", "coupon_credit");

            $output .= '
                  <br />
                  <br />
                  <span>'.$raffle["credits"].'</span>
                  <span>'.$tip.'</span>';
          }

          if ( $raffle["money"] != 0 )
          {
            // extract gold/silver/copper from single gold number
            $raffle["money"] = str_pad($raffle["money"], 4, "0", STR_PAD_LEFT);
            $raffle_g = substr($raffle["money"],  0, -4);
            if ( $raffle_g == '' )
              $raffle_g = 0;
            $raffle_s = substr($raffle["money"], -4,  2);
            if ( ( $raffle_s == '' ) || ( $raffle_s == '00' ) )
              $raffle_s = 0;
            $raffle_c = substr($raffle["money"], -2);
            if ( ( $raffle_c == '' ) || ( $raffle_c == '00' ) )
              $raffle_c = 0;

            $output .= '
                  <br />
                  <br />
                  <span>'.$raffle_g.'</span>
                  <img src="img/gold.gif" alt="gold" />
                  <span>'.$raffle_s.'</span>
                  <img src="img/silver.gif" alt="gold" />
                  <span>'.$raffle_c.'</span>
                  <img src="img/copper.gif" alt="gold" />';
          }

          if ( $raffle["item_id"] != 0 )
          {
            if ( $raffle["item_id"] > 0 )
            {
              // get item data
              if ( $core == 1 )
              {
                $i_query = "SELECT 
                  *, description AS description1, name1 AS name, quality AS Quality, inventorytype AS InventoryType, 
                  socket_color_1 AS socketColor_1, socket_color_2 AS socketColor_2, socket_color_3 AS socketColor_3,
                  requiredlevel AS RequiredLevel, allowableclass AS AllowableClass,
                  sellprice AS SellPrice, itemlevel AS ItemLevel
                  FROM items "
                    .( ( $locales_search_option != 0 ) ? "LEFT JOIN items_localized ON (items_localized.entry=items.entry AND language_code='".$locales_search_option."') " : " " ).
                  "WHERE items.entry='".$raffle["item_id"]."'";
              }
              else
              {
                $i_query = "SELECT *, description AS description1 FROM item_template "
                    .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_item ON locales_item.entry=item_template.entry " : " " ).
                  "WHERE item_template.entry='".$raffle["item_id"]."'";
              }

              $i_result = $sql["world"]->query($i_query);
              $i = $sql["world"]->fetch_assoc($i_result);

              $output .= '
                    <br />
                    <br />
                    <div class="coupon_item">
                      <div>
                        <a href="'.$base_datasite.$item_datasite.$raffle["item_id"].'" target="_blank" onmouseover="ShowTooltip(this,\'_b'.$raffle["entry"].'\');" onmouseout="HideTooltip(\'_b'.$raffle["entry"].'\');">
                          <img src="'.get_item_icon($raffle["item_id"]).'" alt="" />
                        </a>';

              if ( $raffle["item_count"] > 1 )
                $output .= '
                        <div class="ch_inv_quantity_shadow">'.$raffle["item_count"].'</div>
                        <div class="ch_inv_quantity">'.$raffle["item_count"].'</div>';

              $output .= '
                      </div>';

              // build a tooltip object for this item
              $output .= '
                      <div class="item_tooltip" id="tooltip_b'.$raffle["entry"].'" style="left: -129px; top: 42px;">
                        <table>
                          <tr>
                            <td>'.get_item_tooltip($i, $item[4], $item[5], $item[6], $item[7], $item[8]).'</td>
                          </tr>
                        </table>
                      </div>';

              $output .= '
                    </div>';
            }
            else
            {
              $output .= '
                    <br />
                    <br />
                    <div class="coupon_item">
                      <div>
                        <a href="point_system.php?action=view_bag&amp;bag_id='.($raffle["item_id"]*-1).'" onmousemove="oldtoolTip(\''.lang("points", "prize_bag").'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()">
                          <img src="'.get_item_icon(1725).'" alt="" />
                        </a>
                      </div>
                    </div>';
            }
          }

          $output .= '
                  </td>
                </tr>';
        }

        if ( ( $raffle["cost_credits"] != 0 ) || ( $raffle["cost_money"] != 0 ) )
        {
          $output .= '
                <tr>
                  <td align="left">
                    <span>'.lang("points", "ticket_cost").':</span>';

          if ( $raffle["cost_credits"] != 0 )
          {
            if ( $raffle["cost_credits"] > 1 )
              $tip = lang("points", "coupon_credits");
            else
              $tip = lang("points", "coupon_credit");

            $output .= '
                    <br />
                    <br />
                    <span>'.$raffle["cost_credits"].'</span>
                    <span>'.$tip.'</span>';
          }

          if ( $raffle["cost_money"] != 0 )
          {
            // extract gold/silver/copper from single gold number
            $raffle["cost_money"] = str_pad($raffle["cost_money"], 4, "0", STR_PAD_LEFT);
            $raffle_cost_g = substr($raffle["cost_money"],  0, -4);
            if ( $raffle_cost_g == '' )
              $raffle_cost_g = 0;
            $raffle_cost_s = substr($raffle["cost_money"], -4,  2);
            if ( ( $raffle_cost_s == '' ) || ( $raffle_cost_s == '00' ) )
              $raffle_cost_s = 0;
            $raffle_cost_c = substr($raffle["cost_money"], -2);
            if ( ( $raffle_cost_c == '' ) || ( $raffle_cost_c == '00' ) )
              $raffle_cost_c = 0;

            $output .= '
                    <br />
                    <br />
                    <span>'.$raffle_cost_g.'</span>
                    <img src="img/gold.gif" alt="gold" />
                    <span>'.$raffle_cost_s.'</span>
                    <img src="img/silver.gif" alt="gold" />
                    <span>'.$raffle_cost_c.'</span>
                    <img src="img/copper.gif" alt="gold" />';
          }

          $output .= '
                  </td>
                </tr>';
        }

        $output .= '
              <tr>
                <td align="right">
                  <a href="point_system.php?action=view_raffle&amp;raffle_id='.$raffle["entry"].'">
                    <span><img src="img/star.png" width="16" height="16" alt="" />&nbsp;'.lang("points", "purchase_ticket").'</span>
                  </a>
                </td>
              </tr>';

        $output .= '
              <tr>
                <th></th>
              </tr>';
      }
    }

    if ( $sql["mgr"]->num_rows($raffle_result) )
      $output = substr($output, 0, strlen($output) - 68);

    $output .= '
            </table>';
  }

  $output .= '
            </div>
            <br />
          </center>
          <!-- end of point_system.php -->';
}

function contests()
{
  global  $output, $sql, $core;

  points_tabs();

  $output .= '
            <div class="tab_content">';

  $output .= '
            </div>
            <br />
          </center>
          <!-- end of point_system.php -->';
}

function view_bag()
{
  global  $output, $locales_search_option, $base_datasitem, $item_datasite, $characters_db, $bag_id, $user_id,
    $sql, $core;

  points_tabs();

  $bag_query = "SELECT * FROM point_system_prize_bags WHERE `entry`='".$bag_id."'";
  $bag_result = $sql["mgr"]->query($bag_query);
  $bag = $sql["mgr"]->fetch_assoc($bag_result);

  $output .= '
            <div class="tab_content">
              <form method="get" action="point_system.php" name="form">
                <input type="hidden" name="action" value="edit_bag" />
                <input type="hidden" name="bag_id" value="'.$bag_id.'" />
                <table>';

  if ( $bag["owner"] == $user_id )
    $output .= '
                  <tr>
                    <td colspan="2" align="left">
                      <span>'.lang("points", "choose_items").':</span>
                    </td>
                  </tr>';

  $output .= '
                  <tr>
                    <td valign="top">
                      <div class="bag" style="width: '.(4*43).'px; height: '.(ceil($bag["slots"]/4)*41).'px;">';

  $dsp = $bag["slots"]%4;

  if ( $dsp )
    $output .= '
                        <div class="no_slot"></div>';

  // get bag items
  $items_query = "SELECT item_id, slot, item_count FROM point_system_prize_bag_items WHERE `bag`='".$bag_id."'";
  $items_result = $sql["mgr"]->query($items_query);

  while ( $item = $sql["mgr"]->fetch_assoc($items_result) )
  {
    $item["item_count"] = ( ( $item["item_count"] == 1 ) ? "" : $item["item_count"] );

    // get item data
    if ( $core == 1 )
    {
      $i_query = "SELECT 
        *, description AS description1, name1 AS name, quality AS Quality, inventorytype AS InventoryType, 
        socket_color_1 AS socketColor_1, socket_color_2 AS socketColor_2, socket_color_3 AS socketColor_3,
        requiredlevel AS RequiredLevel, allowableclass AS AllowableClass,
        sellprice AS SellPrice, itemlevel AS ItemLevel
        FROM items "
          .( ( $locales_search_option != 0 ) ? "LEFT JOIN items_localized ON (items_localized.entry=items.entry AND language_code='".$locales_search_option."') " : " " ).
        "WHERE items.entry='".$item["item_id"]."'";
    }
    else
    {
      $i_query = "SELECT *, description AS description1 FROM item_template "
          .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_item ON locales_item.entry=item_template.entry " : " " ).
        "WHERE item_template.entry='".$item["item_id"]."'";
    }

    $i_result = $sql["world"]->query($i_query);
    $i = $sql["world"]->fetch_assoc($i_result);

    $output .= '
                        <div class="bag_slot" style="left: '.((($item["slot"]+$dsp)%4*43)+4).'px; top: '.(((floor(($item["slot"]+$dsp)/4)*41))+4).'px;">
                          <a href="'.$base_datasite.$item_datasite.$item["item_id"].'" target="_blank" onmouseover="ShowTooltip(this,\'_bp'.$item["slot"].(($item["slot"]+$dsp)%4*42).'x'.(floor(($item["slot"]+$dsp)/4)*41).'\');" onmouseout="HideTooltip(\'_bp'.$item["slot"].(($item["slot"]+$dsp)%4*42).'x'.(floor(($item["slot"]+$dsp)/4)*41).'\');">
                            <img src="'.get_item_icon($item["item_id"]).'" alt="" class="inv_icon" />
                          </a>';

    if ( $bag["owner"] == $user_id )
      $output .= '
                          <div class="prize_bag_check">
                            <input type="checkbox" name="chosen_slot[]" value="'.$item["slot"].'" />
                          </div>';

    $output .= '
                          <div class="ch_inv_quantity_shadow">'.$item["item_count"].'</div>
                          <div class="ch_inv_quantity">'.$item["item_count"].'</div>
                        </div>';

    // build a tooltip object for this item
    $output .= '
                      <div class="item_tooltip" id="tooltip_bp'.$item["slot"].(($item["slot"]+$dsp)%4*42).'x'.(floor(($item["slot"]+$dsp)/4)*41).'" style="left: '.((($item["slot"]+$dsp)%4*42)-129).'px; top: '.((floor(($item["slot"]+$dsp)/4)*41)+42).'px;">
                        <table>
                          <tr>
                            <td>'.get_item_tooltip($i, $item[4], $item[5], $item[6], $item[7], $item[8]).'</td>
                          </tr>
                        </table>
                      </div>';

    $item["item_count"] = ( ( $item["item_count"] == "" ) ? 1 : $item["item_count"] );
  }

  $output .= '
                      </div>
                    </td>
                    <td>';

  if ( $bag["owner"] == $user_id )
  {
    // get characters
    $char_list = array();
    $realm_list = array();

    foreach ( $characters_db as $db )
    {
      $sqlt = new SQL;
      $sqlt->connect($db["addr"], $db["user"], $db["pass"], $db["name"], $db["encoding"]);

      $realm_char_list = array();

      // store the realm id for later
      $realm_list[] = $db["id"];

      if ( $core == 1 )
        $char_query = "SELECT guid FROM characters WHERE acct='".$user_id."' ORDER BY guid ASC";
      else
        $char_query = "SELECT guid FROM characters WHERE account='".$user_id."' ORDER BY guid ASC";

      $char_result = $sqlt->query($char_query);

      while ( $row = $sqlt->fetch_assoc($char_result) )
        $realm_char_list[] = $row["guid"];

      $char_list[] = $realm_char_list;
    }

    $output .= '
                          <div class="coupon_part_title">
                            <span>'.lang("points", "choose_char_items").':</span>
                          </div>';

    for ( $i = 0; $i < count($char_list); $i++ )
    {
      $realm_chars = $char_list[$i];

      $cur_realm = $realm_list[$i];
      $realm_name_query = "SELECT * FROM config_servers WHERE `Index`='".$cur_realm."'";
      $realm_name_result = $sql["mgr"]->query($realm_name_query);
      $realm_name_result = $sql["mgr"]->fetch_assoc($realm_name_result);
      $cur_realm_name = $realm_name_result["Name"];

      $sqlt = new SQL;
      $sqlt->connect($characters_db[$cur_realm]["addr"], $characters_db[$cur_realm]["user"], $characters_db[$cur_realm]["pass"], $characters_db[$cur_realm]["name"], $characters_db[$cur_realm]["encoding"]);

      if ( count($realm_list) > 1 )
        $output .= '
                          <div class="coupon_part_chars">
                            <span>'.$cur_realm_name.'</span>
                          </div>';

      $first = true; // we want the first character to be selected

      foreach ( $realm_chars as $row)
      {
        $row_name_query = "SELECT * FROM characters WHERE guid='".$row."'";
        $row_name_result = $sqlt->query($row_name_query);
        $row_name_result = $sqlt->fetch_assoc($row_name_result);

        $output .= '
                          <div class="coupon_part_chars">
                            <input type="radio" name="item_character" value="'.($i + 1)."-".$row.'-'.$row_name_result["name"].'" '.( ( isset($first) ) ? 'checked="checked" ' : '' ).'/>
                            <a href="char.php?id='.$row.'&amp;realm='.$cur_realm.'">'.$row_name_result["name"].'</a> - <img src="img/c_icons/'.$row_name_result["race"].'-'.$row_name_result["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($row_name_result["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                            <img src="img/c_icons/'.$row_name_result["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($row_name_result["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt=""/> - '.lang("char", "level_short").char_get_level_color($row_name_result["level"]).'
                          </div>';

        unset($first);
      }
    }

    makebutton(lang("points", "prizes_send"), 'javascript:do_submit()', 130);
  }

  $output .= '
                    </td>
                  </tr>
                </table>
              </form>
            </div>
            <br />
          </center>
          <!-- end of point_system.php -->';
}

function edit_bag()
{
  global  $output, $bag_id, $sql, $core;

  // check chosen slots for injection
  $slots = array();
  $temp = $_GET["chosen_slot"];
  for ( $i = 0; $i < count($temp); $i++ )
  {
    if ( is_numeric($temp[$i]) )
      $slots[] = $temp[$i];
    else
      error(lang("global", "err_invalid_input"));
  }

  // build query list
  $query_list = "(";
  for ( $i = 0; $i < count($temp); $i++ )
    $query_list .= $temp[$i].( ( $i < count($temp) - 1 ) ? ", " : ")" );

  // get items
  $query = "SELECT * FROM point_system_prize_bag_items WHERE bag='".$bag_id."' AND slot IN ".$query_list;
  $result = $sql["mgr"]->query($query);

  $items = array();
  $item_counts = array();

  while ( $row = $sql["mgr"]->fetch_assoc($result) )
  {
    $items[] = $row["item_id"];
    $item_counts[] = $row["item_count"];
  }

  $item_temp = explode("-", $_GET["item_character"]);
  $item_realm_id = $item_temp[0];
  $item_receiver = $item_temp[1];
  $item_to = $item_temp[2];

  $temp = array();
  $temp_counts = array();

  while ( count($items) != 0 )
  {
    if ( count($temp) )
    {
      unset($temp);
      unset($temp_counts);
      $temp = array();
      $temp_counts = array();
    }

    $lim = ( ( count($items) > 12 ) ? 12 : count($items) );

    for ( $i = 0; $i < $lim; $i++ )
    {
      $temp[] = array_pop($items);
      $temp_counts[] = array_pop($item_counts);
    }

    $mails = array();

    // if the money & item characters are the same, we only need to send one mail
    $mail["receiver"] = $item_receiver;
    $mail["subject"] = lang("points", "mail_subject_bag");
    $mail["body"] = lang("points", "mail_body_bag");
    $mail["att_gold"] = 0;
    $mail["att_item"] = $temp;
    $mail["att_stack"] = $temp_counts;
    $mail["receiver_name"] = $item_to;

    array_push($mails, $mail);

    // send
    if ( $core == 1 )
      $result = send_ingame_mail_A($item_realm_id, $mails, true);
    else
      $result = send_ingame_mail_MT($item_realm_id, $mails, true);
  }

  // remove the items from the bag
  $query = "DELETE FROM point_system_prize_bag_items WHERE bag='".$bag_id."' AND slot IN ".$query_list;
  $result = $sql["mgr"]->query($query);

  redirect("point_system.php?action=view_bag&bag_id=".$bag_id);
}

function view_raffle()
{
  global  $output, $coupon_id, $raffle_id, $bag_id, $characters_db, $user_id, $user_name, $base_datasite, $item_datasite,
    $sql, $core;

  points_tabs();

  if ( !isset($_GET["purchased"]) )
  {
    $query = "SELECT * FROM point_system_raffles WHERE entry='".$raffle_id."'";
    $result = $sql["mgr"]->query($query);
    $raffle = $sql["mgr"]->fetch_assoc($result);

    $my_usage_query = "SELECT * FROM point_system_raffle_tickets WHERE raffle='".$raffle_id."' AND user='".$user_id."'";
    $my_usage_result = $sql["mgr"]->query($my_usage_query);
    $my_usage_count = $sql["mgr"]->num_rows($my_usage_result);

    $usage_query = "SELECT * FROM point_system_raffle_tickets WHERE raffle='".$raffle_id."'";
    $usage_result = $sql["mgr"]->query($usage_query);
    $usage_count = $sql["mgr"]->num_rows($usage_result);

    $output .= '
          <div class="tab_content">';

    // make sure we're allowed to use this coupon
    if ( ( ( $raffle["tickets_per_user"] <= $my_usage_count ) && ( $raffle["tickets_per_user"] != -1 ) ) || ( ( $usage_count >= $raffle["ticket_limit"] ) && ( $raffle["ticket_limit"] != -1 ) ) )
      $output .= '
            <span class="error">'.lang("points", "cannot_purchase_ticket").'</span>';
    else
    {
      $output .= '
            <form action="point_system.php" name="form1">
              <input type="hidden" name="action" value="do_purchase" />
              <input type="hidden" name="raffle_id" value="'.$raffle_id.'" />
              <table class="lined" id="coupon_table">
                <tr>
                  <td align="left">'.$raffle["title"].'</td>
                </tr>';

      if ( $raffle["text"] != "" )
        $output .= '
                <tr>
                  <td align="left">'.$raffle["text"].'</td>
                </tr>';

      $output .= '
              <tr>
                <td align="left"><span>'.lang("points", "drawing").'</span>:&nbsp;'.$raffle["drawing"].'</td>
              </tr>';

      if ( ( $raffle["credits"] != 0 ) || ( $raffle["money"] != 0 ) || ( $raffle["item_id"] != 0 ) )
      {
        $output .= '
                <tr>
                  <td>
                    <div class="coupon_parts">'.lang("points", "raffle_prizes").':</div>';

        if ( $raffle["credits"] != 0 )
        {
          if ( $raffle["credits"] > 1 )
            $tip = lang("points", "raffle_credits");
          else
            $tip = lang("points", "raffle_credit");

          $output .= '
                      <div class="coupon_parts">
                        <span>'.$raffle["credits"].'</span>
                        <span>'.$tip.'</span>
                      </div>';
        }

        if ( $raffle["money"] != 0 )
        {
          // extract gold/silver/copper from single gold number
          $raffle["money"] = str_pad($raffle["money"], 4, "0", STR_PAD_LEFT);
          $raffle_g = substr($raffle["money"],  0, -4);
          if ( $raffle_g == '' )
            $raffle_g = 0;
          $raffle_s = substr($raffle["money"], -4,  2);
          if ( ( $raffle_s == '' ) || ( $raffle_s == '00' ) )
            $raffle_s = 0;
          $raffle_c = substr($raffle["money"], -2);
          if ( ( $raffle_c == '' ) || ( $raffle_c == '00' ) )
            $raffle_c = 0;

          $output .= '
                      <div class="coupon_parts">
                        <span>'.$raffle_g.'</span>
                        <img src="img/gold.gif" alt="gold" />
                        <span>'.$raffle_s.'</span>
                        <img src="img/silver.gif" alt="gold" />
                        <span>'.$raffle_c.'</span>
                        <img src="img/copper.gif" alt="gold" />
                      </div>';
        }

        if ( $raffle["item_id"] != 0 )
        {
          if ( $raffle["item_id"] > 0 )
          {
            // get item data
            if ( $core == 1 )
            {
              $i_query = "SELECT 
                *, description AS description1, name1 AS name, quality AS Quality, inventorytype AS InventoryType, 
                socket_color_1 AS socketColor_1, socket_color_2 AS socketColor_2, socket_color_3 AS socketColor_3,
                requiredlevel AS RequiredLevel, allowableclass AS AllowableClass,
                sellprice AS SellPrice, itemlevel AS ItemLevel
                FROM items "
                  .( ( $locales_search_option != 0 ) ? "LEFT JOIN items_localized ON (items_localized.entry=items.entry AND language_code='".$locales_search_option."') " : " " ).
                "WHERE items.entry='".$raffle["item_id"]."'";
            }
            else
            {
              $i_query = "SELECT *, description AS description1 FROM item_template "
                  .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_item ON locales_item.entry=item_template.entry " : " " ).
                "WHERE item_template.entry='".$raffle["item_id"]."'";
            }

            $i_result = $sql["world"]->query($i_query);
            $i = $sql["world"]->fetch_assoc($i_result);

            $output .= '
                      <div class="coupon_parts">
                        <div class="coupon_item">
                          <div>
                            <a id="ch_inv_padding" href="'.$base_datasite.$item_datasite.$raffle["item_id"].'" target="_blank" onmouseover="ShowTooltip(this,\'_b\');" onmouseout="HideTooltip(\'_b\');">
                              <img src="'.get_item_icon($raffle["item_id"]).'" alt="" />
                            </a>';

            if ( $raffle["item_count"] > 1 )
              $output .= '
                            <div id="coupon_item_quantity_shadow">'.$raffle["item_count"].'</div>
                            <div id="coupon_item_quantity">'.$raffle["item_count"].'</div>';

            $output .= '
                          </div>';

            // build a tooltip object for this item
            $output .= '
                          <div class="item_tooltip" id="tooltip_b" style="left: -129px; top: 42px;">
                            <table>
                              <tr>
                                <td>'.get_item_tooltip($i, $item[4], $item[5], $item[6], $item[7], $item[8]).'</td>
                              </tr>
                            </table>
                          </div>';

            $output .= '
                        </div>
                      </div>';
          }
          else
          {
            $output .= '
                      <div class="coupon_parts">
                        <div>
                          <a href="point_system.php?action=view_bag&amp;bag_id='.($coupon["item_id"]*-1).'&amp;raffle_id='.$raffle_id.'" onmousemove="oldtoolTip(\''.lang("points", "prize_bag").'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()">
                            <img src="'.get_item_icon(1725).'" alt="" />
                          </a>
                        </div>
                      </div>';
          }
        }

        $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="coupon_parts">'.lang("points", "ticket_cost").':</div>';

        if ( $raffle["cost_credits"] != 0 )
        {
          // get our credit balance
          $query = "SELECT credits FROM config_accounts WHERE Login='".$user_name."'";
          $result = $sql["mgr"]->query($query);
          $result = $sql["mgr"]->fetch_assoc($result);
          $credits = $result["credits"];

          // check our funds vs Unlimited and the raffle's requirement
          $insufficient = false;
          if ( ( $credits < $raffle["cost_credits"] ) && ( $credits > -1 ) )
            $insufficient = true;

          if ( $raffle["cost_credits"] > 1 )
            $tip = lang("points", "raffle_credits");
          else
            $tip = lang("points", "raffle_credit");

          $output .= '
                      <div class="coupon_parts">
                        <span>'.$raffle["cost_credits"].'</span>
                        <span>'.$tip.'</span>
                        <span class="points_credit_highlight">'.( ( $credits > -1 ) ? '&nbsp;'.lang("points", "balance").':&nbsp;'.rtrim($credits, "0.") : '' ).'</span>
                        <span class="points_credit_highlight">'.( ( $insufficient ) ? '&nbsp;<b>('.lang("points", "insufficient_funds").')</b>' : '' ).'</span>
                        <span class="points_credit_highlight">'.( ( $credits <= -1 ) ? '&nbsp;<b>('.lang("points", "unlimited").')</b>' : '' ).'</span>
                      </div>';
        }

        if ( $raffle["cost_money"] != 0 )
        {
          // extract gold/silver/copper from single gold number
          $raffle["cost_money"] = str_pad($raffle["cost_money"], 4, "0", STR_PAD_LEFT);
          $raffle_cost_g = substr($raffle["cost_money"],  0, -4);
          if ( $raffle_cost_g == '' )
            $raffle_cost_g = 0;
          $raffle_cost_s = substr($raffle["cost_money"], -4,  2);
          if ( ( $raffle_cost_s == '' ) || ( $raffle_cost_s == '00' ) )
            $raffle_cost_s = 0;
          $raffle_cost_c = substr($raffle["cost_money"], -2);
          if ( ( $raffle_cost_c == '' ) || ( $raffle_cost_c == '00' ) )
            $raffle_cost_c = 0;

          $output .= '
                      <div class="coupon_parts">
                        <span>'.$raffle_cost_g.'</span>
                        <img src="img/gold.gif" alt="gold" />
                        <span>'.$raffle_cost_s.'</span>
                        <img src="img/silver.gif" alt="gold" />
                        <span>'.$raffle_cost_c.'</span>
                        <img src="img/copper.gif" alt="gold" />
                      </div>';

          $output .= '
                      <div class="coupon_part_title">
                        <span>'.lang("points", "choose_char_use_money").':</span>
                      </div>';

          // get characters
          $char_list = array();
          $realm_list = array();

          foreach ( $characters_db as $db )
          {
            $sqlt = new SQL;
            $sqlt->connect($db["addr"], $db["user"], $db["pass"], $db["name"], $db["encoding"]);

            $realm_char_list = array();

            // store the realm id for later
            $realm_list[] = $db["id"];

            if ( $core == 1 )
              $char_query = "SELECT guid FROM characters WHERE acct='".$user_id."' ORDER BY guid ASC";
            else
              $char_query = "SELECT guid FROM characters WHERE account='".$user_id."' ORDER BY guid ASC";

            $char_result = $sqlt->query($char_query);

            while ( $row = $sqlt->fetch_assoc($char_result) )
              $realm_char_list[] = $row["guid"];

            $char_list[] = $realm_char_list;
          }

          for ( $i = 0; $i < count($char_list); $i++ )
          {
            $realm_chars = $char_list[$i];

            $cur_realm = $realm_list[$i];
            $realm_name_query = "SELECT * FROM config_servers WHERE `Index`='".$cur_realm."'";
            $realm_name_result = $sql["mgr"]->query($realm_name_query);
            $realm_name_result = $sql["mgr"]->fetch_assoc($realm_name_result);
            $cur_realm_name = $realm_name_result["Name"];

            $sqlt = new SQL;
            $sqlt->connect($characters_db[$cur_realm]["addr"], $characters_db[$cur_realm]["user"], $characters_db[$cur_realm]["pass"], $characters_db[$cur_realm]["name"], $characters_db[$cur_realm]["encoding"]);

            if ( count($realm_list) > 1 )
              $output .= '
                      <div class="coupon_part_chars">
                        <span>'.$cur_realm_name.'</span>
                      </div>';

            $output .= '
                      <div class="coupon_part_chars">
                        <div class="fake_table">';

            $first = true; // we want the first character to be selected

            foreach ( $realm_chars as $row)
            {
              if ( $core == 1 )
                $char_query = "SELECT *, gold AS money FROM characters WHERE guid='".$row."'";
              else
                $char_query = "SELECT * FROM characters WHERE guid='".$row."'";

              $char_result = $sqlt->query($char_query);
              $char = $sqlt->fetch_assoc($char_result);

              // extract gold/silver/copper from single gold number
              $char["money"] = str_pad($char["money"], 4, "0", STR_PAD_LEFT);
              $char_g = substr($char["money"],  0, -4);
              if ( $char_g == '' )
                $char_g = 0;
              $char_s = substr($char["money"], -4,  2);
              if ( ( $char_s == '' ) || ( $char_s == '00' ) )
                $char_s = 0;
              $char_c = substr($char["money"], -2);
              if ( ( $char_c == '' ) || ( $char_c == '00' ) )
                $char_c = 0;

              $output .= '
                          <div class="fake_table_cell">
                            <input type="radio" name="money_character" value="'.($i + 1)."-".$row.'-'.$char["name"].'"'.( ( isset($first) ) ? ' checked="checked"' : '' ).( ( ( $char["online"] ) || ( $char["money"] < $raffle["cost_money"] ) ) ? ' disabled="disabled"' : '' ).' />
                            <a href="char.php?id='.$row.'&amp;realm='.$cur_realm.'">'.$char["name"].'</a> - <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                            <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt=""/> - '.lang("char", "level_short").char_get_level_color($char["level"]).'
                          </div>
                          <div class="fake_table_cell">
                            <span>'.$char_g.'</span>
                            <img src="img/gold.gif" alt="gold" />
                            <span>'.$char_s.'</span>
                            <img src="img/silver.gif" alt="gold" />
                            <span>'.$char_c.'</span>
                            <img src="img/copper.gif" alt="gold" />
                          </div>';

              unset($first);
            }
          }
        }

        $output .= '
                        </div>
                      </div>
                    </td>
                  </tr>';
      }

      if ( $raffle["tickets_per_user"] > 1 )
        $output .= '
                  <tr>
                    <td>
                      <div class="coupon_parts">'.lang("points", "tickets_purchased").':&nbsp;<b>'.$my_usage_count.'</b></div>
                    </td>
                  </tr>';

      $output .= '
                  <tr>
                    <td align="right">
                      <a href="javascript:do_submit(\'form1\',0)">
                        <span><img src="img/aff_tick.png" width="16" height="16" alt="" />&nbsp;'.lang("points", "confirm_purchase").'</span>
                      </a>
                    </td>
                  </tr>
                </table>
              </form>';
    }
  }
  else
  {
    $output .= '
            <div class="tab_content">';

    $output .= '
              <span>'.lang("points", "purchased").'</span>';
  }

  $output .= '
            </div>
            <br />
          </center>
          <!-- end of point_system.php -->';
}

function do_purchase()
{
  global  $output, $characters_db, $raffle_id, $user_name, $user_id, $sql, $core;

  // make sure the money character (if provided) is not an injection
  $money_character = explode("-", $_GET["money_character"]);
  $char_choice = $money_character[1];
  $char_choice = ( ( isset($_GET["money_character"]) ) ? $char_choice : NULL );
  $char_choice = ( ( is_numeric($char_choice) ) ? $char_choice : 0 );
  $char_realm = $money_character[0];
  $char_realm = ( ( is_numeric($char_realm) ) ? $char_realm : 0 );

  // check that the realm id is at least a number
  if ( $char_realm == 0 )
    error(lang("global", "err_invalid_input"));

  // get our raffle
  $query = "SELECT * FROM point_system_raffles WHERE entry='".$raffle_id."'";
  $result = $sql["mgr"]->query($query);
  $raffle = $sql["mgr"]->fetch_assoc($result);

  // get our usage
  $query = "SELECT * FROM point_system_raffle_tickets WHERE raffle='".$coupon_id."' AND user='".$user_id."'";
  $result = $sql["mgr"]->query($query);
  $my_usage = $sql["mgr"]->num_rows($result);

  // get overall usage
  $query = "SELECT * FROM point_system_raffle_tickets WHERE raffle='".$coupon_id."'";
  $result = $sql["mgr"]->query($query);
  $usage = $sql["mgr"]->num_rows($result);

  // check whether this raffle is available for us to use
  if ( ( ( $raffle["tickets_per_iser"] != -1 ) && ( $raffle["tickets_per_user"] <= $my_usage ) ) || ( ( $raffle["ticket_limit"] != -1 ) && ( $raffle["ticket_limit"] <= $usage ) ) )
    redirect("point_system.php?action=view_raffle&raffle_id=".$raffle_id."&error=3");

  // if the raffle requires payment in credits, deduct those
  if ( $raffle["cost_credits"] != 0 )
  {
    // get our credit balance
    $query = "SELECT credits FROM config_accounts WHERE Login='".$user_name."'";
    $result = $sql["mgr"]->query($query);
    $result = $sql["mgr"]->fetch_assoc($result);
    $credits = $result["credits"];

    // first, we check whether we have normal credits and less than required
    if ( $credits > -1 )
    {
      if ( $credits < $raffle["cost_credits"] )
        redirect("point_system.php?action=view_raffle&raffle_id=".$raffle_id."&error=4");
      else
      {
        // deduct the credits from our account
        $query = "UPDATE config_accounts SET credits=credits-'".$raffle["cost_credits"]."' WHERE Login='".$user_name."'";
        $result = $sql["mgr"]->query($query);
      }
    }
  }

  // if the raffle requires payment in monies, deduct those
  if ( $raffle["cost_money"] != 0 )
  {
    $sqlt = new SQL;
    $sqlt->connect($characters_db[$char_realm]["addr"], $characters_db[$char_realm]["user"], $characters_db[$char_realm]["pass"], $characters_db[$char_realm]["name"], $characters_db[$char_realm]["encoding"]);

    // the selected character's money
    if ( $core == 1 )
      $char_query = "SELECT gold AS money, online FROM characters WHERE guid='".$char_choice."'";
    else
      $char_query = "SELECT money, online FROM characters WHERE guid='".$char_choice."'";

    $char_result = $sqlt->query($char_query);
    $char = $sqlt->fetch_assoc($char_result);

    // make sure the character has enough money and is offline
    if ( $char["money"] <= $raffle["cost_money"] )
      redirect("point_system.php?action=view_raffle&raffle_id=".$raffle_id."&error=5");

    if ( $char["online"] )
      redirect("point_system.php?action=view_raffle&raffle_id=".$raffle_id."&error=6");

    // deduct the required money from the character's gold
    if ( $core == 1 )
      $query = "UPDATE characters SET gold=gold-'".$raffle["cost_money"]."' WHERE guid='".$char_choice."'";
    else
      $query = "UPDATE characters SET money=money-'".$raffle["cost_money"]."' WHERE guid='".$char_choice."'";

    $result = $sqlt->query($query);
  }

  // make an entry for our usage
  $query = "INSERT INTO point_system_raffle_tickets (raffle, user, date_purchased) VALUES ('".$raffle_id."', '".$user_id."', NOW())";
  $result = $sql["mgr"]->query($query);

  redirect("point_system.php?action=view_raffle&purchased=1");
}

function points_tabs()
{
  global  $output, $coupon_id, $raffle_id, $bag_id, $action, $sql, $core;

  $output .= '
        <!-- start of point_system.php -->
        <center>
          <div class="tab">
            <ul>
              <li'.( ( $action == "coupons" ) ? ' class="selected"' : '' ).'><a href="point_system.php?action=coupons'.( ( $coupon_id != 0 ) ? '&amp;coupon_id='.$coupon_id : '' ).( ( $bag_id != 0 ) ? '&amp;bag_id='.$bag_id : '' ).( ( $raffle_id != 0 ) ? '&amp;raffle_id='.$raffle_id : '' ).'">'.lang("points", "coupons").'</a></li>
              <li'.( ( $action == "raffles" ) ? ' class="selected"' : '' ).'><a href="point_system.php?action=raffles'.( ( $coupon_id != 0 ) ? '&amp;coupon_id='.$coupon_id : '' ).( ( $bag_id != 0 ) ? '&amp;bag_id='.$bag_id : '' ).( ( $raffle_id != 0 ) ? '&amp;raffle_id='.$raffle_id : '' ).'">'.lang("points", "raffles").'</a></li>
              <!-- li'.( ( $action == "contests" ) ? ' class="selected"' : '' ).'><a href="point_system.php?action=contests">'.lang("points", "contests").'</a></li -->';

  if ( ( $action == "redeem_coupon" ) || ( $coupon_id != 0 ) )
    $output .= '
              <li'.( ( $action == "redeem_coupon" ) ? ' class="selected"' : '' ).'><a href="point_system.php?action=redeem_coupon&amp;coupon_id='.$coupon_id.( ( $bag_id != 0 ) ? '&amp;bag_id='.$bag_id : '' ).( ( $raffle_id != 0 ) ? '&amp;raffle_id='.$raffle_id : '' ).'">'.lang("points", "redeem_coupon").'</a></li>';

  if ( $action == "view_bag" )
    $output .= '
              <li'.( ( $action == "view_bag" ) ? ' class="selected"' : '' ).'><a href="point_system.php?action=view_bag&amp;bag_id='.$bag_id.( ( $coupon_id != 0 ) ? '&amp;coupon_id='.$coupon_id : '' ).( ( $raffle_id != 0 ) ? '&amp;raffle_id='.$raffle_id : '' ).'">'.lang("points", "view_bag").'</a></li>';

  if ( ( $action == "view_raffle" ) || ( $raffle_id != 0 ) )
    $output .= '
              <li'.( ( $action == "view_raffle" ) ? ' class="selected"' : '' ).'><a href="point_system.php?action=view_raffle&amp;raffle_id='.$raffle_id.( ( $coupon_id != 0 ) ? '&amp;coupon_id='.$coupon_id : '' ).( ( $bag_id != 0 ) ? '&amp;bag_id='.$bag_id : '' ).'">'.lang("points", "view_raffle").'</a></li>';

  $output .= '
            </ul>
          </div>';
}


//#############################################################################
// MAIN
//#############################################################################
$err = ( ( isset($_GET["error"]) ) ? $_GET["error"] : NULL );

$output .= '
      <div class="bubble">
        <div class="top">';

switch ( $err )
{
  case 1:
    $output .= '
          <h1>
            <font class="error">'.lang("global", "empty_fields").'</font>
          </h1>';
    break;
  case 2:
    $output .= '
          <h1>
            <font class="error">'.lang("points", "not_allowed").'</font>
          </h1>';
    break;
  case 3:
    $output .= '
          <h1>
            <font class="error">'.lang("points", "cannot_purchase_ticket").'</font>
          </h1>';
    break;
  case 4:
    $output .= '
          <h1>
            <font class="error">'.lang("points", "insufficient_funds").'</font>
          </h1>';
    break;
  case 5:
    $output .= '
          <h1>
            <font class="error">'.lang("points", "insufficient_funds_gold").'</font>
          </h1>';
    break;
  case 6:
    $output .= '
          <h1>
            <font class="error">'.lang("points", "character_online").'</font>
          </h1>';
    break;
  default: //no error
    $output .= '
          <h1>'.lang("points", "points").'</h1>';
}

$output .= '
        </div>';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : "coupons" );

$coupon_id = ( ( isset($_GET["coupon_id"]) ) ? $_GET["coupon_id"] : 0 );
$bag_id = ( ( isset($_GET["bag_id"]) ) ? $_GET["bag_id"] : 0 );
$raffle_id = ( ( isset($_GET["raffle_id"]) ) ? $_GET["raffle_id"] : 0 );

// prevent injection
if ( ( !is_numeric($coupon_id) ) || ( !is_numeric($bag_id) ) || ( !is_numeric($raffle_id) ) )
  error(lang("global", "err_invalid_input"));

if ( $action == "coupons" )
  coupons();
elseif ( $action == "redeem_coupon" )
  redeem_coupon();
elseif ( $action == "do_redeem" )
  do_redeem();
elseif ( $action == "raffles" )
  raffles();
elseif ( $action == "view_raffle" )
  view_raffle();
elseif ( $action == "do_purchase" )
  do_purchase();
elseif ( $action == "contests" )
  contests();
elseif ( $action == "view_bag" )
  view_bag();
elseif ( $action == "edit_bag" )
  edit_bag();

unset($action_permission);

require_once "footer.php";


?>
