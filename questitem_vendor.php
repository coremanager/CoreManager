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
valid_login($action_permission['view']);

//########################################################################################################################
// SHOW CHARACTER LIST
//########################################################################################################################
function show_list()
{
  global $realm_id, $output, $logon_db, $characters_db, $itemperpage, $action_permission, $user_lvl, $sql;

  valid_login($action_permission['view']);

  $query = "SELECT * FROM characters WHERE acct='".$_SESSION['user_id']."'";
  $result = $sql['char']->query($query);
  $num_rows = $sql['char']->num_rows($result);

  $output .= "
          <table class='top_hidden'>
            <tr>
              <td>
                <center>";
  $output .= "<fieldset class='half_frame'>
                <legend>".lang('questitem', 'selectchar')."</legend>";
  if($num_rows == 0)
  {
    $output .= "<b>".$_SESSION['login'].", ".lang('questitem', 'nochars')."</b><br /><br />";
    makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);
  }
  else
  {
    $output .= "<form method='get' action='questitem_vendor.php' name='form'>
                  <input type='hidden' name='action' value='selected_char' />
                    <table>";
    if($num_rows > 1)
    {
      while ($field = $sql['char']->fetch_assoc($result))
      {
        $output .= "<tr>
                      <td>
                        <input type='radio' name='charname' value='".$field['name']."' />".$field['name']."
                      </td>
                    </tr>";
      }
    }
    else
    {
      $field = $sql['char']->fetch_assoc($result);
      $output .= "<tr>
                    <td>
                      <input type='radio' name='charname' value='".$field['name']."' checked='true' />".$field['name']."
                    </td>
                  </tr>";
    }
    $output .= "</table>
                <br />";
    makebutton(lang('questitem', 'select'), "javascript:do_submit()\" type=\"def",180);
    makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);
    $output .= "</form>";
  }
  $output .= "</fieldset>
                </center>
              </td>
              </tr>
          </table>";

}


//########################################################################################################################
// SHOW CHARACTER'S QUESTS
//########################################################################################################################
function select_quest()
{
  global $world_db, $characters_db, $realm_id, $user_name, $output, $action_permission, $user_lvl, $sql;

  valid_login($action_permission['insert']);

  if( (empty($_GET['charname'])) )
    redirect("questitem_vendor.php?error=1");

  $query = "SELECT guid,gold,level FROM characters WHERE name = '".$_GET['charname']."'";
  $result = $sql['char']->query($query);
  $field = $sql['char']->fetch_assoc($result);
  $guid = $field['guid'];

  $query = "SELECT * FROM questlog WHERE player_guid = '".$guid."'";
  $result = $sql['char']->query($query);
  $num_rows = $sql['char']->num_rows($result);

  $output .= "
          <table class='top_hidden'>
            <tr>
              <td>
                <center>
                <fieldset class='half_frame'>
                  <legend>".lang('questitem', 'selectquest')."</legend>";

  if ($num_rows == 0)
  {
    $output .= "<b>".$_GET['charname']." ".lang('questitem', 'noquests')."</b><br /><br />";
    makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);
  }
  else
  {
    $output .= "<form method='get' action='questitem_vendor.php' name='form'>
                  <input type='hidden' name='action' value='selected_quest' />
                  <input type='hidden' name='chargold' value='".$field['gold']."' />
                  <input type='hidden' name='charname' value='".$_GET['charname']."' />
                  <input type='hidden' name='charlevel' value='".$field['level']."' />
                    <table>";
    if ($num_rows > 1)
    {
      while ($field = $sql['char']->fetch_assoc($result))
      {
        $qquery = "SELECT * FROM quests WHERE entry = '".$field['quest_id']."'";
        $qresult = $sql['world']->query($qquery);
        $quest = $sql['world']->fetch_assoc($qresult);

        $output .= "<tr><td>
                      <input type='radio' name = 'charquest' value='".$quest['entry']."' />".$quest['Title']."
                    </td></tr>";
      }
    }
    else
    {
      $field = $sql['char']->fetch_assoc($result);
      $qquery = "SELECT * FROM quests WHERE entry = '".$field['quest_id']."'";
      $qresult = $sql['world']->query($qquery);
      $quest = $sql['char']->fetch_assoc($qresult);

      $output .= "<tr><td>
                    <input type='radio' name = 'charquest' value='".$quest['entry']."' checked='true' />".$quest['Title']."
                  </td></tr>";
    }
    $output .= "</table>
                <br />";
    makebutton(lang('questitem', 'select'), "javascript:do_submit()\" type=\"def",180);
    makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);
    $output .= "</form>";
  }
  $output .= "</fieldset>
            </center>
              </td>
              </tr>
          </table>";
}


