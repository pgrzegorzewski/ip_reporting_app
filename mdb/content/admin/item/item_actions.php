<?php
session_start();
require_once '../../core/connect.php';
require '../../class/class_item.php';

$login = $_SESSION['user'];
$action = $_POST['action'];

if(isset($_POST['itemId'])) {
  $itemId = $_POST['itemId'];
}

if(isset($_POST['item_name'])) {
  $itemName = $_POST['item_name'];
}

if(isset($_POST['is_active'])) {
  $isActive = 1;
} else {
  $isActive = 0;
}

if(isset($_POST['group'])) {
  $groupId = $_POST['group'];
}

if(isset($_POST['type'])) {
  $typeId = $_POST['type'];
}

if(isset($_POST['price_go'])) {
  $priceGo = $_POST['price_go'];
}

if(isset($_POST['price_po'])) {
  $pricePo = $_POST['price_po'];
}

if(isset($_POST['price_gd'])) {
  $priceGd = $_POST['price_gd'];
}

if(isset($_POST['price_pd'])) {
  $pricePd = $_POST['price_pd'];
}

if(isset($_POST['item_name_new'])) {
  $itemNameNew = $_POST['item_name_new'];
}

if(isset($_POST['is_active_new'])) {
  $isActiveNew = 1;
} else {
  $isActiveNew = 0;
}

if(isset($_POST['group_new'])) {
  $groupIdNew = $_POST['group_new'];
}

if(isset($_POST['type_new'])) {
  $typeIdNew = $_POST['type_new'];
}

if(isset($_POST['price_go_new'])) {
  $priceGoNew = $_POST['price_go_new'];
}

if(isset($_POST['price_po_new'])) {
  $pricePoNew = $_POST['price_po_new'];
}

if(isset($_POST['price_gd_new'])) {
  $priceGdNew = $_POST['price_gd_new'];
}

if(isset($_POST['price_pd_new'])) {
  $pricePdNew = $_POST['price_pd_new'];
}

$item = new Item();

switch ($action) {
  case 'getItems':
    $items = $item->getItemManagementList($login);
    return $items;
    break;
  case 'getItemData':
    $itemData = $item->getItemData($itemId);
    return $itemId;
    break;
  case 'updateItem':
    $itemData = $item->updateItemData($itemId, $itemName, $isActive, $groupId, $typeId, $priceGo, $pricePo, $priceGd, $pricePd);
    header('Location:./item.php');
    break;
  case 'addItem':
    $itemData = $item->addItem($itemNameNew, $isActiveNew, $groupIdNew, $typeIdNew, $priceGoNew, $pricePoNew, $priceGdNew, $pricePdNew);
    header('Location:./item.php');
    break;
}


 ?>
