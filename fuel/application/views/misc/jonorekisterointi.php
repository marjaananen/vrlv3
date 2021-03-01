<h1><?=$title?></h1>

<?php echo fuel_var('text_view', '')?>
<p>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php
        echo fuel_var('form', '');
    ?>
</p>