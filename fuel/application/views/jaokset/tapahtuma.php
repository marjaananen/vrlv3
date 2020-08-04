<h2>Tapahtuma: <?=$tapahtuma['otsikko']?> (<?php echo $jaos['lyhenne']; ?>)</h2>


<?php if(isset($form)){
    echo "<h3>Muokkaa tapahtuman tietoja</h3>";
    echo $form;
}?>

<?php if(isset($horse_form)){
    echo "<h3>Lisää palkittu hevonen</h3>";

    echo $horse_form;
}?>


<?=$palkitut?>