<h2>Tervetuloa omaan profiliisi, <?php
    echo $nimimerkki; ?>!</h2>
<p>
   Olet kirjannut sähköpostiosoitteeksesi <?php
    echo $email; ?>, pidäthän sen ajan tasalla! Pääset muokkaamaan omia tietojasi <?php echo "<a href='" . site_url('/profiili/tiedot') . "'>tästä</a>";?>. <br />Olet ollut VRL:n jäsen <?php
    echo $hyvaksytty; ?> alkaen.
</p>

<table class="table table-condensed">
   <tr>
     <th>#</th>
     <th>Rekisteröity</th>
     <th>Jonossa</th>
   </tr>
   <tr>
      <td>Tallit</td>
      <td><?=$stable_stats['all']?> joista toiminnassa <?=$stable_stats['active']?></td>
      <td><?=$stable_stats['queued']?></td>
   </tr>
   <tr>
      <td>Seurat</td>
      <td>0 joista toiminnassa 0 </td>
      <td>0</td>
   </tr>
   <tr>
      <td>Kasvattajanimet</td>
      <td>0</td>
      <td>0</td>
   </tr>
   <tr>
      <td>Seurat</td>
      <td>0 joista toiminnassa 0</td>
      <td>0</td>
   </tr>
   <tr>
      <td>Hevoset</td>
      <td>0 joista elossa 0</td>
      <td>0</td>
   </tr>
   <tr>
      <td>Kilpailut</td>
      <td>0 joista tuloksettomia 0</td>
      <td>0 kutsu(a) ja 0 tulos(ta)</td>
   </tr>
</table>
