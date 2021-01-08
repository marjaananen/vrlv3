<h2><?=$tapahtuma['otsikko']?> (<?php echo $jaos['lyhenne']; ?>)</h2>


<table class="table table-striped">
 	<tr>
		<th scope="row">Tapahtumapäivä</th> <td> <?php echo date('d.m.Y', strtotime($tapahtuma['pv'])); ?></td>
		<th scope="row">Tapahtuman järjestäjä</th> <td> <?php echo $jaos['nimi']; ?> (<?php echo $jaos['lyhenne']; ?>)</td>
	</tr>
	<tr>
		<th scope="row">Vastuuhenkilö</th> <td> <a href="<?php echo site_url('tunnus/'.$tapahtuma['vastuu']); ?>">VRL-<?php echo $tapahtuma['vastuu']; ?></a></td>
		<th scope="row">Tyyppi</th> <td> <?php echo $tapahtumatyyppi; ?></td>
	</tr>
</table>
<?php
echo $tapahtuma['info'];
?>

<H3>Palkitut</h3>


<?=$palkitut?>