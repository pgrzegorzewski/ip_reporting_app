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


$client = new Client();

switch ($action) {
  case 'getClients':
    $clients = $client->getClientManagementList();
    return $clients;
    break;
  case 'getClientData':
    $clientData = $client->getClientData($clientId);
    return $clientId;
    break;
  case 'updateClient':
    $clientData = $client->updateClientData($clientId, $clientName, $street, $address2, $postCode, $city, $country, $isActive, $isBlackList);
    header('Location:./client.php');
    break;

}


 ?>
