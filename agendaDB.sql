SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema agendaDB
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema agendaDB
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `agendaDB` DEFAULT CHARACTER SET utf8 ;
USE `agendaDB` ;

-- -----------------------------------------------------
-- Table `agendaDB`.`usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agendaDB`.`usuario` (
  `idUsuario` INT NOT NULL AUTO_INCREMENT,
  `nom_usuario` VARCHAR(45) NOT NULL,
  `correo` VARCHAR(45) NOT NULL,
  `contrasena` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idUsuario`)
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `agendaDB`.`habitos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agendaDB`.`habitos` (
  `idhabitos` INT NOT NULL AUTO_INCREMENT,
  `nom_habito` VARCHAR(45) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `prioridad` INT NOT NULL,
  `lunes` TINYINT NULL,
  `martes` TINYINT NULL,
  `miercoles` TINYINT NULL,
  `jueves` TINYINT NULL,
  `viernes` TINYINT NULL,
  `sabado` TINYINT NULL,
  `domingo` TINYINT NULL,
  `usuario_idUsuario` INT NOT NULL,
  PRIMARY KEY (`idhabitos`),
  INDEX `fk_habitos_usuario1_idx` (`usuario_idUsuario` ASC),
  CONSTRAINT `fk_habitos_usuario1`
    FOREIGN KEY (`usuario_idUsuario`)
    REFERENCES `agendaDB`.`usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `agendaDB`.`tareas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agendaDB`.`tareas` (
  `idtareas` INT NOT NULL AUTO_INCREMENT,
  `nom_tarea` VARCHAR(45) NOT NULL,
  `fecha` DATE NOT NULL,
  `lugar` VARCHAR(100) NOT NULL,
  `duracion` VARCHAR(45) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `prioridad` INT NOT NULL,
  PRIMARY KEY (`idtareas`)
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `agendaDB`.`compartir`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agendaDB`.`compartir` (
  `propietario` TINYINT NOT NULL,
  `usuario_idUsuario` INT NOT NULL,
  `tareas_idtareas` INT NOT NULL,
  `estado` INT NOT NULL,
  `aceptar` BOOLEAN NOT NULL DEFAULT 0,
  PRIMARY KEY (`usuario_idUsuario`, `tareas_idtareas`),
  INDEX `fk_compartir_tareas1_idx` (`tareas_idtareas` ASC),
  CONSTRAINT `fk_compartir_usuario`
    FOREIGN KEY (`usuario_idUsuario`)
    REFERENCES `agendaDB`.`usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_compartir_tareas1`
    FOREIGN KEY (`tareas_idtareas`)
    REFERENCES `agendaDB`.`tareas` (`idtareas`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `agendaDB`.`fechas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agendaDB`.`fechas` (
  `idfechas` INT NOT NULL AUTO_INCREMENT,
  `fecha` DATE NOT NULL,
  `cumplido` TINYINT NOT NULL,
  `habitos_idhabitos` INT NOT NULL,
  PRIMARY KEY (`idfechas`),
  INDEX `fk_fechas_habitos1_idx` (`habitos_idhabitos` ASC),
  CONSTRAINT `fk_fechas_habitos1`
    FOREIGN KEY (`habitos_idhabitos`)
    REFERENCES `agendaDB`.`habitos` (`idhabitos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