//########################################################################################################################
// SHOW QUEST'S ITEMS
//########################################################################################################################
function select_item()
{
  global $world_db, $characters_db, $realm_id, $user_name, $output, $action_permission, $user_lvl, $sql;

  valid_login($action_permission['insert']);

  if( (empty($_GET['charquest'])) )
    redirect("questitem_vendor.php?error=1");

  $query = "SELECT * FROM quests WHERE entry = '".$_GET['charquest']."'";
  $result = $sql['world']->query($query);
  $quest = $sql['world']->fetch_assoc($result);

  $output .= "
          <table class='top_hidden'>
            <tr>
              <td>
                <center>
                <fieldset class='half_frame'>
                  <legend>".lang('questitem', 'selectitem')."</legend>";

  if ($quest['ReqItemId1'] == 0)
  {
    $output .= "<b>".$quest['Title']." ".lang('questitem', 'noitems')."</b><br /><br />";
    makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);
  }
  else
  {
    $output .= "<form method='get' action='questitem_vendor.php' name='form'>
                  <input type='hidden' name='action' value='selected_item' />
                  <input type='hidden' name='charname' value='".$_GET['charname']."' />
                  <input type='hidden' name='charquest' value='".$_GET['charquest']."' />
                    <table>";
    if ($quest['ReqItemId1'])
    {
      $iquery = "SELECT * FROM items WHERE entry='".$quest['ReqItemId1']."'";
      $iresult = $sql['world']->query($iquery);
      $item = $sql['world']->fetch_assoc($iresult);
      $output .= "<tr><td>
                    <input type='radio' name = 'questitem' value='".$item['entry']."_".$quest['ReqItemCount1']."' />".$item['name1']."
                  </td></tr>";
    }
    if ($quest['ReqItemId2'] <> 0)
    {
      $iquery = "SELECT * FROM items WHERE entry='".$quest['ReqItemId2']."'";
      $iresult = $sql['world']->query($iquery);
      $item = $sql['world']->fetch_assoc($iresult);
      $output .= "<tr><td>
                    <input type='radio' name = 'questitem' value='".$item['entry']."_".$quest['ReqItemCount2']."' />".$item['name1']."
                  </td></tr>";
    }
    if ($quest['ReqItemId3'] <> 0)
    {
      $iquery = "SELECT * FROM items WHERE entry='".$quest['ReqItemId3']."'";
      $iresult = $sql['world']->query($iquery);
      $item = $sql['world']->fetch_assoc($iresult);
      $output .= "<tr><td>
                    <input type='radio' name = 'questitem' value='".$item['entry']."_".$quest['ReqItemCount3']."' />".$item['name1']."
                  </td></tr>";
    }
    if ($quest['ReqItemId4'] <> 0)
    {
      $iquery = "SELECT * FROM items WHERE entry='".$quest['ReqItemId4']."'";
      $iresult = $sql['world']->query($iquery);
      $item = $sql['world']->fetch_assoc($iresult);
      $output .= "<tr><td>
                    <input type='radio' name = 'questitem' value='".$item['entry']."_".$quest['ReqItemCount4']."' />".$item['name1']."
                  </td></tr>";
    }
    $output .= "</table>
                <br />";
    makebutton(lang('questitem', 'select'), "javascript:do_submit()\" type=\"def",180);
    makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);
    $output .= "</form>";
  }
  $output .= "</fieldset>
            </center>
              </td>
              </tr>
          </table>";
}


