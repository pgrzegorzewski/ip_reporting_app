<?php
session_start();
require_once '../../core/connect.php';
require '../../class/class_item.php';

$login = $_SESSION['user'];
$action = $_POST['action'];
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
    $itemData = $item->updateItemData($itemId);
    header('Location:./item.php');
    break;
}


 ?>
