<h2><?php echo $title; ?></h2>

<?php echo $text_view;?>

<?php if(strlen(fuel_var('msg', '')) > 0){ ?>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
        
        <?php if(isset($msg_details)){
            foreach ($msg_details as $detail){
                echo "<br># ".$detail;
            }
        }?>
    </div>
    
    <?php } ?>

<?php if(isset($form)){
    echo $form;
}?>
<?php echo $list; ?>