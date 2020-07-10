
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


