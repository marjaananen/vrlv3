CREATE DATABASE  IF NOT EXISTS `vrlv3` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `vrlv3`;
-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: vrlv3.copbw4eldk6h.eu-west-1.rds.amazonaws.com    Database: vrlv3
-- ------------------------------------------------------
-- Server version	5.7.26-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '';

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
  `laji` smallint(2) NOT NULL,
  `s_salli_porrastetut` tinyint(4) NOT NULL DEFAULT '0',
  `s_luokkia_per_kisa_max` int(11) NOT NULL DEFAULT '15',
  `s_luokkia_per_kisa_min` int(11) NOT NULL DEFAULT '1',
  `s_hevosia_per_luokka_max` int(11) NOT NULL DEFAULT '100',
  `s_hevosia_per_luokka_min` int(11) NOT NULL DEFAULT '30',
  `s_luokkia_per_hevonen_max` int(11) NOT NULL DEFAULT '2',
  `s_luokkia_per_hevonen_min` int(11) NOT NULL DEFAULT '1',
  `nayttelyt` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `laji_idx` (`laji`),
  CONSTRAINT `laji` FOREIGN KEY (`laji`) REFERENCES `vrlv3_lista_painotus` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_kisat_jaokset`
--

LOCK TABLES `vrlv3_kisat_jaokset` WRITE;
/*!40000 ALTER TABLE `vrlv3_kisat_jaokset` DISABLE KEYS */;
INSERT INTO `vrlv3_kisat_jaokset` VALUES (1,'Esteratsastusjaos','ERJ',0,'http://huvituksen.net/erj/','Esteratsastusjaos ERJ on vuonna 2001 perustettu virtuaalinen jaos, jonka tarkoituksena on yhdistää, tukea, valvoa ja edistää esteratsastustoimintaa virtuaalimaailmassa. Keinoja tähän on mm. Virtuaalisen ratsastajainliiton alla toimiva kilpailukalenteri, yhteiset kilpailusäännöt, laatuarvostelut sekä kasvattaja- ja jälkeläispalkinnot jotka otettiin käyttöön kesällä 2018.',1,0,15,1,100,30,2,1,0),(2,'Kouluratsastusjaos','KRJ',0,'http://www.lasileija.net/krj/','KRJ on joulukuusta 2001 lähtien toiminut virtuaalinen kilpailujaos, joka on ensimmäinen VRL:n alainen kilpailujaos. Sen pitkäaikaisia ylläpitäjiä ovat olleet Ulla P., Heidi C., Absinth, yersinio ja Milja. KRJ on esteratsastusjaoksen ohella suosituin kilpailujaos VRL:n alaisuudessa.',2,0,15,1,100,30,2,1,0),(3,'Kenttäratsastusjaos','KERJ',0,'http://karmes.net/kerj/','Kenttäratsastusjaos on perustettu vuonna 2001, ja sen toiminta on virallisesti alkanut vuonna 2002. Jaoksen tavoitteena on aina ollut kenttäratsastuksen kehitys virtuaalimaailmassa pitäen laji kuitenkin mahdollisimman lähellä todellisuutta.',3,0,15,1,100,30,2,1,0),(4,'Valjakkoajojaos','VVJ',0,'http://kasvukipuja.net/vvj/','VVJ on huhtikuussa 2002 Annen ja Raisan toimesta perustettu kilpailujaos ollen yksi uusimmista VRL:n alaisista kilpailujaoksista. Valjakkoajojaoksen tavoitteena on yhdistää lajin harastajat, valvoa ja edistää lajin kilpailutoimintaa sekä suosiota virtuaalimaailmassa. Keinoja tähän ovat olleet lajin oma kilpailukalenteri, kuukausittain pyörivä CUP, sekä aktiivisesti toimiva laatuarvostelu. Mukaan on otettu myös vuonna 2018 kasvattaja- ja jälkeläispalkinnot, sekä pitkään tauolla ollut Keilakunkku.',6,0,12,1,100,30,2,1,0),(5,'Westernjaos','WRJ',0,'http://cornfield-chase.com/wrj/index.php','WRJ on vuonna 2003 perustettu virtuaalinen ratsastusjaos, jonka tarkoituksena on yhdistää ja tukea lajin harrastajia, sekä valvoa ja edistää virtuaalista lännenratsastusta.',5,0,15,1,100,30,2,1,0),(6,'Askellajiratsastusjaos','ARJ',0,'http://arj.altervista.org/','ARJ on perustettu syksyllä 2003 edistämään ja nostamaan tietoisuuteen askellajiratsastuskisoja. Askellajikisoissa kisaavat ns. askellajirotuiset hevoset (gaited horses) roduilleen ominaisissa askellajeissa. Tunnetuin näistä roduista lienee islanninhevonen, jonka ehdoilla ARJ pyörikin hyvän aikaa.',7,0,15,1,100,30,2,1,0);
/*!40000 ALTER TABLE `vrlv3_kisat_jaokset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrlv3_kisat_jaokset_ominaisuudet`
--

DROP TABLE IF EXISTS `vrlv3_kisat_jaokset_ominaisuudet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_jaokset_ominaisuudet` (
  `jaos` int(11) NOT NULL AUTO_INCREMENT,
  `ominaisuus` int(8) NOT NULL,
  PRIMARY KEY (`jaos`,`ominaisuus`),
  KEY `ominaisuusnimi_idx` (`ominaisuus`),
  CONSTRAINT `jaosnimi` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `ominaisuusnimi` FOREIGN KEY (`ominaisuus`) REFERENCES `vrlv3_lista_ominaisuudet` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_kisat_jaokset_ominaisuudet`
