<h1>Kutsujen hallinta</h1>
<table><tr><td class="tk">
<?php echo "<img src=\"".base_url()."ulkoasu/kisakeskus/add.png\" height =\"40px\"/><a href=\"".base_url()."kisakeskus/kisaeditori\">Lisää kutsu.</a>";?>
</td></tr></table>

<h2>SÄÄNNÖT</H2>
<p>

1) Jos klikkaat "poista" jonkun kutsun kohdalla, niin se sitten oikeasti myös poistuu kyselemättä mitään. Älä siis hutiklikkaile!
</p><p>
2) Jotta kisan saa näkyviin keskuksen omalla kisakalenterilla, sille pitää merkitä VRL-id, joka löytyy VRL:n sivuilta omien kisojen listasta. Sen avulla tulee myös automaattisesti tuloslinkki sivuille.
</p><p>
3) Jos kisaan tulee virheellisiä osallistumisia, huuda #virtuaalihevoset IRC-kanavalla Marsupientä, ja kerro kutsun numero.
</p><p>
4) Luokkia ei voi jälkikäteen muokata, joten valitsethan heti kutsua luodessasi oikeat luokat. Jos käy kämmi, poista kutsu ja luo uusi!

</p>



<script type="text/javascript">

function Pager(tableName, itemsPerPage) {

this.tableName = tableName;

this.itemsPerPage = itemsPerPage;

this.currentPage = 1;

this.pages = 0;

this.inited = false;

this.showRecords = function(from, to) {

var rows = document.getElementById(tableName).rows;

// i starts from 1 to skip table header row

for (var i = 1; i < rows.length; i++) {

if (i < from || i > to)

rows[i].style.display = 'none';

else

rows[i].style.display = '';

}

}

this.showPage = function(pageNumber) {

if (! this.inited) {

alert("not inited");

return;

}

var oldPageAnchor = document.getElementById('pg'+this.currentPage);

oldPageAnchor.className = 'pg-normal';

this.currentPage = pageNumber;

var newPageAnchor = document.getElementById('pg'+this.currentPage);

newPageAnchor.className = 'pg-selected';

var from = (pageNumber - 1) * itemsPerPage + 1;

var to = from + itemsPerPage - 1;

this.showRecords(from, to);

}

this.prev = function() {

if (this.currentPage > 1)

this.showPage(this.currentPage - 1);

}

this.next = function() {

if (this.currentPage < this.pages) {

this.showPage(this.currentPage + 1);

}

}

this.init = function() {

var rows = document.getElementById(tableName).rows;

var records = (rows.length - 1);

this.pages = Math.ceil(records / itemsPerPage);

this.inited = true;

}

this.showPageNav = function(pagerName, positionId) {

if (! this.inited) {

alert("not inited");

return;

}

var element = document.getElementById(positionId);

var pagerHtml = '<span onclick="' + pagerName + '.prev();" class="pg-normal"> « Prev </span> ';

for (var page = 1; page <= this.pages; page++)

pagerHtml += '<span id="pg' + page + '" class="pg-normal" onclick="' + pagerName + '.showPage(' + page + ');">' + page + '</span> ';

pagerHtml += '<span onclick="'+pagerName+'.next();" class="pg-normal"> Next »</span>';

element.innerHTML = pagerHtml;

}

}

</script>


<?php
	echo '<table border="0" width="100%" id="tablepaging">';
	echo '<tr><td class="tk">Pvm</td><td class="tk">Vip</td><td class="tk">Laji</th><td class="tk">Järjestäjä/järjestyspaikka</th><td class="tk">Kutsu</th><td class="tk">Editori</th>';
	echo '';
	
foreach($kisat as $kisa){

		$id = $kisa["id"];
		$pvm = $kisa["pvm"];
		$vip = $kisa["vip"];
		$laji = $kisa["laji"];
		$otsikko = $kisa["otsikko"];
		
		if ($kisa["oma"] == 0){
			$paikka =  $kisa["talli_nimi"];
			$paikka_url = $kisa["talli_url"];;
			$user = $kisa["username"];
		}
		
		else {
			$paikka = "Karkurannan ponitalli";
			$paikka_url = base_url();
			$user = "Marsupieni";
		}

		
		if ($kisa['hilight_notnumbered'] == true){
			echo '<tr class="varattuponi">';
			$vip = "<b>" .  $vip . "</b>";
		}
		
		else if ($kisa['hilight_vip'] == true){
			echo '<tr class="myyntiponi">';
			$vip = "<b>" .  $vip . "</b>";
		}
		
		else if ($kisa['downhilight_pvm'] == true){
			echo '<tr class="mennyt">';
		}
		else {
			echo "<tr>";
		}
		echo "<td>$pvm</td><td>$vip</td><td>$laji</td><td><a href=\"".$paikka_url."\">$paikka</a> ($user)</td><td><a href=\"".base_url()."kisakeskus/k/$id\">$otsikko</a></td>";
		echo "<td><a href=\"".base_url()."kisakeskus/kisaeditori/$id\"><img src=\"".base_url()."ulkoasu/kisakeskus/edit.png\" /></a> <a href=\"".base_url()."kisakeskus/kisaeditori/$id/1/SURE\" onclick=\"return confirm('Oletko varma, että haluat poistaa tämän kutsun?')\"><img src=\"".base_url()."ulkoasu/kisakeskus/delete.png\" /></a></td>";
		echo "</tr>";
	}
	echo "</table>";
	
?>
<div id="pageNavPosition" style="padding-top: 20px" align="center">
</div>
<script type="text/javascript"><!--
var pager = new Pager('tablepaging', 20);
pager.init();
pager.showPageNav('pager', 'pageNavPosition');
pager.showPage(1);
</script>
