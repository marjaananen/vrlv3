
INSERT INTO vrlv3_hevosrekisteri_ominaisuudet ( 
      reknro, 
      ominaisuus, 
      arvo) 
SELECT reknro, 3,
       (hyppykapasiteetti_rohkeus/2)
FROM hevosrekisteri_ominaisuudet
WHERE hyppykapasiteetti_rohkeus > 0.00 AND
EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_ominaisuudet.reknro);


INSERT INTO vrlv3_hevosrekisteri_ominaisuudet ( 
      reknro, 
      ominaisuus, 
      arvo) 
SELECT reknro, 4,
       (hyppykapasiteetti_rohkeus/2)
FROM hevosrekisteri_ominaisuudet
WHERE hyppykapasiteetti_rohkeus > 0.00 AND
EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_ominaisuudet.reknro);

INSERT INTO vrlv3_hevosrekisteri_ominaisuudet ( 
      reknro, 
      ominaisuus, 
      arvo) 
SELECT reknro, 1,
       (nopeus_kestavyys/2)
FROM hevosrekisteri_ominaisuudet
WHERE nopeus_kestavyys > 0.00 AND
EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_ominaisuudet.reknro);

INSERT INTO vrlv3_hevosrekisteri_ominaisuudet ( 
      reknro, 
      ominaisuus, 
      arvo) 
SELECT reknro, 2,
       (nopeus_kestavyys/2)
FROM hevosrekisteri_ominaisuudet
WHERE nopeus_kestavyys > 0.00 AND
EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_ominaisuudet.reknro);

INSERT INTO vrlv3_hevosrekisteri_ominaisuudet ( 
      reknro, 
      ominaisuus, 
      arvo) 
SELECT reknro, 5,
      kuuliaisuus_luonne
FROM hevosrekisteri_ominaisuudet
WHERE kuuliaisuus_luonne > 0.00 AND
EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_ominaisuudet.reknro);

INSERT INTO vrlv3_hevosrekisteri_ominaisuudet ( 
      reknro, 
      ominaisuus, 
      arvo) 
SELECT reknro, 6,
     tahti_irtonaisuus
FROM hevosrekisteri_ominaisuudet
WHERE tahti_irtonaisuus > 0.00 AND
EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_ominaisuudet.reknro);

INSERT INTO vrlv3_hevosrekisteri_ominaisuudet ( 
      reknro, 
      ominaisuus, 
      arvo) 
SELECT reknro, 7,
      tarkkuus_ketteryys
FROM hevosrekisteri_ominaisuudet
WHERE tarkkuus_ketteryys > 0.00 AND
EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_ominaisuudet.reknro);

INSERT INTO vrlv3.vrlv3_hevosrekisteri_ikaantyminen (reknro, 3vuotta, 4vuotta, 5vuotta, 6vuotta, 7vuotta)
Select reknro, 3vuotta, 4vuotta, 5vuotta, 6vuotta, 7vuotta
FROM hevosrekisteri_ikaantyminen WHERE EXISTS
(SELECT * from  vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_ikaantyminen.reknro);

INSERT INTO vrlv3_hevosrekisteri_kisatiedot ( 
      reknro, 
      jaos, 
      taso_max) 
SELECT reknro, 1,
      porr_erj_max
FROM hevosrekisteri_lisatiedot
WHERE EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_lisatiedot.reknro);



INSERT INTO vrlv3_hevosrekisteri_kisatiedot ( 
      reknro, 
      jaos, 
      taso_max) 
SELECT reknro, 2,
      porr_krj_max
FROM hevosrekisteri_lisatiedot
WHERE EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_lisatiedot.reknro);

INSERT INTO vrlv3_hevosrekisteri_kisatiedot ( 
      reknro, 
      jaos, 
      taso_max) 
SELECT reknro, 3,
      porr_kerj_max
FROM hevosrekisteri_lisatiedot
WHERE EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_lisatiedot.reknro);

INSERT INTO vrlv3_hevosrekisteri_kisatiedot ( 
      reknro, 
      jaos, 
      taso_max) 
SELECT reknro, 4,
      porr_vvj_max
FROM hevosrekisteri_lisatiedot
WHERE EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_lisatiedot.reknro);

INSERT INTO vrlv3_hevosrekisteri_kisatiedot ( 
	reknro,
    jaos,
      os, 
      voi, 
      sij,
      porr_os,
      porr_voi,
      porr_sij) 
SELECT reknro, 1,
      erj_os,
      erj_voi,
      erj_sij,
      erj_os_porr,
      erj_voi_porr,
      erj_sij_porr
FROM hevosrekisteri_statistiikka_new
WHERE EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_statistiikka_new.reknro)
AND NOT EXISTS (SELECT reknro from vrlv3_hevosrekisteri_kisatiedot 
where vrlv3_hevosrekisteri_kisatiedot.reknro = hevosrekisteri_statistiikka_new.reknro and vrlv3_hevosrekisteri_kisatiedot.jaos = 1);


INSERT INTO vrlv3_hevosrekisteri_kisatiedot ( 
	reknro,
    jaos,
      os, 
      voi, 
      sij,
      porr_os,
      porr_voi,
      porr_sij) 
SELECT reknro, 2,
      krj_os,
      krj_voi,
      krj_sij,
      krj_os_porr,
      krj_voi_porr,
      krj_sij_porr
FROM hevosrekisteri_statistiikka_new
WHERE EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_statistiikka_new.reknro)
AND NOT EXISTS (SELECT reknro from vrlv3_hevosrekisteri_kisatiedot 
where vrlv3_hevosrekisteri_kisatiedot.reknro = hevosrekisteri_statistiikka_new.reknro and vrlv3_hevosrekisteri_kisatiedot.jaos = 2);


