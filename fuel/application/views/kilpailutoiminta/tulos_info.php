
<table class="table table-striped">
 	<tr>
		<th scope="row">Kilpailu</th> <td> #<?php echo $tulos['kisa_id']; ?></td>
		<th scope="row">Tulos</th> <td> #<?php echo $tulos['tulos_id']; ?></td>
	</tr>
	<tr>
		<th scope="row">Kisapäivä</th> <td> <?php echo date('d.m.Y', strtotime($tulos['kp'])); ?></td>
		<th scope="row">VIP</th> <td> <?php echo date('d.m.Y', strtotime($tulos['vip'])); ?></td>
	</tr>
	<tr>
		<th scope="row">Jaos</th> <td> <?php echo $tulos['jaos_info']['nimi']." (".$tulos['jaos_info']['lyhenne'].")"; ?></td>
		<th scope="row">Arvontatapa</th> <td> <?php
            
            $arvontatavat = $tulos['jarjestelma']->arvontatavat_options_legacy();
				if( empty($tulos['arvontatapa'] || !isset($arvontatavat[$tulos['arvontatapa']]) )) {
					print 'Arvontatapaa ei ole valittu';
				} else {
					echo $arvontatavat[$tulos['arvontatapa']];
				}
			?></td>
	</tr>

	<tr>
		<th scope="row">Vastuuhenkilö</th> <td> <?php echo $tulos['tunnus']->nimimerkki; ?>,  <a href="<?php echo site_url('tunnus/'.$tulos['tunnus']->tunnus); ?>">VRL-<?php echo $tulos['tunnus']->tunnus; ?></a> </td>
			<th scope="row">Järjestävä talli</th> <td><a href="<?php print site_url('tallit/talli/'.$tulos['jarj_talli']); ?>">
			<?php print $tulos['talli_info']['nimi'].' ('.$tulos['jarj_talli'].')'; ?></a></td>
		
	</tr>
	<tr>
	<?php
	$url = "";
	if($tulos['porrastettu'] == 1 && !isset($tulos['url']) && strlen($tulos['url']) < 5) { $url = site_url('kilpailutoiminta/k/'.$tulos['kisa_id']); }
	else { $url = $tulos['url']; }
	?>
		<th scope="row">Kutsu</th> <td> <span><a href="<?php print $url; ?>">www</a></td>
		<th scope="row">Info</th> <td> <?php echo $tulos['info']; ?></td>
	</tr>
	<tr>
		<th scope="row">Tulokset lähetetty</th> <td>
		<?php echo date('d.m.Y', strtotime($tulos['ilmoitettu'])); ?><br />
		<?php echo $tulos['tulosten_lah']->nimimerkki; ?>, 
		<a href="<?php echo site_url('tunnus/'.$tulos['tulosten_lah']->tunnus); ?>">VRL-<?php echo $tulos['tulosten_lah']->tunnus; ?></a>
		
		<?php if(isset($tulos['takaaja']) && $tulos['takaaja']->tunnus != '00000'){?>
					(Takaaja: <?php echo $tulos['takaaja']->nimimerkki;?>,
					<a href="<?php echo site_url('tunnus/'.$tulos['takaaja']); ?>">VRL-<?php echo $kutsu['takaaja']->tunnus; ?></a>)					
					<?php } ?>
		</td>

		<th scope ="row">Tulokset hyväksytty</th> <td>
			<?php 
					if ( !empty ($tulos['hyvaksytty']) && isset($tulos['hyvaksyi'])) {
						print	date("d.m.Y", strtotime($tulos['hyvaksytty'])).'<br />';
                        
						print	$tulos['hyvaksyi']->nimimerkki . ' <a href="'.site_url()."tunnus/". 'VRL-'.
								$tulos['hyvaksyi']->tunnus.'">'.'VRL-'.$tulos['hyvaksyi']->tunnus.'</a>';
					} else {
						print '<strong>Tuloksia ei ole vielä hyväksytty!</strong>';
					}
				?> 		
		</td>
	</tr>
			

			
</table>