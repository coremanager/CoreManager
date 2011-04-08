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


require_once 'db_lib.php';

$sqlm = new SQL;
$sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

$temp = $sqlm->fetch_assoc($sqlm->query('SELECT * FROM config_dbc_database'));
$dbc_db["addr"]     = $temp["Address"].":".$temp["Port"];
$dbc_db["user"]     = $temp["User"];
$dbc_db["pass"]     = $temp["Password"];
$dbc_db["name"]     = $temp["Name"];
$dbc_db["encoding"] = $temp["Encoding"];

$temp = $sqlm->fetch_assoc($sqlm->query('SELECT * FROM config_logon_database'));
$logon_db["addr"]     = $temp["Address"].":".$temp["Port"];
$logon_db["user"]     = $temp["User"];
$logon_db["pass"]     = $temp["Password"];
$logon_db["name"]     = $temp["Name"];
$logon_db["encoding"] = $temp["Encoding"];

$temp = $sqlm->query('SELECT * FROM config_world_databases');
while ($world = $sqlm->fetch_assoc($temp))
{
  $world_db[$world["Index"]]['id']            = $world["Index"];
  $world_db[$world["Index"]]['addr']          = $world["Address"].":".$world["Port"];
  $world_db[$world["Index"]]['user']          = $world["User"];
  $world_db[$world["Index"]]['pass']          = $world["Password"];
  $world_db[$world["Index"]]['name']          = $world["Name"];
  $world_db[$world["Index"]]['encoding']      = $world["Encoding"];
}

$temp = $sqlm->query('SELECT * FROM config_character_databases');
while ($char = $sqlm->fetch_assoc($temp))
{
  $characters_db[$char["Index"]]['id']       = $char["Index"];
  $characters_db[$char["Index"]]['addr']     = $char["Address"].":".$char["Port"];
  $characters_db[$char["Index"]]['user']     = $char["User"];
  $characters_db[$char["Index"]]['pass']     = $char["Password"];
  $characters_db[$char["Index"]]['name']     = $char["Name"];
  $characters_db[$char["Index"]]['encoding'] = $char["Encoding"];
}

$temp = $sqlm->query('SELECT * FROM config_servers');
while ($servers = $sqlm->fetch_assoc($temp))
{
  $server[$servers["Index"]]['id']            = $servers["Index"];
  $server[$servers["Index"]]['addr']          = $servers["Address"];
  $server[$servers["Index"]]['game_port']     = $servers["Port"];
  $server[$servers["Index"]]['telnet_port']   = $servers["Telnet_Port"];
  $server[$servers["Index"]]['telnet_user']   = $servers["Telnet_User"];
  $server[$servers["Index"]]['telnet_pass']   = $servers["Telnet_Pass"];
  $server[$servers["Index"]]['both_factions'] = $servers["Both_Factions"];
  $server[$servers["Index"]]['stats.xml']     = $servers["Stats_XML"];
}

$temp = $sqlm->query('SELECT * FROM config_valid_ip_mask');
while ($mask = $sqlm->fetch_assoc($temp))
{
  $valid_ip_mask[$mask["Index"]] = $mask["ValidIPMask"];
}

$temp = $sqlm->query('SELECT * FROM config_gm_level_names');
while ($levels = $sqlm->fetch_assoc($temp))
{
  $gm_level_arr[$levels["Security_Level"]][0] = $levels["Full_Name"];
  $gm_level_arr[$levels["Security_Level"]][1] = $levels["Short_Name"];
}

$menu_array = array();
$temp = $sqlm->query('SELECT * FROM config_top_menus');
while ($tmenus = $sqlm->fetch_assoc($temp))
{
  $top = array();
  $top[0] = $tmenus["Action"];
  $top[1] = $tmenus["Name"];

  $m = array();
  $temp_menus = $sqlm->query("SELECT * FROM config_menus WHERE Menu = '".$tmenus["Index"]."' ORDER BY `Order`");
  while ($menus = $sqlm->fetch_assoc($temp_menus))
  {
    if ($menus["Enabled"])
    {
      $menu = array();
      array_push($menu,$menus["Action"]);
      array_push($menu,$menus["Name"]);
      array_push($menu,$menus["View"]);
      array_push($menu,$menus["Insert"]);
      array_push($menu,$menus["Update"]);
      array_push($menu,$menus["Delete"]);
      array_push($m, $menu);
    }
  }

  $top[2] = $m;

  array_push($menu_array, $top);
}

