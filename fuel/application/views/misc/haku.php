<?php
if (isset($title)){
    echo "<h1>".$title."</h1>";
}

if (isset($title2)){
    echo "<h2>".$title2."</h2>";
}

?>

<?php
    echo fuel_var('text_view', '');
?>

<p>

<?php if(strlen(fuel_var('msg', '')) > 0){ ?>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php } ?>
    
    <?php
        echo fuel_var('form', '');
    ?>
    
    <br />
    
<?php echo fuel_var('tulokset', '')?>