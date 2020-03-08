<?php
session_start();
?>

<!DOCTYPE html>
<html lang = "pl">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta charset="utf-8">

    <link rel="icon" href="img/mdb-favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/mdb.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="StyleSheet" href="scss/core/app/home.css"/>
    <link rel="StyleSheet" href="scss/core/app/index.css" />

    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/mdb.min.js"></script>
    <script type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="StyleSheet" href="scss/core/app/home.css" />
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
        W tej sekcji możesz sporządzić różne raporty i podsumowania. Wybierz interesujący Cię raport z listy.
    </section>
    <div>

    </div>
    <div class="footer">
        © 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
    </div>
</div>

</body>
</html>

