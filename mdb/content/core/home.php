<?php
    session_start();
    if(isset($_SESSION['user']) != true && isset($_SESSION['is_logged']) != true)
    {
        header('Location:../../index.php');
        exit();
    }
    require './connect.php';
    include './../class/class_user.php';

    $loggedUser = new User();

?>

<!DOCTYPE html>
<html lang = "pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Instalplast księgowość</title>

    <link rel="icon" href="../../resources/ip_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/mdb.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="StyleSheet" href="../../scss/core/app/home.css"/>

    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <script type="text/javascript" src="../../js/popper.min.js"></script>
    <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../js/mdb.min.js"></script>
    <script type="text/javascript"></script>
	   <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">

</head>

<body>

<div class="container-fluid">

	<header class ="header">
		<table width = 100%>
			<tr>
				<td style = "text-align:left">
					<h1 id="title"><a href ="../../index.php"><img src="../../resources/instalplast.png" style="width:300px;"></h1>
				</td>
				<td style = "text-align:right">
					<span>Zalogowany jako: <?php echo $_SESSION['user'] ?>&ensp;</span><a href = "logout.php"><button class="btn btn-danger" value="">Wyloguj</button></a>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#changePasswordModal">
                        Zmień hasło
                    </button>
				</td>
			</tr>
		</table>
	</header>
		<div class="nav">
			<ol>
				<li>
					<a href ='../invoice_import/invoice_import.php'>Wprowadzenie faktury</a>
				</li>
				<li>
					<a href ='../reports/reports.php'>Raporty i podsumowania</a>
				</li>
				<li>
					<a href ='#'>Korekty</a>
				</li>
                <?php
                $query = "
                       SELECT * FROM  usr.sf_sprawdz_prawo_dostepu('" . $_SESSION['user'] . "', 1)
                ";
                $hasAccessQuery = @pg_query($connection, $query);
                $hasAccess = pg_fetch_assoc($hasAccessQuery);
                if($hasAccess['sf_sprawdz_prawo_dostepu'] == 1) {
                    echo "<li>
                        <a href ='../admin/user/user.php'>Użytkownicy</a>
                    </li>";
                }
				?>
			</ol>
		</div>

        <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordLabel">Zmień hasło</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form  action = "change_password.php" method="post">
                            Stare hasło: <br />
                            <input type = "password" name="old_password"><br /><br />

                            Nowe hasło: <br />
                            <input type = "password" name="new_password"><br /><br />

                            Powtórz hasło: <br />
                            <input type = "password" name="new_password_2"><br /><br />

                            <div class="modal-footer">
                                <input class="btn btn-info" type = "submit" value = "Zmień hasło" />
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if(isset($_SESSION['e_password'])){
            echo '<div class = "error">'.$_SESSION['e_password'].'</div>';
            unset($_SESSION['e_password']);
        }
        ?>
		<section class = "section"></section>
		<div class="footer">
		© 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
		</div>
	</div>

</body>
</html>
