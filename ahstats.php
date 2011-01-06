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
require_once("libs/get_lib.php");
require_once("libs/item_lib.php");
valid_login($action_permission["view"]);

//#############################################################################
// BROWSE AUCTIONS
//#############################################################################
function browse_auctions()
{
  global $output, $characters_db, $world_db, $realm_id, $locales_search_option,
    $itemperpage, $base_datasite, $item_datasite, $server, $user_lvl, $user_id, $sql, $core;

  //wowhead_tt();

  $red = '"#DD5047"';
  $blue = '"#0097CD"';
  $sidecolor = array(1 => $blue, 2 => $red, 3 => $blue, 4 => $blue, 5 => $red, 6 => $red, 7 => $blue, 8 => $red, 10 => $red);
  $hiddencols = array(1, 8, 9, 10);

  //==========================$_GET and SECURE=================================
  $start = ( ( isset($_GET["start"]) ) ? $sql["char"]->quote_smart($_GET["start"]) : 0 );
  if ( !is_numeric($start) )
    $start = 0;

  $order_by = (isset($_GET["order_by"])) ? $sql["char"]->quote_smart($_GET["order_by"]) : "time";
  if ( !preg_match("/^[_[:lower:]]{1,15}$/", $order_by) )
    $order_by = "time";

  $dir = ( ( isset($_GET["dir"]) ) ? $sql["char"]->quote_smart($_GET["dir"]) : 1 );
  if ( !preg_match("/^[01]{1}$/", $dir) )
    $dir = 1;

  $order_dir = ( ( $dir ) ? "ASC" : "DESC" );
  $dir = ( ( $dir ) ? 0 : 1 );
  //==========================$_GET and SECURE end=============================

  if ( !$user_lvl && !$server[$realm_id]["both_factions"] )
  {
    $result = $sql["char"]->query("SELECT race FROM characters
      WHERE account=".$user_id." AND totaltime=(SELECT MAX(totaltime) FROM characters WHERE account=".$user_id.")
      LIMIT 1");
    if ( $sql["char"]->num_rows($result) )
    {
      $order_side = ( ( in_array($sql["char"]->result($result, 0, "race"), array(2, 5, 6, 8, 10)) ) ?
      " AND characters.race IN (2,5,6,8,10) " : " AND characters.race IN (1,3,4,7,11) " );
    }
    else
      $order_side = "";
  }
  else
    $order_side = "";

  //==========================Browse/Search CHECK==============================
  $search_by = '';
  $search_value = '';
  $search_filter = '';
  $search_class = -1;
  $search_quality = -1;

  if ( ( isset($_GET["search_value"]) && isset($_GET["search_by"]) ) || ( isset($_GET["search_class"]) ) || ( isset($_GET["search_quality"]) ) )
  {
    $search_value = $sql["char"]->quote_smart($_GET["search_value"]);
    $search_by = $sql["char"]->quote_smart($_GET["search_by"]);
    $search_class = $sql["char"]->quote_smart($_GET["search_class"]);
    $search_quality = $sql["char"]->quote_smart($_GET["search_quality"]);

    switch ( $search_by )
    {
      case "item_name":
        if ( ( ( $search_class >= 0 ) || ( $search_quality >= 0 ) ) && ( !isset($search_value) ) )
        {
          if ( $search_class >= 0 )
            if ( $core == 1 )
              $search_filter = "AND items.class='".$search_class."'";
            else
              $search_filter = "AND item_template.class='".$search_class."'";
          if ( $search_quality >= 0 )
            if ( $core == 1 )
              $search_filter = "AND items.quality='".$search_quality."'";
            else
              $search_filter = "AND item_template.Quality='".$search_quality."'";
        }
        else
        {
          $item_prefix = "";
          if ( $search_class >= 0 )
            if ( $core == 1 )
              $item_prefix .= "AND items.class='".$search_class."' ";
            else
              $item_prefix .= "AND item_template.class='".$search_class."' ";
          if ( $search_quality >= 0 )
            if ( $core == 1 )
              $item_prefix .= "AND items.quality='".$search_quality."' ";
            else
              $item_prefix .= "AND item_template.Quality='".$search_quality."' ";

          if ( $core == 1 )
            $result = $sql["char"]->query("SELECT entry FROM ".$world_db[$realm_id]['name'].".items WHERE name LIKE '%".$search_value."%' ".$item_prefix);
          else
            $result = $sql["char"]->query("SELECT entry FROM ".$world_db[$realm_id]['name'].".item_template WHERE name LIKE '%".$search_value."%' ".$item_prefix);

          if ( $core == 1 )
            $search_filter = "AND auctions.item IN(0";
          elseif ( $core == 2 )
            $search_filter = "AND auction.item_template IN(0";
          else
            $search_filter = "AND item_instance.itemEntry IN(0";

          while ( $item = $sql["char"]->fetch_row($result) )
            $search_filter .= ", ".$item[0];
            $search_filter .= ")";
        }
        break;
      case "item_id":
        if ( $core == 1 )
          $search_filter = "AND auctions.item='".$search_value."'";
        elseif ( $core == 2 )
          $search_filter = "AND auction.item_template='".$search_value."'";
        else
          $search_filter = "AND item_instance.itemEntry='".$search_value."'";
        break;
      case "seller_name":
        if ( ( ( $search_class >= 0 ) || ( $search_quality >= 0 ) ) && ( !isset($search_value) ) )
        {
          if ( $search_class >= 0 )
            if ( $core == 1 )
              $search_filter = "AND items.class='".$search_class."'";
            else
              $search_filter = "AND item_template.class='".$search_class."'";
          if ( $search_quality >= 0 )
            if ( $core == 1 )
              $search_filter = "AND items.quality='".$search_quality."'";
            else
              $search_filter = "AND item_template.Quality='".$search_quality."'";
        }
        else
        {
          $item_prefix = "";
          if ( $search_class >= 0 )
            if ( $core == 1 )
              $item_prefix .= "AND items.class='".$search_class."' ";
            else
              $item_prefix .= "AND item_template.class='".$search_class."' ";
          if ( $search_quality >= 0 )
            if ( $core == 1 )
              $item_prefix .= "AND items.quality='".$search_quality."' ";
            else
              $item_prefix .= "AND item_template.Quality='".$search_quality."' ";

          $result = $sql["char"]->query("SELECT guid FROM characters WHERE name LIKE '%".$search_value."%'");
          $search_filter = $item_prefix;

          if ( $core == 1 )
            $search_filter .= "AND auctions.owner IN(0";
          elseif ( $core == 2 )
            $search_filter .= "AND auction.itemowner IN(0";
          else
            $search_filter .= "AND auctionhouse.itemowner IN(0";

          while ( $char = $sql["char"]->fetch_row($result) )
            $search_filter .= ", ".$char[0];
          $search_filter .= ")";
          $search_filter .= $item_prefix;
        }
        break;
      case "buyer_name":
        if ( ( ( $search_class >= 0 ) || ( $search_quality >= 0 ) ) && ( !isset($search_value) ) )
        {
          if ( $search_class >= 0 )
            if ( $core == 1 )
              $search_filter = "AND items.class='".$search_class."'";
            else
              $search_filter = "AND item_template.class='".$search_class."'";
          if ( $search_quality >= 0 )
            if ( $core == 1 )
              $search_filter = "AND items.quality='".$search_quality."'";
            else
              $search_filter = "AND item_template.Quality='".$search_quality."'";
        }
        else
        {
          $item_prefix = "";
          if ( $search_class >= 0 )
            if ( $core == 1 )
              $item_prefix .= "AND items.class='".$search_class."' ";
            else
              $item_prefix .= "AND item_template.class='".$search_class."' ";
          if ( $search_quality >= 0 )
            if ( $core == 1 )
              $item_prefix .= "AND items.quality='".$search_quality."' ";
            else
              $item_prefix .= "AND item_template.Quality='".$search_quality."' ";

          $result = $sql["char"]->query("SELECT guid FROM characters WHERE name LIKE '%".$search_value."%'");
          $search_filter = $item_prefix;

          if ( $core == 1 )
            $search_filter .= "AND auctions.bidder IN(-1";
          elseif ( $core == 2 )
            $search_filter .= "AND auction.buyguid IN(-1";
          else
            $search_filter .= "AND auctionhouse.buyguid IN(-1";

          while ( $char = $sql["char"]->fetch_row($result) )
            $search_filter .= ", ".$char[0];
          $search_filter .= ")";
        }
        break;
      default:
        redirect("ahstats.php?error=1");
    }
    /*$query_1 = $sql["char"]->query("SELECT count(*)
      FROM `".$characters_db[$realm_id]['name']."`.`characters` , `".$characters_db[$realm_id]['name']."`.`item_instance` ,
      `".$world_db[$realm_id]['name']."`.`item_template` , `".$characters_db[$realm_id]['name']."`.`auctionhouse`
      LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` c2 ON `c2`.`guid`=`auctionhouse`.`buyguid`
      WHERE `auctionhouse`.`itemowner`=`characters`.`guid` AND `auctionhouse`.`item_template`=`item_template`.`entry` AND `auctionhouse`.`itemguid`=`item_instance`.`guid`
      $search_filter $order_side");*/

    // this_is_junk: really?
    if ( $core == 1 )
      $query_1 = $sql["char"]->query("SELECT COUNT(*) FROM auctions");
    elseif ( $core == 2 )
      $query_1 = $sql["char"]->query("SELECT COUNT(*) FROM auction");
    else
      $query_1 = $sql["char"]->query("SELECT COUNT(*) FROM auctionhouse");
  }
  else
  {
    if ( $core == 1 )
      $query_1 = $sql["char"]->query("SELECT COUNT(*) FROM auctions");
    elseif ( $core == 2 )
      $query_1 = $sql["char"]->query("SELECT COUNT(*) FROM auction");
    else
      $query_1 = $sql["char"]->query("SELECT COUNT(*) FROM auctionhouse");
  }

  /*$result = $sql["char"]->query("SELECT `characters`.`name` AS `seller`, `auctionhouse`.`item_template` AS `itemid`,
    `item_template`.`name` AS `itemname`, `auctionhouse`.`buyoutprice` AS `buyout`, `auctionhouse`.`time`-unix_timestamp(),
    `c2`.`name` AS `encherisseur`, `auctionhouse`.`lastbid`, `auctionhouse`.`startbid`,
    SUBSTRING_INDEX(SUBSTRING_INDEX(`item_instance`.`data`, ' ',15), ' ',-1) AS qty, `characters`.`race` AS seller_race,
    `c2`.`race` AS buyer_race
    FROM `".$characters_db[$realm_id]['name']."`.`characters` , `".$characters_db[$realm_id]['name']."`.`item_instance` ,
    `".$world_db[$realm_id]['name']."`.`item_template` , `".$characters_db[$realm_id]['name']."`.`auctionhouse`
    LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` c2 ON `c2`.`guid`=`auctionhouse`.`buyguid`
    WHERE `auctionhouse`.`itemowner`=`characters`.`guid` AND `auctionhouse`.`item_template`=`item_template`.`entry` AND `auctionhouse`.`itemguid`=`item_instance`.`guid`
    $search_filter
    $order_side ORDER BY `auctionhouse`.`$order_by` $order_dir LIMIT $start, $itemperpage");*/

  // Sorting special cases, everything else works as is...
  if ( $core == 1 )
  {
    switch ( $order_by )
    {
      case "owner":
        $post_order_by = "characters.name";
        break;
      case "item":
        $post_order_by = "playeritems.entry";
        break;
      default:
        $post_order_by = $order_by;
    }
  }
  elseif ( $core == 2 )
  {
    switch ( $order_by )
    {
      case "owner":
        $post_order_by = "characters.name";
        break;
      case "item":
        $post_order_by = "auction.item_template";
        break;
      case "buyout":
        $post_order_by = "auction.buyoutprice";
        break;
      case "bidder":
        $post_order_by = "auction.buyguid";
        break;
      case "bid":
        $post_order_by = "auction.lastbid";
        break;
      default:
        $post_order_by = $order_by;
    }
  }
  else
  {
    switch ( $order_by )
    {
      case "owner":
        $post_order_by = "characters.name";
        break;
      case "item":
        $post_order_by = "item_instance.itemEntry";
        break;
      case "buyout":
        $post_order_by = "auctionhouse.buyoutprice";
        break;
      case "bidder":
        $post_order_by = "auctionhouse.buyguid";
        break;
      case "bid":
        $post_order_by = "auctionhouse.lastbid";
        break;
      default:
        $post_order_by = $order_by;
    }
  }

  if ( $core == 1 )
    // this_is_junk: the guid in auction is stored raw, so we have to subtract 4611686018427387904 to get the matching guid stored in playeritems :/
    $query = "SELECT characters.name AS owner_name, owner, playeritems.entry AS item_entry,
      item-4611686018427387904 AS item, buyout, time-UNIX_TIMESTAMP() AS time, bidder, bid
      FROM auctions, ".$world_db[$realm_id]['name'].".items
        LEFT JOIN characters ON auctions.owner=characters.guid
        LEFT JOIN playeritems ON auctions.item-4611686018427387904=playeritems.guid
      ".$seach_filter." ".$order_side." ORDER BY ".$post_order_by." ".$order_dir." LIMIT ".$start.", ".$itemperpage;
  elseif ( $core == 2 )
    $query = "SELECT characters.name AS owner_name, auction.item_template AS item_entry,
      auction.itemowner AS owner, item_template.name AS itemname, itemguid AS item,
      auction.buyoutprice AS buyout, auction.time-UNIX_TIMESTAMP() AS time,
      c2.name AS bidder_name, auction.buyguid AS bidder, auction.lastbid AS bid, auction.startbid,
      SUBSTRING_INDEX(SUBSTRING_INDEX(item_instance.data, ' ',15), ' ',-1) AS qty,
      characters.race AS seller_race, c2.race AS buyer_race
      FROM characters, item_instance, ".$world_db[$realm_id]['name'].".item_template, auction
        LEFT JOIN characters c2 ON c2.guid=auction.buyguid
      WHERE auction.itemowner=characters.guid
        AND auction.item_template=item_template.entry
        AND auction.itemguid=item_instance.guid
      ".$search_filter." ".$order_side." ORDER BY ".$post_order_by." ".$order_dir." LIMIT ".$start.", ".$itemperpage;
  else
    $query = "SELECT characters.name AS owner_name, item_instance.itemEntry AS item_entry,
      auctionhouse.itemowner AS owner, item_template.name AS itemname, itemguid AS item,
      auctionhouse.buyoutprice AS buyout, auctionhouse.time-unix_timestamp() AS time,
      c2.name AS bidder_name, auctionhouse.buyguid AS bidder, auctionhouse.lastbid AS bid, auctionhouse.startbid,
      item_instance.count AS qty,
      characters.race AS seller_race, c2.race AS buyer_race
      FROM characters, item_instance, ".$world_db[$realm_id]['name'].".item_template, auctionhouse
        LEFT JOIN characters c2 ON c2.guid=auctionhouse.buyguid
      WHERE auctionhouse.itemowner=characters.guid
        AND item_instance.itemEntry=item_template.entry
        AND auctionhouse.itemguid=item_instance.guid
      ".$search_filter." ".$order_side." ORDER BY ".$post_order_by." ".$order_dir." LIMIT ".$start.", ".$itemperpage;

  $result = $sql["char"]->query($query);

  $all_record = $sql["char"]->result($query_1, 0);

  // give ourselves a little less XSS
  $search_value = htmlspecialchars($search_value);
  $search_by = htmlspecialchars($search_by);
  $search_class = htmlspecialchars($search_class);
  $search_quality = htmlspecialchars($search_quality);

  //=====================top tage navigaion starts here========================
  $output .= '
        <center>
          <table class="top_hidden">
            <tr>
              <td width="80%">
                <form action="ahstats.php" method="get" name="form">
                  <input type="hidden" name="error" value="2" />
                  <table class="hidden">
                    <tr>
                      <td>
                        <input type="text" size="24" name="search_value" value="'.$search_value.'" />
                      </td>
                      <td>
                        <select name="search_by">
                          <option'.( $search_by == "item_name" ? ' selected="selected"' : '' ).' value="item_name">'.lang("auctionhouse", "item_name").'</option>
                          <option'.( $search_by == "item_id" ? ' selected="selected"' : '' ).' value="item_id">'.lang("auctionhouse", "item_id").'</option>
                          <option'.( $search_by == "seller_name" ? ' selected="selected"' : '' ).' value="seller_name">'.lang("auctionhouse", "seller_name").'</option>
                          <option'.( $search_by == "buyer_name" ? ' selected="selected"' : '' ).' value="buyer_name">'.lang("auctionhouse", "buyer_name").'</option>
                        </select>
                      </td>
                      <td>
                        <select name="search_class">
                          <option'.( $search_class == -1 ? ' selected="selected"' : '' ).' value="-1">'.lang("auctionhouse", "all").'</option>
                          <option'.( $search_class == 0 ? ' selected="selected"' : '' ).' value="0">'.lang("item", "consumable").'</option>
                          <option'.( $search_class == 1 ? ' selected="selected"' : '' ).' value="1">'.lang("item", "bag").'</option>
                          <option'.( $search_class == 2 ? ' selected="selected"' : '' ).' value="2">'.lang("item", "weapon").'</option>
                          <option'.( $search_class == 4 ? ' selected="selected"' : '' ).' value="4">'.lang("item", "armor").'</option>
                          <option'.( $search_class == 5 ? ' selected="selected"' : '' ).' value="5">'.lang("item", "reagent").'</option>
                          <option'.( $search_class == 7 ? ' selected="selected"' : '' ).' value="7">'.lang("item", "trade_goods").'</option>
                          <option'.( $search_class == 9 ? ' selected="selected"' : '' ).' value="9">'.lang("item", "recipe").'</option>
                          <option'.( $search_class == 11 ? ' selected="selected"' : '' ).' value="11">'.lang("item", "quiver").'</option>
                          <option'.( $search_class == 14 ? ' selected="selected"' : '' ).' value="14">'.lang("item", "permanent").'</option>
                          <option'.( $search_class == 15 ? ' selected="selected"' : '' ).' value="15">'.lang("item", "misc_short").'</option>
                        </select>
                      </td>
                      <td>
                        <select name="search_quality">
                          <option'.( $search_quality == -1 ? ' selected="selected"' : '' ).' value="-1">'.lang("auctionhouse", "all").'</option>
                          <option'.( $search_quality == 0 ? ' selected="selected"' : '' ).' value="0">'.lang("item", "poor").'</option>
                          <option'.( $search_quality == 1 ? ' selected="selected"' : '' ).' value="1">'.lang("item", "common").'</option>
                          <option'.( $search_quality == 2 ? ' selected="selected"' : '').' value="2">'.lang("item", "uncommon").'</option>
                          <option'.( $search_quality == 3 ? ' selected="selected"' : '' ).' value="3">'.lang("item", "rare").'</option>
                          <option'.( $search_quality == 4 ? ' selected="selected"' : '' ).' value="4">'.lang("item", "epic").'</option>
                          <option'.( $search_quality == 5 ? ' selected="selected"' : '' ).' value="5">'.lang("item", "legendary").'</option>
                          <option'.( $search_quality == 6 ? ' selected="selected"' : '' ).' value="6">'.lang("item", "artifact").'</option>
                        </select>
                      </td>
                      <td>';
  makebutton(lang("global", "search"), "javascript:do_submit()",80);
  $output .= '
                      </td>
                      <td>';
  ( ( ( $search_by && $search_value ) || ( $search_class != -1 ) || ( $search_quality != -1 ) ) ? makebutton(lang("global", "back"), "javascript:window.history.back()",80) : $output .= "" );
  $output .= '
                      </td>
                    </tr>
                  </table>
                </form>
              </td>
              <td width="25%" align="right">';
  $output .= generate_pagination("ahstats.php?order_by=".$order_by.( ( ( $search_by && $search_value ) || ( $search_class != -1 ) || ( $search_quality != -1 ) ) ? "&amp;search_by=".$search_by."&amp;search_value=".$search_value."&amp;search_quality=".$search_quality."&amp;search_class=".$search_class."&amp;error=2" : "" )."&amp;dir=".( ( $dir ) ? 0 : 1 ), $all_record, $itemperpage, $start);
  $output .= '
              </td>
            </tr>
          </table>
          <table class="lined">
            <tr>
              <th width="10%"><a href="ahstats.php?order_by=owner&amp;start='.$start.( ( ( $search_by && $search_value ) || ( $search_class != -1 ) || ( $search_quality != -1 ) ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'&amp;search_quality='.$search_quality.'&amp;search_class='.$search_class.'&amp;error=2' : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'owner' ) ? '<img src="img/arr_'.( ( $dir ) ? 'dw' : 'up' ).'.gif" alt="" /> ' : '' ).lang("auctionhouse", "seller").'</a></th>
              <th width="20%" colspan="2"><a href="ahstats.php?order_by=item&amp;start='.$start.( ( ( $search_by && $search_value ) || ( $search_class != -1 ) || ( $search_quality != -1 ) ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'&amp;search_quality='.$search_quality.'&amp;search_class='.$search_class.'&amp;error=2' : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'item' ) ? '<img src="img/arr_'.( ( $dir ) ? 'dw' : 'up' ).'.gif" alt="" /> ' : '' ).lang("auctionhouse", "item").'</a></th>
              <th width="15%"><a href="ahstats.php?order_by=buyout&amp;start='.$start.( ( ( $search_by && $search_value ) || ( $search_class != -1 ) || ( $search_quality != -1 ) ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'&amp;search_quality='.$search_quality.'&amp;search_class='.$search_class.'&amp;error=2' : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'buyout' ) ? '<img src="img/arr_'.( ( $dir ) ? 'dw' : 'up' ).'.gif" alt="" /> ' : '' ).lang("auctionhouse", "buyoutprice").'</a></th>
              <th width="15%"><a href="ahstats.php?order_by=time-unix_timestamp()&amp;start='.$start.( ( ( $search_by && $search_value ) || ( $search_class != -1 ) || ( $search_quality != -1 ) ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'&amp;search_quality='.$search_quality.'&amp;search_class='.$search_class.'&amp;error=2' : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'time' ) ? '<img src="img/arr_'.( ( $dir ) ? 'dw' : 'up' ).'.gif" alt="" /> ' : '' ).lang("auctionhouse", "timeleft").'</a></th>
              <th width="10%"><a href="ahstats.php?order_by=bidder&amp;start='.$start.( ( ( $search_by && $search_value ) || ( $search_class != -1 ) || ( $search_quality != -1 ) ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'&amp;search_quality='.$search_quality.'&amp;search_class='.$search_class.'&amp;error=2' : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'bidder' ) ? '<img src="img/arr_'.( ( $dir ) ? 'dw' : 'up' ).'.gif" alt="" /> ' : '' ).lang("auctionhouse", "buyer").'</a></th>
              <th width="15%"><a href="ahstats.php?order_by=bid&amp;start='.$start.( ( ( $search_by && $search_value ) || ( $search_class != -1 ) || ( $search_quality != -1 ) ) ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'&amp;search_quality='.$search_quality.'&amp;search_class='.$search_class.'&amp;error=2' : '' ).'&amp;dir='.$dir.'">'.( ( $order_by == 'bid' ) ? '<img src="img/arr_'.( ( $dir ) ? 'dw' : 'up' ).'.gif" alt="" /> ' : '' ).lang("auctionhouse", "lastbid").'</a></th>
            </tr>';
  
  while ( $rows = $sql["char"]->fetch_assoc($result) )
  {
    // get item info
    if ( $core == 1 )
      $i_query = "SELECT name1 AS name, quality AS Quality, inventorytype AS InventoryType, 
            socket_color_1 AS socketColor_1, socket_color_2 AS socketColor_2, socket_color_3 AS socketColor_3,
            requiredlevel AS RequiredLevel, allowableclass AS AllowableClass,
            sellprice AS SellPrice, itemlevel AS ItemLevel,
            creator, enchantments AS enchantment, randomprop AS property, count, durability, flags
            FROM items
              LEFT JOIN playeritems ON playeritems.guid=".$rows["item"]." "
              .( ( $locales_search_option != 0 ) ? "LEFT JOIN items_localized ON (items_localized.entry=items.entry AND language_code='".$locales_search_option."') " : " " ).
            "WHERE items.entry=".$rows["item_entry"];
    elseif ( $core == 2 )
      $i_query = "SELECT *, 
            SUBSTRING_INDEX(SUBSTRING_INDEX(`".$characters_db[$realm_id]["name"]."`.item_instance.data, ' ', 11), ' ', -1) AS creator,
            SUBSTRING_INDEX(SUBSTRING_INDEX(`".$characters_db[$realm_id]["name"]."`.item_instance.data, ' ', 23), ' ', -1) AS enchantment, 
            SUBSTRING_INDEX(SUBSTRING_INDEX(`".$characters_db[$realm_id]["name"]."`.item_instance.data, ' ', 60), ' ', -1) AS property, 
            SUBSTRING_INDEX(SUBSTRING_INDEX(`".$characters_db[$realm_id]["name"]."`.item_instance.data, ' ', 15), ' ', -1) AS count,
            SUBSTRING_INDEX(SUBSTRING_INDEX(`".$characters_db[$realm_id]["name"]."`.item_instance.data, ' ', 62), ' ', -1) AS durability,
            SUBSTRING_INDEX(SUBSTRING_INDEX(`".$characters_db[$realm_id]["name"]."`.item_instance.data, ' ', 22), ' ', -1) AS flags
            FROM item_template
              LEFT JOIN `".$characters_db[$realm_id]["name"]."`.character_inventory ON character_inventory.item=".$rows["item"]."
              LEFT JOIN `".$characters_db[$realm_id]["name"]."`.item_instance ON item_instance.guid=".$rows["item"]." "
              .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_item ON locales_item.entry=item_template.entry " : " " ).
            "WHERE item_template.entry=".$rows["item_entry"];
    else
      $i_query = "SELECT *, 
            creatorGuid AS creator,
            enchantments AS enchantment, 
            randomPropertyId AS property, 
            count, durability, `".$characters_db[$realm_id]["name"]."`.item_instance.flags AS flags
            FROM item_template
              LEFT JOIN `".$characters_db[$realm_id]["name"]."`.character_inventory ON character_inventory.item=".$rows["item"]."
              LEFT JOIN `".$characters_db[$realm_id]["name"]."`.item_instance ON item_instance.guid=".$rows["item"]." "
              .( ( $locales_search_option != 0 ) ? "LEFT JOIN locales_item ON locales_item.entry=item_template.entry " : " " ).
            "WHERE item_template.entry=".$rows["item_entry"];

    $i_result = $sql["world"]->query($i_query);
    $item_result = $sql["world"]->fetch_assoc($i_result);

    // Localization
    if ( $locales_search_option != 0 )
    {
      if ( $core == 1 )
        $item_result["name"] = $item_result["name"];
      else
        $item_result["name"] = $item_result["name_loc".$locales_search_option];
    }
    else
      $item_result["name"] = $item_result["name"];

    // calculate the buyout value
    $value = $rows["buyout"];
    $g = floor($value/10000);
    $value -= $g*10000;
    $s = floor($value/100);
    $value -= $s*100;
    $c = $value;
    $buyout = $g.'<img src="./img/gold.gif" alt="" /> '.$s.'<img src="./img/silver.gif" alt="" /> '.$c.'<img src="./img/copper.gif" alt="" /> ';
    
    // calculate the remaining time
    $tot_time = $rows["time"];
    $total_days = (int)($tot_time/86400);
    $tot_time = $tot_time - ($tot_days*86400);
    $total_hours = (int)($tot_time/3600);
    $tot_time = $tot_time - ($total_hours*3600);
    $total_min = (int)($tot_time/60);

    // get bidder name
    $bidder_result = $sql["char"]->result($sql["char"]->query("SELECT name FROM characters WHERE guid = '".$rows["bidder"]."'"), 0);

    // calculate the last bid value
    $value = $rows["bid"];
    $g = floor($value/10000);
    $value -= $g*10000;
    $s = floor($value/100);
    $value -= $s*100;
    $c = $value;
    $bid = $g.'<img src="./img/gold.gif" alt="" /> '.$s.'<img src="./img/silver.gif" alt="" /> '.$c.'<img src="./img/copper.gif" alt="" /> ';


    $output .= '
            <tr>
              <td>
                <center>
                  <a href="./char.php?id='.$rows["owner"].'">'.$rows["owner_name"].'</a>
                </center>
              </td>
              <td>';

    $item_icon = get_item_icon($rows["item_entry"]);
    $item_border = get_item_border($rows["item_entry"]);
    $output .= '
                <a href="'.$base_datasite.$item_datasite.$rows["item_entry"].'" target="_blank" onmouseover="ShowTooltip(this,\'_'.$rows["item"].'\');" onmouseout="HideTooltip(\'_'.$rows["item"].'\');">
                  <img src="'.$item_icon.'" class="'.$item_border.'" alt="" />
                </a>';

    $output .= '
                <div class="item_tooltip" id="tooltip_'.$rows["item"].'">
                  <table>
                    <tr>
                      <td>
                        '.get_item_tooltip($item_result, $item_result['enchantment'], $item_result['property'], $item_result['creator'], $item_result['durability'], $item_result['flags']).'
                      </td>
                    </tr>
                  </table>
                </div>';

    $output .= '
              </td>
              <td>
                <center>
                  <a href="'.$base_datasite.$item_datasite.$rows["item_entry"].'" target="_blank" onmouseover="ShowTooltip(this,\'_'.$rows["item"].'\');" onmouseout="HideTooltip(\'_'.$rows["item"].'\');" style="color:'.get_item_quality_color($item_result["Quality"]).'">'.$item_result["name"].'</a>
                </center>
              </td>
              <td>
                <center>
                  '.$buyout.'
                </center>
              </td>
              <td>
                <center>
                  '.( $total_days <> 0 ? $total_days.' days, ' : '' ).( $total_hours <> 0 ? $total_hours.' hours, ' : '' ).( $total_min<>0 ? $total_min.' minutes' : '' ).'
                </center>
              </td>
              <td>
                <center>
                  <a href="./char.php?id='.$rows["bidder"].'">'.$bidder_result.'</a>
                </center>
              </td>
              <td>
                <center>
                  '.( $bidder_result <> '' ? $bid : '' ).'
                </center>
              </td>
            </tr>
          </tr>';
  }
  $output .= '
            <tr>
              <td colspan="7" class="hidden" align="right" width="25%">';
  $output .= generate_pagination("ahstats.php?order_by=".$order_by.( ( ( $search_by && $search_value ) || ( $search_class != -1 ) || ( $search_quality != -1 ) ) ? "&amp;search_by=".$search_by."&amp;search_value=".$search_value."&amp;search_quality=".$search_quality."&amp;search_class=".$search_class."&amp;error=2" : "" )."&amp;dir=".( ( $dir ) ? 0 : 1 ), $all_record, $itemperpage, $start);
  $output .= '
              </td>
            </tr>
            <tr>
              <td colspan="7" class="hidden" align="right">'.lang("auctionhouse", "total_auctions").' : '.$all_record.'
              </td>
            </tr>
          </table>
        </center>';
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
          <h1><font class="error">'.lang("global", "empty_fields").'</font></h1>';
    break;
  case 2:
    $output .= '
          <h1><font class="error">'.lang("auctionhouse", "search_results").'</font></h1>';
    break;
 default:
    $output .= '
          <h1>'.lang("auctionhouse", "auctionhouse").'</h1>';
}

unset($err);

$output .= '
        </div>';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

switch ( $action )
{
  case "unknown":
    break;
  default:
    browse_auctions();
}

unset($action);
unset($action_permission);

require_once("footer.php");

?>
