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

//###########################################################################
// print mail form
function print_mail_form()
{
  global $output, $action_permission;
  
  valid_login($action_permission["update"]);

  $to = ( ( isset($_GET["to"]) ) ? $_GET["to"] : NULL );
  $type = ( ( isset($_GET["type"]) ) ? $_GET["type"] : "ingame_mail" );

  $output .= '
        <center>
          <form action="mail.php" method="get" name="form">
            <input type="hidden" name="action" value="send_mail" />
            <input type="hidden" name="type" value="'.$type.'" />
            <div id="tab">
              <ul>
                <li '.( ( $type == "ingame_mail" ) ? 'id="selected"' : '' ).'><a href="mail.php?to='.$to.'&amp;type=ingame_mail">'.lang("mail", "ingame_mail").'</a></li>
                <li '.( ( $type == "email" ) ? 'id="selected"' : '' ).'><a href="mail.php?to='.$to.'&amp;type=email">'.lang("mail", "email").'</a></li>
              </ul>
            </div>
            <div id="tab_content">
              <div id="mail_type_field" class="fieldset_border">
                <span class="legend">'.lang("mail", "mail_type").'</span>
                <table class="top_hidden" id="mail_type">
                  <tr>
                    <td align="left">'.lang("mail", "recipient").': 
                      <input type="text" name="to" size="32" value="'.$to.'" maxlength="225" />
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3">
                      <hr />
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3">'.lang("mail", "dont_use_both_groupsend_and_to").'</td>
                  </tr>
                  <tr>
                    <td colspan="3">'.lang("mail", "group_send").':
                      <select name="group_send">
                          <option value="gm_level">'.lang("mail", "gm_level").'</option>';

  if ( $type == "email" )
    $output .= '
                          <option value="locked">'.lang("mail", "locked_accouns").'</option>
                          <option value="banned">'.lang("mail", "banned_accounts").'</option>';

  if ( $type == "ingame_mail" )
    $output .= '
                          <option value="char_level">'.lang("mail", "char_level").'</option>
                          <option value="online">'.lang("mail", "online").'</option>';

  $output .= '
                      </select>
                      <select name="group_sign">
                        <option value="=">=</option>
                        <option value="&lt;">&lt;</option>
                        <option value=">">&gt;</option>
                        <option value="!=">!=</option>
                      </select>
                      <input type="text" name="group_value" size="20" maxlength="40" />
                    </td>
                  </tr>';

  if ( $type == "ingame_mail" )
  {
    $output .= '
                  <tr>
                    <td colspan="3">
                      <hr />
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" align="left">'.lang("mail", "attachments").':</td>
                  </tr>
                  <tr>
                    <td colspan="3">'
                      .lang("mail", "money").': <input type="text" name="money" value="0" size="10" maxlength="10" />
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3">
                      <table class="top_hidden" id="mail_items">
                        <tr>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item1" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack1" value="0" size="10" maxlength="10" />
                          </td>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item2" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack2" value="0" size="10" maxlength="10" />
                          </td>
                        </tr>
                        <tr>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item3" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack3" value="0" size="10" maxlength="10" />
                          </td>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item4" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack4" value="0" size="10" maxlength="10" />
                          </td>
                        </tr>
                        <tr>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item5" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack5" value="0" size="10" maxlength="10" />
                          </td>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item6" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack6" value="0" size="10" maxlength="10" />
                          </td>
                        </tr>
                        <tr>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item7" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack7" value="0" size="10" maxlength="10" />
                          </td>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item8" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack8" value="0" size="10" maxlength="10" />
                          </td>
                        </tr>
                        <tr>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item9" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack9" value="0" size="10" maxlength="10" />
                          </td>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item10" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack10" value="0" size="10" maxlength="10" />
                          </td>
                        </tr>
                        <tr>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item11" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack11" value="0" size="10" maxlength="10" />
                          </td>
                          <td>'
                            .lang("mail", "item").': <input type="text" name="att_item12" value="0" size="10" maxlength="10" />'
                            .lang("mail", "stack").': <input type="text" name="att_stack12" value="0" size="10" maxlength="10" />
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>';
  }

  $output .= '
                </table>
                <br />
              </div>
              <div id="mail_body_field" class="fieldset_border">
                <span class="legend">'.lang("mail", "mail_body").'</span>
                <table class="top_hidden" id="mail_body_table">
                  <tr>
                    <td align="left">'.lang("mail", "subject").': 
                      <input type="text" name="subject" size="32" maxlength="50" />
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <textarea name="body" rows="14" cols="92" id="mail_body"></textarea>
                    </td>
                  </tr>
                </table>
                <table>
                  <tr>
                    <td>';
  makebutton(lang("mail", "send"), "javascript:do_submit()",130);
  $output .= '
                    </td>
                  </tr>
                </table>
              </div>
            </div>
            <br />
          </form>
        </center>';
}