//########################################################################################################################
// SELECT QUANTITY OF ITEM
//########################################################################################################################
function select_quantity()
{
  global $world_db, $characters_db, $realm_id, $user_name, $output, $action_permission, $user_lvl,
    $quest_item, $sql;

  valid_login($action_permission['insert']);

  if( (empty($_GET['questitem'])) )
    redirect("questitem_vendor.php?error=1");

  $query = "SELECT * FROM quests WHERE entry = '".$_GET['charquest']."'";
  $result = $sql['world']->query($query);
  $quest = $sql['world']->fetch_assoc($result);

  // this_is_junk: We have to pass the required count with the item id or we'll get the required counts
  //               for every other item the quest requires.
  $questitem = explode("_", $_GET['questitem']);
  $count = $questitem[1];
  $questitem = $questitem[0];

  $iquery = "SELECT * FROM items WHERE entry = '".$questitem."'";
  $iresult = $sql['world']->query($iquery);
  $item = $sql['world']->fetch_assoc($iresult);

  $cquery = "SELECT guid,level,gold FROM characters WHERE name = '".$_GET['charname']."'";
  $cresult = $sql['char']->query($cquery);
  $char = $sql['char']->fetch_assoc($cresult);

  $ciquery = "SELECT * FROM playeritems WHERE ownerguid='".$char['guid']."' AND entry='".$questitem."'";
  $ciresult = $sql['char']->query($ciquery);
  $cifield = $sql['char']->fetch_assoc($ciresult);
  $cinumrows = $sql['char']->num_rows($ciresult);
  if($cinumrows == 0)
  {
    $have = 0;
  }
  elseif($cinumrows == 1)
  {
    $have = $cifield['count'];
  }
  else
  {
    $have = 0;
    while ($field = $sql['char']->fetch_assoc($ciresult))
    {
      $have = $have + $field['count'];
    }
  }

  $chargold = $char['gold'];
  $chargold = str_pad($chargold, 4, "0", STR_PAD_LEFT);
  $pg = substr($chargold,  0, -4);
  if ($pg == '')
    $pg = 0;
  $ps = substr($chargold, -4,  2);
  if ( ($ps == '') || ($ps == '00') )
    $ps = 0;
  $pc = substr($chargold, -2);
  if ( ($pc == '') || ($pc == '00') )
    $pc = 0;

  $RewMoney = $quest['RewMoney'];
  $RewMoney = str_pad($RewMoney, 4, "0", STR_PAD_LEFT);
  $rg = substr($RewMoney,  0, -4);
  if ($rg == '')
    $rg = 0;
  $rs = substr($RewMoney, -4,  2);
  if ( ($rs == '') || ($rs == '00') )
    $rs = 0;
  $rc = substr($RewMoney, -2);
  if ( ($rc == '') || ($rc == '00') )
    $rc = 0;

  $output .= "
          <table class='top_hidden'>
            <tr>
              <td>
                <center>
                <fieldset class='half_frame'>
                  <legend>".lang('questitem', 'selectquantity')."</legend>";
  $output .= "<b>".$_GET['charname']."</b> ".lang('questitem', 'has')." ".$pg."<img src='img/gold.gif' alt='' align='middle' />
             ".$ps."<img src='img/silver.gif' alt='' align='middle' />
             ".$pc."<img src='img/copper.gif' alt='' align='middle' /><br /><br />";
  $output .= "<b>".$quest['Title']."</b> ".lang('questitem', 'willreward')." ".$rg."<img src='img/gold.gif' alt='' align='middle' />
             ".$rs."<img src='img/silver.gif' alt='' align='middle' />
             ".$rc."<img src='img/copper.gif' alt='' align='middle' /><br /><br />";

  if ($quest['RewMoney'] == 0)
  {
    $gold = $char['level'] * $quest_item['levelMul'];
  }
  else
  {
    $gold = $quest['RewMoney'] * $quest_item['rewMul'];
  }
  $gold = str_pad($gold, 4, "0", STR_PAD_LEFT);
  $cg = substr($gold,  0, -4);
  if ($cg == '')
    $cg = 0;
  $cs = substr($gold, -4,  2);
  if ( ($cs == '') || ($cs == '00') )
    $cs = 0;
  $cc = substr($gold, -2);
  if ( ($cc == '') || ($cc == '00') )
    $cc = 0;

  $output .= lang('questitem', 'peritem1')." <b>".$item['name1']."</b> ".lang('questitem', 'peritem2')." ".$cg."<img src='img/gold.gif' alt='' align='middle' />
             ".$cs."<img src='img/silver.gif' alt='' align='middle' />
             ".$cc."<img src='img/copper.gif' alt='' align='middle' /><br /><br />";

  $output .= "<b>".$quest['Title']."</b> ".lang('questitem', 'requires')." <span id='qiv_quest_requires'>".$count."</span>x".
              " <b>".$item['name1']."</b>;<br />".lang('questitem', 'and')." <b>".$_GET['charname']."</b> ".
              lang('questitem', 'has')." <span id='qiv_player_has'>".$have."</span>.<br /><br />";

  $need = $count - $have;

  $output .= "<form method='get' action='questitem_vendor.php' name='form'>
                <input type='hidden' name='action' value='selected_quantity' />
                  <table>";
  $output .= "Quanity desired:&nbsp;";
  $output .= "<input type='text' name='want' value='".$need."' />
              <input type='hidden' name='charname' value='".$_GET['charname']."' />
              <input type='hidden' name='gold' value='".$gold."' />
              <input type='hidden' name='item' value='".$item['entry']."' />";
  $output .= "</table>
                <br />";
  makebutton(lang('questitem', 'submit'), "javascript:do_submit()\" type=\"def",180);
  makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);
  $output .= "</form>";
  $output .= "</fieldset>
            </center>
              </td>
              </tr>
          </table>";
}


