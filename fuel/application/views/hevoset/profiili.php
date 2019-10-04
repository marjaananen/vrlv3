<?php
///Muunnokset, älä muokkaa jos et tiedä mitä

//kasvattajanimitieto
    $kasvattajanimitieto = "-";
    if (!isset($hevonen['kasvattajanimi_id']) || strlen($hevonen['kasvattajanimi_id']) == 0){
        if (isset($hevonen['kasvattajanimi'])){
            $kasvattajanimitieto == $hevonen['kasvattajanimi'];
        }    
    }
    else {
        $kasvattajanimitieto = "<a href=\"" . site_url('kasvatus/kasvattajanimet/nimi/'.$hevonen['kasvattajanimi_id']) . "\">". $hevonen['kasvattajanimi'] . "</a>";
    }

//omistajatieto
        $omistajatieto = "";
        $first = true;
        foreach($owners as $o){
            if($first){
                $omistajatieto .= $o['nimimerkki'] . " (<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
                $first = false;
                }
            else{
                $omistajatiedto .= ", " . $o['nimimerkki'] . " (<a href='" . site_url('tunnus') . "/VRL-" . $o['omistaja'] . "'>VRL-" . $o['omistaja'] . "</a>)";
            }
        }
    
//kasvattajatieto
$kasvattajatieto = "";
if(isset($hevonen['kasvattaja_talli'])){
    $kasvattajatieto = '<a href="' . site_url('tallit/talli/'.$hevonen['kasvattaja_talli']).'">'.$hevonen['kasvattaja_talli'].'</a>';  
    
}

if(isset($hevonen['kasvattaja_tunnus'])){
    if (strlen($kasvattajatieto) > 0){
        $kasvattajatieto .= ', ';
    }

    $kasvattajatieto .= '<a href="' . site_url('tunnus') . "/VRL-" . $hevonen['kasvattaja_tunnus']. '">VRL-' . $hevonen['kasvattaja_tunnus'] . "</a>";
    
}


    
?>




<h2><?=$hevonen['h_nimi']?> (<?=$hevonen['reknro']?>)</h2>

<?php if($hevonen['kuollut']) : ?>
    <h3>Tämä hevonen on kuollut <?=$hevonen['kuol_pvm']?>.</h3>
<?php endif; ?>

<div class="container">
    <p><b>Rotu:</b> <?=$hevonen['h_rotunimi']?></p>
    <p><b>Sukupuoli:</b> <?=$hevonen['sukupuoli']?></p>
    <p><b>Säkäkorkeus:</b> <?=$hevonen['sakakorkeus']?>cm</p>
    <p><b>Syntymäaika:</b> <?=$hevonen['syntymaaika']?>, <?=$hevonen['maa']?></p>
    <p><b>Väri:</b> <?=$hevonen['h_varinimi']?></p>
    <p><b>Painotus:</b> <?=$hevonen['h_painotusnimi']?></p>
    <p><b>Url:</b> <a href="<?=$hevonen['h_url']?>"><?=$hevonen['h_url']?></a></p>
    <p><b>Rekisteröity:</b> <?=$hevonen['rekisteroity']?></p>
    <p><b>Kotitalli:</b> <a href="<?php echo site_url('tallit/talli/'.$hevonen['kotitalli'])?>"><?=$hevonen['kotitalli']?></a></p>
    <p><b>Kasvattajanimi:</b> <?php echo $kasvattajanimitieto; ?></p>
    <p><b>Kasvattaja:</b> <?php echo $kasvattajatieto; ?></p>
    <p><b>Omistajat:</b> <?php echo $omistajatieto; ?></p>
</div>

<div class="container">
    <?php $pedigree_printer->createPedigree($suku, 4);?>
</div>





