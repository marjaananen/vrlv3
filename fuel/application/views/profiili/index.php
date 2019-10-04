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
   </tr>
   <tr>
      <td>Tallit</td>
      <td><?php echo $stats['tallit'];?> kpl</td>

   </tr>
   <tr>
      <td>Hevoset</td>
      <td><?php echo $stats['hevoset'];?> kpl </td>
   </tr>
   <tr>
      <td>Kasvattajanimet</td>
      <td><?php echo $stats['kasvattajanimet'];?> kpl</td>
   </tr>
</table>