//########################################################################################################################
// APPROVE TOTAL COST AND PURCHASE
//########################################################################################################################
function approve()
{
  global $world_db, $characters_db, $realm_id, $user_name, $output, $action_permission, $user_lvl,
    $quest_item, $sql;

  valid_login($action_permission['insert']);

  if( (empty($_GET['item'])) )
    redirect("questitem_vendor.php?error=1");
  if( (empty($_GET['gold'])) )
    redirect("questitem_vendor.php?error=1");
  if( (empty($_GET['want'])) )
    redirect("questitem_vendor.php?error=1");

  $query = "SELECT * FROM items WHERE entry = '".$_GET['item']."'";
  $result = $sql['world']->query($query);
  $item = $sql['world']->fetch_assoc($result);

  $cquery = "SELECT * FROM characters WHERE name = '".$_GET['charname']."'";
  $cresult = $sql['char']->query($cquery);
  $char = $sql['char']->fetch_assoc($cresult);

  $total = $_GET['gold'] * $_GET['want'];
  $total = str_pad($total, 4, "0", STR_PAD_LEFT);
  $cg = substr($total,  0, -4);
  if($cg == '')
    $cg = 0;
  $cs = substr($total, -4,  2);
  if(($cs == '') || ($cs == '00'))
    $cs = 0;
  $cc = substr($total, -2);
  if(($cc == '') || ($cc == '00'))
    $cc = 0;

  $output .= "
          <table class='top_hidden'>
            <tr>
              <td>
                <center>
                <fieldset class='half_frame'>
                  <legend>".lang('questitem', 'approvecost')."</legend>";
  if ($total > $char['gold'])
  {
    $output .= "<b>".$char['name']."</b> ".lang('questitem', 'insufficientfunds')." <span id='qiv_insuffiecient_funds'>".$_GET['want']."</span>x <b>".$item['name1']."</b>.<br /><br />";
    makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);
  }
  else
  {
    $output .= "<form method='get' action='questitem_vendor.php' name='form'>
                  <input type='hidden' name='action' value='purchase' />
                  <input type='hidden' name='char' value='".$char['name']."' />
                  <input type='hidden' name='item' value='".$item['entry']."' />
                  <input type='hidden' name='want' value='".$_GET['want']."' />
                  <input type='hidden' name='total' value='".$total."' />
                    <table>";
    $output .= lang('questitem', 'purchase')." <span id='qiv_approve_quantity'>".$_GET['want']."</span>x <b>"
               .$item['name1']."</b> ".lang('questitem', 'for')." ".$cg."<img src='img/gold.gif' alt='' align='middle' /> "
               .$cs."<img src='img/silver.gif' alt='' align='middle' /> "
               .$cc."<img src='img/copper.gif' alt='' align='middle' />?<br />";
    $output .= "</table>
                <br />";
    makebutton(lang('questitem', 'submit'), "javascript:do_submit()\" type=\"def",180);
    makebutton(lang('global', 'back'), "javascript:window.history.back()\" type=\"def",130);
    $output .= "</form>";
  }
  $output .= "</fieldset>
            </center>
              </td>
              </tr>
          </table>";
}


