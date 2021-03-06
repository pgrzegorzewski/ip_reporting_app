<?php

class Client
{
    public $clientId;
    protected $connection;

    function __construct() {
      $this->setConnection();
    }

    private function setConnection() {
      require ($_SERVER["DOCUMENT_ROOT"]. '/ip_reporting_app/mdb/content/core/connect.php');
      $this->connection = $connection;
    }

    public function getClientManagementList($login) {
      $query = "SELECT * FROM app.tf_pobierz_kontrahentow($1)";
      $result = pg_query_params($this->connection, $query, array($login));
      $resp = array();

      while($row = pg_fetch_assoc($result))
      {
        array_push($resp, array(
                                  'kontrahent_id' => $row['kontrahent_id'],
                                  'kontrahent_nazwa' => $row['kontrahent_nazwa'],
                                  'ulica' => $row['ulica'],
                                  'nr_domu' => $row['nr_domu'],
                                  'kod_pocztowy' => $row['kod_pocztowy'],
                                  'miasto' => $row['miasto'],
                                  'wojewodztwo' => $row['wojewodztwo'],
                                  'region' => $row['region'],
                                  'kraj' => $row['kraj'],
                                  'jest_aktywny' => $row['jest_aktywny'],
                                  'czarna_lista' => $row['czarna_lista'],
                                  'bonus' => ($row['bonus'] * 100),
                                  'domyslna_wartosc_przelew' => $row['domyslna_wartosc_przelew'],
                                  'domyslna_wartosc_dostawa' => $row['domyslna_wartosc_dostawa'],
                                  'domyslna_wartosc_eksport' => $row['domyslna_wartosc_eksport'],
                                  'waluta_kod' => $row['waluta_kod'],
                                  'sprzedawca' => $row['sprzedawca'],
                                  'edycja'=> "<button style='padding:5px' data-id='" . $row['kontrahent_id'] . "' id='clientId-" . $row['kontrahent_id'] . "' class='btn btn-info' data-toggle='modal' data-target='#editClientModal'>edytuj</button>",
                                  'edycja_dostep' => $row['edycja_dostep'])
                                );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }

    public function getClientData($clientId)
    {
      $query = "SELECT * FROM app.tf_pobierz_kontrahenta($1)";
      $result = pg_query_params($this->connection, $query, array($clientId));
      $resp = array();

      while($row = pg_fetch_assoc($result))
      {
        array_push($resp, array(
                                  'kontrahent_id' => $row['kontrahent_id'],
                                  'kontrahent_nazwa' => $row['kontrahent_nazwa'],
                                  'ulica' => $row['ulica'],
                                  'nr_domu' => $row['nr_domu'],
                                  'kod_pocztowy' => $row['kod_pocztowy'],
                                  'miasto' => $row['miasto'],
                                  'wojewodztwo_id' => $row['wojewodztwo_id'],
                                  'region_id' => $row['region_id'],
                                  'kraj_id' => $row['kraj_id'],
                                  'kraj' => $row['kraj'],
                                  'jest_aktywny' => $row['jest_aktywny'],
                                  'czarna_lista' => $row['czarna_lista'],
                                  'bonus' => ($row['bonus'] * 100),
                                  'domyslna_wartosc_przelew' => $row['domyslna_wartosc_przelew'],
                                  'domyslna_wartosc_dostawa' => $row['domyslna_wartosc_dostawa'],
                                  'domyslna_wartosc_eksport' => $row['domyslna_wartosc_eksport'],
                                  'domyslna_wartosc_waluta_id' => $row['domyslna_wartosc_waluta_id'],
                                  'domyslna_wartosc_sprzedawca_id' => $row['domyslna_wartosc_sprzedawca_id'])
                                );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }

    public function updateClientData($clientId, $clientName, $street, $address2, $postCode, $city, $voivodeship, $region, $country, $isActive, $isBlackList, $bonus, $transfer, $delivery, $export, $currency, $salesman)
    {
      $success = true;
      $bonus = $bonus / 100;
      if(!$clientId) {
        $_SESSION['e_client_update'] = '<p style = "color:red; text-align:center;">Błąd podczas przekazania id kontrahenta</p>';
        $success = false;
      }
      if(strlen(preg_replace('/\s/', '', $clientName)) < 3) {
        $_SESSION['e_client_update'] = '<p style = "color:red; text-align:center;">Zbyt krótka nazwa kontrahenta</p>';
        $success = false;
      }

      try {
        $query = "SELECT * FROM app.sf_sprawdz_unikalnosc_kontrahenta($1) AS is_client_name_unique";
        $client_name_unique_check = pg_query_params($this->connection, $query, array($clientName));
        $is_client_name_unique_check = pg_fetch_assoc($client_name_unique_check);

        $query = "SELECT kontrahent_nazwa AS old_client_name FROM app.tbl_kontrahent WHERE kontrahent_id = $1";
        $client_name_changed_check = pg_query_params($this->connection, $query, array($clientId));
        $old_client_name = pg_fetch_assoc($client_name_changed_check);

        $clientNameChanged = true;
        if($old_client_name['old_client_name'] == $clientName) {
          $clientNameChanged = false;
        }

        if($is_client_name_unique_check['is_client_name_unique'] != 1 && $clientNameChanged)
        {
            $success = false;
            $_SESSION['e_client_update'] = '<p style = "color:red; text-align:center;">Kontrahent o takiej nazwie już istnieje.</p>';
        }
      } catch(Exception $error) {
          $error->getMessage();
      }

      if($success == true) {
        try {
          $query = "SELECT * FROM app.sp_zaktualizuj_dane_kontrahenta($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17)";
          $result = pg_query_params($this->connection, $query, array($clientId, $clientName, $street, $address2, $postCode, $city, $voivodeship, $region, $country, $isActive, $isBlackList, $bonus, $transfer, $delivery, $export, $currency, $salesman));
          $_SESSION['e_client_update'] = '<p style = "color:green; text-align:center;">Kontrahent zaktualizowany pomyślnie.</p>';

        } catch(Exception $error) {
            $error->getMessage();
        }
      }
    }

    public function addClient($clientNameNew, $streetNew, $address2New, $postCodeNew, $cityNew, $voivodeshipNew, $regionNew, $countryNew, $isActiveNew, $isBlackListNew, $transferNew, $deliveryNew, $exportNew, $currencyNew, $salesmanNew)
    {
      $success = true;

      if(strlen(preg_replace('/\s/', '', $clientNameNew)) < 3) {
        $_SESSION['e_client_update'] = '<p style = "color:red; text-align:center;">Zbyt krótka nazwa kontrahenta</p>';
        $success = false;
      }

      try {
        $query = "SELECT * FROM app.sf_sprawdz_unikalnosc_kontrahenta($1) AS is_client_name_unique";
        $client_name_unique_check = pg_query_params($this->connection, $query, array($clientNameNew));
        $is_client_name_unique_check = pg_fetch_assoc($client_name_unique_check);

        if($is_client_name_unique_check['is_client_name_unique'] != 1 )
        {
            $success = false;
            $_SESSION['e_client_update'] = '<p style = "color:red; text-align:center;">Kontrahent o takiej nazwie już istnieje.</p>';
        }
      } catch(Exception $error) {
          $error->getMessage();
      }

      if($success == true) {
        try {
          $query = "SELECT * FROM app.sp_dodaj_kontrahenta($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15)";
          $result = pg_query_params($this->connection, $query, array($clientNameNew, $streetNew, $address2New, $postCodeNew, $cityNew, $voivodeshipNew, $regionNew, $countryNew, $isActiveNew, $isBlackListNew, $transferNew, $deliveryNew, $exportNew, $currencyNew, $salesmanNew));
          $_SESSION['e_client_update'] = '<p style = "color:green; text-align:center;">Kontrahent dodany pomyślnie.</p>';

        } catch(Exception $error) {
            $error->getMessage();
        }
      }

    }

}
