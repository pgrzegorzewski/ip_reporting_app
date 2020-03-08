<?php 

session_start();

include '../../app/connect.php';

if(isset($_SESSION['user']))
{ 
    $username = $_SESSION['user'];
    
    $result =  pg_query($connection, " SELECT * FROM questions.tf_user_question_answers_per_date_summary (
                                      '2018-12-20',
                                      '2019-01-15',
                                      '".$username."')");
    $data = array();
    
    while ($row = pg_fetch_assoc($result))
    {
        $data[] = $row;
    }
        
    echo json_encode($data);    
}
?>