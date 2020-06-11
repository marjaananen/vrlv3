function getCheckboxes(name) {
    // returns array of checked checkboxes with 'name' in 'form_id'
    var inputs = document.getElementsByName(name);
    var values = [];
    for (var i = 0; i < inputs.length; ++i) {
        if (inputs[i].type === "checkbox" && 
            inputs[i].checked) 
        {
              values.push(inputs[i].value);
        }
    }
    return values;
}

function getHiddens(name) {
    // returns array of checked checkboxes with 'name' in 'form_id'
    var inputs = document.getElementsByName(name);
    var values = [];
    for (var i = 0; i < inputs.length; ++i) {
	if (inputs[i].type === "hidden"){
              values.push(inputs[i].value);
		}
	}
    return values;
}

function getRadio(name){
    // returns array of checked radioboxes with 'name' in 'form_id'
	var radios = document.getElementsByName(name);
	var value = 0;
	for (var i = 0, length = radios.length; i < length; i++) {
    if (radios[i].checked) {
        value = radios[i].value;
    }
}
    return value;
}

function jarjestysTaulu(){
var taulu = new Array("gen_tvkko",	"gen_mush", "gen_kirj", "gen_kirj_tkirj", "gen_kirj_t", "gen_kirj_s", "gen_kirj_fo", "gen_kirj_spl",  "gen_km", "gen_pais", "gen_hkko", "gen_vkko", "rn", "rt", "m", "emtpohja", "gen_cha", "gen_hp");
return taulu;
	}
	
function perusmuotoTaulu(){
var taulu = new Array("tuplavoikko",	"mushroom",  "kirjava", "tiikerinkirjava", "kirjava tobiano", "kirjava sabino", "kirjava frame overo", "kirjava splashed white", "kimo", "p&auml;ist&auml;rikk&ouml;", "hallakko", "voikko", "ruunikko", "rautias", "musta", "", "samppanja", "hopea");
return taulu;
	}

function genetiiviTaulu(){
var taulu = new Array("tuplavoikko",	"mushroom", "kirjavan", " tiikerin", " tobianon", " sabinon", " frame overon", " splashed whiten", "kimon", "p&auml;ist&auml;rik&ouml;n", "hallakon", "voikon", "ruunikon", "rautiaan", "mustan", "", "samppanjan", "hopean");
return taulu;
	}

function muodosta_vari (varit, perusvarit) {
	var jarjestys = jarjestysTaulu();
	var perusmuoto = perusmuotoTaulu();
	var genetiivi = genetiiviTaulu();
	var varinimi = "null";
	var haettava = "";
	var sijainti = -1;
	var edellinen = "";
	
	for (var i = 0, length = jarjestys.length; i < length; i++) {
		haettava = jarjestys[i];
		sijainti = varit.indexOf(haettava);
		if (sijainti != -1){
			//jos on tuplavoikko niin muita turha selata
			if(perusvarit == "m" && jarjestys[i] == "gen_tvkko"){
				varinimi = "smoky cream";
				break; //muita ei selata
			}
			else if(perusvarit == "rt" && jarjestys[i] == "gen_tvkko"){
				varinimi = "cremello";
				break; //muita ei selata
			}		
			if(perusvarit == "rn" && jarjestys[i] == "gen_tvkko"){
				varinimi = "perlino";
				break; //muita ei selata
			}			
			//jos on sanan viimeinen, tulee perusmuodossa
			else if (varinimi == "null"){
				if (perusvarit == "m" && jarjestys[i] == "gen_hkko"){
					varinimi = "hiirakko";
				}
				
				else{
					varinimi = perusmuoto[i];
				}
			
			}
			
		
			else {
				if(perusvarit == "m" && jarjestys[i] == "gen_hkko"){
					var sana = "hiirakon";
					varinimi = sana.concat(varinimi);
					}
			
			
				else if (jarjestys[i] == "rt" && edellinen == "gen_hkko"){
					var sana = "puna";
					varinimi = sana.concat(varinimi);
					}
					
				else if ((jarjestys[i] == "m" && edellinen == "gen_hkko") || (jarjestys[i] == "rt" && edellinen == "gen_vkko")){
					var sana = "";
					varinimi = sana.concat(varinimi);
					}
					
				else if ((jarjestys[i] == "rn" && edellinen == "gen_hkko") || (jarjestys[i] == "rn" && edellinen == "gen_vkko")){
					var sana = "ruuni";
					varinimi = sana.concat(varinimi);
					}
				else {
					var sana = genetiivi[i];
					varinimi = sana.concat(varinimi);
				}
				}
	
		edellinen = haettava;
		}
	}
	return varinimi;
}

function tulosta_vari(perusnimi, erikoisnimi) {
	//haetaan taulukkoon valitut arvot
	var varit = getCheckboxes(erikoisnimi);
	var perusvarit = getRadio(perusnimi);
	if (perusvarit != 0){
	varit.push(perusvarit);
	}
	//nyt on kaikki v&auml;rit varit-arrayssa.
	
	var varinimi = muodosta_vari(varit, perusvarit);
	
	//alustetaan muuttuja tulostuspaikalle
	var paikka;
	
	//Jos klikattiin isän tietoja, päivitetään isän väriä, muuten emän.
	if (perusnimi == 'isapohja'){
		paikka = document.getElementById('isavari');
		}
		
	else{
		paikka = document.getElementById('emavari');
		}
	
	//tulostakaamme se.
	paikka.innerHTML = varinimi;
}


function tulosta_varsavari(perusnimi, erikoisnimi) {
	//haetaan taulukkoon valitut arvot
	var varit = getCheckboxes(erikoisnimi);
	var varmatvarit = getHiddens(erikoisnimi);
	var perusvarit = getHiddens(perusnimi);
	//varmat varit on erikoisvareja
	for (var i = 0, length = varmatvarit.length; i < length; i++) {
		varit.push(varmatvarit[i]);
		}
		
	for (var i = 0, length = perusvarit.length; i < length; i++) {
		var perusvari = perusvarit[i];
		var taulu = new Array (perusvarit[i]);
		var tulostettavat = varit.concat(perusvari);
	//nyt on kaikki v&auml;rit varit-arrayssa.
		var varinimi = muodosta_vari(tulostettavat, perusvari);
	//alustetaan muuttuja tulostuspaikalle
		var paikka;
	//Jos klikattiin isän tietoja, päivitetään isän väriä, muuten emän.
		if (perusvarit[i] == 'rn'){
		paikka = document.getElementById('rnkohta');
		}
		else if (perusvarit[i] == 'rt'){
		paikka = document.getElementById('rtkohta');
		}
		else if (perusvarit[i] == 'm'){
		paikka = document.getElementById('mkohta');
		}
	
	//tulostakaamme se.
	paikka.innerHTML = varinimi;
	}
	
}
