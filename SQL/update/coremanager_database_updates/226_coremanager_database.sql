DROP TABLE IF EXISTS `invitations`;
CREATE TABLE `invitations` (
  `issuer_acct_id` int(11) DEFAULT NULL,
  `invited_email` varchar(255) DEFAULT NULL,
  `invitation_key` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO config_misc (`Key`, `Value`) VALUES ('Invitation_Only', '0');
