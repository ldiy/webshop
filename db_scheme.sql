-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema lshop
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema lshop
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `lshop` DEFAULT CHARACTER SET utf8 ;
USE `lshop` ;

-- -----------------------------------------------------
-- Table `lshop`.`role`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`role` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(319) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  INDEX `role_id_idx` (`role_id` ASC),
  CONSTRAINT `user_role_id`
    FOREIGN KEY (`role_id`)
    REFERENCES `lshop`.`role` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`address`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`address` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `country_code` VARCHAR(2) NOT NULL,
  `postcode` VARCHAR(10) NOT NULL,
  `city` VARCHAR(64) NOT NULL,
  `address_line1` VARCHAR(128) NOT NULL,
  `address_line2` VARCHAR(128) NULL,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `user_id_idx` (`user_id` ASC),
  CONSTRAINT `address_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `lshop`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`manufacturer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`manufacturer` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `description` TEXT(1024) NULL,
  `logo` VARCHAR(64) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`product` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `description` TEXT(512) NULL DEFAULT NULL,
  `price` DECIMAL(20,2) GENERATED ALWAYS AS (0) VIRTUAL,
  `stock_quantity` INT(10) NULL DEFAULT 0,
  `width` DECIMAL(10,3) NOT NULL DEFAULT 0,
  `height` DECIMAL(10,3) NOT NULL DEFAULT 0,
  `depth` DECIMAL(10,3) NOT NULL DEFAULT 0,
  `weight` DECIMAL(10,3) NOT NULL DEFAULT 0,
  `manufacturer_id` INT UNSIGNED NULL DEFAULT NULL,
  `ean13` VARCHAR(13) NULL DEFAULT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `manufacturer_id_idx` (`manufacturer_id` ASC),
  CONSTRAINT `product_manufacturer_id`
    FOREIGN KEY (`manufacturer_id`)
    REFERENCES `lshop`.`manufacturer` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(128) NOT NULL,
  `description` TEXT(256) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`category_product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`category_product` (
  `category_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`product_id`, `category_id`),
  UNIQUE INDEX `categor_id_product_id_UNIQUE` (`product_id` ASC, `category_id` ASC),
  INDEX `category_id_idx` (`category_id` ASC),
  CONSTRAINT `cp_category_id`
    FOREIGN KEY (`category_id`)
    REFERENCES `lshop`.`category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `cp_product_id`
    FOREIGN KEY (`product_id`)
    REFERENCES `lshop`.`product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`order`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`order` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `address_id` INT UNSIGNED NOT NULL,
  `status` INT(3) UNSIGNED NOT NULL,
  `total_products_tax_excl` DECIMAL(20,2) NOT NULL DEFAULT 0.00,
  `total_products_tax_incl` DECIMAL(20,2) NOT NULL DEFAULT 0.00,
  `total_shipping_tax_incl` DECIMAL(20,2) NOT NULL DEFAULT 0.00,
  `total_shipping_tax_excl` DECIMAL(20,2) NOT NULL DEFAULT 0.00,
  `total_paid` DECIMAL(20,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`order_product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`order_product` (
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT(10) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`order_id`, `product_id`),
  UNIQUE INDEX `order_id_product_id_UNIQUE` (`order_id` ASC, `product_id` ASC),
  INDEX `product_id_idx` (`product_id` ASC),
  CONSTRAINT `op_order_id`
    FOREIGN KEY (`order_id`)
    REFERENCES `lshop`.`order` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `op_product_id`
    FOREIGN KEY (`product_id`)
    REFERENCES `lshop`.`product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`cart`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`cart` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `address_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `address_id_idx` (`address_id` ASC),
  INDEX `user_id_idx` (`user_id` ASC),
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC),
  CONSTRAINT `cart_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `lshop`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `cart_address_id`
    FOREIGN KEY (`address_id`)
    REFERENCES `lshop`.`address` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`cart_product`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`cart_product` (
  `cart_id` INT UNSIGNED NOT NULL,
  `product_id` INT(10) UNSIGNED NOT NULL,
  `quantity` INT(10) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`cart_id`, `product_id`),
  UNIQUE INDEX `cart_id_product_id_UNIQUE` (`cart_id` ASC, `product_id` ASC),
  INDEX `product_id_idx` (`product_id` ASC),
  CONSTRAINT `cartp_cart_id`
    FOREIGN KEY (`cart_id`)
    REFERENCES `lshop`.`cart` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `cartp_product_id`
    FOREIGN KEY (`product_id`)
    REFERENCES `lshop`.`product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `lshop`.`product_photo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `lshop`.`product_photo` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `thumbnail_path` VARCHAR(255) NULL,
  `alt` VARCHAR(64) NOT NULL,
  `order` INT(2) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `product_id_idx` (`product_id` ASC),
  CONSTRAINT `photo_product_id`
    FOREIGN KEY (`product_id`)
    REFERENCES `lshop`.`product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
