<h2>Muokkaa kasvattajanimeä: <?=$nimi['kasvattajanimi']?> (#<?=$nimi['id']?>)</h2>

<?php if(strlen(fuel_var('msg', '')) > 0){ ?>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php } ?>
    
<?php echo $nimi_info;?>

<h2>Muokattavat tiedot</h2>

   <ul class="nav nav-tabs">
       <li role="presentation" class="<?php if ($sivu == 'tiedot'){echo "active";}?>"><a href="<?php echo base_url('kasvatus/kasvattajanimet/muokkaa/'. $nimi['id'] . '/tiedot')?>">Tiedot</a></li>
    
        <li role="presentation" class="<?php if ($sivu == 'rodut'){echo "active";}?>"><a href="<?php echo base_url('kasvatus/kasvattajanimet/muokkaa/'. $nimi['id'] . '/rodut')?>">Rodut</a></li>
    
        <li role="presentation" class="<?php if ($sivu == 'omistajat'){echo "active";}?>"><a href="<?php echo base_url('kasvatus/kasvattajanimet/muokkaa/'. $nimi['id'] . '/omistajat')?>">Omistajat</a></li>


    </ul>
    <?php echo fuel_var('info', '');?>
    
    
    <?php if($sivu == 'rodut'){ ?>
            
            <div class="panel panel-default"><div class="panel-body">
            <p>Puuttuuko kasvattajanimeltäsi rotu? Täällä voit päivittää kasvattajanimen rotulistauksen vastaamaan rekisteröityjen kasvattien rotuja. Huom! Tässä lasketaan vain kasvatit, joilla on kasvattajaliite merkittynä rekisterisivulle.</p>
            
            <?php echo fuel_var('form', '');?>
           </div></div>
            <?php echo fuel_var('breeds', '');?>
            
            
     <?php    }
        else if($sivu == 'omistajat'){?>
          <div class="panel panel-default"><div class="panel-body">
            <p>Kasvattajanimellä voi olla useita omistajia (taso 1) ja haltijoita (taso 0).
            Omistaja pystyy muokkaamaan kasvattajanimen muita omistajia, haltija pystyy muokkaamaan kasvattajanimen tietoja,
            mutta ei voi muokata omistajia. Kasvattajanimellä pitää olla vähintään yksi omistaja.</p>

            <?php echo fuel_var('form', '');?>
             </div></div>
            <?php echo fuel_var('ownership', '');?>

            
            
        <?php }
        
           else if($sivu == 'tiedot'){  echo fuel_var('form', '');
            
           }
           
           ?>
        
        
    
    
</div>


