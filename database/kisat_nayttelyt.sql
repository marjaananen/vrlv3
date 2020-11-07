

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
  CONSTRAINT `talli_keyiI` FOREIGN KEY (`jarj_talli`) REFERENCES `vrlv3_tallirekisteri` (`tnro`) ON DELETE SET NULL ON UPDATE CASCADE,
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

