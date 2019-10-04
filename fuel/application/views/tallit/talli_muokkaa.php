<h2>Muokkaa tallia: <?=$stable['nimi']?> (<?=$stable['tnro']?>)</h2>



   <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'tiedot'){echo "active";}?>"><a href="<?php echo base_url('tallit/muokkaa/'. $stable['tnro'] . '/tiedot')?>">Tiedot</a></li>
    
        <li role="presentation" class="<?php if ($sivu == 'omistajat'){echo "active";}?>"><a href="<?php echo base_url('tallit/muokkaa/'. $stable['tnro'] . '/omistajat')?>">Omistajat</a></li>

        <li role="presentation" class="<?php if ($sivu == 'lopeta'){echo "active";}?>"><a href="<?php echo base_url('tallit/muokkaa/'. $stable['tnro'] . '/lopeta')?>">Lopeta</a></li>

    </ul>
   
   
     <?php
    if($sivu == 'tiedot'){     
        echo fuel_var('editor', '');
        }
    else if($sivu == 'lopeta'){    
        echo fuel_var('editor', '');
        }
    else if($sivu == 'omistajat'){?>
          <div class="panel panel-default"><div class="panel-body">
            <p>Kasvattajanimellä voi olla useita omistajia (taso 1) ja haltijoita (taso 0).
            Omistaja pystyy muokkaamaan kasvattajanimen muita omistajia, haltija pystyy muokkaamaan kasvattajanimen tietoja,
            mutta ei voi muokata omistajia. Kasvattajanimellä pitää olla vähintään yksi omistaja.</p>

            <?php echo fuel_var('form', '');?>
             </div></div>
            <?php echo fuel_var('ownership', '');?>

            
            
        <?php }
        
        ?>