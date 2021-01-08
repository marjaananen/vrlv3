CREATE TABLE `vrlv3_lista_puljutyyppi` (
  `pid` smallint(2) NOT NULL AUTO_INCREMENT,
  `tyyppi` varchar(20) NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `vrlv3_puljut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nimi` varchar(45) NOT NULL,
  `lyhenne` varchar(10) NOT NULL,
  `toiminnassa` tinyint(4) NOT NULL DEFAULT '1',
  `url` text,
  `kuvaus` text,
  `tyyppi` smallint(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `laji_idx` (`tyyppi`),
  CONSTRAINT `tyyppi` FOREIGN KEY (`tyyppi`) REFERENCES `vrlv3_lista_puljutyyppi` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `vrlv3_puljut_omistajat` (
  `jid` int(11) NOT NULL,
  `tunnus` int(11) unsigned NOT NULL,
  `taso` int(3) DEFAULT '0',
  PRIMARY KEY (`jid`,`tunnus`),
  KEY `vrltunnus_idxxxx` (`tunnus`),
  CONSTRAINT `puljutunnusx` FOREIGN KEY (`jid`) REFERENCES `vrlv3_puljut` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vrltunnusx` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `vrlv3_puljut_rodut` (
  `pulju` int(11) NOT NULL,
  `rotu` smallint(3) NOT NULL,
  PRIMARY KEY (`pulju`,`rotu`),
  KEY `rotunimi_idxx` (`rotu`),
  CONSTRAINT `rotunimi` FOREIGN KEY (`rotu`) REFERENCES `vrlv3_lista_rodut` (`rotunro`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `puljunimi` FOREIGN KEY (`pulju`) REFERENCES `vrlv3_puljut` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `vrlv3`.`vrlv3_lista_puljutyyppi` (`pid`, `tyyppi`) VALUES ('1', 'kantakirja');
INSERT INTO `vrlv3`.`vrlv3_lista_puljutyyppi` (`pid`, `tyyppi`) VALUES ('2', 'rotuyhdistys');
INSERT INTO `vrlv3`.`vrlv3_lista_puljutyyppi` (`pid`, `tyyppi`) VALUES ('3', 'laatuarvostelu');

INSERT INTO `vrlv3`.`vrlv3_puljut` (`id`, `nimi`, `lyhenne`, `toiminnassa`, `url`, `kuvaus`, `tyyppi`) VALUES ('1', 'Virtuaalinen Hevoskantakirja', 'hevosktk', '1', 'http://sumuvuorten.net/hevosktk/', 'Virtuaalinen Hevoskantakirja on kantakirja kaikille niille virtuaalihevosroduille joille ei ole omaa kantakirjaa. Hevoskantakirja on ponikantakirjan ohella vanhimpia virtuaalikantakirjoja.', '1');
INSERT INTO `vrlv3`.`vrlv3_puljut` (`nimi`, `lyhenne`, `toiminnassa`, `url`, `kuvaus`, `tyyppi`) VALUES ('Virtuaalinen Ponikantakirja', 'poniktk', '1', 'http://sudenmarja.org/poniktk/', 'Virtuaalinen Ponikantakirja on kantakirja kaikille niille virtuaaliponiroduille, joille ei ole omaa kantakirjaa. Ponikantakirja perustettiin vuonna 2001. ', '1');
INSERT INTO `vrlv3`.`vrlv3_puljut` (`nimi`, `lyhenne`, `toiminnassa`, `url`, `kuvaus`, `tyyppi`) VALUES ('Virtuaalinen Suomenhevoskantakirja', 'shktk', '1', 'http://varjoton.net/shktk/', 'Virtuaalinen Suomenhevoskantakirja perustettiin vuoden 2003 heinäkuussa rodun edustajien runsaan määrän sekä monipuolisuuden vuoksi. Kuten IRL-maailmassa, myös virtuaalisille suomenhevosille tarjotaan mahdollisuus kantakirjata hevoset neljälle eri suunnalle. Nämä suunnat ovat ratsu-, juoksija-, työhevos- ja pienhevossuunta. ', '1');
INSERT INTO `vrlv3`.`vrlv3_puljut` (`nimi`, `lyhenne`, `toiminnassa`, `kuvaus`, `tyyppi`) VALUES ('Virtuaalinen Arabikantakirja', 'arabiktk', '0', 'Virtuaalinen Arabikantakirja toimi vuosina 2003-2015, jonka jälkeen arabit siirtyivät hevoskantakirjaan.', '1');
INSERT INTO `vrlv3`.`vrlv3_puljut` (`nimi`, `lyhenne`, `toiminnassa`, `url`, `kuvaus`, `tyyppi`) VALUES ('Yleislaatuarvostelu', 'yla', '1', 'http://bijinkei.net/yla/', 'Virtuaalinen Yleislaatuarvostelu on perustettu vuonna 2002. Idea YLA:sta lähti siitä havainnosta, että \'hyvä hevonen\'-käsite tuntui vääristyneeltä ja sitä haluttiin muuttaa. Yleislaatuarvostelun tarkoitus on nimensä mukaan arvostella virtuaalihevoset yleisellä tasolla. Menestymisen kannalta ei ole merkitystä sillä, onko hevonen hieno kilparatsu vai tavallinen harrastehevonen. Myös ravihevoset ja laukkaratsut ovat tilaisuuksiin tervetulleita. Pääpaino arvostelussa on teksteissä, mutta niiden lisäksi arvostellaan myös rakenne, kilpamenestys, sukutaulu ja jälkeläiset. ', '3');
INSERT INTO `vrlv3`.`vrlv3_puljut` (`nimi`, `lyhenne`, `toiminnassa`, `url`, `kuvaus`, `tyyppi`) VALUES ('Virtuaalinen Ratsuponiyhdistys', 'vrpy', '1', 'http://kimmellys.net/vrpy/', 'Virtuaalinen Ratsuponiyhdistys on perustettu vuonna 2003 tuomaan yhteen ratsuponikasvattajia ja järjestämään toimintaa rodun ympärille. ', '2');
INSERT INTO `vrlv3`.`vrlv3_puljut` (`nimi`, `lyhenne`, `toiminnassa`, `kuvaus`, `tyyppi`) VALUES ('PKK:n kantakirja', 'pkkktk', '0', 'Piirretyt Kuvat Kunniaan - yhdistys ja sen kantakirja oli aikanaan VRL:n alaisuudessa. Myöhemmin se jättäytyi VRL:n toiminnan ulkopuolelle.', '1');
INSERT INTO `vrlv3`.`vrlv3_puljut` (`nimi`, `lyhenne`, `toiminnassa`, `kuvaus`, `tyyppi`) VALUES ('Virtuaalinen esikantakirja', 'esiktk', '0', 'Esikantakirja toimi vuosina 2006-2008 esikarsintana virtuaalisiin kantakirjoihin näyttelyjaoksen jäätyä muutamaksi vuodeksi VRL:n toiminnan ulkopuolelle.', '1');



INSERT INTO `vrlv3`.`vrlv3_puljut_rodut` (`pulju`, `rotu`) VALUES ('6', '29');
INSERT INTO `vrlv3`.`vrlv3_puljut_rodut` (`pulju`, `rotu`) VALUES ('6', '261');




ALTER TABLE `vrlv3`.`vrlv3_tapahtumat` 
ADD COLUMN `pulju_id` INT(11) NULL DEFAULT NULL AFTER `jaos_id`,
ADD INDEX `tapahtumanjarjestajapulju_idx` (`pulju_id` ASC);
;
ALTER TABLE `vrlv3`.`vrlv3_tapahtumat` 
ADD CONSTRAINT `tapahtumanjarjestajapulju`
  FOREIGN KEY (`pulju_id`)
  REFERENCES `vrlv3`.`vrlv3_puljut` (`id`)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

UPDATE vrlv3_tapahtumat SET pulju_id = 1 WHERE jaos = 'hevosktk' and id > 0;
UPDATE vrlv3_tapahtumat SET pulju_id = 2 WHERE jaos = 'poniktk' and id > 0;
UPDATE vrlv3_tapahtumat SET pulju_id = 3 WHERE jaos = 'shktk' and id > 0;
UPDATE vrlv3_tapahtumat SET pulju_id = 4 WHERE jaos = 'arabiktk' and id > 0;
UPDATE vrlv3_tapahtumat SET pulju_id = 5 WHERE jaos = 'yla' and id > 0;
UPDATE vrlv3_tapahtumat SET pulju_id = 7 WHERE jaos = 'pkkktk' and id > 0;
UPDATE vrlv3_tapahtumat SET pulju_id = 8 WHERE jaos = 'esiktk' and id > 0;


