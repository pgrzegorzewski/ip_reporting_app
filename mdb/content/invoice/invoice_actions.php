<?php
session_start();
require_once '../core/connect.php';
require '../class/class_invoice.php';

$login = $_SESSION['user'];
$action = $_POST['action'];

if(isset($_POST['invoiceItemId'])) {
  $invoiceItemId = $_POST['invoiceItemId'];
}

if(isset($_POST['invoiceNumber'])) {
  $invoiceNumber = $_POST['invoiceNumber'];
}

if(isset($_POST['invoiceDate'])) {
  $invoiceDate = $_POST['invoiceDate'];
}

if(isset($_POST['salesman'])) {
  $salesman = $_POST['salesman'];
}

if(isset($_POST['currency'])) {
  $currency = $_POST['currency'];
}

if(isset($_POST['rate'])) {
  $rate = $_POST['rate'];
}

if(isset($_POST['export'])) {
  $export = $_POST['export'];
}

if(isset($_POST['transfer'])) {
  $transfer = $_POST['transfer'];
}

if(isset($_POST['delivery'])) {
  $delivery = $_POST['delivery'];
}

if(isset($_POST['client'])) {
  $client = $_POST['client'];
}

if(isset($_POST['country'])) {
  $country = $_POST['country'];
}

if(isset($_POST['voivodship'])) {
  $voivodship = $_POST['voivodship'];
}

if(isset($_POST['region'])) {
  $region = $_POST['region'];
}

if(isset($_POST['note'])) {
  $note = $_POST['note'];
}

if(isset($_POST['invoiceActive'])) {
  $invoiceActive = $_POST['invoiceActive'];
}

if(isset($_POST['item'])) {
  $item = $_POST['item'];
}

if(isset($_POST['amount'])) {
  $amount = $_POST['amount'];
}

if(isset($_POST['unit'])) {
  $unit = $_POST['unit'];
}

if(isset($_POST['price'])) {
  $price = $_POST['price'];
}

if(isset($_POST['priceZero'])) {
  $priceZero = $_POST['priceZero'];
}

if(isset($_POST['value'])) {
  $value = $_POST['value'];
}

if(isset($_POST['margin'])) {
  $margin = $_POST['margin'];
}

if(isset($_POST['percent'])) {
  $percent = $_POST['percent'];
}

if(isset($_POST['itemActive'])) {
  $invoiceItemActive = 0;
} else {
  $invoiceItemActive = 1;
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
  case 'updateInvoiceHeader':
    $invoiceItemData = $invoice->updateInvoiceHeader($invoiceItemId, $invoiceNumber, $invoiceDate, $salesman, $currency, $rate, $export, $transfer, $delivery, $client, $country, $voivodship, $region, $note, $invoiceActive, $login);
    return $invoiceItemData;
    break;
  case 'updateInvoiceItem':
    $invoiceItemData = $invoice->updateInvoiceItem($invoiceItemId, $item, $amount, $unit, $price, $priceZero, $value, $margin, $percent, $invoiceItemActive, $login);
    header('Location:./invoice.php');
    break;
  case 'addInvoiceItem':
    $invoiceItemData = $invoice->addInvoiceItem($invoiceItemId, $item, $amount, $unit, $price, $priceZero, $value, $margin, $percent,  $login);
    header('Location:./invoice.php');
    break;
}


 ?>
