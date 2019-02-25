<h2>Tallin rekisteröinti</h2>
<p><I>"Sääntööjää on siinä monenlaista, moooonneenlaaaaistaaa. <br> Kuiske kuuluu, mistä voisin laistaa, vooooisiiiin laaaistaaa... </I><br>~Piparitauon muistoa kunnioittaen</p>

<?php
if ($loggedin) { echo '<a href="' . site_url('profiili/omat-tallit/rekisteroi') . '">Rekisteröi tallisi täältä.</a>';}
else echo "Voit rekisteröidä tallisi kirjauduttuasi sisään";
?>