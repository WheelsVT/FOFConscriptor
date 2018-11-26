UPDATE `team` SET in_game_id=99 WHERE team_name='xxx';
UPDATE `team` SET in_game_id=-1 WHERE team_id='1';
alter table `staff` add fired int(11) default 0;

alter table `staff_trans_history` add staff_trans_year int(11) default 0;
alter table `staff_trans_history` drop column input_file_number;

DROP TABLE IF EXISTS `fof7_attribute_map`;
CREATE TABLE IF NOT EXISTS `fof7_attribute_map` (`attribute_id` int(11),`fof7_attribute_column` int(11),KEY `attribute_id` (`attribute_id`));
INSERT INTO `fof7_attribute_map` (`attribute_id`, `fof7_attribute_column` ) VALUES (1,-1),(2,4),(3,5),(4,6),(5,7),(6,8),(7,9),(13,10),(8,12),(9,13),(10,14),(11,15),(12,16),(15,22),(16,23),(17,24),(18,25),(19,26),(20,27),(21,28),(22,29),(23,30),(24,31),(25,32),(33,33),(34,34),(35,35),(26,36),(27,37),(30,39),(31,40),(32,41),(37,42),(38,43),(39,44),(42,45),(43,46),(40,47),(41,48),(44,49),(45,50),(47,51),(48,52),(49,53),(46,54),(50,55),(51,56),(52,57),(28,58),(29,59),(36,60),(14,61);
