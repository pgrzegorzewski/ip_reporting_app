<?php
session_start();
require_once './connect.php';

$login = $_POST['login'];
$password = $_POST['password'];

$login = htmlentities($login, ENT_QUOTES, "UTF-8");

try
{
    $result =  @pg_query($connection, "SELECT * FROM usr.sf_user_password_verify('$login') AS success");
    $row = pg_fetch_assoc($result);
    
    if($row['success'] == 1)
    {
        $_SESSION['user'] = $login;

        try
        {
            $user_password = @pg_query($connection, "SELECT * FROM usr.sf_user_password_get('$login') AS password_string");
            $password_string = pg_fetch_assoc($user_password);
            
            if(password_verify($password, $password_string['password_string']))
            {
                header('Location:home.php');
                $_SESSION['is_logged'] = true;
            }
            else
            {
                $_SESSION['error'] = '<p style = "color:red; text-align:center;">Nieprawidłowy login lub hasło</p>';
                header('Location:index.php');
            }
        }
        catch(Exception $error)
        {
            $error->getMessage();
        }
        
    }
    else
    {
        $_SESSION['error'] = '<p style = "color:red; text-align:center;">Nieprawidłowy login lub hasło</p>';
        header('Location:index.php');
    }
    pg_free_result($result);
    pg_close($connection);
    
} catch (Exception $error)
{
    echo $error->getMessage();
}


?>