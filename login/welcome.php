<?php
session_start();

if(!isset($_SESSION['register_success']))
{
	header('Location:index.php');
	exit(); 						//reszta php się nie wykona
}
else
{
	unset($_SESSION['register_success']);	
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
	Thank You for registration. You can now login into your account
	<br><br>

	<a href = "index.php">Login</a>
	

</body>

</html>