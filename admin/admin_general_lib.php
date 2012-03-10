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

function general()
{
  global $output, $corem_db, $core;

  // we need $core to be set
  if ( $core == 0 )
    $core = detectcore();

  $sqlm = new SQL;
  $sqlm->connect($corem_db["addr"], $corem_db["user"], $corem_db["pass"], $corem_db["name"], $corem_db["encoding"]);

  $subsection = ( ( isset($_GET["subsection"]) ) ? $sqlm->quote_smart($_GET["subsection"]) : 1 );

  $output .= '
        <table id="sidebar">
          <tr>
            <td '.( ( $subsection == "more" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=more">'.lang("admin", "more").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "version" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=version">'.lang("admin", "version").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "mail" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=mail">'.lang("admin", "mail").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "irc" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=irc">'.lang("admin", "irc").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "proxy" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=proxy">'.lang("admin", "proxy").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "datasite" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=datasite">'.lang("admin", "datasite").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "acctcreation" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=acctcreation">'.lang("admin", "acct_creation").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "guests" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=guests">'.lang("admin", "guests").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "extratools" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=extratools">'.lang("admin", "extra_tools").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "internalmap" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=internalmap">'.lang("admin", "internal_map").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "validip" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=validip">'.lang("admin", "validip").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "ads" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=ads">'.lang("admin", "ads").'</a>
            </td>
          </tr>
          <tr>
            <td '.( ( $subsection == "gmlevels" ) ? 'class="current"' : '' ).'>
              <a href="admin.php?section=general&amp;subsection=gmlevels">'.lang("admin", "gm_levels").'</a>
            </td>
          </tr>
        </table>';

  if ( isset($_GET["error"]) )
    $output .= '
      <div id="misc_error">';
  else
    $output .= '
      <div id="misc">';

  $sub_action = ( ( isset($_GET["subaction"]) ) ? $_GET["subaction"] : '' );

  switch ( $subsection )
  {
    case "version":
    {
      if ( !$sub_action )
      {
        $show_version_show = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Version_Show'"));
        $show_version_version = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Version_Version'"));
        $show_version_version_lvl = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Version_Version_Lvl'"));
        $show_version_revision = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Version_SVNRev'"));
        $show_version_revision_lvl = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Version_SVNRev_Lvl'"));
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="saveversion" />
          <input type="hidden" name="subsection" value="version" />
          <table class="simple">
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "show_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "show").'</a>:
              </td>
              <td>
                <select name="showversion" id="admin_showversion_field">
                  <option value="0" '.( ( $show_version_show["Value"] == 0 ) ? 'selected="selected"' : '' ).'>'.lang("admin", "dontshow").'</option>
                  <option value="1" '.( ( $show_version_show["Value"] == 1 ) ? 'selected="selected"' : '' ).'disabled="disabled">'.lang("admin", "version").'</option>
                  <option value="2"'.( ( $show_version_show["Value"] == 2 ) ? 'selected="selected"' : '' ).'>'.lang("admin", "verrev").'</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "version_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "version").'</a>:
              </td>
              <td>
                <input type="text" name="version" value="'.$show_version_version["Value"].'" readonly="readonly" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "versionlvl_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "versionlvl").'</a>:
              </td>
              <td>
                <input type="text" name="versionlvl" value="'.$show_version_version_lvl["Value"].'" readonly="readonly" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "revision_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "revision").'</a>:
              </td>
              <td>
                <input type="text" name="revision" value="'.$show_version_revision["Value"].'" readonly="readonly" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "revisionlvl_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "revisionlvl").'</a>:
              </td>
              <td>
                <input type="text" name="revisionlvl" value="'.$show_version_revision_lvl["Value"].'" />
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        $show_version = $sqlm->quote_smart($_GET["showversion"]);
        $version = $sqlm->quote_smart($_GET["version"]);
        $version_lvl = $sqlm->quote_smart($_GET["versionlvl"]);
        $revision = $sqlm->quote_smart($_GET["revision"]);
        $revision_lvl = $sqlm->quote_smart($_GET["revisionlvl"]);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$show_version."' WHERE `Key`='Show_Version_Show'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$version."' WHERE `Key`='Show_Version_Version'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$version_lvl."' WHERE `Key`='Show_Version_Version_Lvl'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$revision."' WHERE `Key`='Show_Version_SVNRev'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$revision_lvl."' WHERE `Key`='Show_Version_SVNRev_Lvl'");

        redirect("admin.php?section=general&subsection=version");
      }
      break;
    }
    case "mail":
    {
      if ( !$sub_action )
      {
        $mail_admin_email = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Mail_Admin_Email'"));
        $mail_mailer_type = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Mail_Mailer_Type'"));
        $mail_from_email = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Mail_From_Email'"));
        $mail_gmailsender = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Mail_GMailSender'"));
        $format_mail_html = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Format_Mail_HTML'"));
        $smtp_host = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='SMTP_Host'"));
        $smtp_port = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='SMTP_Port'"));
        $smtp_user = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='SMTP_User'"));
        $smtp_pass = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='SMTP_Pass'"));
        $pm_from_char = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='PM_From_Char'"));
        $pm_stationary = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='PM_Stationary'"));
        $url_path = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='URL_Path'"));
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="savemail" />
          <input type="hidden" name="subsection" value="mail" />
          <table class="simple">
            <tr>
              <td colspan="2"><b>'.lang("admin", "email").'</b></td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "adminemail_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "adminemail").'</a>:
              </td>
              <td>
                <input type="text" name="adminemail" value="'.$mail_admin_email["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "mailertype_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "mailertype").'</a>:
              </td>
              <td>
                <select name="mailertype" id="admin_mailertype_field">
                  <option value="mail" '.( ( $mail_mailer_type["Value"] == "mail" ) ? 'selected="selected" ' : '' ).'>'.lang("admin", "mail").'</option>
                  <option value="sendmail" '.( ( $mail_mailer_type["Value"] == "sendmail" ) ? 'selected="selected" ' : '' ).'>'.lang("admin", "sendmail").'</option>
                  <option value="smtp"'.( ( $mail_mailer_type["Value"] == "smtp" ) ? 'selected="selected" ' : '' ).'>'.lang("admin", "smtp").'</option>
                  <option value="gmailsmtp"'.( ( $mail_gmailsender["Value"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("admin", "gmailsmtp").'</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "fromemail_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "fromemail").'</a>:
              </td>
              <td>
                <input type="text" name="fromemail" value="'.$mail_from_email["Value"].'" />
              </td>
            </tr>
            <!-- tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "usegmail_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "usegmail").'</a>:
              </td>
              <td>
                <input type="checkbox" name="gmail" '.( ( $mail_gmailsender["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr -->
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "formathtml_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "formathtml").'</a>:
              </td>
              <td>
                <input type="checkbox" name="usehtml" '.( ( $format_mail_html["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "urlpath_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "urlpath").'</a>:
              </td>
              <td>
                <input type="text" name="urlpath" value="'.$url_path["Value"].'" />
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <b>'.lang("admin", "smtp").'</b>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "smtphost_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "smtphost").'</a>:
              </td>
              <td>
                <input type="text" name="smtphost" value="'.$smtp_host["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "smtpport_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "smtpport").'</a>:
              </td>
              <td>
                <input type="text" name="smtpport" value="'.$smtp_port["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "smtpuser_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "smtpuser").'</a>:
              </td>
              <td>
                <input type="text" name="smtpuser" value="'.$smtp_user["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "smtppass_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "smtppass").'</a>:
              </td>
              <td>
                <input type="text" name="smtppass" value="'.$smtp_pass["Value"].'" />
              </td>
            </tr>';

        if ( $core == 1 )
          $output .= '
            <tr>
              <td colspan="2">
                <b>'.lang("admin", "pm").'</b>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "pmfrom_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "pmfrom").'</a>:
              </td>
              <td>
                <input type="text" name="fromchar" value="'.$pm_from_char["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "pmstation_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "pmstation").'</a>:
              </td>
              <td>
                <input type="text" name="stationary" value="'.$pm_stationary["Value"].'" />
              </td>
            </tr>';

        $output .= '
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        $mail_admin_email = $sqlm->quote_smart($_GET["adminemail"]);
        $mail_mailer_type = $sqlm->quote_smart($_GET["mailertype"]);
        $mail_from_email = $sqlm->quote_smart($_GET["fromemail"]);

        if ( $mail_mailer_type == "gmailsmtp" )
          $mail_gmailsender = 1;
        else
          $mail_gmailsender = 0;

        $format_mail_html = ( ( isset($_GET["usehtml"]) ) ? 1 : 0 );
        $smtp_host = $sqlm->quote_smart($_GET["smtphost"]);
        $smtp_port = $sqlm->quote_smart($_GET["smtpport"]);
        $smtp_user = $sqlm->quote_smart($_GET["smtpuser"]);
        $smtp_pass = $sqlm->quote_smart($_GET["smtppass"]);
        $pm_from_char = ( ( isset($_GET["fromchar"]) ) ? $sqlm->quote_smart($_GET["fromchar"]) : 1 );
        $pm_stationary = ( ( isset($_GET["stationary"]) ) ? $sqlm->quote_smart($_GET["stationary"]) : 41 );
        $url_path = $sqlm->quote_smart($_GET["urlpath"]);

        // clean up $url_path in case user can't read
        if ( ( $url_path != "" ) && ( $url_path[0] != "/" ) )
          $url_path = "/".$url_path;

        if ( $url_path == "/" )
          $url_path = "";

        $result = $sqlm->query("UPDATE config_misc SET Value='".$mail_admin_email."' WHERE `Key`='Mail_Admin_Email'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$mail_mailer_type."' WHERE `Key`='Mail_Mailer_Type'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$mail_from_email."' WHERE `Key`='Mail_From_Email'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$mail_gmailsender."' WHERE `Key`='Mail_GMailSender'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$format_mail_html."' WHERE `Key`='Format_Mail_HTML'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$smtp_host."' WHERE `Key`='SMTP_Host'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$smtp_port."' WHERE `Key`='SMTP_Port'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$smtp_user."' WHERE `Key`='SMTP_User'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$smtp_pass."' WHERE `Key`='SMTP_Pass'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$pm_from_char."' WHERE `Key`='PM_From_Char'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$pm_stationary."' WHERE `Key`='PM_Stationary'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$url_path."' WHERE `Key`='URL_Path'");

        redirect("admin.php?section=general&subsection=mail");
      }
      break;
    }
    case "irc":
    {
      if ( !$sub_action )
      {
        $irc_host = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='IRC_Server'"));
        $irc_port = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='IRC_Port'"));
        $irc_channel = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='IRC_Channel'"));
        $irc_helppage = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='IRC_HelpPage'"));
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="saveirc" />
          <input type="hidden" name="subsection" value="irc" />
          <table class="simple">
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "irchost_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "irchost").'</a>:
              </td>
              <td>
                <input type="text" name="irchost" value="'.$irc_host["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ircport_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ircport").'</a>:
              </td>
              <td>
                <input type="text" name="ircport" value="'.$irc_port["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ircchannel_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ircchannel").'</a>:
              </td>
              <td>
                <input type="text" name="ircchannel" value="'.$irc_channel["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "irchelppage_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "irchelppage").'</a>:
              </td>
              <td>
                <input type="text" name="irchelppage" value="'.$irc_helppage["Value"].'" readonly="readonly" />
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        $irc_host = $sqlm->quote_smart($_GET["irchost"]);
        $irc_port = $sqlm->quote_smart($_GET["ircport"]);
        $irc_channel = $sqlm->quote_smart($_GET["ircchannel"]);
        $irc_helppage = $sqlm->quote_smart($_GET["irchelppage"]);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$irc_host."' WHERE `Key`='IRC_Server'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$irc_port."' WHERE `Key`='IRC_Port'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$irc_channel."' WHERE `Key`='IRC_Channel'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$irc_helppage."' WHERE `Key`='IRC_HelpPage'");

        redirect("admin.php?section=general&subsection=irc");
      }
      break;
    }
    case "proxy":
    {
      if ( !$sub_action )
      {
        $proxy_host = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Proxy_Addr'"));
        $proxy_port = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Proxy_Port'"));
        $proxy_user = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Proxy_User'"));
        $proxy_pass = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Proxy_Pass'"));
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="saveproxy" />
          <input type="hidden" name="subsection" value="proxy" />
          <table class="simple">
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "proxyhost_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "proxyhost").'</a>:
              </td>
              <td>
                <input type="text" name="proxyhost" value="'.$proxy_host["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "proxyport_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "proxyport").'</a>:
              </td>
              <td>
                <input type="text" name="proxyport" value="'.$proxy_port["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "proxyuser_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "proxyuser").'</a>:
              </td>
              <td>
                <input type="text" name="proxyuser" value="'.$proxy_user["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "proxypass_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "proxypass").'</a>:
              </td>
              <td>
                <input type="text" name="proxypass" value="'.$proxy_pass["Value"].'" />
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        $proxy_host = $sqlm->quote_smart($_GET["proxyhost"]);
        $proxy_port = $sqlm->quote_smart($_GET["proxyport"]);
        $proxy_user = $sqlm->quote_smart($_GET["proxyuser"]);
        $proxy_pass = $sqlm->quote_smart($_GET["proxypass"]);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$proxy_host."' WHERE `Key`='Proxy_Addr'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$proxy_port."' WHERE `Key`='Proxy_Port'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$proxy_user."' WHERE `Key`='Proxy_User'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$proxy_pass."' WHERE `Key`='Proxy_Pass'");

        redirect("admin.php?section=general&subsection=proxy");
      }
      break;
    }
    case "datasite":
    {
      if ( !$sub_action )
      {
        $datasite_base = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Base'"));
        $datasite_name = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Name'"));
        $datasite_item = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Item'"));
        $datasite_quest = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Quest'"));
        $datasite_creature = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Creature'"));
        $datasite_spell = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Spell'"));
        $datasite_skill = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Skill'"));
        $datasite_go = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_GO'"));
        $datasite_achieve = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Achievement'"));
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="savedatasite" />
          <input type="hidden" name="subsection" value="datasite" />
          <table class="simple" id="admin_datasite">
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "datasitebase_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "datasitebase").'</a>:
              </td>
              <td>
                <input type="text" name="datasitebase" value="'.$datasite_base["Value"].'" size="50" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "datasitename_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "datasitename").'</a>:
              </td>
              <td>
                <input type="text" name="datasitename" value="'.$datasite_name["Value"].'" size="50" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "datasiteitem_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "datasiteitem").'</a>:
              </td>
              <td>
                <input type="text" name="datasiteitem" value="'.$datasite_item["Value"].'" size="50" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "datasitequest_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "datasitequest").'</a>:
              </td>
              <td>
                <input type="text" name="datasitequest" value="'.$datasite_quest["Value"].'" size="50" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "datasitecreature_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "datasitecreature").'</a>:
              </td>
              <td>
                <input type="text" name="datasitecreature" value="'.$datasite_creature["Value"].'" size="50" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "datasitespell_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "datasitespell").'</a>:
              </td>
              <td>
                <input type="text" name="datasitespell" value="'.$datasite_spell["Value"].'" size="50" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "datasiteskill_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "datasiteskill").'</a>:
              </td>
              <td>
                <input type="text" name="datasiteskill" value="'.$datasite_skill["Value"].'" size="50" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "datasitego_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "datasitego").'</a>:
              </td>
              <td>
                <input type="text" name="datasitego" value="'.$datasite_go["Value"].'" size="50" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "datasiteachieve_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "datasiteachieve").'</a>:
              </td>
              <td>
                <input type="text" name="datasiteachieve" value="'.$datasite_achieve["Value"].'" size="50" />
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        $datasite_base = $sqlm->quote_smart($_GET["datasitebase"]);
        $datasite_name = $sqlm->quote_smart($_GET["datasitename"]);
        $datasite_item = $sqlm->quote_smart($_GET["datasiteitem"]);
        $datasite_quest = $sqlm->quote_smart($_GET["datasitequest"]);
        $datasite_creature = $sqlm->quote_smart($_GET["datasitecreature"]);
        $datasite_spell = $sqlm->quote_smart($_GET["datasitespell"]);
        $datasite_skill = $sqlm->quote_smart($_GET["datasiteskill"]);
        $datasite_go = $sqlm->quote_smart($_GET["datasitego"]);
        $datasite_achieve = $sqlm->quote_smart($_GET["datasiteachieve"]);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$datasite_base."' WHERE `Key`='Datasite_Base'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$datasite_name."' WHERE `Key`='Datasite_Name'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$datasite_item."' WHERE `Key`='Datasite_Item'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$datasite_quest."' WHERE `Key`='Datasite_Quest'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$datasite_creature."' WHERE `Key`='Datasite_Creature'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$datasite_spell."' WHERE `Key`='Datasite_Spell'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$datasite_skill."' WHERE `Key`='Datasite_Skill'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$datasite_go."' WHERE `Key`='Datasite_GO'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$datasite_achieve."' WHERE `Key`='Datasite_Achievement'");

        redirect("admin.php?section=general&subsection=datasite");
      }
      break;
    }
    case "acctcreation":
    {
      if ( !$sub_action )
      {
        $disable_acc_creation = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Disable_Acc_Creation'"));
        $invite_only = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Invitation_Only'"));
        $disable_reg_invite = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Disable_Invitation'"));
        $expansion_select = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Expansion_Select'"));
        $default_expansion = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Default_Expansion'"));
        $enabled_captcha = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Enabled_Captcha'"));
        $using_recaptcha = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Use_Recaptcha'"));
        $publickey = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Recaptcha_Public_Key'"));
        $privatekey = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Recaptcha_Private_Key'"));
        $send_mail_on_creation = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Send_Mail_On_Creation'"));
        $send_confirmation_mail_on_creation = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Send_Confirmation_Mail_On_Creation'"));
        $validate_mail_host = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Validate_Mail_Host'"));
        $limit_acc_per_ip = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Limit_Acc_Per_IP'"));
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="saveacctcreation" />
          <input type="hidden" name="subsection" value="acctcreation" />
          <table class="simple" id="admin_acct_creation">
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "disableacccreation_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "disableacccreation").'</a>:
              </td>
              <td>
                <input type="checkbox" name="disableacccreation" '.( ( $disable_acc_creation["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "inviteonly_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "inviteonly").'</a>:
              </td>
              <td>
                <input type="checkbox" name="inviteonly" '.( ( $invite_only["Value"] == 1 ) ? 'checked="checked"' : '' ).' '.( ( $disable_acc_creation["Value"] == 1 ) ? '' : 'disabled="disabled"' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "disablereginvite_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "disablereginvite").'</a>:
              </td>
              <td>
                <input type="checkbox" name="disablereginvite" '.( ( $disable_reg_invite["Value"] == 1 ) ? 'checked="checked"' : '' ).' '.( ( $invite_only["Value"] == 0 ) ? '' : 'disabled="disabled"' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "expansionselect_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "expansionselect").'</a>:
              </td>
              <td>
                <input type="checkbox" name="expansionselect" '.( ( $expansion_select["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "defaultexpansion_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "defaultexpansion").'</a>:
              </td>
              <td>
                <select name="defaultexpansion">';
        if ( $core == 1 )
          $output .= '
                  <option value="0" '.( ( $default_expansion["Value"] == 0 ) ? 'selected="selected"' : '' ).'>Classic</option>
                  <option value="8" '.( ( $default_expansion["Value"] == 8 ) ? 'selected="selected"' : '' ).'>BC</option>
                  <option value="16" '.( ( $default_expansion["Value"] == 16 ) ? 'selected="selected"' : '' ).'>WotLK</option>
                  <option value="24" '.( ( $default_expansion["Value"] == 24 ) ? 'selected="selected"' : '' ).'>WotLK+BC</option>';
        else
          $output .= '
                  <option value="0" '.( ( $default_expansion["Value"] == 0 ) ? 'selected="selected"' : '' ).'>Classic</option>
                  <option value="1" '.( ( $default_expansion["Value"] == 1 ) ? 'selected="selected"' : '' ).'>BC</option>
                  <option value="2" '.( ( $default_expansion["Value"] == 2 ) ? 'selected="selected"' : '' ).'>WotLK+BC</option>';
        $output .= '
                </select>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "enabledcaptcha_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "enabledcaptcha").'</a>:
              </td>
              <td>
                <input type="checkbox" name="enabledcaptcha" '.( ( $enabled_captcha["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "userecaptcha_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "userecaptcha").'</a>:
              </td>
              <td>
                <input type="checkbox" name="userecaptcha" '.( ( $using_recaptcha["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "publickey_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "publickey").'</a>:
              </td>
              <td>
                <input type="text" name="publickey" value="'.$publickey["Value"].'" size="52" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "privatekey_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "privatekey").'</a>:
              </td>
              <td>
                <input type="text" name="privatekey" value="'.$privatekey["Value"].'" size="52" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "sendmailoncreation_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "sendmailoncreation").'</a>:
              </td>
              <td>
                <input type="checkbox" name="sendmailoncreation" '.( ( $send_mail_on_creation["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "sendconfirmmailoncreation_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "sendconfirmmailoncreation").'</a>:
              </td>
              <td>
                <input type="checkbox" name="sendconfirmmailoncreation" '.( ( $send_confirmation_mail_on_creation["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "validatemailhost_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "validatemailhost").'</a>:
              </td>
              <td>
                <input type="checkbox" name="validatemailhost" '.( ( $validate_mail_host["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "limitaccperip_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "limitaccperip").'</a>:
              </td>
              <td>
                <input type="checkbox" name="limitaccperip" '.( ( $limit_acc_per_ip["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        $disable_acc_creation = ( ( isset($_GET["disableacccreation"]) ) ? 1 : 0 );
        $invite_only = ( ( isset($_GET["inviteonly"]) ) ? 1 : 0 );
        $disable_reg_invite = ( ( isset($_GET["disablereginvite"]) ) ? 1 : 0 );
        $expansion_select = ( ( isset($_GET["expansionselect"]) ) ? 1 : 0 );
        $default_expansion = $sqlm->quote_smart($_GET["defaultexpansion"]);
        $enabled_captcha = ( ( isset($_GET["enabledcaptcha"]) ) ? 1 : 0 );
        $using_recaptcha = ( ( isset($_GET["userecaptcha"]) ) ? 1 : 0 );
        $publickey = $sqlm->quote_smart($_GET["publickey"]);
        $privatekey = $sqlm->quote_smart($_GET["privatekey"]);
        $send_mail_on_creation = ( ( isset($_GET["sendmailoncreation"]) ) ? 1 : 0 );
        $send_confirmation_mail_on_creation = ( ( isset($_GET["sendconfirmmailoncreation"]) ) ? 1 : 0 );
        $validate_mail_host = ( ( isset($_GET["validatemailhost"]) ) ? 1 : 0 );
        $limit_acc_per_ip = ( ( isset($_GET["limitaccperip"]) ) ? 1 : 0 );

        $result = $sqlm->query("UPDATE config_misc SET Value='".$disable_acc_creation."' WHERE `Key`='Disable_Acc_Creation'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$invite_only."' WHERE `Key`='Invitation_Only'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$disable_reg_invite."' WHERE `Key`='Disable_Invitation'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$expansion_select."' WHERE `Key`='Expansion_Select'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$default_expansion."' WHERE `Key`='Default_Expansion'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$enabled_captcha."' WHERE `Key`='Enabled_Captcha'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$using_recaptcha."' WHERE `Key`='Use_Recaptcha'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$publickey."' WHERE `Key`='Recaptcha_Public_Key'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$privatekey."' WHERE `Key`='Recaptcha_Private_Key'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$send_mail_on_creation."' WHERE `Key`='Send_Mail_On_Creation'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$send_confirmation_mail_on_creation."' WHERE `Key`='Send_Confirmation_Mail_On_Creation'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$validate_mail_host."' WHERE `Key`='Validate_Mail_Host'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$limit_acc_per_ip."' WHERE `Key`='Limit_Acc_Per_IP'");

        redirect("admin.php?section=general&subsection=acctcreation");
      }
      break;
    }
    case "guests":
    {
      if ( !$sub_action )
      {
        $acp_allow_anony = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Allow_Anony'"));
        $acp_anony_name = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Anony_Name'"));
        $acp_anony_realm_id = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Anony_Realm_ID'"));
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="saveguests" />
          <input type="hidden" name="subsection" value="guests" />
          <table class="simple">
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "allowanony_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "allowanony").'</a>:
              </td>
              <td>
                <input type="checkbox" name="allowanony" '.( ( $acp_allow_anony["Value"] == 1 ) ? 'checked="checked"' : '' ).' disabled="disabled" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "anonyname_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "anonyname").'</a>:
              </td>
              <td>
                <input type="text" name="anonyname" value="'.$acp_anony_name["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "anonyrealmid_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "anonyrealmid").'</a>:
              </td>
              <td>
                <input type="text" name="anonyrealmid" value="'.$acp_anony_realm_id["Value"].'" />
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        // Allow Anonymous is disabled but must stay checked
        $acp_allow_anony = 1;
        $acp_anony_name = $sqlm->quote_smart($_GET["anonyname"]);
        $acp_anony_realm_id = $sqlm->quote_smart($_GET["anonyrealmid"]);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$acp_allow_anony."' WHERE `Key`='Allow_Anony'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$acp_anony_name."' WHERE `Key`='Anony_Name'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$acp_anony_realm_id."' WHERE `Key`='Anony_Realm_ID'");

        redirect("admin.php?section=general&subsection=guests");
      }
      break;
    }
    case "extratools":
    {
      if ( !$sub_action )
      {
        $quest_item_vendor_level_mul = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Quest_Item_Vendor_Level_Mul'"));
        $quest_item_vendor_rew_mul = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Quest_Item_Vendor_Rew_Mul'"));
        $ultra_vendor_mult_0 = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Ultra_Vendor_Mult_0'"));
        $ultra_vendor_mult_1 = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Ultra_Vendor_Mult_1'"));
        $ultra_vendor_mult_2 = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Ultra_Vendor_Mult_2'"));
        $ultra_vendor_mult_3 = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Ultra_Vendor_Mult_3'"));
        $ultra_vendor_mult_4 = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Ultra_Vendor_Mult_4'"));
        $ultra_vendor_mult_5 = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Ultra_Vendor_Mult_5'"));
        $ultra_vendor_mult_6 = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Ultra_Vendor_Mult_6'"));
        $ultra_vendor_mult_7 = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Ultra_Vendor_Mult_7'"));
        $ultra_vendor_base = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Ultra_Vendor_Base'"));
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="saveextratools" />
          <input type="hidden" name="subsection" value="extratools" />
          <table class="simple">
            <tr>
              <td colspan="2">
                <b>'.lang("admin", "questitemvendor").'</b>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "questitemvendorlevelmul_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "questitemvendorlevelmul").'</a>:
              </td>
              <td>
                <input type="text" name="questitemvendorlevelmul" value="'.$quest_item_vendor_level_mul["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "questitemvendorrewmul_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "questitemvendorrewmul").'</a>:
              </td>
              <td>
                <input type="text" name="questitemvendorrewmul" value="'.$quest_item_vendor_rew_mul["Value"].'" />
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <b>'.lang("admin", "ultravendor").'</b>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ultravendormult0_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ultravendormult0").'</a>:
              </td>
              <td>
                <input type="text" name="ultravendormult0" value="'.$ultra_vendor_mult_0["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ultravendormult1_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ultravendormult1").'</a>:
              </td>
              <td>
                <input type="text" name="ultravendormult1" value="'.$ultra_vendor_mult_1["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ultravendormult2_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ultravendormult2").'</a>:
              </td>
              <td>
                <input type="text" name="ultravendormult2" value="'.$ultra_vendor_mult_2["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ultravendormult3_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ultravendormult3").'</a>:
              </td>
              <td>
                <input type="text" name="ultravendormult3" value="'.$ultra_vendor_mult_3["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ultravendormult4_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ultravendormult4").'</a>:
              </td>
              <td>
                <input type="text" name="ultravendormult4" value="'.$ultra_vendor_mult_4["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ultravendormult5_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ultravendormult5").'</a>:
              </td>
              <td>
                <input type="text" name="ultravendormult5" value="'.$ultra_vendor_mult_5["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ultravendormult6_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ultravendormult6").'</a>:
              </td>
              <td>
                <input type="text" name="ultravendormult6" value="'.$ultra_vendor_mult_6["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ultravendormult7_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ultravendormult7").'</a>:
              </td>
              <td>
                <input type="text" name="ultravendormult7" value="'.$ultra_vendor_mult_7["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "ultravendorbase_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "ultravendorbase").'</a>:
              </td>
              <td>
                <input type="text" name="ultravendorbase" value="'.$ultra_vendor_base["Value"].'" />
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        $quest_item_vendor_level_mul = $sqlm->quote_smart($_GET["questitemvendorlevelmul"]);
        $quest_item_vendor_rew_mul = $sqlm->quote_smart($_GET["questitemvendorrewmul"]);
        $ultra_vendor_mult_0 = $sqlm->quote_smart($_GET["ultravendormult0"]);
        $ultra_vendor_mult_1 = $sqlm->quote_smart($_GET["ultravendormult1"]);
        $ultra_vendor_mult_2 = $sqlm->quote_smart($_GET["ultravendormult2"]);
        $ultra_vendor_mult_3 = $sqlm->quote_smart($_GET["ultravendormult3"]);
        $ultra_vendor_mult_4 = $sqlm->quote_smart($_GET["ultravendormult4"]);
        $ultra_vendor_mult_5 = $sqlm->quote_smart($_GET["ultravendormult5"]);
        $ultra_vendor_mult_6 = $sqlm->quote_smart($_GET["ultravendormult6"]);
        $ultra_vendor_mult_7 = $sqlm->quote_smart($_GET["ultravendormult7"]);
        $ultra_vendor_base = $sqlm->quote_smart($_GET["ultravendorbase"]);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$quest_item_vendor_level_mul."' WHERE `Key`='Quest_Item_Vendor_Level_Mul'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$quest_item_vendor_rew_mul."' WHERE `Key`='Quest_Item_Vendor_Rew_Mul'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$ultra_vendor_mult_0."' WHERE `Key`='Ultra_Vendor_Mult_0'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$ultra_vendor_mult_1."' WHERE `Key`='Ultra_Vendor_Mult_1'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$ultra_vendor_mult_2."' WHERE `Key`='Ultra_Vendor_Mult_2'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$ultra_vendor_mult_3."' WHERE `Key`='Ultra_Vendor_Mult_3'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$ultra_vendor_mult_4."' WHERE `Key`='Ultra_Vendor_Mult_4'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$ultra_vendor_mult_5."' WHERE `Key`='Ultra_Vendor_Mult_5'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$ultra_vendor_mult_6."' WHERE `Key`='Ultra_Vendor_Mult_6'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$ultra_vendor_mult_7."' WHERE `Key`='Ultra_Vendor_Mult_7'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$ultra_vendor_base."' WHERE `Key`='Ultra_Vendor_Base'");

        redirect("admin.php?section=general&subsection=extratools");
      }
      break;
    }
    case "internalmap":
    {
      if ( !$sub_action )
      {
        $map_gm_show_online_only_gmoff = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_GM_Show_Online_Only_GMOff'"));
        $map_gm_show_online_only_gmvisible = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_GM_Show_Online_Only_GMVisible'"));
        $map_gm_add_suffix = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_GM_Add_Suffix'"));
        $map_status_gm_include_all = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_Status_GM_Include_All'"));
        $map_show_status = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_Show_Status'"));
        $map_show_timer = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_Show_Timer'"));
        $map_timer = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_Timer'"));
        $map_show_online = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_Show_Online'"));
        $map_time_to_show_uptime = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_Time_To_Show_Uptime'"));
        $map_time_to_show_maxonline = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_Time_To_Show_MaxOnline'"));
        $map_time_to_show_gmonline = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Map_Time_To_Show_GMOnline'"));
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="saveinternalmap" />
          <input type="hidden" name="subsection" value="internalmap" />
          <table class="simple">
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "statusgmincludeall_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "statusgmincludeall").'</a>:
              </td>
              <td>
                <input type="checkbox" name="statusgmincludeall" '.( ( $map_status_gm_include_all["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <!-- tr>
              <td class="help"><a href="#" onmouseover="oldtoolTip(\''.lang("admin", "gmshowonlineonlygmoff_tip").'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "gmshowonlineonlygmoff").'</a>: </td>
              <td><input type="checkbox" name="gmshowonlineonlygmoff" '.( ( $map_gm_show_online_only_gmoff["Value"] == 1 ) ? 'checked="checked"' : '' ).' /></td>
            </tr>
            <tr>
              <td class="help"><a href="#" onmouseover="oldtoolTip(\''.lang("admin", "gmshowonlineonlygmvisible_tip").'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "gmshowonlineonlygmvisible").'</a>: </td>
              <td><input type="checkbox" name="gmshowonlineonlygmvisible" '.( ( $map_gm_show_online_only_gmvisible["Value"] == 1 ) ? 'checked="checked"' : '' ).' disabled="disabled" /></td>
            </tr -->
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "gmaddsuffix_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "gmaddsuffix").'</a>:
              </td>
              <td>
                <input type="checkbox" name="gmaddsuffix" '.( ( $map_gm_add_suffix["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <!-- tr>
              <td class="help"><a href="#" onmouseover="oldtoolTip(\''.lang("admin", "showstatus_tip").'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "showstatus").'</a>: </td>
              <td><input type="checkbox" name="showstatus" '.( ( $map_show_status["Value"] == 1 ) ? 'checked="checked"' : '' ).' disabled="disabled" /></td>
            </tr>
            <tr>
              <td class="help"><a href="#" onmouseover="oldtoolTip(\''.lang("admin", "showtimer_tip").'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "showtimer").'</a>: </td>
              <td><input type="checkbox" name="showtimer" '.( ( $map_show_timer["Value"] == 1 ) ? 'checked="checked"' : '' ).' disabled="disabled" /></td>
            </tr>
            <tr>
              <td class="help"><a href="#" onmouseover="oldtoolTip(\''.lang("admin", "timer_tip").'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "timer").'</a>: </td>
              <td><input type="text" name="timer" value="'.$map_timer["Value"].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td class="help"><a href="#" onmouseover="oldtoolTip(\''.lang("admin", "showonline_tip").'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "showonline").'</a>: </td>
              <td><input type="checkbox" name="showonline" '.( ( $map_show_online["Value"] == 1 ) ? 'checked="checked"' : '' ).' disabled="disabled" /></td>
            </tr>
            <tr>
              <td class="help"><a href="#" onmouseover="oldtoolTip(\''.lang("admin", "timetoshowuptime_tip").'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "timetoshowuptime").'</a>: </td>
              <td><input type="text" name="timetoshowuptime" value="'.$map_time_to_show_uptime["Value"].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td class="help"><a href="#" onmouseover="oldtoolTip(\''.lang("admin", "timetoshowmaxonline_tip").'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "timetoshowmaxonline").'</a>: </td>
              <td><input type="text" name="timetoshowmaxonline" value="'.$map_time_to_show_maxonline["Value"].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td class="help"><a href="#" onmouseover="oldtoolTip(\''.lang("admin", "timetoshowgmonline_tip").'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "timetoshowgmonline").'</a>: </td>
              <td><input type="text" name="timetoshowgmonline" value="'.$map_time_to_show_gmonline["Value"].'" readonly="readonly" /></td>
            </tr -->
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        $map_gm_show_online_only_gmoff = ( ( isset($_GET["gmshowonlineonlygmoff"]) ) ? 1 : 0 );
        $map_gm_show_online_only_gmvisible = ( ( isset($_GET["gmshowonlineonlygmvisible"]) ) ? 1 : 0 );
        $map_gm_add_suffix = ( ( isset($_GET["gmaddsuffix"]) ) ? 1 : 0 );
        $map_status_gm_include_all = ( ( isset($_GET["statusgmincludeall"]) ) ? 1 : 0 );
        $map_show_status = ( ( isset($_GET["showstatus"]) ) ? 1 : 0 );
        $map_show_timer = ( ( isset($_GET["showtimer"]) ) ? 1 : 0 );
        $map_timer = $sqlm->quote_smart($_GET["timer"]);
        $map_show_online = ( ( isset($_GET["showonline"]) ) ? 1 : 0 );
        $map_time_to_show_uptime = $sqlm->quote_smart($_GET["timetoshowuptime"]);
        $map_time_to_show_maxonline = $sqlm->quote_smart($_GET["timetoshowmaxonline"]);
        $map_time_to_show_gmonline = $sqlm->quote_smart($_GET["timetoshowgmonline"]);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_gm_show_online_only_gmoff."' WHERE `Key`='Map_GM_Show_Online_Only_GMOff'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_gm_show_online_only_gmvisible."' WHERE `Key`='Map_GM_Show_Online_Only_GMVisible'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_gm_add_suffix."' WHERE `Key`='Map_GM_Add_Suffix'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_status_gm_include_all."' WHERE `Key`='Map_Status_GM_Include_All'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_show_status."' WHERE `Key`='Map_Show_Status'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_gm_add_suffix."' WHERE `Key`='Map_Show_Timer'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_timer."' WHERE `Key`='Map_Timer'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_show_online."' WHERE `Key`='Map_Show_Online'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_time_to_show_uptime."' WHERE `Key`='Map_Time_To_Show_Uptime'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_time_to_show_maxonline."' WHERE `Key`='Map_Time_To_Show_MaxOnline'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$map_time_to_show_gmonline."' WHERE `Key`='Map_Time_To_Show_GMOnline'");

        redirect("admin.php?section=general&subsection=internalmap");
      }
      break;
    }
    case "validip":
    {
      if ( !$sub_action )
      {
        $masks_query = $sqlm->query("SELECT * FROM config_valid_ip_mask");
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="editvalidip" />
          <input type="hidden" name="subsection" value="validip" />
          <table class="simple">
            <tr>
              <th width="10%">&nbsp;</th>
              <th width="20%">
                <center>'.lang("admin", "index").'</center>
              </th>
              <th>'.lang("admin", "validipmask").'</th>
            </tr>';
        while ( $mask = $sqlm->fetch_assoc($masks_query) )
        {
          $output .= '
            <tr>
              <td>
                <input type="radio" name="index" value="'.$mask["Index"].'" />
              </td>
              <td>
                <center>'.$mask["Index"].'</center>
              </td>
              <td>'.$mask["ValidIPMask"].'</td>
            </tr>';
        }
        $output .= '
          </table>
          <input type="submit" name="edit" value="'.lang("admin", "editipmask").'" />
          <input type="submit" name="add" value="'.lang("admin", "addipmask").'" />
          <input type="submit" name="delete" value="'.lang("admin", "deleteipmask").'" />
        </form>';
      }
      elseif ( $sub_action == "editvalidip" )
      {
        if ( isset($_GET["add"]) )
        {
          $lim = $sqlm->fetch_assoc($sqlm->query("SELECT MAX(`Index`) FROM config_valid_ip_mask"));
          $lim = $lim["MAX(`Index`)"] + 1;
          $sqlm->query("INSERT INTO config_valid_ip_mask SET `Index`='".$lim."', ValidIPMask=''");
          redirect("admin.php?section=general&subsection=validip");
        }
        elseif ( isset($_GET["delete"]) )
        {
          $index = $sqlm->quote_smart($_GET["index"]);
          if ( !is_numeric($index) )
            redirect("admin.php?section=general&subsection=validip&error=1");

          $result = $sqlm->query("DELETE FROM config_valid_ip_mask WHERE `Index`='".$index."'");
          redirect("admin.php?section=general&subsection=validip");
        }
        else
        {
          $index = $sqlm->quote_smart($_GET["index"]);
          if ( !is_numeric($index) )
            redirect("admin.php?section=general&subsection=validip&error=1");

          $mask = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_valid_ip_mask WHERE `Index`='".$index."'"));
          $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="savevalidip" />
          <input type="hidden" name="subsection" value="validip" />
          <input type="hidden" name="index" value="'.$mask["Index"].'" />
          <table class="simple">
            <tr>
              <th width="20%">
                <center>'.lang("admin", "index").'</center>
              </th>
              <th class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "validipmask_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "validipmask").'</a>
              </th>
            </tr>
            <tr>
              <td>
                <center>'.$mask["Index"].'</center>
              </td>
              <td>
                <input type="text" name="mask" value="'.$mask["ValidIPMask"].'" />
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
        }
      }
      else
      {
        $index = $sqlm->quote_smart($_GET["index"]);
        $mask = $sqlm->quote_smart($_GET["mask"]);
        $result = $sqlm->query("UPDATE config_valid_ip_mask SET ValidIPMask='".$mask."' WHERE `Index`='".$index."'");

        redirect("admin.php?section=general&subsection=validip");
      }
      break;
    }
    case "ads":
    {
      if ( !$sub_action )
      {
        $enable_bottom_ad = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Enable_Page_Bottom_Ad'"));
        $bottom_ad_content = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Page_Bottom_Ad_Content'"));
        $output .= '
        <form name="form" action="admin.php" method="get">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="saveads" />
          <input type="hidden" name="subsection" value="ads" />
          <table class="simple" id="admin_more">
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "enablebottomad_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "enablebottomad").'</a>:
              </td>
              <td>
                <input type="checkbox" name="enablebottomad" '.( ( $enable_bottom_ad["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "bottomadcontent_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "bottomadcontent").'</a>:
              </td>
              <td>
                <textarea name="bottomadcontent" rows="5" cols="40">'.$bottom_ad_content["Value"].'</textarea>
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        $page_bottom_ad = ( ( isset($_GET["enablebottomad"]) ) ? 1 : 0 );
        $page_bottom_ad_content = $sqlm->quote_smart($_GET["bottomadcontent"]);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$page_bottom_ad."' WHERE `Key`='Enable_Page_Bottom_Ad'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$page_bottom_ad_content."' WHERE `Key`='Page_Bottom_Ad_Content'");

        redirect("admin.php?section=general&subsection=ads");
      }
      break;
    }
    case "more":
    {
      if ( !$sub_action )
      {
        $sql_search_limit = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='SQL_Search_Limit'"));
        $item_icons = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Item_Icons'"));
        $remember_me_checked = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Remember_Me_Checked'"));
        $site_title = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Site_Title'"));
        $item_per_page = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Item_Per_Page'"));
        $show_country_flags = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Country_Flags'"));
        $default_theme = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Default_Theme'"));
        $default_language = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Default_Language'"));
        $timezone = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Timezone'"));
        $timezone_offset = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Timezone_Offset'"));
        $player_online = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Player_Online'"));
        $gm_online = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='GM_Online'"));
        $gm_online_count = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='GM_Online_Count'"));
        $hide_uptime = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Hide_Uptime'"));
        $hide_max_players = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Hide_Max_Players'"));
        $hide_avg_latency = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Hide_Avg_Latency'"));
        $hide_server_mem = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Hide_Server_Mem'"));
        $hide_plr_latency = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Hide_Plr_Latency'"));
        $hide_coupons = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Hide_Coupons_Main_Page'"));
        $backup_dir = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Backup_Dir'"));
        $debug = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Debug'"));
        $test_mode = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Test_Mode'"));
        $multi_realm = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Multi_Realm'"));
        $show_emblem = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Guild_Emblem'"));
        $language_locales_search_option = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Language_Locales_Search_Option'"));
        $language_site_encoding = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Language_Site_Encoding'"));
        $show_newest_user = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Newest_User'"));
        $send_on_email = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Send_Mail_On_Email_Change'"));
        $use_custom_logo = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Use_Custom_Logo'"));
        $custom_logo = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Custom_Logo'"));
        $allow_caching = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Allow_Logo_Caching'"));
        $index_show_realms = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Index_Show_Realms'"));

        $custom_logos_result = $sqlm->query("SELECT * FROM custom_logos");
        $custom_logo_count = $sqlm->num_rows($custom_logos_result);
        $custom_logos = array();
        while ( $row = $sqlm->fetch_assoc($custom_logos_result) )
        {
          $custom_logos[] = $row;
        }

        $output .= '
        <form name="form" action="admin.php" method="get" enctype="multipart/form-data">
          <input type="hidden" name="section" value="general" />
          <input type="hidden" name="subaction" value="savemore" />
          <input type="hidden" name="subsection" value="more" />
          <table class="simple" id="admin_more">
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "sqlsearchlimit_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "sqlsearchlimit").'</a>:
              </td>
              <td>
                <input type="text" name="sqlsearchlimit" value="'.$sql_search_limit["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "itemicons_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "itemicons").'</a>:
              </td>
              <td>
                <input type="text" name="itemicons" value="'.$item_icons["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "remembermechecked_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "remembermechecked").'</a>:
              </td>
              <td>
                <input type="checkbox" name="remembermechecked" '.( ( $remember_me_checked["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "sitetitle_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "sitetitle").'</a>:
              </td>
              <td>
                <input type="text" name="sitetitle" value="'.$site_title["Value"].'" size="50"/>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "itemperpage_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "itemperpage").'</a>:
              </td>
              <td>
                <input type="text" name="itemperpage" value="'.$item_per_page["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "showcountryflags_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "showcountryflags").'</a>:
              </td>
              <td>
                <input type="checkbox" name="showcountryflags" '.( ( $show_country_flags["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "defaulttheme_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "defaulttheme").'</a>:
              </td>
              <td>
                <input type="text" name="defaulttheme" value="'.$default_theme["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "defaultlanguage_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "defaultlanguage").'</a>:
              </td>
              <td>
                <select name="defaultlanguage">';
        if ( is_dir("./lang") )
        {
          if ( $dh = opendir("./lang") )
          {
            while ( ( $file = readdir($dh) ) == true )
            {
              $lang_temp = explode(".", $file);
              if ( isset($lang_temp[1]) && ( $lang_temp[1] == "php" ) )
              {
                $output .= '
                      <option value="'.$lang_temp[0].'"'.( ( $default_language["Value"] == $lang_temp[0] ) ? ' selected="selected" ' : '' ).'>'.lang("edit", $lang_temp[0]).'</option>';
              }
            }
            closedir($dh);
          }
        }
        $output .= '
                </select>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "timezone_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "timezone").'</a>:
              </td>
              <td>
                <select name="timezone">
                  <option value="-12.0" '.( ( $timezone["Value"] == "-12.0" ) ? 'selected="selected"' : '' ).'>(UTC -12:00) Eniwetok, Kwajalein</option>
                  <option value="-11.0" '.( ( $timezone["Value"] == "-11.0" ) ? 'selected="selected"' : '' ).'>(UTC -11:00) Midway Island, Samoa</option>
                  <option value="-10.0" '.( ( $timezone["Value"] == "-10.0" ) ? 'selected="selected"' : '' ).'>(UTC -10:00) Hawaii</option>
                  <option value="-9.0" '.( ( $timezone["Value"] == "-9.0" ) ? 'selected="selected"' : '' ).'>(UTC -9:00) Alaska</option>
                  <option value="-8.0" '.( ( $timezone["Value"] == "-8.0" ) ? 'selected="selected"' : '' ).'>(UTC -8:00) Pacific Time (US &amp; Canada)</option>
                  <option value="-7.0" '.( ( $timezone["Value"] == "-7.0" ) ? 'selected="selected"' : '' ).'>(UTC -7:00) Mountain Time (US &amp; Canada)</option>
                  <option value="-6.0" '.( ( $timezone["Value"] == "-6.0" ) ? 'selected="selected"' : '' ).'>(UTC -6:00) Central Time (US &amp; Canada), Mexico City</option>
                  <option value="-5.0" '.( ( $timezone["Value"] == "-5.0" ) ? 'selected="selected"' : '' ).'>(UTC -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                  <option value="-4.5" '.( ( $timezone["Value"] == "-4.5" ) ? 'selected="selected"' : '' ).'>(UTC -4:30) Caracas</option>
                  <option value="-4.0" '.( ( $timezone["Value"] == "-4.0" ) ? 'selected="selected"' : '' ).'>(UTC -4:00) Atlantic Time (Canada), La Paz</option>
                  <option value="-3.5" '.( ( $timezone["Value"] == "-3.5" ) ? 'selected="selected"' : '' ).'>(UTC -3:30) Newfoundland</option>
                  <option value="-3.0" '.( ( $timezone["Value"] == "-3.0" ) ? 'selected="selected"' : '' ).'>(UTC -3:00) Brazil, Buenos Aires, Georgetown</option>
                  <option value="-2.0" '.( ( $timezone["Value"] == "-2.0" ) ? 'selected="selected"' : '' ).'>(UTC -2:00) Mid-Atlantic</option>
                  <option value="-1.0" '.( ( $timezone["Value"] == "-1.0" ) ? 'selected="selected"' : '' ).'>(UTC -1:00) Azores, Cape Verde Islands</option>
                  <option value="0.0" '.( ( $timezone["Value"] == "0.0" ) ? 'selected="selected"' : '' ).'>(UTC) Western Europe Time, London, Lisbon, Casablanca</option>
                  <option value="1.0" '.( ( $timezone["Value"] == "1.0" ) ? 'selected="selected"' : '' ).'>(UTC +1:00) Brussels, Copenhagen, Madrid, Paris</option>
                  <option value="2.0" '.( ( $timezone["Value"] == "2.0" ) ? 'selected="selected"' : '' ).'>(UTC +2:00) Kaliningrad, South Africa</option>
                  <option value="3.0" '.( ( $timezone["Value"] == "3.0" ) ? 'selected="selected"' : '' ).'>(UTC +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
                  <option value="3.5" '.( ( $timezone["Value"] == "3.5" ) ? 'selected="selected"' : '' ).'>(UTC +3:30) Tehran</option>
                  <option value="4.0" '.( ( $timezone["Value"] == "4.0" ) ? 'selected="selected"' : '' ).'>(UTC +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
                  <option value="4.5" '.( ( $timezone["Value"] == "4.5" ) ? 'selected="selected"' : '' ).'>(UTC +4:30) Kabul</option>
                  <option value="5.0" '.( ( $timezone["Value"] == "5.0" ) ? 'selected="selected"' : '' ).'>(UTC +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                  <option value="5.5" '.( ( $timezone["Value"] == "5.5" ) ? 'selected="selected"' : '' ).'>(UTC +5:30) Bombay, Calcutta, Madras, New Delhi</option>
                  <option value="5.75" '.( ( $timezone["Value"] == "5.75" ) ? 'selected="selected"' : '' ).'>(UTC +5:45) Kathmandu</option>
                  <option value="6.0" '.( ( $timezone["Value"] == "6.0" ) ? 'selected="selected"' : '' ).'>(UTC +6:00) Almaty, Dhaka, Colombo</option>
                  <option value="7.0" '.( ( $timezone["Value"] == "7.0" ) ? 'selected="selected"' : '' ).'>(UTC +7:00) Bangkok, Hanoi, Jakarta</option>
                  <option value="8.0" '.( ( $timezone["Value"] == "8.0" ) ? 'selected="selected"' : '' ).'>(UTC +8:00) Beijing, Perth, Singapore, Hong Kong</option>
                  <option value="9.0" '.( ( $timezone["Value"] == "9.0" ) ? 'selected="selected"' : '' ).'>(UTC +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                  <option value="9.5" '.( ( $timezone["Value"] == "9.5" ) ? 'selected="selected"' : '' ).'>(UTC +9:30) Adelaide, Darwin</option>
                  <option value="10.0" '.( ( $timezone["Value"] == "10.0" ) ? 'selected="selected"' : '' ).'>(UTC +10:00) Eastern Australia, Guam, Vladivostok</option>
                  <option value="11.0" '.( ( $timezone["Value"] == "11.0" ) ? 'selected="selected"' : '' ).'>(UTC +11:00) Magadan, Solomon Islands, New Caledonia</option>
                  <option value="12.0" '.( ( $timezone["Value"] == "12.0" ) ? 'selected="selected"' : '' ).'>(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "timezone_offset_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "timezone_offset").'</a>:
              </td>
              <td>
                <input type="text" name="timezone_offset" value="'.$timezone_offset["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "playeronline_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "playeronline").'</a>:
              </td>
              <td>
                <select name="playeronline">';

        $sl_query = "SELECT * FROM config_gm_level_names";
        $sl_result = $sqlm->query($sl_query);

        while ( $row = $sqlm->fetch_assoc($sl_result) )
        {
          $output .= '
                          <option value="'.$row["Security_Level"].'" '.( ( $player_online["Value"] == $row["Security_Level"] ) ? 'selected="selected"' : '' ).'>'.$row["Full_Name"].' ('.$row["Security_Level"].')</option>';
        }

        $output .= '
                </select>
                <!-- input type="checkbox" name="playeronline" '.( ( $player_online["Value"] == 1 ) ? 'checked="checked"' : '' ).' / -->
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "gmonline_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "gmonline").'</a>:
              </td>
              <td>
                <input type="checkbox" name="gmonline" '.( ( $gm_online["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "gmonlinecount_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "gmonlinecount").'</a>:
              </td>
              <td>
                <input type="checkbox" name="gmonlinecount" '.( ( $gm_online_count["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "hideuptime_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "hideuptime").'</a>:
              </td>
              <td>
                <input type="checkbox" name="hideuptime" '.( ( $hide_uptime["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "hidemaxplayers_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "hidemaxplayers").'</a>:
              </td>
              <td>
                <input type="checkbox" name="hidemaxplayers" '.( ( $hide_max_players["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "hideavglatency_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "hideavglatency").'</a>:
              </td>
              <td>
                <input type="checkbox" name="hideavglatency" '.( ( $hide_avg_latency["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>';

        if ( $core == 1 )
          $output .= '
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "hideservermem_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "hideservermem").'</a>:
              </td>
              <td>
                <select name="hideservermem">
                  <option value="0" '.( ( $hide_server_mem["Value"] == 0 ) ? 'selected="selected"' : '' ).'>'.lang("admin", "hide").'</option>
                  <option value="1" '.( ( $hide_server_mem["Value"] == 1 ) ? 'selected="selected"' : '' ).'>'.lang("admin", "showtogmsonly").'</option>
                  <option value="2" '.( ( $hide_server_mem["Value"] == 2 ) ? 'selected="selected"' : '' ).'>'.lang("admin", "showall").'</option>
                </select>
              </td>
            </tr>';

        $output .= '
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "hideplrlatency_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "hideplrlatency").'</a>:
              </td>
              <td>
                <input type="checkbox" name="hideplrlatency" '.( ( $hide_plr_latency["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "hidecoupons_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "hidecoupons").'</a>:
              </td>
              <td>
                <input type="checkbox" name="hidecoupons" '.( ( $hide_coupons["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "backupdir_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "backupdir").'</a>:
              </td>
              <td>
                <input type="text" name="backupdir" value="'.$backup_dir["Value"].'" readonly="readonly" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "debug_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "debug").'</a>:
              </td>
              <td>
                <input type="text" name="debug" value="'.$debug["Value"].'" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "testmode_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "testmode").'</a>:
              </td>
              <td>
                <input type="text" name="testmode" value="'.$test_mode["Value"].'" readonly="readonly" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "multirealm_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "multirealm").'</a>:
              </td>
              <td>
                <input type="text" name="multirealm" value="'.$multi_realm["Value"].'" readonly="readonly" />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "showemblem_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "showemblem").'</a>:
              </td>
              <td>
                <input type="checkbox" name="showemblem" '.( ( $show_emblem["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "shownewuser_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "shownewuser").'</a>:
              </td>
              <td>
                <input type="checkbox" name="shownewuser" '.( ( $show_newest_user["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "sendonemail_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "sendonemail").'</a>:
              </td>
              <td>
                <input type="checkbox" name="sendonemail" '.( ( $send_on_email["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "indexshowrealms_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "indexshowrealms").'</a>:
              </td>
              <td>
                <input type="checkbox" name="indexshowrealms" '.( ( $index_show_realms["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <b>'.lang("admin", "customlogos").'</b>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "usecustomlogo_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "usecustomlogo").'</a>:
              </td>
              <td>
                <input type="checkbox" name="usecustomlogo" '.( ( $use_custom_logo["Value"] == 1 ) ? 'checked="checked"' : '' ).' '.( ( $custom_logo_count > 0 ) ? '' : 'disabled="disabled"' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "customlogo_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "customlogo").'</a>:
              </td>
              <td>
                <select name="customlogo" '.( ( $custom_logo_count > 0 ) ? '' : 'disabled="disabled"' ).'>';
        foreach ( $custom_logos as $row )
        {
          $output .= '
                      <option value="'.$row["id"].'" '.( ( $row["id"] == $custom_logo["Value"] ) ? 'selected="selected"' : '' ).'>'.$row["filename"].'</option>';
        }
        $output .= '
                </select>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "deleteselectedlogo_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "deleteselectedlogo").'</a>:
              </td>
              <td>
                <input type="checkbox" name="deleteselectedlogo" '.( ( $custom_logo_count > 0 ) ? '' : 'disabled="disabled"' ).' />
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "uploadlogo_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "uploadlogo").'</a>:
              </td>
              <td>
                <a href="admin.php?section=general&amp;subsection=upload_logo">'.lang("admin", "upload").'</a>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "allowcaching_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "allowcaching").'</a>:
              </td>
              <td>
                <input type="checkbox" name="allowcaching" '.( ( $allow_caching["Value"] == 1 ) ? 'checked="checked"' : '' ).' />
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <b>'.lang("admin", "language").'</b>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "languagelocalessearchoption_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "languagelocalessearchoption").'</a>:
              </td>
              <td>
                <select name="languagelocalessearchoption">
                  <option value="0" '.( ( $language_locales_search_option["Value"] == 0 ) ? 'selected="selected" ' : '' ).'>'.lang("global", "language_0").'</option>
                  <option value="1" '.( ( $language_locales_search_option["Value"] == 1 ) ? 'selected="selected" ' : '' ).'>'.lang("global", "language_1").'</option>
                  <option value="2" '.( ( $language_locales_search_option["Value"] == 2 ) ? 'selected="selected" ' : '' ).'>'.lang("global", "language_2").'</option>
                  <option value="3" '.( ( $language_locales_search_option["Value"] == 3 ) ? 'selected="selected" ' : '' ).'>'.lang("global", "language_3").'</option>
                  <option value="4" '.( ( $language_locales_search_option["Value"] == 4 ) ? 'selected="selected" ' : '' ).'>'.lang("global", "language_4").'</option>
                  <option value="5" '.( ( $language_locales_search_option["Value"] == 5 ) ? 'selected="selected" ' : '' ).'>'.lang("global", "language_5").'</option>
                  <option value="6" '.( ( $language_locales_search_option["Value"] == 6 ) ? 'selected="selected" ' : '' ).'>'.lang("global", "language_6").'</option>
                  <option value="7" '.( ( $language_locales_search_option["Value"] == 7 ) ? 'selected="selected" ' : '' ).'>'.lang("global", "language_7").'</option>
                  <option value="8" '.( ( $language_locales_search_option["Value"] == 8 ) ? 'selected="selected" ' : '' ).'>'.lang("global", "language_8").'</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "languagesiteencoding_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "languagesiteencoding").'</a>:
              </td>
              <td>
                <input type="text" name="languagesiteencoding" value="'.$language_site_encoding["Value"].'" />
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
        </form>';
      }
      else
      {
        $sql_search_limit = $sqlm->quote_smart($_GET["sqlsearchlimit"]);
        $item_icons = $sqlm->quote_smart($_GET["itemicons"]);
        $remember_me_checked = ( ( isset($_GET["remembermechecked"]) ) ? 1 : 0 );
        $site_title = $sqlm->quote_smart($_GET["sitetitle"]);
        $item_per_page = $sqlm->quote_smart($_GET["itemperpage"]);
        $show_country_flags = ( ( isset($_GET["showcountryflags"]) ) ? 1 : 0 );
        $default_theme = $sqlm->quote_smart($_GET["defaulttheme"]);
        $default_language = $sqlm->quote_smart($_GET["defaultlanguage"]);
        $timezone = $sqlm->quote_smart($_GET["timezone"]);
        $timezone_offset = $sqlm->quote_smart($_GET["timezone_offset"]);
        $player_online = $sqlm->quote_smart($_GET["playeronline"]);
        $gm_online = ( ( isset($_GET["gmonline"]) ) ? 1 : 0 );
        $gm_online_count = ( ( isset($_GET["gmonlinecount"]) ) ? 1 : 0 );
        $hide_uptime = ( ( isset($_GET["hideuptime"]) ) ? 1 : 0 );
        $hide_max_players = ( ( isset($_GET["hidemaxplayers"]) ) ? 1 : 0 );
        $hide_avg_latency = ( ( isset($_GET["hideavglatency"]) ) ? 1 : 0 );
        $hide_plr_latency = ( ( isset($_GET["hideplrlatency"]) ) ? 1 : 0 );
        $hide_coupons = ( ( isset($_GET["hidecoupons"]) ) ? 1 : 0 );
        $backup_dir = $sqlm->quote_smart($_GET["backupdir"]);
        $debug = $sqlm->quote_smart($_GET["debug"]);
        $test_mode = $sqlm->quote_smart($_GET["testmode"]);
        $multi_realm = $sqlm->quote_smart($_GET["multirealm"]);
        $show_emblem = ( ( isset($_GET["showemblem"]) ) ? 1 : 0 );
        $language_locales_search_option = $sqlm->quote_smart($_GET["languagelocalessearchoption"]);
        $language_site_encoding = $sqlm->quote_smart($_GET["languagesiteencoding"]);
        $hide_server_mem = $sqlm->quote_smart($_GET["hideservermem"]);
        $show_newest_user = ( ( isset($_GET["shownewuser"]) ) ? 1 : 0 );
        $send_on_email = ( ( isset($_GET["sendonemail"]) ) ? 1 : 0 );
        $index_show_realms = ( ( isset($_GET["indexshowrealms"]) ) ? 1 : 0 );
        $use_custom_logo = ( ( isset($_GET["usecustomlogo"]) ) ? 1 : 0 );
        $custom_logo = ( ( isset($_GET["customlogo"]) ) ? $sqlm->quote_smart($_GET["customlogo"]) : NULL );
        $delete_selected = ( ( isset($_GET["deleteselectedlogo"]) ) ? 1 : 0 );
        $allow_caching = ( ( isset($_GET["allowcaching"]) ) ? 1 : 0 );

        $result = $sqlm->query("UPDATE config_misc SET Value='".$sql_search_limit."' WHERE `Key`='SQL_Search_Limit'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$item_icons."' WHERE `Key`='Item_Icons'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$remember_me_checked."' WHERE `Key`='Remember_Me_Checked'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$site_title."' WHERE `Key`='Site_Title'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$item_per_page."' WHERE `Key`='Item_Per_Page'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$show_country_flags."' WHERE `Key`='Show_Country_Flags'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$default_theme."' WHERE `Key`='Default_Theme'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$default_language."' WHERE `Key`='Default_Language'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$timezone."' WHERE `Key`='Timezone'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$timezone_offset."' WHERE `Key`='Timezone_Offset'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$player_online."' WHERE `Key`='Player_Online'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$gm_online."' WHERE `Key`='GM_Online'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$gm_online_count."' WHERE `Key`='GM_Online_Count'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$hide_uptime."' WHERE `Key`='Hide_Uptime'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$hide_max_players."' WHERE `Key`='Hide_Max_Players'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$hide_avg_latency."' WHERE `Key`='Hide_Avg_Latency'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$hide_server_mem."' WHERE `Key`='Hide_Server_Mem'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$hide_plr_latency."' WHERE `Key`='Hide_Plr_Latency'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$hide_coupons."' WHERE `Key`='Hide_Coupons_Main_Page'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$backup_dir."' WHERE `Key`='Backup_Dir'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$debug."' WHERE `Key`='Debug'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$test_mode."' WHERE `Key`='Test_Mode'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$multi_realm."' WHERE `Key`='Multi_Realm'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$show_emblem."' WHERE `Key`='Show_Guild_Emblem'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$language_locales_search_option."' WHERE `Key`='Language_Locales_Search_Option'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$language_site_encoding."' WHERE `Key`='Language_Site_Encoding'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$show_newest_user."' WHERE `Key`='Show_Newest_User'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$send_on_email."' WHERE `Key`='Send_Mail_On_Email_Change'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$index_show_realms."' WHERE `Key`='Index_Show_Realms'");

        if ( $delete_selected )
        {
          $result = $sqlm->query("DELETE FROM custom_logos WHERE id='".$custom_logo."'");

          // if we have no more logos, then we don't want Use Custom Logos checked.
          $result = $sqlm->query("SELECT * FROM custom_logos");
          $logo_count = $sqlm->num_rows($result);

          if ( $logo_count == 0 )
            $use_custom_logo = 0;
          else
          {
            // we don't want the Custom_Logo field set to the one we just deleted
            // so we'll set it to the first one on the list
            $temp = $sqlm->fetch_assoc($result);
            $custom_logo = $temp["id"];
          }
        }

        $result = $sqlm->query("UPDATE config_misc SET Value='".$use_custom_logo."' WHERE `Key`='Use_Custom_Logo'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$custom_logo."' WHERE `Key`='Custom_Logo'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$allow_caching."' WHERE `Key`='Allow_Logo_Caching'");

        redirect("admin.php?section=general&subsection=more");
      }
      break;
    }
    case "upload_logo":
    {
      if ( !$sub_action )
      {
        $upload_err = ( ( isset($_GET["up_err"]) ) ? $_GET["up_err"] : NULL );

        $output .= '
        <form name="form" action="admin.php?section=general&subsection=upload_logo&subaction=upload" method="post" enctype="multipart/form-data">
          <table class="simple" id="admin_more">';

        if ( isset($upload_err) )
        {
          $msg = lang("admin", "uploaderror".abs($upload_err));

          $output .= '
            <td colspan="2">
              <span class="error" style="display: block; width: 100%; text-align: center;">'.$msg.'</span>
            </td>';
        }

        $output .= '
            <tr>
              <td class="help">
                <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "uploadlogo_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "uploadlogo").'</a>:
              </td>
              <td>
                <input type="file" name="image" />
              </td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang("admin", "save").'" />
          <input type="button" name="cancel" value="'.lang("admin", "cancel").'" onclick="window.location=\'admin.php?section=general&subsection=more\'"/>
        </form>';
      }
      else
      {
        if ( !array_key_exists("image", $_FILES) )
          redirect("admin.php?section=general&subsection=upload_logo&error=1");

        $image = $_FILES["image"];

        $err = checkValidUpload($image["error"]);

        if ( $err < 0 )
          redirect("admin.php?section=general&subsection=upload_logo&up_err=".$err);
        else
        {
          if ( !is_uploaded_file($image["tmp_name"]) )
            redirect("admin.php?section=general&subsection=upload_logo&up_err=-8");

          $info = getImageSize($image["tmp_name"]);

          if ( !$info )
            redirect("admin.php?section=general&subsection=upload_logo&up_err=-9");

          $name = $sqlm->quote_smart($image["name"]);
          $mime = $sqlm->quote_smart($info["mime"]);
          $data = $sqlm->quote_smart(file_get_contents($image["tmp_name"]));

          $upload_query = "INSERT INTO custom_logos (filename, mime_type, file_size, file_data) VALUES ('".$name."', '".$mime."', '".$image['size']."', '".$data."')";
          $sqlm->query($upload_query);

          redirect("admin.php?section=general&subsection=more");
        }
      }
      break;
    }
    case "gmlevels":
    {
      if ( !$sub_action )
      {
        $gm_lvls = $sqlm->query("SELECT * FROM config_gm_level_names");

        if ( !isset($_GET["edit_btn"]) )
        {
          $output .= '
        <center>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="general" />
            <input type="hidden" name="subsection" value="gmlevels" />
            <input type="hidden" name="edit_btn" value="edit" />
            <table class="simple">
              <tr>
                <th>'.lang("admin", "edit").'</th>
                <th>'.lang("admin", "remove").'</th>
                <th>'.lang("admin", "seclvl").'</th>
                <th>'.lang("admin", "fullname").'</th>
                <th>'.lang("admin", "shortname").'</th>
              </tr>';

          $color = "#EEEEEE";

          while( $gm_lvl = $sqlm->fetch_assoc($gm_lvls) )
          {
            $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=general&amp;subsection=gmlevels&amp;edit='.$gm_lvl["Index"].'&amp;edit_btn=Edit">
                      <img src="img/edit.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=general&amp;subsection=gmlevels&amp;delrow=deleterow&amp;edit='.$gm_lvl["Index"].'&amp;edit_btn=Edit">
                      <img src="img/aff_cross.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'">'.$gm_lvl["Security_Level"].'</td>
                <td style="background-color:'.$color.'">'.$gm_lvl["Full_Name"].'</td>
                <td style="background-color:'.$color.'">'.$gm_lvl["Short_Name"].'</td>
              </tr>';

            if ( $color == "#EEEEEE" )
              $color = "#FFFFFF";
            else
              $color = "#EEEEEE";
          }
          $output .= '
              <tr>
                <td style="background-color:'.$color.'">
                  <center>
                    <a href="admin.php?section=general&amp;subsection=gmlevels&amp;edit_btn=Edit&amp;addrow=addrow">
                      <img src="img/add.png" alt="" />
                    </a>
                  </center>
                </td>
                <td style="background-color:'.$color.'" colspan="4">
                  <a href="admin.php?section=general&amp;subsection=gmlevels&amp;edit_btn=Edit&amp;addrow=addrow">'.lang("admin", "addrow").'</a>
                </td>
              </tr>';

          $output .= '
            </table>
            <!-- input type="checkbox" name="addrow">'.lang("admin", "addrow").'
            <input type="checkbox" name="delrow">'.lang("admin", "delrow").'
            <br />
            <input type="submit" name="addrow" value="'.lang("admin", "addrow").'" -->
          </form>
        </center>';
        }
        else
        {
          if ( !isset($_GET["edit"]) )
            if ( !isset($_GET["addrow"]) )
              redirect("admin.php?section=general&subsection=gmlevels");

          $del_row = ( ( isset($_GET["delrow"]) ) ? $_GET["delrow"] : "" );
          $add_row = ( ( isset($_GET["addrow"]) ) ? $_GET["addrow"] : "" );
          $edit_row = $sqlm->quote_smart($_GET["edit"]);

          if ( $add_row )
          {
            $add_result = $sqlm->query("INSERT INTO config_gm_level_names (Security_Level) VALUES ('-1')");
            redirect("admin.php?section=general&subsection=gmlevels");
          }

          if ( $del_row )
          {
            $del_result = $sqlm->query("DELETE FROM config_gm_level_names WHERE `Index`='".$edit_row."'");
            redirect("admin.php?section=general&subsection=gmlevels");
          }

          $gm_level = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_gm_level_names WHERE `Index`='".$edit_row."'"));
          $output .= '
        <center>
          <form name="form" action="admin.php" method="get">
            <input type="hidden" name="section" value="general" />
            <input type="hidden" name="subsection" value="gmlevels" />
            <input type="hidden" name="subaction" value="savegms" />
            <input type="hidden" name="index" value="'.$gm_level["Index"].'" />
            <fieldset id="admin_gm_level">
            <table>
              <tr>
                <td class="help">
                  <a href="#" onmouseover="oldtoolTip(\''.lang("admin", "seclvl_tip").'\', \'info_tooltip\')" onmouseout="oldtoolTip()">'.lang("admin", "seclvl").'</a>:
                </td>
                <td>
                  <input type="text" name="seclvl" value="'.$gm_level["Security_Level"].'" />
                </td>
              </tr>
              <tr>
                <td>'.lang("admin", "fullname").': </td>
                <td>
                  <input type="text" name="fullname" value="'.$gm_level["Full_Name"].'" />
                </td>
              </tr>
              <tr>
                <td>'.lang("admin", "shortname").': </td>
                <td>
                  <input type="text" name="shortname" value="'.$gm_level["Short_Name"].'" />
                </td>
              </tr>
            </table>
            </fieldset>
            <input type="submit" name="save" value="'.lang("admin", "save").'" />
          </form>
        </center>';
        }
      }
      else
      {
        $index = $sqlm->quote_smart($_GET["index"]);
        $sec_lvl = $sqlm->quote_smart($_GET["seclvl"]);
        $full_name = $sqlm->quote_smart($_GET["fullname"]);
        $short_name = $sqlm->quote_smart($_GET["shortname"]);

        $result = $sqlm->query("UPDATE config_gm_level_names SET Security_Level='".$sec_lvl."', Full_Name='".$full_name."', Short_Name='".$short_name."' WHERE `Index`='".$index."'");
        redirect("admin.php?section=general&subsection=gmlevels");
      }
      break;
    }
  }

  $output .= '
      </div>';
}

?>
