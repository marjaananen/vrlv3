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
    <p><b>Tykkäykset:</b> <?=$likes?></p>
    
    <br />
    <p>
        <?php if($liked_date === 'notset') : ?>
            Kirjaudu sisään tykätäksesi tallista.
        <?php elseif($liked_date == '0000-00-00') : ?>
            <a href="<?php echo site_url('tallit/tykkaa') . '/' . $stable['tnro'] . '/' . '1'; ?>">Tykkää tallista</a>
        <?php else : ?>
            <a href="<?php echo site_url('tallit/tykkaa') . '/' . $stable['tnro'] . '/' . '-1'; ?>">Vedä tykkäyksesi pois</a> (tykkäsit tallista <?php echo date("d.m.Y", strtotime($liked_date)); ?>)
        <?php endif; ?>
    </p>
</div>



