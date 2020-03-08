<?php 

session_start();

include '../../app/connect.php';

if(isset($_SESSION['user']))
{ 
    $username = $_SESSION['user'];
    
    $result =  pg_query($connection, " SELECT * FROM questions.tf_user_category_question_per_level_summary (
                                      '".$username."')");
    $data = array();
    
    while ($row = pg_fetch_assoc($result))
    {
        $data[] = $row;
    }
        
    echo json_encode($data);    
}
?>