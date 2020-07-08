<?php    
    
    echo '<table class="table">
        <thead>
          <tr>
            <th scope="col">Jaos</th>
            <th scope="col">Pisteet</th>        
            <th scope="col">Taso</th>
          </tr>
        </thead>';
    echo "<tbody>";
    
    
foreach ($horse_levels as $id=>$level){
    echo "<tr>";
    echo "<td><b>". $id . "</b></td>";
    echo "<td>".$level['points']."</td>";
    echo "<td>".$level['level']."</td>";
    echo "</tr>";
    
}

    echo "</tbody>";
    echo "</table>";
    
    ?>