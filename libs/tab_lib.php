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


//list of tables in ArcManager DB will be saved on Global backup
$tables_backup_arcm = Array
(
  'motd',
  'forum_posts',
  'point_system_invites',
  'realmlist',
);

//list of tables in ArcEmu Logon DB will be saved on Global backup
$tables_backup_logon = Array
(
  'accounts',
  'ipbans',
);

//list of tables in ArcEmu Characters DB will be saved on Global backup
$tables_backup_characters = Array
(
  'account_data',
  'account_forced_permissions',
  'arenateams',
  'auctions',
  'banned_names',
  'character_achievement',
  'character_achievement_progress',
  'characters',
  'characters_insert_queue',
  'charters',
  'clientaddons',
  'command_overrides',
  'corpses',
  'gm_tickets',
  'groups',
  'guild_bankitems',
  'guild_banklogs',
  'guild_banktabs',
  'guild_data',
  'guild_logs',
  'guild_ranks',
  'guilds',
  'instanceids',
  'instances',
  'mailbox',
  'mailbox_insert_queue',
  'playercooldowns',
  'playeritems',
  'playeritems_insert_queue',
  'playerpets',
  'playerpetspells',
  'playersummons',
  'playersummonspells',
  'questlog',
  'server_settings',
  'social_friends',
  'social_ignores',
  'tutorials',
);


//list of tables in characters db you need to delete data from on user deletion
$tab_del_user_characters = Array
(
  Array('auctions','owner'),
  Array('character_achievement','guid'),
  Array('character_achievement_progress','guid'),
  Array('playerpetspells','ownerguid'),
  Array('playerpets','ownerguid'),
  Array('instances','creator_guid'),
  Array('charters','leaderguid'),
  Array('gm_tickets','playerguid'),
  Array('guild_data','playerid'),
  Array('guilds','leaderguid'),
  Array('instanceids','playerguid'),
  Array('playercooldowns','player_guid'),
  Array('mailbox','player_guid'),
  Array('playeritems','ownerguid'),
  Array('playersummons','ownerguid'),
  Array('playersummonspells','ownerguid'),
  Array('questlog','player_guid'),
  Array('social_friends','character_guid'),
  Array('social_ignores','character_guid'),
  Array('tutorials','playerid')
);


//list of tables in characters db you need to backup data from on single user backup
$tab_backup_user_characters = $tab_del_user_characters;


?>
