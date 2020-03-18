<?php

class User
{
    public $userId;
    protected $connection;

    function __construct() {
      $this->setConnection();
    }

    private function setConnection() {
      require ($_SERVER["DOCUMENT_ROOT"]. '/reporting-app/mdb/content/core/connect.php');
      $this->connection = $connection;
    }

    public function userIdGet($username)
    {

        $result =  pg_query($connection, "SELECT user_id FROM usr.tbl_user WHERE username = '$username'");
        $row = pg_fetch_row($result);

        $this->userId = $row['user_id'];

        return $this->userId;

    }

    public function getUserManagementList()
    {
      $query = "SELECT * FROM usr.tf_pobierz_uzytkownikow()";
      $result = pg_query($this->connection, $query);
      $resp = array();

      while($row = pg_fetch_assoc($result))
      {
        array_push($resp, array(
                                  'uzytkownik_id' => $row['uzytkownik_id'],
                                  'username' => $row['username'],
                                  'imie' => $row['imie'],
                                  'nazwisko' => $row['nazwisko'],
                                  'jest_aktywny' => $row['jest_aktywny'],
                                  'rola_nazwa' => $row['rola_nazwa'],
                                  'edycja'=> "<button style='padding:5px' data-id='" . $row['uzytkownik_id'] . "' id='usrId-" . $row['uzytkownik_id'] . "' class='btn btn-info' data-toggle='modal' data-target='#editUserModal'>edytuj</button>")
                                );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }


}

?>
