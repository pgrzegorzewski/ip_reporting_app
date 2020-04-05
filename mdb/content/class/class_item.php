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

    public function updateItemData($itemId, $itemName, $isActive, $groupId, $typeId, $priceGo, $pricePo, $priceGd, $pricePd)
    {
      $success = true;
      if(!$itemId) {
        $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Błąd podczas przekazania id towaru</p>';
        $success = false;
      }
      if(strlen(preg_replace('/\s/', '', $itemName)) < 2) {
        $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Zbyt krótka nazwa towaru</p>';
        $success = false;
      }
      if(!$groupId || intval($groupId) <= 0) {
        $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Brak szeregu</p>';
        $success = false;
      }
      if(!$typeId || intval($typeId) <= 0) {
        $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Brak rodzaju</p>';
        $success = false;
      }

      try {
        $query = "SELECT * FROM app.sf_sprawdz_unikalnosc_towaru('$itemName') AS is_item_name_unique";
        $item_name_unique_check = pg_query($this->connection, $query);
        $is_item_name_unique_check = pg_fetch_assoc($item_name_unique_check);

        $query = "SELECT towar_nazwa AS old_item_name FROM app.tbl_towar WHERE towar_id = $itemId ";
        $item_name_changed_check = pg_query($this->connection, $query);
        $old_item_name = pg_fetch_assoc($item_name_changed_check);

        $itemNameChanged = true;
        if($old_item_name['old_item_name'] == $itemName) {
          $itemNameChanged = false;
        }

        if($is_item_name_unique_check['is_item_name_unique'] != 1 && $itemNameChanged)
        {
            $success = false;
            $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Towar o takiej nazwie już istnieje.</p>';
        }
      } catch(Exception $error) {
          $error->getMessage();
      }

      if($success == true) {
        try {
          $query = "SELECT * FROM app.sp_zaktualizuj_dane_towaru
                    (   $itemId
                        ,'$itemName'
                        ,$isActive::BIT
                        ,$typeId
                        ,$groupId
                        ,$priceGo
                        ,$pricePo
                        ,$priceGd
                        ,$pricePd
                    )";
          $result = pg_query($this->connection, $query);
          $_SESSION['e_item_update'] = '<p style = "color:green; text-align:center;">Towar zaktualizowany pomyślnie.</p>';

        } catch(Exception $error) {
            $error->getMessage();
        }
      }
    }
}
