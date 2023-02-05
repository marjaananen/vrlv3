

DROP TABLE IF EXISTS `vrlv3_hevosrekisteri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_hevosrekisteri` (
  `reknro` int(9) unsigned zerofill NOT NULL,
  `nimi` varchar(80) NOT NULL,
  `rotu` smallint(6) NOT NULL,
  `sukupuoli` enum('1','2','3','') NOT NULL,
  `sakakorkeus` smallint(6) DEFAULT NULL,
  `syntymaaika` datetime NOT NULL,
  `vari` smallint(6) DEFAULT NULL,
  `painotus` smallint(6) DEFAULT NULL,
  `syntymamaa` smallint(6) DEFAULT NULL,
  `url` text NOT NULL,
  `rekisteroity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  `kotitalli` varchar(8) DEFAULT NULL,
  `kuollut` int(11) NOT NULL DEFAULT '0',
  `kuol_merkkasi` int(5) unsigned zerofill DEFAULT NULL,
  `kuol_pvm` datetime DEFAULT NULL,
  `kasvattajanimi` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `kasvattajanimi_id` int(11) DEFAULT NULL,
  `kasvattaja_talli` varchar(8) DEFAULT NULL,
  `kasvattaja_tunnus` int(5) unsigned zerofill DEFAULT NULL,
  `porr_kilpailee` int(11) NOT NULL DEFAULT '1',
  `polv_tark` int(10) unsigned DEFAULT '0',
  `polv_tark_vrl` int(5) unsigned zerofill DEFAULT NULL,
  `polv_tark_date` datetime DEFAULT NULL,
  `polv_pros` decimal(11,8) DEFAULT NULL,
  `evm` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`reknro`),
  KEY `rotu` (`rotu`),
  KEY `hyvaksyi` (`hyvaksyi`),
  KEY `vrlv3_hevosrekisteri_ibfk_3` (`kotitalli`),
  KEY `kuol_merkkasi` (`kuol_merkkasi`),
  KEY `vari` (`vari`),
  KEY `painotus` (`painotus`),
  KEY `syntymamaa` (`syntymamaa`),
  KEY `vrlv3_hevosrekisteri_ibfk_8_idx` (`kasvattaja_tunnus`),
  KEY `vrlv3_hevosrekisteri_ibfk_9_idx` (`kasvattaja_talli`),
  KEY `vrlv3_hevosrekisteri_ibfk_10_idx` (`kasvattajanimi_id`),
  KEY `vrlv3_hevosrekisteri_ibfk_6_idx` (`polv_tark_vrl`),
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_1` FOREIGN KEY (`rotu`) REFERENCES `vrlv3_lista_rodut` (`rotunro`),
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_10` FOREIGN KEY (`kasvattajanimi_id`) REFERENCES `vrlv3_kasvattajanimet` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_11` FOREIGN KEY (`kotitalli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_12` FOREIGN KEY (`kasvattaja_tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_13` FOREIGN KEY (`polv_tark_vrl`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_2` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`),
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_3` FOREIGN KEY (`kotitalli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_4` FOREIGN KEY (`kuol_merkkasi`) REFERENCES `vrlv3_tunnukset` (`tunnus`),
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_5` FOREIGN KEY (`vari`) REFERENCES `vrlv3_lista_varit` (`vid`),
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_6` FOREIGN KEY (`painotus`) REFERENCES `vrlv3_lista_painotus` (`pid`),
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_7` FOREIGN KEY (`syntymamaa`) REFERENCES `vrlv3_lista_maat` (`id`),
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_8` FOREIGN KEY (`kasvattaja_tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `vrlv3_hevosrekisteri_ibfk_9` FOREIGN KEY (`kasvattaja_talli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_hevosrekisteri_ikaantyminen`
--

DROP TABLE IF EXISTS `vrlv3_hevosrekisteri_ikaantyminen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_hevosrekisteri_ikaantyminen` (
  `reknro` int(9) unsigned zerofill NOT NULL,
  `3vuotta` date DEFAULT '0000-00-00',
  `4vuotta` date DEFAULT '0000-00-00',
  `5vuotta` date DEFAULT '0000-00-00',
  `6vuotta` date DEFAULT '0000-00-00',
  `7vuotta` date DEFAULT '0000-00-00',
  `8vuotta` date DEFAULT '0000-00-00',
  `ikaantyminen_d` int(11) DEFAULT '0',
  PRIMARY KEY (`reknro`),
  CONSTRAINT `reknro_key` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_hevosrekisteri_kisatiedot`
--

DROP TABLE IF EXISTS `vrlv3_hevosrekisteri_kisatiedot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_hevosrekisteri_kisatiedot` (
  `reknro` int(9) unsigned zerofill NOT NULL,
  `jaos` int(11) NOT NULL,
  `taso_max` int(11) NOT NULL DEFAULT '10',
  `os` int(11) NOT NULL DEFAULT '0',
  `sij` int(11) NOT NULL DEFAULT '0',
  `voi` int(11) NOT NULL DEFAULT '0',
  `porr_os` int(11) NOT NULL DEFAULT '0',
  `porr_sij` int(11) NOT NULL DEFAULT '0',
  `porr_voi` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`reknro`,`jaos`),
  KEY `jaos_idx` (`jaos`),
  KEY `reknro_idx` (`reknro`),
  KEY `primary_idx` (`reknro`,`jaos`),
  CONSTRAINT `jaosf` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`),
  CONSTRAINT `reknrof` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_hevosrekisteri_ominaisuudet`
--

DROP TABLE IF EXISTS `vrlv3_hevosrekisteri_ominaisuudet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_hevosrekisteri_ominaisuudet` (
  `reknro` int(9) unsigned zerofill NOT NULL,
  `ominaisuus` int(11) NOT NULL,
  `arvo` decimal(8,2) NOT NULL,
  PRIMARY KEY (`reknro`,`ominaisuus`),
  KEY `prime` (`reknro`,`ominaisuus`),
  KEY `HEVO` (`reknro`),
  KEY `OMINAISUUS_idx` (`ominaisuus`),
  CONSTRAINT `OMINAI-suus` FOREIGN KEY (`ominaisuus`) REFERENCES `vrlv3_lista_ominaisuudet` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `REKNRO` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_hevosrekisteri_ominaisuudet_jonossa`
--

DROP TABLE IF EXISTS `vrlv3_hevosrekisteri_ominaisuudet_jonossa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_hevosrekisteri_ominaisuudet_jonossa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reknro` int(9) unsigned zerofill NOT NULL,
  `ominaisuus` int(8) NOT NULL,
  `arvo` decimal(8,2) NOT NULL,
  `tulos_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hevosrekisteri_reknro` (`reknro`),
  KEY `hevosrekisteri_ominaisuus` (`ominaisuus`),
  KEY `kisakalenteri_tulos` (`tulos_id`),
  CONSTRAINT `ominaisuusfff` FOREIGN KEY (`ominaisuus`) REFERENCES `vrlv3_lista_ominaisuudet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reknrofff` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tulosfff` FOREIGN KEY (`tulos_id`) REFERENCES `vrlv3_kisat_tulokset` (`tulos_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=177480 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_hevosrekisteri_omistajamuutokset`
--

DROP TABLE IF EXISTS `vrlv3_hevosrekisteri_omistajamuutokset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_hevosrekisteri_omistajamuutokset` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `reknro` int(9) unsigned zerofill NOT NULL,
  `omistaja` int(5) unsigned zerofill NOT NULL,
  `aika` datetime NOT NULL,
  `muokkasi` int(5) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id`),
  KEY `omistaja` (`omistaja`),
  KEY `omistajamuutos_hevonen` (`reknro`),
  KEY `omistajamuutos_omistaja` (`omistaja`),
  KEY `omistajamuutos_muokkasi` (`muokkasi`),
  CONSTRAINT `omistajamuutokset_hevo_f` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `omistajamuutokset_muokkasi_f` FOREIGN KEY (`muokkasi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  CONSTRAINT `omistajamuutokset_omistaja_f` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63266 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_hevosrekisteri_omistajat`
--

DROP TABLE IF EXISTS `vrlv3_hevosrekisteri_omistajat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_hevosrekisteri_omistajat` (
  `reknro` int(9) unsigned zerofill NOT NULL,
  `omistaja` int(5) unsigned zerofill NOT NULL,
  `taso` int(11) NOT NULL,
  PRIMARY KEY (`reknro`,`omistaja`),
  KEY `reknro` (`reknro`),
  KEY `omistaja` (`omistaja`),
  CONSTRAINT `vrlv3_hevosrekisteri_omistajat_ibfk_1` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_hevosrekisteri_omistajat_ibfk_2` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_hevosrekisteri_sukutaulut`
--

DROP TABLE IF EXISTS `vrlv3_hevosrekisteri_sukutaulut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_hevosrekisteri_sukutaulut` (
  `reknro` int(9) unsigned zerofill NOT NULL,
  `i_nro` int(9) unsigned zerofill DEFAULT NULL,
  `e_nro` int(9) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`reknro`),
  KEY `vanhemmat` (`e_nro`),
  KEY `i_nro` (`i_nro`),
  CONSTRAINT `vanhemmat` FOREIGN KEY (`e_nro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`),
  CONSTRAINT `vrlv3_hevosrekisteri_sukutaulut_ibfk_1` FOREIGN KEY (`i_nro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`),
  CONSTRAINT `vrlv3_hevosrekisteri_sukutaulut_ibfk_2` FOREIGN KEY (`reknro`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kasvattajanimet`
--

DROP TABLE IF EXISTS `vrlv3_kasvattajanimet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kasvattajanimet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kasvattajanimi` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `rekisteroity` datetime DEFAULT CURRENT_TIMESTAMP,
  `tnro` varchar(8) DEFAULT NULL,
  `tila` int(11) DEFAULT '1',
  `rekisteroi` int(5) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vrlv3_kasvattajanimet_talli_idx` (`tnro`),
  KEY `vrlv3_kasvattajanimet_rekisteroi` (`rekisteroi`),
  CONSTRAINT `vrlv3_kasvattajanimet_rekisteroi_f` FOREIGN KEY (`rekisteroi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_kasvattajanimet_talli` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3091 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kasvattajanimet_omistajat`
--

DROP TABLE IF EXISTS `vrlv3_kasvattajanimet_omistajat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kasvattajanimet_omistajat` (
  `kid` int(11) NOT NULL,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `taso` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`kid`,`tunnus`),
  KEY `vrlv3_kasvattajanimet_om_idx` (`kid`),
  KEY `vrlv3_kasvattajanimet_om_idx2` (`tunnus`),
  CONSTRAINT `vrlv3_kasvattajanimet_omistajat_knimi` FOREIGN KEY (`kid`) REFERENCES `vrlv3_kasvattajanimet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_kasvattajanimet_omistajat_tunnus` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kasvattajanimet_rodut`
--

DROP TABLE IF EXISTS `vrlv3_kasvattajanimet_rodut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kasvattajanimet_rodut` (
  `kid` int(11) NOT NULL,
  `rotu` smallint(6) NOT NULL,
  PRIMARY KEY (`kid`,`rotu`),
  KEY `vrlv3_kasvattajanimet_rod_idx` (`kid`),
  KEY `vrlv3_kasvattajanimet_rod_idx2` (`rotu`),
  CONSTRAINT `vrlv3_kasvattajanimet_rodut_knimi` FOREIGN KEY (`kid`) REFERENCES `vrlv3_kasvattajanimet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_kasvattajanimet_rodut_rotu` FOREIGN KEY (`rotu`) REFERENCES `vrlv3_lista_rodut` (`rotunro`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_bis_tulosrivit`
--

DROP TABLE IF EXISTS `vrlv3_kisat_bis_tulosrivit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_bis_tulosrivit` (
  `tulosrivi_id` int(11) NOT NULL AUTO_INCREMENT,
  `bis_id` int(11) NOT NULL,
  `nayttely_id` int(11) NOT NULL,
  `palkinto` varchar(100) CHARACTER SET latin1 NOT NULL,
  `vh` int(9) unsigned zerofill NOT NULL,
  `vh_nimi` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `vh_id` int(9) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`tulosrivi_id`),
  KEY `bis_id` (`bis_id`,`nayttely_id`,`vh`),
  KEY `vh_id` (`vh_id`),
  CONSTRAINT `bis_idxx` FOREIGN KEY (`bis_id`) REFERENCES `vrlv3_kisat_nayttelytulokset` (`bis_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vh_idxx` FOREIGN KEY (`vh_id`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77254 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_etuuspisteet`
--

DROP TABLE IF EXISTS `vrlv3_kisat_etuuspisteet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_etuuspisteet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `jaos` int(11) NOT NULL,
  `pisteet` double(6,2) NOT NULL,
  `nollattu` tinyint(1) DEFAULT NULL,
  `muokattu` datetime DEFAULT NULL,
  `muokkaaja` int(5) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tunnusjaosunique` (`tunnus`,`jaos`),
  KEY `tunnusdidx` (`tunnus`),
  KEY `jaosidx` (`jaos`),
  KEY `muokkaajaindex` (`muokkaaja`),
  CONSTRAINT `jaosetuusforein` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `muokkaajaforein` FOREIGN KEY (`muokkaaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`),
  CONSTRAINT `tunnusetuusforein` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6150 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_jaokset`
--

DROP TABLE IF EXISTS `vrlv3_kisat_jaokset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_jaokset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nimi` varchar(45) NOT NULL,
  `lyhenne` varchar(10) NOT NULL,
  `toiminnassa` tinyint(4) NOT NULL DEFAULT '1',
  `url` text,
  `kuvaus` text,
  `laji` smallint(6) NOT NULL,
  `s_salli_porrastetut` tinyint(4) NOT NULL DEFAULT '0',
  `s_luokkia_per_kisa_max` int(11) NOT NULL DEFAULT '15',
  `s_luokkia_per_kisa_max_norm` int(11) NOT NULL DEFAULT '15',
  `s_luokkia_per_kisa_min` int(11) NOT NULL DEFAULT '1',
  `s_hevosia_per_luokka_max` int(11) NOT NULL DEFAULT '100',
  `s_hevosia_per_luokka_min` int(11) NOT NULL DEFAULT '30',
  `s_luokkia_per_hevonen_max` int(11) NOT NULL DEFAULT '2',
  `s_luokkia_per_hevonen_min` int(11) NOT NULL DEFAULT '1',
  `nayttelyt` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `laji_idx` (`laji`),
  CONSTRAINT `laji` FOREIGN KEY (`laji`) REFERENCES `vrlv3_lista_painotus` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_jaokset_ominaisuudet`
--

DROP TABLE IF EXISTS `vrlv3_kisat_jaokset_ominaisuudet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_jaokset_ominaisuudet` (
  `jaos` int(11) NOT NULL AUTO_INCREMENT,
  `ominaisuus` int(11) NOT NULL,
  PRIMARY KEY (`jaos`,`ominaisuus`),
  KEY `ominaisuusnimi_idx` (`ominaisuus`),
  CONSTRAINT `jaosnimi` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`),
  CONSTRAINT `ominaisuusnimi` FOREIGN KEY (`ominaisuus`) REFERENCES `vrlv3_lista_ominaisuudet` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_jaokset_omistajat`
--

DROP TABLE IF EXISTS `vrlv3_kisat_jaokset_omistajat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_jaokset_omistajat` (
  `jid` int(11) NOT NULL,
  `tunnus` int(10) unsigned NOT NULL,
  `taso` int(11) DEFAULT '0',
  PRIMARY KEY (`jid`,`tunnus`),
  KEY `vrltunnus_idx` (`tunnus`),
  CONSTRAINT `jaostunnus` FOREIGN KEY (`jid`) REFERENCES `vrlv3_kisat_jaokset` (`id`),
  CONSTRAINT `vrltunnus` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_jaokset_palkinnot`
--

DROP TABLE IF EXISTS `vrlv3_kisat_jaokset_palkinnot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_jaokset_palkinnot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kuvaus` text NOT NULL,
  `jaos` int(11) NOT NULL,
  `palkinto` varchar(32) NOT NULL,
  `kaytossa` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `jarjnro` int(3) unsigned NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`),
  KEY `jaos_idxi` (`jaos`),
  CONSTRAINT `jaos_linki` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_kisakalenteri`
--

DROP TABLE IF EXISTS `vrlv3_kisat_kisakalenteri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_kisakalenteri` (
  `kisa_id` int(11) NOT NULL AUTO_INCREMENT,
  `vip` datetime NOT NULL,
  `kp` datetime NOT NULL,
  `laji` smallint(2) NOT NULL,
  `jaos` int(11) NOT NULL,
  `url` tinytext CHARACTER SET latin1 NOT NULL,
  `info` mediumtext CHARACTER SET latin1 NOT NULL,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `jarj_talli` varchar(8) NOT NULL,
  `jarj_seura` int(10) DEFAULT NULL,
  `arvontatapa` int(2) DEFAULT NULL,
  `takaaja` int(5) unsigned zerofill DEFAULT NULL,
  `ilmoitettu` datetime NOT NULL,
  `seuralle` int(11) DEFAULT NULL,
  `hyvaksytty` datetime DEFAULT NULL,
  `kasitelty` datetime DEFAULT NULL,
  `kasittelija` int(5) unsigned zerofill DEFAULT NULL,
  `tulokset` int(1) NOT NULL DEFAULT '0',
  `hyvaksyi` int(5) unsigned zerofill DEFAULT NULL,
  `seura_hyv` int(1) DEFAULT NULL,
  `siirretty` int(5) unsigned zerofill DEFAULT NULL,
  `vanha` int(1) NOT NULL DEFAULT '0',
  `porrastettu` int(1) NOT NULL DEFAULT '0',
  `s_luokkia_per_hevonen` int(3) DEFAULT NULL,
  `s_hevosia_per_luokka` int(3) DEFAULT NULL,
  PRIMARY KEY (`kisa_id`),
  KEY `jaoss` (`jaos`),
  KEY `lajii` (`laji`),
  KEY `talli_key_idxi` (`jarj_talli`),
  KEY `tunnus_key_idxi` (`tunnus`),
  KEY `kasittelija_idx` (`kasittelija`),
  CONSTRAINT `jaos_keyi` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `laji_keyi` FOREIGN KEY (`laji`) REFERENCES `vrlv3_lista_painotus` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `talli_keyi` FOREIGN KEY (`jarj_talli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tunnus_keyi` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=171178 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_kisaluokat`
--

DROP TABLE IF EXISTS `vrlv3_kisat_kisaluokat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_kisaluokat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kisa_id` int(11) NOT NULL,
  `luokka_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kisaluokat_kisaid` (`kisa_id`),
  KEY `kisaluokat_luokkaid` (`luokka_id`),
  CONSTRAINT `kisaluokkaf` FOREIGN KEY (`luokka_id`) REFERENCES `vrlv3_kisat_luokat` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `kisaluokkaff` FOREIGN KEY (`kisa_id`) REFERENCES `vrlv3_kisat_kisakalenteri` (`kisa_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=47306 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_kisaosallis`
--

DROP TABLE IF EXISTS `vrlv3_kisat_kisaosallis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_kisaosallis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kisa_id` int(11) NOT NULL,
  `kisaluokka_id` int(11) NOT NULL,
  `VH` int(9) unsigned zerofill NOT NULL,
  `VRL` int(5) unsigned zerofill NOT NULL,
  `rimpsu` varchar(220) NOT NULL,
  `osallistui` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kisa_id` (`kisa_id`),
  KEY `kisaluokka_id` (`kisaluokka_id`),
  KEY `VH` (`VH`),
  KEY `VRL` (`VRL`),
  CONSTRAINT `kilvat_kisaosallis_ibfk_1` FOREIGN KEY (`kisa_id`) REFERENCES `vrlv3_kisat_kisakalenteri` (`kisa_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `kilvat_kisaosallis_ibfk_2` FOREIGN KEY (`kisaluokka_id`) REFERENCES `vrlv3_kisat_kisaluokat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `kilvat_kisaosallis_ibfk_3` FOREIGN KEY (`VH`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `kilvat_kisaosallis_ibfk_4` FOREIGN KEY (`VRL`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=377273 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_luokat`
--

DROP TABLE IF EXISTS `vrlv3_kisat_luokat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_luokat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nimi` text NOT NULL,
  `jaos` int(11) NOT NULL,
  `laji` smallint(6) DEFAULT NULL,
  `porrastettu` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `taso` int(11) NOT NULL,
  `aste` int(11) NOT NULL,
  `minheight` int(11) DEFAULT NULL,
  `min_age` int(10) unsigned NOT NULL DEFAULT '3',
  `kaytossa` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `jarjnro` int(10) unsigned NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`),
  KEY `jaos_idx` (`jaos`),
  KEY `laji_idx` (`laji`),
  CONSTRAINT `jaos_link` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`),
  CONSTRAINT `laji_link` FOREIGN KEY (`laji`) REFERENCES `vrlv3_lista_painotus` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_nayttelykalenteri`
--

DROP TABLE IF EXISTS `vrlv3_kisat_nayttelykalenteri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_nayttelykalenteri` (
  `kisa_id` int(11) NOT NULL AUTO_INCREMENT,
  `vip` datetime NOT NULL,
  `kp` datetime NOT NULL,
  `laji` smallint(2) NOT NULL,
  `jaos` int(11) NOT NULL,
  `url` tinytext NOT NULL,
  `info` mediumtext NOT NULL,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `jarj_talli` varchar(8) DEFAULT NULL,
  `jarj_seura` int(10) DEFAULT NULL,
  `arvontatapa` int(2) DEFAULT NULL,
  `takaaja` int(5) unsigned zerofill DEFAULT NULL,
  `ilmoitettu` datetime NOT NULL,
  `seuralle` int(11) DEFAULT NULL,
  `hyvaksytty` datetime DEFAULT NULL,
  `kasitelty` datetime DEFAULT NULL,
  `kasittelija` int(5) unsigned zerofill DEFAULT NULL,
  `tulokset` int(1) NOT NULL DEFAULT '0',
  `hyvaksyi` int(5) unsigned zerofill DEFAULT NULL,
  `seura_hyv` int(1) DEFAULT NULL,
  `siirretty` int(5) unsigned zerofill DEFAULT NULL,
  `vanha` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`kisa_id`),
  KEY `jaossS` (`jaos`),
  KEY `lajiiI` (`laji`),
  KEY `talli_key_idxiI` (`jarj_talli`),
  KEY `tunnus_key_idxiI` (`tunnus`),
  KEY `kasittelija_key_idxI` (`kasittelija`),
  KEY `hyvaksyi:keyu_idx` (`hyvaksyi`),
  CONSTRAINT `hyvaksyi:keyu` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `jaos_keyiI` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `kasittelija_keyI` FOREIGN KEY (`kasittelija`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `laji_keyiI` FOREIGN KEY (`laji`) REFERENCES `vrlv3_lista_painotus` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `talli_keyiI` FOREIGN KEY (`jarj_talli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON UPDATE CASCADE,
  CONSTRAINT `tunnus_keyiI` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3721 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_nayttelytulokset`
--

DROP TABLE IF EXISTS `vrlv3_kisat_nayttelytulokset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_nayttelytulokset` (
  `bis_id` int(11) NOT NULL AUTO_INCREMENT,
  `nayttely_id` int(11) NOT NULL,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `paatuomari_nimi` varchar(100) NOT NULL,
  `luokkatuomarit_nimi` tinytext NOT NULL,
  `hyvaksyi` int(5) unsigned zerofill DEFAULT NULL,
  `hyvaksytty` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ilmoitettu` datetime DEFAULT NULL,
  `kasitelty` datetime DEFAULT NULL,
  `kasittelija` int(5) unsigned zerofill DEFAULT NULL,
  `tulokset` text,
  PRIMARY KEY (`bis_id`),
  UNIQUE KEY `nayttely_id` (`nayttely_id`),
  KEY `nayttelyt_hyvaksyi_id` (`hyvaksyi`),
  KEY `nayttelyt_kasittelija_id` (`kasittelija`),
  KEY `nayttely_jarjestaja` (`tunnus`),
  CONSTRAINT `hyvaksyja_idcx` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `ilmoittaja_idx` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `kasittelija_idx` FOREIGN KEY (`kasittelija`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `nayttely_idx` FOREIGN KEY (`nayttely_id`) REFERENCES `vrlv3_kisat_nayttelykalenteri` (`kisa_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3715 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_kisat_tulokset`
--

DROP TABLE IF EXISTS `vrlv3_kisat_tulokset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_tulokset` (
  `tulos_id` int(11) NOT NULL AUTO_INCREMENT,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `ilmoitettu` datetime NOT NULL,
  `tulokset` mediumtext NOT NULL,
  `luokat` mediumtext NOT NULL,
  `hylatyt` mediumtext,
  `hyvaksytty` datetime NOT NULL,
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  `kasitelty` datetime NOT NULL,
  `kasittelija` int(5) unsigned zerofill DEFAULT NULL,
  `kisa_id` int(11) NOT NULL,
  PRIMARY KEY (`tulos_id`),
  UNIQUE KEY `kisa_id` (`kisa_id`),
  KEY `kisat_tulokset_ibfk_1_idx` (`kisa_id`),
  KEY `tunnus_idx_tulos` (`tunnus`),
  KEY `hyvaksyi_tulokset_idx` (`hyvaksyi`),
  KEY `kasittelija_idx` (`kasittelija`),
  CONSTRAINT `hyvaksyi_tulokset` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `kasittelija_tunnus_tulokset` FOREIGN KEY (`kasittelija`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `kisat_tulokset_ibfk_1` FOREIGN KEY (`kisa_id`) REFERENCES `vrlv3_kisat_kisakalenteri` (`kisa_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tunnus_tulokset` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=165936 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_puljut`
--

DROP TABLE IF EXISTS `vrlv3_puljut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_puljut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nimi` varchar(45) NOT NULL,
  `lyhenne` varchar(10) NOT NULL,
  `toiminnassa` tinyint(4) NOT NULL DEFAULT '1',
  `url` text,
  `kuvaus` text,
  `tyyppi` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `laji_idx` (`tyyppi`),
  CONSTRAINT `tyyppi` FOREIGN KEY (`tyyppi`) REFERENCES `vrlv3_lista_puljutyyppi` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_puljut_omistajat`
--

DROP TABLE IF EXISTS `vrlv3_puljut_omistajat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_puljut_omistajat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jid` int(11) NOT NULL,
  `tunnus` int(10) unsigned NOT NULL,
  `taso` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `omistajaryhma` (`jid`,`tunnus`,`taso`),
  KEY `vrltunnus_idxxxx` (`tunnus`),
  CONSTRAINT `puljutunnusx` FOREIGN KEY (`jid`) REFERENCES `vrlv3_puljut` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrltunnusx` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_puljut_rodut`
--

DROP TABLE IF EXISTS `vrlv3_puljut_rodut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_puljut_rodut` (
  `pulju` int(11) NOT NULL,
  `rotu` smallint(6) NOT NULL,
  PRIMARY KEY (`pulju`,`rotu`),
  KEY `rotunimi_idxx` (`rotu`),
  CONSTRAINT `puljunimi` FOREIGN KEY (`pulju`) REFERENCES `vrlv3_puljut` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rotunimi` FOREIGN KEY (`rotu`) REFERENCES `vrlv3_lista_rodut` (`rotunro`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_tallirekisteri`
--

DROP TABLE IF EXISTS `vrlv3_tallirekisteri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_tallirekisteri` (
  `tnro` varchar(8) NOT NULL,
  `nimi` text NOT NULL,
  `url` text NOT NULL,
  `kuvaus` text NOT NULL,
  `perustettu` datetime NOT NULL,
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  `piilotettu` tinyint(1) NOT NULL DEFAULT '0',
  `hyvaksytty` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lopettanut` int(11) NOT NULL DEFAULT '0',
  `lopetti_pvm` datetime DEFAULT NULL,
  `lopetti_tunnus` int(5) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`tnro`),
  KEY `lopetti_tunnus` (`lopetti_tunnus`),
  KEY `hyvaksyi_tunnus` (`hyvaksyi`),
  CONSTRAINT `vrlv3_tallirekisteri_hyvaksyi_tunnus` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `vrlv3_tallirekisteri_ibfk_2` FOREIGN KEY (`lopetti_tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_tallirekisteri_kategoriat`
--

DROP TABLE IF EXISTS `vrlv3_tallirekisteri_kategoriat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_tallirekisteri_kategoriat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tnro` varchar(8) NOT NULL,
  `kategoria` smallint(6) NOT NULL,
  `anoi` int(5) unsigned zerofill NOT NULL,
  `hyvaksyi` int(5) unsigned zerofill NOT NULL,
  `lisatty` datetime NOT NULL,
  `tila` int(11) NOT NULL DEFAULT '0',
  `kasitelty` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tnro` (`tnro`),
  KEY `tnro_2` (`tnro`),
  KEY `kategoria` (`kategoria`),
  KEY `kasittelija` (`anoi`),
  KEY `lisaaja` (`hyvaksyi`),
  CONSTRAINT `vrlv3_tallirekisteri_kategoriat_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_tallirekisteri_kategoriat_ibfk_2` FOREIGN KEY (`kategoria`) REFERENCES `vrlv3_lista_tallikategoriat` (`kat`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_tallirekisteri_kategoriat_ibfk_3` FOREIGN KEY (`anoi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_tallirekisteri_kategoriat_ibfk_4` FOREIGN KEY (`hyvaksyi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43845 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_tallirekisteri_muutokset`
--

DROP TABLE IF EXISTS `vrlv3_tallirekisteri_muutokset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_tallirekisteri_muutokset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tnro` varchar(8) NOT NULL,
  `muokkasi` int(5) unsigned zerofill NOT NULL,
  `aika` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `field` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tnro` (`tnro`),
  KEY `tnro_2` (`tnro`),
  KEY `muokkasi` (`muokkasi`),
  CONSTRAINT `vrlv3_tallirekisteri_muutokset_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_tallirekisteri_muutokset_ibfk_3` FOREIGN KEY (`muokkasi`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_tallirekisteri_omistajat`
--

DROP TABLE IF EXISTS `vrlv3_tallirekisteri_omistajat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_tallirekisteri_omistajat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tnro` varchar(8) NOT NULL,
  `omistaja` int(5) unsigned zerofill NOT NULL,
  `taso` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `omistajaryhma` (`tnro`,`omistaja`,`taso`),
  KEY `tnro` (`tnro`),
  KEY `omistaja` (`omistaja`),
  KEY `tnro_2` (`tnro`),
  KEY `omistaja_2` (`omistaja`),
  CONSTRAINT `vrlv3_tallirekisteri_omistajat_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_tallirekisteri_omistajat_ibfk_2` FOREIGN KEY (`omistaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9946 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_tallirekisteri_paivitetty`
--

DROP TABLE IF EXISTS `vrlv3_tallirekisteri_paivitetty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_tallirekisteri_paivitetty` (
  `id` int(11) NOT NULL,
  `tnro` varchar(8) NOT NULL,
  `paivitti` int(5) unsigned zerofill NOT NULL,
  `aika` datetime NOT NULL,
  `text` text,
  KEY `vrlv3_tallirekisteri_paivitetty_ibfk_1` (`tnro`),
  KEY `vrlv3_tallirekisteri_paivitetty_ibfk_2` (`paivitti`),
  CONSTRAINT `vrlv3_tallirekisteri_paivitetty_ibfk_1` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_tallirekisteri_paivitetty_ibfk_2` FOREIGN KEY (`paivitti`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_tapahtumat`
--

DROP TABLE IF EXISTS `vrlv3_tapahtumat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_tapahtumat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pv` date NOT NULL,
  `vip` date DEFAULT NULL,
  `ilmo` date DEFAULT NULL,
  `vastuu` int(5) unsigned zerofill NOT NULL,
  `jaos` varchar(10) NOT NULL,
  `otsikko` tinytext NOT NULL,
  `info` text,
  `luokkia` varchar(2) DEFAULT NULL,
  `tulos` tinyint(1) DEFAULT '1',
  `jaos_id` int(11) DEFAULT NULL,
  `pulju_id` int(11) DEFAULT NULL,
  `type` INT(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `tapahtumajarjestajatunnus_idx` (`vastuu`),
  KEY `tapahtumanjarjestajajaos_idx` (`jaos_id`),
  KEY `tapahtumanjarjestajapulju_idx` (`pulju_id`),
  CONSTRAINT `tapahtumajarjestajatunnus` FOREIGN KEY (`vastuu`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tapahtumanjarjestajajaos` FOREIGN KEY (`jaos_id`) REFERENCES `vrlv3_kisat_jaokset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tapahtumanjarjestajapulju` FOREIGN KEY (`pulju_id`) REFERENCES `vrlv3_puljut` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=316 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_tapahtumat_osallistujat`
--

DROP TABLE IF EXISTS `vrlv3_tapahtumat_osallistujat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_tapahtumat_osallistujat` (
  `oid` int(11) NOT NULL AUTO_INCREMENT,
  `tapahtuma` int(11) NOT NULL,
  `vh` int(9) unsigned zerofill NOT NULL,
  `ilm` datetime NOT NULL,
  `hyv` int(1) unsigned NOT NULL DEFAULT '0',
  `syy` text NOT NULL,
  `luokka` varchar(2) NOT NULL,
  `tulos` decimal(10,3) NOT NULL,
  `palkinto` varchar(50) NOT NULL,
  `kommentti` text NOT NULL,
  PRIMARY KEY (`oid`),
  KEY `vh` (`vh`),
  KEY `id` (`tapahtuma`),
  CONSTRAINT `tapahtuma_ff` FOREIGN KEY (`tapahtuma`) REFERENCES `vrlv3_tapahtumat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tapahtuma_vh` FOREIGN KEY (`vh`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12969 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `vrlv3_tapahtumat_osallistujat_talli`;
CREATE TABLE `vrlv3_tapahtumat_osallistujat_talli` (
  `oid` int(11) NOT NULL AUTO_INCREMENT,
  `tapahtuma` int(11) NOT NULL,
  `tnro` varchar(8) NOT NULL,
  `ilm` datetime NOT NULL,
  `hyv` int(1) unsigned NOT NULL DEFAULT '0',
  `syy` text CHARACTER SET latin1 NOT NULL,
  `luokka` varchar(2) CHARACTER SET latin1 NOT NULL,
  `tulos` decimal(10,3) NOT NULL,
  `palkinto` varchar(50) CHARACTER SET latin1 NOT NULL,
  `kommentti` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`oid`),
  KEY `id` (`tapahtuma`),
  KEY `tapahtuma_tnro` (`tnro`),
  CONSTRAINT `tapahtuma_fff` FOREIGN KEY (`tapahtuma`) REFERENCES `vrlv3_tapahtumat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tapahtuma_tnro` FOREIGN KEY (`tnro`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `vrlv3_tapahtumat_osallistujat_tunnus`;
CREATE TABLE `vrlv3_tapahtumat_osallistujat_tunnus` (
  `oid` int(11) NOT NULL AUTO_INCREMENT,
  `tapahtuma` int(11) NOT NULL,
  `tunnus` int(5) unsigned zerofill NOT NULL,
  `ilm` datetime NOT NULL,
  `hyv` int(1) unsigned NOT NULL DEFAULT '0',
  `syy` text CHARACTER SET latin1 NOT NULL,
  `luokka` varchar(2) CHARACTER SET latin1 NOT NULL,
  `tulos` decimal(10,3) NOT NULL,
  `palkinto` varchar(50) CHARACTER SET latin1 NOT NULL,
  `kommentti` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`oid`),
  KEY `id` (`tapahtuma`),
  KEY `tunnus` (`tunnus`),
  CONSTRAINT `tapahtuma_ffff` FOREIGN KEY (`tapahtuma`) REFERENCES `vrlv3_tapahtumat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tapahtuma_tunnus` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Table structure for table `vrlv3_tiedotukset`
--

DROP TABLE IF EXISTS `vrlv3_tiedotukset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_tiedotukset` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `aika` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `otsikko` text CHARACTER SET latin1 NOT NULL,
  `teksti` text CHARACTER SET latin1 NOT NULL,
  `lahettaja` int(5) unsigned zerofill NOT NULL,
  `julkinen` int(11) NOT NULL DEFAULT '1',
  `muokkaaja` int(5) unsigned zerofill NOT NULL COMMENT 'Viimeisin muokkaaja',
  `muokpvm` datetime NOT NULL,
  PRIMARY KEY (`tid`),
  KEY `vrlv3_tiedotukset_ibfk_1` (`muokkaaja`),
  KEY `vrlv3_tiedotukset_ibfk_2` (`lahettaja`),
  CONSTRAINT `vrlv3_tiedotukset_ibfk_1` FOREIGN KEY (`muokkaaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_tiedotukset_ibfk_2` FOREIGN KEY (`lahettaja`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=685 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vrlv3_tiedotukset_kategoriat`
--

DROP TABLE IF EXISTS `vrlv3_tiedotukset_kategoriat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_tiedotukset_kategoriat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `kid` int(11) NOT NULL,
  `kategoria` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kid` (`kid`),
  KEY `kid_2` (`kid`),
  KEY `tid` (`tid`),
  CONSTRAINT `vrlv3_tiedotukset_kategoriat_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `vrlv3_tiedotukset` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrlv3_tiedotukset_kategoriat_ibfk_2` FOREIGN KEY (`kid`) REFERENCES `vrlv3_lista_tiedotuskategoriat` (`kid`)
) ENGINE=InnoDB AUTO_INCREMENT=659 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
