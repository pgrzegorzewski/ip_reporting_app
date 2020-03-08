<?php 
    session_start();
    if(isset($_SESSION['registrationSuccessful']) != true)
    {
        header('Location:index.php');
        exit();
    }
    header( "refresh:4; url=index.php" );
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
	<link rel="StyleSheet" href="../css/home.css" />
	<link rel="StyleSheet" href="../css/index.css" />
	<title>Registered</title>
</head>
<body>
	<div class="container-fluid">
    	<div class = "container section">   
    	 	<div class="form">
    	 		<h1><b>Q</b>u¿zzy</h1>
    	 		<br />
    	 		<br />
        	 	Registration successful!<br /> 
        	 	You will be redirected to login page in few seconds. We hope You will have fun with Qu¿zzy!;)
        	</div>
        	
    	</div>
    <div class="footer">
		© 2018 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
	</div>
    </div>
</body>


</html>