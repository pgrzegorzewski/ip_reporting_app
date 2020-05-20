<?php
    session_start();
    if(isset($_SESSION['user']) == true && isset($_SESSION['is_logged']) == true)
    {
        header('Location:./content/core/home.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Instalplast rozliczenia</title>

    <link rel="icon" href="resources/ip_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/mdb.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="StyleSheet" href="scss/core/app/index.css" />
    <link rel="StyleSheet" href="scss/core/app/home.css"/>

    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/mdb.min.js"></script>
    <script type="text/javascript"></script>
</head>
<body>


    <div class="container-fluid">
        <div class = "container section-dark">
            <div class="form"><img src="resources/instalplast.png" style="width:300px;"></div><br/>

            <form action = "./content/core/login.php" method = "post">

                <div class="form">Login:<br /><input type="text" name="login" /><br/><br /></div>
                <div class="form">Hasło:<br /> <input type="password" name="password" /><br /><br/></div>
                <div class="form"><input class="btn btn-info" type = "submit" value = "Login" /></div>

            </form>
            <?php
            if (isset($_SESSION['error']) == true)
            {
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            }
            ?>


        </div>
        <div class="footer">
            © 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
        </div>
    </div>
<body>

</html>
