<?php 
    session_start();
    if(isset($_SESSION['user']) == true && isset($_SESSION['is_logged']) == true)
    {
        header('Location:home.php');
        exit();
    }
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
	<title>InstalPlast Łask księgowość</title>
</head>
<body>
	<div class="container-fluid">
    	<div class = "container section">   
            <div class="form"><img src="../resources/instalplast.png" style="width:300px;"></div>
    	 	
        	<form action = "login.php" method = "post">

            	<div class="form">Login:<br /><input type="text" name="login" /><br/></div>
            	<div class="form">Password:<br /> <input type="password" name="password" /><br /><br/></div>
            	<div class="form"><input id="login_btn" type = "submit" value = "Login" /></div>
        	
        	</form>
        	<?php 
        	  if (isset($_SESSION['error']) == true)
        	  {
        	      echo $_SESSION['error'];
        	      unset($_SESSION['error']);
        	  }
        	?>
        	<div class="form">
        		<br/><br />
        		<h5>Nie posiadasz jeszcze konta?</h5>
        		<br />
        		<input id="register_btn" type="submit" value="Zarejestruj" 
    			onclick="window.location='register.php';" /> 
        	</div>
        	
    	</div>
        <div class="footer">
            © 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
        </div>
    </div>
</body>


</html>