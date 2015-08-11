<h2>Liity jäseneksi</h2>

<p>
    <div class="alert alert-<?php echo fuel_var('join_msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('join_msg', 'Tähdellä merkityt kentät ovat pakollisia! Rekisteröitymällä joudut jonoon.')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php
        echo fuel_var('join_form', '');
    ?>
</p>