<?php

session_start();
require '../app/connect.php';

class Test
{   
    public  $defaultTestSize = 10;
    public $size;
    public $testQuestionsTypes = array();
    public $userId;
            
	public function countTestQuestions($connection, $category_id)
	{
	    $sqlTestSize= "
                              SELECT COUNT(DISTINCT y.question_id) AS test_size
                              FROM 
                                    (  
                                        SELECT
                                                q.question_id,
                                                q.question_text,
                                                x.question_order,
                                                qa.question_answer_id,
                                                qa.is_true,
                                                qa.answer_text,
                                                qa.question_answer_order,
                                                qa.question_answer_label,
                                                q.is_image AS is_question_image,
                                                q.image_url AS question_image_url,
                                                qa.is_image AS is_answer_image,
                                                qa.image_url AS answer_image_url
                                        FROM
                                                questions.tbl_question q
                                        INNER JOIN (
        	        
                                                        SELECT
                                                        	t.question_id
                                                            ,xx.question_order
                                                        FROM questions.tbl_question t
                                                        INNER JOIN
                                                                    (
                                                                        SELECT
                                                                        	question_id
                                                                        	,ROW_NUMBER() OVER (ORDER BY question_id) AS question_order
                                                                        FROM questions.tbl_question
                                                                        WHERE
                                                                            category_id = " .$category_id. "
                                                                        ORDER BY question_id
                                                                        LIMIT 10
                                                                    )xx
                                                                    ON xx.question_id = t.question_id
                                                                                
                                                    )x
                                        ON x.question_id = q.question_id
                                        INNER JOIN questions.tbl_question_answer qa			ON				qa.question_id = x.question_id
                                     ) y   
											";
	    
	    $result =  pg_query($connection, $sqlTestSize);
	    
	    $row = pg_fetch_assoc($result);
	    $this->size = $row['test_size'];
	    	    
	}


	
	public function drawTestSingleQuestion($connection, $category_id, $question_order, $userId)
	{
	    
		$sqlTestQuestions = "
                                SELECT 
                                  user_id,
                                  question_id,
                                  category_id,
                                  question_text,
                                  question_order,
                                  question_answer_id,
                                  is_true,
                                  answer_text,
                                  question_answer_order,
                                  question_answer_label,
                                  is_question_image,
                                  question_image_url,
                                  is_answer_image,
                                  answer_image_url
                                FROM 
                                    questions.tbl_user_category_question_test_current
                                WHERE 
                                  question_order = " .$question_order. "
                                  AND category_id = " .$category_id. "
                                  AND user_id = ".$userId."

                          ";

		$result =  pg_query($connection, $sqlTestQuestions);
		
		$row = pg_fetch_assoc($result);
		$row_counter = 0;
		if($row["question_text"] && $row["is_question_image"] == 0)							//draw only text questions
		{
			echo "<table class =\"table\" id =\"question ".$row_counter."\" align = center >
					<tr>
						<th>Question ".$row["question_order"]."</th>
					</tr>";
			echo "<tr>
					<td style = \"text-align:center\">".$row["question_text"]."</td>
				  </tr>
			  </table>";
			array_push($this -> testQuestionsTypes, 'normal');
			$row_counter++;
		}elseif ($row["question_text"] && $row["is_question_image"] == 1)					//draw image questions
		{
			echo "<table class =\"table img_question\" id =\"question ".$row_counter."\" align = center >
					<tr>
						<th>Question ".$row["question_order"]. "</th>
					</tr>";
			echo "<tr>
					<td style = \"text-align:center\"><img style = \"width: 300px; height: 180px \" src = \"..".$row["question_image_url"]." \" /><br>".$row["question_text"]."</td>
				  </tr>
				</table>";
			array_push($this -> testQuestionsTypes, 'image');
			$row_counter++;
			
		}
		$loopImageRowCounter = 0;
		pg_result_seek($result, 0);
		
		$loopCounter = 0;
		if($result > 0){
			
			echo "<table id = \"question".$row["question_order"]."\" align = center >
											<tr style = \" height:120px\";>";
			
			while ($row = pg_fetch_assoc($result)){	
				if($row["is_answer_image"] == 0){
					//echo "<td id = \"question\"><button type=\"button\" class=\"btn btn-info\" id =\"".$row["is_true"]." \"style=\"width:50px;\" onclick =\"checkTestQuestionAnswear(this, ".$row["question_order"].", ".$this -> defaultTestSize.", ".$row['is_question_image'].")\" >".$row["question_answear_label"]."</button><br>".$row["answear_text"]."</td>";
					echo "<td id = \"question\"><button type=\"button\" class=\"btn btn-info\" id =\"".$row["question_answer_id"]." \"style=\"width:50px;\" onclick =\"checkTestQuestionAnswer(this, ".$row["question_order"].", ".$this -> defaultTestSize.", ".$row['is_question_image']." ,'".$_SESSION['user']."')\" >".$row["question_answer_label"]."</button><br>".$row["answer_text"]."</td>";
				    
				}
				else{
				    echo "<td id = \"question\" colspan = 2><img style = \"width: 280px; height: 180px \" src = \"..".$row["answer_image_url"]. " \" /><button type=\"button\" class=\"btn btn-info\" id =\" ".$row["is_true"]. " \"style=\"width:50px;\" onclick =\"checkTestQuestionAnswer(this, ".$row["question_order"].", ".$this -> defaultTestSize.", ".$row['is_question_image']." ,'".$_SESSION['user']."')\" >".$row["question_answer_label"]."</button><br>".$row["answer_text"]."</td>";
					$loopImageRowCounter++;
					if($loopImageRowCounter% 2 == 0){
						echo "</tr><tr>";
					}
				
				}
				
			}
			
			echo"</tr>"	;
			;
			echo "</tr></table>";//<br/><br/><br/><br/>";
		}
		
	}
	
