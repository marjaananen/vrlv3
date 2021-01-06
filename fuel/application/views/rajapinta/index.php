<h1>Rajapinta</h1>

<p>VRL:n rajapinnan kautta voit hakea ja näyttää omilla sivuillasi ajantasaisia tietoja suoraan VRL:n rekistereistä.
Edellytyksenä on php:n tai muun vastaavan ohjelmointikielen käyttömahdollisuus. Rajapinta palauttaa tiedot json-muodossa.</p>

<h2>Rajapinnan käyttö</h2>

<p>Alla on esimerkki rajapinnan käyttöönotosta PHP-koodilla. Allaolevan koodin suorituksen jälkeen json-muotoinen data on $obj nimisessä PHP arrayssa, jota voit käsitellä kuten normaalia PHP Arrayta</p>
<p><samp>$url = 'http://rajapinnan osoite parametreineen';<br>
$obj = json_decode(file_get_contents($url), true);<br>
</p>

<p>Esimerkkihakuja tekemällä näet mitä sisältöjä rajapinnasta tulee. Rajapintoja voidaan muokata jälkikäteen, mutta
muokkaukset eivät muuta olemassaolevia kenttiä tai niiden järjestystä. Kenttiä voi kuitenkin tulla lisää.
Tee siis toteutuksesi siten, että mahdolliset uudet kentät eivät vaikuta siihen! </p>

<p>Mikäli rajapintakutsu onnistuu, paluuarvo on suunnilleen tämän muotoinen:
</p>
<p><samp><?php echo $esimerkit['success'];?></samp></p>
<p>Onnistunut tulos sisältää aina error kentän jonka arvo on 0. Virheviesti sisältää error kentän jossa on arvo 1. Lisäksi viestissä on mukana virhekoodi ja virheen sanallinen kuvaus.
</p>
<p>Mikäli rajapintakutsussa tapahtuu virhe tai tuloksia ei löydy, paluuarvo on tämän näköinen.
</p>
<p><samp><?php echo $esimerkit['error'];?></samp></p>

<h3>Virhekoodit</h3>
<?php
print_error_codes($virhekoodit);

?>

<h2>Rajapinnat</h2>

<?php
print_apis($rajapinnat);
?>


<?php

function print_error_codes($virhekoodit){
    echo "<ul>";
    foreach ($virhekoodit as $koodi=>$kuvaus){
        echo "<li><b>".$koodi.": </b>" . $kuvaus . "</li>";
    }
    echo"</ul>";
}


function print_apis($rajapinnat){
    echo '<div class="panel-group">';
    ksort($rajapinnat);
    foreach ($rajapinnat as $rajapinta=>$sisalto){
        echo '<div class="panel panel-info">';
        echo '<div class="panel-heading"><b>';
        echo strtoupper($rajapinta) . ":</b> ";
        echo site_url() . 'rajapinta/'. $rajapinta . "/";            
        foreach ($sisalto['parametrit'] as $p){
            echo "[".$p . "]/";
        }
        echo '</div>';
        echo '<div class="panel-body">';

        echo "<p>". $sisalto['kuvaus'] . "</p>";
        
        $esimerkkiurl = site_url() . 'rajapinta/'. $rajapinta . '/';
        foreach ($sisalto['esimerkki'] as $p){
            $esimerkkiurl .= $p . "/";
        }
        echo "Esimerkki: ";
        echo '<a href="'.$esimerkkiurl.'">'.$esimerkkiurl.'</a>';
        echo "</div>";
        echo '</div>';
            
        
    }
    
    echo "</div>";
}

?>