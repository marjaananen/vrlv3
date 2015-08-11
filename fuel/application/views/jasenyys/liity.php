<h2>Liity jäseneksi</h2>

<p>
    <div class="alert alert-<?php echo fuel_var('join_msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('join_msg', 'Tähdellä merkityt kentät ovat pakollisia! Rekisteröitymisen jälkeen saat sähköpostilla salasanan ja koodin, jolla aktivoida tunnuksesi. Huomaathan, että ylläpidon tulee tarkastaa hakemuksesi ennen kuin voit kirjautua! ')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php
        echo fuel_var('join_form', '');
    ?>
</p>