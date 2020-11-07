<h1>Etuuspistejärjestelmä</h1>

<p>
Jokainen VRL:n jäsen kerää etuuspisteitä järjestäessään kisoja ja lähettäessään näistä tulokset ajoissa.
Etuuspisteet säätelevät sitä, kuinka monta avointa kutsua kalenterissa kyseisellä ihmisellä voi olla yhtä aikaa.
Rajoitukset ovat tehty sen vuoksi että kilpailuista saataisiin tulokset ajallaan, ja että vain tunnollisiksi
osoittautuneet kisanjärjestäjät voivat järjestää useita kisoja kerralla.</p>

<div class="panel panel-default">
      <div class="panel-heading">Tulosten lähettäminen ajoissa ja etuuspisteiden kerryttäminen</div>
    <div class="panel-body">

            <p>
Etuuspisteitä kerrytetään lähettämällä tulokset viimeistään viikon kuluttua kisapäivästä.
Mikäli vastuuhenkilö hoitaa ensimmäiset kilpailunsa ajoissa loppuun, hän saa tuplapisteet, eli siirtyy suoraan seuraavalle etuuspistetasolle.
            </p><p><strong>Näytteluyt tai porrastetut kilpailut eivät kerrytä eivätkä vaadi etuuspisteitä!</strong></p>
      </div>
  </div>

 <div class="panel panel-default">
      <div class="panel-heading">Etuuspisteiden nollaus ja takaajat</div>
      <div class="panel-body">

            <p>Etuuspisteet voidaan nollata johtuen esimerkiksi pitkästä myöhästymisajasta tuloksissa. Mikäli etuuspisteesi on nollattu,
            joudut hankkimaan ensimmäisille kisoillesi nollaamisen jälkeen takaajan, joka sitoutuu pitämään kisat loppuun, mikäli vastuuhenkilö ei sitä tee.
            Takaajalle ilmoitetaan yksityisviestillä, kun hänet on merkitty hyväksyttyihin kilpailuihin takaajaksi. Takaajalta tulee kysyä takauksesta etukäteen.
            </p><p>
            Kisojen takaajalla tulee olla vähintään kolme etuuspistettä, että hän kelpaa takaajaksi.
            </p>
    </div>
 </div>
 



 <div class="panel panel-default">
      <div class="panel-heading">Etuuspisteiden vaikutus tuleviin kisoihin</div>
      <div class="panel-body">
        
        
    <table class="table">
    <thead>
      <tr>
        <th>Etuuspisteet</th>
        <th>Avoimet kutsut (max)</th>
        <th>Suoraan kalenteriin?</th>
      </tr>
    </thead>
    <tbody>

<?php

$pisteet = 0;
$avoimet = 1;
$alkup = 0;



while ($pisteet < 110){
    $avoimet_uusi = $kisajarjestelma->sallitutKisamaarat($pisteet, null);
    if($avoimet_uusi>$avoimet){
        $tulosta = $pisteet-1;
        $cal = '<img src="'.site_url('assets/images/icons/cancel.png').'" alt="Ei"/>';
        if($kisajarjestelma->directlyCalender($tulosta, 0)){
            $cal = '<img src="'.site_url('assets/images/icons/accept.png').'" alt="Kyllä"/>';
        }
        echo "<tr><td>". $alkup . "-" .$tulosta." </td><td> " . $avoimet . "kpl" . "</td><td>".$cal."</td></tr>";
        $alkup = $pisteet;
        $avoimet = $avoimet_uusi;
        
    }
    $pisteet = $pisteet+1;
    
}
        $cal = '<img src="'.site_url('assets/images/icons/accept.png').'" alt="Kyllä"/>';

        echo "<tr><td>100+ </td><td> Rajattomasti </td><td>".$cal."</td></tr>";


?>

</tbody>
    </table>

    </div>
 </div>
  