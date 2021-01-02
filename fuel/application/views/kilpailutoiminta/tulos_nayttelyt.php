<?php

print	'<hr /> <h3>Tulokset</h3>';


if(strlen($tulokset['tulokset']) > 1){
    echo nl2br($tulokset['tulokset']);
    
    
} else {

//vanhanmalliset tulokset

    $tulostettavat_tulokset = array();
        echo "<p>";
	foreach ($bistulokset as $bistulos) {
            if ($bistulos['vh'] != "000000000"){
                $reknro = "VH".substr($bistulos['vh'], 0, 2)."-".substr($bistulos['vh'], 2, 3)."-".substr($bistulos['vh'], 5, 4);
            }
            else {
                $reknro = "(ei rek)";
            }
            
            IF (strpos($bistulos['palkinto'], "BIS") !== false){
                 echo "<b>".$bistulos['palkinto'] . "</b> " . $bistulos['vh_nimi'] . " " . $reknro . " <br />";
            }
            
            else {
                $tulostettavat_tulokset[$bistulos['palkinto']][] = $bistulos['vh_nimi'] . " " . $reknro;
            }
     
        }
        
        echo "</p>";
        
        
        unset($tulostettavat_tulokset['MVA-SERT']);
        
        foreach ($tulostettavat_tulokset as $key=>$bisrivi){
            
            echo "<b>" .  $key . "</b><p>";
            foreach ($bisrivi as $hevonen){
                echo $hevonen . "<br />";
            }
            echo "</p>";              
            
        }

	
}


        
        ?>
        
        <h3>Hevosten profiileihin sijoitetut palkinnot</h3>
        <?php echo $bistaulu;?>