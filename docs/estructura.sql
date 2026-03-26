-- --------------------------------------------------------
-- Host:                         172.21.111.67
-- Versión del servidor:         5.0.77 - Source distribution
-- SO del servidor:              redhat-linux-gnu
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para proservipol_test
CREATE DATABASE IF NOT EXISTS `proservipol_test` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `proservipol_test`;

-- Volcando estructura para tabla proservipol_test.ACCESORIO_SERVICIO
CREATE TABLE IF NOT EXISTS `ACCESORIO_SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `TACC_CODIGO` int(11) NOT NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`FUN_CODIGO`,`TACC_CODIGO`),
  KEY `ACCESORIO_SERVICIO_FK` (`TACC_CODIGO`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`FUN_CODIGO`),
  CONSTRAINT `FK_ACCESORIO_SERVICIO` FOREIGN KEY (`TACC_CODIGO`) REFERENCES `TIPO_ACCESORIO` (`TACC_CODIGO`),
  CONSTRAINT `FK_ACCESORIO_SERVICIO2` FOREIGN KEY (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `FUN_CODIGO`) REFERENCES `FUNCIONARIO_SERVICIO` (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `FUN_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='TABLA QUE CONTIENE LOS ACCESORIOS QUE FUERON OCUPADOS POR LO';

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ACTA_OIC
CREATE TABLE IF NOT EXISTS `ACTA_OIC` (
  `COD_ACTA_OIC` int(11) NOT NULL auto_increment,
  `NUM_ACTA` int(11) default NULL,
  `UNI_CODIGO` int(11) default NULL,
  `FECHA_SERVICIOS` date default NULL,
  `ESTADO` int(11) default NULL,
  PRIMARY KEY  (`COD_ACTA_OIC`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ACTIVIDAD_FUERA_CUARTEL
CREATE TABLE IF NOT EXISTS `ACTIVIDAD_FUERA_CUARTEL` (
  `COD_ACTIVIDAD_FUERA_CUARTEL` int(11) NOT NULL auto_increment,
  `NUM_DOCUMENTO` varchar(12) collate latin1_spanish_ci default NULL,
  `FUN_RUT` varchar(10) collate latin1_spanish_ci NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `FECHA_INICIO` date NOT NULL,
  `FECHA_TERMINO` date NOT NULL,
  `FECHA_INICIO_REAL` date default NULL,
  `FECHA_TERMINO_REAL` date default NULL,
  `NOMBRE_ARCHIVO` varchar(100) collate latin1_spanish_ci default NULL,
  `DIR_IP_UNIDAD` varchar(15) collate latin1_spanish_ci NOT NULL,
  `TIPO_ACTIVIDAD` int(11) NOT NULL COMMENT 'Relacionada a la tabla tipo servicio, indica el tipo de actidad fuera cuartel',
  `FUN_CODIGO_UNIDAD` char(7) collate latin1_spanish_ci NOT NULL COMMENT 'Codigo funcionario de la persona que ingreso la comision de servicio al sistema',
  `FECHA_REGISTRO` date NOT NULL,
  `ESTADO` tinyint(4) NOT NULL default '1',
  `FECHA_MODIFICA` date default NULL COMMENT 'Fecha que se modifico la comision de servicio del sistema',
  `DIR_IP_MODIFICA` varchar(12) collate latin1_spanish_ci default NULL COMMENT 'IP del funcionario que modifico la comision de servicio del sistema',
  `FUN_CODIGO_MODIFICA` char(7) collate latin1_spanish_ci default NULL COMMENT 'Codigo funcionario de la persona que modifico la comision de servicio del sistema',
  PRIMARY KEY  (`COD_ACTIVIDAD_FUERA_CUARTEL`,`UNI_CODIGO`)
) ENGINE=InnoDB AUTO_INCREMENT=50226 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ANIMALES_SERVICIO
CREATE TABLE IF NOT EXISTS `ANIMALES_SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `ANIM_CODIGO` int(11) NOT NULL default '0',
  `TANIM_CODIGO` int(11) NOT NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`ANIM_CODIGO`,`TANIM_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ANIMAL_SERVICIO
CREATE TABLE IF NOT EXISTS `ANIMAL_SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `TANIM_CODIGO` int(11) NOT NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`FUN_CODIGO`,`TANIM_CODIGO`),
  KEY `ANIMAL_SERVICIO_FK` (`TANIM_CODIGO`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`FUN_CODIGO`),
  CONSTRAINT `FK_ANIMAL_SERVICIO` FOREIGN KEY (`TANIM_CODIGO`) REFERENCES `TIPO_ANIMAL` (`TANIM_CODIGO`),
  CONSTRAINT `FK_ANIMAL_SERVICIO2` FOREIGN KEY (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `FUN_CODIGO`) REFERENCES `FUNCIONARIO_SERVICIO` (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `FUN_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ANOTACIONES
CREATE TABLE IF NOT EXISTS `ANOTACIONES` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `NUMERO_MEDIO` int(11) NOT NULL,
  `ANOT_ID` int(11) NOT NULL,
  `FACT_CODIGO` int(11) default NULL,
  `ANOT_HORA_INICIO` time default NULL,
  `ANOT_HORA_TERMINO` time default NULL,
  `CUADRANTE_CODIGO` int(11) default NULL,
  `CUADRANTE_CODIGO_OTRO` int(11) default NULL,
  `UNI_CODIGO_OTRO` int(11) default NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`NUMERO_MEDIO`,`ANOT_ID`),
  KEY `ANOTACION_HOJARUTA_FK` (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`NUMERO_MEDIO`),
  KEY `FACT_CODIGO` (`FACT_CODIGO`),
  KEY `CUADRANTE_CODIGO` (`CUADRANTE_CODIGO`),
  KEY `CUADRANTE_CODIGO_OTRO` (`CUADRANTE_CODIGO_OTRO`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`),
  CONSTRAINT `FK_ANOTACION_HOJARUTA` FOREIGN KEY (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `NUMERO_MEDIO`) REFERENCES `HOJA_RUTA` (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `NUMERO_MEDIO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_CUADRANTE_ANOTACION` FOREIGN KEY (`CUADRANTE_CODIGO`) REFERENCES `UNIDAD_CUADRANTE` (`CUADRANTE_CODIGO`),
  CONSTRAINT `FK_FACTOR_ANOTACION` FOREIGN KEY (`FACT_CODIGO`) REFERENCES `FACTORES` (`FACT_CODIGO`),
  CONSTRAINT `FK_OTRA_UNIDAD_ANOTACION` FOREIGN KEY (`UNI_CODIGO`) REFERENCES `UNIDAD` (`UNI_CODIGO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_OTRO_CUADRANTE_ANOTACION` FOREIGN KEY (`CUADRANTE_CODIGO_OTRO`) REFERENCES `UNIDAD_CUADRANTE` (`CUADRANTE_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ARCHIVO_LICENCIA_CONDUCIR
CREATE TABLE IF NOT EXISTS `ARCHIVO_LICENCIA_CONDUCIR` (
  `FUN_CODIGO` char(7) collate latin1_general_ci default NULL,
  `TIPO` varchar(20) collate latin1_general_ci default NULL,
  `NOMBRE_ARCHIVO` varchar(100) collate latin1_general_ci default NULL,
  KEY `FUN_CODIGO` (`FUN_CODIGO`),
  KEY `TIPO` (`TIPO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ARMA
CREATE TABLE IF NOT EXISTS `ARMA` (
  `ARM_CODIGO` bigint(20) NOT NULL auto_increment,
  `MODARM_CODIGO` int(11) NOT NULL,
  `UNI_CODIGO` int(11) default NULL,
  `TARM_CODIGO` int(11) NOT NULL,
  `ARM_NUMEROSERIE` varchar(20) collate latin1_general_ci default NULL,
  `ARM_BCU` varchar(20) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`ARM_CODIGO`),
  KEY `TIPO_DE_ARMA_FK` (`TARM_CODIGO`),
  KEY `UNIDAD_ARMA_FK` (`UNI_CODIGO`),
  KEY `ARMA_MODELO_FK` (`MODARM_CODIGO`),
  CONSTRAINT `FK_ARMA_MODELO` FOREIGN KEY (`MODARM_CODIGO`) REFERENCES `MODELO_ARMA` (`MODARM_CODIGO`),
  CONSTRAINT `FK_TIPO_DE_ARMA` FOREIGN KEY (`TARM_CODIGO`) REFERENCES `TIPO_ARMA` (`TARM_CODIGO`),
  CONSTRAINT `FK_UNIDAD_ARMA` FOREIGN KEY (`UNI_CODIGO`) REFERENCES `UNIDAD` (`UNI_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=188733 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ARMA_SERVICIO
CREATE TABLE IF NOT EXISTS `ARMA_SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `ARM_CODIGO` bigint(20) NOT NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`FUN_CODIGO`,`ARM_CODIGO`),
  KEY `ARMA_SERVICIO_FK` (`ARM_CODIGO`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`),
  KEY `UNI_CODIGO_2` (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`FUN_CODIGO`),
  CONSTRAINT `FK_ARMA_SERVICIO` FOREIGN KEY (`ARM_CODIGO`) REFERENCES `ARMA` (`ARM_CODIGO`),
  CONSTRAINT `FK_ARMA_SERVICIO2` FOREIGN KEY (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `FUN_CODIGO`) REFERENCES `FUNCIONARIO_SERVICIO` (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `FUN_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.BITACORA_USUARIO
CREATE TABLE IF NOT EXISTS `BITACORA_USUARIO` (
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `US_FECHAHORA_INICIO` datetime NOT NULL,
  `US_FECHAHORA_TERMINO` datetime default NULL,
  `US_DIRECCION_IP` char(15) collate latin1_general_ci NOT NULL,
  `TUS_CODIGO` int(11) default NULL,
  `US_EVENTO` varchar(40) collate latin1_general_ci default 'CIERRE DE SESION:',
  PRIMARY KEY  (`FUN_CODIGO`,`UNI_CODIGO`,`US_FECHAHORA_INICIO`),
  KEY `FUNCIONARIO_BITACORA_FK` (`FUN_CODIGO`),
  KEY `UNIDAD_BITACORA_FK` (`UNI_CODIGO`),
  KEY `TIPO_USU_BITACORA_FK` (`TUS_CODIGO`),
  KEY `FECHA_INICIO_BITACORA_FK` (`US_FECHAHORA_INICIO`),
  KEY `FECHA_TERMINO_BITACORA_FK` (`US_FECHAHORA_TERMINO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CABALLO
CREATE TABLE IF NOT EXISTS `CABALLO` (
  `CAB_CODIGO` bigint(20) NOT NULL auto_increment,
  `TANI_CODIGO` int(11) default NULL,
  `CAB_BCU` varchar(20) collate latin1_spanish_ci default NULL,
  `CAB_NOMBRE` varchar(50) collate latin1_spanish_ci default NULL,
  `UNI_CODIGO` int(11) default NULL,
  `FECHA_NAC` varchar(10) collate latin1_spanish_ci default NULL,
  `CAB_RAZA` varchar(20) collate latin1_spanish_ci default NULL,
  `CAB_COLOR` varchar(20) collate latin1_spanish_ci default NULL,
  `CAB_PELAJE` varchar(20) collate latin1_spanish_ci default NULL,
  `CAB_SEXO` varchar(20) collate latin1_spanish_ci default NULL,
  `VERIFICACION_ESTADO` varchar(2) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`CAB_CODIGO`)
) ENGINE=InnoDB AUTO_INCREMENT=2389 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CAPACITACION
CREATE TABLE IF NOT EXISTS `CAPACITACION` (
  `CORRELATIVO_CAPACITACION` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `FECHA_CAPACITACION` date NOT NULL,
  `FECHA_VALIDEZ` date default NULL,
  `VERSION_PROSERVIPOL` varchar(5) collate latin1_general_ci NOT NULL,
  `NOTA_PROSERVIPOL` float(2,1) default NULL,
  `TIPO_CAPACITACION` varchar(100) collate latin1_general_ci NOT NULL default '',
  `ACTIVO` int(11) NOT NULL,
  `CODIGO_VERIFICACION` varchar(18) collate latin1_general_ci default NULL,
  `TIPO_CERTIFICADO` varchar(50) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`CORRELATIVO_CAPACITACION`,`FUN_CODIGO`),
  KEY `FUN_CODIGO_ACTIVO` (`FUN_CODIGO`,`ACTIVO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CARGO
CREATE TABLE IF NOT EXISTS `CARGO` (
  `CAR_CODIGO` int(11) NOT NULL,
  `CAR_DESCRIPCION` varchar(100) collate latin1_general_ci default NULL,
  `CAR_MOSTRAR` tinyint(4) default '1',
  `CAR_CLASE` char(1) collate latin1_general_ci default NULL,
  `CAR_GRUPO1` varchar(60) collate latin1_general_ci default NULL,
  `CAR_GRUPO2` varchar(60) collate latin1_general_ci default NULL,
  `CAR_GRUPO3` int(11) default NULL COMMENT 'CONTIENE INDICADOR DE CARGOS QUE PUEDEN TENER SERVICIO SUPERVISION EN EL TERRITORIO',
  `CAR_CLASIFICACION` varchar(30) collate latin1_general_ci default NULL,
  `CAR_ACTIVO` tinyint(4) default '1',
  `CAR_GRUPO4` varchar(50) collate latin1_general_ci default NULL COMMENT 'CONTIENE LA CLASIFICACION ENTRE INTRACUARTEL, INTRACUARTEL VARIABLE Y EN EL TERRITORIO, SEGUN CATEGORIZACION DE CUARTELES DEFINIDA POR LA LA DIPLADECAR (OCTUBRE 2018) Y SERA APLICADA DESDE ENERO DE 2018 HACIA ADELANTE ',
  `CAR_GRUPO5` varchar(50) collate latin1_general_ci default NULL COMMENT 'CATEGORIA DE CARGOS EN FICHA FUNCIONARIO',
  PRIMARY KEY  (`CAR_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CARGO_CUARTEL
CREATE TABLE IF NOT EXISTS `CARGO_CUARTEL` (
  `CAR_CODIGO` int(11) NOT NULL,
  `TCU_CODIGO` int(11) NOT NULL,
  `TESPC_CODIGO` int(11) NOT NULL,
  `GCAR_CODIGO` int(11) NOT NULL,
  `PERMITE_CPR` tinyint(4) NOT NULL,
  `ACTIVO` tinyint(4) NOT NULL,
  `ORDEN` int(11) NOT NULL default '0',
  PRIMARY KEY  (`CAR_CODIGO`,`TCU_CODIGO`,`TESPC_CODIGO`),
  KEY `FK_CARGO_CUARTEL_TIPO_CUARTEL` (`TCU_CODIGO`),
  KEY `FK_CARGO_CUARTEL_TIPO_ESPECIALIDAD_CUARTEL` (`TESPC_CODIGO`),
  KEY `FK_CARGO_CUARTEL_GRUPO_CARGO` (`GCAR_CODIGO`),
  CONSTRAINT `FK_CARGO_CUARTEL_CARGO` FOREIGN KEY (`CAR_CODIGO`) REFERENCES `CARGO` (`CAR_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_CARGO_CUARTEL_GRUPO_CARGO` FOREIGN KEY (`GCAR_CODIGO`) REFERENCES `GRUPO_CARGO` (`GCAR_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_CARGO_CUARTEL_TIPO_CUARTEL` FOREIGN KEY (`TCU_CODIGO`) REFERENCES `TIPO_CUARTEL` (`TCU_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_CARGO_CUARTEL_TIPO_ESPECIALIDAD_CUARTEL` FOREIGN KEY (`TESPC_CODIGO`) REFERENCES `TIPO_ESPECIALIDAD_CUARTEL` (`TESPC_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CARGO_FUNCIONARIO
CREATE TABLE IF NOT EXISTS `CARGO_FUNCIONARIO` (
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `CORRELATIVO_CARGOFUNCIONARIO` int(11) NOT NULL,
  `CAR_CODIGO` int(11) NOT NULL,
  `UNI_CODIGO` int(11) default '0',
  `FECHA_DESDE` date default NULL,
  `FECHA_HASTA` date default NULL,
  `CUADRANTE_CODIGO` int(11) default NULL,
  `UNI_AGREGADO` int(11) default NULL,
  `CANT_DIAS` int(11) default NULL,
  `SEC_CODIGO` int(11) default NULL,
  PRIMARY KEY  (`FUN_CODIGO`,`CORRELATIVO_CARGOFUNCIONARIO`),
  KEY `CARGO_FUNCIONARIO2_FK` (`CAR_CODIGO`),
  KEY `CARGO_FUNCIONARIO3_FK` (`UNI_CODIGO`),
  KEY `CUADRANTE_CODIGO` (`CUADRANTE_CODIGO`),
  KEY `CUADRANTE_CARGO_FK` (`CUADRANTE_CODIGO`),
  KEY `FUN_CODIGO` (`FUN_CODIGO`),
  KEY `FECHA_DESDE` (`FECHA_DESDE`),
  KEY `FECHA_HASTA` (`FECHA_HASTA`),
  KEY `AGREGADO` (`UNI_AGREGADO`),
  CONSTRAINT `FK_CARGO_FUNCIONARIO` FOREIGN KEY (`FUN_CODIGO`) REFERENCES `FUNCIONARIO` (`FUN_CODIGO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_CARGO_FUNCIONARIO2` FOREIGN KEY (`CAR_CODIGO`) REFERENCES `CARGO` (`CAR_CODIGO`),
  CONSTRAINT `FK_CARGO_FUNCIONARIO3` FOREIGN KEY (`UNI_CODIGO`) REFERENCES `UNIDAD` (`UNI_CODIGO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_CUADRANTE_CARGO` FOREIGN KEY (`CUADRANTE_CODIGO`) REFERENCES `UNIDAD_CUADRANTE` (`CUADRANTE_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CARGO_TUNIDAD
CREATE TABLE IF NOT EXISTS `CARGO_TUNIDAD` (
  `UNI_TIPOUNIDAD` int(11) NOT NULL,
  `CAR_CODIGO` int(11) NOT NULL,
  `ACTIVO` smallint(6) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CLASE_LICENCIA_CONDUCIR
CREATE TABLE IF NOT EXISTS `CLASE_LICENCIA_CONDUCIR` (
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `LM_NUMERO` int(11) NOT NULL,
  `TLIC_CODIGO` int(11) NOT NULL,
  PRIMARY KEY  (`FUN_CODIGO`,`LM_NUMERO`,`TLIC_CODIGO`),
  UNIQUE KEY `CLASE_LICENCIA_CONDUCIR_PK` (`FUN_CODIGO`,`LM_NUMERO`,`TLIC_CODIGO`),
  KEY `CLASE_LICENCIA_CONDUCIR_FK` (`FUN_CODIGO`,`LM_NUMERO`),
  KEY `CLASE_LICENCIA_CONDUCIR2_FK` (`TLIC_CODIGO`),
  CONSTRAINT `FK_CLASE_LICENCIA_CONDUCIR` FOREIGN KEY (`FUN_CODIGO`, `LM_NUMERO`) REFERENCES `LICENCIA_MUNICIPAL` (`FUN_CODIGO`, `LM_NUMERO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_CLASE_LICENCIA_CONDUCIR2` FOREIGN KEY (`TLIC_CODIGO`) REFERENCES `TIPO_LICENCIA_CONDUCIR` (`TLIC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.COLOR_LICENCIA
CREATE TABLE IF NOT EXISTS `COLOR_LICENCIA` (
  `COLOR_LICENCIA` char(20) collate latin1_spanish_ci NOT NULL,
  `COLOR_DESCRIPCION` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  PRIMARY KEY  (`COLOR_LICENCIA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.COMUNA
CREATE TABLE IF NOT EXISTS `COMUNA` (
  `COM_CODIGO` int(11) NOT NULL,
  `COM_DESCRIPCION` varchar(100) collate latin1_general_ci default NULL,
  `COM_CODIGOINSTITUCIONAL` varchar(6) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`COM_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CONFIG_SYS
CREATE TABLE IF NOT EXISTS `CONFIG_SYS` (
  `ID` int(11) NOT NULL auto_increment,
  `FECHA_CIERRE` date default NULL COMMENT 'Indica la fecha que se cerrara el mes',
  `FECHA_LIMITE` date default NULL COMMENT 'Indica la fecha de bloqueo en el sistema',
  `FECHA_PROXIMO_CIERRE` date default NULL COMMENT 'Indica la fecha que se cerrara el proximo mes',
  `ACTIVO` int(11) NOT NULL default '0' COMMENT 'FECHA ACTIVA',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `CONFIG_SYS_FECHA_CIERRE` (`FECHA_CIERRE`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CONSULTA_SERVICIO
CREATE TABLE IF NOT EXISTS `CONSULTA_SERVICIO` (
  `US_LOGIN` char(7) collate latin1_general_ci NOT NULL,
  `TUS_CODIGO` int(11) NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `FECHA_HORA` datetime NOT NULL,
  `IP` varchar(15) collate latin1_general_ci NOT NULL,
  `PARAMETRO1` char(7) collate latin1_general_ci NOT NULL,
  `PARAMETRO2` varchar(10) collate latin1_general_ci NOT NULL,
  `PARAMETRO3` varchar(10) collate latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CUADRANTE
CREATE TABLE IF NOT EXISTS `CUADRANTE` (
  `CUA_CODIGO` int(11) NOT NULL,
  `CUA_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `CUA_ABREVIATURA` varchar(10) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`CUA_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.CUADRANTE_SERVICIO
CREATE TABLE IF NOT EXISTS `CUADRANTE_SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `CUADRANTE_CODIGO` int(11) NOT NULL default '0',
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`FUN_CODIGO`,`CUADRANTE_CODIGO`),
  KEY `CUADRANTE_SERVICIO2_FK` (`CUADRANTE_CODIGO`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`FUN_CODIGO`),
  CONSTRAINT `FK_CUADRANTE_SERVICIO` FOREIGN KEY (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `FUN_CODIGO`) REFERENCES `FUNCIONARIO_SERVICIO` (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `FUN_CODIGO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_CUADRANTE_SERVICIO2` FOREIGN KEY (`CUADRANTE_CODIGO`) REFERENCES `UNIDAD_CUADRANTE` (`CUADRANTE_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.cuadrante_servicio_factor
CREATE TABLE IF NOT EXISTS `cuadrante_servicio_factor` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_spanish_ci NOT NULL,
  `CUADRANTE_CODIGO` int(11) NOT NULL default '0',
  `FACTOR_PREVENTIVO` int(11) NOT NULL default '0',
  `FACTOR_FISCALIZACION` int(11) NOT NULL default '0',
  `FACTOR_OOJJ` int(11) NOT NULL default '0',
  `FACTOR_SS_EXTRAORDINARIOS` int(11) NOT NULL default '0',
  `FACTOR_PROCEDIMIENTOS` int(11) NOT NULL default '0',
  `TIEMPO_AUTOASIGNADO` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.DELITOS_AUPOL
CREATE TABLE IF NOT EXISTS `DELITOS_AUPOL` (
  `UNI_CODIGO` int(11) NOT NULL,
  `FECHA` date NOT NULL,
  `CODIGO_INTERVALO` int(11) NOT NULL,
  `CODIGO_ENUSC` int(11) NOT NULL,
  `CANTIDAD_DELITOS` int(11) default NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`FECHA`,`CODIGO_INTERVALO`,`CODIGO_ENUSC`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.DELITOS_CODIGO_ENUSC
CREATE TABLE IF NOT EXISTS `DELITOS_CODIGO_ENUSC` (
  `CODIGO_ENUSC` int(11) NOT NULL,
  `DESCRIPCION_ENUSC` varchar(50) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`CODIGO_ENUSC`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.DELITOS_ENUSC
CREATE TABLE IF NOT EXISTS `DELITOS_ENUSC` (
  `CODIGO_ENUSC` int(11) NOT NULL,
  `CODIGO_AUPOL` char(20) collate latin1_spanish_ci NOT NULL,
  PRIMARY KEY  (`CODIGO_ENUSC`,`CODIGO_AUPOL`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.DELITOS_INTERVALO
CREATE TABLE IF NOT EXISTS `DELITOS_INTERVALO` (
  `CODIGO_INTERVALO` int(11) NOT NULL,
  `HORA_INICIO` time default NULL,
  `HORA_TERMINO` time default NULL,
  PRIMARY KEY  (`CODIGO_INTERVALO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ESCALAFON
CREATE TABLE IF NOT EXISTS `ESCALAFON` (
  `ESC_CODIGO` int(11) NOT NULL,
  `ESC_DESCRIPCION` varchar(100) collate latin1_general_ci default NULL,
  `TESC_CODIGO` int(11) default NULL,
  `ACTIVO` smallint(6) default '1',
  `FILTRO_SERVICIOS_OPERATIVOS` int(11) NOT NULL default '0' COMMENT '* 1 NO SE MUESTRA EN SERVICIOS OPERATIVOS. \r\n* 0 SE MUESTRA EN SERVICIOS OPERATVIVOS.',
  `TIPO_ESCALAFON` varchar(4) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`ESC_CODIGO`),
  KEY `TESC_CODIGO` (`TESC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ESPECIALIDAD_MEDICA
CREATE TABLE IF NOT EXISTS `ESPECIALIDAD_MEDICA` (
  `MED_COD` int(11) NOT NULL,
  `MED_DESCRIPCION` varchar(30) collate latin1_spanish_ci NOT NULL,
  `MED_COD_HOSCAR` int(11) default NULL,
  PRIMARY KEY  (`MED_COD`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ESPECIALIDAD_TSERVICIO
CREATE TABLE IF NOT EXISTS `ESPECIALIDAD_TSERVICIO` (
  `UNIDAD_ESPECIALIDAD` int(11) NOT NULL COMMENT '"UNIDAD.UNI_ESPECIALIDAD"\r\nSi es distinto de (10,30,31,32,33,40,41,50,60,80,90,110,130,42,150,17,160 ,180) When es 70',
  `TIPO_SERVICIO` int(11) NOT NULL,
  `TIPO` char(1) collate latin1_general_ci NOT NULL,
  `ACTIVO` smallint(6) default NULL,
  PRIMARY KEY  (`UNIDAD_ESPECIALIDAD`,`TIPO_SERVICIO`,`TIPO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ESPECIALIDAD_TUNIDAD
CREATE TABLE IF NOT EXISTS `ESPECIALIDAD_TUNIDAD` (
  `UNI_ESPECIALIZADA` int(11) NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `UNI_TIPOUNIDAD` int(11) NOT NULL,
  `FECHA_DESDE` date default NULL,
  `FECHA_HASTA` date default NULL,
  `ACTIVO` smallint(6) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ESTADO
CREATE TABLE IF NOT EXISTS `ESTADO` (
  `EST_CODIGO` int(11) NOT NULL,
  `EST_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `EST_ABREVIATURA` varchar(10) collate latin1_general_ci default NULL,
  `EST_ACTIVO` tinyint(4) default NULL,
  `EST_VEHICULO` tinyint(4) default NULL,
  `EST_ARMA` tinyint(4) default NULL,
  `EST_CAMARA_CORPORAL` tinyint(4) default NULL,
  PRIMARY KEY  (`EST_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ESTADO_ANIMAL
CREATE TABLE IF NOT EXISTS `ESTADO_ANIMAL` (
  `ANI_CODIGO` bigint(20) NOT NULL,
  `EST_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_ESTADOANIMAL` int(11) NOT NULL,
  `UNI_CODIGO` int(11) default NULL,
  `FECHA_DESDE` date NOT NULL,
  `FECHA_HASTA` date default NULL,
  `UNI_AGREGADO` int(11) default NULL,
  `SEC_CODIGO` int(11) default NULL,
  PRIMARY KEY  (`ANI_CODIGO`,`EST_CODIGO`,`CORRELATIVO_ESTADOANIMAL`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`),
  KEY `FECHA_DESDE` (`FECHA_DESDE`),
  KEY `FECHA_HASTA` (`FECHA_HASTA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ESTADO_ARMA
CREATE TABLE IF NOT EXISTS `ESTADO_ARMA` (
  `EST_CODIGO` int(11) NOT NULL,
  `ARM_CODIGO` bigint(20) NOT NULL,
  `CORRELATIVO_ESTADOARMA` int(11) NOT NULL,
  `UNI_CODIGO` int(11) default NULL,
  `FECHA_DESDE` date NOT NULL,
  `FECHA_HASTA` date default NULL,
  `UNI_AGREGADO` int(11) default NULL,
  `SEC_CODIGO` int(11) default NULL,
  PRIMARY KEY  (`EST_CODIGO`,`ARM_CODIGO`,`CORRELATIVO_ESTADOARMA`),
  KEY `ESTADO_ARMA_FK` (`ARM_CODIGO`),
  KEY `UNI_AGREGADO` (`UNI_AGREGADO`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`),
  KEY `FECHA_DESDE` (`FECHA_DESDE`),
  KEY `FECHA_HASTA` (`FECHA_HASTA`),
  CONSTRAINT `FK_ESTADO_ARMA` FOREIGN KEY (`ARM_CODIGO`) REFERENCES `ARMA` (`ARM_CODIGO`),
  CONSTRAINT `FK_ESTADO_ARMA2` FOREIGN KEY (`EST_CODIGO`) REFERENCES `ESTADO` (`EST_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ESTADO_SIMCCAR
CREATE TABLE IF NOT EXISTS `ESTADO_SIMCCAR` (
  `SIM_CODIGO` bigint(20) NOT NULL,
  `EST_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_ESTADOSIMCCAR` int(11) NOT NULL,
  `UNI_CODIGO` int(11) default NULL,
  `FECHA_DESDE` date NOT NULL,
  `FECHA_HASTA` date default NULL,
  `UNI_AGREGADO` int(11) default NULL,
  `SIM_REEMPLAZO` int(11) default NULL,
  `SEC_CODIGO` int(11) default NULL,
  PRIMARY KEY  (`SIM_CODIGO`,`EST_CODIGO`,`CORRELATIVO_ESTADOSIMCCAR`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ESTADO_VEHICULO
CREATE TABLE IF NOT EXISTS `ESTADO_VEHICULO` (
  `VEH_CODIGO` bigint(20) NOT NULL,
  `EST_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_ESTADOVEHICULO` int(11) NOT NULL,
  `UNI_CODIGO` int(11) default NULL,
  `FECHA_DESDE` date NOT NULL,
  `FECHA_HASTA` date default NULL,
  `EST_DOCUMENTO` varchar(15) collate latin1_general_ci default NULL,
  `UNI_AGREGADO` int(11) default NULL,
  `EST_LUGARREPARACION` int(11) default NULL,
  `TFALLA_CODIGO` int(11) default NULL,
  `SEC_CODIGO` int(11) default NULL,
  `TCLASIFICACION_CITACION_CODIGO` int(11) default NULL COMMENT 'Campo indicador falla para Citacion L3',
  PRIMARY KEY  (`VEH_CODIGO`,`EST_CODIGO`,`CORRELATIVO_ESTADOVEHICULO`),
  KEY `ESTADO_VEHICULO2_FK` (`EST_CODIGO`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`),
  KEY `UNI_AGREGADO` (`UNI_AGREGADO`),
  KEY `EST_LUGARREPARACION` (`EST_LUGARREPARACION`),
  KEY `FECHA_DESDE` (`FECHA_DESDE`),
  KEY `FECHA_HASTA` (`FECHA_HASTA`),
  KEY `FK_TCLASIFICACION_CITACION` (`TCLASIFICACION_CITACION_CODIGO`),
  CONSTRAINT `FK_ESTADO_VEHICULO` FOREIGN KEY (`VEH_CODIGO`) REFERENCES `VEHICULO` (`VEH_CODIGO`),
  CONSTRAINT `FK_ESTADO_VEHICULO2` FOREIGN KEY (`EST_CODIGO`) REFERENCES `ESTADO` (`EST_CODIGO`),
  CONSTRAINT `FK_LUGAR_REPARACION` FOREIGN KEY (`EST_LUGARREPARACION`) REFERENCES `LUGAR_REPARACION` (`LREP_CODIGO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_TCLASIFICACION_CITACION` FOREIGN KEY (`TCLASIFICACION_CITACION_CODIGO`) REFERENCES `TIPO_CLASIFICACION_CITACION` (`TCLASIFICACION_CITACION_CODIGO`),
  CONSTRAINT `FK_UNIDAD_ESTADO` FOREIGN KEY (`UNI_CODIGO`) REFERENCES `UNIDAD` (`UNI_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.ESTADO_VIDEOCAMARA
CREATE TABLE IF NOT EXISTS `ESTADO_VIDEOCAMARA` (
  `VC_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_ESTADOVIDEOCAMARA` int(11) NOT NULL,
  `EST_CODIGO` int(11) NOT NULL,
  `UNI_CODIGO` int(11) default NULL,
  `FECHA_DESDE` date default NULL,
  `FECHA_HASTA` date default NULL,
  `UNI_AGREGADO` int(11) default NULL,
  PRIMARY KEY  (`VC_CODIGO`,`CORRELATIVO_ESTADOVIDEOCAMARA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.FACTORES
CREATE TABLE IF NOT EXISTS `FACTORES` (
  `FACT_CODIGO` int(11) NOT NULL,
  `FACT_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `FACT_ABREVIATURA` varchar(20) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`FACT_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.FALLA_VEHICULO
CREATE TABLE IF NOT EXISTS `FALLA_VEHICULO` (
  `CORRELATIVO_ESTADOVEHICULO` int(11) NOT NULL,
  `VEH_CODIGO` int(11) NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `TFALLA_CODIGO` int(11) NOT NULL,
  PRIMARY KEY  (`CORRELATIVO_ESTADOVEHICULO`,`VEH_CODIGO`,`TFALLA_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.FERPER
CREATE TABLE IF NOT EXISTS `FERPER` (
  `FOLIO_PERMISO` varchar(13) collate latin1_spanish_ci NOT NULL,
  `FUN_RUT` varchar(10) collate latin1_spanish_ci NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `FECHA_INICIO` date NOT NULL,
  `FECHA_TERMINO` date NOT NULL,
  `TIPO_PERMISO` int(11) NOT NULL,
  `NOMBRE_ARCHIVO` varchar(100) collate latin1_spanish_ci default NULL,
  `DIR_IP_UNIDAD` varchar(12) collate latin1_spanish_ci NOT NULL,
  `FUN_CODIGO_UNIDAD` char(7) collate latin1_spanish_ci NOT NULL COMMENT 'Codigo funcionario de la persona que ingreso la licencia medica al sistema',
  `FECHA_REGISTRO` date NOT NULL,
  `ESTADO_PERMISO` tinyint(4) NOT NULL default '1',
  `FECHA_TERMINO_REAL` date default NULL,
  `FECHA_MODIFICA` date default NULL,
  `DIR_IP_MODIFICA` varchar(15) collate latin1_spanish_ci default NULL,
  `FUN_CODIGO_MODIFICA` char(7) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`FOLIO_PERMISO`,`ESTADO_PERMISO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.folio_hojaruta
CREATE TABLE IF NOT EXISTS `folio_hojaruta` (
  `UNI_CODIGO` int(11) NOT NULL,
  `FOLIO_HOJARUTA` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FECHA_SERVICIOS` date default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.FUNCIONARIO
CREATE TABLE IF NOT EXISTS `FUNCIONARIO` (
  `FUN_CODIGO` char(7) character set latin1 collate latin1_general_ci NOT NULL,
  `FUN_RUT` varchar(10) collate latin1_spanish_ci default NULL,
  `ESC_CODIGO` int(11) NOT NULL,
  `GRA_CODIGO` int(11) NOT NULL,
  `UNI_CODIGO` int(11) default NULL,
  `FUN_APELLIDOPATERNO` varchar(30) character set latin1 collate latin1_general_ci default NULL,
  `FUN_APELLIDOMATERNO` varchar(30) character set latin1 collate latin1_general_ci default NULL,
  `FUN_NOMBRE` varchar(30) character set latin1 collate latin1_general_ci default NULL,
  `FUN_NOMBRE2` varchar(30) character set latin1 collate latin1_general_ci default NULL,
  `FUN_CODIGO_INVALIDO` char(7) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`FUN_CODIGO`),
  KEY `GRADO_DEL_FUNCIONARIO_FK` (`ESC_CODIGO`,`GRA_CODIGO`),
  KEY `UNIDAD_FUNCIONARIO_FK` (`UNI_CODIGO`),
  KEY `RUT_FUNCIONARIO_FK` (`FUN_RUT`),
  CONSTRAINT `FK_GRADO_DEL_FUNCIONARIO` FOREIGN KEY (`ESC_CODIGO`, `GRA_CODIGO`) REFERENCES `GRADO` (`ESC_CODIGO`, `GRA_CODIGO`),
  CONSTRAINT `FK_UNIDAD_FUNCIONARIO` FOREIGN KEY (`UNI_CODIGO`) REFERENCES `UNIDAD` (`UNI_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='InnoDB free: 7168 kB; (`ESC_CODIGO` `GRA_CODIGO`) REFER `DB_';

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.FUNCIONARIO_ANIMAL
CREATE TABLE IF NOT EXISTS `FUNCIONARIO_ANIMAL` (
  `FUN_UNI_CODIGO` int(11) NOT NULL,
  `FUN_CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `ANIM_UNI_CODIGO` int(11) NOT NULL,
  `ANIM_CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `ANIM_CODIGO` bigint(20) NOT NULL,
  PRIMARY KEY  (`FUN_UNI_CODIGO`,`FUN_CORRELATIVO_SERVICIO`,`FUN_CODIGO`,`ANIM_UNI_CODIGO`,`ANIM_CORRELATIVO_SERVICIO`,`ANIM_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.FUNCIONARIO_SERVICIO
CREATE TABLE IF NOT EXISTS `FUNCIONARIO_SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `NUMERO_MEDIO` int(11) default NULL,
  `FACT_CODIGO` int(11) default NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`FUN_CODIGO`),
  KEY `FUNCIONARIO_ASIGNADO_FK` (`FUN_CODIGO`),
  KEY `FACT_CODIGO` (`FACT_CODIGO`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`NUMERO_MEDIO`),
  CONSTRAINT `FK_FUNCIONARIO_ASIGNADO` FOREIGN KEY (`FUN_CODIGO`) REFERENCES `FUNCIONARIO` (`FUN_CODIGO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_FUNCIONARIO_DE_SERVICIO` FOREIGN KEY (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`) REFERENCES `SERVICIO` (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`),
  CONSTRAINT `FK_FUNCIONARIO_FACTOR` FOREIGN KEY (`FACT_CODIGO`) REFERENCES `FACTORES` (`FACT_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.FUNCIONARIO_SIMCCAR
CREATE TABLE IF NOT EXISTS `FUNCIONARIO_SIMCCAR` (
  `FUN_UNI_CODIGO` int(11) NOT NULL,
  `FUN_CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `SIM_UNI_CODIGO` int(11) NOT NULL,
  `SIM_CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `SIM_CODIGO` bigint(20) NOT NULL,
  PRIMARY KEY  (`FUN_UNI_CODIGO`,`FUN_CORRELATIVO_SERVICIO`,`FUN_CODIGO`,`SIM_UNI_CODIGO`,`SIM_CORRELATIVO_SERVICIO`,`SIM_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.FUNCIONARIO_VEHICULO
CREATE TABLE IF NOT EXISTS `FUNCIONARIO_VEHICULO` (
  `FUN_UNI_CODIGO` int(11) NOT NULL,
  `FUN_CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `VEH_UNI_CODIGO` int(11) NOT NULL,
  `VEH_CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `VEH_CODIGO` bigint(20) NOT NULL,
  PRIMARY KEY  (`FUN_UNI_CODIGO`,`FUN_CORRELATIVO_SERVICIO`,`FUN_CODIGO`,`VEH_UNI_CODIGO`,`VEH_CODIGO`,`VEH_CORRELATIVO_SERVICIO`),
  KEY `FUNCIONARIO_VEHICULO_FK` (`VEH_UNI_CODIGO`,`VEH_CORRELATIVO_SERVICIO`,`VEH_CODIGO`),
  KEY `FUN_UNI_CODIGO` (`FUN_UNI_CODIGO`,`FUN_CORRELATIVO_SERVICIO`,`FUN_CODIGO`),
  CONSTRAINT `FK_FUNCIONARIO_VEHICULO` FOREIGN KEY (`VEH_UNI_CODIGO`, `VEH_CORRELATIVO_SERVICIO`, `VEH_CODIGO`) REFERENCES `VEHICULO_SERVICIO` (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `VEH_CODIGO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_FUNCIONARIO_VEHICULO2` FOREIGN KEY (`FUN_UNI_CODIGO`, `FUN_CORRELATIVO_SERVICIO`, `FUN_CODIGO`) REFERENCES `FUNCIONARIO_SERVICIO` (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `FUN_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.FUNCIONARIO_VIDEOCAMARA
CREATE TABLE IF NOT EXISTS `FUNCIONARIO_VIDEOCAMARA` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `VC_CODIGO` int(11) NOT NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`FUN_CODIGO`,`VC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.GRADO
CREATE TABLE IF NOT EXISTS `GRADO` (
  `ESC_CODIGO` int(11) NOT NULL,
  `GRA_CODIGO` int(11) NOT NULL,
  `GRA_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`ESC_CODIGO`,`GRA_CODIGO`),
  KEY `ESCALAFON_GRADO_FK` (`ESC_CODIGO`),
  CONSTRAINT `FK_ESCALAFON_GRADO` FOREIGN KEY (`ESC_CODIGO`) REFERENCES `ESCALAFON` (`ESC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.GRUPO_CARGO
CREATE TABLE IF NOT EXISTS `GRUPO_CARGO` (
  `GCAR_CODIGO` int(11) NOT NULL,
  `GCAR_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `GCAR_ORDEN_PRECEDENCIA` int(11) default NULL,
  PRIMARY KEY  (`GCAR_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.GRUPO_SERVICIO
CREATE TABLE IF NOT EXISTS `GRUPO_SERVICIO` (
  `GSER_CODIGO` int(11) NOT NULL,
  `GSER_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `GCAR_ORDEN_PRECEDENCIA` int(11) default NULL,
  PRIMARY KEY  (`GSER_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.HOJA_RUTA
CREATE TABLE IF NOT EXISTS `HOJA_RUTA` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `NUMERO_MEDIO` int(11) NOT NULL,
  `HR_HORA_INICIO_REAL` time default NULL,
  `HR_HORA_TERMINO_REAL` time default NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`NUMERO_MEDIO`),
  CONSTRAINT `FK_HOKARUTA_MEDIO` FOREIGN KEY (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `NUMERO_MEDIO`) REFERENCES `FUNCIONARIO_SERVICIO` (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`, `NUMERO_MEDIO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.LICENCIA_MEDICA
CREATE TABLE IF NOT EXISTS `LICENCIA_MEDICA` (
  `COLOR_LICENCIA` char(2) collate latin1_spanish_ci NOT NULL,
  `FOLIO_LICENCIA` varchar(12) collate latin1_spanish_ci NOT NULL,
  `FUN_RUT` varchar(10) collate latin1_spanish_ci NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `FECHA_OTORGAMIENTO` date NOT NULL,
  `FECHA_INICIO` date NOT NULL,
  `FECHA_TERMINO` date NOT NULL,
  `NUM_DIAS` int(11) NOT NULL,
  `FECHA_INICIO_REAL` date NOT NULL,
  `FECHA_TERMINO_REAL` date NOT NULL COMMENT 'Fecha de termino anticipado, modificaciÃ³n de fecha real de termino',
  `RUT_HIJO` varchar(10) collate latin1_spanish_ci default NULL,
  `FECHA_NAC_HIJO` date default NULL,
  `TIPO_LICENCIA_MEDICA` int(11) NOT NULL,
  `CODIGO_SELIME` char(1) collate latin1_spanish_ci NOT NULL,
  `RECUERABILIDAD_LABORAL` int(11) NOT NULL,
  `INICIO_TRAMITE_INVALIDEZ` int(11) NOT NULL,
  `TIPO_REPOSO` int(11) NOT NULL,
  `LUGAR_REPOSO` int(11) NOT NULL,
  `RUT_PROFESIONAL` varchar(10) collate latin1_spanish_ci NOT NULL,
  `TIPO_PROFESIONAL` int(11) NOT NULL,
  `ESPECIALIDAD_PROFESIONAL` int(11) NOT NULL,
  `TIPO_ATENCION` int(11) NOT NULL,
  `NOMBRE_ARCHIVO` varchar(100) collate latin1_spanish_ci default NULL,
  `DIR_IP_UNIDAD` varchar(15) collate latin1_spanish_ci NOT NULL,
  `FUN_CODIGO_UNIDAD` char(7) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Codigo funcionario de la persona que ingreso la licencia medica al sistema',
  `FECHA_REGISTRO` date NOT NULL,
  `FUERA_PLAZO` int(1) NOT NULL default '0' COMMENT 'Registrada fuera de plazo',
  `ESTADO_LICENCIA` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`COLOR_LICENCIA`,`FOLIO_LICENCIA`,`ESTADO_LICENCIA`),
  KEY `TIPO_LICENCIA_FK` (`TIPO_LICENCIA_MEDICA`),
  KEY `UNIDAD_LICENCIA_FK` (`UNI_CODIGO`),
  KEY `FECHA_INICIO_REAL_FK` (`FECHA_INICIO_REAL`),
  KEY `FECHA_TERMINO_REAL_FK` (`FECHA_TERMINO_REAL`),
  KEY `FECHA_INICIO_FK` (`FECHA_INICIO`),
  KEY `RUT_LICENCIA_FK` (`FUN_RUT`),
  KEY `ESTADO_LICENCIA_FK` (`ESTADO_LICENCIA`),
  KEY `FECHA_REGISTRO` (`FECHA_REGISTRO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.LICENCIA_MUNICIPAL
CREATE TABLE IF NOT EXISTS `LICENCIA_MUNICIPAL` (
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `LM_NUMERO` int(11) NOT NULL,
  `COM_CODIGO` int(11) default NULL,
  `LM_FECHA_ULTIMO_CONTROL` date default NULL,
  `LM_FECHA_PROXIMO_CONTROL` date default NULL,
  `LM_OBSERVACIONES` varchar(200) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`FUN_CODIGO`,`LM_NUMERO`),
  UNIQUE KEY `LICENCIA_MUNICIPAL_PK` (`FUN_CODIGO`,`LM_NUMERO`),
  KEY `COMUNA_LICENCIA_MUNICIPAL_FK` (`COM_CODIGO`),
  KEY `FUNCIONARIO_LICENCIA_MUNICIPAL_FK` (`FUN_CODIGO`),
  CONSTRAINT `FK_COMUNA_LICENCIA_MUNICIPAL` FOREIGN KEY (`COM_CODIGO`) REFERENCES `COMUNA` (`COM_CODIGO`),
  CONSTRAINT `FK_FUNCIONARIO_LICENCIA_MUNICIPAL` FOREIGN KEY (`FUN_CODIGO`) REFERENCES `FUNCIONARIO` (`FUN_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.LICENCIA_SEMEP
CREATE TABLE IF NOT EXISTS `LICENCIA_SEMEP` (
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `LS_FECHA_HABILITACION` date NOT NULL,
  `TEV_CODIGO` int(11) default NULL,
  `LS_FECHA_RENOVACION` date default NULL,
  `LS_OBSERVACIONES` varchar(200) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`FUN_CODIGO`,`LS_FECHA_HABILITACION`),
  KEY `FUN_CODIGO` (`FUN_CODIGO`),
  CONSTRAINT `FK_FUNCIONARIO_LICENCIA_SEMEP` FOREIGN KEY (`FUN_CODIGO`) REFERENCES `FUNCIONARIO` (`FUN_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.LUGAR_REPARACION
CREATE TABLE IF NOT EXISTS `LUGAR_REPARACION` (
  `LREP_CODIGO` int(11) NOT NULL,
  `LREP_DESCRIPCION` varchar(60) collate latin1_spanish_ci default NULL,
  `LREP_ACTIVO` tinyint(4) default NULL,
  PRIMARY KEY  (`LREP_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.MARCA_ARMA
CREATE TABLE IF NOT EXISTS `MARCA_ARMA` (
  `MARM_CODIGO` int(11) NOT NULL,
  `MARM_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`MARM_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.MARCA_VEHICULO
CREATE TABLE IF NOT EXISTS `MARCA_VEHICULO` (
  `MVEH_CODIGO` int(11) NOT NULL,
  `MVEH_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `MVEH_CODIGO_OLD` varchar(10) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`MVEH_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.MARCA_VIDEOCAMARA
CREATE TABLE IF NOT EXISTS `MARCA_VIDEOCAMARA` (
  `MVC_CODIGO` int(11) NOT NULL,
  `MVC_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`MVC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.MARCELO_FECHA
CREATE TABLE IF NOT EXISTS `MARCELO_FECHA` (
  `FECHA` date NOT NULL,
  `FECHA2` date default NULL,
  PRIMARY KEY  (`FECHA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.MATRICULA_FUNCIONARIO
CREATE TABLE IF NOT EXISTS `MATRICULA_FUNCIONARIO` (
  `ID` int(11) NOT NULL auto_increment,
  `RUT` varchar(10) collate latin1_general_ci default NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `PRIMER_NOMBRE` varchar(50) collate latin1_general_ci NOT NULL,
  `SEGUNDO_NOMBRE` varchar(50) collate latin1_general_ci NOT NULL,
  `APELLIDO_PATERNO` varchar(50) collate latin1_general_ci NOT NULL,
  `APELLIDO_MATERNO` varchar(50) collate latin1_general_ci NOT NULL,
  `GRA_CODIGO` int(11) default NULL,
  `ESC_CODIGO` int(11) default NULL,
  `GRADO` varchar(50) collate latin1_general_ci NOT NULL,
  `DOTACION` varchar(50) collate latin1_general_ci NOT NULL,
  `REPARTICION_DEPENDIENTE` varchar(50) collate latin1_general_ci default NULL,
  `ALTA_REPARTICION` varchar(50) collate latin1_general_ci default NULL,
  `NUMERO_CELULAR` int(8) NOT NULL,
  `NUMERO_IP` int(5) NOT NULL,
  `EMAIL` varchar(50) collate latin1_general_ci NOT NULL,
  `IP` varchar(12) collate latin1_general_ci NOT NULL,
  `FECHA` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ACTIVO` int(1) NOT NULL default '1',
  `TIPO_CURSO` varchar(50) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`ID`),
  KEY `ID` USING BTREE (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=8873 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.mcc_proservipol
CREATE TABLE IF NOT EXISTS `mcc_proservipol` (
  `id_auditoria` int(11) NOT NULL auto_increment,
  `tabla` varchar(100) default '',
  `id_alterado` int(11) default NULL,
  `usuario` varchar(35) default '',
  `fecha` datetime default NULL,
  `tipo` varchar(100) default '',
  `id_conexion` varchar(100) default '',
  `usuario_ip` varchar(100) default '',
  `version_bd` varchar(100) default '',
  PRIMARY KEY  (`id_auditoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.member
CREATE TABLE IF NOT EXISTS `member` (
  `id` int(11) NOT NULL auto_increment,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `address` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.MENSAJE
CREATE TABLE IF NOT EXISTS `MENSAJE` (
  `ID` int(11) NOT NULL auto_increment,
  `TITULO` varchar(50) collate latin1_general_ci NOT NULL COMMENT 'titulo para mostar en el mensaje, con formato html',
  `CONTENIDO` text collate latin1_general_ci NOT NULL COMMENT 'contenido del mensaje, sin formato html',
  `CONTENIDO_FORMATEADO` text collate latin1_general_ci NOT NULL COMMENT 'contenido del mensaje, formateado para mostar en la ventana modal',
  `TIEMPO` int(11) default '0' COMMENT 'tiempo para ver mensaje de forma fozada, en seg, 0 para simpre activo boton cerrar',
  `FECHA_INICIO` date NOT NULL,
  `FECHA_TERMINO` date NOT NULL,
  `UNIDAD_VISIBLE` tinytext collate latin1_general_ci COMMENT 'unidades que pueden ver mensaje, NULL para todos',
  `RESUMEN` tinytext collate latin1_general_ci COMMENT 'Resumen corto del informativo',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.MODELO_ARMA
CREATE TABLE IF NOT EXISTS `MODELO_ARMA` (
  `MODARM_CODIGO` int(11) NOT NULL,
  `MARM_CODIGO` int(11) NOT NULL,
  `MODARM_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`MODARM_CODIGO`),
  KEY `MODELO_MARCA_ARMA_FK` (`MARM_CODIGO`),
  CONSTRAINT `FK_MODELO_MARCA_ARMA` FOREIGN KEY (`MARM_CODIGO`) REFERENCES `MARCA_ARMA` (`MARM_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.MODELO_VEHICULO
CREATE TABLE IF NOT EXISTS `MODELO_VEHICULO` (
  `MODVEH_CODIGO` int(11) NOT NULL,
  `MVEH_CODIGO` int(11) default NULL,
  `MODVEH_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `MODVEH_CODIGO_OLD` varchar(10) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`MODVEH_CODIGO`),
  KEY `MARCA_VEHICULO_FK` (`MVEH_CODIGO`),
  CONSTRAINT `FK_MARCA_VEHICULO` FOREIGN KEY (`MVEH_CODIGO`) REFERENCES `MARCA_VEHICULO` (`MVEH_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.MODELO_VIDEOCAMARA
CREATE TABLE IF NOT EXISTS `MODELO_VIDEOCAMARA` (
  `MVC_CODIGO` int(11) default NULL,
  `MODVC_CODIGO` int(11) NOT NULL,
  `MODVC_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`MODVC_CODIGO`),
  KEY `fk_MODELO_VIDEOCAMARA_MARCA_VIDEOCAMARA` (`MVC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.MOVIMIENTO
CREATE TABLE IF NOT EXISTS `MOVIMIENTO` (
  `SOL_CODIGO` int(11) NOT NULL,
  `MOV_CODIGO` int(11) NOT NULL,
  `TMOV_CODIGO` int(11) NOT NULL,
  `TEXTO` text collate latin1_spanish_ci NOT NULL,
  `FUNCIONARIO_IMPLICADO` char(7) character set latin1 collate latin1_general_ci default NULL,
  `ROL_FUNCIONARIO_IMPLICADO` int(11) NOT NULL,
  `FECHA` datetime NOT NULL,
  `FECHA_INICIO` date NOT NULL,
  `FECHA_TERMINO` date default NULL,
  `FUNCIONARIO_DERIBA` char(7) character set latin1 collate latin1_general_ci default NULL,
  `SDEPTO_CODIGO` int(11) default NULL,
  `ARCHIVO` varchar(200) collate latin1_spanish_ci default NULL,
  `VISIBLE` int(11) default NULL,
  PRIMARY KEY  (`SOL_CODIGO`,`MOV_CODIGO`),
  KEY `TIPO_MOV_CODIGO_FK` (`TMOV_CODIGO`),
  KEY `FECHA_INICIO` (`FECHA_INICIO`),
  KEY `FECHA_TERMINO` (`FECHA_TERMINO`),
  KEY `FUN_IMPLICADO` (`FUNCIONARIO_IMPLICADO`),
  KEY `MOVIMIENTO_SECCION` (`SDEPTO_CODIGO`),
  KEY `MOVIMIENTO_VISIBLE` (`VISIBLE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.PASO
CREATE TABLE IF NOT EXISTS `PASO` (
  `FECHA` date default NULL,
  `FUNCIONARIO` char(7) collate latin1_general_ci default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.pasoVistaPersonalAsigando
CREATE TABLE IF NOT EXISTS `pasoVistaPersonalAsigando` (
  `FUNCIONARIO_CODIGO` char(7) default NULL,
  `UNIDAD_CARGO` char(12) default NULL,
  `FECHA` date default NULL,
  `CARGO` varchar(100) default NULL,
  `UNIDAD_AGREGADO` char(12) default NULL,
  `GRA_DESCRIPCION` varchar(60) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.PERSONAL_MOCK
CREATE TABLE IF NOT EXISTS `PERSONAL_MOCK` (
  `PEFBCOD` varchar(7) NOT NULL COMMENT 'Código Funcionario',
  `PEFBRUT` varchar(12) NOT NULL COMMENT 'RUT',
  `PEFBNOM1` varchar(50) NOT NULL COMMENT 'Primer Nombre',
  `PEFBNOM2` varchar(50) default NULL COMMENT 'Segundo Nombre',
  `PEFBAPEP` varchar(50) NOT NULL COMMENT 'Apellido Paterno',
  `PEFBAPEM` varchar(50) NOT NULL COMMENT 'Apellido Materno',
  `PEFBESC` varchar(7) NOT NULL COMMENT 'C',
  `PEFBGRA` varchar(2) NOT NULL COMMENT 'Código Grado',
  `ESC_CODIGO` int(11) default NULL,
  `GRA_CODIGO` int(11) default NULL,
  `GRADO_DESCRIPCION` varchar(100) NOT NULL COMMENT 'Descripción Grado',
  `REPARTICION_DESC` varchar(200) NOT NULL COMMENT 'Dotación/Repartición',
  `REPARTICION_DEP` varchar(200) default NULL COMMENT 'Repartición Dependiente',
  `ALTA_REPARTICION` varchar(200) default NULL COMMENT 'Alta Repartición',
  `PEFBACT` int(11) default '0' COMMENT 'Estado Activo',
  `CREATED_AT` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`PEFBCOD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabla MOCK para simular BD Personal en desarrollo';

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.PROBLEMA
CREATE TABLE IF NOT EXISTS `PROBLEMA` (
  `PROB_CODIGO` int(11) NOT NULL,
  `PROB_DESCRIPCION` varchar(60) collate latin1_spanish_ci NOT NULL,
  `ACTIVO` int(11) default NULL,
  PRIMARY KEY  (`PROB_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.PROCEDENCIA_RECURSO
CREATE TABLE IF NOT EXISTS `PROCEDENCIA_RECURSO` (
  `PREC_CODIGO` int(11) NOT NULL,
  `PREC_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `PREC_CODIGO_OLD` char(5) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`PREC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.RESTRICCION_CONDUCIR_MUNICIPAL
CREATE TABLE IF NOT EXISTS `RESTRICCION_CONDUCIR_MUNICIPAL` (
  `TRE_CODIGO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `LM_NUMERO` int(11) NOT NULL,
  PRIMARY KEY  (`TRE_CODIGO`,`FUN_CODIGO`,`LM_NUMERO`),
  UNIQUE KEY `RETRICCION_CONDUCIR_MUNICIPAL_PK` (`TRE_CODIGO`,`FUN_CODIGO`,`LM_NUMERO`),
  KEY `RETRICCION_CONDUCIR_MUNICIPAL_FK` (`TRE_CODIGO`),
  KEY `RETRICCION_CONDUCIR_MUNICIPAL2_FK` (`FUN_CODIGO`,`LM_NUMERO`),
  CONSTRAINT `FK_RETRICCION_CONDUCIR_MUNICIPAL` FOREIGN KEY (`TRE_CODIGO`) REFERENCES `TIPO_RESTRICCION_CONDUCIR` (`TRE_CODIGO`),
  CONSTRAINT `FK_RETRICCION_CONDUCIR_MUNICIPAL2` FOREIGN KEY (`FUN_CODIGO`, `LM_NUMERO`) REFERENCES `LICENCIA_MUNICIPAL` (`FUN_CODIGO`, `LM_NUMERO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.RESTRICCION_CONDUCIR_SEMEP
CREATE TABLE IF NOT EXISTS `RESTRICCION_CONDUCIR_SEMEP` (
  `TRE_CODIGO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `LS_FECHA_HABILITACION` date NOT NULL,
  PRIMARY KEY  (`TRE_CODIGO`,`FUN_CODIGO`,`LS_FECHA_HABILITACION`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.RP_FUNCIONARIOS_POR_SERVICIO
CREATE TABLE IF NOT EXISTS `RP_FUNCIONARIOS_POR_SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `FECHA` date NOT NULL,
  `CODIGO_INTERVALO` int(11) NOT NULL,
  `CANTIDAD` int(11) default NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`FECHA`,`CODIGO_INTERVALO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.SECCION_TUNIDAD
CREATE TABLE IF NOT EXISTS `SECCION_TUNIDAD` (
  `UNI_CODIGO` int(11) NOT NULL,
  `SEC_CODIGO` int(11) NOT NULL,
  `ACTIVO` smallint(6) default NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`SEC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.SECOM_TRASLADOS
CREATE TABLE IF NOT EXISTS `SECOM_TRASLADOS` (
  `FUN_CODIGO` char(7) collate latin1_general_ci default NULL,
  `UNI_ORIGEN` char(12) collate latin1_general_ci default NULL,
  `UNI_DESTINO` char(12) collate latin1_general_ci default NULL,
  `FECHA_DESTINO` date default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.SERVICIO
CREATE TABLE IF NOT EXISTS `SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `TSERV_CODIGO` int(11) NOT NULL,
  `TEXT_CODIGO` int(11) default NULL,
  `FECHA` date NOT NULL,
  `HORA_INICIO` time default NULL,
  `HORA_TERMINO` time default NULL,
  `DESCRIPCION_OTRO_EXTRAORDINARIO` varchar(100) collate latin1_general_ci default NULL,
  `DESCRIPCION_SERVICIO` text collate latin1_general_ci,
  `UNI_CODIGO_DESTINO` int(11) default NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`),
  KEY `TIPO_SERVICIO_FK` (`TSERV_CODIGO`),
  KEY `TIPO_EXTRAORDINARIO_FK` (`TEXT_CODIGO`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`),
  KEY `FECHA_SERVICIO` (`FECHA`),
  KEY `UNI_CODIGO_DESTINO` (`UNI_CODIGO_DESTINO`),
  CONSTRAINT `FK_TIPO_EXTRAORDINARIO` FOREIGN KEY (`TEXT_CODIGO`) REFERENCES `TIPO_EXTRAORDINARIO` (`TEXT_CODIGO`),
  CONSTRAINT `FK_TIPO_SERVICIO` FOREIGN KEY (`TSERV_CODIGO`) REFERENCES `TIPO_SERVICIO` (`TSERV_CODIGO`),
  CONSTRAINT `FK_UNIDAD_SERVICIO` FOREIGN KEY (`UNI_CODIGO`) REFERENCES `UNIDAD` (`UNI_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.SERVICIOS_CERTIFICADO
CREATE TABLE IF NOT EXISTS `SERVICIOS_CERTIFICADO` (
  `UNI_CODIGO` int(11) NOT NULL default '0',
  `FECHA_SERVICIOS` date NOT NULL default '0000-00-00',
  `FECHA_CERTIFICADO` date default NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci default NULL,
  `HORA_CERTIFICADO` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`UNI_CODIGO`,`FECHA_SERVICIOS`),
  KEY `UNIDAD_FECHA_SERVCIO_IND` (`UNI_CODIGO`,`FECHA_SERVICIOS`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.servicios_certificado_hojaruta
CREATE TABLE IF NOT EXISTS `servicios_certificado_hojaruta` (
  `UNI_CODIGO` int(11) default NULL,
  `CORRELATIVO_SERVICIO` int(11) default NULL,
  `FECHA_SERVICIOS` date default NULL,
  `FECHA_CERTIFICADO` date default NULL,
  `FUN_CODIGO` char(7) collate latin1_spanish_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.SERVICIOS_CERTIFICADO_OIC
CREATE TABLE IF NOT EXISTS `SERVICIOS_CERTIFICADO_OIC` (
  `UNI_CODIGO` int(11) NOT NULL default '0',
  `FECHA_SERVICIOS` date NOT NULL default '0000-00-00',
  `FECHA_CERTIFICADO` date default NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci default NULL,
  `HORA_CERTIFICADO` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`UNI_CODIGO`,`FECHA_SERVICIOS`),
  KEY `UNIDAD_FECHA_SERVCIO_IND` USING BTREE (`UNI_CODIGO`,`FECHA_SERVICIOS`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.SERVICIOS_DESVALIDADOS
CREATE TABLE IF NOT EXISTS `SERVICIOS_DESVALIDADOS` (
  `UNI_CODIGO` int(11) NOT NULL default '0',
  `FECHA_SERVICIOS` date NOT NULL default '0000-00-00',
  `FECHA_CERTIFICADO` date default NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci default NULL,
  `US_DIRECCION_IP` char(15) collate latin1_general_ci NOT NULL,
  `FECHA_DESVALIDACION` date NOT NULL default '0000-00-00',
  `HORA_DESVALIDACION` time NOT NULL default '00:00:00',
  `TDESVALIDACION_CODIGO` int(11) default NULL,
  `FUN_CODIGO_CERTIFICADO` char(7) collate latin1_general_ci default NULL,
  `HORA_CERTIFICACION` time NOT NULL default '00:00:00',
  `TIPO_DESVALIDACION` tinyint(4) default '1' COMMENT 'VALIDARDOR: TITULAR 1 - VALIDADOR OIC: 2',
  PRIMARY KEY  (`UNI_CODIGO`,`FECHA_SERVICIOS`,`FECHA_DESVALIDACION`,`HORA_DESVALIDACION`),
  KEY `servicios_desvalidados_serv_uni` (`UNI_CODIGO`,`FECHA_SERVICIOS`),
  KEY `foreign_key_tipodesvalidacion` (`TDESVALIDACION_CODIGO`),
  CONSTRAINT `SERVICIOS_DESVALIDADOS_ibfk_1` FOREIGN KEY (`TDESVALIDACION_CODIGO`) REFERENCES `TIPO_DESVALIDACION` (`TDESVALIDACION_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.SIMCCAR
CREATE TABLE IF NOT EXISTS `SIMCCAR` (
  `SIM_CODIGO` bigint(20) NOT NULL auto_increment,
  `SIM_SERIE` varchar(60) character set latin1 collate latin1_general_ci default NULL,
  `SIM_TARJETA` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  `SIM_IMEI` varchar(60) character set latin1 collate latin1_general_ci default NULL,
  `UNI_CODIGO` int(11) default NULL,
  `MSIM_CODIGO` varchar(50) collate latin1_spanish_ci default NULL,
  `MODSIM_CODIGO` varchar(50) collate latin1_spanish_ci default NULL,
  `ANNO_FABRICACION` char(4) collate latin1_spanish_ci default NULL,
  `ORIGEN_SIMCCAR` varchar(20) collate latin1_spanish_ci default NULL,
  `VERIFICACION_ESTADO` char(2) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`SIM_CODIGO`)
) ENGINE=InnoDB AUTO_INCREMENT=101527 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.SIMCCAR_SERVICIO
CREATE TABLE IF NOT EXISTS `SIMCCAR_SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `SIM_CODIGO` int(11) NOT NULL default '0',
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`SIM_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.SOLICITUD
CREATE TABLE IF NOT EXISTS `SOLICITUD` (
  `SOL_CODIGO` int(11) NOT NULL auto_increment,
  `UNI_CODIGO` int(11) NOT NULL,
  `PROB_CODIGO` int(11) NOT NULL,
  `SUBP_CODIGO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) character set latin1 collate latin1_general_ci default NULL,
  `SOL_FECHA` datetime NOT NULL,
  `SOL_TEXTO` text collate latin1_spanish_ci NOT NULL,
  `VALOR_IDENTI1` varchar(50) collate latin1_spanish_ci default NULL,
  `VALOR_IDENTI2` varchar(50) collate latin1_spanish_ci default NULL,
  `ETIQUETA_IDENTI1` varchar(30) collate latin1_spanish_ci default NULL,
  `ETIQUETA_IDENTI2` varchar(30) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`SOL_CODIGO`),
  KEY `UNIDAD_SOLICITUD_FK` (`UNI_CODIGO`),
  KEY `PROBLEMA_SOLICITUD_FK` (`PROB_CODIGO`),
  KEY `SUBPROBLEMA_SOLICITUD_FK` (`SUBP_CODIGO`),
  KEY `FECHA_SOLICITUD_FK` (`SOL_FECHA`)
) ENGINE=InnoDB AUTO_INCREMENT=39189 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.SUBPROBLEMA
CREATE TABLE IF NOT EXISTS `SUBPROBLEMA` (
  `PROB_CODIGO` int(11) NOT NULL,
  `SUBP_CODIGO` int(11) NOT NULL,
  `SUBP_DESCRIPCION` varchar(60) collate latin1_spanish_ci NOT NULL,
  `ACTIVO` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TARJETA_COMBUSTIBLE
CREATE TABLE IF NOT EXISTS `TARJETA_COMBUSTIBLE` (
  `TC_CODIGO` int(11) NOT NULL,
  `TC_NRO_TARJETA` varchar(8) collate latin1_general_ci NOT NULL default '',
  `TC_NRO_TARJETA_DV` varchar(1) collate latin1_general_ci NOT NULL default '',
  `COD_VEH` bigint(20) NOT NULL,
  `TC_FECHA_DESDE` date NOT NULL,
  `TC_FECHA_HASTA` date default NULL,
  `TC_ARCHIVO` varchar(30) collate latin1_general_ci default NULL,
  `TC_NRO_TARJETA_VALIDADO` int(1) default '0',
  `TC_FECHA_REGISTRO` datetime default NULL,
  `TC_CODFUNCIONARIO_REGISTRA` varchar(7) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`TC_CODIGO`,`COD_VEH`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TARJETA_COMBUSTIBLE_TEMPORAL
CREATE TABLE IF NOT EXISTS `TARJETA_COMBUSTIBLE_TEMPORAL` (
  `TC_NRO_TARJETA` char(8) collate latin1_general_ci NOT NULL default '',
  `TC_NRO_TARJETA_DV` char(1) collate latin1_general_ci NOT NULL default '',
  `TC_ACTIVA` int(1) NOT NULL default '1',
  `TC_FECHA_CARGA` date NOT NULL,
  PRIMARY KEY  (`TC_NRO_TARJETA`,`TC_NRO_TARJETA_DV`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TA_ELIMINAR_ARMAS
CREATE TABLE IF NOT EXISTS `TA_ELIMINAR_ARMAS` (
  `ID` int(11) NOT NULL,
  `NRO_SERIE` varchar(100) collate latin1_general_ci default NULL,
  `ESTADO` varchar(50) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TA_ELIMINAR_SERVICIOS
CREATE TABLE IF NOT EXISTS `TA_ELIMINAR_SERVICIOS` (
  `ID` int(11) NOT NULL,
  `CODIGO_FUNCIONARIO` varchar(10) collate latin1_general_ci default NULL,
  `FECHA_DESDE` date default NULL,
  `FECHA_HASTA` date default NULL,
  `CODIGO_SERVICIO` int(11) default NULL,
  `ESTADO` varchar(50) collate latin1_general_ci default NULL,
  `VISIBLE` int(1) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TA_INSERTAR_SIMCAR
CREATE TABLE IF NOT EXISTS `TA_INSERTAR_SIMCAR` (
  `ID` int(11) NOT NULL auto_increment,
  `SIM_SERIE` varchar(50) collate latin1_general_ci default NULL,
  `SIM_TARJETA` varchar(50) collate latin1_general_ci default NULL,
  `SIM_IMEI` varchar(50) collate latin1_general_ci default NULL,
  `MSIM_CODIGO` varchar(50) collate latin1_general_ci default NULL,
  `MODSIM_CODIGO` varchar(50) collate latin1_general_ci default NULL,
  `ANNO_FABRICACION` int(10) default NULL,
  `ESTADO` varchar(30) collate latin1_general_ci default NULL,
  `VISIBLE` int(1) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TA_TIPOUNIDAD
CREATE TABLE IF NOT EXISTS `TA_TIPOUNIDAD` (
  `ID` int(10) NOT NULL,
  `TIPO_UNIDAD` varchar(50) collate latin1_general_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_ACCESORIO
CREATE TABLE IF NOT EXISTS `TIPO_ACCESORIO` (
  `TACC_CODIGO` int(11) NOT NULL,
  `TACC_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `TACC_CODIGO_OLD` int(11) default NULL,
  `ORDEN` int(11) default NULL COMMENT 'ORDEN DE LOS ACCESORIOS PARA ASIGNAR AL SERVICIO',
  `ACTIVO` int(1) default NULL,
  PRIMARY KEY  (`TACC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_ANIMAL
CREATE TABLE IF NOT EXISTS `TIPO_ANIMAL` (
  `TANIM_CODIGO` int(11) NOT NULL,
  `TANIM_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `TANIM_CODIGO_OLD` int(11) default NULL,
  PRIMARY KEY  (`TANIM_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_ARMA
CREATE TABLE IF NOT EXISTS `TIPO_ARMA` (
  `TARM_CODIGO` int(11) NOT NULL,
  `TARM_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`TARM_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_CLASIFICACION_CITACION
CREATE TABLE IF NOT EXISTS `TIPO_CLASIFICACION_CITACION` (
  `TCLASIFICACION_CITACION_CODIGO` int(11) NOT NULL,
  `TCLASIFICACION_CITACION_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `TCLASIFICACION_CITACION_ACTIVO` tinyint(4) default NULL,
  PRIMARY KEY  (`TCLASIFICACION_CITACION_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_CLASIFICACION_SEMEP
CREATE TABLE IF NOT EXISTS `TIPO_CLASIFICACION_SEMEP` (
  `TSEM_CODIGO` int(11) NOT NULL,
  `TSEM_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `TSEM_ACTIVO` smallint(6) default NULL,
  PRIMARY KEY  (`TSEM_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_CUARTEL
CREATE TABLE IF NOT EXISTS `TIPO_CUARTEL` (
  `TCU_CODIGO` int(11) NOT NULL,
  `TCU_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`TCU_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_DESVALIDACION
CREATE TABLE IF NOT EXISTS `TIPO_DESVALIDACION` (
  `TDESVALIDACION_CODIGO` int(11) NOT NULL,
  `TDESVALIDACION_DESCRIPCION` varchar(60) collate latin1_spanish_ci NOT NULL,
  `ACTIVO` int(11) default NULL,
  PRIMARY KEY  (`TDESVALIDACION_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_ESCALAFON
CREATE TABLE IF NOT EXISTS `TIPO_ESCALAFON` (
  `TESC_CODIGO` int(11) NOT NULL,
  `TESC_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`TESC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_ESPECIALIDAD_CUARTEL
CREATE TABLE IF NOT EXISTS `TIPO_ESPECIALIDAD_CUARTEL` (
  `TESPC_CODIGO` int(11) NOT NULL,
  `TESPC_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`TESPC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_EVALUACION_SEMEP
CREATE TABLE IF NOT EXISTS `TIPO_EVALUACION_SEMEP` (
  `TEV_CODIGO` int(11) NOT NULL,
  `TEV_DESCRIPCION` varchar(60) collate latin1_general_ci NOT NULL,
  `TEV_ACTIVO` tinyint(4) NOT NULL,
  PRIMARY KEY  (`TEV_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_EXTRAORDINARIO
CREATE TABLE IF NOT EXISTS `TIPO_EXTRAORDINARIO` (
  `TEXT_CODIGO` int(11) NOT NULL,
  `TEXT_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `TEXT_ACTIVO` tinyint(1) default NULL,
  `TEXT_PRIORIDAD` int(11) default NULL,
  PRIMARY KEY  (`TEXT_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_EXTRAORDINARIO_CUARTEL
CREATE TABLE IF NOT EXISTS `TIPO_EXTRAORDINARIO_CUARTEL` (
  `TSERV_CODIGO` int(11) NOT NULL,
  `TCU_CODIGO` int(11) NOT NULL,
  `TESPC_CODIGO` int(11) NOT NULL,
  `GSER_CODIGO` int(11) NOT NULL,
  `TEXT_CODIGO` int(11) NOT NULL,
  `PERMITE_CPR` tinyint(4) default NULL,
  `ACTIVO` tinyint(4) default NULL,
  `ORDEN` int(11) default NULL,
  PRIMARY KEY  (`TSERV_CODIGO`,`TCU_CODIGO`,`TESPC_CODIGO`,`TEXT_CODIGO`,`GSER_CODIGO`),
  KEY `FK_TIPO_EXTRAORDINARIO_CUARTEL_TIPO_SERVICIO_CUARTEL` (`TSERV_CODIGO`,`TCU_CODIGO`,`TESPC_CODIGO`,`GSER_CODIGO`),
  KEY `FK_TIPO_EXTRAORDINARIO_CUARTEL_TIPO_EXTRAORDINARIO` (`TEXT_CODIGO`),
  CONSTRAINT `FK_TIPO_EXTRAORDINARIO_CUARTEL_TIPO_EXTRAORDINARIO` FOREIGN KEY (`TEXT_CODIGO`) REFERENCES `TIPO_EXTRAORDINARIO` (`TEXT_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_TIPO_EXTRAORDINARIO_CUARTEL_TIPO_SERVICIO_CUARTEL` FOREIGN KEY (`TSERV_CODIGO`, `TCU_CODIGO`, `TESPC_CODIGO`, `GSER_CODIGO`) REFERENCES `TIPO_SERVICIO_CUARTEL` (`TSERV_CODIGO`, `TCU_CODIGO`, `TESPC_CODIGO`, `GSER_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_FALLA_VEHICULO
CREATE TABLE IF NOT EXISTS `TIPO_FALLA_VEHICULO` (
  `TFALLA_CODIGO` int(11) NOT NULL,
  `TFALLA_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `TFALLA_TIPO` varchar(2) collate latin1_general_ci default NULL,
  `TFALLA_GRUPO` varchar(20) collate latin1_general_ci default NULL,
  `TFALLA_REP` int(11) default NULL,
  `TFALLA_ACTIVO` tinyint(4) default NULL,
  PRIMARY KEY  (`TFALLA_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_LICENCIA_CONDUCIR
CREATE TABLE IF NOT EXISTS `TIPO_LICENCIA_CONDUCIR` (
  `TLIC_CODIGO` int(11) NOT NULL,
  `TLIC_DESCRIPCION` varchar(20) collate latin1_general_ci default NULL,
  `TLIC_ACTIVO` smallint(6) default NULL,
  PRIMARY KEY  (`TLIC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_MOVIMIENTO
CREATE TABLE IF NOT EXISTS `TIPO_MOVIMIENTO` (
  `TMOV_CODIGO` int(11) NOT NULL,
  `TMOV_DESCRIPCION` varchar(60) collate latin1_spanish_ci NOT NULL,
  `UNIDAD` int(11) NOT NULL,
  `CONTACT` int(11) NOT NULL,
  `INFORMATICA` int(11) NOT NULL,
  `OPU` int(11) NOT NULL,
  `ACTIVO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_RESTRICCION_CONDUCIR
CREATE TABLE IF NOT EXISTS `TIPO_RESTRICCION_CONDUCIR` (
  `TRE_CODIGO` int(11) NOT NULL,
  `TRE_DESCRIPCION` varchar(40) collate latin1_general_ci default NULL,
  `TRE_MUNICIPAL` tinyint(4) default NULL,
  `TRE_SEMEP` tinyint(4) default NULL,
  `TRE_ACTIVO` tinyint(4) default NULL,
  PRIMARY KEY  (`TRE_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_SECCION
CREATE TABLE IF NOT EXISTS `TIPO_SECCION` (
  `SEC_CODIGO` int(11) NOT NULL,
  `SEC_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `ACTIVO` int(11) default NULL,
  PRIMARY KEY  (`SEC_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_SERVICIO
CREATE TABLE IF NOT EXISTS `TIPO_SERVICIO` (
  `TSERV_CODIGO` int(11) NOT NULL,
  `TSERV_DESCRIPCION` varchar(100) collate latin1_general_ci default NULL,
  `TSERV_TIPO` varchar(2) collate latin1_general_ci default NULL,
  `TSERV_TIPO_ANALISIS` varchar(50) collate latin1_general_ci default NULL,
  `TSERV_TIPO_ANALISIS_2` varchar(20) collate latin1_general_ci default NULL,
  `TSERV_TIPO_ANALISIS_3` varchar(50) collate latin1_general_ci default NULL,
  `TSERV_ACTIVO` tinyint(1) default NULL,
  `TSERV_GRUPO` varchar(20) collate latin1_general_ci default NULL,
  `TSERV_ORDEN` smallint(6) default NULL,
  `TSERV_CALIDAD` varchar(20) collate latin1_general_ci default 'ORDINARIO',
  `TSERV_CODIGO_SELIME` char(1) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`TSERV_CODIGO`),
  KEY `TSERV_ORDEN` (`TSERV_ORDEN`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_SERVICIO_CUARTEL
CREATE TABLE IF NOT EXISTS `TIPO_SERVICIO_CUARTEL` (
  `TSERV_CODIGO` int(11) NOT NULL,
  `TCU_CODIGO` int(11) NOT NULL,
  `TESPC_CODIGO` int(11) NOT NULL,
  `GSER_CODIGO` int(11) NOT NULL,
  `PERMITE_CPR` tinyint(4) default NULL,
  `ACTIVO` tinyint(4) default NULL,
  `ORDEN` int(11) default NULL,
  PRIMARY KEY  (`TSERV_CODIGO`,`TCU_CODIGO`,`TESPC_CODIGO`,`GSER_CODIGO`),
  KEY `FK_TIPO_SERVICIO_CUARTEL_TIPO_CUARTEL` (`TCU_CODIGO`),
  KEY `FK_TIPO_SERVICIO_CUARTEL_TIPO_ESPECIALIDAD_CUARTEL` (`TESPC_CODIGO`),
  KEY `FK_TIPO_SERVICIO_CUARTEL_GRUPO_SERVICIO` (`GSER_CODIGO`),
  CONSTRAINT `FK_TIPO_SERVICIO_CUARTEL_GRUPO_SERVICIO` FOREIGN KEY (`GSER_CODIGO`) REFERENCES `GRUPO_SERVICIO` (`GSER_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_TIPO_SERVICIO_CUARTEL_TIPO_CUARTEL` FOREIGN KEY (`TCU_CODIGO`) REFERENCES `TIPO_CUARTEL` (`TCU_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_TIPO_SERVICIO_CUARTEL_TIPO_ESPECIALIDAD_CUARTEL` FOREIGN KEY (`TESPC_CODIGO`) REFERENCES `TIPO_ESPECIALIDAD_CUARTEL` (`TESPC_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_TIPO_SERVICIO_CUARTEL_TIPO_SERVICIO` FOREIGN KEY (`TSERV_CODIGO`) REFERENCES `TIPO_SERVICIO` (`TSERV_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_UNIDAD
CREATE TABLE IF NOT EXISTS `TIPO_UNIDAD` (
  `TUNI_CODIGO` int(11) NOT NULL,
  `TUNI_DESCRIPCIION` varchar(60) collate latin1_spanish_ci default NULL,
  PRIMARY KEY  (`TUNI_CODIGO`),
  UNIQUE KEY `TUNI_CODIGO` (`TUNI_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci PACK_KEYS=0 COMMENT='CONTIENE CATEGORIZACION DE TIPO DE UNIDAD SEGUN METODOLOGIA ';

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_UNIDAD_CARGOS
CREATE TABLE IF NOT EXISTS `TIPO_UNIDAD_CARGOS` (
  `CAR_CODIGO` int(11) NOT NULL,
  `TUNI_CODIGO` int(11) NOT NULL,
  `ACTIVO` tinyint(1) default NULL,
  PRIMARY KEY  (`CAR_CODIGO`,`TUNI_CODIGO`),
  KEY `FK_TIPO_UNIDAD_TIPO_UNIDAD_CARGOS` (`TUNI_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci PACK_KEYS=0;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_USUARIO
CREATE TABLE IF NOT EXISTS `TIPO_USUARIO` (
  `TUS_CODIGO` int(11) NOT NULL,
  `TUS_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `VALIDAR` tinyint(4) default NULL COMMENT 'Permiso para validar y desvalidar servicios',
  `VALIDAR_OIC` tinyint(4) default NULL COMMENT 'Permiso para validar y desvalidar servicios OIC (Oficina de IntegraciÃ³n Comunitaria)',
  `REGISTRAR` tinyint(4) default NULL COMMENT 'Permiso para ingresar, modificar o eliminar servicios, funcionario, vehiculos, etc',
  `CONSULTAR_UNIDAD` tinyint(4) default NULL COMMENT 'Permiso para entrar como otra unidad',
  `CONSULTAR_PERFIL` tinyint(4) default NULL COMMENT 'Permiso para entrar como otro perfil',
  `TUS_ACTIVO` tinyint(4) default NULL,
  PRIMARY KEY  (`TUS_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.TIPO_VEHICULO
CREATE TABLE IF NOT EXISTS `TIPO_VEHICULO` (
  `TVEH_CODIGO` int(11) NOT NULL,
  `TVEH_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `TVEH_CODIGO_OLD` int(11) default NULL,
  `TVEH_CLASIFICACION` varchar(30) collate latin1_general_ci default NULL,
  `TVEH_KM` int(11) NOT NULL default '0' COMMENT 'Determina si se debe agregar kilometraje al ser asignado a un servicio',
  PRIMARY KEY  (`TVEH_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.UNIDAD
CREATE TABLE IF NOT EXISTS `UNIDAD` (
  `UNI_CODIGO` int(11) NOT NULL,
  `UNI_PADRE` int(11) default NULL,
  `UNI_DESCRIPCION` varchar(60) collate latin1_general_ci default NULL,
  `UNI_PLANCUADRANTE` tinyint(4) default '0',
  `UNI_CODIGO_OLD` char(12) collate latin1_general_ci default NULL,
  `UNI_CODIGO_CARABINEROS` char(12) collate latin1_general_ci default NULL,
  `UNI_CODIGO_SIICGE` char(12) collate latin1_general_ci default NULL,
  `UNI_MOSTRAR` tinyint(4) default '1',
  `UNI_SELECCIONABLE` tinyint(4) default '1',
  `UNI_TIPOUNIDAD` int(11) default NULL COMMENT '210 - AVANZADA',
  `UNI_ESPECIALIDAD` int(11) default NULL,
  `UNI_BLOQUEO` tinyint(4) default NULL,
  `UNI_CODIGO_SECOM` char(12) collate latin1_general_ci default NULL,
  `UNI_ORDEN` int(11) default NULL,
  `UNI_PLANCUADRANTE_DIPRES` smallint(6) default '0',
  `UNI_VISUALIZACION_SIICGE` tinyint(4) default '0',
  `UNI_CODIGO_ESPECIALIDAD` int(11) default NULL,
  `UNI_DESCRIPCION_ESPECIALIDAD` varchar(60) collate latin1_general_ci default NULL,
  `UNI_TIPO_UNIDAD` int(11) default NULL,
  `UNI_CONTIENEHIJOS` tinyint(4) default '0',
  `UNI_CAPTURA` tinyint(4) default '0',
  `UNI_CLUSTER` varchar(30) collate latin1_general_ci default NULL,
  `UNI_ACTIVO` tinyint(4) default '0',
  `TUNI_CODIGO` int(11) default NULL COMMENT 'Metodologia categorizacion de unidades: \r\nDOMINIO (0,1,2,3,4,11)\r\nDESCRIPCION EN TABLA TIPO_UNIDAD\r\nEN DWH CAMPO UNI_TIPOESPECIALIDAD',
  `TCU_CODIGO` int(11) default NULL COMMENT 'DETERMINA EL TIPO DE CUARTEL (ZONA, PREFECTURA, COMISARIA .... ETC)',
  `TESPC_CODIGO` int(11) default NULL COMMENT 'DETERMINA LA ESPECIALIDAD DEL CUARTEL (TERRITORIAL, COP, GOPE .... ETC)',
  PRIMARY KEY  (`UNI_CODIGO`),
  KEY `UNIDAD_DEPENDE_DE_FK` (`UNI_PADRE`),
  KEY `IDX_TIPO_UNIDAD` (`TUNI_CODIGO`),
  KEY `FK_UNIDAD_TIPO_CUARTEL` (`TCU_CODIGO`),
  KEY `FK_UNIDAD_TIPO_ESPECIALIDAD_CUARTEL` (`TESPC_CODIGO`),
  CONSTRAINT `FK_UNIDAD_TIPO_CUARTEL` FOREIGN KEY (`TCU_CODIGO`) REFERENCES `TIPO_CUARTEL` (`TCU_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_UNIDAD_TIPO_ESPECIALIDAD_CUARTEL` FOREIGN KEY (`TESPC_CODIGO`) REFERENCES `TIPO_ESPECIALIDAD_CUARTEL` (`TESPC_CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.UNIDAD_CLUSTER_PASO
CREATE TABLE IF NOT EXISTS `UNIDAD_CLUSTER_PASO` (
  `UNIDAD_SIICGE` char(12) collate latin1_general_ci default NULL,
  `CLUSTER` varchar(30) collate latin1_general_ci default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.UNIDAD_CUADRANTE
CREATE TABLE IF NOT EXISTS `UNIDAD_CUADRANTE` (
  `CUADRANTE_CODIGO` int(11) NOT NULL,
  `CUA_CODIGO` int(11) NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `CUA_DEMANDA` decimal(11,2) default NULL,
  `ACTIVO` tinyint(1) default '1',
  PRIMARY KEY  (`CUADRANTE_CODIGO`),
  KEY `UNIDAD_CUADRANTE_FK` (`UNI_CODIGO`),
  KEY `CUADRANTE_UNIDAD_FK` (`CUA_CODIGO`),
  CONSTRAINT `FK_CUADRANTE_UNIDAD` FOREIGN KEY (`CUA_CODIGO`) REFERENCES `CUADRANTE` (`CUA_CODIGO`),
  CONSTRAINT `FK_UNIDAD_CUADRANTE` FOREIGN KEY (`UNI_CODIGO`) REFERENCES `UNIDAD` (`UNI_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.UNIDAD_OOJJ
CREATE TABLE IF NOT EXISTS `UNIDAD_OOJJ` (
  `UNI_CODIGO_OOJJ` int(11) NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `UNI_DESCRIPCION_OOJJ` varchar(60) collate latin1_spanish_ci default NULL,
  `ACTIVO` tinyint(4) default '1',
  `VERIFICADO` int(11) default NULL,
  PRIMARY KEY  (`UNI_CODIGO_OOJJ`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AVG_ROW_LENGTH=64;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.UNIDAD_PERSONAL
CREATE TABLE IF NOT EXISTS `UNIDAD_PERSONAL` (
  `UNI_PERSONAL` char(12) collate latin1_spanish_ci NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `ACTIVO` tinyint(4) default '1',
  `VERIFICADO` int(11) default NULL,
  PRIMARY KEY  (`UNI_PERSONAL`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AVG_ROW_LENGTH=64;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.UNIDAD_SERVICIO
CREATE TABLE IF NOT EXISTS `UNIDAD_SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `UNIDAD_SERVICIO` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.USUARIO
CREATE TABLE IF NOT EXISTS `USUARIO` (
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `UNI_CODIGO` int(11) NOT NULL,
  `US_LOGIN` varchar(10) collate latin1_general_ci NOT NULL,
  `US_RUT` varchar(12) default NULL,
  `US_PASSWORD` char(10) collate latin1_general_ci NOT NULL,
  `TUS_CODIGO` int(11) default NULL,
  `US_TOKEN` varchar(512) default NULL,
  `US_TOKEN_EXPIRA` datetime default NULL,
  `US_FECHACREACION` date NOT NULL,
  `US_FECHAMODIFICACION` date default NULL,
  `US_ACTIVO` int(1) default NULL,
  PRIMARY KEY  (`FUN_CODIGO`),
  KEY `UNIDAD_USUARIO_FK` (`UNI_CODIGO`),
  KEY `TIPO_USUARIO_FK` (`TUS_CODIGO`),
  KEY `LOGIN_USUARIO_FK` (`US_LOGIN`),
  CONSTRAINT `FK_FUNCIONARIO_USUARIO` FOREIGN KEY (`FUN_CODIGO`) REFERENCES `FUNCIONARIO` (`FUN_CODIGO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_TIPO_USUARIO` FOREIGN KEY (`TUS_CODIGO`) REFERENCES `TIPO_USUARIO` (`TUS_CODIGO`),
  CONSTRAINT `FK_UNIDAD_USUARIO` FOREIGN KEY (`UNI_CODIGO`) REFERENCES `UNIDAD` (`UNI_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.VEHICULO
CREATE TABLE IF NOT EXISTS `VEHICULO` (
  `VEH_CODIGO` bigint(20) NOT NULL auto_increment,
  `TVEH_CODIGO` int(11) default NULL,
  `PREC_CODIGO` int(11) default NULL,
  `VEH_BCU` varchar(20) collate latin1_general_ci default NULL,
  `VEH_SAP` bigint(20) default NULL,
  `UNI_CODIGO` int(11) default NULL,
  `MVEH_CODIGO` int(11) default NULL,
  `MODVEH_CODIGO` int(11) default NULL,
  `VEH_PATENTE` varchar(10) collate latin1_general_ci default NULL,
  `VEH_NUMEROINSITUCIONAL` varchar(20) collate latin1_general_ci default NULL,
  `ANNO_FABRICACION` int(11) default NULL,
  `VALIDA_ANNO_FABRICACION` tinyint(4) default '0',
  `VEH_SIGLA_INSTITUCIONAL` varchar(10) collate latin1_general_ci default NULL,
  `VEH_PPU` varchar(10) collate latin1_general_ci default NULL,
  `VEH_COD_ACTIVO_SAP` bigint(20) default NULL,
  `VEH_COD_EQUIPO_SAP` bigint(20) default NULL,
  `VEH_HOMOLOGADO` tinyint(1) default '0',
  `VEH_CODIGO_ANTERIOR` bigint(20) default NULL,
  PRIMARY KEY  (`VEH_CODIGO`),
  KEY `TIPO_DE_VEHICULO_FK` (`TVEH_CODIGO`),
  KEY `UNIDAD_VEHICULO_FK` (`UNI_CODIGO`),
  KEY `PROCEDENCIA_VEHICULO_FK` (`PREC_CODIGO`),
  KEY `MODELO_VEHICULO` (`MODVEH_CODIGO`),
  KEY `MVEH_CODIGO` (`MVEH_CODIGO`),
  KEY `VEH_BCU` (`VEH_BCU`),
  KEY `VEH_BCU_2` (`VEH_BCU`),
  CONSTRAINT `FK_MODELO_VEHICULO` FOREIGN KEY (`MODVEH_CODIGO`) REFERENCES `MODELO_VEHICULO` (`MODVEH_CODIGO`),
  CONSTRAINT `FK_PROCEDENCIA_VEHICULO` FOREIGN KEY (`PREC_CODIGO`) REFERENCES `PROCEDENCIA_RECURSO` (`PREC_CODIGO`),
  CONSTRAINT `FK_TIPO_DE_VEHICULO` FOREIGN KEY (`TVEH_CODIGO`) REFERENCES `TIPO_VEHICULO` (`TVEH_CODIGO`),
  CONSTRAINT `FK_UNIDAD_VEHICULO` FOREIGN KEY (`UNI_CODIGO`) REFERENCES `UNIDAD` (`UNI_CODIGO`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25469 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.VEHICULO_AUTORIZADO_SEMEP
CREATE TABLE IF NOT EXISTS `VEHICULO_AUTORIZADO_SEMEP` (
  `TSEM_CODIGO` int(11) NOT NULL,
  `FUN_CODIGO` char(7) collate latin1_general_ci NOT NULL,
  `LS_FECHA_HABILITACION` date NOT NULL,
  PRIMARY KEY  (`TSEM_CODIGO`,`FUN_CODIGO`,`LS_FECHA_HABILITACION`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.VEHICULO_HOMOLOGACION
CREATE TABLE IF NOT EXISTS `VEHICULO_HOMOLOGACION` (
  `O` int(11) default NULL,
  `DESTACAMENTO` varchar(100) character set utf8 collate utf8_bin default NULL,
  `TIPO_VEHICULO` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  `CODIGO_TIPO_VEHICULO` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  `PROCEDENCIA` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  `CODIGO_PROCEDENCIA` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  `MARCA` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  `MODELO` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  `ANO_FABRICACION` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  `SIGLA` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  `PATENTE` varchar(100) collate utf8_spanish2_ci default NULL,
  `COD_EQUIPO_SAP` varchar(100) character set latin1 collate latin1_general_ci default NULL,
  `COD_ACTIVO_SAP` varchar(100) character set latin1 collate latin1_general_ci default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.VEHICULO_SERVICIO
CREATE TABLE IF NOT EXISTS `VEHICULO_SERVICIO` (
  `UNI_CODIGO` int(11) NOT NULL,
  `CORRELATIVO_SERVICIO` int(11) NOT NULL,
  `VEH_CODIGO` bigint(20) NOT NULL,
  `KM_INICIAL` int(11) default NULL,
  `KM_FINAL` int(11) default NULL,
  PRIMARY KEY  (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`,`VEH_CODIGO`),
  KEY `VEHICULO_SERVICIO_FK` (`VEH_CODIGO`),
  KEY `UNI_CODIGO` (`UNI_CODIGO`,`CORRELATIVO_SERVICIO`),
  CONSTRAINT `FK_VEHICULO_ASIGNADO` FOREIGN KEY (`VEH_CODIGO`) REFERENCES `VEHICULO` (`VEH_CODIGO`) ON UPDATE CASCADE,
  CONSTRAINT `FK_VEHICULO_SERVICIO` FOREIGN KEY (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`) REFERENCES `SERVICIO` (`UNI_CODIGO`, `CORRELATIVO_SERVICIO`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para vista proservipol_test.VEH_DISPONIBLES
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VEH_DISPONIBLES` (
	`UNI_CODIGO` INT(11) NULL,
	`FECHA` DATE NOT NULL,
	`TVEH_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`VEH_PATENTE` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`EST_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`VEH_CODIGO` BIGINT(20) NOT NULL
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VEH_SERVICIO
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VEH_SERVICIO` (
	`TSERV_CODIGO` INT(11) NOT NULL,
	`FECHA` DATE NOT NULL,
	`VEH_CODIGO` BIGINT(20) NOT NULL
) ENGINE=MyISAM;

-- Volcando estructura para tabla proservipol_test.VIDEOCAMARA
CREATE TABLE IF NOT EXISTS `VIDEOCAMARA` (
  `VC_CODIGO` int(11) NOT NULL auto_increment,
  `MVC_CODIGO` int(11) default NULL,
  `MODVC_CODIGO` int(11) default NULL,
  `PREC_CODIGO` int(11) default NULL,
  `UNI_CODIGO` int(11) default NULL,
  `VC_COD_ACTIVO_SAP` bigint(20) default NULL,
  `VC_COD_EQUIPO_SAP` bigint(20) default NULL,
  `VC_NRO_SERIE` varchar(45) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`VC_CODIGO`),
  KEY `fk_VIDEOCAMARA_MODELO_VIDEOCAMARA` (`MODVC_CODIGO`),
  KEY `fk_VIDEOCAMARA_UNIDAD` (`UNI_CODIGO`),
  KEY `fk_VIDEOCAMARA_MARCA_VIDEOCAMARA` (`MVC_CODIGO`),
  KEY `fk_VIDEOCAMARA_PROCEDENCIA_RECURSO` (`PREC_CODIGO`),
  CONSTRAINT `fk_VIDEOCAMARA_MARCA_VIDEOCAMARA` FOREIGN KEY (`MVC_CODIGO`) REFERENCES `MARCA_VIDEOCAMARA` (`MVC_CODIGO`),
  CONSTRAINT `fk_VIDEOCAMARA_MODELO_VIDEOCAMARA` FOREIGN KEY (`MODVC_CODIGO`) REFERENCES `MODELO_VIDEOCAMARA` (`MODVC_CODIGO`),
  CONSTRAINT `fk_VIDEOCAMARA_PROCEDENCIA_RECURSO` FOREIGN KEY (`PREC_CODIGO`) REFERENCES `PROCEDENCIA_RECURSO` (`PREC_CODIGO`),
  CONSTRAINT `fk_VIDEOCAMARA_UNIDAD` FOREIGN KEY (`UNI_CODIGO`) REFERENCES `UNIDAD` (`UNI_CODIGO`)
) ENGINE=InnoDB AUTO_INCREMENT=4242 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.vistaTemporal
CREATE TABLE IF NOT EXISTS `vistaTemporal` (
  `COD_UNIDAD` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para vista proservipol_test.VISTA_ARBOL_UNIDADES
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_ARBOL_UNIDADES` (
	`ZONA_CODIGO` BIGINT(11) NOT NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_orden` BIGINT(11) NULL,
	`PREFECTURA_CODIGO` BIGINT(11) NOT NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`COMISARIA_CODIGO` BIGINT(11) NOT NULL,
	`COMISARIA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_CODIGO` INT(11) NOT NULL,
	`UNI_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_TIPOUNIDAD` INT(11) NULL COMMENT '210 - AVANZADA'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_ARBOL_UNIDADES_FFEE
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_ARBOL_UNIDADES_FFEE` (
	`ZONA_CODIGO` BIGINT(11) NOT NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_TIPOUNIDAD` BIGINT(11) NULL,
	`ZONA_ORDEN` BIGINT(11) NULL,
	`PREFECTURA_CODIGO` BIGINT(11) NOT NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`PREFECTURA_TIPOUNIDAD` BIGINT(11) NULL,
	`COMISARIA_CODIGO` BIGINT(11) NOT NULL,
	`COMISARIA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`COMISARIA_TIPOUNIDAD` BIGINT(11) NULL,
	`UNI_CODIGO` INT(11) NOT NULL,
	`UNI_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_TIPOUNIDAD` INT(11) NULL COMMENT '210 - AVANZADA'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_ARBOL_UNIDADES_FRONTERAS
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_ARBOL_UNIDADES_FRONTERAS` (
	`ZONA_CODIGO` BIGINT(11) NOT NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_orden` BIGINT(11) NULL,
	`PREFECTURA_CODIGO` BIGINT(11) NOT NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`COMISARIA_CODIGO` BIGINT(11) NOT NULL,
	`COMISARIA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_CODIGO` INT(11) NOT NULL,
	`UNI_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_TIPOUNIDAD` INT(11) NULL COMMENT '210 - AVANZADA'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_ARBOL_UNIDADES_NACIONAL
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_ARBOL_UNIDADES_NACIONAL` (
	`ZONA_CODIGO` BIGINT(11) NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_TIPOUNIDAD` BIGINT(11) NULL,
	`ZONA_ORDEN` BIGINT(11) NULL,
	`PREFECTURA_CODIGO` BIGINT(11) NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`PREFECTURA_TIPOUNIDAD` BIGINT(11) NULL,
	`PREFECTURA_ESPECIALIDAD` BIGINT(11) NULL,
	`DEPENDIENTE_CODIGO` BIGINT(11) NULL,
	`DEPENDIENTE_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`DEPENDIENTE_TIPOUNIDAD` BIGINT(11) NULL,
	`DEPENDIENTE_ESPECIALIDAD` BIGINT(11) NULL,
	`COMISARIA_CODIGO` BIGINT(11) NULL,
	`COMISARIA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`COMISARIA_TIPOUNIDAD` BIGINT(11) NULL,
	`COMISARIA_ESPECIALIDAD` BIGINT(11) NULL,
	`UNI_CODIGO` INT(11) NOT NULL,
	`UNI_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_TIPOUNIDAD` BIGINT(11) NULL,
	`UNI_CODIGO_ESPECIALIDAD` INT(11) NULL,
	`UNI_ESPECIALIDAD` INT(11) NULL,
	`UNI_PLANCUADRANTE` TINYINT(4) NULL,
	`UNI_CAPTURA` TINYINT(4) NULL,
	`UNI_ACTIVO` TINYINT(4) NULL,
	`TUNI_CODIGO` INT(11) NULL COMMENT 'Metodologia categorizacion de unidades: \r\nDOMINIO (0,1,2,3,4,11)\r\nDESCRIPCION EN TABLA TIPO_UNIDAD\r\nEN DWH CAMPO UNI_TIPOESPECIALIDAD'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_ARBOL_UNIDADES_NACIONAL_CLUSTER
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_ARBOL_UNIDADES_NACIONAL_CLUSTER` (
	`ZONA_CODIGO` BIGINT(11) NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_TIPOUNIDAD` BIGINT(11) NULL,
	`ZONA_ORDEN` BIGINT(11) NULL,
	`PREFECTURA_CODIGO` BIGINT(11) NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`PREFECTURA_TIPOUNIDAD` BIGINT(11) NULL,
	`PREFECTURA_ESPECIALIDAD` BIGINT(11) NULL,
	`DEPENDIENTE_CODIGO` BIGINT(11) NULL,
	`DEPENDIENTE_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`DEPENDIENTE_TIPOUNIDAD` BIGINT(11) NULL,
	`DEPENDIENTE_ESPECIALIDAD` BIGINT(11) NULL,
	`COMISARIA_CODIGO` BIGINT(11) NULL,
	`COMISARIA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`COMISARIA_TIPOUNIDAD` BIGINT(11) NULL,
	`COMISARIA_ESPECIALIDAD` BIGINT(11) NULL,
	`UNI_CODIGO` INT(11) NOT NULL,
	`UNI_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_TIPOUNIDAD` INT(11) NULL COMMENT '210 - AVANZADA',
	`UNI_CODIGO_ESPECIALIDAD` INT(11) NULL,
	`UNI_ESPECIALIDAD` INT(11) NULL,
	`UNI_PLANCUADRANTE` TINYINT(4) NULL,
	`UNI_CAPTURA` TINYINT(4) NULL,
	`UNI_CLUSTER` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_ACTIVO` TINYINT(4) NULL
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS` (
	`ZONA_CODIGO` BIGINT(11) NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_TIPOUNIDAD` BIGINT(11) NULL,
	`ZONA_ORDEN` BIGINT(11) NULL,
	`PREFECTURA_CODIGO` BIGINT(11) NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`PREFECTURA_TIPOUNIDAD` BIGINT(11) NULL,
	`PREFECTURA_ESPECIALIDAD` BIGINT(11) NULL,
	`DEPENDIENTE_CODIGO` BIGINT(11) NULL,
	`DEPENDIENTE_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`DEPENDIENTE_TIPOUNIDAD` BIGINT(11) NULL,
	`DEPENDIENTE_ESPECIALIDAD` BIGINT(11) NULL,
	`COMISARIA_CODIGO` BIGINT(11) NULL,
	`COMISARIA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`COMISARIA_TIPOUNIDAD` BIGINT(11) NULL,
	`COMISARIA_ESPECIALIDAD` BIGINT(11) NULL,
	`UNI_CODIGO` INT(11) NOT NULL,
	`UNI_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_TIPOUNIDAD` INT(11) NULL COMMENT '210 - AVANZADA',
	`UNI_CODIGO_ESPECIALIDAD` INT(11) NULL,
	`UNI_ESPECIALIDAD` INT(11) NULL,
	`UNI_PLANCUADRANTE` TINYINT(4) NULL,
	`UNI_CAPTURA` TINYINT(4) NULL,
	`TUNI_CODIGO` INT(11) NULL COMMENT 'Metodologia categorizacion de unidades: \r\nDOMINIO (0,1,2,3,4,11)\r\nDESCRIPCION EN TABLA TIPO_UNIDAD\r\nEN DWH CAMPO UNI_TIPOESPECIALIDAD'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS2
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS2` (
	`ZONA_CODIGO` BIGINT(11) NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_TIPOUNIDAD` BIGINT(11) NULL,
	`ZONA_ORDEN` BIGINT(11) NULL,
	`PREFECTURA_CODIGO` BIGINT(11) NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`PREFECTURA_TIPOUNIDAD` BIGINT(11) NULL,
	`PREFECTURA_ESPECIALIDAD` BIGINT(11) NULL,
	`DEPENDIENTE_CODIGO` BIGINT(11) NULL,
	`DEPENDIENTE_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`DEPENDIENTE_TIPOUNIDAD` BIGINT(11) NULL,
	`DEPENDIENTE_ESPECIALIDAD` BIGINT(11) NULL,
	`COMISARIA_CODIGO` BIGINT(11) NULL,
	`COMISARIA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`COMISARIA_TIPOUNIDAD` BIGINT(11) NULL,
	`COMISARIA_ESPECIALIDAD` BIGINT(11) NULL,
	`UNI_CODIGO` INT(11) NOT NULL,
	`UNI_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_TIPOUNIDAD` INT(11) NULL COMMENT '210 - AVANZADA',
	`UNI_CODIGO_ESPECIALIDAD` INT(11) NULL,
	`UNI_ESPECIALIDAD` INT(11) NULL,
	`UNI_PLANCUADRANTE` TINYINT(4) NULL,
	`UNI_CAPTURA` TINYINT(4) NULL,
	`TUNI_CODIGO` INT(11) NULL COMMENT 'Metodologia categorizacion de unidades: \r\nDOMINIO (0,1,2,3,4,11)\r\nDESCRIPCION EN TABLA TIPO_UNIDAD\r\nEN DWH CAMPO UNI_TIPOESPECIALIDAD'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_ARBOL_UNIDADES_TRANSITO
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_ARBOL_UNIDADES_TRANSITO` (
	`ZONA_CODIGO` BIGINT(11) NOT NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_TIPOUNIDAD` BIGINT(11) NULL,
	`ZONA_orden` BIGINT(11) NULL,
	`PREFECTURA_CODIGO` BIGINT(11) NOT NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`PREFECTURA_TIPOUNIDAD` BIGINT(11) NULL,
	`COMISARIA_CODIGO` BIGINT(11) NOT NULL,
	`COMISARIA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`COMISARIA_TIPOUNIDAD` BIGINT(11) NULL,
	`UNI_CODIGO` INT(11) NOT NULL,
	`UNI_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_TIPOUNIDAD` INT(11) NULL COMMENT '210 - AVANZADA'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_FECHA_ULTIMO_SERV_POR_VEH
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_FECHA_ULTIMO_SERV_POR_VEH` (
	`VEH_PATENTE` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`VEH_CODIGO` BIGINT(20) NOT NULL,
	`FIELD_1` DATE NULL
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_PASO_COLACION_PARA_15_UNDADES
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_PASO_COLACION_PARA_15_UNDADES` (
	`UNIDAD_SERVICIO` CHAR(12) NULL COLLATE 'latin1_general_ci',
	`FECHA_SERVICIO` DATE NOT NULL,
	`FUNCIONARIO_SERVICIO` CHAR(7) NOT NULL COLLATE 'latin1_general_ci',
	`TIEMPO_COLACION` DECIMAL(25,0) NULL
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_PASO_SERV_RRCC_15UNIDADES
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_PASO_SERV_RRCC_15UNIDADES` (
	`UNI_CODIGO` CHAR(12) NULL COLLATE 'latin1_general_ci',
	`UNI_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`FECHA` DATE NOT NULL,
	`FUN_CODIGO` CHAR(7) NOT NULL COLLATE 'latin1_general_ci',
	`FUN_APELLIDOPATERNO` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`FUN_APELLIDOMATERNO` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`FUN_NOMBRE` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`FUN_NOMBRE2` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`TSERV_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`HORA_INICIO` TIME NULL,
	`HORA_TERMINO` TIME NULL,
	`TIEMPO` DECIMAL(15,4) NULL,
	`TVEH_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`VEH_PATENTE` VARCHAR(1) NULL COLLATE 'latin1_general_ci'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_PERSONAL_ASIGNADO_POR_DIA
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_PERSONAL_ASIGNADO_POR_DIA` (
	`FUNCIONARIO_CODIGO` CHAR(7) NOT NULL COLLATE 'latin1_general_ci',
	`UNI_CODIGO` INT(11) NULL,
	`UNIDAD_CARGO` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`FECHA` DATE NOT NULL,
	`CARGO` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNIDAD_AGREGADO` VARCHAR(1) NULL COLLATE 'latin1_general_ci'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_PERSONAL_POR_CARGO
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_PERSONAL_POR_CARGO` (
	`ZONA_CODIGO` BIGINT(11) NOT NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`PREFECTURA_CODIGO` BIGINT(11) NOT NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNIDAD_CODIGO` BIGINT(11) NOT NULL,
	`UNIDAD_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_CODIGO` INT(11) NOT NULL,
	`UNI_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`CAR_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`GRA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`CANTIDAD` BIGINT(21) NOT NULL
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_PERSONAL_POR_SERVICIO
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_PERSONAL_POR_SERVICIO` (
	`ZONA_CODIGO` BIGINT(11) NOT NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`PREFECTURA_CODIGO` BIGINT(11) NOT NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNIDAD_CODIGO` BIGINT(11) NOT NULL,
	`UNIDAD_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`DESTACAMENTO_CODIGO` INT(11) NOT NULL,
	`DESTACAMENTO_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`FECHA` DATE NOT NULL,
	`TSERV_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`CORRELATIVO_SERVICIO` INT(11) NOT NULL,
	`CANTIDAD` BIGINT(21) NOT NULL
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_PERSONAL_SERVICIO_POR_DIA
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_PERSONAL_SERVICIO_POR_DIA` (
	`FUN_CODIGO` CHAR(7) NOT NULL COLLATE 'latin1_general_ci',
	`FECHA` DATE NOT NULL,
	`TIPO_SERVICIO` VARCHAR(1) NULL COLLATE 'latin1_general_ci'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_UNIDADES
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_UNIDADES` (
	`DIRECCION_CODIGO` BIGINT(11) NULL,
	`DIRECCION_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_CODIGO` BIGINT(11) NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`PREFECTURA_CODIGO` BIGINT(11) NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`COMISARIA_CODIGO` BIGINT(11) NULL,
	`COMISARIA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`DESTACAMENTO_CODIGO` INT(11) NULL,
	`DESTACAMENTO_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci'
) ENGINE=MyISAM;

-- Volcando estructura para vista proservipol_test.VISTA_UNIDADES_2
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `VISTA_UNIDADES_2` (
	`DESTACAMENTO_CODIGO` INT(11) NOT NULL,
	`DESTACAMENTO_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`UNI_TIPOUNIDAD` INT(11) NULL COMMENT '210 - AVANZADA',
	`COD_SIICGE` CHAR(12) NULL COLLATE 'latin1_general_ci',
	`COMISARIA_CODIGO` BIGINT(11) NOT NULL,
	`COMISARIA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`PREFECTURA_CODIGO` BIGINT(11) NOT NULL,
	`PREFECTURA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_CODIGO` BIGINT(11) NOT NULL,
	`ZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`ZONA_ORDEN` BIGINT(11) NULL,
	`SUPERZONA_CODIGO` BIGINT(11) NOT NULL,
	`SUPERZONA_DESCRIPCION` VARCHAR(1) NULL COLLATE 'latin1_general_ci',
	`NACIONAL_CODIGO` VARCHAR(1) NOT NULL COLLATE 'latin1_swedish_ci',
	`NACIONAL_DESCRIPCION` VARCHAR(1) NOT NULL COLLATE 'latin1_swedish_ci'
) ENGINE=MyISAM;

-- Volcando estructura para tabla proservipol_test.VISTA_UNIDADES_4
CREATE TABLE IF NOT EXISTS `VISTA_UNIDADES_4` (
  `DIRECCION_CODIGO` bigint(11) default NULL,
  `DIRECCION_DESCRIPCION` varchar(60) default NULL,
  `ZONA_CODIGO` bigint(11) default NULL,
  `ZONA_DESCRIPCION` varchar(60) default NULL,
  `PREFECTURA_CODIGO` bigint(11) default NULL,
  `PREFECTURA_DESCRIPCION` varchar(60) default NULL,
  `COMISARIA_CODIGO` bigint(11) default NULL,
  `COMISARIA_DESCRIPCION` varchar(60) default NULL,
  `DESTACAMENTO_CODIGO` int(11) default NULL,
  `DESTACAMENTO_DESCRIPCION` varchar(60) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.VISTA_USUARIOS
CREATE TABLE IF NOT EXISTS `VISTA_USUARIOS` (
  `ZONA_CODIGO` bigint(11) default NULL,
  `ZONA_DESCRIPCION` varchar(60) default NULL,
  `PREFECTURA_CODIGO` bigint(11) default NULL,
  `PREFECTURA_DESCRIPCION` varchar(60) default NULL,
  `UNIDAD_CODIGO` bigint(11) default NULL,
  `UNIDAD_DESCRIPCION` varchar(60) default NULL,
  `UNI_CODIGO` int(11) default NULL,
  `UNI_DESCRIPCION` varchar(60) default NULL,
  `FUN_CODIGO` char(7) default NULL,
  `FUN_APELLIDOPATERNO` varchar(30) default NULL,
  `FUN_APELLIDOMATERNO` varchar(30) default NULL,
  `FUN_NOMBRE` varchar(30) default NULL,
  `GRA_DESCRIPCION` varchar(60) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.VISTA_VEHICULO_ASIGNADO_POR_DIA
CREATE TABLE IF NOT EXISTS `VISTA_VEHICULO_ASIGNADO_POR_DIA` (
  `VEH_CODIGO` bigint(20) default NULL,
  `UNI_CODIGO` int(11) default NULL,
  `UNI_DESCRIPCION` varchar(60) default NULL,
  `FECHA` date default NULL,
  `TVEH_DESCRIPCION` varchar(60) default NULL,
  `VEH_PATENTE` varchar(10) default NULL,
  `EST_DESCRIPCION` varchar(60) default NULL,
  `UNIDAD_AGREGADO` varchar(60) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.VISTA_VEHICULO_SERVICIO_POR_DIA
CREATE TABLE IF NOT EXISTS `VISTA_VEHICULO_SERVICIO_POR_DIA` (
  `VEH_CODIGO` bigint(20) default NULL,
  `FECHA` date default NULL,
  `TIPO_SERVICIO` varchar(100) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.vw_servicios
CREATE TABLE IF NOT EXISTS `vw_servicios` (
  `ZONA` varchar(60) default NULL,
  `PREFECTURA` varchar(60) default NULL,
  `COMISARIA` varchar(60) default NULL,
  `DESTACAMENTO` varchar(60) default NULL,
  `CODIGO_FUNC` char(7) default NULL,
  `RUT_FUNC` varchar(10) default NULL,
  `GRADO` varchar(60) default NULL,
  `NOMBRE_COMPLETO` varchar(123) default NULL,
  `FECHA` date default NULL,
  `UNI_CODIGO` int(11) default NULL,
  `CORRELATIVO SERVICIO` int(11) default NULL,
  `CODIGO_SERVICIO` int(11) default NULL,
  `SERVICIO_REALIZADO` varchar(100) default NULL,
  `UCASE(SERVICIO.DESCRIPCION_SERVICIO)` longtext,
  `HRA_INICIO` time default NULL,
  `HRA_TERMINO` time default NULL,
  `DESCRIPCION_SERVICIO` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.V_CANTIDAD_SERV_POR_FUNCIONARIO
CREATE TABLE IF NOT EXISTS `V_CANTIDAD_SERV_POR_FUNCIONARIO` (
  `UNI_CODIGO` int(11) default NULL,
  `FUN_CODIGO` char(7) default NULL,
  `FECHA` date default NULL,
  `CANTIDAD_SERVICIOS` bigint(21) default NULL,
  `TIEMPO` decimal(37,4) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.V_SERVICIOS_POR_FUNC_POR_DIA
CREATE TABLE IF NOT EXISTS `V_SERVICIOS_POR_FUNC_POR_DIA` (
  `UNI_CODIGO` int(11) default NULL,
  `FUN_CODIGO` char(7) default NULL,
  `FECHA` date default NULL,
  `TIPO_SERVICIO` varchar(100) default NULL,
  `HORA_INICIO` time default NULL,
  `HORA_TERMINO` time default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla proservipol_test.Z_VEHICULO_CARGA
CREATE TABLE IF NOT EXISTS `Z_VEHICULO_CARGA` (
  `VEH_CODIGO` bigint(20) NOT NULL auto_increment,
  `TVEH_CODIGO` int(11) NOT NULL,
  `PREC_CODIGO` char(5) collate latin1_general_ci default NULL,
  `VEH_BCU` char(11) collate latin1_general_ci default NULL,
  `UNI_CODIGO` char(12) collate latin1_general_ci default NULL,
  `MVEH_CODIGO` char(10) collate latin1_general_ci default NULL,
  `MODVEH_CODIGO` char(10) collate latin1_general_ci default NULL,
  `VEH_PATENTE` varchar(7) collate latin1_general_ci default NULL,
  `VEH_NUMEROINSITUCIONAL` char(9) collate latin1_general_ci default NULL,
  `VEH_CODIGOANTERIOR` bigint(20) default NULL,
  PRIMARY KEY  (`VEH_CODIGO`),
  UNIQUE KEY `VEH_BCU` (`VEH_BCU`)
) ENGINE=InnoDB AUTO_INCREMENT=401 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- La exportación de datos fue deseleccionada.

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VEH_DISPONIBLES`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VEH_DISPONIBLES` AS select `ESTADO_VEHICULO`.`UNI_CODIGO` AS `UNI_CODIGO`,`MARCELO_FECHA`.`FECHA` AS `FECHA`,`TIPO_VEHICULO`.`TVEH_DESCRIPCION` AS `TVEH_DESCRIPCION`,`VEHICULO`.`VEH_PATENTE` AS `VEH_PATENTE`,`ESTADO`.`EST_DESCRIPCION` AS `EST_DESCRIPCION`,`VEHICULO`.`VEH_CODIGO` AS `VEH_CODIGO` from (((((`ESTADO_VEHICULO` join `ESTADO` on((`ESTADO_VEHICULO`.`EST_CODIGO` = `ESTADO`.`EST_CODIGO`))) join `VEHICULO` on((`ESTADO_VEHICULO`.`VEH_CODIGO` = `VEHICULO`.`VEH_CODIGO`))) join `MARCELO_FECHA` on(((`ESTADO_VEHICULO`.`FECHA_DESDE` <= `MARCELO_FECHA`.`FECHA`) and ((`ESTADO_VEHICULO`.`FECHA_HASTA` > `MARCELO_FECHA`.`FECHA`) or isnull(`ESTADO_VEHICULO`.`FECHA_HASTA`))))) join `TIPO_VEHICULO` on((`VEHICULO`.`TVEH_CODIGO` = `TIPO_VEHICULO`.`TVEH_CODIGO`))) join `UNIDAD` on((`ESTADO_VEHICULO`.`UNI_CODIGO` = `UNIDAD`.`UNI_CODIGO`))) where ((`MARCELO_FECHA`.`FECHA` between _latin1'20151201' and _latin1'20151201') and (`UNIDAD`.`UNI_TIPOUNIDAD` in (50,60,70,80)) and (`ESTADO_VEHICULO`.`EST_CODIGO` <> 3000) and (`ESTADO_VEHICULO`.`UNI_CODIGO` = 65)) union select `UNIDAD1`.`UNI_CODIGO` AS `UNI_CODIGO`,`MARCELO_FECHA`.`FECHA` AS `FECHA`,`TIPO_VEHICULO`.`TVEH_DESCRIPCION` AS `TVEH_DESCRIPCION`,`VEHICULO`.`VEH_PATENTE` AS `VEH_PATENTE`,`ESTADO`.`EST_DESCRIPCION` AS `EST_DESCRIPCION`,`VEHICULO`.`VEH_CODIGO` AS `VEH_CODIGO` from ((((((`ESTADO_VEHICULO` join `ESTADO` on((`ESTADO_VEHICULO`.`EST_CODIGO` = `ESTADO`.`EST_CODIGO`))) join `VEHICULO` on((`ESTADO_VEHICULO`.`VEH_CODIGO` = `VEHICULO`.`VEH_CODIGO`))) join `MARCELO_FECHA` on(((`ESTADO_VEHICULO`.`FECHA_DESDE` <= `MARCELO_FECHA`.`FECHA`) and ((`ESTADO_VEHICULO`.`FECHA_HASTA` > `MARCELO_FECHA`.`FECHA`) or isnull(`ESTADO_VEHICULO`.`FECHA_HASTA`))))) join `UNIDAD` on((`ESTADO_VEHICULO`.`UNI_CODIGO` = `UNIDAD`.`UNI_CODIGO`))) left join `UNIDAD` `UNIDAD1` on((`ESTADO_VEHICULO`.`UNI_AGREGADO` = `UNIDAD1`.`UNI_CODIGO`))) join `TIPO_VEHICULO` on((`VEHICULO`.`TVEH_CODIGO` = `TIPO_VEHICULO`.`TVEH_CODIGO`))) where ((`MARCELO_FECHA`.`FECHA` between _latin1'20151201' and _latin1'20151201') and (`UNIDAD1`.`UNI_TIPOUNIDAD` in (50,60,70,80)) and (`UNIDAD1`.`UNI_CODIGO` = 65));

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VEH_SERVICIO`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VEH_SERVICIO` AS select `SERVICIO`.`TSERV_CODIGO` AS `TSERV_CODIGO`,`SERVICIO`.`FECHA` AS `FECHA`,`VEHICULO_SERVICIO`.`VEH_CODIGO` AS `VEH_CODIGO` from (`SERVICIO` join `VEHICULO_SERVICIO` on(((`SERVICIO`.`UNI_CODIGO` = `VEHICULO_SERVICIO`.`UNI_CODIGO`) and (`SERVICIO`.`CORRELATIVO_SERVICIO` = `VEHICULO_SERVICIO`.`CORRELATIVO_SERVICIO`)))) where ((`SERVICIO`.`FECHA` between _latin1'20151201' and _latin1'20151201') and (`SERVICIO`.`UNI_CODIGO` = 65));

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_ARBOL_UNIDADES`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_ARBOL_UNIDADES` AS select if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD3`.`UNI_CODIGO`) AS `ZONA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD3`.`UNI_DESCRIPCION`) AS `ZONA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_ORDEN`,`UNIDAD3`.`UNI_ORDEN`) AS `ZONA_orden`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD2`.`UNI_CODIGO`) AS `PREFECTURA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`) AS `PREFECTURA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`) AS `COMISARIA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`) AS `COMISARIA_DESCRIPCION`,`UNIDAD`.`UNI_CODIGO` AS `UNI_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION` AS `UNI_DESCRIPCION`,`UNIDAD`.`UNI_TIPOUNIDAD` AS `UNI_TIPOUNIDAD` from (((`UNIDAD` join `UNIDAD` `UNIDAD1` on((`UNIDAD`.`UNI_PADRE` = `UNIDAD1`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD2` on((`UNIDAD1`.`UNI_PADRE` = `UNIDAD2`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD3` on((`UNIDAD2`.`UNI_PADRE` = `UNIDAD3`.`UNI_CODIGO`))) where (`UNIDAD`.`UNI_TIPOUNIDAD` in (50,60,70,80)) order by if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_ORDEN`,`UNIDAD3`.`UNI_ORDEN`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD3`.`UNI_DESCRIPCION`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`),`UNIDAD`.`UNI_DESCRIPCION`;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_ARBOL_UNIDADES_FFEE`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_ARBOL_UNIDADES_FFEE` AS (select if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD3`.`UNI_CODIGO`) AS `ZONA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD3`.`UNI_DESCRIPCION`) AS `ZONA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_TIPOUNIDAD`,`UNIDAD3`.`UNI_TIPOUNIDAD`) AS `ZONA_TIPOUNIDAD`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_ORDEN`,`UNIDAD3`.`UNI_ORDEN`) AS `ZONA_ORDEN`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD2`.`UNI_CODIGO`) AS `PREFECTURA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`) AS `PREFECTURA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_TIPOUNIDAD`,`UNIDAD2`.`UNI_TIPOUNIDAD`) AS `PREFECTURA_TIPOUNIDAD`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`) AS `COMISARIA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`) AS `COMISARIA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_TIPOUNIDAD`,`UNIDAD1`.`UNI_TIPOUNIDAD`) AS `COMISARIA_TIPOUNIDAD`,`UNIDAD`.`UNI_CODIGO` AS `UNI_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION` AS `UNI_DESCRIPCION`,`UNIDAD`.`UNI_TIPOUNIDAD` AS `UNI_TIPOUNIDAD` from (((`UNIDAD` join `UNIDAD` `UNIDAD1` on((`UNIDAD`.`UNI_PADRE` = `UNIDAD1`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD2` on((`UNIDAD1`.`UNI_PADRE` = `UNIDAD2`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD3` on((`UNIDAD2`.`UNI_PADRE` = `UNIDAD3`.`UNI_CODIGO`))) where ((`UNIDAD`.`UNI_CODIGO_ESPECIALIDAD` = 30) and (`UNIDAD`.`UNI_TIPOUNIDAD` in (50,60,80))) order by if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_ORDEN`,`UNIDAD3`.`UNI_ORDEN`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD3`.`UNI_DESCRIPCION`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`),`UNIDAD`.`UNI_DESCRIPCION`);

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_ARBOL_UNIDADES_FRONTERAS`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_ARBOL_UNIDADES_FRONTERAS` AS select if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD3`.`UNI_CODIGO`) AS `ZONA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD3`.`UNI_DESCRIPCION`) AS `ZONA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_ORDEN`,`UNIDAD3`.`UNI_ORDEN`) AS `ZONA_orden`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD2`.`UNI_CODIGO`) AS `PREFECTURA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`) AS `PREFECTURA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`) AS `COMISARIA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`) AS `COMISARIA_DESCRIPCION`,`UNIDAD`.`UNI_CODIGO` AS `UNI_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION` AS `UNI_DESCRIPCION`,`UNIDAD`.`UNI_TIPOUNIDAD` AS `UNI_TIPOUNIDAD` from (((`UNIDAD` join `UNIDAD` `UNIDAD1` on((`UNIDAD`.`UNI_PADRE` = `UNIDAD1`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD2` on((`UNIDAD1`.`UNI_PADRE` = `UNIDAD2`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD3` on((`UNIDAD2`.`UNI_PADRE` = `UNIDAD3`.`UNI_CODIGO`))) where ((`UNIDAD`.`UNI_TIPOUNIDAD` in (50,60,70,80)) and (`UNIDAD`.`UNI_CODIGO_ESPECIALIDAD` = 80)) order by if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_ORDEN`,`UNIDAD3`.`UNI_ORDEN`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD3`.`UNI_DESCRIPCION`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`),`UNIDAD`.`UNI_DESCRIPCION`;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_ARBOL_UNIDADES_NACIONAL`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_ARBOL_UNIDADES_NACIONAL` AS select (case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_CODIGO` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_CODIGO` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_CODIGO` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_CODIGO` end) AS `ZONA_CODIGO`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_DESCRIPCION` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_DESCRIPCION` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_DESCRIPCION` end) AS `ZONA_DESCRIPCION`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_TIPOUNIDAD` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_TIPOUNIDAD` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_TIPOUNIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 20) then if((`U`.`TCU_CODIGO` in (120,130)),50,`U`.`UNI_TIPOUNIDAD`) end) AS `ZONA_TIPOUNIDAD`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_ORDEN` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_ORDEN` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_ORDEN` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_ORDEN` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_ORDEN` end) AS `ZONA_ORDEN`,(case when ((`U3`.`UNI_TIPOUNIDAD` = 30) or (`U3`.`UNI_TIPOUNIDAD` = 120) or (`U3`.`UNI_TIPOUNIDAD` = 40)) then `U3`.`UNI_CODIGO` when ((`U2`.`UNI_TIPOUNIDAD` = 30) or (`U2`.`UNI_TIPOUNIDAD` = 120) or (`U2`.`UNI_TIPOUNIDAD` = 40)) then `U2`.`UNI_CODIGO` when ((`U1`.`UNI_TIPOUNIDAD` = 30) or (`U1`.`UNI_TIPOUNIDAD` = 120) or (`U1`.`UNI_TIPOUNIDAD` = 40)) then `U1`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` <> 30) then `U`.`UNI_CODIGO` end) AS `PREFECTURA_CODIGO`,(case when ((`U3`.`UNI_TIPOUNIDAD` = 30) or (`U3`.`UNI_TIPOUNIDAD` = 120) or (`U3`.`UNI_TIPOUNIDAD` = 40)) then `U3`.`UNI_DESCRIPCION` when ((`U2`.`UNI_TIPOUNIDAD` = 30) or (`U2`.`UNI_TIPOUNIDAD` = 120) or (`U2`.`UNI_TIPOUNIDAD` = 40)) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 30) or (`U1`.`UNI_TIPOUNIDAD` = 120) or (`U1`.`UNI_TIPOUNIDAD` = 40)) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 30) then `U`.`UNI_DESCRIPCION` end) AS `PREFECTURA_DESCRIPCION`,(case when ((`U3`.`UNI_TIPOUNIDAD` = 30) or (`U3`.`UNI_TIPOUNIDAD` = 120) or (`U3`.`UNI_TIPOUNIDAD` = 40)) then `U3`.`UNI_TIPOUNIDAD` when ((`U2`.`UNI_TIPOUNIDAD` = 30) or (`U2`.`UNI_TIPOUNIDAD` = 120) or (`U2`.`UNI_TIPOUNIDAD` = 40)) then `U2`.`UNI_TIPOUNIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 30) or (`U1`.`UNI_TIPOUNIDAD` = 120) or (`U1`.`UNI_TIPOUNIDAD` = 40)) then `U1`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 30) then if((`U`.`TCU_CODIGO` in (120,130)),50,`U`.`UNI_TIPOUNIDAD`) end) AS `PREFECTURA_TIPOUNIDAD`,(case when ((`U3`.`UNI_TIPOUNIDAD` = 30) or (`U3`.`UNI_TIPOUNIDAD` = 120) or (`U3`.`UNI_TIPOUNIDAD` = 40)) then `U3`.`UNI_ESPECIALIDAD` when ((`U2`.`UNI_TIPOUNIDAD` = 30) or (`U2`.`UNI_TIPOUNIDAD` = 120) or (`U2`.`UNI_TIPOUNIDAD` = 40)) then `U2`.`UNI_ESPECIALIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 30) or (`U1`.`UNI_TIPOUNIDAD` = 120) or (`U1`.`UNI_TIPOUNIDAD` = 40)) then `U1`.`UNI_ESPECIALIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 30) then `U`.`UNI_ESPECIALIDAD` end) AS `PREFECTURA_ESPECIALIDAD`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_CODIGO` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` <> 40) then `U`.`UNI_CODIGO` end) AS `DEPENDIENTE_CODIGO`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 40) then `U`.`UNI_DESCRIPCION` end) AS `DEPENDIENTE_DESCRIPCION`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_TIPOUNIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 40) then if((`U`.`TCU_CODIGO` in (120,130)),50,`U`.`UNI_TIPOUNIDAD`) end) AS `DEPENDIENTE_TIPOUNIDAD`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_ESPECIALIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_ESPECIALIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 40) then `U`.`UNI_ESPECIALIDAD` end) AS `DEPENDIENTE_ESPECIALIDAD`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` <> 60) then `U`.`UNI_CODIGO` end) AS `COMISARIA_CODIGO`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 60) then `U`.`UNI_DESCRIPCION` end) AS `COMISARIA_DESCRIPCION`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 60) then if((`U`.`TCU_CODIGO` in (120,130)),50,`U`.`UNI_TIPOUNIDAD`) end) AS `COMISARIA_TIPOUNIDAD`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_ESPECIALIDAD` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_ESPECIALIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 60) then `U`.`UNI_ESPECIALIDAD` end) AS `COMISARIA_ESPECIALIDAD`,`U`.`UNI_CODIGO` AS `UNI_CODIGO`,`U`.`UNI_DESCRIPCION` AS `UNI_DESCRIPCION`,if((`U`.`TCU_CODIGO` in (120,130)),50,`U`.`UNI_TIPOUNIDAD`) AS `UNI_TIPOUNIDAD`,`U`.`UNI_CODIGO_ESPECIALIDAD` AS `UNI_CODIGO_ESPECIALIDAD`,`U`.`UNI_ESPECIALIDAD` AS `UNI_ESPECIALIDAD`,`U`.`UNI_PLANCUADRANTE` AS `UNI_PLANCUADRANTE`,`U`.`UNI_CAPTURA` AS `UNI_CAPTURA`,`U`.`UNI_ACTIVO` AS `UNI_ACTIVO`,`U`.`TUNI_CODIGO` AS `TUNI_CODIGO` from ((((`UNIDAD` `U` left join `UNIDAD` `U1` on((`U1`.`UNI_CODIGO` = `U`.`UNI_PADRE`))) left join `UNIDAD` `U2` on((`U2`.`UNI_CODIGO` = `U1`.`UNI_PADRE`))) left join `UNIDAD` `U3` on((`U3`.`UNI_CODIGO` = `U2`.`UNI_PADRE`))) left join `UNIDAD` `U4` on((`U4`.`UNI_CODIGO` = `U3`.`UNI_PADRE`))) where (((`U`.`UNI_TIPOUNIDAD` in (50,60,70,80,110,130,135,140,150,160,170,180,190,200,210,220,230,240,250)) or (`U`.`TCU_CODIGO` in (120,130))) and (`U`.`UNI_ACTIVO` = 1)) order by (case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_ORDEN` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_ORDEN` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_ORDEN` when (`U1`.`UNI_TIPOUNIDAD` = 20) then `U1`.`UNI_ORDEN` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_ORDEN` end),(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_DESCRIPCION` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_DESCRIPCION` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_DESCRIPCION` when (`U1`.`UNI_TIPOUNIDAD` = 20) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_DESCRIPCION` end),(case when ((`U3`.`UNI_TIPOUNIDAD` = 30) or (`U3`.`UNI_TIPOUNIDAD` = 120) or (`U3`.`UNI_TIPOUNIDAD` = 40)) then `U3`.`UNI_DESCRIPCION` when ((`U2`.`UNI_TIPOUNIDAD` = 30) or (`U2`.`UNI_TIPOUNIDAD` = 120) or (`U2`.`UNI_TIPOUNIDAD` = 40)) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 30) or (`U1`.`UNI_TIPOUNIDAD` = 120) or (`U1`.`UNI_TIPOUNIDAD` = 40)) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 30) then `U`.`UNI_DESCRIPCION` end),(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 40) then `U`.`UNI_DESCRIPCION` end),(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 60) then `U`.`UNI_DESCRIPCION` end),`U`.`UNI_DESCRIPCION`;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_ARBOL_UNIDADES_NACIONAL_CLUSTER`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_ARBOL_UNIDADES_NACIONAL_CLUSTER` AS select (case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_CODIGO` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_CODIGO` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_CODIGO` when (`U1`.`UNI_TIPOUNIDAD` = 20) then `U1`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_CODIGO` end) AS `ZONA_CODIGO`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_DESCRIPCION` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_DESCRIPCION` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_DESCRIPCION` when (`U1`.`UNI_TIPOUNIDAD` = 20) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_DESCRIPCION` end) AS `ZONA_DESCRIPCION`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_TIPOUNIDAD` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_TIPOUNIDAD` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_TIPOUNIDAD` when (`U1`.`UNI_TIPOUNIDAD` = 20) then `U1`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_TIPOUNIDAD` end) AS `ZONA_TIPOUNIDAD`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_ORDEN` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_ORDEN` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_ORDEN` when (`U1`.`UNI_TIPOUNIDAD` = 20) then `U1`.`UNI_ORDEN` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_ORDEN` end) AS `ZONA_ORDEN`,(case when ((`U3`.`UNI_TIPOUNIDAD` = 30) or (`U3`.`UNI_TIPOUNIDAD` = 120) or (`U3`.`UNI_TIPOUNIDAD` = 40)) then `U3`.`UNI_CODIGO` when ((`U2`.`UNI_TIPOUNIDAD` = 30) or (`U2`.`UNI_TIPOUNIDAD` = 120) or (`U2`.`UNI_TIPOUNIDAD` = 40)) then `U2`.`UNI_CODIGO` when ((`U1`.`UNI_TIPOUNIDAD` = 30) or (`U1`.`UNI_TIPOUNIDAD` = 120) or (`U1`.`UNI_TIPOUNIDAD` = 40)) then `U1`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` <> 30) then `U`.`UNI_CODIGO` end) AS `PREFECTURA_CODIGO`,(case when ((`U3`.`UNI_TIPOUNIDAD` = 30) or (`U3`.`UNI_TIPOUNIDAD` = 120) or (`U3`.`UNI_TIPOUNIDAD` = 40)) then `U3`.`UNI_DESCRIPCION` when ((`U2`.`UNI_TIPOUNIDAD` = 30) or (`U2`.`UNI_TIPOUNIDAD` = 120) or (`U2`.`UNI_TIPOUNIDAD` = 40)) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 30) or (`U1`.`UNI_TIPOUNIDAD` = 120) or (`U1`.`UNI_TIPOUNIDAD` = 40)) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 30) then `U`.`UNI_DESCRIPCION` end) AS `PREFECTURA_DESCRIPCION`,(case when ((`U3`.`UNI_TIPOUNIDAD` = 30) or (`U3`.`UNI_TIPOUNIDAD` = 120) or (`U3`.`UNI_TIPOUNIDAD` = 40)) then `U3`.`UNI_TIPOUNIDAD` when ((`U2`.`UNI_TIPOUNIDAD` = 30) or (`U2`.`UNI_TIPOUNIDAD` = 120) or (`U2`.`UNI_TIPOUNIDAD` = 40)) then `U2`.`UNI_TIPOUNIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 30) or (`U1`.`UNI_TIPOUNIDAD` = 120) or (`U1`.`UNI_TIPOUNIDAD` = 40)) then `U1`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 30) then `U`.`UNI_TIPOUNIDAD` end) AS `PREFECTURA_TIPOUNIDAD`,(case when ((`U3`.`UNI_TIPOUNIDAD` = 30) or (`U3`.`UNI_TIPOUNIDAD` = 120) or (`U3`.`UNI_TIPOUNIDAD` = 40)) then `U3`.`UNI_ESPECIALIDAD` when ((`U2`.`UNI_TIPOUNIDAD` = 30) or (`U2`.`UNI_TIPOUNIDAD` = 120) or (`U2`.`UNI_TIPOUNIDAD` = 40)) then `U2`.`UNI_ESPECIALIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 30) or (`U1`.`UNI_TIPOUNIDAD` = 120) or (`U1`.`UNI_TIPOUNIDAD` = 40)) then `U1`.`UNI_ESPECIALIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 30) then `U`.`UNI_ESPECIALIDAD` end) AS `PREFECTURA_ESPECIALIDAD`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_CODIGO` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` <> 40) then `U`.`UNI_CODIGO` end) AS `DEPENDIENTE_CODIGO`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 40) then `U`.`UNI_DESCRIPCION` end) AS `DEPENDIENTE_DESCRIPCION`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_TIPOUNIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 40) then `U`.`UNI_TIPOUNIDAD` end) AS `DEPENDIENTE_TIPOUNIDAD`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_ESPECIALIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_ESPECIALIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 40) then `U`.`UNI_ESPECIALIDAD` end) AS `DEPENDIENTE_ESPECIALIDAD`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` <> 60) then `U`.`UNI_CODIGO` end) AS `COMISARIA_CODIGO`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 60) then `U`.`UNI_DESCRIPCION` end) AS `COMISARIA_DESCRIPCION`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 60) then `U`.`UNI_TIPOUNIDAD` end) AS `COMISARIA_TIPOUNIDAD`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_ESPECIALIDAD` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_ESPECIALIDAD` when (`U`.`UNI_TIPOUNIDAD` <> 60) then `U`.`UNI_ESPECIALIDAD` end) AS `COMISARIA_ESPECIALIDAD`,`U`.`UNI_CODIGO` AS `UNI_CODIGO`,`U`.`UNI_DESCRIPCION` AS `UNI_DESCRIPCION`,`U`.`UNI_TIPOUNIDAD` AS `UNI_TIPOUNIDAD`,`U`.`UNI_CODIGO_ESPECIALIDAD` AS `UNI_CODIGO_ESPECIALIDAD`,`U`.`UNI_ESPECIALIDAD` AS `UNI_ESPECIALIDAD`,`U`.`UNI_PLANCUADRANTE` AS `UNI_PLANCUADRANTE`,`U`.`UNI_CAPTURA` AS `UNI_CAPTURA`,`U`.`UNI_CLUSTER` AS `UNI_CLUSTER`,`U`.`UNI_ACTIVO` AS `UNI_ACTIVO` from ((((`UNIDAD` `U` left join `UNIDAD` `U1` on((`U1`.`UNI_CODIGO` = `U`.`UNI_PADRE`))) left join `UNIDAD` `U2` on((`U2`.`UNI_CODIGO` = `U1`.`UNI_PADRE`))) left join `UNIDAD` `U3` on((`U3`.`UNI_CODIGO` = `U2`.`UNI_PADRE`))) left join `UNIDAD` `U4` on((`U4`.`UNI_CODIGO` = `U3`.`UNI_PADRE`))) where ((`U`.`UNI_TIPOUNIDAD` in (50,60,70,80,110,130,135,140,150,160)) and (`U`.`UNI_ACTIVO` = 1)) order by (case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_ORDEN` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_ORDEN` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_ORDEN` when (`U1`.`UNI_TIPOUNIDAD` = 20) then `U1`.`UNI_ORDEN` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_ORDEN` end),(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_DESCRIPCION` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_DESCRIPCION` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_DESCRIPCION` when (`U1`.`UNI_TIPOUNIDAD` = 20) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 20) then `U`.`UNI_DESCRIPCION` end),(case when ((`U3`.`UNI_TIPOUNIDAD` = 30) or (`U3`.`UNI_TIPOUNIDAD` = 120) or (`U3`.`UNI_TIPOUNIDAD` = 40)) then `U3`.`UNI_DESCRIPCION` when ((`U2`.`UNI_TIPOUNIDAD` = 30) or (`U2`.`UNI_TIPOUNIDAD` = 120) or (`U2`.`UNI_TIPOUNIDAD` = 40)) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 30) or (`U1`.`UNI_TIPOUNIDAD` = 120) or (`U1`.`UNI_TIPOUNIDAD` = 40)) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 30) then `U`.`UNI_DESCRIPCION` end),(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 40) then `U`.`UNI_DESCRIPCION` end),(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` <> 60) then `U`.`UNI_DESCRIPCION` end),`U`.`UNI_DESCRIPCION`;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS` AS select (case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_CODIGO` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_CODIGO` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_CODIGO` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_CODIGO` else `U`.`UNI_CODIGO` end) AS `ZONA_CODIGO`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_DESCRIPCION` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_DESCRIPCION` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_DESCRIPCION` else `U`.`UNI_DESCRIPCION` end) AS `ZONA_DESCRIPCION`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_TIPOUNIDAD` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_TIPOUNIDAD` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_TIPOUNIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_TIPOUNIDAD` else `U`.`UNI_TIPOUNIDAD` end) AS `ZONA_TIPOUNIDAD`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_ORDEN` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_ORDEN` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_ORDEN` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_ORDEN` else `U`.`UNI_ORDEN` end) AS `ZONA_ORDEN`,(case when (`U3`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U3`.`UNI_CODIGO` when (`U2`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U2`.`UNI_CODIGO` when (`U1`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U1`.`UNI_CODIGO` else `U`.`UNI_CODIGO` end) AS `PREFECTURA_CODIGO`,(case when (`U3`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U3`.`UNI_DESCRIPCION` when (`U2`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U2`.`UNI_DESCRIPCION` when (`U1`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U1`.`UNI_DESCRIPCION` else `U`.`UNI_DESCRIPCION` end) AS `PREFECTURA_DESCRIPCION`,(case when (`U3`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U3`.`UNI_TIPOUNIDAD` when (`U2`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U2`.`UNI_TIPOUNIDAD` when (`U1`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U1`.`UNI_TIPOUNIDAD` else `U`.`UNI_TIPOUNIDAD` end) AS `PREFECTURA_TIPOUNIDAD`,(case when (`U3`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U3`.`UNI_ESPECIALIDAD` when (`U2`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U2`.`UNI_ESPECIALIDAD` when (`U1`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U1`.`UNI_ESPECIALIDAD` else `U`.`UNI_ESPECIALIDAD` end) AS `PREFECTURA_ESPECIALIDAD`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_CODIGO` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_CODIGO` else `U`.`UNI_CODIGO` end) AS `DEPENDIENTE_CODIGO`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_DESCRIPCION` else `U`.`UNI_DESCRIPCION` end) AS `DEPENDIENTE_DESCRIPCION`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_TIPOUNIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_TIPOUNIDAD` else `U`.`UNI_TIPOUNIDAD` end) AS `DEPENDIENTE_TIPOUNIDAD`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_ESPECIALIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_ESPECIALIDAD` else `U`.`UNI_ESPECIALIDAD` end) AS `DEPENDIENTE_ESPECIALIDAD`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_CODIGO` else `U`.`UNI_CODIGO` end) AS `COMISARIA_CODIGO`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_DESCRIPCION` else `U`.`UNI_DESCRIPCION` end) AS `COMISARIA_DESCRIPCION`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_TIPOUNIDAD` else `U`.`UNI_TIPOUNIDAD` end) AS `COMISARIA_TIPOUNIDAD`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_ESPECIALIDAD` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_ESPECIALIDAD` else `U`.`UNI_ESPECIALIDAD` end) AS `COMISARIA_ESPECIALIDAD`,`U`.`UNI_CODIGO` AS `UNI_CODIGO`,`U`.`UNI_DESCRIPCION` AS `UNI_DESCRIPCION`,`U`.`UNI_TIPOUNIDAD` AS `UNI_TIPOUNIDAD`,`U`.`UNI_CODIGO_ESPECIALIDAD` AS `UNI_CODIGO_ESPECIALIDAD`,`U`.`UNI_ESPECIALIDAD` AS `UNI_ESPECIALIDAD`,`U`.`UNI_PLANCUADRANTE` AS `UNI_PLANCUADRANTE`,`U`.`UNI_CAPTURA` AS `UNI_CAPTURA`,`U`.`TUNI_CODIGO` AS `TUNI_CODIGO` from ((((`UNIDAD` `U` left join `UNIDAD` `U1` on((`U1`.`UNI_CODIGO` = `U`.`UNI_PADRE`))) left join `UNIDAD` `U2` on((`U2`.`UNI_CODIGO` = `U1`.`UNI_PADRE`))) left join `UNIDAD` `U3` on((`U3`.`UNI_CODIGO` = `U2`.`UNI_PADRE`))) left join `UNIDAD` `U4` on((`U4`.`UNI_CODIGO` = `U3`.`UNI_PADRE`))) where ((`U`.`UNI_ACTIVO` = 1) and (`U`.`UNI_CAPTURA` = 1));

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS2`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_ARBOL_UNIDADES_NACIONAL_CON_PREFECTURAS2` AS select (case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_CODIGO` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_CODIGO` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_CODIGO` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_CODIGO` else `U`.`UNI_CODIGO` end) AS `ZONA_CODIGO`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_DESCRIPCION` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_DESCRIPCION` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_DESCRIPCION` else `U`.`UNI_DESCRIPCION` end) AS `ZONA_DESCRIPCION`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_TIPOUNIDAD` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_TIPOUNIDAD` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_TIPOUNIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_TIPOUNIDAD` else `U`.`UNI_TIPOUNIDAD` end) AS `ZONA_TIPOUNIDAD`,(case when (`U4`.`UNI_TIPOUNIDAD` = 20) then `U4`.`UNI_ORDEN` when (`U3`.`UNI_TIPOUNIDAD` = 20) then `U3`.`UNI_ORDEN` when (`U2`.`UNI_TIPOUNIDAD` = 20) then `U2`.`UNI_ORDEN` when ((`U1`.`UNI_TIPOUNIDAD` = 20) or (`U`.`UNI_TIPOUNIDAD` = 200)) then `U1`.`UNI_ORDEN` else `U`.`UNI_ORDEN` end) AS `ZONA_ORDEN`,(case when (`U3`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U3`.`UNI_CODIGO` when (`U2`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U2`.`UNI_CODIGO` when (`U1`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U1`.`UNI_CODIGO` else `U`.`UNI_CODIGO` end) AS `PREFECTURA_CODIGO`,(case when (`U3`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U3`.`UNI_DESCRIPCION` when (`U2`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U2`.`UNI_DESCRIPCION` when (`U1`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U1`.`UNI_DESCRIPCION` else `U`.`UNI_DESCRIPCION` end) AS `PREFECTURA_DESCRIPCION`,(case when (`U3`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U3`.`UNI_TIPOUNIDAD` when (`U2`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U2`.`UNI_TIPOUNIDAD` when (`U1`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U1`.`UNI_TIPOUNIDAD` else `U`.`UNI_TIPOUNIDAD` end) AS `PREFECTURA_TIPOUNIDAD`,(case when (`U3`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U3`.`UNI_ESPECIALIDAD` when (`U2`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U2`.`UNI_ESPECIALIDAD` when (`U1`.`UNI_TIPOUNIDAD` in (30,120,40)) then `U1`.`UNI_ESPECIALIDAD` else `U`.`UNI_ESPECIALIDAD` end) AS `PREFECTURA_ESPECIALIDAD`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_CODIGO` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_CODIGO` else `U`.`UNI_CODIGO` end) AS `DEPENDIENTE_CODIGO`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_DESCRIPCION` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_DESCRIPCION` else `U`.`UNI_DESCRIPCION` end) AS `DEPENDIENTE_DESCRIPCION`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_TIPOUNIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_TIPOUNIDAD` else `U`.`UNI_TIPOUNIDAD` end) AS `DEPENDIENTE_TIPOUNIDAD`,(case when (`U2`.`UNI_TIPOUNIDAD` = 40) then `U2`.`UNI_ESPECIALIDAD` when ((`U1`.`UNI_TIPOUNIDAD` = 40) and (`U2`.`UNI_TIPOUNIDAD` <> 20)) then `U1`.`UNI_ESPECIALIDAD` else `U`.`UNI_ESPECIALIDAD` end) AS `DEPENDIENTE_ESPECIALIDAD`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_CODIGO` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_CODIGO` else `U`.`UNI_CODIGO` end) AS `COMISARIA_CODIGO`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_DESCRIPCION` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_DESCRIPCION` else `U`.`UNI_DESCRIPCION` end) AS `COMISARIA_DESCRIPCION`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_TIPOUNIDAD` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_TIPOUNIDAD` else `U`.`UNI_TIPOUNIDAD` end) AS `COMISARIA_TIPOUNIDAD`,(case when (`U1`.`UNI_TIPOUNIDAD` = 60) then `U1`.`UNI_ESPECIALIDAD` when (`U`.`UNI_TIPOUNIDAD` = 60) then `U`.`UNI_ESPECIALIDAD` else `U`.`UNI_ESPECIALIDAD` end) AS `COMISARIA_ESPECIALIDAD`,`U`.`UNI_CODIGO` AS `UNI_CODIGO`,`U`.`UNI_DESCRIPCION` AS `UNI_DESCRIPCION`,`U`.`UNI_TIPOUNIDAD` AS `UNI_TIPOUNIDAD`,`U`.`UNI_CODIGO_ESPECIALIDAD` AS `UNI_CODIGO_ESPECIALIDAD`,`U`.`UNI_ESPECIALIDAD` AS `UNI_ESPECIALIDAD`,`U`.`UNI_PLANCUADRANTE` AS `UNI_PLANCUADRANTE`,`U`.`UNI_CAPTURA` AS `UNI_CAPTURA`,`U`.`TUNI_CODIGO` AS `TUNI_CODIGO` from ((((`UNIDAD` `U` left join `UNIDAD` `U1` on((`U1`.`UNI_CODIGO` = `U`.`UNI_PADRE`))) left join `UNIDAD` `U2` on((`U2`.`UNI_CODIGO` = `U1`.`UNI_PADRE`))) left join `UNIDAD` `U3` on((`U3`.`UNI_CODIGO` = `U2`.`UNI_PADRE`))) left join `UNIDAD` `U4` on((`U4`.`UNI_CODIGO` = `U3`.`UNI_PADRE`)));

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_ARBOL_UNIDADES_TRANSITO`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_ARBOL_UNIDADES_TRANSITO` AS select if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD3`.`UNI_CODIGO`) AS `ZONA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD3`.`UNI_DESCRIPCION`) AS `ZONA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_TIPOUNIDAD`,`UNIDAD3`.`UNI_TIPOUNIDAD`) AS `ZONA_TIPOUNIDAD`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_ORDEN`,`UNIDAD3`.`UNI_ORDEN`) AS `ZONA_orden`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD2`.`UNI_CODIGO`) AS `PREFECTURA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`) AS `PREFECTURA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_TIPOUNIDAD`,`UNIDAD2`.`UNI_TIPOUNIDAD`) AS `PREFECTURA_TIPOUNIDAD`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`) AS `COMISARIA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`) AS `COMISARIA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_TIPOUNIDAD`,`UNIDAD1`.`UNI_TIPOUNIDAD`) AS `COMISARIA_TIPOUNIDAD`,`UNIDAD`.`UNI_CODIGO` AS `UNI_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION` AS `UNI_DESCRIPCION`,`UNIDAD`.`UNI_TIPOUNIDAD` AS `UNI_TIPOUNIDAD` from (((`UNIDAD` join `UNIDAD` `UNIDAD1` on((`UNIDAD`.`UNI_PADRE` = `UNIDAD1`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD2` on((`UNIDAD1`.`UNI_PADRE` = `UNIDAD2`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD3` on((`UNIDAD2`.`UNI_PADRE` = `UNIDAD3`.`UNI_CODIGO`))) where (`UNIDAD`.`UNI_CODIGO_ESPECIALIDAD` in (10,90)) order by if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_ORDEN`,`UNIDAD3`.`UNI_ORDEN`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD3`.`UNI_DESCRIPCION`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`),`UNIDAD`.`UNI_DESCRIPCION`;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_FECHA_ULTIMO_SERV_POR_VEH`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_FECHA_ULTIMO_SERV_POR_VEH` AS select `VEHICULO`.`VEH_PATENTE` AS `VEH_PATENTE`,`VEHICULO_SERVICIO`.`VEH_CODIGO` AS `VEH_CODIGO`,max(`SERVICIO`.`FECHA`) AS `FIELD_1` from (((`VEHICULO_SERVICIO` join `SERVICIO` on(((`VEHICULO_SERVICIO`.`UNI_CODIGO` = `SERVICIO`.`UNI_CODIGO`) and (`VEHICULO_SERVICIO`.`CORRELATIVO_SERVICIO` = `SERVICIO`.`CORRELATIVO_SERVICIO`)))) join `ESTADO_VEHICULO` on((`ESTADO_VEHICULO`.`VEH_CODIGO` = `VEHICULO_SERVICIO`.`VEH_CODIGO`))) join `VEHICULO` on((`VEHICULO`.`VEH_CODIGO` = `ESTADO_VEHICULO`.`VEH_CODIGO`))) where isnull(`ESTADO_VEHICULO`.`FECHA_HASTA`) group by `VEHICULO`.`VEH_PATENTE`,`VEHICULO_SERVICIO`.`VEH_CODIGO`;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_PASO_COLACION_PARA_15_UNDADES`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_PASO_COLACION_PARA_15_UNDADES` AS select `UNIDAD`.`UNI_CODIGO_SIICGE` AS `UNIDAD_SERVICIO`,`SERVICIO`.`FECHA` AS `FECHA_SERVICIO`,`FUNCIONARIO_SERVICIO`.`FUN_CODIGO` AS `FUNCIONARIO_SERVICIO`,sum((case when (`SERVICIO`.`TSERV_CODIGO` = 142) then -(30) when (`SERVICIO`.`TSERV_CODIGO` = 148) then -(45) when (`SERVICIO`.`TSERV_CODIGO` = 143) then -(60) when (`SERVICIO`.`TSERV_CODIGO` = 149) then -(75) when (`SERVICIO`.`TSERV_CODIGO` = 144) then -(90) when (`SERVICIO`.`TSERV_CODIGO` = 151) then -(105) when (`SERVICIO`.`TSERV_CODIGO` = 145) then -(120) when (`SERVICIO`.`TSERV_CODIGO` = 152) then -(135) when (`SERVICIO`.`TSERV_CODIGO` = 146) then -(150) when (`SERVICIO`.`TSERV_CODIGO` = 153) then -(165) when (`SERVICIO`.`TSERV_CODIGO` = 147) then -(180) end)) AS `TIEMPO_COLACION` from ((((`FUNCIONARIO_SERVICIO` join `SERVICIO` on(((`FUNCIONARIO_SERVICIO`.`UNI_CODIGO` = `SERVICIO`.`UNI_CODIGO`) and (`FUNCIONARIO_SERVICIO`.`CORRELATIVO_SERVICIO` = `SERVICIO`.`CORRELATIVO_SERVICIO`)))) join `TIPO_SERVICIO` on((`SERVICIO`.`TSERV_CODIGO` = `TIPO_SERVICIO`.`TSERV_CODIGO`))) join `UNIDAD` on((`SERVICIO`.`UNI_CODIGO` = `UNIDAD`.`UNI_CODIGO`))) join `MARCELO_FECHA` on((`SERVICIO`.`FECHA` = `MARCELO_FECHA`.`FECHA`))) where ((`MARCELO_FECHA`.`FECHA` between _latin1'20140801' and _latin1'20140831') and (`UNIDAD`.`UNI_CODIGO_SIICGE` in (_latin1'317100000000',_latin1'317300000000',_latin1'375100000000',_latin1'375200000000',_latin1'375300000000',_latin1'454100000000',_latin1'454200000000',_latin1'456200000000',_latin1'456400000000',_latin1'458100000000',_latin1'620540000000',_latin1'650210000000',_latin1'650230000000',_latin1'650250000000',_latin1'720500000000')) and (`SERVICIO`.`TSERV_CODIGO` in (142,143,144,145,146,147,148,149,151,152,153)) and (`UNIDAD`.`UNI_TIPOUNIDAD` in (50,60,70,80))) group by `UNIDAD`.`UNI_CODIGO_SIICGE`,`SERVICIO`.`FECHA`,`FUNCIONARIO_SERVICIO`.`FUN_CODIGO`;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_PASO_SERV_RRCC_15UNIDADES`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_PASO_SERV_RRCC_15UNIDADES` AS select `UNIDAD`.`UNI_CODIGO_SIICGE` AS `UNI_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION` AS `UNI_DESCRIPCION`,`SERVICIO`.`FECHA` AS `FECHA`,`FUNCIONARIO_SERVICIO`.`FUN_CODIGO` AS `FUN_CODIGO`,`FUNCIONARIO`.`FUN_APELLIDOPATERNO` AS `FUN_APELLIDOPATERNO`,`FUNCIONARIO`.`FUN_APELLIDOMATERNO` AS `FUN_APELLIDOMATERNO`,`FUNCIONARIO`.`FUN_NOMBRE` AS `FUN_NOMBRE`,`FUNCIONARIO`.`FUN_NOMBRE2` AS `FUN_NOMBRE2`,`TIPO_SERVICIO`.`TSERV_DESCRIPCION` AS `TSERV_DESCRIPCION`,`SERVICIO`.`HORA_INICIO` AS `HORA_INICIO`,`SERVICIO`.`HORA_TERMINO` AS `HORA_TERMINO`,if((`SERVICIO`.`HORA_TERMINO` > `SERVICIO`.`HORA_INICIO`),((time_to_sec(`SERVICIO`.`HORA_TERMINO`) - time_to_sec(`SERVICIO`.`HORA_INICIO`)) / 60),(1440 - ((time_to_sec(`SERVICIO`.`HORA_INICIO`) - time_to_sec(`SERVICIO`.`HORA_TERMINO`)) / 60))) AS `TIEMPO`,`TIPO_VEHICULO`.`TVEH_DESCRIPCION` AS `TVEH_DESCRIPCION`,`VEHICULO`.`VEH_PATENTE` AS `VEH_PATENTE` from ((((((((`SERVICIO` join `FUNCIONARIO_SERVICIO` on(((`SERVICIO`.`UNI_CODIGO` = `FUNCIONARIO_SERVICIO`.`UNI_CODIGO`) and (`SERVICIO`.`CORRELATIVO_SERVICIO` = `FUNCIONARIO_SERVICIO`.`CORRELATIVO_SERVICIO`)))) join `UNIDAD` on((`SERVICIO`.`UNI_CODIGO` = `UNIDAD`.`UNI_CODIGO`))) join `TIPO_SERVICIO` on((`SERVICIO`.`TSERV_CODIGO` = `TIPO_SERVICIO`.`TSERV_CODIGO`))) left join `FUNCIONARIO_VEHICULO` on(((`FUNCIONARIO_SERVICIO`.`UNI_CODIGO` = `FUNCIONARIO_VEHICULO`.`FUN_UNI_CODIGO`) and (`FUNCIONARIO_SERVICIO`.`CORRELATIVO_SERVICIO` = `FUNCIONARIO_VEHICULO`.`FUN_CORRELATIVO_SERVICIO`) and (`FUNCIONARIO_SERVICIO`.`FUN_CODIGO` = `FUNCIONARIO_VEHICULO`.`FUN_CODIGO`)))) left join `VEHICULO_SERVICIO` on(((`FUNCIONARIO_VEHICULO`.`VEH_UNI_CODIGO` = `VEHICULO_SERVICIO`.`UNI_CODIGO`) and (`FUNCIONARIO_VEHICULO`.`VEH_CORRELATIVO_SERVICIO` = `VEHICULO_SERVICIO`.`CORRELATIVO_SERVICIO`) and (`FUNCIONARIO_VEHICULO`.`VEH_CODIGO` = `VEHICULO_SERVICIO`.`VEH_CODIGO`)))) left join `VEHICULO` on((`VEHICULO_SERVICIO`.`VEH_CODIGO` = `VEHICULO`.`VEH_CODIGO`))) left join `TIPO_VEHICULO` on((`VEHICULO`.`TVEH_CODIGO` = `TIPO_VEHICULO`.`TVEH_CODIGO`))) join `FUNCIONARIO` on((`FUNCIONARIO_SERVICIO`.`FUN_CODIGO` = `FUNCIONARIO`.`FUN_CODIGO`))) where ((`UNIDAD`.`UNI_CODIGO_SIICGE` in (_latin1'317100000000',_latin1'317300000000',_latin1'375100000000',_latin1'375200000000',_latin1'375300000000',_latin1'454100000000',_latin1'454200000000',_latin1'456200000000',_latin1'456400000000',_latin1'458100000000',_latin1'620540000000',_latin1'650210000000',_latin1'650230000000',_latin1'650250000000',_latin1'720500000000')) and (`SERVICIO`.`FECHA` between _latin1'20140801' and _latin1'20140831') and (`SERVICIO`.`TSERV_CODIGO` in (613,614,619,620)));

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_PERSONAL_ASIGNADO_POR_DIA`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_PERSONAL_ASIGNADO_POR_DIA` AS select `CARGO_FUNCIONARIO`.`FUN_CODIGO` AS `FUNCIONARIO_CODIGO`,`CARGO_FUNCIONARIO`.`UNI_CODIGO` AS `UNI_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION` AS `UNIDAD_CARGO`,`MARCELO_FECHA`.`FECHA` AS `FECHA`,`CARGO`.`CAR_DESCRIPCION` AS `CARGO`,`UNIDAD1`.`UNI_DESCRIPCION` AS `UNIDAD_AGREGADO` from (((((`CARGO_FUNCIONARIO` join `CARGO` on((`CARGO_FUNCIONARIO`.`CAR_CODIGO` = `CARGO`.`CAR_CODIGO`))) join `UNIDAD` on((`CARGO_FUNCIONARIO`.`UNI_CODIGO` = `UNIDAD`.`UNI_CODIGO`))) left join `UNIDAD` `UNIDAD1` on((`CARGO_FUNCIONARIO`.`UNI_AGREGADO` = `UNIDAD1`.`UNI_CODIGO`))) join `FUNCIONARIO` on((`CARGO_FUNCIONARIO`.`FUN_CODIGO` = `FUNCIONARIO`.`FUN_CODIGO`))) join `MARCELO_FECHA` on(((`CARGO_FUNCIONARIO`.`FECHA_DESDE` <= `MARCELO_FECHA`.`FECHA`) and ((`CARGO_FUNCIONARIO`.`FECHA_HASTA` > `MARCELO_FECHA`.`FECHA`) or isnull(`CARGO_FUNCIONARIO`.`FECHA_HASTA`))))) where ((`CARGO_FUNCIONARIO`.`UNI_CODIGO` = 10) and (`FUNCIONARIO`.`UNI_CODIGO` is not null) and (`MARCELO_FECHA`.`FECHA` between _latin1'20120101' and _latin1'20120131'));

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_PERSONAL_POR_CARGO`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_PERSONAL_POR_CARGO` AS select if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD3`.`UNI_CODIGO`,`UNIDAD2`.`UNI_CODIGO`) AS `ZONA_CODIGO`,if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD3`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`) AS `ZONA_DESCRIPCION`,if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`) AS `PREFECTURA_CODIGO`,if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`) AS `PREFECTURA_DESCRIPCION`,if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD`.`UNI_CODIGO`) AS `UNIDAD_CODIGO`,if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD`.`UNI_DESCRIPCION`) AS `UNIDAD_DESCRIPCION`,`UNIDAD`.`UNI_CODIGO` AS `UNI_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION` AS `UNI_DESCRIPCION`,`CARGO`.`CAR_DESCRIPCION` AS `CAR_DESCRIPCION`,`GRADO`.`GRA_DESCRIPCION` AS `GRA_DESCRIPCION`,count(0) AS `CANTIDAD` from (((((((`CARGO_FUNCIONARIO` join `FUNCIONARIO` on((`CARGO_FUNCIONARIO`.`FUN_CODIGO` = `FUNCIONARIO`.`FUN_CODIGO`))) join `GRADO` on(((`FUNCIONARIO`.`ESC_CODIGO` = `GRADO`.`ESC_CODIGO`) and (`FUNCIONARIO`.`GRA_CODIGO` = `GRADO`.`GRA_CODIGO`)))) join `CARGO` on((`CARGO_FUNCIONARIO`.`CAR_CODIGO` = `CARGO`.`CAR_CODIGO`))) join `UNIDAD` on((`CARGO_FUNCIONARIO`.`UNI_CODIGO` = `UNIDAD`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD1` on((`UNIDAD`.`UNI_PADRE` = `UNIDAD1`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD2` on((`UNIDAD1`.`UNI_PADRE` = `UNIDAD2`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD3` on((`UNIDAD2`.`UNI_PADRE` = `UNIDAD3`.`UNI_CODIGO`))) where (isnull(`CARGO_FUNCIONARIO`.`FECHA_HASTA`) and ((`UNIDAD`.`UNI_PADRE` = 1950) or `UNIDAD`.`UNI_PADRE` in (select `UNIDAD`.`UNI_CODIGO` AS `UNI_CODIGO` from `UNIDAD` where ((`UNIDAD`.`UNI_PADRE` = 1950) or `UNIDAD`.`UNI_PADRE` in (select `UNIDAD`.`UNI_CODIGO` AS `UNI_CODIGO` from `UNIDAD` where ((`UNIDAD`.`UNI_PADRE` = 1950) or `UNIDAD`.`UNI_PADRE` in (select `UNIDAD`.`UNI_CODIGO` AS `UNI_CODIGO` from `UNIDAD` where (`UNIDAD`.`UNI_PADRE` = 1950))))))) and (`CARGO_FUNCIONARIO`.`CAR_CODIGO` in (40,50,60,120,130,140,160,170,190,200,210,220,260,270,280,290,300,330))) group by if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD3`.`UNI_CODIGO`,`UNIDAD2`.`UNI_CODIGO`),if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD3`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`),if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`),if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`),if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD`.`UNI_CODIGO`),if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD`.`UNI_DESCRIPCION`),`UNIDAD`.`UNI_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION`,`CARGO`.`CAR_DESCRIPCION`,`GRADO`.`GRA_DESCRIPCION` order by if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`),if((`UNIDAD3`.`UNI_CODIGO` = 1950),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD`.`UNI_CODIGO`),`UNIDAD`.`UNI_CODIGO`,`CARGO`.`CAR_DESCRIPCION`,`GRADO`.`GRA_DESCRIPCION`;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_PERSONAL_POR_SERVICIO`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_PERSONAL_POR_SERVICIO` AS select if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD3`.`UNI_CODIGO`,`UNIDAD2`.`UNI_CODIGO`) AS `ZONA_CODIGO`,if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD3`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`) AS `ZONA_DESCRIPCION`,if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`) AS `PREFECTURA_CODIGO`,if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`) AS `PREFECTURA_DESCRIPCION`,if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD`.`UNI_CODIGO`) AS `UNIDAD_CODIGO`,if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD`.`UNI_DESCRIPCION`) AS `UNIDAD_DESCRIPCION`,`SERVICIO`.`UNI_CODIGO` AS `DESTACAMENTO_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION` AS `DESTACAMENTO_DESCRIPCION`,`SERVICIO`.`FECHA` AS `FECHA`,`TIPO_SERVICIO`.`TSERV_DESCRIPCION` AS `TSERV_DESCRIPCION`,`SERVICIO`.`CORRELATIVO_SERVICIO` AS `CORRELATIVO_SERVICIO`,count(0) AS `CANTIDAD` from ((((((`SERVICIO` join `TIPO_SERVICIO` on((`SERVICIO`.`TSERV_CODIGO` = `TIPO_SERVICIO`.`TSERV_CODIGO`))) join `FUNCIONARIO_SERVICIO` on(((`SERVICIO`.`UNI_CODIGO` = `FUNCIONARIO_SERVICIO`.`UNI_CODIGO`) and (`SERVICIO`.`CORRELATIVO_SERVICIO` = `FUNCIONARIO_SERVICIO`.`CORRELATIVO_SERVICIO`)))) join `UNIDAD` on((`SERVICIO`.`UNI_CODIGO` = `UNIDAD`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD1` on((`UNIDAD`.`UNI_PADRE` = `UNIDAD1`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD2` on((`UNIDAD1`.`UNI_PADRE` = `UNIDAD2`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD3` on((`UNIDAD2`.`UNI_PADRE` = `UNIDAD3`.`UNI_CODIGO`))) where ((`SERVICIO`.`FECHA` between 20110214 and 20110306) and (`SERVICIO`.`TSERV_CODIGO` in (50,60,70,80,90,100))) group by if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD3`.`UNI_CODIGO`,`UNIDAD2`.`UNI_CODIGO`),if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD3`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`),if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`),if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`),if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD`.`UNI_CODIGO`),if((`UNIDAD3`.`UNI_CODIGO` <> 20),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD`.`UNI_DESCRIPCION`),`SERVICIO`.`UNI_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION`,`SERVICIO`.`FECHA`,`TIPO_SERVICIO`.`TSERV_DESCRIPCION`,`SERVICIO`.`CORRELATIVO_SERVICIO`;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_PERSONAL_SERVICIO_POR_DIA`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_PERSONAL_SERVICIO_POR_DIA` AS select distinct `FUNCIONARIO_SERVICIO`.`FUN_CODIGO` AS `FUN_CODIGO`,`SERVICIO`.`FECHA` AS `FECHA`,ucase(`TIPO_SERVICIO`.`TSERV_DESCRIPCION`) AS `TIPO_SERVICIO` from (((`FUNCIONARIO_SERVICIO` join `SERVICIO` on(((`FUNCIONARIO_SERVICIO`.`UNI_CODIGO` = `SERVICIO`.`UNI_CODIGO`) and (`FUNCIONARIO_SERVICIO`.`CORRELATIVO_SERVICIO` = `SERVICIO`.`CORRELATIVO_SERVICIO`)))) join `MARCELO_FECHA` on((`SERVICIO`.`FECHA` = `MARCELO_FECHA`.`FECHA`))) join `TIPO_SERVICIO` on((`SERVICIO`.`TSERV_CODIGO` = `TIPO_SERVICIO`.`TSERV_CODIGO`))) where (`MARCELO_FECHA`.`FECHA` between _latin1'20120101' and _latin1'20120131');

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_UNIDADES`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_UNIDADES` AS select if((`UNIDAD3`.`UNI_CODIGO` = 20),`UNIDAD3`.`UNI_CODIGO`,`UNIDAD4`.`UNI_CODIGO`) AS `DIRECCION_CODIGO`,if((`UNIDAD3`.`UNI_CODIGO` = 20),`UNIDAD3`.`UNI_DESCRIPCION`,`UNIDAD4`.`UNI_DESCRIPCION`) AS `DIRECCION_DESCRIPCION`,if((`UNIDAD3`.`UNI_CODIGO` = 20),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD3`.`UNI_CODIGO`) AS `ZONA_CODIGO`,if((`UNIDAD3`.`UNI_CODIGO` = 20),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD3`.`UNI_DESCRIPCION`) AS `ZONA_DESCRIPCION`,if((`UNIDAD3`.`UNI_CODIGO` = 20),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD2`.`UNI_CODIGO`) AS `PREFECTURA_CODIGO`,if((`UNIDAD3`.`UNI_CODIGO` = 20),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`) AS `PREFECTURA_DESCRIPCION`,if((`UNIDAD3`.`UNI_CODIGO` = 20),`UNIDAD`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`) AS `COMISARIA_CODIGO`,if((`UNIDAD3`.`UNI_CODIGO` = 20),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`) AS `COMISARIA_DESCRIPCION`,`UNIDAD`.`UNI_CODIGO` AS `DESTACAMENTO_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION` AS `DESTACAMENTO_DESCRIPCION` from ((((`UNIDAD` `UNIDAD1` left join `UNIDAD` on((`UNIDAD`.`UNI_PADRE` = `UNIDAD1`.`UNI_CODIGO`))) left join `UNIDAD` `UNIDAD2` on((`UNIDAD1`.`UNI_PADRE` = `UNIDAD2`.`UNI_CODIGO`))) left join `UNIDAD` `UNIDAD3` on((`UNIDAD2`.`UNI_PADRE` = `UNIDAD3`.`UNI_CODIGO`))) left join `UNIDAD` `UNIDAD4` on((`UNIDAD3`.`UNI_PADRE` = `UNIDAD4`.`UNI_CODIGO`)));

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `VISTA_UNIDADES_2`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `VISTA_UNIDADES_2` AS select `UNIDAD`.`UNI_CODIGO` AS `DESTACAMENTO_CODIGO`,`UNIDAD`.`UNI_DESCRIPCION` AS `DESTACAMENTO_DESCRIPCION`,`UNIDAD`.`UNI_TIPOUNIDAD` AS `UNI_TIPOUNIDAD`,`UNIDAD`.`UNI_CODIGO_SIICGE` AS `COD_SIICGE`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_CODIGO`,`UNIDAD1`.`UNI_CODIGO`) AS `COMISARIA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`) AS `COMISARIA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_CODIGO`,`UNIDAD2`.`UNI_CODIGO`) AS `PREFECTURA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`) AS `PREFECTURA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_CODIGO`,`UNIDAD3`.`UNI_CODIGO`) AS `ZONA_CODIGO`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_DESCRIPCION`,`UNIDAD3`.`UNI_DESCRIPCION`) AS `ZONA_DESCRIPCION`,if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_ORDEN`,`UNIDAD3`.`UNI_ORDEN`) AS `ZONA_ORDEN`,if(((`UNIDAD4`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD3`.`UNI_CODIGO`,`UNIDAD4`.`UNI_CODIGO`) AS `SUPERZONA_CODIGO`,if(((`UNIDAD4`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD3`.`UNI_DESCRIPCION`,`UNIDAD4`.`UNI_DESCRIPCION`) AS `SUPERZONA_DESCRIPCION`,_latin1'20' AS `NACIONAL_CODIGO`,_latin1'NIVEL NACIONAL' AS `NACIONAL_DESCRIPCION` from ((((`UNIDAD` `UNIDAD1` join `UNIDAD` on((`UNIDAD`.`UNI_PADRE` = `UNIDAD1`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD2` on((`UNIDAD1`.`UNI_PADRE` = `UNIDAD2`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD3` on((`UNIDAD2`.`UNI_PADRE` = `UNIDAD3`.`UNI_CODIGO`))) join `UNIDAD` `UNIDAD4` on((`UNIDAD3`.`UNI_PADRE` = `UNIDAD4`.`UNI_CODIGO`))) where (isnull(`UNIDAD`.`UNI_TIPOUNIDAD`) or (`UNIDAD`.`UNI_TIPOUNIDAD` = 50)) order by if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD2`.`UNI_ORDEN`,`UNIDAD3`.`UNI_ORDEN`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD1`.`UNI_DESCRIPCION`,`UNIDAD2`.`UNI_DESCRIPCION`),if(((`UNIDAD3`.`UNI_CODIGO` = 20) or (`UNIDAD3`.`UNI_CODIGO` = 1950)),`UNIDAD`.`UNI_DESCRIPCION`,`UNIDAD1`.`UNI_DESCRIPCION`),`UNIDAD`.`UNI_DESCRIPCION`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
