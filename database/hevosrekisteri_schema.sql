-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 31.03.2019 klo 09:23
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
-- Rakenne taululle `vrlv3_hevosrekisteri`
--

CREATE TABLE `vrlv3_hevosrekisteri` (
  `reknro` int(9) UNSIGNED ZEROFILL NOT NULL,
  `nimi` varchar(80) CHARACTER SET utf8 NOT NULL,
  `rotu` smallint(3) NOT NULL,
  `sukupuoli` enum('1','2','3','') CHARACTER SET utf8 NOT NULL,
  `sakakorkeus` smallint(3) NOT NULL,
  `syntymaaika` datetime NOT NULL,
  `vari` smallint(4) DEFAULT NULL,
  `painotus` smallint(2) DEFAULT NULL,
  `syntymamaa` smallint(4) DEFAULT NULL,
  `url` text CHARACTER SET utf8 NOT NULL,
  `rekisteroity` datetime NOT NULL,
  `hyvaksyi` int(5) UNSIGNED ZEROFILL NOT NULL,
  `kotitalli` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `kuollut` int(1) NOT NULL DEFAULT '0',
  `kuol_merkkasi` int(5) UNSIGNED ZEROFILL NOT NULL,
  `kuol_pvm` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  ADD PRIMARY KEY (`reknro`),
  ADD KEY `rotu` (`rotu`),
  ADD KEY `hyvaksyi` (`hyvaksyi`),
  ADD KEY `vrlv3_hevosrekisteri_ibfk_3` (`kotitalli`),
  ADD KEY `kuol_merkkasi` (`kuol_merkkasi`),
  ADD KEY `vari` (`vari`),
  ADD KEY `painotus` (`painotus`),
  ADD KEY `syntymamaa` (`syntymamaa`);

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
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_2` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_3` FOREIGN KEY (`kotitalli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_4` FOREIGN KEY (`kuol_merkkasi`) REFERENCES `vrlv3_tunnukset` (`tunnus`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_5` FOREIGN KEY (`vari`) REFERENCES `vrlv3_lista_varit` (`vid`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_6` FOREIGN KEY (`painotus`) REFERENCES `vrlv3_lista_painotus` (`pid`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_ibfk_7` FOREIGN KEY (`syntymamaa`) REFERENCES `vrlv3_lista_maat` (`id`);

--
-- Rajoitteet taululle `vrlv3_hevosrekisteri_omistajat`
--
ALTER TABLE `vrlv3_hevosrekisteri_omistajat`
  ADD CONSTRAINT `vrlv3_hevosrekisteri_omistajat_ibfk_1` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_omistajat_ibfk_2` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`);

--
-- Rajoitteet taululle `vrlv3_hevosrekisteri_sukutaulut`
--
ALTER TABLE `vrlv3_hevosrekisteri_sukutaulut`
  ADD CONSTRAINT `vanhemmat` FOREIGN KEY (`e_nro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_sukutaulut_ibfk_1` FOREIGN KEY (`i_nro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`),
  ADD CONSTRAINT `vrlv3_hevosrekisteri_sukutaulut_ibfk_2` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
