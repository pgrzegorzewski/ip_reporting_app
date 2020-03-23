<?php
  session_start();
  require_once '../core/connect.php';

  if(isset($_POST['data'])) {
    $invoiceHeader = json_decode($_POST['data']);
  } else {
    return 0;
  }

  if($invoiceHeader) {
    try {
      $query = "SELECT * FROM app.sp_dodaj_fakture
                (
                  '$invoiceHeader->invoice_number'
                  ,$invoiceHeader->client
                  ,$invoiceHeader->salesman
                  ,$invoiceHeader->region
                  ,$invoiceHeader->country
                  ,$invoiceHeader->voivodeship
                  ,$invoiceHeader->currency
                  ,$invoiceHeader->export::BIT
                  ,'$invoiceHeader->invoice_date'::date
                  ,$invoiceHeader->rate
                  ,$invoiceHeader->money_transfer::BIT
                  ,$invoiceHeader->delivery::BIT
                  ,'$invoiceHeader->comment'
                  ,1
                )";
      $result = pg_query($connection, $query);
      $_SESSION['e_invoice'] = $query;
    } catch(Exception $error) {
        $error->getMessage();
    }
  }

?>
