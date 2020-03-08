<?php

require ($_SERVER["DOCUMENT_ROOT"]. '/reporting-app/mdb/content/connect.php');

$filterType = $_POST['type'];
if ($connection) {
    try {
        switch ($filterType) {
            case 'region':
                $query = "
                        SELECT region_id, region_nazwa FROM app.tbl_region
                ";
                $regionQuery = @pg_query($connection, $query);

                $regionArray = array();

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
                        SELECT kraj_id, kraj_nazwa FROM app.tbl_kraj
                ";
                $countryQuery = @pg_query($connection, $query);

                $regionArray = array();

                while($row = pg_fetch_assoc($countryQuery))
                {
                    $countryId = $row["kraj_id"];
                    $countryName = $row["kraj_nazwa"];
                    $countryArray[] = array("kraj_id" => $countryId, "kraj_nazwa" => $countryName);
                }
                echo json_encode($countryArray);
                break;
            case 'voivodeship':
                $query = "
                        SELECT wojewodztwo_id, wojewodztwo_nazwa FROM app.tbl_wojewodztwo
                ";
                $voivodeshipQuery = @pg_query($connection, $query);

                $regionArray = array();

                while($row = pg_fetch_assoc($voivodeshipQuery))
                {
                    $voivodeshipId = $row["wojewodztwo_id"];
                    $voivodeshipName = $row["wojewodztwo_nazwa"];
                    $voivodeshipArray[] = array("wojewodztwo_id" => $voivodeshipId, "wojewodztwo_nazwa" => $voivodeshipName);
                }
                echo json_encode($voivodeshipArray);
                break;
            }
    } catch(Exception $err)
    {
        echo '<span style="color:red;">Server error';
        echo '<br/>Dev info: '.$error->getMessage();
    }
}

?>
