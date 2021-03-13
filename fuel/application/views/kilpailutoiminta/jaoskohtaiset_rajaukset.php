<table class="table">
    <thead>
      <tr>
        <th>Jaos</th>
        <th>Sallittu luokkamäärä / kisa</th>
        <th>Sallittu ratsukkomäärä / luokka</th>
        <th>Sallittu lähtömäärä / hevonen / kisa</th>
      </tr>
    </thead>
    <tbody>
    <?php
    foreach ($jaokset as $jaos){
      echo "<tr>";
      echo "<td><b>".$jaos['lyhenne']."</b></td>";
    print_rule($jaos['s_luokkia_per_kisa_min'], $jaos['s_luokkia_per_kisa_max_norm'], $jaos['s_salli_porrastetut'], $jaos['s_luokkia_per_kisa_min'], $jaos['s_luokkia_per_kisa_max']);
    print_rule($jaos['s_hevosia_per_luokka_min'], $jaos['s_hevosia_per_luokka_max']);
    print_rule($jaos['s_luokkia_per_hevonen_min'], $jaos['s_luokkia_per_hevonen_max']);
      echo "</tr>";
    }
      ?>
    </tbody>
  </table>
  
  <?php
  function print_rule($rule_min, $rule_max, $porr = false, $porr_min=0, $porr_max=0){
    if($rule_min != $rule_max){
        echo "<td>".$rule_min." - ".$rule_max;
      }else {
        echo "<td>".$rule_max;
      }
      
      
      if($porr){
          echo ", porrastetut: ";
          if($porr_min != $porr_max){
          echo $porr_min." - ".$porr_max;
        }else {
          echo $porr_max;
        }
      }
       
      echo "</td>";
      
  }?>