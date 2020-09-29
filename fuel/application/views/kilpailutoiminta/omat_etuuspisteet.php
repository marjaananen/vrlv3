<h1>Omat etuuspisteesi</h1>

  <table class="table">
    <thead>
      <tr>
        <th>Jaos</th>
        <th>Etuuspisteet</th>
        <th>Avoimet kutsut/sallittu maksimi</th>
        <th>Suoraan kalenteriin?</th>
      </tr>
    </thead>
    <tbody>

<?php

foreach ($pisterivit as $rivi){
    $lyhenne = $rivi['jaos']['lyhenne'];
    $pisteet = $rivi['pisteet']['pisteet'] ?? 0;
    $avoimet = $rivi['avoimet'];
    $sallitut = $kisajarjestelma->sallitutKisamaarat($pisteet, $rivi['jaos']['id']);
    $suoraan = $kisajarjestelma->directlyCalender($pisteet, $rivi['jaos']['id']);

    echo "<tr>";
    echo "<td><strong>".$lyhenne."</strong></td>";
    echo "<td>".$pisteet."p</td>";
    echo "<td>".$avoimet ."/".$sallitut."</td>";
    if($suoraan === false) {
        echo '<td><img src="'.site_url('assets/images/icons/cancel.png').'" alt="Ei"/></td>';
    }else {
        echo '<td><img src="'.site_url('assets/images/icons/accept.png').'" alt="Kyllä"/></td>';

    }
    echo "</tr>";

}


?>

    </tbody></table>