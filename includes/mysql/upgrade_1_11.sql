DROP TABLE IF EXISTS `time_zone`;
CREATE TABLE IF NOT EXISTS `time_zone` (
  `time_zone_id` int(11) NOT NULL auto_increment,
  `time_zone_php` varchar(255) default NULL,
  `time_zone_mysql` varchar(255) default NULL,
  `time_zone_title` varchar(255) default NULL,
  PRIMARY KEY  (`time_zone_id`),
  UNIQUE KEY `time_zone_php` (`time_zone_php`),
  KEY `time_zone_title` (`time_zone_title`)
);

INSERT INTO `time_zone` (`time_zone_id`, `time_zone_php`, `time_zone_mysql`, `time_zone_title`) VALUES
(1, 'EST', '-5:00', 'Eastern'),
(2, 'EDT', '-4:00', NULL),
(3, 'CST', '-6:00', 'Central'),
(4, 'CDT', '-5:00', NULL),
(5, 'MST', '-7:00', 'Mountain'),
(6, 'MDT', '-6:00', NULL),
(7, 'PST', '-8:00', 'Pacific'),
(8, 'PDT', '-7:00', NULL);
