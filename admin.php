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


$time_start = microtime(true);
// resuming login session if available, or start new one
if ( ini_get('session.auto_start') )
  ;
else
  session_start();

require_once("configs/config.php");
require_once("admin/admin_lib.php");
require_once("libs/lang_lib.php");

valid_login_webadmin(0);

$output = '';

require_once("admin/header.php");

function database()
{
  global $output, $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $dbc_db = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_dbc_database"));
  $logon_db = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_logon_database"));
  $char_dbs = $sqlm->query("SELECT * FROM config_character_databases");
  $world_dbs = $sqlm->query("SELECT * FROM config_world_databases");

  $output .= lang('admin','db_warn').'
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="databases">
          <input type="hidden" name="action" value="savedbs">
          <table>
            <tr>
              <td>
                <fieldset id="admin_editdb_field">
                  <legend>'.lang('admin', 'arcm').'</legend>
                  <table>
                    <tr>
                      <td width=75px>'.lang('admin', 'host').': </td>
                      <td><input type="text" name="corem_host" value="'.$dbc_db['Address'].'" size="10%"></td>
                      <td width=75px>'.lang('admin', 'port').': </td>
                      <td><input type="text" name="corem_port" value="'.$dbc_db['Port'].'" size="10%"></td>
                    </tr>
                    <tr>
                      <td width=75px>'.lang('admin', 'user').': </td>
                      <td><input type="text" name="corem_user" value="'.$dbc_db['User'].'" size="10%"></td>
                      <td width=75px>'.lang('admin', 'pass').': </td>
                      <td><input type="text" name="corem_pass" value="'.$dbc_db['Password'].'" size="10%"></td>
                    </tr>
                    <tr>
                      <td width=75px>'.lang('admin', 'name').': </td>
                      <td><input type="text" name="corem_name" value="'.$dbc_db['Name'].'" size="10%"></td>
                    </tr>
                  </table>
                </fieldset>
              </td>
              <td>
                <fieldset id="admin_editdb_field">
                  <legend>'.lang('admin', 'logon').'</legend>
                  <table>
                    <tr>
                      <td width=75px>'.lang('admin', 'host').': </td>
                      <td><input type="text" name="logon_host" value="'.$logon_db['Address'].'" size="10%"></td>
                      <td width=75px>'.lang('admin', 'port').': </td>
                      <td><input type="text" name="logon_port" value="'.$logon_db['Port'].'" size="10%"></td>
                    </tr>
                    <tr>
                      <td width=75px>'.lang('admin', 'user').': </td>
                      <td><input type="text" name="logon_user" value="'.$logon_db['User'].'" size="10%"></td>
                      <td width=75px>'.lang('admin', 'pass').': </td>
                      <td><input type="text" name="logon_pass" value="'.$logon_db['Password'].'" size="10%"></td>
                    </tr>
                    <tr>
                      <td width=75px>'.lang('admin', 'name').': </td>
                      <td><input type="text" name="logon_name" value="'.$logon_db['Name'].'" size="10%"></td>
                    </tr>
                  </table>
                </fieldset>
              </td>
            </tr>
            <tr>
              <td colspan=2><hr></td>
            </tr>
            <tr>';
  while ( $char = $sqlm->fetch_assoc($char_dbs) )
  {
    $output .= '
              <td>
                <input type="hidden" name="char_realm[]" value="'.$char['Index'].'">
                <fieldset id="admin_editdb_field">
                  <legend>'.lang('admin', 'char').' ('.lang('admin', 'realm').' '.$char['Index'].')</legend>
                  <table>
                    <tr>
                      <td width=75px>'.lang('admin', 'host').': </td>
                      <td><input type="text" name="char_host[]" value="'.$char['Address'].'" size="10%"></td>
                      <td width=75px>'.lang('admin', 'port').': </td>
                      <td><input type="text" name="char_port[]" value="'.$char['Port'].'" size="10%"></td>
                    </tr>
                    <tr>
                      <td width=75px>'.lang('admin', 'user').': </td>
                      <td><input type="text" name="char_user[]" value="'.$char['User'].'" size="10%"></td>
                      <td width=75px>'.lang('admin', 'pass').': </td>
                      <td><input type="text" name="char_pass[]" value="'.$char['Password'].'" size="10%"></td>
                    </tr>
                    <tr>
                      <td width=75px>'.lang('admin', 'name').': </td>
                      <td><input type="text" name="char_name[]" value="'.$char['Name'].'" size="10%"></td>
                      <td colspan=2><center><input type="checkbox" name="remove_char_'.$char['Index'].'"> '.lang('admin', 'remove').'</center><td>
                    </tr>
                  </table>
                </fieldset>
              </td>';
  }
  $output .= '
            </tr>
            <tr>
              <td><input type="checkbox" name="addchar"><b>'.lang('admin', 'addchar').'</b></td>
            </tr>';
  $output .= '
            <tr>';
  while ( $world = $sqlm->fetch_assoc($world_dbs) )
  {
    $output .= '
              <td>
                <input type="hidden" name="world_realm[]" value="'.$world['Index'].'">
                <fieldset id="admin_editdb_field">
                  <legend>'.lang('admin', 'world').' ('.lang('admin', 'realm').' '.$world['Index'].')</legend>
                  <table>
                    <tr>
                      <td width=75px>'.lang('admin', 'host').': </td>
                      <td><input type="text" name="world_host[]" value="'.$world['Address'].'" size="10%"></td>
                      <td width=75px>'.lang('admin', 'port').': </td>
                      <td><input type="text" name="world_port[]" value="'.$world['Port'].'" size="10%"></td>
                    </tr>
                    <tr>
                      <td width=75px>'.lang('admin', 'user').': </td>
                      <td><input type="text" name="world_user[]" value="'.$world['User'].'" size="10%"></td>
                      <td>'.lang('admin', 'pass').': </td>
                      <td><input type="text" name="world_pass[]" value="'.$world['Password'].'" size="10%"></td>
                    </tr>
                    <tr>
                      <td width=75px>'.lang('admin', 'name').': </td>
                      <td><input type="text" name="world_name[]" value="'.$world['Name'].'" size="10%"></td>
                      <td colspan=2><center><input type="checkbox" name="remove_world_'.$world['Index'].'"> '.lang('admin', 'remove').'</center><td>
                    </tr>
                  </table>
                </fieldset>
              </td>';
  }
  $output .= '
            </tr>
            <tr>
              <td><input type="checkbox" name="addworld"><b>'.lang('admin', 'addworld').'</b></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'">
        </form>';
}

function savedbs()
{
  global $output, $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $corem_host = $sqlm->quote_smart($_GET['corem_host']);
  $corem_port = $sqlm->quote_smart($_GET['corem_port']);
  $corem_user = $sqlm->quote_smart($_GET['corem_user']);
  $corem_pass = $sqlm->quote_smart($_GET['corem_pass']);
  $corem_name = $sqlm->quote_smart($_GET['corem_name']);
  $result_arcm = $sqlm->query("UPDATE config_dbc_database SET Address='".$corem_host."', Port='".$corem_port."', User='".$corem_user."', Password='".$corem_pass."', Name='".$corem_name."' WHERE `Index`=1");

  $logon_host = $sqlm->quote_smart($_GET['logon_host']);
  $logon_port = $sqlm->quote_smart($_GET['logon_port']);
  $logon_user = $sqlm->quote_smart($_GET['logon_user']);
  $logon_pass = $sqlm->quote_smart($_GET['logon_pass']);
  $logon_name = $sqlm->quote_smart($_GET['logon_name']);
  $result_logon = $sqlm->query("UPDATE config_logon_database SET Address='".$logon_host."', Port='".$logon_port."', User='".$logon_user."', Password='".$logon_pass."', Name='".$logon_name."' WHERE `Index`=1");

  $char_realms = $sqlm->quote_smart($_GET['char_realm']);
  $char_hosts = $sqlm->quote_smart($_GET['char_host']);
  $char_ports = $sqlm->quote_smart($_GET['char_port']);
  $char_users = $sqlm->quote_smart($_GET['char_user']);
  $char_passes = $sqlm->quote_smart($_GET['char_pass']);
  $char_names = $sqlm->quote_smart($_GET['char_name']);

  for ( $i=0; $i<count($char_hosts); $i++ )
  {
    $result_char = $sqlm->query("UPDATE config_character_databases SET Address='".$char_hosts[$i]."', Port='".$char_ports[$i]."', User='".$char_users[$i]."', Password='".$char_passes[$i]."', Name='".$char_names[$i]."' WHERE `Index`='".$char_realms[$i]."'");
    if ( isset($_GET['remove_char_'.$char_realms[$i]]) )
      $result_char = $sqlm->query("DELETE FROM config_character_databases WHERE `Index`='".$char_realms[$i]."'");
  }

  $world_realms = $sqlm->quote_smart($_GET['world_realm']);
  $world_hosts = $sqlm->quote_smart($_GET['world_host']);
  $world_ports = $sqlm->quote_smart($_GET['world_port']);
  $world_users = $sqlm->quote_smart($_GET['world_user']);
  $world_passes = $sqlm->quote_smart($_GET['world_pass']);
  $world_names = $sqlm->quote_smart($_GET['world_name']);
    
  for ( $i=0; $i<count($world_hosts); $i++ )
  {
    $result_world = $sqlm->query("UPDATE config_world_databases SET Address='".$world_hosts[$i]."', Port='".$world_ports[$i]."', User='".$world_users[$i]."', Password='".$world_passes[$i]."', Name='".$world_names[$i]."' WHERE `Index`='".$world_realms[$i]."'");
    if ( isset($_GET['remove_world_'.$world_realms[$i]]) )
      $result_world = $sqlm->query("DELETE FROM config_world_databases WHERE `Index`='".$world_realms[$i]."'");
  }

  if ( isset($_GET['addchar']) )
    $result_addchar = $sqlm->query("INSERT INTO config_character_databases (Encoding) VALUES ('utf8')");

  if ( isset($_GET['addworld']) )
    $result_addworld = $sqlm->query("INSERT INTO config_world_databases (Encoding) VALUES ('utf8')");

  redirect("admin.php?section=databases");
}

