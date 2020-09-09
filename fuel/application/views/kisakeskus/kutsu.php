
<h2><?php echo "Kilpailukutsu (" . $laji . ")"; ?></h2>

 <table class="table table-striped">
                 <tr>
                <th scope="row">Jaos</th> <td> <?php echo $jaos['nimi']." (".$jaos['lyhenne'].")"; ?></td>
                <th scope="row">Arvontatapa</th> <td>Porrastettu (kts. <a href="<?php echo site_url('kilpailutoiminta/porrastetut');?>">säännöt</a>)</td>
            </tr>
            <tr>
                <th scope="row">Päivämäärä</th> <td> <?php echo $kp; ?></td>
                <th scope="row">VIP</th> <td><?php echo $vip; ?></td>
            </tr>
	                <tr>
                <th scope="row">Vastuuhenkilö</th> <td> <?php echo $username; ?>,  <a href="<?php echo site_url('tunnus/'.$user_vrl); ?>"><?php echo $user_vrl; ?></a> <br /><?php echo $user_email; ?></td>
                <th scope="row">Järjestävä talli</th> <td> <?php echo '<a href="'. $talli['url'] . '">'.$talli['nimi'].'</a><br />
                <a href="'.site_url('tallit/talli/'. $jarj_talli). '">'.$jarj_talli.'</a>';?></td>
            </tr>
                             <tr>
                <th scope="row">Kisa id</th> <td> #<?php echo $kisa_id; ?></td>
                <th scope="row">Tulokset</th> <td> <?php if(isset($tulos) && sizeof($tulos) > 0){
                 echo '#<a href="'.site_url('kilpailutoiminta/tulosarkisto/tulos/').'">'.$tulos['tulos_id'].'</a>';} else { echo "-" ;}?> </td>
            </tr>
		
           </table>
 
                <h2>Rajoitukset</h2>
	   
		      <strong><?php echo $s_max_hevo_luokka_per_user; ?></strong> hevosta / luokka / <?php if ($laji == 'valjakko') { echo "henkilö"; } else {echo "ratsastaja";} ?><br />
		      Hevonen voi osallistua <strong><?php echo $s_luokkia_per_hevonen; ?></strong> luokkaan<br />
		      Hevonen voi osallistua vain kerran yhteen luokkaan<br />
		      Luokkiin otetaan <strong><?php echo $s_hevosia_per_luokka; ?></strong> <?php if ($laji == 'valjakko') { echo "valjakkoa"; } else {echo "ratsukkoa";} ?><br />

		
<h2>
                Luokat</h2>
		
		      <?php
				 $int = 1;
				 foreach ($luokat as $luokka){
					    echo "Luokka " . $int . ". " . $luokka['nimi'] . ",  vt. " . $luokka['taso'];
					    if ($laji == 'valjakko' && $luokka['russeille'] == 0){
						       
						       echo ", avoin";
						       
					    }					    

					    echo " (osallistujat " . sizeof($luokka['osallistujat']) . "/" . $s_hevosia_per_luokka . ")";
					   
					    

					    
					    if (sizeof($luokka['osallistujat']) == $s_hevosia_per_luokka){
						       
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
		     
		      
		      if ($interval > 0){
				  echo "<div class=\"alert alert-danger\" role=\"alert\"> <span class=\"glyphicon glyphicon-time\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom:</span> VIP on mennyt, ja osallistuminen on sulkeutunut!</div>";
 
				 if (isset($tulos) && sizeof($tulos)>0){
					    $tulosten_id = $tulos['result_id'];
					    echo "<div class=\"alert alert-success\" role=\"alert\"> <span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom: Tulokset ovat tulleet ja ne löytyvät </span><a href=\"".site_url('kilpailutoiminta/tulosarkisto/tulos/'.$tulos['tulos_id'])." \">tulosarkistosta.</a></div>";
				 }
				 
				 else {
					    echo "<div class=\"alert alert-warning\" role=\"alert\"> <span class=\"glyphicon glyphicon-info-sign\" aria-hidden=\"true\"></span>
  <span class=\"sr-only\">Huom:</span>Tuloksia ei ole vielä hyväksytty.</div>";
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
				 echo form_open('kilpailutoiminta/osallistu/'.$id);
				 if (sizeof($error) > 0){
           echo "<div class=\"alert alert-warning\" role=\"alert\"> <span class=\"glyphicon glyphicon-info-sign\" aria-hidden=\"true\"></span>
        <b>Virheellisiä osallistumisia</b>";
					    echo "<ul>";
					    foreach ($error as $virhe){
					    echo "<li>" . htmlspecialchars($virhe) . "</li>";
					    
					    
					    }
					    echo "</ul></div>";
					    
					    echo "Hyväksytyt osallistumiset lisättiin listaan, jos niitä oli. <br /><br />";
				 }
				 $int = 1;
				 foreach ($luokat as $luokka){
					    echo "Luokka " . $int . ". " . $luokka['nimi'] . ", vt. " . $luokka['taso'] ."<br />";
					    $int ++;
					    
					    echo "<textarea rows=\"5\" cols=\"60\" name=\"". $luokka['kisaluokka_id'] . "\"></textarea><br />";
				 }
				 	   
				 
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
		      echo "<b>Luokka " . $int . ". " . $luokka['nimi'] . ", vt. " . $luokka['taso'] ."  (" . sizeof($luokka['osallistujat']) . "/" . $s_hevosia_per_luokka . ")</b><p>";
		      $int ++;
		      				 foreach ($luokka['osallistujat'] as $osallistuja){
					    echo htmlspecialchars($osallistuja['rimpsu']) . "<br />";						       
				 }
					    
		      echo "</p>";
	   }		      
	    ?>
