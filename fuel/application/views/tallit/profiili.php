<h2>Talliprofiili: <?=$stable['nimi']?></h2>

<div class="container">
    <p><b>Tallitunnus:</b> <?=$stable['tnro']?></p>
    <p><b>Rekisteröity:</b> <?=$stable['perustettu']?></p>
    <p><b>Kotisivu:</b> <a href="<?=$stable['url']?>"><?=$stable['url']?></a></p>
    <p><b>Kuvaus:</b> <?=$stable['kuvaus']?></p>
    <p><b>Kategoriat:</b>
        <?php
            $first = true;
            foreach($categories as $c)
            {
                if(count($categories) == 1 || $loggedin_owner == false)
                {
                    if($first)
                    {
                        echo $c['katelyh'];
                        $first = false;
                    }
                    else
                        echo ", " . $c['katelyh'];
                }
                else
                {
                    if($first)
                    {
                        echo $c['katelyh'] . "<a href='" . site_url("tallit/poista_kategoria") . "/" . $c['id'] . "'>(Poista kategoria)</a>";
                        $first = false;
                    }
                    else
                        echo ", " . $c['katelyh'] . "<a href='" . site_url("tallit/poista_kategoria") . "/" . $c['id'] . "'>(Poista kategoria)</a>";
                }
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
    
    <p>
        <?php
            if($loggedin_owner)
            {
                echo "<br />";
                echo "<a href='" . site_url('tallit/rekisteroi_kategoria') . "/" . $stable['tnro'] . "'>Ano uutta kategoriaa tallille</a>";
            }
        ?>
    </p>
</div>