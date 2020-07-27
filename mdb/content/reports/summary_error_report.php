<?php
session_start();
require_once '../core/connect.php';

$login = $_SESSION['user'];
$dateFrom = $_POST['dateFrom'];
$dateTo = $_POST['dateTo'];

try {
    $query = "SELECT * FROM app.tf_podsumowanie_bledne_faktur($1, $2)";
    $result = pg_query_params($connection, $query, array($dateFrom, $dateTo));
    $resp = array();

    while($row = pg_fetch_assoc($result))
    {
      array_push($resp, array(
                                'faktura_numer' => $row['faktura_numer'],
                                'data_wystawienia' => $row['data_wystawienia'],
                                'kontrahent' => $row['kontrahent'],
                                'sprzedawca' => $row['sprzedawca'],
                                'region' => $row['region'],
                                'kraj' => $row['kraj'],
                                'wojewodztwo' => $row['wojewodztwo'],
                                'waluta' => $row['waluta'],
                                'eksport' => $row['eksport'],
                                'kurs' => $row['kurs'],
                                'cena_zero' => $row['cena_zero'],
                                'cena' => $row['cena'])
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
