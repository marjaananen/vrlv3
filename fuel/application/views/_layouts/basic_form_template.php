<div class="panel panel-default">
    <div class="panel-body">
            <?php foreach($fields as $field) : ?>
            
            <?php if(strpos($field['field'], "<h3") === false){?>
            <div class="form-group">
                <?php if (strpos($field['field'], "hidden") === false  && strpos($field['field'], "submit") === false){ ?>
                <?php if (strpos($field['field'], "checkbox") === false && strpos($field['field'], "radio") === false){?>
                    <?=$field['label']?>
                    <?=$field['field']?>
                    
                <?php } else {?>
                    <div class="checkbox-label"><?=$field['field']?> <b class="label-text"><?=$field['label']?></b></div>
                    
                    <?php } ?>
                <?php } else echo $field['field']; ?>
                
                
            </div>
            <?php }
            else {
                echo $field['field'];
            }
            ?>
            <?php endforeach; ?>
       
       
       
    </div>
    
    
</div>



  