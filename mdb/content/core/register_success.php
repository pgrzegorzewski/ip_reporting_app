<?php
    session_start();
    if(isset($_SESSION['registrationSuccessful']) != true)
    {
        header('Location:../../index.php');
        exit();
    }
    header( "refresh:4; url=../../index.php" );
?>

<!DOCTYPE HTML>
<html>

<head>
	<meta charset = "utf-8"/>
	<meta http-equiv ="X-UA-COMPATIBLE" content = "IE=edge,chrom=1"/>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
  <link rel="StyleSheet" href="../../scss/core/app/home.css"/>
  <link rel="StyleSheet" href="../../scss/core/app/index.css"/>
	<title>Zarejestrowany</title>
</head>
<body>
	<div class="container-fluid">
    	<div class = "container section">
    	 	<div class="form">
                <img src="../../resources/instalplast.png" style="width:300px;">
    	 		<br />
    	 		<br />
        	 	Rejestracja zakończona sukcesem<br />
        	 	Za chwilę zostaniesz przeniesiony na stronę logowania.
        	</div>

    	</div>
    <div class="footer">
		© 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
	</div>
    </div>
</body>


</html>
