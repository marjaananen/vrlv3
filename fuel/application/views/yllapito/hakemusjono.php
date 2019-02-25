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
        Jonossa on <?php echo $queue_length; ?> hakemusta, joista <?php echo $queue_length-$queue_unlocked_num; ?> on lukittuna.
        <?php
            if($queue_length > 0)
            {
                echo "Vanhin hakemus on lähetetty " . $oldest_application . ".</p><p>";
            }
        ?>
    </p>
<?php elseif ($view_status === 'next_join_application'): ?>
    
    <div class="container">
        <h3>Hakemuksen tiedot</h3>
        
        <p>Nimimerkki: <?=$application_data['nimimerkki']?></p>
        <p>Sähköpostiosoite: <?=$application_data['email']?></p>
        <p>Syntymäaika: <?=$application_data['syntymavuosi']?></p>
        <p>Sijainti: <?=$application_data['sijainti']?></p>
        <p>Rekisteröitymisaika: <?=$application_data['rekisteroitynyt']?></p>
        <p>IP-osoite: <?=$application_data['ip']?></p>
    </div>
    
    <?php if(!empty($same_ip_logins)) : ?>
        <p>
            Viimeisimmät kirjautumiset samasta IP:stä:
            <ul>
                <?php
                    foreach($same_ip_logins as $sil)
                    {
                        echo "<li><a href='" . site_url('/tunnukset/tunnus') . "/" . $sil['tunnus'] . "'>" . $sil['nimimerkki'] . " (" . $sil['aika'] . ")</a></li>";
                    }
                ?>
            </ul>
        </p>
    <?php endif; ?>
    
    <?php if(!empty($same_nicknames)) : ?>
        <p>
            Käyttäjät samalla nimimerkillä:
            <ul>
                <?php
                    foreach($same_nicknames as $sn)
                    {
                        echo "<li><a href='" . site_url('/tunnukset/tunnus') . "/" . $sn['tunnus'] . "'>VRL-" . $sn['tunnus'] . "</a></li>";
                    }
                ?>
            </ul>
        </p>
    <?php endif; ?>
    
    <?php if(!empty($same_dateofbirths)) : ?>
        <p>
            Käyttäjät samalla syntymäpäivällä:
            <ul>
                <?php
                    foreach($same_dateofbirths as $sd)
                    {
                        echo "<li><a href='" . site_url('/tunnukset/tunnus') . "/" . $sd['tunnus'] . "'>" . $sd['nimimerkki'] . "</a></li>";
                    }
                ?>
            </ul>
        </p>
    <?php endif; ?>
        
    <br />
    
    <p>
        <form method="post" action="<?=site_url('/yllapito_tunnukset/kasittele_hakemus')?>/hyvaksy/<?=$application_data['id']?>">
            <input type="submit" value="Hyväksy">
        </form>
        
        <form method="post" action="<?=site_url('yllapito/tunnukset/kasittele/')?>/hylkaa/<?=$application_data['id']?>">
            Hylkäyssyy: <input type="text" name="rejection_reason">
            <input type="submit" value="Hylkää">
        </form>
        
        <form method="post" action="<?=site_url('/yllapito/hakemusjono')?>">
            <input type="submit" value="Ohita ja ota seuraava">
        </form>
    </p>
<?php endif; ?>
