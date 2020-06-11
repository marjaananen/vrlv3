<h2>Värien periytymislaskuri</h2>
 <script src="<?php echo base_url();?>assets/js/periytymisjavascript.js"></script>


<?php
if($haku){
 echo '<div class="panel-group">';
 echo '<div class="panel panel-info">';
 echo '<div class="panel-heading isa">Is&auml;n v&auml;ri: <span id="isavari"></span></div>';
 echo '<div class="panel-body">';
 echo '<form id="varit" action="'. current_url().'" method="post">';
 	$inheritance_printer->tulostalista("isa[]");		
 echo '</div></div>';
 echo '<div class="panel panel-danger">';
 echo '<div class="panel-heading ema">Em&auml;n v&auml;ri: <span id="emavari"></span></div>';
 echo '<div class="panel-body">';
 	$inheritance_printer->tulostalista("ema[]");	
 echo '</div></div></div>';
 echo '<input type="submit" value="generoi varsan v&auml;rit"> <input type="reset" value="tyhjenn&auml; valinnat">';
 echo '</form>';
}

else if($tulos){
 echo '<div class="panel panel-success">';
 echo '<div class="panel-heading varsa">Varsan v&auml;rivaihtoehdot:</div>';
 echo '<div class="panel-body">';
 	$inheritance_printer->tulosta_varsatulos($baby, $varmalla);
  $inheritance_printer->tulosta_varsalisatiedot($baby);
 
	
	echo '</div></div>';
 
 echo '<p><a href="'.current_url().'">Palaa alkuun</a></p>';
 
}
?>

<?php echo fuel_var('text_view', "")?>

<?php
 
 
 

?>
 

 



