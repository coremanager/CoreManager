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


require_once 'db_lib.php';

$sqlm = new SQL;
$sqlm->connect($arcm_db['addr'], $arcm_db['user'], $arcm_db['pass'], $arcm_db['name']);

$show_version['show']        = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Show_Version_Show"'), 0, "Value");
$show_version['version']     = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Show_Version_Version"'), 0, "Value");
$show_version['version_lvl'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Show_Version_Version_Lvl"'), 0, "Value");
$show_version['svnrev']      = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Show_Version_SVNRev"'), 0, "Value");
$show_version['svnrev_lvl']  = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Show_Version_SVNRev_Lvl"'), 0, "Value");

$sql_search_limit = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "SQL_Search_Limit"'), 0, "Value");

$temp = $sqlm->fetch_assoc($sqlm->query('SELECT * FROM config_dbc_database'));
$dbc_db['addr']     = $temp["Address"].":".$temp["Port"];
$dbc_db['user']     = $temp["User"];
$dbc_db['pass']     = $temp["Password"];
$dbc_db['name']     = $temp["Name"];
$dbc_db['encoding'] = $temp["Encoding"];

$temp = $sqlm->fetch_assoc($sqlm->query('SELECT * FROM config_logon_database'));
$logon_db['addr']     = $temp["Address"].":".$temp["Port"];
$logon_db['user']     = $temp["User"];
$logon_db['pass']     = $temp["Password"];
$logon_db['name']     = $temp["Name"];
$logon_db['encoding'] = $temp["Encoding"];

$temp = $sqlm->query('SELECT * FROM config_world_databases');
while ($world = $sqlm->fetch_assoc($temp))
{
  $world_db[$world['Index']]['id']            = $world["Index"];
  $world_db[$world['Index']]['addr']          = $world["Address"].":".$world["Port"];
  $world_db[$world['Index']]['user']          = $world["User"];
  $world_db[$world['Index']]['pass']          = $world["Password"];
  $world_db[$world['Index']]['name']          = $world["Name"];
  $world_db[$world['Index']]['encoding']      = $world["Encoding"];
}

$temp = $sqlm->query('SELECT * FROM config_character_databases');
while ($char = $sqlm->fetch_assoc($temp))
{
  $characters_db[$char['Index']]['id']       = $char["Index"];
  $characters_db[$char['Index']]['addr']     = $char["Address"].":".$char["Port"];
  $characters_db[$char['Index']]['user']     = $char["User"];
  $characters_db[$char['Index']]['pass']     = $char["Password"];
  $characters_db[$char['Index']]['name']     = $char["Name"];
  $characters_db[$char['Index']]['encoding'] = $char["Encoding"];
}

$temp = $sqlm->query('SELECT * FROM config_servers');
while ($servers = $sqlm->fetch_assoc($temp))
{
  $server[$servers['Index']]['id']            = $servers["Index"];
  $server[$servers['Index']]['addr']          = $servers["Address"];
  $server[$servers['Index']]['game_port']     = $servers["Port"];
  $server[$servers['Index']]['both_factions'] = $servers["Both_Factions"];
  $server[$servers['Index']]['stats.xml']     = $servers["Stats_XML"];
}

$admin_mail  = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Mail_Admin_Email"'), 0, "Value");
$mailer_type = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Mail_Mailer_Type"'), 0, "Value");
$from_mail   = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Mail_From_Email"'), 0, "Value");
$GMailSender = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Mail_GMailSender"'), 0, "Value");

$from_char   = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "PM_From_Char"'), 0, "Value");
$stationary  = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Show_Version_Show"'), 0, "Value");

$smtp_cfg['host'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "SMTP_Host"'), 0, "Value");
$smtp_cfg['port'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "SMTP_Port"'), 0, "Value");
$smtp_cfg['user'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "SMTP_User"'), 0, "Value");
$smtp_cfg['pass'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "SMTP_Pass"'), 0, "Value");

$irc_cfg['server']  = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "IRC_Server"'), 0, "Value");
$irc_cfg['port']    = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "IRC_Port"'), 0, "Value");
$irc_cfg['channel'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "IRC_Channel"'), 0, "Value");
$irc_cfg['helppage'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "IRC_HelpPage"'), 0, "Value");

$proxy_cfg['addr'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Proxy_Addr"'), 0, "Value");
$proxy_cfg['port'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Proxy_Port"'), 0, "Value");
$proxy_cfg['user'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Proxy_User"'), 0, "Value");
$proxy_cfg['pass'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Proxy_Pass"'), 0, "Value");

$item_datasite        = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Datasite_Item"'), 0, "Value");
$quest_datasite       = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Datasite_Quest"'), 0, "Value");
$creature_datasite    = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Datasite_Creature"'), 0, "Value");
$spell_datasite       = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Datasite_Spell"'), 0, "Value");
$skill_datasite       = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Datasite_Skill"'), 0, "Value");
$go_datasite          = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Datasite_GO"'), 0, "Value");
$achievement_datasite = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Datasite_Achievement"'), 0, "Value");

$item_icons           = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Item_Icons"'), 0, "Value");

$disable_acc_creation  = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Disable_Acc_Creation"'), 0, "Value");
$expansion_select      = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Expansion_Select"'), 0, "Value");
$defaultoption         = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Default_Expansion"'), 0, "Value");
$enable_captcha        = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Enabled_Captcha"'), 0, "Value");
$use_recaptcha         = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Use_Recaptcha"'), 0, "Value");
$recaptcha_public_key  = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Recaptcha_Public_Key"'), 0, "Value");
$recaptcha_private_key = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Recaptcha_Private_Key"'), 0, "Value");

$send_mail_on_creation = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Send_Mail_On_Creation"'), 0, "Value");
$format_mail_html      = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Format_Mail_HTML"'), 0, "Value");
$validate_mail_host    = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Validate_Mail_Host"'), 0, "Value");
$limit_acc_per_ip      = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Limit_Acc_Per_IP"'), 0, "Value");

$temp = $sqlm->query('SELECT * FROM config_valid_ip_mask');
while ($mask = $sqlm->fetch_assoc($temp))
{
  $valid_ip_mask[$mask['Index']] = $mask['ValidIPMask'];
}

$remember_me_checked  = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Remember_Me_Checked"'), 0, "Value");

$allow_anony         = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Allow_Anony"'), 0, "Value");
$anony_uname         = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Anony_Name"'), 0, "Value");
$anony_realm_id      = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Anony_Realm_ID"'), 0, "Value");

$title               = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Site_Title"'), 0, "Value");

$itemperpage         = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Item_Per_Page"'), 0, "Value");
$showcountryflag     = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Show_Country_Flags"'), 0, "Value");

$theme               = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Default_Theme"'), 0, "Value");
$language            = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Default_Language"'), 0, "Value");
$timezone            = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Timezone"'), 0, "Value");
$gm_online           = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "GM_Online"'), 0, "Value");
$gm_online_count     = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "GM_Online_Count"'), 0, "Value");

$hide_max_players    = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Hide_Max_Players"'), 0, "Value");
$hide_avg_latency    = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Hide_Avg_Latency"'), 0, "Value");
$hide_server_mem     = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Hide_Server_Mem"'), 0, "Value");
$hide_plr_latency    = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Hide_Plr_Latency"'), 0, "Value");

$quest_item['levelMul'] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Quest_Item_Vendor_Level_Mul"'), 0, "Value");
$quest_item['rewMul']   = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Quest_Item_Vendor_Rew_Mul"'), 0, "Value");

$ultra_mult[0] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Ultra_Vendor_Mult_0"'), 0, "Value");
$ultra_mult[1] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Ultra_Vendor_Mult_1"'), 0, "Value");
$ultra_mult[2] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Ultra_Vendor_Mult_2"'), 0, "Value");
$ultra_mult[3] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Ultra_Vendor_Mult_3"'), 0, "Value");
$ultra_mult[4] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Ultra_Vendor_Mult_4"'), 0, "Value");
$ultra_mult[5] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Ultra_Vendor_Mult_5"'), 0, "Value");
$ultra_mult[6] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Ultra_Vendor_Mult_6"'), 0, "Value");
$ultra_mult[7] = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Ultra_Vendor_Mult_7"'), 0, "Value");
$ultra_base    = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Ultra_Vendor_Base"'), 0, "Value");

$map_gm_show_online_only_gmoff     = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_GM_Show_Online_Only_GMOff"'), 0, "Value");
$map_gm_show_online_only_gmvisible = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_GM_Show_Online_Only_GMVisible"'), 0, "Value");
$map_gm_add_suffix                 = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_GM_Add_Suffix"'), 0, "Value");
$map_status_gm_include_all         = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_Status_GM_Include_All"'), 0, "Value");

$map_show_status = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_Show_Status"'), 0, "Value");
$map_show_time   = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_Show_Timer"'), 0, "Value");
$map_time        = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_Timer"'), 0, "Value");

$map_show_online = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_Show_Online"'), 0, "Value");

$map_time_to_show_uptime    = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_Time_To_Show_Uptime"'), 0, "Value");
$map_time_to_show_maxonline = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_Time_To_Show_MaxOnline"'), 0, "Value");
$map_time_to_show_gmonline  = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Map_Time_To_Show_GMOnline"'), 0, "Value");

$locales_search_option = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Language_Locales_Search_Option"'), 0, "Value");
$site_encoding         = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Language_Site_Encoding"'), 0, "Value");

$backup_dir = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Backup_Dir"'), 0, "Value");

$temp = $sqlm->query('SELECT * FROM config_gm_level_names');
while ($levels = $sqlm->fetch_assoc($temp))
{
  $gm_level_arr[$levels['Security_Level']][0] = $levels['Full_Name'];
  $gm_level_arr[$levels['Security_Level']][1] = $levels['Short_Name'];
}

$menu_array = array();
$temp = $sqlm->query('SELECT * FROM config_top_menus');
while ($tmenus = $sqlm->fetch_assoc($temp))
{
  $top = array();
  $top[0] = $tmenus['Action'];
  $top[1] = $tmenus['Name'];

  $m = array();
  $temp_menus = $sqlm->query("SELECT * FROM config_menus WHERE Menu = '".$tmenus['Index']."'");
  while ($menus = $sqlm->fetch_assoc($temp_menus))
  {
    if ($menus['Enabled'])
    {
      $menu = array();
      array_push($menu,$menus['Action']);
      array_push($menu,$menus['Name']);
      array_push($menu,$menus['View']);
      array_push($menu,$menus['Insert']);
      array_push($menu,$menus['Update']);
      array_push($menu,$menus['Delete']);
      array_push($m, $menu);
    }
  }

  $top[2] = $m;

  array_push($menu_array, $top);
}

$debug = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Debug"'), 0, "Value");

$developer_test_mode = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Test_Mode"'), 0, "Value");
$multi_realm_mode    = $sqlm->result($sqlm->query('SELECT * FROM config_misc WHERE `Key` = "Multi_Realm"'), 0, "Value");

?>
