<?php

class Item
{
    public $itemId;
    protected $connection;

    function __construct() {
      $this->setConnection();
    }

    private function setConnection() {
      require ($_SERVER["DOCUMENT_ROOT"]. '/reporting-app/mdb/content/core/connect.php');
      $this->connection = $connection;
    }

    public function getItemManagementList() {
      $query = "SELECT * FROM app.tf_pobierz_towary()";
      $result = pg_query($this->connection, $query);
      $resp = array();

      while($row = pg_fetch_assoc($result))
      {
        array_push($resp, array(
                                  'towar_id' => $row['towar_id'],
                                  'towar_nazwa' => $row['towar_nazwa'],
                                  'jest_aktywny' => $row['jest_aktywny'],
                                  'szereg_nazwa' => $row['szereg_nazwa'],
                                  'rodzaj_nazwa' => $row['rodzaj_nazwa'],
                                  'cena_go' => $row['cena_go'],
                                  'cena_po' => $row['cena_po'],
                                  'cena_gd' => $row['cena_gd'],
                                  'cena_pd' => $row['cena_pd'],
                                  'edycja'=> "<button style='padding:5px' data-id='" . $row['towar_id'] . "' id='itemId-" . $row['towar_id'] . "' class='btn btn-info' data-toggle='modal' data-target='#editItemModal'>edytuj</button>")
                                );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }

    public function getItemData($itemId)
    {
      $query = "SELECT * FROM app.tf_pobierz_towar($itemId)";
      $result = pg_query($this->connection, $query);
      $resp = array();

      while($row = pg_fetch_assoc($result))
      {
        array_push($resp, array(
                                  'towar_id' => $row['towar_id'],
                                  'towar_nazwa' => $row['towar_nazwa'],
                                  'jest_aktywny' => $row['jest_aktywny'],
                                  'szereg_nazwa' => $row['szereg_nazwa'],
                                  'szereg_id' => $row['szereg_id'],
                                  'rodzaj_nazwa' => $row['rodzaj_nazwa'],
                                  'rodzaj_id' => $row['rodzaj_id'],
                                  'cena_go' => $row['cena_go'],
                                  'cena_po' => $row['cena_po'],
                                  'cena_gd' => $row['cena_gd'],
                                  'cena_pd' => $row['cena_pd'])
                                );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }

}
