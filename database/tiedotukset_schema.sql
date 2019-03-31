-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 31.03.2019 klo 09:22
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
-- Rakenne taululle `vrlv3_tiedotukset`
--

CREATE TABLE `vrlv3_tiedotukset` (
  `tid` int(11) NOT NULL,
  `aika` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `otsikko` text CHARACTER SET latin1 NOT NULL,
  `teksti` text CHARACTER SET latin1 NOT NULL,
  `lahettaja` int(5) UNSIGNED ZEROFILL NOT NULL,
  `julkinen` int(1) NOT NULL DEFAULT '1',
  `muokkaaja` int(5) UNSIGNED ZEROFILL NOT NULL COMMENT 'Viimeisin muokkaaja',
  `muokpvm` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_tiedotukset_kategoriat`
--

CREATE TABLE `vrlv3_tiedotukset_kategoriat` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `kid` int(11) NOT NULL,
  `kategoria` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vrlv3_tiedotukset`
--
ALTER TABLE `vrlv3_tiedotukset`
  ADD PRIMARY KEY (`tid`);

--
-- Indexes for table `vrlv3_tiedotukset_kategoriat`
--
ALTER TABLE `vrlv3_tiedotukset_kategoriat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kid` (`kid`),
  ADD KEY `kid_2` (`kid`),
  ADD KEY `tid` (`tid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vrlv3_tiedotukset`
--
ALTER TABLE `vrlv3_tiedotukset`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vrlv3_tiedotukset_kategoriat`
--
ALTER TABLE `vrlv3_tiedotukset_kategoriat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `vrlv3_tiedotukset`
--
ALTER TABLE `vrlv3_tiedotukset`
  ADD CONSTRAINT `vrlv3_tiedotukset_ibfk_1` FOREIGN KEY (`muokkaaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tiedotukset_ibfk_2` FOREIGN KEY (`lahettaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE;

--
-- Rajoitteet taululle `vrlv3_tiedotukset_kategoriat`
--
ALTER TABLE `vrlv3_tiedotukset_kategoriat`
  ADD CONSTRAINT `vrlv3_tiedotukset_kategoriat_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `vrlv3_tiedotukset` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vrlv3_tiedotukset_kategoriat_ibfk_2` FOREIGN KEY (`kid`) REFERENCES `vrlv3_lista_tiedotuskategoriat` (`kid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
