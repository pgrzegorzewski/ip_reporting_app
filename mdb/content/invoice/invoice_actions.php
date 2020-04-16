<?php
session_start();
require_once '../core/connect.php';
require '../class/class_invoice.php';

$login = $_SESSION['user'];
$action = $_POST['action'];

if(isset($_POST['invoiceItemId'])) {
  $invoiceItemId = $_POST['invoiceItemId'];
}

$invoice = new Invoice();

switch ($action) {
  case 'getInvoiceHeaderData':
    $invoiceHeaderData = $invoice->getInvoiceHeaderData($invoiceItemId);
    return $invoiceHeaderData;
    break;
  case 'getInvoiceItemData':
    $invoiceItemData = $invoice->getInvoiceItemData($invoiceItemId);
    return $invoiceItemData;
    break;

}


 ?>
