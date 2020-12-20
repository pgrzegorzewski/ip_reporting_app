<?php

class User
{
    public $userId;
    protected $connection;

    function __construct() {
      $this->setConnection();
    }

    private function setConnection() {
      require ($_SERVER["DOCUMENT_ROOT"]. '/ip_reporting_app/mdb/content/core/connect.php');
      $this->connection = $connection;
    }

    public function userIdGet($username)
    {

        $result =  pg_query_params($connection, "SELECT user_id FROM usr.tbl_uzytkownik WHERE username = $1", array($username));
        $row = pg_fetch_row($result);

        $this->userId = $row['uzytkownik_id'];

        return $this->userId;

    }

    public function userIdByUsernameGet($username)
    {

        $result =  pg_query_params($this->connection, "SELECT uzytkownik_id FROM usr.tbl_uzytkownik WHERE username = $1", array($username));
        $row = pg_fetch_assoc($result);

        return $row['uzytkownik_id'];
    }

    public function getUserManagementList($login)
    {
      $query = "SELECT * FROM usr.tf_pobierz_uzytkownikow($1)";
      $result = pg_query_params($this->connection, $query, array($login));
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
                                  'edycja' => "<button style='padding:5px' data-id='" . $row['uzytkownik_id'] . "' id='usrId-" . $row['uzytkownik_id'] . "' class='btn btn-info' data-toggle='modal' data-target='#editUserModal'>edytuj</button>",
                                  'edycja_dostep' => $row['edycja_dostep'])
                                );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }

    public function getUserData($userId)
    {
      $query = "SELECT * FROM usr.tf_pobierz_uzytkownika($1)";
      $result = pg_query_params($this->connection, $query, array($userId));
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
                                  'rola_id' => $row['rola_id'],
                                  'stanowisko' => $row['stanowisko_nazwa'])
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
        $query = "SELECT * FROM usr.sf_sprawdz_unikalnosc_login($1) AS is_username_unique";
        $username_unique_check = pg_query_params($this->connection, $query, array($username));
        $is_username_unique_check = pg_fetch_assoc($username_unique_check);

        $query = "SELECT * FROM usr.sf_pobierz_login($1) AS old_username";
        $username_changed_check = pg_query_params($this->connection, $query, array($userId));
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
          $query = "SELECT * FROM usr.sp_zaktualizuj_dane_uzytkownika($1, $2, $3, $4, $5, $6)";
          $result = pg_query_params($this->connection, $query, array($userId, $username, $firstName, $lastName, $role, $isActive));
          $_SESSION['e_user_update'] = '<p style = "color:green; text-align:center;">Użytkownik zaktualizowany pomyślnie.</p>';
        } catch(Exception $error) {
            $error->getMessage();
        }
      }
    }

    public function assignTemporaryPassword($userId, $passwordTemporary) {

      $success = true;

      try {
          if (strlen($passwordTemporary) < 8 || strlen($passwordTemporary) > 20) {
              $success = false;
              $_SESSION['e_password_temporary_update'] .= '<p style = "color:red; text-align:center;">Hasło musi posiadać od 8 do 20 znaków.</p>';
          }
  
          if(!preg_match('/\d/', $passwordTemporary)){
              $success = false;
              $_SESSION['e_password_temporary_update'] .= '<p style = "color:red; text-align:center;">Hasło musi zawierać cyfrę</p>';
          }
  
          if(!preg_match('/[A-Z]/', $passwordTemporary)){
              $success = false;
              $_SESSION['e_password_temporary_update'] .= '<p style = "color:red; text-align:center;">Hasło musi zawierać wielką literę.</p>';
          }
  
          if (!preg_match('/[^a-zA-Z0-9]/', $passwordTemporary))
          {
              $success = false;
              $_SESSION['e_password_temporary_update'] .= '<p style = "color:red; text-align:center;">Hasło musi zawierać znak specjalny.</p>';
          }
      }
      catch (Exception $error) {
          echo $error->getMessage();
      }

      if($success) {
        $passwordTemporaryHashed = password_hash($passwordTemporary, PASSWORD_DEFAULT);
        
        try {
          $query = "SELECT * FROM usr.sp_przypisz_haslo_tymczasowe($1, $2)";
          $result = pg_query_params($this->connection, $query, array($userId, $passwordTemporaryHashed));
          echo 'haslo przypisane';
        } catch(Exception $error) {
            $error->getMessage();
            $_SESSION['e_password_temporary_update'] = 'coś poszło nie tak:(';
        }
      }

    }

    public function updateUserLatePayValue($login, $userId, $latePayYear, $latePayMonth, $latePayValue) {
      $isLatePayEntered = 0;
      $updatingUser = $this->userIdByUsernameGet($login);
      $date = new DateTime();

      try {
        $query = "SELECT
                    COUNT(*) as late_pay_cnt
                  FROM
                    usr.tbl_uzytkownik_kwota_przeterminowana
                  WHERE
                    rok = $1
                    AND miesiac = $2
                    AND uzytkownik_id = $3";
        $result = pg_query_params($this->connection, $query, array( $latePayYear, $latePayMonth, $userId));
        $isLatePayEntered = pg_fetch_assoc($result);
      } catch(Exception $error) {
          $error->getMessage();
          $_SESSION['e_late_pay_update'] = 'coś poszło nie tak:(';
      }
      if ($isLatePayEntered['late_pay_cnt'] == 0) {
        try {
          $queryInsert = "INSERT INTO usr.tbl_uzytkownik_kwota_przeterminowana(uzytkownik_id, rok, miesiac, kwota_przeterminowana, data_utworzenia, wprowadzone_przez_uzytkownik_id)
                    VALUES($1, $2, $3, $4, $5, $6);
                    ";
          $result = pg_query_params($this->connection, $queryInsert, array($userId, $latePayYear, $latePayMonth, $latePayValue, date_format($date, 'Y-m-d H:i:s'), $updatingUser));

          echo 'Wartość wprowadzona';
        } catch(Exception $error) {
            $error->getMessage();
            $_SESSION['e_password_temporary_update'] = 'coś poszło nie tak:(';
        }
      } elseif ($isLatePayEntered['late_pay_cnt'] == 1) {
        $queryUpdate = "UPDATE usr.tbl_uzytkownik_kwota_przeterminowana
                  SET
                    kwota_przeterminowana = $1
                    ,ostatnio_zaktualizowane_przez_uzytkownik_id = $2
                    ,data_ostatniej_aktualizacji = $3
                  WHERE
                    rok = $4
                    AND miesiac = $5
                    AND uzytkownik_id = $6
                  ";
        $result = pg_query_params($this->connection, $queryUpdate, array($latePayValue, $updatingUser,  date_format($date, 'Y-m-d H:i:s'), $latePayYear, $latePayMonth, $userId));
        echo 'Wartość zaktualizowana';
      } else {
        $_SESSION['e_late_pay_update'] = 'coś poszło nie tak:(';
      }
    }

    public function getUserLatePay($userId, $latePayYear, $latePayMonth) {
      try {
        $query = "SELECT COALESCE((
                    SELECT
                        ukp.kwota_przeterminowana
                    FROM
                        usr.tbl_uzytkownik_kwota_przeterminowana ukp
                    WHERE uzytkownik_kwota_przeterminowana_id =
                                                               (
                                                                SELECT
                                                                    MAX(uzytkownik_kwota_przeterminowana_id ) AS uzytkownik_kwota_przeterminowana_id
                                                                FROM
                                                                    usr.tbl_uzytkownik_kwota_przeterminowana
                                                                WHERE
                                                                    uzytkownik_id = $1
                                                                    AND rok = $2
                                                                    AND miesiac = $3
                                                                )

                    ), 0) as kwota_przeterminowana";

        $result = pg_query_params($this->connection, $query, array($userId, $latePayYear, $latePayMonth));
        $row = pg_fetch_assoc($result);

        echo $row['kwota_przeterminowana'];
      } catch(Exception $error) {
          $error->getMessage();
      }
    }

    public function addUser($username, $firstName, $lastName, $role, $isActive, $passwordTemporary)
    {
      $success = true;

      if(strlen(preg_replace('/\s/', '', $username)) < 2) {
        $_SESSION['e_user_update'] .= '<p style = "color:red; text-align:center;">Zbyt krótki login.</p>';
        $success = false;
      }
      if(strlen(preg_replace('/\s/', '', $firstName)) < 3) {
        $_SESSION['e_user_update'] .= '<p style = "color:red; text-align:center;">Zbyt krótkie imię.</p>';
        $success = false;
      }
      if(strlen(preg_replace('/\s/', '', $lastName)) < 3) {
        $_SESSION['e_user_update'] .= '<p style = "color:red; text-align:center;">Zbyt krótkie nazwisko.</p>';
        $success = false;
      }
 
      if (strlen($passwordTemporary) < 8 || strlen($passwordTemporary) > 20) {
            $success = false;
            $_SESSION['e_user_update'] .= '<p style = "color:red; text-align:center;">Hasło musi posiadać od 8 do 20 znaków.</p>';
      }

      if(!preg_match('/\d/', $passwordTemporary)){
          $success = false;
          $_SESSION['e_user_update'] .= '<p style = "color:red; text-align:center;">Hasło musi zawierać cyfrę</p>';
      }

      if(!preg_match('/[A-Z]/', $passwordTemporary)){
          $success = false;
          $_SESSION['e_user_update'] .= '<p style = "color:red; text-align:center;">Hasło musi zawierać wielką literę.</p>';
      }

      if (!preg_match('/[^a-zA-Z0-9]/', $passwordTemporary))
      {
          $success = false;
          $_SESSION['e_user_update'] .= '<p style = "color:red; text-align:center;">Hasło musi zawierać znak specjalny.</p>';
      }

      if(!$role || intval($role) <= 0) {
        $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Brak rodzaju</p>';
        $success = false;
      }

      try {
        $query = "SELECT * FROM usr.sf_sprawdz_unikalnosc_login($1) AS is_username_unique";
        $username_unique_check = pg_query_params($this->connection, $query, array($username));
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
          $query = "SELECT * FROM usr.sp_dodaj_uzytkownika($1, $2, $3, $4, $5, $6)";
          $result = pg_query_params($this->connection, $query, array($username, $firstName, $lastName, $passwordTemporary, $role, $isActive));
          $_SESSION['e_user_update'] = '<p style = "color:green; text-align:center;">Użytkownik pomyślnie dodany.</p>';
        } catch(Exception $error) {
            $error->getMessage();
        }
      }
    }

}

?>
