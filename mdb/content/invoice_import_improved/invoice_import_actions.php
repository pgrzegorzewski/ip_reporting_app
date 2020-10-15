<?php
session_start();
require_once '../core/connect.php';
require '../class/class_item.php';
require '../class/class_invoice.php';

$login = $_SESSION['user'];
$action = $_POST['action'];

if(isset($_POST['item'])) {
  $itemId = $_POST['item'];
}

if(isset($_POST['amount'])) {
  $amount = $_POST['amount'];
}

if(isset($_POST['invoice_number'])) {
  $invoiceNumber = $_POST['invoice_number'];
}

switch ($action) {
  case 'getItemPrices':
    $item = new Item();
    $itemPrices = $item->getItemPrices($itemId, $amount);
    return $itemPrices;
    break;
  case 'isInvoiceImported':
    $invoice = new Invoice();
    $isInvoiceImported = $invoice->isInvoiceImported($invoiceNumber);
    return $isInvoiceImported;
    break;
}


 ?>
