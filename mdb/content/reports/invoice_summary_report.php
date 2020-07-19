<?php
session_start();
require_once '../core/connect.php';

$login = $_SESSION['user'];
$salesman = $_SESSION['salesman'];
$dateFrom = $_POST['dateFrom'];
$dateTo = $_POST['dateTo'];

try {
    $query = "SELECT * FROM app.tf_podsumowanie_faktur_per_sprzedawca($1, $2, $4, $3)";
    $result = pg_query_params($connection, $query, array($dateFrom, $dateTo, $salesman, $login));
    $resp = array();

    while($row = pg_fetch_assoc($result))
    {
      array_push($resp, array(
                                'faktura_numer' => $row['faktura_numer'],
                                'data_wystawienia' => $row['data_wystawienia'],
                                'kontrahent' => $row['kontrahent'],
                                'sprzedawca' => $row['sprzedawca'],
                                'suma_wartosci' => $row['suma_wartosci']
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
