<?php 
    session_start();
    require 'connect.php';
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
	<link rel="StyleSheet" href="../css/class_7_test.css" />
	<link rel="StyleSheet" href="../css/side_menu_leaderboard.css" />	
	<link rel="StyleSheet" href="../css/achievement_modal.css" />
	<script type="text/javascript" src="../js/user.js"></script>
	<script type="text/javascript" src="../js/question.js"></script>
	<script type="text/javascript" src="../js/class_7_test.js"></script>
	<script type="text/javascript" src="../js/side_menu_leaderboard.js"></script>

</head>
<script type="text/javascript">
    var username ='<?php echo $_SESSION['user'];?>';
</script>
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
					<li class = 'class_7'><a href="#" id = "visited">Klasa 7</a></li>
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
			<h4>Klasa 7</h4>
			
		</div>
		<div class = "row">
			<div class="col-sm-12" id="tiles">
				<h5 style = "text-align:center">Wybierz dział:</h5>
				<?php //here php to implement dynamic creation of category tiles
				
				echo "
					<table id = \"tile\">
						<tr>
							<td>						
								<button  name = '1' id = '4' class = 'category btn' >Chemia</button>
							</td>
							<td>
								<button name = '2' id = '5'  class = 'category btn' value = 'test'>Język angielski</button>
							</td>
						</tr>
						<tr>
							<td>
								<button  name = '3' id = '6' class = 'category btn' >Historia</button>
							</td>
							<td>
								<button name = '4' id = '7' class = 'category btn' >Język niemiecki</button>
							</td>
						</tr>
					</table>
				";
				?>
				<br/><br/>

			</div>
		</div>
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
	<div id="award_FAST" class="achievement_modal">
        <div class="modal-content">
       		<table id="modal_table">
       			<tr>
       				<td rowspan="2">
       					<img src ='../resources/img/awards.png' width="90px" height = "100px" class ="trumpets"/>
       				</td>
       				<td valign="top">
       					Gratulacje!<br/>Zdobywasz odznake
       				</td>
       				<td  rowspan="2">
       					<img src ='../resources/img/awards.png' width="90px" height = "100px" class="flipped trumpets"/>
       				</td>
       			</tr>
       			<tr>
       				<td id="award_img">
       					<img src= '../resources/img/achievments/fast.png' width = "120px" />
       				</td>
       			</tr>
       		</table>
       	
        </div>
    </div>
    
	<div class="footer">
		© 2018 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
	</div>
</div>




</body>
</html>

