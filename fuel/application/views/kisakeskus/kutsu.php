
<h2><?php if ($porrastettu == 1) { echo "Porrastetut " . $laji . "kilpailut";} else { echo ucfirst ($laji) . "kilpailut";}?></h2>

 <table id="perustiedot">
            <tr>
                <td class="tk">Päivämäärä</td> <td> <?php echo $pvm; ?></td>
                <td class="tk">Vip</td> <td><?php echo $vip; ?></td>
            </tr>
	                <tr>
                <td class="tk">Vastuuhenkilö</td> <td> <?php echo $username; ?>,  <?php echo $user_vrl; ?> <br /><?php echo $user_email; ?></td>
                <td class="tk">Järjestävä talli</td> <td><?php if ($oma === 1){ echo 'Karkurannan Ponitalli, <a href="http://www.virtuaalihevoset.net/?tallit/tallirekisteri/talli.html?talli=KARK4835">KARK4835</a>';} else {echo '<a href="'. $talli_url . '">'.$talli_nimi.'</a><br />  <a href="http://www.virtuaalihevoset.net/?tallit/tallirekisteri/talli.html?talli='. $talli_vrl . '">'.$talli_vrl.'</a>';}?></td>
            </tr>
			<tr>
	   <td class="tk">Tyyppi</td> <td> Porrastettu</td>
                <td class="tk">Arvontatapa</td> <td>Suhteutettu arvonta</td>
			</tr>
           </table>
                <h2>Rajoitukset</h2>
	   
		      Luokat ovat avoinna kaikille, ellei toisin sanota.<br />
		      <?php echo $max_hevo_luokka; ?> hevosta / luokka / <?php if ($laji == 'valjakko') { echo "henkilö"; } else {echo "ratsastaja";} ?><br />
		      Hevonen voi osallistua <?php echo $max_start_hevo; ?> luokkaan<br />
		      Hevonen voi osallistua vain kerran yhteen luokkaan<br />
		      Luokkiin otetaan <?php echo $max_os_luokka; ?> <?php if ($laji == 'valjakko') { echo "valjakkoa"; } else {echo "ratsukkoa";} ?><br />
		      KILPAILUUN SAA OSALLISTUA VASTA KUN KUTSU LÖYTYY KALENTERISTA!

		
