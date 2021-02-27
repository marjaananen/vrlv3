<h2><?php echo $title; ?></h2>


<?php
    
    foreach ($puljut as $jaos){
        $rodut = "";
        if(isset($jaos['rodut']) && sizeof($jaos['rodut'])> 0){
            $rotulista = array();
            foreach ($jaos['rodut'] as $rotu){
                $rotulista[] = '<a href="'.site_url('virtuaalihevoset/rotu/'.$rotu['rotunro']). '">'. $rotu['rotu'].'</a>';
            }
            
            $rodut = "<p>". implode(", ", $rotulista) . '</p>';
        }
?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><a href="<?php echo $jaos['url']; ?>"><?php echo $jaos['nimi']; ?></a> (<?php echo $jaos['lyhenne']; ?>) </h3>
          </div>
          <div class="panel-body">
            <?php echo $rodut;?>
            <?php echo $jaos['kuvaus']; ?>
          </div>
          <div class="panel-footer">
            <?php
            if(sizeof($jaos['yllapito'])== 0){
                echo "Ei ylläpitäjää";
            }else {
                $eka = true;
            foreach($jaos['yllapito'] as $yp){
                if ($eka){
                    $eka = false;
                }else {
                    echo ", ";
                    }?>
                    
                <a href="<?php echo site_url('tunnus/VRL-'.$yp['omistaja']); ?>">VRL-<?php echo $yp['omistaja']; ?></a> <strong><?php echo $yp['nimimerkki'];?></strong>
        
            <?php
            }}
            ?>
            </div>
        </div>
        
<?php
        
}?>


<?php
    if(isset($puljut_offline) && sizeof($puljut_offline)>0){
        echo "<h3>Vanhat</h3><p>Nämä ovat lopettaneet tai poistuneet VRL:n alaisuudesta, mutta näiden tilaisuuksia, palkintoja yms. on edelleen VRL:n tietokannassa.</p>";
        foreach($puljut_offline as $pulju){
            echo '<p>';
            echo '<b>'.$pulju['nimi'].'</b>  (' .  $pulju['lyhenne'] . '): ' . $pulju['kuvaus'];
            echo '</p>';
        }
    }
?>

        
