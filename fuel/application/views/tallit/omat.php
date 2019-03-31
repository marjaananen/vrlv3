<h2>Omat tallit</h2>



<p>
    <?php
        echo "<ul>";
        
        foreach($stables as $s)
        {
            echo "<li><b><a href='" . site_url('/tallit/talliprofiili/')  . $s['tnro'] . "'>" . $s['tnro'] . "</a>: </b>" . $s['nimi'] . " <a href='" . site_url('/profiili/tallit/muokkaa/') . $s['tnro'] . "'>(Muokkaa tietoja)</a></li>";
        }
        
        echo "</ul>";
    ?>
</p>