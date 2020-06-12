<h1>Ylläpito & yhteydenotto</h1>

<div class="alert alert-danger" role="alert">Tän sivuston sisältö on super muinainen. Kantakirjat tuskin tulee VRL:n alle.</div>
<p>Virtuaalisen ratsastajainliiton johdossa on yhden tai kaksi henkilöä käsittävä ylläpito, jonka alaisuudessa toimii kourallinen osa-alueiden vastaavia. Kaikilla osa-alueiden vastaavilla on alaisuudessaan vaihteleva määrä työntekijöitä. Koko liittoa pyöritetään täysin vapaaehtoistoimin.</p>

<p>Otathan aina ensisijaisesti yhteyttä tuen kautta <a href="http://virtuaalihevoset.net/tuki">http://virtuaalihevoset.net/tuki</a>, josta eri osa-alueiden työntekijät pääsevät käsittelemään kysymyksesi. Tuessa on useita eri kategorioita, joista voit valita sopivimman. Useamman asian ollessa kyseessä tee niistä erilliset ilmoitukset. Mikäli asiasi ei koske varsinaisesti tukiasioita, ota yhteyttä vastuuhenkilöihin, joiden vastuualueet ja sähköpostiosoitteet löydät alta.</p>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Ylläpito</h3>
  </div>
  <div class="panel-body">
    <p>Ylläpito vastaa VRL:n palvelimesta rahallisesti, ja tekee tekee päätökset uusien ominaisuuksien ohjelmoimisesta. Ylläpitoon otetaan myös yhteys esimerkiksi sponsori- ja mainosasioissa. Ylläpito myös vastaa suurista sääntömuutoksista, ja siitä, että osa-alueiden vastaavat hoitavat tehtävänsä. </p>
    <p>Ylläpitäjinä toimivat: <br>
      <?php print_users($users['admin']);?>
    
    </p>
  </div>
   <div class="panel-footer"><s><a href="mailto:yllapito(a)virtuaalihevoset.net">yllapito(a)virtuaalihevoset.net</a></s></div>
</div>


<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Tunnusvastaava</h3>
  </div>
  <div class="panel-body">
    <p>Tunnusvastaava vastaa VRL-tunnusten hyväksynnästä, tuplatunnusten käsittelystä ja väärinkäyttötapauksissa tunnusten jäähylle asettamisesta sekä jäädyttämisestä. </p>
    <p>Tunnusvastaavana toimii: <br>
        <?php print_users($users['tunnukset']);?>
    
    
    </p>
  </div>
  <div class="panel-footer"><a href="mailto:tunnukset(a)virtuaalihevoset.net">tunnukset(a)virtuaalihevoset.net</a> </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Työvoimavastaava</h3>
  </div>
  <div class="panel-body">
    <p>Työvoimavastaavan tehtäviin kuuluu uusien työntekijöiden rekrytointi ja valinta, osa-alueiden vastaavien rekrytointi ja valinta yhdessä ylläpidon kanssa, sekä rekistereiden ja rekisterityöntekijöiden valvonta kilpailujaoksia lukuunottamatta. Mikäli haluat töihin VRL:oon, ota yhteyttä!</p>
    <p>Työvoimasvastaavana toimii<br>
    <?php print_users($users['tyovoima']);?>
    
    </p>
  </div>
  <div class="panel-footer"><s><a href="mailto:tyovoima(a)virtuaalihevoset.net">tyovoima(a)virtuaalihevoset.net</a></s> </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Jaosvastaava</h3>
  </div>
  <div class="panel-body">
    <p>Jaosvastaava valvoo, että kaikki jaokset toimivat, ja noudattavat yhteisiä sääntöjä. Hän toimii yhteyshenkilönä jaosten ja VRL:n ylläpidon välillä, rekrytoi tarpeen tullen uusia apukäsiä jaosten tehtäviin, vie läpi sääntömuutokset, ja vastaa jaosten ylläpitäjien kysymyksiin. </p>
    <p>Jaosvastaavana toimii <br>
    <?php print_users($users['jaos']);?>
    </p>
  </div>
  <div class="panel-footer"><s><a href="mailto:jaokset(a)virtuaalihevoset.net">jaokset(a)virtuaalihevoset.net</a></s> </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Rekisterityöntekijät</h3>
  </div>
  <div class="panel-body">
    <p>Hevos-, talli- ja kasvattajanimirekistereissä työskentelee vapaaehtoisia, jotka huolehtivat rekisterien sujuvasta toiminnasta ja virheiden korjaamisesta, sekä ylläpidon luomien sääntöjen noudattamisesta.</p>
   <table width="100%">
    
    <tr>
      <td><b>Hevosrekisteri</b></td><td><strong>Tallirekisteri</strong></td><td><strong>Kasvattajanimet</strong></td>
    </tr>
    <tr>
      <td valign="top"><?php print_users($users['hevosrekisteri']);?></td>
      <td valign="top"><?php print_users($users['tallirekisteri']);?></td>
      <td valign="top"><?php print_users($users['kasvattajanimet']);?></td>
    </tr>
   
   </table>
  </div>
  <div class="panel-footer">Ota yhteyttä rekisterityöntekijöihin tuen kautta.</div>
</div>



<!---älä koske -->
<?php
function print_users($array){

    if(isset($array) && sizeof($array) > 0) {
      echo '<ul>';
      foreach  ($array as $user){
      echo '<li><a href="' . site_url('tunnus/'.$user['tunnus']) . '">VRL-' . $user['tunnus'] . '</a> ' . $user['nimimerkki'] . '</li>';
      }
    } else {
      echo '<li>Tällä hetkellä ei kukaan.</li>';
    }
    echo '</ul>';

}

?>
