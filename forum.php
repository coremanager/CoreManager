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
require_once("configs/forum.conf.php");
require_once("libs/forum_lib.php");
require_once("libs/bb2html_lib.php");
valid_login($action_permission["view"]);

if ( isset($_COOKIE["corem_lang"]) )
{
  $forumlang = $_COOKIE["corem_lang"];

  if ( !file_exists("lang/".$forumlang.".php") )
    $forumlang = $language;
}
else
  $forumlang = $language;

require_once("lang/".$forumlang.".php");

foreach ( $forum_skeleton as $cid => $category )
{
  if ( !isset($category["level_read"]) )
    $forum_skeleton[$cid]["level_read"] = 0;

  if ( !isset($category["level_post"]) )
    $forum_skeleton[$cid]["level_post"] = 0;

  if ( !isset($category["level_post_topic"]) )
    $forum_skeleton[$cid]["level_post_topic"] = 0;

  if ( !isset($category["side_access"]) )
    $forum_skeleton[$cid]["side_access"] = "ALL";

  foreach ( $category["forums"] as $id => $forum)
  {
    if ( !isset($forum["level_read"]) )
      $forum_skeleton[$cid]["forums"][$id]["level_read"] = 0;
    if ( !isset($forum["level_post"]) )
      $forum_skeleton[$cid]["forums"][$id]["level_post"] = 0;
    if ( !isset($forum["level_post_topic"]) )
      $forum_skeleton[$cid]["forums"][$id]["level_post_topic"] = 0;
    if ( !isset($forum["side_access"]) )
      $forum_skeleton[$cid]["forums"][$id]["side_access"] = "ALL";
  }
}


// #######################################################################################################
// Forum_Index: Display the forums in categories
// #######################################################################################################
function forum_index()
{
  global $enablesidecheck, $forum_skeleton, $forumlang, $user_lvl, $output, $logon_db, $corem_db,
    $corem_db, $sql;

  if ( $enablesidecheck )
    $side = get_side();

  $result = $sql["mgr"]->query("SELECT `authorname`, `id`, `name`, `time`, `forum` FROM `forum_posts` WHERE `id` IN (SELECT MAX(`id`) FROM `forum_posts` GROUP BY `forum`) ORDER BY `forum`;");
  $lasts = array();
  if ( $sql["mgr"]->num_rows($result) > 0 )
  {
    while ( $row = $sql["mgr"]->fetch_row($result) )
      $lasts[$row[4]] = $row;
  }
  $output .= '
          <div class="top">
            <h1>'.lang("forum", "forums").'</h1>'.lang("forum", "you_are_here").': <a href="forum.php">'.lang("forum", "forum_index").'</a>
          </div>
          <center>
            <table class="lined">';
  foreach ( $forum_skeleton as $category )
  {
    if ( ( $category["level_read"] > $user_lvl ) )
      continue;
    if ( ( $user_lvl == 0 ) && $enablesidecheck )
    {
      if ( $category["side_access"] != "ALL" )
      { // Not an all side forum
        if ( $side == "NO" ) // No char
          continue;
        elseif ( $category["side_access"] != $side ) // Forumside different of the user side
          continue;
      }
    }
    $output .= '
              <tr>
                <td class="head" align="left">'.$category["name"].'</td>
                <td class="head">'.lang("forum", "topics").'</td>
                <td class="head">'.lang("forum", "replies").'</td>
                <td class="head" align="right">'.lang("forum", "last_post").'</td>
              </tr>';
    foreach ( $category["forums"] as $id => $forum )
    {
      if ( $forum["level_read"] > $user_lvl )
        continue;
      if ( ( $user_lvl == 0 ) && $enablesidecheck )
      {
        if ( $forum["side_access"] != "ALL" )
        { // Not an all side forum
          if ( $side == "NO" ) // No char
            continue;
          elseif ( $forum["side_access"] != $side ) // Forumside different of the user side
            continue;
        }
      }
      $totaltopics = $sql["mgr"]->query("SELECT id FROM forum_posts WHERE forum='".$id."' AND id=`topic`;");
      $numtopics = $sql["mgr"]->num_rows($totaltopics);
      $totalreplies = $sql["mgr"]->query("SELECT id FROM forum_posts WHERE forum='".$id."';");
      $numreplies = $sql["mgr"]->num_rows($totalreplies);
      $output .= '
              <tr>
                <td align="left">
                  <a href="forum.php?action=view_forum&amp;id='.$id.'">'.$forum["name"].'</a>
                  <br />'.$forum["desc"].'
                </td>
                <td>'.$numtopics.'</td>
                <td>'.$numreplies.'</td>';
      if ( isset($lasts[$id]) )
      {
        // Use screen name if available
        $sn_query = "SELECT * FROM config_accounts WHERE Login='".$lasts[$id][0]."'";
        $sn_result = $sql["mgr"]->query($sn_query);
        if ( $sql["mgr"]->num_rows($sn_result) )
        {
          $sn = $sql["mgr"]->fetch_assoc($sn_result);
          $lasts[$id][0] = $sn["ScreenName"];
        }
        $lasts[$id][2] = htmlspecialchars($lasts[$id][2]);
        $output .= '
                <td align="right">
                  <a href="forum.php?action=view_topic&amp;postid='.$lasts[$id][1].'">'.$lasts[$id][2].'</a>
                  <br />'
                  .lang("forum", "by").': '.$lasts[$id][0].'
                  <br />'
                  .$lasts[$id][3].'
                </td>
              </tr>';
      }
      else
      {
        $output .= '
                <td align="right">'.lang("forum", "no_topics").'</td>
              </tr>';
      }
    }
  }
  $output .= '
              <tr>
                <td align="right" class="hidden"></td>
              </tr>
            </table>
          </center>
          <br/>';
}


