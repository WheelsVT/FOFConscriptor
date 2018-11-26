DROP TABLE IF EXISTS `staff_selection`;
CREATE TABLE IF NOT EXISTS `staff_selection` (
  `team_id` int(11) NOT NULL default '0',
  `staff_id` int(11) NOT NULL default '0',
  `staff_role` int(11) NOT NULL default '0',
  `selection_priority` int(11) NOT NULL default '0',
  PRIMARY KEY  (`team_id`,`staff_id`)
);

DROP TABLE IF EXISTS `fof7_rookie_attribute_map`;
CREATE TABLE IF NOT EXISTS `fof7_rookie_attribute_map` (`attribute_id` int(11),`fof7_attribute_column` int(11),KEY `attribute_id` (`attribute_id`));
INSERT INTO `fof7_rookie_attribute_map` (`attribute_id`, `fof7_attribute_column` ) VALUES (1,-1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(13,8),(8,10),(9,11),(10,12),(11,13),(12,14),(15,20),(16,21),(17,22),(18,23),(19,24),(20,25),(21,26),(22,27),(23,28),(24,29),(25,30),(33,31),(34,32),(35,33),(26,34),(27,35),(30,37),(31,38),(32,39),(37,40),(38,41),(39,42),(42,43),(43,44),(40,45),(41,46),(44,47),(45,48),(47,49),(48,50),(49,51),(46,52),(50,53),(51,54),(52,55),(28,56),(29,57),(36,58),(14,59);
