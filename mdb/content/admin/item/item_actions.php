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
  $isActive = $_POST['is_active'];
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

$item = new Item();

switch ($action) {
  case 'getItems':
    $items = $item->getItemManagementList();
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
}


 ?>
