<?php 
	session_start();
	if(!isset($_SESSION['islogged']))
	{
		header('Location:index.php');
		exit();
	}

?>


<!DOCTYPE HTML>
<html lang="pl-PL">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content ="IE=edge,chrome=1" />
	<title>Main</title>
</head>

<body>
	Welcome. You are logged into queezy:D
	
	
	<?php 
		
		echo "<p>Hello ".$_SESSION['user']."!</p>";
		echo "<p><i>".$_SESSION['email']."</i></p>";
		
		echo '<a href = "logout.php">Logout</a>';
		
	?>




</body>

</html>