<h2><?=$title ?></h2>


<?php
    $amount = 0;
    $tammat = 0;
    $tammatp = 0;
    $orit = 0;
    $oritp = 0;
    $ruunat = 0;
    $ruunatp = 0;

if (isset($genders['total']) & $genders['total'] > 0){
    $amount = $genders['total'];
    
    if (isset($genders['tammat']) && $genders['tammat'] > 0){
        $tammat = $genders['tammat'];
        $tammatp = floor(($tammat/$amount)*100);
        
    }
    
    if (isset($genders['orit'])&& $genders['orit'] > 0){
        $orit = $genders['orit'];
        $oritp = floor(($orit/$amount)*100);

        
    }
    
    if (isset($genders['ruunat'])&& $genders['ruunat'] > 0){
        $ruunat = $genders['ruunat'];
        $ruunatp = floor(($ruunat/$amount)*100);

        
    }
    //let's fix error margin
    if($ruunat > 0){
        $ruunatp = 100 - $oritp - $tammatp;
    }
    else if ($orit > 0){
        $oritp = 100 - $tammatp;
    }
    else {
        $tammatp = 100;
    }
    
}

?>

<h3>Rekisteröidyt hevoset</h3>

<div class="progress">
  <div class="progress-bar progress-bar-danger" style="width: <?php echo $tammatp; ?>%">
    Tammat (<?php echo $tammatp; ?>%)
  </div>
  <div class="progress-bar progress-bar-info" style="width: <?php echo $oritp; ?>%">
    Orit (<?php echo $oritp; ?>%)
  </div>
  <div class="progress-bar progress-bar-success" style="width: <?php echo $ruunatp; ?>%">
    Ruunat (<?php echo $ruunatp; ?>%)
  </div>
</div>

<p>Yhteensä <b><?php echo $amount; ?></b> kpl, joista
tammoja <b><?php echo $tammat;?></b> kpl (<?php echo $tammatp; ?>%),
oreja <b><?php echo $orit;?></b> kpl (<?php echo $oritp; ?>%) ja
ruunia <b><?php echo $ruunat;?></b> kpl (<?php echo $ruunatp; ?>%).</p>



<?php
if (isset ($other_data)){
    foreach ($other_data as $data){
        
        echo $data;
        
    }
}

?>