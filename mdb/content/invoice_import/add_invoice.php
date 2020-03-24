<?php
  session_start();
  require_once '../core/connect.php';

  if(isset($_SESSION['user']) != true && isset($_SESSION['is_logged']) != true)
  {
      header('Location:../core/index.php');
      exit();
  }

  $user = $_SESSION['user'];

  if(isset($_POST['data'])) {
    $invoiceHeader = json_decode($_POST['data']);
  } else {
    $_SESSION['e_invoice'] = "Nieznany błąd";
    return 0;
  }

  $success = new stdClass();
  $invoice_number_check_flag = 1;
  $success->success = $invoice_number_check_flag;
  try {
    $query = "SELECT * FROM app.sf_sprawdz_unikalnosc_faktura_numer('$invoiceHeader->invoice_number') AS is_invoice_number_unique";
    $result = pg_query($connection, $query);
    $invoice_number_check = pg_fetch_assoc($result);

    if($invoice_number_check['is_invoice_number_unique'] != 1) {
      $invoice_number_check_flag = 0;
      $success->success = $invoice_number_check_flag;
      echo json_encode($success);
      return 0;
    }
    pg_free_result($result);

  } catch(Exception $error) {
      $error->getMessage();
  }

  if($invoiceHeader && $invoice_number_check_flag == 1) {
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
                  ,'$user'
                )";
      $result = pg_query($connection, $query);
      echo json_encode($success);
      return 1;
    } catch(Exception $error) {
        $error->getMessage();
    }
  }

?>
