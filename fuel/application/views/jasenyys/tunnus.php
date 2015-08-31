<h2><?=$tunnus?>: <?=$nimimerkki?></h2>

<div class="container">
    <p><b>Nimimerkki:</b> <?=$nimimerkki?></p>
    <p><b>Sähköpostiosoite:</b> <?=$email?></p>
    <p><b>Syntymäaika:</b> <?=$syntymavuosi?></p>
    <p><b>Sijainti:</b> <?=$sijainti?></p>
    <p>
        <b>Muut yhteystiedot:</b>
        <?php
            if(empty($muut_yhteystiedot))
                echo "-";
                
            echo "<ul>";
            
            foreach($muut_yhteystiedot as $my)
            {
                echo "<li><b>" . $my['tyyppi'] . ": </b>" . $my['tieto'] . "</li>";
            }
            
            echo "</ul>";
        ?>
    </p>
</div>

