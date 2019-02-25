
<?php echo form_open('kisakeskus/lisaa_kutsu'); ?>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="porrastettu" value="1">
	   
<?php

if($message_type == 'danger'){
	   
	     echo "<div class=\"alert alert-danger\" role=\"alert\"> <span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom:</span> ". $message."</div>";
 
	   
}

else if($message_type == 'success'){
	   
	     echo "<div class=\"alert alert-success\" role=\"alert\"> <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom:</span> ". $message."</div>";
 
}

?>
 <table id="perustiedot">
            <tr>
                <td class="tk">PVM</td> <td> <input type="text" name="pvm" value="<?php echo $pvm; ?>"> <br />(vvvv-kk-pp)</td>
                <td class="tk">VIP</td> <td><input type="text" name="vip" value="<?php echo $vip; ?>"><br />(vvvv-kk-pp)</td>
            </tr>
            <tr>
                <td class="tk" colspan ="2">Maksimiosallistujamäärä</td><td colspan ="2"> <input type="text" name="max_os_luokka" value="<?php echo $max_os_luokka; ?>"></td>
	    </tr>
		   <tr>
                <td class="tk" colspan ="2">Ratsastajalta hevosia/luokka</td><td colspan ="2"> <input type="text" name="max_hevo_luokka" value="<?php echo $max_hevo_luokka; ?>"></td>
	    </tr>   <tr>
                <td class="tk" colspan ="2">Hevosen maksimistarttimäärä</td><td colspan ="2"> <input type="text" name="max_start_hevo" value="<?php echo $max_start_hevo; ?>"></td>
	    </tr>
		<tr>
                <td class="tk" colspan ="2">Otsikko</td><td colspan ="2"> <input type="text" name="otsikko" value="<?php echo $otsikko; ?>"></td>
	    </tr>
		
	   <tr>
                <td class="tk" colspan ="2">Järjestävän tallin nimi</td><td colspan ="2"> <input type="text" name="talli_nimi" value="<?php echo $talli_nimi; ?>"></td>
	    </tr>
	   <tr>
                <td class="tk" colspan ="2">Järjestävän tallin VRL-koodi</td><td colspan ="2"> <input type="text" name="talli_vrl" value="<?php echo $talli_vrl; ?>"></td>
	    </tr>
	   	   <tr>
                <td class="tk" colspan ="2">Järjestävän tallin url</td><td colspan ="2"> <input type="text" name="talli_url" value="<?php echo $talli_url; ?>"></td>
	    </tr>
	   <tr>
                <td class="tk" colspan ="2">Kutsu ID</td><td colspan ="2"> <input type="text" name="VRL_kisa_id" value="<?php echo $VRL_kisa_id; ?>"> (Lisätään jälkikäteen kun kutsu kalenterissa)</td>
	    </tr>
            <tr>
                <td class="tk">Luokat</td>
		
		<td colspan = "3">
		      <?php
		      
		      if ($id == -1){
				 $lajike = "";
				 foreach ($luokat as $luokka){
					    if ($lajike !== $luokka['laji']){
						       echo "<br /><b>" . $luokka['laji'] . "</b><br />";
						       $lajike = $luokka['laji'];
						       
						       
					    }
					    echo "<input type=\"checkbox\" name=\"luokat[]\" value=\"" . $luokka['luokka_id'] . "\"";
					    
					    if (array_search($luokka['luokka_id'], $valitut_luokat) !== false){
						   echo " checked";    
					    }
					    
					    echo ">" . $luokka['teksti'] . " vt. " . $luokka['porr_vaikeus'] . "<br />";	 				 
				 }
		      }
		      
		      else {
				 echo "Kutsun luokkia ei voi muoksia jälkikäteen!<br />";
				 $int = 1;
				 foreach ($luokat as $luokka){
					    
					    echo $int . ". " . $luokka['teksti'] . "<br />";
					    $int ++;
				 }				 
		      }
		      ?>
		
                    </td>
            </tr>
	</table>
<?php echo form_submit('mysubmit','Submit!');  ?>
<?php echo form_close(); ?>