<h2>Omat tiedot</h2>
Tervetuloa VRL:n profiiliin. 

<h2>Pikaviestit</h2>

Voit lähettää pikaviestejä muille VRL-tunnuksille. Luetut viestit säilyvät 7 vuorokautta lähetyksestä, jonka jälkeen ne poistetaan automaattisesti. Halutessasi voit merkata viestin tärkeäksi klikkaamalla . Tärkeitä viestejä ei poisteta. Lähetä vastaus lähettäjälle klikkaamalla .
Viestissä voi olla korkeintaan 360 merkkiä, eikä viestissä voi käyttää käyttää html-koodeja eikä rivinvaihtoja!

<p>
    <?php if(!empty(validation_errors())) : ?>
        <div class="alert alert-danger" role="alert">
            <p>Viestin lähetys epäonnistui!</p>
            <?php
                echo validation_errors();
            ?>
        </div>
    <?php elseif($success == true) : ?>
        <div class="alert alert-success" role="alert">
            <p>Viesti lähetetty.</p>
        </div>
    <?php endif; ?>
</p>
    
<p>   
    <?php
        echo fuel_var('quick_messages_form', 'Pikaviestin lähetys on rikki, ota yhteys ylläpitoon!');
    ?>
</p>

<p>
    <table class="table">
        <tr>
            <th>#</th>
            <th>Päivämäärä</th>
            <th>Lähettäjä</th>
            <th>Viesti</th>

        </tr> 
        <?php
        
            $delete = img_path('icons/delete.png');
            $icon = img_path('icons/star_grey.png');
            $class="";
            
            foreach($messages as $message)
            {
                if ($message['tarkea']){
                    $icon = img_path('icons/star.png');
                }
                
                if ($message['lahettaja'] === "00000"){
                    $lahettaja = "Ylläpito";
                }
                
                else {
                    $lahettaja = "VRL-".$message['lahettaja'];
                }
                
                if($message['luettu'] == 0){
                    $class = " class=\"success\"";
                }
                echo "<tr$class>";
                echo "<td> <a href=\"". site_url('/profiili/aseta_tarkeys') . "/" . $message['id'] . "/". $message['tarkea'] . "\"><img src=\"$icon\"></a> <a href=\"". site_url('/profiili/poista_pikaviesti') . "/" . $message['id'] . "\"><img src=\"$delete\"></a></td>";
                echo "<td>" . $message['aika'] . "</td>";
                echo "<td>$lahettaja</td>";
                echo "</tr>";
            }
            
            
            
            
        ?> 
    </table>
</p>