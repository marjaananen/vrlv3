<div class="panel panel-default">
    <div class="panel-body">
            <?php foreach($fields as $field) : ?>
            <div class="form-group">
            <?php if (strpos($field['field'], "checkbox") === false){?>
                <?=$field['label']?>
                <p><?=$field['field']?></p>
                
            <?php } else {?>
                <?=$field['label']?> <?=$field['field']?>
                
                <?php } ?>
            </div>
            <?php endforeach; ?>
    </div>
    
    
</div>

