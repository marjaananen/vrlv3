

        
		




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
		