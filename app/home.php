<?php 
    session_start();
    if(isset($_SESSION['user']) != true && isset($_SESSION['is_logged']) != true)
    {
        header('Location:index.php');
        exit();
    }
    require 'connect.php';
    include '../php/class_user.php';

    $loggedUser = new User();
?>

<!DOCTYPE html>
<html lang = "pl">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta charset="utf-8">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
	<link rel="StyleSheet" href="../scss/home.css" />
</head>

<body>

<div class="container-fluid">

	<header class ="header">
		<table width = 100%>
			<tr>
				<td style = "text-align:left">
					<h1 id="title"><a href ="index.php"><img src="../resources/instalplast.png" style="width:300px;"></h1>
				</td>
				<td style = "text-align:right">
					<span>Zalogowany jako: <?php echo $_SESSION['user'] ?>&ensp;</span><span><a href = "logout.php">Wyloguj</a></span>
				</td>
			</tr>
		</table>
	</header>
    <div class="nav">
        <ol>
            <li>
                <a  href ='#'>Wprowadzenie faktury</a>
            </li>
            <li>
                <a href ='reports.php'>Raporty i podsumowania</a>
            </li>
            <li>
                <a href ='#'>Korekty</a>
            </li>
            <li>
                <a href ='#'>Użytkownicy</a>
            </li>
        </ol>
    </div>
    <section class = "section">


    </section>
    <div class="footer">
    © 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
    </div>
</div>

</body>
</html>