--

LOCK TABLES `vrlv3_kisat_jaokset_ominaisuudet` WRITE;
/*!40000 ALTER TABLE `vrlv3_kisat_jaokset_ominaisuudet` DISABLE KEYS */;
INSERT INTO `vrlv3_kisat_jaokset_ominaisuudet` VALUES (4,5),(4,7);
/*!40000 ALTER TABLE `vrlv3_kisat_jaokset_ominaisuudet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrlv3_kisat_jaokset_omistajat`
--

DROP TABLE IF EXISTS `vrlv3_kisat_jaokset_omistajat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_kisat_jaokset_omistajat` (
  `jid` int(11) NOT NULL,
  `tunnus` int(11) unsigned NOT NULL,
  `taso` int(3) DEFAULT '0',
  PRIMARY KEY (`jid`,`tunnus`),
  KEY `vrltunnus_idx` (`tunnus`),
  CONSTRAINT `jaostunnus` FOREIGN KEY (`jid`) REFERENCES `vrlv3_kisat_jaokset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `vrltunnus` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `laji` smallint(2) DEFAULT NULL,
  `porrastettu` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `taso` int(11) NOT NULL,
  `aste` int(2) NOT NULL,
  `minheight` int(3) DEFAULT NULL,
  `min_age` int(1) unsigned NOT NULL DEFAULT '3',
  `kaytossa` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `jarjnro` int(3) unsigned NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`),
  KEY `jaos_idx` (`jaos`),
  KEY `laji_idx` (`laji`),
  CONSTRAINT `jaos_link` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `laji_link` FOREIGN KEY (`laji`) REFERENCES `vrlv3_lista_painotus` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_kisat_luokat`
--

LOCK TABLES `vrlv3_kisat_luokat` WRITE;
/*!40000 ALTER TABLE `vrlv3_kisat_luokat` DISABLE KEYS */;
INSERT INTO `vrlv3_kisat_luokat` VALUES (1,'40cm (seura)',1,1,1,0,1,NULL,3,1,0),(2,'60cm (seura)',1,1,1,0,1,NULL,3,1,0),(3,'80cm (seura)',1,1,1,0,1,NULL,3,1,0),(4,'80cm (alue)',1,1,1,1,2,NULL,4,1,0),(5,'90cm (seura)',1,1,1,0,1,NULL,3,1,0),(6,'90cm (alue)',1,1,1,1,2,NULL,4,1,0),(7,'100cm (seura)',1,1,1,0,1,NULL,3,1,0),(8,'100cm (alue)',1,1,1,1,2,NULL,4,1,0),(9,'110cm (alue)',1,1,1,2,2,NULL,4,1,0),(10,'110cm (kansallinen)',1,1,1,5,3,NULL,6,1,0),(11,'120cm (alue)',1,1,1,3,2,140,5,1,0),(12,'120cm (kansallinen)',1,1,1,6,3,140,6,1,0),(13,'130cm (alue)',1,1,1,4,2,140,5,1,0),(14,'130cm (kansallinen)',1,1,1,7,3,140,7,1,0),(15,'140cm (alue)',1,1,1,5,2,149,6,1,0),(16,'140cm (kansallinen)',1,1,1,8,3,149,7,1,0),(17,'150cm (kansallinen)',1,1,1,9,3,149,8,1,0),(18,'160cm (kansallinen)',1,1,1,10,3,149,8,1,0),(19,'Helppo C (seura)',2,2,1,0,1,NULL,3,1,0),(20,'KN Special (seura)',2,2,1,0,1,NULL,3,1,0),(21,'Helppo B (seura)',2,2,1,0,1,NULL,3,1,0),(22,'Helppo A (seura)',2,2,1,0,1,NULL,3,1,0),(23,'Helppo C (alue)',2,2,1,1,2,NULL,4,1,0),(24,'KN Special (alue)',2,2,1,1,2,NULL,4,1,0),(25,'Helppo B (alue)',2,2,1,1,2,NULL,4,1,0),(26,'Helppo A (alue)',2,2,1,2,2,NULL,4,1,0),(27,'Helppo A (kansallinen)',2,2,1,4,3,NULL,5,1,0),(28,'Vaativa B (alue)',2,2,1,3,2,140,5,1,0),(29,'Vaativa B (kansallinen)',2,2,1,5,3,140,6,1,0),(30,'Vaativa A (kansallinen)',2,2,1,6,3,149,6,1,0),(31,'Prix St. Georges (kansallinen)',2,2,1,7,3,149,7,1,0),(32,'Intermediate I (kansallinen)',2,2,1,8,3,149,7,1,0),(33,'Intermediate II (kansallinen)',2,2,1,9,3,149,8,1,0),(34,'Grand Prix (kansallinen)',2,2,1,10,3,149,8,1,0),(35,'Aloittelijaluokka (avoin kaikille - seura)',3,3,1,0,1,NULL,3,1,0),(36,'Harrasteluokka (avoin kaikille - seura)',3,3,1,0,1,NULL,3,1,0),(37,'Tutustumisluokka (avoin kaikille - seura)',3,3,1,0,1,NULL,3,1,0),(38,'Aloittelijaluokka (avoin kaikille - alue)',3,3,1,1,2,NULL,4,1,0),(39,'Harrasteluokka (avoin kaikille - alue)',3,3,1,1,2,NULL,4,1,0),(40,'Tutustumisluokka (avoin kaikille - alue)',3,3,1,1,2,NULL,4,1,0),(41,'Helppo (avoin kaikille - alue)',3,3,1,2,2,NULL,4,1,0),(42,'CIC1 (avoin kaikille - alue)',3,3,1,3,2,NULL,5,1,0),(43,'CIC1 (avoin hevosille, ei suomenhevosille - alue)',3,3,1,3,2,149,5,1,0),(44,'CIC2 (avoin poneille sk alle 149cm ja hevosille, ei suomenhevosille - kansallinen)',3,3,1,5,3,140,6,1,0),(45,'CIC2 (avoin hevosille, ei suomenhevosille - kansallinen)',3,3,1,5,3,149,6,1,0),(46,'CIC3 (avoin hevosille, ei suomenhevosille - kansallinen)',3,3,1,6,3,149,6,1,0),(47,'CIC4 (avoin hevosille, ei suomenhevosille - kansallinen)',3,3,1,7,3,149,7,1,0),(48,'noviisi koulukoe (seura)',4,6,1,0,1,NULL,3,1,0),(49,'noviisi tarkkuuskoe (seura)',4,6,1,0,1,NULL,3,1,0),(50,'noviisi kestävyyskoe (seura)',4,6,1,0,1,NULL,3,1,0),(51,'noviisi yhdistetty (seura)',4,6,1,0,1,NULL,3,1,0),(52,'noviisi koulukoe (alue)',4,6,1,1,2,NULL,4,1,0),(53,'noviisi tarkkuuskoe (alue)',4,6,1,1,2,NULL,4,1,0),(54,'noviisi kestävyyskoe (alue)',4,6,1,1,2,NULL,4,1,0),(55,'noviisi yhdistetty (alue)',4,6,1,3,2,NULL,5,1,0),(56,'vaativa koulukoe (alue)',4,6,1,3,2,NULL,5,1,0),(57,'vaativa tarkkuuskoe (alue)',4,6,1,3,2,NULL,5,1,0),(58,'vaativa kestävyyskoe (alue)',4,6,1,3,2,NULL,5,1,0),(59,'vaativa yhdistetty (alue)',4,6,1,5,2,NULL,6,1,0),(60,'vaativa koulukoe (kansallinen)',4,6,1,5,3,NULL,6,1,0),(61,'vaativa tarkkuuskoe (kansallinen)',4,6,1,5,3,NULL,6,1,0),(62,'vaativa kestävyyskoe (kansallinen)',4,6,1,5,3,NULL,6,1,0),(63,'vaativa yhdistetty (kansallinen)',4,6,1,7,3,NULL,7,1,0),(64,'vaikea koulukoe (kansallinen)',4,6,1,7,3,NULL,7,1,0),(65,'vaikea tarkkuuskoe (kansallinen)',4,6,1,7,3,NULL,7,1,0),(66,'vaikea kestävyyskoe (kansallinen)',4,6,1,7,3,NULL,7,1,0),(67,'vaikea yhdistetty (kansallinen)',4,6,1,9,3,NULL,8,1,0),(71,'Helppo D (seura)',2,2,1,0,1,NULL,3,1,0),(72,'CIC1 (avoin kaikille - kansallinen)',3,3,1,4,3,140,5,1,0),(73,'CIC1 (avoin hevosille, ei suomenhevosille - kansallinen)',3,3,1,4,3,149,5,1,0),(74,'Helppo (avoin hevosille, ei suomenhevosille - alue)',3,3,1,2,2,149,4,1,0),(75,'100cm (kansallinen)',1,1,1,4,3,NULL,5,1,0);


