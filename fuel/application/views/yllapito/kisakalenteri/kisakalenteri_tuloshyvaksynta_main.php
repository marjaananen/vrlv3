<h1>Tulosten hyväksyntä</h1>

<?php if(strlen(fuel_var('msg', '')) > 0){ ?>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php } ?>

<?php
if(isset($jaokset)){
  foreach ($jaokset as $jaos){
    echo '<p><strong>'.$jaos['lyhenne'].'</strong><br>';
    echo '<a href="'.site_url($url. '/tuloshyvaksynta/'.$jaos['id']).'">Käsittele tulosjonoa</a>.'; 
  
    echo $jaos['tulokset_norm']." hakemusta tulosjonossa ". latest('tulokset_norm', $jaos).". ".
      $jaos['tulokset_porr']." hakemusta porrastettujen tulosjonossa ". latest('tulokset_porr', $jaos)."</p>";
      
  }
}

?>



<?php

function latest($key, $data){
    if($data[$key] > 0){
        return "<small>(vanhin: " . date( "d.m.Y", strtotime ( $data[$key.'_latest'] )) . ")</small>";
    }
}

?>

							