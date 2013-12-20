
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- category
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- project
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `project`;

CREATE TABLE `project`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `slug` VARCHAR(20) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `content` TEXT,
    `date` DATE,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `project_U_1` (`slug`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- project_image
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `project_image`;

CREATE TABLE `project_image`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `project_id` INTEGER NOT NULL,
    `file` VARCHAR(20) NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT,
    PRIMARY KEY (`id`),
    INDEX `project_image_FI_1` (`project_id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- project_category
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `project_category`;

CREATE TABLE `project_category`
(
    `project_id` INTEGER NOT NULL,
    `category_id` INTEGER NOT NULL,
    PRIMARY KEY (`project_id`,`category_id`),
    INDEX `project_category_FI_2` (`category_id`)
) ENGINE=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
