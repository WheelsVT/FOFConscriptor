DROP TABLE IF EXISTS `time_zone`;
CREATE TABLE IF NOT EXISTS `time_zone` (
  `time_zone_id` int(11) NOT NULL auto_increment,
  `time_zone_php` varchar(255) default NULL,
  `time_zone_mysql` varchar(255) default NULL,
  `time_zone_title` varchar(255) default NULL,
  PRIMARY KEY  (`time_zone_id`),
  UNIQUE KEY `time_zone_php` (`time_zone_php`),
  KEY `time_zone_title` (`time_zone_title`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

INSERT INTO `time_zone` (`time_zone_id`, `time_zone_php`, `time_zone_mysql`, `time_zone_title`) VALUES
(1, 'EST', '-5:00', 'EST'),
(2, 'EDT', '-4:00', 'EDT'),
(3, 'CST', '-6:00', 'CST'),
(4, 'CDT', '-5:00', 'CDT'),
(5, 'MST', '-7:00', 'MST'),
(6, 'MDT', '-6:00', 'MDT'),
(7, 'PST', '-8:00', 'PST'),
(8, 'PDT', '-7:00', 'PDT');
