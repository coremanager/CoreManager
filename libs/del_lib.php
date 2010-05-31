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


require_once 'tab_lib.php';

//##########################################################################################
//Delete character
function del_char($guid, $realm)
{
  global $characters_db, $logon_db, $realm_id,
    $user_lvl, $user_id, $tab_del_user_characters, $sqlc, $sqll;

  $sql = new SQL;
  $sql->connect($characters_db[$realm]['addr'], $characters_db[$realm]['user'], $characters_db[$realm]['pass'], $characters_db[$realm]['name']);

  $query = $sql->query("SELECT acct, online FROM characters WHERE guid = '".$guid."' LIMIT 1");
  $owner_acc_id = $sql->result($query, 0, 'acct');

  $owner_gmlvl = $sqll->result($sqll->query("SELECT gm FROM accounts WHERE acct = '".$owner_acc_id."'"), 0);

  if ( ($user_lvl >= gmlevel($owner_gmlvl)) || ($owner_acc_id == $user_id) )
  {
    if ($sql->result($query, 0, 'online'));
    else
    {
      // in case the character is a guild leader
      $sql->query('
        DELETE FROM guild_data WHERE guildid IN
        (SELECT guildid FROM guilds WHERE leaderguid IN
        (SELECT guid FROM characters WHERE guid = '.$guid.'))');

      // delete everything else from the character
      foreach ($tab_del_user_characters as $value)
        $sql->query('DELETE FROM '.$value[0].' WHERE '.$value[1].' = '.$guid.'');

      // delete account_data for this user
      $sql->query("DELETE FROM account_data WHERE acct = '".$owner_acc_id."'");
      // finally, delete the character
      $sql->query("DELETE FROM characters WHERE guid = '".$guid."'");
      return true;
    }
  }
  return false;
}


//##########################################################################################
//Delete Account - return array(deletion_flag , number_of_chars_deleted)
function del_acc($acc_id)
{
  global $characters_db, $logon_db, $arcm_db, $realm_id,
    $user_lvl, $user_id, $tab_del_user_realmd, $tab_del_user_char, $tab_del_user_characters, $sqlc, $sqll, $sqlm;

  $del_char = 0;

  $query = $sqll->query('SELECT gm FROM accounts WHERE acct ='.$acc_id.'');
  $gmlevel = $sqll->result($query, 0, 'gm');
  
  //get login name to delete from config_accounts
  $query = $sqll->query('SELECT login FROM accounts WHERE acct ='.$acc_id.'');
  $acct_login = $sqll->result($query, 0, 'login');

  if ( ($user_lvl >= gmlevel($gmlevel)) || ($acc_id == $user_id) )
  {
    $online = $sqlc->result($sqlc->query("SELECT COUNT(*) FROM characters WHERE acct = '".$acct_id."'"), 0);
    if ($online > 0);
    else
    {
      foreach ($characters_db as $db)
      {
        $sql = new SQL;
        $sql->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
        $result = $sql->query('SELECT guid FROM characters WHERE acct = '.$acc_id.'');
        while ($row = $sql->fetch_assoc($result))
        {
          $temp = del_char($row['guid'], $db['id']);
          $del_char++;
        }
      }
      
      $sqll->query("DELETE FROM accounts WHERE acct = '".$acc_id."'");
      $sqlm->query("DELETE FROM config_accounts WHERE Login = '".$acct_login."'");
      if ($sqll->affected_rows())
        return array(true, $del_char);
    }
  }
  return array(false, $del_char);
}


//##########################################################################################
//Delete Guild
function del_guild($guid, $realm)
{
  global $characters_db, $sqlc;

  require_once 'libs/data_lib.php';

  //clean data inside characters.data field
  while ($guild_member = $sqlc->result($sqlc->query('SELECT guid FROM guild_member WHERE guildid = '.$guid.''),0))
  {
    $data = $sqlc->result($sqlc->query('SELECT data FROM characters WHERE guid = '.$guild_member.''), 0);
    $data = explode(' ', $data);
    $data[CHAR_DATA_OFFSET_GUILD_ID] = 0;
    $data[CHAR_DATA_OFFSET_GUILD_RANK] = 0;
    $data = implode(' ', $data);
    $sqlc->query('UPDATE characters SET data = '.$data.' WHERE guid = '.$guild_member.'');
  }

  $sqlc->query('DELETE FROM item_instance WHERE guid IN (SELECT item_guid FROM guild_bank_item WHERE guildid ='.$guid.')');
  $sqlc->query('DELETE FROM guild_bank_item WHERE guildid = '.$guid.'');
  $sqlc->query('DELETE FROM guild_bank_eventlog WHERE guildid = '.$guid.'');
  $sqlc->query('DELETE FROM guild_bank_right WHERE guildid = '.$guid.'');
  $sqlc->query('DELETE FROM guild_bank_tab WHERE guildid = '.$guid.'');
  $sqlc->query('DELETE FROM guild_eventlog WHERE guildid = '.$guid.'');
  $sqlc->query('DELETE FROM guild_rank WHERE guildid = '.$guid.'');
  $sqlc->query('DELETE FROM guild_member WHERE guildid = '.$guid.'');
  $sqlc->query('DELETE FROM guild WHERE guildid = '.$guid.'');

  if ($sqlc->affected_rows())
    return true;
  else
    return false;

}


//##########################################################################################
//Delete Arena Team
function del_arenateam($guid, $realm)
{
  global $characters_db, $sqlc;

  $sqlc->query('DELETE FROM arena_team WHERE arenateamid = '.$guid.'');
  $sqlc->query('DELETE FROM arena_team_stats WHERE arenateamid = '.$guid.'');
  $sqlc->query('DELETE FROM arena_team_member WHERE arenateamid = '.$guid.'');

  if ($sqlc->affected_rows())
    return true;
  else
    return false;

}


?>
