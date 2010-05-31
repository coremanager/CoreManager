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
require_once("scripts/forum.conf.php");
require_once("libs/forum_lib.php");
require_once("libs/bb2html_lib.php");
valid_login($action_permission['view']);

if (isset($_COOKIE["lang"])){
  $forumlang = $_COOKIE["lang"];
  if (!file_exists("lang/$forumlang.php")) $forumlang = $language;
  } else $forumlang = $language;
require_once("lang/$forumlang.php");

foreach($forum_skeleton as $cid => $category){
  if(!isset($category["level_read"])) $forum_skeleton[$cid]["level_read"] = 0;
  if(!isset($category["level_post"])) $forum_skeleton[$cid]["level_post"] = 0;
  if(!isset($category["level_post_topic"])) $forum_skeleton[$cid]["level_post_topic"] = 0;
  if(!isset($category["side_access"])) $forum_skeleton[$cid]["side_access"] = "ALL";
  foreach($category["forums"] as $id => $forum){
    if(!isset($forum["level_read"])) $forum_skeleton[$cid]["forums"][$id]["level_read"] = 0;
    if(!isset($forum["level_post"])) $forum_skeleton[$cid]["forums"][$id]["level_post"] = 0;
    if(!isset($forum["level_post_topic"])) $forum_skeleton[$cid]["forums"][$id]["level_post_topic"] = 0;
    if(!isset($forum["side_access"])) $forum_skeleton[$cid]["forums"][$id]["side_access"] = "ALL";
  }
}

//$lang_forum = lang_forum();

// #######################################################################################################
// Forum_Index : Display the forums in categories
// #######################################################################################################
function forum_index()
{
  global $enablesidecheck, $forum_skeleton, $forumlang, $user_lvl, $output, $logon_db, $arcm_db,
    $arcm_db, $sqlm;

  if($enablesidecheck)
    $side = get_side();

  $result = $sqlm->query("SELECT `authorname`,`id`,`name`,`time`,`forum` FROM `forum_posts` WHERE `id` IN (SELECT MAX(`id`) FROM `forum_posts` GROUP BY `forum`) ORDER BY `forum`;");
  $lasts = array();
  if($sqlm->num_rows($result) > 0)
  {
    while($row = $sqlm->fetch_row($result))
      $lasts[$row[4]] = $row;
  }
  $output .= "<div class=\"top\"><h1>".lang('forum', 'forums')."</h1>".lang('forum', 'you_are_here')." : <a href=\"forum.php\">".lang('forum', 'forum_index')."</a></div><center><table class=\"lined\">";
  foreach($forum_skeleton as $category)
  {
    if(($category["level_read"] > $user_lvl))
      continue;
    if($user_lvl == 0 && $enablesidecheck)
    {
      if($category["side_access"] != "ALL")
      { // Not an all side forum
        if($side == "NO") // No char
          continue;
        else if($category["side_access"] != $side) // Forumside different of the user side
          continue;
      }
    }
    $output .= "<tr><td class=\"head\" align=\"left\">".$sqlm->result($sqlm->query("SELECT * FROM config_lang_forum WHERE `Key`='".$category["name"]."' AND Lang='".$forumlang."'"), 0, "Value")."</td>
                    <td class=\"head\">".lang('forum', 'topics')."</td>
                    <td class=\"head\">".lang('forum', 'replies')."</td>
                    <td class=\"head\" align=\"right\">".lang('forum', 'last_post')."</td></tr>";
    foreach($category["forums"] as $id => $forum)
    {
      if($forum["level_read"] > $user_lvl)
        continue;
      if($user_lvl == 0 && $enablesidecheck)
      {
        if($forum["side_access"] != "ALL")
        { // Not an all side forum
          if($side == "NO") // No char
            continue;
          else if($forum["side_access"] != $side) // Forumside different of the user side
            continue;
        }
      }
      $totaltopics = $sqlm->query("SELECT id FROM forum_posts WHERE forum = '$id' AND id = `topic`;");
      $numtopics = $sqlm->num_rows($totaltopics);
      $totalreplies = $sqlm->query("SELECT id FROM forum_posts WHERE forum = '$id';");
      $numreplies = $sqlm->num_rows($totalreplies);
      $output .= "<tr><td align=\"left\"><a href=\"forum.php?action=view_forum&amp;id=$id\">".$sqlm->result($sqlm->query("SELECT * FROM config_lang_forum WHERE `Key`='".$forum["name"]."' AND Lang='".$forumlang."'"), 0, "Value")."</a><br />".$sqlm->result($sqlm->query("SELECT * FROM config_lang_forum WHERE `Key`='".$forum["desc"]."' AND Lang='".$forumlang."'"), 0, "Value")."</td>
                        <td>{$numtopics}</td>
                        <td>{$numreplies}</td>";
      if(isset($lasts[$id]))
      {
        // Use screen name if available
        $sn_query = "SELECT * FROM config_accounts WHERE Login = '".$lasts[$id][0]."'";
        $sn_result = $sqlm->query($sn_query);
        if ($sqlm->num_rows($sn_result))
        {
          $sn = $sqlm->fetch_assoc($sn_result);
          $lasts[$id][0] = $sn['ScreenName'];
        }
        $lasts[$id][2] = htmlspecialchars($lasts[$id][2]);
        $output .= "<td align=\"right\"><a href=\"forum.php?action=view_topic&amp;postid={$lasts[$id][1]}\">{$lasts[$id][2]}</a><br />".lang('forum', 'by')." {$lasts[$id][0]} <br /> {$lasts[$id][3]} </td></tr>";
      }
      else
      {
        $output .= "<td align=\"right\">".lang('forum', 'no_topics')."</td></tr>";
      }
    }
  }
  $output .= "<tr><td align=\"right\" class=\"hidden\"></td></tr></table></center><br/>";
  //$sqlm->close();
  // Queries : 1
}

