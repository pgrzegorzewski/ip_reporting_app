<?php

require '../app/connect.php';

class Achievement
{  
    public $userBadgetList = array();
    public $badgetList = array();
    public $user;
    //private $connection;
        
    public function setUserAchievementBadgets($connection, $user)
    {
        
        $userBadgetsSql =  pg_query($connection, "SELECT 
                                                        ua.user_id, 
                                                        ua.achievement_id 
                                                  FROM 
                                                        usr.tbl_user_achievement ua
                                                  INNER JOIN usr.tbl_achievement a          ON          a.achievement_id = ua.achievement_id
                                                  INNER JOIN usr.tbl_user u                 ON          u.user_id = ua.user_id
                                                  WHERE u.username  = '".$user."'");
        
        while ($row = pg_fetch_assoc($userBadgetsSql)) {
            array_push($this->userBadgetList, $row['achievement_id']);
        }
    }
    
    public function getBadgetList($connection)
    {
        $userBadgetsSql =  pg_query($connection, "SELECT
                                                        achievement_id
                                                        ,image_url
                                                  FROM
                                                        usr.tbl_achievement 
                                                  WHERE is_active = 1::BIT
                                                  ORDER BY achievement_order");
        
        while ($row = pg_fetch_assoc($userBadgetsSql)) {
            array_push($this->badgetList, $row['achievement_id']);
        }
        
    }
    
    public function getAchievementBadgetUrl($connection, $achievementId)
    {
        $badgetUrlsql = pg_query($connection, "SELECT
                                                image_url
                                            FROM
                                                usr.tbl_achievement
                                            WHERE 
                                                achievement_id = ".$achievementId."");
        $row = pg_fetch_assoc($badgetUrlsql);
        
        return $row['image_url'];
    }
}
?>