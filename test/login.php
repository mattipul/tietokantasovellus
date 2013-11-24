<?php

session_start();

if( empty( $_SESSION['id'] ) );
else{
	header("Location: index.php");
	die();
}

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

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container" style="max-width:400px">

      <form class="form-signin">
        <h2 class="form-signin-heading">Tietokantasovellus</h2><br/>
		<p id="error" style="color:red"></p>
        <input type="text" id="user" class="form-control" placeholder="Käyttäjätunnus" required autofocus><br/>
        <input type="password" id="pass" class="form-control" placeholder="Salasana" required><br/>

        <button class="btn btn-lg btn-primary btn-block" onclick="auth();" type="button">Kirjaudu sisään</button>
      </form>
	  Käyttäjätunnus:admin, Salasana:admin

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
  <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  <script>
  
  function auth(){
	var user_str=$("#user").val();
	var pass_str=$("#pass").val();
	$.post( "php/handle_post.php", { type:8, user:user_str, pass:pass_str})
	.done(function(data ) {
		if(data==1){
			window.location = "index.php";
		}else{
			$("#error").html("Virhe! Väärä käyttäjätunnus tai salasana.");
		}
	});
  }
  
  </script>
  
</html>