// #######################################################################################################
//
// #######################################################################################################
function forum_view_forum()
{
  global $enablesidecheck, $forum_skeleton, $maxqueries, $user_lvl, $output, $corem_db, $sql;

  if ( $enablesidecheck )
    $side = get_side();

  if ( !isset($_GET["id"]) )
    error(lang("forum", "no_such_forum"));
  else
    $id = $sql["mgr"]->quote_smart($_GET["id"]);
  if ( !isset($_GET["page"]) )
    $page = 0;
  else
    $page = $sql["mgr"]->quote_smart($_GET["page"]);
  $cat = 0;
  foreach ( $forum_skeleton as $cid => $category )
  {
    foreach ( $category["forums"] as $fid => $forum )
    {
      if ( $fid == $id )
        $cat = $cid;
    }
  }
  if ( empty($forum_skeleton[$cat]["forums"][$id]) )
    error(lang("forum", "no_such_forum"));
  $forum = $forum_skeleton[$cat]["forums"][$id];
  if ( ( $forum_skeleton[$cat]["level_read"] > $user_lvl ) || ( $forum["level_read"] > $user_lvl ) )
    error(lang("forum", "no_access"));

  if ( ( $user_lvl == 0 ) && $enablesidecheck )
  {
    if ( $forum_skeleton[$cat]["side_access"] != "ALL" )
    { // Not an all side forum
      if ( $side == "NO" ) // No char
        continue;
      elseif ( $forum_skeleton[$cat]["side_access"] != $side ) // Forumside different of the user side
        continue;
    }
    if ( $forum["side_access"] != "ALL" )
    { // Not an all side forum
      if ( $side == "NO" ) // No char
        continue;
      elseif ( $forum["side_access"] != $side ) // Forumside different of the user side
        continue;
    }
  }

  $start = ($maxqueries * $page);
  $output .= '
          <div class="top">
            <h1>'.lang("forum", "forums").'</h1>'.lang("forum", "you_are_here").': <a href="forum.php">'.lang("forum", "forum_index").'</a> -> <a href="forum.php?action=view_forum&amp;id='.$id.'">'.$forum["name"].'</a>
          </div>
          <center>
            <table class="lined">';
  $topics = $sql["mgr"]->query("SELECT id, authorid, authorname, name, announced, sticked, closed FROM forum_posts WHERE (forum='".$id."' AND id=`topic`) OR announced = 1 AND id=`topic` ORDER BY announced DESC, sticked DESC, lastpost DESC LIMIT ".$start.", ".$maxqueries.";");
  $result = $sql["mgr"]->query("SELECT `topic` AS curtopic, (SELECT COUNT(`id`)-1 FROM forum_posts WHERE `topic`=`curtopic`) AS replies, lastpost AS curlastpost, (SELECT authorname FROM forum_posts WHERE id=curlastpost) AS authorname, (SELECT time FROM forum_posts WHERE id=curlastpost) AS time FROM `forum_posts` WHERE (`forum`=".$id." AND `topic`=`id` ) OR announced=1;");
  $lasts = array();
  if ( $sql["mgr"]->num_rows($result) > 0 )
  {
    while ( $row = $sql["mgr"]->fetch_row($result) )
      $lasts[$row[0]] = $row;
  }
  if ( $forum_skeleton[$cat]["level_post_topic"] <= $user_lvl && $forum["level_post_topic"] <= $user_lvl )
    $output .= '
              <tr>
                <td colspan="4" id="forum_topic_list_header">
                  <a href="forum.php?action=add_topic&amp;id='.$id.'">'.lang("forum", "new_topic").'</a>
                </td>
              </tr>';
  if ( $sql["mgr"]->num_rows($topics) != 0 )
  {
    $output .= '
              <tr>
                <td id="forum_topic_list_header_title">'.lang("forum", "title").'</td>
                <td id="forum_topic_list_header_author">'.lang("forum", "author").'</td>
                <td>'.lang("forum", "replies").'</td>
                <td>'.lang("forum", "last_post").'</td>
              </tr>';
    while ( $topic = $sql["mgr"]->fetch_row($topics) )
    {
      $output .= '
            <tr>
              <td id="forum_topic_list_title">';
      if ( $topic[4] == "1" )
        $output .= lang("forum", "announcement").": ";
      else
      {
        if ( $topic[5] == "1" )
          $output .= lang("forum", "sticky").": ";
        else
        {
          if ( $topic[6] == "1" )
            $output .= "[".lang("forum", "closed")."] ";
        }
      }
      $topic[3] = htmlspecialchars($topic[3]);
      // Use screen name if available
      $sn_query = "SELECT * FROM config_accounts WHERE Login='".$topic[2]."'";
      $sn_result = $sql["mgr"]->query($sn_query);
      if ( $sql["mgr"]->num_rows($sn_result) )
      {
        $sn = $sql["mgr"]->fetch_assoc($sn_result);
        $topic[2] = $sn["ScreenName"];
      }
      $output .= '
                  <a href="forum.php?action=view_topic&amp;id='.$topic[0].'">'.$topic[3].'</a>
                </td>
                <td>'.$topic[2].'</td>
                <td>'.$lasts[$topic[0]][1].'</td>
                <td>'.lang("forum", "last_post_by").' '.$lasts[$topic[0]][3].', '.$lasts[$topic[0]][4].'</td>
              </tr>';
    }
    $totaltopics = $sql["mgr"]->query("SELECT id FROM forum_posts WHERE forum='".$id."' AND id=`topic`;"); //My page system is so roxing, i can' t break this query xD
    $pages = ceil($sql["mgr"]->num_rows($totaltopics)/$maxqueries);
    $output .= '
              <tr>
                <td align="right" colspan="4">'.lang("forum", "pages").': ';
    for ( $x = 1; $x <= $pages; $x++ )
    {
      $y = $x-1;
      $output .= '
                  <a href="forum.php?action=view_forum&amp;id='.$id.'&amp;page='.$y.'">'.$x.'</a> ';
    }
    $output .= '
                </td>
              </tr>';
  }
  else
    $output .= '
              <tr>
                <td>'.lang("forum", "no_topics").'</td>
              </tr>';

  $output .= '
              <tr>
                <td align="right" class="hidden"></td>
              </tr>
            </table>
          </center>
          <br/>';
  // Queries: 3
}


