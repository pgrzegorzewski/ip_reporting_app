<?php
session_start();
require_once '../core/connect.php';

$login = $_SESSION['user'];
$dateFrom = $_POST['dateFrom'];
$dateTo = $_POST['dateTo'];

try {
    $query = "SELECT * FROM app.tf_pobierz_kwoty_przeterminowane($1, $2, $3)";
    $result = pg_query_params($connection, $query, array($dateFrom, $dateTo, $login));
    $resp = array();

    while($row = pg_fetch_assoc($result))
    {
      array_push($resp, array(
                                'data' => $row['data'],
                                'sprzedawca' => $row['sprzedawca'],
                                'wartosc_przeterminowana' => $row['wartosc_przeterminowana'],
                                'edytuj' => "<button style='padding:5px' data-id='" . $row['sprzedawca'] . "-" .  $row['data'] . "' id='" . $row['sprzedawca'] . "-" .  $row['data'] . "' class='btn btn-info'>Zapisz</button>"
                              )
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
