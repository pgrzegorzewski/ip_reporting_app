<?php
    session_start();
    require_once './connect.php';

    $login = $_SESSION['user'];
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $newPassword2 = $_POST['new_password_2'];
    $oldPasswordHashed = '';

    $success = true;

    try {
        $result = @pg_query_params($connection, "SELECT * FROM usr.sf_pobierz_haslo($1) AS password_string", array($login));
        $passwordString = pg_fetch_assoc($result);

        if (!password_verify($oldPassword, $passwordString['password_string'])) {
            $success = false;
            $_SESSION['e_password'] .= '<p style = "color:red; text-align:center;">Nieprawidłowe hasło.</p>';
        } else {
            $oldPasswordHashed = $passwordString['password_string'];
        }

        pg_free_result($result);
        if (strlen($newPassword) < 8 || strlen($newPassword) > 20) {
            $success = false;
            $_SESSION['e_password'] .= '<p style = "color:red; text-align:center;">Hasło musi posiadać od 8 do 20 znaków.</p>';
        }
        

        if ($newPassword != $newPassword2) {
            $success = false;
            $_SESSION['e_password'] .= '<p style = "color:red; text-align:center;">Hasła są różne.</p>';
        }

        if(!preg_match('/\d/', $newPassword)){
            $success = false;
            $_SESSION['e_password'] .= '<p style = "color:red; text-align:center;">Hasło musi zawierać cyfrę</p>';
        }

        if(!preg_match('/[A-Z]/', $newPassword)){
            $success = false;
            $_SESSION['e_password'] .= '<p style = "color:red; text-align:center;">Hasło musi zawierać wielką literę.</p>';
        }

        if (!preg_match('/[^a-zA-Z0-9]/', $newPassword))
        {
            $success = false;
            $_SESSION['e_password'] .= '<p style = "color:red; text-align:center;">Hasło musi zawierać znak specjalny.</p>';
        }
        

    }
    catch (Exception $error) {
        echo $error->getMessage();
    }

    try {
        if($success == true) {

            $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);

            @pg_query_params($connection, "SELECT * FROM usr.sp_zaktualizuj_haslo($1, $2, $3)", array($login, $newPasswordHashed, $oldPasswordHashed));
            $_SESSION['e_password'] = "Hasło pomyslnie zmienione!";
        }
    } catch(Exception $error) {
        $error->getMessage();
    }

    pg_close($connection);
    header('Location:../../index.php');

?>
