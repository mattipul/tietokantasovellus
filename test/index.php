<?php
session_start();

if( empty( $_SESSION['id'] ) ){
	header("Location: login.php");
	die();
}

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
			    <li><a data-toggle="modal" data-target="#ominaisuudet" class="sis" href="#">Ominaisuudet</a></li>
			    <hr/>
			    <li><a data-toggle="modal" data-target="#uusi_taulu" class="sis" href="#">Uusi taulu</a></li>
			    <li><a data-toggle="modal" data-target="#uusi_asetelma" class="sis" href="#">Uusi asetelma</a></li>
			    <hr/>
			    <li><a data-toggle="modal" onclick="get_table_list();get_layout_list();" data-target="#hallitse" class="sis" href="#">Hallitse tauluja ja asetelmia</a></li>
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
				<li><a class="sis" onclick="sign_out();" href="#">Kirjaudu ulos</a></li>
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


		<div id="ominaisuudet" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Tietokannan ominaisuudet</h4>
	      </div>
	      <div class="modal-body">
		  <div style="display:table" >
			<table>
			<tr>
			<td><b>Tietokannan nimi:</b></td>
			<td>tietokanta</td>
			</tr>
			<tr>
			<td><b>Taulujen määrä:</b></td>
			<td></td>
			</tr>
			<tr>
			<td><b>Asetelmien määrä:</b></td>
			<td></td>
			</tr>
			<tr>
			<td><b>Tietokannan koko:</b></td>
			<td></td>
			</tr>
			<tr>
			<td><b>Rivien määrä:</b></td>
			<td></td>
			</tr>
			<tr>
			<td><b>Sarakkeiden määrä:</b></td>
			<td></td>
			</tr>
			</table>
			</div>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Sulje</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	
	<div id="hallitse" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Hallitse tauluja ja asetelmia</h4>
	      </div>
	      <div class="modal-body">
				<ul class="nav nav-tabs">
				  <li><a href="#hallitse_asetelma" data-toggle="tab">Asetelmat</a></li>
				  <li><a href="#hallitse_taulu" data-toggle="tab">Taulut</a></li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
				  <div class="tab-pane" id="hallitse_taulu">
				    <div style="width:65%;float:left">
						<select id="taulujen_nimet" style="padding:5px;width:100%; height:300px;" size="100">
						</select>
					</div>
					<div style="width:35%;float:right;text-align:right;">
						<a data-toggle="modal" data-target="#uusi_sarake" style="margin:5px;width:100%" href="#" class="btn btn-default"><i class="icon-chevron-right"></i> Lisää sarake</a>
						<a data-toggle="modal" data-target="#muuta_taulun_nimi" style="margin:5px;width:100%" href="#" class="btn btn-default"><i class="icon-chevron-right"></i> Taulun nimi</a>
						<a data-toggle="modal" data-target="#muuta_sarakkeen_nimi" style="margin:5px;width:100%" href="#" class="btn btn-default"><i class="icon-chevron-right"></i> Sarakkeen nimi/tyyppi</a>
						<a data-toggle="modal" data-target="#poista_sarake" style="margin:5px;width:100%" href="#" class="btn btn-default"><i class="icon-chevron-right"></i> Poista sarake</a>
						<a data-toggle="modal" data-target="#poista_taulu" style="margin:5px;width:100%" href="#" class="btn btn-default"><i class="icon-chevron-right"></i> Poista taulu</a>
					</div>
					<div style="clear:both"></div>
				  </div>
				  <div class="tab-pane" id="hallitse_asetelma">
				    <div style="width:65%;float:left">
						<select id="asetelmien_nimet" style="padding:5px;width:100%; height:300px;" size="100">
						</select>
					</div>
					<div style="width:35%;float:right;text-align:right;">
						<a data-toggle="modal" data-target="#muuta_asetelman_nimi" style="margin:5px;width:100%" href="#" class="btn btn-default"><i class="icon-chevron-right"></i> Nimi</a>
						<a data-toggle="modal" data-target="#muuta_asetelman_sqllauseke" style="margin:5px;width:100%" href="#" class="btn btn-default"><i class="icon-chevron-right"></i> SQL-lauseke</a>
						<a data-toggle="modal" data-target="#poista_asetelma" style="margin:5px;width:100%" href="#" class="btn btn-default"><i class="icon-chevron-right"></i> Poista asetelma</a>
					</div>
					<div style="clear:both"></div>
				  </div>
				</div>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Sulje</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	
	
	
	
	
	<!------------------>
	<!------------------>
	<!------------------>
	
		<div id="uusi_sarake" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Lisää tauluun uusi sarake</h4>
	      </div>
	      <div class="modal-body">
		<div style="background-color:rgb(240,240,240);padding:5px;width:100%">
		<span>Sarakkeen nimi:</span>
		<input id="uusi_sarakkeen_nimi" style="width:55%" type="text" />
		<select id="uusi_sarakkeen_tyyppi">
		<option value="-1">Tyyppi</option><option value="TEXT">TEXT</option><option value="INT">INT</option><option value="DATE">DATE</option><option value="DOUBLE">DOUBLE</option>
		</select>
		</div>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Sulje</button>
		<button type="button" class="btn btn-primary" onclick="add_column();">Lisää sarake</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<!------------------>
	<!------------------>
	<!------------------>
	
	<div id="muuta_taulun_nimi" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Muuta taulun nimeä</h4>
	      </div>
	      <div class="modal-body">
		<div style="background-color:rgb(240,240,240);padding:5px;width:100%">
		<span>Taulun nimi:</span>
		<input id="uusi_taulun_nimi" style="width:70%" type="text" />
		</div>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Sulje</button>
		<button type="button" class="btn btn-primary" onclick="change_table_name();">Muuta</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<!------------------>
	<!------------------>
	<!------------------>
	
	<div id="muuta_sarakkeen_nimi" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Muuta sarakkeen nimeä ja tyyppiä</h4>
	      </div>
	      <div class="modal-body">
		<div style="background-color:rgb(240,240,240);padding:5px;width:100%">
		<span>Sarakkeen nimi:</span>
		<input id="uusi_sarakkeen_nimi_muuta" style="width:50%" type="text" />
		<select id="muuta_sarakkeen_tyyppi">
		<option value="-1">Tyyppi</option><option value="TEXT">TEXT</option><option value="INT">INT</option><option value="DATE">DATE</option><option value="DOUBLE">DOUBLE</option>
		</select>
		</div>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Sulje</button>
		<button type="button" class="btn btn-primary" onclick="change_column_name();">Muuta</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<!------------------>
	<!------------------>
	<!------------------>
	
	
	
		<!------------------>
	<!------------------>
	<!------------------>
	
	<div id="poista_sarake" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Poistetaanko sarake?</h4>
	      </div>
	      <div class="modal-body">
			<p>Tämä on peruuttamaton toimenpide</p>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Ei</button>
		<button type="button" class="btn btn-primary" onclick="destroy_column()">Kyllä</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	
			<!------------------>
	<!------------------>
	<!------------------>
	
	<div id="poista_taulu" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Poistetaanko taulu?</h4>
	      </div>
	      <div class="modal-body">
			<p>Tämä on peruuttamaton toimenpide</p>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Ei</button>
		<button type="button" class="btn btn-primary" onclick="destroy_table()">Kyllä</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

			<!------------------>
	<!------------------>
	<!------------------>
	
	<div id="muuta_asetelman_nimi" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Muuta asetelman nimeä</h4>
	      </div>
	      <div class="modal-body">
		<div style="background-color:rgb(240,240,240);padding:5px;width:100%">
		<span>Asetelman nimi:</span>
		<input id="uusi_asetelman_nimi" style="width:70%" type="text" />
		</div>		
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Sulje</button>
		<button type="button" class="btn btn-primary" onclick="change_layout_name()">Muuta nimi</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

			<!------------------>
	<!------------------>
	<!------------------>
	
	<div id="muuta_asetelman_sqllauseke" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Muuta asetelman SQL-lauseketta</h4>
	      </div>
	      <div class="modal-body">
		<div style="background-color:rgb(240,240,240);padding:5px;width:100%">
		<span>SQL-lauseke:</span>
		<input id="uusi_sqllauseke" style="width:70%" type="text" />
		</div>		
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Sulje</button>
		<button type="button" class="btn btn-primary" onclick="change_layout_sql()">Muuta SQL-lauseke</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

			<!------------------>
	<!------------------>
	<!------------------>
	
	<div id="poista_asetelma" class="modal fade">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Poistetaanko asetelma?</h4>
	      </div>
	      <div class="modal-body">
			<p>Tämä on peruuttamaton toimenpide</p>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Ei</button>
		<button type="button" class="btn btn-primary" onclick="destroy_layout()">Kyllä</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>

