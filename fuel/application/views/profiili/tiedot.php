<h2>Omat tiedot</h2>

<p>
    <?php if(!empty($fail_msg) || !empty(validation_errors())) : ?>
        <div class="alert alert-danger" role="alert">
            <p>Tietojen päivitys epäonnistui!</p>
            <?php
                echo validation_errors();
                echo $fail_msg;
                $error_showed = true;
            ?>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($success) && $success == true) : ?>
        <div class="alert alert-success" role="alert">
            <p>Tietojen päivitys onnistui.</p>
        </div>
    <?php elseif(!empty($success) && $success == false && !isset($error_showed)) : ?>
        <div class="alert alert-danger" role="alert">
            <p>Tietojen päivitys epäonnistui! Tarkasta tietosi uudelleen profiilistasi.</p>
        </div>
    <?php endif; ?>
    
    <?php
        echo fuel_var('profile_form', '');
    ?>
</p>