	public function returnTest($connection, $size, $category_id)
	{
	    
	    $result =  pg_query($connection, "SELECT user_id FROM usr.tbl_user WHERE username = '".$_SESSION['user']."'");
	    $row = pg_fetch_assoc($result);
	    
	    $this->userId = $row['user_id'];
	    
	    $sqlTestQuestions = "
                                SELECT
                                      *
                                FROM  questions.sp_user_category_qestion_test_generate  (
                                	$category_id
                                    ,".(int)$this->userId."
                                )
                                    
                          ";
                                	
        $result =  pg_query($connection, $sqlTestQuestions);
	    
	    for($x = 1; $x <= $size; $x++)
		{
		    $this->drawTestSingleQuestion($connection, $category_id, $x, $this->userId);
		}
		pg_close($connection);
	}
	
	public function generateAnswearDivs($size)
	{

	    echo "<br /><br />";
		for($x = 0; $x < $size; $x++)
		{
			echo"
					<div id = \"answear ".$x."\">
						<div class = \"row\">
							<div style = \" height:37px\"; class = \"col-sm-16 answear_header\" id =\"answearHeader\"></div>
						</div>
						<div class = \"row\">
							<div class = \"col-sm-1 \"></div>
							<div class = \"col-sm-2 answear_img\"><p id = \"answear_img".$x."\"></p></div>
							<div class = \"col-sm-4 answear\"><p id = \"answear".$x."\"></p></div>
						</div>
					</div>";
			
			if($this -> testQuestionsTypes[$x] == 'normal'){
				echo "<div class = \"row normal_question_answear_div\">";
			}else{
				echo "<div class = \"row image_question_answear_div\">";
			}
			
			echo "
						<div class = \"col-sm-1\"></div>
						<div class = \"col-sm-2 answear_img\"><p></p></div>
						<div class = \"col-sm-4 answear\"></div>
					</div>
				";
		}
	}
	

