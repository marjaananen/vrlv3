<h2>Talliprofiili: <?=$stable['nimi']?></h2>

<?php if($stable['lopettanut']) : ?>
    <h3>Tämä talli on lopettanut</h3>
<?php endif; ?>

<div class="container">
    <p><b>Tallinumero:</b> <?=$stable['tnro']?></p>
    <p><b>Rekisteröity:</b> <?=$stable['perustettu']?></p>
    <p><b>Kotisivu:</b> <a href="<?=$stable['url']?>"><?=$stable['url']?></a></p>
    <p><b>Kuvaus:</b> <?=$stable['kuvaus']?></p>
    <p><b>Kategoriat:</b>
        <?php
            $first = true;
            foreach($categories as $c)
            {
                if($first)
                {
                    echo $c['katelyh'];
                    $first = false;
                }
                else
                    echo ", " . $c['katelyh'];
            }
        ?>
    </p>
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
                    echo ", " . $o['nimimerkki'] . "(<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
            }
        ?>
    </p>
    
</div>

   <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'hevoset'){echo "active";}?>"><a href="<?php echo base_url('tallit/talli/'. $stable['tnro'] . '/hevoset')?>">Hevoset</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kasvatit'){echo "active";}?>"><a href="<?php echo base_url('tallit/talli/'. $stable['tnro'] . '/kasvatit')?>">Kasvatit</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kasvattajanimet'){echo "active";}?>"><a href="<?php echo base_url('tallit/talli/'. $stable['tnro'] . '/kasvattajanimet')?>">Kasvattajanimet</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kilpailut'){echo "active";}?>"><a href="<?php echo base_url('tallit/talli/'. $stable['tnro'] . '/kilpailut')?>">Kilpailut ja näyttelyt</a></li>
    </ul>
    
    <?php
        if($sivu == 'hevoset')
        {
            echo $horses;
        }
        else if($sivu == 'kasvatit'){
            echo $foals;

        } 
            else if($sivu == 'kasvattajanimet'){
                    echo $names;

        }
    
    ?>
</div>


