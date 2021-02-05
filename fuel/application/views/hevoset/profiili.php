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

$kotitallitieto = "-";

if(isset($hevonen['kotitalli'])){
 $kotitallitieto = '<a href="'. site_url('tallit/talli/'.$hevonen['kotitalli']).'">'.$hevonen['t_nimi'].'</a> ('.$hevonen['kotitalli'].')';
}

$maatieto = "";
if(isset($hevonen['maa'])) {
 $maatieto = ', <a href="'. site_url('virtuaalihevoset/maa/'.$hevonen['maaid']).'">'.$hevonen['maa'].'</a>';

 }


    
?>




<h2><?=$hevonen['h_nimi']?> (<?=$hevonen['reknro']?>)</h2>

<?php if($hevonen['kuollut']) : ?>

    <div class="alert alert-warning">
      <span class="glyphicon glyphicon-info-sign"></span> Tämä hevonen on kuollut <?=$hevonen['kuol_pvm']?>.</span></div>
<?php endif; ?>

<?php if (isset($palkinnot) && sizeof($palkinnot) > 0){
 echo '<div class="row">';
 echo '<div class="col-xs-12 col-md-8">';
}


?>

<table class="table table-striped">
   <tr><th scope="row">Rotu</th><td> <a href="<?php echo site_url().'/virtuaalihevoset/rotu/'.$hevonen['rotunro'];?>"><?=$hevonen['h_rotunimi']?></a></td></tr>
   <tr><th scope="row">Sukupuoli</th><td> <?=$hevonen['sukupuoli']?></td></tr>
   <tr><th scope="row">Säkäkorkeus</th><td> <?=$hevonen['sakakorkeus']?> cm</td></tr>
   <tr><th scope="row">Syntynyt</th><td> <?=$hevonen['syntymaaika']?><?=$maatieto;?></td></tr>
   <tr><th scope="row">Väri</th><td> <a href="<?php echo site_url().'/virtuaalihevoset/vari/'.$hevonen['vid'];?>"><?=$hevonen['h_varinimi']?></a></td></tr>
   <tr><th scope="row">Painotus</th><td> <?php if (isset($hevonen['painotusnimi'])) { echo $hevonen['painotusnimi']; } else {echo "-";}?></td></tr>
   <tr><th scope="row">Sivut</th><td> <a href="<?=$hevonen['h_url']?>"><?=$hevonen['h_url']?></a></td></tr>
   <tr><th scope="row">Rekisteröity</th><td> <?=$hevonen['rekisteroity']?></td></tr>
   <tr><th scope="row">Kotitalli</th><td> <?php echo $kotitallitieto; ?></td></tr>
   <tr><th scope="row">Kasvattajanimi</th><td> <?php echo $kasvattajanimitieto; ?></td></tr>
   <tr><th scope="row">Kasvattaja</th><td> <?php echo $kasvattajatieto; ?></td></tr>
   <tr><th scope="row">Omistajat</th><td> <?php echo $omistajatieto; ?></td></tr>
</table>
 

<?php
if (isset($palkinnot) && sizeof($palkinnot) > 0){
 echo '</div><div class="col-xs-6 col-md-4">';
 
 
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
        <li role="presentation" class="<?php if ($sivu == 'nayttelyt'){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/hevonen/'. $hevonen['reknro'] . '/nayttelyt')?>">Näyttelyt</a></li>
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
        
        else if($sivu == 'nayttelyt'){
            if(isset($show_palkinnot) && sizeof($show_palkinnot)> 0){
             echo '<ul>';

             $last_id = 0;
             $shows = array();
             $rewards = array();
             foreach($show_palkinnot as $show){
              $shows[$show['bis_id']] = $show;
              if(isset($rewards[$show['bis_id']])){
               $rewards[$show['bis_id']] = $rewards[$show['bis_id']] . ", " . $show['palkinto'];
              }
              else {
               $rewards[$show['bis_id']] = $show['palkinto'];
              }
             }
             
             foreach ($shows as $id => $show){
              $bis = '(<a href="'.site_url().'/kilpailutoiminta/tulosarkisto/bis/'.$id.'">tulosarkisto</a>)';

              echo '<li>' . $vrl_helper->sql_date_to_normal($show['kp']).', <b>' . $rewards[$id] .'</b>, päätuomari: ' . $show['paatuomari_nimi'] . ' ' . $bis; 
              echo '</li>';
      
             
             }
             echo '</ul>';
            }else {
             echo "<p>Ei näyttelypalkintoja</p>";
            }

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





