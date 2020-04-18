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
                procent
            FROM app.tf_pobierz_informacje_o_fakturach($1, $2, $3, $4, $5, $6, $7, $8, $9)";
      $resp = array();
      $result = pg_query_params($connection, $query, array($invoiceFilters->invoice_date_from, $invoiceFilters->invoice_date_to, $invoiceFilters->invoice_number, $invoiceFilters->salesman, $invoiceFilters->client, $invoiceFilters->country, $invoiceFilters->voivodeship, $invoiceFilters->region, $user));
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