//#############################################################################
// Send the actual mail(s)
function send_mail()
{
  global $output, $logon_db, $characters_db, $realm_id, $action_permission,
         $user_name, $from_mail, $mailer_type, $smtp_cfg, $GMailSender, $sql, $core;

  // if we came here from Quest Item Vendor or Ultra Vendor,
  // we need to bypass the normal permissions
  if ( $_SESSION["vendor_permission"] )
  {
    valid_login($action_permission["view"]);
    unset($_SESSION["vendor_permission"]);
  }
  else
    valid_login($action_permission["update"]);

  $type = ( ( isset($_GET["type"]) ) ? $_GET["type"] : "ingame_mail" );

  if ( empty($_GET["body"]) || empty($_GET["subject"]) || empty($_GET["group_sign"]) || empty($_GET["group_send"]) )
    redirect("mail.php?error=1");

  $body = explode("\n", $_GET["body"]);
  $subject = $sql["char"]->quote_smart($_GET["subject"]);

  if ( isset($_GET["to"]) && ( $_GET["to"] != "" ) )
    $to = $sql["char"]->quote_smart($_GET["to"]);
  else
  {
    $to = 0;
    if ( !isset($_GET["group_value"]) || $_GET["group_value"] === '' )
      redirect("mail.php?error=1");
    else
    {
      $group_value = $sql["char"]->quote_smart($_GET["group_value"]);
      $group_sign = $sql["char"]->quote_smart($_GET["group_sign"]);
      $group_send = $sql["char"]->quote_smart($_GET["group_send"]);
    }
  }

  //$type = addslashes($type);
  $att_gold = $sql["char"]->quote_smart($_GET["money"]);

  for ( $i = 0; $i < 12; $i++ )
  {
    $temp_item = $sql["char"]->quote_smart($_GET["att_item".($i+1)]);
    $temp_stack = $sql["char"]->quote_smart($_GET["att_stack".($i+1)]);

    if ( ( $temp_item <> 0 ) && ( $temp_stack == 0 ) )
      $temp_stack = 1;

    if ( $temp_item != "0" )
    {
      $att_item[] = $temp_item;
      $att_stack[] = $temp_stack;
    }
  }

  switch ( $type )
  {
    case "email":
    {
      require_once("libs/mailer/class.phpmailer.php");
      require_once("libs/mailer/authgMail_lib.php");
      $mail = new PHPMailer();
      $mail->Mailer = $mailer_type;
      if ( $mailer_type == "smtp" )
      {
        $mail->Host = $smtp_cfg["host"];
        $mail->Port = $smtp_cfg["port"];
        if ( $smtp_cfg["user"] != "" )
        {
          $mail->SMTPAuth  = true;
          $mail->Username  = $smtp_cfg["user"];
          $mail->Password  =  $smtp_cfg["pass"];
        }
      }

      $value = NULL;
      for ( $i = 0; $i < count($body); $i++ )
        $value .= $body[$i]."\r\n";
      $body=$value;

      $mail->From = $from_mail;
      $mail->FromName = $user_name;
      $mail->Subject = $subject;
      $mail->IsHTML(true);

      $body = str_replace("\n", "<br />", $body);
      $body = str_replace("\r", " ", $body);
      $body = str_replace(array("\r\n", "\n", "\r"), "<br />", $body);
      $body = preg_replace( "/([^\/=\"\]])((http|ftp)+(s)?:\/\/[^<>\s]+)/i", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>",  $body);
      $body = preg_replace('/([^\/=\"\]])(www\.)(\S+)/', '\\1<a href="http://\\2\\3" target="_blank">\\2\\3</a>', $body);

      $mail->Body = $body;
      $mail->WordWrap = 50;

      if ( $to )
      {
        if ( !$GMailSender )
        {
          //single Recipient
          $mail->AddAddress($to);
          if ( !$mail->Send() )
          {
            $mail->ClearAddresses();
            redirect("mail.php?error=3&mail_err=".$mail->ErrorInfo);
          }
          else
          {
            $mail->ClearAddresses();
            redirect("mail.php?error=2");
          }
        }
        else
        {
          //single Recipient
          $mail_result = authgMail($from_mail, $user_name, $to, $to, $subject, $body, $smtp_cfg);
          if ( $mail_result["quitcode"] <> 221 )
            redirect("mail.php?error=3&mail_err=".$mail_result["die"]);
          else
            redirect("mail.php?error=2");
        }
      }
      elseif ( isset($group_value) )
      {
        //group send
        $email_array = array();
        switch ( $group_send )
        {
          case "gm_level":
            if ( $core == 1 )
              $result = $sql["logon"]->query("SELECT email FROM accounts WHERE gm".$group_sign."'".$group_value."'");
            else
              $result = $sql["logon"]->query("SELECT email FROM account
                  LEFT JOIN account_access ON account_access.id=account.id
                WHERE IFNULL(gmlevel, 0)".$group_sign."'".$group_value."'");
            while ( $user = $sql["logon"]->fetch_row($result) )
            {
              if ( $user[0] != "" )
                array_push($email_array, $user[0]);
            }
            break;
          case "locked":
            //this_is_junk: I'm going to pretend that locked is muted
            if ( $core == 1 )
              $result = $sql["logon"]->query("SELECT email FROM accounts WHERE muted".$group_sign."'".$group_value."'");
            else
              $result = $sql["logon"]->query("SELECT email FROM accounts WHERE locked".$group_sign."'".$group_value."'");
            while ( $user = $sql["logon"]->fetch_row($result) )
            {
              if ( $user[0] != "" )
                array_push($email_array, $user[0]);
            }
            break;
          case "banned":
            //this_is_junk: sigh...
            $que = $sql["logon"]->query("SELECT id FROM account_banned");
            while ( $banned = $sql->fetch_row($que) )
            {
              $result = $sql["logon"]->query("SELECT email FROM accounts WHERE acct='".$banned[0]."'");
              if ( $sqlr->result($result, 0, 'email') )
                array_push($email_array, $sql->result($result, 0, "email"));
            }
            break;
          default:
            redirect("mail.php?error=5");
            break;
        }
        if ( !$GMailSender )
        {
          foreach ( $email_array as $mail_addr )
          {
            $mail->AddAddress($mail_addr);
            if ( !$mail->Send() )
            {
              $mail->ClearAddresses();
              redirect("mail.php?error=3&mail_err=".$mail->ErrorInfo);
            }
            else
              $mail->ClearAddresses();
          }
        }
        else
        {
          $mail_to = implode(",", $email_array);
          $mail_result = authgMail($from_mail, $user_name, $mail_to, "", $subject, $body, $smtp_cfg);
          if ( $mail_result["quitcode"] <> 221 )
            redirect("mail.php?error=3&mail_err=".$mail_result["die"]);
          else
            redirect("mail.php?error=2");
          
        }
        redirect("mail.php?error=2");
      }
      else
        redirect("mail.php?error=1");
      break;
    }
    case "ingame_mail":
    {
      $value = NULL;
      for ( $i = 0; $i < count($body); $i++ )
        $value .= $body[$i]." ";
      $body = $value;
      $body = str_replace("\r", " ", $body);
      $body = $sql["char"]->quote_smart($body);

      if ( $to )
      {
        //single Recipient
        $result = $sql["char"]->query("SELECT guid FROM characters WHERE name='".$to."'");
        if ( $sql["char"]->num_rows($result) == 1 )
        {
          $receiver = $sql["char"]->result($result, 0, 'guid');
          $mails = array();
          $mail["receiver"] = $receiver;
          $mail["subject"] = $subject;
          $mail["body"] = $body;
          $mail["att_gold"] = $att_gold;
          $mail["att_item"] = $att_item;
          $mail["att_stack"] = $att_stack;
          $mail["receiver_name"] = $to;
          //array_push($mails, array($receiver, $subject, $body, $att_gold, $att_item, $att_stack));
          array_push($mails, $mail);
          if ( $core == 1 )
            send_ingame_mail_A($realm_id, $mails);
          else
            send_ingame_mail_MT($realm_id, $mails);
        }
        else
          redirect("mail.php?error=4");

        redirect("mail.php?error=2");
        break;
      }
      elseif ( isset($group_value) )
      {
        //group send
        $char_array = array();
        switch ( $group_send )
        {
          case "gm_level":
            if ( $core == 1 )
              $result = $sql["logon"]->query("SELECT acct FROM accounts WHERE gm".$group_sign."'".$group_value."'");
            else
              $result = $sql["logon"]->query("SELECT account.id AS acct FROM account
                  LEFT JOIN account_access ON account_access.id=account.id
                WHERE IFNULL(gmlevel, 0)".$group_sign."'".$group_value."'");
            while ( $acc = $sql["char"]->fetch_row($result) )
            {
              if ( $core == 1 )
                $result_2 = $sql["char"]->query("SELECT name FROM `characters` WHERE acct='".$acc[0]."'");
              else
                $result_2 = $sql["char"]->query("SELECT name FROM `characters` WHERE account='".$acc[0]."'");
              while ( $char = $sql["char"]->fetch_row($result_2) )
                array_push($char_array, $char[0]);
            }
            break;
          case "online":
            $result = $sql["char"]->query("SELECT name FROM `characters` WHERE online".$group_sign."'".$group_value."'");
            while ( $user = $sql["char"]->fetch_row($result) )
              array_push($char_array, $user[0]);
            break;
          case "char_level":
            $result = $sql["char"]->query("SELECT name FROM `characters` WHERE level".$group_sign."'".$group_value."'");
            while ( $user = $sql["char"]->fetch_row($result) )
              array_push($char_array, $user[0]);
            break;
          default:
            redirect("mail.php?error=5");
        }
        $mails = array();
        if ( $sql["char"]->num_rows($result) )
        {
          foreach ( $char_array as $receiver )
          {
            $result = $sql["char"]->query("SELECT guid FROM characters WHERE name='".$receiver."'");
            $char_guid = $sql["char"]->fetch_row($result);
            $mail = array();
            $mail["receiver"] = $char_guid[0];
            $mail["subject"] = $subject;
            $mail["body"] = $body;
            $mail["att_gold"] = $att_gold;
            $mail["att_item"] = $att_item;
            $mail["att_stack"] = $att_stack;
            $mail["receiver_name"] = $receiver;
            //array_push($mails, array($receiver, $subject, $body, $att_gold, $att_item, $att_stack));
            array_push($mails, $mail);
          }
          if ( $core == 1 )
            send_ingame_mail_A($realm_id, $mails);
          else
            send_ingame_mail_MT($realm_id, $mails);
          redirect("mail.php?error=2");
        }
        else
          redirect("mail.php?error=4");
      }
      break;
    }
    default:
      redirect("mail.php?error=1");
  }

}

//##########################################################################################
//SEND INGAME MAIL
//
function send_ingame_mail_A($realm_id, $massmails)
{
  global $server, $characters_db, $realm_id, $from_char, $stationary, $sql;

  //$mess_str = '';
  $mess = 0;
  $result = '';
  $receivers = array();
  foreach ( $massmails as $mails )
  {
    if ( count($mails["att_item"]) < 1 )
    {
      $mails["att_item"] = array(0);
      $mails["att_stack"] = array(0);
    }

    // build insert query
    $query = "INSERT INTO mailbox_insert_queue (sender_guid, receiver_guid, subject, body, stationary, money, 
              item_id, item_stack";

    $att_item = $mails["att_item"];
    $att_stack = $mails["att_stack"];

    if ( count($att_item) > 1 )
    {
      for ( $i = 1; $i < count($att_item); $i++ )
      {
        $query .= ", item_id".($i+1).", item_stack".($i+1);
      }
    }

    $query .= "
              )
              VALUES ('".$from_char."', '".$mails["receiver"]."', '".$mails["subject"]."', '".$mails["body"]."', '".$stationary."', '".$mails["att_gold"]."', 
              '".$att_item[0]."', '".$att_stack[0]."'";

    if ( count($att_item) > 1 )
    {
      for ( $i = 1; $i < count($att_item); $i++ )
      {
        $query .= ", '".$att_item[$i]."', '".$att_stack[$i]."'";
      }
    }

    $query .= "
              )";

    $sql["char"]->query($query);

    if ( $sql["char"]->affected_rows() )
    {
      //$mess_str .= "Successfully sent message sent to ". $mails["receiver_name"]."<br />";
      $mess = 0; // success
      $result = "RESULT";
      array_push($receivers, $mails["receiver_name"]);
    }
    else
    {
      //$mess_str .= "Failed to send message to ".$mails["receiver_name"]."<br />";
      $mess = -1; // failure
      $result = "RESULT";
    }
  }

  $receiver_list = '';
  foreach ( $receivers as $receiver )
  {
    $receiver_list .= ', '.$receiver;
  }
  $reveiver_list = substr($receiver_list, 2, strlen($receiver_list)-2);

  if ( !isset($_GET["redirect"]) )
    //redirect("mail.php?action=result&error=6&mess=$mess_str");
    redirect("mail.php?action=result&error=6&mess=".$mess."&recipient=".$receiver_list);
  else
  {
    $money_result = $sql["char"]->quote_smart($_GET["moneyresult"]);

    redirect($redirect."?moneyresult=".$money_result."&mailresult=1");
  }

}


//##########################################################################################
//SEND INGAME MAIL BY TELNET
//
// Xiong Guoy
// 2009-08-08
function send_ingame_mail_MT($realm_id, $massmails)
{
  require_once 'libs/telnet_lib.php';
  global $server, $sql;
  $telnet = new telnet_lib();

  $result = $telnet->Connect($server[$realm_id]["addr"], $server[$realm_id]["telnet_port"], $server[$realm_id]["telnet_user"], $server[$realm_id]["telnet_pass"]);

  if ( $result == 0 )
  {
    $mess_str = '';
    $result = '';
    $receivers = array();
    foreach( $massmails as $mails )
    {
      $att_item = $mails["att_item"];
      $att_stack = $mails["att_stack"];

      if ( $mails["att_gold"] && ( count($att_item) > 0 ) )
      {
        $mess_str1 = "send money ".$mails["receiver_name"]." \"".$mails["subject"]."\" \"".$mails["body"]."\" ".$mails["att_gold"]."";
        $telnet->DoCommand($mess_str1, $result1);

        $mess_str .= $mess_str1."<br >";
        $result .= $result1."";

        $mess_str1 = "send item ".$mails["receiver_name"]." \"".$mails["subject"]."\" \"".$mails["body"]."\" ";

        for ( $i = 0; $i < count($att_item); $i++ )
          $mess_str1 .= $att_item[$i].( ( $att_stack[$i] > 1 ) ? ":".$att_stack[$i]." " : " " );

        $telnet->DoCommand($mess_str1, $result1);

        $mess_str .= $mess_str1."<br >";
        $result .= $result1."";
      }
      elseif ( $mails["att_gold"] )
      {
        $mess_str1 = "send money ".$mails["receiver_name"]." \"".$mails["subject"]."\" \"".$mails["body"]."\" ".$mails["att_gold"]."";
        $telnet->DoCommand($mess_str1, $result1);

        $mess_str .= $mess_str1."<br >";
        $result .= $result1."";
      }
      elseif ( count($att_item) > 0 )
      {
        $mess_str1 = "send item ".$mails["receiver_name"]." \"".$mails["subject"]."\" \"".$mails["body"]."\" ";

        for ( $i = 0; $i < count($att_item); $i++ )
          $mess_str1 .= $att_item[$i].( ( $att_stack[$i] > 1 ) ? ":".$att_stack[$i]." " : " " );

        $telnet->DoCommand($mess_str1, $result1);

        $mess_str .= $mess_str1."<br >";
        $result .= $result1."";
      }
      else
      {
        $mess_str1 = "send mail ".$mails["receiver_name"]." \"".$mails["subject"]."\" \"".$mails["body"]."\"";
        $telnet->DoCommand($mess_str1, $result1);

        $mess_str .= $mess_str1."<br >";
        $result .= $result1."";
      }
      array_push($receivers, $mails["receiver_name"]);
    }
    if ( $core == 2 )
      $core_prompt = "mangos";
    elseif ( $core == 3 )
      $core_prompt = "TC";
    $result = str_replace($core_prompt.">","",$result);
    $result = str_replace(array("\r\n", "\n", "\r"), '<br />', $result);
    $mess_str .= "<br /><br />".$result;
    $telnet->Disconnect();

    $receiver_list = '';
    foreach ( $receivers as $receiver )
    {
      $receiver_list .= ', '.$receiver;
    }
    $receiver_list = substr($receiver_list, 2, strlen($receiver_list)-2);
  }
  elseif ( $result == 1 )
    $mess_str = lang("telnet", "unable");
  elseif ( $result == 2 )
    $mess_str = lang("telnet", "unknown_host");
  elseif ( $result == 3 )
    $mess_str = lang("telnet", "login_failed");
  elseif ( $result == 4 )
    $mess_str = lang("telnet", "not_supported");

  if ( !isset($_GET["redirect"]) )
  {
    if ( count($massmails) == 1 )
      redirect("mail.php?action=result&error=6&mess=".$mess_str."&mailresult=".$result."&recipient=".$receiver_list);
    else
      redirect("mail.php?action=result&error=6&mess=&mailresult=".$result."&recipient=".$receiver_list);
  }
  else
  {
    $money_result = $sql["char"]->quote_smart($_GET["moneyresult"]);
    $redirect = $sql["char"]->quote_smart($_GET["redirect"]);

    redirect($redirect."?moneyresult=".$money_result."&mailresult=".$result);
  }

}


//########################################################################################################################
// InGame Mail Result
//########################################################################################################################
//
// Xiong Guoy
// 2009-08-08
// report page for send_ingame_mail
function result()
{
  global $output;

  $mess = ( ( isset($_GET["mess"]) ) ? $_GET["mess"] : NULL );
  $resultcode = ( ( isset($_GET["mailresult"]) ) ? $_GET["mailresult"] : NULL );
  $recipient = $_GET["recipient"];

  switch ( $resultcode )
  {
    case 0: // success
    {
      $mess = lang("mail", "result_success");
      $mess = str_replace("%1", $recipient, $mess);
      break;
    }
    default: //failure
    {
      $mess .= "<br ><br />".lang("mail", "result_failed");
      $mess = str_replace("%1", $recipient, $mess);
      break;
    }
  }
  $output .= '
        <center>
          <br />
          <table width="400" class="flat">
            <tr>
              <td align="left">
                <br />'.$mess.'<br />';
  unset($mess);
  $output .= '
              </td>
            </tr>
          </table>
          <br />
          <table width="400" class="hidden">
            <tr>
              <td align="center">';
  makebutton(lang("global", "back"), 'mail.php', 130);
  $output .= '
              </td>
            </tr>
          </table>
          <br />
        </center>';

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
          <h1><font class="error">'.lang("mail", "mail_sent").'</font></h1>';
    break;
  case 3:
    $mail_err = ( ( isset($_GET["mail_err"]) ) ? $_GET["mail_err"] : NULL );
    $output .= '
          <h1><font class="error">'.lang("mail", "mail_err").': '.$mail_err.'</font></h1>';
    break;
  case 4:
    $output .= '
          <h1><font class=\"error">'.lang("mail", "no_recipient_found").'</font></h1>'
          .lang("mail", "use_name_or_email");
    break;
  case 5:
    $output .= '
          <h1><font class="error">'.lang("mail", "option_unavailable").'</font></h1>'
          .lang("mail", "use_currect_option");
    break;
  case 6:
    $output .= '
          <h1><font class="error">'.lang("mail", "result").'</font></h1>';
    break;
  default: //no error
    $output .= '
          <h1>'.lang("mail", "send_mail").'</h1>';
}
unset($err);

$output .= '
        </div>';

$action = ( ( isset($_GET["action"]) ) ? $_GET["action"] : NULL );

switch ( $action )
{
  case "send_mail":
    send_mail();
    break;
  case "result":
    result();
    break;
  default:
    print_mail_form();
}

unset($action);
unset($action_permission);

require_once("footer.php");

?>
