<?php 
	
	session_start();			//w każdym dokumencie gdzie wykorzystujemy sesje
	
	if(!isset($_POST['login']) || (!isset($_POST['password'])))
	{
		header('Loacation:index.php');
		exit();
		
	}
	
	require_once "connect.php";

	$connection = @new mysqli($host, $db_user, $db_password, $db_name);
	
	if($connection -> connect_errno != 0)
	{
		echo "Error:".$connection -> connect_errno;
	}
	else 
	{
		$login = $_POST['login'];
		$password = $_POST['password'];
		
		$login - htmlentities($login, ENT_QUOTES, "UTF-8");
		//$password - htmlentities($password, ENT_QUOTES, "UTF-8");
		
		/*$sql = "
					SELECT 
						* 
					FROM 
						user.tbl_user
					WHERE 
						username = '$login'
						AND password = '$password' 
				";
		*/
		/*if($result  = @$connection -> query($sql));*/
		if($result = @$connection -> query(
		sprintf("SELECT * FROM user.tbl_user where username = '%s'",
		mysqli_real_escape_string($connection, $login))))	
		{
			$user_num = $result ->num_rows;
			if($user_num > 0)
			{
				$row = $result -> fetch_assoc();
				if(password_verify($password, $row['password']))
				{
					$_SESSION['islogged'] = true;
					$_SESSION['id'] = $row['id'];
					$_SESSION['user'] = $row['username'];
					$_SESSION['email'] = $row['email'];
					
					//echo $user;
					unset($_SESSION['error']);
					$result -> close(); 				//free()   free_result()
					header('Location:main.php');
				}
				else 
				{
					$_SESSION['error'] = '<span style ="color:red">Wrong credentials</span>';
					
					header('Location:index.php');
				}
			}
			else 
			{
				$_SESSION['error'] = '<span style ="color:red">Wrong credentials</span>';
				
				header('Location:index.php');
			}
			
		}
		
		$connection ->close();
	}
	

?>