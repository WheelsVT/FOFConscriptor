ALTER TABLE `team` ADD `team_owner` TEXT;
ALTER TABLE `chat` ADD `team_owner` TEXT;
ALTER TABLE `team` ADD `draft_admin` int(11) default '0';
CREATE TABLE `last_update` ( `latest_message` int(11), `time` timestamp );
INSERT INTO `last_update` (`latest_message`) VALUES (1);
