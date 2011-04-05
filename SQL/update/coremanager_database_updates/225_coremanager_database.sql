DROP TABLE IF EXISTS `custom_logos`;
CREATE TABLE `custom_logos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_data` longblob NOT NULL,
 
  primary key (id),
  index (filename)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO config_misc (`Key`, `Value`) VALUES ('Use_Custom_Logo', '0');
INSERT INTO config_misc (`Key`, `Value`) VALUES ('Custom_Logo', '0');