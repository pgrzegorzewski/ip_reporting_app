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
                                  'region_id' => $row['region_id'])
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
}