ALTER TABLE `vrlv3`.`tapahtumat` 
ENGINE = InnoDB ,
ADD COLUMN `jaos_id` INT(11) NULL AFTER `tulos`;

ALTER TABLE `vrlv3`.`tapahtumat` 
ADD INDEX `tapahtumajarjestajatunnus_idx` (`vastuu` ASC),
ADD INDEX `tapahtumanjarjestajajaos_idx` (`jaos_id` ASC);
;
ALTER TABLE `vrlv3`.`tapahtumat` 
ADD CONSTRAINT `tapahtumajarjestajatunnus`
  FOREIGN KEY (`vastuu`)
  REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `tapahtumanjarjestajajaos`
  FOREIGN KEY (`jaos_id`)
  REFERENCES `vrlv3`.`vrlv3_kisat_jaokset` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
  ALTER TABLE `vrlv3`.`tapahtumat` 
RENAME TO  `vrlv3`.`vrlv3_tapahtumat` ;


ALTER TABLE `vrlv3`.`tapahtumat_osallistujat` 
ENGINE = InnoDB ,
ADD COLUMN `oid` INT(11) NOT NULL AUTO_INCREMENT FIRST,
CHANGE COLUMN `id` `tapahtuma` INT(11) NOT NULL ,
CHANGE COLUMN `vh` `vh` INT(9) ZEROFILL NOT NULL ,
ADD PRIMARY KEY (`oid`);
;

