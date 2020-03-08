<?php 
    session_start();
    
    require 'connect.php';
    include '../php/class_achievement.php';
    include '../php/class_user.php';
    include '../php/class_predefined_test.php';
    $achievement = new Achievement();
    $achievement->setUserAchievementBadgets($connection, $_SESSION['user']);
    $achievement->getBadgetList($connection);
    
    $predefinedTest = new PredefinedTest();
    
    $loggedUser = new User();
    $_SESSION['class_number'] = $loggedUser->userClassNumberGet($connection, $_SESSION['user']);
    
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
	<link rel="StyleSheet" href="../css/home.css" />
	<link rel="StyleSheet" href="../css/question.css" />
	<link rel="StyleSheet" href="../css/class_7_test.css" />
	<link rel="StyleSheet" href="../css/predefined_test.css" />
	<link rel="StyleSheet" href="../css/side_menu_leaderboard.css" />
	<script type="text/javascript" src="../js/question.js"></script>
	<script type="text/javascript" src="../js/predefined_test.js"></script>
	<script type="text/javascript" src="../js/side_menu_leaderboard.js"></script>
</head>

<body>

<div class="container-fluid">
	
	<div class= "sidemenu_2">
	
	</div>
	<div class= "sidemenu">
		<p style="cursor:pointer"><img src = "../resources/img/trophy.png" height = "50px" onmouseover="openLeaderboard()"/></p> <!-- &#9776; -->
	</div>
	<div id = 'leaderboard' class = 'leaderboard' onmouseleave = "closeLeaderboard()" >
		<span><b>Naklejki za osiągnięcia!</b></span><a href = "javascript:void(0)" class = "closebtn" onclick = "closeLeaderboard()">&times;</a>
		
		<table>
			<?php 
			     $trCounter = 0;
			     foreach ($achievement->badgetList as $badgets)
			     {
			         if($trCounter % 3 == 0 && $trCounter == 0)    
			         {
			             echo "<tr>";
			         }
			         if ($trCounter % 3 == 0 && $trCounter > 0)
			         {
			             echo "</tr><tr>";
			         }
			         echo "<td width:20px><img height='62' width='62' ";
			         if (in_array($badgets, $achievement->userBadgetList)) 
			         {
			            echo "src = ".$achievement->getAchievementBadgetUrl($connection, $badgets)." ";
			         }
			         else
			         {
			             echo 'src = "../resources/img/question_mark.png"';
			         }
			         echo '" /></td>';
			         
			         $trCounter++;   
			     }
			     echo "</tr>";
			     
			?>
		</table>
	</div>


	<header class ="header">
		<h1 id="title"><a href ="home.php"><b>Q</b>u¿zzy</a></h1>
	</header>
	
	<div class="nav">
		<ol>
			<li>
				<a href ='#' id="visited">Gotowe testy</a>
			</li>
			<li>
				<a href ='#'>Testy</a>
				<ul>
					<li class = 'class_4'><a href="#">Klasa 4</a></li>
					<li class = 'class_5'><a href="#">Klasa 5</a></li>
					<li class = 'class_6'><a href="#">Klasa 6</a></li>
					<li class = 'class_7'><a href="class_7_test.php">Klasa 7</a></li>
					<li class = 'class_8'><a href="#">Klasa 8</a></li>
				</ul>
			</li>
			<li>
				<a href ='add_test.php'>Dodaj własny test</a>
			</li>
			<li>
				<a href ='materials.php'>Materiały</a>
			</li>
			<li>
					<a href ='statistics.php'>Statystyki</a>
			</li>
		</ol>
	</div>
	
	<section class = "section">
		
		<div id ="welcome_div">
			<h4>Gotowe testy</h4>
			<p>Witamy w sekcji gdzie znajdziesz testy stworzone przez nauczycieli. Pytania zawarte w testach są niezmienne, ponieważ zawierają najistotniejsze pytania z danego działu<br><br>Dobrej zabawy i powodzenia!!!<br><br><br></p>				
		</div>
		<h4>Klasa 4</h4><br />
		<?php 
		      $predefinedTest->predefinedTestListGet($connection);
		      pg_close($connection);
		?>
		
		<div class ="row">
			<div class="col-sm-12">	
				<h4 id="test_title" hidden = "true">Zaczynajmy!</h4>
			</div>
		</div>
		<div id = "test_questions">

		</div>
		<div class ='row'>
				<div class='col-sm-12' id = 'result_div'>	
					<h4 id= 'result_text' hidden = 'true'></h4>
					<h4 id= 'result_award' hidden = 'true' style ="text-align:center;"></h4>
				</div>
		</div>	
	</section>
	
	
	<div class="footer">
		© 2018 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
	</div>
</div>




</body>
</html>

