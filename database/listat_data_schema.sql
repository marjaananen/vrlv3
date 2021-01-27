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
-- Table structure for table `vrlv3_lista_maat`
--

DROP TABLE IF EXISTS `vrlv3_lista_maat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_lista_maat` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `lyh` varchar(2) NOT NULL,
  `maa` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=254 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_lista_maat`
--

LOCK TABLES `vrlv3_lista_maat` WRITE;
/*!40000 ALTER TABLE `vrlv3_lista_maat` DISABLE KEYS */;
INSERT INTO `vrlv3_lista_maat` VALUES (1,'AF','Afganistan'),(2,'AX','Ahvenanmaa'),(3,'NL','Alankomaat'),(4,'AN','Alankomaiden Antillit'),(5,'AL','Albania'),(6,'DZ','Algeria'),(7,'AS','Amerikan Samoa'),(8,'AD','Andorra'),(9,'AO','Angola'),(10,'AI','Anguilla'),(11,'AQ','Antarktis'),(12,'AG','Antigua ja Barbuda'),(13,'AE','Arabiemiirikunnat'),(14,'AR','Argentiina'),(15,'AM','Armenia'),(16,'AW','Aruba'),(17,'AC','Ascension Island'),(18,'AU','Australia'),(19,'AZ','Azerbaidžan'),(20,'BS','Bahama'),(21,'BH','Bahrain'),(22,'BD','Bangladesh'),(23,'BB','Barbados'),(24,'BE','Belgia'),(25,'BZ','Belize'),(26,'BJ','Benin'),(27,'BM','Bermuda'),(28,'BT','Bhutan'),(29,'BO','Bolivia'),(30,'BA','Bosnia ja Hertsegovina'),(31,'BW','Botswana'),(32,'BV','Bouvet’nsaari'),(33,'BR','Brasilia'),(34,'IO','Brittiläinen Intian valtameren alue'),(35,'VG','Brittiläiset Neitsytsaaret'),(36,'BN','Brunei'),(37,'BG','Bulgaria'),(38,'BF','Burkina Faso'),(39,'BI','Burundi'),(40,'KY','Caymansaaret'),(41,'EA','Ceuta, Melilla'),(42,'CL','Chile'),(43,'CP','Clippertoninsaari'),(44,'CK','Cookinsaaret'),(45,'CR','Costa Rica'),(46,'DG','Diego Garcia'),(47,'DJ','Djibouti'),(48,'DM','Dominica'),(49,'DO','Dominikaaninen tasavalta'),(50,'EC','Ecuador'),(51,'EG','Egypti'),(52,'SV','El Salvador'),(53,'ER','Eritrea'),(54,'ES','Espanja'),(55,'ET','Etiopia'),(56,'ZA','Etelä-Afrikka'),(57,'GS','Etelä-Georgia ja Eteläiset Sandwichsaaret'),(58,'EU','Euroopan unioni'),(59,'FK','Falklandinsaaret'),(60,'FO','Färsaaret'),(61,'FJ','Fidži'),(62,'PH','Filippiinit'),(63,'GA','Gabon'),(64,'GM','Gambia'),(65,'GE','Georgia'),(66,'GH','Ghana'),(67,'GI','Gibraltar'),(68,'GD','Grenada'),(69,'GL','Grönlanti'),(70,'GP','Guadeloupe'),(71,'GU','Guam'),(72,'GT','Guatemala'),(73,'GG','Guernsey'),(74,'GN','Guinea'),(75,'GW','Guinea-Bissau'),(76,'GY','Guyana'),(77,'HT','Haiti'),(78,'HM','Heard ja McDonaldinsaaret'),(79,'HN','Honduras'),(80,'HK','Hongkong'),(81,'ID','Indonesia'),(82,'IN','Intia'),(83,'IQ','Irak'),(84,'IR','Iran'),(85,'IE','Irlanti'),(86,'IS','Islanti'),(87,'IL','Israel'),(88,'IT','Italia'),(89,'TL','Itä-Timor'),(90,'AT','Itävalta'),(91,'JM','Jamaika'),(92,'JP','Japani'),(93,'YE','Jemen'),(94,'JE','Jersey'),(95,'JO','Jordania'),(96,'CX','Joulusaari'),(97,'KH','Kambodža'),(98,'CM','Kamerun'),(99,'CA','Kanada'),(100,'IC','Kanariansaaret'),(101,'CV','Kap Verde'),(102,'KZ','Kazakstan'),(103,'KE','Kenia'),(104,'CF','Keski-Afrikan tasavalta'),(105,'CN','Kiina'),(106,'KG','Kirgisia'),(107,'KI','Kiribati'),(108,'CO','Kolumbia'),(109,'KM','Komorit'),(110,'CD','Kongon demokraattinen tasavalta'),(111,'CG','Kongon tasavalta'),(112,'CC','Kookossaaret'),(113,'KP','Korean demokraattinen kansantasavalta'),(114,'KR','Korean tasavalta'),(115,'GR','Kreikka'),(116,'HR','Kroatia'),(117,'CU','Kuuba'),(118,'KW','Kuwait'),(119,'CY','Kypros'),(120,'LA','Laos'),(121,'LV','Latvia'),(122,'LS','Lesotho'),(123,'LB','Libanon'),(124,'LR','Liberia'),(125,'LY','Libya'),(126,'LI','Liechtenstein'),(127,'LT','Liettua'),(128,'LU','Luxemburg'),(129,'EH','Länsi-Sahara'),(130,'MO','Macao'),(131,'MG','Madagaskar'),(132,'MK','Makedonia'),(133,'MW','Malawi'),(134,'MV','Malediivit'),(135,'MY','Malesia'),(136,'ML','Mali'),(137,'MT','Malta'),(138,'IM','Mansaari'),(139,'MA','Marokko'),(140,'MH','Marshallinsaaret'),(141,'MQ','Martinique'),(142,'MR','Mauritania'),(143,'MU','Mauritius'),(144,'YT','Mayotte'),(145,'MX','Meksiko'),(146,'FM','Mikronesian liittovaltio'),(147,'MD','Moldova'),(148,'MC','Monaco'),(149,'MN','Mongolia'),(150,'ME','Montenegro'),(151,'MS','Montserrat'),(152,'MZ','Mosambik'),(153,'MM','Myanmar'),(154,'NA','Namibia'),(155,'NR','Nauru'),(156,'NP','Nepal'),(157,'NI','Nicaragua'),(158,'NE','Niger'),(159,'NG','Nigeria'),(160,'NU','Niue'),(161,'NF','Norfolkinsaari'),(162,'NO','Norja'),(163,'CI','Norsunluurannikko'),(164,'OM','Oman'),(165,'PK','Pakistan'),(166,'PW','Palau'),(167,'PS','Palestiina'),(168,'PA','Panama'),(169,'PG','Papua-Uusi-Guinea'),(170,'PY','Paraguay'),(171,'PE','Peru'),(172,'MP','Pohjois-Mariaanit'),(173,'PN','Pitcairn'),(174,'PT','Portugali'),(175,'PR','Puerto Rico'),(176,'PL','Puola'),(177,'GQ','Päiväntasaajan Guinea'),(178,'QA','Qatar'),(179,'FR','Ranska'),(180,'FX','Ranska (Eurooppaan kuuluvat osat)'),(181,'TF','Ranskan eteläiset alueet'),(182,'GF','Ranskan Guayana'),(183,'PF','Ranskan Polynesia'),(184,'RE','Réunion'),(185,'RO','Romania'),(186,'RW','Ruanda'),(187,'SE','Ruotsi'),(188,'SH','Saint Helena'),(189,'KN','Saint Kitts ja Nevis'),(190,'LC','Saint Lucia'),(191,'PM','Saint-Pierre ja Miquelon'),(192,'VC','Saint Vincent ja Grenadiinit'),(193,'DE','Saksa'),(194,'SB','Salomonsaaret'),(195,'ZM','Sambia'),(196,'WS','Samoa'),(197,'SM','San Marino'),(198,'ST','São Tomé ja Príncipe'),(199,'SA','Saudi-Arabia'),(200,'SN','Senegal'),(201,'RS','Serbia'),(202,'SC','Seychellit'),(203,'SL','Sierra Leone'),(204,'SG','Singapore'),(205,'SK','Slovakia'),(206,'SI','Slovenia'),(207,'SO','Somalia'),(208,'LK','Sri Lanka'),(209,'SD','Sudan'),(210,'FI','Suomi'),(211,'SR','Suriname'),(212,'SJ','Svalbard ja Jan Mayen'),(213,'SZ','Swazimaa'),(214,'CH','Sveitsi'),(215,'SY','Syyria'),(216,'TJ','Tadžikistan'),(217,'TW','Taiwan'),(218,'TZ','Tansania'),(219,'DK','Tanska'),(220,'TH','Thaimaa'),(221,'TG','Togo'),(222,'TK','Tokelau'),(223,'TO','Tonga'),(224,'TT','Trinidad ja Tobago'),(225,'TA','Tristan da Cunha'),(226,'TD','Tšad'),(227,'CZ','Tšekki'),(228,'TN','Tunisia'),(229,'TR','Turkki'),(230,'TM','Turkmenistan'),(231,'TC','Turks- ja Caicossaaret'),(232,'TV','Tuvalu'),(233,'UG','Uganda'),(234,'UA','Ukraina'),(235,'HU','Unkari'),(236,'UY','Uruguay'),(237,'NC','Uusi-Kaledonia'),(238,'NZ','Uusi-Seelanti'),(239,'UZ','Uzbekistan'),(240,'BY','Valko-Venäjä'),(241,'VU','Vanuatu'),(242,'VA','Vatikaanivaltio'),(243,'VE','Venezuela'),(244,'RU','Venäjä'),(245,'VN','Vietnam'),(246,'EE','Viro'),(247,'WF','Wallis ja Futunasaaret'),(248,'GB','Iso-Britannia'),(249,'UK','Yhdistynyt kuningaskunta'),(250,'US','Yhdysvallat'),(251,'VI','Yhdysvaltain Neitsytsaaret'),(252,'UM','Yhdysvaltain Tyynenmeren erillissaaret'),(253,'ZW','Zimbabwe');
/*!40000 ALTER TABLE `vrlv3_lista_maat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrlv3_lista_ominaisuudet`
--

DROP TABLE IF EXISTS `vrlv3_lista_ominaisuudet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_lista_ominaisuudet` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `ominaisuus` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_lista_ominaisuudet`
--

