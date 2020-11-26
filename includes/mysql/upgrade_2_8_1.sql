--
-- Add column to hold slotted draft due datetime
--
ALTER TABLE `pick`
  ADD `slotted_draft_expire` datetime DEFAULT NULL;
  