-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema cotizaciones
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema cotizaciones
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `cotizaciones` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
USE `cotizaciones` ;

-- -----------------------------------------------------
-- Table `cotizaciones`.`categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cotizaciones`.`categoria` (
  `categoria_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL DEFAULT 'NONAME',
  PRIMARY KEY (`categoria_id`),
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `cotizaciones`.`cotizacion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cotizaciones`.`cotizacion` (
  `cotizacion_id` INT(11) NOT NULL AUTO_INCREMENT,
  `proyecto` VARCHAR(200) NULL DEFAULT NULL,
  `total` FLOAT NULL DEFAULT NULL,
  `fecha` DATE NULL DEFAULT NULL,
  `cliente` VARCHAR(200) NULL DEFAULT NULL,
  PRIMARY KEY (`cotizacion_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 31
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `cotizaciones`.`nota`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cotizaciones`.`nota` (
  `nota_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nota` VARCHAR(254) NULL DEFAULT NULL,
  PRIMARY KEY (`nota_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 15
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `cotizaciones`.`cotizacion_has_nota`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cotizaciones`.`cotizacion_has_nota` (
  `cotizacion_cotizacion_id` INT(11) NOT NULL,
  `nota_nota_id` INT(11) NOT NULL,
  PRIMARY KEY (`cotizacion_cotizacion_id`, `nota_nota_id`),
  INDEX `fk_cotizacion_has_nota_nota1_idx` (`nota_nota_id` ASC) VISIBLE,
  INDEX `fk_cotizacion_has_nota_cotizacion1_idx` (`cotizacion_cotizacion_id` ASC) VISIBLE,
  CONSTRAINT `fk_cotizacion_has_nota_cotizacion1`
    FOREIGN KEY (`cotizacion_cotizacion_id`)
    REFERENCES `cotizaciones`.`cotizacion` (`cotizacion_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cotizacion_has_nota_nota1`
    FOREIGN KEY (`nota_nota_id`)
    REFERENCES `cotizaciones`.`nota` (`nota_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `cotizaciones`.`um`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cotizaciones`.`um` (
  `um_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL DEFAULT 'NONAME',
  PRIMARY KEY (`um_id`),
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `cotizaciones`.`producto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cotizaciones`.`producto` (
  `producto_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL DEFAULT 'NONAME',
  `descripcion` VARCHAR(200) NOT NULL DEFAULT 'NODESC',
  `precio` FLOAT NOT NULL DEFAULT 0,
  `img_ruta` VARCHAR(254) NULL DEFAULT NULL,
  `um_um_id` INT(11) NOT NULL,
  `categoria_categoria_id` INT(11) NOT NULL,
  PRIMARY KEY (`producto_id`, `um_um_id`, `categoria_categoria_id`),
  INDEX `fk_producto_um_idx` (`um_um_id` ASC) VISIBLE,
  INDEX `fk_producto_categoria1_idx` (`categoria_categoria_id` ASC) VISIBLE,
  CONSTRAINT `fk_producto_categoria1`
    FOREIGN KEY (`categoria_categoria_id`)
    REFERENCES `cotizaciones`.`categoria` (`categoria_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_producto_um`
    FOREIGN KEY (`um_um_id`)
    REFERENCES `cotizaciones`.`um` (`um_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `cotizaciones`.`producto_has_cotizacion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cotizaciones`.`producto_has_cotizacion` (
  `producto_producto_id` INT(11) NOT NULL,
  `cotizacion_cotizacion_id` INT(11) NOT NULL,
  `cantidad` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`producto_producto_id`, `cotizacion_cotizacion_id`),
  INDEX `fk_producto_has_cotizacion_cotizacion1_idx` (`cotizacion_cotizacion_id` ASC) VISIBLE,
  INDEX `fk_producto_has_cotizacion_producto1_idx` (`producto_producto_id` ASC) VISIBLE,
  CONSTRAINT `fk_producto_has_cotizacion_cotizacion1`
    FOREIGN KEY (`cotizacion_cotizacion_id`)
    REFERENCES `cotizaciones`.`cotizacion` (`cotizacion_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_producto_has_cotizacion_producto1`
    FOREIGN KEY (`producto_producto_id`)
    REFERENCES `cotizaciones`.`producto` (`producto_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
