<?php
include "config.php";

$data = $_POST;

if (isset($data['add_description'])) {
		$errors = array();

	if (trim($data['description']) == '')
	{
		$errors[] = 'Введите описание';
	}
	if ($data['date'] == '') 
	{
		$errors[] = 'Введите дату';
	}
						
	$sth = $pdo->prepare("SELECT * FROM task WHERE description LIKE '%{$data['description']}%' and user_id = '{$_SESSION['user_id']}'");
	$sth->execute();

	$result = $sth->fetchAll(PDO::FETCH_ASSOC);
//	echo '<pre>';
	//var_dump($result) ;
	foreach ($result as  $toDoRow) {
		if ($data['description'] === $toDoRow['description']) {
			$errors[] = 'Дело с таким описанием уже существует!';		
		}
	}
	// if ($data['description'] === $result['0']['description']) {
	// 	$errors[] = 'Дело с таким описанием уже существует!';
	// }
	$id = $_SESSION['user_id'];
	if (empty($errors)) {

		//echo 'empty($errors)';
		$row = $pdo->exec("INSERT INTO task (user_id, description, date_added) VALUES ($id, '{$data['description']}', '{$data['date']}')");
		//$row1 = $pdo->exec("UPDATE task SET assigned_user_id = $id");
		header('Location: /');
			echo '<br>' . "Дело '{$data['description']}' успешно добавлено!";
	} else {
		echo '<div style="color: red;">' . array_shift($errors).'</div>';
	}

}
