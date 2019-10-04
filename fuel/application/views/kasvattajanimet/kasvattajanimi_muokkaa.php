<h2>Muokkaa kasvattajanimeä: <?=$nimi['kasvattajanimi']?> (#<?=$nimi['id']?>)</h2>


<div class="container">
    <p><b>Talli:</b> <?=$nimi['tnro']?></p>
    <p><b>Rekisteröity:</b> <?=$nimi['rekisteroity']?></p>
    <p><b>Omistajat:</b>
        <?php
            $first = true;
            foreach($owners as $o)
            {
                if($first)
                {
                    echo $o['nimimerkki'] . "(<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
                    $first = false;
                }
                else
                    echo ", " . $o['nimimerkki'] . " (<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
            }
        ?>
    </p>
    
</div>

<h2>Muokattavat tiedot</h2>

   <ul class="nav nav-tabs">
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
        
        
    
    ?>
</div>


