<h2><?=$title ?></h2>



<?php echo fuel_var('text_view', "")?>

<div class="alert alert-<?php echo fuel_var('msg_type', 'info')?>" role="alert">   
        <?php echo fuel_var('msg', 'Täytä tunnukset muodossa VH00-000-0000.')?>
        <?php echo validation_errors(); ?>
</div>
<?php echo fuel_var('form', "")?>

<?php
if(isset($suku) and sizeof($suku) > 0){
 $pedigree_printer->createPedigree($suku, 4);
 }
 
 ?>
 <?php if(isset($varit)){?>
  <script src="<?php echo base_url();?>assets/js/periytymisjavascript.js"></script>

 <h3>Värien periytyminen</h3>
 <p>Huom! Virtuaalihevosharrastuksessa perinnöllisyyttä ei ole pakko noudattaa, ja hevosen väri voi vaihtua sen elämän aikana. Tässä osiossa esitelty tieto perustuu valittujen vanhempien tämänhetkiseen rekisteröityyn väriin. </p>
 
 <?php echo fuel_var('varit', 'Värien periytyminen voidaan laskea vain jos molemmilla vanhemmilla on väri.')?>
 
 <?php } ?>
 
 
  <?php if(isset($ominaisuudet)){?>

 <h3>Porrastettujen kilpailujen ominaisuuspisteet</h3>
 <p>Lue lisää porrastetuista kilpailutoiminta-sivulta.</p>
 
 <?php echo fuel_var('ominaisuudet', 'Ominaisuuspisteiden lasku ei onnistunut, ota yhteyttä ylläpitoon.')?>
 
 <?php } ?>
 
 
 

 
 
 




