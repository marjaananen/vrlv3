<h2><?=$tapahtuma['otsikko']?> (<?php echo $jaos['lyhenne']; ?>)</h2>
 <?php
 if (isset($msg)){
    ?>
    
       <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
        
        <?php if(isset($msg_details)){
            foreach ($msg_details as $detail){
                echo "<br># ".$detail;
            }
        }?>
		
		    </div>
    
<?php
 }?>


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

<?php if(isset($form)){
    echo "<h3>Muokkaa tapahtuman tietoja</h3>";
    echo $form;
}?>

<?php
if(isset($delete_url)){
	?>
	<h3>Poista tapahtuma</h3>
	<p>Voit poistaa vain tapahtumia joissa ei ole palkittuja hevosia!</p>
	<p><a href="<?php echo site_url($delete_url);?>"><button type="button" class="btn btn-warning">Poista tapahtuma</button></a></p>
	
	<?php
} ?>








<?php
echo $tapahtuma['info'];
?>

<H3>Palkitut</h3>


<?=$palkitut?>