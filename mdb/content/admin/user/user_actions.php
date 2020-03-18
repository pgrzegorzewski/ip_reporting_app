<?php
session_start();
require_once '../../core/connect.php';
require '../../class/class_user.php';

$login = $_SESSION['user'];
$action = $_POST['action'];

if(isset($_POST['userId'])) {
  $userId = $_POST['userId'];
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
}


 ?>
