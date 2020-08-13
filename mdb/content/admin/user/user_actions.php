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
  $isActive = 1;
} else {
  $isActive = 0;
}

if(isset($_POST['passwordTemporary'])) {
  $passwordTemporary = $_POST['passwordTemporary'];
}

if(isset($_POST['password_temporary'])) {
  $passwordTemporaryNew = $_POST['password_temporary'];
}

if(isset($_POST['latePayYear'])) {
  $latePayYear = $_POST['latePayYear'];
}

if(isset($_POST['latePayMonth'])) {
  $latePayMonth = $_POST['latePayMonth'];
}

if(isset($_POST['latePayValue'])) {
  $latePayValue = $_POST['latePayValue'];
}

$user = new User();

switch ($action) {
  case 'getUsers':
    $users = $user->getUserManagementList($login);
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
  case 'updateUserLatePayValue':
    $latePayData = $user->updateUserLatePayValue($login, $userId, $latePayYear, $latePayMonth, $latePayValue);
    return $latePayData;
    break;
  case 'addUser':
    $userData = $user->addUser($username, $firstName, $lastName, $role, $isActive, $passwordTemporaryNew);
    header('Location:./user.php');
    break;
}


 ?>
