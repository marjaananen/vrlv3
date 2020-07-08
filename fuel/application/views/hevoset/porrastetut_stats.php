<?php    
    
    echo '<table class="table">
        <thead>
          <tr>
            <th scope="col">Ominaisuus</th>
            <th scope="col">Pisteet</th>
          </tr>
        </thead>';
    echo "<tbody>";
    
    
foreach ($traits as $id=>$trait){
    echo "<tr>";

    echo "<td><b>". $trait . "</b></td>";
    echo "<td>".$horse_traits[$id]."</td>";
    echo "</tr>";

    
}

    echo "</tbody>";
    echo "</table>";
    
    ?>