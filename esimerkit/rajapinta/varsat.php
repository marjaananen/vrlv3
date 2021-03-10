


<?php 
//Muokkaa tähän hevosesi VH-tunnus
$vh = 'VH03-028-8756';
$url = 'http://virtuaalihevoset.net/rajapinta/varsat/'.$vh;
$obj = json_decode(file_get_contents($url), true);

if(isset($obj['error']) && $obj['error'] == 0){
    $varsat = $obj['varsat'];
    
    if(sizeof($varsat) == 0){
        echo "Ei jälkeläisiä";
    }
    else {
        foreach ($varsat as $varsa){
            $reknro =  $varsa['reknro'];
            $nimi = $varsa['nimi'];
            $rotunro = $varsa['rotunro']; //katso VRL:stä mikä numero vastaa mitäkin rotua
            $rotulyhenne = $varsa['rotulyhenne'];
            $vari =  $varsa['vari']; //katso VRL:sta mikä numero vastaa mitäkin väriä
            $varilyhenne = $varsa['varilyhenne'];
            $sukupuoli = $varsa['sukupuoli']; // 1=tamma, 2=ori, 3=ruuna
            $syntymaaika = $varsa['syntymaaika'];
            $url = $varsa['url'];
            $rek_url = $varsa['rek_url'];
            $vanhempi = $varsa['vanhempi']; //samassa muodossa kuin varsan omat tiedot
            
            
            $skp_kirjain = array(1=>"t", 2=>"o", 3=>"r");
            $vanhempi_kirjain = array(1=>"e", 2=>"i", 3=>"i");
    
            //tulostus
            echo $syntymaaika . ", " . $skp_kirjain[$sukupuoli] . ". <a href=\"".$url."\">".$nimi."</a> (<a href=\"".$rek_url."\">".$reknro."</a>)";
            
            if(isset($vanhempi) && sizeof($vanhempi)>0){
                echo ", " . $vanhempi_kirjain[$vanhempi['sukupuoli']] . ". <a href=\"".$vanhempi['url']."\">".$vanhempi['nimi']."</a> (<a href=\"".$vanhempi['rek_url']."\">".$vanhempi['reknro']."</a>)";
            }
            echo "<br>";
            
        }
    }
    
    
    
    
}else if($obj['error'] == 1){
    echo $obj['error_description'];
}else {
    echo "Tapahtui odottamaton virhe!";
}

?>