<?php

require ($_SERVER["DOCUMENT_ROOT"]. '/reporting-app/mdb/content/core/connect.php');

class User
{
    public $userId;

    public function userIdGet($username)
    {

        $result =  pg_query($connection, "SELECT user_id FROM usr.tbl_user WHERE username = '$username'");
        $row = pg_fetch_row($result);

        $this->userId = $row['user_id'];

        return $this->userId;

    }

}

?>