function general()
{
  global $output, $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  if ( isset($_GET['subsection']) )
    $subsection = $sqlm->quote_smart($_GET['subsection']);
  else
    $subsection = 1;

  $output .= '
        <table id="sidebar">
          <tr>
            <td '.($subsection == 'version' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=version">'.lang('admin', 'version').'</a></td>
          </tr>
          <tr>
            <td '.($subsection == 'mail' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=mail">'.lang('admin', 'mail').'</a></td>
          </tr>
          <tr>
            <td '.($subsection == 'irc' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=irc">'.lang('admin', 'irc').'</a></td>
          </tr>
          <tr>
            <td '.($subsection == 'proxy' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=proxy">'.lang('admin', 'proxy').'</a></td>
          </tr>
          <tr>
            <td '.($subsection == 'datasite' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=datasite">'.lang('admin', 'datasite').'</a></td>
          </tr>
          <tr>
            <td '.($subsection == 'acctcreation' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=acctcreation">'.lang('admin', 'acct_creation').'</a></td>
          </tr>
          <tr>
            <td '.($subsection == 'guests' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=guests">'.lang('admin', 'guests').'</a></td>
          </tr>
          <tr>
            <td '.($subsection == 'extratools' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=extratools">'.lang('admin', 'extra_tools').'</a></td>
          </tr>
          <tr>
            <td '.($subsection == 'internalmap' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=internalmap">'.lang('admin', 'internal_map').'</a></td>
          </tr>
          <tr>
            <td '.($subsection == 'validip' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=validip">'.lang('admin', 'validip').'</a></td>
          </tr>
          <tr>
            <td '.($subsection == 'more' ? 'id="current"' : '').'><a href="admin.php?section=general&subsection=more">'.lang('admin', 'more').'</a></td>
          </tr>
        </table>';

  if ( isset($_GET['error']) )
    $output .= '
      <div id="misc_error">';
  else
    $output .= '
      <div id="misc">';

  if ( isset($_GET['subaction']) )
     $sub_action = $_GET['subaction'];
  else
     $sub_action = '';

  switch ( $subsection )
  {
    case 'version':
    {
      if ( !$sub_action )
      {
        $show_version_show = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Version_Show'"));
        $show_version_version = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Version_Version'"));
        $show_version_version_lvl = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Version_Version_Lvl'"));
        $show_version_revision = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Version_SVNRev'"));
        $show_version_revision_lvl = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Show_Version_SVNRev_Lvl'"));
        $output .= '
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="saveversion">
          <input type="hidden" name="subsection" value="version">
          <table class="simple">
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'show').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'show').'</a>: </td>
              <td>
                <select name="showversion" id="admin_showversion_field">
                  <option value="0" '.(($show_version_show['Value'] == 0) ? 'selected="selected"' : '').'>'.lang('admin', 'dontshow').'</option>
                  <option value="1" '.(($show_version_show['Value'] == 1) ? 'selected="selected"' : '').'disabled="disabled">'.lang('admin', 'version').'</option>
                  <option value="2"'.(($show_version_show['Value'] == 2) ? 'selected="selected"' : '').'>'.lang('admin', 'verrev').'</option>
                </select>
              </td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'version').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'version').'</a>: </td>
              <td><input type="text" name="version" value="'.$show_version_version['Value'].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'versionlvl').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'versionlvl').'</a>: </td>
              <td><input type="text" name="versionlvl" value="'.$show_version_version_lvl['Value'].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'revision').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'revision').'</a>: </td>
              <td><input type="text" name="revision" value="'.$show_version_revision['Value'].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'revisionlvl').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'revisionlvl').'</a>: </td>
              <td><input type="text" name="revisionlvl" value="'.$show_version_revision_lvl['Value'].'" /></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
      }
      else
      {
        $show_version = $sqlm->quote_smart($_GET['showversion']);
        $version = $sqlm->quote_smart($_GET['version']);
        $version_lvl = $sqlm->quote_smart($_GET['versionlvl']);
        $revision = $sqlm->quote_smart($_GET['revision']);
        $revision_lvl = $sqlm->quote_smart($_GET['revisionlvl']);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$show_version."' WHERE `Key`='Show_Version_Show'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$version."' WHERE `Key`='Show_Version_Version'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$version_lvl."' WHERE `Key`='Show_Version_Version_Lvl'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$revision."' WHERE `Key`='Show_Version_SVNRev'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$revision_lvl."' WHERE `Key`='Show_Version_SVNRev_Lvl'");

        redirect("admin.php?section=general&subsection=version");
      }
    break;
    }
    case 'mail':
    {
      if ( !$sub_action )
      {
        $mail_admin_email = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Mail_Admin_Email'"));
        $mail_mailer_type = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Mail_Mailer_Type'"));
        $mail_from_email = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Mail_From_Email'"));
        $mail_gmailsender = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Mail_GMailSender'"));
        $smtp_host = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='SMTP_Host'"));
        $smtp_port = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='SMTP_Port'"));
        $smtp_user = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='SMTP_User'"));
        $smtp_pass = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='SMTP_Pass'"));
        $pm_from_char = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='PM_From_Char'"));
        $pm_stationary = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='PM_Stationary'"));
        $output .= '
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="savemail">
          <input type="hidden" name="subsection" value="mail">
          <table class="simple">
            <tr>
              <td colspan=2><b>'.lang('admin', 'email').'</b></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'adminemail').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'adminemail').'</a>: </td>
              <td><input type="text" name="adminemail" value="'.$mail_admin_email['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'mailertype').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'mailertype').'</a>: </td>
              <td>
                <select name="mailertype" id="admin_mailertype_field">
                  <option value="mail" '.(($mail_mailer_type['Value'] == 'mail') ? 'selected="selected"' : '').'>'.lang('admin', 'mail').'</option>
                  <option value="sendmail" '.(($mail_mailer_type['Value'] == 'sendmail') ? 'selected="selected"' : '').'">'.lang('admin', 'sendmail').'</option>
                  <option value="smtp"'.(($mail_mailer_type['Value'] == 'smtp') ? 'selected="selected"' : '').'>'.lang('admin', 'smtp').'</option>
                </select>
              </td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'fromemail').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'fromemail').'</a>: </td>
              <td><input type="text" name="fromemail" value="'.$mail_from_email['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'usegmail').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'usegmail').'</a>: </td>
              <td><input type="checkbox" name="gmail" '.($mail_gmailsender['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td colspan=2><b>'.lang('admin', 'smtp').'</b></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'smtphost').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'smtphost').'</a>: </td>
              <td><input type="text" name="smtphost" value="'.$smtp_host['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'smtpport').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'smtpport').'</a>: </td>
              <td><input type="text" name="smtpport" value="'.$smtp_port['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'smtpuser').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'smtpuser').'</a>: </td>
              <td><input type="text" name="smtpuser" value="'.$smtp_user['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'smtppass').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'smtppass').'</a>: </td>
              <td><input type="text" name="smtppass" value="'.$smtp_pass['Value'].'" /></td>
            </tr>
            <tr>
              <td colspan=2><b>'.lang('admin', 'pm').'</b></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'pmfrom').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'pmfrom').'</a>: </td>
              <td><input type="text" name="fromchar" value="'.$pm_from_char['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'pmstation').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'pmstation').'</a>: </td>
              <td><input type="text" name="stationary" value="'.$pm_stationary['Value'].'" /></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
      }
      else
      {
        $mail_admin_email = $sqlm->quote_smart($_GET['adminemail']);
        $mail_mailer_type = $sqlm->quote_smart($_GET['mailertype']);
        $mail_from_email = $sqlm->quote_smart($_GET['fromemail']);
        if ( isset($_GET['gmail']) )
          $mail_gmailsender = 1;
        else
          $mail_gmailsender = 0;
        $smtp_host = $sqlm->quote_smart($_GET['smtphost']);
        $smtp_port = $sqlm->quote_smart($_GET['smtpport']);
        $smtp_user = $sqlm->quote_smart($_GET['smtpuser']);
        $smtp_pass = $sqlm->quote_smart($_GET['smtppass']);
        $pm_from_char = $sqlm->quote_smart($_GET['fromchar']);
        $pm_stationary = $sqlm->quote_smart($_GET['stationary']);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$mail_admin_email."' WHERE `Key`='Mail_Admin_Email'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$mail_mailer_type."' WHERE `Key`='Mail_Mailer_Type'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$mail_from_email."' WHERE `Key`='Mail_From_Email'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$mail_gmailsender."' WHERE `Key`='Mail_GMailSender'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$smtp_host."' WHERE `Key`='SMTP_Host'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$smtp_port."' WHERE `Key`='SMTP_Port'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$smtp_user."' WHERE `Key`='SMTP_User'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$smtp_pass."' WHERE `Key`='SMTP_Pass'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$pm_from_char."' WHERE `Key`='PM_From_Char'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$pm_stationary."' WHERE `Key`='PM_Stationary'");

        redirect("admin.php?section=general&subsection=mail");
      }
    break;
    }
    case 'irc':
    {
      if ( !$sub_action )
      {
        $irc_host = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='IRC_Server'"));
        $irc_port = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='IRC_Port'"));
        $irc_channel = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='IRC_Channel'"));
        $irc_helppage = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='IRC_HelpPage'"));
        $output .= '
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="saveirc">
          <input type="hidden" name="subsection" value="irc">
          <table class="simple">
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'irchost').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'irchost').'</a>: </td>
              <td><input type="text" name="irchost" value="'.$irc_host['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ircport').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ircport').'</a>: </td>
              <td><input type="text" name="ircport" value="'.$irc_port['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ircchannel').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ircchannel').'</a>: </td>
              <td><input type="text" name="ircchannel" value="'.$irc_channel['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'irchelppage').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'irchelppage').'</a>: </td>
              <td><input type="text" name="irchelppage" value="'.$irc_helppage['Value'].'" readonly="readonly" /></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
      }
      else
      {
        $irc_host = $sqlm->quote_smart($_GET['irchost']);
        $irc_port = $sqlm->quote_smart($_GET['ircport']);
        $irc_channel = $sqlm->quote_smart($_GET['ircchannel']);
        $irc_helppage = $sqlm->quote_smart($_GET['irchelppage']);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$irc_host."' WHERE `Key`='IRC_Server'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$irc_port."' WHERE `Key`='IRC_Port'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$irc_channel."' WHERE `Key`='IRC_Channel'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$irc_helppage."' WHERE `Key`='IRC_HelpPage'");

        redirect("admin.php?section=general&subsection=irc");
      }
    break;
    }
    case 'proxy':
    {
      if ( !$sub_action )
      {
        $proxy_host = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Proxy_Addr'"));
        $proxy_port = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Proxy_Port'"));
        $proxy_user = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Proxy_User'"));
        $proxy_pass = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Proxy_Pass'"));
        $output .= '
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="saveproxy">
          <input type="hidden" name="subsection" value="proxy">
          <table class="simple">
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'proxyhost').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'proxyhost').'</a>: </td>
              <td><input type="text" name="proxyhost" value="'.$proxy_host['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'proxyport').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'proxyport').'</a>: </td>
              <td><input type="text" name="proxyport" value="'.$proxy_port['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'proxyuser').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'proxyuser').'</a>: </td>
              <td><input type="text" name="proxyuser" value="'.$proxy_user['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'proxypass').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'proxypass').'</a>: </td>
              <td><input type="text" name="proxypass" value="'.$proxy_pass['Value'].'" /></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
      }
      else
      {
        $proxy_host = $sqlm->quote_smart($_GET['proxyhost']);
        $proxy_port = $sqlm->quote_smart($_GET['proxyport']);
        $proxy_user = $sqlm->quote_smart($_GET['proxyuser']);
        $proxy_pass = $sqlm->quote_smart($_GET['proxypass']);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$proxy_host."' WHERE `Key`='Proxy_Addr'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$proxy_port."' WHERE `Key`='Proxy_Port'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$proxy_user."' WHERE `Key`='Proxy_User'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$proxy_pass."' WHERE `Key`='Proxy_Pass'");

        redirect("admin.php?section=general&subsection=proxy");
      }
    break;
    }
    case 'datasite':
    {
      if ( !$sub_action )
      {
        $datasite_item = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Item'"));
        $datasite_quest = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Quest'"));
        $datasite_creature = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Creature'"));
        $datasite_spell = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Spell'"));
        $datasite_skill = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Skill'"));
        $datasite_go = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_GO'"));
        $datasite_achieve = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Datasite_Achievement'"));
        $output .= '
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="savedatasite">
          <input type="hidden" name="subsection" value="datasite">
          <table class="simple" id="admin_datasite">
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'datasiteitem').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'datasiteitem').'</a>: </td>
              <td><input type="text" name="datasiteitem" value="'.$datasite_item['Value'].'" size="50" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'datasitequest').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'datasitequest').'</a>: </td>
              <td><input type="text" name="datasitequest" value="'.$datasite_quest['Value'].'" size="50" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'datasitecreature').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'datasitecreature').'</a>: </td>
              <td><input type="text" name="datasitecreature" value="'.$datasite_creature['Value'].'" size="50" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'datasitespell').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'datasitespell').'</a>: </td>
              <td><input type="text" name="datasitespell" value="'.$datasite_spell['Value'].'" size="50" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'datasiteskill').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'datasiteskill').'</a>: </td>
              <td><input type="text" name="datasiteskill" value="'.$datasite_skill['Value'].'" size="50" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'datasitego').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'datasitego').'</a>: </td>
              <td><input type="text" name="datasitego" value="'.$datasite_go['Value'].'" size="50" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'datasiteachieve').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'datasiteachieve').'</a>: </td>
              <td><input type="text" name="datasiteachieve" value="'.$datasite_achieve['Value'].'" size="50" /></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
      }
      else
      {
        $datasite_item = $sqlm->quote_smart($_GET['datasiteitem']);
        $datasite_quest = $sqlm->quote_smart($_GET['datasitequest']);
        $datasite_creature = $sqlm->quote_smart($_GET['datasitecreature']);
        $datasite_spell = $sqlm->quote_smart($_GET['datasitespell']);
        $datasite_skill = $sqlm->quote_smart($_GET['datasiteskill']);
        $datasite_go = $sqlm->quote_smart($_GET['datasitego']);
        $datasite_achieve = $sqlm->quote_smart($_GET['datasiteachieve']);

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
    case 'acctcreation':
    {
      if ( !$sub_action )
      {
        $disable_acc_creation = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Disable_Acc_Creation'"));
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
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="saveacctcreation">
          <input type="hidden" name="subsection" value="acctcreation">
          <table class="simple" id="admin_acct_creation">
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'disableacccreation').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'disableacccreation').'</a>: </td>
              <td><input type="checkbox" name="disableacccreation" '.($disable_acc_creation['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'expansionselect').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'expansionselect').'</a>: </td>
              <td><input type="checkbox" name="expansionselect" '.($expansion_select['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'defaultexpansion').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'defaultexpansion').'</a>: </td>
              <td><input type="text" name="defaultexpansion" value="'.$default_expansion['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'enabledcaptcha').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'enabledcaptcha').'</a>: </td>
              <td><input type="checkbox" name="enabledcaptcha" '.($enabled_captcha['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'userecaptcha').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'userecaptcha').'</a>: </td>
              <td><input type="checkbox" name="userecaptcha" '.($using_recaptcha['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'publickey').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'publickey').'</a>: </td>
              <td><input type="text" name="publickey" value="'.$publickey['Value'].'" size="60" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'privatekey').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'privatekey').'</a>: </td>
              <td><input type="text" name="privatekey" value="'.$privatekey['Value'].'" size="60" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'sendmailoncreation').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'sendmailoncreation').'</a>: </td>
              <td><input type="checkbox" name="sendmailoncreation" '.($send_mail_on_creation['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'sendconfirmmailoncreation').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'sendconfirmmailoncreation').'</a>: </td>
              <td><input type="checkbox" name="sendconfirmmailoncreation" '.($send_confirmation_mail_on_creation['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'validatemailhost').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'validatemailhost').'</a>: </td>
              <td><input type="checkbox" name="validatemailhost" '.($validate_mail_host['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'limitaccperip').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'limitaccperip').'</a>: </td>
              <td><input type="checkbox" name="limitaccperip" '.($limit_acc_per_ip['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
      }
      else
      {
        if ( isset($_GET['disableacccreation']) )
          $disable_acc_creation = 1;
        else
          $disable_acc_creation = 0;
        if ( isset($_GET['expansionselect']) )
          $expansion_select = 1;
        else
          $expansion_select = 0;
        $default_expansion = $sqlm->quote_smart($_GET['defaultexpansion']);
        if ( isset($_GET['enabledcaptcha']) )
          $enabled_captcha = 1;
        else
          $enabled_captcha = 0;
        if ( isset($_GET['userecaptcha']) )
          $using_recaptcha = 1;
        else
          $using_recaptcha = 0;
        $publickey = $sqlm->quote_smart($_GET['publickey']);
        $privatekey = $sqlm->quote_smart($_GET['privatekey']);
        if ( isset($_GET['sendmailoncreation']) )
          $send_mail_on_creation = 1;
        else
          $send_mail_on_creation = 0;
        if ( isset($_GET['sendconfirmmailoncreation']) )
          $send_confirmation_mail_on_creation = 1;
        else
          $send_confirmation_mail_on_creation = 0;
        if ( isset($_GET['validatemailhost']) )
          $validate_mail_host = 1;
        else
          $validate_mail_host = 0;
        if ( isset($_GET['limitaccperip']) )
          $limit_acc_per_ip = 1;
        else
          $limit_acc_per_ip = 0;

        $result = $sqlm->query("UPDATE config_misc SET Value='".$disable_acc_creation."' WHERE `Key`='Disable_Acc_Creation'");
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
    case 'guests':
    {
      if ( !$sub_action )
      {
        $acp_allow_anony = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Allow_Anony'"));
        $acp_anony_name = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Anony_Name'"));
        $acp_anony_realm_id = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Anony_Realm_ID'"));
        $output .= '
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="saveguests">
          <input type="hidden" name="subsection" value="guests">
          <table class="simple">
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'allowanony').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'allowanony').'</a>: </td>
              <td><input type="checkbox" name="allowanony" '.($acp_allow_anony['Value'] == 1 ? 'checked="checked"' : '').' disabled="disabled" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'anonyname').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'anonyname').'</a>: </td>
              <td><input type="text" name="anonyname" value="'.$acp_anony_name['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'anonyrealmid').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'anonyrealmid').'</a>: </td>
              <td><input type="text" name="anonyrealmid" value="'.$acp_anony_realm_id['Value'].'" /></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
      }
      else
      {
        if ( isset($_GET['allowanony']) )
          $acp_allow_anony = 1;
        else
          $acp_allow_anony = 0;
        $acp_anony_name = $sqlm->quote_smart($_GET['anonyname']);
        $acp_anony_realm_id = $sqlm->quote_smart($_GET['anonyrealmid']);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$acp_allow_anony."' WHERE `Key`='Allow_Anony'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$acp_anony_name."' WHERE `Key`='Anony_Name'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$acp_anony_realm_id."' WHERE `Key`='Anony_Realm_ID'");

        redirect("admin.php?section=general&subsection=guests");
      }
    break;
    }
    case 'extratools':
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
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="saveextratools">
          <input type="hidden" name="subsection" value="extratools">
          <table class="simple">
            <tr>
              <td colspan=2><b>'.lang('admin', 'questitemvendor').'</b></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'questitemvendorlevelmul').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'questitemvendorlevelmul').'</a>: </td>
              <td><input type="text" name="questitemvendorlevelmul" value="'.$quest_item_vendor_level_mul['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'questitemvendorrewmul').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'questitemvendorrewmul').'</a>: </td>
              <td><input type="text" name="questitemvendorrewmul" value="'.$quest_item_vendor_rew_mul['Value'].'" /></td>
            </tr>
            <tr>
              <td colspan=2><b>'.lang('admin', 'ultravendor').'</b></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ultravendormult0').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ultravendormult0').'</a>: </td>
              <td><input type="text" name="ultravendormult0" value="'.$ultra_vendor_mult_0['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ultravendormult1').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ultravendormult1').'</a>: </td>
              <td><input type="text" name="ultravendormult1" value="'.$ultra_vendor_mult_1['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ultravendormult2').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ultravendormult2').'</a>: </td>
              <td><input type="text" name="ultravendormult2" value="'.$ultra_vendor_mult_2['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ultravendormult3').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ultravendormult3').'</a>: </td>
              <td><input type="text" name="ultravendormult3" value="'.$ultra_vendor_mult_3['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ultravendormult4').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ultravendormult4').'</a>: </td>
              <td><input type="text" name="ultravendormult4" value="'.$ultra_vendor_mult_4['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ultravendormult5').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ultravendormult5').'</a>: </td>
              <td><input type="text" name="ultravendormult5" value="'.$ultra_vendor_mult_5['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ultravendormult6').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ultravendormult6').'</a>: </td>
              <td><input type="text" name="ultravendormult6" value="'.$ultra_vendor_mult_6['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ultravendormult7').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ultravendormult7').'</a>: </td>
              <td><input type="text" name="ultravendormult7" value="'.$ultra_vendor_mult_7['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'ultravendorbase').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'ultravendorbase').'</a>: </td>
              <td><input type="text" name="ultravendorbase" value="'.$ultra_vendor_base['Value'].'" /></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
      }
      else
      {
        $quest_item_vendor_level_mul = $sqlm->quote_smart($_GET['questitemvendorlevelmul']);
        $quest_item_vendor_rew_mul = $sqlm->quote_smart($_GET['questitemvendorrewmul']);
        $ultra_vendor_mult_0 = $sqlm->quote_smart($_GET['ultravendormult0']);
        $ultra_vendor_mult_1 = $sqlm->quote_smart($_GET['ultravendormult1']);
        $ultra_vendor_mult_2 = $sqlm->quote_smart($_GET['ultravendormult2']);
        $ultra_vendor_mult_3 = $sqlm->quote_smart($_GET['ultravendormult3']);
        $ultra_vendor_mult_4 = $sqlm->quote_smart($_GET['ultravendormult4']);
        $ultra_vendor_mult_5 = $sqlm->quote_smart($_GET['ultravendormult5']);
        $ultra_vendor_mult_6 = $sqlm->quote_smart($_GET['ultravendormult6']);
        $ultra_vendor_mult_7 = $sqlm->quote_smart($_GET['ultravendormult7']);
        $ultra_vendor_base = $sqlm->quote_smart($_GET['ultravendorbase']);

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
    case 'internalmap':
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
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="saveinternalmap">
          <input type="hidden" name="subsection" value="internalmap">
          <table class="simple">
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'statusgmincludeall').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'statusgmincludeall').'</a>: </td>
              <td><input type="checkbox" name="statusgmincludeall" '.($map_status_gm_include_all['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <!-- td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'gmshowonlineonlygmoff').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'gmshowonlineonlygmoff').'</a>: </td>
              <td><input type="checkbox" name="gmshowonlineonlygmoff" '.($map_gm_show_online_only_gmoff['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'gmshowonlineonlygmvisible').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'gmshowonlineonlygmvisible').'</a>: </td>
              <td><input type="checkbox" name="gmshowonlineonlygmvisible" '.($map_gm_show_online_only_gmvisible['Value'] == 1 ? 'checked="checked"' : '').' disabled="disabled" /></td>
            </tr -->
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'gmaddsuffix').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'gmaddsuffix').'</a>: </td>
              <td><input type="checkbox" name="gmaddsuffix" '.($map_gm_add_suffix['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <!-- tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'showstatus').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'showstatus').'</a>: </td>
              <td><input type="checkbox" name="showstatus" '.($map_show_status['Value'] == 1 ? 'checked="checked"' : '').' disabled="disabled" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'showtimer').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'showtimer').'</a>: </td>
              <td><input type="checkbox" name="showtimer" '.($map_show_timer['Value'] == 1 ? 'checked="checked"' : '').' disabled="disabled" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'timer').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'timer').'</a>: </td>
              <td><input type="text" name="timer" value="'.$map_timer['Value'].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'showonline').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'showonline').'</a>: </td>
              <td><input type="checkbox" name="showonline" '.($map_show_online['Value'] == 1 ? 'checked="checked"' : '').' disabled="disabled" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'timetoshowuptime').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'timetoshowuptime').'</a>: </td>
              <td><input type="text" name="timetoshowuptime" value="'.$map_time_to_show_uptime['Value'].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'timetoshowmaxonline').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'timetoshowmaxonline').'</a>: </td>
              <td><input type="text" name="timetoshowmaxonline" value="'.$map_time_to_show_maxonline['Value'].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'timetoshowgmonline').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'timetoshowgmonline').'</a>: </td>
              <td><input type="text" name="timetoshowgmonline" value="'.$map_time_to_show_gmonline['Value'].'" readonly="readonly" /></td>
            </tr -->
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
      }
      else
      {
        if ( isset($_GET['gmshowonlineonlygmoff']) )
          $map_gm_show_online_only_gmoff = 1;
        else
          $map_gm_show_online_only_gmoff = 0;
        if ( isset($_GET['gmshowonlineonlygmvisible']) )
          $map_gm_show_online_only_gmvisible = 1;
        else
          $map_gm_show_online_only_gmvisible = 0;
        if ( isset($_GET['gmaddsuffix']) )
          $map_gm_add_suffix = 1;
        else
          $map_gm_add_suffix = 0;
        if ( isset($_GET['statusgmincludeall']) )
          $map_status_gm_include_all = 1;
        else
          $map_status_gm_include_all = 0;
        if ( isset($_GET['showstatus']) )
          $map_show_status = 1;
        else
          $map_show_status = 0;
        if ( isset($_GET['showtimer']) )
          $map_show_timer = 1;
        else
          $map_show_timer = 0;
        $map_timer = $sqlm->quote_smart($_GET['timer']);
        if ( isset($_GET['showonline']) )
          $map_show_online = 1;
        else
          $map_show_online = 0;
        $map_time_to_show_uptime = $sqlm->quote_smart($_GET['timetoshowuptime']);
        $map_time_to_show_maxonline = $sqlm->quote_smart($_GET['timetoshowmaxonline']);
        $map_time_to_show_gmonline = $sqlm->quote_smart($_GET['timetoshowgmonline']);

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
    case 'validip':
    {
      if ( !$sub_action )
      {
        $masks_query = $sqlm->query("SELECT * FROM config_valid_ip_mask");
        $output .= '
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="editvalidip">
          <input type="hidden" name="subsection" value="validip">
          <table class="simple">
            <tr>
              <th width="10%">&nbsp;</th>
              <th width="20%"><center>'.lang('admin', 'index').'</center></th>
              <th>'.lang('admin', 'validipmask').'</th>
            </tr>';
        while ( $mask = $sqlm->fetch_assoc($masks_query) )
        {
          $output .= '
            <tr>
              <td><input type="radio" name="index" value="'.$mask['Index'].'" /></td>
              <td><center>'.$mask['Index'].'</center></td>
              <td>'.$mask['ValidIPMask'].'</td>
            </tr>';
        }
        $output .= '
          </table>
          <input type="submit" name="edit" value="'.lang('admin', 'editipmask').'" />
          <input type="submit" name="add" value="'.lang('admin', 'addipmask').'" />
          <input type="submit" name="delete" value="'.lang('admin', 'deleteipmask').'" />
        </form>';
      }
      elseif ( $sub_action == "editvalidip" )
      {
        if ( isset($_GET['add']) )
        {
          $lim = $sqlm->fetch_assoc($sqlm->query("SELECT MAX(`Index`) FROM config_valid_ip_mask"));
          $lim = $lim['MAX(`Index`)'] + 1;
          $sqlm->query("INSERT INTO config_valid_ip_mask SET `Index`='".$lim."', ValidIPMask=''");
          redirect("admin.php?section=general&subsection=validip");
        }
        elseif ( isset($_GET['delete']) )
        {
          $index = $sqlm->quote_smart($_GET['index']);
          if ( !is_numeric($index) )
            redirect("admin.php?section=general&subsection=validip&error=1");

          $result = $sqlm->query("DELETE FROM config_valid_ip_mask WHERE `Index`='".$index."'");
          redirect("admin.php?section=general&subsection=validip");
        }
        else
        {
          $index = $sqlm->quote_smart($_GET['index']);
          if ( !is_numeric($index) )
            redirect("admin.php?section=general&subsection=validip&error=1");

          $mask = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_valid_ip_mask WHERE `Index`='".$index."'"));
          $output .= '
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="savevalidip">
          <input type="hidden" name="subsection" value="validip">
          <input type="hidden" name="index" value="'.$mask['Index'].'">
          <table class="simple">
            <tr>
              <th width="20%"><center>'.lang('admin', 'index').'</center></th>
              <th id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'validipmask').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'validipmask').'</a></th>
            </tr>
            <tr>
              <td><center>'.$mask['Index'].'</center></td>
              <td><input type="text" name="mask" value="'.$mask['ValidIPMask'].'" /></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
        }
      }
      else
      {
        $index = $sqlm->quote_smart($_GET['index']);
        $mask = $sqlm->quote_smart($_GET['mask']);
        $result = $sqlm->query("UPDATE config_valid_ip_mask SET ValidIPMask='".$mask."' WHERE `Index`='".$index."'");

        redirect("admin.php?section=general&subsection=validip");
      }
    break;
    }
    case 'more':
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
        $gm_online = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='GM_Online'"));
        $gm_online_count = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='GM_Online_Count'"));
        $hide_max_players = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Hide_Max_Players'"));
        $hide_avg_latency = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Hide_Avg_Latency'"));
        $hide_server_mem = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Hide_Server_Mem'"));
        $hide_plr_latency = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Hide_Plr_Latency'"));
        $backup_dir = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Backup_Dir'"));
        $debug = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Debug'"));
        $test_mode = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Test_Mode'"));
        $multi_realm = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Multi_Realm'"));
        $language_locales_search_option = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Language_Locales_Search_Option'"));
        $language_site_encoding = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_misc WHERE `Key`='Language_Site_Encoding'"));
        $output .= '
        <form name="form" action="admin.php" method="GET">
          <input type="hidden" name="section" value="general">
          <input type="hidden" name="subaction" value="savemore">
          <input type="hidden" name="subsection" value="more">
          <table class="simple" id="admin_more">
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'sqlsearchlimit').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'sqlsearchlimit').'</a>: </td>
              <td><input type="text" name="sqlsearchlimit" value="'.$sql_search_limit['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'itemicons').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'itemicons').'</a>: </td>
              <td><input type="text" name="itemicons" value="'.$item_icons['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'remembermechecked').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'remembermechecked').'</a>: </td>
              <td><input type="checkbox" name="remembermechecked" '.($remember_me_checked['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'sitetitle').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'sitetitle').'</a>: </td>
              <td><input type="text" name="sitetitle" value="'.$site_title['Value'].'" size="50"/></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'itemperpage').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'itemperpage').'</a>: </td>
              <td><input type="text" name="itemperpage" value="'.$item_per_page['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'showcountryflags').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'showcountryflags').'</a>: </td>
              <td><input type="checkbox" name="showcountryflags" '.($show_country_flags['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'defaulttheme').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'defaulttheme').'</a>: </td>
              <td><input type="text" name="defaulttheme" value="'.$default_theme['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'defaultlanguage').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'defaultlanguage').'</a>: </td>
              <td><input type="text" name="defaultlanguage" value="'.$default_language['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'timezone').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'timezone').'</a>: </td>
              <td>
                <select name="timezone">
                  <option value="-12.0" '.($timezone['Value'] == "-12.0" ? 'selected="selected"' : '').'>(UTC -12:00) Eniwetok, Kwajalein</option>
                  <option value="-11.0" '.($timezone['Value'] == "-11.0" ? 'selected="selected"' : '').'>(UTC -11:00) Midway Island, Samoa</option>
                  <option value="-10.0" '.($timezone['Value'] == "-10.0" ? 'selected="selected"' : '').'>(UTC -10:00) Hawaii</option>
                  <option value="-9.0" '.($timezone['Value'] == "-9.0" ? 'selected="selected"' : '').'>(UTC -9:00) Alaska</option>
                  <option value="-8.0" '.($timezone['Value'] == "-8.0" ? 'selected="selected"' : '').'>(UTC -8:00) Pacific Time (US &amp; Canada)</option>
                  <option value="-7.0" '.($timezone['Value'] == "-7.0" ? 'selected="selected"' : '').'>(UTC -7:00) Mountain Time (US &amp; Canada)</option>
                  <option value="-6.0" '.($timezone['Value'] == "-6.0" ? 'selected="selected"' : '').'>(UTC -6:00) Central Time (US &amp; Canada), Mexico City</option>
                  <option value="-5.0" '.($timezone['Value'] == "-5.0" ? 'selected="selected"' : '').'>(UTC -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                  <option value="-4.0" '.($timezone['Value'] == "-4.0" ? 'selected="selected"' : '').'>(UTC -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
                  <option value="-3.5" '.($timezone['Value'] == "-3.5" ? 'selected="selected"' : '').'>(UTC -3:30) Newfoundland</option>
                  <option value="-3.0" '.($timezone['Value'] == "-3.0" ? 'selected="selected"' : '').'>(UTC -3:00) Brazil, Buenos Aires, Georgetown</option>
                  <option value="-2.0" '.($timezone['Value'] == "-2.0" ? 'selected="selected"' : '').'>(UTC -2:00) Mid-Atlantic</option>
                  <option value="-1.0" '.($timezone['Value'] == "-1.0" ? 'selected="selected"' : '').'>(UTC -1:00) Azores, Cape Verde Islands</option>
                  <option value="0.0" '.($timezone['Value'] == "0.0" ? 'selected="selected"' : '').'>(UTC) Western Europe Time, London, Lisbon, Casablanca</option>
                  <option value="1.0" '.($timezone['Value'] == "1.0" ? 'selected="selected"' : '').'>(UTC +1:00) Brussels, Copenhagen, Madrid, Paris</option>
                  <option value="2.0" '.($timezone['Value'] == "2.0" ? 'selected="selected"' : '').'>(UTC +2:00) Kaliningrad, South Africa</option>
                  <option value="3.0" '.($timezone['Value'] == "3.0" ? 'selected="selected"' : '').'>(UTC +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
                  <option value="3.5" '.($timezone['Value'] == "3.5" ? 'selected="selected"' : '').'>(UTC +3:30) Tehran</option>
                  <option value="4.0" '.($timezone['Value'] == "4.0" ? 'selected="selected"' : '').'>(UTC +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
                  <option value="4.5" '.($timezone['Value'] == "4.5" ? 'selected="selected"' : '').'>(UTC +4:30) Kabul</option>
                  <option value="5.0" '.($timezone['Value'] == "5.0" ? 'selected="selected"' : '').'>(UTC +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                  <option value="5.5" '.($timezone['Value'] == "5.5" ? 'selected="selected"' : '').'>(UTC +5:30) Bombay, Calcutta, Madras, New Delhi</option>
                  <option value="5.75" '.($timezone['Value'] == "5.75" ? 'selected="selected"' : '').'>(UTC +5:45) Kathmandu</option>
                  <option value="6.0" '.($timezone['Value'] == "6.0" ? 'selected="selected"' : '').'>(UTC +6:00) Almaty, Dhaka, Colombo</option>
                  <option value="7.0" '.($timezone['Value'] == "7.0" ? 'selected="selected"' : '').'>(UTC +7:00) Bangkok, Hanoi, Jakarta</option>
                  <option value="8.0" '.($timezone['Value'] == "8.0" ? 'selected="selected"' : '').'>(UTC +8:00) Beijing, Perth, Singapore, Hong Kong</option>
                  <option value="9.0" '.($timezone['Value'] == "9.0" ? 'selected="selected"' : '').'>(UTC +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                  <option value="9.5" '.($timezone['Value'] == "9.5" ? 'selected="selected"' : '').'>(UTC +9:30) Adelaide, Darwin</option>
                  <option value="10.0" '.($timezone['Value'] == "10.0" ? 'selected="selected"' : '').'>(UTC +10:00) Eastern Australia, Guam, Vladivostok</option>
                  <option value="11.0" '.($timezone['Value'] == "11.0" ? 'selected="selected"' : '').'>(UTC +11:00) Magadan, Solomon Islands, New Caledonia</option>
                  <option value="12.0" '.($timezone['Value'] == "12.0" ? 'selected="selected"' : '').'>(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
</select></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'gmonline').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'gmonline').'</a>: </td>
              <td><input type="checkbox" name="gmonline" '.($gm_online['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'gmonlinecount').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'gmonlinecount').'</a>: </td>
              <td><input type="checkbox" name="gmonlinecount" '.($gm_online_count['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'hidemaxplayers').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'hidemaxplayers').'</a>: </td>
              <td><input type="checkbox" name="hidemaxplayers" '.($hide_max_players['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'hideavglatency').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'hideavglatency').'</a>: </td>
              <td><input type="checkbox" name="hideavglatency" '.($hide_avg_latency['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'hideservermem').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'hideservermem').'</a>: </td>
              <td>
                <select name="hideservermem">
                  <option value="0" '.($hide_server_mem['Value'] == 0 ? 'selected="selected"' : '').'>'.lang('admin', 'hide').'</option>
                  <option value="1" '.($hide_server_mem['Value'] == 1 ? 'selected="selected"' : '').'>'.lang('admin', 'showtogmsonly').'</option>
                  <option value="2" '.($hide_server_mem['Value'] == 2 ? 'selected="selected"' : '').'>'.lang('admin', 'showall').'</option>
                </select>
              </td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'hideplrlatency').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'hideplrlatency').'</a>: </td>
              <td><input type="checkbox" name="hideplrlatency" '.($hide_plr_latency['Value'] == 1 ? 'checked="checked"' : '').' /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'backupdir').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'backupdir').'</a>: </td>
              <td><input type="text" name="backupdir" value="'.$backup_dir['Value'].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'debug').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'debug').'</a>: </td>
              <td><input type="text" name="debug" value="'.$debug['Value'].'" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'testmode').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'testmode').'</a>: </td>
              <td><input type="text" name="testmode" value="'.$test_mode['Value'].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'multirealm').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'multirealm').'</a>: </td>
              <td><input type="text" name="multirealm" value="'.$multi_realm['Value'].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td colspan=2><b>'.lang('admin', 'language').'</b.</td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'languagelocalessearchoption').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'languagelocalessearchoption').'</a>: </td>
              <td><input type="text" name="languagelocalessearchoption" value="'.$language_locales_search_option['Value'].'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'languagesiteencoding').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'languagesiteencoding').'</a>: </td>
              <td><input type="text" name="languagesiteencoding" value="'.$language_site_encoding['Value'].'" /></td>
            </tr>
          </table>
          <input type="submit" name="save" value="'.lang('admin', 'save').'" />
        </form>';
      }
      else
      {
        $sql_search_limit = $sqlm->quote_smart($_GET['sqlsearchlimit']);
        $item_icons = $sqlm->quote_smart($_GET['itemicons']);
        if ( isset($_GET['remembermechecked']) )
          $remember_me_checked = 1;
        else
          $remember_me_checked = 0;
        $site_title = $sqlm->quote_smart($_GET['sitetitle']);
        $item_per_page = $sqlm->quote_smart($_GET['itemperpage']);
        if ( isset($_GET['showcountryflags']) )
          $show_country_flags = 1;
        else
          $show_country_flags = 0;
        $default_theme = $sqlm->quote_smart($_GET['defaulttheme']);
        $default_language = $sqlm->quote_smart($_GET['defaultlanguage']);
        $timezone = $sqlm->quote_smart($_GET['timezone']);
        if ( isset($_GET['gmonline']) )
          $gm_online = 1;
        else
          $gm_online = 0;
        if ( isset($_GET['gmonlinecount']) )
          $gm_online_count = 1;
        else
          $gm_online_count = 0;
        if ( isset($_GET['hidemaxplayers']) )
          $hide_max_players = 1;
        else
          $hide_max_players = 0;
        if ( isset($_GET['hideavglatency']) )
          $hide_avg_latency = 1;
        else
          $hide_avg_latency = 0;
        if ( isset($_GET['hideplrlatency']) )
          $hide_plr_latency = 1;
        else
          $hide_plr_latency = 0;
        $backup_dir = $sqlm->quote_smart($_GET['backupdir']);
        $debug = $sqlm->quote_smart($_GET['debug']);
        $test_mode = $sqlm->quote_smart($_GET['testmode']);
        $multi_realm = $sqlm->quote_smart($_GET['multirealm']);
        $language_locales_search_option = $sqlm->quote_smart($_GET['languagelocalessearchoption']);
        $language_site_encoding = $sqlm->quote_smart($_GET['languagesiteencoding']);
        $hide_server_mem = $sqlm->quote_smart($_GET['hideservermem']);

        $result = $sqlm->query("UPDATE config_misc SET Value='".$sql_search_limit."' WHERE `Key`='SQL_Search_Limit'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$item_icons."' WHERE `Key`='Item_Icons'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$remember_me_checked."' WHERE `Key`='Remember_Me_Checked'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$site_title."' WHERE `Key`='Site_Title'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$item_per_page."' WHERE `Key`='Item_Per_Page'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$show_country_flags."' WHERE `Key`='Show_Country_Flags'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$default_theme."' WHERE `Key`='Default_Theme'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$default_language."' WHERE `Key`='Default_Language'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$timezone."' WHERE `Key`='Timezone'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$gm_online."' WHERE `Key`='GM_Online'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$gm_online_count."' WHERE `Key`='GM_Online_Count'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$hide_max_players."' WHERE `Key`='Hide_Max_Players'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$hide_avg_latency."' WHERE `Key`='Hide_Avg_Latency'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$hide_server_mem."' WHERE `Key`='Hide_Server_Mem'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$hide_plr_latency."' WHERE `Key`='Hide_Plr_Latency'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$backup_dir."' WHERE `Key`='Backup_Dir'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$debug."' WHERE `Key`='Debug'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$test_mode."' WHERE `Key`='Test_Mode'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$multi_realm."' WHERE `Key`='Multi_Realm'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$language_locales_search_option."' WHERE `Key`='Language_Locales_Search_Option'");
        $result = $sqlm->query("UPDATE config_misc SET Value='".$language_site_encoding."' WHERE `Key`='Language_Site_Encoding'");
		
        redirect("admin.php?section=general&subsection=more");
      }
    break;
    }
  }

  $output .= '
      </div>';
}

