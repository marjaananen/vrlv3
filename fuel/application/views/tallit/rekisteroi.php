<h2>Rekisteröi talli</h2>

<p>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', 'Tähdellä merkityt kentät ovat pakollisia! Rekisteröimisen jälkeen ylläpito käsittelee anomuksesi. Muista, että tallin kaikilta pääsivuilta tulee olla löydettävissä sana "virtuaalitalli"! Tallin omistajaksi merkitään rekisteröintihakemuksen lähettäjä. Voit lisätä tallille lisää omistajia rekisteröinnin jälkeen.')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php
        echo fuel_var('form', '');
    ?>
</p>