<?php

$edit = "";

if(isset($edit_tools) && $edit_tools == true){
 $button = '<button type="button" class="btn btn-default">
  <img src="'.base_url().'assets/images/icons/edit.png" /></button>';
 $edit = '<a href="'.site_url('/virtuaalihevoset/muokkaa/'. $hevonen['reknro']).'">'.$button.'</a>';
}

?>



<h2>Talliprofiili: <?=$stable['nimi']?> <?=$edit;?></h2>

<?php if($stable['lopettanut']) : ?>
    <div class="alert alert-warning">
      <span class="glyphicon glyphicon-info-sign"></span> Tämä talli on lopettanut toimintansa!
    </div>
<?php endif; ?>
<table class="table table-striped">
 	</tr>
   <tr>
		<th scope="row">Tallinumero:</th><td> <?=$stable['tnro']?></td>
    </tr>
   <tr>
		<th scope="row">Rekisteröity:</th><td> <?=$stable['perustettu']?></td>
    </tr>
   <tr>
		<th scope="row">Kotisivu:</th><td> <a href="<?=$stable['url']?>"><?=$stable['url']?></a></td>
    </tr>
   <tr>
		<th scope="row">Kuvaus:</th><td> <?=$stable['kuvaus']?></td>
    </tr>
   <tr>
		<th scope="row">Kategoriat:</th><td>
        <?php
            $first = true;
            foreach($categories as $c)
            {
                if($first)
                {
                    echo $c['nimi'] . " (". $c['katelyh']. ")";
                    $first = false;
                }
                else
                    echo ", " . $c['nimi'] . " (". $c['katelyh']. ")";
            }
        ?>
    </td>
        </tr>
   <tr>
		<th scope="row">Omistajat:</th><td>
        <?php
            $first = true;
            foreach($owners as $o)
            {
                if($first)
                {
                    echo $o['nimimerkki'] . " (<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
                    $first = false;
                }
                else
                    echo ", " . $o['nimimerkki'] . " (<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
            }
        ?>
   </td>
   </tr></table>    


   <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if ($sivu == 'hevoset'){echo "active";}?>"><a href="<?php echo base_url('tallit/talli/'. $stable['tnro'] . '/hevoset')?>">Hevoset</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kasvatit'){echo "active";}?>"><a href="<?php echo base_url('tallit/talli/'. $stable['tnro'] . '/kasvatit')?>">Kasvatit</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kasvattajanimet'){echo "active";}?>"><a href="<?php echo base_url('tallit/talli/'. $stable['tnro'] . '/kasvattajanimet')?>">Kasvattajanimet</a></li>
        <li role="presentation" class="<?php if ($sivu == 'kilpailut'){echo "active";}?>"><a href="<?php echo base_url('tallit/talli/'. $stable['tnro'] . '/kilpailut')?>">Kilpailut</a></li>
        <li role="presentation" class="<?php if ($sivu == 'nayttelyt'){echo "active";}?>"><a href="<?php echo base_url('tallit/talli/'. $stable['tnro'] . '/nayttelyt')?>">Näyttelyt</a></li>
 
   </ul>
    
    <?php
        if($sivu == 'hevoset')
        {
            echo $horses;
        }
        else if($sivu == 'kasvatit'){
            echo $foals;

        } 
         else if($sivu == 'kasvattajanimet'){
                    echo $names;

        }
        
         else if($sivu == 'kilpailut'){
                    echo $competitions;

        }
                
         else if($sivu == 'nayttelyt'){
                    echo $shows;

        }
    
    ?>