	public function drawPredefinedTestSingleQuestion($connection, $testId, $size, $question_order)
	{

	    
	    $sqlTestQuestions = "
                                SELECT * FROM questions.tf_predefined_test_questions_list (
                                  ".(int)$testId."
                                )x
                                WHERE x.question_order = " .$question_order. "
                                    
                          ";
	    
	    
    	$result =  pg_query($connection, $sqlTestQuestions);
    	
    	$row = pg_fetch_assoc($result);
    	$row_counter = 0;
    	if($row["question_text"] && $row["is_question_image"] == 0)							//draw only text questions
    	{
            echo "<table class =\"table\" id =\"question ".$row_counter."\" align = center >
					<tr>
						<th>Question ".$row["question_order"]."</th>
					</tr>";
                                	    echo "<tr>
					   <td style = \"text-align:center\">".$row["question_text"]."</td>
				     </tr>
			      </table>";
    	    array_push($this -> testQuestionsTypes, 'normal');
    	    $row_counter++;
    	}elseif ($row["question_text"] && $row["is_question_image"] == 1)					//draw image questions
    	{
            echo "<table class =\"table img_question\" id =\"question ".$row_counter."\" align = center >
					<tr>
						<th>Question ".$row["question_order"]. "</th>
					</tr>";
                                	    echo "<tr>
					<td style = \"text-align:center\"><img style = \"width: 300px; height: 180px \" src = \"..".$row["question_image_url"]." \" /><br>".$row["question_text"]."</td>
				  </tr>
				</table>";
    	    array_push($this -> testQuestionsTypes, 'image');
    	    $row_counter++;
    	    
    	}
    	$loopImageRowCounter = 0;
    	pg_result_seek($result, 0);
    	
    	$loopCounter = 0;
    	if($result > 0){
    	    
    	    echo "<table id = \"question".$row["question_order"]."\" align = center >
    			<tr style = \" height:120px\";>";
    	    
    	    while ($row = pg_fetch_assoc($result)){
    	        if($row["is_answer_image"] == 0){
    	            //echo "<td id = \"question\"><button type=\"button\" class=\"btn btn-info\" id =\"".$row["is_true"]." \"style=\"width:50px;\" onclick =\"checkTestQuestionAnswear(this, ".$row["question_order"].", ".$this -> defaultTestSize.", ".$row['is_question_image'].")\" >".$row["question_answear_label"]."</button><br>".$row["answear_text"]."</td>";
    	            echo "<td id = \"question\"><button type=\"button\" class=\"btn btn-info\" id =\"".$row["question_answer_id"]." \"style=\"width:50px;\" onclick =\"checkTestQuestionAnswer(this, ".$row["question_order"].", ".$size.", ".$row['is_question_image']." ,'".$_SESSION['user']."')\" >".$row["question_answer_label"]."</button><br>".$row["answer_text"]."</td>";
    	            
    	        }
    	        else{
    	            echo "<td id = \"question\" colspan = 2><img style = \"width: 280px; height: 180px \" src = \"..".$row["answer_image_url"]. " \" /><button type=\"button\" class=\"btn btn-info\" id =\" ".$row["is_true"]. " \"style=\"width:50px;\" onclick =\"checkTestQuestionAnswer(this, ".$row["question_order"].", ".$size.", ".$row['is_question_image']." ,'".$_SESSION['user']."')\" >".$row["question_answer_label"]."</button><br>".$row["answer_text"]."</td>";
    	            $loopImageRowCounter++;
    	            if($loopImageRowCounter% 2 == 0){
    	                echo "</tr><tr>";
    	            }
    	            
    	        }
    	        
    	    }
    	    
    	    echo"</tr>"	;
    	    ;
    	    echo "</tr></table>";//<br/><br/><br/><br/>";
    	}
                                	
	}

	public function returnPredefinedTest($connection, $size, $testId)
	{
	    for($x = 1; $x <= $size; $x++)
	    {
	        $this->drawPredefinedTestSingleQuestion($connection, $testId, $size, $x);
	    }
	    pg_close($connection);
	}
	
	public function predefinedTestSizeGet($connection, $testId)
	{
	    $sqlTestSize= "
                     SELECT COUNT(DISTINCT question_id) AS test_size FROM questions.tf_predefined_test_questions_list (
                          ".(int)$testId."
                        )x
					";
	    
	    $result =  pg_query($connection, $sqlTestSize);
	    
	    $row = pg_fetch_assoc($result);
	    $this->size = $row['test_size'];
	    
	}
	
}





?>