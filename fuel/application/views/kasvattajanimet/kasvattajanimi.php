<h2>Kasvattajanimi: <?=$nimi['kasvattajanimi']?> (#<?=$nimi['id']?>)</h2>


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

   <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'rodut'){echo "active";}?>"><a href="<?php echo base_url('kasvatus/kasvattajanimet/nimi/'. $nimi['id'] . '/rodut')?>">Rodut</a></li>

        <li role="presentation" class="<?php if ($sivu == 'kasvatit'){echo "active";}?>"><a href="<?php echo base_url('kasvatus/kasvattajanimet/nimi/'. $nimi['id'] . '/kasvatit')?>">Kasvatit</a></li>

    </ul>
    
    <?php
        if($sivu == 'rodut'){
            echo $breeds;
        }
        else if($sivu == 'kasvatit'){
            echo $foals;
        } 
        
    
    ?>
</div>


