<h2>Virtuaaliset kilpailujaokset </h2>


<?php
    foreach ($jaokset as $jaos){
?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><a href="<?php echo $jaos['url']; ?>"><?php echo $jaos['nimi']; ?></a> - (<?php echo $jaos['lyhenne']; ?>)</h3>
          </div>
          <div class="panel-body">
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
                    
                <a href="<?php ECHO site_url('tunnus/VRL-'.$yp['omistaja']); ?>">VRL-<?php echo $yp['omistaja']; ?></a> <strong><?php echo $yp['nimimerkki'];?></strong>
        
            <?php
            }}
            ?>
            </div>
        </div>
        
<?php
        
}?>

        
