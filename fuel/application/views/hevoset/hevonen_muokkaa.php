<h2>Muokkaa jaosta <?=$jaos['nimi']?> (<?=$jaos['lyhenne']?>)</h2>


   <ul class="nav nav-tabs">
      <li role="presentation" class="<?php if ($sivu == 'tiedot'){echo "active";}?>"><a href="<?php echo base_url($url . '/tiedot')?>">Tiedot</a></li>
      <li role="presentation" class="<?php if ($sivu == 'saannot'){echo "active";}?>"><a href="<?php echo base_url($url . '/saannot')?>">Säännot</a></li>
      <li role="presentation" class="<?php if ($sivu == 'luokat'){echo "active";}?>"><a href="<?php echo base_url($url . '/luokat')?>">Luokat</a></li>
      <li role="presentation" class="<?php if ($sivu == 'porrastetut'){echo "active";}?>"><a href="<?php echo base_url($url . '/porrastetut')?>">Porrastetut</a></li>
      <li role="presentation" class="<?php if ($sivu == 'yllapito'){echo "active";}?>"><a href="<?php echo base_url($url . '/yllapito')?>">Ylläpito</a></li>


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