<?php 
	session_start();
	
	if(isset($_SESSION['islogged']) && $_SESSION['islogged'] == true)
	{
		header('Location:main.php');
		exit(); 						//reszta php się nie wykona
	}
?>

<!DOCTYPE HTML>
<html lang="pl-PL">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content ="IE=edge,chrome=1" />
	<title>Login</title>
</head>

<body>
	Witamy na stronie logowania.
	<br><br>

	<a href = "register.php">Register</a>
	
	<form action = "login.php" method="POST">
		
		Login:<br/><input type = "text" name = "login" /><br/>
		Hasło:<br/><input type = "password" name = "password" /><br/>
		<input type = "submit" value = "Login" />
	
	</form>

	<?php 
		if(isset($_SESSION['error']))
			echo $_SESSION['error'];
	
	?>




</body>

</html>