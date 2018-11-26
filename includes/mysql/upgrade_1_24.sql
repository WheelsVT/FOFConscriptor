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
