<?php
/*
    CoreManager, PHP Front End for ArcEmu, MaNGOS, and TrinityCore
    Copyright (C) 2010  CoreManager Project

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
require_once 'libs/item_lib.php';
valid_login($action_permission["view"]);

//########################################################################################################################
// GUILD BANK
//########################################################################################################################
function guild_bank()
{
  global  $output, $realm_id, $characters_db, $arcm_db, $world_db, $item_datasite, $base_datasite,
    $item_icons, $sql;

  wowhead_tt();

  if ( empty($_GET["id"]) ) 
    error(lang("global", "empty_fields"));

  // this is multi realm support, as of writing still under development
  //  this page is already implementing it
  if ( empty($_GET["realm"]) ) 
    $realmid = $realm_id;
  else
  {
    $realmid = $sql["logon"]->quote_smart($_GET["realm"]);
    if ( is_numeric($realmid) )
      $sql["char"]->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name'], $characters_db[$realmid]['encoding']);
    else
      $realmid = $realm_id;
  }

  $guild_id = $sql["char"]->quote_smart($_GET["id"]);
  if ( is_numeric($guild_id) )
    ;
  else
    $guild_id = 0;

  if ( empty($_GET["tab"]) )
    $current_tab = 0;
  else
    $current_tab = $sql["char"]->quote_smart($_GET["tab"]);
  if ( is_numeric($current_tab) || ($current_tab > 6) )
    ;
  else
    $current_tab = 0;

  if ( $core == 1 )
    $result = $sql["char"]->query('SELECT guildName, bankBalance FROM guilds WHERE guildid = '.$guild_id.' LIMIT 1');
  else
    $result = $sql["char"]->query('SELECT name AS guildName, BankMoney AS bankBalance FROM guild WHERE guildid = '.$guild_id.' LIMIT 1');

  if( $sql["char"]->num_rows($result) )
  {
    $guild_name  = $sql["char"]->result($result, 0, 'guildName');
    $bank_gold   = $sql["char"]->result($result, 0, 'bankBalance');

    if ( $core == 1 )
      $result = $sql["char"]->query('SELECT TabId, TabName, TabIcon FROM guild_banktabs WHERE guildid = '.$guild_id.' LIMIT 6');
    else
      $result = $sql["char"]->query('SELECT TabId, TabName, TabIcon FROM guild_bank_tab WHERE guildid = '.$guild_id.' LIMIT 6');
    $tabs = array();
    while ( $tab = $sql["char"]->fetch_assoc($result) )
    {
      $tabs[$tab["TabId"]] = $tab;
    }
    $output .= '
          <div class="top">
            <h1>'.$guild_name.' '.lang("guildbank", "guildbank").'</h1>
          </div>
          <center>
            <div id="tab">
              <ul>';
    for( $i=0; $i<6; ++$i )
    {
      if ( isset($tabs[$i]) )
      {
        $output .= '
                <li'.(($current_tab == $i) ? ' id="selected"' : '').'>
                  <a href="guildbank.php?id='.$guild_id.'&amp;tab='.$i.'&amp;realm='.$realmid.'">';
        if ( $tabs[$i]['TabIcon'] == '' )
        {
          $output .= '
                    <img src="img/INV/INV_blank_32.gif" class="icon_border_0"';
        }
        else
        {
          // make sure we're looking for the file name with the correct capitalization
          $ii_query = "SELECT * FROM itemdisplayinfo WHERE LCASE(IconName)='".strtolower($tabs[$i]['TabIcon'])."' LIMIT 1";
          $ii_result = $sql["dbc"]->query($ii_query);
          $ii_fields = $sql["dbc"]->fetch_assoc($ii_result);
          $tabs[$i]['TabIcon'] = $ii_fields["IconName"];

          if ( file_exists(''.$item_icons.'/'.$tabs[$i]['TabIcon'].'.png') )
            $output .= '
                    <img src="'.$item_icons.'/'.$tabs[$i]['TabIcon'].'.png" class="icon_border_0"';
          else
            $output .= '
                    <img src="img/INV/INV_blank_32.gif" class="icon_border_0"';
        }
        if ( $tabs[$i]['TabName'] == '' )
          $output .= ' onmousemove="oldtoolTip(\''.lang("guildbank", "tab").($i+1).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />';
        else
          $output .= ' onmousemove="oldtoolTip(\''.$tabs[$i]['TabName'].'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />';
        $output .= '
                  </a>
                </li>';
      }
    }
    $output .= '
              </ul>
            </div>
            <div id="tab_content">';

    if ( $core == 1 )
      $result = $sql["char"]->query('SELECT gbi.SlotId, gbi.itemGuid, ii.entry,
        ii.count AS stack_count,
        FROM guild_bankitems gbi INNER JOIN playeritems ii on ii.guid = gbi.itemGuid
        WHERE gbi.guildid = '.$guild_id.' AND TabID = '.$current_tab.'');
    elseif ( $core == 2 )
      $result = $sql["char"]->query('SELECT gbi.SlotId, gbi.item_guid AS itemGuid, gbi.item_entry AS entry, 
        SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", 15), " ", -1) as stack_count 
        FROM guild_bank_item gbi INNER JOIN item_instance ii on ii.guid = gbi.item_guid 
        WHERE gbi.guildid = '.$guild_id.' AND TabID = '.$current_tab.'');
    else
      $result = $sql["char"]->query('SELECT gbi.SlotId, gbi.item_guid AS itemGuid, gbi.item_entry AS entry, 
        ii.count as stack_count 
        FROM guild_bank_item gbi INNER JOIN item_instance ii on ii.guid = gbi.item_guid 
        WHERE gbi.guildid = '.$guild_id.' AND TabID = '.$current_tab.'');
        
    $gb_slots = array();
    while ( $tab = $sql["char"]->fetch_assoc($result) )
      if ( $tab["itemGuid"] )
        $gb_slots[$tab["SlotId"]] = $tab;

    // this_is_junk: style left hardcoded because it's calculated.
    $output .= '
              <table id="guildbank_tabs">
                <tr>
                  <td class="bag" align="center">
                    <div style="width:'.((14*43)+2).'px;height:'.(7*41).'px;">';

    $item_position = 0;
    for ( $i=0; $i<7; ++$i )
    {
      for ( $j=0; $j<14; ++$j )
      {
        $item_position = $j*7+$i;
        if ( isset($gb_slots[$item_position]) )
        {
          $gb_item_id = $gb_slots[$item_position]['entry'];
          $stack = ( $gb_slots[$item_position]['stack_count'] == 1 ? '' : $gb_slots[$item_position]['stack_count'] );
          // this_is_junk: style left hardcoded because it's calculated.
          $output .= '
                      <div style="left:'.($j*43).'px;top:'.($i*41).'px;">
                        <a id="guildbank_padding" href="'.$base_datasite.$item_datasite.$gb_item_id.'">
                          <img src="'.get_item_icon($gb_item_id).'" alt="" />
                        </a>
                        <div id="guildbank_quanity_shadow">'.$stack.'</div>
                        <div id="guildbank_quantity">'.$stack.'</div>
                      </div>';
        }
      }
    }
    $output .= '
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="hidden" align="right">
                    '.substr($bank_gold,  0, -4).'<img src="img/gold.gif" alt="" align="middle" />
                    '.substr($bank_gold, -4,  2).'<img src="img/silver.gif" alt="" align="middle" />
                    '.substr($bank_gold, -2).'<img src="img/copper.gif" alt="" align="middle" />
                  </td>
                </tr>
              </table>
            </div>
            <br />
            <table class="hidden">
              <tr>
                <td>';
                    makebutton(lang("guildbank", "guild"), 'guild.php?action=view_guild&amp;realm='.$realmid.'&amp;error=3&amp;id='.$guild_id.'', 130);
    $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>';
    unset($bank_gold);
  }
  else
    redirect('error.php?err='.lang("guildbank", "notfound"));

}


//#############################################################################
// MAIN
//#############################################################################
//$err = (isset($_GET["error"])) ? $_GET["error"] : NULL;

//unset($err);

//$action = (isset($_GET["action"])) ? $_GET["action"] : NULL;

$output .= "
      <div class=\"bubble\">";

guild_bank();

//unset($action);
unset($action_permission);
//unset($lang_guildbank);

require_once 'footer.php';


?>