// #######################################################################################################
//
// #######################################################################################################
function forum_view_forum(){
  global $enablesidecheck, $forum_skeleton, $maxqueries, $user_lvl, $output, 
    $arcm_db, $arcm_db, $sqlm;

  if($enablesidecheck)
    $side = get_side();

  if(!isset($_GET["id"])) error(lang('forum', 'no_such_forum'));
  else $id = $sqlm->quote_smart($_GET["id"]);
  if(!isset($_GET["page"])) $page = 0;
  else $page = $sqlm->quote_smart($_GET["page"]);
  $cat = 0;
  foreach($forum_skeleton as $cid => $category)
  {
    foreach($category["forums"] as $fid => $forum)
    {
      if($fid == $id)
        $cat = $cid;
    }
  }
  if(empty($forum_skeleton[$cat]["forums"][$id]))
    error(lang('forum', 'no_such_forum'));
  $forum = $forum_skeleton[$cat]["forums"][$id];
  if(($forum_skeleton[$cat]["level_read"] > $user_lvl) || ($forum["level_read"] > $user_lvl))
    error(lang('forum', 'no_access'));

  if($user_lvl == 0 && $enablesidecheck){
    if($forum_skeleton[$cat]["side_access"] != "ALL")
    { // Not an all side forum
      if($side == "NO") // No char
        continue;
      else if($forum_skeleton[$cat]["side_access"] != $side) // Forumside different of the user side
        continue;
    }
    if($forum["side_access"] != "ALL")
    { // Not an all side forum
      if($side == "NO") // No char
        continue;
      else if($forum["side_access"] != $side) // Forumside different of the user side
        continue;
    }
  }

  $start = ($maxqueries * $page);
  $output .= "<div class=\"top\"><h1>".lang('forum', 'forums')."</h1>".lang('forum', 'you_are_here')." : <a href=\"forum.php\">".lang('forum', 'forum_index')."</a> -> <a href=\"forum.php?action=view_forum&amp;id={$id}\">{$forum["name"]}</a></div>
        <center><table class=\"lined\">";
  $topics = $sqlm->query("SELECT id, authorid, authorname, name, annouced, sticked, closed FROM forum_posts WHERE (forum = '$id' AND id = `topic`) OR annouced = 1 AND id = `topic` ORDER BY annouced DESC, sticked DESC, lastpost DESC LIMIT $start, $maxqueries;");
  $result = $sqlm->query("SELECT `topic` as curtopic,(SELECT count(`id`)-1 FROM forum_posts WHERE `topic` = `curtopic`) AS replies,lastpost as curlastpost,(SELECT authorname FROM forum_posts WHERE id = curlastpost) as authorname,(SELECT time FROM forum_posts WHERE id = curlastpost) as time FROM `forum_posts` WHERE (`forum` = $id AND `topic` = `id` ) OR annouced = 1;");
  $lasts = array();
  if($sqlm->num_rows($result) > 0)
  {
    while($row = $sqlm->fetch_row($result))
      $lasts[$row[0]] = $row;
  }
  if($forum_skeleton[$cat]["level_post_topic"] <= $user_lvl && $forum["level_post_topic"] <= $user_lvl)
    $output .= "<tr><td colspan=\"4\" id=\"forum_topic_list_header\"><a href=\"forum.php?action=add_topic&amp;id={$id}\">".lang('forum', 'new_topic')."</a></td></tr>";
  if($sqlm->num_rows($topics)!=0)
  {
    $output .= "<tr>
      <td id=\"forum_topic_list_header_title\">".lang('forum', 'title')."</td>
      <td id=\"forum_topic_list_header_author\">".lang('forum', 'author')."</td>
      <td>".lang('forum', 'replies')."</td>
      <td>".lang('forum', 'last_post')."</td>
    </tr>";
    while($topic = $sqlm->fetch_row($topics))
    {
      $output .= "<tr>
              <td id=\"forum_topic_list_title\">";
      if($topic[4]=="1")
        $output .= lang('forum', 'annoucement')." : ";
      else
      {
        if($topic[5]=="1")
          $output .= lang('forum', 'sticky')." : ";
        else
        {
          if($topic[6]=="1")
            $output .= "[".lang('forum', 'closed')."] ";
        }
      }
      $topic[3] = htmlspecialchars($topic[3]);
      // Use screen name if available
      $sn_query = "SELECT * FROM config_accounts WHERE Login = '".$topic[2]."'";
      $sn_result = $sqlm->query($sn_query);
      if ($sqlm->num_rows($sn_result))
      {
        $sn = $sqlm->fetch_assoc($sn_result);
        $topic[2] = $sn['ScreenName'];
      }
      $output .= "<a href=\"forum.php?action=view_topic&amp;id={$topic[0]}\">{$topic[3]}</a></td><td>{$topic[2]}</td>
              <td>{$lasts[$topic[0]][1]}</td>
              <td>".lang('forum', 'last_post_by')." {$lasts[$topic[0]][3]}, {$lasts[$topic[0]][4]}</td>
            </tr>";
    }
    $totaltopics = $sqlm->query("SELECT id FROM forum_posts WHERE forum = '$id' AND id = `topic`;"); //My page system is so roxing, i can' t break this query xD
    $pages = ceil($sqlm->num_rows($totaltopics)/$maxqueries);
    $output .= "<tr><td align=\"right\" colspan=\"4\">".lang('forum', 'pages')." : ";
    for($x = 1; $x <= $pages; $x++)
    {
      $y = $x-1;
      $output .= "<a href=\"forum.php?action=view_forum&amp;id=$id&amp;page=$y\">$x</a> ";
    }
    $output .= "</td></tr>";
  }
  else
    $output .= "<tr><td>".lang('forum', 'no_topics')."</td></tr>";
  //$sqlm->close();
  $output .= "<tr><td align=\"right\" class=\"hidden\"></td></tr></table></center><br/>";
  // Queries : 3
}
// #######################################################################################################
//
// #######################################################################################################
function forum_view_topic(){

  global $enablesidecheck, $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output,
    $realm_db, $characters_db, $realm_id, $arcm_db, $logon_db, $arcm_db, $sqlm, $sqll, $core;

  if($enablesidecheck) $side = get_side(); // Better to use it here instead of call it many time in the loop :)

  if(isset($_GET["id"])){
    $id = $sqlm->quote_smart($_GET["id"]);
    $post = false;
  }
  else{
    if(isset($_GET["postid"])){
      $id = $sqlm->quote_smart($_GET["postid"]);
      $post = true;
    }
    else
      error(lang('forum', 'no_such_topic'));
  }


  if(!isset($_GET["page"])) $page = 0;
  else $page = $sqlm->quote_smart($_GET["page"]); // Fok you mathafoker haxorz
  $start = ($maxqueries * $page);

  if(!$post){
    $posts = $sqlm->query("SELECT id,authorid,authorname,forum,name,text,time,annouced,sticked,closed FROM forum_posts WHERE topic = '$id' ORDER BY id ASC LIMIT $start, $maxqueries;");

// Thx qsa for the query structure

    //$link = $sqlm->connect($logon_db['addr'], $logon_db['user'], $logon_db['pass'], $logon_db['name']);

//$query = "SELECT account,name,gender,race,class,
 //level,(SELECT gmlevel FROM `{$realm_db['name']}`.account WHERE `{$realm_db['name']}`.account.id = `{$characters_db[$realm_id]['name']}`.characters.account) as gmlevel
//FROM `{$characters_db[$realm_id]['name']}`.characters WHERE totaltime IN ( SELECT MAX(totaltime) FROM `{$characters_db[$realm_id]['name']}`.characters WHERE account IN (";

if ( $core == 1 )
  $query = "SELECT acct,name,gender,race,class,
    level,(SELECT gm FROM `{$logon_db['name']}`.accounts WHERE `{$logon_db['name']}`.accounts.acct = `{$characters_db[$realm_id]['name']}`.characters.acct) as gmlevel
    FROM `{$characters_db[$realm_id]['name']}`.characters WHERE level IN ( SELECT MAX(level) FROM `{$characters_db[$realm_id]['name']}`.characters WHERE acct IN (";
else
{
  $query = "SELECT account AS acct,name,gender,race,class,
    level,(SELECT gmlevel FROM `{$logon_db['name']}`.account_access WHERE `{$logon_db['name']}`.account_access.id = `{$characters_db[$realm_id]['name']}`.characters.account) as gmlevel
    FROM `{$characters_db[$realm_id]['name']}`.characters WHERE level IN ( SELECT MAX(level) FROM `{$characters_db[$realm_id]['name']}`.characters WHERE account IN (";
}
while($post = $sqlm->fetch_row($posts)){
  $query .= "$post[1],";
}
mysql_data_seek($posts,0);
if ( $core == 1 )
  $query .= "0) GROUP BY acct);";
else
  $query .= "0) GROUP BY account);";
    /*$link = $sqlm->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);*/
    $results = $sqlm->query($query);

    while($avatar = $sqlm->fetch_row($results))
    {
      $gmlevel = gmlevel($avatar[6]);
      $char_gender = str_pad(dechex($avatar[2]),8, 0, STR_PAD_LEFT);
      $avatars[$avatar[0]]["name"] = $avatar[1];
      $avatars[$avatar[0]]["sex"] = $char_gender[3];
      $avatars[$avatar[0]]["race"] = $avatar[3];
      $avatars[$avatar[0]]["class"] = $avatar[4];
      $avatars[$avatar[0]]["level"] = $avatar[5];
      //$avatars[$avatar[0]]["gm"] = $avatar[6];
      $avatars[$avatar[0]]["gm"] = $gmlevel;
    }

//    $link = $sqlm->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
    $replies = $sqlm->num_rows($posts);
    if($replies==0)
      error(lang('forum', 'no_such_topic'));
    $post = $sqlm->fetch_row($posts);
    $fid = $post[3];
    $cat = 0;
    foreach($forum_skeleton as $cid => $category){
      foreach($category["forums"] as $fid_ => $forum){
        if($fid_ == $fid) $cat = $cid;
      }
    }
    if(empty($forum_skeleton[$cat]["forums"][$fid]))
      error(lang('forum', 'no_such_forum'));
    $forum = $forum_skeleton[$cat]["forums"][$fid];
    if($forum_skeleton[$cat]["level_read"] > $user_lvl || $forum["level_read"] > $user_lvl) error(lang('forum', 'no_access'));

    if($user_lvl == 0 && $enablesidecheck){
      if($forum_skeleton[$cat]["side_access"] != "ALL"){ // Not an all side forum
        if($side == "NO") // No char
          continue;
        else if($forum_skeleton[$cat]["side_access"] != $side) // Forumside different of the user side
          continue;
      }
      if($forum["side_access"] != "ALL"){ // Not an all side forum
        if($side == "NO") // No char
          continue;
        else if($forum["side_access"] != $side) // Forumside different of the user side
          continue;
      }
    }

    $post[4] = htmlspecialchars($post[4]);
    $post[5] = bb2html($post[5]);

    $output .= "<div class=\"top\"><h1>".lang('forum', 'forums')."</h1>".lang('forum', 'you_are_here')." : <a href=\"forum.php\">".lang('forum', 'forum_index')."</a> -> <a href=\"forum.php?action=view_forum&amp;id={$fid}\">{$forum["name"]}</a> -> <a href=\"forum.php?action=view_topic&amp;id={$id}\">{$post[4]}</a></div>
          <center><table class=\"lined\">
          <tr>
            <td id=\"forum_topic_header_info\">".lang('forum', 'info')."</td>
            <td id=\"forum_topic_header_text\">".lang('forum', 'text')."</td>
            <td id=\"forum_topic_header_misc\">";
            if($user_lvl > 0)
            {
              if($post[8]=="1"){
                if($post[7]=="1"){
                  // Annoucement
                  $output .= lang('forum', 'annoucement')."
                  <a href=\"forum.php?action=edit_announce&amp;id={$post[0]}&amp;state=0\"><img src=\"img/forums/down.gif\" border=\"0\" alt=\"".lang('forum', 'down')."\" /></a>";
                }
                else{
                  // Sticky
                  $output .= lang('forum', 'sticky')."
                  <a href=\"forum.php?action=edit_stick&amp;id={$post[0]}&amp;state=0\"><img src=\"img/forums/down.gif\" border=\"0\" alt=\"".lang('forum', 'down')."\" /></a>
                  <a href=\"forum.php?action=edit_announce&amp;id={$post[0]}&amp;state=1\"><img src=\"img/forums/up.gif\" border=\"0\" alt=\"".lang('forum', 'up')."\" /></a>";
                }
              }
              else{
                if($post[7]=="1"){
                  // Annoucement
                  $output .= lang('forum', 'annoucement')."
                  <a href=\"forum.php?action=edit_announce&amp;id={$post[0]}&amp;state=0\"><img src=\"img/forums/down.gif\" border=\"0\" alt=\"".lang('forum', 'down')."\" /></a>";
                }
                else{
                  // Normal Topic
                  $output .= lang('forum', 'normal')."
                  <a href=\"forum.php?action=edit_stick&amp;id={$post[0]}&amp;state=1\"><img src=\"img/forums/up.gif\" border=\"0\" alt=\"".lang('forum', 'up')."\" /></a>";

                }
              }

              if($post[9]=="1")
                $output .= " <a href=\"forum.php?action=edit_close&amp;id={$post[0]}&amp;state=0\"><img src=\"img/forums/lock.gif\" border=\"0\" alt=\"".lang('forum', 'open')."\" /></a>";
              else
                $output .= " <a href=\"forum.php?action=edit_close&amp;id={$post[0]}&amp;state=1\"><img src=\"img/forums/unlock.gif\" border=\"0\" alt=\"".lang('forum', 'close')."\" /></a>";
              $output .= " <a href=\"forum.php?action=move_topic&amp;id={$post[0]}\"><img src=\"img/forums/move.gif\" border=\"0\" alt=\"".lang('forum', 'move')."\" /></a>";
            }
            if(isset($avatars[$post[1]]))
              $avatar = gen_avatar_panel(
                $avatars[$post[1]]["level"],
                $avatars[$post[1]]["sex"],
                $avatars[$post[1]]["race"],
                $avatars[$post[1]]["class"],1,
                $avatars[$post[1]]["gm"]);
            else
              $avatar = "";
            $output .= "<tr><td id=\"forum_topic_avatar\"><center>$avatar</center>".lang('forum', 'author')." : ";
            if($user_lvl > 0)
              $output .= "<a href=\"user.php?action=edit_user&error=11&acct={$post[1]}\">";
            // Use screen name if available
            // we have to get the actual login name first here
            if ( $core == 1 )
              $un_query = "SELECT * FROM accounts WHERE acct = '".$post[1]["name"]."'";
            else
              $un_query = "SELECT * FROM account WHERE id = '".$post[1]["name"]."'";
            $un_results = $sqll->query($un_query);
            $un = $sqll->fetch_assoc($un_results);
            $sn_query = "SELECT * FROM config_accounts WHERE Login = '".$un['login']."'";
            $sn_result = $sqlm->query($sn_query);
            if ($sqlm->num_rows($sn_result))
            {
              $sn = $sqlm->fetch_assoc($sn_result);
              $post[1]["name"] = $sn['ScreenName'];
              $post[2] = $sn['ScreenName'];
            }
            if(isset($avatars[$post[1]]))
              $output .= $avatars[$post[1]]["name"];
            else
              $output .= $post[2];
            if($user_lvl > 0)
              $output .= "</a>";
            $output .= "<br /> ".lang('forum', 'at')." : {$post[6]}</td>
            <td colspan=\"2\" id=\"forum_topic_text\">{$post[5]}<br /><div id=\"forum_topic_controls\">";
            if($user_lvl > 0 || $user_id == $post[1])
              $output .= "<a href=\"forum.php?action=edit_post&amp;id={$post[0]}\"><img src=\"img/forums/edit.gif\" border=\"0\" alt=\"".lang('forum', 'edit')."\" /></a>
               <a href=\"forum.php?action=delete_post&amp;id={$post[0]}\"><img src=\"img/forums/delete.gif\" border=\"0\" alt=\"".lang('forum', 'delete')."\" /></a>";
          $output .= "</div></td></tr>";
          $closed = $post[9];

    while($post = $sqlm->fetch_row($posts)){
          $post[5] = bb2html($post[5]);

            if(isset($avatars[$post[1]]))
              $avatar = gen_avatar_panel(
                $avatars[$post[1]]["level"],
                $avatars[$post[1]]["sex"],
                $avatars[$post[1]]["race"],
                $avatars[$post[1]]["class"],1,
                $avatars[$post[1]]["gm"]);
            else
              $avatar = "";
            $output .= "<tr><td id=\"forum_topic_reply_avatar\"><center>$avatar</center>".lang('forum', 'author')." : ";
            if($user_lvl > 0)
              $output .= "<a href=\"user.php?action=edit_user&error=11&acct={$post[1]}\">";
            // Use screen name if available
            // we have to get the actual login name first here
            $un_query = "SELECT * FROM accounts WHERE acct = '".$post[1]["name"]."'";
            $un_results = $sqll->query($un_query);
            $un = $sqll->fetch_assoc($un_results);
            $sn_query = "SELECT * FROM config_accounts WHERE Login = '".$un['login']."'";
            $sn_result = $sqlm->query($sn_query);
            if ($sqlm->num_rows($sn_result))
            {
              $sn = $sqlm->fetch_assoc($sn_result);
              $post[1]["name"] = $sn['ScreenName'];
              $post[2] = $sn['ScreenName'];
            }
            if(isset($avatars[$post[1]]))
              $output .= $avatars[$post[1]]["name"];
            else
              $output .= $post[2];
            if($user_lvl > 0)
              $output .= "</a>";
            $output .= "<br /> ".lang('forum', 'at')." : {$post[6]}</td>
            <td colspan=\"2\" id=\"forum_topic_reply_text\">{$post[5]}<br />";
            if($user_lvl > 0 || $user_id == $post[1])
              $output .= "<div id=\"forum_topic_reply_controls\"><a href=\"forum.php?action=edit_post&amp;id={$post[0]}\"><img src=\"img/forums/edit.gif\" border=\"0\" alt=\"".lang('forum', 'edit')."\" /></a>
               <a href=\"forum.php?action=delete_post&amp;id={$post[0]}\"><img src=\"img/forums/delete.gif\" border=\"0\" alt=\"".lang('forum', 'delete')."\" /></a></div>";
          $output .= "</td></tr>";
    }

    //$link = $sqlm->connect($arcm_db['addr'], $arcm_db['user'], $arcm_db['pass'], $arcm_db['name']);

    $totalposts = $sqlm->query("SELECT id FROM forum_posts WHERE topic = '$id';");
    $totalposts = $sqlm->num_rows($totalposts);

    $pages = ceil($totalposts/$maxqueries);
    $output .= "<tr><td align=\"right\" colspan=\"3\">".lang('forum', 'pages')." : ";
    for($x = 1; $x <= $pages; $x++){
      $y = $x-1;
      $output .= "<a href=\"forum.php?action=view_topic&amp;id=$id&amp;page=$y\">$x</a> ";
    }
    $output .= "</td></tr><tr><td align=\"right\" class=\"hidden\"></td></tr></table>";

    // Quick reply form
    if((($user_lvl > 0)||!$closed)&&($forum_skeleton[$cat]["level_post"] <= $user_lvl && $forum["level_post"] <= $user_lvl)
    ){
      $output .= "<form action=\"forum.php?action=do_add_post\" method=\"POST\" name=\"form\">
      <table class=\"top_hidden\">
      <tr>
      <td align=\"left\">";
      makebutton(lang('forum', 'post'), "javascript:do_submit()",100);
      $output .= "</td><td align=\"right\">".lang('forum', 'quick_reply')."</td></tr>
      <tr><td colspan=\"2\">";
      bbcode_add_editor();
      $output .= "<TEXTAREA id=\"msg\" NAME=\"msg\" ROWS=8 COLS=93></TEXTAREA></table><br/>
      <input type=\"hidden\" name=\"forum\" value=\"$fid\" />
      <input type=\"hidden\" name=\"topic\" value=\"$id\" />
      </form>";
    }

    $output .= "</center>";
    //$sqlm->close();
  }
  else{
    $output .= "<div class=\"top\"><h1>Stand by...</h1></div>";

    $post = $sqlm->query("SELECT topic, id FROM forum_posts WHERE id = '$id'"); // Get our post id
    if($sqlm->num_rows($post)==0)
      error(lang('forum', 'no_such_topic'));
    $post = $sqlm->fetch_row($post);
    if($post[0]==$post[1])
      redirect("forum.php?action=view_topic&id=$id");
    $topic = $post[0];
    $posts = $sqlm->query("SELECT id FROM forum_posts WHERE topic = '$topic';"); // Get posts in our topic
    $replies = $sqlm->num_rows($posts);
    if($replies==0)
      error(lang('forum', 'no_such_topic'));
    $row = 0;
    while($post = $sqlm->fetch_row($posts)){ // Find the row of our post, so we could have his ratio (topic x/total topics) and knew the page to show
      $row++;
      if($topic==$id) break;
    }
    $page = 0;
    while(($page * $maxqueries) < $row){
      $page++;
    };
    $page--;
    //$sqlm->close();
    redirect("forum.php?action=view_topic&id=$topic&page=$page");
  }
  // Queries : 2 with id || 2 (+2) with postid
}
function forum_do_edit_close(){
  global $user_lvl, $arcm_db, $sqlm;

  if($user_lvl == 0)
    error(lang('forum', 'no_access'));

  if(!isset($_GET["id"])) error(lang('forum', 'no_such_topic'));
  else $id = $sqlm->quote_smart($_GET["id"]);

  if(!isset($_GET["state"])) error("Bad request, please mail admin and describe what you did to get this error.");
  else $state = $sqlm->quote_smart($_GET["state"]);

  $sqlm->query("UPDATE forum_posts SET closed = '$state' WHERE id = '$id'");
  //$sqlm->close();
  redirect("forum.php?action=view_topic&id=$id");
  // Queries : 1
}
function forum_do_edit_announce(){
  global $user_lvl, $arcm_db, $sqlm;

  if($user_lvl == 0)
    error(lang('forum', 'no_access'));

  if(!isset($_GET["id"])) error(lang('forum', 'no_such_topic'));
  else $id = $sqlm->quote_smart($_GET["id"]);

  if(!isset($_GET["state"])) error("Bad request, please mail admin and describe what you did to get this error.");
  else $state = $sqlm->quote_smart($_GET["state"]);

  $sqlm->query("UPDATE forum_posts SET annouced = '$state' WHERE id = '$id'");
  //$sqlm->close();
  redirect("forum.php?action=view_topic&id=$id");
  // Queries : 1
}
function forum_do_edit_stick(){
  global $user_lvl, $arcm_db, $sqlm;

  if($user_lvl == 0)
    error(lang('forum', 'no_access'));

  if(!isset($_GET["id"])) error(lang('forum', 'no_such_topic'));
  else $id = $sqlm->quote_smart($_GET["id"]);

  if(!isset($_GET["state"])) error("Bad request, please mail admin and describe what you did to get this error.");
  else $state = $sqlm->quote_smart($_GET["state"]);

  $sqlm->query("UPDATE forum_posts SET sticked = '$state' WHERE id = '$id'");
  //$sqlm->close();
  redirect("forum.php?action=view_topic&id=$id");
  // Queries : 1
}
function forum_delete_post(){
  global $enablesidecheck, $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output, $arcm_db, $sqlm;
  
  if(!isset($_GET["id"])) error(lang('forum', 'no_such_post'));
  else $id = $sqlm->quote_smart($_GET["id"]);

  $topic = $sqlm->query("SELECT id,topic,authorid,forum FROM forum_posts WHERE id = '$id';");
  if($sqlm->num_rows($topic)==0) error(lang('forum', 'no_such_post'));
  $topic = $sqlm->fetch_row($topic);
  if($user_lvl == 0 && $topic[2] != $user_id) error(lang('forum', 'no_access'));
  $fid = $topic[3];

  $topic2 = $sqlm->query("SELECT name FROM forum_posts WHERE id = '{$topic[1]}';");
  $name = $sqlm->fetch_row($topic2);

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid_ => $forum){
      if($fid_ == $fid) $cat = $cid;
    }
  }

  if(empty($forum_skeleton[$cat]["forums"][$fid])) // No such forum..
    error(lang('forum', 'no_such_forum'));
  $forum = $forum_skeleton[$cat]["forums"][$fid];
  $output .= "<div class=\"top\"><h1>".lang('forum', 'forums')."</h1>".lang('forum', 'you_are_here')." : <a href=\"forum.php\">".lang('forum', 'forum_index')."</a> -> <a href=\"forum.php?action=view_forum&amp;id={$fid}\">{$forum["name"]}</a> -> <a href=\"forum.php?action=view_topic&amp;id={$topic[1]}\">{$name[0]}</a> -> ".lang('forum', 'delete')."!</div><center><table class=\"lined\">";
  if($topic[0]==$topic[1])
    $output .= "<tr><td>".lang('forum', 'delete_topic')."</td></tr></table><table class=\"hidden\"><tr><td>";
  else
    $output .= "<tr><td>".lang('forum', 'delete_post')."</td></tr></table><table class=\"hidden\"><tr><td>";
  makebutton(lang('forum', 'back'), "javascript:window.history.back()", 120);
  makebutton(lang('forum', 'confirm'), "forum.php?action=do_delete_post&amp;id={$topic[0]}", 120);
  $output .= "</td></tr></table></center>";
  //$sqlm->close();
  // Queries : 1
}
function forum_do_delete_post(){
  global $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output, $arcm_db, $sqlm;

  if(!isset($_GET["id"])) error(lang('forum', 'no_such_post'));
  else $id = $sqlm->quote_smart($_GET["id"]);

  $topic = $sqlm->query("SELECT id,topic,name,authorid,forum FROM forum_posts WHERE id = '$id';");
  if($sqlm->num_rows($topic)==0) error(lang('forum', 'no_such_post'));
  $topic = $sqlm->fetch_row($topic);
  if($user_lvl == 0 && $topic[3] != $user_id) error(lang('forum', 'no_access'));
  $fid = $topic[4];

  if($id==$topic[1]){
    $sqlm->query("DELETE FROM forum_posts WHERE topic = '$id'");
    redirect("forum.php?action=view_forum&id=$fid");
  }
  else
  {
    $sqlm->query("DELETE FROM forum_posts WHERE id = '$id'");
    $result = $sqlm->query("SELECT id FROM forum_posts WHERE topic = '{$topic[1]}' ORDER BY id DESC LIMIT 1;"); // get last post id
    $lastpostid = $sqlm->fetch_row($result);
    $lastpostid = $lastpostid[0];
    $sqlm->query("UPDATE forum_posts SET lastpost = '$lastpostid' WHERE id = '{$topic[1]}'"); // update topic' s last post id
    redirect("forum.php?action=view_topic&id={$topic[1]}");
  }
  // Queries : 1 (if delete topic) || 4 if delete post
}

function forum_add_topic(){
  global $enablesidecheck, $forum_skeleton, $maxqueries, $minfloodtime, $user_lvl, $user_id, $output, $arcm_db, $sqlm;

  if($enablesidecheck) $side = get_side(); // Better to use it here instead of call it many time in the loop :)

  if($minfloodtime > 0)
  {
    $userposts = $sqlm->query("SELECT time FROM forum_posts WHERE authorid = '$user_id' ORDER BY id DESC LIMIT 1;");
    if($sqlm->num_rows($userposts) != 0)
    {
      $mintimeb4post = $sqlm->fetch_row($userposts);
      $mintimeb4post = time() - strtotime($mintimeb4post[0]);

      if($mintimeb4post < $minfloodtime)
        error(lang('forum', 'please_wait1')." ".$minfloodtime." ".lang('forum', 'please_wait2'));
    }
  }

  if(!isset($_GET["id"])) error(lang('forum', 'no_such_forum'));
  else $id = $sqlm->quote_smart($_GET["id"]);

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid => $forum){
      if($fid == $id) $cat = $cid;
    }
  }

  if(empty($forum_skeleton[$cat]["forums"][$id])) error(lang('forum', 'no_such_forum'));
  $forum = $forum_skeleton[$cat]["forums"][$id];
  if($forum_skeleton[$cat]["level_post_topic"] > $user_lvl || $forum["level_post_topic"] > $user_lvl) error(lang('forum', 'no_access'));

  if($user_lvl == 0 && $enablesidecheck){
    if($forum_skeleton[$cat]["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        continue;
      else if($forum_skeleton[$cat]["side_access"] != $side) // Forumside different of the user side
        continue;
    }
    if($forum["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        continue;
      else if($forum["side_access"] != $side) // Forumside different of the user side
        continue;
    }
  }


  $output .= "<div class=\"top\"><h1>".lang('forum', 'forums')."</h1>".lang('forum', 'you_are_here')." : <a href=\"forum.php\">".lang('forum', 'forum_index')."</a> -> <a href=\"forum.php?action=view_forum&amp;id={$id}\">{$forum["name"]}</a> -> ".lang('forum', 'new_topic')."</div><center><table class=\"lined\">";

  $output .= "<form action=\"forum.php?action=do_add_topic\" method=\"POST\" name=\"form\"><table class=\"top_hidden\"><tr><td align=\"left\">";
  makebutton("Post", "javascript:do_submit()",100);
  $output .= "</td><td align=\"right\">".lang('forum', 'topic_name').": <input name=\"name\" SIZE=\"40\"></td></tr>
  <tr><td colspan=\"2\">".bbcode_editor_js()."
        <a href=\"javascript:ajtBBCode('[b]','[/b]')\">".lang('forum', 'bold')."</a>,
        <a href=\"javascript:ajtBBCode('[i]','[/i]')\">".lang('forum', 'italic')."</a>,
        <a href=\"javascript:ajtBBCode('[u]','[/u]')\">".lang('forum', 'underline')."</a>,
        <a href=\"javascript:ajtBBCode('[img]','[/img]')\">".lang('forum', 'image')."</a>,
        <a href=\"javascript:ajtBBCode('[url]','[/url]')\">".lang('forum', 'url')."</a>,
        <a href=\"javascript:ajtBBCode('[url=Click here]','[/url]')\">".lang('forum', 'url2')."</a>,
        <a href=\"javascript:ajtBBCode('[code]','[/code]')\">".lang('forum', 'code')."</a>,
        <a href=\"javascript:ajtBBCode('[quote]','[/quote]')\">".lang('forum', 'quote')."</a>,
        <a href=\"javascript:ajtBBCode('[quote=Someone]','[/quote]')\">".lang('forum', 'quote2')."</a>,
        <a href=\"javascript:ajtBBCode('[media]','[/media]')\">".lang('forum', 'media')."</a>
                <a href=\"javascript:ajtBBCode('[youtube]','[/youtube]')\">".lang('forum', 'YouTube')."</a>
        ".lang('forum', 'color')." : <select name=\"fontcolor\" onChange=\"ajtBBCode('[color=' + this.form.fontcolor.options[this.form.fontcolor.selectedIndex].value + ']', '[/color]'); this.selectedIndex=0;\" onMouseOver=\"helpline('fontcolor')\" id=\"forum_topic_reply_color_bg\">
          <option value=\"black\" style=\"color:black\">Black</option>
          <option value=\"silver\" style=\"color:silver\">Silver</option>
          <option value=\"gray\" style=\"color:gray\">Gray</option>
          <option value=\"maroon\" style=\"color:maroon\">Maroon</option>
          <option value=\"red\" style=\"color:red\">Red</option>
          <option value=\"purple\" style=\"color:purple\">Purple</option>
          <option value=\"fuchsia\" style=\"color:fuchsia\">Fuchsia</option>
          <option value=\"navy\" style=\"color:navy\">Navy</option>
          <option value=\"blue\" style=\"color:blue\">Blue</option>
          <option value=\"aqua\" style=\"color:aqua\">Aqua</option>
          <option value=\"teal\" style=\"color:teal\">Teal</option>
          <option value=\"lime\" style=\"color:lime\">Lime</option>
          <option value=\"green\" style=\"color:green\">Green</option>
          <option value=\"olive\" style=\"color:olive\">Olive</option>
          <option value=\"yellow\" style=\"color:yellow\">Yellow</option>
          <option value=\"white\" style=\"color:white\">White</option>
        </select>
        </td></tr><tr><td colspan=\"2\">
        <a href=\"javascript:ajtTexte(':)')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/smile.gif\"></a><a href=\"javascript:ajtTexte(':|')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/neutral.gif\"></a><a href=\"javascript:ajtTexte(':(')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/sad.gif\"></a><a href=\"javascript:ajtTexte(':D')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/razz.gif\"></a><a href=\"javascript:ajtTexte(':o')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/yikes.gif\"></a><a href=\"javascript:ajtTexte(';)')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/wink.gif\"></a><a href=\"javascript:ajtTexte(':/')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/hmm.gif\" /></a><a href=\"javascript:ajtTexte(':p')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/tongue.gif\"></a><a href=\"javascript:ajtTexte(':lol:')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/lol.gif\"></a><a href=\"javascript:ajtTexte(':mad:')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/angry.gif\"></a><a href=\"javascript:ajtTexte(':rolleyes:')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/roll.gif\"></a><a href=\"javascript:ajtTexte(':cool:')\"><img id=\"forum_topic_reply_0_border\" src=\"img/emoticons/cool.gif\"></a>
        </td></tr></table><TEXTAREA NAME=\"msg\" ROWS=8 COLS=93></TEXTAREA>
  <input type=\"hidden\" name=\"forum\" value=\"$id\" /></form>";
  $output .= "</center><br/>";
  //$sqlm->close();
  // Queries : 1
}
function forum_do_add_topic(){
  global $enablesidecheck, $forum_skeleton, $user_lvl, $user_name, $user_id, $arcm_db, $minfloodtime, $sqlm;

  if($enablesidecheck) $side = get_side(); // Better to use it here instead of call it many time in the loop :)


  {
    $userposts = $sqlm->query("SELECT time FROM forum_posts WHERE authorid = '$user_id' ORDER BY id DESC LIMIT 1;");
    if($sqlm->num_rows($userposts) != 0)
    {
      $mintimeb4post = $sqlm->fetch_row($userposts);
      $mintimeb4post = time() - strtotime($mintimeb4post[0]);

      if($mintimeb4post < $minfloodtime)
        error(lang('forum', 'please_wait'));
    }
  }

  if(!isset($_POST['forum'])) error(lang('forum', 'no_such_forum'));
  else $forum = $sqlm->quote_smart($_POST['forum']);

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid => $forum_){
      if($fid == $forum) $cat = $cid;
    }
  }
  if(empty($forum_skeleton[$cat]["forums"][$forum])) error(lang('forum', 'no_such_forum'));
  $forum_ = $forum_skeleton[$cat]["forums"][$forum];
  if($forum_skeleton[$cat]["level_post_topic"] > $user_lvl || $forum_["level_post_topic"] > $user_lvl) error(lang('forum', 'no_access'));

  if($user_lvl == 0 && $enablesidecheck){
    if($forum_skeleton[$cat]["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        continue;
      else if($forum_skeleton[$cat]["side_access"] != $side) // Forumside different of the user side
        continue;
    }
    if($forum_["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        continue;
      else if($forum_["side_access"] != $side) // Forumside different of the user side
        continue;
    }
  }

//  $_POST['msg'] = htmlspecialchars($_POST['msg']);
  $msg = trim($sqlm->quote_smart($_POST['msg']), " ");
//  $_POST['name'] = htmlspecialchars($_POST['name']);
  $name = trim($sqlm->quote_smart($_POST['name']), " ");

  if (strlen($name) > 49){
    //$sqlm->close();
    error(lang('forum', 'name_too_long'));
  }

  if (strlen($name) < 5){
    //$sqlm->close();
    error(lang('forum', 'name_too_short'));
  }

  if (strlen($msg) < 5){
    //$sqlm->close();
    error(lang('forum', 'msg_too_short'));
  }

  $msg = str_replace('\n', '<br />', $msg);
//  $msg = str_replace('\r', '<br />', $msg);

  $time = date("m/d/y H:i:s");

  $sqlm->query("INSERT INTO forum_posts (authorid, authorname, forum, name, text, time) VALUES ('$user_id', '$user_name', '$forum', '$name', '$msg', '$time');");
  $id = $sqlm->insert_id();
  $sqlm->query("UPDATE forum_posts SET topic = '$id', lastpost = '$id' WHERE id = '$id';");

  //$sqlm->close();

  redirect("forum.php?action=view_topic&id=$id");
  // Queries : 3
}
function forum_do_add_post(){
  global $enablesidecheck, $forum_skeleton, $minfloodtime, $user_lvl, $user_name, $user_id, $arcm_db, $sqlm;

  if($enablesidecheck) $side = get_side(); // Better to use it here instead of call it many time in the loop :)

  if($minfloodtime > 0)
  {
    $userposts = $sqlm->query("SELECT time FROM forum_posts WHERE authorid = '$user_id' ORDER BY id DESC LIMIT 1;");
    if($sqlm->num_rows($userposts) != 0)
    {
      $mintimeb4post = $sqlm->fetch_row($userposts);
      $mintimeb4post = time() - strtotime($mintimeb4post[0]);

      if($mintimeb4post < $minfloodtime)
        error(lang('forum', 'please_wait'));
    }
  }

  if(!isset($_POST['forum'])) error(lang('forum', 'no_such_forum'));
  else $forum = $sqlm->quote_smart($_POST['forum']);

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid => $forum_){
      if($fid == $forum) $cat = $cid;
    }
  }

  if(empty($forum_skeleton[$cat]["forums"][$forum])) error(lang('forum', 'no_such_forum'));
  $forum_ = $forum_skeleton[$cat]["forums"][$forum];
  if((($user_lvl > 0)||!$closed)&&($forum_skeleton[$cat]["level_post"] > $user_lvl || $forum_["level_post"] > $user_lvl)) error(lang('forum', 'no_access'));

  if($user_lvl == 0 && $enablesidecheck){
    if($forum_skeleton[$cat]["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        continue;
      else if($forum_skeleton[$cat]["side_access"] != $side) // Forumside different of the user side
        continue;
    }
    if($forum_["side_access"] != "ALL"){ // Not an all side forum
      if($side == "NO") // No char
        continue;
      else if($forum_["side_access"] != $side) // Forumside different of the user side
        continue;
    }
  }

  if(!isset($_POST['topic'])) error(lang('forum', 'no_such_topic'));
  else $topic = $sqlm->quote_smart($_POST['topic']);

//  $_POST['msg'] = htmlspecialchars($_POST['msg']);
  $msg = trim($sqlm->quote_smart($_POST['msg']), " ");

  $msg = str_replace('\n', '<br />', $msg);
//  $msg = str_replace('\r', '<br />', $msg);

  if (strlen($msg) < 5){
    //$sqlm->close();
    error(lang('forum', 'msg_too_short'));
  }

  $name = $sqlm->query("SELECT name FROM forum_posts WHERE id = '$topic';");
  $name = $sqlm->fetch_row($name);
  $name = $sqlm->quote_smart($name[0]);

  $time = date("m/d/y H:i:s");

  $sqlm->query("INSERT INTO forum_posts (authorid, authorname, forum, topic, name, text, time) VALUES ('$user_id', '$user_name', '$forum', $topic, '$name', '$msg', '$time');");
  $query = "SELECT id FROM forum_posts WHERE authorid='".$user_id."' AND topic='".$topic."' AND time='".$time."'";
  $result = $sqlm->query($query);
  $fields = $sqlm->fetch_assoc($result);
  $id = $fields['id'];
  //$id = @mysql_insert_id($sqlm);
  $sqlm->query("UPDATE forum_posts SET lastpost = $id WHERE id = $topic;");

  //$sqlm->close();

  redirect("forum.php?action=view_topic&id=$topic");
  // Queries : 4
}

function forum_edit_post(){
  global $forum_skeleton, $maxqueries, $minfloodtime, $user_lvl, $user_id, $output, $arcm_db, $sqlm;

  if(!isset($_GET["id"])) error(lang('forum', 'no_such_post'));
  else $id = $sqlm->quote_smart($_GET["id"]);

  $post = $sqlm->query("SELECT id,topic,authorid,forum,name,text FROM forum_posts WHERE id = '$id';");
  if($sqlm->num_rows($post)==0) error(lang('forum', 'no_such_post'));
  $post = $sqlm->fetch_row($post);

  if($user_lvl == 0 && $user_id != $post[2])
    error(lang('forum', 'no_access'));

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid_ => $forum){
      if($fid_ == $post[3]) $cat = $cid;
    }
  }
  if(empty($forum_skeleton[$cat]["forums"][$post[3]])) // No such forum..
    error(lang('forum', 'no_such_forum'));
  $forum = $forum_skeleton[$cat]["forums"][$post[3]];

  $output .= "<div class=\"top\"><h1>".lang('forum', 'forums')."</h1>".lang('forum', 'you_are_here')." : <a href=\"forum.php\">".lang('forum', 'forum_index')."</a> -> <a href=\"forum.php?action=view_forum&amp;id={$post[3]}\">{$forum["name"]}</a> -> <a href=\"forum.php?action=view_topic&amp;id={$post[1]}\">{$post[4]}</a> -> ".lang('forum', 'edit')."</div><form action=\"forum.php?action=do_edit_post\" method=\"POST\" name=\"form\"><center><table class=\"lined\">";

  $output .= "<table class=\"top_hidden\"><tr><td align=\"left\">";
  makebutton("Post", "javascript:do_submit()",220);
  if($post[0] = $post[0])
    $output .= "</td><td align=\"right\"><input type=\"hidden\" name=\"topic\" value=\"1\" /><input name=\"name\" SIZE=\"50\" value=\"$post[4]\"></td></tr>";
  else
    $output .= "</td><td align=\"right\">$post[4]</td></tr>";

  //$post[5] = str_replace('<br />', chr(10), $post[5]);

  $output .= "<tr><td colspan=\"2\">";
  bbcode_add_editor();
  $output .= "</td></tr></table>";

  $output .= "<TEXTAREA id=\"msg\" NAME=\"msg\" ROWS=8 COLS=93>$post[5]</TEXTAREA>
  <input type=\"hidden\" name=\"forum\" value=\"{$post[3]}\" />
  <input type=\"hidden\" name=\"post\" value=\"{$post[0]}\" />";

  $output .= "</center></form><br/>";
  //$sqlm->close();
  // Queries : 1
}
function forum_do_edit_post(){
  global $user_lvl, $user_name, $user_id, $arcm_db, $sqlm;

  if(!isset($_POST['forum'])) error(lang('forum', 'no_such_forum'));
  else $forum = $sqlm->quote_smart($_POST['forum']);
  if(!isset($_POST['post'])) error(lang('forum', 'no_such_post'));
  else $post = $sqlm->quote_smart($_POST['post']);

  if(!isset($_POST['name']))
    $topic = 0;
  else{
    $topic = 1;
//    htmlspecialchars($_POST['name']);
    $name = $sqlm->quote_smart($_POST['name']);
    if (strlen($name) > 49){
      //$sqlm->close();
      error(lang('forum', 'name_too_long'));
    }
    if (strlen($name) < 5){
      //$sqlm->close();
      error(lang('forum', 'name_too_short'));
    }
  }

//  $_POST['msg'] = htmlspecialchars($_POST['msg']);
  $msg = trim($sqlm->quote_smart($_POST['msg']), " ");

  if (strlen($msg) < 5){
    //$sqlm->close();
    error(lang('forum', 'msg_too_short'));
  }

  //$msg = str_replace('\n', '<br />', $msg);
//  $msg = str_replace('\r', '<br />', $msg);

  $result = $sqlm->query("SELECT topic FROM forum_posts WHERE id = $post;");
  $topicid = $sqlm->fetch_row($result);

  $sqlm->query("UPDATE forum_posts SET text = '$msg' WHERE id = $post;");

  if($topic == 1){
    $sqlm->query("UPDATE forum_posts SET name = '$name' WHERE topic = {$topicid[0]};");
  }

  $result = $sqlm->query("SELECT topic FROM forum_posts WHERE id = $post;");
  $topicid = $sqlm->fetch_row($result);

  //$sqlm->close();
  redirect("forum.php?action=view_topic&id={$topicid[0]}");
  // Queries : 3 (+1 if topic)
}

