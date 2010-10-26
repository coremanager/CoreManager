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


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/achieve_lib.php';
// minimum permission to view page
valid_login($action_permission["view"]);

//#############################################################################
// SHOW CHARACTERS ACHIEVEMENTS
//#############################################################################
function char_achievements()
{
  global $output, $logon_db, $site_encoding,
    $realm_id, $characters_db, $corem_db,
    $action_permission, $user_lvl, $user_name,
    $achievement_datasite, $sql, $core;

  // this page uses wowhead tooltops
  wowhead_tt();

  // we need at least an id or we would have nothing to show
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
      $sql["char"]->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
    else
      $realmid = $realm_id;
  }

  //-------------------SQL Injection Prevention--------------------------------
  // no point going further if we don have a valid ID
  $id = $sql["char"]->quote_smart($_GET["id"]);
  if ( is_numeric($id) )
    ;
  else
    error(lang("global", "empty_fields"));

  $show_type = ( ( isset($_POST["show_type"]) ) ? $sql["char"]->quote_smart($_POST["show_type"]) : 0 );
  if ( is_numeric($show_type) )
    ;
  else
    $show_type = 0;

  // getting character data from database
  if ( $core == 1 )
    $result = $sql["char"]->query("SELECT acct, name, race, class, level, gender
      FROM characters WHERE guid='".$id."' LIMIT 1");
  else
    $result = $sql["char"]->query("SELECT account AS acct, name, race, class, level, gender
      FROM characters WHERE guid='".$id."' LIMIT 1");

  // no point going further if character does not exist
  if ( $sql["char"]->num_rows($result) )
  {
    $char = $sql["char"]->fetch_assoc($result);

    // we get user permissions first
    $owner_acc_id = $sql["char"]->result($result, 0, 'acct');

    if ( $core == 1 )
      $result = $sql["logon"]->query("SELECT login FROM accounts WHERE acct='".$char["acct"]."'");
    else
      $result = $sql["logon"]->query("SELECT username AS login FROM account WHERE id='".$char["acct"]."'");

    $owner_name = $sql["logon"]->result($result, 0, 'login');
      
    $sec_res = $sql["mgr"]->query("SELECT SecurityLevel AS gm FROM config_accounts WHERE Login='".$owner_name."'");
    $owner_gmlvl = $sql["mgr"]->result($sec_res, 0, 'gm');

    // check user permission
    if ( ( $user_lvl > $owner_gmlvl ) || ( $owner_name === $user_name ) || ( $user_lvl == $action_permission["delete"] ) )
    {
      //------------------------Character Tabs---------------------------------
      // we start with a lead of 10 spaces,
      //  because last line of header is an opening tag with 8 spaces
      //  keep html indent in sync, so debuging from browser source would be easy to read
      $output .= '
          <!-- start of char_achieve.php -->
          <center>
            <script type="text/javascript">
              function expand(thistag)
              {
                var i = 0;
                %%REPLACE%%

                if (thistag == \'tsummary\')
                {
                  document.getElementById(\'tsummary\').style.display="table";
                  document.getElementById(\'divsummary\').innerHTML = \'[-] '.lang("char", "summary").'\' ;
                  for(x in main_cats)
                  {
                    if(document.getElementById(main_cats[x]).style.display=="table")
                    {
                      document.getElementById(main_cats[x]).style.display="none";
                      document.getElementById(main_cats_achieve[x]).style.display="none";
                      document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                    }
                  }
                  for(x in main_sub_cats)
                  {
                    if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                    {
                      document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                      document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                    }
                  }
                }
                else
                {
                  if (document.getElementById(\'tsummary\').style.display="table")
                  {
                    document.getElementById(\'tsummary\').style.display="none";
                    document.getElementById(\'divsummary\').innerHTML = \'[+] '.lang("char", "summary").'\' ;
                  }
                  for(x in main_cats)
                  {
                    if (main_cats[x] == thistag)
                    {
                      i = 1;
                    }
                  }

                  if (i == 1)
                  {
                    for(x in main_cats)
                    {
                      if (main_cats[x] == thistag)
                      {
                        if(document.getElementById(main_cats[x]).style.display=="table")
                        {
                          document.getElementById(main_cats[x]).style.display="none";
                          document.getElementById(main_cats_achieve[x]).style.display="none";
                          document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                          document.getElementById(\'tsummary\').style.display="table";
                          document.getElementById(\'divsummary\').innerHTML = \'[-] '.lang("char", "summary").'\' ;
                        }
                        else
                        {
                          document.getElementById(main_cats[x]).style.display="table";
                          document.getElementById(main_cats_achieve[x]).style.display="table";
                          document.getElementById(main_cats_div[x]).innerHTML = \'[-] \' + main_cats_name[x];
                        }
                      }
                      else
                      {
                        if(document.getElementById(main_cats[x]).style.display=="table")
                        {
                          document.getElementById(main_cats[x]).style.display="none";
                          document.getElementById(main_cats_achieve[x]).style.display="none";
                          document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                        }
                      }
                    }
                    for(x in main_sub_cats)
                    {
                      if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                      {
                        document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                        document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                      }
                    }
                  }
                  else if (i == 0)
                  {
                    for(x in main_sub_cats)
                    {
                      if (main_sub_cats[x] == thistag)
                      {
                        if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                        {
                          document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                          document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                        }
                        else
                        {
                          document.getElementById(main_sub_cats_achieve[x]).style.display="table";
                          document.getElementById(main_sub_cats_div[x]).innerHTML = \'[-] \' + main_sub_cats_name[x];
                        }
                      }
                      else
                      {
                        if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                        {
                          document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                          document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                        }
                      }
                    }
                    for(x in main_cats)
                    {
                      if(document.getElementById(main_cats_achieve[x]).style.display=="table")
                      {
                        document.getElementById(main_cats_achieve[x]).style.display="none";
                      }
                    }
                  }
                }
              }
            </script>
            <div id="tab">
              <ul>
                <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "char_sheet").'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "inventory").'</a></li>
                '.( ( $char["level"] < 10 ) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "talents").'</a></li>' ).'
                <li id="selected"><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "achievements").'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "quests").'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "friends").'</a></li>
                <li><a href="char_view.php?id='.$id.'&amp;realm='.$realmid.'">'.lang("char", "view").'</a></li>
              </ul>
            </div>
            <div id="tab_content">
              <font class="bold">
                '.htmlentities($char["name"], ENT_COMPAT, $site_encoding).' -
                <img src="img/c_icons/'.$char["race"].'-'.$char["gender"].'.gif" onmousemove="oldtoolTip(\''.char_get_race_name($char["race"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" />
                <img src="img/c_icons/'.$char["class"].'.gif" onmousemove="oldtoolTip(\''.char_get_class_name($char["class"]).'\', \'old_item_tooltip\')" onmouseout="oldtoolTip()" alt="" /> - '.lang("char", "level_short").char_get_level_color($char["level"]).'
              </font>
              <br /><br />';
      //---------------Page Specific Data Starts Here--------------------------

      $output .= '
              <table class="top_hidden" id="ch_ach_info">
                <tr>
                  <td width="30%">
                  </td>
                  %%REPLACE_POINTS%%
                  <td align="right">
                    <form action="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'" method="post" name="form">
                      '.lang("char", "show").' :
                      <select name="show_type">
                        <option value="1"'.( ( $show_type == 1 ) ? ' selected="selected"' : '' ).'>'.lang("char", "all").'</option>
                        <option value="0"'.( ( $show_type == 0 ) ? ' selected="selected"' : '' ).'>'.lang("char", "earned").'</option>
                        <option value="2"'.( ( $show_type == 2 ) ? ' selected="selected"' : '' ).'>'.lang("char", "incomplete").'</option>
                      </select>
                    </form>
                  </td>
                  <td align="right">';
      makebutton('View', 'javascript:do_submit()', 130);
      $output .= '
                  </td>
                </tr>
              </table>
              <table class="lined" id="ch_ach_main">
                <tr valign="top">
                  <td id="ch_ach_categories">
                    <table class="hidden" id="ch_ach_categories_list">
                      <tr>
                         <th align="left">
                           <div id="divsummary" onclick="expand(\'tsummary\')">[-] '.lang("char", "summary").'</div>
                         </th>
                      </tr>
                      <tr>
                        <td>
                        </td>
                      </tr>';
      $result = $sql["char"]->query("SELECT achievement, date FROM character_achievement WHERE guid='".$id."'");
      $char_achieve = array();
      while ( $temp = $sql["char"]->fetch_assoc($result) )
        $char_achieve[$temp["achievement"]] = $temp["date"];
      $result = $sql["char"]->query("SELECT achievement, date FROM character_achievement WHERE guid='".$id."' ORDER BY date DESC LIMIT 4");

      $points = 0;

      $main_cats = achieve_get_main_category();
      $sub_cats  = achieve_get_sub_category();

      $output_achieve_main_cat = array();
      $output_u_achieve_main_cat = array();
      $output_achieve_sub_cat = array();
      $output_u_achieve_sub_cat = array();

      $js_main_cats = '
                var main_cats = new Array();
                var main_cats_div = new Array();
                var main_cats_name = new Array();
                var main_cats_achieve = new Array();
                var main_sub_cats = new Array();
                var main_sub_cats_div = new Array();
                var main_sub_cats_name = new Array();
                var main_sub_cats_achieve = new Array();';

      foreach ( $main_cats as $cat_id => $cat )
      {
        if ( isset($cat["Name"]) )
        {
          $i = 0;
          $output_achieve_main_cat[$cat_id] = '';
          $output_u_achieve_main_cat[$cat_id] = '';
          $achieve_main_cat = achieve_get_id_category($cat["ID"]);
          foreach ( $achieve_main_cat as $achieve_id => $cid )
          {
            if ( isset($achieve_id) && isset($cid["id"]) )
            {
              if ( isset($char_achieve[$cid["id"]]) )
              {
                if ( $show_type < 2 )
                {
                  $cid["name01"] = str_replace('&', '&amp;', $cid["name"]);
                  $cid["description01"] = str_replace('&', '&amp;', $cid["description"]);
                  $cid["rewarddesc01"] = str_replace('&', '&amp;', $cid["reward"]);
                  $output_achieve_main_cat[$cat_id] .= '
                      <tr>
                        <td width="1%" align="left">
                          <a href="'.$achievement_datasite.$cid["id"].'" target="_blank">
                            <img src="'.achieve_get_icon($cid["id"]).'" width="36" height="36" class="icon_border_0" alt="" />
                          </a>
                        </td>
                        <td colspan="2" align="left">
                          <a href="'.$achievement_datasite.$cid["id"].'" target="_blank">'.$cid["name"].'</a><br />
                          '.$cid["description"].'<br />
                          '.$cid["reward"].'
                        </td>
                        <td width="5%" align="right">'.$cid["points"].' <img src="img/money_achievement.gif" alt="" /></td>
                        <td width="15%" align="right">'.date('o-m-d', $char_achieve[$cid["id"]]).'</td>
                      </tr>';
                  ++$i;
                }
                $points += $cid["rewpoints"];
              }
              elseif ( $show_type && isset($achieve_id) )
              {
                $cid["name"] = str_replace('&', '&amp;', $cid["name"]);
                $cid["description"] = str_replace('&', '&amp;', $cid["description"]);
                $cid["reward"] = str_replace('&', '&amp;', $cid["reward"]);
                $output_u_achieve_main_cat[$cat_id] .= '
                      <tr>
                        <td width="1%" align="left">
                          <a href="'.$achievement_datasite.$cid["id"].'" target="_blank">
                            <span id="ch_ach_opacity">
                              <img src="'.achieve_get_icon($cid["id"]).'" width="36" height="36" class="icon_border_0" alt="" />
                            </span>
                          </a>
                        </td>
                        <td colspan="2" align="left">
                          <a href="'.$achievement_datasite.$cid["id"].'" target="_blank">'.$cid["name"].'</a><br />
                          '.$cid["description"].'<br />
                          '.$cid["reward"].'
                        </td>
                        <td width="5%" align="right">'.$cid["points"].' <img src="img/money_achievement.gif" alt="" /></td>
                        <td width="15%" align="right">'.lang("char", "incomplete").'</td>
                      </tr>';
                ++$i;
              }
            }
          }
          unset($achieve_main_cat);
          // this_is_junk: unfortunately the CSS here needs to be hardcoded.
          $output_achieve_main_cat[$cat_id] = '
                    <table class="hidden" id="ta'.$cat_id.'" style="width: 100%; display: none;">
                      <tr>
                        <th colspan="3" align="left">'.lang("char", "achievement_title").'</th>
                        <th width="5%">'.lang("char", "achievement_points").'</th>
                        <th width="15%">'.lang("char", "achievement_date").'</th>
                      </tr>'.$output_achieve_main_cat[$cat_id].$output_u_achieve_main_cat[$cat_id].'
                    </table>';
          unset($output_u_achieve_main_cat);
          $js_main_cats .='
                  main_cats_achieve['.$cat_id.'] = "ta'.$cat_id.'";';

          $output_sub_cat = '';
          $total_sub_cat = 0;
          if ( isset($sub_cats[$cat["ID"]]) )
          {
            $main_sub_cats = $sub_cats[$cat["ID"]];
            foreach ( $main_sub_cats as $sub_cat_id => $sub_cat )
            {
              if ( isset($sub_cat) )
              {
                $j = 0;
                $output_achieve_sub_cat[$sub_cat_id] = '';
                $output_u_achieve_sub_cat[$sub_cat_id] = '';
                $achieve_sub_cat = achieve_get_id_category($sub_cat_id);
                foreach ( $achieve_sub_cat as $achieve_id => $cid )
                {
                  if ( isset($achieve_id) && isset($cid["id"]) )
                  {
                    if ( isset($char_achieve[$cid["id"]]) )
                    {
                      if ( $show_type < 2 )
                      {
                        $cid["name"] = str_replace('&', '&amp;', $cid["name"]);
                        $cid["description"] = str_replace('&', '&amp;', $cid["description"]);
                        $cid["reward"] = str_replace('&', '&amp;', $cid["reward"]);
                        $output_achieve_sub_cat[$sub_cat_id] .= '
                            <tr>
                              <td width="1%" align="left">
                                <a href="'.$achievement_datasite.$cid["id"].'" target="_blank">
                                  <img src="'.achieve_get_icon($cid["id"]).'" width="36" height="36" class="icon_border_0" alt="" />
                                </a>
                              </td>
                              <td colspan="2" align="left">
                                <a href="'.$achievement_datasite.$cid["id"].'" target="_blank">'.$cid["name"].'</a><br />
                                '.$cid["description"].'<br />
                                '.$cid["rewarddesc"].'
                              </td>
                              <td width="5%" align="right">'.$cid["points"].' <img src="img/money_achievement.gif" alt="" /></td>
                              <td width="15%" align="right">'.date('o-m-d', $char_achieve[$cid["id"]]).'</td>
                            </tr>';
                        ++$j;
                      }
                      $points += $cid["points"];
                    }
                    elseif ( $show_type && isset($achieve_id) )
                    {
                      $cid["name"] = str_replace('&', '&amp;', $cid["name"]);
                      $cid["description"] = str_replace('&', '&amp;', $cid["description"]);
                      $cid["reward"] = str_replace('&', '&amp;', $cid["reward"]);
                      $output_u_achieve_sub_cat[$sub_cat_id] .= '
                            <tr>
                              <td width="1%" align="left">
                                <a href="'.$achievement_datasite.$cid["id"].'" target="_blank">
                                  <span id="ch_ach_opacity">
                                    <img src="'.achieve_get_icon($cid["id"]).'" width="36" height="36" class="icon_border_0" alt="" />
                                  </span>
                                </a>
                              </td>
                              <td colspan="2" align="left">
                                <a href="'.$achievement_datasite.$cid["id"].'" target="_blank">'.$cid["name"].'</a><br />
                                '.$cid["description"].'<br />
                                '.$cid["reward"].'
                              </td>
                              <td width="5%" align="right">'.$cid["points"].' <img src="img/money_achievement.gif" alt="" /></td>
                              <td width="15%" align="right">'.lang("char", "incomplete").'</td>
                            </tr>';
                      ++$j;
                    }
                  }
                }
                unset($achieve_sub_cat);
                $total_sub_cat = $total_sub_cat + $j;
                if ( $j )
                {
                  $sub_cat["name"] = str_replace('&', '&amp;', $sub_cat["name"]);
                  $output_sub_cat .='
                              <tr>
                                <th align="left">
                                  <div id="divs'.$sub_cat_id.'" onclick="expand(\'tsa'.$sub_cat_id.'\');">[+] '.$sub_cat.' ('.$j.')</div>
                                </th>
                              </tr>';
                  $js_main_cats .='
                      main_sub_cats['.$sub_cat_id.']      = "tsa'.$sub_cat_id.'";
                      main_sub_cats_div['.$sub_cat_id.']  = "divs'.$sub_cat_id.'";
                      main_sub_cats_name['.$sub_cat_id.'] = "'.$sub_cat.' ('.$j.')";';
                  // this_is_junk: unfortunately the CSS here needs to be hardcoded.
                  $output_achieve_sub_cat[$sub_cat_id] = '
                      <table class="hidden" id="tsa'.$sub_cat_id.'" style="width: 100%; display: none;">
                        <tr>
                          <th colspan="3" align="left">'.lang("char", "achievement_title").'</th>
                          <th width="5%">'.lang("char", "achievement_points").'</th>
                          <th width="15%">'.lang("char", "achievement_date").'</th>
                        </tr>'.$output_achieve_sub_cat[$sub_cat_id].$output_u_achieve_sub_cat[$sub_cat_id].'
                      </table>';
                  unset($output_u_achieve_sub_cat);
                  $js_main_cats .='
                      main_sub_cats_achieve['.$sub_cat_id.'] = "tsa'.$sub_cat_id.'";';
                }
              }
            }
            unset($main_sub_cats);
          }
          if ( $total_sub_cat || $i )
          {
            $cat["Name"] = str_replace('&', '&amp;', $cat["Name"]);
            // this_is_junk: unfortunately the CSS here needs to be hardcoded.
            $output .= '
                        <tr>
                          <th align="left">
                            <div id="div'.$cat_id.'" onclick="expand(\'t'.$cat_id.'\');">[+] '.$cat["Name"].' ('.($i+$total_sub_cat).')</div>
                          </th>
                        </tr>
                        <tr>
                          <td>
                            <table class="hidden" id="t'.$cat_id.'" style="width: 100%; display: none;">'.$output_sub_cat.'
                            </table>
                          </td>
                        </tr>';
            $js_main_cats .='
                    main_cats['.$cat_id.']      = "t'.$cat_id.'";
                    main_cats_div['.$cat_id.']  = "div'.$cat_id.'";
                    main_cats_name['.$cat_id.'] = "'.$cat["Name"].' ('.($i+$total_sub_cat).')";';
          }
          unset($output_sub_cat);
        }
      }
      unset($sub_cats);
      unset($main_cats);
      unset($char_achieve);

      $output = str_replace('%%REPLACE%%', $js_main_cats, $output);
      unset($js_main_cats);
      $output = str_replace('%%REPLACE_POINTS%%', '
                  <td align="right">
                    '.lang("char", "achievements").' '.lang("char", "achievement_points").': '.$points.'
                  </td>', $output);
      unset($point);
      $output .= '
                    </table>
                  </td>
                  <td>';

      foreach ( $output_achieve_main_cat as $temp )
        $output .= $temp;
      foreach ( $output_achieve_sub_cat as $temp )
        $output .= $temp;
      unset($temp);
      unset($output_achieve_main_cat);
      unset($output_achieve_sub_cat);

      // this_is_junk: unfortunately the CSS here needs to be hardcoded.
      $output .= '
                    <table class="hidden" id="tsummary" style="width: 100%; display: table;">
                      <tr>
                        <th colspan="5">
                          '.lang("char", "recent").' '.lang("char", "achievements").'
                        </th>
                      </tr>
                      <tr>
                        <th colspan="3" align="left">'.lang("char", "achievement_title").'</th>
                        <th width="5%">'.lang("char", "achievement_points").'</th>
                        <th width="15%">'.lang("char", "achievement_date").'</th>
                      </tr>';
      while ( $temp = $sql["char"]->fetch_assoc($result) )
      {
        $cid = achieve_get_details($temp["achievement"]);
        $cid["name"] = str_replace('&', '&amp;', $cid["name"]);
        $cid["description"] = str_replace('&', '&amp;', $cid["description"]);
        $cid["reward"] = str_replace('&', '&amp;', $cid["reward"]);
        $output .= '
                      <tr>
                        <td width="1%" align="left">
                          <a href="'.$achievement_datasite.$cid["id"].'" target="_blank">
                            <img src="'.achieve_get_icon($cid["id"]).'" width="36" height="36" class="icon_border_0" alt="" />
                          </a>
                        </td>
                        <td colspan="2" align="left">
                          <a href="'.$achievement_datasite.$cid["id"].'" target="_blank">'.$cid["name"].'</a><br />
                          '.$cid["description"].'<br />
                          '.$cid["reward"].'
                        </td>
                        <td width="5%" align="right">'.$cid["points"].' <img src="img/money_achievement.gif" alt="" /></td>
                        <td width="15%" align="right">'.date('o-m-d', $temp["date"]).'</td>
                      </tr>';
      }
      unset($cid);
      unset($temp);
      unset($result);
      $output .= '
                    </table>
                  </td>';
      //---------------Page Specific Data Ends here----------------------------
      //---------------Character Tabs Footer-----------------------------------
      $output .= '
                </tr>
              </table>
            </div>
            <br />
            <table class="hidden">
              <tr>
                <td>';
      // button to user account page, user account page has own security
      makebutton(lang("char", "chars_acc"), 'user.php?action=edit_user&amp;id='.$owner_acc_id.'', 130);
      $output .= '
                </td>
                <td>';

      // only higher level GM with delete access can edit character
      //  character edit allows removal of character items, so delete permission is needed
      if ( ( $user_lvl > $owner_gmlvl ) && ( $user_lvl >= $action_permission["delete"] ) )
      {
                  //makebutton($lang_char["edit_button"], 'char_edit.php?id='.$id.'&amp;realm='.$realmid.'', 130);
        $output .= '
                </td>
                <td>';
      }
      // only higher level GM with delete access, or character owner can delete character
      if ( ( ( $user_lvl > $owner_gmlvl ) && ( $user_lvl >= $action_permission["delete"] ) ) || ( $owner_name === $user_name ) )
      {
        makebutton(lang("char", "del_char"), 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 130);
        $output .= '
                </td>
                <td>';
      }
      // only GM with update permission can send mail, mail can send items, so update permission is needed
      if ( $user_lvl >= $action_permission["update"] )
      {
        makebutton(lang("char", "send_mail"), 'mail.php?type=ingame_mail&amp;to='.$char["name"].'', 130);
        $output .= '
                </td>
                <td>';
      }
      makebutton(lang("global", "back"), 'javascript:window.history.back()" type="def', 130);
      $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>
          <!-- end of char_achieve.php -->';
    }
    else
      error(lang("char", "no_permission"));
  }
  else
    error(lang("char", "no_char_found"));

}


//#############################################################################
// MAIN
//#############################################################################

// action variable reserved for future use
//$action = (isset($_GET["action"])) ? $_GET["action"] : NULL;

$output .= '
      <div class="bubble">';

char_achievements();

unset($action_permission);

require_once 'footer.php';


?>