$misc = $sqlm->query("SELECT * FROM config_misc");
while ( $misc_row = $sqlm->fetch_assoc($misc) )
{
  switch ( $misc_row["Key"] )
  {
    case "Show_Version_Show":
    {
      $show_version["show"] = $misc_row["Value"];
      break;
    }
    case "Show_Version_Version":
    {
      $show_version["version"] = $misc_row["Value"];
      break;
    }
    case "Show_Version_Version_Lvl":
    {
      $show_version["version_lvl"] = $misc_row["Value"];
      break;
    }
    case "Show_Version_SVNRev":
    {
      $show_version["svnrev"] = $misc_row["Value"];
      break;
    }
    case "Show_Version_SVNRev_Lvl":
    {
      $show_version["svnrev_lvl"] = $misc_row["Value"];
      break;
    }
    case "SQL_Search_Limit":
    {
      $sql_search_limit = $misc_row["Value"];
      break;
    }
    case "Mail_Admin_Email":
    {
      $admin_mail = $misc_row["Value"];
      break;
    }
    case "Mail_Mailer_Type":
    {
      $mailer_type = $misc_row["Value"];
      break;
    }
    case "Mail_From_Email":
    {
      $from_mail = $misc_row["Value"];
      break;
    }
    case "Mail_GMailSender":
    {
      $GMailSender = $misc_row["Value"];
      break;
    }
    case "PM_From_Char":
    {
      $from_char = $misc_row["Value"];
      break;
    }
    case "Show_Version_Show":
    {
      $stationary = $misc_row["Value"];
      break;
    }
    case "SMTP_Host":
    {
      $smtp_cfg["host"] = $misc_row["Value"];
      break;
    }
    case "SMTP_Port":
    {
      $smtp_cfg["port"] = $misc_row["Value"];
      break;
    }
    case "SMTP_User":
    {
      $smtp_cfg["user"] = $misc_row["Value"];
      break;
    }
    case "SMTP_Pass":
    {
      $smtp_cfg["pass"] = $misc_row["Value"];
      break;
    }
    case "IRC_Server":
    {
      $irc_cfg["server"] = $misc_row["Value"];
      break;
    }
    case "IRC_Port":
    {
      $irc_cfg["port"] = $misc_row["Value"];
      break;
    }
    case "IRC_Channel":
    {
      $irc_cfg["channel"] = $misc_row["Value"];
      break;
    }
    case "IRC_HelpPage":
    {
      $irc_cfg["helppage"] = $misc_row["Value"];
      break;
    }
    case "Proxy_Addr":
    {
      $proxy_cfg["addr"] = $misc_row["Value"];
      break;
    }
    case "Proxy_Port":
    {
      $proxy_cfg["port"] = $misc_row["Value"];
      break;
    }
    case "Proxy_User":
    {
      $proxy_cfg["user"] = $misc_row["Value"];
      break;
    }
    case "Proxy_Pass":
    {
      $proxy_cfg["pass"] = $misc_row["Value"];
      break;
    }
    case "Datasite_Base":
    {
      $base_datasite = $misc_row["Value"];
      break;
    }
    case "Datasite_Name":
    {
      $name_datasite = $misc_row["Value"];
      break;
    }
    case "Datasite_Item":
    {
      $item_datasite = $misc_row["Value"];
      break;
    }
    case "Datasite_Quest":
    {
      $quest_datasite = $misc_row["Value"];
      break;
    }
    case "Datasite_Creature":
    {
      $creature_datasite = $misc_row["Value"];
      break;
    }
    case "Datasite_Spell":
    {
      $spell_datasite = $misc_row["Value"];
      break;
    }
    case "Datasite_Skill":
    {
      $skill_datasite = $misc_row["Value"];
      break;
    }
    case "Datasite_GO":
    {
      $go_datasite = $misc_row["Value"];
      break;
    }
    case "Datasite_Achievement":
    {
      $achievement_datasite = $misc_row["Value"];
      break;
    }
    case "Item_Icons":
    {
      $item_icons = $misc_row["Value"];
      break;
    }
    case "Disable_Acc_Creation":
    {
      $disable_acc_creation = $misc_row["Value"];
      break;
    }
    case "Expansion_Select":
    {
      $expansion_select = $misc_row["Value"];
      break;
    }
    case "Default_Expansion":
    {
      $defaultoption = $misc_row["Value"];
      break;
    }
    case "Enabled_Captcha":
    {
      $enable_captcha = $misc_row["Value"];
      break;
    }
    case "Use_Recaptcha":
    {
      $use_recaptcha = $misc_row["Value"];
      break;
    }
    case "Recaptcha_Public_Key":
    {
      $recaptcha_public_key = $misc_row["Value"];
      break;
    }
    case "Recaptcha_Private_Key":
    {
      $recaptcha_private_key = $misc_row["Value"];
      break;
    }
    case "Send_Mail_On_Creation":
    {
      $send_mail_on_creation = $misc_row["Value"];
      break;
    }
    case "Send_Confirmation_Mail_On_Creation":
    {
      $send_confirmation_mail_on_creation = $misc_row["Value"];
      break;
    }
    case "Format_Mail_HTML":
    {
      $format_mail_html = $misc_row["Value"];
      break;
    }
    case "Validate_Mail_Host":
    {
      $validate_mail_host = $misc_row["Value"];
      break;
    }
    case "Limit_Acc_Per_IP":
    {
      $limit_acc_per_ip = $misc_row["Value"];
      break;
    }
    case "Remember_Me_Checked":
    {
      $remember_me_checked = $misc_row["Value"];
      break;
    }
    case "Allow_Anony":
    {
      $allow_anony = $misc_row["Value"];
      break;
    }
    case "Anony_Name":
    {
      $anony_uname = $misc_row["Value"];
      break;
    }
    case "Anony_Realm_ID":
    {
      $anony_realm_id = $misc_row["Value"];
      break;
    }
    case "Site_Title":
    {
      $title = $misc_row["Value"];
      break;
    }
    case "Item_Per_Page":
    {
      $itemperpage = $misc_row["Value"];
      break;
    }
    case "Show_Country_Flags":
    {
      $showcountryflag = $misc_row["Value"];
      break;
    }
    case "Default_Theme":
    {
      $theme = $misc_row["Value"];
      break;
    }
    case "Default_Language":
    {
      $language = $misc_row["Value"];
      break;
    }
    case "Timezone":
    {
      $timezone = $misc_row["Value"];
      break;
    }
    case "GM_Online":
    {
      $gm_online = $misc_row["Value"];
      break;
    }
    case "GM_Online_Count":
    {
      $gm_online_count = $misc_row["Value"];
      break;
    }
    case "Hide_Max_Players":
    {
      $hide_max_players = $misc_row["Value"];
      break;
    }
    case "Hide_Avg_Latency":
    {
      $hide_avg_latency = $misc_row["Value"];
      break;
    }
    case "Hide_Server_Mem":
    {
      $hide_server_mem = $misc_row["Value"];
      break;
    }
    case "Hide_Plr_Latency":
    {
      $hide_plr_latency = $misc_row["Value"];
      break;
    }
    case "Quest_Item_Vendor_Level_Mul":
    {
      $quest_item["levelMul"] = $misc_row["Value"];
      break;
    }
    case "Quest_Item_Vendor_Rew_Mul":
    {
      $quest_item["rewMul"] = $misc_row["Value"];
      break;
    }
    case "Ultra_Vendor_Mult_0":
    {
      $ultra_mult[0] = $misc_row["Value"];
      break;
    }
    case "Ultra_Vendor_Mult_1":
    {
      $ultra_mult[1] = $misc_row["Value"];
      break;
    }
    case "Ultra_Vendor_Mult_2":
    {
      $ultra_mult[2] = $misc_row["Value"];
      break;
    }
    case "Ultra_Vendor_Mult_3":
    {
      $ultra_mult[3] = $misc_row["Value"];
      break;
    }
    case "Ultra_Vendor_Mult_4":
    {
      $ultra_mult[4] = $misc_row["Value"];
      break;
    }
    case "Ultra_Vendor_Mult_5":
    {
      $ultra_mult[5] = $misc_row["Value"];
      break;
    }
    case "Ultra_Vendor_Mult_6":
    {
      $ultra_mult[6] = $misc_row["Value"];
      break;
    }
    case "Ultra_Vendor_Mult_7":
    {
      $ultra_mult[7] = $misc_row["Value"];
      break;
    }
    case "Ultra_Vendor_Base":
    {
      $ultra_base = $misc_row["Value"];
      break;
    }
    case "Map_GM_Show_Online_Only_GMOff":
    {
      $map_gm_show_online_only_gmoff = $misc_row["Value"];
      break;
    }
    case "Map_GM_Show_Online_Only_GMVisible":
    {
      $map_gm_show_online_only_gmvisible = $misc_row["Value"];
      break;
    }
    case "Map_GM_Add_Suffix":
    {
      $map_gm_add_suffix = $misc_row["Value"];
      break;
    }
    case "Map_Status_GM_Include_All":
    {
      $map_status_gm_include_all = $misc_row["Value"];
      break;
    }
    case "Map_Show_Status":
    {
      $map_show_status = $misc_row["Value"];
      break;
    }
    case "Map_Show_Timer":
    {
      $map_show_time = $misc_row["Value"];
      break;
    }
    case "Map_Timer":
    {
      $map_time = $misc_row["Value"];
      break;
    }
    case "Map_Show_Online":
    {
      $map_show_online = $misc_row["Value"];
      break;
    }
    case "Map_Time_To_Show_Uptime":
    {
      $map_time_to_show_uptime = $misc_row["Value"];
      break;
    }
    case "Map_Time_To_Show_MaxOnline":
    {
      $map_time_to_show_maxonline = $misc_row["Value"];
      break;
    }
    case "Map_Time_To_Show_GMOnline":
    {
      $map_time_to_show_gmonline = $misc_row["Value"];
      break;
    }
    case "Language_Locales_Search_Option":
    {
      $locales_search_option = $misc_row["Value"];
      break;
    }
    case "Language_Site_Encoding":
    {
      $site_encoding = $misc_row["Value"];
      break;
    }
    case "Backup_Dir":
    {
      $backup_dir = $misc_row["Value"];
      break;
    }
    case "Debug":
    {
      $debug = $misc_row["Value"];
      break;
    }
    case "Test_Mode":
    {
      $developer_test_mode = $misc_row["Value"];
      break;
    }
    case "Multi_Realm":
    {
      $multi_realm_mode = $misc_row["Value"];
      break;
    }
    case "Enable_Page_Bottom_Ad":
    {
      $page_bottom_ad = $misc_row["Value"];
      break;
    }
    case "Page_Bottom_Ad_Content":
    {
      $page_bottom_ad_content = $misc_row["Value"];
      break;
    }
    case "Show_Guild_Emblem":
    {
      $show_guild_emblem = $misc_row["Value"];
      break;
    }
    case "Show_Newest_User":
    {
      $show_newest_user = $misc_row["Value"];
      break;
    }
    case "Send_Mail_On_Email_Change":
    {
      $send_mail_on_email_change = $misc_row["Value"];
      break;
    }
    case "Use_Custom_Logo":
    {
      $use_custom_logo = $misc_row["Value"];
      break;
    }
    case "Custom_Logo":
    {
      $custom_logo = $misc_row["Value"];
      break;
    }
    case "Invitation_Only":
    {
      $invite_only = $misc_row["Value"];
      break;
    }
    case "Disable_Invitation":
    {
      $disable_reg_invite = $misc_row["Value"];
      break;
    }
    case "Allow_Logo_Caching":
    {
      $allow_caching = $misc_row["Value"];
      break;
    }
  }
}

?>
