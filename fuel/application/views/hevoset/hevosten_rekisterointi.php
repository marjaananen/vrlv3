<h1>Hevosten rekisteröinti</h1>

<ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'yksi' || empty($sivu)){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/rekisterointi')?>">Rekisteröinti</a></li>
        <li role="presentation" class="<?php if ($sivu == 'massa'){echo "active";}?>"><a href="<?php echo base_url('virtuaalihevoset/massarekisterointi')?>">Massarekisteröinti</a></li>
         </ul>


<?php
if($sivu == 'massa'){
    echo $massarekisterointi;
}else {?>

<p>
    <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
    </div>
    
    <?php
        echo fuel_var('form', '');
    ?>
</p>

<?php }?>