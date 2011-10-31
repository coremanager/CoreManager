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


  // level 1 debug prints total queries,
  //  so we would have to close these, or we can't have debug output
  if ( $debug )
  {
    if ( isset($sql["logon"]) )
      $sql["logon"]->close();
    if ( isset($sql["char"]) )
      $sql["char"]->close();
    if ( isset($sql["mgr"]) )
      $sql["mgr"]->close();
    if ( isset($sql["world"]) )
      $sql["world"]->close();

    // level 3 debug lists all global vars, but can't read classes
    // level 4 debug prints all global arrays, but can't print content of classes
    //  so we would have to close these, or we can't have debug output
    if ( $debug > 2)
    {
      unset($sql);
      /*unset($sql["logon"]);
      unset($sql["char"]);
      unset($sql["mgr"]);
      unset($sql["world"]);*/
    }
  }

  // we start with a lead of 10 spaces,
  //  because last line of header is an opening tag with 8 spaces
  //  so if the file before this follows the indent, we will be at the same place it starts
  //  keep html indent in sync, so debuging from browser source would be easy to read
  $output .= '
        </div><!-- bubble -->
        <!-- start of footer.php -->';

  if ( !$debug && $index_show_realms )
  {
    $result = $sql["mgr"]->query("SELECT `Index` AS id, Name AS name FROM `config_servers`");

    if ( ( $sql["mgr"]->num_rows($result) > 1 ) && ( count($server) > 1 ) && ( $_SERVER["PHP_SELF"] == "/index.php" ) )
    {
      $output .= '
        <div class="bubble">
          <center>
            <div class="fieldset_border realm_fieldset">
              <span class="legend">'.lang("header", "realms").'</span>
              <table class="lined" style="width: 97%;">
                <tr>
                  <th>'.lang("realm", "name").'</th>
                  <th width="15%">'.lang("realm", "status").'</th>
                  <th width="15%">'.lang("realm", "online").'</th>
                </tr>';

      while ( $row = $sql["mgr"]->fetch_assoc($result) )
      {
        $output .= '
                <tr>
                  <td><a href="realm.php?action=set_def_realm&amp;id='.$row["id"].'&amp;url='.$_SERVER["PHP_SELF"].'">'.htmlentities($row["name"], ENT_COMPAT, $site_encoding).'</a></td>';

        // show server status
        if ( test_port($server[$row["id"]]["addr"], $server[$row["id"]]["game_port"]) )
          $output .= '
                  <td><img src="img/up.gif" alt="" /></td>';
        else
          $output .= '
                  <td><img src="img/down.gif" alt="" /></td>';

        $sqlt = new SQL;
        $sqlt->connect($characters_db[$row["id"]]["addr"], $characters_db[$row["id"]]["user"], $characters_db[$row["id"]]["pass"], $characters_db[$row["id"]]["name"], $characters_db[$row["id"]]["encoding"]);

        // get max characters for this realm
        if ( $core == 1 )
          $c_query = "SELECT COUNT(*) FROM characters";
        else
          $c_query = "SELECT COUNT(*) FROM characters";
        $c_result = $sqlt->query($c_query);
        $c_fields = $sqlt->fetch_assoc($c_result);
        $c_count = $c_fields["COUNT(*)"];

        // get online characters for this realm
        if ( $core == 1 )
          $o_query = "SELECT COUNT(*) FROM characters WHERE online<>0";
        else
          $o_query = "SELECT COUNT(*) FROM characters WHERE online<>0";
        $o_result = $sqlt->query($o_query);
        $o_fields = $sqlt->fetch_assoc($o_result);
        $o_count = $o_fields["COUNT(*)"];

        $output .= '
                  <td>'.$o_count.'/'.$c_count.'</td>
                </tr>';

        unset($sqlt);
      }

      $output .= '
              </table>
            </div>
          </center>
        </div>';
    }
  }

  $output .= '
        <div id="body_bottom">
          <table class="table_bottom">
            <tr>
              <td class="table_bottom_left"></td>
              <td class="table_bottom_middle">';
  // we can't get the newest user if we are in debug mode
  if ( $show_newest_user && !$debug )
  {
    if ( $core == 1 )
    {
      $new_query = "SELECT accounts.acct, config_accounts.Login, JoinDate AS joindate
        FROM config_accounts
          LEFT JOIN `".$logon_db["name"]."`.accounts ON accounts.login=config_accounts.Login COLLATE utf8_unicode_ci
        ORDER BY joindate DESC LIMIT 1";
      $new_result = $sql["mgr"]->query($new_query);
      $new = $sql["mgr"]->fetch_assoc($new_result);
    }
    else
    {
      $new_query = "SELECT id AS acct, username AS Login, joindate
        FROM account ORDER BY joindate DESC LIMIT 1";
      $new_result = $sql["logon"]->query($new_query);
      $new = $sql["logon"]->fetch_assoc($new_result);
    }

    $output .= 
                lang("footer", "newest").': '.( ( $user_lvl >= $action_permission["insert"] ) ? '<a href="user.php?action=edit_user&error=11&acct='.$new["acct"].'">' : '' ).$new["Login"].( ( $user_lvl >= $action_permission["insert"] ) ? '</a>' : '' ).'
                <br />';
  }

  $output .=
                lang("footer", "bugs_to_admin").' <a href="mailto:'.$admin_mail.'">'.lang("footer", "site_admin").'</a><br />';

  unset($admin_mail);
  $output .= sprintf(lang("footer", "execute").': %.5f', (microtime(true) - $time_start)).' '.lang("footer", "seconds").'.';
  unset($time_start);

  // if any debug mode is activated, show memory usage
  if ( $debug )
  {
    $output .= '
                Queries: '.$tot_queries.' on '.$_SERVER["SERVER_SOFTWARE"];
    unset($tot_queries);
    if ( function_exists('memory_get_usage') )
      $output .= sprintf('<br />Mem. Usage: %.0f/%.0fK Peek: %.0f/%.0fK Global: %.0fK Limit: %s', memory_get_usage()/1024, memory_get_usage(true)/1024, memory_get_peak_usage()/1024, memory_get_peak_usage(true)/1024, sizeof($GLOBALS), ini_get('memory_limit'));
  }

  //---------------------Version Information-------------------------------------

  $output .= '
                <div id="version">'.lang("footer", "powered").': ';
  if ( $show_version["show"] && $user_lvl >= $show_version["version_lvl"] )
  {
    if ( ( 1 < $show_version["show"] ) && ( $user_lvl >= $show_version["svnrev_lvl"] ) )
    {
      $show_version["svnrev"] = '';
      // if file exists and readable
      if ( is_readable('.svn/entries') )
      {
        $file_obj = new SplFileObject('.svn/entries');
        // line 4 is where svn revision is stored
        $file_obj->seek(3);
        $show_version["svnrev"] = rtrim($file_obj->current());
        unset($file_obj);
      }

      if ( strlen($current) == 0 )
      {
        // if we didn't get a revision number from the entries file then we might be using SVN 1.7+
        if ( is_readable(".svn/wc.db") )
        {
          class wcDB extends SQLite3
          {
            function __construct()
            {
              $this->open(".svn/wc.db");
            }
          }

          $db = new wcDB();
          $result = $db->query("SELECT MAX(revision) FROM `NODES`");
          $result = $result->fetchArray();
          $show_version["svnrev"] = $result[0];

          unset($db);
        }
      }

      $output .= 
        $show_version["version"].lang("footer", "revision").': <a href="http://trac6.assembla.com/coremanager/changeset/'.$show_version["svnrev"].'">'.$show_version["svnrev"].'</a>';
    }
    else
    {
      $output .= 
        lang("footer", "version").': '.$show_version["version"].lang("footer", "revision").' '.$show_version["svnrev"];
    }
  }
  $output .= '
                </div>';

  // links at footer
  $output .= '
                <p>';
  
  switch ( $core )
  {
    case 1:
    {
      $output .= '
                  <a href="http://www.arcemu.org/" target="_blank"><img src="img/logo-arcemu.png" class="logo_border" alt="arcemu" /></a>';
      break;
    }
    case 2:
    {
      $output .= '
                  <a href="http://getmangos.com/" target="_blank"><img src="img/logo-mangos.png" class="logo_border" alt="mangos" /></a>';
      break;
    }
    case 3:
    {
      $output .= '
                  <a href="http://www.trinitycore.org/" target="_blank"><img src="img/logo-trinity.png" class="logo_border" alt="trinity" /></a>';
      break;
    }
  }
  $output .= '
                  <a href="http://www.php.net/" target="_blank"><img src="img/logo-php.png" class="logo_border" alt="php" /></a>
                  <a href="http://www.mysql.com/" target="_blank"><img src="img/logo-mysql.png" class="logo_border" alt="mysql" /></a>
                  <!-- a href="http://validator.w3.org/check?uri=referer" target="_blank"><img src="img/logo-css.png" class="logo_border" alt="w3" /></a -->
                  <br />
                  <a href="http://www.mozilla.com/" target="_blank"><img src="img/logo-firefox.png" class="logo_border" alt="firefox" /></a>
                  <a href="http://www.google.com/chrome?hl=en&amp;brand=CHMI" target="_blank"><img src="img/logo-chrome.png" class="logo_border" alt="firefox" /></a>
                  <a href="http://www.apple.com/safari/" target="_blank"><img src="img/logo-safari.png" class="logo_border" alt="firefox" /></a>
                  <a href="http://www.opera.com/" target="_blank"><img src="img/logo-opera.png" class="logo_border" alt="opera" /></a>
                </p>
              </td>
              <td class="table_bottom_right"></td>
            </tr>
          </table>
          <br />';
  if ( $page_bottom_ad )
  {
    $output .= '
          <table class="table_bottom">
            <tr>
              <td>'
                .$page_bottom_ad_content.'
              </td>
            </tr>
          </table>
          <br />';
  }

  echo $output;

  unset($output);
  // we need to close $output before we start debug mode 3 or higher
  //  we will get double output if we don't
  if ( $debug > 2 )
  {
    echo '
          <table>
            <tr>
              <td align="left">';
    $arrayObj = new ArrayObject(get_defined_vars());
    for ( $iterator = $arrayObj->getIterator(); $iterator->valid(); $iterator->next() )
    {
      if ( is_array($iterator->current()) )
        echo '
                <br />'.$iterator->key().' => '.print_r($iterator->current());
      elseif ( !is_a($iterator->current(), "SQL") )
        echo '
                <br />'.$iterator->key().' => '.$iterator->current();
    }
    unset($iterator);
    unset($arrayObj);
    // debug mode 3 lists all global vars and their values, but not for arrays
    // debug mode 4 branches all arrays and their content,
    if ( $debug > 3 )
    {
      echo '
                <pre>';
                  print_r($GLOBALS);
      echo '
                </pre>';
    }
    echo '
              </td>
            </tr>
          </table>';
  }

?>

        </div><!-- body_bottom -->
      </div><!-- body_main -->
    </center>
  </body>
</html>
