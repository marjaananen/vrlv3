<h2><?=$title ?></h2>

<p>Kilpailukalenterissa näytetään kaikki avoimet kilpailut, joista ei vielä ole tullut tuloksia.
Porrastetut ja perinteiset sekä tarina- ja kysymyskilpailut ovat omilla välilehdillään. Tuloksellisia kilpailuja voit selata tulosarkistosta.
Kalenterissa on vain VRL:n alaisten kilpailujaosten alaiset kilpailut. </p>


 <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'porrastetut'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/kilpailukalenteri/porrastetut'); ?>">Porrastetut</a></li>
        <li role="presentation" class="<?php if ($sivu == 'perinteiset'|| empty($sivu)){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/kilpailukalenteri/perinteiset'); ?>">Perinteiset</a></li>
        <li role="presentation" class="<?php if ($sivu == 'tarinalliset'){echo "active";}?>"><a href="<?php echo base_url('kilpailutoiminta/kilpailukalenteri/tarinalliset'); ?>">Tarina ja kysymykset</a></li>

    </ul>
    
    <?=$kalenteri ?>