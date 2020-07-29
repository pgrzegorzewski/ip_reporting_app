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

if(isset($_POST['voivodeship'])) {
  $voivodeship = $_POST['voivodeship'];
}

if(isset($_POST['region'])) {
  $region = $_POST['region'];
}

if(isset($_POST['country'])) {
  $country = $_POST['country'];
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

if(isset($_POST['currency'])) {
  $currency = $_POST['currency'];
}

if(isset($_POST['salesman'])) {
  $salesman = $_POST['salesman'];
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

if(isset($_POST['country_new'])) {
  $countryNew = $_POST['country_new'];
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
    $clientData = $client->addClient($clientNameNew, $streetNew, $address2New, $postCodeNew, $cityNew, $countryNew, $isActiveNew, $isBlackListNew);
    header('Location:./client.php');
    break;
}


 ?>
