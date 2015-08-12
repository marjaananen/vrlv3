<h2>Omat tiedot</h2>

<p>
    <?php if(!empty(validation_errors())) : ?>
        <div class="alert alert-danger" role="alert">
            <p>Tietojen päivitys epäonnistui!</p>
            <?php echo validation_errors(); ?>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($success) && $success == true) : ?>
        <div class="alert alert-success" role="alert">
            <p>Tietojen päivitys onnistui.</p>
        </div>
    <?php endif; ?>
    
    <?php
        echo fuel_var('profile_form', '');
    ?>
</p>