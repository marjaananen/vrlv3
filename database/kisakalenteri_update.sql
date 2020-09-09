ALTER TABLE `vrlv3`.`kisat_kisakalenteri` 
CHANGE COLUMN `laji` `laji` SMALLINT(2) NOT NULL ,
CHANGE COLUMN `jaos` `jaos` INT(11) NOT NULL ,
CHANGE COLUMN `tulokset` `tulokset` INT(1) NOT NULL DEFAULT '0' ;

UPDATE vrlv3.kisat_kisakalenteri SET laji = 1 where jaos = 1; -- vanha erj = 1
UPDATE vrlv3.kisat_kisakalenteri SET laji = 2 where jaos = 3; -- vanha krj = 3
UPDATE vrlv3.kisat_kisakalenteri SET laji = 3 where jaos = 5; -- vanha vvj = 5
UPDATE vrlv3.kisat_kisakalenteri SET laji = 6 where jaos = 2; -- vanha kerj = 2
UPDATE vrlv3.kisat_kisakalenteri SET laji = 5 where jaos = 6; -- vanha wrj = 6
UPDATE vrlv3.kisat_kisakalenteri SET laji = 7 where jaos = 4; -- vanha arj = 4

ALTER TABLE `vrlv3`.`kisat_kisakalenteri` 
ADD INDEX `laji` (`laji` ASC);
;


UPDATE vrlv3.kisat_kisakalenteri SET jaos = 1 where laji = 1; -- uusi erj = 1
UPDATE vrlv3.kisat_kisakalenteri SET jaos = 2 where laji = 2; -- uusi krj = 2
UPDATE vrlv3.kisat_kisakalenteri SET jaos = 3 where laji = 3; -- uusi kerj = 3
UPDATE vrlv3.kisat_kisakalenteri SET jaos = 3 where laji = 6; -- uusi vvj = 4
UPDATE vrlv3.kisat_kisakalenteri SET jaos = 5 where laji = 5; -- uusi wrj = 5
UPDATE vrlv3.kisat_kisakalenteri SET jaos = 6 where laji = 7; -- uusi arj = 6

ALTER TABLE `vrlv3`.`kisat_kisakalenteri` 
ADD CONSTRAINT `jaos_key`
  FOREIGN KEY (`jaos`)
  REFERENCES `vrlv3`.`vrlv3_kisat_jaokset` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `laji_key`
  FOREIGN KEY (`laji`)
  REFERENCES `vrlv3`.`vrlv3_lista_painotus` (`pid`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
  ALTER TABLE `vrlv3`.`kisat_kisakalenteri` 
ADD INDEX `tunnus_key_idx` (`tunnus` ASC);
;
ALTER TABLE `vrlv3`.`kisat_kisakalenteri` 
ADD CONSTRAINT `tunnus_key`
  FOREIGN KEY (`tunnus`)
  REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
  DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '4323');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '79219');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '86409');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '86410');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '86411');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '86412');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '86413');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '86414');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '86415');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '86416');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '86417');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '86418');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '110612');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '110620');
DELETE FROM `vrlv3`.`kisat_kisakalenteri` WHERE (`kisa_id` = '131371');