INSERT INTO vrlv3_hevosrekisteri_kisatiedot ( 
	reknro,
    jaos,
      os, 
      voi, 
      sij,
      porr_os,
      porr_voi,
      porr_sij) 
SELECT reknro, 3,
      kerj_os,
      kerj_voi,
      kerj_sij,
      kerj_os_porr,
      kerj_voi_porr,
      kerj_sij_porr
FROM hevosrekisteri_statistiikka_new
WHERE EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_statistiikka_new.reknro)
AND NOT EXISTS (SELECT reknro from vrlv3_hevosrekisteri_kisatiedot 
where vrlv3_hevosrekisteri_kisatiedot.reknro = hevosrekisteri_statistiikka_new.reknro and vrlv3_hevosrekisteri_kisatiedot.jaos = 3);


INSERT INTO vrlv3_hevosrekisteri_kisatiedot ( 
	reknro,
    jaos,
      os, 
      voi, 
      sij,
      porr_os,
      porr_voi,
      porr_sij) 
SELECT reknro, 4,
      vvj_os,
      vvj_voi,
      vvj_sij,
      vvj_os_porr,
      vvj_voi_porr,
      vvj_sij_porr
FROM hevosrekisteri_statistiikka_new
WHERE EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_statistiikka_new.reknro)
AND NOT EXISTS (SELECT reknro from vrlv3_hevosrekisteri_kisatiedot 
where vrlv3_hevosrekisteri_kisatiedot.reknro = hevosrekisteri_statistiikka_new.reknro and vrlv3_hevosrekisteri_kisatiedot.jaos = 4);

INSERT INTO vrlv3_hevosrekisteri_kisatiedot ( 
	reknro,
    jaos,
      os, 
      voi, 
      sij) 
SELECT reknro, 5,
      wrj_os,
      wrj_voi,
      wrj_sij
FROM hevosrekisteri_statistiikka_new
WHERE EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_statistiikka_new.reknro)
AND NOT EXISTS (SELECT reknro from vrlv3_hevosrekisteri_kisatiedot 
where vrlv3_hevosrekisteri_kisatiedot.reknro = hevosrekisteri_statistiikka_new.reknro and vrlv3_hevosrekisteri_kisatiedot.jaos = 5);

INSERT INTO vrlv3_hevosrekisteri_kisatiedot ( 
	reknro,
    jaos,
      os, 
      voi, 
      sij) 
SELECT reknro, 6,
      arj_os,
      arj_voi,
      arj_sij
FROM hevosrekisteri_statistiikka_new
WHERE EXISTS (SELECT reknro from vrlv3_hevosrekisteri where vrlv3_hevosrekisteri.reknro = hevosrekisteri_statistiikka_new.reknro)
AND NOT EXISTS (SELECT reknro from vrlv3_hevosrekisteri_kisatiedot 
where vrlv3_hevosrekisteri_kisatiedot.reknro = hevosrekisteri_statistiikka_new.reknro and vrlv3_hevosrekisteri_kisatiedot.jaos = 6);


UPDATE vrlv3_hevosrekisteri_kisatiedot as k
INNER JOIN hevosrekisteri_statistiikka_new as s on k.reknro = s.reknro  
SET k.os = s.erj_os,
k.voi = s.erj_voi,
k.sij = s.erj_sij,
k.porr_os = s.erj_os_porr,
k.porr_voi = s.erj_voi_porr,
k.porr_sij = s.erj_sij_porr 
WHERE k.reknro = s.reknro AND k.jaos = 1;

UPDATE vrlv3_hevosrekisteri_kisatiedot as k
INNER JOIN hevosrekisteri_statistiikka_new as s on k.reknro = s.reknro  
SET k.os = s.krj_os,
k.voi = s.krj_voi,
k.sij = s.krj_sij,
k.porr_os = s.krj_os_porr,
k.porr_voi = s.krj_voi_porr,
k.porr_sij = s.krj_sij_porr 
WHERE k.reknro = s.reknro AND k.jaos = 2;

UPDATE vrlv3_hevosrekisteri_kisatiedot as k
INNER JOIN hevosrekisteri_statistiikka_new as s on k.reknro = s.reknro  
SET k.os = s.kerj_os,
k.voi = s.kerj_voi,
k.sij = s.kerj_sij,
k.porr_os = s.kerj_os_porr,
k.porr_voi = s.kerj_voi_porr,
k.porr_sij = s.kerj_sij_porr 
WHERE k.reknro = s.reknro AND k.jaos = 3;

UPDATE vrlv3_hevosrekisteri_kisatiedot as k
INNER JOIN hevosrekisteri_statistiikka_new as s on k.reknro = s.reknro  
SET k.os = s.vvj_os,
k.voi = s.vvj_voi,
k.sij = s.vvj_sij,
k.porr_os = s.vvj_os_porr,
k.porr_voi = s.vvj_voi_porr,
k.porr_sij = s.vvj_sij_porr 
WHERE k.reknro = s.reknro AND k.jaos = 4;

UPDATE vrlv3_hevosrekisteri_kisatiedot as k
INNER JOIN hevosrekisteri_statistiikka_new as s on k.reknro = s.reknro  
SET k.os = s.wrj_os,
k.voi = s.wrj_voi,
k.sij = s.wrj_sij 
WHERE k.reknro = s.reknro AND k.jaos = 5;

UPDATE vrlv3_hevosrekisteri_kisatiedot as k
INNER JOIN hevosrekisteri_statistiikka_new as s on k.reknro = s.reknro  
SET k.os = s.arj_os,
k.voi = s.arj_voi,
k.sij = s.arj_sij 
WHERE k.reknro = s.reknro AND k.jaos = 6;






