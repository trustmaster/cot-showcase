CREATE TABLE IF NOT EXISTS `cot_showcase` (
	`sc_id` INT NOT NULL AUTO_INCREMENT,
	`sc_domain` VARCHAR(100) NOT NULL,
	`sc_title` VARCHAR(100) NOT NULL,
	`sc_owner` INT NOT NULL REFERENCES `cot_users` (`user_id`),
	`sc_date` INT NOT NULL,
	`sc_active` TINYINT NOT NULL DEFAULT 0,
	`sc_descr` VARCHAR(255),
	PRIMARY KEY (`sc_id`),
	KEY (`sc_domain`),
	KEY (`sc_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;