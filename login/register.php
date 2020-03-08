<?php
	session_start();

	if(isset($_POST['email']))
	{
		//successful registration
		$register_success = true;
		
		//check nickname
		$nick = $_POST['nick'];
		if(strlen($nick) < 3 || strlen($nick) > 20)
		{
			$register_success = false;
			$_SESSION['e_nick'] = "Nick length must be between 3 and 20 characters";
		}
		
		if(!ctype_alnum($nick))
		{
			$register_success = false;
			$_SESSION['e_nick'] = "Nick must contain only alphanumerical characters";;
		}
		
		//email check
		$email = $_POST['email'];
		$emailSafe = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if((filter_var($emailSafe, FILTER_VALIDATE_EMAIL) == false) || ($emailSafe != $email))
		{
			$register_success = false;
			$_SESSION['e_email'] = "Wrong email";
			
		}
		
		$password = $_POST['password'];
		$password2 = $_POST['password2'];
		
		if((strlen($password) < 8 || strlen($password) > 20))
		{
			$register_success = false;
			$_SESSION['e_password'] = "Password must have between 8 and 20 characters";
		}
		
		if($password  != $password2)
		{
			$register_success = false;
			$_SESSION['e_password'] = "Passwords must be the same";
			
		}
		
		$password_hashed = password_hash($password, PASSWORD_DEFAULT);
		
		if(!isset($_POST['terms']))
		{
			$register_success = false;
			$_SESSION['e_terms'] = "Check terms and agreements";
		}
		
		$secret = '6Lf9YhwUAAAAADRPoJ6_ZwUHHEDDQiIjkbxMeoYA';
		$checkCaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
		
		$response = json_decode($checkCaptcha);
		
		if($response ->success != true)
		{
			$register_success = false;
			$_SESSION['e_recaptcha'] = "Confirm that You are not a bot";	
		}
		
		require_once 'connect.php';
		mysqli_report(MYSQLI_REPORT_STRICT);			//brak warningu o exception
		
		try
		{
			$connection = new mysqli($host, $db_user, $db_password, $db_name);
			if($connection -> connect_errno != 0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				$result = $connection -> query("
													SELECT user_id FROM tbl_user
													WHERE
													email = '$email'
												");
				if(!$result)
				{
					throw new Exception($connection -> error);
				}
				$email_amount = $result -> num_rows;
				if($email_amount > 0)
				{
					$register_success = false;
					$_SESSION['e_email'] = "Email already in use.";
				}
				
				
				$result = $connection -> query("
						SELECT user_id FROM tbl_user
						WHERE
						username = '$nick'
						");
				if(!$result)
				{
					throw new Exception($connection -> error);
				}
				$nick_amount = $result -> num_rows;
				if($nick_amount > 0)
				{
					$register_success = false;
					$_SESSION['e_nick'] = "Nick already in use.";
				}
				
				if($register_success == true)
				{
					if( $connection -> query("INSERT INTO tbl_user(username, password,email) VALUES('$nick', '$password_hashed', '$email');"))
					{
						$_SESSION['register_success'] = true;
						header("Location: welcome.php");
					}
					else 
					{
						throw new Exception($connection -> error);
					}
				}
				
				
				$connection -> close();
				
				
			}
			
		}
		catch(Exception $e)
		{
			echo '<span style = color:red;">Server error</span>';
			//echo '<br />Error: '.$e;													//info dla mnie, nie dla usera
			
		}
		
		if($register_success == true)
		{
			$result = $connection -> query("
					INSERT INTO tbl_user
					(
						username,
					WHERE
					username = '$nick'
					");
			echo "account created";
			//insert
			exit();
		}
	}
?>

<!DOCTYPE HTML>
<html lang="pl-PL">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content ="IE=edge,chrome=1" />
	<script src='https://www.google.com/recaptcha/api.js'></script>
	
	<link rel="StyleSheet" href="../css/register.css" />

	
	<title>Queezy register</title>
</head>

<body>
	
	<form method = "post">
		Nickname:<br /> <input type = "text" name = "nick" /><br />
		
		<?php 
			if(isset($_SESSION['e_nick'])){
				echo '<div class = "error">'.$_SESSION['e_nick'].'</div>';
				unset($_SESSION['e_nick']);
			}
		?>
		
		Email:<br /><input type = "text" name = "email" /><br />
		
		<?php 
			if(isset($_SESSION['e_email'])){
				echo '<div class = "error">'.$_SESSION['e_email'].'</div>';
				unset($_SESSION['e_email']);
			}
		?>
		
		Password:<br /><input type = "password" name = "password" /><br />
		
		Repeat password:<br /><input type = "password" name = "password2" /><br />
		
		<?php 
			if(isset($_SESSION['e_password'])){
				echo '<div class = "error">'.$_SESSION['e_password'].'</div>';
				unset($_SESSION['e_password']);
			}
		?>
		
		<label>
			<input type = "checkbox" name="terms"/>Terms and agreements
		</label>
		<?php 
			if(isset($_SESSION['e_terms'])){
				echo '<div class = "error">'.$_SESSION['e_terms'].'</div>';
				unset($_SESSION['e_terms']);
			}
		?>
		<div class="g-recaptcha" data-sitekey="6Lf9YhwUAAAAAD9ikfHftOu5GXoDOQWbwE_HM4ML"></div>
		<?php 
			if(isset($_SESSION['e_recaptcha'])){
				echo '<div class = "error">'.$_SESSION['e_recaptcha'].'</div>';
				unset($_SESSION['e_recaptcha']);
			}
		?>
		<br /><input type = "submit" value = "Register">
	</form>
	
	
</body>

</html>