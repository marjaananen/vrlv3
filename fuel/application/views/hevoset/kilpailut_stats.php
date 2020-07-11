<p>Statistiikka on kerätty kilpailuista, joissa hevonen on kilpaillut VH-tunnuksen kanssa 5.5.2014 alkaen.</p>

<?php

echo "<h3>Porrastetut kilpailut</h2>";
tableHead();
    
foreach ($kisatiedot as $jaos=>$info){
    if($jaokset[$jaos]['s_salli_porrastetut'] == 0){
        continue;
    }
    

    echo "<tr>";
    echo "<td><b>". $jaokset[$jaos]['lyhenne']. "</b></td>";
    echo "<td>".$info['porr_voi']."</td>";
    echo "<td>".$info['porr_sij']."</td>";
    echo "<td>".$info['porr_os']."</td>";
        echo "<td>".sijpros($info['porr_voi'], $info['porr_sij'], $info['porr_os']) ."%</td>";

    echo "</tr>";
    
}

tableEnd();


echo "<h3>Perinteiset kilpailut</h3>";
    
  
tableHead();
    
foreach ($kisatiedot as $jaos=>$info){
    echo "<tr>";
    echo "<td><b>". $jaokset[$jaos]['lyhenne']. "</b></td>";
    echo "<td>".$info['voi']."</td>";
    echo "<td>".$info['sij']."</td>";
    echo "<td>".$info['os']."</td>";
        echo "<td>".sijpros($info['voi'], $info['sij'], $info['os']) ."%</td>";

    echo "</tr>";
    
}

tableEnd();
    
    
    
    
    
    
    
    
    
    function sijPros($voi, $sij, $os){
        $voi = intval(round($voi));
        $sij = intval(round($sij));
        $os = intval(round($os));
        
        if ($os === 0){
            return 0;
        }
        else {
            $sijpros = ($voi + $sij)/$os;
            $sijpros = $sijpros * 100;
            $sijpros = round($sijpros);
            return $sijpros;
        }
    }
    
    
    function tableHead(){
          echo '<table class="table">
        <thead>
          <tr>
            <th scope="col">Jaos</th>
            <th scope="col">Voitot</th>
            <th scope="col">Muut sijoitukset</th>
            <th scope="col">Osallistumiset</th>        
            <th scope="col">Sijoitusprosentti</th>
          </tr>
        </thead>';
    echo "<tbody>";
    }

    
    function tableEnd(){
        echo "</tbody>";
    echo "</table>";
    
    }
    
    
    ?>