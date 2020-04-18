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
    $invoiceItemData = $invoice->updateInvoiceHeader($invoiceItemId, $invoiceNumber, $invoiceDate, $salesman, $currency, $rate, $export, $transfer, $delivery, $client, $country, $voivodship, $region, $note, $login);
    return $invoiceItemData;
    break;

}


 ?>
