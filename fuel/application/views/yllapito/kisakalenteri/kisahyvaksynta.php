<h1>Hyväksy kilpailuja kalenteriin</h1>
<?php if(strlen(fuel_var('msg', '')) > 0){ ?>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php } ?>
<?php

if(!isset($kutsu) || sizeof($kutsu)== 0){

echo "<div class=\"alert alert-success\" role=\"alert\"> <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
  Kaikki kilpailuanomukset on käyty läpi tai käsittelyssä!</div>";
				 }
                 
 else {
		
	?>
		
<table class="table table-striped">
 	<tr><th scope="row">#</th> <td> <?php echo $kutsu['kisa_id']; ?></td></tr>
	<tr><th scope="row">Kisapäivä</th> <td> <?php echo date('d.m.Y', strtotime($kutsu['kp'])); ?></td></tr>
	<tr><th scope="row">VIP</th> <td> <?php echo date('d.m.Y', strtotime($kutsu['vip'])); ?></td></tr>
	<tr><th scope="row">Jaos</th> <td> <?php echo $jaos['nimi']." (".$jaos['lyhenne'].")"; ?></td></tr>
	<tr><th scope="row">Laji</th> <td> <?php echo $kutsu['laji']; ?></td></tr>
		<tr><th scope="row">Arvontatapa</th> <td> <?php echo $kutsu['arvontatapa']; ?></td></tr>

		<tr><th scope="row">Vastuuhenkilö</th> <td> <?php echo $username; ?>,  <a href="<?php echo site_url('tunnus/'.$user_vrl); ?>"><?php echo $user_vrl; ?></a> </td></tr>
<tr><th scope="row">Järjestävä talli</th> <td> <?php echo '<a href="'. $talli['url'] . '">'.$talli['nimi'].'</a>, 
                <a href="'.site_url('tallit/talli/'. $kutsu['jarj_talli']). '">'.$kutsu['jarj_talli'].'</a>';?></td></tr>
	<tr><th scope="row">Kutsu</th> <td> <a href="<?php echo $kutsu['url']; ?>">www</a></td></tr>
		<tr><th scope="row">Info</th> <td> <?php echo $kutsu['info']; ?></td></tr>
			<tr><th scope="row">Ilmoitettu</th> <td> <?php echo date('d.m.Y', strtotime($kutsu['ilmoitettu'])); ?></td></tr>
			<?php if(isset($kutsu['takaaja']) && $kutsu['takaaja'] != '00000'){?>
					<tr class="warning"><th scope="row">Takaaja</th> <td> <a href="<?php echo site_url('tunnus/'.$kutsu['takaaja']); ?>">VRL-<?php echo $kutsu['takaaja']; ?></a> </td></tr>
					
					<?php } ?>
			

			
</table>



	



							
	<?php				
					echo '<form method="post" action="'.site_url('yllapito/kalenterit/kisahyvaksynta/'.$kutsu['jaos'].'/hylkaa/'.$kutsu['kisa_id']).'">
					<div class="form">
							<div class="panel panel-default">
									<div class="panel-body">';
												echo '<a href="'.site_url('yllapito/kalenterit/kisahyvaksynta/'.$kutsu['jaos'].'/hyvaksy/'.$kutsu['kisa_id']).'">'.
										'<button type="button" class="btn btn-success">Hyväksy</button></a> ';
					
											echo '<a href="'.site_url('yllapito/kalenterit/kisahyvaksynta/'.$kutsu['jaos']).'">'.
										'<button type="button" class="btn btn-warning">Ohita</button></a> ';
										
										echo '<input type="submit" name="Hylkää" value="Hylkää" id="Hylkää" class="btn btn-danger"/>';

												echo '<div class="form-group">
                <input type="hidden" name="kisa_id" id="kisa_id" value="'.$kutsu['kisa_id'] .'" class="field_type_hidden form-control" />                          
            </div>
            <div class="form-group">
               <label for="viesti" id="label_viesti">Hylkäyksen syy<span class="required">*</span></label>
															<input type="text" name="viesti" id="viesti" value="" class="field_type_text form-control" size="40" required  />

            </div>
									</div>   
						</div>
			</div>
</form>';

}


?>