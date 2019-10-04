
<p>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', 'Tähdellä merkittyjä kenttiä ei voi jättää tyhjäksi.')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php
        echo fuel_var('form', '');
        echo "<br />";
        echo fuel_var('append', '');
    ?>
</p>