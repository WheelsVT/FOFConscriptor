ALTER TABLE `settings` CHANGE `setting_value` `setting_value` VARCHAR( 4096 ) NOT NULL;
INSERT INTO `settings`(`setting_id`, `setting_value`) VALUES (18,'Paste chatroom code here');