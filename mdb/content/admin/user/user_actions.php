<?php
session_start();
require_once '../../core/connect.php';
require '../../class/class_user.php';

$login = $_SESSION['user'];
$action = $_POST['action'];
$user = new User();

switch ($action) {
  case 'getUsers':
    $users = $user->getUsers();
    return $users;
    break;
}

pg_close($connection);

 ?>
