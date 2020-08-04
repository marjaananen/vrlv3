<h2>Kilpailutulos #<?=$tulos['tulos_id'] ?> (Kilpailu #<?=$tulos['kisa_id']?>)</h2>

<p><a href="<?php echo site_url(); ?>kilpailutoiminta/tulosarkisto">Takaisin tulosarkistoon</a></p>
<hr />

<?php

        $kisapv = date("d.m.Y",strtotime($tulos['kp']));
		$vip = date("d.m.Y",strtotime($tulos['vip']));
		$ilmoitettu = date("d.m.Y",strtotime($tulos['ilmoitettu']));
		
		// Haetaan jaoksen nimi
		$jaos_nimi = $tulos['jaos_info']['lyhenne'];

?>
        <p><label for="jaos">Jaos:</label> <span><?php print $jaos_nimi; ?></span></p>   
		<p><label for="url">Kutsun URL:</label> <span><a href="<?php print $tulos['url']; ?>"><?php print $tulos['url']; ?></a></span></p>   
		<p><label for="kp">Kisapäivä / VIP:</label> <span><?php print $kisapv; ?> / <?php print $vip; ?></span></p> 

        <p>
            <label for="talli">Järjestänyt talli:</label> 
			<span><a href="http://www.virtuaalihevoset.net/?tallit/tallirekisteri/talli.html?talli=<?php print $tulos['jarj_talli']; ?>">
			<?php print $tulos['talli_info']['nimi'].' ('.$tulos['jarj_talli'].')'; ?></a></span>
		</p>
        
        <p>
			<label for="arvontatapa">Arvontatapa:</label> 
			<span>
			<?php
            
            $arvontatavat = $tulos['jarjestelma']->arvontatavat_options_legacy();
				if( empty($tulos['arvontatapa'] || !isset($arvontatavat[$tulos['arvontatapa']]) )) {
					print 'Arvontatapaa ei ole valittu';
				} else {
					echo $arvontatavat[$tulos['arvontatapa']];
				}
			?>
			</span>
		</p>
        
        

		
	<hr />
	<?php
		######## Haetaan vastuuhenkilön tiedot
				?>
		<p>
			<label for="ilmoittaja">Vastuuhenkilö:</label> 
			<span>
				<a href="<?php echo site_url(); echo "tunnus/"; echo $tulos['tunnus']; ?>">
				VRL-<?php print $tulos['tunnus']; ?>
				</a>
			</span>
		</p>

		
	<?php 
		######## Tulokset lähetti
						?>
		<p>
			<label for="ilmoittaja">Tulokset lähetti:</label> 
			<span>
				<a href="<?php echo site_url(); echo "tunnus/"; echo $tulos['tulosten_lah']; ?>">VRL-<?php print $tulos['tulosten_lah']; ?>

				</a> 
			</span>
		</p>
	<?php 
		
		######## Tulokset hyväksyi
		
	?>
		<p>
			<label for="ilmoittaja">Tulokset hyväksyi:</label> 
			<span>
				<?php 
					if ( !empty ($tulos['hyvaksytty']) ) {
						print	date("d.m.Y", strtotime($tulos['hyvaksytty'])).' ';
                        
						print	'<a href="'.site_url()."tunnus/". 'VRL-'.
								$tulos['hyvaksyi'].'">'.'VRL-'.$tulos['hyvaksyi'].'</a>';
					} else {
						print '<strong>Tuloksia ei ole vielä hyväksytty!</strong>';
					}
				?> 				
			</span>
		</p>
        
        <hr />



<?php
    $luokkien_max = 40;

    
## Tulosten näyttö

	# Rikotaan ensin luokat rivinvaihdon kohdalta ja filtteröidään tyhjät pois
	$luokat = explode("\n",$tulos['luokat']);
	$luokat = preg_grep('/^\s*\z/', $luokat, PREG_GREP_INVERT);
	$luokat = array_values( array_filter($luokat) );
	
	$luokkien_maara = sizeof($luokat);

	# Sitten rikotaan tulokset ~- merkin kohdalta, eli mikä merkitsee luokan loppua
	$tulokset = explode("~",$tulos['tulokset']);

	# Luokan hylätyt, ~ merkin kohdalta
	$hylsyt = explode("~",$tulos['hylatyt']);
				
	if($luokkien_maara >= $luokkien_max) { $luokkien_maara = $luokkien_max; }
	print '<strong>Luokkien määrä: </strong> <span id="luokkien_maara">'.$luokkien_maara.' kpl</span>';
	
		// Luokkien alle niiden tulokset
		for($i = 0; $i < $luokkien_maara; $i++) {


			$voittajat = explode("\n",$tulokset[$i]);
			$voittajat = preg_grep('/^\s*\z/', $voittajat, PREG_GREP_INVERT);
			$voittajat = array_values( array_filter($voittajat) );
			
			$voittajat_maara = sizeof($voittajat);
			$osallistujia = $voittajat_maara;
			
            //katsotaan sijoittuneiden määrä
            $sijoittuu = $tulos['jarjestelma']->sijoittuu($osallistujia, $tulos['jaos']);

			print	'<p>'.
					'<h3>'.$luokat[$i].'</span> (<span id="luokan_max_os" style="margin:0; padding:0;">'.$osallistujia.'</span> osallistujaa)</h3> ';
					
				
			for($j = 0; $j < $voittajat_maara; $j++) {
				if($sijoittuu > $j) {
					print '<strong>'.$voittajat[$j]."</strong> <br />";
				} else {
					print $voittajat[$j]."<br />";
				}
			}
			
			print '</p>';
			
			
			$hylatyt = explode("\n",$hylsyt[$i]);
			$hylatyt = preg_grep('/^\s*\z/', $hylatyt, PREG_GREP_INVERT);
			$hylatyt = array_values( array_filter($hylatyt) );
			$hylatyt_maara = sizeof($hylatyt);

			if( ($hylatyt_maara) > 0) {
				
				print	'<p><strong>Hylätyt</strong><br />';
				for($k = 0; $k < $hylatyt_maara; $k++) {
					if( !empty($hylatyt[$k]) ) {
						print $hylatyt[$k]."<br />";
					} else {
						print '';
					}
				}
				
			} else {
			}
			
			print '</p>';
			
		}
		