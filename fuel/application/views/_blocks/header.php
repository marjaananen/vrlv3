<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>
		<?php 
			echo fuel_var('page_title', '');
		?>
		</title>
		<meta name="generator" content="Bootply" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo site_url();?>assets/css/vrl.css">
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<style type="text/css">
		<!--
			header {
				margin-bottom:30px;
			}
		-->
		</style>
		<!-- script references -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		
		
		
		<!-- DataTables CSS -->
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/jquery.dataTables.min.css">
		<!--- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.css">---!>
		<!-- DataTables -->
		<!--<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.9/js/jquery.dataTables.js"></script>-->
		<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
		<!-- moment -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script>
	$.fn.dataTable.moment = function ( format, locale ) {
    var types = $.fn.dataTable.ext.type;
 
    // Add type detection
    types.detect.unshift( function ( d ) {
        return moment( d, format, locale, true ).isValid() ?
            'moment-'+format :
            null;
    } );
 
    // Add sorting method - use an integer for the sorting
    types.order[ 'moment-'+format+'-pre' ] = function ( d ) {
        return moment( d, format, locale, true ).unix();
    };
};
</script>
		
		<?php echo js('jqcloud.min.js'); ?>
		<?php echo css('jqcloud.min.css'); ?>
		<?php echo css('vrl.css'); ?>
	</head>
	<body>
<header class="navbar navbar-default navbar-static-top" role="banner">
  <div class="container">
    <div class="navbar-header">
      <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="/" class="navbar-brand">VRL</a>
    </div>
    <nav class="collapse navbar-collapse" role="navigation">
	  <?php echo fuel_nav(array('container_tag_id' => 'topmenu', 'container_tag_class' => 'nav navbar-nav', 'item_id_prefix' => 'topmenu_', 'depth'=>'0')); ?>
    </nav>
  </div>
</header>
<!-- Begin Body -->
<div class="container">
	<div class="row">
  			<div class="col-md-3" id="leftCol">
              	
				<div class="well"> 
					<?php if (!empty($sidemenu)) { echo $sidemenu;} else {echo $main_quickmenu;} ?>
  				</div>
				
				<div id="infoMessage"><b><?php echo fuel_var('message', '');?></b></div>
<?php
//kirjautumislomake / profiili ja logout
	if(!empty($this->ion_auth))
	{
		if ($this->ion_auth->logged_in())
		{
			echo "<p>Tervetuloa, " . $this->session->userdata( 'tunnus' ) . "<br /> <a href=" . site_url('/auth/logout') . ">Kirjaudu ulos</a> <br /> <a href=" . site_url('/profiili') . ">Profiili</a>";
			echo "<br />Sinulle on " . $this->tunnukset_model->unread_messages($this->session->userdata('identity')) . " uutta <a href=" . site_url('/profiili/pikaviestit') . ">pikaviestiä</a>.</p>";
			if ($this->ion_auth->is_admin()){
				echo "<p>Olet ylläpitäjä, tästä pääset ylläpitäjähommiin:</p>";
				echo $adminmainmenu;
			}
		}
		else
		{
			
			$identity = [
				'name' => 'identity',
				'id' => 'identity',
				'type' => 'text',
				'value' => "",
				'class' => 'form-control',
			];

			$password = [
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
				'class' => 'form-control',
			];
			
			$hidden = array('url'=>current_url());
			
			?>
				
			<?php echo form_open("auth/login", '', $hidden);?>

			<div class="panel panel-default">
				<div class="panel-body">
                    <div class="form-group">
						<label for="identity" id="label_identity">Tunnus<span class="required">*</span></label><p><?php echo form_input($identity);?></p>
					</div>
                    <div class="form-group">
						<label for="password" id="label_password">Salasana<span class="required">*</span></label><p><?php echo form_input($password);?></p>
					</div>
					<div class="form-group">
						<label for="remember" id="label_remember">Muista minut</label> <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?> 
					</div>
				</div>
            </div>
			  <p><?php echo form_submit('submit', "Kirjaudu");?></p>

			<?php echo form_close();?>

    			
			
			
			
			<?php
			
			echo "<br /><a href='" . site_url('/auth/forgot_password') . "'>Unohtuiko salasana?</a>";
		}
	}
?>
      		</div>  
      		<div class="col-md-9">
	<?php echo fuel_nav(array('render_type' => 'breadcrumb', 'container_tag_class' => 'breadcrumb', 'delimiter' => '&nbsp;', 'order' => 'desc', 'home_link' => 'Etusivu','depth'=>'3'));?>