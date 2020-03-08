<?php
class Question
{   
    public function drawRandomQuestion($conn)
    {
    	$sqlQuestion = "SELECT
    			
  											q.question_id,
  											q.question_text,
                                            qa.question_answer_id,
											qa.is_true,
  											qa.answer_text,
  											qa.question_answer_order,
  											qa.question_answer_label
  										FROM
  											questions.tbl_question q
										INNER JOIN (
    			
														SELECT COALESCE(t.question_id, 1) AS question_id FROM questions.tbl_question t
														INNER JOIN
														(
										    				SELECT question_id FROM questions.tbl_question
															WHERE question_id NOT IN (SELECT " .implode(' UNION SELECT ', $_SESSION["used_question_ids"]). " )
															
															LIMIT 1
														)xx
														ON xx.question_id = t.question_id
																	
										    		)x
										ON x.question_id = q.question_id
										INNER JOIN questions.tbl_question_answer qa			ON				qa.question_id = x.question_id
										WHERE qa.question_id = q.question_id
                                        ORDER BY qa.question_answer_order
										--ORDER BY random()
										";
    	
    	$result =  pg_query($conn, $sqlQuestion);
    	
    	if($row = pg_fetch_assoc($result))
    	{
    		//array_push($_SESSION["used_question_ids"], $row["question_id"]);
    		$_SESSION["used_question_ids"][] =  $row["question_id"];
    		echo "<table class =\"table\" id =\"question\" align = center >
										<tr>
											<th>Pytanie</th>
										</tr>";
    		echo "<tr>
									<td>".$row["question_text"]."</td>
								  </tr>
								  </table>";
    		pg_result_seek($result, 0);
    		
    	}
    	
    	if($row = pg_fetch_assoc($result)){
    		echo "<table id = \"question\" align = center >
									<tr>";
    		pg_result_seek($result, 0);
    		while ($row = pg_fetch_assoc($result)){
    		    echo "<th><button type=\"button\" class=\"btn btn-info\" id =\" ".$row["question_answer_id"]. "\"onclick =\"checkAnswer(this)\" >".$row["question_answer_label"]."</button></th>";
    		}
    		echo"</tr><tr>"	;
    		pg_result_seek($result, 0);
    		while ($row = pg_fetch_assoc($result)){
    			echo "<td>".$row["answer_text"]."</td>";
    		}
    		echo "</tr></table>";
    	}
    	else{
    		echo "You have solved all daily quizes;)";
    	}
    	
    	pg_close($conn);
    }
    
    /*public function checkAnswear($button, $id, $conn)
    {
    	$check = $button.attr("id");
    	$id = id;
    	
    	$sql = 'SELECT
                    qa.question_answear_id,
    				qa.qestion_answear_text,																
    				qa.is_true																				
    			FROM questions.tbl_question_answear qa														
    			INNER JOIN questions.tbl_question q			ON		q.question_id = qa.question_id 			
    			WHERE 																						
    				q.question_id = $id																		
    				AND qa.question_answear_order = $check';
    	
    	//$result = $conn -> query($sqlQuestion);
    	$pg_query($conn, $sqlQuestion);
    	$row = pg_fetch_assoc($result);
    	if($row["is_true"] == $check)
    	{
    		alert("Brawo poprawna odpowiedź!");
    	}
    	else{
    		alert("Niestety nie :( ");
    	}
    	
    }*/
    
    
}
?>