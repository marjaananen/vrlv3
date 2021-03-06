-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 31.05.2019 klo 09:48
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
-- Rakenne taululle `vrlv3_kasvattajanimet`
--

DROP TABLE IF EXISTS `vrlv3_kasvattajanimet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kasvattajanimet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kasvattajanimi` varchar(45) NOT NULL,
  `rekisteroity` datetime DEFAULT CURRENT_TIMESTAMP,
  `tnro` varchar(8) DEFAULT NULL,
  `tila` int(11) DEFAULT '1',
  `rekisteroi` INT(5) UNSIGNED ZEROFILL NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vrlv3_kasvattajanimet_talli_idx` (`tnro`),
  KEY  `vrlv3_kasvattajanimet_rekisteroi` (`rekisteroi` ASC),
  CONSTRAINT `vrlv3_kasvattajanimet_rekisteroi_f`
  FOREIGN KEY (`rekisteroi`)
  REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
  ON DELETE RESTRICT
  ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_kasvattajanimet_talli` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_kasvattajanimet_omistajat`
--

CREATE TABLE `vrlv3_kasvattajanimet_omistajat` (
  `kid` int(11) NOT NULL,
  `tunnus` int(5) UNSIGNED ZEROFILL NOT NULL,
  `taso` int(1) NOT NULL DEFAULT '1'

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_kasvattajanimet_rodut`
--

CREATE TABLE `vrlv3_kasvattajanimet_rodut` (
  `kid` int(11) NOT NULL,
  `rotu` smallint(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vrlv3_kasvattajanimet`
--


--
-- Indexes for table `vrlv3_kasvattajanimet_omistajat`
--
ALTER TABLE `vrlv3_kasvattajanimet_omistajat`
  ADD PRIMARY KEY (`kid`,`tunnus`),
  ADD KEY `vrlv3_kasvattajanimet_om_idx` (`kid`),
  ADD KEY `vrlv3_kasvattajanimet_om_idx2` (`tunnus`);

--
-- Indexes for table `vrlv3_kasvattajanimet_rodut`
--
ALTER TABLE `vrlv3_kasvattajanimet_rodut`
  ADD PRIMARY KEY (`kid`,`rotu`),
  ADD KEY `vrlv3_kasvattajanimet_rod_idx` (`kid`),
  ADD KEY `vrlv3_kasvattajanimet_rod_idx2` (`rotu`);

--
-- AUTO_INCREMENT for dumped tables
--

--

--
-- Rajoitteet taululle `vrlv3_kasvattajanimet_omistajat`
--
ALTER TABLE `vrlv3_kasvattajanimet_omistajat`
  ADD CONSTRAINT `vrlv3_kasvattajanimet_omistajat_knimi` FOREIGN KEY (`kid`) REFERENCES `vrlv3_kasvattajanimet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_omistajat_tunnus` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_kasvattajanimet_rodut`
--
ALTER TABLE `vrlv3_kasvattajanimet_rodut`
  ADD CONSTRAINT `vrlv3_kasvattajanimet_rodut_knimi` FOREIGN KEY (`kid`) REFERENCES `vrlv3_kasvattajanimet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_kasvattajanimet_rodut_rotu` FOREIGN KEY (`rotu`) REFERENCES `vrlv3_lista_rodut` (`rotunro`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
