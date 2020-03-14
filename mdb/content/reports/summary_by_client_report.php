<?php
session_start();
require_once '../core/connect.php';

$login = $_SESSION['user'];
$dateFrom = $_POST['dateFrom'];
$dateTo = $_POST['dateTo'];

try {
    $query = "SELECT * FROM app.tf_podsumowanie_sprzedazy_per_kontrahent('$dateFrom', '$dateTo')";
    $result = pg_query($connection, $query);
    $resp = array();

    while($row = pg_fetch_assoc($result))
    {
      array_push($resp, array(
                                'kontrahent' => $row['kontrahent'],
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
