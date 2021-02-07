<?php
session_start();
require_once '../core/connect.php';

$login = $_SESSION['user'];
$dateFrom = $_POST['dateFrom'];
$dateTo = $_POST['dateTo'];
$region = $_POST['region'];

if(!is_numeric($region)) {
  $region = null;
}

try {
    $query = "SELECT * FROM app.tf_podsumowanie_sprzedazy_per_towar_region($1, $2, $3, $4)";
    $result = pg_query_params($connection, $query, array($dateFrom, $dateTo, $region, $login));
    $resp = array();

    while($row = pg_fetch_assoc($result))
    {
      array_push($resp, array(
                                'towar' => $row['towar'],
                                'region' => $row['region'],
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
