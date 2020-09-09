<h1>Kilpailujen hyväksyntä kalenteriin</h1>

<?php if(strlen(fuel_var('msg', '')) > 0){ ?>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php } ?>

<?php

foreach ($jaokset as $jaos){
  echo '<p><strong>'.$jaos['lyhenne'].'</strong><br>';
  echo '<a href="'.site_url($url. 'kisahyvaksynta/'.$jaos['id']).'">Käsittele kutsujonoa</a>.'; 
  echo "Tällä hetkellä " . $jaos['hakemukset_norm']." kisa-anomusta ". latest('hakemukset_norm', $jaos)."</p>";

    
}

?>



<?php

function latest($key, $data){
    if($data[$key] > 0){
        return "<small>(vanhin: " . date( "d.m.Y", strtotime ( $data[$key.'_latest'] )) . ")</small>";
    }
}

?>

							