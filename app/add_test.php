<?php 
    session_start();
    
    require 'connect.php';
    include '../php/class_achievement.php';
    include '../php/class_user.php';
    $achievement = new Achievement();
    $achievement->setUserAchievementBadgets($connection, $_SESSION['user']);
    $achievement->getBadgetList($connection);
    
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
	<link rel="StyleSheet" href="../css/add_test.css" />
	<link rel="StyleSheet" href="../css/side_menu_leaderboard.css" />
	<script type="text/javascript" src="../js/question.js"></script>
	<script type="text/javascript" src="../js/add_test.js"></script>
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
			     pg_close($connection);
			?>
		</table>
	</div>

	<header class ="header">
		<h1 id="title"><a href ="home.php"><b>Q</b>u¿zzy</a></h1>
	</header>
	
	<div class="nav">
		<ol>
			<li>
					<a href ='predefined_test.php'>Gotowe testy</a>
				</li>
				<li>
					<a href ='#'>Testy</a>
					<ul>
						<li><a href="#">Klasa 4</a></li>
						<li><a href="#">Klasa 5</a></li>
						<li><a href="#">Klasa 6</a></li>
						<li><a href="class_7_test.php">Klasa 7</a></li>
						<li><a href="#">Klasa 8</a></li>
					</ul>
				</li>
				<li>
					<a href ='add_test.php' id = 'visited'>Dodaj własny test</a>
				</li>
				<li>
					<a href ='materials.php'>Materiały</a>
				</li>
				<li>
					<a href ='statistics.php'>Statystyki</a>
				</li>
				<!--  <li>
					<a href ='#'>O autorach</a>
				</li>-->
		</ol>
	</div>
	
	<section class = "section">
		<div id ="welcome_div">
			<h4>Dodaj własny test</h4>
			<div id = "add_test_container">
				<p>Wypełnij formularz aby dodać własnt test, wymagane informacje są oznaczone gwiazdką'*'.</p>
				<br>
				<form>
					<span class="questionTitle">Nazwa testu*:</span><br>
                    <input class = "testGeneralInformation" id = "testName" type ="text" name ="test_name"><br><br>
                    <span class="questionTitle">Wybierz klasę*:</span><br>
                    <table id = "category">
                    	<tr>
                    		<td>
                    			<input type ="radio" name ="testClassChoose" value = "4"> Klasa 4
                       		</td>
                    		<td>
                    			<input type ="radio" name ="testClassChoose" value = "5"> Klasa 5
                            </td>
                    		<td>
                    			<input type ="radio" name ="testClassChoose" value = "6"> Klasa 6
                      		</td>
                            <td>
                            	<input type ="radio" name ="testClassChoose" value = "7"> Klasa 7	
        					</td>
        					<td>
                            	<input type ="radio" name ="testClassChoose" value = "8"> Klasa 8
        					</td>
        				</tr>
					</table>	  
                    <br><br>
				 </form>
				 <form>
                    <span class="questionTitle">Wybierz kategorię*:</span><br>
                    <table id = "category">
                    	<tr>
                    		<td>
                    			<input type ="radio" name ="testCategoryChoose" value = "maths"> Matematyka
                       		</td>
                    		<td>
                    			<input type ="radio" name ="testCategoryChoose" value = "chemistry"> Chemia
                            </td>
                    		<td>
                    			<input type ="radio" name ="testCategoryChoose" value = "english"> Język angielski
                      		</td>
                            <td>
                            	<input type ="radio" name ="testCategoryChoose" value = "geography"> Geografia	
        					</td>
        				</tr>
					</table>	  
				</form><br>
				<div id = "question_container">
					<div class = 'new_question_div'>
						<form class = "newQuestion" id = '1'>
						<span class="questionTitle">1. Pytanie:</span><br><br>
						Dodaj pytanie*:<br>
						<input class = "testQuestion" type = "text" name = "question"><br><br>
						Wybierz liczbę odpowiedzi*:
						<select id = "selectAnswers1" name="answers_amount"  onchange="addAnswers(this)">
							  <option selected hidden = "true" value ="2">2</option>
							  <option value="2">2</option>
							  <option value="3">3</option>
							  <option value="4">4</option>
						</select>
						<div id = "question_answers_container_1"></div><br>
						<table>
							<tr>
								<td>
									<input class = "nextQuestionAdd" id = '1' type = "button" value = "Dodaj kolejne pytanie" onclick = "addNewQuestion(this)">
								</td>
								<td>
									<input id = "testSubmit" class = "submit" type = "button" onclick = "add()" value = "Zatwierdź">
								</td>
							</tr>
						</table>
						</form>
					</div>
				</div>
			</div>
		</div>
							
	</section>
	<div class="footer">
		© 2018 PAWEŁ GRZEGORZEWSKI ALL RIGHTS RESERVED
	</div>
</div>


<script type="text/template" id="questionTemplate">
    <br>
	<div class = 'new_question_div'>
		<form class = "newQuestion" id = "{{id}}">
		<span class="questionTitle">{{id}} . Pytanie:</span><br><br>
		Dodaj pytanie*:<br>
		<input class = "testQuestion" type = "text" name = "question"><br><br>
			Wybierz liczbe odpowiedzi*:
			<select id = 'selectAnswers{{id}}' name="answers_amount" onchange="addAnswers(this)">
				  <option selected hidden = "true" value ="2">2</option>
				  <option value="2">2</option>
				  <option value="3">3</option>
				  <option value="4">4</option>
			</select>
			<div id = "question_answers_container_{{id}}"></div><br>
				<input class = "nextQuestionAdd" id = "{{id}}" type = "button" value = "Dodaj nowe pytanie" onclick = "addNewQuestion(this)">
				<input id = "testSubmit" class = "submit" type = "button" onclick = "add()" value = "Zatwierdź">
		</form>
	</div>
</script>


<script type="text/template" id="questionAnswersTemplate">
    <br>
	<table>
		<tr>
			<td class = 'questionAnswer{{id}}' id = 'answer{{id}}_1' hidden = true><span class="questionTitle">A</span><br/><input class = "question_answer_input" type = "text" name = "answer{{id}}A"><br /> <input type ="radio" name="{{id}}" value = "Aprawda">prawda</td>
			<td class = 'questionAnswer{{id}}' id = 'answer{{id}}_2' hidden = true><span class="questionTitle">B</span><br/><input class = "question_answer_input" type = "text" name = "answer{{id}}B"><br /> <input type ="radio" name="{{id}}" value = "Bprawda">prawda</td>
			<td class = 'questionAnswer{{id}}' id = 'answer{{id}}_3' hidden = true><span class="questionTitle">C</span><br/><input class = "question_answer_input" type = "text" name = "answer{{id}}C"><br /> <input type ="radio" name="{{id}}" value = "Cprawda">prawda</td>
			<td class = 'questionAnswer{{id}}' id = 'answer{{id}}_4' hidden = true><span class="questionTitle">D</span><br/><input class = "question_answer_input" type = "text" name = "answer{{id}}D"><br /> <input type ="radio" name="{{id}}" value = "Dprawda">prawda</td>
		</tr>
	</table>
</script>


</body>
</html>


<?php 
	$answerAmount = 2;
	
	function setAnswerAmount($value){
		$answerAmount = $value;
	}
?>