function gmlevels()
{
  global $output, $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $gm_lvls = $sqlm->query("SELECT * FROM config_gm_level_names");

  if ( !isset($_GET['edit_btn']) )
  {
    $output .= '
        <center>
          <form name="form" action="admin.php" method="GET">
            <input type="hidden" name="section" value="gmlevels">
            <table class="simple">
              <tr>
                <th>Edit</th>
                <th>Security Level</th>
                <th>Full Name</th>
                <th>Short Name</th>
              </tr>';
    $color = "#EEEEEE";
    while( $gm_lvl = $sqlm->fetch_assoc($gm_lvls) )
    {
      $output .= '
              <tr>
                <td style="background-color:'.$color.'"><input type="radio" name="edit" value="'.$gm_lvl['Index'].'"></td>
                <td style="background-color:'.$color.'">'.$gm_lvl['Security_Level'].'</td>
                <td style="background-color:'.$color.'">'.$gm_lvl['Full_Name'].'</td>
                <td style="background-color:'.$color.'">'.$gm_lvl['Short_Name'].'</td>
              </td>
          ';
      if ( $color == "#EEEEEE" )
        $color = "#FFFFFF";
      else
        $color = "#EEEEEE";
    }

    $output .= '
            </table>
            <input type="checkbox" name="addrow">'.lang('admin', 'addrow').'
            <input type="checkbox" name="delrow">'.lang('admin', 'delrow').'
            <br />
            <input type="submit" name="edit_btn" value="'.lang('admin', 'edit').'">
          </form>
        </center>';
    }
    else
    {
      if ( !isset($_GET['edit']) )
        if ( !isset($_GET['addrow']) )
          redirect("admin.php?section=gmlevels");

      if ( isset($_GET['delrow']) )
        $del_row = $_GET['delrow'];
      else
        $del_row = "";

      if ( isset($_GET['addrow']) )
        $add_row = $_GET['addrow'];
      else
        $add_row = "";

      if ( $add_row )
      {
        $add_result = $sqlm->query("INSERT INTO config_gm_level_names (Security_Level) VALUES ('-1')");
        redirect("admin.php?section=gmlevels");
      }

      if ( $del_row )
      {
        $del_result = $sqlm->query("DELETE FROM config_gm_level_names WHERE `Index` = '".$_GET['edit']."'");
        redirect("admin.php?section=gmlevels");
      }

      $gm_level = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_gm_level_names WHERE `Index` = '".$_GET['edit']."'"));
      $output .= '
        <center>
          <form name="form" action="admin.php" method="GET">
            <input type="hidden" name="section" value="gmlevels">
            <input type="hidden" name="action" value="savegms">
            <input type="hidden" name="index" value="'.$gm_level['Index'].'">
            <fieldset id="admin_gm_level">
            <table>
              <tr>
                <td width="45%" id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'gmlvl').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'gmlvl').'</a>: </td>
                <td><input type="text" name="gmlvl" value="'.$gm_level['GM_Level'].'"></td>
              </tr>
              <tr>
                <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'seclvl').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'seclvl').'</a>: </td>
                <td><input type="text" name="seclvl" value="'.$gm_level['Security_Level'].'"></td>
              </tr>
              <tr>
                <td>'.lang('admin', 'fullname').': </td>
                <td><input type="text" name="fullname" value="'.$gm_level['Full_Name'].'"></td>
              </tr>
              <tr>
                <td>'.lang('admin', 'shortname').': </td>
                <td><input type="text" name="shortname" value="'.$gm_level['Short_Name'].'"></td>
              </tr>
            </table>
            </fieldset>
            <input type="submit" name="save" value="'.lang('admin', 'save').'">
          </form>
        </center>';
    }
}

