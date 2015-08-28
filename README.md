# vrlv3

Oletuksena tietokannan nimi on vrlv3, serveri localhost ja user root ilman salasanaa. 
Kopioi tiedosto fuel/application/config/database_skeleton.php samaan kansioon ja nimeä se pelkäksi database.php:ksi. 
Sitten muokkaa se vastaamaan omaa konfiguraatiotasi, mutta ÄLÄ COMMITOI TÄTÄ TIEDOSTOA, vaan laita se .gitignoreen vaikka tortoisegitin avulla!

Oletuksena salausavaimeksi on asetettu $config['encryption_key'] = 'test_test_test_test';
Kopioi tiedosto fuel/application/config/config_skeleton.php samaan kansioon ja nimeä se pelkäksi config.php:ksi. 
Sitten voit halutessasi muokata sen vastaamaan omaa konfiguraatiotasi, mutta ÄLÄ COMMITOI TÄTÄ TIEDOSTOA, vaan laita se .gitignoreen vaikka tortoisegitin avulla!

Kuvat, javascripta yms. sijoitetaan assets kansioon

Alkuunsa luo tietokanta, ja aja sinne database kansion _schema.sql tiedostot seuraavassa järjestyksessä:
fuel_schema.sql
ci_sessions_schema.sql
ion_auth.sql
listat_data_schema.sql 
tunnukset_schema.sql
tallirekisteri_schema.sql



# FUEL CMS
FUEL CMS is a [CodeIgniter](http://ellislab.com/codeigniter) based content management system. To learn more about its features visit: http://www.getfuelcms.com

### Installation
To install FUEL CMS, copy the contents of this folder to a web accessible 
folder and browse to the index.php file. Next, follow the directions on the 
screen. 

### Documentation
To access the documentation, you can visit it [here](http://docs.getfuelcms.com).

### Bugs
To file a bug report, go to the [issues](http://github.com/daylightstudio/FUEL-CMS/issues) page.

### License
FUEL CMS is licensed under [Apache 2](http://www.apache.org/licenses/LICENSE-2.0.html). The full text of the license can be found in the fuel/licenses/fuel_license.txt file.

<br>
___

__Developed by David McReynolds, of [Daylight Studio](http://www.thedaylightstudio.com/)__



