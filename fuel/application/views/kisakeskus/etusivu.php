<h1>Kilpailukalenteri</h1>


<?php if ($edit_tools){ echo "<h2>EDIT TOOLS</h2> <P><a href=\"kisat/kisaeditori\">LISÄÄ KUTSU</a>. POISTO ON EDITORILINKKI + /1/SURE<br /><a href=\"kisat/cuptuloksia\">CUPPI</a></P>";}?>

<p>Tältä sivulta löydät kaikki Karus hevosurheilukeskuksen tiloissa tai muuten yhteistyössä keskuksen kanssa järjestetyt kilpailut. Järjestämme vain porrastettuja kilpailuja! Jos kutsuissa näkyy virheellisiä osallistumisia, otathan yhteyttä kilpailukeskuksen ylläpitoon osoitteella marsupieni@gmail.com.</p>

<?php
	echo '<table border="0" width="100%" class="table" id="lista">';
	echo '<thead><tr><th class="tk">Pvm</td><th class="tk">Vip</td><th class="tk">Laji</th><th class="tk">Järjestäjä/järjestyspaikka</th><th class="tk">Kutsu</th></tr></thead><tbody>';
	echo '';
	
foreach($kisat as $kisa){

		$id = $kisa["id"];
		$pvm = $kisa["pvm"];
		$vip = $kisa["vip"];
		$laji = $kisa["laji"];
		$otsikko = $kisa["otsikko"];
		
		if ($kisa["oma"] == 0){
			$paikka =  $kisa["talli_nimi"];
			$paikka_url = $kisa["talli_url"];;
			$user = $kisa["username"];
		}
		
		else {
			$paikka = "Karkurannan ponitalli";
			$paikka_url = base_url();
			$user = "Marsupieni";
		}

		
		if ($kisa['hilight_notnumbered'] == true){
			echo '<tr class="varattuponi">';
			$vip = "<b>" .  $vip . "</b>";
		}
		
		else if ($kisa['hilight_vip'] == true){
			echo '<tr class="myyntiponi">';
			$vip = "<b>" .  $vip . "</b>";
		}
		
		else if ($kisa['downhilight_pvm'] == true){
			echo '<tr class="mennyt">';
		}
		else {
			echo "<tr>";
		}
		echo "<td>$pvm</td><td>$vip</td><td>$laji</td><td><a href=\"".$paikka_url."\">$paikka</a> ($user)</td><td><a href=\"kisakeskus/k/$id\">$otsikko</a></td>";
		echo "</tr>";
	}
	echo "</tbody></table>";
	
?>
<div id="pageNavPosition" style="padding-top: 20px" align="center">
</div>

