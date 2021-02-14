<?php

$edit = "";

if(isset($edit_tools) && $edit_tools == true){
 $button = '<button type="button" class="btn btn-default">
  <img src="'.base_url().'assets/images/icons/edit.png" /></button>';
 $edit = '<a href="'.site_url('/kasvatus/kasvattajanimet/muokkaa/'. $nimi['id']).'">'.$button.'</a>';
}

?>

<h2>Kasvattajanimi: <?=$nimi['kasvattajanimi']?> (#<?=$nimi['id']?>) <?=$edit;?></h2>

<?php echo $nimi_info;?>

   <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'rodut'){echo "active";}?>"><a href="<?php echo base_url('kasvatus/kasvattajanimet/nimi/'. $nimi['id'] . '/rodut')?>">Rodut</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kasvatit'){echo "active";}?>"><a href="<?php echo base_url('kasvatus/kasvattajanimet/nimi/'. $nimi['id'] . '/kasvatit')?>">Kasvatit</a></li>
        <li role="presentation" class="<?php if ($sivu == 'stats'){echo "active";}?>"><a href="<?php echo base_url('kasvatus/kasvattajanimet/nimi/'. $nimi['id'] . '/stats')?>">Statistikka</a></li>

    </ul>
    
    <?php
        if($sivu == 'rodut'){
            echo $breeds;
        }
        else if($sivu == 'kasvatit'){
            echo $foals;
        }
         else if($sivu == 'stats'){
            echo $stats;
        } 
        
    
    ?>
</div>


