-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 31.05.2019 klo 09:50
-- Palvelimen versio: 10.1.40-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vrl`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_hevosrekisteri`
--

DROP TABLE IF EXISTS `vrlv3_hevosrekisteri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_hevosrekisteri` (
  `reknro` int(9) unsigned zerofill NOT NULL,
  `nimi` varchar(80) NOT NULL,
  `rotu` smallint(3) NOT NULL,
  `sukupuoli` enum('1','2','3','') NOT NULL,
  `sakakorkeus` smallint(3) DEFAULT NULL,
  `syntymaaika` datetime NOT NULL,
  `vari` smallint(4) DEFAULT NULL,
  `painotus` smallint(2) DEFAULT NULL,
  `syntymamaa` smallint(4) DEFAULT NULL,
  `url` text NOT NULL,
  `rekisteroity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  `kotitalli` varchar(8) DEFAULT NULL,
  `kuollut` int(1) NOT NULL DEFAULT '0',
  `kuol_merkkasi` int(5) unsigned zerofill DEFAULT NULL,
  `kuol_pvm` datetime DEFAULT NULL,
  `kasvattajanimi` varchar(25) CHARACTER SET latin1 DEFAULT NULL,
  `kasvattajanimi_id` int(8) DEFAULT NULL,
  `kasvattaja_talli` varchar(8) DEFAULT NULL,
  `kasvattaja_tunnus` int(5) unsigned zerofill DEFAULT NULL,
  `porr_kilpailee` int(1) NOT NULL DEFAULT '1',
  `polv_tark` INT(1) UNSIGNED NULL DEFAULT 0,
  `polv_tark_vrl` INT(5) UNSIGNED ZEROFILL NULL DEFAULT NULL,
  `polv_tark_date` DATETIME NULL DEFAULT NULL,
  `polv_pros` DECIMAL(11,8) NULL DEFAULT NULL,

  PRIMARY KEY (`reknro`)


) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_hevosrekisteri_omistajat`
--

CREATE TABLE `vrlv3_hevosrekisteri_omistajat` (
  `reknro` int(9) UNSIGNED ZEROFILL NOT NULL,
  `omistaja` int(5) UNSIGNED ZEROFILL NOT NULL,
  `taso` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_hevosrekisteri_sukutaulut`
--

CREATE TABLE `vrlv3_hevosrekisteri_sukutaulut` (
  `reknro` int(9) UNSIGNED ZEROFILL NOT NULL,
  `i_nro` int(9) UNSIGNED ZEROFILL DEFAULT NULL,
  `e_nro` int(9) UNSIGNED ZEROFILL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vrlv3_hevosrekisteri`
--
ALTER TABLE `vrlv3_hevosrekisteri`
  ADD KEY `rotu` (`rotu`),
  ADD KEY `hyvaksyi` (`hyvaksyi`),
  ADD KEY `vrlv3_hevosrekisteri_ibfk_3` (`kotitalli`),
  ADD KEY `kuol_merkkasi` (`kuol_merkkasi`),
  ADD KEY `vari` (`vari`),
  ADD KEY `painotus` (`painotus`),
  ADD KEY `syntymamaa` (`syntymamaa`),
  ADD KEY `vrlv3_hevosrekisteri_ibfk_8_idx` (`kasvattaja_tunnus`),
  ADD KEY `vrlv3_hevosrekisteri_ibfk_9_idx` (`kasvattaja_talli`),
  ADD KEY `vrlv3_hevosrekisteri_ibfk_10_idx` (`kasvattajanimi_id`),
  ADD  KEY `vrlv3_hevosrekisteri_ibfk_6_idx` (`polv_tark_vrl`);


--
-- Indexes for table `vrlv3_hevosrekisteri_omistajat`
--
ALTER TABLE `vrlv3_hevosrekisteri_omistajat`
  ADD KEY `reknro` (`reknro`),
  ADD KEY `omistaja` (`omistaja`);

--
-- Indexes for table `vrlv3_hevosrekisteri_sukutaulut`
--
ALTER TABLE `vrlv3_hevosrekisteri_sukutaulut`
  ADD PRIMARY KEY (`reknro`),
  ADD KEY `vanhemmat` (`e_nro`),
  ADD KEY `i_nro` (`i_nro`);

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `vrlv3_hevosrekisteri`
--
ALTER TABLE `vrlv3_hevosrekisteri`
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_1` FOREIGN KEY (`rotu`) REFERENCES `vrlv3_lista_rodut` (`rotunro`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_10` FOREIGN KEY (`kasvattajanimi_id`) REFERENCES `vrlv3_kasvattajanimet` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_11` FOREIGN KEY (`kotitalli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_12` FOREIGN KEY (`kasvattaja_tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_2` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_3` FOREIGN KEY (`kotitalli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_4` FOREIGN KEY (`kuol_merkkasi`) REFERENCES `vrlv3_tunnukset` (`tunnus`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_5` FOREIGN KEY (`vari`) REFERENCES `vrlv3_lista_varit` (`vid`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_6` FOREIGN KEY (`painotus`) REFERENCES `vrlv3_lista_painotus` (`pid`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_7` FOREIGN KEY (`syntymamaa`) REFERENCES `vrlv3_lista_maat` (`id`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_8` FOREIGN KEY (`kasvattaja_tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_9` FOREIGN KEY (`kasvattaja_talli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_13` FOREIGN KEY (`polv_tark_vrl`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_hevosrekisteri_omistajat`
--
ALTER TABLE `vrlv3_hevosrekisteri_omistajat`
  ADD CONSTRAINT `vrlv3_hevosrekisteri_omistajat_ibfk_1` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_hevosrekisteri_omistajat_ibfk_2` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_hevosrekisteri_sukutaulut`
--
ALTER TABLE `vrlv3_hevosrekisteri_sukutaulut`
  ADD CONSTRAINT `vanhemmat` FOREIGN KEY (`e_nro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_sukutaulut_ibfk_1` FOREIGN KEY (`i_nro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_sukutaulut_ibfk_2` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE;
  
  
  CREATE TABLE `vrlv3`.`vrlv3_hevosrekisteri_ominaisuudet` (
  `reknro` INT(9) UNSIGNED ZEROFILL NOT NULL,
  `ominaisuus` INT(8) NULL,
  `arvo` DECIMAL(8,2) NOT NULL,
  INDEX `prime` (`reknro` ASC, `ominaisuus` ASC),
  INDEX `HEVO` (`reknro` ASC),
  INDEX `OMINAISUUS_idx` (`ominaisuus` ASC),
  CONSTRAINT `REKNRO`
    FOREIGN KEY (`reknro`)
    REFERENCES `vrlv3`.`vrlv3_hevosrekisteri` (`reknro`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `OMINAISUUS`
    FOREIGN KEY (`ominaisuus`)
    REFERENCES `vrlv3`.`vrlv3_lista_ominaisuudet` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
  
  
  CREATE TABLE `vrlv3`.`vrlv3_hevosrekisteri_ikaantyminen` (
  `reknro` INT(9) UNSIGNED ZEROFILL NOT NULL,
  `3vuotta` DATE NULL DEFAULT '00000-00-00',
  `4vuotta` DATE NULL DEFAULT '00000-00-00',
  `5vuotta` DATE NULL DEFAULT '00000-00-00',
  `6vuotta` DATE NULL DEFAULT '00000-00-00',
  `7vuotta` DATE NULL DEFAULT '00000-00-00',
  `8vuotta` DATE NULL DEFAULT '00000-00-00',
  `ikaantyminen_d` INT(3) NULL DEFAULT 0,
  PRIMARY KEY (`reknro`),
  CONSTRAINT `reknro_key`
    FOREIGN KEY (`reknro`)
    REFERENCES `vrlv3`.`vrlv3_hevosrekisteri` (`reknro`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);
  
  
  CREATE TABLE `vrlv3`.`vrlv3_hevosrekisteri_kisatiedot` (
  `reknro` INT(9) ZEROFILL UNSIGNED NOT NULL,
  `jaos` INT(11) NOT NULL,
  `taso_max` INT NOT NULL DEFAULT 10,
  `os` INT NOT NULL DEFAULT 0,
  `sij` INT NOT NULL DEFAULT 0,
  `voi` INT NOT NULL DEFAULT 0,
  `porr_os` INT NOT NULL DEFAULT 0,
  `porr_sij` INT NOT NULL DEFAULT 0,
  `porr_voi` INT NOT NULL DEFAULT 0,
  INDEX `jaos_idx` (`jaos` ASC),
  INDEX `reknro_idx` (`reknro` ASC),
  INDEX `primary_idx` (`reknro` ASC, `jaos` ASC),
  CONSTRAINT `jaosf`
    FOREIGN KEY (`jaos`)
    REFERENCES `vrlv3`.`vrlv3_kisat_jaokset` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `reknrof`
    FOREIGN KEY (`reknro`)
    REFERENCES `vrlv3`.`vrlv3_hevosrekisteri` (`reknro`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);




COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
