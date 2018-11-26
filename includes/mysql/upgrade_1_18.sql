DROP TABLE IF EXISTS `team_need`;
CREATE TABLE IF NOT EXISTS `team_need` (
  `team_need_id` int(11) NOT NULL auto_increment,
  `team_id` int(11) default NULL,
  `position_id` int(11) default NULL,
  `pick_id` int(11) default NULL,
  `mock_pick_id` int(11) default NULL,
  `team_need_order` int(11) default NULL,
  PRIMARY KEY  (`team_need_id`),
  KEY `team_id` (`team_id`),
  KEY `position_id` (`position_id`),
  KEY `pick_id` (`pick_id`),
  KEY `team_need_order` (`team_need_order`),
  KEY `mock_pick_id` (`mock_pick_id`)
);

CREATE TABLE IF NOT EXISTS `team_to_name` (
  `team_to_name_id` int(11) NOT NULL auto_increment,
  `team_id` int(11) default NULL,
  `team_name` varchar(255) default NULL,
  PRIMARY KEY  (`team_to_name_id`),
  KEY `team_id` (`team_id`),
  KEY `team_name` (`team_name`)
);

DROP TABLE IF EXISTS `pick_method`;
CREATE TABLE IF NOT EXISTS `pick_method` (
  `pick_method_id` int(11) NOT NULL auto_increment,
  `pick_method_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`pick_method_id`)
);

INSERT INTO `pick_method` (`pick_method_id`, `pick_method_name`) VALUES
(1, 'Player Queue'),
(2, 'Best At Position'),
(3, 'Scout Pick');

DROP TABLE IF EXISTS `mock_draft`;
CREATE TABLE IF NOT EXISTS `mock_draft` (
  `pick_id` int(11) NOT NULL default '0',
  `team_id` int(11) default NULL,
  `player_id` int(11) default NULL,
  PRIMARY KEY  (`pick_id`),
  KEY `player_id` (`player_id`),
  KEY `team_id` (`team_id`)
);

ALTER TABLE `pick` ADD PRIMARY KEY ( `pick_id` );