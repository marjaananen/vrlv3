<h1>Porrastetut kilpailut</h1>
<div class="panel-group">
    <div class="panel panel-default">
      <div class="panel-heading">Osallistuminen</div>
      <div class="panel-body">
        <p>Porrastettuihin kilpailuihin saavat osallistua kaikki VRL:n rekisteriin rekisteröidyt virtuaalihevoset.
        Hevosen osallistumisoikeuteen vaikuttaa seuraavat tiedot:</p>
        <ul>
            <li>Hevosen ominaisuuspisteet ja taso</li>
            <li>Hevosen ikä</li>
            <li>Hevosen säkäkorkeus</li>
        </ul>
        <p>Hevonen voi osallistua vain sellaisiin luokkiin, joiden vaatimukset se täyttää. Hevonen, jolla on liikaa tai liian vähän ominaisuuspisteitä (eli jonka taso on liian korkea tai matala), hylätään luokasta.</p>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">Ominaisuudet</div>
      <div class="panel-body">
        <p>Porrastetuissa kilpailuissa kilpailevat hevoset voivat kerätä pisteitä seuraaviin ominaisuuksiin. Voit tarkistaa hevosesi ominaisuuspisteet sen profiilisivulta.  </p>
        <ul>
        <?php
        foreach ($traits as $trait){
            echo "<li>".$trait['ominaisuus']."</li>";
        }?>
      </ul>
        
        <p>Eri lajit vaativat erilaisia ominaisuuksia, ja niissä kilpaileminen myös kehittää eri ominaisuuksia. Eri jaosten kilpailuissa ominaisuudet vaikuttavat seuraavasti.</p>
        <ul>
        <?php
        foreach ($jaokset as $jaos){

            echo "<li><b>".$jaos['jaos']['lyhenne']."</b>: ";
            print_trait_list($jaos['traits']);
            echo "</li>";
        }?>
        </ul>
      </div>
    </div>
    
     <div class="panel panel-default">
      <div class="panel-heading">Tasot</div>
      <div class="panel-body">
        <p>Jokaisella luokalla on taso 0-10, ja osallistuminen kyseiseen luokkaan edellyttää että hevonen on kyseisellä tasolla. Hevosen taso määräytyy sen ominaisuuspisteiden mukaan. 
        Ominaisuuspistemäärä lasketaan yhteensä lajiin vaikuttavista ominaisuuksista. Pisteiden summan tulee olla yli alimmaispisterajan.
        Hyvät pisteet toisessa ominaisuudessa siis korvaavat mahdollisesti toisessa ominaisuudessa puuttuvia pisteitä.</p>
        
        <p>Oman tasonsa lisäksi hevonen voi kilpailla yhtä isommalla tasolla. Poikkeuksena tasot 1 ja 2,
        joilla voi kilpailla myös yhtä alemmalla tasolla nopeamman pisteiden kerryttämisen vuoksi.
        </p>
        
        <table class="table">
        <thead>
          <tr>
            <th scope="col">Taso</th>
            <th scope="col">Minimi-ikä</th>
            <th scope="col">Ominaisuuspisteet</th>
            <th scope="col">Tasonousuun tarvittava pistemäärä</th>
          </tr>
        </thead>
        <tbody>
            <?php
            foreach ($levels as $key=>$level){
                echo "<tr>";
                echo '<th scope="row">'.$key.'</th>';
                echo  "<td>".$level['min_age']." vuotta</td>";
                echo "<td>".$level['point_min'] . " - " . $level['point_max']."</td>";
                echo  "<td>".$level['points_to_level_up']."</td>";
                echo "</tr>";
            }?>
            
        </tbody>
        </table>
      
     </div>
    </div>
     





 <div class="panel panel-default">
      <div class="panel-heading">Pisteiden periytyminen</div>
      <div class="panel-body">
Ominaisuuspisteet periytyvät emältä ja isältä varsalle. Varsa saa pisteitä jokaisesta ominaisuudesta kaavalla (isän pisteet + emän pisteet) / 2 * 0.25, eli 25 % isän ja emän keskiarvosta, ja pisteet lisätään varsan profiiliin heti kun rekisteröinti hyväksytään.
</div>
    </div>




 <div class="panel panel-default">
      <div class="panel-heading">
Ominaisuuspisteiden kertyminen kilpailuista</div>
      <div class="panel-body">
<p>Porrastetut kilpailut arvotaan VRL:n kilpailujärjestelmällä, joka tarkistaa hevosen osallistumisoikeuden ja kerätyt ominaisuuspisteet. 
Jokainen hyväksytty kilpailusuoritus kasvattaa hevosen ominaisuuspisteitä seuraavan kaavan mukaisesti:

<pre>
( 100 / [osallistujien määrä] * ( ( [osallistujien määrä] - [ratsukon sijoitus] + 0,4) / 10) * <br>
(1+ [tason maksimipistemäärä] / [ratsukon sijoitus]) ) - <br>
( 100 / [osallistujien määrä] * ( ( [osallistujien määrä] - [ratsukon sijoitus] + 0,4) / 10) * <br>
(1+ [tason maksimipistemäärä] / [ratsukon sijoitus]) ) / 10</pre>

<p>
Kaavaa ei tarvitse osata laskea itse. Kilpailujärjestelmä huolehtii pisteiden laskusta, ja ne kirjataan hevosen profiiliin, kun porrastetut tulokset on hyväksytty VRL:n tulosarkistoon.
</p><p>
Hevosen kerryttämä pistemäärä riippuu luokan tasosta, hevosen sijoituksesta ja satunnaistekijästä. Kaikki lajiin vaikuttavat ominaisuuspisteet kasvavat jokaisen hyväksytyn suorituksen myötä. Kokonaispistemäärä jaetaan satunnaisesti kaikkien lajissa vaikuttavien ominaisuuksien kesken, eli hevonen voi olla toisessa ominaisuudessaan paljon parempi kuin toisessa. Hylätyt hevoset eivät saa pisteitä.
</p><p>

Hevosen keräämät ominaisuuspisteet löytyvät hevosen profiilisivulta. 
</p>
      </div></div>



</div>
<?php



function print_trait_list($traits){
    $eka = true;
    foreach ($traits as $trait){
        if(!$eka){
            echo ", ";
        }
                echo $trait['ominaisuus'];

        
        $eka = false;
    }
}

?>