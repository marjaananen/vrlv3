
        
        
        
    <h2>Muokkaa jaosta <?=$jaos['nimi']?> (<?=$jaos['lyhenne']?>)</h2>


   <ul class="nav nav-tabs">
      <li role="presentation" class="<?php if ($sivu == 'tiedot'){echo "active";}?>"><a href="<?php echo base_url($url . 'tiedot')?>">Tiedot</a></li>
      <li role="presentation" class="<?php if ($sivu == 'saannot'){echo "active";}?>"><a href="<?php echo base_url($url . 'saannot')?>">Säännot</a></li>
      <li role="presentation" class="<?php if ($sivu == 'luokat'){echo "active";}?>"><a href="<?php echo base_url($url . 'luokat')?>">Luokat</a></li>
      <li role="presentation" class="<?php if ($sivu == 'ominaisuudet'){echo "active";}?>"><a href="<?php echo base_url($url . 'ominaisuudet')?>">Ominaisuudet</a></li>
      <li role="presentation" class="<?php if ($sivu == 'omistajat'){echo "active";}?>"><a href="<?php echo base_url($url . 'omistajat')?>">Ylläpito</a></li>
      <li role="presentation" class="<?php if ($sivu == 'online'){echo "active";}?>"><a href="<?php echo base_url($url . 'online')?>">Toiminnassa</a></li>
    </ul>

   
   <?php


   
       if (isset($msg)){
         echo '<div class="alert alert-'. fuel_var('msg_type', 'info') . '" role="alert">';
         echo fuel_var('msg', '');
         echo validation_errors();
         echo '</div>';
         
         
       }
       
       
       echo fuel_var('info', '');
       
       if($sivu == 'luokat'){
         echo '<p>Tältä sivulta voit hallita jaoksesi luokkia. Järjestysnumerolla voit valita, missä järjestyksessä luokat esitetään sääntölistauksissa (pienin ylimpänä). Huom! Luokkia, joilla on järjestetty jo kilpailuja, ei voi poistaa.
         Ne voi kuitenkin merkitä pois käytöstä, jolloin ne eivät näy sääntölistauksissa eikä niitä voi valita kilpailuihin.</p>';
         
         echo '<a href="'.base_url($url . $sivu . "/lisaa") . '">Lisää uusi luokka</a>';
       }
       
      else if($sivu == 'ominaisuudet'){
         echo '<p>Tältä sivuilta voit valita mitkä ominaisuudet vaikuttavat jaoksesi porrastetuissa kilpailuissa!
         Ominaisuuksia voi valita 2-4. Voit muokata tätä vain, jos jaoksen porrastetut kilpailut eivät ole vielä asetettu toimintaan.</p>';
                }
                
      else if($sivu == 'omistajat'){
         
      echo '<p>Jaoksella voi olla useita ylläpitäjiä (taso 1) ja kalenteriityöntekijöitä (taso 0).
      Ylläpitäjä pystyy muokkaamaan jaoksen sääntöjä, ylläpitäjiä ja työntekijöitä. Kalenterityöntekijällä on oikeus hyväksyä ja muokata kilpailuja ja tuloksia.</p>';
      }
       
       echo fuel_var('form', '');
       echo fuel_var('list', ''); 

 ?>