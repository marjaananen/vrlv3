<h1>Statistiikkaa</h1>
<p>Kilpailukeskuksella on järjestetty <b><?php echo $kisamaara; ?></b> kilpailua, joissa on ollut yhteensä <b><?php echo $luokkamaara; ?></b> luokkaa.
Kilpailuissamme on tehty  <b><?php echo $osallistujamaara; ?></B> starttia, ja niissä on käynyt yhteenssä <B><?php echo $kisaajamaara; ?></B> eri ratsastajaa ja <B><?php echo $hevosmaara; ?></B> eri hevosta ja ponia.
Keskimäärin yhdessä kilpailussa on pidetty noin <b><?php echo round($luokkamaara/$kisamaara); ?></b> luokkaa, ja yhdessä luokassa on ollut keskimäärin <b><?php echo round($osallistujamaara/$luokkamaara); ?></b> starttia.
Yksi hevonen on startannut keskimäärin <b><?php echo round($osallistujamaara/$hevosmaara); ?></b> kertaa, ja yksi ratsastaja keskimäärin <b><?php echo round($osallistujamaara/$kisaajamaara); ?></b> kertaa.
<h2>Järjestetyistä kilpailuista</h2>
<table>
	<tr>
		<td></td><td class="tk">Järjestetyt kilpailut</td><td class="tk">Järjestetyt luokat</td><td class="tk">Keskimäärin luokkia/kisa</td>
	</tr>
	<tr>
		<td class="tk">Esteratsastus</td><td><?php echo $kisamaara_lajit['este']['kisat']; ?></td><td><?php echo $kisamaara_lajit['este']['luokat']; ?></td><td><?php echo round($kisamaara_lajit['este']['luokat']/$kisamaara_lajit['este']['kisat']); ?></td>
	</tr>
		<tr>
		<td class="tk">Kouluratsastus</td><td><?php echo $kisamaara_lajit['koulu']['kisat']; ?></td><td><?php echo $kisamaara_lajit['koulu']['luokat']; ?></td><td><?php echo round($kisamaara_lajit['koulu']['luokat']/$kisamaara_lajit['koulu']['kisat']); ?></td>
	</tr>
	<tr>
		<td class="tk">Kenttäratsastus</td><td><?php echo $kisamaara_lajit['kentta']['kisat']; ?></td><td><?php echo $kisamaara_lajit['kentta']['luokat']; ?></td><td><?php echo round($kisamaara_lajit['kentta']['luokat']/$kisamaara_lajit['kentta']['kisat']); ?></td>
	</tr>
	<tr>
		<td class="tk">Valjakkoajo</td><td><?php echo $kisamaara_lajit['valjakko']['kisat']; ?></td><td><?php echo $kisamaara_lajit['valjakko']['luokat']; ?></td><td><?php echo round($kisamaara_lajit['valjakko']['luokat']/$kisamaara_lajit['valjakko']['kisat']); ?></td>
	</tr>
</table>
<table>
	<tr>
		<td>
		<h2>TOP10 aktiviset kisaajat</h2>
<?php
$i = 1;
foreach ($parhaat_kisaajat as $rivi){
		echo $i . ". <a href=\"http://www.virtuaalihevoset.net/?vrl/jaesenyys/profiili.html?hlo=" . substr($rivi['VRL'], 4) . "\">". $rivi['VRL']."</a> (" . $rivi['kpl'] . " starttia)<br />";
		$i++;
	}
?>	
		</td>
		
		<td>
			<h2>TOP10 aktiviset hevoset</h2>
<?php
$i = 1;
foreach ($parhaat_hevoset as $rivi){
		echo $i . ". <a href=\"http://www.virtuaalihevoset.net/?hevoset/hevosrekisteri/hevonen.html?vh=" . $rivi['VH'] . "\">". $rivi['VH']."</a> (" . $rivi['kpl'] . " starttia)<br />";
		$i++;
	}
?>
		</td>
	</tr>
