<h2>Omat kilpailut</h2>

<p>Tältä sivulta näet kaikki omat kilpailusi, sekä lähettämäsi tulokset. Tämän sivun kautta voit lähettää tuloksia ja poistaa jonossa olevia kutsuja ja tuloksia. Vuosia vanhoja tuloksellisia kilpailuja on silloin tällöin poistettu kalenterista. Etuuspisteet ja hevosten ansaitsemat sijoitukset yms. kuitenkin säilyvät. </p>


<?php if(strlen(fuel_var('msg', '')) > 0){ ?>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php } ?>

 <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'jonossa'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/jonossa'); ?>">Kutsujonossa</a></li>
        <li role="presentation" class="<?php if ($sivu == 'porrastetut'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/porrastetut'); ?>">Avoimet (porrastetut)</a></li>
        <li role="presentation" class="<?php if ($sivu == 'avoimet'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/avoimet'); ?>">Avoimet (perinteiset)</a></li>
        <li role="presentation" class="<?php if ($sivu == 'tulosjonossa'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/tulosjonossa'); ?>">Tulosjonossa</a></li>
        <li role="presentation" class="<?php if ($sivu == 'omat'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/menneet'); ?>">Tulokselliset</a></li>
        

    </ul>
    
    
    <?php if(isset($kisat)){
        echo $kisat;
    }?>
