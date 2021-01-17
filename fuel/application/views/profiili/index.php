<h1>Tervetuloa omaan profiliisi, <?php
    echo $nimimerkki; ?>!</h2>
<p>
   Olet kirjannut sähköpostiosoitteeksesi   <span class="bg-primary">&nbsp; <?php
    echo $email; ?> &nbsp;</span>, pidäthän sen ajan tasalla! 
</p>

<?php if($admin){
   echo '<p> Olet VRL:n ylläpitäjä!</p>';
}?>

<table class="table table-condensed">
   <tr>
     <th>#</th>
     <th>Rekisteröity</th>
   </tr>
    <tr>
      <td><a href="<?php echo $tunnus_url;?>">VRL-<?php echo $tunnus;?></a></td>
      <td><?php echo $hyvaksytty;?></td>

   </tr>
   <tr>
      <td><a href="<?php echo $tallit_url;?>">Tallit</a></td>
      <td><?php echo $stats['tallit']['kaikki'] ?? 0;?> kpl (toiminnassa <?php echo $stats['tallit']['toiminnassa'] ?? 0;?> kpl)</td>

   </tr>
   <tr>
      <td><a href="<?php echo $hevoset_url;?>">Hevoset</a></td>
      <td><?php echo $stats['hevoset']['kaikki'] ?? 0;?> kpl (elossa <?php echo $stats['hevoset']['elossa'] ?? 0;?> kpl)</td>
   </tr>
   <tr>
      <td><a href="<?php echo $kasvattajanimet_url;?>">Kasvattajanimet</a></td>
      <td><?php echo $stats['kasvattajanimet'];?> kpl</td>
   </tr>
</table>

<?php echo $vastuut;?>

   