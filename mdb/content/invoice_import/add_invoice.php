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
    $query = "SELECT * FROM app.sf_sprawdz_unikalnosc_faktura_numer($1) AS is_invoice_number_unique";
    $result = pg_query_params($connection, $query, array($invoiceHeader->invoice_number));
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
      $query = "SELECT * FROM app.sp_dodaj_fakture($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15)";
      $result = pg_query_params (
        $connection,
        $query,
        array(
          $invoiceHeader->invoice_number,
          $invoiceHeader->client,
          $invoiceHeader->salesman,
          $invoiceHeader->region,
          $invoiceHeader->country,
          $invoiceHeader->voivodeship,
          $invoiceHeader->currency,
          $invoiceHeader->export,
          $invoiceHeader->invoice_date,
          $invoiceHeader->rate,
          $invoiceHeader->money_transfer,
          $invoiceHeader->delivery,
          $invoiceHeader->comment,
          (($invoiceHeader->bonus)/100),
          $user
        ));
      $response = pg_fetch_array($result);
      echo  json_encode(array('faktura_id' => $response['sp_dodaj_fakture']));
    } catch(Exception $error) {
        $error->getMessage();
    }
  }

?>
