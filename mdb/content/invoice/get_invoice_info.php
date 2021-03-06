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
    $invoiceFilters = json_decode($_POST['data']);
  } else {
    $_SESSION['e_invoice'] = "Nieznany błąd";
    return 0;
  }

  foreach ($invoiceFilters as $key => $value) {
    if(!$value) {
      $invoiceFilters->$key = NULL;
    }
  }

  $dateFrom =  date('Y-m-d',strtotime('first day of this month'));
  $dateTo =  date('Y-m-d',strtotime('last day of this month'));

  if($invoiceFilters->invoice_date_from != NULL) {
      $dateFrom = $invoiceFilters->invoice_date_from;
  }

  if($invoiceFilters->invoice_date_to != NULL) {
      $dateTo = $invoiceFilters->invoice_date_to;
  }

  if($invoiceFilters->invoice_date_from != NULL && ($invoiceFilters->invoice_date_to == NULL)) {
      $dateTo = '2200-01-01';
  }

  if($invoiceFilters->invoice_date_to != NULL && ($invoiceFilters->invoice_date_from == NULL)) {
      $dateFrom = '1900-01-01';
  }

  try {
    $query = "SELECT
                faktura_numer,
                faktura_pozycja_id,
                data_wystawienia,
                uzytkownik,
                waluta_kod,
                kurs,
                eksport,
                dostawa,
                przelew,
                kontrahent_nazwa,
                kraj_kod,
                wojewodztwo_kod,
                region_kod,
                uwagi,
                edycja,
                pozycja_faktura,
                towar_nazwa,
                ilosc,
                jednostka,
                cena,
                cena_zero,
                wartosc,
                marza,
                procent,
                (bonus * 100) AS bonus
            FROM app.tf_pobierz_informacje_o_fakturach($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13)";
      $resp = array();
      $result = pg_query_params($connection, $query, array($dateFrom, $dateTo, $invoiceFilters->invoice_number, $invoiceFilters->salesman, $invoiceFilters->export, $invoiceFilters->pay, $invoiceFilters->delivery, $invoiceFilters->currency, $invoiceFilters->client, $invoiceFilters->country, $invoiceFilters->voivodeship, $invoiceFilters->region, $user));
      $loopCnt = 0;
      while($row = pg_fetch_assoc($result))
      {
        array_push($resp, array(
                                  'faktura_numer' => $row['faktura_numer'],
                                  'data_wystawienia' => $row['data_wystawienia'],
                                  'uzytkownik' => $row['uzytkownik'],
                                  'waluta_kod' => $row['waluta_kod'],
                                  'kurs' => $row['kurs'],
                                  'eksport' => $row['eksport'],
                                  'dostawa' => $row['dostawa'],
                                  'przelew' => $row['przelew'],
                                  'kontrahent_nazwa' => $row['kontrahent_nazwa'],
                                  'kraj_kod' => $row['kraj_kod'],
                                  'wojewodztwo_kod' => $row['wojewodztwo_kod'],
                                  'region_kod' => $row['region_kod'],
                                  'bonus' => $row['bonus'],
                                  'pozycja_faktura' => $row['pozycja_faktura'],
                                  'towar_nazwa' => $row['towar_nazwa'],
                                  'ilosc' => $row['ilosc'],
                                  'jednostka' => $row['jednostka'],
                                  'cena' => $row['cena'],
                                  'cena_zero' => $row['cena_zero'],
                                  'wartosc' => $row['wartosc'],
                                  'marza' => $row['marza'],
                                  'procent' => $row['procent'],
                                  'uwagi' => $row['uwagi']
                                  )
                                );
        if($row['edycja'] == 0){
          $resp[$loopCnt] += ['edycja'=>$row['edycja']];
        } else {
          $resp[$loopCnt] += ['edycja'=> "<button style='padding:5px' data-id='" . $row['faktura_pozycja_id'] . "' id='fpId-" . $row['faktura_pozycja_id'] . "' class='btn btn-info' data-toggle='modal' data-target='#editInvoiceItemModal'>Edytuj</button>"];
        }
        $loopCnt++;
      }
      pg_free_result($result);
      echo json_encode($resp);
    } catch(Exception $error) {
        $error->getMessage();
    }


?>
