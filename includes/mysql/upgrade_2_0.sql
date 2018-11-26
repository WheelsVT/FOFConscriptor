DROP TABLE IF EXISTS `roster`;
CREATE TABLE IF NOT EXISTS `roster` (
    `player_id` int(11) default NULL,
    `position_id` int(11) default NULL,
    `in_game_team_id` int(11) default NULL,
    `team_id` int(11) default NULL,
    `current` int(11) default '0',
    `future` int(11) default '0',
    KEY `player_id` (`player_id`),
    KEY `team_id` (`team_id`)
  );


DROP TABLE IF EXISTS `player`;
CREATE TABLE `player` (
  `player_id` int(11) NOT NULL auto_increment,
  `player_in_game_id` int(11) default NULL,
  `player_name` varchar(255) default NULL,
  `player_score` float(5,2) default NULL,
  `player_adj_score` float(5,2) default NULL,
  `position_id` int(11) default NULL,
  `player_school` varchar(255) default NULL,
  `player_dob` date default NULL,
  `player_hometown` varchar(255) default NULL,
  `player_agent` varchar(255) default NULL,
  `player_designation` varchar(255) default NULL,
  `player_height` smallint(6) default NULL,
  `player_weight` smallint(6) default NULL,
  `player_experience` int(11) default NULL,
  `player_jersey` int(11) default NULL,
  `player_loyalty` int(11) default NULL,
  `player_winner` int(11) default NULL,
  `player_leader` int(11) default NULL,
  `player_intelligence` int(11) default NULL,
  `player_personality` int(11) default NULL,
  `player_popularity` int(11) default NULL,
  `player_mentor_to` varchar(255) default NULL,
  `player_interviewed` varchar(255) default NULL,
  `player_impression` varchar(255) default NULL,
  `player_current` int(11) default NULL,
  `player_future` int(11) default NULL,
  `player_conflicts` varchar(255) default NULL,
  `player_affinities` varchar(255) default NULL,
  `player_character` varchar(255) default NULL,
  `player_vol` smallint(6) default NULL,
  `player_solec` smallint(6) default NULL,
  `player_40` float(5,2) default NULL,
  `player_bench` smallint(6) default NULL,
  `player_agil` float(5,2) default NULL,
  `player_broad` smallint(6) default NULL,
  `player_pos_drill` smallint(6) default NULL,
  `player_developed` smallint(6) default NULL,
  PRIMARY KEY  (`player_id`),
  KEY `position_id` (`position_id`)
);

DROP TABLE IF EXISTS `player_temp`;
CREATE TABLE `player_temp` (
  `player_id` int(11) default NULL,
  `player_in_game_id` int(11) default NULL,
  `player_name` varchar(255) default NULL,
  `player_score` float(5,2) default NULL,
  `player_adj_score` float(5,2) default NULL,
  `position_id` int(11) default NULL,
  `player_school` varchar(255) default NULL,
  `player_dob` date default NULL,
  `player_hometown` varchar(255) default NULL,
  `player_agent` varchar(255) default NULL,
  `player_designation` varchar(255) default NULL,
  `player_height` smallint(6) default NULL,
  `player_weight` smallint(6) default NULL,
  `player_experience` int(11) default NULL,
  `player_jersey` int(11) default NULL,
  `player_loyalty` int(11) default NULL,
  `player_winner` int(11) default NULL,
  `player_leader` int(11) default NULL,
  `player_intelligence` int(11) default NULL,
  `player_personality` int(11) default NULL,
  `player_popularity` int(11) default NULL,
  `player_mentor_to` varchar(255) default NULL,
  `player_interviewed` varchar(255) default NULL,
  `player_impression` varchar(255) default NULL,
  `player_current` int(11) default NULL,
  `player_future` int(11) default NULL,
  `player_conflicts` varchar(255) default NULL,
  `player_affinities` varchar(255) default NULL,
  `player_character` varchar(255) default NULL,
  `player_vol` smallint(6) default NULL,
  `player_solec` smallint(6) default NULL,
  `player_40` float(5,2) default NULL,
  `player_bench` smallint(6) default NULL,
  `player_agil` float(5,2) default NULL,
  `player_broad` smallint(6) default NULL,
  `player_pos_drill` smallint(6) default NULL,
  `player_developed` smallint(6) default NULL,
  KEY `position_id` (`position_id`)
);

