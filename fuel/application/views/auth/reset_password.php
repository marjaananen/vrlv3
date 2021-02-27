<h1>Vaihda salasana</h1>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open('auth/reset_password/' . $code);?>

	<p>
		<label for="new_password">Uusi salasana (min. <?php $min_password_length; ?> merkkiä)</label> <br />
		<?php echo form_input($new_password);?>
	</p>

	<p>
		<label for="new_password_confirm">Uusi salasana uudelleen</label><br />
		<?php echo form_input($new_password_confirm);?>
	</p>

	<?php echo form_input($user_id);?>
	<?php echo form_hidden($csrf); ?>

	<p><?php echo form_submit('submit', "Lähetä");?></p>

<?php echo form_close();?>