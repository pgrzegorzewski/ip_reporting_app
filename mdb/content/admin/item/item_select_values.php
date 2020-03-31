<?php

require ($_SERVER["DOCUMENT_ROOT"]. '/reporting-app/mdb/content/core/connect.php');

$filterType = $_POST['type'];
if ($connection) {
    try {
        switch ($filterType) {
          case 'type':
              $query = "
                      SELECT rodzaj_id, rodzaj_nazwa FROM app.tbl_rodzaj
              ";
              $itemQuery = @pg_query($connection, $query);

              while($row = pg_fetch_assoc($itemQuery))
              {
                  $typeId = $row["rodzaj_id"];
                  $typeName = $row["rodzaj_nazwa"];
                  $typeArray[] = array("rodzaj_id" => $typeId, "rodzaj_nazwa" => $typeName);
              }
              echo json_encode($typeArray);
              break;
          case 'group':
              $query = "
                      SELECT szereg_id, szereg_nazwa FROM app.tbl_szereg
              ";
              $itemQuery = @pg_query($connection, $query);

              while($row = pg_fetch_assoc($itemQuery))
              {
                  $groupId = $row["szereg_id"];
                  $groupName = $row["szereg_nazwa"];
                  $groupArray[] = array("szereg_id" => $groupId, "szereg_nazwa" => $groupName);
              }
              echo json_encode($groupArray);
              break;
            }
    } catch(Exception $err)
    {
        echo '<span style="color:red;">Server error';
        echo '<br/>Dev info: '.$error->getMessage();
    }
}

?>
