DROP TABLE IF EXISTS `point_system_coupons`;
CREATE TABLE `point_system_coupons` (
  `entry` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `target` int(10) unsigned NOT NULL,
  `date_issued` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expiration` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `credits` int(10) unsigned NOT NULL,
  `money` int(10) unsigned NOT NULL,
  `item_id` int(10) NOT NULL,
  `item_count` int(10) unsigned NOT NULL,
  `raffle_id` int(10) NOT NULL,
  `redemption_option` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `text` varchar(1024) NOT NULL,
  `usage_limit` int(11) NOT NULL,
  `enabled` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entry`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `point_system_coupon_usage`;
CREATE TABLE `point_system_coupon_usage` (
  `coupon` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `date_used` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`coupon`,`user`,`date_used`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `point_system_prize_bags`;
CREATE TABLE `point_system_prize_bags` (
  `entry` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slots` int(10) unsigned NOT NULL,
  `owner` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entry`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `point_system_prize_bag_items`;
CREATE TABLE `point_system_prize_bag_items` (
  `bag` int(10) unsigned NOT NULL,
  `slot` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `item_count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`bag`,`slot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `point_system_raffles`;
CREATE TABLE `point_system_raffles` (
  `entry` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `drawing` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `credits` int(10) unsigned NOT NULL,
  `money` int(10) unsigned NOT NULL,
  `item_id` int(10) NOT NULL,
  `item_count` int(10) unsigned NOT NULL,
  `cost_credits` int(10) unsigned NOT NULL,
  `cost_money` int(10) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `text` varchar(512) NOT NULL,
  `tickets_per_user` int(10) NOT NULL,
  `ticket_limit` int(11) NOT NULL,
  `announce_acct` int(10) unsigned NOT NULL,
  `enabled` int(10) unsigned NOT NULL,
  `completed` int(11) NOT NULL,
  `winner` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entry`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `point_system_raffle_tickets`;
CREATE TABLE `point_system_raffle_tickets` (
  `raffle` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `date_purchased` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`raffle`,`user`,`date_purchased`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
