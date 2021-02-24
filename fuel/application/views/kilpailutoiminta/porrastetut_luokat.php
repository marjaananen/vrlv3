     <h3>Porrastettujen kilpailujen luokat</h3>
     
     <div class="panel panel-default">
      <div class="panel-heading">Luokat</div>
      <div class="panel-body">
        <p>Porrastetuissa kilpailuissa voidaan järjestää VAIN alla olevia luokkia.</p>
        
        <?php
        foreach ($jaokset as $jaos){
            print_jaos($aste, $jaos);
        }
        
       ?>
        </table>      </div>
    </div>
     
     <?php
     function print_jaos($aste, $jaos){
    echo "<h3>".$jaos['jaos']['nimi']." (".$jaos['jaos']['lyhenne'].")</h3>";
    echo '<table class="table">
        <thead>
          <tr>
            <th scope="col">Luokka</th>
            <th scope="col">Taso</th>
            <th scope="col">Aste</th>
            <th scope="col">Minimisäkäkorkeus</th>
          </tr>
        </thead>';
    echo "<tbody>";
    foreach ($jaos['classes'] as $class){
        echo "<tr>";
        echo '<th scope="row">' . $class['nimi'] . "</th>";
        echo '<td>'.$class['taso'].'</td>';
        echo '<td>'.$aste[$class['aste']].'</td>';
        if(isset($class['minheight']) && strlen($class['minheight'])>2){
         echo '<td>'.$class['minheight'].' cm</td>';
        }else {
            echo "<td>-</td>";
        }
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
        
}
     ?>