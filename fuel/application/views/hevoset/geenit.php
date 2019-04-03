<h3>Värigeenit (pohjaväri: <?=$colour['pvari']?> )</h3>


<?php

$table_n = array();
$table_k = array();
$ok = '<button type="button" class="btn btn-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>';
$nope = '<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>';

foreach ($gene_lists['norm'] as $gene=>$text){
    $table_n[] = array("vari"=>$text, "onko" =>$colour[$gene]);
}
foreach ($gene_lists['kirj'] as $gene=>$text){
    $table_k[] = array("vari"=>$text, "onko" =>$colour[$gene]);
}

echo "<table class=\"table\">";
for ($i = 0; $i < max(sizeof($gene_lists['norm']), sizeof($gene_lists['kirj'])); $i++){
    echo "<tr>";
    
    echo "<td>";
    if (isset($table_n[$i]['vari'])) {echo $table_n[$i]['vari'];} else {echo "";}
    echo "</td><td>";
    if (!isset($table_n[$i])){echo "";} elseif ($table_n[$i]['onko']){echo $ok;} else {echo $nope;}
    echo "</td>";
    
    echo "<td>";
    if (isset($table_k[$i]['vari'])) {echo $table_k[$i]['vari'];} else {echo "";};
    echo "</td><td>";
    if (!isset($table_k[$i])){echo "";} elseif($table_k[$i]['onko']){echo $ok;} else {echo $nope;}
    echo "</td>";  
    
    echo "</tr>";
}
?>

</table>

