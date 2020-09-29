
<h2><?php echo "Ilmoita tulokset kilpailuun #" . $kisa_id . ")"; ?></h2>

 <table class="table table-striped">
                 <tr>
                <th scope="row">Jaos</th> <td> <?php echo $jaos['nimi']." (".$jaos['lyhenne'].")"; ?></td>
                <th scope="row">Kutsu</th> <td><a href="<?php echo $url;?>"></a>kutsu</a></td>
            </tr>
            <tr>
                <th scope="row">Päivämäärä</th> <td> <?php echo $kp; ?></td>
                <th scope="row">VIP</th> <td><?php echo $vip; ?></td>
            </tr>
	                <tr>
                <th scope="row">Vastuuhenkilö</th> <td> <?php echo $username; ?>,  <a href="<?php echo site_url('tunnus/'.$user_vrl); ?>"><?php echo $user_vrl; ?></a> <br /><?php echo $user_email; ?></td>
                <th scope="row">Järjestävä talli</th> <td> <?php echo '<a href="'. $talli['url'] . '">'.$talli['nimi'].'</a><br />
                <a href="'.site_url('tallit/talli/'. $jarj_talli). '">'.$jarj_talli.'</a>';?></td>
            </tr>

		
           </table>
 
 
 <?php
 
 if(isset($luokat) && $luokat == true){
    echo "<h2>Ilmoita järjestetyt luokat</h2>";
    echo "<p>Järjestelmä hakee lomakkeelle oletuksena edellisten saman lajin kilpailujesi luokat. Voit muokata niitä vaapasti. Ilmoita kaikki luokat, vaikka niitä ei oltaisi (esim. osallistujien puutteessa) järjestetty.</p>";
 }else {
    echo "<p>Ilmoita tuloksissa kaikki osallistuneet ratsukot, ei pelkästään sijoittuneita.
    Mikäli kilpailun kaikkia luokkia ei ole järjestetty esim. osallistujien puuttuessa, jätä luokan osallistujakenttä/kentät tyhjäksi.
    Tuloksissa tulee näkyä ratsastajan viisinumeroinen VRL-tunnus, hevosen mahdollinen 11-numeroinen VH-numero ja ratsukon sijoitus. Koskee myös ei-sijoittuneita!
    <b>Muista varmistaa jaoksen sivuilta jaoskohtaiset säännöt!</b>.</p>";
 }
 ?>
 
 
 
 <?php echo $form; ?>