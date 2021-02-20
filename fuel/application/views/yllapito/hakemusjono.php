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
        
        <table class="table table-striped">
        <tr><th scope="row">Nimimerkki</th> <td><?=$application_data['nimimerkki']?></td></tr>
        <tr><th scope="row">Sähköpostiosoite</th> <td><?=$application_data['email']?></td></tr>
        <tr><th scope="row">Rekisteröitymisaika</th> <td><?=$application_data['rekisteroitynyt']?></td></tr>
        <tr><th scope="row">IP-osoite</th> <td><?=$application_data['ip']?></td></tr>

    
    <?php if(!empty($same_ip_logins)) : ?>
        <tr><th scope="row">Viimeisimmät kirjautumiset samasta IP:stä</th> <td>
            
            <ul>
                <?php
                    foreach($same_ip_logins as $sil)
                    {
                        echo "<li><a href='" . site_url('/tunnukset/tunnus') . "/" . $sil['tunnus'] . "'>" . $sil['nimimerkki'] . " (" . $sil['aika'] . ")</a></li>";
                    }
                ?>
            </ul>
        </td></tr>
    <?php endif; ?>
    
    <?php if(!empty($same_nicknames)) : ?>
        <tr><th scope="row">
            Käyttäjät samalla nimimerkillä:</th><td>
            <ul>
                <?php
                    foreach($same_nicknames as $sn)
                    {
                        echo "<li><a href='" . site_url('/tunnukset/tunnus') . "/" . $sn['tunnus'] . "'>VRL-" . $sn['tunnus'] . "</a></li>";
                    }
                ?>
            </ul>
        </td></tr>
    <?php endif; ?>
    

        
    
    </table>
    </div>

							
	<?php				
			echo '<form method="post" action="'.site_url('/yllapito/tunnukset/kasittele').'/hylkaa/'.$application_data['id'].'">
					<div class="form">
							<div class="panel panel-default">
									<div class="panel-body">';
												echo '<a href="'.site_url('/yllapito/tunnukset/kasittele').'/hyvaksy/'.$application_data['id'].'">'.
										'<button type="button" class="btn btn-success">Hyväksy</button></a> ';
					
											echo '<a href="'.site_url('/yllapito/tunnukset/hyvaksy').'">'.
										'<button type="button" class="btn btn-warning">Ohita</button></a> ';
										
										echo '<input type="submit" name="Hylkää" value="Hylkää" id="Hylkää" class="btn btn-danger"/>';

            echo '
            <div class="form-group">
               <label for="viesti" id="label_viesti">Hylkäyksen syy<span class="required">*</span></label>
															<input type="text" name="rejection_reason" id="rejection_reason" value="" class="field_type_text form-control" size="40" required  />

            </div>
			</div>   
			</div>
			</div>
</form>';




?>
    
    
    
   
<?php endif; ?>
