<p>Joukkorekisteröintilomakkeen avulla voit lähettää rekisteröinnit suurelle hevosmäärälle kerralla csv tiedoston avulla. Joukkorekisteröintiin pätee
aivan samat säännöt, kuin tavallisella lomakkeella rekisteröintiin, joten muistathan lukea rekisteröintiohjeet. Hevosen omistajaksi merkitään rekisteröintihakemuksen lähettäjä.
Voit lisätä hevoselle lisää omistajia rekisteröinnin jälkeen. Muista, että hevosen sivuilla tulee lukea sana "virtuaalihevonen".</p>

<?php if(!$allowed){ ?>
    <div class="alert alert-danger" role="alert">   
        Massarekisteröinti ei ole käytössäsi! Käyttääksesi massarekisteröintiä, sinun tulee olla rekisteröinyt vähintään kymmenen hevosta puolen vuoden sisällä.
    </div>
    
    <?php } ?>



<div class="form-group">
    <label for="exampleFormControlTextarea1">CSV-sisältö</label>
    <textarea class="form-control" id="exampleFormControlTextarea1" rows="10">
"nimi", "rotu", "sukupuoli", "saka", "syntymaaika", "url", "vari", "painotus", "syntymamaa", "3vuotta", "4vuotta", "5vuotta", "6vuotta", "7vuotta", "8vuotta", "i_nro", "e_nro", "kasvattajanimi", "kasvattaja_tunnus", "kuollut", "kotitalli"
"Karkurannan Ronan", "28", "ori", "0", "16.04.2009", "http://karkuranta.marsupieni.net/poni/karkurannan-ronan", "", "", "", "24.12.2009", "18.03.2010", "10.06.2010", "02.09.2010", "25.11.2010", "17.02.2011", "VH05-028-3841", "VH03-028-3739", "Karkurannan", "KARK4835", "", "KARK4835"
"Karkurannan Tuisku", "28", "tamma", "0", "01.05.2009", "http://karkuranta.marsupieni.net/poni/karkurannan-tuisku", "", "", "", "08.01.2010", "02.04.2010", "25.06.2010", "17.09.2010", "10.12.2010", "04.03.2011", "VH05-028-8544", "VH03-028-3740", "Karkurannan", "KARK4835", "", "KARK4835"
"Karus Bazaar", "28", "ori", "126", "11.02.2015", "http://karkuranta.marsupieni.net/poni/karus-bazaar", "", "", "", "17.06.2015", "29.07.2015", "09.09.2015", "21.10.2015", "02.12.2015", "13.01.2016", "VH14-028-0065", "VH15-028-0149", "Karus", "Karkurannan", "", "KARK4835"
"Karus Gullringduva", "28", "tamma", "124", "22.03.2015", "http://karkuranta.marsupieni.net/poni/karus-gullringduva", "", "", "", "26.07.2015", "06.09.2015", "18.10.2015", "29.11.2015", "10.01.2016", "21.02.2016", "VH14-028-0307", "VH15-028-0156", "Karus", "KARK4835", "", "KARK4835"
"Karkurannan Aredhel", "28", "tamma", "124", "01.05.2015", "http://karkuranta.marsupieni.net/poni/karkurannan-aredhel", "", "", "", "04.09.2015", "16.10.2015", "27.11.2015", "08.01.2016", "19.02.2016", "01.04.2016", "VH14-028-0330", "VH14-028-0238", "Karkurannan", "KARK4835", "", "KARK4835"
</textarea>
  </div>

<button type="button" class="btn btn-primary">Lähetä</button>


<div class="progress">
  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 1%">
    <span class="sr-only">40% Complete (success)</span>
  </div>
</div>

<div class="well">Konsoli</div>


<script>
    /*
    Button *klik*
    Laske rivit, aseta progress-barin aria-valuemax = rivimäärä ja progress-bar näkyviin
    
    Jokaiselle riville (paitsi ekalle, joka on otsikkorivi, huom, jos lähetetään vain yksi rivi, se on virhe!)
    
        Lähetä site_url('hevosrekisteri/rekisterointi_csv'); postilla seuraavaa;
        headers = eka rivi
        values = käsiteltävä rivi
        
        Paluuarvo json.
        Jos error = 0
            kaikki onnistui, ja kentässä 'vh' on hevosen saama rekisterinumero.
            Lisää progress-bariin widthiin sopiva prosenttimäärä ja aria-valuenow +1
            Lisää "konsoliin" rivi jossa kerrotaan mikä rivi (identifiointi tulostamalla rivin alku? rivillä olevan hevosen nimi?) rekisteröitiin ja millä tunnuksella
        Jos error = 1
            rekisteröinti epäonnistui, kentässä error_message on virheen kuvaus.
            Lisää progress-bariin widthiin sopiva prosenttimäärä ja aria-valuenow +1
            Lisää "konsoliin" rivi jossa kerrotaan mikä rivi (identifiointi tulostamalla rivin alku? rivillä olevan hevosen nimi?) feilasi, ja virheilmo.
            Jos virhe oli viides, lopeta homma ja ilmoita "konsolissa" että lähetys keskeytyi.
        
        Jos rivit loppui, ilmoita että valmis.
        
    
    */
    
    
    
</script>

<h2>CSV:n muodostusohje</h2>
<p>CSV:n tulee sisältää otsikkorivin, jossa on listattu allaolevia kenttiä pilkulla(,) erotettuna ja heittomerkeillä ympäröitynä, ja
yhden arvorivin per rekisteröitävä hevonen. Rivinvaihto on sallittu vain rivin lopussa.</p>
<p><strong>Esimerkki</strong></p>
<pre>"nimi", "rotu", "sukupuoli", "saka", "syntymaaika", "url", "vari"
"Karkurannan Ronan", "28", "ori", "123", "16.04.2009", "http://karkuranta.marsupieni.net/poni/karkurannan-ronan", ""
"Karus Bazaar", "28", "ori", "126", "11.02.2015", "http://karkuranta.marsupieni.net/poni/karus-bazaar", "11"
</pre>

<p>Alla on listattu kaikki sallitut kentät ja niiden täyttöohjeet.</p>
<p>* merkityt kentät ovat pakollisia.</p>
<table class="table table-striped">
    
    <?php foreach($kentat as $nimi => $kentta) {?>
        <tr>
            <th scope="row"><?php echo $nimi;?><?php if(isset($kentta['required']) && $kentta['required'] == true){ echo "*";}?></th>
            <td> <?php echo $kentta['kuvaus'];?></td>
        </tr>
  <?php } ?>
</table>