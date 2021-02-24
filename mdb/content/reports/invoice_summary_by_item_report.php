<?php
session_start();
require_once '../core/connect.php';

$login = $_SESSION['user'];
$item = $_POST['item'];
$dateFrom = $_POST['dateFrom'];
$dateTo = $_POST['dateTo'];

if(!is_numeric($item)) {
  $item = null;
}

try {
    $query = "SELECT * FROM app.tf_listowanie_faktur_per_towar($1, $2, $3, $4)";
    $result = pg_query_params($connection, $query, array($dateFrom, $dateTo, $item, $login));
    $resp = array();

    while($row = pg_fetch_assoc($result))
    {
      array_push($resp, array(
                                'towar' => $row['towar'],
                                'faktura_numer' => $row['faktura_numer'],
                                'kontrahent_nazwa' => $row['kontrahent_nazwa'],
                                'sprzedawca' => $row['sprzedawca'],
                                'ilosc' => $row['ilosc'],
                                'suma_wartosci' => $row['suma_wartosci'],
                                'suma_marz' => $row['suma_marz'],
                                'procent' => $row['procent'])
                              );
    }
    pg_free_result($result);
    echo json_encode($resp);

}
catch (Exception $error) {
    echo $error->getMessage();
}
pg_close($connection);

 ?>
