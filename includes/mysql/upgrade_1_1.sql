ALTER TABLE `team` ADD `team_comments` TEXT NULL AFTER `team_name` ;
ALTER TABLE `player` ADD `player_score` FLOAT( 5, 2 ) NULL AFTER `player_name` ,
ADD `player_adj_score` FLOAT( 5, 2 ) NULL AFTER `player_score` ;
DROP TABLE IF EXISTS `column`;
CREATE TABLE `column` (
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
INSERT INTO `column` VALUES (1, 'position.position_name', NULL, 'Pos', NULL, NULL, NULL, 10);
INSERT INTO `column` VALUES (2, 'player.player_school', NULL, 'School', NULL, NULL, NULL, 20);
INSERT INTO `column` VALUES (3, 'player.player_height', NULL, 'Height', 'height_convert', NULL, NULL, 30);
INSERT INTO `column` VALUES (4, 'player.player_weight', NULL, 'Weight', NULL, NULL, NULL, 40);
INSERT INTO `column` VALUES (5, 'player.player_vol', NULL, 'Volatility', NULL, NULL, NULL, 50);
INSERT INTO `column` VALUES (6, 'player.player_solec', 'if (player.player_solec <= combine_ratings.combine_low_sole,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_solec >= combine_ratings.combine_high_sole,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)', 'Solecismic', NULL, NULL, NULL, 60);
INSERT INTO `column` VALUES (7, 'player.player_40', 'if (player.player_40 >= combine_ratings.combine_low_40,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_40 <= combine_ratings.combine_high_40,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)', '40-time', NULL, NULL, NULL, 70);
INSERT INTO `column` VALUES (8, 'player.player_bench', 'if (player.player_bench <= combine_ratings.combine_low_strength,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_bench >= combine_ratings.combine_high_strength,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)', 'Bench Press Reps', NULL, NULL, NULL, 80);
INSERT INTO `column` VALUES (9, 'player.player_agil', 'if (player.player_agil >= combine_ratings.combine_low_agil,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_agil <= combine_ratings.combine_high_agil,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)', 'Agility', NULL, NULL, NULL, 90);
INSERT INTO `column` VALUES (10, 'player.player_broad', 'if (player.player_broad <= combine_ratings.combine_low_broad,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_broad >= combine_ratings.combine_high_broad,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)', 'Broad Jump', 'height_convert', NULL, NULL, 100);
INSERT INTO `column` VALUES (11, 'player.player_pos_drill', 'if (combine_ratings.combine_high_pos is not null, \r\n\r\nif (player.player_pos_drill <= combine_ratings.combine_low_pos,\r\n"color: #0b0; font-weight: bold",\r\n\r\nif (player.player_pos_drill >= combine_ratings.combine_high_pos,\r\n"color: #00f; font-weight: bold", NULL)\r\n\r\n)\r\n\r\n, \r\n\r\nNULL)', 'Position Drill', NULL, NULL, NULL, 110);
INSERT INTO `column` VALUES (12, 'player.player_developed', NULL, 'Developed', NULL, NULL, NULL, 120);
INSERT INTO `column` VALUES (13, 'player.player_dob', NULL, 'Birthdate', NULL, 'n/g/Y', NULL, 130);
INSERT INTO `column` VALUES (14, 'player.player_hometown', NULL, 'Hometown', NULL, NULL, NULL, 140);
INSERT INTO `column` VALUES (15, 'player.player_agent', NULL, 'Agent', NULL, NULL, NULL, 150);
INSERT INTO `column` VALUES (16, 'player.player_experience', NULL, 'Exp', NULL, NULL, NULL, 160);
INSERT INTO `column` VALUES (17, 'team_player.player_loyalty', NULL, 'Loyalty', NULL, NULL, NULL, 170);
INSERT INTO `column` VALUES (18, 'team_player.player_winner', NULL, 'Winner', NULL, NULL, NULL, 180);
INSERT INTO `column` VALUES (19, 'team_player.player_leader', NULL, 'Leader', NULL, NULL, NULL, 190);
INSERT INTO `column` VALUES (20, 'team_player.player_intelligence', NULL, 'Intelligence', NULL, NULL, NULL, 200);
INSERT INTO `column` VALUES (21, 'team_player.player_personality', NULL, 'Personality', NULL, NULL, NULL, 210);
INSERT INTO `column` VALUES (22, 'team_player.player_popularity', NULL, 'Popularity', NULL, NULL, NULL, 220);
INSERT INTO `column` VALUES (23, 'team_player.player_interviewed', NULL, 'Interviewed', NULL, NULL, NULL, 230);
INSERT INTO `column` VALUES (24, 'team_player.player_impression', NULL, 'Impression', NULL, NULL, NULL, 240);
INSERT INTO `column` VALUES (25, 'team_player.player_current', NULL, 'Current', NULL, NULL, NULL, 250);
INSERT INTO `column` VALUES (26, 'team_player.player_future', NULL, 'Future', NULL, NULL, NULL, 260);
INSERT INTO `column` VALUES (27, 'team_player.player_conflicts', NULL, 'Conflicts', NULL, NULL, NULL, 270);
INSERT INTO `column` VALUES (28, 'team_player.player_affinities', NULL, 'Affinities', NULL, NULL, NULL, 280);
INSERT INTO `column` VALUES (29, 'player.player_score', NULL, 'Grade', NULL, NULL, NULL, 41);
INSERT INTO `column` VALUES (30, 'player.player_adj_score', NULL, 'Adjusted Grade', NULL, NULL, NULL, 42);
