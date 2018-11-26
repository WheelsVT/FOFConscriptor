DROP TABLE IF EXISTS `position`;
CREATE TABLE IF NOT EXISTS `position` (
  `position_id` int(11) NOT NULL auto_increment,
  `position_name` varchar(10) default NULL,
  `position_scout_weight` int(11) NOT NULL default '100',
  PRIMARY KEY  (`position_id`)
);

INSERT INTO `position` (`position_id`, `position_name`, `position_scout_weight`) VALUES
(1, 'QB', 100),
(2, 'RB', 100),
(3, 'FB', 75),
(4, 'TE', 100),
(5, 'WR', 100),
(6, 'C', 100),
(7, 'G', 100),
(8, 'T', 100),
(9, 'P', 100),
(10, 'K', 100),
(11, 'DE', 100),
(12, 'DT', 100),
(13, 'ILB', 100),
(14, 'OLB', 100),
(15, 'CB', 100),
(16, 'S', 100);

