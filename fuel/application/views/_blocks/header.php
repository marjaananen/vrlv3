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
					<?php if (!empty($sidemenu)) : ?>
					<?php echo $sidemenu; ?>
					<?php endif ?>

  				</div>
				
				<a href="<?php echo base_url();?>profiili">Oma profiili</a>

      		</div>  

      		<div class="col-md-9">

	<?php echo fuel_nav(array('render_type' => 'breadcrumb', 'container_tag_class' => 'breadcrumb', 'delimiter' => '&nbsp;', 'order' => 'desc', 'home_link' => 'Etusivu','depth'=>'3'));?>

