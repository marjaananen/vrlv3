<h1>Salasanan palauttaminen</h1>
<p>Ilmoita VRL-tunnuksesi, niin saat ohjeet salasanan palauttamiseksi siihen sähköpostiosoitteeseen, joka on rekisteröity kyseiselle tunnukselle.</p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("auth/forgot_password");?>

      <p>
      	<label for="identity">VRL-</label> <br />
      	<?php echo form_input($identity);?>
      </p>

      <p><?php echo form_submit('submit', "Lähetä");?></p>

<?php echo form_close();?>
