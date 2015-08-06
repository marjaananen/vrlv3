<h2>Hakemusjono</h2>

<p>
    //tämän sivun css pitäisi uusia bootstrappiin
    <br>
    //myös routet pitää korjata
</p>

<?php if($this->session->flashdata('return_status') != '') : ?>
    <div class="alert alert-<?php echo $this->session->flashdata('return_status'); ?>" role="alert">
        <p>
            <?php echo $this->session->flashdata('return_info'); ?>
        </p>
    </div>
<?php endif; ?>

<?php if ($view_status === 'queue_status') : ?>
    <p>
        Jonossa on <?php echo $queue_length; ?> hakemusta.
    </p>
    <?php
        if($queue_length > 0)
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
    
    <form method="post" action="<?=site_url('/yllapito/kasittele_hakemus')?>/hyvaksy/<?=$application_data['id']?>">
        <input type="submit" value="Hyväksy">
    </form>
    
    <form method="post" action="<?=site_url('/yllapito/kasittele_hakemus')?>/hylkaa/<?=$application_data['id']?>">
        <input type="submit" value="Hylkää">
    </form>
    
    <form method="post" action="<?=site_url('/yllapito/hakemusjono')?>">
        <input type="submit" value="Ohita ja ota seuraava">
    </form>
    
<?php else: ?>
    <div class="alert alert-danger" role="alert">
        <p>
            Tämä alue on vain ylläpidolle.
        </p>
    </div>
<?php endif; ?>
