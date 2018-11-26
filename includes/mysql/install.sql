DROP TABLE IF EXISTS `last_update`;
CREATE TABLE IF NOT EXISTS `last_update` (
  `latest_message` int(11) NOT NULL DEFAULT '1',
  `time` timestamp,
  PRIMARY KEY (`latest_message`)
);

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

DROP TABLE IF EXISTS `pick`;
CREATE TABLE IF NOT EXISTS `pick` (
  `pick_id` int(11) NOT NULL default '0',
  `team_id` int(11) default NULL,
 `player_id` int(11) default NULL,
  `pick_time` datetime default NULL,
  `pick_start` datetime default NULL,
  `pick_expired` int(1) default NULL,
  PRIMARY KEY  (`pick_id`),
  KEY `team_id` (`team_id`),
  KEY `player_id` (`player_id`)
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
  KEY `position_id` (`position_id`),
  KEY `player_in_game_id` (`player_in_game_id`)
);

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
   `staff_trans_year` int(11) default NULL,
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
  `fired` int(11) default '0',
  PRIMARY KEY (`staff_id`)
);

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
(16, 'S', 100),
(17, 'LS', 50);

DROP TABLE IF EXISTS `position_to_alias`;
CREATE TABLE IF NOT EXISTS `position_to_alias` (
  `position_id` int(11) NOT NULL,
  `alias_name` varchar(10) NOT NULL,
  PRIMARY KEY  (`position_id`,`alias_name`)
);

INSERT INTO `position_to_alias` VALUES (1, 'QB');
INSERT INTO `position_to_alias` VALUES (2, 'RB');
INSERT INTO `position_to_alias` VALUES (3, 'FB');
INSERT INTO `position_to_alias` VALUES (4, 'TE');
INSERT INTO `position_to_alias` VALUES (5, 'FL');
INSERT INTO `position_to_alias` VALUES (5, 'SE');
INSERT INTO `position_to_alias` VALUES (6, 'C');
INSERT INTO `position_to_alias` VALUES (7, 'LG');
INSERT INTO `position_to_alias` VALUES (7, 'RG');
INSERT INTO `position_to_alias` VALUES (8, 'LT');
INSERT INTO `position_to_alias` VALUES (8, 'RT');
INSERT INTO `position_to_alias` VALUES (9, 'P');
INSERT INTO `position_to_alias` VALUES (10, 'K');
INSERT INTO `position_to_alias` VALUES (11, 'LDE');
INSERT INTO `position_to_alias` VALUES (11, 'RDE');
INSERT INTO `position_to_alias` VALUES (12, 'LDT');
INSERT INTO `position_to_alias` VALUES (12, 'NT');
INSERT INTO `position_to_alias` VALUES (12, 'RDT');
INSERT INTO `position_to_alias` VALUES (13, 'MLB');
INSERT INTO `position_to_alias` VALUES (13, 'SILB');
INSERT INTO `position_to_alias` VALUES (13, 'WILB');
INSERT INTO `position_to_alias` VALUES (14, 'SLB');
INSERT INTO `position_to_alias` VALUES (14, 'WLB');
INSERT INTO `position_to_alias` VALUES (15, 'LCB');
INSERT INTO `position_to_alias` VALUES (15, 'RCB');
INSERT INTO `position_to_alias` VALUES (16, 'FS');
INSERT INTO `position_to_alias` VALUES (16, 'SS');
INSERT INTO `position_to_alias` VALUES (17,'LS');

DROP TABLE IF EXISTS `selection`;
CREATE TABLE IF NOT EXISTS `selection` (
  `team_id` int(11) NOT NULL default '0',
  `player_id` int(11) NOT NULL default '0',
  `selection_priority` int(11) NOT NULL default '0',
  PRIMARY KEY  (`team_id`,`player_id`)
);

DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `team_id` int(11) NOT NULL AUTO_INCREMENT,
  `team_name` varchar(255) DEFAULT NULL,
  `team_owner` text,
  `team_comments` text,
  `team_password` varchar(255) DEFAULT NULL,
  `team_email` varchar(255) DEFAULT NULL,
  `team_multipos` char(1) NOT NULL DEFAULT '0',
  `team_autopick` char(1) NOT NULL DEFAULT '1',
  `team_autopick_wait` int(11) DEFAULT '30',
  `team_clock_adj` float(3,2) NOT NULL DEFAULT '1.00',
  `pick_method_id` int(11) NOT NULL DEFAULT '3',
  `team_chat_time` datetime NOT NULL,
  `team_email_prefs` tinyint(4) NOT NULL DEFAULT '1',
  `team_phone` text,
  `team_carrier` text,
  `team_sms_setting` int(11) DEFAULT '0',
  `team_user_link` text,
  `draft_admin` int(11) default '0',
  `in_game_id` int(11) default NULL,
  PRIMARY KEY (`team_id`),
  KEY `pick_method_id` (`pick_method_id`),
  KEY `team_chat_time` (`team_chat_time`)
);

