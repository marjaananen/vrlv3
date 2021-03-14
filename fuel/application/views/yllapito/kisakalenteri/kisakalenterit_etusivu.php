<H1>Kilpailukalenterien hallinta</H1>

<hr />
<a href="<?php echo site_url('yllapito/kalenterit/porrastetut_run');?>">

<?php
$amount = "100+";
$type = "danger";

if ($porrastetut_amount < 100) {
  $amount = $porrastetut_amount;
  $type = "warning";
  if($porrastetut_amount == 0){
    $type = "success";
  }
}

?>
<button type="button" class="btn btn-<?php echo $type;?>">Arvontajono <span class="badge"><?php echo $amount;?></span></button>
</a><p>
Porrastettujen arvontajonossa on <?php if ($porrastetut_amount < 100) { echo $porrastetut_amount;} else { echo "yli 100"; } ?> porrastettua kisaa joiden
kilpailupäivä on tänään tai aiemmin. Käynnistä arvonta klikkaamalla yo. painiketta. Jonain päivänä tämä alkaa tapahtua automaattisesti, mutta sitä
ennen jonkun (kenen tahansa) on välillä klikattava tätä nappulaa.
</p>

<hr />
<?php

foreach ($jaokset as $jaos){
  $amount = "100+";
  $type = "danger";
  
  if ($jaos['hakemukset_norm'] < 100) {
    $amount = $jaos['hakemukset_norm'];
    $type = "warning";
    if($jaos['hakemukset_norm'] == 0){
      $type = "success";
    }
  }
  
  echo '<strong>'.$jaos['lyhenne'].'</strong><br>';
  echo '<table cellpadding="5"><tr><td>';
  echo '<a href="'.site_url($url. 'kisahyvaksynta/'.$jaos['id']).'">';
  echo '<button type="button" class="btn btn-'. $type .'">Kutsujono <span class="badge">'. $amount .'</span></button> &nbsp;';
  echo '</a> <br /> ' . latest('hakemukset_norm', $jaos) . '&nbsp;</td>';

  $amount = "100+";
  $type = "danger";
  
  if ($jaos['tulokset_norm'] < 100) {
    $amount = $jaos['tulokset_norm'];
    $type = "warning";
    if($jaos['tulokset_norm'] == 0){
      $type = "success";
    }
  }
  echo '<td>';
  echo '<a href="'.site_url($url. 'tuloshyvaksynta/'.$jaos['id']).'">';
  echo '<button type="button" class="btn btn-'. $type .'">Tulosjono <span class="badge">'. $amount .'</span></button> ';
  echo '</a> <br /> &nbsp;' . latest('tulokset_norm', $jaos) . '</td></tr></table>';
    
}

?>



<?php

function latest($key, $data){
    if($data[$key] > 0){
        return "<small>(vanhin: " . date( "d.m.Y", strtotime ( $data[$key.'_latest'] )) . ")</small>";
    }
}

?>

							