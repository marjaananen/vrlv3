<?php
///Muunnokset, älä muokkaa jos et tiedä mitä

//kasvattajanimitieto
    $kasvattajanimitieto = "-";
    if (!isset($hevonen['kasvattajanimi_id']) || strlen($hevonen['kasvattajanimi_id']) == 0){
        if (isset($hevonen['kasvattajanimi'])){
            $kasvattajanimitieto == $hevonen['kasvattajanimi'];
        }    
    }
    else {
        $kasvattajanimitieto = "<a href=\"" . site_url('kasvatus/kasvattajanimet/nimi/'.$hevonen['kasvattajanimi_id']) . "\">". $hevonen['kasvattajanimi'] . "</a>";
    }

//omistajatieto
        $omistajatieto = "";
        $first = true;
        foreach($owners as $o){
            if($first){
                $omistajatieto .= $o['nimimerkki'] . " (<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
                $first = false;
                }
            else{
                $omistajatieto .= ", " . $o['nimimerkki'] . " (<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
            }
        }
    
//kasvattajatieto
$kasvattajatieto = "";
if(isset($hevonen['kasvattaja_talli'])){
    $kasvattajatieto = '<a href="' . site_url('tallit/talli/'.$hevonen['kasvattaja_talli']).'">'.$hevonen['kasvattaja_talli'].'</a>';  
    
}

if(isset($hevonen['kasvattaja_tunnus'])){
    if (strlen($kasvattajatieto) > 0){
        $kasvattajatieto .= ', ';
    }

    $kasvattajatieto .= '<a href="' . site_url('tunnus') . "/VRL-" . $hevonen['kasvattaja_tunnus']. '">VRL-' . $hevonen['kasvattaja_tunnus'] . "</a>";
    
}


    
?>




<h2><?=$hevonen['h_nimi']?> (<?=$hevonen['reknro']?>)</h2>

<?php if($hevonen['kuollut']) : ?>
    <h3>Tämä hevonen on kuollut <?=$hevonen['kuol_pvm']?>.</h3>
<?php endif; ?>

<?php if (isset($palkinnot) && sizeof($palkinnot) > 0){
 echo '<div class="row">';
 echo '<div class="col-xs-12 col-md-8">';
}

else {
 echo '<div class="container">';
}

?>
  
    <p><b>Rotu:</b> <?=$hevonen['h_rotunimi']?></p>
    <p><b>Sukupuoli:</b> <?=$hevonen['sukupuoli']?></p>
    <p><b>Säkäkorkeus:</b> <?=$hevonen['sakakorkeus']?>cm</p>
    <p><b>Syntymäaika:</b> <?=$hevonen['syntymaaika']?>, <?=$hevonen['maa']?></p>
    <p><b>Väri:</b> <?=$hevonen['h_varinimi']?></p>
    <p><b>Painotus:</b> <?=$hevonen['h_painotusnimi']?></p>
    <p><b>Url:</b> <a href="<?=$hevonen['h_url']?>"><?=$hevonen['h_url']?></a></p>
    <p><b>Rekisteröity:</b> <?=$hevonen['rekisteroity']?></p>
    <p><b>Kotitalli:</b> <a href="<?php echo site_url('tallit/talli/'.$hevonen['kotitalli'])?>"><?=$hevonen['kotitalli']?></a></p>
    <p><b>Kasvattajanimi:</b> <?php echo $kasvattajanimitieto; ?></p>
    <p><b>Kasvattaja:</b> <?php echo $kasvattajatieto; ?></p>
    <p><b>Omistajat:</b> <?php echo $omistajatieto; ?></p>
</div>

<?php
if (isset($palkinnot) && sizeof($palkinnot) > 0){
 echo '<div class="col-xs-6 col-md-4">';
 
 
 foreach ($palkinnot as $palkinto){
  echo '<p>';
  echo '<b>'.$palkinto['palkinto'];
  if ($palkinto['tulos'] > 0){
   echo ' ('.$palkinto['tulos'].'p)';
  }
 echo '</b>, ' . $vrl_helper->sql_date_to_normal($palkinto['pv']);
 if(isset($palkinto['jaos'])){
  echo ', '. $palkinto['jaos'];
 }
  echo '</p>';

 }
 
 ?>
 
   <a href="<?php echo base_url('virtuaalihevoset/hevonen/'. $hevonen['reknro'] . '/palkinnot')?>">Tarkemmat kuvaukset palkinnoista</a>
<?php
 echo '</div>';
 echo '</div>';
}
?>


 <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'suku' || empty($sivu)){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/hevonen/'. $hevonen['reknro'] . '/suku')?>">Suku</a></li>
        <li role="presentation" class="<?php if ($sivu == 'varsat'){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/hevonen/'. $hevonen['reknro'] . '/varsat')?>">Jälkeläiset</a></li>
        <li role="presentation" class="<?php if ($sivu == 'palkinnot'){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/hevonen/'. $hevonen['reknro'] . '/palkinnot')?>">Palkinnot</a></li>
        <li role="presentation" class="<?php if ($sivu == 'porrastetut'){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/hevonen/'. $hevonen['reknro'] . '/porrastetut')?>">Ominaisuudet</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kilpailut'){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/hevonen/'. $hevonen['reknro'] . '/kilpailut')?>">Kilpailustatistiikka</a></li>
    </ul>
    
    <?php
        if($sivu == 'suku' || empty($sivu))
        {
             $pedigree_printer->createPedigree($suku, 4);
        }
        else if($sivu == 'varsat'){
            echo $foals;

        } 
        else if($sivu == 'porrastetut'){
            echo $porr_levels;
            echo $porr_stats;



        }
        
        else if($sivu == 'kilpailut'){
            echo $kilpailut;

        }
        
        else if($sivu == "palkinnot"){
         if (isset($palkinnot) && sizeof($palkinnot) > 0){
 
           foreach ($palkinnot as $palkinto){
            echo '<p>';
            echo '<b>'.$palkinto['palkinto'];
            if ($palkinto['tulos'] > 0){
             echo ' ('.$palkinto['tulos'].'p)';
            }
            echo '</b>';
            if(isset($palkinto['jaos'])){
             echo ', '. $palkinto['jaos'];
            }
            echo '<br />';
            echo $vrl_helper->sql_date_to_normal($palkinto['pv']).', '.$palkinto['otsikko'].'<br />';
            echo $palkinto['kommentti'];
            echo '</p>';
          
           }
        }
        
        else {
         echo '<p>Ei palkintoja</p>';
        }
       }
    
    ?>

<hr>

<div class="alert alert-info" role="alert">
  <?=$hevonen['h_nimi']?> on virtuaalihevonen. Se ei ole oikeasti olemassa.
</div>





