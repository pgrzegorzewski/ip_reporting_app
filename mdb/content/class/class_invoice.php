<?php

class Invoice
{
    public $invoiceId;
    protected $connection;

    function __construct() {
      $this->setConnection();
    }

    private function setConnection() {
      require ($_SERVER["DOCUMENT_ROOT"]. '/reporting-app/mdb/content/core/connect.php');
      $this->connection = $connection;
    }

    public function getInvoiceHeaderData($invoiceItemId)
    {
      $query = "SELECT * FROM app.tf_pobierz_fakture($1)";
      $result = pg_query_params($this->connection, $query, array($invoiceItemId));
      $resp = array();

      while($row = pg_fetch_assoc($result))
      {
        array_push($resp, array(
                                  'faktura_id' => $row['faktura_id'],
                                  'faktura_numer' => $row['faktura_numer'],
                                  'data_wystawienia' => $row['data_wystawienia'],
                                  'uzytkownik_id' => $row['uzytkownik_id'],
                                  'waluta_id' => $row['waluta_id'],
                                  'kurs' => $row['kurs'],
                                  'eksport' => $row['eksport'],
                                  'dostawa' => $row['dostawa'],
                                  'przelew' => $row['przelew'],
                                  'kontrahent_id' => $row['kontrahent_id'],
                                  'kraj_id' => $row['kraj_id'],
                                  'wojewodztwo_id' => $row['wojewodztwo_id'],
                                  'region_id' => $row['region_id'],
                                  'uwagi' => $row['uwagi'])
                                );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }

    public function getInvoiceItemData($invoiceItemId)
    {
      $query = "SELECT * FROM app.tf_pobierz_pozycje_faktury($1)";
      $result = pg_query_params($this->connection, $query, array($invoiceItemId));
      $resp = array();

      while($row = pg_fetch_assoc($result))
      {
        array_push($resp, array(
                                  'faktura_pozycja_id' => $row['faktura_pozycja_id'],
                                  'pozycja_faktura' => $row['pozycja_faktura'],
                                  'towar_id' => $row['towar_id'],
                                  'ilosc' => $row['ilosc'],
                                  'jednostka' => $row['jednostka'],
                                  'cena' => $row['cena'],
                                  'cena_zero' => $row['cena_zero'],
                                  'wartosc' => $row['wartosc'],
                                  'marza' => $row['marza'],
                                  'procent' => $row['procent'])
                                );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }


    public function updateInvoiceHeader($invoiceItemId, $invoiceNumber, $invoiceDate, $salesman, $currency, $rate, $export, $transfer, $delivery, $client, $country, $voivodship, $region, $note, $login)
    {
      $success = true;
      try {
        $query = "SELECT * FROM app.sf_sprawdz_unikalnosc_faktura_numer($1) AS is_invoice_number_unique";
        $result = pg_query_params($this->connection, $query, array($invoiceNumber));
        $invoiceNumberCheck = pg_fetch_assoc($result);

        $query = "SELECT DISTINCT tf.faktura_numer FROM app.tbl_faktura tf INNER JOIN app.tbl_faktura_pozycja tfp ON tf.faktura_id =  tfp.faktura_id WHERE tfp.faktura_pozycja_id = $1";
        $invoiceNumberChangedCheck = pg_query_params($this->connection, $query, array($invoiceItemId));
        $oldInvoiceNumber = pg_fetch_assoc($invoiceNumberChangedCheck);

        $query = "SELECT DISTINCT tfp.faktura_id FROM app.tbl_faktura_pozycja tfp WHERE tfp.faktura_pozycja_id = $1";
        $invoiceIdQuery = pg_query_params($this->connection, $query, array($invoiceItemId));
        $invoiceId = pg_fetch_assoc($invoiceIdQuery);
        $invoiceId = $invoiceId['faktura_id'];

        $invoiceNumberChanged = true;
        if($oldInvoiceNumber['faktura_numer'] == $invoiceNumber) {
          $invoiceNumberChanged = false;
        }

        if($invoiceNumberCheck['is_invoice_number_unique'] != 1 && $invoiceNumberChanged) {
          $success = false;
          echo json_encode('Faktura o takiej nazwie już istnieje.');
        }
        pg_free_result($result);

      } catch(Exception $error) {
          $error->getMessage();
      }
      if($success) {
        try {
          $query = "SELECT * FROM app.sp_zaktualizuj_dane_faktury($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15)";
          $result = pg_query_params(
            $this->connection,
            $query,
            array(
              $invoiceId,
              $invoiceNumber,
              $invoiceDate,
              $salesman,
              $currency,
              $rate,
              $export,
              $transfer,
              $delivery,
              $client,
              $country,
              $voivodship,
              $region,
              $note,
              $login
          ));

        echo json_encode('Faktura zaktualizowana pomyślnie');
        } catch(Exception $error) {
            $error->getMessage();
        }
      }

    }

}
