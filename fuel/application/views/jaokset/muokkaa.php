<h2>Muokkaa hevosta <?=$hevonen['nimi']?> (<?=$hevonen['reknro']?>)</h2>


   <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'tiedot'){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/muokkaa/'. $hevonen['reknro'] . '/tiedot')?>">Tiedot</a></li>
    
        <li role="presentation" class="<?php if ($sivu == 'omistajat'){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/muokkaa/'. $hevonen['reknro'] . '/omistajat')?>">Omistajat</a></li>

    </ul>
       <?php echo fuel_var('info', '');?>

   
     <?php
    if($sivu == 'tiedot'){     
        echo fuel_var('editor', '');
        }

    else if($sivu == 'omistajat'){?>
          <div class="panel panel-default"><div class="panel-body">
            <p>Hevosella voi olla useita omistajia (taso 1) ja haltijoita (taso 0).
            Omistaja pystyy muokkaamaan kasvattajanimen muita omistajia, haltija pystyy muokkaamaan kasvattajanimen tietoja,
            mutta ei voi muokata omistajia. Kasvattajanimellä pitää olla vähintään yksi omistaja.</p>

            <?php echo fuel_var('form', '');?>
             </div></div>
            <?php echo fuel_var('ownership', '');?>

            
            
        <?php }
        
        ?>