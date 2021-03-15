<h1>Omat kilpailut</h1>

<p>Tältä sivulta näet kaikki omat kilpailusi sekä lähettämäsi tulokset. Tämän sivun kautta voit lähettää tuloksia ja poistaa jonossa olevia kutsuja ja tuloksia. Vuosia vanhoja tuloksellisia kilpailuja on silloin tällöin poistettu kalenterista. Etuuspisteet ja hevosten ansaitsemat sijoitukset yms. kuitenkin säilyvät. </p>


<?php if(strlen(fuel_var('msg', '')) > 0){ ?>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php } ?>
    
 <h2>Kilpailut</h2>


 <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($category == 'kisat' && $sivu == 'jonossa'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/kisat/jonossa'); ?>">Kutsujonossa</a></li>
        <li role="presentation" class="<?php if ($category == 'kisat' && $sivu == 'porrastetut'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/kisat/porrastetut'); ?>">Avoimet (porrastetut)</a></li>
        <li role="presentation" class="<?php if ($category == 'kisat' && $sivu == 'avoimet'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/kisat/avoimet'); ?>">Avoimet (perinteiset)</a></li>
        <li role="presentation" class="<?php if ($category == 'kisat' && $sivu == 'tulosjonossa'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/kisat/tulosjonossa'); ?>">Tulosjonossa</a></li>
        <li role="presentation" class="<?php if ($category == 'kisat' && $sivu == 'menneet'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/kisat/menneet'); ?>">Tulokselliset</a></li>
        

 </ul>
 
 
 <?php if($sivu == 'porrastetut' || $sivu == 'avoimet'){?>
<p> <div class="alert alert-warning" role="alert">   <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>

<b>Huom!</b> Vanhantyyppisistä porrastetuista tulosten lähetys onnistuu seuraavalla osoitteella (loppuosan [id] korvataan kyseisen kilpailun ID:llä): <br />
 <b>http://virtuaalihevoset.net/kilpailutoiminta/ilmoita_tulokset/<i>[id]</i></b></p>
    </div>
 
 <?php }?>
    
    <?php if(isset($kisat)){
        echo $kisat;
    }?>


<h2>Näyttelyt</h2>

 <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($category == 'nayttelyt' && $sivu == 'jonossa'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/nayttelyt/jonossa'); ?>">Kutsujonossa</a></li>
        <li role="presentation" class="<?php if ($category == 'nayttelyt' && $sivu == 'avoimet'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/nayttelyt/avoimet'); ?>">Avoimet</a></li>
        <li role="presentation" class="<?php if ($category == 'nayttelyt' && $sivu == 'tulosjonossa'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/nayttelyt/tulosjonossa'); ?>">Tulosjonossa</a></li>
        <li role="presentation" class="<?php if ($category == 'nayttelyt' && $sivu == 'menneet'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/omat/nayttelyt/menneet'); ?>">Tulokselliset</a></li>
        

 </ul>
    
    
    <?php if(isset($nayttelyt)){
        echo $nayttelyt;
    }?>