CREATE TABLE `vrlv3_kisat_kisakalenteri` (
  `kisa_id` int(11) NOT NULL,
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
  `tulokset` int(1) NOT NULL DEFAULT '0',
  `hyvaksyi` int(5) unsigned zerofill DEFAULT NULL,
  `seura_hyv` int(1) DEFAULT NULL,
  `siirretty` int(5) unsigned zerofill DEFAULT NULL,
  `vanha` int(1) NOT NULL DEFAULT '0',
  `porrastettu` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`kisa_id`),
  KEY `jaoss` (`jaos`),
  KEY `lajii` (`laji`),
  KEY `talli_key_idxi` (`jarj_talli`),
  KEY `tunnus_key_idxi` (`tunnus`),
  CONSTRAINT `jaos_keyi` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `laji_keyi` FOREIGN KEY (`laji`) REFERENCES `vrlv3_lista_painotus` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `tunnus_keyi` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `talli_keyi` FOREIGN KEY (`jarj_talli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE NO ACTION ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT CHARSET=utf8;




insert into vrlv3_kisat_kisakalenteri (kisa_id, vip, laji, jaos, url, info, tunnus, jarj_talli, 
jarj_seura, arvontatapa, takaaja, ilmoitettu, seuralle, hyvaksytty, kasitelty, tulokset, hyvaksyi, seura_hyv, siirretty, vanha, porrastettu)
select kisa_id, vip, laji, jaos, url, info, tunnus, jarj_talli, 
jarj_seura, arvontatapa, takaaja, ilmoitettu, seuralle, hyvaksytty, kasitelty, tulokset, hyvaksyi, seura_hyv, siirretty, vanha, porrastettu from kisat_kisakalenteri;

  ALTER TABLE `vrlv3`.`vrlv3_kisat_kisakalenteri` 
CHANGE COLUMN `kisa_id` `kisa_id` INT(11) NOT NULL AUTO_INCREMENT ;



ALTER TABLE `vrlv3`.`kisat_tulokset` 
DROP FOREIGN KEY `kisat_tulokset_ibfk_1`;
ALTER TABLE `vrlv3`.`kisat_tulokset`
ADD COLUMN `kasittelija` INT(5) ZEROFILL UNSIGNED NULL DEFAULT NULL AFTER `kasitelty`,
ADD INDEX `kisat_tulokset_ibfk_1_idx` (`kisa_id` ASC),
ADD INDEX `tunnus_idx_tulos` (`tunnus` ASC),
ADD INDEX `hyvaksyi_tulokset_idx` (`hyvaksyi` ASC),
ADD INDEX `kasittelija_idx` (`kasittelija` ASC);
;
ALTER TABLE `vrlv3`.`kisat_tulokset` 
RENAME TO  `vrlv3`.`vrlv3_kisat_tulokset`,
ADD CONSTRAINT `kisat_tulokset_ibfk_1`
  FOREIGN KEY (`kisa_id`)
  REFERENCES `vrlv3`.`vrlv3_kisat_kisakalenteri` (`kisa_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
ADD CONSTRAINT `tunnus_tulokset`
  FOREIGN KEY (`tunnus`)
  REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `hyvaksyi_tulokset`
  FOREIGN KEY (`hyvaksyi`)
  REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `kasittelija_tunnus`
  FOREIGN KEY (`tunnus`)
  REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
  ON DELETE SET NULL
  ON UPDATE CASCADE;
  



ALTER TABLE `vrlv3`.`vrlv3_kisat_kisakalenteri` 
ADD COLUMN `kasittelija` INT(5) ZEROFILL UNSIGNED NULL DEFAULT NULL AFTER `kasitelty`,
ADD INDEX `kasittelija_idx` (`kasittelija` ASC);
;
ALTER TABLE `vrlv3`.`vrlv3_kisat_kisakalenteri` 
ADD CONSTRAINT `kasittelija_tunnus`
  FOREIGN KEY (`tunnus`)
  REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
  ON DELETE SET NULL
  ON UPDATE CASCADE;

  
  
  insert into vrlv3_kisat_etuuspisteet (tunnus, jaos, pisteet, nollattu, muokattu) SELECT tunnus, 1, ERJ, nollattu, muokattu FROM vrlv3.tunnukset_etuuspisteet;
insert into vrlv3_kisat_etuuspisteet (tunnus, jaos, pisteet, nollattu, muokattu) SELECT tunnus, 2, KRJ, nollattu, muokattu FROM vrlv3.tunnukset_etuuspisteet;
insert into vrlv3_kisat_etuuspisteet (tunnus, jaos, pisteet, nollattu, muokattu) SELECT tunnus, 3, KERJ, nollattu, muokattu FROM vrlv3.tunnukset_etuuspisteet;
insert into vrlv3_kisat_etuuspisteet (tunnus, jaos, pisteet, nollattu, muokattu) SELECT tunnus, 4, VVJ, nollattu, muokattu FROM vrlv3.tunnukset_etuuspisteet;
insert into vrlv3_kisat_etuuspisteet (tunnus, jaos, pisteet, nollattu, muokattu) SELECT tunnus, 5, WRJ, nollattu, muokattu FROM vrlv3.tunnukset_etuuspisteet;
insert into vrlv3_kisat_etuuspisteet (tunnus, jaos, pisteet, nollattu, muokattu) SELECT tunnus, 6, ARJ, nollattu, muokattu FROM vrlv3.tunnukset_etuuspisteet;