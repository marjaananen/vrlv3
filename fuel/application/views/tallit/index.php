<h2>Omat tallit</h2>

<p>
    <a href="<?php echo site_url('/profiili/omat-tallit/rekisteroi')?>">Rekisteröi uusi talli</a>
</p>

<p>
    Tallisi: 
    <?php
        echo "<ul>";
        
        foreach($stables as $s)
        {
            echo "<li><b><a href='" . site_url('/tallit/talliprofiili') . '/' . $s['tnro'] . "'>" . $s['tnro'] . "</a>: </b>" . $s['nimi'] . " <a href='" . site_url('/profiili/omat-tallit/muokkaa/') . '/' . $s['tnro'] . '/edit' . "'>(Muokkaa tietoja)</a></li>";
        }
        
        echo "</ul>";
    ?>
</p>