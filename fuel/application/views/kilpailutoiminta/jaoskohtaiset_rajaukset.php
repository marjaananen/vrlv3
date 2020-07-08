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
    print_rule($jaos['s_luokkia_per_kisa_min'], $jaos['s_luokkia_per_kisa_max']);
    print_rule($jaos['s_hevosia_per_luokka_min'], $jaos['s_hevosia_per_luokka_max']);
    print_rule($jaos['s_luokkia_per_hevonen_min'], $jaos['s_luokkia_per_hevonen_max']);
      echo "</tr>";
    }
      ?>
    </tbody>
  </table>
  
  <?php
  function print_rule($rule_min, $rule_max){
    if($rule_min != $rule_max){
        echo "<td>".$rule_min." - ".$rule_max."</td>";
      }else {
        echo "<td>".$rule_max."</td>";
      }
      
  }?>