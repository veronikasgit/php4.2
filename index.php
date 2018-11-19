<?php
include "config.php";

$data = $_POST;

$sql3 = $pdo->prepare("SELECT * FROM user");	
$sql3->execute();
$assignedUserList = $sql3->fetchAll(PDO::FETCH_ASSOC);
	
if (isset($_SESSION['user_id'])) {
	echo "Авторизован!" . '<br>';
	echo "Привет, " . $_SESSION['user_name'] . '<br>'; 
	
	echo '<a href="logout.php">Выйти</a><hr>';

	$sql = $pdo->prepare("SELECT * from task WHERE user_id = '{$_SESSION['user_id']}' ORDER BY date_added");
	$sql->execute();
	$task = $sql->fetchAll(PDO::FETCH_ASSOC);

	$sql2  = $pdo->prepare("SELECT * from task WHERE assigned_user_id = '{$_SESSION['user_id']}' ORDER BY date_added");
	$sql2->execute();
	$task2 = $sql2->fetchAll(PDO::FETCH_ASSOC);
	
	$sql4 = $pdo->prepare("
	SELECT * 
	FROM task t 
	INNER JOIN user u ON u.id=t.assigned_user_id 
	WHERE 
	t.user_id = '{$_SESSION['user_id']}' OR t.assigned_user_id = t.user_id ");
	$sql4->execute();
	$assignedUserList2 = $sql4->fetchAll(PDO::FETCH_ASSOC);

	$sql5 = $pdo->prepare("
	SELECT * 
	FROM task t 
	INNER JOIN user u ON u.id=t.user_id 
	WHERE 
	t.user_id = t.assigned_user_id OR t.assigned_user_id = '{$_SESSION['user_id']}' ");
	$sql5->execute();
	$assignedUserList3 = $sql5->fetchAll(PDO::FETCH_ASSOC);
} else {
	echo "Вы не авторизованы!" . '<br>';
	echo '<a href="login.php">Авторизоваться</a><br>';
	echo '<a href="signup.php">Регистрация</a>';
	exit;
}

if (isset($_GET['deleted'])) {
    $id = $_GET['deleted'];
	$del = $pdo->exec("DELETE FROM task WHERE user_id = '{$_SESSION['user_id']}' AND id = $id  LIMIT 1");
}

if (isset($_GET['is_done'])) {
    $is_done = $_GET['is_done'];
	$setIsDone = $pdo->exec("UPDATE task SET is_done = 1 WHERE user_id = '{$_SESSION['user_id']}' AND id = $is_done LIMIT 1");
}

$sql5 = $pdo->prepare("SELECT COUNT(*) FROM task WHERE user_id = '{$_SESSION['user_id']}' OR assigned_user_id = '{$_SESSION['user_id']}'");
	$sql5->execute();
	$task3 = $sql5->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
	<title>php4.1</title><style>
		table {
		    border-collapse: collapse;
		    border: 2px solid grey; }
		th {
		    border: 2px solid grey;
		    padding: 10px 15px;
		}
		 td {
		    border: 1px solid grey;
		    text-align: center;
		    padding: 3px 5px;
		}
		</style>
</head>
<body>

	<form action="adddescription.php" method="POST">
	
	<p>
		<p><strong>Описание</strong></p>
		<input type="text" name="description" value="<?php echo @$data['description']; ?>">
	</p>

		<p>
		<p><strong>Дата</strong></p>
		<input type="date" name="date" value="<?php echo $data['date_added']; ?>">
	</p>

	<p>
		<button type="submit" name="add_description">Добавить дело</button>
	</p>

</form>

	<h2>Список дел</h2>

	<table>
		<tr>
			<th>Дела</th>
			<th>Когда</th>
			<th>Выполнено/невыполнено</th>
			<th>Исполнитель</th>
			<th>Удаление</th>
		</tr>
		
		 <?php foreach ($task as $key => $row1) : ?> 
		 	
		 	<?php if(is_null($row1['assigned_user_id'])) : ?>
	
				<tr>
					<td><?php echo $row1['description']."<br />"; ?></td>
					<td><?php echo $row1['date_added']."<br />"; ?></td>
					<td>
						<?php if ($row1['is_done'] == 1) : ?>
							<a href="#">Выполнено</a>
						<?php elseif ($row1['is_done'] === 0) : ?>
							<a href="#"><?php echo "Невыполнено"."<br />"; ?></a>
						<?php else : ?>	
							<a href="index.php?is_done=<?php echo $row1['id']; ?>"><?php echo "Выполнить"."<br />"; ?></a>
						<?php endif; ?>
					</td>
					<td>						
						<form action="delegate.php" method="POST">
							<input name="task_id" type="hidden" value="<?php echo $row1['id'] ?>"> 
							<input name="user_id" type="hidden" value="<?php echo $_SESSION['user_id'] ?>"> 
							<select name="assigned_user_id">
							<?php foreach ($assignedUserList as $number => $assignedUser): ?>
								<?php if ($assignedUser['id'] != $_SESSION['user_id']) : ?>
									<option <?php if ($row1['assigned_user_id'] == $assignedUser['id']):?>
									    selected<?php endif; ?> value="<?= $assignedUser['id'] ?>">
									    <?php echo $assignedUser['login'] ?>
									</option>
								<?php endif; ?>
							<?php endforeach; ?>
							</select>
							<input type="submit" name="delegate" value="Делегировать"> 
						</form>
					</td>
					<td><a href = "index.php?deleted=<?php echo $row1['id']; ?>">Удалить</a></td>					
				</tr>
			<?php endif; ?>
		<?php endforeach; ?> 
		
	</table>

	<h2>Список делегированных мне дел</h2>
	<table>
		<tr>
			<th>Дела</th>
			<th>Когда</th>
			<th>Кто делегировал</th>
		</tr>
		
		 <?php foreach ($assignedUserList3 as $row3) : ?> 
		 	
				<tr>
					<td><?php echo $row3['description']."<br />"; ?></td>
					<td><?php echo $row3['date_added']."<br />"; ?></td>
					<td><?php echo $row3['login']."<br />"; ?></td>	
						
				</tr>

		<?php endforeach; ?> 
		
	</table>

	<h2>Список делегированных дел</h2>
	<table>
		<tr>
			<th>Дела</th>
			<th>Когда</th>
			<th>Кому делегировал</th>
		</tr>
		
		 <?php foreach ($assignedUserList2 as $row2) : ?> 
		 	
				<tr>
					<td><?php echo $row2['description']."<br />"; ?></td>
					<td><?php echo $row2['date_added']."<br />"; ?></td>
					<td><?php echo $row2['login']."<br />"; ?></td>	
						
				</tr>

		<?php endforeach; ?> 
		
	</table>
	<p><?php echo "Всего дел: $task3";?></p>
</body>
</html>