<?php 

session_start();

include '../app/connect.php';

if(isset($_POST['question_answer_id']))
{
    
    $answer_id = $_POST['question_answer_id'];   
    
    $result =  pg_query($connection, "SELECT is_true FROM questions.tbl_question_answer WHERE question_answer_id = ".$answer_id."");
    $row = pg_fetch_assoc($result);
   
    $is_true = $row['is_true'];
    
    
    echo $is_true;
    
}
?>