<h2>Vastuutehtävät</h2>

<?php
if($oma){

echo '<div class="alert alert-warning">
  <strong>Huom!</strong> Ei-toiminnassa olevat jaokset ja yhdistykset näkyvät vain käyttäjälle itselleen ja VRL:n ylläpidolle.
</div>';

    
}?>
<?php
if(!(isset($stats['jaokset']) && sizeof($stats['jaokset']) > 0)
   && !(isset($stats['puljut']) && sizeof($stats['puljut']) > 0)
   && !(isset($vastuut) && sizeof($vastuut) > 0)){
    
    ECHO '<P>Käyttäjällä ei ole vastuutehtäviä VRL:ssä.</p>';
}

if(isset($stats['jaokset']) && sizeof($stats['jaokset']) > 0){
   echo "<h3>Jaokset</h3>";
   echo '<table class="table table-condensed">
   <tr>
     <th>#</th>
     <th>Toiminnassa</th>
     <th>Rooli</th>
   </tr>';
   
   foreach ($stats['jaokset'] as $jaos){
    if($oma || $jaos['toiminnassa'] == 1){
        echo '<tr><td>';
        echo $jaos['nimi'] . " (" . $jaos['lyhenne'] . ")";
        echo '</td><td>';
        if($jaos['toiminnassa'] == 1){
           echo "kyllä";
        }else {
           echo "ei";
        }
        echo '</td><td>';
        
        if($jaos['taso'] == 1){
           echo "Ylläpitäjä";
        } else {
           echo "Työntekijä";
        }
        echo '</td></tr>';
    }
   }

echo '</table>';
}


 if(isset($stats['puljut']) && sizeof($stats['puljut']) > 0){
   echo "<h3>Alayhdistykset</h3>";
   echo '<table class="table table-condensed">
   <tr>
     <th>#</th>
     <th>Toiminnassa</th>
     <th>Rooli</th>
   </tr>';
   
   foreach ($stats['puljut'] as $jaos){
    if($oma || $jaos['toiminnassa'] == 1){
      echo '<tr><td>';
      echo $jaos['nimi'] . " (" . $jaos['lyhenne'] . ")";
      echo '</td><td>';
       if($jaos['toiminnassa'] == 1){
         echo "kyllä";
      }else {
         echo "ei";
      }
      echo '</td><td>';
      
      if($jaos['taso'] == 1){
         echo "Ylläpitäjä";
      } else {
         echo "Työntekijä";
      }
      echo '</td></tr>';
    }
   }

echo '</table>';
 }


if(isset($vastuut) && sizeof($vastuut) > 0){
   echo '<h3>Muut vastuutehtävät</h3>';
   echo '<ul>';
   
   foreach ($vastuut as $vastuu){
      echo '<li>' . $vastuu['description'] . ' ('.$vastuu['name'].')</li>';
      
   }
   echo '</ul>';
   
}
?>