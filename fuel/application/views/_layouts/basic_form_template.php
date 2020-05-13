<div class="panel panel-default">
    <div class="panel-body">
            <?php foreach($fields as $field) : ?>
            <div class="form-group">
                <?php if (strpos($field['field'], "hidden") === false){ ?>
                <?php if (strpos($field['field'], "checkbox") === false){?>
                    <?=$field['label']?>
                    <p><?=$field['field']?></p>
                    
                <?php } else {?>
                    <?=$field['label']?> <?=$field['field']?>
                    
                    <?php } ?>
                <?php } else echo $field['field']; ?>
                
                
            </div>
            <?php endforeach; ?>
    </div>
    
    
</div>

