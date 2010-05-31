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


//#############################################################################
// Language Pack handler
function lang($section, $key)
{
  global $lang;

  if ( isset( $GLOBALS['lang_'.$section][$key] ) )
    $lang_out = $GLOBALS['lang_'.$section][$key];
  else
  {
    lang_unpack();
    require("lang/english.php");
    $lang_out = $GLOBALS['lang_'.$section][$key];
    lang_unpack();
    require("lang/".$lang.".php");
  }
  return $lang_out;
}


//#############################################################################
// Clear Language Pack Data
function lang_unpack()
{
  unset($GLOBALS['lang_global']);
  unset($GLOBALS['lang_login']);
  unset($GLOBALS['lang_guild']);
  unset($GLOBALS['lang_guildbank']);
  unset($GLOBALS['lang_register']);
  unset($GLOBALS['lang_index']);
  unset($GLOBALS['lang_header']);
  unset($GLOBALS['lang_footer']);
  unset($GLOBALS['lang_repair']);
  unset($GLOBALS['lang_backup']);
  unset($GLOBALS['lang_banned']);
  unset($GLOBALS['lang_char']);
  unset($GLOBALS['lang_item']);
  unset($GLOBALS['lang_char_list']);
  unset($GLOBALS['lang_cleanup']);
  unset($GLOBALS['lang_edit']);
  unset($GLOBALS['lang_mail']);
  unset($GLOBALS['lang_motd']);
  unset($GLOBALS['lang_run_patch']);
  unset($GLOBALS['lang_ssh']);
  unset($GLOBALS['lang_realm']);
  unset($GLOBALS['lang_ticket']);
  unset($GLOBALS['lang_user']);
  unset($GLOBALS['lang_stat']);
  unset($GLOBALS['lang_tele']);
  unset($GLOBALS['lang_wbm']);
  unset($GLOBALS['lang_command']);
  unset($GLOBALS['lang_item_edit']);
  unset($GLOBALS['lang_creature']);
  unset($GLOBALS['lang_vendor']);
  unset($GLOBALS['lang_game_object']);
  unset($GLOBALS['lang_auctionhouse']);
  unset($GLOBALS['lang_id_tab']);
  unset($GLOBALS['lang_arenateam']);
  unset($GLOBALS['lang_honor']);
  unset($GLOBALS['lang_questitem']);
  unset($GLOBALS['lang_ultra']);
  unset($GLOBALS['lang_ultra_quality']);
  unset($GLOBALS['lang_events']);
  unset($GLOBALS['lang_instances']);
  unset($GLOBALS['lang_captcha']);
  unset($GLOBALS['lang_top']);
  unset($GLOBALS['lang_spelld']);
  unset($GLOBALS['lang_telnet']);
  unset($GLOBALS['lang_message']);
  unset($GLOBALS['lang_forum']);
  unset($GLOBALS['lang_xname']);
  unset($GLOBALS['lang_xrace']);
  unset($GLOBALS['lang_admin']);
  unset($GLOBALS['lang_admin_tip']);
  unset($GLOBALS['lang_setup']);
}


?>
