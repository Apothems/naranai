--
-- Add Color fields.
--
ALTER TABLE `images` ADD `primary_color` VARCHAR( 20 ) NOT NULL ;
ALTER TABLE `images` ADD `secondary_color` VARCHAR( 20 ) NOT NULL ;
ALTER TABLE `images` ADD `tertiary_color` VARCHAR( 20 ) NOT NULL ;