<h1>Virtuaalinen ratsastajainliitto</h1>

<p>
Virtuaalinen ratsastajainliitto eli VRL on virtuaalihevosten ja -tallien harrastajien keskusjärjestö ja kohtaamispaikka.
Liitto on toiminut vuodesta 1999 tavoitteenaan yhtenäistää ja kehittää virtuaalimaailmaa, opastaa niin uusia kuin vanhojakin harrastajia ja
tiedottaa ajankohtaisista asioista. VRL:n tärkeimpiin tavoitteisiin kuuluu myös virtuaalihevosmaailman perinteiden vaaliminen ja historiatiedon
säilöminen, onhan VRL ollut mukana harrastuksen vaiheissa jo kolmatta vuosikymmentä!
</p>

<p>
Toimintaan on helppo tulla mukaan. Ensikertalaisen kannattaa aloittaa Mikä on virtuaalimaailma? Infosivulta. Monipuolisemmin harrastuksen koukeroita ja historiaa avaa VirtuaaliWiki. Jos kiinnostuit virtuaalihevosharrastuksesta, tai olet jo harrastaja, saat VRL:stä eniten irti rekisteröimällä itsellesi VRL-tunnuksen. VRL-tunnuksen avulla jatkaa tutustumista esimerkiksi ilmoittamalla virtuaalitallisi tallilistalle, rekisteröimällä hevosesi tai osallistumalla kilpailuihin. Tutustu myös moniin liiton alaisiin järjestöihin ja yhdistyksiin, jotka järjestävät erilaista toimintaa virtuaalimaailmassa.

</p>
<h2>Ylläpito</h2>
<p>VRL:n ylläpito on täysin harrastajien omissa käsissä. Sivuston toiminnasta ovat vastuussa <?php foreach ($admins as $key=>$admin){
   echo '<strong>'.$admin['nimimerkki'] . "</strong> (VRL-". $admin['tunnus'] . ")";
   
   if(sizeof($admins) == 1){
      echo ', ';
   }
   else if(sizeof($admins) - 2 == $key){
      echo ' ja ';
   }
   else {
      echo ', ';
   }
}
   
   ?> ja liiton pyörittämisessä apuna toimii kymmeniä vapaaehtoistyöntekijöitä monissa eri tehtävissä! Mikäli olet kiinnostunut auttamaan liiton pyörittämisessä, älä epäröi ottaa yhteyttä! Yhteystietosivulta löydät kaikkien osa-alueiden vastuuhenkilöiden nimet ja yhteystiedot.</p>


<h2>Tiedotukset</h2>
<?php foreach ($tiedotukset as $tiedotus){
   echo date( "d.m.Y", strtotime($tiedotus['aika'])) ." - <a href='". site_url("liitto/tiedotus/".$tiedotus['tid']) . "'>". $tiedotus['otsikko'] . "</a><br>";  
    
}?>

<a href="<?php echo site_url("liitto/tiedotukset")?>">Lue lisää tiedotuksia</a>.