function savegms()
{
  global $output, $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $index = $sqlm->quote_smart($_GET['index']);
  $sec_lvl = $sqlm->quote_smart($_GET['seclvl']);
  $full_name = $sqlm->quote_smart($_GET['fullname']);
  $short_name = $sqlm->quote_smart($_GET['shortname']);

  $result = $sqlm->query("UPDATE config_gm_level_names SET Security_Level = '".$sec_lvl."', Full_Name = '".$full_name."', Short_Name = '".$short_name."' WHERE `Index` = '".$index."'");
  redirect("admin.php?section=gmlevels");
}

function servers()
{
  global $output, $corem_db, $lang_global;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $result = $sqlm->query("SELECT * FROM config_servers");

  $server_action = 0;
  if ( isset($_GET['editserver']) )
    $server_action = 'edit';
  if ( isset($_GET['delserver']) )
    $server_action = 'del';
  if ( isset($_GET['addserver']) )
    $server_action = 'add';

  if ( !$server_action )
  {
    $output .= '
        <center>
          <form name="form" action="admin.php" method="GET">
            <input type="hidden" name="section" value="servers">
            <table class="simple" id="admin_servers">
              <tr>
                <th width="1%">&nbsp;</th>
                <th width="40%">'.lang('admin', 'host').'</th>
                <th width="1%">'.lang('admin', 'port').'</th>
                <th width="10%">'.lang('admin', 'bothfactions').'</th>
                <th width="40%">'.lang('admin', 'statsxml').'</th>
              </tr>';
    $color = "#EEEEEE";
    while ( $server = $sqlm->fetch_assoc($result) )
    {
      $output .= '
              <tr>
                <td style="background-color:'.$color.'"><input type="radio" name="sel_server" value="'.$server['Index'].'"></td>
                <td style="background-color:'.$color.'"><center>'.$server['Address'].'</center></td>
                <td style="background-color:'.$color.'"><center>'.$server['Port'].'</center></td>
                <td style="background-color:'.$color.'"><center>'.$server['Both_Factions'].'</center></td>
                <td style="background-color:'.$color.'"><center>'.$server['Stats_XML'].'</center></td>
              </tr>';
      if ( $color == "#EEEEEE" )
        $color = "#FFFFFF";
      else
        $color = "#EEEEEE";
    }

    $output .= '
            </table>
            <input type="submit" name="editserver" value="'.lang('admin', 'editserver').'">
            <input type="submit" name="addserver" value="'.lang('admin', 'addserver').'">
            <input type="submit" name="delserver" value="'.lang('admin', 'delserver').'">
          </form>
        </center>';
  }
  else
  {
    if ( $server_action == 'edit' )
    {
      $server_id = $sqlm->quote_smart($_GET['sel_server']);
      if ( is_numeric($server_id) )
      {
        $server = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_servers WHERE `Index`='".$server_id."'"));
        $output .= '
        <center>
          <form name="form" action="admin.php" method="GET">
            <fieldset id="admin_edit_server">
              <input type="hidden" name="section" value="servers">
              <input type="hidden" name="action" value="saveserver">
              <input type="hidden" name="index" value="'.$server['Index'].'">
              <table>
                <tr>
                  <td width="45%">'.lang('admin', 'host').': </td>
                  <td><input type="text" name="server_host" value="'.$server['Address'].'"></td>
                </tr>
                <tr>
                  <td>'.lang('admin', 'port').': </td>
                  <td><input type="text" name="server_port" value="'.$server['Port'].'"></td>
                </tr>
                <tr>
                  <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'bothfactions').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'bothfactions').'</a>: </td>
                  <td><input type="text" name="server_both" value="'.$server['Both_Factions'].'"></td>
                </tr>
                <tr>
                  <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'statsxml').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'statsxml').'</a>: </td>
                  <td><input type="text" name="server_stats" value="'.$server['Stats_XML'].'"></td>
                </tr>
              </table>
            </fieldset>
            <input type="submit" name="saveserver" value="'.lang('admin', 'save').'">
          </form>
        </center>';
      }
      else
        redirect("admin.php?section=servers&error=1");
    }
    elseif ( $server_action == 'del' )
    {
      $server_id = $sqlm->quote_smart($_GET['sel_server']);
      if ( is_numeric($server_id) )
      {
        $result = $sqlm->query("DELETE FROM config_servers WHERE `Index` = '".$server_id."'");
        redirect("admin.php?section=servers");
      }
      else
        redirect("admin.php?section=servers&error=1");
    }
    else
    {
      $result = $sqlm->query("INSERT INTO config_servers (Port, Both_Factions) VALUES (8129,1)");
      redirect("admin.php?section=servers");
    }
  }
}