INSERT INTO `position_to_alias` (`position_id`,`alias_name`) VALUES (17,'LS');
INSERT INTO `position` (`position_id`, `position_name`, `position_scout_weight`) VALUES
(17, 'LS', 70);
INSERT INTO `position_to_attribute` VALUES (17, 36, 1);
INSERT INTO `combine_ratings` VALUES (17, 19, 30, 21, 29, 5.39, 5.23, 8.17, 7.91, 89, 99, NULL, NULL);
INSERT INTO `height_weight` VALUES (17, 170, 197, 203, 211, 219, 236, 1000, 68, 68, 71, 72, 74, 100);

DROP TABLE IF EXISTS `staff_roles`;
CREATE TABLE IF NOT EXISTS `staff_roles` (
   `staff_role_id` int(11) NOT NULL auto_increment,
   `staff_role_name` varchar(255) default NULL,
   KEY `staff_role_id` (`staff_role_id`)
);
INSERT INTO `staff_roles` (`staff_role_id`,`staff_role_name`) VALUES
   (1,'Head Coach'),
   (2,'Offensive Coordinator'),
   (3,'Defensive Coordinator'),
   (4,'Assistant Coach'),
   (5,'Strength Coordinator'),
   (99,'None');

DROP TABLE IF EXISTS `staff_pri_group`;
CREATE TABLE IF NOT EXISTS `staff_pri_group` (
   `staff_pri_group_id` int(11) NOT NULL auto_increment,
   `staff_pri_group_name` varchar(255) default NULL,
   KEY `staff_pri_group_id` (`staff_pri_group_id`)
);
INSERT INTO `staff_pri_group` (`staff_pri_group_id`,`staff_pri_group_name`) VALUES
   (1,'Quarterbacks'),
   (2,'Running Backs'),
   (3,'Tight Ends'),
   (4,'Wide Receivers'),
   (5,'Offensive Linemen'),
   (6,'Defensive Linemen'),
   (7,'Linebackers'),
   (8,'Secondary'),
   (9,'Strength'),
   (99,'None');


DROP TABLE IF EXISTS `staff_trans_history`;
CREATE TABLE IF NOT EXISTS `staff_trans_history` (
   `input_file_number` int(11) default NULL,
   `staff_id` int(11) default NULL,
   `staff_trans_id` int(11) default NULL,
   `staff_team_id` int(11) default NULL,
   KEY `staff_id` (`staff_id`)
);
DROP TABLE IF EXISTS `staff_trans_types`;
CREATE TABLE IF NOT EXISTS `staff_trans_types` (
   `staff_trans_id` int(11) default NULL,
   `staff_trans_name` varchar(255) default NULL,
   KEY `staff_trans_id` (`staff_trans_id`)
);
INSERT INTO `staff_trans_types` (`staff_trans_id`,`staff_trans_name`) VALUES
   (1,'hired as head coach'),
   (2,'fired as head coach'),
   (3,'hired as offensive coordinator'),
   (4,'fired as offensive coordinator'),
   (5,'hired as defensive coordinator'),
   (6,'fired as defensive coordinator'),
   (7,'hired as assistant coach'),
   (8,'fired as assistant coach'),
   (9,'hired as strength coordinator'),
   (10,'fired as strength coordinator');

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `staff_id` int(11) NOT NULL auto_increment,
  `staff_in_game_id` int(11) default NULL,
  `staff_name` varchar(255) default NULL,
  `staff_curr_team_id` int(11) default NULL,
  `staff_role_id` int(11) NOT NULL default '99',
  `staff_pri_group_id` int(11) NOT NULL default '99',
  `staff_salary` int(11) default NULL,
  `staff_player_dev` int(11) default NULL,
  `staff_young_player_dev` int(11) default NULL,
  `staff_motivation` int(11) default NULL,
  `staff_discipline` int(11) default NULL,
  `staff_play_calling` int(11) default NULL,
  `staff_str_training` int(11) default NULL,
  `staff_conditioning` int(11) default NULL,
  `staff_intelligence` int(11) default NULL,
  `staff_scouting` int(11) default NULL,
  `staff_interviewing` int(11) default NULL,
  `staff_age` int(11) default NULL,
  `staff_retired` int(11) NOT NULL default '0',
  `staff_yrs_on_contract` int(11) default NULL,
  `staff_suitable_hc` int(11) default NULL,
  `staff_suitable_oc` int(11) default NULL,
  `staff_suitable_dc` int(11) default NULL,
  `staff_suitable_ac` int(11) default NULL,
  `staff_suitable_sc` int(11) default NULL,
  `staff_amenable` varchar(3) default 'Y',
  `staff_team_draft_order` int(11) default NULL,
  `staff_recent_hire` int(11) default '0',
  `drafted` int(11) default '0',
  PRIMARY KEY (`staff_id`)
);

