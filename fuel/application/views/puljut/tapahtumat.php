<h2><?php echo $title; ?></h2>
 

 
 <?php echo $text_view;?>

 
 <?php
 if (isset($msg)){
    ?>
    
       <div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', '')?>
        <?php echo validation_errors(); ?>
        
        <?php if(isset($msg_details)){
            foreach ($msg_details as $detail){
                echo "<br># ".$detail;
            }
        }?>

    </div>
    
<?php
 }?>
 
 <?php if(isset($form)){ echo $form; }
    else {
?>

 <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'jaokset' || empty($sivu)){echo "active";}?>"><a href="<?php echo base_url('alayhdistykset/tapahtumat/jaokset')?>">Jaosten laatuarvostelut</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kantakirjat'){echo "active";}?>"><a href="<?php echo base_url('alayhdistykset/tapahtumat/kantakirjat')?>">Kantakirjat</a></li>
        <li role="presentation" class="<?php if ($sivu == 'laatuarvostelut'){echo "active";}?>"><a href="<?php echo base_url('alayhdistykset/tapahtumat/laatuarvostelut')?>">Muut laatuarvostelut</a></li>
</ul>
  
 
 

    
<?php }

echo $lista; ?>