LOCK TABLES `vrlv3_lista_ominaisuudet` WRITE;
/*!40000 ALTER TABLE `vrlv3_lista_ominaisuudet` DISABLE KEYS */;
INSERT INTO `vrlv3_lista_ominaisuudet` VALUES (1,'nopeus'),(2,'kestävyys'),(3,'hyppykapasiteetti'),(4,'rohkeus'),(5,'kuuliaisuus ja luonne'),(6,'tahti ja irtonaisuus'),(7,'tarkkuus ja ketteryys'),(8,'askellajien näyttävyys'),(9,'lallattavuus');
/*!40000 ALTER TABLE `vrlv3_lista_ominaisuudet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrlv3_lista_painotus`
--

DROP TABLE IF EXISTS `vrlv3_lista_painotus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_lista_painotus` (
  `pid` smallint(2) NOT NULL AUTO_INCREMENT,
  `painotus` varchar(20) NOT NULL,
  `lyhenne` varchar(5) NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_lista_painotus`
--

LOCK TABLES `vrlv3_lista_painotus` WRITE;
/*!40000 ALTER TABLE `vrlv3_lista_painotus` DISABLE KEYS */;
INSERT INTO `vrlv3_lista_painotus` VALUES (1,'esteratsastus','re.'),(2,'kouluratsastus','ko.'),(3,'kenttäratsastus','kent.'),(4,'matkaratsastus','matk.'),(5,'lännenratsastus','länn.'),(6,'valjakkoajo','valj.'),(7,'askellajiratsastus','askel'),(8,'ravit','ravit'),(9,'työhevosajo','työh.'),(10,'laukat','lauk.'),(11,'poniravit','pora'),(12,'maastoeste','me'),(13,'näyttelyt','n.');
/*!40000 ALTER TABLE `vrlv3_lista_painotus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrlv3_lista_puljutyyppi`
--

DROP TABLE IF EXISTS `vrlv3_lista_puljutyyppi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_lista_puljutyyppi` (
  `pid` smallint(2) NOT NULL AUTO_INCREMENT,
  `tyyppi` varchar(20) NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_lista_puljutyyppi`
--

LOCK TABLES `vrlv3_lista_puljutyyppi` WRITE;
/*!40000 ALTER TABLE `vrlv3_lista_puljutyyppi` DISABLE KEYS */;
INSERT INTO `vrlv3_lista_puljutyyppi` VALUES (1,'kantakirja'),(2,'rotuyhdistys'),(3,'laatuarvostelu');
/*!40000 ALTER TABLE `vrlv3_lista_puljutyyppi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrlv3_lista_rodut`
--

DROP TABLE IF EXISTS `vrlv3_lista_rodut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_lista_rodut` (
  `rotunro` smallint(3) NOT NULL AUTO_INCREMENT,
  `rotu` text NOT NULL,
  `lyhenne` varchar(6) NOT NULL,
  `roturyhma` int(1) DEFAULT NULL,
  `harvinainen` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rotunro`),
  KEY `roturyhma` (`roturyhma`),
  CONSTRAINT `vrlv3_lista_rodut_ibfk_1` FOREIGN KEY (`roturyhma`) REFERENCES `vrlv3_lista_roturyhmat` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=315 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_lista_rodut`
--

LOCK TABLES `vrlv3_lista_rodut` WRITE;
/*!40000 ALTER TABLE `vrlv3_lista_rodut` DISABLE KEYS */;
INSERT INTO `vrlv3_lista_rodut` VALUES (1,'Ahaltekinhevonen','at',9,0),(2,'Amerikanponi','poa',10,0),(3,'Arabialainen täysverinen','ox',1,0),(4,'Azteca','azt',9,0),(5,'Clevelandinruunikko','cb',9,0),(6,'Englantilainen täysiverinen','xx',1,0),(7,'Falabella','falab',10,0),(8,'Frederiksborginhevonen','fred',9,0),(9,'Kalliovuortenhevonen','rmh',9,0),(10,'Knabstrupinhevonen','knn',9,0),(11,'Hannoverinhevonen','hann',2,0),(12,'Hollanninpuoliverinen','kwpn',2,0),(13,'Oldenburginhevonen','old',2,0),(14,'Quarterhevonen','qh',9,0),(15,'Russian-Based Sport Horse','rbsh',9,0),(16,'Saddlebred','sdb',9,0),(17,'Shetlanninponi','she',10,0),(18,'Suomenhevonen','sh',3,0),(19,'Tennesseenwalker','twh',9,0),(20,'Terskinhevonen','tersk',9,0),(21,'Trakehner','trak',2,0),(22,'Welsh sektio B','wB',10,0),(23,'Welsh Part Bred','wpb',10,0),(24,'Welsh Mountain','wA',10,0),(25,'Welsh sektio C','wC',10,0),(26,'Welsh sektio D','wD',10,0),(27,'Newforestinponi','nf',10,0),(28,'Gotlanninrussi','russ',10,0),(29,'Ratsuponi','rp',10,0),(30,'Islanninhevonen','isl',NULL,0),(31,'Suomalainen puoliverinen','fwb',2,0),(32,'Gelderlandinhevonen','geldr',9,0),(33,'Andalusianhevonen','pre',9,0),(34,'Angloarabialainen täysiverinen','x',1,0),(35,'Connemaranponi','conn',10,0),(36,'Irlannintyöhevonen','id',4,0),(37,'Appaloosa','app',9,0),(38,'Amerikanravuri','lv',9,0),(39,'Budjonnynhevonen','budj',9,0),(40,'Friisiläishevonen','fri',NULL,0),(41,'Perchenhevonen','perch',4,0),(42,'Kaspianponi','kasp',10,0),(43,'Ruotsinpuoliverinen','swb',2,0),(44,'Holsteininhevonen','holst',2,0),(45,'Lämminveriravuri','lv',9,0),(46,'Irlannincob','ic',NULL,0),(47,'Hackney','hackn',9,0),(48,'Westfaleninhevonen','westf',2,0),(49,'Furioso','fur',9,0),(50,'Marwarinhevonen','marw',9,0),(51,'Pohjoisnorjanponi','pnp',10,0),(52,'Paint Horse','ph',9,0),(53,'Norjanvuonohevonen','nvh',3,0),(54,'Lämminverinen ratsuhevonen','pv',2,0),(55,'Mustangi','must',9,0),(56,'Unkarinpuoliverinen','hwb',2,0),(57,'Haflinginhevonen','hafl',3,0),(58,'Pintabian','pintab',9,0),(59,'Morganinhevonen','morg',9,0),(60,'Eestinhevonen','eest',3,0),(61,'Lewitzer','lew',10,0),(62,'Clydesdalenhevonen','clyd',4,0),(63,'Dartmoorinponi','dartm',10,0),(64,'Lipizzanhevonen','lip',9,0),(65,'Pinto','pinto',NULL,0),(66,'Brandenburginhevonen','brdbg',2,0),(67,'Paso Fino','pf',9,0),(68,'Perunpaso','pp',9,0),(69,'Irlannintyöhevonen','idh',9,0),(70,'Kisber-felver','kisb',9,0),(71,'Ylämaanponi','highl',10,0),(72,'Missourinfoxtrotter','mft',9,0),(73,'Kanadanhevonen','kan',9,0),(74,'Turkmeeninhevonen','turkm',9,0),(75,'Kerry Bog Pony','kbp',10,0),(76,'Belgianpuoliverinen','bwp',2,0),(77,'Tanskanpuoliverinen','dwb',2,0),(78,'National Show Horse','nsh',9,0),(79,'Dalesponi','dales',10,0),(80,'Pohjoisruotsinhevonen','prh',3,0),(81,'American Bashkir Curly','abc',9,0),(82,'Mangalarga','manga',9,0),(83,'Criollo','cri',9,0),(84,'Berberihevonen','berb',9,0),(85,'Hackneyponi','hacknp',10,0),(86,'Ariegenponi','arieg',10,0),(87,'Lusitano','lusit',9,0),(88,'Camarguenhevonen','camar',9,0),(89,'Fellponi','fell',10,0),(90,'Anglo-trakehner','a-trak',2,0),(91,'Amerikanshetlanninponi','amshe',10,0),(92,'Exmoorinponi','exm',10,0),(93,'Torinhevonen','tori',NULL,0),(94,'Ardennienhevonen','ard',4,0),(95,'Belgiantyöhevonen','belg',4,0),(96,'Vladimirintyöhevonen','vlad',4,0),(97,'Orlovravuri','orlov',9,0),(98,'Ranskanravuri','ransk',9,0),(99,'Shagya','sha',9,0),(100,'Wielkopolski','wielk',9,0),(101,'Venäjänratsuhevonen','vrh',9,0),(102,'Tilastohevonen','til',NULL,0),(103,'Sleesianhevonen','slee',NULL,0),(104,'Shirenhevonen','shire',4,0),(105,'Nonius','non',9,0),(106,'Huzulponi','huzul',NULL,0),(107,'Malopolski','malo',9,0),(108,'Normandiancob','ncob',4,0),(109,'Salernonhevonen','saler',9,0),(110,'Boulognenhevonen','boul',4,0),(111,'Santelipuuponi','sant',10,0),(112,'Noricuminhevonen','norik',4,0),(113,'Jakutianponi','jakut',10,0),(114,'Morab','morab',9,0),(115,'Australianhevonen','austr',9,0),(116,'Novokirgiisi','novok',9,0),(117,'Valkovenäjäntyöhevonen','valk',4,0),(118,'Baskirianponi','baskir',10,0),(119,'Suffolkinhevonen','suff',4,0),(120,'Doninhevonen','don',9,0),(121,'Ranskanpuoliverinen','sf',2,0),(122,'Irlanninpuoliverinen','ihb',2,0),(123,'Ukrainanratsuhevonen','ukrh',9,0),(124,'Auxois','aux',4,0),(125,'Egyptinhevonen','egypt',9,0),(126,'Saarenmaanponi','saare',10,0),(127,'Sanhe','sanhe',9,0),(128,'Guangxi','guang',10,0),(129,'Schwarzwaldinrautias','schwa',4,0),(130,'American Cream Draft','amc',4,0),(131,'Drum Horse','drum',4,0),(132,'Misaki','misak',10,0),(133,'Colorado Ranger','colr',9,0),(134,'Eteläsaksankylmäverinen','eskv',4,0),(135,'Hollannintyöhevonen','holkv',4,0),(136,'Altainhevonen','altai',9,0),(137,'Lokainponi','lokai',10,0),(138,'Iomud','iomud',9,0),(139,'Hesseninhevonen','hesse',2,0),(140,'Basutonponi','basuto',10,0),(141,'Sveitsinpuoliverinen','chwb',2,0),(142,'Deliboz','delib',9,0),(143,'Avellinonponi','avell',10,0),(144,'Jyllanninhevonen','jyll',4,0),(145,'Sorraianponi','sorr',10,0),(146,'Vironpuoliverinen','estpv',2,0),(147,'Garranonponi','garra',10,0),(148,'Hispano','hispa',9,0),(149,'Tsetsenianvuoristoponi','tsets',10,0),(150,'Karpaaninhevonen','karp',NULL,0),(151,'Baijerinhevonen','baij',2,0),(152,'Florida Cracker','flacr',9,0),(153,'Mecklenburginhevonen','meckl',2,0),(154,'Cayuse','cay',9,0),(155,'Amerikanpuoliverinen','amwb',2,0),(156,'Freiberginhevonen','freib',3,0),(157,'Abessinianhevonen','abess',9,0),(158,'Adaev','adaev',9,0),(159,'Bretagnenhevonen','bret',4,0),(160,'Alter real','alter',9,0),(161,'Jaavanponi','jaav',10,0),(162,'Zemaituka','zhe',3,0),(163,'Anglo-kabardinhevonen','a-kab',9,0),(164,'Australianponi','austp',10,0),(165,'Friesian Sport Horse','fsh',9,0),(166,'Batakinponi','batak',10,0),(167,'Baskinponi','pottok',10,0),(168,'Dülmeninponi','dülm',10,0),(169,'Dölenhevonen','döle',3,0),(170,'Kisonponi','kiso',10,0),(171,'Peneioksenponi','penei',10,0),(172,'Maremmanhevonen','marem',9,0),(173,'Konik','konik',NULL,0),(174,'Einsiedelninhevonen','einsi',2,0),(175,'Barock-pinto','barock',9,0),(176,'Walkaloosa','walkal',9,0),(177,'American Miniature Horse','amh',10,0),(178,'Ara-appaloosa','ara-ap',9,0),(179,'Quarab','qox',9,0),(180,'Pintaloosa','pintl',NULL,0),(181,'Skyroksenponi','skyro',10,0),(182,'Padang','pad',10,0),(183,'Hokkaido','hokk',10,0),(184,'Bardianponi','bard',10,0),(185,'Sumbanponi','sumb',10,0),(186,'Kustanairinhevonen','kustn',9,0),(187,'Kabardiini','kabard',9,0),(188,'Ban-ei','ban-ei',4,0),(189,'Landesinponi','landes',10,0),(190,'Buurinponi','buur',10,0),(191,'Vjatkanponi','vjatk',10,0),(192,'Latvianhevonen','latv',NULL,0),(193,'Karabair','karab',9,0),(194,'Tiibetinponi','tiib',10,0),(195,'Gidran','gid',9,0),(196,'Paso Creole','pc',9,0),(197,'Chincoteaguenponi','cctg',10,0),(198,'Dutch Harness Horse','dhh',9,0),(199,'Raskas liettuantyöhevonen','liet',4,0),(200,'Eriskaynponi','erisk',10,0),(201,'Kladrubinhevonen','klad',9,0),(202,'Englanninpuoliverinen','ewb',2,0),(203,'Zweibrücker','zweib',2,0),(204,'Comtois','comt',4,0),(205,'Reinintyöhevonen','rkv',4,0),(206,'Noma','noma',10,0),(207,'Altösterreichisches Warmblut','altö',2,0),(208,'British Spotted Pony','bsp',10,0),(209,'Irish Sport Horse','ish',2,0),(210,'Spanish Barb','spb',9,0),(211,'Spanish-Norman','spn',9,0),(212,'Galiceño','gcñ',9,0),(213,'Spotted Draft Horse','sdh',4,0),(214,'Baluchinhevonen','baluch',9,0),(215,'Bosnianponi','bosn',10,0),(216,'Hackneyarabi','hac-ox',9,0),(217,'Saksalainen ratsuhevonen','saksrh',2,0),(218,'Arabialainen puoliverinen','awb',2,0),(219,'Asturianponi','ast',10,0),(220,'Newfoundlandinponi','nfld',10,0),(221,'Slovakianpuoliverinen','slvkpv',2,0),(222,'Sardinianhevonen','sard',9,0),(223,'Spotted Saddle Horse','ssh',9,0),(224,'Hunter','hunter',9,0),(225,'Poitounhevonen','poitou',4,0),(226,'Raskas neuvostoliitontyöhevonen','shd',4,0),(227,'Eestin raskas vetohevonen','erv',4,0),(228,'Färsaartenponi','färsp',10,0),(229,'Badenwürttemberginhevonen','bwürt',2,0),(230,'Reininhevonen','rhld',2,0),(231,'Slovenianpuoliverinen','slvnpv',2,0),(232,'Itävallanpuoliverinen','itävpv',2,0),(233,'Itäfriisinhevonen','itäfr',2,0),(234,'Tigerhevonen','tiger',9,0),(235,'Quarterponi','qp',10,0),(236,'Gaited Baroque','gb',9,0),(237,'Welara','welara',10,0),(238,'Welara Sport Pony','welsp',10,0),(239,'Lippitt Morgan','lpm',9,0),(241,'Italiantyöhevonen','tpr',4,0),(242,'Euroopan miniatyyrihevonen','emh',10,0),(243,'Kinskynhevonen','kinsk',2,0),(244,'Georgian Grande','gg',9,0),(245,'American Walking Pony','awp',10,0),(246,'Moriesian','mories',9,0),(248,'Waler','waler',9,0),(249,'Choctaw','choct',9,0),(250,'Tokara','tokar',10,0),(251,'Pottokponi','pottok',10,0),(252,'Murgenhevonen','murg',9,0),(253,'Sim-Game Sport Horse','sgsh',2,0),(254,'Eestin urheiluhevonen','esh',2,0),(255,'Muraközinhevonen','murak',4,0),(256,'Argentiinanpuoliverinen','arwb',2,0),(257,'Brasilianpuoliverinen','brzwb',2,0),(258,'Luxemburginpuoliverinen','luxwb',2,0),(259,'Saksin-Anhaltinhevonen','s-anh',2,0),(260,'Pooloponi','poolo',9,0),(261,'Virtuaalinen ratsuponi','vrp',10,0),(262,'Trait du Nord','tdn',4,0),(263,'Kanadanpuoliverinen','kanpv',2,0),(264,'Campolina','camp',9,0),(265,'Nokota','nok',9,0),(266,'Anglo-argentino','a-arg',9,0),(267,'Norjanpuoliverinen','nwb',2,0),(268,'Sim Sport Pony','ssp',10,0),(269,'Sim Sport Warmblood','ssw',9,0),(270,'Kylmäveriravuri','kvr',3,0),(271,'Raskas venäjäntyöhevonen','rhd',4,0),(272,'Zangersheide','Z',2,0),(273,'Venäjänravuri','venrav',9,0),(274,'Albanianponi','alb',NULL,0),(275,'Puolalainen puoliverinen','pl',NULL,0),(276,'Kathiawarinhevonen','kath',NULL,0),(277,'Dongola','dong.',9,1),(278,'Anastafjahinhevonen','ana',9,0),(279,'Italianratsuhevonen','si',2,0),(281,'Seepraponi','sepn',10,0),(282,'Navarran poni','nav',10,0),(283,'Namibialainen puoliverinen','nwh',2,0),(284,'Aegidienberger','aeg',3,0),(285,'Warlander','war',3,0),(286,'Nez Perce Horse','nezph',9,0),(287,'Iberian Warmblood','ibw',9,0),(288,'American Draft Pony ','',10,0),(289,'Miniature Gypsy Horse','mic',10,0),(290,'Arasien','ara',9,0),(291,'Tšekkiläinen puoliverinen','',2,0),(292,'Tšekintyöhevonen','tse',3,0),(298,'Latvialainen puoliverinen','lszaa',2,0),(299,'Mongolianhevonen','mong',10,0),(300,'Rahvan','rv',9,0),(301,'Espanjalainen puoliverinen','sw',2,0),(302,'Henson','hens',9,0),(303,'Arabohaflinger','ar-haf',3,0),(304,'Meckelnburginkylmäverinen','mecklk',3,0),(305,'Posavjenhevonen','pos',3,0),(306,'Puolalainen kylmäverinen','plkv',3,0),(307,'Ruotsalainen ardennienhevonen','rard',3,0),(308,'Schleswig-holsteininhevonen','schles',3,0),(309,'Sloveniankylmäverinen','slvnkv',3,0),(310,'Groningeninhevonen','gron',2,0),(311,'Saksin-Thüringenin raskas puoliverinen','sthür',2,0),(312,'Etelä-Afrikan puoliverinen','saw',2,0),(313,'Sportaloosa','spor',NULL,1),(314,'chaonponi','capo',5,1);
/*!40000 ALTER TABLE `vrlv3_lista_rodut` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrlv3_lista_roturyhmat`
--

DROP TABLE IF EXISTS `vrlv3_lista_roturyhmat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_lista_roturyhmat` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `ryhma` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_lista_roturyhmat`
--

LOCK TABLES `vrlv3_lista_roturyhmat` WRITE;
/*!40000 ALTER TABLE `vrlv3_lista_roturyhmat` DISABLE KEYS */;
INSERT INTO `vrlv3_lista_roturyhmat` VALUES (1,'täysiveriset'),(2,'puoliveriset'),(3,'kylmäveriset'),(4,'raskaat kylmäveriset'),(5,'a-ponit'),(6,'b-ponit'),(7,'c-ponit'),(8,'d-ponit'),(9,'lämminveriset'),(10,'ponit');
/*!40000 ALTER TABLE `vrlv3_lista_roturyhmat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrlv3_lista_tallikategoriat`
--

DROP TABLE IF EXISTS `vrlv3_lista_tallikategoriat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_lista_tallikategoriat` (
  `kat` smallint(2) NOT NULL AUTO_INCREMENT,
  `kategoria` varchar(20) CHARACTER SET utf8 NOT NULL,
  `katelyh` varchar(3) CHARACTER SET utf8 NOT NULL,
  `katnro` varchar(4) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`kat`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_lista_tallikategoriat`
--

LOCK TABLES `vrlv3_lista_tallikategoriat` WRITE;
/*!40000 ALTER TABLE `vrlv3_lista_tallikategoriat` DISABLE KEYS */;
INSERT INTO `vrlv3_lista_tallikategoriat` VALUES (1,'ratsastuskoulu','RK','KAT1'),(2,'siittola','ST','KAT2'),(3,'kilpailukeskus','KK','KAT3'),(4,'valjakkotalli','VT','KAT4'),(5,'ravitalli','RT','KAT4'),(6,'laukkatalli','LK','KAT4'),(7,'westerntalli','WT','KAT4'),(8,'myyntitalli','MT','KAT5'),(9,'oriasema','OA','KAT6'),(10,'yksityistalli','YT','KAT7'),(11,'muu kilpatalli','KT','KAT4'),(12,'tamma-asema','TA','KAT6'),(13,'harrastetalli','HT','KAT0');
/*!40000 ALTER TABLE `vrlv3_lista_tallikategoriat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrlv3_lista_tiedotuskategoriat`
--

DROP TABLE IF EXISTS `vrlv3_lista_tiedotuskategoriat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_lista_tiedotuskategoriat` (
  `kid` int(11) NOT NULL AUTO_INCREMENT,
  `kategoria` varchar(25) NOT NULL,
  PRIMARY KEY (`kid`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_lista_tiedotuskategoriat`
--

LOCK TABLES `vrlv3_lista_tiedotuskategoriat` WRITE;
/*!40000 ALTER TABLE `vrlv3_lista_tiedotuskategoriat` DISABLE KEYS */;
INSERT INTO `vrlv3_lista_tiedotuskategoriat` VALUES (1,'VVJ'),(2,'kilpailut'),(3,'kasvattajanimet'),(4,'kantakirjat'),(5,'sivusto'),(6,'rotuyhdistykset'),(7,'ERJ'),(8,'Vippos'),(9,'KERJ'),(10,'VRL-tunnukset'),(11,'seurat'),(12,'KRJ'),(13,'tallit'),(14,'VMJ'),(15,'PKK'),(16,'laatuarvostelut'),(17,'työpaikat'),(18,'ranking'),(19,'porrastetut kilpailut'),(20,'rekisteri'),(21,'kasvattajaklubi'),(22,'ARJ'),(23,'opisto'),(24,'WRJ'),(25,'hallitus'),(26,'näyttelyt'),(27,'hevosrekisteri'),(28,'VRL'),(29,'adoptointi');
/*!40000 ALTER TABLE `vrlv3_lista_tiedotuskategoriat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vrlv3_lista_varit`
--

DROP TABLE IF EXISTS `vrlv3_lista_varit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vrlv3_lista_varit` (
  `vid` smallint(4) NOT NULL AUTO_INCREMENT,
  `vari` text NOT NULL,
  `lyhenne` varchar(20) NOT NULL,
  `pvari` enum('Ei tiedossa','rn','m','rt') NOT NULL DEFAULT 'Ei tiedossa',
  `gen_vkko` tinyint(1) NOT NULL DEFAULT '0',
  `gen_tvkko` tinyint(1) NOT NULL DEFAULT '0',
  `gen_hkko` tinyint(1) NOT NULL DEFAULT '0',
  `gen_hp` tinyint(1) NOT NULL DEFAULT '0',
  `gen_cha` tinyint(1) NOT NULL DEFAULT '0',
  `gen_p` tinyint(1) NOT NULL DEFAULT '0',
  `gen_km` tinyint(1) NOT NULL DEFAULT '0',
  `gen_pais` tinyint(1) NOT NULL DEFAULT '0',
  `gen_kirj` tinyint(1) NOT NULL DEFAULT '0',
  `gen_kirj_t` tinyint(1) NOT NULL DEFAULT '0',
  `gen_kirj_s` tinyint(1) NOT NULL DEFAULT '0',
  `gen_kirj_fo` tinyint(1) NOT NULL DEFAULT '0',
  `gen_kirj_spl` tinyint(1) NOT NULL DEFAULT '0',
  `gen_kirj_tkirj` tinyint(1) NOT NULL DEFAULT '0',
  `gen_savy` tinyint(1) NOT NULL DEFAULT '0',
  `gen_mush` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vid`)
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vrlv3_lista_varit`
--

LOCK TABLES `vrlv3_lista_varit` WRITE;
/*!40000 ALTER TABLE `vrlv3_lista_varit` DISABLE KEYS */;
INSERT INTO `vrlv3_lista_varit` VALUES (1,'samppanjanruunikko','acha','rn',0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0),(2,'samppanjanruunikonkimo','achakm','rn',0,0,0,0,1,0,1,0,0,0,0,0,0,0,0,0),(3,'samppanjanruunikonkirjava','achakrj','rn',0,0,0,0,1,0,0,0,1,0,0,0,0,0,0,0),(4,'samppanjanruunikonpäistärikkö','achapäis','rn',0,0,0,0,1,0,0,1,0,0,0,0,0,0,0,0),(5,'samppanjanruunivoikko','accha','rn',1,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0),(6,'samppanjanruunivoikonkimo','acchakm','rn',1,0,0,0,1,0,1,0,0,0,0,0,0,0,0,0),(7,'samppanjanruunivoikonkirjava','acchakrj','rn',1,0,0,0,1,0,0,0,1,0,0,0,0,0,0,0),(8,'samppanjanruunivoikonpäistärikkö','acchapäis','rn',1,0,0,0,1,0,0,1,0,0,0,0,0,0,0,0),(9,'samppanjanruunihallakko','adcha','rn',0,0,1,0,1,0,0,0,0,0,0,0,0,0,0,0),(10,'samppanjanruunihallakonkimo','adchakm','rn',0,0,1,0,1,0,1,0,0,0,0,0,0,0,0,0),(11,'samppanjanruunihallakonkirjava','adchakrj','rn',0,0,1,0,1,0,0,0,1,0,0,0,0,0,0,0),(12,'samppanjanruunihallakonpäistärikkö','adchapäis','rn',0,0,1,0,1,0,0,1,0,0,0,0,0,0,0,0),(13,'samppanjanmusta','clcha','m',0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0),(14,'samppanjanmustankimo','clchakm','m',0,0,0,0,1,0,1,0,0,0,0,0,0,0,0,0),(15,'samppanjanmustankirjava','clchakrj','m',0,0,0,0,1,0,0,0,1,0,0,0,0,0,0,0),(16,'samppanjanmustanpäistärikkö','clchapäis','m',0,0,0,0,1,0,0,1,0,0,0,0,0,0,0,0),(17,'samppanjanmustanvoikko','clccha','m',1,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0),(18,'samppanjanmustanvoikonkimo','clcchakm','m',1,0,0,0,1,0,1,0,0,0,0,0,0,0,0,0),(19,'samppanjanmustanvoikonkirjava','clcchakrj','m',1,0,0,0,1,0,0,0,1,0,0,0,0,0,0,0),(20,'samppanjanmustanvoikonpäistärikkö','clcchapäis','m',1,0,0,0,1,0,0,1,0,0,0,0,0,0,0,0),(21,'samppanjanhiirakko','cldcha','m',0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0),(22,'samppanjanhiirakonkimo','cldchakm','m',0,0,0,0,1,0,1,0,0,0,0,0,0,0,0,0),(23,'samppanjanhiirakonkirjava','cldchakjr','m',0,0,0,0,1,0,0,0,1,0,0,0,0,0,0,0),(24,'samppanjanhiirakonpäistärikkö','cldchapäis','m',0,0,0,0,1,0,0,1,0,0,0,0,0,0,0,0),(25,'cremello','cre','rt',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(26,'cremellonkimo','crekm','rt',0,1,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(27,'cremellonkirjava','crekrj','rt',0,1,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(28,'cremellonpäistärikkö','crepäis','rt',0,1,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(29,'dominanttivalkoinen','dv','Ei tiedossa',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(30,'samppanjanrautias','gcha','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(31,'samppanjanrautiaankimo','gchakm','rt',0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(32,'samppanjanrautiaankirjava','gchakrj','rt',0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(33,'samppanjanrautiaanpäistärikkö','gchapäis','rt',0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(34,'samppanjanvoikko','gccha','rt',1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(35,'samppanjanvoikonkimo','gcchakm','rt',1,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(36,'samppanjanvoikonkirjava','gcchakrj','rt',1,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(37,'samppanjanvoikonpäistärikkö','gcchapäis','rt',1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(38,'samppanjanpunahallakko','gdcha','rt',0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0),(39,'samppanjanpunahallakonkimo','gdchakm','rt',0,0,1,0,0,0,1,0,0,0,0,0,0,0,0,0),(40,'samppanjanpunahallakonkirjava','gdchakrj','rt',0,0,1,0,0,0,0,0,1,0,0,0,0,0,0,0),(41,'samppanjanpunahallakonpäistärikkö','gdchapäis','rt',0,0,1,0,0,0,0,1,0,0,0,0,0,0,0,0),(46,'hiirakko','vm','m',0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0),(47,'hiirakonkimo','vmkm','m',0,0,1,0,0,0,1,0,0,0,0,0,0,0,0,0),(48,'hiirakonkirjava','vmkrj','m',0,0,1,0,0,0,0,0,1,0,0,0,0,0,0,0),(49,'hiirakonpäistärikkö','vmpäis','m',0,0,1,0,0,0,0,1,0,0,0,0,0,0,0,0),(50,'hopeanhiirakko','hpvm','m',0,0,1,1,0,0,0,0,0,0,0,0,0,0,0,0),(51,'hopeanhiirakonkimo','hpvmkm','m',0,0,1,1,0,0,1,0,0,0,0,0,0,0,0,0),(52,'hopeanhiirakonkirjava','hpvmkrj','m',0,0,1,1,0,0,0,0,1,0,0,0,0,0,0,0),(53,'hopeanhiirakonpäistärikkö','hpvmpäis','m',0,0,1,1,0,0,0,1,0,0,0,0,0,0,0,0),(54,'hopeanmusta','hpm','m',0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0),(55,'hopeanmustankimo','hpmkm','m',0,0,0,1,0,0,1,0,0,0,0,0,0,0,0,0),(56,'hopeanmustankirjava','hpmkrj','m',0,0,0,1,0,0,0,0,1,0,0,0,0,0,0,0),(57,'hopeanmustanpäistärikkö','hpmpäis','m',0,0,0,1,0,0,0,1,0,0,0,0,0,0,0,0),(58,'hopeanmustanvoikko','hpmvkk','m',1,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0),(59,'hopeanmustanvoikonkimo','hpmvkkokm','m',1,0,0,1,0,0,1,0,0,0,0,0,0,0,0,0),(60,'hopeanmustanvoikonkirjava','hpmvkkokrj','m',1,0,0,1,0,0,0,0,1,0,0,0,0,0,0,0),(61,'hopeanmustanvoikonpäistärikkö','hpmvkkopäis','m',1,0,0,1,0,0,0,1,0,0,0,0,0,0,0,0),(62,'hopeanruunikko','hprn','rn',0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0),(63,'hopeanruunikonkimo','hprnkm','rn',0,0,0,1,0,0,1,0,0,0,0,0,0,0,0,0),(64,'hopeanruunikonkirjava','hprnkrj','rn',0,0,0,1,0,0,0,0,1,0,0,0,0,0,0,0),(65,'hopeanruunikonpäistärikkö','hprnpäis','rn',0,0,0,1,0,0,0,1,0,0,0,0,0,0,0,0),(66,'hopeanruunihallakko','hprnhkko','rn',0,0,1,1,0,0,0,0,0,0,0,0,0,0,0,0),(67,'hopeanruunihallakonkimo','hprnhkkokm','rn',0,0,1,1,0,0,1,0,0,0,0,0,0,0,0,0),(68,'hopeanruunihallakonkirjava','hprnhkkokrj','rn',0,0,1,1,0,0,0,0,1,0,0,0,0,0,0,0),(69,'hopeanruunihallakonpäistärikkö','hprnhkkopäis','rn',0,0,1,1,0,0,0,1,0,0,0,0,0,0,0,0),(70,'hopeanruunivoikko','hprnvkk','rn',1,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0),(71,'hopeanruunivoikonhallakko','hprnvkkohkko','rn',1,0,1,1,0,0,0,0,0,0,0,0,0,0,0,0),(72,'hopeanruunivoikonhallakonkimo','hprnvkkohkkokm','rn',1,0,1,1,0,0,1,0,0,0,0,0,0,0,0,0),(73,'hopeanruunivoikonhallakonkirjava','hprnvkkohkkokrj','rn',1,0,1,1,0,0,0,0,1,0,0,0,0,0,0,0),(74,'hopeanruunivoikonhallakonpäistärikkö','hprnvkkohkkopäis','rn',1,0,1,1,0,0,0,1,0,0,0,0,0,0,0,0),(75,'hopeanruunivoikonkimo','hprnvkkokm','rn',1,0,0,1,0,0,1,0,0,0,0,0,0,0,0,0),(76,'hopeanruunivoikonkirjava','hprnvkkokrj','rn',1,0,0,1,0,0,0,0,1,0,0,0,0,0,0,0),(77,'hopeanruunivoikonpäistärikkö','hprnvkkopäis','rn',1,0,0,1,0,0,0,1,0,0,0,0,0,0,0,0),(78,'kanelirautias','krt','Ei tiedossa',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(79,'kimo','km','Ei tiedossa',0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(80,'kulorautias','klrt','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(81,'kärpäskimo','kkm','Ei tiedossa',0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(82,'lehmänkirjava','krj','Ei tiedossa',0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(83,'liinakko','lkk','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(84,'maksanrautias','mksrt','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(85,'mushroom','msh','Ei tiedossa',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(86,'mushroominkimo','mshkm','Ei tiedossa',0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(87,'mushroominkirjava','mshkrj','Ei tiedossa',0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(88,'mushroominpäistärikkö','mshpäis','Ei tiedossa',0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(89,'musta','m','m',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(90,'mustankimo','mkm','m',0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(91,'mustankimonkirjava','mkmkrj','m',0,0,0,0,0,0,1,0,1,0,0,0,0,0,0,0),(92,'mustankirjava','mkrj','m',0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(93,'mustanpäistärikkö','mpäis','m',0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(94,'mustanpäistärikönkirjava','mpäiskrj','m',0,0,0,0,0,0,0,1,1,0,0,0,0,0,0,0),(95,'mustanruunikko','mrn','rn',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(96,'mustanvoikko','mvkk','m',1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(97,'mustanvoikonkimo','mvkkokm','m',1,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(98,'mustanvoikonkirjava','mvkkokrj','m',1,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(99,'mustanvoikonpäistärikkö','mvkkopäis','m',1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(100,'perlino','pe','rn',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(101,'perlinonkimo','pekm','rn',0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(102,'perlinonkirjava','pekrj','rn',0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(103,'perlinonpäistärikkö','pepäis','rn',0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(104,'punahallakko','phkko','rt',0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0),(105,'punahallakonkimo','phkkokm','rt',0,0,1,0,0,0,1,0,0,0,0,0,0,0,0,0),(106,'punahallakonkirjava','phkkokrj','rt',0,0,1,0,0,0,0,0,1,0,0,0,0,0,0,0),(107,'punahallakonpäistärikkö','phkkopäis','rt',0,0,1,0,0,0,0,1,0,0,0,0,0,0,0,0),(108,'punarautias','prt','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(109,'punaruunikko','prn','rn',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(110,'päistärikkö','päis','Ei tiedossa',0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(111,'päistärikönkimo','päiskm','Ei tiedossa',0,0,0,0,0,0,1,1,0,0,0,0,0,0,0,0),(113,'päistärikönkirjava','päiskrj','Ei tiedossa',0,0,0,0,0,0,0,1,1,0,0,0,0,0,0,0),(114,'rautias','rt','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(115,'rautiaankimo','rtkm','rt',0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(116,'rautiaankimonkirjava','rtkmkrj','rt',0,0,0,0,0,0,1,0,1,0,0,0,0,0,0,0),(117,'rautiaankirjava','rtkrj','rt',0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(118,'rautiaanpäistärikkö','rtpäis','rt',0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(119,'rautiaanpäistärikönkirjava','rtpäiskrj','rt',0,0,0,0,0,0,0,1,1,0,0,0,0,0,0,0),(120,'ruunikko','rn','rn',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(121,'ruunihallakko','rnhkko','rn',0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0),(122,'ruunihallakonkimo','rnhkkokm','rn',0,0,1,0,0,0,1,0,0,0,0,0,0,0,0,0),(123,'ruunihallakonkirjava','rnhkkokrj','rn',0,0,1,0,0,0,0,0,1,0,0,0,0,0,0,0),(124,'ruunihallakonpäistärikkö','rnhkkopäis','rn',0,0,1,0,0,0,0,1,0,0,0,0,0,0,0,0),(125,'ruunikonkimo','rnkm','rn',0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(126,'ruunikonkimonkirjava','rnkrjkm','rn',0,0,0,0,0,0,1,0,1,0,0,0,0,0,0,0),(127,'ruunikonkirjava','rnkrj','rn',0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(128,'ruunikonpäistärikkö','rnpäis','rn',0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(129,'ruunikonpäistärikönkirjava','rnpäiskrj','rn',0,0,0,0,0,0,0,1,1,0,0,0,0,0,0,0),(130,'ruunivoikko','rnvkk','rn',1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(131,'ruunivoikonkimo','rnvkkokm','rn',1,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(132,'ruunivoikonkirjava','rnvkkokrj','rn',1,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(133,'ruunivoikonpäistärikkö','rnvkkopäis','rn',1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(134,'ruunivoikonhallakko','vhkko','rn',1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0),(135,'ruunivoikonhallakonkimo','vhkkokm','rn',1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0),(136,'ruunivoikonhallakonkirjava','vhkkokrj','rn',1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0),(137,'ruunivoikonhallakonpäistärikkö','vhkkopäis','rn',1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0),(138,'savakkorautias','srt','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(143,'tiikerinkirjava','kn','Ei tiedossa',0,0,0,0,0,0,0,0,1,0,0,0,0,1,0,0),(144,'tummanpunarautias','tprt','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(145,'tummanpunaruunikko','tprn','rn',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(146,'tummanrautias','trt','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(147,'tummanruunikko','trn','rn',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(148,'vaaleanpunarautias','vprt','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(149,'vaaleanpunaruunikko','vprn','rn',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(150,'vaaleanrautias','vrt','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(151,'vaaleanruunikko','vrn','rn',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(152,'valkovoikko','bec','Ei tiedossa',0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(153,'voikko','vkk','rt',1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(154,'voikonhallakko','khkko','rt',1,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0),(155,'voikonkirjava','vkkokrj','rt',1,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(156,'smoky cream','smoky cream','m',1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(157,'smoky cream kimo','smoky cream km','m',1,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(158,'smoky cream päistärikkö','smoky cream päis','m',1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(159,'smoky cream kirjava','smoky cream krj','m',1,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0),(160,'voikonkimo','vkkkm','rt',1,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0),(161,'voikonpäistärikkö','vkkpäis','rt',1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0),(162,'kulomusta','klm','m',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(163,'musta tiikerinkirjava','mtkkrj','m',0,0,0,0,0,0,0,0,1,0,0,0,0,1,0,0),(164,'rautias tiikerinkirjava','rtkkrj','rt',0,0,0,0,0,0,0,0,1,0,0,0,0,1,0,0),(166,'ruunikko tiikerinkirjava','rntkkrj','rt',0,0,0,0,0,0,0,0,1,0,0,0,0,1,0,0),(167,'rautiaanpäistärikönkimo','rtpäistkm','rt',0,0,0,0,0,0,1,1,0,0,0,0,0,0,0,0),(168,'rautiaanpäistärikönkimonkirjava','rtpäistkmkrj','rt',0,0,0,0,0,0,1,1,1,0,0,0,0,0,0,0),(169,'mustanpäistärikönkimonkirjava','mpäistkmkrj','m',0,0,0,0,0,0,1,1,1,0,0,0,0,0,0,0),(170,'ruunikonpäistärikönkimonkirjava','rnpäistkmkrj','rn',0,0,0,0,0,0,1,1,1,0,0,0,0,0,0,0),(172,'hopeanruunikonpäistärikönkimo','hprnpäiskm','rn',0,0,0,1,0,0,1,1,0,0,0,0,0,0,0,0),(173,'hopeanmustankimonkirjava','hpmkmkrj','m',0,0,0,1,0,0,1,0,1,0,0,0,0,0,0,0),(174,'hopeanruunikonkimonkirjava','hprnkmkrj','rn',0,0,0,1,0,0,1,0,1,0,0,0,0,0,0,0),(175,'voikonkimonkirjava','vkkokmkrj','rt',1,0,0,0,0,0,1,0,1,0,0,0,0,0,0,0),(176,'hiirakonpäistärikönkirjava','vmpäiskrj','m',0,0,1,0,0,0,0,1,1,0,0,0,0,0,0,0),(177,'ruunikonpäistärikönkimo','rnpäiskm','rn',0,0,0,0,0,0,1,1,0,0,0,0,0,0,0,0),(178,'sysirautias','sysrt','rt',0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0),(179,'sfgsdg','AERY','rn',1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,1),(181,'vihreä','vr','m',1,1,0,1,0,0,0,0,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `vrlv3_lista_varit` ENABLE KEYS */;
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

-- Dump completed on 2021-01-27 21:00:31
