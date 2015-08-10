<div class="panel panel-default">
    <div class="panel-body">
            <?php foreach($fields as $field) : ?>
            <div class="form-group">
                <?=$field['label']?>
                <p><?=$field['field']?></p>
            </div>
            <?php endforeach; ?>
    </div>
    
    
</div>

