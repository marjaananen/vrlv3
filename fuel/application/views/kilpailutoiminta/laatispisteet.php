<h1>Porrastettujen laatispisteet</h1>

<p>Porrastettujen kilpailujen pisteytys jaosten laatuarvosteluissa lasketaan tällä laskurilla. </p>

<p>
Koska hevonen pystyy keräämään esim. ERJ kilpailuissa myös KRJ:n laatuarvosteluissa tarvittavia pisteitä, useammassa lajissa
kilpailleelta hevoselta vaaditaan enemmän pisteitä kuin tiettyyn lajiin keskittyneeltä hevoselta.
Hevosen lajikohtaisesti kerryttämistä ominaisuuspisteistä huomioidaan siis tietty %-määrä riippuen siitä paljonko pisteitä se on
kerännyt toisiinsa vaikuttavista lajeista. </p>
<p>
<b>Toisiinsa vaikuttavat lajit</b><br />
<u>ERJ</u>: KRJ, VVJ, KERJ<br />
<u>KRJ</u>: ERJ, VVJ<br />
<u>VVJ</u>: KRJ, ERJ<br />
<u>KERJ</u>: ERJ<br />
</p>
<p>
Vaikuttavaksi lajiksi lasketaan kaikki ne lajit, joissa ominaisuuspisteet ovat vähintään 75% arvosteltavaan lajiin nähden.<br />
0 vaikuttavaa lajia (100%)<br />
1 vaikuttava laji (80%)<br />
2 vaikuttavaa lajia (65%)<br />
3 vaikuttavaa lajia (60%)<br />

</p>


<h2>Tarkasta hevosen laatispisteet porrastetuissa</h2>

<?php echo fuel_var('msg', "")?>

<?php if(isset($pisteet) && $pisteet == true){
    print '<div class="well">';
    
    ?>
    
    
<div class="alert alert-success">
  <strong>Tulokset</strong> <p>


    <?php
            print '<ul><li><b>Kisapisteet:</b> ';
            print $kisapisteet;
           print "p, kaava: (" . $lajin_pisteet . "/" . $lajin_max . ")*" . $laatis_kisamax . "*" . $kerroin . " = ". round($kisapisteet) . "</li>";
           print '<li><b>Pisteet hevosen vanhemmille/jälkeläisille:</b> ';
           print $sukupisteet;
           print "p, kaava: (" . $lajin_pisteet . "/" . $lajin_max . ")*" . $laatis_sukumax . "*" . $kerroin . " = ". round($sukupisteet) . "</li></ul>";
           

?><p>
<small>Mikäli tuomarointi koskee hevosta itseään, poimi ylempi tulos.
Mikäli vanhempaa/jälkeläistä, poimi pisteet alempi.</small></p>
</div>

<strong>Laskussa käytetyt lukuarvot</strong>
    <ul>
   <li>Hevosen pisteet lajissa: <?= $lajin_pisteet;?></li>
  <li>Maksimipisteet koulutustasolle:  <?= $lajin_max;?></li>
   <li>Jaoksen maksimipisteet hevoselle:  <?= $laatis_kisamax;?></li>
    <li>Jaoksen maksimipisteet sukulaiselle:  <?= $laatis_sukumax;?></li>
     <li>Kerroin vaikuttavien lajien perusteella:  <?= $kerroin;?></li>
    </ul>

</div>
    
    <?php
}
?>
<?=$form?>

