DROP TABLE IF EXISTS `chat`;
CREATE TABLE `chat` (
  `chat_id` int(11) NOT NULL auto_increment,
  `team_id` int(11) NOT NULL,
  `chat_time` datetime NOT NULL,
  `chat_message` text NOT NULL,
  `chat_room_id` int(11) default NULL,
  PRIMARY KEY  (`chat_id`),
  KEY `team_id` (`team_id`),
  KEY `chat_room_id` (`chat_room_id`)
);
    
DROP TABLE IF EXISTS `chat_room`;
CREATE TABLE `chat_room` (
  `chat_room_id` int(11) NOT NULL auto_increment,
  `team_1_id` int(11) default NULL,
  `team_2_id` int(11) default NULL,
  `team_1_arrived` char(1) default NULL,
  `team_2_arrived` char(1) default NULL,
  `chat_room_ping` datetime default NULL,
  PRIMARY KEY  (`chat_room_id`)
);
    
DROP TABLE IF EXISTS `last_update`;
CREATE TABLE IF NOT EXISTS `last_update` (
  `latest_message` int(11) NOT NULL default '1',
  `time` timestamp,
  PRIMARY KEY (`latest_message`)
);
  
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (11,0);
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (12,1);
INSERT INTO `last_update` (`latest_message`) VALUES (1);
ALTER TABLE `chat` ADD `team_owner` TEXT;

INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (13,'localhost');
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (14,25);
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (15,'user');
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (16,'password');
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (17,0);
