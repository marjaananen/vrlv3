<h1>Porrastettujen kilpailulistat</h1>
<p>Tältä sivulta näet kaikki elävät hevosesi ja ponisi, jotka ovat yli kolmevuotiaita ja joille on merkitty kolmevuotissyntymäpäivä sekä säkäkorkeus rekisteriin. Hevoset on listattu yleisimpään osallistumismuotoon ja jaoteltu lajeittain saavutetun vaikeustason mukaan. Voit suodattaa hevosia painotuksen, kotitallin ja rodun mukaan.</p>
<p>Hevosten rekisterisivulta voit asettaa yksittäisille hevosille maksimitasot tai estää niiden näkymisen tällä listalla kokonaan merkitsemällä, ettei hevonen kilpaile porrastetuissa.</p>

<h2>Suodata hevosia</h2>
<?php echo $form; ?>



<h2>Listat</h2>


<?php foreach ($jaokset as $jaos){
echo '<button title="Click to show/hide content" type="button" onclick="PonyListToggler (\''.$jaos['lyhenne'].'\')">Näytä '.$jaos['lyhenne'].'</button> ';
}
?>

<?php

foreach ($jaokset as $jaos){
	echo "<div id=\"". $jaos['lyhenne'] . "_list\" style=\"display:none\">";
	echo "<h2>". $jaos['lyhenne'] . " kilpailulista </h2>";

	if(isset($printArray[$jaos['lyhenne']])){
		$laji = $printArray[$jaos['lyhenne']];
		ksort($laji);
	
		foreach ($laji as $taso => $ponit){
			echo "<h3>Vaikeustaso ". $taso . "</h3><p>";
				foreach ($ponit as $poni){
					echo $poni;
				}
		echo "</p>";
		}
	}else {
		echo "<p>Ei kilpailevia hevosia  (".$jaos['lyhenne'].")</p>";
	}
	echo "<p><h3>Odottavat hevoset</h3></p><p>";
	if(isset($printArrayWaitlist[$jaos['lyhenne']])){
		foreach ($printArrayWaitlist[$jaos['lyhenne']] as $poni){
			echo $poni;
			
		}
	}else {
				echo "Ei odottavia hevosia (".$jaos['lyhenne'].")";

	}
	echo "</p><p><h3>Valmiit hevoset</h3></p><p>";

	if(isset($printArrayReadylist[$jaos['lyhenne']])){
		foreach ($printArrayReadylist[$jaos['lyhenne']] as $poni){
			echo $poni;
			
		}
	}else {
				echo "Ei valmiita hevosia (".$jaos['lyhenne'].")";

	}
	
	echo "</p></div>";	
}


	
?>


<script>
function PonyListToggler (tunnus){
	console.log(tunnus);
	//Suljetaan kaikki
    //
    <?php foreach ($jaokset as $jaos){
	echo "document.getElementById('".$jaos['lyhenne']."_list').style.display='none';";
    }
    ?>
	
	var kentta = tunnus + "_list";
	console.log(kentta);
	//Avataan klikattu
	if(document.getElementById(kentta).style.display=='none') {
        document.getElementById(kentta).style.display='';}
    else{document.getElementById(kentta).style.display='none';}
	}
</script>