//########################################################################################################################
// CHARGE THE CHARACTER AND SEND THE ITEM
//########################################################################################################################
function purchase()
{
  global $world_db, $characters_db, $realm_id, $user_name, $output, $action_permission, $user_lvl,
    $from_char, $stationary, $sql;

  valid_login($action_permission['insert']);

  if( (empty($_GET['item'])) )
    redirect("questitem_vendor.php?error=1");
  if( (empty($_GET['total'])) )
    redirect("questitem_vendor.php?error=1");
  if( (empty($_GET['want'])) )
    redirect("questitem_vendor.php?error=1");

  $iquery = "SELECT * FROM items WHERE entry='".$_GET['item']."'";
  $iresult = $sql['world']->query($iquery);
  $item = $sql['world']->fetch_assoc($iresult);

  $cquery = "SELECT * FROM characters WHERE name='".$_GET['char']."'";
  $cresult = $sql['char']->query($cquery);
  $char = $sql['char']->fetch_assoc($cresult);

  $char_money = $char['gold'];
  $char_money = $char_money - $_GET['total'];

  $mail_query = "INSERT INTO mailbox_insert_queue VALUES ('".$from_char."', '".$char['guid']."', '".lang('questitem', 'questitems')."', ".chr(34).$_GET['want']."x ".$item['name1'].chr(34).", '".$stationary."', '0', '".$_GET['item']."', '".$_GET['want']."')";
  $money_query = "UPDATE characters SET gold = '".$char_money."' WHERE guid = '".$char['guid']."'";

  $mail_result = $sql['char']->query($mail_query);
  $money_result = $sql['char']->query($money_query);

  if ($mail_result & $money_result)
    redirect("questitem_vendor.php?error=3");
  else
    redirect("questitem_vendor.php?error=2");
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
      <div class=\"bubble\">
          <div class=\"top\">";

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">".lang('global', 'empty_fields')."</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">".lang('questitem', 'failed')."</font></h1>";
    break;
  case 3:
    $output .= "
          <h1>".lang('questitem', 'done')."</h1>";
    break;
  default: //no error
    $output .= "
          <h1>".lang('questitem', 'title')."</h1>";
}
unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "purchase":
    purchase();
    break;
  case "selected_quantity":
    approve();
    break;
  case "selected_item":
    select_quantity();
    break;
  case "selected_char":
    select_quest();
    break;
  case "selected_quest":
    select_item();
    break;
  default:
    show_list();
}

unset($action);
unset($action_permission);

require_once("footer.php");

?>
