<?php
///Muunnokset, älä muokkaa jos et tiedä mitä

//kasvattajanimitieto
    $kasvattajanimitieto = "-";
    if (!isset($hevonen['kasvattajanimi_id']) || strlen($hevonen['kasvattajanimi_id']) == 0){
        if (isset($hevonen['kasvattajanimi'])){
            $kasvattajanimitieto = $hevonen['kasvattajanimi'] . " (ei rekisteröity)";
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
 
$painotustieto = "";
if(isset($hevonen['h_painotusnimi'])) {
 $painotustieto = '<a href="'. site_url('virtuaalihevoset/laji/'.$hevonen['pid']).'">'.$hevonen['h_painotusnimi'].'</a>';

 }

$rotutieto = "";
if(isset($hevonen['polv_tark']) && $hevonen['polv_tark'] == 1){
 $rotutieto = '<br /><small>Polveutuminen tarkastettu ' . $hevonen['polv_tark_date'] .'.
                                                             Veriprosentti: '.floatval($hevonen['polv_pros']) . '%, tarkastaja: VRL-'.$hevonen['polv_tark_vrl'].'.</small>';
    
}

$exomistajatieto = "";
foreach($exowners as $o){
 
    $exomistajatieto .= '<li>'.$o['nimimerkki'] . " (<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>) " .
    " <small>Muutettu: ".date("d.m.Y",strtotime($o['aika'])).", Muutti: VRL-" . $o['muokkasi'] . "</small></li>";

}

$ikaantymistieto = "";

        $compare_day = date("Y-m-d");
        if(isset($hevonen['kuollut']) && $hevonen['kuollut']){
            $compare_day = date($hevonen['kuol_pvm']);
            $ikaantymistieto = "Kuollessaan ";
        }
        $calculated_age = $this->porrastetut->calculate_age($hevonen['age'], $compare_day);
        $ikaantyminen = $hevonen['age']['ikaantyminen_d'] ?? 0;
        
        if($calculated_age == 0){
            if( $ikaantyminen < 1){
                $ikaantymistieto .= "alle 3v, tai ikääntymistä ei ole ilmoitettu";
            }else {
                $ikaantymistieto .= $this->age_calc->calculateAge ($hevonen['syntymaaika'],  $ikaantyminen, $compare_day);
                $ikaantymistieto .= " vuotta, ikääntyminen: " . $ikaantyminen . "pv = 1v";
            }
        }else if ($calculated_age == 8){
            if($ikaantyminen < 1){
                $ikaantymistieto.= "yli 8v";
            }else {
                $ikaantymistieto .= $this->age_calc->calculateAge ($hevonen['syntymaaika'], $ikaantyminen, $compare_day);
                $ikaantymistieto .= " vuotta, ikääntyminen: " . $ikaantyminen . "pv = 1v";
            }
        }else {
            $ikaantymistieto .= $calculated_age;
            if($ikaantyminen > 0){
                $ikaantymistieto .= " vuotta, ikääntyminen: " . $ikaantyminen . "pv = 1v";
            }

        }


$edit = "";

if(isset($edit_tools) && $edit_tools == true){
 $button = '<button type="button" class="btn btn-default">
  <img src="'.base_url().'assets/images/icons/edit.png" /></button>';
 $edit = '<a href="'.site_url('/virtuaalihevoset/muokkaa/'. $hevonen['reknro']).'">'.$button.'</a>';
}

?>



<h2><?=$hevonen['h_nimi']?> (<?=$hevonen['reknro']?>) <?=$edit?></h2>

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
   <tr><th scope="row">Rotu</th><td> <a href="<?php echo site_url().'/virtuaalihevoset/rotu/'.$hevonen['rotunro'];?>"><?=$hevonen['h_rotunimi']?></a><?=$rotutieto;?></td></tr>
   <tr><th scope="row">Sukupuoli</th><td> <?=$hevonen['sukupuoli']?></td></tr>
   <tr><th scope="row">Säkäkorkeus</th><td> <?php if(isset($hevonen['sakakorkeus']) && $hevonen['sakakorkeus'] > 0){ echo $hevonen['sakakorkeus'] . " cm"; } ?></td></tr>
   <tr><th scope="row">Syntynyt</th><td> <?=$hevonen['syntymaaika']?><?=$maatieto;?>, <?=$ikaantymistieto;?></td></tr>
   <tr><th scope="row">Väri</th><td> <a href="<?php echo site_url().'/virtuaalihevoset/vari/'.$hevonen['vid'];?>"><?=$hevonen['h_varinimi']?></a></td></tr>
   <tr><th scope="row">Painotus</th><td> <?php echo $painotustieto; ?></td></tr>
   <tr><th scope="row">Sivut</th><td> <a href="<?=$hevonen['h_url']?>"><?=$hevonen['h_url']?></a></td></tr>
   <tr><th scope="row">Rekisteröity</th><td> <?=$hevonen['rekisteroity']?></td></tr>
   <tr><th scope="row">Kotitalli</th><td> <?php echo $kotitallitieto; ?></td></tr>
   <tr><th scope="row">Kasvattajanimi</th><td> <?php echo $kasvattajanimitieto; ?></td></tr>
   <tr><th scope="row">Kasvattaja</th><td> <?php echo $kasvattajatieto; ?></td></tr>
   <tr><th scope="row">Omistajat</th><td> <?php echo $omistajatieto; ?></td></tr>
   <?php if (strlen($exomistajatieto)>1){?>
      <tr><th scope="row">Omistajahistoria</th><td> <?php echo $exomistajatieto; ?></td></tr>
   <?php } ?>

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
             $sspros = 0;
             $skatopros = 1;
             if(isset($suku) and sizeof($suku) > 0){
              $sspros = $pedigree_printer->countInbreedingPercentage($suku);
              $skatopros = $pedigree_printer->countMissingPercentage($suku);
             }
              
              ?>
              <h3>Suvun tiedot</h3>
              <p>Sukusiitosprosentti: <?=$sspros;?>%<br />Sukukatokerroin: <?=$skatopros;?></p>
              <p><a href="<?php echo site_url(). 'kasvatus/jalostus';?>">Lue lisää</a></p>
              <?php
              echo "<h4>Isälinja</h4> <p>";
             $pedigree_printer->print_line('i', $suku);
             echo "</p>";
             echo "<h4>Emälinja</h4><p> ";
             $pedigree_printer->print_line('e', $suku);
             echo "</p>";
           
           ?>
              
              <?php
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
         if((isset($show_palkinnot) && sizeof($show_palkinnot)> 0) || (isset($old_show_palkinnot) && sizeof($old_show_palkinnot)> 0)){
          
            if(isset($show_palkinnot) && sizeof($show_palkinnot)> 0){
             echo '<ul>';            
             print_shows($show_palkinnot, $vrl_helper);           
             echo '</ul>';
            }
            if(isset($old_show_palkinnot) && sizeof($old_show_palkinnot)> 0){
             echo '<strong>Vanhat näyttelypalkinnot*</strong><br />';
             echo '<ul>';            
             print_shows($old_show_palkinnot, $vrl_helper);           
             echo '</ul>';
             echo '*<small>Nämä palkinnot on saatu kun hevonen on kilpaillut näyttelyissä ilman rekisterinumeroa.
             Nämä on haettu tietokannasta nimen perusteella, ja esim. ktk-tuomareiden tulee tarkastaa tulosarkistosivulta
             onko palkinto ok (vai onko kyseessä esim. erirotuinen tai ennen tämän hevosen syntymää kilpaillut kaima).</small>';
            }
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





<?php
function print_shows($show_palkinnot, $vrl_helper){
 
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
}?>