<?php
session_start();

if(isset($_POST['nick']))
{
    $success = true;

    $first_name = $_POST['first_name'];
    if($first_name == '' || strlen($first_name) < 2)
    {
        $success = false;
        $_SESSION['e_first_name'] = "Niepoprawne imię";
    }

    $last_name = $_POST['last_name'];
    if($last_name == '' || strlen($last_name) < 2)
    {
        $success = false;
        $_SESSION['e_last_name'] = "Niepoprawne nazwisko";
    }

    $nick = $_POST['nick'];
    if(strlen($nick) < 3 || strlen($nick) > 20)
    {
        $success = false;
        $_SESSION['e_nick'] = "Nick musi mieć długość od 3 do 20 znaków";
    }

    if(!ctype_alnum($nick))
    {
        $success = false;
        $_SESSION['e_nick'] = "Nick musi zawierać wyłącznie znaki alfanumeryczne nie zawierające akcentów";
    }


    $password = $_POST['password'];
    $password_2 = $_POST['password_2'];

    if(strlen($password) < 8 || strlen($password) > 20)
    {
        $success = false;
        $_SESSION['e_password'] = "Hasło musi posiadać od 8 do 20 znaków";

    }

    if($password != $password_2)
    {
        $success = false;
        $_SESSION['e_password'] = "Hasła są różne";

    }

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    require_once 'connect.php';

    try
    {
        if($connection)
        {
            $username_check = pg_query_params($connection, "SELECT * FROM usr.sf_sprawdz_unikalnosc_login($1) AS is_username_unique", array($nick));
            $is_username_check = pg_fetch_assoc($username_check);

            if($is_username_check['is_username_unique'] != 1)
            {
                $success = false;
                $_SESSION['e_nick'] = "Login w użyciu";

            }
            pg_free_result($username_check);


            if($success == true)
            {
                $role = 5;
                $isActive = true;
                //@pg_query_params($connection, "SELECT * FROM usr.sp_dodaj_uzytkownika($1, $2, $3, $4, $5)", array($nick, $password_hashed, $first_name, $last_name, $email));
                //$query = "SELECT * FROM usr.sp_dodaj_uzytkownika($1, $2, $3, $4, $5, $6)";
                pg_query_params($connection, $query, array($nick, $first_name, $last_name, $password_hashed, $role, $isActive));
                $_SESSION['registrationSuccessful'] = true;
                header('Location: register_success.php');
            }

            pg_close($connection);
        }
    }
    catch(Exception $err)
    {
        echo '<span style="color:red;">Server error';
        echo '<br/>Dev info: '.$error->getMessage();
    }

}


?>
<!DOCTYPE HTML>
<html>

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
    <link rel="StyleSheet" href="../../scss/core/app/index.css"/>

    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <script type="text/javascript" src="../../js/popper.min.js"></script>
    <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../js/mdb.min.js"></script>
    <script type="text/javascript"></script>
    <title>Rejestracja</title>
</head>
<body>
<div class="container-fluid">
    <div class = "container section-dark">
        <div class="form"><img src="../../resources/instalplast.png" style="width:300px;"></div><br />
        <div class="form"><h5 class="form-h">Formularz rejestracji</h4><br></div>
        <div class="form">
            <form method="post">

                Imię: <br />
                <input type = "text" name="first_name"><br /><br />

                <?php
                if(isset($_SESSION['e_first_name'])){
                    echo '<div class = "error">'.$_SESSION['e_first_name'].'</div>';
                    unset($_SESSION['e_first_name']);
                }
                ?>

                Nazwisko: <br />
                <input type = "text" name="last_name"><br /><br />

                <?php
                if(isset($_SESSION['e_last_name'])){
                    echo '<div class = "error">'.$_SESSION['e_last_name'].'</div>';
                    unset($_SESSION['e_last_name']);
                }
                ?>

                Login: <br />
                <input type = "text" name="nick"><br /><br />

                <?php
                if(isset($_SESSION['e_nick'])){
                    echo '<div class = "error">'.$_SESSION['e_nick'].'</div>';
                    unset($_SESSION['e_nick']);
                }
                ?>

                Hasło: <br />
                <input type = "password" name="password"><br /><br />

                Powtórz hasło: <br />
                <input type = "password" name="password_2"><br /><br />

                <?php
                if(isset($_SESSION['e_password'])){
                    echo '<div class = "error">'.$_SESSION['e_password'].'</div>';
                    unset($_SESSION['e_password']);
                }
                ?>


                <input class="btn btn-info" type = "submit" value = "Zarejestruj" />

            </form>
        </div>

    </div>
    <div class="footer">
        © 2020 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
    </div>
</div>
</body>


</html>
