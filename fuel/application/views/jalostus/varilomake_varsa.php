


<?php

if (isset($v_msg)){?>
    

<div class="alert alert-<?php echo fuel_var('v_msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('v_msg', 'Täytä tunnukset muodossa VH00-000-0000.')?>
</div>
<?php } else {


echo '<div class="panel panel-success">';
echo '<div class="panel-heading varsa">Varsan v&auml;rivaihtoehdot:</div>';
echo '<div class="panel-body">';
  $inheritance_printer->tulosta_varsatulos($baby, $varmalla);
  $inheritance_printer->tulosta_varsalisatiedot($baby);
echo '</div></div>';
}
    ?>