function saveserver()
{
  global $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $server_id = $sqlm->quote_smart($_GET['index']);
  $server_host = $sqlm->quote_smart($_GET['server_host']);
  $server_port = $sqlm->quote_smart($_GET['server_port']);
  $server_factions = $sqlm->quote_smart($_GET['server_both']);
  $server_stats = $sqlm->quote_smart($_GET['server_stats']);

  $result = $sqlm->query("UPDATE config_servers SET Address='".$server_host."', Port='".$server_port."', Both_Factions='".$server_factions."', Stats_XML='".$server_stats."' WHERE `Index`='".$server_id."'");
  redirect("admin.php?section=servers");
}

function menus()
{
  global $output, $corem_db, $lang_global;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $menu_action = 'start';
  if ( isset($_GET['editmenu']) )
    $menu_action = 'edit';
  if ( isset($_GET['delmenu']) )
    $menu_action = 'delmenu';
  if ( isset($_GET['addmenu']) )
    $menu_action = 'addmenu';
  if ( isset($_GET['editmenu_item']) )
    $menu_action = 'edititem';
  if ( isset($_GET['delmenu_item']) )
    $menu_action = 'delitem';
  if ( isset($_GET['addmenu_item']) )
    $menu_action = 'additem';
  if ( isset($_GET['savemenu']) )
    $menu_action = 'savemenu';

  switch ( $menu_action )
  {
    case "start";
    {
      $top_menus = $sqlm->query("SELECT * FROM config_top_menus");
      $output .= '
        <center>
          <form name="form" action="admin.php" method="GET">
            <input type="hidden" name="section" value="menus">
            <table class="simple" id="admin_top_menus">
              <tr>
                <th>&nbsp;</th>
                <th>'.lang('admin', 'name').': </th>
                <th>'.lang('admin', 'action').': </th>
              </tr>';
      $color = "#EEEEEE";
      while ( $top_menu = $sqlm->fetch_assoc($top_menus) )
      {
        $output .= '
              <tr>
                <td style="background-color:'.$color.'"><input type="radio" name="top_menu" value="'.$top_menu['Index'].'"></td>
                <td style="background-color:'.$color.'"><center>'.$top_menu['Name'].'</center></td>
                <td style="background-color:'.$color.'"><center>'.$top_menu['Action'].'</center></td>
              </tr>';
        if ( $color == "#EEEEEE" )
          $color = "#FFFFFF";
        else
          $color = "#EEEEEE";
      }
      $output .= '
            </table>
            <input type="submit" name="editmenu" value="'.lang('admin', 'editmenu').'">
            <input type="submit" name="addmenu" value="'.lang('admin', 'addmenu').'">
            <input type="submit" name="delmenu" value="'.lang('admin', 'delmenu').'">
          </form>
        </center>';
      break;
    }
    case 'edit':
    {
      $top_menu = $sqlm->quote_smart($_GET['top_menu']);
      $top = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_top_menus WHERE `Index`='".$top_menu."'"));
      $output .= '
        <center>
          <form name="form" action="admin.php" method="GET">
            <input type="hidden" name="section" value="menus">
            <input type="hidden" name="top_index" value="'.$top_menu.'">
            <table class="simple" id="admin_edit_top_menu_nameaction">
              <tr>
                <th colspan=2>'.lang('admin', 'top_menu').'</th>
              </tr>
              <tr>
                <td>'.lang('admin', 'name').': </td>
                <td><input type="text" name="top_name" value="'.$top['Name'].'" id="admin_edit_top_menu_action"></td>
              </tr>
              <tr>
                <td>'.lang('admin', 'action').': </td>
                <td><textarea name="menu_action" id="admin_edit_top_menu_action" rows="2">'.$top['Action'].'</textarea></td>
              </tr>
            </table>
            <table class="simple" id="admin_edit_top_menu_submenus">
              <tr>
                <th>&nbsp;</th>
                <th>'.lang('admin', 'name').'</th>
                <th>'.lang('admin', 'action').'</th>
                <th>'.lang('admin', 'view').'</th>
                <th>'.lang('admin', 'insert').'</th>
                <th>'.lang('admin', 'update').'</th>
                <th>'.lang('admin', 'delete').'</th>
                <th>'.lang('admin', 'enabled').'</th>
              </tr>';
      $menus = $sqlm->query("SELECT * FROM config_menus WHERE Menu='".$top_menu."'");
      $color = "#EEEEEE";
      while ( $menu = $sqlm->fetch_assoc($menus) )
      {
        $output .= '
              <tr>
                <td style="background-color:'.$color.'"><input type="radio" name="menu_item" value="'.$menu['Index'].'"></td>
                <td width="15%" style="background-color:'.$color.'"><center>'.$menu['Name'].'</center></td>
                <td width="35%" style="background-color:'.$color.'"><center>'.$menu['Action'].'</center></td>
                <td style="background-color:'.$color.'"><center>'.sec_level_name($menu['View']).' ('.$menu['View'].')'.'</center></td>
                <td style="background-color:'.$color.'"><center>'.sec_level_name($menu['Insert']).' ('.$menu['Insert'].')'.'</center></td>
                <td style="background-color:'.$color.'"><center>'.sec_level_name($menu['Update']).' ('.$menu['Update'].')'.'</center></td>
                <td style="background-color:'.$color.'"><center>'.sec_level_name($menu['Delete']).' ('.$menu['Delete'].')'.'</center></td>
                <td style="background-color:'.$color.'"><center>'.($menu['Enabled'] ? '<img src="img/up.gif">' : '<img src="img/down.gif">').'</center></td>
              </tr>';
        if ( $color == "#EEEEEE" )
          $color = "#FFFFFF";
        else
          $color = "#EEEEEE";
      }
      $output .= '
            </table>
            <input type="submit" name="editmenu_item" value="'.lang('admin', 'editmenu_item').'">
            <input type="submit" name="addmenu_item" value="'.lang('admin', 'addmenu_item').'">
            <input type="submit" name="savemenu" value="'.lang('admin', 'save').'">
            <input type="submit" name="delmenu_item" value="'.lang('admin', 'delmenu_item').'">
          </form>
        </center>';
      break;
    }
    case 'edititem':
    {
      $menu_item = $sqlm->quote_smart($_GET['menu_item']);
      $menu = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_menus WHERE `Index`='".$menu_item."'"));
      $sec_list = sec_level_list();
      $output .= '
        <center>
          <form name="form" action="admin.php" method="GET">
            <input type="hidden" name="section" value="menus">
            <input type="hidden" name="action" value="savemenu">
            <input type="hidden" name="menu_item" value="'.$menu_item.'">
            <fieldset id="admin_edit_menu_field">
              <table id="help" id="admin_edit_menu">
                <tr>
                  <td><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'menu').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'menu').'</a>: </td>
                  <td><input type="text" name="menu" value="'.$menu['Menu'].'" id="admin_edit_menu_fields"></td>
                </tr>
                <tr>
                  <td><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'menuname').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'name').'</a>: </td>
                  <td><input type="text" name="name" value="'.$menu['Name'].'" id="admin_edit_menu_fields"></td>
                </tr>
                <tr>
                  <td><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'action').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'action').'</a>: </td>
                  <td><textarea name="menu_action" style="width:260px" rows="2">'.$menu['Action'].'</textarea></td>
                </tr>
                <tr>
                  <td><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'view').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'view').'</a>: </td>
                  <td>
                    <select name="view">';
      foreach ($sec_list as $row)
      {
        $output .= '
                      <option value="'.$row['Sec'].'" '.($row['Sec'] == $menu['View'] ? 'selected="selected"' : '').'>'.$row['Name'].' ('.$row['Sec'].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'insert').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'insert').'</a>: </td>
                  <td>
                    <select name="insert">';
      foreach ($sec_list as $row)
      {
        $output .= '
                      <option value="'.$row['Sec'].'" '.($row['Sec'] == $menu['Insert'] ? 'selected="selected"' : '').'>'.$row['Name'].' ('.$row['Sec'].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'update').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'update').'</a>: </td>
                  <td>
                    <select name="update">';
      foreach ($sec_list as $row)
      {
        $output .= '
                      <option value="'.$row['Sec'].'" '.($row['Sec'] == $menu['Update'] ? 'selected="selected"' : '').'>'.$row['Name'].' ('.$row['Sec'].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>
                <tr>
                  <td><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'delete').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'delete').'</a>: </td>
                  <td>
                    <select name="delete">';
      foreach ($sec_list as $row)
      {
        $output .= '
                      <option value="'.$row['Sec'].'" '.($row['Sec'] == $menu['Delete'] ? 'selected="selected"' : '').'>'.$row['Name'].' ('.$row['Sec'].')</option>';
      }
      $output .= '
                    </select>
                  </td>
                </tr>';
      if ( $menu_item <> 8 )
        $output .= '
                <tr>
                  <td><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'enabled').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'enabled').'</a>: </td>
                  <td><input type="checkbox" name="enabled" '.($menu['Enabled'] ? 'CHECKED' : '').'></td>
                </tr>';
      else
        $output .= '
                <tr>
                  <td><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'enabled').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'enabled').'</a>: </td>
                  <td><input type="checkbox" name="enabled" '.($menu['Enabled'] ? 'CHECKED' : '').' disabled="disabled"></td>
                </tr>';
      $output .= '
              </table>
            </fieldset>
            <input type="submit" name="save_menu_item" value="'.lang('admin', 'save').'">
          </form>
        </center>';
      break;
    }
    case "addmenu":
    {
      $max = $sqlm->fetch_assoc($sqlm->query("SELECT MAX(`Index`) FROM config_top_menus"));
      $max = $max['MAX(`Index`)'] + 1;
      $result = $sqlm->query("INSERT INTO config_top_menus (`Index`, Action, Name) VALUES ('".$max."', '','')");
      redirect("admin.php?section=menus");
      break;
    }
    case "delmenu":
    {
      $top_menu = $sqlm->quote_smart($_GET['top_menu']);
      if ( is_numeric($top_menu) )
      {
        $result = $sqlm->query("DELETE FROM config_top_menus WHERE `Index`='".$top_menu."'");
        redirect("admin.php?section=menus");
      }
      else
        redirect("admin.php?section=menus&error=1");
      break;
    }
    case "savemenu":
    {
      $top_index = $sqlm->quote_smart($_GET['top_index']);
      $top_name = $sqlm->quote_smart($_GET['top_name']);
      $top_action = $sqlm->quote_smart($_GET['top_action']);
      $result = $sqlm->query("UPDATE config_top_menus SET Name='".$top_name."', Action='".$top_action."' WHERE `Index`='".$top_index."'");
      redirect("admin.php?section=menus");
      break;
    }
    case "additem":
    {
      $top_index = $sqlm->quote_smart($_GET['top_index']);
      $result = $sqlm->query("INSERT INTO config_menus (Menu, Action, Name) VALUES ('".$top_index."', '','')");
      redirect("admin.php?section=menus");
      break;
    }
    case "delitem":
    {
      $menu_item = $sqlm->quote_smart($_GET['menu_item']);
      if ( is_numeric($menu_item) )
      {
        $result = $sqlm->query("DELETE FROM config_menus WHERE `Index`='".$menu_item."'");
        redirect("admin.php?section=menus");
      }
      else
        redirect("admin.php?section=menus&error=1");
      break;
    }
    default:
      redirect("admin.php?section=menus&error=1");
      break;
  }
}

