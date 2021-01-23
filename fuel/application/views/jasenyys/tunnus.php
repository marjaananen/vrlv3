<h2><?=$tunnus?>: <?=$nimimerkki?></h2>
    
<table class="table table-striped">
 	<tr>
		<th scope="row">Nimimerkki</th> <td> <?=$nimimerkki?></td>
	</tr>
	<tr>
		<th scope="row">Rekisteröity</th> <td> <?=$rekisteroitynyt?></td>
	</tr>
	<tr>
		<th scope="row">Sähköposti </th> <td> <?php if ($logged_in) { echo $email; } else { echo "Näytetään sisäänkirjautuneille"; }?></td>
	</tr>
    <tr>
		<th scope="row">Kuvaus</th> <td> <?=$kuvaus?></td>
	</tr>

    
    <?php if ($logged_in){ ?>
    <tr>
       
    <th scope="row">Muut yhteystiedot:</th><td>
            <?php
                if(empty($muut_yhteystiedot))
                    echo "-";
                    
                echo "<ul>";
                
                foreach($muut_yhteystiedot as $my)
                {
                    echo "<li><b>" . $my['tyyppi'] . ": </b>" . $my['tieto'] . "</li>";
                }
                
                echo "</ul>";
            ?>
    </td>
	</tr><tr>
    
        <th scope="row">Vanhat nimimerkit:</th><td>
            <?php
                if(empty($nimimerkit))
                    echo "-";
                    
                echo "<ul>";
                
                foreach($nimimerkit as $n)
                {
                    echo "<li>" . $n['nimimerkki'] . " (vaihdettu " . date("d.m.Y", strtotime($n['vaihtanut'])) . ")</li>";
                }
                
                echo "</ul>";
            ?>
        </td></tr>
    <?php } else {?>
        <div class="alert alert-success" role="alert">
            Näet enemmän profiilitietoja sisäänkirjautuneena!
        </div>
    <?php }?>
    </table>
    <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'vastuut'){echo "active";}?>"><a href="<?php echo base_url($url. $tunnus . '/vastuut')?>">Vastuutehtävät</a></li>
        <li role="presentation" class="<?php if ($sivu == 'hevoset'){echo "active";}?>"><a href="<?php echo base_url($url. $tunnus . '/hevoset')?>">Hevoset</a></li>
        <li role="presentation" class="<?php if ($sivu == 'tallit'){echo "active";}?>"><a href="<?php echo base_url($url. $tunnus . '/tallit')?>">Tallit</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kasvattajanimet'){echo "active";}?>"><a href="<?php echo base_url($url. $tunnus . '/kasvattajanimet')?>">Kasvattajanimet</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kasvatit'){echo "active";}?>"><a href="<?php echo base_url($url. $tunnus . '/kasvatit')?>">Kasvatit</a></li>
    </ul>
    
    <?php
        if($sivu == 'tallit')
        {
            echo $stables;
        }
        else if($sivu == 'hevoset')
        {
            echo $horses;
        }
        else if($sivu == 'kasvatit'){
            echo $foals;

        } 
            else if($sivu == 'kasvattajanimet'){
                    echo $names;

        } else if($sivu == 'vastuut'){
            echo $vastuut;
        }
    
    ?>

