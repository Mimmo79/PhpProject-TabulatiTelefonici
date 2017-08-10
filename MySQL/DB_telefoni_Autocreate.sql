-- --------------------------------------------------------
-- Host:                         lnx023
-- Versione server:              5.1.73-0ubuntu0.10.04.1 - (Ubuntu)
-- S.O. server:                  debian-linux-gnu
-- HeidiSQL Versione:            9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dump della struttura del database telefonia
CREATE DATABASE IF NOT EXISTS `telefonia` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `telefonia`;

-- Dump della struttura di tabella telefonia.abb_dati
CREATE TABLE IF NOT EXISTS `abb_dati` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nSIM` varchar(50) NOT NULL,
  `cod` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `apn` varchar(50) NOT NULL,
  `data_conn` date NOT NULL,
  `ora` time NOT NULL,
  `durata` time NOT NULL,
  `byte` int(11) NOT NULL,
  `costo` double NOT NULL,
  `bundle` varchar(50) NOT NULL,
  KEY `Indice 1` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella telefonia.abb_voce
CREATE TABLE IF NOT EXISTS `abb_voce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nSIM` varchar(50) NOT NULL,
  `cod` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `direttrice` varchar(50) NOT NULL,
  `numeroChiamato` varchar(50) NOT NULL,
  `data_chiamata` date NOT NULL,
  `ora` time NOT NULL,
  `durata` time NOT NULL,
  `costo` double NOT NULL,
  KEY `Indice 1` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella telefonia.num_mobile
CREATE TABLE IF NOT EXISTS `num_mobile` (
  `idMobile` int(11) NOT NULL AUTO_INCREMENT,
  `numeroMobile` varchar(50) NOT NULL DEFAULT '0',
  `dataAttivazione` date DEFAULT NULL,
  `dataDisattivazione` date DEFAULT NULL,
  `dataInserimento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `Indice 1` (`idMobile`)
) ENGINE=MyISAM AUTO_INCREMENT=2121 DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella telefonia.ric_dati
CREATE TABLE IF NOT EXISTS `ric_dati` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nSIM` varchar(50) NOT NULL,
  `cod` int(11) NOT NULL,
  `data_conn` date NOT NULL,
  `durata` time NOT NULL,
  `direttrice` varchar(50) NOT NULL,
  `byte` int(11) NOT NULL,
  `costo` double NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `apn` varchar(50) NOT NULL,
  KEY `Indice 1` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1131 DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella telefonia.ric_riep
CREATE TABLE IF NOT EXISTS `ric_riep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nSIM` varchar(50) NOT NULL,
  `cod` int(11) NOT NULL,
  `direttrice` varchar(50) NOT NULL,
  `numeroChiamate` int(11) NOT NULL,
  `durata` time NOT NULL,
  `nonUsato` varchar(50) NOT NULL,
  `costo` double NOT NULL,
  `data_chiamata` date NOT NULL,
  KEY `Indice 1` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella telefonia.ric_voce
CREATE TABLE IF NOT EXISTS `ric_voce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nSIM` varchar(50) NOT NULL,
  `cod` int(11) NOT NULL,
  `data_chiamata` date NOT NULL,
  `ora` time NOT NULL,
  `numeroChiamato` varchar(50) NOT NULL,
  `durata` time NOT NULL,
  `costo` double NOT NULL,
  `direttrice` varchar(50) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  KEY `Indice 1` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11322 DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
