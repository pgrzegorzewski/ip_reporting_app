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

    public function getUserData($userId)
    {
      $query = "SELECT * FROM usr.tf_pobierz_uzytkownika('$userId')";
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
                                  'rola_id' => $row['rola_id'])
                                );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }

    public function updateUserData($userId, $username, $firstName, $lastName, $role, $isActive)
    {
      $success = true;
      if(!$userId) {
        $_SESSION['e_user_update'] = '<p style = "color:red; text-align:center;">Błąd podczas przekazania użytkownika</p>';
        $success = false;
      }
      if(strlen(preg_replace('/\s/', '', $username)) < 2) {
        $_SESSION['e_user_update'] = '<p style = "color:red; text-align:center;">Zbyt krótki login.</p>';
        $success = false;
      }
      if(strlen(preg_replace('/\s/', '', $firstName)) < 3) {
        $_SESSION['e_user_update'] = '<p style = "color:red; text-align:center;">Zbyt krótkie imię.</p>';
        $success = false;
      }
      if(strlen(preg_replace('/\s/', '', $lastName)) < 3) {
        $_SESSION['e_user_update'] = '<p style = "color:red; text-align:center;">Zbyt krótkie nazwisko.</p>';
        $success = false;
      }

      try {
        $query = "SELECT * FROM usr.sf_sprawdz_unikalnosc_login('$username') AS is_username_unique";
        $username_unique_check = pg_query($this->connection, $query);
        $is_username_unique_check = pg_fetch_assoc($username_unique_check);

        $query = "SELECT * FROM usr.sf_pobierz_login($userId) AS old_username";
        $username_changed_check = pg_query($this->connection, $query);
        $old_username = pg_fetch_assoc($username_changed_check);

        $usernameChanged = true;
        if($old_username['old_username'] == $username) {
          $usernameChanged = false;
        }

        if($is_username_unique_check['is_username_unique'] != 1 && $usernameChanged)
        {
            $success = false;
            $_SESSION['e_user_update'] = '<p style = "color:red; text-align:center;">login w użyciu.</p>';
        }
      } catch(Exception $error) {
          $error->getMessage();
      }

      if($success == true) {
        try {
          $query = "SELECT * FROM usr.sp_zaktualizuj_dane_uzytkownika
                    (   $userId
                        ,'$username'
                        ,'$firstName'
                        ,'$lastName'
                        ,'$role'
                        ,$isActive::BIT
                    )";
          $result = pg_query($this->connection, $query);
          $_SESSION['e_user_update'] = '<p style = "color:green; text-align:center;">Użytkownik zaktualizowany pomyślnie.</p>';
        } catch(Exception $error) {
            $error->getMessage();
        }
      }
    }

    public function assignTemporaryPassword($userId, $passwordTemporary) {

      $passwordTemporaryHashed = password_hash($passwordTemporary, PASSWORD_DEFAULT);
      try {
        $query = "SELECT * FROM usr.sp_przypisz_haslo_tymczasowe
                  (   $userId
                      ,'$passwordTemporaryHashed'
                  )";
        $result = pg_query($this->connection, $query);
        echo 'haslo przypisane';
      } catch(Exception $error) {
          $error->getMessage();
          $_SESSION['e_password_temporary_update'] = 'coś poszło nie tak:(';
      }

    }

    public function addUser($username, $firstName, $lastName, $role, $isActive, $passwordTemporary)
    {
      $success = true;

      if(strlen(preg_replace('/\s/', '', $username)) < 2) {
        $_SESSION['e_user_update'] = '<p style = "color:red; text-align:center;">Zbyt krótki login.</p>';
        $success = false;
      }
      if(strlen(preg_replace('/\s/', '', $firstName)) < 3) {
        $_SESSION['e_user_update'] = '<p style = "color:red; text-align:center;">Zbyt krótkie imię.</p>';
        $success = false;
      }
      if(strlen(preg_replace('/\s/', '', $lastName)) < 3) {
        $_SESSION['e_user_update'] = '<p style = "color:red; text-align:center;">Zbyt krótkie nazwisko.</p>';
        $success = false;
      }
      if(strlen(preg_replace('/\s/', '', $passwordTemporary)) < 5) {
        $_SESSION['e_user_update'] = '<p style = "color:red; text-align:center;">Zbyt krótkie hasło.</p>';
        $success = false;
      }
      if(!$role || intval($role) <= 0) {
        $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Brak rodzaju</p>';
        $success = false;
      }

      try {
        $query = "SELECT * FROM usr.sf_sprawdz_unikalnosc_login('$username') AS is_username_unique";
        $username_unique_check = pg_query($this->connection, $query);
        $is_username_unique_check = pg_fetch_assoc($username_unique_check);

        if($is_username_unique_check['is_username_unique'] != 1)
        {
            $success = false;
            $_SESSION['e_user_update'] = '<p style = "color:red; text-align:center;">login w użyciu.</p>';
        }
      } catch(Exception $error) {
          $error->getMessage();
      }

      $passwordTemporary = password_hash($passwordTemporary, PASSWORD_DEFAULT);
      if($success == true) {
        try {
          $query = "SELECT * FROM usr.sp_dodaj_uzytkownika_v2
                    (   '$username'
                        ,'$firstName'
                        ,'$lastName'
                        ,'$passwordTemporary'
                        ,$role
                        ,$isActive::BIT
                    )";
          $result = pg_query($this->connection, $query);
          //$_SESSION['e_user_update'] = '<p style = "color:green; text-align:center;">Użytkownik dodany.</p>';
          $_SESSION['e_user_update'] = $query;
        } catch(Exception $error) {
            $error->getMessage();
        }
      }
    }

}

?>