function savemenu()
{
  global $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $menu_item = $sqlm->quote_smart($_GET['menu_item']);
  $menu = $sqlm->quote_smart($_GET['menu']);
  $name = $sqlm->quote_smart($_GET['name']);
  $action = $sqlm->quote_smart($_GET['menu_action']);
  $view = $sqlm->quote_smart($_GET['view']);
  $insert = $sqlm->quote_smart($_GET['insert']);
  $update = $sqlm->quote_smart($_GET['update']);
  $delete = $sqlm->quote_smart($_GET['delete']);
  if ( isset($_GET['enabled']) )
    $enabled = 1;
  else
    $enabled = 0;

  $result = $sqlm->query("SELECT * FROM config_menus WHERE `Index`='".$menu_item."'");
  if ( $sqlm->num_rows($result) )
    $result = $sqlm->query("UPDATE config_menus SET Menu='".$menu."', Name='".$name."', Action='".$action."', View='".$view."', `Insert`='".$insert."', `Update`='".$update."', `Delete`='".$delete."', Enabled='".$enabled."' WHERE `Index`='".$menu_item."'");
  else
    $result = $sqlm->query("INSERT INTO config_menus (Menu, Name, Action, View, Insert, Update, Delete, Enabled) VALUES ('".$menu."', '".$name."', '".$action."', '".$view."', '".$insert."', '".$update."', '".$delete."', '".$enabled."')");

  redirect("admin.php?section=menus");
}

