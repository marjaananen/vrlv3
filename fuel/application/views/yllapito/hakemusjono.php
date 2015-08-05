<h2>Hakemusjono</h2>

<?php if ($view_status === 'queue_status') : ?>
    <p>
        Jonossa on <?php echo $queue_length; ?> hakemusta.
    </p>
    <?php
        echo fuel_var('get_next_application', '');
    ?>
<?php elseif ($view_status === 'next_join_application'): ?>
    
    <div class="application_data">
        <h3>Hakemuksen tiedot</h3>
        
        <p>Nimimerkki: <?=$application_data['nimimerkki']?></p>
        <p>Sähköpostiosoite: <?=$application_data['email']?></p>
        <p>Syntymäaika: <?=$application_data['syntymavuosi']?></p>
        <p>Sijainti: <?=$application_data['sijainti']?></p>
        <p>Rekisteröitymisaika: <?=$application_data['rekisteroitynyt']?></p>
        <p>IP-osoite: <?=$application_data['ip']?></p>
    </div>
    
    //luo hyväksy hylkää seuraava napit
    
<?php else: ?>
    <div class="alert alert-danger" role="alert">
        <p>
            Tämä alue on vain ylläpidolle.
        </p>
    </div>
<?php endif; ?>