ALTER TABLE `vrlv3`.`tapahtumat_osallistujat` 
ADD CONSTRAINT `tapahtumaid`
  FOREIGN KEY (`tapahtuma`)
  REFERENCES `vrlv3`.`vrlv3_tapahtumat` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
  delete FROM vrlv3.tapahtumat_osallistujat WHERE NOT EXISTS (SELECT*from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = tapahtumat_osallistujat.vh) and oid > 0;
  
  ALTER TABLE `vrlv3`.`tapahtumat_osallistujat` 
ADD CONSTRAINT `tapahtumahevo`
  FOREIGN KEY (`vh`)
  REFERENCES `vrlv3`.`vrlv3_hevosrekisteri` (`reknro`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
  ALTER TABLE `vrlv3`.`tapahtumat_osallistujat` 
CHANGE COLUMN `palkinto` `palkinto` VARCHAR(50) NOT NULL , RENAME TO  `vrlv3`.`vrlv3_tapahtumat_osallistujat` ;

UPDATE vrlv3_tapahtumat SET jaos_id = 4 WHERE jaos = 'vvj' and id > 0;
UPDATE vrlv3_tapahtumat SET jaos_id = 1 WHERE jaos = 'erj' and id > 0;
UPDATE vrlv3_tapahtumat SET jaos_id = 2 WHERE jaos = 'krj' and id > 0;
UPDATE vrlv3_tapahtumat SET jaos_id = 3 WHERE jaos = 'kerj' and id > 0;
UPDATE vrlv3_tapahtumat SET jaos_id = 5 WHERE jaos = 'wrj' and id > 0;
UPDATE vrlv3_tapahtumat SET jaos_id = 6 WHERE jaos = 'arj' and id > 0;

ALTER TABLE `vrlv3`.`vrlv3_kisat_kisakalenteri` 
ADD COLUMN `s_luokkia_per_hevonen` INT(3) NULL AFTER `porrastettu`,
ADD COLUMN `s_hevosia_per_luokka` INT(3) NULL AFTER `s_luokkia_per_hevonen`;




ALTER TABLE `vrlv3`.`vrlv3_tapahtumat` 
CHANGE COLUMN `vip` `vip` DATE NULL ,
CHANGE COLUMN `ilmo` `ilmo` DATE NULL ,
CHANGE COLUMN `info` `info` TEXT NULL ,
CHANGE COLUMN `luokkia` `luokkia` VARCHAR(2) NULL,
CHANGE COLUMN `tulos` `tulos` TINYINT(1) DEFAULT 1;


CREATE TABLE `vrlv3`.`vrlv3_kisat_etuuspisteet` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tunnus` INT(5) UNSIGNED ZEROFILL NOT NULL,
  `jaos` INT(11) NOT NULL,
  `pisteet` DOUBLE(6,2) NOT NULL,
  `nollattu` TINYINT(1) NULL,
  `muokattu` DATETIME NULL,
  INDEX `tunnusdidx` (`tunnus` ASC),
  INDEX `jaosidx` (`jaos` ASC),
  UNIQUE INDEX `tunnusjaosunique` (`tunnus` ASC, `jaos` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `tunnusetuusforein`
    FOREIGN KEY (`tunnus`)
    REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `jaosetuusforein`
    FOREIGN KEY (`jaos`)
    REFERENCES `vrlv3`.`vrlv3_kisat_jaokset` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);


CREATE TABLE `vrlv3`.`vrlv3_kisat_kisaluokat` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `kisa_id` INT(11) NOT NULL,
  `luokka_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `kisaluokat_kisaid` (`kisa_id` ASC),
  INDEX `kisaluokat_luokkaid` (`luokka_id` ASC),
  CONSTRAINT `kisaluokkaf`
    FOREIGN KEY (`luokka_id`)
    REFERENCES `vrlv3`.`vrlv3_kisat_luokat` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `kisaluokkaff`
    FOREIGN KEY (`kisa_id`)
    REFERENCES `vrlv3`.`vrlv3_kisat_kisakalenteri` (`kisa_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


CREATE TABLE IF NOT EXISTS `vrlv3_kisat_kisaosallis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kisa_id` int(11) NOT NULL,
  `kisaluokka_id` int(11) NOT NULL,
  `VH` INT(9) unsigned zerofill NOT NULL,
  `VRL` int(5) unsigned zerofill NOT NULL,
  `rimpsu` varchar(220) NOT NULL,
  `osallistui` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kisa_id` (`kisa_id`),
  KEY `kisaluokka_id` (`kisaluokka_id`),
    KEY `VH` (`VH`),
    KEY `VRL` (`VRL`)

) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Rajoitteet vedostauluille
--

--
-- Rajoitteet taululle `kilvat_kisaosallis`
--
ALTER TABLE `vrlv3_kisat_kisaosallis`
  ADD CONSTRAINT `kilvat_kisaosallis_ibfk_1` FOREIGN KEY (`kisa_id`) REFERENCES `vrlv3_kisat_kisakalenteri` (`kisa_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kilvat_kisaosallis_ibfk_2` FOREIGN KEY (`kisaluokka_id`) REFERENCES `vrlv3_kisat_kisaluokat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `kilvat_kisaosallis_ibfk_3` FOREIGN KEY (`VH`) REFERENCES `vrlv3_hevosrekisteri` (`reknro`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `kilvat_kisaosallis_ibfk_4` FOREIGN KEY (`VRL`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE CASCADE ON UPDATE CASCADE;


CREATE TABLE `vrlv3`.`hevosrekisteri_ominaisuudet_jonossa` (
  `id` INT NOT NULL,
  `reknro` INT(9) ZEROFILL UNSIGNED NOT NULL,
  `ominaisuus` INT(8) NOT NULL,
  `arvo` DECIMAL(8,2) NOT NULL,
  `tulos_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `hevosrekisteri_reknro` (`reknro` ASC),
  INDEX `hevosrekisteri_ominaisuus` (`ominaisuus` ASC),
  INDEX `kisakalenteri_tulos` (`tulos_id` ASC),
  CONSTRAINT `reknroff`
    FOREIGN KEY (`reknro`)
    REFERENCES `vrlv3`.`vrlv3_hevosrekisteri` (`reknro`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `ominaisuusff`
    FOREIGN KEY (`ominaisuus`)
    REFERENCES `vrlv3`.`vrlv3_lista_ominaisuudet` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `tulosff`
    FOREIGN KEY (`tulos_id`)
    REFERENCES `vrlv3`.`vrlv3_kisat_tulokset` (`tulos_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);



/*!40000 ALTER TABLE `vrlv3_kisat_luokat` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-07-05 20:18:45