function forum()
{
  global $output, $corem_db, $logon_db, $lang_global;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $forum_action = 0;
  if ( isset($_GET['editforum']) )
    $forum_action = 'edit';
  if ( isset($_GET['saveforum']) )
    $forum_action = 'save';
  if ( isset($_GET['delforum']) )
    $forum_action = 'del';

  $forums = $sqlm->query("SELECT * FROM config_lang_forum");

  if ( !$forum_action )
  {
    $output .= '
        <center>
          <form name="form" action="admin.php" method="GET">
            <input type="hidden" name="section" value="forum">
            <table class="simple" id="admin_forum_lang">
              <tr>
                <th width="5%">&nbsp;</th>
                <th width="10%">'.lang('admin', 'key').'</th>
                <th width="10%">'.lang('admin', 'lang').'</th>
                <th width="50%">'.lang('admin', 'value').'</th>
              </tr>';
    $color = "#EEEEEE";
    while ( $forum = $sqlm->fetch_assoc($forums) )
    {
      $output .= '
              <tr>
                <td style="background-color:'.$color.'"><input type="radio" name="forum" value="'.$forum['Key']."^^".$forum['Lang'].'"></td>
                <td style="background-color:'.$color.'"><center>'.$forum['Key'].'</center></td>
                <td style="background-color:'.$color.'"><center>'.$forum['Lang'].'</center></td>
                <td style="background-color:'.$color.'"><center>'.$forum['Value'].'</center></td>
              </tr>';
      if ( $color == "#EEEEEE" )
        $color = "#FFFFFF";
      else
        $color = "#EEEEEE";
    }
    $output .= '
            </table>
            <input type="submit" name="editforum" value="'.lang('admin', 'editforum').'">
            <input type="submit" name="delforum" value="'.lang('admin', 'delforum').'">
          </form>
        </center>';
  }
  elseif ( $forum_action == 'edit' )
  {
    if ( isset($_GET['forum']) )
      $forum_KL = $sqlm->quote_smart($_GET['forum']);
    else
      redirect("admin.php?section=forum&error=1");
    $keylang = explode("^^", $forum_KL);
    $forum = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_lang_forum WHERE `Key`='".$keylang[0]."' AND `Lang`='".$keylang[1]."'"));

    $output .= '
        <center>
          <span>'.lang('admin', 'foruminfo').'</span>
          <br />
          <br />
          <form name="form" action="admin.php" method="GET">
            <input type="hidden" name="section" value="forum">
            <fieldset id="admin_edit_forum_lang">
              <table>
                <tr>
                  <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'key').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'key').'</a>: </td>
                  <td><input type="text" name="key" value="'.$forum['Key'].'">
                </tr>
                <tr>
                  <td>'.lang('admin', 'lang').': </td>
                  <td><input type="text" name="lang" value="'.$forum['Lang'].'">
                </tr>
                <tr>
                  <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'value').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'value').'</a>: </td>
                  <td><textarea name="value" cols="40" rows="3" />'.htmlentities($forum['Value']).'</textarea>
                </tr>
              </table>
            </fieldset>
            <input type="submit" name="saveforum" value="'.lang('admin', 'save').'">
          </form>
        </center>';
  }
  elseif ( $forum_action == 'save' )
  {
    $key = $sqlm->quote_smart($_GET['key']);
    $lang = $sqlm->quote_smart($_GET['lang']);
    $value = $sqlm->quote_smart($_GET['value']);

    if ( strstr($key, "^") )
      redirect("admin.php?section=forum&error=2");
    if ( strstr($lang, "^") )
      redirect("admin.php?section=forum&error=2");
    if ( strstr($value, "^") )
      redirect("admin.php?section=forum&error=2");

    $count = $sqlm->num_rows($sqlm->query("SELECT * FROM config_lang_forum WHERE `Key`='".$key."' AND `Lang`='".$lang."'"));
    if ( $count == 1 )
      $result = $sqlm->query("UPDATE config_lang_forum SET `Value`='".$value."' WHERE `Key`='".$key."' AND `Lang`='".$lang."'");
    else
      $result = $sqlm->query("INSERT INTO config_lang_forum (`Key`,`Lang`,`Value`) VALUES ('".$key."', '".$lang."', '".$value."')");

    redirect("admin.php?section=forum");
  }
  elseif ( $forum_action == 'del' )
  {
    if ( isset($_GET['forum']) )
      $forum_KL = $sqlm->quote_smart($_GET['forum']);
    else
      redirect("admin.php?section=forum&error=1");
    $keylang = explode("^^", $forum_KL);
    
    $count = $sqlm->num_rows($sqlm->query("SELECT * FROM config_lang_forum WHERE `Key`='".$keylang[0]."' AND `Lang`='".$keylang[1]."'"));
    if ( $count == 1 )
      $result = $sqlm->query("DELETE FROM config_lang_forum WHERE `Key`='".$keylang[0]."' AND `Lang`='".$keylang[1]."'");
    else
      redirect("admin.php?section=forum&error=1");

    redirect("admin.php?section=forum");
  }
  else
    redirect("admin.php?section=forum&error=1");
}

