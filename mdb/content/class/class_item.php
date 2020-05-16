<?php

class Item
{
    public $itemId;
    protected $connection;

    function __construct() {
      $this->setConnection();
    }

    private function setConnection() {
      require ($_SERVER["DOCUMENT_ROOT"]. '/ip_reporting_app/mdb/content/core/connect.php');
      $this->connection = $connection;
    }

    public function getItemManagementList($login) {
      $query = "SELECT * FROM app.tf_pobierz_towary($1)";
      $result = pg_query_params($this->connection, $query, array($login));
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
                                  'edycja'=> "<button style='padding:5px' data-id='" . $row['towar_id'] . "' id='itemId-" . $row['towar_id'] . "' class='btn btn-info' data-toggle='modal' data-target='#editItemModal'>edytuj</button>",
                                  'edycja_dostep' => $row['edycja_dostep'])
                                );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }

    public function getItemPrices($itemId, $amount) {
      $query = "SELECT * FROM app.tf_pobierz_ceny_towaru($1, $2)";
      $result = pg_query_params($this->connection, $query, array($itemId, $amount));
      $resp = array();

      while($row = pg_fetch_assoc($result))
      {
        array_push($resp, array(
                                  'cena_go' => $row['cena_go_finalna'],
                                  'cena_po' => $row['cena_po_finalna'],
                                  'cena_gd' => $row['cena_gd_finalna'],
                                  'cena_pd' => $row['cena_pd_finalna']
                               )
        );
      }
      pg_free_result($result);
      echo json_encode($resp);
    }

    public function getItemData($itemId)
    {
      $query = "SELECT * FROM app.tf_pobierz_towar($1)";
      $result = pg_query_params($this->connection, $query, array($itemId));
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
        $query = "SELECT * FROM app.sf_sprawdz_unikalnosc_towaru($1) AS is_item_name_unique";
        $item_name_unique_check = pg_query_params($this->connection, $query, array($itemName));
        $is_item_name_unique_check = pg_fetch_assoc($item_name_unique_check);

        $query = "SELECT towar_nazwa AS old_item_name FROM app.tbl_towar WHERE towar_id = $1 ";
        $item_name_changed_check = pg_query_params($this->connection, $query, array($itemId));
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
          $query = "SELECT * FROM app.sp_zaktualizuj_dane_towaru($1, $2, $3, $4, $5, $6, $7, $8, $9)";
          $result = pg_query_params($this->connection, $query, array($itemId, $itemName, $isActive, $typeId, $groupId, $priceGo, $pricePo, $priceGd, $pricePd));
          $_SESSION['e_item_update'] = '<p style = "color:green; text-align:center;">Towar zaktualizowany pomyślnie.</p>';

        } catch(Exception $error) {
            $error->getMessage();
        }
      }
    }

    public function addItem($itemNameNew, $isActiveNew, $groupIdNew, $typeIdNew, $priceGoNew, $pricePoNew, $priceGdNew, $pricePdNew)
    {
      $success = true;

      if(strlen(preg_replace('/\s/', '', $itemNameNew)) < 2) {
        $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Zbyt krótka nazwa towaru</p>';
        $success = false;
      }

      if(!$groupIdNew || intval($groupIdNew) <= 0) {
        $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Brak szeregu</p>';
        $success = false;
      }
      if(!$typeIdNew || intval($typeIdNew) <= 0) {
        $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Brak rodzaju</p>';
        $success = false;
      }

      try {
        $query = "SELECT * FROM app.sf_sprawdz_unikalnosc_towaru($1) AS is_item_name_unique";
        $item_name_unique_check = pg_query_params($this->connection, $query, array($itemNameNew));
        $is_item_name_unique_check = pg_fetch_assoc($item_name_unique_check);

        if($is_item_name_unique_check['is_item_name_unique'] != 1)
        {
            $success = false;
            $_SESSION['e_item_update'] = '<p style = "color:red; text-align:center;">Towar o takiej nazwie już istnieje.</p>';
        }
      } catch(Exception $error) {
          $error->getMessage();
      }

      if($success == true) {
        try {
          $query = "SELECT * FROM app.sp_dodaj_towar($1, $2, $3, $4, $5, $6, $7, $8)";
          $result = pg_query_params($this->connection, $query, array($itemNameNew, $isActiveNew, $typeIdNew, $groupIdNew, $priceGoNew, $pricePoNew, $priceGdNew, $pricePdNew));
          $_SESSION['e_item_update'] = '<p style = "color:green; text-align:center;">Towar dodany pomyślnie.</p>';
        } catch(Exception $error) {
            $error->getMessage();
        }
      }
    }
}