</table>


<h2>Osallistujien määrä luokittain</h2></h2>
<p>Alle on listattu luokat järjestyksessä sen mukaan, kuinka monta osallistujaa niihin keskimäärin on osallistunut.</p>
<table width="100%">

	<tr>
		<td class="tk">Esteratsastus</td>

		
	</tr>
	<tr>
		<td><?php tulosta_listake2('este', 'fav', $luokkainfo_part);?></td>
	</tr>
	
	<tr>
		<td class="tk">Kouluratsastus</td>

		
	</tr>
	<tr>
		<td><?php tulosta_listake2('koulu', 'fav', $luokkainfo_part);?></td>
	</tr>
	
	
	<tr>
		<td class="tk">Kenttäratsastus</td>

		
	</tr>
	<tr>
		<td><?php tulosta_listake2('kentta', 'fav', $luokkainfo_part);?></td>
	</tr>
		
	
	<tr>
		<td class="tk">Valjakkoajo</td>

		
	</tr>
	<tr>
		<td><?php tulosta_listake2('valjakko', 'fav', $luokkainfo_part);?></td>
	</tr>
		
</table>

<h2>Järjestetyistä luokista</h2></h2>
<table width="100%">

	<tr>
		<td class="tk" rowspan="4">Esteratsastus</td>

		<td class="tk">TOP5 järjestetyt luokat</td>
	</tr>
	<tr>
		<td><?php tulosta_listake('este', 'fav', $luokkainfo);?></td>
	</tr>
		<tr>
		<td class="tk">TOP5 harvoinjärjestetyt luokat</td>
	</tr>
	<tr>
		<td><?php tulosta_listake('este', 'hate', $luokkainfo);?></td>
	</tr>
	
	<tr>
		<td class="tk" rowspan="4">Kouluratsastus</td>

		<td class="tk">TOP5 järjestetyt luokat</td>
	</tr>
	<tr>
		<td><?php tulosta_listake('koulu', 'fav', $luokkainfo);?></td>
	</tr>
		<tr>
		<td class="tk">TOP5 harvoinjärjestetyt luokat</td>
	</tr>
	<tr>
		<td><?php tulosta_listake('koulu', 'hate', $luokkainfo);?></td>
	</tr>
	
		<tr>
		<td class="tk" rowspan="4">Kenttäratsastus</td>

		<td class="tk">TOP5 järjestetyt luokat</td>
	</tr>
	<tr>
		<td><?php tulosta_listake('kentta', 'fav', $luokkainfo);?></td>
	</tr>
		<tr>
		<td class="tk">TOP5 harvoinjärjestetyt luokat</td>
	</tr>
	<tr>
		<td><?php tulosta_listake('kentta', 'hate', $luokkainfo);?></td>
	</tr>
	
		<tr>
		<td class="tk" rowspan="4">Valjakkoajo</td>

		<td class="tk">TOP5 järjestetyt luokat</td>
	</tr>
	<tr>
		<td><?php tulosta_listake('valjakko', 'fav', $luokkainfo);?></td>
	</tr>
		<tr>
		<td class="tk">TOP5 harvoinjärjestetyt luokat</td>
	</tr>
	<tr>
		<td><?php tulosta_listake('valjakko', 'hate', $luokkainfo);?></td>
	</tr>
</table>


<?php function tulosta_listake($laji, $mika, $luokkainfo){
	$i = 1;
	foreach ($luokkainfo[$laji][$mika] as $rivi){
		echo $i . ". " . $rivi['teksti'] . " (" . $rivi['kpl'] . "kpl)<br />";
		$i++;
	}
}
?>


<?php function tulosta_listake2($laji, $mika, $luokkainfo){
	$i = 1;
	foreach ($luokkainfo[$laji][$mika] as $rivi){
		echo $i . ". " . $rivi['teksti'] . " (keskimäärin " . round($rivi['prosentti']) . "/100kpl)<br />";
		$i++;
	}
}
?>


