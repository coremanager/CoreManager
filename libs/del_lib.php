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
    $user_lvl, $user_id, $tab_del_user_characters, $sql, $core;

  if ( $core == 1 )
    $query = $sql['char']->query("SELECT acct, online FROM characters WHERE guid='".$guid."' LIMIT 1");
  else
    $query = $sql['char']->query("SELECT account AS acct, online FROM characters WHERE guid='".$guid."' LIMIT 1");

  $owner_acc_id = $sql['char']->result($query, 0, 'acct');

  if ( $core == 1 )
    $acc_query = "SELECT login AS username FROM accounts WHERE acct='".$owner_acc_id."'";
  else
    $acc_query = "SELECT username FROM account WHERE id='".$owner_acc_id."'";

  $owner_acc_result = $sql['logon']->query($acc_query);
  $owner_acc = $sql['logon']->fetch_assoc($owner_acc_result);

  $owner_gmlvl = $sql['mgr']->result($sql['mgr']->query("SELECT SecurityLevel FROM config_accounts WHERE Login='".$owner_acc['username']."'"), 0);

  if ( ( $user_lvl >= gmlevel($owner_gmlvl) ) || ( $owner_acc_id == $user_id ) )
  {
    if ( $sql['char']->result($query, 0, 'online') )
      ;
    else
    {
      // in case the character is a guild leader
      if ( $core == 1 )
        $sql['char']->query("DELETE FROM guild_data WHERE guildid IN
          (SELECT guildid FROM guilds WHERE leaderguid IN
          (SELECT guid FROM characters WHERE guid='".$guid."'))'");
      else
        $sql['char']->query("DELETE FROM guild_member WHERE guildid IN
          (SELECT guildid FROM guild WHERE leaderguid IN
          (SELECT guid FROM characters WHERE guid='".$guid."'))");
          
      // MaNGOS & Trinity: Delete Pets
      if ( $core != 1 )
      {
        $pet_query = "SELECT id FROM character_pet WHERE owner='".$guid."'";
        $pet_result = $sql['char']->query($pet_query);
        $pet = $sql['char']->fetch_assoc($pet_result);

        // delete pet spells etc...
        $sql['char']->query("DELETE FROM pet_aura WHERE guid='".$pet['id']."'");
        $sql['char']->query("DELETE FROM pet_spell WHERE guid='".$pet['id']."'");
        $sql['char']->query("DELETE FROM pet_spell_cooldown WHERE guid='".$pet['id']."'");
        // delete the pet
        $sql['char']->query("DELETE FROM character_pet WHERE owner='".$guid."'");
      }

      // MaNGOS & Trinity: Delete character_tutorial
      if ( $core != 1 )
        $sql['char']->query('DELETE FROM character_tutorial WHERE account='.$owner_acc_id.'');

      // delete everything else from the character
      foreach ( $tab_del_user_characters as $value )
        $sql['char']->query('DELETE FROM '.$value[0].' WHERE '.$value[1].'='.$guid.'');

      // ArcEmu: Delete account_data for this user
      if ( $core == 1 )
        $sql['char']->query("DELETE FROM account_data WHERE acct='".$owner_acc_id."'");

      // finally, delete the character
      $sql['char']->query("DELETE FROM characters WHERE guid = '".$guid."'");
      return true;
    }
  }
  return false;
}


//##########################################################################################
//Delete Account - return array(deletion_flag , number_of_chars_deleted)
function del_acc($acc_id)
{
  global $characters_db, $logon_db, $corem_db, $realm_id,
    $user_lvl, $user_id, $tab_del_user_realmd, $tab_del_user_char, $tab_del_user_characters, $sql, $core;

  $del_char = 0;
  
  // get username name to delete from account table
  if ( $core == 1 )
    $query = $sql['logon']->query("SELECT login AS username FROM accounts WHERE acct='".$acc_id."'");
  else
    $query = $sql['logon']->query("SELECT username FROM account WHERE id='".$acc_id."'");

  $acct_name = $sql['logon']->result($query, 0, 'username');

  // get the account's owner's SecurityLevel (not 100% perfect since we don't use the core gm)
  $query = $sql['mgr']->query("SELECT SecurityLevel FROM config_accounts WHERE Login='".$acc_name."'");
  $gmlevel = $sql['mgr']->result($query, 0, 'SecurityLevel');

  if ( ( $user_lvl >= gmlevel($gmlevel) ) || ( $acc_id == $user_id ) )
  {
    if ( $core == 1 )
      $char_count_query = "SELECT COUNT(*) FROM characters WHERE acct='".$acct_id."'";
    else
      $char_count_query = "SELECT COUNT(*) FROM characters WHERE account='".$acct_id."'";

    $online = $sql['char']->result($sql['char']->query($char_count_query), 0);
    if ( $online > 0 );
    else
    {
      foreach ( $characters_db as $db )
      {
        $sqlx = new SQL;
        $sqlx->connect($db['addr'], $db['user'], $db['pass'], $db['name']);

        if ( $core == 1 )
          $result = $sqlx->query("SELECT guid FROM characters WHERE acct='".$acc_id."'");
        else
          $result = $sqlx->query("SELECT guid FROM characters WHERE account='".$acc_id."'");

        while ( $row = $sqlx->fetch_assoc($result) )
        {
          $temp = del_char($row['guid'], $db['id']);
          $del_char++;
        }
      }

      if ( $core == 1 )
        $sql['logon']->query("DELETE FROM accounts WHERE acct='".$acc_id."'");
      else
        $sql['logon']->query("DELETE FROM account WHERE id='".$acc_id."'");

      $sql['mgr']->query("DELETE FROM config_accounts WHERE Login = '".$acct_name."'");
      if ( $sql['logon']->affected_rows() )
        return array(true, $del_char);
    }
  }
  return array(false, $del_char);
}


//##########################################################################################
//Delete Guild
function del_guild($guid, $realm)
{
  global $characters_db, $sql;

  require_once 'libs/data_lib.php';

  // clean data inside characters.data field
  while ( $guild_member = $sql['char']->result($sql['char']->query('SELECT guid FROM guild_member WHERE guildid = '.$guid.''),0) )
  {
    $data = $sql['char']->result($sql['char']->query('SELECT data FROM characters WHERE guid = '.$guild_member.''), 0);
    $data = explode(' ', $data);
    $data[CHAR_DATA_OFFSET_GUILD_ID] = 0;
    $data[CHAR_DATA_OFFSET_GUILD_RANK] = 0;
    $data = implode(' ', $data);
    $sql['char']->query('UPDATE characters SET data = '.$data.' WHERE guid = '.$guild_member.'');
  }

  $sql['char']->query('DELETE FROM item_instance WHERE guid IN (SELECT item_guid FROM guild_bank_item WHERE guildid ='.$guid.')');
  $sql['char']->query('DELETE FROM guild_bank_item WHERE guildid = '.$guid.'');
  $sql['char']->query('DELETE FROM guild_bank_eventlog WHERE guildid = '.$guid.'');
  $sql['char']->query('DELETE FROM guild_bank_right WHERE guildid = '.$guid.'');
  $sql['char']->query('DELETE FROM guild_bank_tab WHERE guildid = '.$guid.'');
  $sql['char']->query('DELETE FROM guild_eventlog WHERE guildid = '.$guid.'');
  $sql['char']->query('DELETE FROM guild_rank WHERE guildid = '.$guid.'');
  $sql['char']->query('DELETE FROM guild_member WHERE guildid = '.$guid.'');
  $sql['char']->query('DELETE FROM guild WHERE guildid = '.$guid.'');

  if ( $sql['char']->affected_rows() )
    return true;
  else
    return false;

}


//##########################################################################################
//Delete Arena Team
function del_arenateam($guid, $realm)
{
  global $characters_db, $sql;

  $sql['char']->query('DELETE FROM arena_team WHERE arenateamid = '.$guid.'');
  $sql['char']->query('DELETE FROM arena_team_stats WHERE arenateamid = '.$guid.'');
  $sql['char']->query('DELETE FROM arena_team_member WHERE arenateamid = '.$guid.'');

  if ($sql['char']->affected_rows())
    return true;
  else
    return false;

}


?>
