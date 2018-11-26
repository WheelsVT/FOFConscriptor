DROP TABLE `chat`;
DROP TABLE `chat_room`;
DROP TABLE `last_update`;
ALTER TABLE `team` ADD `team_phone` TEXT;
ALTER TABLE `team` ADD `team_carrier` TEXT;
ALTER TABLE `team` ADD `team_user_link` TEXT;
ALTER TABLE `team` ADD `team_sms_setting` INT(11) DEFAULT '0';
ALTER TABLE `team` ALTER `team_autopick_wait` SET DEFAULT 30;
ALTER TABLE `team` ALTER `pick_method_id` SET DEFAULT 3;
