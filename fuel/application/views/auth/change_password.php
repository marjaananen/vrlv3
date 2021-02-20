<h1>Vaihda salasana</h1>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("auth/change_password");?>

      <p>
            <label for="old_password">Vanha salasana </label><br />
            <?php echo form_input($old_password);?>
      </p>

      <p>
            <label for="new_password">Uusi salasana (min. <?= $min_password_length;?> merkkiä)</label> <br />
            <?php echo form_input($new_password);?>
      </p>

      <p>
           <label for="new_password_confirm">Vahvista uusi salasana </label><br />
            <?php echo form_input($new_password_confirm);?>
      </p>

      <?php echo form_input($user_id);?>
      <p><?php echo form_submit('submit', "Lähetä");?></p>

<?php echo form_close();?>