function forum_move_topic(){
  global $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output, $arcm_db, $sqlm;
  
  if(!isset($_GET["id"])) error(lang('forum', 'no_such_topic'));
  else $id = $sqlm->quote_smart($_GET["id"]);

  $topic = $sqlm->query("SELECT id,topic,authorid,forum, name FROM forum_posts WHERE id = '$id';");
  //                0 1   2   3   4
  if($sqlm->num_rows($topic)==0) error(lang('forum', 'no_such_topic'));
  $topic = $sqlm->fetch_row($topic);
  if($user_lvl == 0) error(lang('forum', 'no_access'));
  $fid = $topic[3];

  $cat = 0;
  foreach($forum_skeleton as $cid => $category){
    foreach($category["forums"] as $fid_ => $forum){
      if($fid_ == $fid) $cat = $cid;
    }
  }

  if(empty($forum_skeleton[$cat]["forums"][$fid])) // No such forum..
    error(lang('forum', 'no_such_forum'));
  $forum = $forum_skeleton[$cat]["forums"][$fid];

  $output .= "<div class=\"top\"><h1>".lang('forum', 'forums')."</h1>".lang('forum', 'you_are_here')." : <a href=\"forum.php\">".lang('forum', 'forum_index')."</a> -> <a href=\"forum.php?action=view_forum&amp;id={$fid}\">{$forum["name"]}</a> -> <a href=\"forum.php?action=view_topic&amp;id={$topic[1]}\">{$topic[4]}</a> -> ".lang('forum', 'move')."!</div><center><table class=\"lined\">
  <tr><td>".lang('forum', 'where')." : <form action=\"forum.php?action=do_move_topic\" method=\"POST\" name=\"form\"><select name=\"forum\">";

  foreach($forum_skeleton as $category){
    foreach($category["forums"] as $fid_ => $forum){
      if($fid_ != $fid)
        $output .= "<option value='$fid_'>{$forum["name"]}</option>";
      else
        $output .= "<option value='$fid_' selected>{$forum["name"]}</option>";
    }
  }

  $output .= "</select><input type=\"hidden\" name=\"id\" value=\"$id\" /></form></td></tr></table><table class=\"hidden\"><tr><td>";
  makebutton(lang('forum', 'back'), "javascript:window.history.back()", 120);
  makebutton(lang('forum', 'confirm'), "javascript:do_submit()", 120);
  $output .= "</td></tr></table></center>";
  //$sqlm->close();
  // Queries : 1
}
function forum_do_move_topic(){
  global $forum_skeleton, $maxqueries, $user_lvl, $user_id, $output, $arcm_db, $sqlm;

  if(!isset($_POST['forum'])) error(lang('forum', 'no_such_forum'));
  else $forum = $sqlm->quote_smart($_POST['forum']);
  if(!isset($_POST['id'])) error(lang('forum', 'no_such_topic'));
  else $id = $sqlm->quote_smart($_POST['id']);

  $sqlm->query("UPDATE forum_posts SET forum = '$forum' WHERE topic = '$id'"); // update topic' s last post id
  redirect("forum.php?action=view_topic&id=$id");
  // Queries : 1
}



if(isset($_GET['action']))
    $action = addslashes($_GET['action']);
else $action = NULL;

$output .= "
      <div class=\"bubble\">";

//$lang_forum = lang_forum();

switch ($action){
  case "index": forum_index(); break;
  case "view_forum": forum_view_forum(); break;
  case "view_topic": forum_view_topic(); break;
  case "add_topic": forum_add_topic(); break;
  case "do_add_topic": forum_do_add_topic(); break;
  case "edit_post": forum_edit_post(); break;
  case "do_edit_post": forum_do_edit_post(); break;
  case "delete_post": forum_delete_post(); break;
  case "do_delete_post": forum_do_delete_post(); break;
  case "do_add_post": forum_do_add_post(); break;
  case "edit_stick": forum_do_edit_stick(); break;
  case "edit_announce": forum_do_edit_announce(); break;
  case "edit_close": forum_do_edit_close(); break;
  case "move_topic": forum_move_topic(); break;
  case "do_move_topic": forum_do_move_topic(); break;
  default: forum_index();
}

unset($action);
//unset($lang_forum);

require_once("footer.php");

?>
