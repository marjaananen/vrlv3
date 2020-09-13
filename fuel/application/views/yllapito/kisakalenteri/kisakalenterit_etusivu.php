

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

							