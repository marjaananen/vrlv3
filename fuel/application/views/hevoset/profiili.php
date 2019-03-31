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
</div>

<div class="container">
    <?php $pedigree_printer->createPedigree($suku, 4);?>
</div>





