<h2>Lisää yhteystietoja</h2>

<p>
    <?php if(!empty(validation_errors())) : ?>
        <div class="alert alert-danger" role="alert">
            <p>Yhteystietojen lisäys epäonnistui!</p>
            <?php
                echo validation_errors();
            ?>
        </div>
    <?php elseif($success == true) : ?>
        <div class="alert alert-success" role="alert">
            <p>Yhteystietojen lisäys onnistui.</p>
        </div>
    <?php endif; ?>
</p>
    
<p>   
    <?php
        echo fuel_var('add_contacts_form', '<a href="">Takaisin yhteystietojen muokkaukseen</a>');
    ?>
</p>

<p>
    <table class="table">
        <tr>
            <th>Tyyppi</th>
            <th>Yhteystieto</th>
            <th>Näkyvyys</th>
            <th>Poista</th>
        </tr> 
        <?php
            foreach($contact_info as $ci)
            {
                echo "<tr>";
                echo "<td>" . $ci['tyyppi'] . "</td>";
                echo "<td>" . $ci['tieto'] . "</td>";
                echo "<td>";
                if($ci['nayta'] == 1)
                    echo "Julkinen";
                else
                    echo "Piilotettu";
                echo "</td>";
                echo "<td><a href='" . site_url('/profiili/poista_yhteystieto') . "/" . $ci['id'] . "'>Poista</a></td>";
                echo "</tr>";
            }
        ?> 
    </table>
</p>