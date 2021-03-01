<h2><?=$title ?></h2>

<p> Tulosarkistosta voit hakea ja tarkastella kilpailujen tuloksia. Tuloksista näytetään max. 1000 hakukriteereihin sopivaa tulosta kisapäivän mukaan uusimmasta alkaen.</p> 
<?php if (isset($id_form))
{ echo $id_form; }?>


 <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'porrastetut'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/tulosarkisto/porrastetut'); ?>">Porrastetut</a></li>
        <li role="presentation" class="<?php if ($sivu == 'perinteiset'|| empty($sivu)){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/tulosarkisto/perinteiset'); ?>">Perinteiset</a></li>
        <li role="presentation" class="<?php if ($sivu == 'tarinalliset'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/tulosarkisto/tarinalliset'); ?>">Tarina ja kysymykset</a></li>
        <li role="presentation" class="<?php if ($sivu == 'nayttelyt'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/tulosarkisto/nayttelyt'); ?>">Näyttelyt</a></li>

    </ul>
    
    <?=$kalenteri ?>