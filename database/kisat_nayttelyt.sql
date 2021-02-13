

CREATE TABLE `vrlv3_kisat_nayttelykalenteri` (
  `kisa_id` int(11) NOT NULL AUTO_INCREMENT,
  `vip` datetime NOT NULL,
  `kp` datetime NOT NULL,
  `laji` smallint(2) NOT NULL,
  `jaos` int(11) NOT NULL,
  `url` tinytext  NOT NULL,
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
  CONSTRAINT `jaos_keyiI` FOREIGN KEY (`jaos`) REFERENCES `vrlv3_kisat_jaokset` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `kasittelija_keyI` FOREIGN KEY (`kasittelija`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `laji_keyiI` FOREIGN KEY (`laji`) REFERENCES `vrlv3_lista_painotus` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `talli_keyiI` FOREIGN KEY (`jarj_talli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `tunnus_keyiI` FOREIGN KEY (`tunnus`) REFERENCES `vrlv3_tunnukset` (`tunnus`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


insert into vrlv3_kisat_nayttelykalenteri (kisa_id, kp, vip, laji, jaos, url, info, tunnus, jarj_talli, 
jarj_seura, arvontatapa, takaaja, ilmoitettu, seuralle, hyvaksytty, kasitelty, tulokset, hyvaksyi, seura_hyv, siirretty, vanha)
select kisa_id, kp, vip, 11, 7, url, info, tunnus, jarj_talli, 
jarj_seura, arvontatapa, takaaja, ilmoitettu, seuralle, hyvaksytty, kasitelty, tulokset, hyvaksyi, seura_hyv, siirretty, 1 from nj_kisakalenteri WHERE EXISTS (select tnro from vrlv3_tallirekisteri where tnro = jarj_talli);


insert into vrlv3_kisat_nayttelykalenteri (kisa_id, kp, vip, laji, jaos, url, info, tunnus, jarj_talli, 
jarj_seura, arvontatapa, takaaja, ilmoitettu, seuralle, hyvaksytty, kasitelty, tulokset, hyvaksyi, seura_hyv, siirretty, vanha)
select kisa_id, kp, vip, 11, 7, url, info, tunnus, NULL, 
jarj_seura, arvontatapa, takaaja, ilmoitettu, seuralle, hyvaksytty, kasitelty, tulokset, hyvaksyi, seura_hyv, siirretty, 1 from nj_kisakalenteri WHERE NOT EXISTS (select tnro from vrlv3_tallirekisteri where tnro = jarj_talli);


ALTER TABLE `vrlv3`.`bis_general` 
ENGINE = InnoDB , RENAME TO  `vrlv3`.`vrlv3_kisat_nayttelytulokset` ;

ALTER TABLE `vrlv3`.`vrlv3_kisat_nayttelytulokset` 
ADD COLUMN `ilmoitettu` DATETIME NULL AFTER `hyvaksytty`,
ADD COLUMN `kasitelty` DATETIME NULL DEFAULT NULL AFTER `ilmoitettu`,
ADD COLUMN `kasittelija` INT(5) UNSIGNED ZEROFILL NULL DEFAULT NULL AFTER `kasitelty`,
ADD COLUMN `tulokset` TEXT NULL DEFAULT NULL AFTER `kasittelija`,
CHANGE COLUMN `ilmoittaja` `tunnus` INT(5) UNSIGNED ZEROFILL NOT NULL ,
CHANGE COLUMN `hyvaksyja` `hyvaksyi` INT(5) UNSIGNED ZEROFILL NULL DEFAULT NULL ,
ADD INDEX `nayttelyt_hyvaksyi_id` (`hyvaksyi` ASC),
ADD INDEX `nayttelyt_kasittelija_id` (`kasittelija` ASC),
ADD INDEX `nayttely_jarjestaja` (`tunnus` ASC);

ALTER TABLE `vrlv3`.`vrlv3_kisat_nayttelytulokset` 
ADD CONSTRAINT `ilmoittaja_idx`
  FOREIGN KEY (`tunnus`)
  REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
  ON DELETE NO ACTION
  ON UPDATE CASCADE,
ADD CONSTRAINT `hyvaksyja_idcx`
  FOREIGN KEY (`hyvaksyi`)
  REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
  ON DELETE SET NULL
  ON UPDATE CASCADE,
ADD CONSTRAINT `kasittelija_idx`
  FOREIGN KEY (`kasittelija`)
  REFERENCES `vrlv3`.`vrlv3_tunnukset` (`tunnus`)
  ON DELETE SET NULL
  ON UPDATE CASCADE;


ALTER TABLE `vrlv3`.`bis_tulosrivit` 
ENGINE = InnoDB ,
ADD COLUMN `vh_id` INT(9) UNSIGNED ZEROFILL NULL DEFAULT NULL AFTER `vh_nimi`,
ADD INDEX `vh_id` (`vh_id` ASC);
ALTER TABLE `vrlv3`.`bis_tulosrivit` 
RENAME TO  `vrlv3`.`vrlv3_kisat_bis_tulosrivit` ,
ADD CONSTRAINT `vh_idxx`
  FOREIGN KEY (`vh_id`)
  REFERENCES `vrlv3`.`vrlv3_hevosrekisteri` (`reknro`)
  ON DELETE SET NULL
  ON UPDATE CASCADE;
  
  DELETE
FROM `vrlv3`.`vrlv3_kisat_nayttelytulokset` 
WHERE NOT EXISTS (SELECT kisa_id from vrlv3_kisat_nayttelykalenteri 
where vrlv3_kisat_nayttelykalenteri.kisa_id = vrlv3_kisat_nayttelytulokset.nayttely_id) and bis_id < 12;

ALTER TABLE `vrlv3`.`vrlv3_kisat_nayttelytulokset` 
ADD CONSTRAINT `nayttely_idx`
  FOREIGN KEY (`nayttely_id`)
  REFERENCES `vrlv3`.`vrlv3_kisat_nayttelykalenteri` (`kisa_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
  
  ALTER TABLE `vrlv3`.`vrlv3_kisat_nayttelytulokset` 
CHANGE COLUMN `hyvaksytty` `hyvaksytty` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';


DELETE 
FROM `vrlv3`.`vrlv3_kisat_bis_tulosrivit` 
WHERE NOT EXISTS (SELECT bis_id from vrlv3_kisat_nayttelytulokset
where vrlv3_kisat_bis_tulosrivit.bis_id = vrlv3_kisat_nayttelytulokset.bis_id) and bis_id < 1717;


ALTER TABLE `vrlv3`.`vrlv3_kisat_bis_tulosrivit` 
ADD CONSTRAINT `bis_idxx`
  FOREIGN KEY (`bis_id`)
  REFERENCES `vrlv3`.`vrlv3_kisat_nayttelytulokset` (`bis_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
  
  
  UPDATE vrlv3_kisat_bis_tulosrivit AS t1, vrlv3_kisat_bis_tulosrivit as t2
SET t1.vh_id = t2.vh 
WHERE t1.vh = t2.vh AND t1.tulosrivi_id < 60000 AND t1.vh_id IS NULL AND t1.vh != '000000000'
AND EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = t2.vh);

UPDATE vrlv3_kisat_bis_tulosrivit AS t1, vrlv3_kisat_bis_tulosrivit as t2
SET t1.vh_id = t2.vh 
WHERE t1.vh = t2.vh AND t1.tulosrivi_id > 4000 AND t1.vh_id IS NULL AND t1.vh != '000000000'
AND EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = t2.vh);