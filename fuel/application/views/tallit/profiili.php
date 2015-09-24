<h2>Talliprofiili: <?=$stable['nimi']?></h2>

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
        <?php if($liked_status === 'notset') : ?>
            Kirjaudu sisään tykätäksesi tallista.
        <?php elseif($liked_status == false) : ?>
            <a href="<?php echo site_url('tallit/tykkaa') . '/' . $stable['tnro'] . '/' . '1'; ?>">Tykkää tallista</a>
        <?php else : ?>
            <a href="<?php echo site_url('tallit/tykkaa') . '/' . $stable['tnro'] . '/' . '-1'; ?>">Vedä tykkäyksesi pois</a>
        <?php endif; ?>
    </p>
</div>



