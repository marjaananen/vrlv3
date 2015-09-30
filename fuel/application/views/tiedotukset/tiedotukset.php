<h2>Tiedotukset <?php if (!empty($header)) { echo "- $header"; }?> </h2>

    
    <div id="lollero" style="width:100%; height:200px;"></div>
    
    <?php if(!empty($tag_cloud)) : ?>
        <script>
            $(document).ready(function() {
                console.log("lol");
                var words = <?=$tag_cloud?>;             
                
                $('#lollero').jQCloud(words);
                console.log("lol2");
            }); 
        </script>
    <?php endif; ?>
</p>

<?php
    foreach ($tiedotukset as $tiedotus){
?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><?php echo $tiedotus['otsikko']; ?> - <?php echo $tiedotus['aika']; ?></h3>
          </div>
          <div class="panel-body">
            <?php echo $tiedotus['teksti']; ?>
            <?php if (substr($tiedotus['teksti'], -3) == "...") { echo "<p><a href=\"" . site_url('tiedotukset/tiedotus/'.$tiedotus['tid']) . "\">Lue koko teksti</a></p>"; }?>
            <?php $tagit = array(); foreach ($tiedotus['kategoriat'] as $kat){$tagit[]= "<a href=\"" . site_url('tiedotukset/kategoria/'.$kat['kid']) . "\">".$kat ['kat']."</a>"; }?>
          </div>
          <div class="panel-footer"><a href="<?php site_url('tunnus/VRL-'.$tiedotus['lahettaja']); ?>">VRL-<?php echo $tiedotus['lahettaja']; ?></a> <strong><?php echo $tiedotus['lahettaja_nick'];?></strong> (<?php echo implode(" | ", $tagit); ?>)</div>
        </div>
        
<?php
        
}?>


<?php
    if (!empty($pagination)){
?>

<nav>
  <ul class="pagination">
    <li <?php if ($pagination['page'] == 1){ echo 'class="disabled"';}?>><a href="?sivu=<?php echo      ['page']-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
    
    <?php    
        $i=0;
        for($i = 1; $i <= $pagination['pages']; $i++){
            
            echo '<li ';
            if ($pagination['page'] == $i){ echo 'class="active"';}
            echo "><a href=\"?sivu=$i\">$i";
            if ($pagination['page'] == $i){echo '<span class="sr-only">(current)</span>';}
            echo '</a></li>';
        }
    ?>
    <li <?php if ($pagination['page'] == ($i-1)){ echo 'class="disabled"';}?>><a href="?sivu=<?php echo $pagination['page']+1; ?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>

    <?php
    } ?>
  </ul>
</nav>
        
