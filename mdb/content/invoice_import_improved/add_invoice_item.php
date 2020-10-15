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
    $invoiceItem = json_decode($_POST['data']);
  } else {
    $_SESSION['e_invoice'] = "Nieznany błąd";
    return 0;
  }

  if(isset($_POST['invoice_id'])) {
    $invoiceId = json_decode($_POST['invoice_id']);
  } else {
    $_SESSION['e_invoice'] = "Błąd podczas dodawania faktury";
    return 0;
  }

  $success = new stdClass();
  $invoice_item_index_check_flag = 1;
  $success->success = $invoice_item_index_check_flag;
  try {
    $query = "SELECT * FROM app.sf_sprawdz_unikalnosc_indeksu_pozycji_faktury($1, $2) AS is_invoice_item_index_unique";
    $result = pg_query_params($connection, $query, array($invoiceId, $invoiceItem->item_index));
    $invoice_item_index_check = pg_fetch_assoc($result);

    if($invoice_item_index_check['is_invoice_item_index_unique'] != 1) {
      $invoice_item_index_check_flag = 0;
      $success->success = $invoice_item_index_check_flag;
      echo json_encode($success);
      return 0;
    }
    pg_free_result($result);

  } catch(Exception $error) {
      $error->getMessage();
  }

  if($invoiceItem && $invoice_item_index_check_flag == 1) {
    try {
      $query = "SELECT * FROM app.sp_dodaj_pozycje_faktury($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)";
      $result = pg_query_params(
        $connection,
        $query,
        array(
          $invoiceId,
          $invoiceItem->item_index,
          $invoiceItem->item_id,
          $invoiceItem->item_amount,
          $invoiceItem->item_unit,
          $invoiceItem->item_price,
          $invoiceItem->item_price_zero,
          $invoiceItem->item_value,
          $invoiceItem->item_margin,
          $invoiceItem->item_percent,
          $user
        ));
      echo json_encode($success);
      return $success;
    } catch(Exception $error) {
        $error->getMessage();
    }
  }

?>
