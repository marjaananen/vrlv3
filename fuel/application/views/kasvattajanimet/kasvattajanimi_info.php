<table class="table table-striped">
   <tr><th scope="row">Talli</th><td><?php if(isset($nimi['tnro'])) {echo $nimi['tnro'];}?></td></tr>
    <tr><th scope="row">Rekisteröity</th><td><?php if(isset($nimi['rekisteroity'])){ echo $nimi['rekisteroity'];}?></td></tr>
    <tr><th scope="row">Omistaja(t)</th><td> <?php
            $first = true;
            foreach($owners as $o)
            {
                if($first)
                {
                    echo $o['nimimerkki'] . " (<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
                    $first = false;
                }
                else
                    echo ", " . $o['nimimerkki'] . " (<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
            }
        ?></td></tr></table>