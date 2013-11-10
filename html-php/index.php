<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once("./php/project.php");

$project = new Project;
$project->project_create_project();

?>









<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">

    <title>Tietokantasovellus</title>

    <!-- Bootstrap core CSS -->
    <link href="./bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="style.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="./bootstrap/js/bootstrap.min.js"></script>
    <script src="skripti.js"></script>

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body onload=" do_on_load();">

	    <div class="navbar navbar-inverse navbar-fixed-top" style="height:30px">
	      <div class="container" s>
		<div class="navbar-header" >
		  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse" >
		    <span class="icon-bar"></span>
		    <span class="icon-bar"></span>
		    <span class="icon-bar"></span>
		  </button>
		</div>
		<div class="collapse navbar-collapse" style="max-height:30px">
		  <ul class="nav navbar-nav" >
			<li>
			  <a data-toggle="dropdown" style="padding:5px;margin-left:5px;" href="#">Tietokanta</a>
			  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel" >
			    <li><a class="sis" href="#">Ominaisuudet</a></li>
			    <hr/>
			    <li><a data-toggle="modal" data-target="#uusi_taulu" class="sis" href="#">Uusi taulu</a></li>
			    <li><a data-toggle="modal" data-target="#uusi_asetelma" class="sis" href="#">Uusi asetelma</a></li>
			    <hr/>
			    <li><a class="sis" href="#">Hallitse tauluja ja asetelmia</a></li>
			    <hr/>
			  </ul>
			</li>

			<li>
			  <a data-toggle="dropdown" style="padding:5px;margin-left:10px;" href="#">Käyttäjät</a>
			  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
			    <li><a class="sis" href="#">Lisää käyttäjä</a></li>
			    <hr/>
			    <li><a class="sis" href="#">Hallitse käyttäjien oikeuksia</a></li>
			    <li><a class="sis" href="#">Poista käyttäjä</a></li>
			    <hr/>
			  </ul>
			</li>

			<li>
			  <a data-toggle="dropdown" style="padding:5px;margin-left:10px;" href="#">Apua</a>
			  <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
			    <li><a class="sis" href="#">Ohjeita</a></li>
			    <hr/>
			  </ul>
			</li>
		  </ul>
		</div><!--/.nav-collapse -->
	      </div>
	    </div>

	    <div class="project">
		<ul class="nav nav-tabs">
			<?php $project->project_create_layouts(); ?>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<?php $project->project_create_layout_workbenchs(); ?>
		</div>

	    </div><!-- /.container -->

	<div id="status">
	<span style="margin:5px;">Rivi </span><span id="row_span" style="margin:5px;">1</span>
	</div>


	<div id="uusi_taulu" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Luo uusi taulu</h4>
	      </div>
	      <div class="modal-body">
		<div style="width:70%;float:left;"><p style="margin:5px;">Uuden taulun nimi:</p><input id="taulun_nimi" style="width:100%" type="text" /></div>
		<div style="width:30%;float:right;text-align:right;"><p style="margin:5px;">Sarakkeiden määrä:</p><input id="sarakkeiden_maara" style="width:40%" type="text" /></div>
	      </div>
		<div style="clear:both;"></div>
		<div style="margin:20px;">
		<p style="margin:5px;">Sarakkeet:</p>
		<div id="sarakkeet_muok" style="text-align:center;width:100%;height:200px;border-color:rgb(200,200,200);border-style:solid;border-width:1px;overflow-y:scroll;">
		</div>
		</div>
	
	      <div class="modal-footer">
		<button type="button" style="" class="btn btn-default" onclick="refresh_columns();">Päivitä</button>
		<button type="button" class="btn btn-default" data-dismiss="modal">Sulje</button>
		<button type="button" class="btn btn-primary" onclick="create_table();">Luo uusi taulu</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<div id="uusi_asetelma" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Luo uusi asetelma</h4>
	      </div>
	      <div class="modal-body">
		<p style="margin:5px;">Uuden asetelman nimi:</p><input id="asetelman_nimi" style="width:100%" type="text" />
		<p style="margin:5px;">SQL:</p><input id="asetelman_sql" style="width:100%" type="text" />
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Sulje</button>
		<button type="button" class="btn btn-primary" onclick="create_new_layout()">Luo uusi asetelma</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->





    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>

