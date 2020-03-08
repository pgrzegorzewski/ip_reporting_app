<?php

	$categoryId = $_POST['categoryId'];
	
	$test2 = printTest($categoryId);
	echo $test2;
	
	function printTest($categoryId)
	{
		require '../app/connect.php';		
		
		require '../php/class_test.php';
		$test = new test();
		$test->countTestQuestions($connection, $categoryId);
		
		echo '<div class ="row">
				<div class="col-sm-6" id = "test_body">';
		
		if($categoryId){
		
			if($test -> size> 0){
				echo "<br/><br/>";	
			}
			else
			{
				echo 'There are no questions from this category. We will add them soon;)';		
			}
			
	
		}
				
		if($test -> size){
			$test -> returnTest($connection, $test->size, $categoryId);
		}else{
			echo 'Unfortunately there are no questions from this category;( We will add them soon, be patient;)';
		}echo '</div>';
		
		echo "<div class='col-sm-6 test_body' id = 'test_body_answear'>";

		$test -> generateAnswearDivs($test->size);
		//
		echo"		
				</div>
			";
		
	}	
?>
