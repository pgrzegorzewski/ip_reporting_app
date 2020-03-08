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
        
        $email = $_POST['email'];
        $emailSanitised = filter_var($email, FILTER_SANITIZE_EMAIL);
        if(!filter_var($emailSanitised, FILTER_VALIDATE_EMAIL) || $email != $emailSanitised)
        {
            $success = false;
            $_SESSION['e_email'] = "Niepoprawny adres email";
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
        
        $class = $_POST['class'];
        
        if(!isset($_POST['terms_and_conditions']))
        {
            $success = false;
            $_SESSION['e_terms_and_conditions'] = "Terms and conditions must be checked";
        }
        
        $secret = '6Lf9YhwUAAAAADRPoJ6_ZwUHHEDDQiIjkbxMeoYA';
        
        $check_recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
        $response_recaptcha = json_decode($check_recaptcha);
        
        if(!$response_recaptcha->success)
        {
            $success = false;
            $_SESSION['e_recaptcha'] = "You are bot";
        }
        
        require_once 'connect.php';
        
        try
        {
            if($connection)
            {
                $username_check = @pg_query($connection, "SELECT * FROM usr.sf_username_unique_check('$login') AS is_username_unique");
                $is_username_check = pg_fetch_assoc($username_check);
                
                if($is_username_check['is_username_unique'] != 1)
                {
                    $success = false;
                    $_SESSION['e_nick'] = "Nick already in use";
                    
                }
                pg_free_result($username_check);
                
                $email_check = @pg_query($connection, "SELECT * FROM usr.sf_email_unique_check('$email') AS is_email_unique");
                $is_email_check = pg_fetch_assoc($email_check);
                if($is_email_check['is_email_unique'] != 1)
                {
                    $success = false;
                    $_SESSION['e_email'] = "Email already in use";
                    
                }
                
                pg_free_result($email_check);
                
                if($success == true)
                {
                    @pg_query($connection, "SELECT * FROM usr.sp_user_create('$nick', '$password_hashed', '$first_name', '$last_name', '$class', '$email')"); 
                    $_SESSION['registrationSuccessful'] = true;
                    header('Location: register_success.php');
                }
                
                pg_close($connection);
            }
        }
        catch(Exception $err)
        {
            echo '<span style="color:red;">Server error! Apologies for inconvenience"';
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

    <link rel="icon" href="img/mdb-favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/mdb.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="StyleSheet" href="../css/home.css" />
    <link rel="StyleSheet" href="../css/index.css" />
	<link rel="StyleSheet" href="../css/home.css" />
	<link rel="StyleSheet" href="../css/index.css" />
</head>
<body>
	<div class="container-fluid">
    	<div class = "container section">   
    	 	<div class="form"><h1><b>Q</b>u¿zzy</h1></div><br />
    	 	<div class="form"><h4>Formularz rejestracji</h4></div>
    	 	<div class="form">
        	 	<form method="post">
        	 		
        	 		Imię: <br />
        	 		<input type = "text" name="first_name"><br />
        	 		
        	 		<?php 
            			if(isset($_SESSION['e_first_name'])){
            				echo '<div class = "error">'.$_SESSION['e_first_name'].'</div>';
            				unset($_SESSION['e_first_name']);
            			}
		            ?>
        	 		
        	 		Nazwisko: <br />
        	 		<input type = "text" name="last_name"><br />
        	 		
        	 		<?php 
            			if(isset($_SESSION['e_last_name'])){
            				echo '<div class = "error">'.$_SESSION['e_last_name'].'</div>';
            				unset($_SESSION['e_last_name']);
            			}
		            ?>
        	 		
        	 		Login: <br />
        	 		<input type = "text" name="nick"><br />
        	 		
        	 		<?php 
            			if(isset($_SESSION['e_nick'])){
            				echo '<div class = "error">'.$_SESSION['e_nick'].'</div>';
            				unset($_SESSION['e_nick']);
            			}
		            ?>
        	 		
        	 		Hasło: <br />
        	 		<input type = "password" name="password"><br />
        	 		
        	 		Powtórz hasło: <br />
        	 		<input type = "password" name="password_2"><br />
        	 		
        	 		<?php 
            			if(isset($_SESSION['e_password'])){
            				echo '<div class = "error">'.$_SESSION['e_password'].'</div>';
            				unset($_SESSION['e_password']);
            			}
		            ?>
		            
		            Klasa: <br />
		            <input type="number" name="class" min="4" max="8" value = "4"><br />
		                   	 		
        	 		Email: <br />
        	 		<input type = "email" name="email"><br />
        	 		
        	 		<?php 
        	 			if(isset($_SESSION['e_email'])){
            			    echo '<div class = "error">'.$_SESSION['e_email'].'</div>';
            			    unset($_SESSION['e_email']);
            			}
            		?>
        	 		
        	 		<label>
        	 			<input type="checkbox" name = "terms_and_conditions">Akceptuję regulamin
        	 		</label>
        	 		
        	 		<?php 
        	 			if(isset($_SESSION['e_terms_and_conditions'])){
            			    echo '<div class = "error">'.$_SESSION['e_terms_and_conditions'].'</div>';
            			    unset($_SESSION['e_terms_and_conditions']);
            			}
            		?>
        	 		
        	 		<br />
        	 		<div class="g-recaptcha" data-sitekey="6Lf9YhwUAAAAAD9ikfHftOu5GXoDOQWbwE_HM4ML" style ="display: inline-block"></div><br />
        	 		<?php 
            			if(isset($_SESSION['e_recaptcha'])){
            				echo '<div class = "error">'.$_SESSION['e_recaptcha'].'</div>';
            				unset($_SESSION['e_recaptcha']);
            			}
		            ?>
		           
		            <input type = "submit" value = "Register" />
	
        	 	</form>
        	 </div>
        	
    	</div>
    <div class="footer">
		© 2018 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
	</div>
    </div>
</body>


</html>