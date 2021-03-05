-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 31.03.2019 klo 09:21
-- Palvelimen versio: 10.1.28-MariaDB
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
-- Rakenne taululle `vrlv3_tallirekisteri`
--

CREATE TABLE `vrlv3_tallirekisteri` (
  `tnro` varchar(8) NOT NULL,
  `nimi` text NOT NULL,
  `url` text NOT NULL,
  `kuvaus` text NOT NULL,
  `perustettu` datetime NOT NULL,
  `hyvaksyi` int(5) UNSIGNED ZEROFILL NOT NULL,
  `piilotettu` tinyint(1) NOT NULL DEFAULT '0',
  `hyvaksytty` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lopettanut` int(1) NOT NULL DEFAULT '0',
  `lopetti_pvm` datetime DEFAULT NULL,
  `lopetti_tunnus` int(5) UNSIGNED ZEROFILL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_kategoriat`
--

CREATE TABLE `vrlv3_tallirekisteri_kategoriat` (
  `id` int(11) NOT NULL,
  `tnro` varchar(8) NOT NULL,
  `kategoria` smallint(2) NOT NULL,
  `anoi` int(5) UNSIGNED ZEROFILL NOT NULL,
  `hyvaksyi` int(5) UNSIGNED ZEROFILL NOT NULL,
  `lisatty` datetime NOT NULL,
  `tila` int(1) NOT NULL DEFAULT '0',
  `kasitelty` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_muutokset`
--

CREATE TABLE `vrlv3_tallirekisteri_muutokset` (
  `id` int(11) NOT NULL,
  `tnro` varchar(8) NOT NULL,
  `muokkasi` int(5) UNSIGNED ZEROFILL NOT NULL,
  `aika` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `field` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_omistajat`
--

CREATE TABLE `vrlv3_tallirekisteri_omistajat` (
  `id` int(11) NOT NULL,
  `tnro` varchar(8) NOT NULL,
  `omistaja` int(5) UNSIGNED ZEROFILL NOT NULL,
  `taso` smallint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tallirekisteri_paivitetty`
--

CREATE TABLE `vrlv3_tallirekisteri_paivitetty` (
  `id` int(11) NOT NULL,
  `tnro` varchar(8) NOT NULL,
  `paivitti` int(5) UNSIGNED ZEROFILL NOT NULL,
  `aika` datetime NOT NULL,
  `text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vrlv3_tallirekisteri`
--
ALTER TABLE `vrlv3_tallirekisteri`
  ADD PRIMARY KEY (`tnro`),
  ADD KEY `lopetti_tunnus` (`lopetti_tunnus`);

--
-- Indexes for table `vrlv3_tallirekisteri_kategoriat`
--
ALTER TABLE `vrlv3_tallirekisteri_kategoriat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tnro` (`tnro`),
  ADD KEY `tnro_2` (`tnro`),
  ADD KEY `kategoria` (`kategoria`),
  ADD KEY `kasittelija` (`anoi`),
  ADD KEY `lisaaja` (`hyvaksyi`);

--
-- Indexes for table `vrlv3_tallirekisteri_muutokset`
--
ALTER TABLE `vrlv3_tallirekisteri_muutokset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tnro` (`tnro`),
  ADD KEY `tnro_2` (`tnro`),
  ADD KEY `muokkasi` (`muokkasi`);

--
-- Indexes for table `vrlv3_tallirekisteri_omistajat`
--
ALTER TABLE `vrlv3_tallirekisteri_omistajat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tnro` (`tnro`),
  ADD KEY `omistaja` (`omistaja`),
  ADD KEY `tnro_2` (`tnro`),
  ADD KEY `omistaja_2` (`omistaja`);

--
-- Indexes for table `vrlv3_tallirekisteri_paivitetty`
--
ALTER TABLE `vrlv3_tallirekisteri_paivitetty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tnro` (`tnro`),
  ADD KEY `tnro_2` (`tnro`),
  ADD KEY `paivitti` (`paivitti`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vrlv3_tallirekisteri_kategoriat`
--
ALTER TABLE `vrlv3_tallirekisteri_kategoriat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrlv3_tallirekisteri_muutokset`
--
ALTER TABLE `vrlv3_tallirekisteri_muutokset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrlv3_tallirekisteri_omistajat`
--
ALTER TABLE `vrlv3_tallirekisteri_omistajat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrlv3_tallirekisteri_paivitetty`
--
ALTER TABLE `vrlv3_tallirekisteri_paivitetty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `vrlv3_tallirekisteri`
--
ALTER TABLE `vrlv3_tallirekisteri`
  ADD CONSTRAINT `vrlv3_tallirekisteri_ibfk_2` FOREIGN KEY (`lopetti_tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_kategoriat`
--
ALTER TABLE `vrlv3_tallirekisteri_kategoriat`
  ADD CONSTRAINT `vrlv3_tallirekisteri_kategoriat_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_kategoriat_ibfk_2` FOREIGN KEY (`kategoria`) REFERENCES `vrlv3_lista_tallikategoriat` (`kat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_kategoriat_ibfk_3` FOREIGN KEY (`anoi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_kategoriat_ibfk_4` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_muutokset`
--
ALTER TABLE `vrlv3_tallirekisteri_muutokset`
  ADD CONSTRAINT `vrlv3_tallirekisteri_muutokset_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_muutokset_ibfk_3` FOREIGN KEY (`muokkasi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_omistajat`
--
ALTER TABLE `vrlv3_tallirekisteri_omistajat`
  ADD CONSTRAINT `vrlv3_tallirekisteri_omistajat_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_omistajat_ibfk_2` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tallirekisteri_paivitetty`
--
ALTER TABLE `vrlv3_tallirekisteri_paivitetty`
  ADD CONSTRAINT `vrlv3_tallirekisteri_paivitetty_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tallirekisteri_paivitetty_ibfk_2` FOREIGN KEY (`paivitti`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