DROP TABLE IF EXISTS `bpa`;
CREATE TABLE `bpa` (
  `bpa_id` int(11) NOT NULL auto_increment,
  `team_id` int(11) default NULL,
  `position_id` int(11) default NULL,
  `bpa_priority` int(11) default NULL,
  `bpa_max_experience` int(11) default NULL,
  `attribute_id` int(11) default NULL,
  PRIMARY KEY  (`bpa_id`),
  KEY `team_id` (`team_id`),
  KEY `position_id` (`position_id`),
  KEY `bpa_priority` (`bpa_priority`),
  KEY `bpamethod_id` (`attribute_id`)
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
(3, 'Scout Pick'),
(4, 'Player Queue then Best At Position');

DROP TABLE IF EXISTS `chat`;
CREATE TABLE `chat` (
  `chat_id` int(11) NOT NULL auto_increment,
  `team_id` int(11) NOT NULL,
  `chat_time` datetime NOT NULL,
  `chat_message` text NOT NULL,
  `chat_room_id` int(11) default NULL,
  `team_owner` text,
  PRIMARY KEY  (`chat_id`),
  KEY `team_id` (`team_id`),
  KEY `chat_room_id` (`chat_room_id`)
);
    
DROP TABLE IF EXISTS `player_comments`;
CREATE TABLE `player_comments` (
  `player_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `player_comments_text` text,
  PRIMARY KEY  (`player_id`,`team_id`)
);

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `setting_value` varchar(4096) NOT NULL,
  PRIMARY KEY  (`setting_id`)
);


DROP TABLE IF EXISTS `column`;
CREATE TABLE IF NOT EXISTS `column` (
  `column_id` int(11) NOT NULL auto_increment,
  `column_query` text,
  `column_style` text,
  `column_header` varchar(255) default NULL,
  `column_exec` varchar(255) default NULL,
  `column_date_format` varchar(255) default NULL,
  `column_number_format` int(11) default NULL,
  `column_order` int(11) default NULL,
  PRIMARY KEY  (`column_id`)
);

INSERT INTO `column` (`column_id`, `column_query`, `column_style`, `column_header`, `column_exec`, `column_date_format`, `column_number_format`, `column_order`) VALUES
(1, 'position.position_name', NULL, 'Pos', NULL, NULL, NULL, 10),
(2, 'player.player_school', NULL, 'School', NULL, NULL, NULL, 20),
(3, 'player.player_height', NULL, 'Height', 'height_convert', NULL, NULL, 30),
(4, 'player.player_weight', NULL, 'Weight', NULL, NULL, NULL, 40),
(5, 'player.player_vol', NULL, 'Volatility', NULL, NULL, NULL, 50),
(6, 'player.player_solec', 'if (player.player_solec <= combine_ratings.combine_low_sole,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_solec >= combine_ratings.combine_high_sole,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)', 'Solecismic', NULL, NULL, NULL, 60),
(7, 'player.player_40', 'if (player.player_40 >= combine_ratings.combine_low_40,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_40 <= combine_ratings.combine_high_40,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)', '40-time', NULL, NULL, NULL, 70),
(8, 'player.player_bench', 'if (player.player_bench <= combine_ratings.combine_low_strength,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_bench >= combine_ratings.combine_high_strength,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)', 'Bench Press Reps', NULL, NULL, NULL, 80),
(9, 'player.player_agil', 'if (player.player_agil >= combine_ratings.combine_low_agil,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_agil <= combine_ratings.combine_high_agil,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)', 'Agility', NULL, NULL, NULL, 90),
(10, 'player.player_broad', 'if (player.player_broad <= combine_ratings.combine_low_broad,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_broad >= combine_ratings.combine_high_broad,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)', 'Broad Jump', 'height_convert', NULL, NULL, 100),
(11, 'player.player_pos_drill', 'if (combine_ratings.combine_high_pos is not null, \r\n\r\nif (player.player_pos_drill <= combine_ratings.combine_low_pos,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_pos_drill >= combine_ratings.combine_high_pos,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)\r\n\r\n, \r\n\r\nNULL)', 'Position Drill', NULL, NULL, NULL, 110),
(12, 'player.player_developed', NULL, 'Developed', NULL, NULL, NULL, 120),
(13, 'player.player_dob', NULL, 'Birthdate', NULL, 'n/j/Y', NULL, 130),
(14, 'player.player_hometown', NULL, 'Hometown', NULL, NULL, NULL, 140),
(15, 'player.player_agent', NULL, 'Agent', NULL, NULL, NULL, 150),
(16, 'player.player_experience', NULL, 'Exp', NULL, NULL, NULL, 160),
(17, 'team_player.player_loyalty', NULL, 'Loyalty', NULL, NULL, NULL, 170),
(18, 'team_player.player_winner', NULL, 'Winner', NULL, NULL, NULL, 180),
(19, 'team_player.player_leader', NULL, 'Leader', NULL, NULL, NULL, 190),
(20, 'team_player.player_intelligence', NULL, 'Intelligence', NULL, NULL, NULL, 200),
(21, 'team_player.player_personality', NULL, 'Personality', NULL, NULL, NULL, 210),
(22, 'team_player.player_popularity', NULL, 'Popularity', NULL, NULL, NULL, 220),
(23, 'team_player.player_interviewed', NULL, 'Interviewed', NULL, NULL, NULL, 230),
(24, 'team_player.player_impression', NULL, 'Impression', NULL, NULL, NULL, 240),
(25, 'if (team_player.player_current is NULL, player.player_current, team_player.player_current)', NULL, 'Current', NULL, NULL, NULL, 250),
(26, 'if (team_player.player_future is NULL, player.player_future, team_player.player_future)', NULL, 'Future', NULL, NULL, NULL, 260),
(27, 'team_player.player_conflicts', NULL, 'Conflicts', NULL, NULL, NULL, 270),
(28, 'team_player.player_affinities', NULL, 'Affinities', NULL, NULL, NULL, 280),
(29, 'player.player_score', NULL, 'Grade', NULL, NULL, NULL, 41),
(30, 'player.player_adj_score', NULL, 'Adjusted Grade', NULL, NULL, NULL, 42),
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
(48, 'staff.staff_suitable_hc', NULL, 'Suitable HC', NULL, NULL, NULL, 430),
(49, 'staff.staff_suitable_oc', NULL, 'Suitable OC', NULL, NULL, NULL, 440),
(50, 'staff.staff_suitable_dc', NULL, 'Suitable DC', NULL, NULL, NULL, 450),
(51, 'staff.staff_suitable_ac', NULL, 'Suitable AC', NULL, NULL, NULL, 460),
(52, 'staff.staff_suitable_sc', NULL, 'Suitable SC', NULL, NULL, NULL, 470),
(53, 'staff.staff_amenable', NULL, 'Amenable', NULL, NULL, NULL, 480);

DROP TABLE IF EXISTS `team_to_column`;
CREATE TABLE `team_to_column` (
  `team_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `team_to_column_order` int(11) default NULL,
  PRIMARY KEY  (`team_id`,`column_id`)
);

DROP TABLE IF EXISTS `attribute`;
CREATE TABLE `attribute` (
  `attribute_id` int(11) NOT NULL auto_increment,
  `attribute_name` varchar(255) default NULL,
  `attribute_abb` varchar(2) default NULL,
  PRIMARY KEY  (`attribute_id`)
);

INSERT INTO `attribute` VALUES (4, 'Medium Passes', 'ME');
INSERT INTO `attribute` VALUES (3, 'Short Passes', 'SH');
INSERT INTO `attribute` VALUES (2, 'Screen Passes', 'SC');
INSERT INTO `attribute` VALUES (1, 'Formations', 'F');
INSERT INTO `attribute` VALUES (5, 'Long Passes', 'LG');
INSERT INTO `attribute` VALUES (6, 'Deep Passes', 'DE');
INSERT INTO `attribute` VALUES (7, 'Third Down Passing', '3D');
INSERT INTO `attribute` VALUES (8, 'Accuracy', 'AC');
INSERT INTO `attribute` VALUES (9, 'Timing', 'TI');
INSERT INTO `attribute` VALUES (10, 'Sense Rush', 'SR');
INSERT INTO `attribute` VALUES (11, 'Read Defense', 'RD');
INSERT INTO `attribute` VALUES (12, 'Two Minute Offense', '2M');
INSERT INTO `attribute` VALUES (13, 'Scramble Frequency', 'SF');
INSERT INTO `attribute` VALUES (14, 'Kick Holding', 'KH');
INSERT INTO `attribute` VALUES (15, 'Breakaway Speed', 'BR');
INSERT INTO `attribute` VALUES (16, 'Power Inside', 'PI');
INSERT INTO `attribute` VALUES (17, 'Third Down Running', '3D');
INSERT INTO `attribute` VALUES (18, 'Hole Recognition', 'HR');
INSERT INTO `attribute` VALUES (19, 'Elusiveness', 'EL');
INSERT INTO `attribute` VALUES (20, 'Speed to Outside', 'SO');
INSERT INTO `attribute` VALUES (21, 'Blitz Pickup', 'BP');
INSERT INTO `attribute` VALUES (22, 'Avoid Drops', 'AD');
INSERT INTO `attribute` VALUES (23, 'Getting Downfield', 'GD');
INSERT INTO `attribute` VALUES (24, 'Route Running', 'RR');
INSERT INTO `attribute` VALUES (25, 'Third Down Catching', '3C');
INSERT INTO `attribute` VALUES (26, 'Punt Returning', 'PR');
INSERT INTO `attribute` VALUES (27, 'Kick Returning', 'KR');
INSERT INTO `attribute` VALUES (28, 'Endurance', 'EN');
INSERT INTO `attribute` VALUES (29, 'Special Teams', 'ST');
INSERT INTO `attribute` VALUES (30, 'Run Blocking', 'RB');
INSERT INTO `attribute` VALUES (31, 'Pass Blocking', 'PB');
INSERT INTO `attribute` VALUES (32, 'Blocking Strength', 'BS');
INSERT INTO `attribute` VALUES (33, 'Big-Play Receiving', 'BP');
INSERT INTO `attribute` VALUES (34, 'Courage', 'CO');
INSERT INTO `attribute` VALUES (35, 'Adjust to Ball', 'AB');
INSERT INTO `attribute` VALUES (36, 'Long Snapping', 'LS');
INSERT INTO `attribute` VALUES (37, 'Kicking Power', 'PP');
INSERT INTO `attribute` VALUES (38, 'Hang Time', 'HT');
INSERT INTO `attribute` VALUES (39, 'Directional Punting', 'DP');
INSERT INTO `attribute` VALUES (40, 'Kicking Accuracy', 'KA');
INSERT INTO `attribute` VALUES (41, 'Kicking Power', 'KP');
INSERT INTO `attribute` VALUES (42, 'Kickoff Distance', 'KD');
INSERT INTO `attribute` VALUES (43, 'Kickoff Hang Time', 'KT');
INSERT INTO `attribute` VALUES (44, 'Run Defense', 'RD');
INSERT INTO `attribute` VALUES (45, 'Pass Rush Technique', 'PT');
INSERT INTO `attribute` VALUES (46, 'Pass Rush Strength', 'PS');
INSERT INTO `attribute` VALUES (47, 'Man-to-Man Defense', 'MM');
INSERT INTO `attribute` VALUES (48, 'Zone Defense', 'ZN');
INSERT INTO `attribute` VALUES (49, 'Bump-and-Run Defense', 'BR');
INSERT INTO `attribute` VALUES (50, 'Play Diagnosis', 'PD');
INSERT INTO `attribute` VALUES (51, 'Punishing Hitter', 'PH');
INSERT INTO `attribute` VALUES (52, 'Interceptions', 'IN');


DROP TABLE IF EXISTS `player_to_attribute`;
CREATE TABLE `player_to_attribute` (
  `player_id` int(11) NOT NULL default '0',
  `attribute_id` int(11) NOT NULL default '0',
  `player_to_attribute_low` int(11) default NULL,
  `player_to_attribute_high` int(11) default NULL,
  PRIMARY KEY  (`player_id`,`attribute_id`)
);

DROP TABLE IF EXISTS `position_to_attribute`;
CREATE TABLE `position_to_attribute` (
  `position_id` int(11) NOT NULL default '0',
  `attribute_id` int(11) NOT NULL default '0',
  `position_to_attribute_order` int(11) default NULL,
  PRIMARY KEY  (`position_id`,`attribute_id`)
);

INSERT INTO `position_to_attribute` VALUES (1, 1, 1);
INSERT INTO `position_to_attribute` VALUES (1, 2, 2);
INSERT INTO `position_to_attribute` VALUES (1, 3, 3);
INSERT INTO `position_to_attribute` VALUES (1, 4, 4);
INSERT INTO `position_to_attribute` VALUES (1, 5, 5);
INSERT INTO `position_to_attribute` VALUES (1, 6, 6);
INSERT INTO `position_to_attribute` VALUES (1, 7, 7);
INSERT INTO `position_to_attribute` VALUES (1, 8, 8);
INSERT INTO `position_to_attribute` VALUES (1, 9, 9);
INSERT INTO `position_to_attribute` VALUES (1, 10, 10);
INSERT INTO `position_to_attribute` VALUES (1, 11, 11);
INSERT INTO `position_to_attribute` VALUES (1, 12, 12);
INSERT INTO `position_to_attribute` VALUES (1, 13, 13);
INSERT INTO `position_to_attribute` VALUES (1, 14, 14);
INSERT INTO `position_to_attribute` VALUES (2, 15, 1);
INSERT INTO `position_to_attribute` VALUES (2, 16, 2);
INSERT INTO `position_to_attribute` VALUES (2, 17, 3);
INSERT INTO `position_to_attribute` VALUES (2, 18, 4);
INSERT INTO `position_to_attribute` VALUES (2, 19, 5);
INSERT INTO `position_to_attribute` VALUES (2, 20, 6);
INSERT INTO `position_to_attribute` VALUES (2, 21, 7);
INSERT INTO `position_to_attribute` VALUES (2, 22, 8);
INSERT INTO `position_to_attribute` VALUES (2, 23, 9);
INSERT INTO `position_to_attribute` VALUES (2, 24, 10);
INSERT INTO `position_to_attribute` VALUES (2, 25, 11);
INSERT INTO `position_to_attribute` VALUES (2, 26, 12);
INSERT INTO `position_to_attribute` VALUES (2, 27, 13);
INSERT INTO `position_to_attribute` VALUES (2, 28, 14);
INSERT INTO `position_to_attribute` VALUES (2, 29, 15);
INSERT INTO `position_to_attribute` VALUES (3, 30, 1);
INSERT INTO `position_to_attribute` VALUES (3, 31, 2);
INSERT INTO `position_to_attribute` VALUES (3, 32, 3);
INSERT INTO `position_to_attribute` VALUES (3, 16, 4);
INSERT INTO `position_to_attribute` VALUES (3, 17, 5);
INSERT INTO `position_to_attribute` VALUES (3, 18, 6);
INSERT INTO `position_to_attribute` VALUES (3, 21, 7);
INSERT INTO `position_to_attribute` VALUES (3, 22, 8);
INSERT INTO `position_to_attribute` VALUES (3, 24, 9);
INSERT INTO `position_to_attribute` VALUES (3, 25, 10);
INSERT INTO `position_to_attribute` VALUES (3, 28, 11);
INSERT INTO `position_to_attribute` VALUES (3, 29, 12);
INSERT INTO `position_to_attribute` VALUES (4, 30, 1);
INSERT INTO `position_to_attribute` VALUES (4, 31, 2);
INSERT INTO `position_to_attribute` VALUES (4, 32, 3);
INSERT INTO `position_to_attribute` VALUES (4, 22, 4);
INSERT INTO `position_to_attribute` VALUES (4, 23, 5);
INSERT INTO `position_to_attribute` VALUES (4, 24, 6);
INSERT INTO `position_to_attribute` VALUES (4, 25, 7);
INSERT INTO `position_to_attribute` VALUES (4, 33, 8);
INSERT INTO `position_to_attribute` VALUES (4, 34, 9);
INSERT INTO `position_to_attribute` VALUES (4, 35, 10);
INSERT INTO `position_to_attribute` VALUES (4, 36, 13);
INSERT INTO `position_to_attribute` VALUES (5, 22, 1);
INSERT INTO `position_to_attribute` VALUES (5, 23, 2);
INSERT INTO `position_to_attribute` VALUES (5, 24, 3);
INSERT INTO `position_to_attribute` VALUES (5, 25, 4);
INSERT INTO `position_to_attribute` VALUES (5, 33, 5);
INSERT INTO `position_to_attribute` VALUES (5, 34, 6);
INSERT INTO `position_to_attribute` VALUES (5, 35, 7);
INSERT INTO `position_to_attribute` VALUES (5, 26, 8);
INSERT INTO `position_to_attribute` VALUES (5, 27, 9);
INSERT INTO `position_to_attribute` VALUES (5, 28, 10);
INSERT INTO `position_to_attribute` VALUES (5, 29, 11);
INSERT INTO `position_to_attribute` VALUES (6, 30, 1);
INSERT INTO `position_to_attribute` VALUES (6, 31, 2);
INSERT INTO `position_to_attribute` VALUES (6, 32, 3);
INSERT INTO `position_to_attribute` VALUES (6, 28, 4);
INSERT INTO `position_to_attribute` VALUES (6, 36, 5);
INSERT INTO `position_to_attribute` VALUES (7, 30, 1);
INSERT INTO `position_to_attribute` VALUES (7, 31, 2);
INSERT INTO `position_to_attribute` VALUES (7, 32, 3);
INSERT INTO `position_to_attribute` VALUES (7, 28, 4);
INSERT INTO `position_to_attribute` VALUES (8, 30, 1);
INSERT INTO `position_to_attribute` VALUES (8, 31, 2);
INSERT INTO `position_to_attribute` VALUES (8, 32, 3);
INSERT INTO `position_to_attribute` VALUES (8, 28, 4);
INSERT INTO `position_to_attribute` VALUES (9, 37, 1);
INSERT INTO `position_to_attribute` VALUES (9, 38, 2);
INSERT INTO `position_to_attribute` VALUES (9, 39, 3);
INSERT INTO `position_to_attribute` VALUES (9, 14, 4);
INSERT INTO `position_to_attribute` VALUES (10, 40, 1);
INSERT INTO `position_to_attribute` VALUES (10, 41, 2);
INSERT INTO `position_to_attribute` VALUES (10, 42, 3);
INSERT INTO `position_to_attribute` VALUES (10, 43, 4);
INSERT INTO `position_to_attribute` VALUES (11, 44, 1);
INSERT INTO `position_to_attribute` VALUES (11, 45, 2);
INSERT INTO `position_to_attribute` VALUES (11, 46, 3);
INSERT INTO `position_to_attribute` VALUES (11, 50, 4);
INSERT INTO `position_to_attribute` VALUES (11, 51, 5);
INSERT INTO `position_to_attribute` VALUES (11, 28, 6);
INSERT INTO `position_to_attribute` VALUES (12, 44, 1);
INSERT INTO `position_to_attribute` VALUES (12, 45, 2);
INSERT INTO `position_to_attribute` VALUES (12, 46, 3);
INSERT INTO `position_to_attribute` VALUES (12, 50, 4);
INSERT INTO `position_to_attribute` VALUES (12, 51, 5);
INSERT INTO `position_to_attribute` VALUES (12, 28, 6);
INSERT INTO `position_to_attribute` VALUES (13, 44, 1);
INSERT INTO `position_to_attribute` VALUES (13, 45, 2);
INSERT INTO `position_to_attribute` VALUES (13, 47, 3);
INSERT INTO `position_to_attribute` VALUES (13, 48, 4);
INSERT INTO `position_to_attribute` VALUES (13, 49, 5);
INSERT INTO `position_to_attribute` VALUES (13, 46, 6);
INSERT INTO `position_to_attribute` VALUES (13, 50, 7);
INSERT INTO `position_to_attribute` VALUES (13, 51, 8);
INSERT INTO `position_to_attribute` VALUES (13, 28, 9);
INSERT INTO `position_to_attribute` VALUES (13, 29, 10);
INSERT INTO `position_to_attribute` VALUES (14, 44, 1);
INSERT INTO `position_to_attribute` VALUES (14, 45, 2);
INSERT INTO `position_to_attribute` VALUES (14, 47, 3);
INSERT INTO `position_to_attribute` VALUES (14, 48, 4);
INSERT INTO `position_to_attribute` VALUES (14, 49, 5);
INSERT INTO `position_to_attribute` VALUES (14, 46, 6);
INSERT INTO `position_to_attribute` VALUES (14, 50, 7);
INSERT INTO `position_to_attribute` VALUES (14, 51, 8);
INSERT INTO `position_to_attribute` VALUES (14, 28, 9);
INSERT INTO `position_to_attribute` VALUES (14, 29, 10);
INSERT INTO `position_to_attribute` VALUES (15, 44, 1);
INSERT INTO `position_to_attribute` VALUES (15, 47, 2);
INSERT INTO `position_to_attribute` VALUES (15, 48, 3);
INSERT INTO `position_to_attribute` VALUES (15, 49, 4);
INSERT INTO `position_to_attribute` VALUES (15, 50, 5);
INSERT INTO `position_to_attribute` VALUES (15, 51, 6);
INSERT INTO `position_to_attribute` VALUES (15, 52, 7);
INSERT INTO `position_to_attribute` VALUES (15, 26, 8);
INSERT INTO `position_to_attribute` VALUES (15, 27, 9);
INSERT INTO `position_to_attribute` VALUES (15, 28, 10);
INSERT INTO `position_to_attribute` VALUES (15, 29, 11);
INSERT INTO `position_to_attribute` VALUES (16, 44, 1);
INSERT INTO `position_to_attribute` VALUES (16, 47, 2);
INSERT INTO `position_to_attribute` VALUES (16, 48, 3);
INSERT INTO `position_to_attribute` VALUES (16, 49, 4);
INSERT INTO `position_to_attribute` VALUES (16, 50, 5);
INSERT INTO `position_to_attribute` VALUES (16, 51, 6);
INSERT INTO `position_to_attribute` VALUES (16, 52, 7);
INSERT INTO `position_to_attribute` VALUES (16, 26, 8);
INSERT INTO `position_to_attribute` VALUES (16, 27, 9);
INSERT INTO `position_to_attribute` VALUES (16, 28, 10);
INSERT INTO `position_to_attribute` VALUES (16, 29, 11);
INSERT INTO `position_to_attribute` VALUES (4, 28, 11);
INSERT INTO `position_to_attribute` VALUES (4, 29, 12);
INSERT INTO `position_to_attribute` VALUES (17, 30, 1);
INSERT INTO `position_to_attribute` VALUES (17, 31, 2);
INSERT INTO `position_to_attribute` VALUES (17, 32, 3);
INSERT INTO `position_to_attribute` VALUES (17, 28, 4);
INSERT INTO `position_to_attribute` VALUES (17, 36, 5);

DROP TABLE IF EXISTS `team_player`;
CREATE TABLE `team_player` (
  `player_id` int(11) NOT NULL default '0',
  `team_id` int(11) NOT NULL default '0',
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
  PRIMARY KEY  (`player_id`,`team_id`)
);

DROP TABLE IF EXISTS `team_player_to_attribute`;
CREATE TABLE `team_player_to_attribute` (
  `player_id` int(11) NOT NULL default '0',
  `attribute_id` int(11) NOT NULL default '0',
  `team_id` int(11) NOT NULL,
  `player_to_attribute_low` int(11) default NULL,
  `player_to_attribute_high` int(11) default NULL,
  PRIMARY KEY  (`player_id`,`attribute_id`,`team_id`)
);

DROP TABLE IF EXISTS `combine_ratings`;
CREATE TABLE `combine_ratings` (
  `position_id` int(11) NOT NULL default '0',
  `combine_low_sole` int(11) default NULL,
  `combine_high_sole` int(11) default NULL,
  `combine_low_strength` int(11) default NULL,
  `combine_high_strength` int(11) default NULL,
  `combine_low_40` float(3,2) default NULL,
  `combine_high_40` float(3,2) default NULL,
  `combine_low_agil` float(3,2) default NULL,
  `combine_high_agil` float(3,2) default NULL,
  `combine_low_broad` int(11) default NULL,
  `combine_high_broad` int(11) default NULL,
  `combine_low_pos` int(11) default NULL,
  `combine_high_pos` int(11) default NULL,
  PRIMARY KEY  (`position_id`)
);

INSERT INTO `combine_ratings` VALUES (1, 16, 34, 8, 13, 4.98, 4.74, 8.06, 7.63, 102, 112, 61, 77);
INSERT INTO `combine_ratings` VALUES (2, 13, 24, 12, 18, 4.74, 4.58, 7.54, 7.25, 114, 124, 14, 25);
INSERT INTO `combine_ratings` VALUES (3, 15, 25, 17, 24, 4.86, 4.71, 7.86, 7.47, 102, 112, 18, 31);
INSERT INTO `combine_ratings` VALUES (4, 16, 27, 19, 26, 4.88, 4.72, 8.07, 7.63, 102, 113, 24, 40);
INSERT INTO `combine_ratings` VALUES (5, 13, 24, 8, 14, 4.64, 4.46, 7.37, 7.11, 114, 125, 34, 51);
INSERT INTO `combine_ratings` VALUES (6, 19, 30, 21, 29, 5.39, 5.23, 8.17, 7.91, 89, 99, NULL, NULL);
INSERT INTO `combine_ratings` VALUES (7, 17, 28, 24, 31, 5.37, 5.19, 8.16, 7.81, 89, 99, NULL, NULL);
INSERT INTO `combine_ratings` VALUES (8, 16, 28, 24, 32, 5.41, 5.19, 8.06, 7.71, 91, 101, NULL, NULL);
INSERT INTO `combine_ratings` VALUES (9, 16, 28, 7, 13, 5.21, 4.98, 8.06, 7.67, 102, 112, NULL, NULL);
INSERT INTO `combine_ratings` VALUES (10, 18, 29, 6, 12, 5.24, 5.06, 8.06, 7.67, 102, 112, NULL, NULL);
INSERT INTO `combine_ratings` VALUES (11, 15, 27, 23, 30, 4.98, 4.79, 7.86, 7.47, 106, 117, NULL, NULL);
INSERT INTO `combine_ratings` VALUES (12, 14, 25, 25, 32, 5.21, 5.02, 8.10, 7.67, 95, 109, NULL, NULL);
INSERT INTO `combine_ratings` VALUES (13, 19, 30, 18, 25, 4.95, 4.80, 7.82, 7.50, 104, 114, NULL, NULL);
INSERT INTO `combine_ratings` VALUES (14, 17, 28, 14, 21, 4.82, 4.65, 7.57, 7.31, 108, 118, 21, 35);
INSERT INTO `combine_ratings` VALUES (15, 14, 25, 9, 15, 4.61, 4.47, 7.37, 7.11, 114, 125, 29, 45);
INSERT INTO `combine_ratings` VALUES (16, 20, 32, 12, 18, 4.69, 4.54, 7.54, 7.25, 109, 125, 29, 45);
INSERT INTO `combine_ratings` VALUES (17, 19, 30, 21, 29, 5.39, 5.23, 8.17, 7.91, 89, 99, NULL, NULL);
DROP TABLE IF EXISTS `height_weight`;
CREATE TABLE `height_weight` (
  `position_id` int(11) NOT NULL default '0',
  `weight_light` int(11) default NULL,
  `weight_wba` int(11) default NULL,
  `weight_ba` int(11) default NULL,
  `weight_avg` int(11) default NULL,
  `weight_aa` int(11) default NULL,
  `weight_waa` int(11) default NULL,
  `weight_heavy` int(11) default NULL,
  `height_short` int(11) default NULL,
  `height_wba` int(11) default NULL,
  `height_ba` int(11) default NULL,
  `height_avg` int(11) default NULL,
  `height_aa` int(11) default NULL,
  `height_waa` int(11) default NULL,
  PRIMARY KEY  (`position_id`)
);

INSERT INTO `height_weight` VALUES (1, 170, 210, 216, 223, 232, 1000, 2000, 0, 71, 73, 74, 76, 100);
INSERT INTO `height_weight` VALUES (2, 170, 208, 214, 221, 230, 265, 1000, 0, 68, 70, 71, 73, 100);
INSERT INTO `height_weight` VALUES (3, 210, 233, 240, 247, 257, 290, 1000, 0, 69, 71, 72, 75, 100);
INSERT INTO `height_weight` VALUES (4, 210, 246, 253, 261, 271, 290, 1000, 71, 71, 75, 76, 78, 100);
INSERT INTO `height_weight` VALUES (5, 170, 289, 195, 200, 209, 236, 1000, 69, 69, 71, 72, 74, 100);
INSERT INTO `height_weight` VALUES (6, 265, 280, 289, 297, 309, 1000, 2000, 0, 72, 74, 75, 77, 100);
INSERT INTO `height_weight` VALUES (7, 270, 300, 310, 318, 330, 1000, 2000, 0, 73, 75, 76, 78, 100);
INSERT INTO `height_weight` VALUES (8, 275, 302, 310, 323, 333, 1000, 2000, 0, 74, 76, 77, 79, 100);
INSERT INTO `height_weight` VALUES (9, 0, 202, 208, 215, 224, 1000, 2000, 0, 71, 73, 74, 76, 100);
INSERT INTO `height_weight` VALUES (10, 0, 192, 198, 198, 212, 1000, 2000, 0, 69, 71, 72, 74, 100);
INSERT INTO `height_weight` VALUES (11, 255, 267, 274, 282, 293, 315, 1000, 71, 73, 75, 76, 78, 100);
INSERT INTO `height_weight` VALUES (12, 280, 289, 298, 308, 319, 1000, 2000, 71, 72, 74, 75, 77, 100);
INSERT INTO `height_weight` VALUES (13, 225, 234, 243, 250, 260, 281, 1000, 0, 70, 72, 73, 75, 100);
INSERT INTO `height_weight` VALUES (14, 220, 230, 236, 243, 254, 274, 1000, 0, 71, 73, 74, 76, 100);
INSERT INTO `height_weight` VALUES (15, 170, 183, 189, 194, 202, 226, 1000, 68, 68, 70, 71, 73, 100);
INSERT INTO `height_weight` VALUES (16, 170, 197, 203, 211, 219, 236, 1000, 68, 68, 71, 72, 74, 100);
INSERT INTO `height_weight` VALUES (17, 170, 197, 203, 211, 219, 236, 1000, 68, 68, 71, 72, 74, 100);

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
(1, 'EST', '-5:00', 'EST'),
(2, 'EDT', '-4:00', 'EDT'),
(3, 'CST', '-6:00', 'CST'),
(4, 'CDT', '-5:00', 'CDT'),
(5, 'MST', '-7:00', 'MST'),
(6, 'MDT', '-6:00', 'MDT'),
(7, 'PST', '-8:00', 'PST'),
(8, 'PDT', '-7:00', 'PDT');

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

DROP TABLE IF EXISTS `team_to_name`;
CREATE TABLE IF NOT EXISTS `team_to_name` (
  `team_to_name_id` int(11) NOT NULL auto_increment,
  `team_id` int(11) default NULL,
  `team_name` varchar(255) default NULL,
  PRIMARY KEY  (`team_to_name_id`),
  KEY `team_id` (`team_id`),
  KEY `team_name` (`team_name`)
);

DROP TABLE IF EXISTS `mock_draft`;
CREATE TABLE IF NOT EXISTS `mock_draft` (
  `pick_id` int(11) NOT NULL default '0',
  `team_id` int(11) default NULL,
  `player_id` int(11) default NULL,
  `mock_draft_commentary` text,
  PRIMARY KEY  (`pick_id`),
  KEY `player_id` (`player_id`),
  KEY `team_id` (`team_id`)
);


DROP TABLE IF EXISTS `staff_selection`;
CREATE TABLE IF NOT EXISTS `staff_selection` (
  `team_id` int(11) NOT NULL default '0',
  `staff_id` int(11) NOT NULL default '0',
  `staff_role` int(11) NOT NULL default '0',
  `selection_priority` int(11) NOT NULL default '0',
  PRIMARY KEY  (`team_id`,`staff_id`)
);


INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (11,0);
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (12,1);
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (13,'localhost');
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (14,25);
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (15,'user');
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (16,'password');
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (17,0);
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (18,'Paste chatroom code here');
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (19,2);
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (20,0);

DROP TABLE IF EXISTS `fof7_attribute_map`;
CREATE TABLE IF NOT EXISTS `fof7_attribute_map` (`attribute_id` int(11),`fof7_attribute_column` int(11),KEY `attribute_id` (`attribute_id`));
INSERT INTO `fof7_attribute_map` (`attribute_id`, `fof7_attribute_column` ) VALUES (1,-1),(2,4),(3,5),(4,6),(5,7),(6,8),(7,9),(13,10),(8,12),(9,13),(10,14),(11,15),(12,16),(15,22),(16,23),(17,24),(18,25),(19,26),(20,27),(21,28),(22,29),(23,30),(24,31),(25,32),(33,33),(34,34),(35,35),(26,36),(27,37),(30,39),(31,40),(32,41),(37,42),(38,43),(39,44),(42,45),(43,46),(40,47),(41,48),(44,49),(45,50),(47,51),(48,52),(49,53),(46,54),(50,55),(51,56),(52,57),(28,58),(29,59),(36,60),(14,61);

DROP TABLE IF EXISTS `fof7_rookie_attribute_map`;
CREATE TABLE IF NOT EXISTS `fof7_rookie_attribute_map` (`attribute_id` int(11),`fof7_attribute_column` int(11),KEY `attribute_id` (`attribute_id`));
INSERT INTO `fof7_rookie_attribute_map` (`attribute_id`, `fof7_attribute_column` ) VALUES (1,-1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(13,8),(8,10),(9,11),(10,12),(11,13),(12,14),(15,20),(16,21),(17,22),(18,23),(19,24),(20,25),(21,26),(22,27),(23,28),(24,29),(25,30),(33,31),(34,32),(35,33),(26,34),(27,35),(30,37),(31,38),(32,39),(37,40),(38,41),(39,42),(42,43),(43,44),(40,45),(41,46),(44,47),(45,48),(47,49),(48,50),(49,51),(46,52),(50,53),(51,54),(52,55),(28,56),(29,57),(36,58),(14,59);
