<?php
session_start();
require_once '../../core/connect.php';
require '../../class/class_user.php';

$login = $_SESSION['user'];
$action = $_POST['action'];

if(isset($_POST['userId'])) {
  $userId = $_POST['userId'];
}

if(isset($_POST['username'])) {
  $username = $_POST['username'];
}

if(isset($_POST['first_name'])) {
  $firstName = $_POST['first_name'];
}

if(isset($_POST['last_name'])) {
  $lastName = $_POST['last_name'];
}

if(isset($_POST['role'])) {
  $role = $_POST['role'];
}

if(isset($_POST['is_active'])) {
  $isActive = $_POST['is_active'];
}

if(isset($_POST['passwordTemporary'])) {
  $passwordTemporary = $_POST['passwordTemporary'];
}



$user = new User();

switch ($action) {
  case 'getUsers':
    $users = $user->getUserManagementList();
    return $users;
    break;
  case 'getUserData':
    $userData = $user->getUserData($userId);
    return $userId;
    break;
  case 'updateUser':
    $userData = $user->updateUserData($userId, $username, $firstName, $lastName, $role, $isActive);
    header('Location:./user.php');
    break;
  case 'assignTemporaryPassword':
    $userData = $user->assignTemporaryPassword($userId, $passwordTemporary);
    return $userData;
    break;
}


 ?>
