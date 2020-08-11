<?php
session_start();
require_once '../../core/connect.php';
require '../../class/class_client.php';

$login = $_SESSION['user'];
$action = $_POST['action'];

if(isset($_POST['clientId'])) {
  $clientId = $_POST['clientId'];
}

if(isset($_POST['client_name'])) {
  $clientName = $_POST['client_name'];
}

if(isset($_POST['street'])) {
  $street = $_POST['street'];
}

if(isset($_POST['address_2'])) {
  $address2 = $_POST['address_2'];
}

if(isset($_POST['post_code'])) {
  $postCode = $_POST['post_code'];
}

if(isset($_POST['city'])) {
  $city = $_POST['city'];
}

if(isset($_POST['voivodeship']) && is_numeric($_POST['voivodeship'])) {
  $voivodeship = $_POST['voivodeship'];
} else {
  $voivodeship = NULL;
}

if(isset($_POST['region']) && is_numeric($_POST['region'])) {
  $region = $_POST['region'];
} else {
  $region = NULL;
}

if(isset($_POST['country']) && is_numeric($_POST['country'])) {
  $country = $_POST['country'];
} else {
  $country= NULL;
}

if(isset($_POST['is_active'])) {
  $isActive = 1;
} else {
  $isActive = 0;
}

if(isset($_POST['black_list'])) {
  $isBlackList = 1;
} else {
  $isBlackList = 0;
}

if(isset($_POST['transferCheckbox'])) {
  $transfer = 1;
} else {
  $transfer = 0;
}

if(isset($_POST['deliveryCheckbox'])) {
  $delivery = 1;
} else {
  $delivery = 0;
}

if(isset($_POST['exportCheckbox'])) {
  $export = 1;
} else {
  $export = 0;
}

if(isset($_POST['currency']) && is_numeric($_POST['currency'])) {
  $currency = $_POST['currency'];
} else {
  $currency = NULL;
}

if(isset($_POST['salesman']) && is_numeric($_POST['salesman'])) {
  $salesman = $_POST['salesman'];
} else {
  $salesman = NULL;
}

if(isset($_POST['bonus'])) {
  $bonus = $_POST['bonus'];
}

if(isset($_POST['client_name_new'])) {
  $clientNameNew = $_POST['client_name_new'];
}

if(isset($_POST['street_new'])) {
  $streetNew = $_POST['street_new'];
}

if(isset($_POST['address_2_new'])) {
  $address2New = $_POST['address_2_new'];
}

if(isset($_POST['post_code_new'])) {
  $postCodeNew = $_POST['post_code_new'];
}

if(isset($_POST['city_new'])) {
  $cityNew = $_POST['city_new'];
}

if(isset($_POST['voivodeship_new']) && is_numeric($_POST['voivodeship_new'])) {
  $voivodeshipNew = $_POST['voivodeship_new'];
} else {
  $voivodeshipNew = NULL;
}

if(isset($_POST['region_new']) && is_numeric($_POST['region_new'])) {
  $regionNew = $_POST['region_new'];
} else {
  $regionNew = NULL;
}

if(isset($_POST['country_new']) && is_numeric($_POST['country_new'])) {
  $countryNew = $_POST['country_new'];
} else {
  $countryNew = NULL;
}

if(isset($_POST['is_active_new'])) {
  $isActiveNew = 1;
} else {
  $isActiveNew = 0;
}

if(isset($_POST['black_list_new'])) {
  $isBlackListNew = 1;
} else {
  $isBlackListNew = 0;
}

if(isset($_POST['transfer_checkbox_new'])) {
  $transferNew = 1;
} else {
  $transferNew = 0;
}

if(isset($_POST['delivery_checkbox_new'])) {
  $deliveryNew = 1;
} else {
  $deliveryNew = 0;
}

if(isset($_POST['export_checkbox_new'])) {
  $exportNew = 1;
} else {
  $exportNew = 0;
}

if(isset($_POST['currency_new']) && is_numeric($_POST['currency_new'])) {
  $currencyNew = $_POST['currency_new'];
} else {
  $currencyNew = NULL;
}

if(isset($_POST['salesman_new'])  && is_numeric($_POST['salesman_new'])) {
  $salesmanNew = $_POST['salesman_new'];
} else {
  $salesmanNew = NULL;
}

$client = new Client();

switch ($action) {
  case 'getClients':
    $clients = $client->getClientManagementList($login);
    return $clients;
    break;
  case 'getClientData':
    $clientData = $client->getClientData($clientId);
    return $clientId;
    break;
  case 'updateClient':
    $clientData = $client->updateClientData($clientId, $clientName, $street, $address2, $postCode, $city, $voivodeship, $region, $country, $isActive, $isBlackList, $bonus, $transfer, $delivery, $export, $currency, $salesman);
    header('Location:./client.php');
    break;
  case 'addClient':
    $clientData = $client->addClient($clientNameNew, $streetNew, $address2New, $postCodeNew, $cityNew, $voivodeshipNew, $regionNew, $countryNew, $isActiveNew, $isBlackListNew, $transferNew, $deliveryNew, $exportNew, $currencyNew, $salesmanNew);
    header('Location:./client.php');
    break;
}


 ?>
