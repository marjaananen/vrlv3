<h2><?=$title ?></h2>



<?php echo fuel_var('text_view', "")?>

<div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', 'Täytä tunnukset muodossa VH00-000-0000.')?>
        <?php echo validation_errors(); ?>
</div>
<?php echo fuel_var('form', "")?>

<?php
if(isset($suku) and sizeof($suku) > 0){
 $pedigree_printer->createPedigree($suku, 4);
 }
 
 ?>