<h2>
                Luokat</h2>
		
		      <?php
				 $int = 1;
				 foreach ($luokat as $luokka){
					    echo "Luokka " . $int . ". " . $luokka['teksti'] . ",  vt. " . $luokka['porr_vaikeus'];
					    if ($laji == 'valjakko' && $luokka['russeille'] == 0){
						       
						       echo ", avoin";
						       
					    }					    

					    echo " (osallistujat " . sizeof($luokka['osallistujat']) . "/" . $max_os_luokka . ")";
					    

					    
					    if ($luokka['russeille'] == 1){
						       
						       echo " VAIN RUSSPONEILLE!";
						       
						       if ($luokka['karus_cup'] > 0){
						       
						       echo " Karus Cup osakilpailu.";
						       
						       }
						       
					    }
					    

					    
					    if (sizeof($luokka['osallistujat']) == $max_os_luokka){
						       
						       echo " <b>Täynnä</b>";
						       
					    }
					    

					    
					    echo "<br />";
					    $int ++;
				 }		      
		      ?>
		<h2>Osallistuminen</h2>
		
		      
		   
		    
		    
		    <?php
		      
		      $ExpDate = new DateTime($vip_sql);
		      $Today = new DateTime(date("Y-m-d"));
		      $interval = $ExpDate->diff($Today);
		      $interval = $interval->format('%R%a days');  //<--- to diffenernce by days.
		      
		      if ($VRL_kisa_id == 0 || $VRL_kisa_id ==""){
				 echo "<div class=\"alert alert-danger\" role=\"alert\"> <span class=\"glyphicon glyphicon-fire\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom:</span> Kilpailunjärjestäjä ei ole lisännyt kutsun tietoihin VRL:n kisa-id:tä. Tuloslinkki ei siis tule sivulle automaattisesti. Osallistuminen on silti sallittua, mikäli kilpailu on VRL:n kalenterissa.</div>";
 
				 
		      }

		      
		      
		      if ($interval > 0){
				  echo "<div class=\"alert alert-danger\" role=\"alert\"> <span class=\"glyphicon glyphicon-time\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom:</span> VIP on mennyt, ja osallistuminen on sulkeutunut!</div>";
 
				 $json = file_get_contents('http://www.virtuaalihevoset.net/?rajapinta/kisat_tulos.html?kisa_id=' . $VRL_kisa_id);
				 $obj = json_decode($json, true);
				 if ($obj['error'] == 0){
					    $tulosten_id = $obj['result_id'];
					    echo "<div class=\"alert alert-success\" role=\"alert\"> <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom:</span><a href=\"http://www.virtuaalihevoset.net/?kilpailut-ja-valmennukset/kilpailukalenteri/tulosarkisto/tulos.html?id=" .$tulosten_id. "\">Tulokset</a> ovat tulleet.</div>";
				 }
				 
				 else if ($VRL_kisa_id == 0 || $VRL_kisa_id ==""){
					     echo "<div class=\"alert alert-warning\" role=\"alert\"> <span class=\"glyphicon glyphicon-info-sign\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom:</span>Tuloslinkki tulee tähän, jos kutsun VRL-id asetetaan.</div>";
				 }
				 else if ($obj['error_code'] == 404){
					    echo "<div class=\"alert alert-warning\" role=\"alert\"> <span class=\"glyphicon glyphicon-info-sign\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom:</span>Tuloksia ei ole vielä hyväksytty tai laitettu jonoon.</div>";
				 }
				 
				 else {
					    echo "<div class=\"alert alert-warning\" role=\"alert\"> <span class=\"glyphicon glyphicon-info-sign\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom:</span> Virhe VRL:n tuloslinkkihaussa, tarkasta myöhemmin uudelleen!</div>";
				 }
				 

				 
		      }
		      
		      else {
				 
				 ?>
		
		 <b>Osallistumismuoto: </b><br />
		    <pre>
		      Ratsastaja (VRL-00000) - Hevonen VH00-000-0000
		      Ratsastaja (VRL-00000) - Hevonen VH00-000-0000</pre>
		    Viisinumeroinen VRL-tunnus, ja täydellinen VH tunnus ovat pakollisia.<br /><br />
		    
		    <?php

				 echo "<b>Osallistumislomake: </b><br />";
				 echo form_open('kisakeskus/osallistu');
				 echo "<input type=\"hidden\" name=\"id\" value=\"" .  $id . "\">";
				 if (sizeof($error) > 0){
					    echo "<b>VIRHEELLISIÄ OSALLISTUMISIA!</b><ul>";
					    foreach ($error as $virhe){
					    echo "<li>" . htmlspecialchars($virhe) . "</li>";
					    
					    
					    }
					    echo "</ul>";
					    
					    echo "Hyväksytyt osallistumiset lisättiin listaan, jos niitä oli. <br /><br />";
				 }
				 $int = 1;
				 foreach ($luokat as $luokka){
					    echo "Luokka " . $int . ". " . $luokka['teksti'] . ", vt. " . $luokka['porr_vaikeus'] ."<br />";
					    $int ++;
					    
					    echo "<textarea rows=\"5\" cols=\"60\" name=\"". $luokka['kisaluokka_id'] . "\"></textarea><br />";
				 }
				 
				 echo '<input type="checkbox" name="send_email"> Lähetä ilmoittautumisesta kopio sähköpostiin. <br /> Sähköpostiosoitteesi (jos tahdot kuittauksen): <input type="text" name="email"><br />';    
	   
				 
				 echo form_submit('mysubmit','Osallistu!');
				 echo form_close();
				 }
		      
		      ?>
		      
		</td>
	    </tr>
	</table>
<h2>Osallistujat</h2>
 
 
 	   <?php
	   $int = 1;
	   foreach ($luokat as $luokka){
		      echo "<b>Luokka " . $int . ". " . $luokka['teksti'] . ", vt. " . $luokka['porr_vaikeus'] ."  (" . sizeof($luokka['osallistujat']) . "/" . $max_os_luokka . ")</b><p>";
		      $int ++;
		      				 foreach ($luokka['osallistujat'] as $osallistuja){
					    echo htmlspecialchars($osallistuja['rimpsu']) . "<br />";						       
				 }
					    
		      echo "</p>";
	   }		      
	    ?>
