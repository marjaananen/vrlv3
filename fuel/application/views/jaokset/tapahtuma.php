<h2>Tapahtuma: <?=$tapahtuma['otsikko']?> (<?php echo $jaos['lyhenne']; ?>)</h2>


<?php if(isset($form)){
    echo "<h3>Muokkaa tapahtuman tietoja</h3>";
    echo $form;
}?>

<h3>Poista tapahtuma</h3>
<p>Voit poistaa vain tapahtumia joissa ei ole palkittuja hevosia!</p>
<p><a href="<?php echo site_url($delete_url);?>"><button type="button" class="btn btn-warning">Poista tapahtuma</button></a></p>



<?=$palkitut?>