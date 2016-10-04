<!DOCTYPE html>
<html lang="en">

<head>
     
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?php echo base_url()?>css/_resources/images/interface/rw_small.ico">
    <?php echo $jquery_1_11_1; ?>

    <title><?php echo $sys['public_name']; ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo base_url(); ?>css/bootstrap/admin2/bower_components/bootstrap/dist/css/<?php echo $sys['bootstrapmin'] ?>" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php echo base_url(); ?>css/bootstrap/admin2/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="<?php echo base_url(); ?>css/bootstrap/admin2/dist/css/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url(); ?>css/bootstrap/admin2/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="<?php echo base_url(); ?>css/bootstrap/admin2/bower_components/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo base_url(); ?>css/bootstrap/admin2/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Jquery UI CSS -->
        <!-- fuente http://jqueryui.com/dialog/ -->
    <script src="<?php echo base_url(); ?>js/jquery-ui.min.js"></script>
    <link href="<?php echo base_url(); ?>css/jquery-ui.min.css" rel="stylesheet" type="text/css" />

    <!-- simple-sidebar --> 
    <link href="<?php echo base_url(); ?>css/simple-sidebar.css" rel="stylesheet" type="text/css" />
    
    <!-- smarttool -->
    <link href="<?php echo base_url(); ?>css/<?php echo $sys['css'] ?>" rel="stylesheet">

    <?php 
    // *** TOKEN INPUT *** //
    if(!empty($tokeninput_js)):
    echo $tokeninput_js;
    echo $tokeninput_css;
    endif;

    if(!empty($jquery_redirect))
    echo $jquery_redirect;

     ?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Esta variable viene del archivo MY_Loader.php -->

</head>

<body>
<div class="col-sm-12 col-md-12 col-lg-12">
    <div id="wrapper">
        <div id="ajax_loading"></div>
            <div class="container-fluid">