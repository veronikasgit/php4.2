<?php
include "config.php";
$pdo = new PDO("mysql:host=127.0.0.1;dbname=golunova;charset=utf8", "golunova", "neto1722");
$data = $_POST;

if (isset($_POST['delegate'])) {
	$delegate = $pdo->exec("UPDATE task SET assigned_user_id='{$_POST['assigned_user_id']}' WHERE id='{$_POST['task_id']}' and user_id='{$_POST['user_id']}' LIMIT 1" );
}
 header('Location: /');
?>