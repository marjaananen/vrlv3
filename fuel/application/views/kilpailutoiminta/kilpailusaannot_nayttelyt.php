<h1>Näyttelyiden järjestäminen</h1>
<div class="panel-group">
    
    <div class="panel panel-default">
      <div class="panel-heading">Näyttelyn järjestäminen</div>
      <div class="panel-body">
        <b>Näyttelyn ajankohta</b>
        <ul>
            <li>Näyttelyt tulee ilmoittaa kalenteriin vähintään neljä vuorokautta ennen viimeistä ilmoittautumispäivää (VIP).</li>
            <li>Viimeisen ilmoittautumispäivän ja näyttelypäivän välissä tulee olla vähintään viikko (esim. VIP 01.01.2013 ja NP 07.01.2013).</li>
            <li>Näyttelyt voi järjestää kuluvalle tai enintään seuraavalle kalenterikuukaudelle (esim. elokuussa voi anoa näyttelyn enintään syyskuun viimeiselle päivälle).</li>
            <li>Viimeistä ilmoittautumispäivää ei saa rajata tiettyyn kellonaikaan. Päivän vaihtuminen lasketaan Suomen aikavyöhykkeen mukaan.</li>
        </ul>
        
        <b>Näyttelyiden järjestyspaikka</b>
        <ul>
        <li>Samalla tallilla voi järjestää vain yhdet saman jaoksen näyttelyt samana päivänä riippumatta vastuuhenkilöstä.</li>
        <li>Järjestäjällä voi olla avoinna vain yksi näyttely per jaos, ellei jaoksen säännöissä anneta poikkeusta.</li>
        <li>Järjestyspaikan tulee olla VRL:n rekisterissä.</li>
        </ul>
        
        

        
        <b>Näyttelykutsun pakollinen sisältö</b>
        <ul>
         <li>Näyttelyn järjestyspaikka (rekisteröity talli tai kilpailukeskus)</li>
         <li>Näyttelypäivä sekä viimeinen ilmoittautumispäivä (myös vuosiluku!)</li>
         <li>Vastuuhenkilön nimi, VRL-tunnus sekä sähköpostiosoite</li>
         <li>Osallistumisrajoitukset, luokat ja tuomarit </li>
        <li>Osallistumisohjeet</li>
        <li>Osallistujalista</li>
        </li>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">Osallistumisvaatimukset ja rajaukset</div>
      <div class="panel-body">

        <b>Yleiset rajaukset</b>
        <ul>
            <li>Osallistujia ei saa ottaa ennen kuin näyttelyt ovat hyväksytty kalenteriin</li>
            <li>VRL-tunnus on pakollinen jokaisella osallistujalla.</li>
            <li>Jokainen voi ilmoittaa näyttelyihin vain itsensä!  </li>      
            <li>Yksi hevonen voi osallistua vain kerran yhteen luokkaan</li>
        </ul>
        
        <b>Jaoskohtaiset rajaukset</b>
        
        <?php echo $jaoskohtaiset; ?>
        <p>Jaosten säännöt voivat sallia näistä poikkeuksia.</p>

      </div>
    </div>
    
    <div class="panel panel-default">
      <div class="panel-heading">Jaoskohtaiset säännöt</div>
      <div class="panel-body">
        <p>Muistathan tarkastaa loput jaoskohtaiset säännöt jaosten omilta sivuilta!</p>
        <ul>
        <?php foreach ($jaokset as $jaos) {
            echo '<li><a href="'.$jaos['url'].'">'.$jaos['nimi'] . ' ('.$jaos['lyhenne'].')</a></li>';
        }
        ?>
        </ul>
        </div>
    </div>
        
    <div class="panel panel-default">
      <div class="panel-heading">Tulokset ja tuomarointi</div>
      <div class="panel-body">
        
        <b>Tulosten tuottaminen</b>
        <ul>
            <li>Osallistujien poisjättäminen luokasta, johon he mahtuisivat ja ovat ajoissa (ja oikein) ilmoittautuneet, on vilpillistä toimintaa.</li>
            <li>Näyttelyissä saa käyttää vain jaoksen hyväksymiä tuomareita. Tarkastathan ajantasaisen listan jaoksen sivuilta!</li>
            <li>Kilpailunjärjestäjä saa osallistujia lisätessään hylätä luokasta hevoset, joiden sivut eivät toimi tai joilta puuttuu kuva tms. vaadittu tieto.</li>
            <li>Osallistujat tulee linkittää kutsusivulle ennen tuomaroinnin aloittamista.</li>
            <li>Tuomarointi tulee tehdä täysin puolueettomasti. Tuomarit eivät tuomaroi omia hevosiaan.</li>
            <li>Kilpailun järjestäjä on vastuussa tuomarien valinnasta ja kutsumisesta, ja tulosten pyytämisestä tuomareilta.</li>
        </ul>
       
        <b>Tulosten ilmoittaminen</b>
        <ul>
            <li>Tulokset tulee lähettää BIS-listalle ohjeen mukaan neljän viikon kuluessa kilpailupäivästä. </li>
            <li>Myöhässä olevista tuloksista aiheutuu jaoksen määräämä sanktio!</li>
        </ul>
        <b>Tulosten arkistointi</b>
        <ul>
            <li>Suosittelemme tulossivun säilyttämistä internetissä vähintään kaksi (2) kuukautta kilpailupäivästä. </li>
        </ul>
      </div>
    </div>
    
 </div>