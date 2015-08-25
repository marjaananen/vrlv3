<h2>Hakemusjono</h2>

<?php if($this->session->flashdata('return_status') != '') : ?>
    <div class="alert alert-<?php echo $this->session->flashdata('return_status'); ?>" role="alert">
        <p>
            <?php echo $this->session->flashdata('return_info'); ?>
        </p>
    </div>
<?php endif; ?>

<?php if ($view_status === 'queue_status') : ?>
    <p>
        Viimeisimmät hyväksynnät:
        <ul>
            <?php
                foreach($latest_approvals as $la)
                {
                    echo "<li>" . $la['nimimerkki'] . " (" . $la['hyvaksytty'] . ")</li>";
                }
            ?>
        </ul>
    </p>
    <p>
        Viimeisimmät kirjautumiset:
        <ul>
            <?php
                foreach($latest_logins as $ll)
                {
                    echo "<li>" . $ll['nimimerkki'] . " (" . $ll['aika'] . ")</li>";
                }
            ?>
        </ul>
    </p>
    <p>
        Viimeisimmät epäonnistuneet kirjautumiset:
        <ul>
            <?php
                $date = new DateTime();
                foreach($latest_failed_logins as $lfl)
                {
                    $date->setTimestamp($lfl['time']);
                    echo "<li>" . $lfl['nimimerkki'] . " (" . $date->format('Y-m-d H:i:s') . ")</li>";
                }
            ?>
        </ul>
    </p>

    <br />
    
    <p>
        Jonossa on <?php echo $queue_length; ?> hakemusta.
        <?php
            if($queue_length > 0)
            {
                echo "Vanhin hakemus on lähetetty " . $oldest_application . ".</p><p>";
                echo fuel_var('get_next_application', '');
            }
        ?>
    </p>
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
        Hylkäyssyy: <input type="text" name="rejection_reason">
        <input type="submit" value="Hylkää">
    </form>
    
    <form method="post" action="<?=site_url('/yllapito/hakemusjono')?>">
        <input type="submit" value="Ohita ja ota seuraava">
    </form>
<?php endif; ?>
