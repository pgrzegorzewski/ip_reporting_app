<?php
session_start();
require_once '../../core/connect.php';
require '../../class/class_client.php';

$login = $_SESSION['user'];
$action = $_POST['action'];

if(isset($_POST['clientId'])) {
  $clientId = $_POST['clientId'];
}
//
// if(isset($_POST['username'])) {
//   $username = $_POST['username'];
// }
//
// if(isset($_POST['first_name'])) {
//   $firstName = $_POST['first_name'];
// }
//
// if(isset($_POST['last_name'])) {
//   $lastName = $_POST['last_name'];
// }
//
// if(isset($_POST['role'])) {
//   $role = $_POST['role'];
// }
//
// if(isset($_POST['is_active'])) {
//   $isActive = 1;
// } else {
//   $isActive = 0;
// }
//
// if(isset($_POST['passwordTemporary'])) {
//   $passwordTemporary = $_POST['passwordTemporary'];
// }
//


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
  // case 'updateUser':
  //   $userData = $user->updateUserData($userId, $username, $firstName, $lastName, $role, $isActive);
  //   header('Location:./user.php');
  //   break;

}


 ?>
