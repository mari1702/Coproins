-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema db_coproins
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema db_coproins
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `db_coproins` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
USE `db_coproins` ;

-- -----------------------------------------------------
-- Table `db_coproins`.`categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`categoria` (
  `categoria_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL DEFAULT 'NONAME',
  PRIMARY KEY (`categoria_id`),
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`cliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`cliente` (
  `id_cliente` INT(11) NOT NULL AUTO_INCREMENT,
  `cliente` VARCHAR(100) NOT NULL,
  `telefono_cliente` VARCHAR(15) NULL DEFAULT NULL,
  `id_admin_creador` INT(11) NOT NULL,
  `estado_cliente` ENUM('Activo', 'Finalizado') NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_cliente`))
ENGINE = InnoDB
AUTO_INCREMENT = 5;


-- -----------------------------------------------------
-- Table `db_coproins`.`contacto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`contacto` (
  `id_contacto` INT(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` INT(11) NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id_contacto`),
  INDEX `id_cliente` (`id_cliente` ASC) VISIBLE,
  CONSTRAINT `contacto_ibfk_1`
    FOREIGN KEY (`id_cliente`)
    REFERENCES `db_coproins`.`cliente` (`id_cliente`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 5;


-- -----------------------------------------------------
-- Table `db_coproins`.`cotizacion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`cotizacion` (
  `cotizacion_id` INT(11) NOT NULL AUTO_INCREMENT,
  `proyecto` VARCHAR(200) NULL DEFAULT NULL,
  `total` FLOAT NULL DEFAULT NULL,
  `fecha` DATE NULL DEFAULT NULL,
  `cliente_id_cliente` INT(11) NOT NULL,
  PRIMARY KEY (`cotizacion_id`, `cliente_id_cliente`),
  INDEX `fk_cotizacion_cliente1_idx` (`cliente_id_cliente` ASC) VISIBLE,
  CONSTRAINT `fk_cotizacion_cliente1`
    FOREIGN KEY (`cliente_id_cliente`)
    REFERENCES `db_coproins`.`cliente` (`id_cliente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 35
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`nota`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`nota` (
  `nota_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nota` VARCHAR(254) NULL DEFAULT NULL,
  PRIMARY KEY (`nota_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 20
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`cotizacion_has_nota`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`cotizacion_has_nota` (
  `cotizacion_cotizacion_id` INT(11) NOT NULL,
  `nota_nota_id` INT(11) NOT NULL,
  PRIMARY KEY (`cotizacion_cotizacion_id`, `nota_nota_id`),
  INDEX `fk_cotizacion_has_nota_nota1_idx` (`nota_nota_id` ASC) VISIBLE,
  INDEX `fk_cotizacion_has_nota_cotizacion1_idx` (`cotizacion_cotizacion_id` ASC) VISIBLE,
  CONSTRAINT `fk_cotizacion_has_nota_cotizacion1`
    FOREIGN KEY (`cotizacion_cotizacion_id`)
    REFERENCES `db_coproins`.`cotizacion` (`cotizacion_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cotizacion_has_nota_nota1`
    FOREIGN KEY (`nota_nota_id`)
    REFERENCES `db_coproins`.`nota` (`nota_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`departamento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`departamento` (
  `departamento_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`departamento_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`usuario` (
  `id_usuario` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(50) NOT NULL,
  `contrasena` VARCHAR(255) NOT NULL,
  `rol` VARCHAR(50) NOT NULL,
  `id_admin_creador` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE INDEX `usuario` (`usuario` ASC) VISIBLE,
  INDEX `id_admin_creador` (`id_admin_creador` ASC) VISIBLE,
  CONSTRAINT `usuario_ibfk_1`
    FOREIGN KEY (`id_admin_creador`)
    REFERENCES `db_coproins`.`usuario` (`id_usuario`)
    ON DELETE SET NULL)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`encargado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`encargado` (
  `id_encargado` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` VARCHAR(100) NOT NULL,
  `telefono_encargado` VARCHAR(15) NULL DEFAULT NULL,
  `id_admin_creador` INT(11) NOT NULL,
  `id_cliente` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id_encargado`),
  INDEX `fk_encargado_admin` (`id_admin_creador` ASC) VISIBLE,
  CONSTRAINT `fk_encargado_admin`
    FOREIGN KEY (`id_admin_creador`)
    REFERENCES `db_coproins`.`usuario` (`id_usuario`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 5;


-- -----------------------------------------------------
-- Table `db_coproins`.`nuevo_proyecto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`nuevo_proyecto` (
  `id_nuevo_proyecto` INT(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` INT(11) NOT NULL,
  `id_encargado` INT(11) NOT NULL,
  `estado` VARCHAR(100) NOT NULL,
  `localidad` VARCHAR(100) NOT NULL,
  `costo_inicial` DECIMAL(10,2) NOT NULL,
  `fecha` DATE NOT NULL,
  `id_admin_creador` INT(11) NULL DEFAULT NULL,
  `estado_proyecto` ENUM('Activo', 'Finalizado') NULL DEFAULT 'Activo',
  PRIMARY KEY (`id_nuevo_proyecto`),
  INDEX `id_cliente` (`id_cliente` ASC) VISIBLE,
  INDEX `id_encargado` (`id_encargado` ASC) VISIBLE,
  INDEX `id_admin_creador` (`id_admin_creador` ASC) VISIBLE,
  CONSTRAINT `nuevo_proyecto_ibfk_1`
    FOREIGN KEY (`id_cliente`)
    REFERENCES `db_coproins`.`cliente` (`id_cliente`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `nuevo_proyecto_ibfk_2`
    FOREIGN KEY (`id_encargado`)
    REFERENCES `db_coproins`.`encargado` (`id_encargado`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `nuevo_proyecto_ibfk_3`
    FOREIGN KEY (`id_admin_creador`)
    REFERENCES `db_coproins`.`usuario` (`id_usuario`))
ENGINE = InnoDB
AUTO_INCREMENT = 11;


-- -----------------------------------------------------
-- Table `db_coproins`.`gasto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`gasto` (
  `id_gasto` INT(11) NOT NULL AUTO_INCREMENT,
  `id_nuevo_proyecto` INT(11) NULL DEFAULT NULL,
  `id_usuario` INT(11) NOT NULL,
  `id_cliente` INT(11) NOT NULL,
  `tipo_gasto` VARCHAR(100) NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `gasto` VARCHAR(255) NOT NULL,
  `fecha` DATE NOT NULL,
  PRIMARY KEY (`id_gasto`),
  INDEX `id_nuevo_proyecto` (`id_nuevo_proyecto` ASC) VISIBLE,
  INDEX `id_usuario` (`id_usuario` ASC) VISIBLE,
  INDEX `id_cliente` (`id_cliente` ASC) VISIBLE,
  CONSTRAINT `gasto_ibfk_1`
    FOREIGN KEY (`id_nuevo_proyecto`)
    REFERENCES `db_coproins`.`nuevo_proyecto` (`id_nuevo_proyecto`),
  CONSTRAINT `gasto_ibfk_2`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `db_coproins`.`usuario` (`id_usuario`),
  CONSTRAINT `gasto_ibfk_3`
    FOREIGN KEY (`id_cliente`)
    REFERENCES `db_coproins`.`cliente` (`id_cliente`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`marca`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`marca` (
  `marca_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`marca_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`herramienta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`herramienta` (
  `herramienta_id` INT(11) NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(254) NULL DEFAULT NULL,
  `img_ruta` VARCHAR(254) NULL DEFAULT NULL,
  `marca_marca_id` INT(11) NOT NULL,
  `departamento_departamento_id` INT(11) NOT NULL,
  PRIMARY KEY (`herramienta_id`, `marca_marca_id`, `departamento_departamento_id`),
  INDEX `fk_herramienta_marca1_idx` (`marca_marca_id` ASC) VISIBLE,
  INDEX `fk_herramienta_Departamento1_idx` (`departamento_departamento_id` ASC) VISIBLE,
  CONSTRAINT `fk_herramienta_Departamento1`
    FOREIGN KEY (`departamento_departamento_id`)
    REFERENCES `db_coproins`.`departamento` (`departamento_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_herramienta_marca1`
    FOREIGN KEY (`marca_marca_id`)
    REFERENCES `db_coproins`.`marca` (`marca_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 9
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`historial_costo_proyecto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`historial_costo_proyecto` (
  `id_historial` INT(11) NOT NULL AUTO_INCREMENT,
  `id_nuevo_proyecto` INT(11) NOT NULL,
  `costo` DECIMAL(10,2) NOT NULL,
  `diferencia` DECIMAL(10,2) NOT NULL,
  `fecha_modificacion` DATETIME NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id_historial`),
  INDEX `id_nuevo_proyecto` (`id_nuevo_proyecto` ASC) VISIBLE,
  CONSTRAINT `historial_costo_proyecto_ibfk_1`
    FOREIGN KEY (`id_nuevo_proyecto`)
    REFERENCES `db_coproins`.`nuevo_proyecto` (`id_nuevo_proyecto`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 11;


-- -----------------------------------------------------
-- Table `db_coproins`.`inventario_herramienta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`inventario_herramienta` (
  `h_inventario_id` INT(11) NOT NULL AUTO_INCREMENT,
  `ubicacion` VARCHAR(254) NULL DEFAULT NULL,
  `fecha` DATE NULL DEFAULT NULL,
  PRIMARY KEY (`h_inventario_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`inventario_herramienta_has_herramienta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`inventario_herramienta_has_herramienta` (
  `inventario_herramienta_h_inventario_id` INT(11) NOT NULL,
  `herramienta_herramienta_id` INT(11) NOT NULL,
  `cantidad` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`inventario_herramienta_h_inventario_id`, `herramienta_herramienta_id`),
  INDEX `fk_inventario_herramienta_has_herramienta_herramienta1_idx` (`herramienta_herramienta_id` ASC) VISIBLE,
  INDEX `fk_inventario_herramienta_has_herramienta_inventario_herram_idx` (`inventario_herramienta_h_inventario_id` ASC) VISIBLE,
  CONSTRAINT `fk_inventario_herramienta_has_herramienta_herramienta1`
    FOREIGN KEY (`herramienta_herramienta_id`)
    REFERENCES `db_coproins`.`herramienta` (`herramienta_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_inventario_herramienta_has_herramienta_inventario_herramie1`
    FOREIGN KEY (`inventario_herramienta_h_inventario_id`)
    REFERENCES `db_coproins`.`inventario_herramienta` (`h_inventario_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`um`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`um` (
  `um_id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL DEFAULT 'NONAME',
  PRIMARY KEY (`um_id`),
  UNIQUE INDEX `nombre_UNIQUE` (`nombre` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`producto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`producto` (
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
    REFERENCES `db_coproins`.`categoria` (`categoria_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_producto_um`
    FOREIGN KEY (`um_um_id`)
    REFERENCES `db_coproins`.`um` (`um_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 25
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`producto_has_cotizacion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`producto_has_cotizacion` (
  `producto_producto_id` INT(11) NOT NULL,
  `cotizacion_cotizacion_id` INT(11) NOT NULL,
  `cantidad` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`producto_producto_id`, `cotizacion_cotizacion_id`),
  INDEX `fk_producto_has_cotizacion_cotizacion1_idx` (`cotizacion_cotizacion_id` ASC) VISIBLE,
  INDEX `fk_producto_has_cotizacion_producto1_idx` (`producto_producto_id` ASC) VISIBLE,
  CONSTRAINT `fk_producto_has_cotizacion_cotizacion1`
    FOREIGN KEY (`cotizacion_cotizacion_id`)
    REFERENCES `db_coproins`.`cotizacion` (`cotizacion_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_producto_has_cotizacion_producto1`
    FOREIGN KEY (`producto_producto_id`)
    REFERENCES `db_coproins`.`producto` (`producto_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `db_coproins`.`proyectos_compartidos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_coproins`.`proyectos_compartidos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_proyecto` INT(11) NOT NULL,
  `id_admin` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `id_proyecto` (`id_proyecto` ASC) VISIBLE,
  INDEX `id_admin` (`id_admin` ASC) VISIBLE,
  CONSTRAINT `proyectos_compartidos_ibfk_1`
    FOREIGN KEY (`id_proyecto`)
    REFERENCES `db_coproins`.`nuevo_proyecto` (`id_nuevo_proyecto`)
    ON DELETE CASCADE,
  CONSTRAINT `proyectos_compartidos_ibfk_2`
    FOREIGN KEY (`id_admin`)
    REFERENCES `db_coproins`.`usuario` (`id_usuario`)
    ON DELETE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
