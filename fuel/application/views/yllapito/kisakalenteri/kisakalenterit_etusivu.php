<H1>Kilpailukalenterien hallinta</H1>

<p>
Arvontajonossa on <?php if ($porrastetut_amount < 100) { echo $porrastetut_amount;} else { echo "yli 100"; } ?> porrastettua kisaa joiden kilpailupäivä on mennyt.
Käynnistä arvonta klikkaamalla <a href="<?php echo site_url('yllapito/kalenterit/porrastetut_run');?>">tästä</a>. Jonain päivänä tämä alkaa tapahtua automaattisesti.
</p>

<hr />
<?php

foreach ($jaokset as $jaos){
  echo '<strong>'.$jaos['lyhenne'].'</strong><br>';
  echo '<a href="'.site_url($url. 'tuloshyvaksynta/'.$jaos['id']).'">Tulosjono</a> & ';
  echo '<a href="'.site_url($url. 'kisahyvaksynta/'.$jaos['id']).'">Kutsujono</a><br />'; 

  echo $jaos['hakemukset_norm']." kisa-anomusta ". latest('hakemukset_norm', $jaos)."<br />".
  //$jaos['hakemukset_porr']." porrastettua kisa-anomusta ". latest('hakemukset_porr', $jaos)."<br />".
      $jaos['tulokset_norm']." hakemusta tulosjonossa ". latest('tulokset_norm', $jaos)."<br /><br />".
    //$jaos['tulokset_porr']." hakemusta porrastettujen tulosjonossa ". latest('tulokset_porr', $jaos)."</p>"
    "";
    
}

?>



<?php

function latest($key, $data){
    if($data[$key] > 0){
        return "<small>(vanhin: " . date( "d.m.Y", strtotime ( $data[$key.'_latest'] )) . ")</small>";
    }
}

?>

							