<?php 
    session_start();
    include '../php/class_achievement.php';
    $achievement = new Achievement();
    $achievement->setUserAchievementBadgets($connection, $_SESSION['user']);
    $achievement->getBadgetList($connection);
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
	<link rel="StyleSheet" href="../css/statistics.css" />
	<link rel="StyleSheet" href="../css/side_menu_leaderboard.css" />
	<script type="text/javascript" src="../js/side_menu_leaderboard.js"></script>
	<script type="text/javascript" src="../js/user.js"></script>
	<script type="text/javascript" src="../js/statistics.js"></script>
	<script src = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>
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
			     pg_close($connection);			 
			?>
		</table>
	</div>
	<header class ="header">
		<table width = 100%>
			<tr>
				<td style = "text-align:left">
					<h1 id="title"><a href ="index.php"><b>Q</b>u¿zzy</a></h1>
				</td>
				<td style = "text-align:right">
					<span name="user" id = "<?php echo $_SESSION['class_number']?>">Zalogowany jako: <?php echo $_SESSION['user'] ?>&ensp;</span><span><a href = "logout.php">Logout</a></span>
				</td>
			</tr>
		</table>
	</header>
		<div class="nav">
			<ol>
				<li>
					<a href ='predefined_test.php'>Gotowe testy</a>
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
					<a href = 'materials.php'>Materiały</a>
				</li>
				<li id = "visited">
					<a href ='#' >Statystyki</a>
				</li>
			</ol>
		</div>
		<section class = "section">
			<div id ="welcome_div">
				<h4>Statystyki</h4> 
				<div id ="user_answer_statistics_div">
					Statystyki odpowiedzi
					<canvas id="user_answer_statistics"></canvas>
				</div>
				<div id = "pie_charts">
    				<table>
        				<tr>
        					<td>
        						Statystyki odpowiedzi z bieżącego miesiąca
        						<canvas id="user_answer_current_month_statistics"></canvas>
        					</td>
        					<td>
        						Statystyki odpowiedzi z poprzedniego miesiąca
        						<canvas id="user_answer_last_month_statistics"></canvas>
        					</td>
        				</tr>
        				<tr>
        					<td colspan = "2">
        						Dni zalogowanych/niezalogowanych
        						<canvas id="user_activity_summary"></canvas>
        					</td>
        					
        				</tr>
    				</table><br /><br />
    				<div id ="user_answer_statistics_div">
						Statystyki poziomu trudności pytań
						<canvas id="user_category_question_per_level_summary"></canvas>
					</div>
				</div>
			</div>

		</section>
		<div class="footer">
		© 2017 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
		</div>
	</div>




</body>
</html>

