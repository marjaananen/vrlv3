-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 22.07.2015 klo 12:00
-- Palvelimen versio: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `vrlv3`
--

-- --------------------------------------------------------

--
-- Rakenne taululle `vrlv3_lista_maakunnat`
--

DROP TABLE IF EXISTS `vrlv3_lista_maakunnat`;
CREATE TABLE `vrlv3_lista_maakunnat` (
  `id` smallint(2) NOT NULL AUTO_INCREMENT,
  `maakunta` tinytext COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=22 ;

--
-- Vedos taulusta `vrlv3_lista_maakunnat`
--

INSERT INTO `vrlv3_lista_maakunnat` (`id`, `maakunta`) VALUES
(0, 'Ei saatavilla'),
(1, 'Ahvenanmaa'),
(2, 'Etelä-Karjala'),
(3, 'Etelä-Pohjanmaa'),
(4, 'Etelä-Savo'),
(5, 'Itä-Uusimaa'),
(6, 'Kainuu'),
(7, 'Kanta-Häme'),
(8, 'Keski-Pohjanmaa'),
(9, 'Keski-Suomi'),
(10, 'Kymenlaakso'),
(11, 'Lappi'),
(12, 'Pirkanmaa'),
(13, 'Pohjanmaa'),
(14, 'Pohjois-Karjala'),
(15, 'Pohjois-Pohjanmaa'),
(16, 'Pohjois-Savo'),
(17, 'Päijät-Häme'),
(18, 'Satakunta'),
(19, 'Uusimaa'),
(20, 'Varsinais-Suomi'),
(21, 'Ulkomaat');


-- 
-- Rakenne taululle `vrlv3_lista_tallikategoriat`
-- 

CREATE TABLE IF NOT EXISTS `vrlv3_lista_tallikategoriat` (
  `kat` smallint(2) NOT NULL auto_increment,
  `kategoria` varchar(20) character set utf8 NOT NULL,
  `katelyh` varchar(3) character set utf8 NOT NULL,
  `katnro` varchar(4) collate utf8_swedish_ci NOT NULL,
  PRIMARY KEY  (`kat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=14 ;

-- 
-- Vedostetaan dataa taulusta `lista_tallikategoriat`
-- 

INSERT INTO `vrlv3_lista_tallikategoriat` (`kat`, `kategoria`, `katelyh`, `katnro`) VALUES 
(1, 'ratsastuskoulu', 'RK', 'KAT1'),
(2, 'siittola', 'ST', 'KAT2'),
(3, 'kilpailukeskus', 'KK', 'KAT3'),
(4, 'valjakkotalli', 'VT', 'KAT4'),
(5, 'ravitalli', 'RT', 'KAT4'),
(6, 'laukkatalli', 'LK', 'KAT4'),
(7, 'westerntalli', 'WT', 'KAT4'),
(8, 'myyntitalli', 'MT', 'KAT5'),
(9, 'oriasema', 'OA', 'KAT6'),
(10, 'yksityistalli', 'YT', 'KAT7'),
(11, 'muu kilpatalli', 'KT', 'KAT4'),
(12, 'tamma-asema', 'TA', 'KAT6'),
(13, 'harrastetalli', 'HT', 'KAT0');



--
-- Rakenne taululle `vrlv3_lista_tiedotuskategoriat`
--

CREATE TABLE IF NOT EXISTS `vrlv3_lista_tiedotuskategoriat` (
  `kid` int(11) NOT NULL AUTO_INCREMENT,
  `kategoria` varchar(25) NOT NULL,
  PRIMARY KEY (`kid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Vedos taulusta `vrlv3_lista_tiedotuskategoriat`
--

INSERT INTO `vrlv3_lista_tiedotuskategoriat` (`kid`, `kategoria`) VALUES
(1, 'VVJ'),
(2, 'kilpailut'),
(3, 'kasvattajanimet'),
(4, 'kantakirjat'),
(5, 'sivusto'),
(6, 'rotuyhdistykset'),
(7, 'ERJ'),
(8, 'Vippos'),
(9, 'KERJ'),
(10, 'VRL-tunnukset'),
(11, 'seurat'),
(12, 'KRJ'),
(13, 'tallit'),
(14, 'VMJ'),
(15, 'PKK'),
(16, 'laatuarvostelut'),
(17, 'työpaikat'),
(18, 'ranking'),
(19, 'porrastetut kilpailut'),
(20, 'rekisteri'),
(21, 'kasvattajaklubi'),
(22, 'ARJ'),
(23, 'opisto'),
(24, 'WRJ'),
(25, 'hallitus'),
(26, 'näyttelyt'),
(27, 'hevosrekisteri'),
(28, 'VRL'),
(29, 'adoptointi');


-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-7
-- http://www.phpmyadmin.net
-- 
-- Palvelin: localhost
-- Luontiaika: 07.10.2015 klo 20:20
-- Palvelimen versio: 5.0.32
-- PHP:n versio: 5.2.0-8+etch11
-- 
-- Tietokanta: `vrlv10`
-- 

-- --------------------------------------------------------
-- 
-- Rakenne taululle `lista_roturyhmat`
-- 

CREATE TABLE `vrlv3_lista_roturyhmat` (
  `id` int(1) NOT NULL auto_increment,
  `ryhma` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- 
-- Vedostetaan dataa taulusta `lista_roturyhmat`
-- 

INSERT INTO `vrlv3_lista_roturyhmat` (`id`, `ryhma`) VALUES
(1, 'täysiveriset'),
(2, 'puoliveriset'),
(3, 'kylmäveriset'),
(4, 'raskaat kylmäveriset'),
(5, 'a-ponit'),
(6, 'b-ponit'),
(7, 'c-ponit'),
(8, 'd-ponit'),
(9, 'lämminveriset'),
(10, 'ponit');


-- 
-- Rakenne taululle `lista_rodut`
-- 

CREATE TABLE `vrlv3_lista_rodut` (
  `rotunro` smallint(3) NOT NULL auto_increment,
  `rotu` text NOT NULL,
  `lyhenne` varchar(6) NOT NULL,
  `roturyhma` int(1),
  `harvinainen` int(1) NOT NULL default '0',
  PRIMARY KEY  (`rotunro`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=277 ;

-- 
-- Vedostetaan dataa taulusta `lista_rodut`
-- 

INSERT INTO `vrlv3_lista_rodut` (`rotunro`, `rotu`, `lyhenne`, `roturyhma`, `harvinainen`) VALUES 
(1, 'Ahaltekinhevonen', 'at', 9, 0),
(2, 'Amerikanponi', 'poa', 10, 0),
(3, 'Arabialainen täysverinen', 'ox', 1, 0),
(4, 'Azteca', 'azt', 9, 0),
(5, 'Clevelandinruunikko', 'cb', 9, 0),
(6, 'Englantilainen täysiverinen', 'xx', 1, 0),
(7, 'Falabella', 'falab', 10, 0),
(8, 'Frederiksborginhevonen', 'fred', 9, 0),
(9, 'Kalliovuortenhevonen', 'rmh', 9, 0),
(10, 'Knabstrupinhevonen', 'knn', 9, 0),
(11, 'Hannoverinhevonen', 'hann', 2, 0),
(12, 'Hollanninpuoliverinen', 'kwpn', 2, 0),
(13, 'Oldenburginhevonen', 'old', 2, 0),
(14, 'Quarterhevonen', 'qh', 9, 0),
(15, 'Russian-Based Sport Horse', 'rbsh', 9, 0),
(16, 'Saddlebred', 'sdb', 9, 0),
(17, 'Shetlanninponi', 'she', 10, 0),
(18, 'Suomenhevonen', 'sh', 3, 0),
(19, 'Tennesseenwalker', 'twh', 9, 0),
(20, 'Terskinhevonen', 'tersk', 9, 0),
(21, 'Trakehner', 'trak', 2, 0),
(22, 'Welsh sektio B', 'wB', 10, 0),
(23, 'Welsh Part Bred', 'wpb', 10, 0),
(24, 'Welsh Mountain', 'wA', 10, 0),
(25, 'Welsh sektio C', 'wC', 10, 0),
(26, 'Welsh sektio D', 'wD', 10, 0),
(27, 'Newforestinponi', 'nf', 10, 0),
(28, 'Gotlanninrussi', 'russ', 10, 0),
(29, 'Ratsuponi', 'rp', 10, 0),
(30, 'Islanninhevonen', 'isl', NULL, 0),
(31, 'Suomalainen puoliverinen', 'fwb', 2, 0),
(32, 'Gelderlandinhevonen', 'geldr', 9, 0),
(33, 'Andalusianhevonen', 'pre', 9, 0),
(34, 'Angloarabialainen täysiverinen', 'x', 1, 0),
(35, 'Connemaranponi', 'conn', 10, 0),
(36, 'Irlannintyöhevonen', 'id', 4, 0),
(37, 'Appaloosa', 'app', 9, 0),
(38, 'Amerikanravuri', 'lv', 9, 0),
(39, 'Budjonnynhevonen', 'budj', 9, 0),
(40, 'Friisiläishevonen', 'fri', NULL, 0),
(41, 'Perchenhevonen', 'perch', 4, 0),
(42, 'Kaspianponi', 'kasp', 10, 0),
(43, 'Ruotsinpuoliverinen', 'swb', 2, 0),
(44, 'Holsteininhevonen', 'holst', 2, 0),
(45, 'Lämminveriravuri', 'lv', 9, 0),
(46, 'Irlannincob', 'ic', NULL, 0),
(47, 'Hackney', 'hackn', 9, 0),
(48, 'Westfaleninhevonen', 'westf', 2, 0),
(49, 'Furioso', 'fur', 9, 0),
(50, 'Marwarinhevonen', 'marw', 9, 0),
(51, 'Pohjoisnorjanponi', 'pnp', 10, 0),
(52, 'Paint Horse', 'ph', 9, 0),
(53, 'Norjanvuonohevonen', 'nvh', 3, 0),
(54, 'Lämminverinen ratsuhevonen', 'pv', 2, 0),
(55, 'Mustangi', 'must', 9, 0),
(56, 'Unkarinpuoliverinen', 'hwb', 2, 0),
(57, 'Haflinginhevonen', 'hafl', 3, 0),
(58, 'Pintabian', 'pintab', 9, 0),
(59, 'Morganinhevonen', 'morg', 9, 0),
(60, 'Eestinhevonen', 'eest', 3, 0),
(61, 'Lewitzer', 'lew', 10, 0),
(62, 'Clydesdalenhevonen', 'clyd', 4, 0),
(63, 'Dartmoorinponi', 'dartm', 10, 0),
(64, 'Lipizzanhevonen', 'lip', 9, 0),
(65, 'Pinto', 'pinto', NULL, 0),
(66, 'Brandenburginhevonen', 'brdbg', 2, 0),
(67, 'Paso Fino', 'pf', 9, 0),
(68, 'Perunpaso', 'pp', 9, 0),
(69, 'Irlannintyöhevonen', 'idh', 9, 0),
(70, 'Kisber-felver', 'kisb', 9, 0),
(71, 'Ylämaanponi', 'highl', 10, 0),
(72, 'Missourinfoxtrotter', 'mft', 9, 0),
(73, 'Kanadanhevonen', 'kan', 9, 0),
(74, 'Turkmeeninhevonen', 'turkm', 9, 0),
(75, 'Kerry Bog Pony', 'kbp', 10, 0),
(76, 'Belgianpuoliverinen', 'bwp', 2, 0),
(77, 'Tanskanpuoliverinen', 'dwb', 2, 0),
(78, 'National Show Horse', 'nsh', 9, 0),
(79, 'Dalesponi', 'dales', 10, 0),
(80, 'Pohjoisruotsinhevonen', 'prh', 3, 0),
(81, 'American Bashkir Curly', 'abc', 9, 0),
(82, 'Mangalarga', 'manga', 9, 0),
(83, 'Criollo', 'cri', 9, 0),
(84, 'Berberihevonen', 'berb', 9, 0),
(85, 'Hackneyponi', 'hacknp', 10, 0),
(86, 'Ariegenponi', 'arieg', 10, 0),
(87, 'Lusitano', 'lusit', 9, 0),
(88, 'Camarguenhevonen', 'camar', 9, 0),
(89, 'Fellponi', 'fell', 10, 0),
(90, 'Anglo-trakehner', 'a-trak', 2, 0),
(91, 'Amerikanshetlanninponi', 'amshe', 10, 0),
(92, 'Exmoorinponi', 'exm', 10, 0),
(93, 'Torinhevonen', 'tori', NULL, 0),
(94, 'Ardennienhevonen', 'ard', 4, 0),
(95, 'Belgiantyöhevonen', 'belg', 4, 0),
(96, 'Vladimirintyöhevonen', 'vlad', 4, 0),
(97, 'Orlovravuri', 'orlov', 9, 0),
(98, 'Ranskanravuri', 'ransk', 9, 0),
(99, 'Shagya', 'sha', 9, 0),
(100, 'Wielkopolski', 'wielk', 9, 0),
(101, 'Venäjänratsuhevonen', 'vrh', 9, 0),
(102, 'Tilastohevonen', 'til', NULL, 0),
(103, 'Sleesianhevonen', 'slee', NULL, 0),
(104, 'Shirenhevonen', 'shire', 4, 0),
(105, 'Nonius', 'non', 9, 0),
(106, 'Huzulponi', 'huzul', NULL, 0),
(107, 'Malopolski', 'malo', 9, 0),
(108, 'Normandiancob', 'ncob', 4, 0),
(109, 'Salernonhevonen', 'saler', 9, 0),
(110, 'Boulognenhevonen', 'boul', 4, 0),
(111, 'Santelipuuponi', 'sant', 10, 0),
(112, 'Noricuminhevonen', 'norik', 4, 0),
(113, 'Jakutianponi', 'jakut', 10, 0),
(114, 'Morab', 'morab', 9, 0),
(115, 'Australianhevonen', 'austr', 9, 0),
(116, 'Novokirgiisi', 'novok', 9, 0),
(117, 'Valkovenäjäntyöhevonen', 'valk', 4, 0),
(118, 'Baskirianponi', 'baskir', 10, 0),
(119, 'Suffolkinhevonen', 'suff', 4, 0),
(120, 'Doninhevonen', 'don', 9, 0),
(121, 'Ranskanpuoliverinen', 'sf', 2, 0),
(122, 'Irlanninpuoliverinen', 'ihb', 2, 0),
(123, 'Ukrainanratsuhevonen', 'ukrh', 9, 0),
(124, 'Auxois', 'aux', 4, 0),
(125, 'Egyptinhevonen', 'egypt', 9, 0),
(126, 'Saarenmaanponi', 'saare', 10, 0),
(127, 'Sanhe', 'sanhe', 9, 0),
(128, 'Guangxi', 'guang', 10, 0),
(129, 'Schwarzwaldinrautias', 'schwa', 4, 0),
(130, 'American Cream Draft', 'amc', 4, 0),
(131, 'Drum Horse', 'drum', 4, 0),
(132, 'Misaki', 'misak', 10, 0),
(133, 'Colorado Ranger', 'colr', 9, 0),
(134, 'Eteläsaksankylmäverinen', 'eskv', 4, 0),
(135, 'Hollannintyöhevonen', 'holkv', 4, 0),
(136, 'Altainhevonen', 'altai', 9, 0),
(137, 'Lokainponi', 'lokai', 10, 0),
(138, 'Iomud', 'iomud', 9, 0),
(139, 'Hesseninhevonen', 'hesse', 2, 0),
(140, 'Basutonponi', 'basuto', 10, 0),
(141, 'Sveitsinpuoliverinen', 'chwb', 2, 0),
(142, 'Deliboz', 'delib', 9, 0),
(143, 'Avellinonponi', 'avell', 10, 0),
(144, 'Jyllanninhevonen', 'jyll', 4, 0),
(145, 'Sorraianponi', 'sorr', 10, 0),
(146, 'Vironpuoliverinen', 'estpv', 2, 0),
(147, 'Garranonponi', 'garra', 10, 0),
(148, 'Hispano', 'hispa', 9, 0),
(149, 'Tsetsenianvuoristoponi', 'tsets', 10, 0),
(150, 'Karpaaninhevonen', 'karp', NULL, 0),
(151, 'Baijerinhevonen', 'baij', 2, 0),
(152, 'Florida Cracker', 'flacr', 9, 0),
(153, 'Mecklenburginhevonen', 'meckl', 2, 0),
(154, 'Cayuse', 'cay', 9, 0),
(155, 'Amerikanpuoliverinen', 'amwb', 2, 0),
(156, 'Freiberginhevonen', 'freib', 3, 0),
(157, 'Abessinianhevonen', 'abess', 9, 0),
(158, 'Adaev', 'adaev', 9, 0),
(159, 'Bretagnenhevonen', 'bret', 4, 0),
(160, 'Alter real', 'alter', 9, 0),
(161, 'Jaavanponi', 'jaav', 10, 0),
(162, 'Zemaituka', 'zhe', 3, 0),
(163, 'Anglo-kabardinhevonen', 'a-kab', 9, 0),
(164, 'Australianponi', 'austp', 10, 0),
(165, 'Friesian Sport Horse', 'fsh', 9, 0),
(166, 'Batakinponi', 'batak', 10, 0),
(167, 'Baskinponi', 'pottok', 10, 0),
(168, 'Dülmeninponi', 'dülm', 10, 0),
(169, 'Dölenhevonen', 'döle', 3, 0),
(170, 'Kisonponi', 'kiso', 10, 0),
(171, 'Peneioksenponi', 'penei', 10, 0),
(172, 'Maremmanhevonen', 'marem', 9, 0),
(173, 'Konik', 'konik', NULL, 0),
(174, 'Einsiedelninhevonen', 'einsi', 2, 0),
(175, 'Barock-pinto', 'barock', 9, 0),
(176, 'Walkaloosa', 'walkal', 9, 0),
(177, 'American Miniature Horse', 'amh', 10, 0),
(178, 'Ara-appaloosa', 'ara-ap', 9, 0),
(179, 'Quarab', 'qox', 9, 0),
(180, 'Pintaloosa', 'pintl', NULL, 0),
(181, 'Skyroksenponi', 'skyro', 10, 0),
(182, 'Padang', 'pad', 10, 0),
(183, 'Hokkaido', 'hokk', 10, 0),
(184, 'Bardianponi', 'bard', 10, 0),
(185, 'Sumbanponi', 'sumb', 10, 0),
(186, 'Kustanairinhevonen', 'kustn', 9, 0),
(187, 'Kabardiini', 'kabard', 9, 0),
(188, 'Ban-ei', 'ban-ei', 4, 0),
(189, 'Landesinponi', 'landes', 10, 0),
(190, 'Buurinponi', 'buur', 10, 0),
(191, 'Vjatkanponi', 'vjatk', 10, 0),
(192, 'Latvianhevonen', 'latv', NULL, 0),
(193, 'Karabair', 'karab', 9, 0),
(194, 'Tiibetinponi', 'tiib', 10, 0),
(195, 'Gidran', 'gid', 9, 0),
(196, 'Paso Creole', 'pc', 9, 0),
(197, 'Chincoteaguenponi', 'cctg', 10, 0),
(198, 'Dutch Harness Horse', 'dhh', 9, 0),
(199, 'Raskas liettuantyöhevonen', 'liet', 4, 0),
(200, 'Eriskaynponi', 'erisk', 10, 0),
(201, 'Kladrubinhevonen', 'klad', 9, 0),
(202, 'Englanninpuoliverinen', 'ewb', 2, 0),
(203, 'Zweibrücker', 'zweib', 2, 0),
(204, 'Comtois', 'comt', 4, 0),
(205, 'Reinintyöhevonen', 'rkv', 4, 0),
(206, 'Noma', 'noma', 10, 0),
(207, 'Altösterreichisches Warmblut', 'altö', 2, 0),
(208, 'British Spotted Pony', 'bsp', 10, 0),
(209, 'Irish Sport Horse', 'ish', 2, 0),
(210, 'Spanish Barb', 'spb', 9, 0),
(211, 'Spanish-Norman', 'spn', 9, 0),
(212, 'Galiceño', 'gcñ', 9, 0),
(213, 'Spotted Draft Horse', 'sdh', 4, 0),
(214, 'Baluchinhevonen', 'baluch', 9, 0),
(215, 'Bosnianponi', 'bosn', 10, 0),
(216, 'Hackneyarabi', 'hac-ox', 9, 0),
(217, 'Saksalainen ratsuhevonen', 'saksrh', 2, 0),
(218, 'Arabialainen puoliverinen', 'awb', 2, 0),
(219, 'Asturianponi', 'ast', 10, 0),
(220, 'Newfoundlandinponi', 'nfld', 10, 0),
(221, 'Slovakianpuoliverinen', 'slvkpv', 2, 0),
(222, 'Sardinianhevonen', 'sard', 9, 0),
(223, 'Spotted Saddle Horse', 'ssh', 9, 0),
(224, 'Hunter', 'hunter', 9, 0),
(225, 'Poitounhevonen', 'poitou', 4, 0),
(226, 'Raskas neuvostoliitontyöhevonen', 'shd', 4, 0),
(227, 'Eestin raskas vetohevonen', 'erv', 4, 0),
(228, 'Färsaartenponi', 'färsp', 10, 0),
(229, 'Badenwürttemberginhevonen', 'bwürt', 2, 0),
(230, 'Reininhevonen', 'rhld', 2, 0),
(231, 'Slovenianpuoliverinen', 'slvnpv', 2, 0),
(232, 'Itävallanpuoliverinen', 'itävpv', 2, 0),
(233, 'Itäfriisinhevonen', 'itäfr', 2, 0),
(234, 'Tigerhevonen', 'tiger', 9, 0),
(235, 'Quarterponi', 'qp', 10, 0),
(236, 'Gaited Baroque', 'gb', 9, 0),
(237, 'Welara', 'welara', 10, 0),
(238, 'Welara Sport Pony', 'welsp', 10, 0),
(239, 'Lippitt Morgan', 'lpm', 9, 0),
(241, 'Italiantyöhevonen', 'tpr', 4, 0),
(242, 'Euroopan miniatyyrihevonen', 'emh', 10, 0),
(243, 'Kinskynhevonen', 'kinsk', 2, 0),
(244, 'Georgian Grande', 'gg', 9, 0),
(245, 'American Walking Pony', 'awp', 10, 0),
(246, 'Moriesian', 'mories', 9, 0),
(248, 'Waler', 'waler', 9, 0),
(249, 'Choctaw', 'choct', 9, 0),
(250, 'Tokara', 'tokar', 10, 0),
(251, 'Pottokponi', 'pottok', 10, 0),
(252, 'Murgenhevonen', 'murg', 9, 0),
(253, 'Sim-Game Sport Horse', 'sgsh', 2, 0),
(254, 'Eestin urheiluhevonen', 'esh', 2, 0),
(255, 'Muraközinhevonen', 'murak', 4, 0),
(256, 'Argentiinanpuoliverinen', 'arwb', 2, 0),
(257, 'Brasilianpuoliverinen', 'brzwb', 2, 0),
(258, 'Luxemburginpuoliverinen', 'luxwb', 2, 0),
(259, 'Saksin-Anhaltinhevonen', 's-anh', 2, 0),
(260, 'Pooloponi', 'poolo', 9, 0),
(261, 'Virtuaalinen ratsuponi', 'vrp', 10, 0),
(262, 'Trait du Nord', 'tdn', 4, 0),
(263, 'Kanadanpuoliverinen', 'kanpv', 2, 0),
(264, 'Campolina', 'camp', 9, 0),
(265, 'Nokota', 'nok', 9, 0),
(266, 'Anglo-argentino', 'a-arg', 9, 0),
(267, 'Norjanpuoliverinen', 'nwb', 2, 0),
(268, 'Sim Sport Pony', 'ssp', 10, 0),
(269, 'Sim Sport Warmblood', 'ssw', 9, 0),
(270, 'Kylmäveriravuri', 'kvr', 3, 0),
(271, 'Raskas venäjäntyöhevonen', 'rhd', 4, 0),
(272, 'Zangersheide', 'Z', 2, 0),
(273, 'Venäjänravuri', 'venrav', 9, 0),
(274, 'Albanianponi', 'alb', NULL, 0),
(275, 'Puolalainen puoliverinen', 'pl', NULL, 0),
(276, 'Kathiawarinhevonen', 'kath', NULL, 0);

ALTER TABLE `vrlv3_lista_rodut` ADD  FOREIGN KEY (`roturyhma`) REFERENCES `vrlv3`.`vrlv3_lista_roturyhmat`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
