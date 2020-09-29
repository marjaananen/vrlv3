<h1>Hyväksy tulos arkistoon</h1>
<?php if(strlen(fuel_var('msg', '')) > 0){ ?>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php } ?>
<?php

if(!isset($tulos) || sizeof($tulos)== 0){

echo "<div class=\"alert alert-success\" role=\"alert\"> <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
  Kaikki tulosanomukset on käyty läpi tai käsittelyssä!</div>";
				 }
                 
 else {
		
	?>
		
<?php echo $tulos_info; ?>
<?php echo $luokat_info; ?>



	



							
	<?php				
					echo '<form method="post" action="'.site_url('yllapito/kalenterit/tuloshyvaksynta/'.$tulos['jaos'].'/hylkaa/'.$tulos['kisa_id']).'">
					<div class="form">
							<div class="panel panel-default">
									<div class="panel-body">';
												echo '<a href="'.site_url('yllapito/kalenterit/tuloshyvaksynta/'.$tulos['jaos'].'/hyvaksy/'.$tulos['kisa_id']).'">'.
										'<button type="button" class="btn btn-success">Hyväksy</button></a> ';
					
											echo '<a href="'.site_url('yllapito/kalenterit/tuloshyvaksynta/'.$tulos['jaos']).'">'.
										'<button type="button" class="btn btn-warning">Ohita</button></a> ';
										
										echo '<input type="submit" name="Hylkää" value="Hylkää" id="Hylkää" class="btn btn-danger"/>';

												echo '<div class="form-group">
                <input type="hidden" name="kisa_id" id="kisa_id" value="'.$tulos['kisa_id'] .'" class="field_type_hidden form-control" />                          
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