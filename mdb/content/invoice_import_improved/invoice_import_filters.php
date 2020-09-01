<?php

require ($_SERVER["DOCUMENT_ROOT"]. '/ip_reporting_app/mdb/content/core/connect.php');

$filterType = $_POST['type'];

$dateFrom =  date('Y-m-d',strtotime('first day of this month'));
$dateTo =  date('Y-m-d',strtotime('last day of this month'));

if(isset($_POST['dateFrom']) && $_POST['dateFrom'] != null && $_POST['dateFrom'] != '') {
    $dateFrom = $_POST['dateFrom'];
}

if(isset($_POST['dateTo']) && $_POST['dateTo'] != null && $_POST['dateTo'] != '') {
    $dateTo = $_POST['dateTo'];
}

if($_POST['dateFrom'] != null && $_POST['dateFrom'] != '' && ($_POST['dateTo'] == null || $_POST['dateTo'] == '')) {
    $dateTo = '2200-01-01';
}

if($_POST['dateTo'] != null && $_POST['dateTo'] != '' && ($_POST['dateFrom'] == null || $_POST['dateFrom'] == '')) {
    $dateFrom = '1900-01-01';
}

if ($connection) {
    try {
        switch ($filterType) {
          case 'invoice_number':
              $query = "
                        SELECT DISTINCT
                          tf.faktura_id,
                          tf.faktura_numer
                        FROM app.tbl_faktura tf
                        LEFT JOIN (
                                    SELECT
                                        COUNT(*) aktywne_pozycje_cnt
                                        ,faktura_id
                                    FROM app.tbl_faktura_pozycja tfp
                                    WHERE
                                        jest_aktywny = 1::BIT
                                    GROUP BY
                                        faktura_id
                                  ) xtfp
                        ON tf.faktura_id = xtfp.faktura_id
                        WHERE
                          tf.data_wystawienia >= $1
                          AND tf.data_wystawienia <= $2
                          AND tf.jest_aktywny = 1::BIT
                          AND xtfp.aktywne_pozycje_cnt > 0
                        ORDER BY
                          tf.faktura_numer
              ";
              $invoiceQuery = pg_query_params($connection, $query, array($dateFrom, $dateTo));

              while($row = pg_fetch_assoc($invoiceQuery))
              {
                  $invoiceId = $row["faktura_id"];
                  $invoiceNumber = $row["faktura_numer"];
                  $invoiceArray[] = array("faktura_id" => $invoiceId, "faktura_numer" => $invoiceNumber);
              }
              echo json_encode($invoiceArray);

              break;
            case 'region':
                $query = "
                        SELECT region_id, region_nazwa FROM app.tbl_region
                        WHERE jest_wybieralne = 1::BIT
                        ORDER BY
                          region_nazwa
                ";
                $regionQuery = @pg_query($connection, $query);

                while($row = pg_fetch_assoc($regionQuery))
                {
                    $regionId = $row["region_id"];
                    $regionName = $row["region_nazwa"];
                    $regionArray[] = array("region_id" => $regionId, "region_nazwa" => $regionName);
                }
                echo json_encode($regionArray);
                break;
            case 'country':
                $query = "
                        SELECT kraj_id, kraj_nazwa, kraj_kod FROM app.tbl_kraj
                        WHERE jest_wybieralne = 1::BIT
                ";
                $countryQuery = @pg_query($connection, $query);

                while($row = pg_fetch_assoc($countryQuery))
                {
                    $countryId = $row["kraj_id"];
                    $countryName = $row["kraj_nazwa"];
                    $countryCode = $row["kraj_kod"];
                    $countryArray[] = array("kraj_id" => $countryId, "kraj_nazwa" => $countryName, "kraj_kod" => $countryCode);
                }
                echo json_encode($countryArray);
                break;
            case 'client':
                $query = "
                        SELECT kontrahent_id, kontrahent_nazwa, bonus FROM app.tbl_kontrahent
                        ORDER BY
                          kontrahent_nazwa
                ";
                $clientQuery = @pg_query($connection, $query);

                while($row = pg_fetch_assoc($clientQuery))
                {
                    $clientId = $row["kontrahent_id"];
                    $clientyName = $row["kontrahent_nazwa"];
                    $bonus = $row["bonus"];
                    $clientArray[] = array("kontrahent_id" => $clientId, "kontrahent_nazwa" => $clientyName, "bonus" => $bonus);
                }
                echo json_encode($clientArray);
                break;
                case 'client_active_only':
                    $query = "
                        SELECT
                          tk.kontrahent_id,
                          tk.kontrahent_nazwa,
                          tk.wojewodztwo_id,
                          tk.region_id,
                          tk.kraj_id,
                          tk.bonus,
                          tk.domyslna_wartosc_przelew,
                          tk.domyslna_wartosc_dostawa,
                          tk.domyslna_wartosc_eksport,
                          tk.domyslna_wartosc_waluta_id,
                          tk.domyslna_wartosc_sprzedawca_id,
                          tk.kontrahent_nazwa_zewnetrzna
                        FROM
                          app.tbl_kontrahent tk
                        WHERE
                          jest_aktywny = 1::BIT
                        ORDER BY
                          kontrahent_nazwa
                    ";
                    $clientQuery = @pg_query($connection, $query);

                    while($row = pg_fetch_assoc($clientQuery))
                    {
                        $clientArray[] = array("kontrahent_id" => $row["kontrahent_id"],
                          "kontrahent_nazwa" => $row["kontrahent_nazwa"],
                          "wojewodztwo_id" => $row["wojewodztwo_id"],
                          "region_id" => $row["region_id"],
                          "kraj_id" => $row["kraj_id"],
                          "bonus" => $row["bonus"],
                          "domyslna_wartosc_przelew" => $row["domyslna_wartosc_przelew"],
                          "domyslna_wartosc_dostawa" => $row["domyslna_wartosc_dostawa"],
                          "domyslna_wartosc_eksport" => $row["domyslna_wartosc_eksport"],
                          "domyslna_wartosc_waluta_id" => $row["domyslna_wartosc_waluta_id"],
                          "domyslna_wartosc_sprzedawca_id" => $row["domyslna_wartosc_sprzedawca_id"],
                          "kontrahent_nazwa_zewnetrzna" => $row["kontrahent_nazwa_zewnetrzna"]
                        );
                    }
                    echo json_encode($clientArray);
                    break;
            case 'salesman':
                $query = "
                  SELECT
                      uzytkownik_id,
                      (imie || ' ' || nazwisko) AS uzytkownik_nazwa,
                      uzytkownik_nazwa_zewnetrzna
                  FROM usr.tbl_uzytkownik tu
                  WHERE
                    stanowisko_id = 1
                  ORDER BY nazwisko
                ";
                $salesmanQuery = @pg_query($connection, $query);

                while($row = pg_fetch_assoc($salesmanQuery))
                {
                    $salesmantId = $row["uzytkownik_id"];
                    $salesmanName = $row["uzytkownik_nazwa"];
                    $salesmanNameExternal = $row["uzytkownik_nazwa_zewnetrzna"];
                    $salesmanArray[] = array("uzytkownik_id" => $salesmantId, "uzytkownik_nazwa" => $salesmanName, "uzytkownik_nazwa_zewnetrzna" => $salesmanNameExternal);
                }
                echo json_encode($salesmanArray);
                break;
            case 'salesman_active_only':
                $query = "
                  SELECT
                      uzytkownik_id,
                      (imie || ' ' || nazwisko) AS uzytkownik_nazwa,
                      uzytkownik_nazwa_zewnetrzna
                  FROM usr.tbl_uzytkownik tu
                  WHERE
                    jest_aktywny = 1::BIT
                    AND stanowisko_id = 1
                  ORDER BY nazwisko
                ";
                $salesmanQuery = @pg_query($connection, $query);

                while($row = pg_fetch_assoc($salesmanQuery))
                {
                    $salesmantId = $row["uzytkownik_id"];
                    $salesmanName = $row["uzytkownik_nazwa"];
                    $salesmanNameExternal = $row["uzytkownik_nazwa_zewnetrzna"];
                    $salesmanArray[] = array("uzytkownik_id" => $salesmantId, "uzytkownik_nazwa" => $salesmanName,  "uzytkownik_nazwa_zewnetrzna" => $salesmanNameExternal);
                }
                echo json_encode($salesmanArray);
                break;
            case 'voivodeship':
                $query = "
                        SELECT wojewodztwo_id, wojewodztwo_nazwa, LOWER(wojewodztwo_nazwa_pelna) AS wojewodztwo_nazwa_pelna FROM app.tbl_wojewodztwo
                        WHERE jest_wybieralne = 1::BIT
                ";
                $voivodeshipQuery = @pg_query($connection, $query);

                while($row = pg_fetch_assoc($voivodeshipQuery))
                {
                    $voivodeshipId = $row["wojewodztwo_id"];
                    $voivodeshipName = $row["wojewodztwo_nazwa"];
                    $voivodeshipNameFull = $row["wojewodztwo_nazwa_pelna"];
                    $voivodeshipArray[] = array("wojewodztwo_id" => $voivodeshipId, "wojewodztwo_nazwa" => $voivodeshipName, "wojewodztwo_nazwa_pelna" => $voivodeshipNameFull);
                }
                echo json_encode($voivodeshipArray);
                break;
            case 'item':
                $query = "
                        SELECT towar_id, towar_nazwa FROM app.tbl_towar
                        ORDER BY
                          towar_nazwa
                ";
                $itemQuery = @pg_query($connection, $query);

                while($row = pg_fetch_assoc($itemQuery))
                {
                    $itemId = $row["towar_id"];
                    $itemName = $row["towar_nazwa"];
                    $itemArray[] = array("towar_id" => $itemId, "towar_nazwa" => $itemName);
                }
                echo json_encode($itemArray);
                break;
            }
    } catch(Exception $err)
    {
        echo '<span style="color:red;">Server error';
        echo '<br/>Dev info: '.$error->getMessage();
    }
}

?>