ALTER TABLE `team` ADD `in_game_id` INT(11) DEFAULT '0';

INSERT INTO `column` (`column_id`, `column_query`, `column_style`, `column_header`, `column_exec`, `column_date_format`, `column_number_format`, `column_order`) VALUES
(31, 'team.team_name', NULL, 'Curr Team', NULL, NULL, NULL, 285),
(32, 'staff_roles.staff_role_name', NULL, 'Role', NULL, NULL, NULL, 286),
(33, 'staff_pri_group.staff_pri_group_name', NULL, 'Pri Group', NULL, NULL, NULL, 288),
(34, 'staff.staff_salary', NULL, 'Salary', NULL, NULL, NULL, 290),
(35, 'staff.staff_player_dev', NULL, 'Player Dev', NULL, NULL, NULL, 300),
(36, 'staff.staff_young_player_dev', NULL, 'Young Player Dev', NULL, NULL, NULL, 310),
(37, 'staff.staff_motivation', NULL, 'Motivation', NULL, NULL, NULL, 320),
(38, 'staff.staff_discipline', NULL, 'Discipline', NULL, NULL, NULL, 330),
(39, 'staff.staff_play_calling', NULL, 'Play Calling', NULL, NULL, NULL, 340),
(40, 'staff.staff_str_training', NULL, 'Str Training', NULL, NULL, NULL, 350),
(41, 'staff.staff_conditioning', NULL, 'Conditioning', NULL, NULL, NULL, 360),
(42, 'staff.staff_intelligence', NULL, 'Intelligence', NULL, NULL, NULL, 370),
(43, 'staff.staff_scouting', NULL, 'Scouting', NULL, NULL, NULL, 380),
(44, 'staff.staff_interviewing', NULL, 'Interviewing', NULL, NULL, NULL, 390),
(45, 'staff.staff_age', NULL, 'Age', NULL, NULL, NULL, 400),
(46, 'staff.staff_retired', NULL, 'Retired', NULL, NULL, NULL, 410),
(47, 'staff.staff_yrs_on_contract', NULL, 'Yrs On Contract', NULL, NULL, NULL, 287),
(48, 'staff.staff_suitable_hc', 'if (calculate_round==1,\r\n"color: #0b0; font-weight: bold",\r\n\r\n, NULL)\r\n\r\n)', 'Suitable HC', NULL, NULL, NULL, 430),
(49, 'staff.staff_suitable_oc', 'if (calculate_round==2,\r\n"color: #0b0; font-weight: bold",\r\n\r\n, NULL)\r\n\r\n)', 'Suitable OC', NULL, NULL, NULL, 440),
(50, 'staff.staff_suitable_dc', NULL, 'Suitable DC', NULL, NULL, NULL, 450),
(51, 'staff.staff_suitable_ac', NULL, 'Suitable AC', NULL, NULL, NULL, 460),
(52, 'staff.staff_suitable_sc', NULL, 'Suitable SC', NULL, NULL, NULL, 470),
(53, 'staff.staff_amenable', NULL, 'Amenable', NULL, NULL, NULL, 480);

INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (20,0);

DROP TABLE IF EXISTS `fof7_attribute_map`;
CREATE TABLE IF NOT EXISTS `fof7_attribute_map` (`attribute_id` int(11),`fof7_attribute_column` int(11),KEY `attribute_id` (`attribute_id`));
INSERT INTO `fof7_attribute_map` (`attribute_id`, `fof7_attribute_column` ) VALUES (1,-1),(2,3),(3,4),(4,5),(5,6),(6,7),(7,8),(13,9),(8,11),(9,12),(10,13),(11,14),(12,15),(15,21),(16,22),(17,23),(18,24),(19,25),(20,26),(21,27),(22,28),(23,29),(24,30),(25,31),(33,32),(34,33),(35,34),(26,35),(27,36),(30,38),(31,39),(32,40),(37,41),(38,42),(39,43),(42,44),(43,45),(40,46),(41,47),(44,48),(45,49),(47,50),(48,51),(49,52),(46,53),(50,54),(51,55),(52,56),(28,57),(29,58),(36,59),(14,60);