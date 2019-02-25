<!DOCTYPE html>
<html lang="fi">
<head>
	<title> KARUS hevosurheilukeskus </title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=utf8">
	
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<!-- DataTables CSS -->
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap.css">
	<link rel="stylesheet" href="<?php echo base_url('/ulkoasu/kisakeskus/tyylit.css');?>" />	
</head>
<body><div id="kokoylaosa">
<div id="viiva"></div>
<div id="teksti">
<span id="header">
		Karus hevosurheilukeskus
</span><br />
<em>
	Virtuaalitalli, elikkä päästä keksitty kisakeskus porrastetuille kisoille.
</em>
</div>
<div id="linkkialue">
	<a href="http://karkuranta.marsupieni.net/kisakeskus">Kilpailukalenteri</a>
	<a href="http://karkuranta.marsupieni.net/kisakeskus/esittely">Esittely</a>
	<a href="http://karkuranta.marsupieni.net/kisakeskus/stats">Statistiikka</a>
	<a href="http://karkuranta.marsupieni.net/kisakeskus/paneeli">Käyttäjäpaneeli</a>
	<a href="http://marsupieni.net">marsupieni.net</a>
</div></div>
<div id="tekstialue">
	<div id="vasen">
	<h2>Tervetuloa</h2>
	<p>Karus hevosurheilukeskus tarjoaa yhteistyökumppaniensa kanssa porrastettuja kilpailuja helpolla osallistumistavalla. Tervetuloa osallistumaan!</p>
	<h2> Kirjautuminen </h2>
	
	<p> Kisakeskuksen kilpailunjärjestäjät voivat kirjautua sisään ja ulos tästä. Tunnuksia jaetaan rajoitetusti, eli vapaata tunnuksen luontia ei ole. </p>
	
	<?php if(!$logged_in){?>
	<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("kisakeskus/login");?>

  <p>
    <?php echo "Sähköpostiosoite";?>
    <?php echo form_input($identity);?>
  </p>

  <p>
     <?php echo "Salasana";?>
    <?php echo form_input($password);?>
  </p>

  <p>
    <?php echo "Muista minut";?>
    <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
  </p>


  <p><?php echo form_submit('submit', 'Kirjaudu');?></p>

<?php echo form_close();?>

<p><a href="auth/forgot_password"><?php echo lang('login_forgot_password');?></a></p>

<?php }

else {
	echo '<a href="kisakeskus/logout">Log out</a>';
	}
?>
	
	</div>
	<div id="oikea">