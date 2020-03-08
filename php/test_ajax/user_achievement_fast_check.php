<?php 

session_start();

include '../../app/connect.php';

if(isset($_POST['user']) && isset($_POST['achievement']))
{ 
    $username = $_POST['user'];   
    $achievement_id = $_POST['achievement'];
    $event = $_POST['event'];
    
    $result =  pg_query($connection, "SELECT 
                                            COUNT(*) AS achievement_cnt
                                      FROM 
                                            usr.tbl_user_achievement ua
                                      INNER JOIN usr.tbl_user u                 ON          u.user_id = ua.user_id
                                      WHERE ua.achievement_id = ".$achievement_id."
                                            AND u.username = '".$username."'");
    $row = pg_fetch_assoc($result);
    
    $achievement_cnt = $row['achievement_cnt'];
    
    if($event == 'result_check')
    {
        @pg_query($connection, "SELECT * FROM  usr.sp_user_achievement_add ('".$username."', ".$achievement_id.")");
    }
    
    echo $achievement_cnt;
    
}
?>