function accounts()
{
  global $output, $corem_db, $logon_db, $lang_global, $core;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $sqll = new SQL;
  $sqll->connect($logon_db['addr'], $logon_db['user'], $logon_db['pass'], $logon_db['name']);

  if ( $core == 1 )
    $result = $sqll->query("SELECT * FROM accounts");
  else
    $result = $sqll->query("SELECT *, username AS login FROM account");

  $accounts_action = 0;
  if ( isset($_GET['editacct']) )
    $accounts_action = 'edit';

  if ( !$accounts_action )
  {
    $output .= '
        <center>
          <form name="form" action="admin.php" method="GET">
            <input type="hidden" name="section" value="accounts">
            <table class="simple" id="admin_accounts">
              <tr>
                <th width="5%">&nbsp;</th>
                <th>'.lang('admin', 'login').'</th>
                <th>'.lang('admin', 'screenname').'</th>
                <th width="20%">'.lang('admin', 'seclvl').'</th>
                <th width="15%">'.lang('admin', 'acpaccess').'</th>
              </tr>';
    $color = "#EEEEEE";
    while ( $acct = $sqll->fetch_assoc($result) )
    {
      $acct_result = $sqlm->query("SELECT * FROM config_accounts WHERE Login='".$acct['login']."'");
      $sn_web = $sqlm->fetch_assoc($acct_result);
      $output .= '
              <tr>
                <td style="background-color:'.$color.'"><input type="radio" name="acct" value="'.$acct['login'].'"></td>
                <td style="background-color:'.$color.'"><center>'.ucfirst(strtolower($acct['login'])).'</center></td>
                <td style="background-color:'.$color.'"><center>'.$sn_web['ScreenName'].'</center></td>
                <td style="background-color:'.$color.'"><center>'.$sn_web['SecurityLevel'].'</center></td>
                <td style="background-color:'.$color.'"><center>'.($sn_web['WebAdmin'] ? '<img src="img/up.gif">' : '<img src="img/down.gif">').'</center></td>
              </tr>';
      if ( $color == "#EEEEEE" )
        $color = "#FFFFFF";
      else
        $color = "#EEEEEE";
    }
    $output .= '
            </table>
            <input type="submit" name="editacct" value="'.lang('admin', 'editacct').'">
          </form>
        </center>';
  }
  else
  {
    if ( isset($_GET['acct']) )
      $acct = $sqlm->quote_smart($_GET['acct']);
    else
      redirect("admin.php?section=accounts&error=1");

    if ( $core == 1 )
      $logon_acct = $sqll->fetch_assoc($sqll->query("SELECT * FROM accounts WHERE login='".$acct."'"));
    else
      $logon_acct = $sqll->fetch_assoc($sqll->query("SELECT *, username AS login FROM account WHERE username='".$acct."'"));

    $sn_acct = $sqlm->fetch_assoc($sqlm->query("SELECT * FROM config_accounts WHERE Login='".$acct."'"));
    $output .= '
        <center>
          <form name="form" action="admin.php" method="GET">
            <input type="hidden" name="section" value="accounts">
            <input type="hidden" name="action" value="saveacct">
            <fieldset id="admin_edit_account">
              <table>
                <tr>
                  <td width="50%">'.lang('admin', 'login').': </td>
                  <td><input type="text" readonly="readonly" name="login" value="'.$logon_acct['login'].'">
                </tr>
                <tr>
                  <td>'.lang('admin', 'screenname').': </td>
                  <td><input type="text" name="sn" value="'.$sn_acct['ScreenName'].'">
                </tr>
                <tr>
                  <td>'.lang('admin', 'seclvl').': </td>
                  <td><input type="text" name="sec" value="'.$sn_acct['SecurityLevel'].'">
                </tr>
                <tr>
                  <td id="help"><a href="#" onmouseover="oldtoolTip(\''.lang('admin_tip', 'acpaccess').'\',\'info_tooltip\')" onmouseout="oldtoolTip()">'.lang('admin', 'acpaccess').'</a>: </td>
                  <td><input type="checkbox" name="acp" '.($sn_acct['WebAdmin'] ? 'checked' : '').'>
                </tr>
              </table>
            </fieldset>
            <input type="submit" name="saveacct" value="'.lang('admin', 'save').'">
          </form>
        </center>';
  }
}

function saveacct()
{
  global $corem_db;

  $sqlm = new SQL;
  $sqlm->connect($corem_db['addr'], $corem_db['user'], $corem_db['pass'], $corem_db['name']);

  $acct = $sqlm->quote_smart($_GET['login']);
  $sn = $sqlm->quote_smart($_GET['sn']);
  $sec = $sqlm->quote_smart($_GET['sec']);
  if ( isset($_GET['acp']) )
    $acp = 1;
  else
    $acp = 0;
    
  if ( !isset($_GET['sec']) )
    $sec = 0;

  $result = $sqlm->query("SELECT * FROM config_accounts WHERE Login='".$acct."'");
  if ( $sqlm->num_rows($result) )
    $result = $sqlm->query("UPDATE config_accounts SET ScreenName='".$sn."', WebAdmin='".$acp."', SecurityLevel='".$sec."' WHERE Login='".$acct."'");
  else
    $result = $sqlm->query("INSERT INTO config_accounts (Login, ScreenName, WebAdmin) VALUES ('".$acct."', '".$sn."', '".$acp."')");

  redirect("admin.php?section=accounts");
}


//#############################################################################
// Fix reditection error under MS-IIS fuckedup-servers.
function redirect($url)
{
  if ( strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') === false )
  {
    header('Location: '.$url);
    exit();
  }
  else
    die('<meta http-equiv="refresh" content="0;URL='.$url.'" />');
}


//#############################################################################
// MAIN
//#############################################################################
$err = ( ( isset($_GET['error']) ) ? $_GET['error'] : NULL );

$output .= "
        <div class='top'>";
$output .= "
          <center>
            <h1>".lang('admin', 'title')."</h1>";

switch ( $err )
{
  case 1:
    $output .= "
            <h1><font class=\"error\">".$lang_global['empty_fields']."</font></h1>";
    break;
  case 2:
    $output .= "
            <h1><font class=\"error\">".lang('admin', 'nocarets')."</font></h1>";
    break;
 default:
    $output .= "
            <h1></h1>";
}

$output .= "
          </center>
        </div>
		<br />
		<br />";

unset($err);

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ( $action )
{
  case "savedbs":
    savedbs();
    break;
  case "savegms":
    savegms();
    break;
  case "saveserver":
    saveserver();
    break;
  case "saveacct":
    saveacct();
    break;
  case "savemenu":
    savemenu();
    break;
}

$section = (isset($_GET['section'])) ? $_GET['section'] : NULL;

switch ( $section )
{
  case "gmlevels":
    gmlevels();
    break;
  case "databases":
    database();
    break;
  case "servers":
    servers();
    break;
  case "menus":
    menus();
    break;
  case "forum":
    forum();
    break;
  case "accounts":
    accounts();
    break;
  default:
    general();
    break;
}

require_once("admin/footer.php");
?>
