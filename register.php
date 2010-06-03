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


 require_once("header.php");

//#####################################################################################################
// DO REGISTER
//#####################################################################################################
function doregister()
{
  global $characters_db, $logon_db, $arcm_db, $realm_id, $disable_acc_creation,
    $limit_acc_per_ip, $valid_ip_mask, $send_mail_on_creation, $create_acc_locked, $from_mail,
    $mailer_type, $smtp_cfg, $title, $expansion_select, $defaultoption, $GMailSender, $format_mail_html,
    $enable_captcha, $use_recaptcha, $recaptcha_private_key, $sql, $core;

  if ($enable_captcha)
  {
    if ($use_recaptcha)
    {
      require_once('libs/recaptcha/recaptchalib.php');

      $resp = recaptcha_check_answer($recaptcha_private_key, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
      
      if (!$resp->is_valid)
      {
        redirect("register.php?err=13");
      }
    }
    else
    {
      if (($_POST['security_code']) != ($_SESSION['security_code']))
      {
        redirect("register.php?err=13");
      }
    }
  }

  if ( empty($_POST['pass']) || empty($_POST['email']) || empty($_POST['username']) )
  {
    redirect("register.php?err=1");
  }

  if ($disable_acc_creation) 
    redirect("register.php?err=4");

  $last_ip =  (getenv('HTTP_X_FORWARDED_FOR')) ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR');

  if (sizeof($valid_ip_mask))
  {
    $qFlag = 0;
    $user_ip_mask = explode('.', $last_ip);

    foreach($valid_ip_mask as $mask)
    {
      $vmask = explode('.', $mask);
      $v_count = 4;
      $i = 0;
      foreach($vmask as $range)
      {
        $vmask_h = explode('-', $range);
        if (isset($vmask_h[1]))
        {
          if (($vmask_h[0]>=$user_ip_mask[$i]) && ($vmask_h[1]<=$user_ip_mask[$i]))
            $v_count--;
        }
        else
        {
          if ($vmask_h[0] == $user_ip_mask[$i])
            $v_count--;
        }
        $i++;
      }
      if (!$v_count)
      {
        $qFlag++;
        break;
      }
    }
    if (!$qFlag)
      redirect("register.php?err=9&usr=$last_ip");
  }

  $user_name = $sql['logon']->quote_smart(trim($_POST['username']));
  $screenname = $sql['mgr']->quote_smart(trim($_POST['screenname']));
  $pass = $sql['logon']->quote_smart($_POST['pass']);
  $pass1 = $sql['logon']->quote_smart($_POST['pass1']);

  //make sure username/pass at least 4 chars long and less than max
  if ((strlen($user_name) < 4) || (strlen($user_name) > 15))
  {
    //$sql['logon']->close();
    redirect("register.php?err=5");
  }
  if ( $core == 1 )
  {
    if ((strlen($pass) < 4) || (strlen($pass) > 15))
    {
      //$sql['logon']->close();
      redirect("register.php?err=5");
    }
  }

  // make sure screen name is at least 4 chars long and less than max
  // Screen Name may be left blank
  if ($screenname)
  {
    if ((strlen($screenname) < 4) || (strlen($screenname) > 15))
    {
      //$sql['logon']->close();
      redirect("register.php?err=5");
    }
  }

  require_once("libs/valid_lib.php");

  //make sure it doesnt contain non english chars.
  if (!valid_alphabetic($user_name))
  {
    //$sql['logon']->close();
    redirect("register.php?err=6");
  }

  //make sure screen name doesnt contain non english chars.
  if (!valid_alphabetic($screenname))
  {
    //$sql['logon']->close();
    redirect("register.php?err=6");
  }

  //make sure the mail is valid mail format
  $mail = $sql['logon']->quote_smart(trim($_POST['email']));
  if ((!valid_email($mail))||(strlen($mail) > 224))
  {
    //$sql['logon']->close();
    redirect("register.php?err=7");
  }

  if ( $core == 1 )
    $per_ip = ($limit_acc_per_ip) ? "OR lastip='$last_ip'" : "";
  else
    $per_ip = ($limit_acc_per_ip) ? "OR last_ip='$last_ip'" : "";

  if ( $core == 1 )
    $result = $sql['logon']->query("SELECT ip FROM ipbans WHERE ip = '$last_ip'");
  else
    $result = $sql['logon']->query("SELECT ip FROM ip_banned WHERE ip = '$last_ip'");
  
  //IP is in ban list
  if ($sql['logon']->num_rows($result))
  {
    //$sql['logon']->close();
    redirect("register.php?err=8&usr=$last_ip");
  }

  //Email check
  if ( $core == 1 )
    $result = $sql['logon']->query("SELECT login, email FROM accounts WHERE login='".$user_name."' OR login='".$screenname."' OR email='".$mail."' ".$per_ip);
  else
    $result = $sql['logon']->query("SELECT username AS login, email FROM account WHERE username='".$user_name."' OR username='".$screenname."' OR email='".$mail."' ".$per_ip);
    
  if ($sql['logon']->num_rows($result))
  {
    //$sql['logon']->close();
    redirect("register.php?err=14");
  }

  //there is already someone with same account name
  if ($sql['logon']->num_rows($result))
  {
    //$sql['logon']->close();
    redirect("register.php?err=3&usr=$user_name");
  }
  else
  {
    // check for existing screen name
    $query = "SELECT * FROM config_accounts WHERE ScreenName ='".$screenname."'";
    $result = $sql['mgr']->query($query);
    if ($sql['mgr']->num_rows($result))
      redirect("register.php?err=3&usr=$screenname");

    if ($expansion_select)
      $expansion = (isset($_POST['expansion'])) ? $sql['logon']->quote_smart($_POST['expansion']) : 0;
    else
      $expansion = $defaultoption;

    // insert screen name (if we didn't get a screen name, we still need to exit registration correctly.
    if ($screenname)
    {
      $query = "INSERT INTO config_accounts (Login, ScreenName) VALUES ('".$user_name."', '".$screenname."')";
      $s_result = $sql['mgr']->query($query);
    }
    else
      $s_result = true;

    if ( $core == 1 )
      $query = "INSERT INTO accounts (login, password, gm, banned, email, flags) VALUES ('".$user_name."', '".$pass."', '0', '0', '".$mail."', '".$expansion."')";
    else
      $query = "INSERT INTO account (username, sha_pass_hash, email, expansion) VALUES ('".$user_name."', '".$pass."', '".$mail."', '".$expansion."')";
    
    $a_result = $sql['logon']->query($query);
    //$sql['logon']->close();
    
    if ( $core == 1 )
      ;
    else
    {
      $id_query = "SELECT * FROM account WHERE username='".$user_name."'";
      $id_result = $sql['logon']->query($id_query);
      $id_fields = $sql['logon']->fetch_assoc($id_result);
      $new_id = $id_fields['id'];
      
      $query = "INSERT INTO account_access (id, gmlevel, RealmID) VALUES ('".$new_id."', '0', '-1')";
      $aa_result = $sql['logon']->query($query);
    }

    if ( $core == 1 )
      $result = $s_result && $a_result;
    else
      $result = $s_result && $a_result && $aa_result;

    setcookie ("terms", "", time() - 3600);

    if ($send_mail_on_creation)
    {
      if ($GMailSender)
      {
        require_once("libs/mailer/authgMail_lib.php");

        if ($format_mail_html)
        {
          $file_name = "mail_templates/mail_welcome.tpl";
        }
        else
        {
          $file_name = "mail_templates/mail_welcome_nohtml.tpl";
        }
        $fh = fopen($file_name, 'r');
        $subject = fgets($fh, 4096);
        $body = fread($fh, filesize($file_name));
        fclose($fh);

        $subject = str_replace("<title>", $title, $subject);
        if ($format_mail_html)
        {
          $body = str_replace("\n", "<br />", $body);
          $body = str_replace("\r", " ", $body);
        }
        $body = str_replace("<username>", $user_name, $body);
        $body = str_replace("<password>", $pass1, $body);
        $body = str_replace("<base_url>", $_SERVER['SERVER_NAME'], $body);

        $fromName = "$title Admin";
        authgMail($from_mail, $fromName, $mail, $mail, $subject, $body, $smtp_cfg);
      }
      else
      {
        require_once("libs/mailer/class.phpmailer.php");
        $mailer = new PHPMailer();
        $mailer->Mailer = $mailer_type;
        if ($mailer_type == "smtp")
        {
          $mailer->Host = $smtp_cfg['host'];
          $mailer->Port = $smtp_cfg['port'];
          if($smtp_cfg['user'] != '')
          {
            $mailer->SMTPAuth  = true;
            $mailer->Username  = $smtp_cfg['user'];
            $mailer->Password  =  $smtp_cfg['pass'];
          }
        }

        if ($format_mail_html)
        {
          $file_name = "mail_templates/mail_welcome.tpl";
        }
        else
        {
          $file_name = "mail_templates/mail_welcome_nohtml.tpl";
        }
        $fh = fopen($file_name, 'r');
        $subject = fgets($fh, 4096);
        $body = fread($fh, filesize($file_name));
        fclose($fh);

        $subject = str_replace("<title>", $title, $subject);
        if ($format_mail_html)
        {
          $body = str_replace("\n", "<br />", $body);
          $body = str_replace("\r", " ", $body);
        }
        $body = str_replace("<username>", $user_name, $body);
        if ($screenname)
          $body = str_replace("<screenname>", $screenname, $body);
        else
          $body = str_replace("<screenname>", "NONE GIVEN", $body);
        $body = str_replace("<password>", $pass1, $body);
        $body = str_replace("<base_url>", $_SERVER['SERVER_NAME'], $body);

        $mailer->WordWrap = 50;
        $mailer->From = $from_mail;
        $mailer->FromName = "$title Admin";
        $mailer->Subject = $subject;
        $mailer->IsHTML($format_mail_html);
        $mailer->Body = $body;
        $mailer->AddAddress($mail);
        $mailer->Send();
        $mailer->ClearAddresses();
      }
    }

    if ($result)
      redirect("login.php?error=6");
  }
}

//#####################################################################################################
// PRINT FORM
//#####################################################################################################
function register()
{
  global $output, $expansion_select, $enable_captcha, $use_recaptcha, $recaptcha_public_key, $core;

  $output .= '
    <center>
      <script type="text/javascript" src="libs/js/sha1.js">
      </script>
      <script type="text/javascript">
        function do_submit_data ()
        {
          if (document.form.pass1.value != document.form.pass2.value)
          {
            alert("'.lang('register', 'diff_pass_entered').'");
            return;
          }
          else if (document.form.pass1.value.length > 225)
          {
            alert("'.lang('register', 'pass_too_long').'");
            return;
          }
          else
          {';
  if ( $core == 1 )
    $output .= '
            document.form.pass.value = document.form.pass1.value;';
  else
    $output .= '
            document.form.pass.value = hex_sha1(document.form.username.value.toUpperCase()+":"+document.form.pass1.value.toUpperCase());';
  $output .= '
            document.form.pass2.value = "0";
            do_submit();
          }
        }
        answerbox.btn_ok="'.lang('register', 'i_agree').'";
        answerbox.btn_cancel="'.lang('register', 'i_dont_agree').'";
        answerbox.btn_icon="";
      </script>
      <fieldset class="half_frame">
        <legend>'.lang('register', 'create_acc').'</legend>
        <form method="post" action="register.php?action=doregister" name="form">
          <input type="hidden" name="pass" value="" maxlength="256" />
          <table class="flat">
            <tr>
              <td valign="top">'.lang('register', 'username').':</td>
              <td>
                <input type="text" name="username" id="reg_username" maxlength="14" />
                <br />
                '.lang('register', 'use_eng_chars_limited_len').'
                <br />
              </td>
            </tr>
            <tr>
              <td valign="top">
                '.lang('register', 'screenname').':
                <br />
                '.lang('register', 'optional').'
              </td>
              <td>
                <input type="text" name="screenname" id="reg_screenname" maxlength="14" />
                <br />
                '.lang('register', 'willbeused').'
                <br />
                '.lang('register', 'use_eng_chars_limited_len').'
                <br />
              </td>
            </tr>
            <tr>
              <td valign="top">'.lang('register', 'password').':</td>
              <td>
                <input type="password" name="pass1" id="reg_pass1" maxlength="25" />
              </td>
            </tr>
            <tr>
              <td valign="top">'.lang('register', 'confirm_password').':</td>
              <td>
                <input type="password" name="pass2" id="reg_pass2" maxlength="25" />
                <br />
                '.lang('register', 'min_pass_len').'
                <br />
              </td>
            </tr>
            <tr>
              <td valign="top">'.lang('register', 'email').':</td>
              <td>
                <input type="text" name="email" id="reg_email" maxlength="225" />
                <br />
                '.lang('register', 'use_valid_mail').'
              </td>
            </tr>';
  if ( $expansion_select )
  {
    if ( $core == 1 )
      $output .= '
            <tr>
              <td valign="top">'.lang('register', 'acc_type').':</td>
              <td>
                <select name="expansion">
                  <option value="24">'.lang('register', 'wotlktbc').'</option>
                  <option value="16">'.lang('register', 'wotlk').'</option>
                  <option value="8">'.lang('register', 'tbc').'</option>
                  <option value="0">'.lang('register', 'classic').'</option>
                </select>
                - '.lang('register', 'acc_type_desc').'
              </td>
            </tr>';
    else
      $output .= '
            <tr>
              <td valign="top">'.lang('register', 'acc_type').':</td>
              <td>
                <select name="expansion">
                  <option value="2">'.lang('register', 'wotlktbc').'</option>
                  <option value="1">'.lang('register', 'tbc').'</option>
                  <option value="0">'.lang('register', 'classic').'</option>
                </select>
                - '.lang('register', 'acc_type_desc').'
              </td>
            </tr>';
  }
  if ( $enable_captcha )
  {
    if ($use_recaptcha)
    {
      require_once('libs/recaptcha/recaptchalib.php');
    
      $output .= '
            <tr>
              <td>
              </td>
              <td>
                '.recaptcha_get_html($recaptcha_public_key).'
              </td>
            </tr>';
    }
    else
    {
      $output .= '
            <tr>
              <td>
              </td>
              <td>
                <img src="libs/captcha/CaptchaSecurityImages.php?width=300&height=80&characters=6" />
                <br />
                <br />
              </td>
            </tr>
            <tr>
              <td valign="top">'.lang('captcha', 'security_code').':</td>
              <td>
                <input type="text" name="security_code" size="45" />
                <br />
              </td>
            </tr>';
    }
  }
  $output .= '
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>
            <tr>
              <td colspan="2">'.lang('register', 'read_terms').'.</td>
            </tr>
            <tr>
              <td colspan="2">
                <hr />
              </td>
            </tr>
            <tr>
              <td>';

  $terms = "<textarea rows=\'18\' cols=\'80\' readonly=\'readonly\'>";
  $fp = fopen("mail_templates/terms.tpl", 'r') or die (error("Couldn't Open terms.tpl File!"));
  while (!feof($fp)) $terms .= fgets($fp, 1024);
  fclose($fp);
  $terms .= "</textarea>";

  makebutton(lang('register', 'create_acc_button'), "javascript:answerBox('".lang('register', 'terms')."<br />$terms', 'javascript:do_submit_data()')",150);
  $output .= '</td><td>';
  makebutton(lang('global', 'back'), "login.php", 328);
  $output .= '
              </td>
            </tr>
          </table>
        </form>
      </fieldset>
      <br />
      <br />
    </center>';
}


//#####################################################################################################
// PRINT PASSWORD RECOVERY FORM
//#####################################################################################################
function pass_recovery()
{
  global $output;

  $output .= '
    <center>
      <fieldset class="half_frame">
      <legend>'.lang('register', 'recover_acc_password').'</legend>
      <form method="post" action="register.php?action=do_pass_recovery" name="form">
        <table class="flat">
          <tr>
            <td valign="top">'.lang('register', 'username').' :</td>
            <td>
              <input type="text" name="username" size="45" maxlength="14" />
              <br />
              '.lang('register', 'user_pass_rec_desc').'
              <br />
            </td>
          </tr>
          <tr>
            <td valign="top">'.lang('register', 'email').' :</td>
            <td>
              <input type="text" name="email" size="45" maxlength="225" />
              <br />
              '.lang('register', 'mail_pass_rec_desc').'
            </td>
          </tr>
          <tr>
            <td>';
  makebutton(lang('register', 'recover_pass'), "javascript:do_submit()",150);
  $output .= '
            </td>
            <td>';
  makebutton(lang('global', 'back'), "javascript:window.history.back()", 328);
  $output .= '
            </td>
          </tr>
        </table>
      </form>
    </fieldset>
    <br />
    <br />
  </center>';
}

//#####################################################################################################
// DO RECOVER PASSWORD
//#####################################################################################################
function do_pass_recovery()
{
  global $logon_db, $from_mail, $mailer_type, $smtp_cfg, $title, $GMailSender,
    $format_mail_html, $sql;

  if ( empty($_POST['username']) || empty($_POST['email']) )
    redirect("register.php?action=pass_recovery&err=1");

  /*$sql = new SQL;
  $sql->connect($logon_db['addr'], $logon_db['user'], $logon_db['pass'], $logon_db['name']);*/

  $user_name = $sql['logon']->quote_smart(trim($_POST['username']));
  $email_addr = $sql['logon']->quote_smart($_POST['email']);

  $result = $sql['logon']->query("SELECT password FROM accounts WHERE login = '$user_name' AND email = '$email_addr'");

  if ($sql['logon']->num_rows($result) == 1)
  {
    $pass = $sql['logon']->fetch_assoc($result);

    if ($GMailSender)
    {
      require_once("libs/mailer/authgMail_lib.php");

      if ($format_mail_html)
        $file_name = "mail_templates/recover_password.tpl";
      else
         $file_name = "mail_templates/recover_password_nohtml.tpl";

      $fh = fopen($file_name, 'r');
      $subject = fgets($fh, 4096);
      $body = fread($fh, filesize($file_name));
      fclose($fh);

      if ($format_mail_html)
      {
        $body = str_replace("\n", "<br />", $body);
        $body = str_replace("\r", " ", $body);
      }
      $body = str_replace("<username>", $user_name, $body);
      $body = str_replace("<password>", $pass['password'], $body);
      $body = str_replace("<base_url>", $_SERVER['HTTP_HOST'], $body);
      $body = str_replace("<title>", $title, $body);

      $namefrom = "$title Admin";
      $result = authgMail($from_mail, $namefrom, $email_addr, $email_addr, $subject, $body, $smtp_cfg);

      if (! ($result['quitcode'] = 221) ) 
      {
        redirect("register.php?action=pass_recovery&err=11&usr=".$result['quitcode']);
      } 
      else 
      {
        redirect("register.php?action=pass_recovery&err=12");
      }
    }
    else
    {
      require_once("libs/mailer/class.phpmailer.php");
      $mail = new PHPMailer();
      $mail->Mailer = $mailer_type;
      if ($mailer_type == "smtp")
      {
        $mail->Host = $smtp_cfg['host'];
        $mail->Port = $smtp_cfg['port'];
        if($smtp_cfg['user'] != '') 
        {
          $mail->SMTPAuth  = true;
          $mail->Username  = $smtp_cfg['user'];
          $mail->Password  =  $smtp_cfg['pass'];
        }
      }

      if ($format_mail_html)
        $file_name = "mail_templates/recover_password.tpl";
      else
         $file_name = "mail_templates/recover_password_nohtml.tpl";

      $fh = fopen($file_name, 'r');
      $subject = fgets($fh, 4096);
      $body = fread($fh, filesize($file_name));
      fclose($fh);

      if ($format_mail_html)
      {
        $body = str_replace("\n", "<br />", $body);
        $body = str_replace("\r", " ", $body);
      }
      $body = str_replace("<username>", $user_name, $body);
      $body = str_replace("<password>", $pass['password'], $body);
      $body = str_replace("<base_url>", $_SERVER['HTTP_HOST'], $body);
      $body = str_replace("<title>", $title, $body);

      $mail->WordWrap = 50;
      $mail->From = $from_mail;
      $mail->FromName = "$title Admin";
      $mail->Subject = $subject;
      $mail->IsHTML($format_mail_html);
      $mail->Body = $body;
      $mail->AddAddress($email_addr);

      if(!$mail->Send()) 
      {
        $mail->ClearAddresses();
        redirect("register.php?action=pass_recovery&err=11&usr=".$mail->ErrorInfo);
      } 
      else 
      {
        $mail->ClearAddresses();
        redirect("register.php?action=pass_recovery&err=12");
      }
    }
  }
  else
    redirect("register.php?action=pass_recovery&err=10");
}


//#####################################################################################################
// DO ACTIVATE RECOVERED PASSWORD
//#####################################################################################################
//
// this_is_junk: we send the password in the clear in the recovery email.
//               this is unused.  And should be deleted later.
function do_pass_activate()
{
 global $logon_db, $sql;

 if ( empty($_GET['h']) || empty($_GET['p']) ) redirect("register.php?action=pass_recovery&err=1");

 /*$sql = new SQL;
 $sql->connect($logon_db['addr'], $logon_db['user'], $logon_db['pass'], $logon_db['name']);*/

 $pass = $sql['logon']->quote_smart(trim($_GET['p']));
 $hash = $sql['logon']->quote_smart($_GET['h']);

 $result = $sql['logon']->query("SELECT id,login FROM accounts WHERE password = '$hash'");

 if ($sql['logon']->num_rows($result) == 1){
  $username = $sql['logon']->result($result, 0, 'username');
  $id = $sql['logon']->result($result, 0, 'id');
  if (substr(sha1(strtoupper($sql['logon']->result($result, 0, 'username'))),0,7) == $pass){
    $sql->query("UPDATE account SET sha_pass_hash=SHA1(CONCAT(UPPER('$username'),':',UPPER('$pass'))), v=0, s=0 WHERE id = '$id'");
    redirect("login.php");
    }

  } else redirect("register.php?action=pass_recovery&err=1");

  redirect("register.php?action=pass_recovery&err=1");
}


//#####################################################################################################
// MAIN
//#####################################################################################################
$err = (isset($_GET['err'])) ? $_GET['err'] : NULL;

if (isset($_GET['usr'])) $usr = $_GET['usr'];
    else $usr = NULL;

//$lang_captcha = lang_captcha();

$output .=  '
  <div class="bubble">
    <div class="top">';

switch ($err)
{
case 1:
   $output .= '<h1><font class="error">'.lang('global', 'empty_fields').'</font></h1>';
   break;
case 2:
   $output .= '<h1><font class="error">'.lang('register', 'diff_pass_entered').'</font></h1>';
   break;
case 3:
   $output .= '<h1><font class="error">'.lang('register', 'username').' '.$usr.' '.lang('register', 'already_exist').'</font></h1>';
   break;
case 4:
   $output .= '<h1><font class="error">'.lang('register', 'acc_reg_closed').'</font></h1>';
   break;
case 5:
   $output .= '<h1><font class="error">'.lang('register', 'wrong_pass_username_size').'</font></h1>';
   break;
case 6:
   $output .= '<h1><font class="error">'.lang('register', 'bad_chars_used').'</font></h1>';
   break;
case 7:
   $output .= '<h1><font class="error">'.lang('register', 'invalid_email').'</font></h1>';
   break;
case 8:
   $output .= '<h1><font class="error">'.lang('register', 'banned_ip').' ('.$usr.')<br />'.lang('register', 'contact_serv_admin').'</font></h1>';
   break;
case 9:
   $output .= '<h1><font class="error">'.lang('register', 'users_ip_range').': '.$usr.' '.lang('register', 'cannot_create_acc').'</font></h1>';
   break;
case 10:
   $output .= '<h1><font class="error">'.lang('register', 'user_mail_not_found').'</font></h1>';
   break;
case 11:
   $output .= '<h1><font class="error">Mailer Error: '.$usr.'</font></h1>';
   break;
case 12:
   $output .= '<h1><font class="error">'.lang('register', 'recovery_mail_sent').'</font></h1>';
   break;
case 13:
    $output .= '<h1><font class="error">'.lang('captcha', 'invalid_code').'</font></h1>';
   break;
case 14:
    $output .= '<h1><font class="error">'.lang('register', 'email_address_used').'</font></h1>';
   break;
default:
   $output .= '<h1><font class="error">'.lang('register', 'fill_all_fields').'</font></h1>';
}

unset($err);

$output .= "
    </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action){
case "doregister":
   doregister();
   break;
case "pass_recovery":
   pass_recovery();
   break;
case "do_pass_recovery":
   do_pass_recovery();
   break;
case "do_pass_activate":
   do_pass_activate();
   break;
default:
    register();
}

unset($action);
unset($action_permission);
//unset($lang_captcha);

require_once("footer.php");
?>