// #######################################################################################################
//
// #######################################################################################################
function forum_view_topic()
{
  global $enablesidecheck, $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output,
    $realm_db, $characters_db, $realm_id, $corem_db, $logon_db, $corem_db, $sql, $core;

  if ( $enablesidecheck )
    $side = get_side(); // Better to use it here instead of call it many time in the loop :)

  if ( isset($_GET["id"]) )
  {
    $id = $sql["mgr"]->quote_smart($_GET["id"]);
    $post = false;
  }
  else
  {
    if ( isset($_GET["postid"]) )
    {
      $id = $sql["mgr"]->quote_smart($_GET["postid"]);
      $post = true;
    }
    else
      error(lang("forum", "no_such_topic"));
  }


  if ( !isset($_GET["page"]) )
    $page = 0;
  else
    $page = $sql["mgr"]->quote_smart($_GET["page"]); // Fok you mathafoker haxorz
  $start = ($maxqueries * $page);

  if ( !$post )
  {
    $posts = $sql["mgr"]->query("SELECT id, authorid, authorname, forum, name, text, time, announced, sticked, closed FROM forum_posts WHERE topic='".$id."' ORDER BY id ASC LIMIT ".$start.", ".$maxqueries.";");

    // Thx qsa for the query structure
    if ( $core == 1 )
      $query = "SELECT acct, name, gender, race, class, level,
        (SELECT gm FROM `".$logon_db["name"]."`.accounts WHERE `".$logon_db["name"]."`.accounts.acct=`".$characters_db[$realm_id]['name']."`.characters.acct) AS gmlevel,
        (SELECT login FROM `".$logon_db["name"]."`.accounts WHERE `".$logon_db["name"]."`.accounts.acct=`".$characters_db[$realm_id]['name']."`.characters.acct) AS login
        FROM `".$characters_db[$realm_id]['name']."`.characters
        WHERE level IN (SELECT MAX(level) FROM `".$characters_db[$realm_id]['name']."`.characters WHERE acct IN (";
    elseif ( $core == 2 )
      $query = "SELECT account AS acct, name, gender, race, class, level,
        (SELECT gmlevel FROM `".$logon_db["name"]."`.account WHERE `".$logon_db["name"]."`.account.id=`".$characters_db[$realm_id]['name']."`.characters.account) AS gmlevel,
        (SELECT username FROM `".$logon_db["name"]."`.account WHERE `".$logon_db["name"]."`.account.id=`".$characters_db[$realm_id]['name']."`.characters.account) AS login
        FROM `".$characters_db[$realm_id]['name']."`.characters
        WHERE level IN (SELECT MAX(level) FROM `".$characters_db[$realm_id]['name']."`.characters WHERE account IN (";
    elseif ( $core == 3 )
      $query = "SELECT account AS acct, name, gender, race, class, level,
        (SELECT gmlevel FROM `".$logon_db["name"]."`.account_access WHERE `".$logon_db["name"]."`.account_access.id=`".$characters_db[$realm_id]['name']."`.characters.account) AS gmlevel,
        (SELECT username FROM `".$logon_db["name"]."`.account WHERE `".$logon_db["name"]."`.account.id=`".$characters_db[$realm_id]['name']."`.characters.account) AS login
        FROM `".$characters_db[$realm_id]['name']."`.characters
        WHERE level IN (SELECT MAX(level) FROM `".$characters_db[$realm_id]['name']."`.characters WHERE account IN (";

    while ( $post = $sql["mgr"]->fetch_row($posts) )
    {
      $query .= $post[1].",";
    }

    mysql_data_seek($posts, 0);

    if ( $core == 1 )
      $query .= "0) GROUP BY acct);";
    else
      $query .= "0) GROUP BY account);";

    $results = $sql["mgr"]->query($query);

    while ( $avatar = $sql["mgr"]->fetch_row($results) )
    {
      // get the post's author's prefered avatar
      $avatar_query = "SELECT Avatar, SecurityLevel,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 1), ' ', -1) AS sex,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 2), ' ', -1) AS race,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 3), ' ', -1) AS class,
        SUBSTRING_INDEX(SUBSTRING_INDEX(Avatar, ' ', 4), ' ', -1) AS level
        FROM config_accounts WHERE Login='".$avatar[7]."'";
      $avatar_result = $sql["mgr"]->query($avatar_query);
      $avatar_fields = $sql["mgr"]->fetch_assoc($avatar_result);

      if ( $avatar_fields["Avatar"] == '' )
      {
        // if the user doesn't have a prefered avatar we go with the generated one
        $avatars[$avatar[0]]["name"] = $avatar[7];
        $avatars[$avatar[0]]["sex"] = $avatar[2];
        $avatars[$avatar[0]]["race"] = $avatar[3];
        $avatars[$avatar[0]]["class"] = $avatar[4];
        $avatars[$avatar[0]]["level"] = $avatar[5];
        $avatars[$avatar[0]]["gm"] = $avatar_fields["SecurityLevel"];
      }
      else
      {
        // otherwise we use the prefered one
        $avatars[$avatar[0]]["name"] = $avatar[7];
        $avatars[$avatar[0]]["sex"] = $avatar_fields["sex"];
        $avatars[$avatar[0]]["race"] = $avatar_fields["race"];
        $avatars[$avatar[0]]["class"] = $avatar_fields["class"];
        $avatars[$avatar[0]]["level"] = $avatar_fields["level"];
        $avatars[$avatar[0]]["gm"] = $avatar_fields["SecurityLevel"];
      }
    }


    $replies = $sql["mgr"]->num_rows($posts);
    if ( $replies == 0 )
      error(lang("forum", "no_such_topic"));
    $post = $sql["mgr"]->fetch_row($posts);
    $fid = $post[3];
    $cat = 0;
    foreach ( $forum_skeleton as $cid => $category )
    {
      foreach ( $category["forums"] as $fid_ => $forum )
      {
        if ( $fid_ == $fid )
          $cat = $cid;
      }
    }
    if ( empty($forum_skeleton[$cat]["forums"][$fid]) )
      error(lang("forum", "no_such_forum"));
    $forum = $forum_skeleton[$cat]["forums"][$fid];
    if ( ( $forum_skeleton[$cat]["level_read"] > $user_lvl ) || ( $forum["level_read"] > $user_lvl ) )
      error(lang("forum", "no_access"));

    if ( ( $user_lvl == 0 ) && $enablesidecheck )
    {
      if ( $forum_skeleton[$cat]["side_access"] != "ALL" )
      { // Not an all side forum
        if ( $side == "NO" ) // No char
          continue;
        elseif ( $forum_skeleton[$cat]["side_access"] != $side ) // Forumside different of the user side
          continue;
      }
      if ( $forum["side_access"] != "ALL" )
      { // Not an all side forum
        if ( $side == "NO" ) // No char
          continue;
        elseif ( $forum["side_access"] != $side ) // Forumside different of the user side
          continue;
      }
    }

    $post[4] = htmlspecialchars($post[4]);

    // get our user's signature
    if ( $core == 1 )
      $sig_user_query = "SELECT login FROM `".$logon_db["name"]."`.accounts WHERE acct='".$post[1]."'";
    else
      $sig_user_query = "SELECT username AS login FROM `".$logon_db["name"]."`.account WHERE id='".$post[1]."'";

    $sig_query = "SELECT Signature FROM config_accounts WHERE Login=(".$sig_user_query.")";
    $sig_result = $sql["mgr"]->query($sig_query);
    $sig_fields = $sql["mgr"]->fetch_assoc($sig_result);

    // append the signature to the post
    if ( !( ( $sig_fields["Signature"] == '' ) || ( $sig_fields["Signature"] == NULL ) ) )
      $post[5] .= "\n\n".$sig_fields["Signature"];

    $post[5] = bb2html($post[5]);

    $output .= '
        <div class="top">
          <h1>'.lang("forum", "forums").'</h1>'.lang("forum", "you_are_here").': <a href="forum.php">'.lang("forum", "forum_index").'</a> -> <a href="forum.php?action=view_forum&amp;id='.$fid.'">'.$forum["name"].'</a> -> <a href="forum.php?action=view_topic&amp;id='.$id.'">'.$post[4].'</a>
        </div>
        <center>
          <table class="lined">
            <tr>
              <td id="forum_topic_header_info">'.lang("forum", "info").'</td>
              <td id="forum_topic_header_text">'.lang("forum", "text").'</td>
              <td id="forum_topic_header_misc">';
    if ( $user_lvl > 0 )
    {
      if ( $post[8] == "1" )
      {
        if ( $post[7] == "1" )
        {
          // Announcement
          $output .= 
                lang("forum", "announcement").'
                <a href="forum.php?action=edit_announce&amp;id='.$post[0].'&amp;state=0">
                  <img src="img/forums/down.gif" border="0" alt="'.lang("forum", "down").'" />
                </a>';
        }
        else
        {
          // Sticky
          $output .= 
                lang("forum", "sticky").'
                <a href="forum.php?action=edit_stick&amp;id='.$post[0].'&amp;state=0">
                  <img src="img/forums/down.gif" border="0" alt="'.lang("forum", "down").'" />
                </a>
                <a href="forum.php?action=edit_announce&amp;id='.$post[0].'&amp;state=1">
                  <img src="img/forums/up.gif" border="0" alt="'.lang("forum", "up").'" />
                </a>';
        }
      }
      else
      {
        if ( $post[7] == "1" )
        {
          // Announcement
          $output .= 
                lang("forum", "announcement").'
                <a href="forum.php?action=edit_announce&amp;id='.$post[0].'&amp;state=0">
                  <img src="img/forums/down.gif" border="0" alt="'.lang("forum", "down").'" />
                </a>';
        }
        else
        {
          // Normal Topic
          $output .= 
                lang("forum", "normal").'
                <a href="forum.php?action=edit_stick&amp;id='.$post[0].'&amp;state=1">
                  <img src="img/forums/up.gif" border="0" alt="'.lang("forum", "up").'" />
                </a>';
        }
      }

      if ( $post[9] == "1" )
        $output .= '
                <a href="forum.php?action=edit_close&amp;id='.$post[0].'&amp;state=0">
                  <img src="img/forums/lock.gif" border="0" alt="'.lang("forum", "open").'" />
                </a>';
      else
        $output .= '
                <a href="forum.php?action=edit_close&amp;id='.$post[0].'&amp;state=1">
                  <img src="img/forums/unlock.gif" border="0" alt="'.lang("forum", "close").'" />
                </a>';

      $output .= '
                <a href="forum.php?action=move_topic&amp;id='.$post[0].'">
                  <img src="img/forums/move.gif" border="0" alt="'.lang("forum", "move").'" />
                </a>';
    }
    if ( isset($avatars[$post[1]]) )
      $avatar = gen_avatar_panel($avatars[$post[1]]["level"], $avatars[$post[1]]["sex"], $avatars[$post[1]]["race"], $avatars[$post[1]]["class"], ( ( $avatars[$post[1]]["gm"] ) ? 0 : 1 ), $avatars[$post[1]]["gm"]);
    else
      $avatar = "";

    $output .= '
              </td>
            </tr>
            <tr>
              <td id="forum_topic_avatar">
                <center>'.$avatar.'</center>'.lang("forum", "author").': ';
    if ( $user_lvl > 0 )
      $output .= '
                <a href="user.php?action=edit_user&error=11&acct='.$post[1].'">';
    // Use screen name if available
    // we have to get the actual login name first here
    if ( $core == 1 )
      $un_query = "SELECT * FROM accounts WHERE acct='".$post[1]["name"]."'";
    else
      $un_query = "SELECT * FROM account WHERE id='".$post[1]["name"]."'";
    $un_results = $sql["logon"]->query($un_query);
    $un = $sql["logon"]->fetch_assoc($un_results);
    $sn_query = "SELECT * FROM config_accounts WHERE Login='".$un["login"]."'";
    $sn_result = $sql["mgr"]->query($sn_query);
    if ( $sql["mgr"]->num_rows($sn_result) )
    {
      $sn = $sql["mgr"]->fetch_assoc($sn_result);
      $post[1]["name"] = $sn["ScreenName"];
      $post[2] = $sn["ScreenName"];
    }
    if ( isset($avatars[$post[1]]) )
      $output .= $avatars[$post[1]]["name"];
    else
      $output .= $post[2];
    if ( $user_lvl > 0 )
      $output .= '
                </a>';

    $output .= '
                <br /> '
                .lang("forum", "at").': '.$post[6].'
              </td>
              <td colspan="2" id="forum_topic_text">'
                .$post[5].'
                <br />
                <div id="forum_topic_controls">';
    if ( ( $user_lvl > 0 ) || ( $user_id == $post[1] ) )
      $output .= '
                  <a href="forum.php?action=edit_post&amp;id='.$post[0].'">
                    <img src="img/forums/edit.gif" border="0" alt="'.lang("forum", "edit").'" />
                  </a>
                  <a href="forum.php?action=delete_post&amp;id='.$post[0].'">
                    <img src="img/forums/delete.gif" border="0" alt="'.lang("forum", "delete").'" />
                  </a>';

    $output .= '
                </div>
              </td>
            </tr>';
    $closed = $post[9];

    while ( $post = $sql["mgr"]->fetch_row($posts) )
    {
      // get our user's signature
      if ( $core == 1 )
        $sig_user_query = "SELECT login FROM `".$logon_db["name"]."`.accounts WHERE acct='".$post[1]."'";
      else
        $sig_user_query = "SELECT username AS login FROM `".$logon_db["name"]."`.account WHERE id='".$post[1]."'";

      $sig_query = "SELECT Signature FROM config_accounts WHERE Login=(".$sig_user_query.")";
      $sig_result = $sql["mgr"]->query($sig_query);
      $sig_fields = $sql["mgr"]->fetch_assoc($sig_result);

      // append the signature to the post
      if ( !( ( $sig_fields["Signature"] == '' ) || ( $sig_fields["Signature"] == NULL ) ) )
        $post[5] .= "\n\n".$sig_fields["Signature"];

      $post[5] = bb2html($post[5]);

      if ( isset($avatars[$post[1]]) )
        $avatar = gen_avatar_panel($avatars[$post[1]]["level"], $avatars[$post[1]]["sex"], $avatars[$post[1]]["race"], $avatars[$post[1]]["class"], ( ( $avatars[$post[1]]["gm"] ) ? 0 : 1 ), $avatars[$post[1]]["gm"]);
      else
        $avatar = "";

      $output .= '
            <tr>
              <td id="forum_topic_reply_avatar">
                <center>'.$avatar.'</center>'.lang("forum", "author").': ';
      if ( $user_lvl > 0 )
        $output .= '
                <a href="user.php?action=edit_user&error=11&acct='.$post[1].'">';
      // Use screen name if available
      // we have to get the actual login name first here
      if ( $core == 1 )
        $un_query = "SELECT * FROM accounts WHERE acct='".$post[1]["name"]."'";
      else
        $un_query = "SELECT * FROM account WHERE id='".$post[1]["name"]."'";
      $un_results = $sql["logon"]->query($un_query);
      $un = $sql["logon"]->fetch_assoc($un_results);
      $sn_query = "SELECT * FROM config_accounts WHERE Login='".$un["login"]."'";
      $sn_result = $sql["mgr"]->query($sn_query);
      if ( $sql["mgr"]->num_rows($sn_result) )
      {
        $sn = $sql["mgr"]->fetch_assoc($sn_result);
        $post[1]["name"] = $sn["ScreenName"];
        $post[2] = $sn["ScreenName"];
      }
      if ( isset($avatars[$post[1]]) )
        $output .= $avatars[$post[1]]["name"];
      else
        $output .= $post[2];
      if ( $user_lvl > 0 )
        $output .= '
                </a>';

      $output .= '
                <br /> '
                .lang("forum", "at").': '.$post[6].'
              </td>
              <td colspan="2" id="forum_topic_reply_text">'
                .$post[5].'
                <br />';
      if ( ( $user_lvl > 0 ) || ( $user_id == $post[1] ) )
        $output .= '
                <div id="forum_topic_reply_controls">
                  <a href="forum.php?action=edit_post&amp;id='.$post[0].'">
                    <img src="img/forums/edit.gif" border="0" alt="'.lang("forum", "edit").'" />
                  </a>
                  <a href="forum.php?action=delete_post&amp;id='.$post[0].'">
                    <img src="img/forums/delete.gif" border="0" alt="'.lang("forum", "delete").'" />
                  </a>
                </div>';

      $output .= '
              </td>
            </tr>';
    }


    $totalposts = $sql["mgr"]->query("SELECT id FROM forum_posts WHERE topic='".$id."';");
    $totalposts = $sql["mgr"]->num_rows($totalposts);

    $pages = ceil($totalposts/$maxqueries);
    $output .= '
            <tr>
              <td align="right" colspan="3">'.lang("forum", "pages").': ';
    for ( $x = 1; $x <= $pages; $x++ )
    {
      $y = $x-1;
      $output .= '
                <a href="forum.php?action=view_topic&amp;id='.$id.'&amp;page='.$y.'">'.$x.'</a> ';
    }
    $output .= '
              </td>
            </tr>
            <tr>
              <td align="right" class="hidden"></td>
            </tr>
          </table>';

    // Quick reply form
    if ( ( ( $user_lvl > 0 ) || !$closed ) && ( $forum_skeleton[$cat]["level_post"] <= $user_lvl && $forum["level_post"] <= $user_lvl ) )
    {
      $output .= '
          <form action="forum.php?action=do_add_post" method="POST" name="form">
            <table class="top_hidden">
              <tr>
                <td>
                  <center>'.lang("forum", "quick_reply").'</center>
                </td>
              </tr>
              <tr>
                <td colspan="2">';
      bbcode_add_editor();
      $output .= '
                  <textarea id="msg" name="msg" rows=8 cols=93></textarea>
                </td>
              </tr>
              <tr>
                <td align="left">';
      makebutton(lang("forum", "post"), "javascript:do_submit()", 100);
      $output .= '
                </td>
              </tr>
            </table>
            <br/>
            <input type="hidden" name="forum" value="'.$fid.'" />
            <input type="hidden" name="topic" value="'.$id.'" />
          </form>';
    }

    $output .= '
        </center>';
  }
  else
  {
    $output .= '
      <div class="top">
        <h1>Stand by...</h1>
      </div>';

    $post = $sql["mgr"]->query("SELECT topic, id FROM forum_posts WHERE id='".$id."'"); // Get our post id
    if ( $sql["mgr"]->num_rows($post) == 0 )
      error(lang("forum", "no_such_topic"));
    $post = $sql["mgr"]->fetch_row($post);
    if ( $post[0] == $post[1] )
      redirect("forum.php?action=view_topic&id=".$id);
    $topic = $post[0];
    $posts = $sql["mgr"]->query("SELECT id FROM forum_posts WHERE topic='".$topic."';"); // Get posts in our topic
    $replies = $sql["mgr"]->num_rows($posts);
    if ( $replies == 0 )
      error(lang("forum", "no_such_topic"));
    $row = 0;
    while ( $post = $sql["mgr"]->fetch_row($posts) )
    { // Find the row of our post, so we could have his ratio (topic x/total topics) and knew the page to show
      $row++;
      if ( $topic == $id )
        break;
    }
    $page = 0;
    while ( ($page * $maxqueries) < $row )
    {
      $page++;
    };
    $page--;
    redirect("forum.php?action=view_topic&id=".$topic."&page=".$page);
  }
  // Queries: 2 with id || 2 (+2) with postid
}


function forum_do_edit_close()
{
  global $user_lvl, $corem_db, $sql;

  if ( $user_lvl == 0 )
    error(lang("forum", "no_access"));

  if ( !isset($_GET["id"]) )
    error(lang("forum", "no_such_topic"));
  else
    $id = $sql["mgr"]->quote_smart($_GET["id"]);

  if ( !isset($_GET["state"]) )
    error("Bad request, please mail admin and describe what you did to get this error.");
  else
    $state = $sql["mgr"]->quote_smart($_GET["state"]);

  $sql["mgr"]->query("UPDATE forum_posts SET closed='".$state."' WHERE id='".$id."'");

  redirect("forum.php?action=view_topic&id=".$id);
  // Queries: 1
}

function forum_do_edit_announce()
{
  global $user_lvl, $corem_db, $sql;

  if ( $user_lvl == 0 )
    error(lang("forum", "no_access"));

  if ( !isset($_GET["id"]) )
    error(lang("forum", "no_such_topic"));
  else
    $id = $sql["mgr"]->quote_smart($_GET["id"]);

  if ( !isset($_GET["state"]) )
    error("Bad request, please mail admin and describe what you did to get this error.");
  else
    $state = $sql["mgr"]->quote_smart($_GET["state"]);

  $sql["mgr"]->query("UPDATE forum_posts SET announced='".$state."' WHERE id='".$id."'");

  redirect("forum.php?action=view_topic&id=".$id);
  // Queries: 1
}
function forum_do_edit_stick()
{
  global $user_lvl, $corem_db, $sql;

  if ( $user_lvl == 0 )
    error(lang("forum", "no_access"));

  if ( !isset($_GET["id"]) )
    error(lang("forum", "no_such_topic"));
  else
    $id = $sql["mgr"]->quote_smart($_GET["id"]);

  if ( !isset($_GET["state"]) )
    error("Bad request, please mail admin and describe what you did to get this error.");
  else
    $state = $sql["mgr"]->quote_smart($_GET["state"]);

  $sql["mgr"]->query("UPDATE forum_posts SET sticked='".$state."' WHERE id='".$id."'");

  redirect("forum.php?action=view_topic&id=$id");
  // Queries: 1
}

function forum_delete_post()
{
  global $enablesidecheck, $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output, $corem_db, $sql;
  
  if ( !isset($_GET["id"]) )
    error(lang("forum", "no_such_post"));
  else
    $id = $sql["mgr"]->quote_smart($_GET["id"]);

  $topic = $sql["mgr"]->query("SELECT id, topic, authorid, forum FROM forum_posts WHERE id='".$id."';");
  if ( $sql["mgr"]->num_rows($topic) == 0 )
    error(lang("forum", "no_such_post"));
  $topic = $sql["mgr"]->fetch_row($topic);
  if ( ( $user_lvl == 0 ) && ( $topic[2] != $user_id ) )
    error(lang("forum", "no_access"));
  $fid = $topic[3];

  $topic2 = $sql["mgr"]->query("SELECT name FROM forum_posts WHERE id='".$topic[1]."';");
  $name = $sql["mgr"]->fetch_row($topic2);

  $cat = 0;
  foreach ( $forum_skeleton as $cid => $category )
  {
    foreach ( $category["forums"] as $fid_ => $forum )
    {
      if ( $fid_ == $fid )
        $cat = $cid;
    }
  }

  if ( empty($forum_skeleton[$cat]["forums"][$fid]) ) // No such forum..
    error(lang("forum", "no_such_forum"));
  $forum = $forum_skeleton[$cat]["forums"][$fid];
  $output .= '
          <div class="top">
            <h1>'.lang("forum", "forums").'</h1>'.lang("forum", "you_are_here").': <a href="forum.php">'.lang("forum", "forum_index").'</a> -> <a href="forum.php?action=view_forum&amp;id='.$fid.'">'.$forum["name"].'</a> -> <a href="forum.php?action=view_topic&amp;id='.$topic[1].'">'.$name[0].'</a> -> '.lang("forum", "delete").'!
            </div>
            <center>
              <table class="lined">';
  if ( $topic[0] == $topic[1] )
    $output .= '
                <tr>
                  <td>'.lang("forum", "delete_topic").'</td>
                </tr>
              </table>
              <table class="hidden">
                <tr>
                  <td>';
  else
    $output .= '
                <tr>
                  <td>'.lang("forum", "delete_post").'</td>
                </tr>
              </table>
              <table class="hidden">
                <tr>
                  <td>';
  makebutton(lang("forum", "back"), "javascript:window.history.back()", 120);
  $output .= '
                  </td>
                  <td>';
  makebutton(lang("forum", "confirm"), "forum.php?action=do_delete_post&amp;id={$topic[0]}", 120);
  $output .= '
                  </td>
                </tr>
              </table>
            </center>';
  // Queries: 1
}

function forum_do_delete_post()
{
  global $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output, $corem_db, $sql;

  if ( !isset($_GET["id"]) )
    error(lang("forum", "no_such_post"));
  else
    $id = $sql["mgr"]->quote_smart($_GET["id"]);

  $topic = $sql["mgr"]->query("SELECT id, topic, name, authorid, forum FROM forum_posts WHERE id='".$id."';");
  if ( $sql["mgr"]->num_rows($topic) == 0 )
    error(lang("forum", "no_such_post"));
  $topic = $sql["mgr"]->fetch_row($topic);
  if ( ( $user_lvl == 0 ) && ( $topic[3] != $user_id ) )
    error(lang("forum", "no_access"));
  $fid = $topic[4];

  if ( $id == $topic[1] )
  {
    $sql["mgr"]->query("DELETE FROM forum_posts WHERE topic='".$id."'");
    redirect("forum.php?action=view_forum&id=".$fid);
  }
  else
  {
    $sql["mgr"]->query("DELETE FROM forum_posts WHERE id='".$id."'");
    $result = $sql["mgr"]->query("SELECT id FROM forum_posts WHERE topic='".$topic[1]."' ORDER BY id DESC LIMIT 1;"); // get last post id
    $lastpostid = $sql["mgr"]->fetch_row($result);
    $lastpostid = $lastpostid[0];
    $sql["mgr"]->query("UPDATE forum_posts SET lastpost='".$lastpostid."' WHERE id='".$topic[1]."'"); // update topic' s last post id
    redirect("forum.php?action=view_topic&id=".$topic[1]);
  }
  // Queries: 1 (if delete topic) || 4 if delete post
}

function forum_add_topic()
{
  global $enablesidecheck, $forum_skeleton, $maxqueries, $minfloodtime, $user_lvl, $user_id, $output, $corem_db, $sql;

  if ( $enablesidecheck )
    $side = get_side(); // Better to use it here instead of call it many time in the loop :)

  if ( $minfloodtime > 0 )
  {
    $userposts = $sql["mgr"]->query("SELECT time FROM forum_posts WHERE authorid='".$user_id."' ORDER BY id DESC LIMIT 1;");
    if ( $sql["mgr"]->num_rows($userposts) != 0 )
    {
      $mintimeb4post = $sql["mgr"]->fetch_row($userposts);
      $mintimeb4post = time() - strtotime($mintimeb4post[0]);

      if ( $mintimeb4post < $minfloodtime )
        error(lang("forum", "please_wait1")." ".$minfloodtime." ".lang("forum", "please_wait2"));
    }
  }

  if ( !isset($_GET["id"]) )
    error(lang("forum", "no_such_forum"));
  else
    $id = $sql["mgr"]->quote_smart($_GET["id"]);

  $cat = 0;
  foreach ( $forum_skeleton as $cid => $category )
  {
    foreach ( $category["forums"] as $fid => $forum )
    {
      if ( $fid == $id )
        $cat = $cid;
    }
  }

  if ( empty($forum_skeleton[$cat]["forums"][$id]) )
    error(lang("forum", "no_such_forum"));
  $forum = $forum_skeleton[$cat]["forums"][$id];
  if ( ( $forum_skeleton[$cat]["level_post_topic"] > $user_lvl ) || ( $forum["level_post_topic"] > $user_lvl ) )
    error(lang("forum", "no_access"));

  if ( ( $user_lvl == 0 ) && $enablesidecheck )
  {
    if ( $forum_skeleton[$cat]["side_access"] != "ALL" )
    { // Not an all side forum
      if ( $side == "NO" ) // No char
        continue;
      elseif ( $forum_skeleton[$cat]["side_access"] != $side ) // Forumside different of the user side
        continue;
    }
    if ( $forum["side_access"] != "ALL" )
    { // Not an all side forum
      if ( $side == "NO" ) // No char
        continue;
      elseif ( $forum["side_access"] != $side ) // Forumside different of the user side
        continue;
    }
  }


  $output .= '
          <div class="top">
            <h1>'.lang("forum", "forums").'</h1>'.lang("forum", "you_are_here").': <a href="forum.php">'.lang("forum", "forum_index").'</a> -> <a href="forum.php?action=view_forum&amp;id='.$id.'">'.$forum["name"].'</a> -> '.lang("forum", "new_topic").'
          </div>
          <center>
            <form action="forum.php?action=do_add_topic" method="post" name="form">
              <table class="top_hidden">
                <tr>
                  <td>'.lang("forum", "topic_name").': <input name="name" size="40"></td>
                </tr>
                <tr>
                  <td colspan="2">';
  bbcode_add_editor();
  $output .= '       
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    <textarea id="msg" name="msg" rows=8 cols=93></textarea>
                    <input type="hidden" name="forum" value="'.$id.'" />
                  </td>
                </tr>
                <tr>
                  <td align="left">';
  makebutton("Post", "javascript:do_submit()", 100);
  $output .= '
                  </td>
                </tr>
              </table>
            </form>
          </center>
          <br/>';
  // Queries: 1
}

function forum_do_add_topic()
{
  global $enablesidecheck, $forum_skeleton, $user_lvl, $user_name, $user_id, $corem_db, $minfloodtime, $sql;

  if ( $enablesidecheck )
    $side = get_side(); // Better to use it here instead of call it many time in the loop :)

  $userposts = $sql["mgr"]->query("SELECT time FROM forum_posts WHERE authorid='".$user_id."' ORDER BY id DESC LIMIT 1;");
  if ( $sql["mgr"]->num_rows($userposts) != 0 )
  {
    $mintimeb4post = $sql["mgr"]->fetch_row($userposts);
    $mintimeb4post = time() - strtotime($mintimeb4post[0]);

    if ( $mintimeb4post < $minfloodtime )
      error(lang("forum", "please_wait"));
  }

  if ( !isset($_POST["forum"]) )
    error(lang("forum", "no_such_forum"));
  else
    $forum = $sql["mgr"]->quote_smart($_POST["forum"]);

  $cat = 0;
  foreach ( $forum_skeleton as $cid => $category )
  {
    foreach ( $category["forums"] as $fid => $forum_ )
    {
      if ( $fid == $forum )
        $cat = $cid;
    }
  }
  if ( empty($forum_skeleton[$cat]["forums"][$forum]) )
    error(lang("forum", "no_such_forum"));
  $forum_ = $forum_skeleton[$cat]["forums"][$forum];
  if ( ( $forum_skeleton[$cat]["level_post_topic"] > $user_lvl ) || ( $forum_["level_post_topic"] > $user_lvl ) )
    error(lang("forum", "no_access"));

  if ( ( $user_lvl == 0 ) && $enablesidecheck )
  {
    if ( $forum_skeleton[$cat]["side_access"] != "ALL" )
    { // Not an all side forum
      if ( $side == "NO" ) // No char
        continue;
      elseif ( $forum_skeleton[$cat]["side_access"] != $side ) // Forumside different of the user side
        continue;
    }
    if ( $forum_["side_access"] != "ALL" )
    { // Not an all side forum
      if ( $side == "NO" ) // No char
        continue;
      elseif ( $forum_["side_access"] != $side ) // Forumside different of the user side
        continue;
    }
  }

  $msg = trim($sql["mgr"]->quote_smart($_POST["msg"]), " ");

  $name = trim($sql["mgr"]->quote_smart($_POST["name"]), " ");

  if ( strlen($name) > 49 )
    error(lang("forum", "name_too_long"));

  if ( strlen($name) < 5 )
    error(lang("forum", "name_too_short"));

  if ( strlen($msg) < 5 )
    error(lang("forum", "msg_too_short"));

  //$msg = str_replace('\n', '<br />', $msg);

  $time = date("m/d/y H:i:s");

  $sql["mgr"]->query("INSERT INTO forum_posts (authorid, authorname, forum, name, text, time) VALUES ('".$user_id."', '".$user_name."', '".$forum."', '".$name."', '".$msg."', '".$time."');");
  $id = $sql["mgr"]->insert_id();
  $sql["mgr"]->query("UPDATE forum_posts SET topic='".$id."', lastpost='".$id."' WHERE id='".$id."';");

  redirect("forum.php?action=view_topic&id=".$id);
  // Queries: 3
}

function forum_do_add_post()
{
  global $enablesidecheck, $forum_skeleton, $minfloodtime, $user_lvl, $user_name, $user_id, $corem_db, $sql;

  if ( $enablesidecheck )
    $side = get_side(); // Better to use it here instead of call it many time in the loop :)

  if ( $minfloodtime > 0 )
  {
    $userposts = $sql["mgr"]->query("SELECT time FROM forum_posts WHERE authorid='".$user_id."' ORDER BY id DESC LIMIT 1;");
    if ( $sql["mgr"]->num_rows($userposts) != 0 )
    {
      $mintimeb4post = $sql["mgr"]->fetch_row($userposts);
      $mintimeb4post = time() - strtotime($mintimeb4post[0]);

      if ( $mintimeb4post < $minfloodtime )
        error(lang("forum", "please_wait"));
    }
  }

  if ( !isset($_POST["forum"]) )
    error(lang("forum", "no_such_forum"));
  else
    $forum = $sql["mgr"]->quote_smart($_POST["forum"]);

  $cat = 0;
  foreach ( $forum_skeleton as $cid => $category )
  {
    foreach ( $category["forums"] as $fid => $forum_ )
    {
      if ( $fid == $forum )
        $cat = $cid;
    }
  }

  if ( empty($forum_skeleton[$cat]["forums"][$forum]) )
    error(lang("forum", "no_such_forum"));
  $forum_ = $forum_skeleton[$cat]["forums"][$forum];
  if ( ( ( $user_lvl > 0 ) || !$closed ) && ( ( $forum_skeleton[$cat]["level_post"] > $user_lvl ) || ( $forum_["level_post"] > $user_lvl ) ) )
    error(lang("forum", "no_access"));

  if ( ( $user_lvl == 0 ) && $enablesidecheck )
  {
    if ( $forum_skeleton[$cat]["side_access"] != "ALL" )
    { // Not an all side forum
      if ( $side == "NO" ) // No char
        continue;
      elseif ( $forum_skeleton[$cat]["side_access"] != $side ) // Forumside different of the user side
        continue;
    }
    if ( $forum_["side_access"] != "ALL" )
    { // Not an all side forum
      if ( $side == "NO" ) // No char
        continue;
      elseif ( $forum_["side_access"] != $side ) // Forumside different of the user side
        continue;
    }
  }

  if ( !isset($_POST["topic"]) )
    error(lang("forum", "no_such_topic"));
  else
    $topic = $sql["mgr"]->quote_smart($_POST["topic"]);

  $msg = trim($sql["mgr"]->quote_smart($_POST["msg"]), " ");

  //$msg = str_replace('\n', '<br />', $msg);

  if ( strlen($msg) < 5 )
    error(lang("forum", "msg_too_short"));

  $name = $sql["mgr"]->query("SELECT name FROM forum_posts WHERE id='".$topic."';");
  $name = $sql["mgr"]->fetch_row($name);
  $name = $sql["mgr"]->quote_smart($name[0]);

  $time = date("m/d/y H:i:s");

  $sql["mgr"]->query("INSERT INTO forum_posts (authorid, authorname, forum, topic, name, text, time) VALUES ('".$user_id."', '".$user_name."', '".$forum."', '".$topic."', '".$name."', '".$msg."', '".$time."');");
  $query = "SELECT id FROM forum_posts WHERE authorid='".$user_id."' AND topic='".$topic."' AND time='".$time."'";
  $result = $sql["mgr"]->query($query);
  $fields = $sql["mgr"]->fetch_assoc($result);
  $id = $fields["id"];
  //$id = @mysql_insert_id($sql["mgr"]);
  $sql["mgr"]->query("UPDATE forum_posts SET lastpost=".$id." WHERE id=".$topic.";");

  redirect("forum.php?action=view_topic&id=".$topic);
  // Queries: 4
}

function forum_edit_post()
{
  global $forum_skeleton, $maxqueries, $minfloodtime, $user_lvl, $user_id, $output, $corem_db, $sql;

  if ( !isset($_GET["id"]) )
    error(lang("forum", "no_such_post"));
  else
    $id = $sql["mgr"]->quote_smart($_GET["id"]);

  $post = $sql["mgr"]->query("SELECT id, topic, authorid, forum, name, text FROM forum_posts WHERE id='".$id."';");
  if ( $sql["mgr"]->num_rows($post) == 0 )
    error(lang("forum", "no_such_post"));
  $post = $sql["mgr"]->fetch_row($post);

  if ( ( $user_lvl == 0 ) && ( $user_id != $post[2] ) )
    error(lang("forum", "no_access"));

  $cat = 0;
  foreach ( $forum_skeleton as $cid => $category )
  {
    foreach ( $category["forums"] as $fid_ => $forum )
    {
      if ( $fid_ == $post[3] )
        $cat = $cid;
    }
  }
  if ( empty($forum_skeleton[$cat]["forums"][$post[3]]) ) // No such forum..
    error(lang("forum", "no_such_forum"));
  $forum = $forum_skeleton[$cat]["forums"][$post[3]];

  $output .= '
          <div class="top">
            <h1>'.lang("forum", "forums").'</h1>'.lang("forum", "you_are_here").': <a href="forum.php">'.lang("forum", "forum_index").'</a> -> <a href="forum.php?action=view_forum&amp;id='.$post[3].'">'.$forum["name"].'</a> -> <a href="forum.php?action=view_topic&amp;id='.$post[1].'">'.$post[4].'</a> -> '.lang("forum", "edit").'
          </div>
          <form action="forum.php?action=do_edit_post" method="post" name="form">
            <input type="hidden" name="forum" value="'.$post[3].'" />
            <input type="hidden" name="post" value="'.$post[0].'" />
            <center>
              <table class="lined">
                <table class="top_hidden">
                  <tr>';
  if ( $post[0] = $post[0] )
    $output .= '
                    </td>
                    <td>'.lang("forum", "topic_name").':<input type="hidden" name="topic" value="1" />
                      <input name="name" size="40" value="'.$post[4].'">
                    </td>
                  </tr>';
  else
    $output .= '
                    </td>
                    <td>'.lang("forum", "topic_name").':
                    <td>'.$post[4].'</td>
                  </tr>';

  //$post[5] = str_replace('<br />', chr(10), $post[5]);

  $output .= '
                  <tr>
                    <td colspan="2">';
  bbcode_add_editor();
  $output .= '
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <textarea id="msg" name="msg" rows=8 cols=93>'.$post[5].'</textarea>
                    </td>
                  </tr>
                  <tr>
                    <td align="left">';
  makebutton("Post", "javascript:do_submit()", 100);
  $output .= '
                    </td>
                  </tr>
                </table>
              </center>
            </form>
            <br/>';
  // Queries: 1
}

function forum_do_edit_post()
{
  global $user_lvl, $user_name, $user_id, $corem_db, $sql;

  if ( !isset($_POST["forum"]) )
    error(lang("forum", "no_such_forum"));
  else
    $forum = $sql["mgr"]->quote_smart($_POST["forum"]);
  if ( !isset($_POST["post"]) )
    error(lang("forum", "no_such_post"));
  else
    $post = $sql["mgr"]->quote_smart($_POST["post"]);

  if ( !isset($_POST["name"]) )
    $topic = 0;
  else
  {
    $topic = 1;
//    htmlspecialchars($_POST["name"]);
    $name = $sql["mgr"]->quote_smart($_POST["name"]);
    if ( strlen($name) > 49 )
      error(lang("forum", "name_too_long"));
    if (strlen($name) < 5)
      error(lang("forum", "name_too_short"));
  }

//  $_POST["msg"] = htmlspecialchars($_POST["msg"]);
  $msg = trim($sql["mgr"]->quote_smart($_POST["msg"]), " ");

  if ( strlen($msg) < 5 )
    error(lang("forum", "msg_too_short"));

  //$msg = str_replace('\n', '<br />', $msg);
//  $msg = str_replace('\r', '<br />', $msg);

  $result = $sql["mgr"]->query("SELECT topic FROM forum_posts WHERE id=".$post.";");
  $topicid = $sql["mgr"]->fetch_row($result);

  $sql["mgr"]->query("UPDATE forum_posts SET text='".$msg."' WHERE id=".$post.";");

  if ( $topic == 1 )
    $sql["mgr"]->query("UPDATE forum_posts SET name='".$name."' WHERE topic=".$topicid[0].";");

  $result = $sql["mgr"]->query("SELECT topic FROM forum_posts WHERE id=".$post.";");
  $topicid = $sql["mgr"]->fetch_row($result);

  redirect("forum.php?action=view_topic&id=".$topicid[0]);
  // Queries: 3 (+1 if topic)
}

function forum_move_topic()
{
  global $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output, $corem_db, $sql;
  
  if ( !isset($_GET["id"]) )
    error(lang("forum", "no_such_topic"));
  else
    $id = $sql["mgr"]->quote_smart($_GET["id"]);

  $topic = $sql["mgr"]->query("SELECT id, topic, authorid, forum, name FROM forum_posts WHERE id='".$id."';");
  //                0 1   2   3   4
  if ( $sql["mgr"]->num_rows($topic) == 0 )
    error(lang("forum", "no_such_topic"));
  $topic = $sql["mgr"]->fetch_row($topic);
  if ( $user_lvl == 0 )
    error(lang("forum", "no_access"));
  $fid = $topic[3];

  $cat = 0;
  foreach ( $forum_skeleton as $cid => $category )
  {
    foreach ( $category["forums"] as $fid_ => $forum )
    {
      if ( $fid_ == $fid )
        $cat = $cid;
    }
  }

  if ( empty($forum_skeleton[$cat]["forums"][$fid]) ) // No such forum..
    error(lang("forum", "no_such_forum"));
  $forum = $forum_skeleton[$cat]["forums"][$fid];

  $output .= '
          <div class="top">
            <h1>'.lang("forum", "forums").'</h1>'.lang("forum", "you_are_here").': <a href="forum.php">'.lang("forum", "forum_index").'</a> -> <a href="forum.php?action=view_forum&amp;id='.$fid.'">'.$forum["name"].'</a> -> <a href="forum.php?action=view_topic&amp;id='.$topic[1].'">'.$topic[4].'</a> -> '.lang("forum", "move").'!
          </div>
          <center>
            <table class="lined">
              <tr>
                <td>'.lang("forum", "where").': 
                  <form action="forum.php?action=do_move_topic" method="post" name="form">
                    <select name="forum">';

  foreach ( $forum_skeleton as $category )
  {
    foreach ( $category["forums"] as $fid_ => $forum )
    {
      if ( $fid_ != $fid )
        $output .= '
                      <option value="'.$fid_.'">'.$forum["name"].'</option>';
      else
        $output .= '
                      <option value="'.$fid_.'" selected>'.$forum["name"].'</option>';
    }
  }

  $output .= '
                    </select>
                    <input type="hidden" name="id" value="'.$id.'" />
                  </form>
                </td>
              </tr>
            </table>
            <table class="hidden">
              <tr>
                <td>';
  makebutton(lang("forum", "back"), "javascript:window.history.back()", 120);
  $output .= '
                </td>
                <td>';
  makebutton(lang("forum", "confirm"), "javascript:do_submit()", 120);
  $output .= '
                </td>
              </tr>
            </table>
          </center>';
  // Queries: 1
}

function forum_do_move_topic()
{
  global $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output, $corem_db, $sql;

  if ( !isset($_POST["forum"]) )
    error(lang("forum", "no_such_forum"));
  else
    $forum = $sql["mgr"]->quote_smart($_POST["forum"]);
  if ( !isset($_POST["id"]) )
    error(lang("forum", "no_such_topic"));
  else
    $id = $sql["mgr"]->quote_smart($_POST["id"]);

  $sql["mgr"]->query("UPDATE forum_posts SET forum='".$forum."' WHERE topic='".$id."'"); // update topic' s last post id
  redirect("forum.php?action=view_topic&id=".$id);
  // Queries: 1
}



//#############################################################################
// MAIN
//#############################################################################

$action = ( ( isset($_GET["action"]) ) ? addslashes($_GET["action"]) : NULL );

$output .= '
        <div class="bubble">';

switch ( $action )
{
  case "index":
    forum_index();
    break;
  case "view_forum":
    forum_view_forum();
    break;
  case "view_topic":
    forum_view_topic();
    break;
  case "add_topic":
    forum_add_topic();
    break;
  case "do_add_topic":
    forum_do_add_topic();
    break;
  case "edit_post":
    forum_edit_post();
    break;
  case "do_edit_post":
    forum_do_edit_post();
    break;
  case "delete_post":
    forum_delete_post();
    break;
  case "do_delete_post":
    forum_do_delete_post();
    break;
  case "do_add_post":
    forum_do_add_post();
    break;
  case "edit_stick":
    forum_do_edit_stick();
    break;
  case "edit_announce":
    forum_do_edit_announce();
    break;
  case "edit_close":
    forum_do_edit_close();
    break;
  case "move_topic":
    forum_move_topic();
    break;
  case "do_move_topic":
    forum_do_move_topic();
    break;
  default:
    forum_index();
}

unset($action);

require_once("footer.php");

?>
