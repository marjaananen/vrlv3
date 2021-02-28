<p>Joukkorekisteröintilomakkeen avulla voit lähettää rekisteröinnit suurelle hevosmäärälle kerralla csv-tiedoston avulla. Joukkorekisteröintiin pätee
aivan samat säännöt kuin tavallisella lomakkeella rekisteröintiin, joten muistathan lukea <a href="<?php echo site_url('virtuaalihevoset/rekisterointi/ohjeet')?>" title="Lue rekisteröintiohjeet">rekisteröintiohjeet</a> huolella. Hevosen omistajaksi merkitään rekisteröintihakemuksen lähettäjä.
Voit lisätä hevoselle lisää omistajia rekisteröinnin jälkeen. Muista, että hevosen sivuilla tulee lukea sana "virtuaalihevonen".</p>

<?php if(!$allowed){ ?>
    <div class="alert alert-danger" role="alert">   
        Massarekisteröinti ei ole käytössäsi! Käyttääksesi massarekisteröintiä sinun tulee olla rekisteröinyt vähintään kymmenen hevosta puolen vuoden sisällä.
    </div>
    
    <?php } ?>



<div class="form-group">
    <label for="csv-content">CSV-sisältö</label>
    <textarea class="form-control" id="csv-content" rows="10">
</textarea>
  </div>

<button type="button" id="submit-csv" class="btn btn-primary">Lähetä</button>


<div class="progress">
  <div id="csv-progress-bar" class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
    <span class="sr-only">40% Complete (success)</span>
  </div>
</div>

<div id="csv-console" class="well"><h4>Konsoli</h4></div>


<script>
  function logMessage(message) {
    const messageLog = $('#csv-console').html();
    $('#csv-console').html(messageLog + "<p>" + message + "</p>");
  }
  
  function setProgressBar(currentValue, maxValue) {
    $("#csv-progress-bar").attr("aria-valuenow", currentValue);
    $("#csv-progress-bar").css("width", ((currentValue/maxValue)*100) + '%');    
  }
  
  // Processes the csv rows recursively one by one
  // rowIndex start from 1 because the first row is the header row
  function processCSVRow(rows, rowIndex, numFails) {
    if (rowIndex >= rows.length) {
      logMessage('<span class="bg-success">' + '<b>Prosessointi valmis!</b></span>');
      return;
    }
    
    const headers = rows[0];
    const values = rows[rowIndex];
    
    console.log("Processing " + rowIndex + "/" + rows.length-1 + ". Headers and values being sent:", headers, values);
    
    $.post(
      "<?php echo site_url('virtuaalihevoset/rekisterointi_csv'); ?>",
      { headers, values },
      function( data ) {
        data = JSON.parse(data);
/*
        Jos error = 0
            kaikki onnistui, ja kentässä 'vh' on hevosen saama rekisterinumero.
            Lisää progress-bariin widthiin sopiva prosenttimäärä ja aria-valuenow +1
            Lisää "konsoliin" rivi jossa kerrotaan mikä rivi (identifiointi tulostamalla rivin alku? rivillä olevan hevosen nimi?) rekisteröitiin ja millä tunnuksella
        Jos error = 1
            rekisteröinti epäonnistui, kentässä error_message on virheen kuvaus.
            Lisää progress-bariin widthiin sopiva prosenttimäärä ja aria-valuenow +1
            Lisää "konsoliin" rivi jossa kerrotaan mikä rivi (identifiointi tulostamalla rivin alku? rivillä olevan hevosen nimi?) feilasi, ja virheilmo.
            Jos virhe oli viides, lopeta homma ja ilmoita "konsolissa" että lähetys keskeytyi.
*/
        setProgressBar(rowIndex, rows.length-1);
        
        var horse_url = '<?php echo site_url('virtuaalihevoset/hevonen'); ?>';

        if (data.error === 0) {
          // Success
          logMessage('<span class="bg-success">' + values.split(',')[0]+ ': Rekisteröinti onnistui. Rekisterinumero <a href=\"' + horse_url +'/'+ data.vh + '\">' + data.vh + '</a></span>');
          processCSVRow(rows, ++rowIndex, numFails);
        } else if (data.error === 1) {
          // Fail
          logMessage('<span class="bg-danger">' +values.split(',')[0]  + ': Rivin käsittely epäonnistui: ' + data.error_message + '</span>');
          
          if (numFails >= 10) {
            logMessage('<span class="bg-danger">' +'Tuli kymmenes virheellinen rivi, prosessointi lopetetaan.</span>');
          } else {
            processCSVRow(rows, ++rowIndex, ++numFails);
          }
        } else {
          logMessage('<span class="bg-danger">' +'Tuntematon paluuarvo palvelimelta. Prosessointi keskeytetty. Ota yhteys ylläpitoon.</span>');
          console.log(data);
        }
      });
  }
  
  function getCSVRows() {
    const rawCSV = $('#csv-content').val();
    const rowStrings = rawCSV.split('\n');
    
    return rowStrings.filter(row => row.length > 1);
  }

  $("#submit-csv").click(function() {
    const rows = getCSVRows();
    
    if (rows.length <= 1) {
      logMessage('CSV on virheellinen!');
      return;
    }
    
    $("#csv-progress-bar").attr("aria-value-max", rows.length-1);
    
    processCSVRow(rows, 1, 0);
  });
  
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
<p>CSV:n tulee sisältää otsikkorivi, jossa on listattu allaolevia kenttiä pilkulla (,) erotettuna ja lainausmerkeillä (") ympäröitynä, sekä
yksi arvorivi per rekisteröitävä hevonen. Rivinvaihto on sallittu vain rivin lopussa.</p>
<p><strong>Esimerkki</strong></p>
<pre>"nimi", "rotu", "sukupuoli", "sakakorkeus", "syntymaaika", "url", "vari"
"Karkurannan Ronan", "28", "2", "123", "16.04.2009", "http://karkuranta.marsupieni.net/poni/karkurannan-ronan", ""
"Karus Bazaar", "28", "2", "126", "11.02.2015", "http://karkuranta.marsupieni.net/poni/karus-bazaar", "11"
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